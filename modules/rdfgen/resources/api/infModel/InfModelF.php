<?php
// ----------------------------------------------------------------------------------
// Class: InfModelF
// ----------------------------------------------------------------------------------

/**
* A InfModelF extends the InfModel Class, with a forward chaining algorithm.  
* If a new statement is added, it is enferd at 
* once and all the entailed statements are added too. 
* When adding or removing a statement, that produced a new inference rule, 
* all entailed statements are discarded and the whole base model is infered 
* again.
* The InfModelF is safe for loops in Ontologies, that would cause infinite loops.
*
* @version  $Id: InfModelF.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>

*
* @package infModel
* @access	public
**/

class InfModelF extends InfModel 
{

	/**
	* Array that holds the position of the infered statements in the model.
	*
	* @var		array
	* @access	private
	*/	
	var $infPos;
	
	
	/**
	* Variable that influences the habbit when adding statements. 
	* Used by the loadModel method to increase performance.
	*
	* @var		boolean
	* @access	private
	*/
	var $inferenceEnabled;
	
		
   	/**
    * Constructor
	* You can supply a base_uri.
    *
    * @param string $baseURI 
	* @access	public
    */		
	function InfModelF($baseURI = NULL)
	{
		parent::InfModel($baseURI);
		$this->infPos=array();
		$this->inferenceEnabled=true;	
	}

	/**
	* Adds a new triple to the MemModel without checking if the statement 
	* is already in the MemModel.
	* So if you want a duplicate free MemModel use the addWithoutDuplicates() 
	* function (which is slower then add())
	* The statement is infered and all entailed statements are added.
	*
	* @param	object Statement	$statement
	* @access	public
	* @throws	PhpError 
	*/
	function add ($statement)
	{
		parent::add($statement);
		if ($this->inferenceEnabled)
		{
			foreach ($this->entailStatement($statement) as $state)
			{
				//a addWithoutDublicates construct
				if(!$this->contains($state))
				{
			
					parent::add($state);
					//save the position of the infered statements
					end($this->triples);
					$this->infPos[]=key($this->triples);
				};
			}; 
		//apply the complete inference to the model, if the added statement was able to add a rule
		if (in_array($statement->getLabelPredicate(),$this->supportedInference))
			$this->applyInference();
		}
	}
	
 
	/**
	* Checks if a new statement is already in the MemModel and adds 
	* the statement, if it is not in the MemModel.
	* addWithoutDuplicates() is significantly slower then add(). 
	* Retruns TRUE if the statement is added.
	* FALSE otherwise.
	* The statement is infered and all entailed statements are added.
	*
	* @param	object Statement	$statement
	* @return   boolean
	* @access	public
	* @throws	PhpError 
	*/	
	 function addWithoutDuplicates(& $statement) 
	 {
		if(!$this->contains($statement))
		{
		 	parent::add($statement);
		 	if ($this->inferenceEnabled)
		 	{
				foreach ($this->entailStatement($statement) as $statement)
				{
					if(!$this->contains($statement))
						{
							parent::add($statement);
							//save the position of the infered statements
							end($this->triples);
							$this->infPos[]=key($this->triples);
						};
				};
			if (in_array($statement->getLabelPredicate(),$this->supportedInference))
				$this->applyInference();
		 	}
			return true;
		 }
		 return false;
	 }

	

	/**
	* Entails every statement and adds the entailments if not already 
	* in the model.
	*
	* @access	private
	*/		
	function applyInference()
	{
		//check every statement in the model
		foreach ($this->triples as $statement)
		{
			//gat all statements, that it recursively entails
			foreach ($this->entailStatement($statement) as $statement)
			{
				if (!$this->contains($statement))
				{
					parent::add($statement);
					//add the InfStatement position to the index
					end($this->triples);
					$this->infPos[]=key($this->triples);
				};
			};
		};
	}
	
 
	/**
	* Entails a statement by recursively using the _entailStatementRec 
	* method.
	* 
	* @param	object Statement	$statement
	* @return   array of statements
	* @access	public
	*/			
	function entailStatement (& $statement)
	{
		$infStatementsIndex=array();
		return $this->_entailStatementRec($statement,$infStatementsIndex);
	}

	/**
	* Recursive method, that checks the statement with the trigger of 
	* every rule. If the trigger matches and entails new statements, 
	* those statements are recursively infered too.
	* The $infStatementsIndex array holds lready infered statements 
	* to prevent infinite loops.
	*
	* 
	* @param	object Statement $statement
	* @param	array $infStatementsIndex
	* @return   array of statements
	* @access	private
	*/
	function _entailStatementRec ( $statement,& $infStatementsIndex)
	{
		$infStatements = array();
		$return = array();
		
		//dont entail statements about the supported inference-schema
		if (!in_array($statement->getLabelPredicate(),$this->supportedInference))
		{
			//check only the rules, that were returned by the index
			foreach ($this->_findRuleTriggerInIndex($statement) as $key )
			{
				$infRule=$this->infRules[$key];
	
				$stateString=$key.serialize($statement);
				//If the statement wasn't infered before
				if (!in_array($stateString,$infStatementsIndex))
				{
					$infStatementsIndex[]=$stateString;
					//Check, if the Statements triggers this rule
					if($infRule->checkTrigger($statement))
					{
						$infStatement=$infRule->entail($statement);
						#if(!$this->contains($infStatement))
						{
							$return[]=$infStatement;
							$return=array_merge($return,
												$this->_entailStatementRec($infStatement, 
																			$infStatementsIndex));
						};	
																		
					};
				};
			};
		};	
		return $return;
	}
	
