<?php
require_once RDFAPI_INCLUDE_DIR . 'model/MemModel.php';
// ----------------------------------------------------------------------------------
// Class: NamedGraphMem
// ----------------------------------------------------------------------------------

/**
* NamedGraph implementation that extends a {@link MemModel}
* with a name.
*
* @version  $Id$
* @author Daniel Westphal <http://www.d-westphal.de>
*
* @package 	dataset
* @access	public
**/
class NamedGraphMem extends MemModel
{

	/**
	* Name of the NamedGraphMem.
	*
	* @var		 string
	* @access	private
	*/
	var $graphName;


	/**
    * Constructor
    * You have to supply a graph name. You can supply a URI.
    *
    * @param  string
    * @param  string
	* @access	public
    */
	function NamedGraphMem($graphName,$baseURI = null)
	{
		$this->setBaseURI($baseURI);
		$this->indexed = INDEX_TYPE;
		$this->setGraphName($graphName);
	}

	/**
    * Sets the graph name.
    *
    * @param  string
	* @access	public
    */
	function setGraphName($graphName)
	{
		$this->graphName=$graphName;
	}

	/**
    * Returns the graph name.
    *
    * @return string
	* @access	public
    */
	function getGraphName()
	{
		return $this->graphName;
	}
}
?>