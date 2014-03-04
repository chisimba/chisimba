<?php

// ----------------------------------------------------------------------------------
// Class: Object
// ----------------------------------------------------------------------------------

/**
 * An abstract object.
 * Root object with some general methods, that should be overloaded. 
 * 
 *
 * @version  $Id: Object.php 295 2006-06-23 06:45:53Z tgauss $
 * @author Chris Bizer <chris@bizer.de>
 *
 * @abstract
 * @package utility
 *
 **/
 class RDFObject {

  /**
   * Serializes a object into a string
   *
   * @access	public
   * @return	string		
   */    
	function toString() {
    	$objectvars = get_object_vars($this);
		foreach($objectvars as $key => $value) 
			$content .= $key ."='". $value. "'; ";
		return "Instance of " . get_class($this) ."; Properties: ". $content;
	}

 }


?>
