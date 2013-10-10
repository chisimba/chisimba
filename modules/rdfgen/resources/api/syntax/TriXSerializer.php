<?php
// ----------------------------------------------------------------------------------
// Class: TriXSerializer
// ----------------------------------------------------------------------------------

/**
* Temporary implementation of a TriX-Serializer
* 
* @version  $Id$
* @author Daniel Westphal (http://www.d-westphal.de)
*
* @package 	dataset
* @access	public
**/
class TriXSerializer  
{
	
	/**
	* Reference to the graphSet
	*
	* @var		object GraphSet
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
	function TriXSerializer(&$graphSet)
	{
		$this->graphSet=&$graphSet;
	}

	/**
	* Serialize the dataset to a TriX string
	*
	* @return   string
	* @access	public
	*/
	function & serializeToString()
	{
		return $this->_serialize();
	}
	
	/**
	* Serialize the dataset to a TriX string and save in file
	*
	* @param   string
	* @access	public
	*/
	function serializeToFile($fileName)
	{
		$serializedString=&$this->_serialize();
		$handle = fopen($fileName, 'w');
	   	fwrite($handle, $serializedString);
		fclose($handle);
	}
	
	
	/**
	* Serialize the dataset to a TriX string
	*
	* @return   string
	* @access	private
	*/
	function & _serialize()
	{
		//Trix header
		$serializedString=
			'<?xml version="1.0" encoding="utf-8"?>'.
			'<TriX xmlns="http://www.w3.org/2004/03/trix/trix-1/">';
		
		//serialize defaultGraph if it is not empty
		$defaultGraph=& $this->graphSet->getDefaultGraph();
		if ($defaultGraph->isEmpty()===false)
		{
			$serializedString.='<graph>';
			for($iterator = $this->graphSet->findInDefaultGraph(null,null,null); $iterator->valid(); $iterator->next()) 
			{
				$serializedString.='<triple>';
				
				$statement=$iterator->current();
				
				$serializedString.=$this->_node2string($statement->getSubject());
				$serializedString.=$this->_node2string($statement->getPredicate());
				$serializedString.=$this->_node2string($statement->getObject());
				
				$serializedString.='</triple>';
			};
			$serializedString.='</graph>';
		}		
			
		//serialize namedGraphs	
		foreach ($this->graphSet->listGraphNames() as $graphName)
		{
			$serializedString.='<graph>';
			$serializedString.='<uri>'.$graphName.'</uri>';
			for($iterator = $this->graphSet->findInNamedGraphs(new Resource($graphName),null,null,null); $iterator->valid(); $iterator->next()) 
			{
				$serializedString.='<triple>';
				
				$statement=$iterator->current();
				
				$serializedString.=$this->_node2string($statement->getSubject());
				$serializedString.=$this->_node2string($statement->getPredicate());
				$serializedString.=$this->_node2string($statement->getObject());
				
				$serializedString.='</triple>';
			};
			$serializedString.='</graph>';
		};		
		//TriX footer	
		$serializedString.='</TriX>';	
		return $serializedString;
	}
	
	/**
	* Serialize node to a TriX string
	*
	* @param Node
	* @return   string
	* @access	private
	*/
	function _node2string($node)
	{
		switch ($node)
		{
			case (is_a($node,'BlankNode')):
				return ('<id>'.$node->getLabel().'</id>');
			
			case (is_a($node,'Resource')):
				return ('<uri>'.$node->getLabel().'</uri>');
			
			case (is_a($node,'Literal')):
			
				if ($node->dtype!=null)
					return ('<typedLiteral datatype="'.htmlentities($node->dtype).'">'.$node->getLabel().'</typedLiteral>');

				if ($node->lang!=null)
					return ('<plainLiteral xml:lang="'.htmlentities($node->lang).'">'.$node->getLabel().'</plainLiteral>');
					
				return ('<plainLiteral>'.htmlentities($node->getLabel()).'</plainLiteral>');
		}
	}
}
?>