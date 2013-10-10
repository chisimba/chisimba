<?php
// ----------------------------------------------------------------------------------
// Class: InfModelB
// ----------------------------------------------------------------------------------

/**
* A InfModelB extends the InfModel Class, with a backward chaining algorithm. 
* Only the loaded or added base-triples are stored. 
* A find-query evaluates the inference rules and recursively tries to find the statements.
* InfModelB memorises "Dead-Ends" until the next add() command, thus 
* makin a second find much faster.
* InfModelB is safe for loops in Ontologies, that would cause infinite loops.
* WARNING: A find(null,null,null) might take very long.
*
* @version  $Id: InfModelB.php 290 2006-06-22 12:23:24Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>

*
* @package infModel
* @access	public
**/
 
class InfModelB extends InfModel 
{

	/**
	* Array that holds combinations of inference rules with distinct 
	* find-querys, that don't lead to any inference.
	*
	* @var		array
	* @access	private
	*/
 	var $findDeadEnds;
 	
 		
   	/**
    * Constructor
	* You can supply a base_uri
    *
    * @param string $baseURI 
	* @access	public
    */	
 	function InfModelB($baseURI = null)
 	{
 		parent::InfModel($baseURI);
 		$this->findDeadEnds=array();	
 	}
	
	/**
	* Adds a new triple to the Model without checking, if the statement 
	* is already in the Model. So if you want a duplicate free Model use 
	* the addWithoutDuplicates() function (which is slower then add())
	*
	* @param	object Statement	$statement
	* @access	public
	* @throws	PhpError 
	*/ 	
 	function add($statement)
 	{
 		parent::add($statement);
 		//Reset the found dead-ends.
 		$this->findDeadEnds=array();	
 	}
 	
	/**
	* General method to search for triples.
	* NULL input for any parameter will match anything.
	* Example:  $result = $m->find( NULL, NULL, $node );
	* Finds all triples with $node as object.
	* Returns an empty MemModel if nothing is found.
	* To improve the search speed with big Models, call index(INDEX_TYPE) 
	* before seaching.
 	*
	* It recursively searches in the statements and rules to find 
	* matching statements.
	*
	* @param	object Node	$subject
	* @param	object Node	$predicate
	* @param	object Node	$object
	* @return	object MemModel
	* @access	public
	* @throws	PhpError
	*/
    function find($subject,$predicate,$object) 
    {
    	$searchStringIndex=array();
    	$resultModel=new MemModel();
    	
    	//add all infered statements without duplicates to the result model
    	foreach ($this->_infFind($subject,$predicate,$object,array())as $statement)
    	{
    		$resultModel->addWithoutDuplicates($statement);	
    	};
    	return $resultModel;
    }

