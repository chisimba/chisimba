<?php
require_once RDFAPI_INCLUDE_DIR . 'model/Resource.php';

// ----------------------------------------------------------------------------------
// Class: BlankNode
// ----------------------------------------------------------------------------------


/**
 * An RDF blank node. 
 * In model theory, blank nodes are considered to be drawn from some set of 
 * 'anonymous' entities which have no label but are unique to the graph.
 * For serialization they are labeled with a URI or a _:X identifier.
 * 
 *
 * @version  $Id: Blanknode.php 453 2007-06-20 21:19:09Z cweiske $
 * @authors Chris Bizer <chris@bizer.de>,
 *          Radoslaw Oldakowski <radol@gmx.de>
 *
 * @package model
 * @access	public
 *
 */ 
 class BlankNode extends Resource {
  
   /**
    * Constructor
	* You can supply a label or You supply a model and a unique ID is gernerated.
    *
    * @param	mixed	$namespace_or_uri_or_model
 	* @param 	string $localName
	* @access	public
    */
    function BlankNode($namespace_or_uri_or_model , $localName = NULL) {
		
        if (is_a($namespace_or_uri_or_model, 'Model')) {
			// generate identifier
			$id = $namespace_or_uri_or_model->getUniqueResourceURI(BNODE_PREFIX);
			
			$this->uri = $id;

		} else {
			// set identifier
			if ($localName == NULL) {
				$this->uri = $namespace_or_uri_or_model;
		  	} else {
				$this->uri = $namespace_or_uri_or_model . $localName;
	  	    }
		 }
    }

  /**
   * Returns the ID of the blank node.
   *
   * @return 	string
   * @access	public  
   */	
  function getID() {
  			return $this->uri;
   }

  /**
   * Returns the ID of the blank node.
   *
   * @return 	string
   * @access	public  
   */	
  function getLabel() {
  		return $this->uri;
   }

  /**
   * Dumps bNode.
   *
   * @access	public 
   * @return	string 
   */  
  function toString() {

        return 'bNode("' . $this->uri . '")';
  }
	
  /**
   * Checks if two blank nodes are equal.
   * Two blank nodes are equal, if they have the same temporary ID.
   *
   * @access	public 
   * @param		object	resource $that
   * @return	boolean 
   */  
   function equals ($that) {
	
	    if ($this == $that) {
	      return true;
	    }
        if (($that == NULL) or !(is_a($that, 'BlankNode'))) {
	      return false;
	    }
	    	
		if ($this->getURI() == $that->getURI()) {
	      return true;
	    }
	
	    return false;
	}


    /**
    *   Doing string magic in PHP5
    *   @return string String representation of this Blank Node
    */
    function __toString()
    {
        return $this->toString();
    }

} // end: BlankNode 


?>