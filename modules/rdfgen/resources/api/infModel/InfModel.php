<?php
// ----------------------------------------------------------------------------------
// Class: infModel
// ----------------------------------------------------------------------------------

/**
 * A InfModel Model extends a MemModel , by adding the ability to infer statements from 
 * known statements and RDFS/OWL-Schematas.
 * It uses the same interface as MemModel, thus making the 
 * infererence process hidden.
 * 
 * @version  $Id: InfModel.php 290 2006-06-22 12:23:24Z tgauss $
 * @author Daniel Westphal <mail at d-westphal dot de>
 *
 * @package infModel
 * @access	public
 */

class InfModel extends MemModel 
{
	/**
	* Array that holds the objects of the class Infrule, 
	* which were assigned by the _addToInference() function
	*
	* @var		array
	* @access	private
	*/
	var $infRules;

	/**
	* Array of URI-Strings that produces Infrules.
	*
	* @var		array
	* @access	private
	*/
	var $supportedInference;
	
	/**
	* Array of the connection between the infrules and the statement 
	* that assigned those rules.
	* array[2][3]=true;array[2][5]=true means, that statement 2 
	* assigned rule 3 & 5 to the model.
	*
	* @var		array
	* @access	private
	*/	
	var $statementRuleIndex;
	
	/**
	* Array of the infRule triggers and the matching infrules.
	* $this->infRulesTriggerIndex['s'] for subject index, ['p'] for predicates, 
	* and ['o'] for objects.
	*
	* @var		array
	* @access	private
	*/	
	var $infRulesTriggerIndex;
	
	/**
	* Array of the infRule entailments and the matching infrules.
	* $this->infRulesEntailIndex['s'] for subject index, ['p'] for predicates, 
	* and ['o'] for objects.
	*
	* @var		array
	* @access	private
	*/	
	var $infRulesEntailIndex;



   /**
    * Constructor
	* You can supply a base_uri
    *
    * @param string $baseURI 
	* @access	public
    */	
	function InfModel ($baseURI = NULL) 
	{
		//call the memmodel constructor method
		parent::MemModel($baseURI);
		//initialise vars
		$this->infRulesTriggerIndex['s']=array();
		$this->infRulesTriggerIndex['p']=array();
		$this->infRulesTriggerIndex['o']=array();
		$this->infRulesEntailIndex['s']=array();
		$this->infRulesEntailIndex['p']=array();
		$this->infRulesEntailIndex['o']=array();
		$this->infRules=array();
		$this->statementRuleIndex = array();
		//arraylist of predicate labels that shall add inference rules 
		//to the model
		//The constants, wich statements will create rules can be configured in constants.php
		if (INF_RES_SUBCLASSOF)
			$this->supportedInference[] = RDF_SCHEMA_URI.RDFS_SUBCLASSOF;
			
		if (INF_RES_SUBPROPERTYOF)
			$this->supportedInference[] = RDF_SCHEMA_URI.RDFS_SUBPROPERTYOF;

		if (INF_RES_RANGE)
			$this->supportedInference[] = RDF_SCHEMA_URI.RDFS_RANGE;

		if (INF_RES_DOMAIN)
			$this->supportedInference[] = RDF_SCHEMA_URI.RDFS_DOMAIN;

		if (INF_RES_OWL_SAMEAS)
			$this->supportedInference[] = OWL_URI.OWL_SAME_AS;

		if (INF_RES_OWL_INVERSEOF)
			$this->supportedInference[] = OWL_URI.OWL_INVERSE_OF;

		//Rule: rdfs12	
		if (INF_RES_RULE_RDFS12)
		{
			$infRule=new InfRule();
			$infRule->setTrigger(null,new Resource(RDF_NAMESPACE_URI.RDF_TYPE),new Resource(RDF_SCHEMA_URI.'ContainerMembershipProperty'));
			$infRule->setEntailment('<s>',new Resource(RDF_SCHEMA_URI.RDFS_SUBPROPERTYOF),new Resource(RDF_SCHEMA_URI.'member'));
			$this->_addInfRule($infRule,'base');
		}
	
		//Rule: rdfs6
		if (INF_RES_RULE_RDFS6)
		{
			$infRule=new InfRule();
			$infRule->setTrigger(null,new Resource(RDF_NAMESPACE_URI.RDF_TYPE),new Resource(RDF_NAMESPACE_URI.RDF_PROPERTY));
			$infRule->setEntailment('<s>',new Resource(RDF_SCHEMA_URI.RDFS_SUBPROPERTYOF),'<s>');
			$this->_addInfRule($infRule,'base');
		}
			
		//Rule: rdfs8
		if (INF_RES_RULE_RDFS8)
		{
			$infRule=new InfRule();
			$infRule->setTrigger(null,new Resource(RDF_NAMESPACE_URI.RDF_TYPE),new Resource(RDF_SCHEMA_URI.RDFS_CLASS));
			$infRule->setEntailment('<s>',new Resource(RDF_SCHEMA_URI.RDFS_SUBCLASSOF),new Resource(RDF_SCHEMA_URI.RDFS_RESOURCE));
			$this->_addInfRule($infRule,'base');
			
		}
		
		//Rule: rdfs10
		if (INF_RES_RULE_RDFS10)
		{
			$infRule=new InfRule();
			$infRule->setTrigger(null,new Resource(RDF_NAMESPACE_URI.RDF_TYPE),new Resource(RDF_SCHEMA_URI.RDFS_CLASS));
			$infRule->setEntailment('<s>',new Resource(RDF_SCHEMA_URI.RDFS_SUBCLASSOF),'<s>');
			$this->_addInfRule($infRule,'base');
		}
				
		//Rule: rdfs13
		if (INF_RES_RULE_RDFS13)
		{
			$infRule=new InfRule();
			$infRule->setTrigger(null,new Resource(RDF_NAMESPACE_URI.RDF_TYPE),new Resource(RDF_SCHEMA_URI.RDFS_DATATYPE));
			$infRule->setEntailment('<s>',new Resource(RDF_SCHEMA_URI.RDFS_SUBCLASSOF),new Resource(RDF_SCHEMA_URI.RDFS_LITERAL));
			$this->_addInfRule($infRule,'base');
		}
				
	}
	
