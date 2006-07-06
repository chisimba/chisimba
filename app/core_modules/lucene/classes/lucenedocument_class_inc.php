<?php

/**
 *  Document indexer Lucene integration class
 *
 * @package lucene
 * @category Chisimba
 * @author Paul Scott
 * @copyright AVOIR UWC GNU/GPL
 * @filesource
 */

class lucenedocument extends object
{
	/**
 	 * A Document is a set of fields. Each field has a name and a textual value.
 	 */

	/**
     * Associative array search_Lucene_Field objects where the keys to the
     * array are the names of the fields.
     *
     * @var array
     */
    protected $_fields = array();

    public $boost = 1.0;


    /**
     * Proxy method for getFieldValue(), provides more convenient access to
     * the string value of a field.
     *
     * @param  $offset
     * @return string
     */
	public function __get($offset)
	{
		return $this->getFieldValue($offset);
	}

	/**
     * Add a field object to this document.
     *
     * @param search_Lucene_Field $field
     */
    public function addField(lucenefield $field)
    {
        $this->_fields[$field->name] = $field;
    }


    /**
     * Return an array with the names of the fields in this document.
     *
     * @return array
     */
    public function getFieldNames()
    {
    	return array_keys($this->_fields);
    }

    /**
     * Returns search_Lucene_Field object for a named field in this document.
     *
     * @param string $fieldName
     * @return search_Lucene_Field
     */
    public function getField($fieldName)
    {
		if (!array_key_exists($fieldName, $this->_fields)) {
			throw new customException("Field name \"$fieldName\" not found in document.");
		}
        return $this->_fields[$fieldName];
    }


    /**
     * Returns the string value of a named field in this document.
     *
     * @see __get()
     * @return string
     */
    public function getFieldValue($fieldName)
    {
    	return $this->getField($fieldName)->stringValue;
    }

}
?>