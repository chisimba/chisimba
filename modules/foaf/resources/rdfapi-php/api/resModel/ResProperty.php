<?php
// ----------------------------------------------------------------------------------
// Class: ResProperty
// ----------------------------------------------------------------------------------

/**
* An RDF Property.
*
*
* @version  $Id: ResProperty.php 7228 2007-09-27 06:24:51Z kudakwashe $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package resModel
* @access	public
**/
class ResProperty extends ResResource  
{
	
	/**
    * Constructor
	* You can supply a URI
    *
    * @param string $uri 
	* @access	public
    */	
	function ResProperty($uri)
	{
		parent::ResResource($uri);
	}
} 
?>