	/**
	* Removes all infered statements from the model but keeps the 
	* infernece rules.
	*
	* @access	public
	*/	 
	function removeInfered()
	{
		$indexTmp=$this->indexed;
		$this->index(-1);
		foreach ($this->infPos as $key)
		{
			unset($this->triples[$key]);
		};
		$this->infPos=array();
		$this->index($indexTmp);
	}	
	 
	 
	/**
	* Load a model from a file containing RDF, N3 or N-Triples.
	* This function recognizes the suffix of the filename (.n3 or .rdf) and
	* calls a suitable parser, if no $type is given as string 
	* ("rdf" "n3" "nt");
	* If the model is not empty, the contents of the file is added to 
	* this DbModel.
	*
	* While loading the model, the inference entailing is disabled, but 
	* new inference rules are added to increase performance.
	* 
	* @param 	string 	$filename
	* @param 	string 	$type
	* @access	public
	*/
	function load($filename, $type = NULL) 
	{
		//Disable entailing to increase performance
	 	$this->inferenceEnabled=false;
	 	parent::load($filename, $type);
	 	//Enable entailing
	 	$this->inferenceEnabled=true;
	 	//Entail all statements
	 	$this->applyInference();
 	}
	
	/**
	* Short Dump of the InfModelF.
	*
	* @access	public 
	* @return	string 
	*/  
	function toString() {
	   return 'InfModelF[baseURI=' . $this->getBaseURI() . ';  
	   			size=' . $this->size(true) . ']';
	}
		
	/**
	* Create a MemModel containing all the triples (including inferred 
	* statements) of the current InfModelF.
	*
	* @return object MemModel
	* @access public
	*/
	function & getMemModel() 
	{
		$return= new MemModel();
		$return->setBaseURI($this->baseURI);
		foreach ($this->triples as $statement)
			$return->add($statement);
		
		$return->addParsedNamespaces($this->getParsedNamespaces());	
		return $return;
	}
	  
	/**
	* Create a MemModel containing only the base triples 
	* (without inferred statements) of the current InfModelF.
	*
	* @return object MemModel
	* @access public
	*/
	function  getBaseMemModel() 
	{
		$return= new MemModel();
		$return->setBaseURI($this->baseURI);
		foreach ($this->triples as $key => $statement)
			if (!in_array($key,$this->infPos))
				$return->add($statement);
		$retun->addParsedNamespaces($this->getParsedNamespaces());
		return $return;
	}
	
	
	/**
	* Removes the triple from the MemModel. 
	* TRUE if the triple is removed.
	* FALSE otherwise.
	*
	* Checks, if it touches any statements, that added inference rules 
	* to the model 
	*
	* @param	object Statement	$statement
	* @return   boolean
	* @access	public
	* @throws	PhpError
	*/
	function remove($statement)
	{
		//If the statement is in the model
		if($this->contains($statement))
		{
			$inferenceRulesWereTouched=false;
			//If the statement was able to add inference rules
			if (in_array($statement->getLabelPredicate(),$this->supportedInference))
			{
				$statementPositions=$this->_removeFromInference($statement);
				$inferenceRulesWereTouched=true;
			} else 
			//get the position of all matching statements
			{
				$statementPositions=array();
				//find the positions of the statements
				$statementPosition=-1;
				do
				{
					
					$statementPosition =
					$this->findFirstMatchOff($statement->getSubject(),
												$statement->getPredicate(),
												$statement->getObject(),
												$statementPosition+1);
												
					if ($statementPosition!=-1)
						$statementPositions[]=$statementPosition;							
												
				}	while ($statementPosition != -1);			
			}
			
			//remove matching statements
			parent::remove($statement);
			foreach ($statementPositions as $statementPosition)
			{
				//if the statement was infered, remove it from the index of the infered statements.
				if (in_array($statementPosition,$this->infPos))
					unset ($this->infPos[$statementPosition]);
			}
			if ($inferenceRulesWereTouched)
			{
				//remove the statement and re-entail the model
				$this->removeInfered();
				$this->applyInference();
			}
			return true;
		} else 
		{
			return false;	
		}
	}
	
	/** 
	* Adds another model to this MemModel.
	* Duplicate statements are not removed. 
	* If you don't want duplicates, use unite().
	* If any statement of the model to be added to this model contains a blankNode 
	* with an identifier already existing in this model, a new blankNode is generated.
	*
	* @param	object Model	$model 
	* @access	public
	* @throws phpErrpr
	*
	*/
	function addModel(&$model)  
	{
		//Disable entailing to increase performance
	 	$this->inferenceEnabled=false;
	 	parent::addModel($model);
	 	//Enable entailing
	 	$this->inferenceEnabled=true;
	 	//Entail all statements
	 	$this->applyInference();
	}
};
?>