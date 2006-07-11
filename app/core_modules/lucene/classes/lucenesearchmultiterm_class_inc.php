<?php

class lucenesearchmultiterm extends lucenesearchquery
{

    /**
     * Terms to find.
     * Array of search_Lucene_Index_Term
     *
     * @var array
     */
    private $_terms = array();

    /**
     * Term signs.
     * If true then term is required.
     * If false then term is prohibited.
     * If null then term is neither prohibited, nor required
     *
     * If array is null then all terms are required
     *
     * @var array
     */

    private $_signs = array();

    /**
     * Result vector.
     * Bitset or array of document IDs
     * (depending from Bitset extension availability).
     *
     * @var mixed
     */
    private $_resVector = null;

    /**
     * Terms positions vectors.
     * Array of Arrays:
     * term1Id => (docId => array( pos1, pos2, ... ), ...)
     * term2Id => (docId => array( pos1, pos2, ... ), ...)
     *
     * @var array
     */
    private $_termsPositions = array();


    /**
     * A score factor based on the fraction of all query terms
     * that a document contains.
     * float for conjunction queries
     * array of float for non conjunction queries
     *
     * @var mixed
     */
    private $_coord = null;


    /**
     * Terms weights
     * array of search_Weight
     *
     * @var array
     */
    private $_weights = array();


    /**
     * Class constructor.  Create a new multi-term query object.
     *
     * @param array $terms    search_Lucene_Index_Term objects
     * @param array $signs    Array of signs.  Sign is boolean|null.
     * @return void
     */
    public function __construct($terms = null, $signs = null)
    {
        /**
         * @todo Check contents of $terms and $signs before adding them.
         */
        if (is_array($terms)) {
            $this->_terms = $terms;

            $this->_signs = null;
            // Check if all terms are required
            if (is_array($signs)) {
                foreach ($signs as $sign ) {
                    if ($sign !== true) {
                        $this->_signs = $signs;
                        continue;
                    }
                }
            }
        }
    }


    /**
     * Add a $term (search_Lucene_Index_Term) to this query.
     *
     * The sign is specified as:
     *     TRUE  - term is required
     *     FALSE - term is prohibited
     *     NULL  - term is neither prohibited, nor required
     *
     * @param  search_Lucene_Index_Term $term
     * @param  boolean|null $sign
     * @return void
     */
    public function addTerm(lucenesearchindexterm $term, $sign=null) {
        $this->_terms[] = $term;

        /**
         * @todo This is not good.  Sometimes $this->_signs is an array, sometimes
         * it is null, even when there are terms.  It will be changed so that
         * it is always an array.
         */
        if ($this->_signs === null) {
            if ($sign !== null) {
                $this->_signs = array();
                foreach ($this->_terms as $term) {
                    $this->_signs[] = null;
                }
                $this->_signs[] = $sign;
            }
        } else {
            $this->_signs[] = $sign;
        }
    }


    /**
     * Returns query term
     *
     * @return array
     */
    public function getTerms()
    {
        return $this->_terms;
    }


    /**
     * Return terms signs
     *
     * @return array
     */
    public function getSigns()
    {
        return $this->_signs;
    }


    /**
     * Set weight for specified term
     *
     * @param integer $num
     * @param search_Weight_Term $weight
     */
    public function setWeight($num, $weight)
    {
        $this->_weights[$num] = $weight;
    }


    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param search_Lucene $reader
     * @return search_Lucene_Search_Weight
     */
    protected function _createWeight($reader)
    {
        return new lucenesearchweightmultiterm($this, $reader);
    }


