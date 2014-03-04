<?php
require_once RDFAPI_INCLUDE_DIR . 'model/Node.php';

// ----------------------------------------------------------------------------------
// Class: Resource
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
 *
 * @version  $Id: Resource.php 453 2007-06-20 21:19:09Z cweiske $
 * @author Chris Bizer <chris@bizer.de>
 *
 * @package model
 * @access	public
 *
 */
 class Resource extends Node {

 	/**
	* URIref to the resource
	* @var		string
	* @access	private
	*/
    var $uri;


   /**
    * Constructor
	* Takes an URI or a namespace/localname combination
    *
    * @param	string	$namespace_or_uri
 	* @param string $localName
	* @access	public
    */
    function Resource($namespace_or_uri , $localName = NULL) {
		if ($localName == NULL) {
			$this->uri = $namespace_or_uri;
	  	} else {
			$this->uri = $namespace_or_uri . $localName;
  	    }
	}


  /**
   * Returns the URI of the resource.
   * @return string
   * @access	public
   */
  function getURI() {
  			return $this->uri;
   }

	/**
	 * Returns the label of the resource, which is the URI of the resource.
     * @access	public
	 * @return string
	 */
    function getLabel() {
    	return $this->getURI();
    }

  /**
   * Returns the namespace of the resource. May return null.
   * @access	public
   * @return string
   */
  function getNamespace() {
  	// Import Package Utility
   	include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);

   	return RDFUtil::guessNamespace($this->uri);
  }

  /**
   * Returns the local name of the resource.
   * @access	public
   * @return string
   */
    function getLocalName() {
    	// Import Package Utility
   		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);

	    return RDFUtil::guessName($this->uri);
  	}

  /**
   * Dumps resource.
   * @access	public
   * @return string
   */
  function toString() {
	return 'Resource("' . $this->uri .'")';
  }

  /**
   * Checks if the resource equals another resource.
   * Two resources are equal, if they have the same URI
   *
   * @access	public
   * @param		object	resource $that
   * @return	boolean
   */
   function equals ($that) {

	    if ($this == $that) {
	      return true;
	    }

	    if (($that == NULL) or !(is_a($that, 'Resource')) or (is_a($that, 'BlankNode'))) {
	      return false;
	    }

		if ($this->getURI() == $that->getURI()) {
	      return true;
	    }

	    return false;
	}




    /**
    *   Doing string magic in PHP5
    *   @return string String representation of this Resource
    */
    function __toString()
    {
        return $this->toString();
    }

}

?>