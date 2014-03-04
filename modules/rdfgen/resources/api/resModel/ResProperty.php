<?php
// ----------------------------------------------------------------------------------
// Class: ResProperty
// ----------------------------------------------------------------------------------

/**
* An RDF Property.
*
*
* @version  $Id: ResProperty.php 320 2006-11-21 09:38:51Z tgauss $
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