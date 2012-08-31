<?php
// ----------------------------------------------------------------------------------
// Class: RDF_Resource
// ----------------------------------------------------------------------------------
/**
 * An RDF resource.
 * Every RDF resource must have a URIref.
 * URIrefs are treated as logical constants, i.e. as names which denote something
 * (the things are called 'resources', but no assumptions are made about the nature of resources.)
 * Many RDF resources are pieces of vocabulary. They typically have a namespace
 * and a local name. In this case, a URI is composed as a
 * concatenation of the namespace and the local name.
 *
 * @version V0.7
 * @author Chris Bizer <chris@bizer.de>
 * @package model
 * @todo nothing
 * @access public
 */

require_once 'RDF/Node.php';

class RDF_Resource extends RDF_Node
{
    /**
     * URIref to the resource
     *
     * @var string
     * @access private
     */
    var $uri;

    /**
     * Takes an URI or a namespace/localname combination
     *
     * @param string $namespace_or_uri
     * @param string $localName
     * @access public
     */
    function factory($namespace_or_uri , $localName = null)
    {
        $resource = new RDF_Resource;
        if ($localName == null) {
            $resource->uri = $namespace_or_uri;
        } else {
            $resource->uri = $namespace_or_uri . $localName;
        }
        return $resource;
    }

    /**
     * Returns the URI of the resource.
     *
     * @return string
     * @access public
     */
    function getURI()
    {
        return $this->uri;
    }

    /**
     * Returns the label of the resource, which is the URI of the resource.
     *
     * @access public
     * @return string
     */
    function getLabel()
    {
        return $this->getURI();
    }

    /**
     * Returns the namespace of the resource. May return null.
     *
     * @access public
     * @return string
     */
    function getNamespace()
    {
        return RDF_guessNamespace($this->uri);
    }

    /**
     * Returns the local name of the resource.
     *
     * @access public
     * @return string
     */
    function getLocalName()
    {
        return RDF_guessName($this->uri);
    }

    /**
     * Dumps resource.
     *
     * @access public
     * @return string
     */
    function toString()
    {
        return 'Resource("' . $this->uri . '")';
    }

    /**
     * Checks if the resource equals another resource.
     * Two resources are equal, if they have the same URI
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
        if (($that == null) or !(is_a($that, 'RDF_Resource')) or (is_a($that, 'RDF_BlankNode'))) {
            return false;
        }

        if ($this->getURI() == $that->getURI()) {
            return true;
        }

        return false;
    }
}

?>