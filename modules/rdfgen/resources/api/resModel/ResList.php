<?php
// ----------------------------------------------------------------------------------
// Class: ResList
// ----------------------------------------------------------------------------------

/**
* Implementation of an rdf:Collection (rdf:List)
* Provides a convenience encapsulation for lists formed from 
* chains of RDF statements arranged to form a head/tail cons-cell 
* structure. 
* 
* A well-formed list has cells that are made up of three statements: 
* one denoting the rdf:type of the list cell, one denoting the link 
* to the value of the list at that point, and one pointing to the 
* list tail. If a list cell is not well-formed, list operations may 
* fail in unpredictable ways. However, to explicitly check that the 
* list is well-formed at all times is expensive, but you can call
* the isValid() method to manually check, if the list is well formed.
*
*
* @version  $Id: ResList.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>

*
* @package resModel
* @access	public
*/
class ResList extends ResResource 
{
	/**
	* Holds a ResResource with the uri rdf:rest
	* @var		ResResource
	* @access	private
	*/
	var $rdfRestResource;
	
	/**
	* Holds a ResResource with the uri rdf:first
	* @var		ResResource
	* @access	private
	*/
	var $rdfFirstResource;
		
	/**
	* Holds a ResResource with the uri rdf:nil
	* @var		ResResource
	* @access	private
	*/
	var $rdfNilResource;
	
	
	/**
    * Constructor
	* You can supply a URI
    *
    * @param string $uri 
	* @access	public
    */		
	function ResList($uri = null)
	{
		//call the parent's constructor
		parent::ResResource($uri);
		//initialize vars
		$this->rdfRestResource = new ResResource(RDF_NAMESPACE_URI.RDF_REST);
		$this->rdfFirstResource = new ResResource(RDF_NAMESPACE_URI.RDF_FIRST);
		$this->rdfNilResource = new ResResource(RDF_NAMESPACE_URI.RDF_NIL);
	}

	/**
	* Returns the value of the list element at the specified position or null.
	*
   	* @param	integer	$position
   	* @return	ResResource 
   	* @access	public
   	*/
	function get($position)
	{
		//init
		$listElement=$this;
		//walk through the list until the position in the list is reached
		for ($i=0;$i<$position;$i++)
		{
			$listElement=$this->_getRestElement($listElement);
			if($listElement===null)
				return null;	
		}
		//return the associated value
		return $this->_getValue($listElement);	
	}	

	/**
	*  Add the given value to the end of the list. 
	*  it is only defined if this is not the empty list.
	*
   	* @param	object ResResource	$resource
   	* @return	boolean 
   	* @access	public
   	*/
	function add($resource)
	{
		//return false if this list is the empty list
		if($this->uri==RDF_NAMESPACE_URI.RDF_NIL)
			return false;
		
		//if this is the first value	
		if ($this->isEmpty())
		{
			$newLastElement =& $this;
		} else
		//if there are other values in the list 
		{
			//get the last list element
			$lastElement=$this->_getListElement();
			//remove the rdf:rest property
			$lastElement->removeAll($this->rdfRestResource);
			//create a new list element
			$newLastElement=$this->model->createResource();
			//concatenate the new list element with the list
			$lastElement->addProperty($this->rdfRestResource,$newLastElement);
		}
		//add the value
		$newLastElement->addProperty($this->rdfFirstResource,$resource);
		//ad the rdf:nil property to the last list element
		$newLastElement->addProperty($this->rdfRestResource,$this->rdfNilResource);
		
		return true;
	}
	
	/**
	*  Update the head of the list to have the given value, 
	*  and return the previous value.
	*
   	* @param	object ResResource	$value
   	* @return	ResResource 
   	* @access	public
   	*/
	//todo: error handling, when empty list
	function setHead($value)
	{	
		//save the old value	
		$oldValue=$this->getHead();
		//remove the old value
		$this->removeAll($this->rdfFirstResource);
		//add the new value
		$this->addProperty($this->rdfFirstResource,$value);
		
		//return the old value
		return $oldValue;
	}
	
