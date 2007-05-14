<?php
// ----------------------------------------------------------------------------------
// Class: RDF_BlankNode
// ----------------------------------------------------------------------------------
/**
 * An RDF blank node.
 * In model theory, blank nodes are considered to be drawn from some set of
 * 'anonymous' entities which have no label but are unique to the graph.
 * For serialization they are labeled with a URI or a _:X identifier.
 *
 * @version V0.7
 * @authors Chris Bizer <chris@bizer.de>,
 *           Radoslaw Oldakowski <radol@gmx.de>
 * @package model
 * @todo nothing
 * @access public
 */

require_once 'RDF/Resource.php';

class RDF_BlankNode extends RDF_Resource
{
    /**
     * You can supply a label or You supply a model and a unique ID is gernerated
     *
     * @param  mixed  $namespace_or_uri_or_model
     * @param  string $localName
     * @access public
     * @todo   nothing
     */
    function factory($namespace_or_uri_or_model , $localName = null)
    {
        $blanknode = new RDF_BlankNode;
        if (is_a($namespace_or_uri_or_model, 'RDF_Model')) {
            // generate identifier
            $id = $namespace_or_uri_or_model->getUniqueResourceURI(RDF_BNODE_PREFIX);

            $blanknode->uri = $id;
        } else {
            // set identifier
            if ($localName == null) {
                $blanknode->uri = $namespace_or_uri_or_model;
            } else {
                $blanknode->uri = $namespace_or_uri_or_model . $localName;
            }
        }
        return $blanknode;
    }

    /**
     * Returns the ID of the blank node.
     *
     * @return string
     * @access public
     */
    function getID()
    {
        return $this->uri;
    }

    /**
     * Returns the ID of the blank node.
     *
     * @return string
     * @access public
     */
    function getLabel()
    {
        return $this->uri;
    }

    /**
     * Dumps bNode.
     *
     * @access public
     * @return string
     */
    function toString()
    {
        return 'bNode("' . $this->uri . '")';
    }

    /**
     * Checks if two blank nodes are equal.
     * Two blank nodes are equal, if they have the same temporary ID
     *
     * @access public
     * @param object resource $that
     * @return boolean
     */
    function equals ($that)
    {
        if ($this == $that) {
            return true;
        }
        if (($that == null) or !(is_a($that, 'RDF_BlankNode'))) {
            return false;
        }

        if ($this->getURI() == $that->getURI()) {
            return true;
        }

        return false;
    }
} // end: BlankNode
?>