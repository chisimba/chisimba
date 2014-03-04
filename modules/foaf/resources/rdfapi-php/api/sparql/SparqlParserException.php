<?php
// ---------------------------------------------
// class: SparqlParserExecption
// ---------------------------------------------
/**
* A SPARQL Parser Execption for better errorhandling.
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 $Id: SparqlParserException.php 7228 2007-09-27 06:24:51Z kudakwashe $
*
* @package sparql
*/
Class SparqlParserException extends Exception{
 
	private $tokenPointer;

	public function __construct($message, $code = 0, $pointer){

		$this->tokenPointer = $pointer;
		parent::__construct($message, $code);
	}

	/**
	* Returns a pointer to the token which caused the exception.
	* @return int
	*/
	public function getPointer(){
		return $this->tokenPointer;
	}

}
?>
