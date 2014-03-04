<?php

// ----------------------------------------------------------------------------------
// Class: SparqlEngine
// ----------------------------------------------------------------------------------

/**
* Client for querying a sparql server.
*
* @version  $Id: SparqlClient.php 465 2007-06-27 16:47:57Z cweiske $
* @author   Tobias Gauß <tobias.gauss@web.de>
* @license http://www.gnu.org/licenses/lgpl.html LGPL
*
* @package sparql
*/

class SparqlClient extends Object {

	var $server;
	var $output;	

	/**
	* Constructor of SparlClient.
	*
	* @param String  $server   server address.
	*/
	function SparqlClient($server){
		$this->server = $server;
		$this->output = "array";
	}


	/**
	* Sets the output format for a SELECT or ASK query. Possible formats are "xml" for
	* Sparql Query Results XML Format (http://www.w3.org/TR/rdf-sparql-XMLres/) or array
	* for the format described in our SparqlEngine.
	*
	* @param String  $format   the format.
	*/
	function setOutputFormat($format){
		if(strtolower($format)=="xml")
		$this->output = "xml";
		if(strtolower($format)=="array")
		$this->output = "array";

	}

	/**
	* Main function of SparqlClient.
	*
	* @param ClientQuery  $query   the ClientQuery object.
	* @return mixed returns an array that contains the variables an their bindings or a MemModel
	*
	*/
	function query($query){
		if(!is_a($query,"ClientQuery"))
		die;//ErrorHandling

		$url    = $this->_buildurl($query);
		$result = $this->_http_get($url);
		return $this->returnResult($result);

	}


	/**
	* Helper function that builds the url.
	*
	* @param ClientQuery  $query   the ClientQuery Object.
	*/

	function _buildurl($query){
		$url = "";
		$url = $this->server."?query=".urlencode($query->query);
		foreach($query->default as $defaultg){
			$url = $url."&default-graph-uri=".$defaultg;
		}
		foreach($query->named as $namedg){
			$url = $url."&named-graph-uri=".$namedg;
		}

		return $url;

	}

	/**
	* Returns the query result.
	*
	* @param String  $result  the result.
	* @return mixed
	*/
	function returnResult($result){

		if(strpos($result,"<rdf:RDF")){
			include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
			$parser = new RdfParser();
			return $parser->generateModel(substr($result,strpos($result,"<rdf:RDF")));
		}

		if($this->output == "xml"){
			$pos = strpos($result,"<?xml");
			return substr($result,$pos);
		}
		if($this->output == "array"){
			//	$pos = strpos($buffer,"<?xml");
			return $this->parseResult($result);
		}
		return $result;
	}


	function parseResult($buffer){
		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_SPARQLRES);
		$parser = new SparqlResultParser();
		return $parser->parse($buffer);
	}


	/**
	* Executes the GET Request.
	*
	* @param String  $url  the url.
	* @return String result.
	*/
	function _http_get($url)
	{
		$url = parse_url($url);
		$port = isset($url['port']) ? $url['port'] : 80;

		$fp = fsockopen($url['host'], $port);

		$replace = $url['path'];

		fputs($fp, "GET ".$replace."?".$url['query']." HTTP/1.0\n");
		fputs($fp, "Host:". $url['host']." \r\n");
		fputs($fp, "Accept: application/sparql-results+xml, application/rdf+xml\r\n");
		fputs($fp, "Connection: close\n\n");

		$buffer = "";
		while ($tmp = fread($fp, 1024))
		{
			$buffer .= $tmp;
		}

		$pos1 = strpos($buffer,"\r\n\r\n");
		$pos2 = strpos($buffer,"\n\n");
		if ($pos1 === false) {
			$pos = $pos2;
		} else {
			$pos = $pos1;
		}
			
		return substr($buffer,$pos);

	}
}



?>