	/**
 	* This is the main inference method of the InfModelB
 	* The algorithm works as follows:
 	* Find all statements in the base model, that matches the current 
 	* find-query.
 	* Check all rules, if they are able to deliver infered statements, 
 	* that match the current find-query. Don't use rules with queries,
 	* that lead to dead-ends and don't use a rule-query-combination that 
 	* was used before in this branch (ontology loops).
 	* If a rule is possible do deliver such statements, get a new 
 	* find-query, that is possible to find those statements, that are able
 	* to trigger this rule.
 	* Call this _infFind method wirh the new find-query and entail the 
 	* resulting statements.
 	* If this rule, wasn't able to return any statements with this distinct 
 	* query, add this combination to the dead-ends.
 	* Return the statements from the base triples and those, which were infered.
 	*
 	* If $findOnlyFirstMatching is set to true, only the first match in 
 	* the base-statements is entailed an returned (used in contains() and 
 	* findFirstMatchingStatement() methods).
 	*
 	* You can set an offset to look for the first matching statement by setting the 
 	* $offset var.
 	* 
	* It recursively searches in the statements and rules to find matching
	* statements
	*
	* @param	object Node	$subject
	* @param	object Node	$predicate
	* @param	object Node	$object
	* @param	array		$searchStringIndex
	* @param	boolean 	$findOnlyFirstMatching
	* @param	integer 	$offset
	* @param	integer 	$resultCount
	* @return	object array Statements
	* @access	private
	*/
    function _infFind ($subject,$predicate,$object, $searchStringIndex, $findOnlyFirstMatching = false, $offset = 0,$resultCount = 0 )
    {
    	$return=array();
    	//Find all matching statements in the base statements
    	$findResult=parent::find($subject,$predicate,$object);
    	//For all found statements
		foreach ($findResult->triples as $statement)
			{
				$return[]=$statement;
				$resultCount++;

				//Return, if only the firstMatchingStatement was wanted
				if ($findOnlyFirstMatching && $resultCount > $offset)
					return $return;
			};
			
		//Don't infer statements about the schema (rdfs:subClass, etc..)
		//is false
    	if ($predicate == null || 
    		(is_a($predicate,'Node') && 
    		!in_array($predicate->getLabel(),$this->supportedInference))
    		)
    		//Check only Rules, that the EntailmentIndex returned.
       		foreach ($this->_findRuleEntailmentInIndex($subject,$predicate,$object) as $ruleKey)
    		{
	    		$infRule=$this->infRules[$ruleKey];
				$serializedRuleStatement=$ruleKey.serialize($subject).serialize($predicate).serialize($object);
	    		//If it is to ontology loop and no dead-end
	    		if (!in_array($serializedRuleStatement, $searchStringIndex) && 
	    			!in_array($serializedRuleStatement, $this->findDeadEnds))
				{	
					//Keep this distinct rule query cobination for 
					//this branch to detect loops
	    			$searchStringIndex[]=$serializedRuleStatement;	
					
	    			//If the rule is able to deliver statements that match 
	    			//this query
		    		if ($infRule->checkEntailment($subject,$predicate,$object))
		    		{
		    			//Get a modified find-query, that matches statements, 
		    			//that trigger this rule
		    			$modefiedFind=$infRule->getModifiedFind($subject,$predicate,$object);
		    			//Call this method with the new find-query
		    			$infFindResult=$this->_infFind($modefiedFind['s'],
		    											$modefiedFind['p'],
		    											$modefiedFind['o'], 
		    											$searchStringIndex, 
		    											$findOnlyFirstMatching,
		    											$offset,
		    											$resultCount) ;
						//If it deliverd statements that matches the trigger
		    			if (isset($infFindResult[0]))
						{
							foreach ($infFindResult as $statement)
			    			{	
			    				//Entail the statements and check, if they are not about the 
			    				//ontology
			    				$newStatement=$infRule->entail($statement);
			    				if (!in_array($newStatement->getLabelPredicate(),$this->supportedInference))
		    						//Check if, the entailed statements are, what we are looking for
				    				if($this->_nodeEqualsFind($subject,$newStatement->getSubject()) && 
				    					$this->_nodeEqualsFind($predicate,$newStatement->getPredicate()) && 
				    					$this->_nodeEqualsFind($object,$newStatement->getObject() ) )
				    				{
				    					//Add to results
			    						$return[]=$newStatement;
			    						$resultCount++;
			    						
			    						//or return at once
			    						if ($findOnlyFirstMatching && $resultCount > $offset)
											return $return;
				    				}
					   		}
						} else 
						{
							//If there were no results of the rule-query-combination, 
							//mark this combination as a dead-end.
							$this->findDeadEnds[]=$serializedRuleStatement;
						}
		    		}
	    		}	
    	}
    	//Return the array of the found statements
    	return $return;	
    }
    
 	/**
	* Tests if the Model contains the given triple.
	* TRUE if the triple belongs to the Model;
	* FALSE otherwise.
	* 
	* @param	object Statement	&$statement
	* @return	boolean
	* @access	public
	*/
	function contains(&$statement) 
	{
	//throws an error, if $statement is not of class Statement
	if(!is_a($statement,'Statement'))
		trigger_error(RDFAPI_ERROR . '(class: InfModelB; method: contains): 
			$statement has to be object of class Statement', E_USER_ERROR);	
	
	//Call the _infFind method, but let it stop, if it finds the first match.	
	if (count( $this->_infFind($statement->getSubject(),
								$statement->getPredicate(),
								$statement->getObject(),
								array(),true) ) >0)
		{
			return true;
		} else 
		{
			return false;
		};
	}
    
  /**
   * Searches for triples and returns the first matching statement.
   * NULL input for any parameter will match anything.
   * Example:  $result = $m->findFirstMatchingStatement( NULL, NULL, $node );
   * Returns the first statement of the MemModel where the object equals $node.
   * Returns an NULL if nothing is found.
   * You can define an offset to search for. Default = 0
   *
   * @param	object Node	$subject
   * @param	object Node	$predicate
   * @param	object Node	$object
   * @param	integer	$offset
   * @return	object Statement      
   * @access	public
   */
   function findFirstMatchingStatement($subject, $predicate, $object, $offset = 0) 
	{
		//Call the _infFind method, but let it stop, if it finds the 
		//first match.	
	   	$res= $this->_infFind($subject,$predicate,$object,array(),true,$offset);
	   		
		if (isset($res[$offset]))
		{
			return $res[$offset];
		} else 
		{
			return NULL;
		};
	}  

	/**
	* Returns a StatementIterator for traversing the Model.
	*
	* @access	public 
	* @return	object	StatementIterator
	*/  
	function & getStatementIterator() 
	{
		// Import Package Utility
			include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);
		// Gets a MemModel by executing a find(null,null,null) to get a 
		//inferable statements.	
		// WARNING: might be slow
		return new StatementIterator($this->getMemModel());
	}	
	
	/**
	* Number of all inferable triples in the Model.
	* WARNING: uses a find(null,null,null) to find all statements! (might take a while)
	* 
	* @param 	boolean
	* @return	integer
	* @access	public
	*/
	function size()
	{
		// Gets a MemModel by executing a find(null,null,null) to get a 
		//inferable statements.	
		// WARNING: might be slow
	   	$res = $this->getMemModel();
	   	return $res->size();
	}
    	
