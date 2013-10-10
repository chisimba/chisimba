<?php
// ----------------------------------------------------------------------------------
// Class: InfRule
// ----------------------------------------------------------------------------------

/**
 * This class represents a single rule in a RDFS inference model.
 * It primary constists of a trigger and an entailment.
 * In the forward-chaining mode (RDFSFModel) a statement is checked, 
 * if it satisfies the trigger. If it does, a new statement is returned.
 * In the backward-chaining mode (RDFSBModel) a find-query is checked 
 * with the entailment. If this entailment could satify the find-query, 
 * a new find-query is returned, that searches for statements that 
 * satisfy the trigger of this rule.
 * 
 * @version  $Id: InfRule.php 290 2006-06-22 12:23:24Z tgauss $
 * @author Daniel Westphal <mail at d-westphal dot de>

 *
 * @package infModel
 * @access	public
 **/
 
class InfRule 
{
	
	/**
	* Array, that hold the trigger subject in key ['s'], the trigger 
	* predicate in ['p'], and the trigger object in ['o'].
	* The array values can be NULL to match anything or be a node that 
	* has to be matched.
	*
	* @var		array
	* @access	private
	*/
	var $trigger;

	/**
	* Array, that hold the entailment subject in key ['s'], the 
	* entailment predicate in ['p'], and the entailment object in ['o'].
	* The array values can be a node that will be inserted in the 
	* returning statement, or '<s>' to insert the subject,'<p>' to insert 
	* the predicate, or '<o>' to insert the object of the checked statement
	* to this position in the new returned statement.
	*
	* @var		array
	* @access	private
	*/	
 	var $entailment;
	
 	
   /**
    * Constructor
	* 
    * 
	* @access	public
    */	 	
 	function infRule()
 	{
		//initialising vars
		$this->trigger=array();
		$this->entailment=array();
 	}
 	