 	/**
   	* Adds a new triple to the Model without checking if the statement 
   	* is already in the Model.
   	* So if you want a duplicate free MemModel use the addWithoutDuplicates()
   	* function (which is slower then add())
   	* If the statement's predicate label is supported by the inference, 
   	* the matching rules are added.
   	*
   	* @param	object Statement	$statement
   	* @access	public
   	* @throws	PhpError 
   	*/	
	function add($statement)
	{
		parent::add($statement);
		//if the predicate is supported by the inference
		if (in_array($statement->getLabelPredicate(),$this->supportedInference))
		{
				$this->_addToInference($statement);
		};
	}

	/**
	* This function analyses the statement's predicate and adds the 
	* matching infrule to the model.
	*
   	* @param	object Statement	$statement
   	* @access	private
   	*/
	function _addToInference($statement)
	{
		$predicateLabel=$statement->getLabelPredicate();
		//get the position of the the statement in the model
		end($this->triples);
		$statementPosition=key($this->triples);
		
		switch ($predicateLabel)
		{
			case RDF_SCHEMA_URI.RDFS_SUBPROPERTYOF :
				//create a new rule
				$infRule=new InfRule();
				//set the trigger to match all statements, having a 
				//predicate, that matches the subject of the statement that 
				//created this rule.
				$infRule->setTrigger(null,$statement->getSubject(),null);
				//set the infrule to return a statement, having the same 
				//subject and object as the statement, that asked for an 
				//entailment, and having the object of the statement, 
				//that created this rule as predicate.
				$infRule->setEntailment('<s>',$statement->getObject(),'<o>');
				//add the infule to Model, Statement/Rule-Index, 
				//and Rule/Trigger (or Rule/Entailment) index
				$this->_addInfRule($infRule,$statementPosition);
			break;
			
			case RDF_SCHEMA_URI.RDFS_SUBCLASSOF :
				$infRule=new InfRule();
				$infRule->setTrigger(null,new Resource(RDF_NAMESPACE_URI.RDF_TYPE),$statement->getSubject());
				$infRule->setEntailment('<s>',new Resource(RDF_NAMESPACE_URI.RDF_TYPE),$statement->getObject());
				$this->infRules[]=$infRule;
				$this->_addInfRule($infRule,$statementPosition);
			break;
			
			case RDF_SCHEMA_URI.RDFS_DOMAIN :
				$infRule=new InfRule();
				$infRule->setTrigger(null,$statement->getSubject(),null);
				$infRule->setEntailment('<s>',new Resource(RDF_NAMESPACE_URI.RDF_TYPE),$statement->getObject());
				$this->infRules[]=$infRule;
				$this->_addInfRule($infRule,$statementPosition);
			break;
			
			case RDF_SCHEMA_URI.RDFS_RANGE :
				$infRule=new InfRule();
				$infRule->setTrigger(null,$statement->getSubject(),null);
				$infRule->setEntailment('<o>',new Resource(RDF_NAMESPACE_URI.RDF_TYPE),$statement->getObject());
				$this->infRules[]=$infRule;
				$this->_addInfRule($infRule,$statementPosition);
			break;
			
			case OWL_URI.OWL_INVERSE_OF :
				$infRule=new InfRule();
				$infRule->setTrigger(null,$statement->getSubject(),null);
				$infRule->setEntailment('<o>',$statement->getObject(),'<s>');
				$this->infRules[]=$infRule;
				$this->_addInfRule($infRule,$statementPosition);
			
				$infRule=new InfRule();
				$infRule->setTrigger(null,$statement->getObject(),null);
				$infRule->setEntailment('<o>',$statement->getSubject(),'<s>');
				$this->infRules[]=$infRule;			
				$this->_addInfRule($infRule,$statementPosition);				
			break;
			
			case OWL_URI.OWL_SAME_AS :
				$infRule=new InfRule();
				$infRule->setTrigger($statement->getSubject(),null,null);
				$infRule->setEntailment($statement->getObject(),'<p>','<o>');
				$this->_addInfRule($infRule,$statementPosition);
			
				$infRule=new InfRule();
				$infRule->setTrigger($statement->getObject(),null,null);
				$infRule->setEntailment($statement->getSubject(),'<p>','<o>');
				$this->_addInfRule($infRule,$statementPosition);
			
				$infRule=new InfRule();
				$infRule->setTrigger(null,$statement->getSubject(),null);
				$infRule->setEntailment('<s>',$statement->getObject(),'<o>');
				$this->_addInfRule($infRule,$statementPosition);
			
				$infRule=new InfRule();
				$infRule->setTrigger(null,$statement->getObject(),null);
				$infRule->setEntailment('<s>',$statement->getSubject(),'<o>');
				$this->_addInfRule($infRule,$statementPosition);
			
				$infRule=new InfRule();
				$infRule->setTrigger(null,null,$statement->getSubject());
				$infRule->setEntailment('<s>','<p>',$statement->getObject());
				$this->_addInfRule($infRule,$statementPosition);
			
				$infRule=new InfRule();
				$infRule->setTrigger(null,null,$statement->getObject());
				$infRule->setEntailment('<s>','<p>',$statement->getSubject());
				$this->_addInfRule($infRule,$statementPosition);		
			break;
		};	
	}

