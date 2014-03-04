<?php

// ----------------------------------------------------------------------------------
// Class: ClientQuery
// ----------------------------------------------------------------------------------

/**
* ClientQuery Object_rap to run a SPARQL Query against a SPARQL server.
*
* @version  $Id: ClientQuery.php 7228 2007-09-27 06:24:51Z kudakwashe $
* @author   Tobias Gauß <tobias.gauss@web.de>
*
* @package sparql
*/

class ClientQuery extends Object_rap {

	var $default  = array();
	var $named    = array();
	var $prefixes = array();
	var $query;

	/**
	* Adds a default graph to the query object.
	*
	* @param String  $default  default graph name
	*/
	function addDefaultGraph($default){
		if(!in_array($this->named,$this->default))
		$this->default[] = $default;
	}
	/**
	* Adds a named graph to the query object.
	*
	* @param String  $default  named graph name
	*/
	function addNamedGraph($named){
		if(!in_array($named,$this->named))
		$this->named[] = $named;
	}
	/**
	* Adds the SPARQL query string to the query object.
	*
	* @param String  $query the query string
	*/
	function query($query){
		$this->query = $query;
	}

}




?>