    /**
     * Calculate result vector for Conjunction query
     * (like '+something +another')
     *
     * @param search_Lucene $reader
     */
    private function _calculateConjunctionResult($reader)
    {
        if (extension_loaded('bitset')) {
            foreach( $this->_terms as $termId=>$term ) {
                if($this->_resVector === null) {
                    $this->_resVector = bitset_from_array($reader->termDocs($term));
                } else {
                    $this->_resVector = bitset_intersection(
                                $this->_resVector,
                                bitset_from_array($reader->termDocs($term)) );
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }
        } else {
            foreach( $this->_terms as $termId=>$term ) {
                if($this->_resVector === null) {
                    $this->_resVector = array_flip($reader->termDocs($term));
                } else {
                    $termDocs = array_flip($reader->termDocs($term));
                    foreach($this->_resVector as $key=>$value) {
                        if (!isset( $termDocs[$key] )) {
                            unset( $this->_resVector[$key] );
                        }
                    }
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }
        }
    }


    /**
     * Calculate result vector for non Conjunction query
     * (like '+something -another')
     *
     * @param search_Lucene $reader
     */
    private function _calculateNonConjunctionResult($reader)
    {
        if (extension_loaded('bitset')) {
            $required   = null;
            $neither    = bitset_empty();
            $prohibited = bitset_empty();

            foreach ($this->_terms as $termId => $term) {
                $termDocs = bitset_from_array($reader->termDocs($term));

                if ($this->_signs[$termId] === true) {
                    // required
                    if ($required !== null) {
                        $required = bitset_intersection($required, $termDocs);
                    } else {
                        $required = $termDocs;
                    }
                } elseif ($this->_signs[$termId] === false) {
                    // prohibited
                    $prohibited = bitset_union($prohibited, $termDocs);
                } else {
                    // neither required, nor prohibited
                    $neither = bitset_union($neither, $termDocs);
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }

            if ($required === null) {
                $required = $neither;
            }
            $this->_resVector = bitset_intersection( $required,
                                                     bitset_invert($prohibited, $reader->count()) );
        } else {
            $required   = null;
            $neither    = array();
            $prohibited = array();

            foreach ($this->_terms as $termId => $term) {
                $termDocs = array_flip($reader->termDocs($term));

                if ($this->_signs[$termId] === true) {
                    // required
                    if ($required !== null) {
                        // substitute for bitset_intersection
                        foreach ($required as $key => $value) {
                            if (!isset( $termDocs[$key] )) {
                                unset($required[$key]);
                            }
                        }
                    } else {
                        $required = $termDocs;
                    }
                } elseif ($this->_signs[$termId] === false) {
                    // prohibited
                    // substitute for bitset_union
                    foreach ($termDocs as $key => $value) {
                        $prohibited[$key] = $value;
                    }
                } else {
                    // neither required, nor prohibited
                    // substitute for bitset_union
                    foreach ($termDocs as $key => $value) {
                        $neither[$key] = $value;
                    }
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }

            if ($required === null) {
                $required = $neither;
            }

            foreach ($required as $key=>$value) {
                if (isset( $prohibited[$key] )) {
                    unset($required[$key]);
                }
            }
            $this->_resVector = $required;
        }
    }


    /**
     * Score calculator for conjunction queries (all terms are required)
     *
     * @param integer $docId
     * @param search_Lucene $reader
     * @return float
     */
    public function _conjunctionScore($docId, $reader)
    {
        if ($this->_coord === null) {
            $this->_coord = $reader->getSimilarity()->coord(count($this->_terms),
                                                            count($this->_terms) );
        }

        $score = 0.0;

        foreach ($this->_terms as $termId=>$term) {
            $score += $reader->getSimilarity()->tf(count($this->_termsPositions[$termId][$docId]) ) *
                      $this->_weights[$termId]->getValue() *
                      $reader->norm($docId, $term->field);
        }

        return $score * $this->_coord;
    }


    /**
     * Score calculator for non conjunction queries (not all terms are required)
     *
     * @param integer $docId
     * @param search_Lucene $reader
     * @return float
     */
    public function _nonConjunctionScore($docId, $reader)
    {
        if ($this->_coord === null) {
            $this->_coord = array();

            $maxCoord = 0;
            foreach ($this->_signs as $sign) {
                if ($sign !== false /* not prohibited */) {
                    $maxCoord++;
                }
            }

            for ($count = 0; $count <= $maxCoord; $count++) {
                $this->_coord[$count] = $reader->getSimilarity()->coord($count, $maxCoord);
            }
        }

        $score = 0.0;
        $matchedTerms = 0;
        foreach ($this->_terms as $termId=>$term) {
            // Check if term is
            if ($this->_signs[$termId] !== false &&            // not prohibited
                isset($this->_termsPositions[$termId][$docId]) // matched
               ) {
                $matchedTerms++;
                $score +=
                      $reader->getSimilarity()->tf(count($this->_termsPositions[$termId][$docId]) ) *
                      $this->_weights[$termId]->getValue() *
                      $reader->norm($docId, $term->field);
            }
        }

        return $score * $this->_coord[$matchedTerms];
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param search_Lucene $reader
     * @return float
     */
    public function score($docId, $reader)
    {
        if($this->_resVector === null) {
            if ($this->_signs === null) {
                $this->_calculateConjunctionResult($reader);
            } else {
                $this->_calculateNonConjunctionResult($reader);
            }

            $this->_initWeight($reader);
        }

        if ( (extension_loaded('bitset')) ?
                bitset_in($this->_resVector, $docId) :
                isset($this->_resVector[$docId])  ) {
            if ($this->_signs === null) {
                return $this->_conjunctionScore($docId, $reader);
            } else {
                return $this->_nonConjunctionScore($docId, $reader);
            }
        } else {
            return 0;
        }
    }
}
?>