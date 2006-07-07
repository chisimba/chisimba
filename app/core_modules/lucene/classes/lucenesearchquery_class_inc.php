<?php
/**
 * Abstract class to handle the search queries
 *
 * @abstract
 * @copyright AVOIR UWC
 * @author Paul Scott
 */
abstract class lucenesearchquery
{

    /**
     * query boost factor
     *
     * @var float
     */
    private $_boost = 1.0;

    /**
     * Query weight
     *
     * @var search_Lucene_Search_Weight
     */
    protected $_weight;


    /**
     * Gets the boost for this clause.  Documents matching
     * this clause will (in addition to the normal weightings) have their score
     * multiplied by boost.   The boost is 1.0 by default.
     *
     * @return float
     */
    public function getBoost()
    {
        return $this->_boost;
    }

    /**
     * Sets the boost for this query clause to $boost.
     *
     * @param float $boost
     */
    public function setBoost($boost)
    {
        $this->_boost = $boost;
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param search_Lucene $reader
     * @return float
     */
    abstract public function score($docId, $reader);

    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param search_Lucene $reader
     * @return search_Lucene_Search_Weight
     */
    abstract protected function _createWeight($reader);

    /**
     * Constructs an initializes a Weight for a query.
     *
     * @param search_Lucene $reader
     */
    protected function _initWeight($reader)
    {
        $this->_weight = $this->_createWeight($reader);
        $sum = $this->_weight->sumOfSquaredWeights();
        $queryNorm = $reader->getSimilarity()->queryNorm($sum);
        $this->_weight->normalize($queryNorm);
    }

}
?>