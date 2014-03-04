<?php
// ----------------------------------------------------------------------------------
// Class: TriXParser
// ----------------------------------------------------------------------------------

/**
* Temporary implementation of a TriX-Parser (Usable only with PHP > V5)
* Currently, it doesn't support any namespaces and has problems with typed literals.
* So this parser only works with TRIX documents where the default namespace is the TRIX namespace.
*
* @version  $Id$
* @author Daniel Westphal (http://www.d-westphal.de)
*
* @package 	dataset
* @access	public
**/
class TriXParser  
{
	/**
	* Reference to the graphSet
	*
	* @var		GraphSet
	* @access	private
	*/
	var $graphSet;
	
	/**
	* Constructor
	* Needs a reference to a graphSet
	*
	* @param    GraphSet 
	* @access	public
	*/		
	function TriXParser(&$graphSet)
	{
		$this->graphSet=&$graphSet;	
	}

	/**
	* Parse an XML string
	*
	* @param   string
	* @access	public
	*/
	function parseString($string)
	{
		$this->_populateGraphSet(simplexml_load_string($string));
	}
	
	/**
	* Parse from a file
	*
	* @param   string
	* @access	public
	*/
	function parseFile($pathToFile)
	{
		$this->_populateGraphSet(simplexml_load_file($pathToFile));
	}
	
	/**
	* Populates the graphSet with namedGraphs and triples.
	*
	* @param   object simpleXMLModel  $xmlModel
	* @access	private
	*/
	function _populateGraphSet(&$xmlModel)
	{
		$defaultGraphOccurred=false;
		
		foreach ($xmlModel->graph as $graph) 
		{
			if (isset($graph->uri)) 
			{
				$graphName=(string)$graph->uri;
				$namedGraph=& $this->graphSet->getNamedGraph($graphName);
				if ($namedGraph ==null)
					$namedGraph=& $this->graphSet->createGraph($graphName);
			} else 
			{
				if ($defaultGraphOccurred)
					trigger_error('Only one unnamed Graph per file allowed', E_USER_ERROR);
				
				$namedGraph=& $this->graphSet->getDefaultGraph();
				$defaultGraphOccurred=true;
			}	
						
			foreach ($graph->triple as $triple)
			{
				$tripleCount=0;
				$tripleArray=array();
				foreach ($triple->children() as $tag => $value)
				{
					$tripleArray[$tripleCount++]=$this->_element2Resource((string)$tag,$value);
				};
				$namedGraph->add(new Statement($tripleArray[0],$tripleArray[1],$tripleArray[2]));	
			};
		};
	}
	
	/**
	* return a mathing resource tyoe
	*
	* @param   string  
	* @param   object simpleXMLNode $value
	* @access	private
	*/
	function _element2Resource($tag,$value)
	{
		switch ($tag) 
		{
				case 'uri':
					return new Resource((string)$value);
				break;
		
				case 'id':
					return new BlankNode((string)$value);
				break;
				
				case 'typedLiteral':
					$literal=new Literal((string)$value);
					$literal->setDatatype((string)$value['datatype']);
					return $literal;
				break;
					
				case 'plainLiteral':
					$literal=new Literal((string)$value);
					if(isset($value['xml:lang']))
						$literal->setLanguage((string)$value['xml:lang']);
					return $literal;
				break;
		}
	}
}
?>