 	/**
	* Sets the trigger of this rule
	* The values can be NULL to match anything or be a node that has to 
	* be matched.
	*
	* @param	object Node OR NULL	$subject
   	* @param	object Node OR NULL	$predicate
   	* @param	object Node OR NULL	$object
	* @access	public
	* @throws	PhpError
	*/	
 	function setTrigger ($subject, $predicate, $object)
 	{
 		//throw an error if subject, predicate, or object are neither 
 		//node, nor null.
		if(!is_a($subject,'Node') && $subject != null) 
			trigger_error(RDFAPI_ERROR . '(class: Infrule; method: 
				setTrigger): $subject has to be null or of class Node'
				, E_USER_ERROR);
		if(!is_a($predicate,'Node') && $predicate != null) 
			trigger_error(RDFAPI_ERROR . '(class: Infrule; method: 
				setTrigger): $predicate has to be null or of class Node'
				, E_USER_ERROR);
		if(!is_a($object,'Node') && $object != null) 
			trigger_error(RDFAPI_ERROR . '(class: Infrule; method: 
				setTrigger): $object has to be null or of class Node'
				, E_USER_ERROR);
 			
 		//set the trigger
		$this->trigger['s']=$subject;
		$this->trigger['p']=$predicate;
		$this->trigger['o']=$object;		
 	} 

 	/**
	* Sets the entailment of this rule
	* The values can be NULL to match anything or be a node that has to 
	* be matched.
	*
	* @param	object Node OR NULL	$subject
   	* @param	object Node OR NULL	$predicate
   	* @param	object Node OR NULL	$object
	* @access	public
	* @throws	PhpError
	*/	 	
	function setEntailment($subject,$predicate,$object)
	{
		//throw an error if subject, predicate, or object are neither node, 
		//nor <s>,<p>,<o>.
		if(!is_a($subject,'Node') && !ereg('<[spo]>', $subject)) 
			trigger_error(RDFAPI_ERROR . '(class: Infrule; method:  
				setEntailment): $subject has to be <s>,<p>,or <o> or of class Node'
				, E_USER_ERROR);
		if(!is_a($predicate,'Node') && !ereg('<[spo]>', $predicate)) 
			trigger_error(RDFAPI_ERROR . '(class: Infrule; method: 
				setEntailment): $predicate has to be <s>,<p>,or <o> or of class Node'
				, E_USER_ERROR);
		if(!is_a($object,'Node') && !ereg('<[spo]>', $object)) 
			trigger_error(RDFAPI_ERROR . '(class: Infrule; method: 
				setEntailment): $object has to be <s>,<p>,or <o> or of class Node'
				, E_USER_ERROR);
		
		$this->entailment['s']=$subject;	
		$this->entailment['p']=$predicate;
		$this->entailment['o']=$object;
	}
 
 	/**
	* Checks, if the statement satisfies the trigger.
	*
   	* @param	object Statement 
	* @return 	boolean
	* @access	public
	* @throws	PhpError
	*/
 	function checkTrigger(& $statement)
 	{	
 		//is true, if the trigger is null to match anything 
 		//or equals the statement's subject
 		$shouldFireS = 	$this->trigger['s'] ==  null || 
 						$this->trigger['s']->equals($statement->getSubject());
 						
 		//is true, if the trigger is null to match anything 
 		//or equals the statement's predicate
 		$shouldFireP = 	$this->trigger['p'] ==  null || 
 						$this->trigger['p']->equals($statement->getPredicate());

 		//is true, if the trigger is null to match anything 
 		//or equals the statement's object
 		$shouldFireO = 	$this->trigger['o'] ==  null || 
 						$this->trigger['o']->equals($statement->getObject());

 		//returns true, if ALL are true
 		return $shouldFireS && $shouldFireP && $shouldFireO;
 	}
 
 	/**
	* Checks, if this rule could entail a statement that matches
	* a find of $subject,$predicate,$object.
	*
   	* @param	object Statement 
	* @return 	boolean
	* @access	public
	* @throws	PhpError
	*/ 	
 	function checkEntailment ($subject, $predicate, $object)
 	{
		//true, if $subject is null, the entailment's subject matches
		//anything, or the $subject equals the entailment-subject.
 		$matchesS=	$subject ==  null ||
		 			!is_a($this->entailment['s'],'Node') ||
		 			$this->entailment['s']->equals($subject);

		//true, if $predicate is null, the entailment's predicate matches 
		//anything, or the $predicate equals the entailment-predicate.		 			
		 $matchesP=	$predicate ==  null ||
		 			!is_a($this->entailment['p'],'Node') ||
		 			$this->entailment['p']->equals($predicate);

		//true, if $object is null, the entailment's object matches 
		//anything, or the $object equals the entailment-object.		 					 			
		$matchesO=	$object ==  null ||
		 			!is_a($this->entailment['o'],'Node') ||
		 			$this->entailment['o']->equals($object);
  		
 		//returns true, if ALL are true
 		return $matchesS && $matchesP && $matchesO;
	}
	
 	/**
	* Returns a infered InfStatement by evaluating the statement with 
	* the entailment rule.
	*
   	* @param	object Statement 
	* @return 	object InfStatement
	* @access	public
	* @throws	PhpError
	*/  	
 	function entail(& $statement)
 	{
 		//if the entailment's subject is <s>,<p>,or <o>, put the statements 
 		//subject,predicate,or object into the subject of the 
 		//entailed statement. If the entailment's subject is a node, 
 		//add that node to the statement.	
	 	switch ($this->entailment['s'])
		{
		case '<s>':
		 	$entailedSubject=$statement->getSubject();
	 	break;
		case '<p>':
		 	$entailedSubject=$statement->getPredicate();
		break;	
 		case '<o>':
	 		$entailedSubject=$statement->getObject();
	 	break;
		default:
		 	$entailedSubject=$this->entailment['s'];
		};
		
 		//if the entailment's predicate is <s>,<p>,or <o>, put the 
 		//statements subject,predicate,or object into the predicate of 
 		//the entailed statement. If the entailment's predicate is a node, 
 		//add that node to the statement.			
		switch ($this->entailment['p'])
		{
		case '<s>':
		 	$entailedPredicate=$statement->getSubject();
	 	break;
		case '<p>':
		 	$entailedPredicate=$statement->getPredicate();
	 	break;	
 		case '<o>':
	 		$entailedPredicate=$statement->getObject();
	 	break;
		default:
		 	$entailedPredicate=$this->entailment['p'];
		};
		
 		//if the entailment's object is <s>,<p>,or <o>, put the 
 		//statements subject,predicate,or object into the object of 
 		//the entailed statement. If the entailment's object is a node,
 		//add that node to the statement.			
		switch ($this->entailment['o'])
		{
		case '<s>':
		 	$entailedObject=$statement->getSubject();
	 	break;
		case '<p>':
		 	$entailedObject=$statement->getPredicate();
	 	break;	
	 	case '<o>':
		 	$entailedObject=$statement->getObject();
	 	break;
		default:
		 	$entailedObject=$this->entailment['o'];
		};
		
		//return the infered statement
		return (new InfStatement($entailedSubject,$entailedPredicate,$entailedObject));
 	}
 	
 	/**
	* Returns a find-query that matches statements, whose entailed 
	* statements would match the supplied find query.
	*
   	* @param	Node OR null $subject
   	* @param	Node OR null $predicate
   	* @param	Node OR null $object
	* @return 	array
	* @access	public
	* @throws	PhpError
	*/  	 	
 	function getModifiedFind( $subject, $predicate, $object)
 	{			
 		$findSubject=$this->trigger['s'];
 		$findPredicate=$this->trigger['p'];
 		$findObject=$this->trigger['o'];
 		
 		switch ($this->entailment['s'])
 			{
 			case '<s>':
 			 	$findSubject=$subject;
		 	break;
 			case '<p>':
 			 	$findPredicate=$subject;
		 	break;	
		 	case '<o>':
			 	$findObject=$subject;
		 	break;
 			};
 			
 		switch ($this->entailment['p'])
 			{
 			case '<s>':
 			 	$findSubject=$predicate;
		 	break;
 			case '<p>':
 			 	$findPredicate=$predicate;
		 	break;	
		 	case '<o>':
			 	$findObject=$predicate;
	 		break;
 			};
 			
 		switch ($this->entailment['o'])
 			{
 			case '<s>':
 			 	$findSubject=$object;
		 	break;
 			case '<p>':
 			 	$findPredicate=$object;
		 	break;	
		 	case '<o>':
			 	$findObject=$object;
		 	break;
 			};
	
 		return array('s' => $findSubject,
			 	     'p' => $findPredicate,
			 		 'o' => $findObject );	
 	}
 	
 	function getTrigger()
 	{
 		return array 	(	's' => $this->trigger['s'],
 							'p' => $this->trigger['p'],	
 							'o' => $this->trigger['o'],
 						);
 	}
 	
 	function getEntailment()
 	{
 		return array 	(	's' => $this->entailment['s'],
 							'p' => $this->entailment['p'],	
 							'o' => $this->entailment['o'],
 						);
 	}
}
?>