	/**
	* Get the value that is associated with the head of the list.
	*
   	* @return	ResResource 
   	* @access	public
   	*/
	//todo: error handling, falls empty list
	function getHead()
	{
		return $this->_getValue($this);
	}
	
	/**
	*  Remove the head of the list. The tail of the list 
	*  remains in the model. Note that no changes are made to 
	*  list cells that point to this list cell as their tail. 	
	*
   	* @return	ResList 
   	* @access	public
   	*/
	function removeHead()
	{
		//get the second list element
		$rest=$this->_getRestElement($this);
		//remove the first element
		$this->removeAll($this->rdfFirstResource);
		$this->removeAll($this->rdfRestResource);
		//change this Resource URI to that of the second list element
		//thus makin it the fist element
		$this->uri=$rest->getURI();
		
		//return the new list
		return $this;
	}	

	/**
	* Get the Position of the first occurrence of the given value in the list, 
	* or -1 if the value is not in the list.
	* You can supply an offset to search for values. (First element has offset 0)
	* Default is 0
	*
   	* @param	object ResResource	$resource
    * @param	integer	$offset
   	* @return	integer 
   	* @access	public
   	*/
	function indexOf($resource, $offset = 0)
	{
		//init
		$element=$this;
		$actualIndex=0;

		//walk through the list until the value is found and the position is higher than
		//the offset
		while ($actualIndex < $offset || !$resource->equals($this->_getValue($element)))
		{
			//get next list element
			$element=$this->_getRestElement($element);
			$actualIndex++;
			
			//if the end of the list is reached and the value isn't found
			if ($element===null)
				return null;
		}
		//return the index value
		return $actualIndex;	
	}
	
	/**
	* Replace the value at the i'th position in the list with the given value
	*
   	* @param	integer				$index
   	* @param	object ResResource	$resource
   	* @return	object ResResource 
   	* @access	public
   	*/
	function replace($index, $resource)
	{
		//get the list element at the $index position
		$listElement=$this->_getListElement($index);
		//get the old value
		$oldValue=$this->_getValue($listElement);
		//remove the old value
		$listElement->removeAll($this->rdfFirstResource);
		//add the new value
		$listElement->addProperty($this->rdfFirstResource,$resource);
		//return the old value
		return $oldValue;
	}
	
	/**
	* Answer true if the given node appears as the value of a value 
	* of any of the cells of this list.
	*
   	* @param	object ResResource	$value
   	* @return	boolean 
   	* @access	public
   	*/
	function contains($value)
	{
		//return true, if a position was found.
		$result=$this->indexOf($value);
		return ($result!==null);
	}
	
	/**
	* Get the list that is the tail of this list.
	*
   	* @return	object ResList 
   	* @access	public
   	*/
	function getTail()
	{
		//get the second list element
		$nextListElement= $this->_getRestElement($this);
		//return the second element as new list
		return $this->model->createList($nextListElement->getURI());	
	}
	
