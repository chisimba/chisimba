<?php
// ----------------------------------------------------------------------------------
// Class: ResBag
// ----------------------------------------------------------------------------------

/**
* This interface defines methods for accessing RDF Bag resources. 
* These methods operate on the RDF statements contained in a model.
*
* @version  $Id: ResBag.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
* @package 	resModel
* @access	public
**/
class ResBag extends ResContainer 
{
	
	/**
    * Constructor
	* You can supply a URI
    *
    * @param string $uri 
	* @access	public
    */		
	function ResBag($uri = null)
	{
		parent::ResContainer($uri);
		$this->containerType=new ResResource(RDF_NAMESPACE_URI.RDF_BAG);
	}
}
?>