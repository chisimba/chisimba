<?php 
/**
* ----------------------------------------------------------------------------------
* Class: ResIterator
* ----------------------------------------------------------------------------------
*
* @package 	resModel
*/


/**
* Implementation of a resource iterator.
*
* This Iterator should be used in a for-loop like:
* $it = $ontClass->listInstances();
* for ($it->rewind(); $it->valid(); $it->next())
* {
*	$currentResource=$it->current();
* };
*
*
* @version  $Id: ResIterator.php 522 2007-08-14 09:22:22Z cweiske $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	resModel
* @access	public
**/
class ResIterator
{
	/**
	* Holds a reference to the assoiated ResModel / OntModel
	* @var		object Model
	* @access	private
	*/
	var $associatedModel;
	
	/**
	* The current position
	* @var		integer
	* @access	private
	*/
	var $key;
	
	/**
	* If the current resource is valid
	* @var		boolean
	* @access	private
	*/
	var $valid;
	
	/**
	* The current resource
	* @var obejct ResResource
	* @access	private
	*/
	var $currentResource;
	
	/**
	* The subject to search for.
	* @var		object ResResource
	* @access	private
	*/
	var $searchSubject;
	
	/**
	* The predicate to search for.
	* @var		object ResResource
	* @access	private
	*/
	var $searchPredicate;
	
	/**
	* The object to search for.
	* @var		object ResResource
	* @access	private
	*/
	var $searchObject;
	
	/**
	* If the resource, we're intrested in is the subject (s), predicate(p), 
	* or object (o) of the found statements
	* 
	* @var		string
	* @access	private
	*/
	var $getSPO;
	
	/**
	* Defines the type of resource, we'd like to receive.
	* 
	* @var		string
	* @access	private
	*/
	var $returnType;
	
	/**
	* If set, each resource will first be checked, if it's
	* language fits.
	* 
	* @var		string
	* @access	private
	*/
	var $findLiteralWithLang;
	
	
	/**
    * Constructor.
    *
	* $subject, $predicate, and $object are used like inf find().
	* $getSPO supports the strings 's', 'p', and 'o' to return
	* either the subject, predicate, or object of the result statements.
	* $returnType supports the strings 'ResProperty', 'ResLiteral',
	* 'OntProperty', 'OntClass', and 'Individual' and returns the resources
	* as the matching type.
    *
    * @param object ResResource  $subject
    * @param object ResResource  $predicate
    * @param object ResResource  $object
    * @param string				 $getSPO
    * @param object ResModel	 $associatedModel
    * @param string				 $returnType
	* @access	public
    */
	function ResIterator($subject,$predicate,$object,$getSPO,& $associatedModel,$returnType = false)
	{
		$this->searchSubject =& $subject;
		$this->searchPredicate =& $predicate;
		$this->searchObject =& $object;
		$this->getSPO = $getSPO;
		$this->returnType = $returnType;
		$this->associatedModel =& $associatedModel;
		$this->findLiteralWithLang = false;
	}
	
	/**
    * Resets iterator list to start
    *
	* @access	public
    */
	function rewind()
	{
		$this->key = -1;
		$this->next();
	}
	
	/**
    * Says if there are additional items left in the list
    *
    * @return	boolean
	* @access	public
    */
	function valid()
	{
		return $this->valid;
	}
	
	/**
    * Moves Iterator to the next item in the list
    *
	* @access	public
    */
	function next()
	{
		$this->key++;
		$this->valid=($this->_getNextResource());
	}
	
	/**
    * Returns the current item
    *
    * @return	mixed
	* @access	public
    */
	function current()
	{
		return $this->currentResource;
	}
	
	/**
    * Returns the next Resource (subject, predicate, 
    * or object of the next matching statement).
    *
    * @return	object resResource
	* @access	private
    */
	function _getNextResource()
	{
		if ($this->findLiteralWithLang)
		{ 
			do 
			{
				$nextStatement = $this->associatedModel->findFirstMatchingStatement($this->searchSubject,$this->searchPredicate,$this->searchObject,$this->key);
				if ($nextStatement === null)
					return false;
					
				$object = $nextStatement->getObject();
				if ($object->getLanguage() != $this->findLiteralWithLang)
				{
					$hasCorrectLanguage=false;
					$this->key++;
				} else 
				{
					$hasCorrectLanguage=true;
				}
					
			} while (!$hasCorrectLanguage);
		} else 
		{
			$nextStatement = $this->associatedModel->findFirstMatchingStatement($this->searchSubject,$this->searchPredicate,$this->searchObject,$this->key);
		}
		if ($nextStatement === null)
			return false;
		
		switch ($this->getSPO) 
		{
			case 's':
				 $this->currentResource = $this->_getResourceAs($nextStatement->getSubject());
				break;

			case 'p':
				 $this->currentResource = $this->_getResourceAs($nextStatement->getPredicate());
				break;
				
			case 'o':
				 $this->currentResource = $this->_getResourceAs($nextStatement->getObject());
				break;
		}
		return (true);
	}
	
	/**
    * Returns the key of the current item
    *
    * @return	integer
	* @access	public
    */
	function key()
	{
		return $this->key;
	}
	
	/**
    * Sets that only Literals with the matching 
    * language should be returned
    *
    * @param	string
	* @access	public
    */
	function setFindLiteralWithLang($language)
	{
		$this->findLiteralWithLang = $language;
	}
	
	/**
    * Returns the $resource as an instance of the type
    * specified in $this->returnType.
    *
    * @param	object ResResource
    * @return	object ResResource
	* @access	private
    */
	function _getResourceAs($resource)
	{
		if ($this->findLiteralWithLang && $resource->getLanguage() != $this->findLiteralWithLang)
			$this->_getNextResource();

		if($this->returnType)	
			switch ($this->returnType) {
		
				case 'ResProperty':
						return $this->associatedModel->createProperty($resource->getLabel());
					break;
					
				case 'ResLiteral':
						$newLiteral = $this->associatedModel->createLiteral($resource->getLabel(),$resource->getLanguage());
						$newLiteral->setDatatype($resource->getDatatype());
						return $newLiteral;
					break;
					
				case 'OntProperty':

						return $this->associatedModel->createOntProperty($resource->getLabel());
					break;
					
				case 'OntClass':
						return $this->associatedModel->createOntClass($resource->getLabel());
					break;
					
				case 'Individual':
					return $this->associatedModel->createIndividual($resource->getLabel());
				break;
			}
		return $resource;
	}
}

?>