	/**
	* This function checks, which infrules were added by the statement and 
	* removes those.
	*
   	* @param	object Statement	$statement
   	* @return	integer 
   	* @access	private
   	*/
	function _removeFromInference($statement)
	{
		$return= array();
		$statementPosition=-1;
		do
		{
			//get the position of the statement that should be removed
			$statementPosition=$this->findFirstMatchOff($statement->getSubject(),
													$statement->getPredicate(),
													$statement->getObject(),
													$statementPosition+1);						
			if ($statementPosition!=-1)
			{
				//if it added any rules
				if (isset ($this->statementRuleIndex[$statementPosition]))
				{
					//remove all rules 
					foreach ($this->statementRuleIndex[$statementPosition] as $key => $value)
					{
						//remove from Rule-Trigger Index
						if (is_a($this,'InfModelF'))
						{
							$trigger=$this->infRules[$key]->getTrigger();
							
							if(is_a($trigger['s'],'Node'))
							{
								$subjectLabel=$trigger['s']->getLabel();
							} else 
							{
								$subjectLabel='null';
							}
							unset ($this->infRulesTriggerIndex['s'][$subjectLabel][array_search($key,$this->infRulesTriggerIndex['s'][$subjectLabel])]);
							
							if(is_a($trigger['p'],'Node'))
							{
								$predicateLabel=$trigger['p']->getLabel();
							} else 
							{
								$predicateLabel='null';
							}
							unset ($this->infRulesTriggerIndex['p'][$predicateLabel][array_search($key,$this->infRulesTriggerIndex['p'][$predicateLabel])]);
							
							if(is_a($trigger['o'],'Node'))
							{
								$objectLabel=$trigger['o']->getLabel();
							} else 
							{
								$objectLabel='null';
							}
							unset ($this->infRulesTriggerIndex['o'][$objectLabel][array_search($key,$this->infRulesTriggerIndex['o'][$objectLabel])]);
						} else
						//remove from Rule-Entailment Index
						{	
							$entailment=$this->infRules[$key]->getEntailment();
							
							if(is_a($entailment['s'],'Node'))
							{
								$subjectLabel=$entailment['s']->getLabel();
							} else 
							{
								$subjectLabel='null';
							}
							unset ($this->infRulesEntailIndex['s'][$subjectLabel][array_search($key,$this->infRulesEntailIndex['s'][$subjectLabel])]);
							
							
							if(is_a($entailment['p'],'Node'))
							{
								$predicateLabel=$entailment['p']->getLabel();
							} else 
							{
								$predicateLabel='null';
							}
							unset ($this->infRulesEntailIndex['p'][$predicateLabel][array_search($key,$this->infRulesEntailIndex['p'][$predicateLabel])]);
							
							if(is_a($entailment['o'],'Node'))
							{
								$objectLabel=$entailment['o']->getLabel();
							} else 
							{
								$objectLabel='null';
							}
							unset ($this->infRulesEntailIndex['o'][$objectLabel][array_search($key,$this->infRulesEntailIndex['o'][$objectLabel])]);
						}	
						//remove from statement-Rule Index
						unset ($this->infRules[$key]);
					}
					unset($this->statementRuleIndex[$statementPosition]);
					$return[]=$statementPosition;
				};
			}
			
		} while($statementPosition!=-1);
		
		//return the positions of the statements to be removed OR emty array 
		//if nothing was found.
		return $return;
	}

