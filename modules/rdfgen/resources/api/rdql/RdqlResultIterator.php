<?php

// ----------------------------------------------------------------------------------
// Class: RdqlResultIterator
// ----------------------------------------------------------------------------------

/**
 * Iterator for traversing Rdql results. 
 * This class can be used for iterating forward and backward trough Rdql results.
 * It should be instanced using the rdqlQueryAsIterator() method of a MemModel or a DBModel.
 * 
 * @version  $Id: RdqlResultIterator.php 268 2006-05-15 05:28:09Z tgauss $
 * @author Daniel Westphal <mail@d-westphal.de>, Chris Bizer <chris@bizer.de>
 *
 * @package rdql
 * @access	public
 *
 */ 
 class RdqlResultIterator extends Object {

 	/**
	* Reference to the Rdql result
	* @var		array RdqlResult
	* @access	private
	*/	
    var $RdqlResult;

 	/**
	* Current position
	* RdqlResultIterator does not use the build in PHP array iterator,
	* so you can use serveral iterators on a single Rdql result.
	* @var		integer
	* @access	private
	*/	
    var $position;
   
  
   /**
    * Constructor
    *
    * @param	object RdqlResult
	* @access	public
    */
    function RdqlResultIterator(&$RdqlResult) {
    	
     	$noResult = TRUE;
    	foreach($RdqlResult[0] as $value) 
      		if ($value != NULL) {
      	   		$noResult = FALSE;
      	   		break;
      		} 	
      		
      	if($noResult)  	     	
       		$this->RdqlResult = NULL;
       	else 
       		$this->RdqlResult = $RdqlResult;	

		$this->position = -1;
	}
 
  /**
   * Returns the labels of the result as array.
   * @return	array of strings with the result labels OR null if there are no results.
   * @access	public  
   */
  function getResultLabels() {
		if(count($this->RdqlResult)>0) {
  			return array_keys($this->RdqlResult[0]);
		} else return null;
   }

   /**
   * Returns the number of results.
   * @return	integer
   * @access	public  
   */
  function countResults() {
		
  			return count($this->RdqlResult);

   }
  /**
   * Returns TRUE if there are more results.
   * @return	boolean
   * @access	public  
   */
  function hasNext() {
  		if ($this->position < count($this->RdqlResult) - 1 ) {			
  			return TRUE;
		} else {
			return FALSE;
		}
   }

  /**
   * Returns TRUE if the first result has not been reached.
   * @return	boolean
   * @access	public  
   */
  function hasPrevious() {
  		if ($this->position > 0) {
			return TRUE;
		} else {
			return FALSE;
		}   }   
   
  /**
   * Returns the next result array.
   * @param 	integer $element	
   * @return	result array OR single result if $element was specified OR NULL if there is no next result.
   * @access	public  
   */
  function next($element = null) {
  		if ($this->position < count($this->RdqlResult) - 1) {
  			$this->position++;
			if ($element) {return $this->RdqlResult[$this->position][$element];}
  				else return $this->RdqlResult[$this->position];
		} else {
			return NULL;
		}
   }

  /**
   * Returns the previous result.
   * @param 	integer $element	
   * @return	result array OR single result if $element was specified OR NULL if there is no next result.
   * @access	public  
   */
  function previous($element = null) {
    	if ($this->position > 0) {
  			$this->position--;
			if ($element) {return $this->RdqlResult[$this->position][$element];}
  				else return $this->RdqlResult[$this->position];
		} else {
			return NULL;
		}   
  }

  /**
   * Returns the current result.
   * @param 	integer $element	
   * @return	result array OR single result if $element was specified OR NULL if there is no next result.
   * @access	public  
   */
  function current($element = null) {
  		if (($this->position >= 0) && ($this->position < count($this->RdqlResult))) {
			if ($element) {return $this->RdqlResult[$this->position][$element];}
  				else return $this->RdqlResult[$this->position];
		} else {
			return NULL;
		} 
   }
   
  /**
   * Moves the pointer to the first result.
   * @return	void
   * @access	public  
   */
  function moveFirst() {
  			$this->position = 0;
   }

  /**
   * Moves the pointer to the last result.
   * @return	void
   * @access	public  
   */
  function moveLast() {
  			$this->position = count($this->RdqlResult) - 1;
   }
   
     /**
   * Moves the pointer to a specific result.
   * If you set an off-bounds value, next(), previous() and current() will return NULL
   * @return	void
   * @access	public  
   */
  function moveTo($position) {
  			$this->position = $position;
   }
   
     /**
   * Returns the current position of the iterator.
   * @return	integer
   * @access	public  
   */
  function getCurrentPosition() {
  	 		return $this->position;
   }
  
} 

?>