	/**
	* Create a MemModel containing all the triples (including inferred 
	* statements) of the current InfModelB.
	*
	* @return object MemModel
	* @access public
	*/
	function & getMemModel() 
	{
		
		$return=$this->find(null,null,null);
		$return->setBaseURI($this->baseURI);
		$return->addParsedNamespaces($this->getParsedNamespaces());
		return $return;
	}

	/**
	* Create a MemModel containing only the base triples (without inferred 
	* statements) of the current InfModelB.
	*
	* @return object MemModel
	* @access public
	*/
	function & getBaseMemModel() 
	{
		$return= new MemModel();
		$return->setBaseURI($this->baseURI);
		foreach ($this->triples as $statement)
			$return->add($statement);
			
		$retun->addParsedNamespaces($this->getParsedNamespaces());
		return $return;
	}

	/**
	* Short Dump of the  InfModelB.
	*
	* @access	public 
	* @return	string 
	*/  
	function toString() 
	{
	   return 'InfModelB[baseURI=' . $this->getBaseURI() . ';  size=' . $this->size(true) . ']';
	}

	/**
	* Dumps of the InfModelB including ALL inferable triples.
	*
	* @access	public 
	* @return	string 
	*/  
	function toStringIncludingTriples() 
	{
	   	$dump = $this->toString() . chr(13);
	   	$stateIt=new StatementIterator($this->find(null,null,null));
		while($statement=$stateIt->next())
		{
	   		$dump .= $statement->toString() . chr(13);
	   	}
	   	return $dump;
	}  

	/**
	* Saves the RDF,N3 or N-Triple serialization of the full InfModelB 
	* (including inferred triples) to a file.
	* You can decide to which format the model should be serialized by 
	* using a corresponding suffix-string as $type parameter. If no $type 
	* parameter is placed this method will serialize the model to XML/RDF 
	* format.
	* Returns FALSE if the InfModelB couldn't be saved to the file.
	*
	* @access	public 
	* @param 	string 	$filename
	* @param 	string 	$type
	* @throws   PhpError
	* @return	boolean   
	*/  
	function saveAs($filename, $type ='rdf') 
	{
	
		$memmodel=$this->getMemModel();
		return $memmodel->saveAs($filename, $type);
	} 

	/**
	* Writes the RDF serialization of the Model including ALL inferable 
	* triples as HTML.
	*
	* @access	public 
	*/  
	function writeAsHtml() 
	{
			require_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
		$ser = new RdfSerializer();
	    $rdf =& $ser->serialize($this->getMemModel());
		$rdf = htmlspecialchars($rdf, ENT_QUOTES);
		$rdf = str_replace(' ', '&nbsp;', $rdf);
		$rdf = nl2br($rdf);
		echo $rdf; 
	}  

	/**
	* Writes the RDF serialization of the Model including ALL inferable 
	* triples as HTML table.
	*
	* @access	public 
	*/  
	function writeAsHtmlTable() 
	{
			// Import Package Utility
			include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);   			
		RDFUtil::writeHTMLTable($this->getMemModel());
	}  
	
	
	/**
	* Writes the RDF serialization of the Model including ALL inferable 
	* triples.
	*
	* @access	public 
	* @return	string 
	*/  
	function writeRdfToString() 
	{
	   	// Import Package Syntax
		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);   		
		$ser = new RdfSerializer();
	    $rdf =& $ser->serialize($this->getMemModel());
		return $rdf;
	}    

	/**
	* Removes the triple from the MemModel. 
	* TRUE if the triple is removed.
	* FALSE otherwise.
	*
	* Checks, if it touches any statements, that added inference rules 
	* to the model.
	*
	* @param	object Statement	$statement
	* @return   boolean
	* @access	public
	* @throws	PhpError
	*/
	function remove($statement)
	{
		if (parent::contains($statement))
		{
			if (in_array($statement->getLabelPredicate(),$this->supportedInference));
				while (count($this->_removeFromInference($statement))>0);
				
			$this->findDeadEnds=array();
			return parent::remove($statement);
		} else 
		{
			return false;
		}
	}

	/**
	* Checks, if a single node matches a single find pattern.
	* TRUE if the node matches.
	* FALSE otherwise.
	*
	* Checks, if it touches any statements, that added inference rules 
	* to the model. 
	*
	* @param	object Statement	$statement
	* @return   boolean
	* @access	private
	*/
	function _nodeEqualsFind(& $find, $node)
	{
		//If the find pattern is a node, use the nodes equal-method and 
		//return the result.
		if (is_a($find,'Node'))
			return $node->equals($find);
		
		//Null-pattern matches anything.
		if ($find == null)
		{
			return true;
		} else 
		{
			return false;
		}
	}
	
	/**
	* Returns a FindIterator for traversing the MemModel.
	* Disabled in InfModelB.
	*
	* @access	public 
	* @return	object	FindIterator
	*/  
	function & findAsIterator($sub=null,$pred=null,$obj=null) {
		$errmsg = RDFAPI_ERROR . '(class: InfModelB; method: findAsIterator): 
									This function is disabled in the
									Inference Model';
		trigger_error($errmsg, E_USER_ERROR); 
	}	  
}
?>