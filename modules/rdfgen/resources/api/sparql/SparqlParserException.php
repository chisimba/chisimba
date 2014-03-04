<?php
// ---------------------------------------------
// class: SparqlParserExecption
// ---------------------------------------------
/**
* A SPARQL Parser Execption for better errorhandling.
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 $Id$
* @license http://www.gnu.org/licenses/lgpl.html LGPL
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