	/**
	* Returns a model, containing all Statements, having a Predicate, that 
	* is supported by the inference. 
	* 
	* @return	object Model
	* @access	public
	*/
	function getSchema() 
	{
		$res=new MemModel();
		//Search the base-model for all statements, having a Predicate, that 
		//is supported by the inference.
		foreach ($this->supportedInference as $inferencePredicateLabel)
		{
			$res->addModel($this->find(null, new Resource($inferencePredicateLabel), null));		
		}
		return $res;
	}
	
	/**
	* General method to replace nodes of a MemModel.
	* This function is disabled in the Inference Model.
	*
	* @param	object Node	$subject
	* @param	object Node	$predicate
	* @param	object Node	$object   
	* @param	object Node	$replacement
	* @access	public
	* @throws	PhpError
	*/
	function replace($subject, $predicate, $object, $replacement) 
	{
	
	   $errmsg = RDFAPI_ERROR . '(class: InfModel; method: replace): This function is disabled in the Inference Model';
	   trigger_error($errmsg, E_USER_ERROR); 
	}

	/**
	* Method to search for triples using Perl-style regular expressions.
	* NULL input for any parameter will match anything.
	* Example:  $result = $m->find_regex( NULL, NULL, $regex );
	* Finds all triples where the label of the object node matches the regular 
	* expression.
	* Returns an empty MemModel if nothing is found.
	* 
	* This function is disabled in the Inference Model
	*
	* @param	string	$subject_regex
	* @param	string	$predicate_regex
	* @param	string	$object_regex
	* @return	object MemModel
	* @access	public
	*/
	function findRegex($subject_regex, $predicate_regex, $object_regex) 
	{

		$errmsg = RDFAPI_ERROR . '(class: InfModel; method: findRegex): 
									This function is disabled in the  
									Inference Model';
	   	trigger_error($errmsg, E_USER_ERROR); 
	} 
   