	/**
	* Remove all of the components of this list from the model. 
	* Note that this is operation is only removing the list cells 
	* themselves, not the resources referenced by the list - 
	* unless being the object of an rdf:first  statement is the 
	* only mention of that resource in the model.
	*
   	* @return	boolean 
   	* @access	public
   	*/
	function removeList()
	{
		$element=$this;

		while ($element!==null)
		{
			$nextElement=$this->_getRestElement($element);
			
			$element->removeAll($this->rdfFirstResource);
			$element->removeAll($this->rdfRestResource);
					
			if (($nextElement !== null) && ($nextElement->getURI()!==RDF_NAMESPACE_URI.RDF_NIL))
			{
				$element=$nextElement;
			} else 
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	* Returns true, if this list is empty
	*
   	* @param	object Statement	$statement
   	* @return	integer 
   	* @access	public
   	*/
	function isEmpty()
	{
		return !$this->hasProperty($this->rdfFirstResource);
	}
	
	/**
	* Get all values in the list as an array of ResResources
	*
   	* @return	array 
   	* @access	public
   	*/
	function getContentInArray()
	{
		$result=array();
		$element=$this;

		while ($element!==null)
		{
			//add the value of the current element to the result if is set.
			$value=$this->_getValue($element);
			if ($value!==null)
				$result[]=$value;
			
			//walk through the list until it's end		
			$nextElement=$this->_getRestElement($element);
			if (($nextElement !== null) && ($nextElement->getURI()!==RDF_NAMESPACE_URI.RDF_NIL))
			{
				$element=$nextElement;
			} else 
			{
				break;
			}
		}
		//return the result
		return $result;
	}
	
	/**
	* Change the tail of this list to point to the given list, so that this list 
	* becomes the list of the concatenation of the elements of 
	* both lists. This is a side-effecting operation on this list; 
	* for a non side-effecting alternative, see append. 
	* 
	*
   	* @param	object ResList	$ResList
   	* @access	public
   	*/
	function concatenate($ResList)
	{
		//get the last list element
		$lastElement=$this->_getListElement();
		//remove the old tail (rdf:nil)
		$lastElement->removeAll($this->rdfRestResource);
		//add the $ResList as new tail
		$lastElement->addProperty($this->rdfRestResource,$ResList);
	}
	
	/**
	* Answer a new list that is formed by adding each element of 
	* this list to the head of the given list. This is a non 
	* side-effecting operation on either this list or the given 
	* list, but generates a copy of this list. For a more storage 
	* efficient alternative, see concatenate
	*
   	* @param	object ResList	$ResList
   	* @return	object ResList 
   	* @access	public
   	*/
	function append($resList)
	{
		//get a copy of this list
		$newList=$this->copy();
		
		//add all values from the $resList to the new list
		foreach ($resList->getContentInArray() as $value)
		{
			$newList->add($value);	
		}
		//return the new list
		return $newList;
	}
	
	/**
	* Answer a list that contains all of the elements of this 
	* list in the same order, but is a duplicate copy in the 
	* underlying model.
	*
   	* @return	object ResList 
   	* @access	public
   	*/
	function copy()
	{
		//create a new list in the model
		$newList=$this->model->createList();
		//add all values from this list to the new list
		foreach ($this->getContentInArray() as $value)
		{
			$newList->add($value);	
		}
		//return the new list
		return $newList;
	}
	
	/**
	* Return a reference to a new list cell whose head is value  
	* and whose tail is this list.
	*
   	* @param	object ResResource	$value
   	* @return	object ResList 
   	* @access	public
   	*/
	function cons($value)
	{
		//create a new list
		$newList=$this->model->createList();
		//add the new value
		$newList->add($value);
		//set this list as the tail of the new list
		$newList->setTail($this);
		
		//return the new list
		return $newList;
	}
	
	/**
	* Update the list cell at the front of the list to have the 
	* given list as tail. The old tail is returned, and remains 
	* in the model.
	*
   	* @param	object ResList	$resList
   	* @return	object Reslist 
   	* @access	public
   	*/
	function setTail($resList)
	{
		//save the old tail
		$oldTail=$this->getTail();
		//remove the old tail
		$this->removeAll($this->rdfRestResource);
		//add the $resList as new Tail
		$this->addProperty($this->rdfRestResource,$resList);
		
		//return the old tail
		return $oldTail;
	}
	
	/**
	* Answer true if this list has the same elements in the 
	* same order as the given list. Note that the standard equals 
	* test just tests for equality of two given list cells. 
	* While such a test is sufficient for many purposes, this 
	* test provides a broader equality definition, but is 
	* correspondingly more expensive to test.
	*
   	* @param	object ResList	$resList
   	* @return	boolean 
   	* @access	public
   	*/
	function sameListAs($resList)
	{
		//init
		$indexPos=0;
		do
		{
			//get the values for both lists at the actual position
			$thisValue=$this->get($indexPos);
			$thatValue=$resList->get($indexPos);
			//if the values aren't equal, return false
			if (($thisValue !== null) && !$thisValue->equals($thatValue))
				return false;
				
			$indexPos++;
			//walk until this list reaches a null value (end)	
		} while ($thisValue!==null);
		
		//if the other list has a null value at this position too, return true
		//else return false
		return ($thatValue===null);
	}
	
	/**
	* Remove the given value from this list. 
	* If value does not occur in the list, no action is taken. Since removing the 
	* head of the list will invalidate the list head cell, in 
	* general the list must return the list that results from 
	* this operation. However, in many cases the return value 
	* will be the same as the object that this method is invoked 
	* on.
	*
   	* @param	object ResResource	$value
   	* @return	object ResList 
   	* @access	public
   	*/
	function remove($value)
	{
		//if the value is the value of the first list element(head)
		//call the remove head position and return the new head
		if ($value->equals($this->_getValue($this)))
			return $this->removeHead();
	
		$element=$this;
		do
		{	
			$newElement=$this->_getRestElement($element);
			
			//if the value equals the value of the current list element
			if ($newElement !== null && $value->equals($this->_getValue($newElement)))
			{
				//remove the link to the list element to be removed
				$element->removeAll($this->rdfRestResource);
				//add a link to the list element AFTER the element to be deleted
				$element->addProperty($this->rdfRestResource,$this->_getRestElement($newElement));
				//remove the list element with values
				$newElement->removeAll($this->rdfFirstResource);
				$newElement->removeAll($this->rdfRestResource);
				//return this ResList
				return $this;	
			}
			$element=$newElement;
		} while ($element!==null);
		//return this list
		return $this;	
	}
	
	/**
	* Answer true if the list is well-formed, by checking that each 
	* node is correctly typed, and has a head and tail pointer from 
	* the correct vocabulary
	*
   	* @return	boolean 
   	* @access	public
   	*/
	function isValid()
	{
		$element=$this;	
		if ($this->_getValue($this)=== null && $this->_getRestElement($this) === null)
			return true;
		do 
		{
			//return true if the last element is a rdf:nil
			if ($element->getURI() == RDF_NAMESPACE_URI.RDF_NIL)
				return true;
			//return false, if the current element has no associated value
			if ($this->_getValue($element) === null)
				return false;
						
			$element=$this->_getRestElement($element);	
		} while ($element !== null);
		//return false, if the current element has no rdf:rest property
		return false;
	}
	
	/**
	* Get the associated rdf:rest Resource from the suplied ResList element
	*
   	* @param	object ResList	$listElement
   	* @return	object ResList 
   	* @access	private
   	*/
	function _getRestElement($listElement)
	{
		//get the rdf:rest property
		$statement= $this->model->getProperty($listElement,$this->rdfRestResource);
		//return null, if this property isn't set
		if ($statement === null)
			return null;
			
		//return the value of the rdf:rest property	
		return $statement->getObject();	
	}
	
	/**
	* Returns the list element at the $index position.
	*
	* If to $index is suplied, the last list element will be returned
	
   	* @param	integer	$index
   	* @return	object ResResource 
   	* @access	private
   	*/
	function _getListElement($index = null)
	{
		$element=$this;
		$actualIndex=0;

		while ($element!=null)
		{
			//return the current element if index matches the current index
			if ($actualIndex === $index)
				return $element;
			
			//return the current element if it the last one		
			if ($element->hasProperty($this->rdfRestResource,$this->rdfNilResource))
				return $element;
			
			$nextElement=$this->_getRestElement($element);
			
			if ($nextElement!==null)
			{
				$element=$nextElement;
				$actualIndex++;
			} else 
			{
				break;
			}
		}
		return $element;
	}
	
	/**
	* Get the value associated to the $listResource by the rdf:first property
	*
   	* @param	object ResList	$listResource
   	* @return	object ResResource 
   	* @access	private
   	*/
	function _getValue($listResource)
	{
		//Return the value of the rdf:first property or null, if it isn't set
		$statement=$this->model->getProperty($listResource,$this->rdfFirstResource);
		if ($statement===null)
			return null;

		return $statement->getObject();	
	}
}
?>