<?php
// ----------------------------------------------------------------------------------
// Class: Individual
// ----------------------------------------------------------------------------------



/**
* Interface that encapsulates an individual in an ontology, sometimes referred
* to as a fact or assertion.
* In order to be recognised as an individual, rather than a generic resource, 
* at least one rdf:type statement, referring to a known class, must be present 
* in the model.
*

* @version  $Id: Individual.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	ontModel
* @access	public
**/	

class Individual extends OntResource   
{
	
	/**
    * Constructor
	* You can supply a uri
    *
    * @param string $uri 
	* @access	public
    */		
	function Individual($uri = null)
	{
		parent::OntResource($uri);
	}
} 
?>