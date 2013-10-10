<?php
require_once RDFAPI_INCLUDE_DIR . 'model/Node.php';

// ----------------------------------------------------------------------------------
// Class: Literal
// ----------------------------------------------------------------------------------

/**
 * An RDF literal.
 * The literal supports the xml:lang and rdf:datatype property.
 * For XML datatypes see: http://www.w3.org/TR/xmlschema-2/
 *
 * @version  $Id: Literal.php 497 2007-08-13 05:14:26Z cweiske $
 * @author Chris Bizer <chris@bizer.de>
 * @author Daniel Westphal <dawe@gmx.de>
 *
 * @package model
 * @access	public
 *
 */
 class Literal extends Node {

	/**
	* Label of the literal
	* @var		string
	* @access	private
	*/
    var $label;
   /**
	* Language of the literal
	* @var		string
	* @access	private
	*/
    var $lang;

   /**
	* Datatype of the literal
	* @var		string
	* @access	private
	*/
    var $dtype;


   /**
    * Constructor
    *
    * @param string $str      label of the literal
    * @param string $language optional language identifier
    * @param string $datatype optional datatype
    *
    */
    function Literal($str, $language = NULL, $datatype = null)
    {
        $this->label = $str;

        if ($language != NULL) {
            $this->lang = $language;
        } else {
            $this->lang = NULL;
        }

        if ($datatype != null) {
            $this->dtype = $datatype;
        } else {
            $this->dtype = NULL;
        }
    }

  /**
   * Returns the string value of the literal.
   *
   * @access	public
   * @return	string value of the literal
   */
  function getLabel() {

    return $this->label;
  }

  /**
   * Returns the language of the literal.
   *
   * @access	public
   * @return	string language of the literal
   */
  function getLanguage() {

    return $this->lang;
  }

  /**
   * Sets the language of the literal.
   *
   * @access	public
   * @param	    string $lang
   */
  function setLanguage($lang) {

    $this->lang = $lang;
  }

  /**
   * Returns the datatype of the literal.
   *
   * @access	public
   * @return	string datatype of the literal
   */
  function getDatatype() {

    return $this->dtype;
  }

  /**
   * Sets the datatype of the literal.
   * Instead of datatype URI, you can also use an datatype shortcuts like STRING or INTEGER.
   * The array $short_datatype with the possible shortcuts is definded in ../constants.php
   *
   * @access	public
   * @param		string URI of XML datatype or datatype shortcut
   *
   */
  function setDatatype($datatype) {
	GLOBAL $short_datatype;
	if  (stristr($datatype,DATATYPE_SHORTCUT_PREFIX))  {
		$this->dtype = $short_datatype[substr($datatype,strlen(DATATYPE_SHORTCUT_PREFIX)) ];}
	else
	    $this->dtype = $datatype;
  }

  /**
   * Checks if ihe literal equals another literal.
   * Two literals are equal, if they have the same label and they
   * have the same language/datatype or both have no language/datatype property set.
   *
   * @access	public
   * @param		object	literal $that
   * @return	boolean
   */
  function equals ($that) {

	if (($that == NULL) or !(is_a($that, 'Literal'))) {
	  return false;
    }

	if ( ($this->label == $that->getLabel()) && ( ( ($this->lang == $that->getLanguage()) ||
	   ($this->lang == NULL && $that->getLanguage() == NULL) )  &&

	   (
	   ($this->dtype == $that->getDatatype() ||
	   ($this->dtype == NULL && $that->getDatatype() == NULL)) ) ) )
	     {
			return true;
	     }

    return false;
  }

  /**
   * Dumps literal.
   *
   * @access	public
   * @return	string
   */
  function toString() {
	$dump = 'Literal("' . $this->label .'"';
	if ($this->lang != NULL)
		$dump .= ', lang="' . $this->lang .'"';
	if ($this->dtype != NULL)
		$dump .= ', datatype="' . $this->dtype .'"';
	$dump .= ')';
    return $dump;
  }



    /**
    *   Doing string magic in PHP5
    *   @return string String representation of this Literal
    */
    function __toString()
    {
        return $this->toString();
    }


} // end: Literal

?>