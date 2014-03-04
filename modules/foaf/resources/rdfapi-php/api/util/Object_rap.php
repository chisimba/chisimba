<?php

// ----------------------------------------------------------------------------------
// Class: Object_rap
// ----------------------------------------------------------------------------------

/**
 * An abstract object.
 * Root object with some general methods, that should be overloaded. 
 * 
 *
 * @version  $Id: Object_rap.php 7228 2007-09-27 06:24:51Z kudakwashe $
 * @author Chris Bizer <chris@bizer.de>
 *
 * @abstract
 * @package utility
 *
 **/
 class Object_rap {

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