	/**
	* Returns all tripels of a certain vocabulary.
	* $vocabulary is the namespace of the vocabulary inluding a # : / char at
	* the end.
	* e.g. http://www.w3.org/2000/01/rdf-schema#
	* Returns an empty MemModel if nothing is found.
	*
	* This function is disabled in the Inference Model.
	*
	* @param	string	$vocabulary
	* @return	object MemModel
	* @access	public
	*/
	function findVocabulary($vocabulary) 
	{
		
		$errmsg = RDFAPI_ERROR . '(class: InfModel; method: findVocabulary): 
									This function is disabled in the  
									Inference Model';
		trigger_error($errmsg, E_USER_ERROR); 
	} 

	
	/**
 	* Adds the URI or NULL to the Infrule trigger or entailment index.
	* 
	*
	* @param	object infrule	$infRule
	* @param	integer	$infRulePosition
	* @access	private
	*/
	function _addInfruleToIndex(& $infRule,& $infRulePosition)
	{
		//Add the rule only to the trigger index, if it is a InfFModel
		if (is_a($this,'InfModelF'))
		{
			//get the trigger
			$trigger = $infRule->getTrigger();
			//evaluate and set the index
			if ($trigger['s'] == null)
			{
				$this->infRulesTriggerIndex['s']['null'][]=$infRulePosition;
			} else 
			{
				$this->infRulesTriggerIndex['s'][$trigger['s']->getLabel()][]=$infRulePosition;
			};
			
			if ($trigger['p'] == null)
			{
				$this->infRulesTriggerIndex['p']['null'][]=$infRulePosition;
			} else 
			{
				$this->infRulesTriggerIndex['p'][$trigger['p']->getLabel()][]=$infRulePosition;
			};
			
			if ($trigger['o'] == null)
			{
				$this->infRulesTriggerIndex['o']['null'][]=$infRulePosition;
			} else 
			{
				$this->infRulesTriggerIndex['o'][$trigger['o']->getLabel()][]=$infRulePosition;
			};
		} else 
		//add to entailment Index if it is a BModel
		{		
			//get the entailment
			$entailment = $infRule->getEntailment();
			//evaluate the entailment and add to index
			if (!is_a($entailment['s'],'Node'))
			{
				$this->infRulesEntailIndex['s']['null'][]=$infRulePosition;
			} else 
			{
				$this->infRulesEntailIndex['s'][$entailment['s']->getLabel()][]=$infRulePosition;
			};
			
			if (!is_a($entailment['p'],'Node'))
			{
				$this->infRulesEntailIndex['p']['null'][]=$infRulePosition;
			} else 
			{
				$this->infRulesEntailIndex['p'][$entailment['p']->getLabel()][]=$infRulePosition;
			};
			
			if (!is_a($entailment['o'],'Node'))
			{
				$this->infRulesEntailIndex['o']['null'][]=$infRulePosition;
			} else 
			{
				$this->infRulesEntailIndex['o'][$entailment['o']->getLabel()][]=$infRulePosition;
			};
		};	
	}
	
	/**
 	* Searches the trigger-index for a matching trigger and returns an
 	* array of infRule positions.
	* 
	*
	* @param	object infrule	$infRule
	* @return	array	integer
	* @access	private
	*/
	function _findRuleTriggerInIndex($statement)
	{
		$return=array();
		//a statement's subject matches all triggers with null and the same URI
		$subjectLabel=$statement->getLabelSubject();
		$inIndexS=array();
		if (isset($this->infRulesTriggerIndex['s']['null'])) 
			$inIndexS=array_values($this->infRulesTriggerIndex['s']['null']);
		if (isset($this->infRulesTriggerIndex['s'][$subjectLabel])) 
			$inIndexS= array_merge($inIndexS,array_values($this->infRulesTriggerIndex['s'][$subjectLabel]));
		
			//a statement's predicate matches all triggers with null and the same URI
		$predicateLabel=$statement->getLabelPredicate();
		$inIndexP=array();
		if (isset($this->infRulesTriggerIndex['p']['null'])) 
			$inIndexP=array_values($this->infRulesTriggerIndex['p']['null']);
		if (isset($this->infRulesTriggerIndex['p'][$predicateLabel])) 
			$inIndexP= array_merge($inIndexP,array_values($this->infRulesTriggerIndex['p'][$predicateLabel]));
		
		//a statement's object matches all triggers with null and the same URI	
		$objectLabel=$statement->getLabelObject();
		$inIndexO=array();
		if (isset($this->infRulesTriggerIndex['o']['null'])) 
			$inIndexO=array_values($this->infRulesTriggerIndex['o']['null']);
		if (isset($this->infRulesTriggerIndex['o'][$objectLabel])) 
			$inIndexO= array_merge($inIndexO,array_values($this->infRulesTriggerIndex['o'][$objectLabel]));

		//if an infrule position occurs in subject, predicate, and object index, add to result array.		
		foreach ($inIndexP as $positionP)
		{
			if (in_array($positionP,$inIndexO))
				if (in_array($positionP,$inIndexS))
					$return[]=$positionP;
		}
		return $return;
	}
	
