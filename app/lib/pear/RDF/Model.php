<?php
// ----------------------------------------------------------------------------------
// Class: RDF_Model
// ----------------------------------------------------------------------------------
/**
 * A model is a programming interface to an RDF graph.
 * An RDF graph is a directed labeled graph, as described in http://www.w3.org/TR/rdf-mt/.
 * It can be defined as a set of <S, P, O> triples, where P is a uriref, S is either
 * a uriref or a blank node, and O is either a uriref, a blank node, or a literal.
 *
 * @version V0.7
 * @author Radoslaw Oldakowski <radol@gmx.de>
 * @author Daniel Westphal <mail@d-westphal.de>
 * @package model
 * @access public
 */

class RDF_Model extends RDF_Object
{
    /**
     * Base URI of the Model.
     * Affects creating of new resources and serialization syntax.
     *
     * @var string
     * @access private
     */
    var $baseURI;

    /**
     * Return current baseURI.
     *
     * @return string
     * @access public
     */
    function getBaseURI()
    {
        return $this->baseURI;
    }
 
    /**
     * Load a model from a file containing RDF, N3 or N-Triples.
     * This function recognizes the suffix of the filename (.n3 or .rdf) and
     * calls a suitable parser, if no $type is given as string ("rdf" "n3" "nt");
     * If the model is not empty, the contents of the file is added to this Model_MDB.
     *
     * @param string $filename
     * @param string $type
     * @access public
     */
    function load($filename, $type = null)
    {
        if ((isset($type)) && ($type =='n3') || ($type =='nt')) {
            $parser =& new RDF_N3_Parser();
        } elseif ((isset($type)) && ($type =='rdf')) {
            $parser =& new RDF_Parser();
        } else {
            // create a parser according to the suffix of the filename
            // if there is no suffix assume the file to be XML/RDF
            $suffix = array();
            preg_match("/\.([a-zA-Z0-9_]+)$/", $filename, $suffix);
            if (isset($suffix[1])
                && (strtolower($suffix[1]) == 'n3') || (strtolower($suffix[1]) == 'nt')
            ) {
                $parser =& new RDF_N3_Parser();
            } else {
                $parser =& new RDF_Parser();
            }
        }
        $temp =& $parser->generateModel($filename);
        $this->addModel($temp);
        if ($this->getBaseURI() == null) {
            $this->setBaseURI($temp->getBaseURI());
        }
    }

    /**
     * Adds a statement from another model to this model. 
     * If the statement to be added contains a blankNode with an identifier 
     * already existing in this model, a new blankNode is generated.
     *
     * @param RDF_Object Statement   $statement
     * @access private
     */ 
    function _addStatementFromAnotherModel($statement, &$blankNodes_tmp)
    {
        $subject = $statement->getSubject();
        $object = $statement->getObject();

        if (is_a($subject, 'RDF_BlankNode')) {
            $label = $subject->getLabel();
            if (!array_key_exists($label, $blankNodes_tmp)) {
                if ($this->findFirstMatchingStatement($subject, null, null)
                    || $this->findFirstMatchingStatement(null, null, $subject)
                ) {
                    $blankNodes_tmp[$label] =& RDF_BlankNode::factory($this);
                    $statement->subj = $blankNodes_tmp[$label];
                } else {
                    $blankNodes_tmp[$label] = $subject;
                }
            } else {
                $statement->subj = $blankNodes_tmp[$label];
            }
        }

        if (is_a($object, 'RDF_BlankNode')) {
            $label = $object->getLabel();
            if (!array_key_exists($label, $blankNodes_tmp)) {
                if ($this->findFirstMatchingStatement($object, null, null)
                    || $this->findFirstMatchingStatement(null, null, $object)
                ) {
                    $blankNodes_tmp[$label] =& RDF_BlankNode::factory($this);
                    $statement->obj = $blankNodes_tmp[$label];
                } else {
                    $blankNodes_tmp[$label] = $object;
                }
            } else {
                $statement->obj = $blankNodes_tmp[$label];
            }
        }

        $this->add($statement);
    }
} // end: Model

?>