	/**
 	* Searches the Entailment-index for a matching Entailment and returns an
 	* array of infRule positions.
	* 
	*
	* @param	node or null	$subject
	* @param	node or null	$predicate
	* @param	node or null	$object
	* @return	array	integer
	* @access	private
	*/
	function _findRuleEntailmentInIndex($subject,$predicate,$object)
	{
		$return=array();
		//a node matches all entailments with NULL or a matching URI
		if(is_a($subject,'Node'))
		{
			$subjectLabel=$subject->getLabel();
			$inIndexS=array();
			if (isset($this->infRulesEntailIndex['s']['null'])) $inIndexS=array_values($this->infRulesEntailIndex['s']['null']);
			if (isset($this->infRulesEntailIndex['s'][$subjectLabel])) $inIndexS= array_merge($inIndexS,array_values($this->infRulesEntailIndex['s'][$subjectLabel]));
		} else 
		//if the subject search pattern is NULL, every rule will match the subject search patter
		{
			$inIndexS=array_keys($this->infRules);
		}
		
		if(is_a($predicate,'Node'))
		{
			$predicateLabel=$predicate->getLabel();
			$inIndexP=array();
			if (isset($this->infRulesEntailIndex['p']['null'])) $inIndexP=array_values($this->infRulesEntailIndex['p']['null']);
			if (isset($this->infRulesEntailIndex['p'][$predicateLabel])) $inIndexP= array_merge($inIndexP,array_values($this->infRulesEntailIndex['p'][$predicateLabel]));
		}  else 
		{
			$inIndexP=array_keys($this->infRules);
		}
		
		if(is_a($object,'Node'))
		{
			$objectLabel=$object->getLabel();
			$inIndexO=array();
			if (isset($this->infRulesEntailIndex['o']['null'])) $inIndexO=array_values($this->infRulesEntailIndex['o']['null']);
			if (isset($this->infRulesEntailIndex['o'][$objectLabel])) $inIndexO= array_merge($inIndexO,array_values($this->infRulesEntailIndex['o'][$objectLabel]));
		} else 
		{
			$inIndexO=array_keys($this->infRules);
		}	
		
		//if an infrule position occurs in subject, predicate, and object index, add to result array.	
		foreach ($inIndexP as $positionP)
		{
			if (in_array($positionP,$inIndexO))
				if (in_array($positionP,$inIndexS))
					$return[]=$positionP;
		}
		return $return;
	}
	
	/**
 	* Adds an InfRule to the InfModel.
 	* $statementPosition states the positiion of the statement, that created
 	* this rule, in the model->triples array.
	* 
	*
	* @param	object Infrule	$infRule
	* @param	integer	$statementPosition
	* @access	private
	*/
	function _addInfRule($infRule, $statementPosition)
	{
		//add the rule
		$this->infRules[]=$infRule;			
		//get the position of the added rule in the model
		end($this->infRules);
		$rulePosition=key($this->infRules);
		//add the information to the index, that this statement 
		//added this rule.
		$this->statementRuleIndex[$statementPosition][$rulePosition]=true;
		//add informations to index over trigger & entailment
		$this->_addInfruleToIndex($infRule,$rulePosition);
	}
 }
?>
