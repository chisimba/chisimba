<?php
// ----------------------------------------------------------------------------------
// Class: RDF_Literal
// ----------------------------------------------------------------------------------
/**
 * An RDF literal.
 * The literal supports the xml:lang and rdf:datatype property.
 * For XML datatypes see: http://www.w3.org/TR/xmlschema-2/
 *
 * @version V0.7
 * @author Chris Bizer <chris@bizer.de>
 * @author Daniel Westphal <dawe@gmx.de>
 * @package model
 * @access public
 */

require_once 'RDF/Node.php';

class RDF_Literal extends RDF_Node
{
    /**
     * Label of the literal
     *
     * @var string
     * @access private
     */
    var $label;
    /**
     * Language of the literal
     *
     * @var string
     * @access private
     */
    var $lang;

    /**
     * Datatype of the literal
     *
     * @var string
     * @access private
     */
    var $dtype;

    /**
     * @param string $str label of the literal
     * @param string $language optional language identifier
     */
    function factory($str, $language = null)
    {
        $literal = new RDF_Literal;

        $literal->dtype = null;
        $literal->label = $str;

        if ($language != null) {
            $literal->lang = $language;
        } else {
            $literal->lang = null;
        }
        return $literal;
    }

    /**
     * Returns the string value of the literal.
     *
     * @access public
     * @return string value of the literal
     */
    function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the language of the literal.
     *
     * @access public
     * @return string language of the literal
     */
    function getLanguage()
    {
        return $this->lang;
    }

    /**
     * Sets the language of the literal.
     *
     * @access public
     * @param string $lang
     */
    function setLanguage($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Returns the datatype of the literal.
     *
     * @access public
     * @return string datatype of the literal
     */
    function getDatatype()
    {
        return $this->dtype;
    }

    /**
     * Sets the datatype of the literal.
     * Instead of datatype URI, you can also use an datatype shortcuts like STRING or INTEGER.
     * The array $GLOBALS['_RDF_default_datatype'] with the possible shortcuts is definded in ../constants.php
     *
     * @access public
     * @param string URI of XML datatype or datatype shortcut
     */
    function setDatatype($datatype)
    {
        if (stristr($datatype, RDF_DATATYPE_SHORTCUT_PREFIX)) {
            $this->dtype = $GLOBALS['_RDF_default_datatype'][substr($datatype, strlen(RDF_DATATYPE_SHORTCUT_PREFIX)) ];
        } else
            $this->dtype = $datatype;
    }

    /**
     * Checks if ihe literal equals another literal.
     * Two literals are equal, if they have the same label and they
     * have the same language/datatype or both have no language/datatype property set.
     *
     * @access public
     * @param object literal $that
     * @return boolean
     */
    function equals ($that)
    {
        if (($that == null) or !(is_a($that, 'RDF_Literal'))) {
            return false;
        }

        if ($this->label == $that->getLabel()
            && (
                ($this->lang == $that->getLanguage()
                    || ($this->lang == null && $that->getLanguage() == null)
                )
                && (
                    ($this->dtype == $that->getDatatype()
                        || ($this->dtype == null && $that->getDatatype() == null)
                    )
                )
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Dumps literal.
     *
     * @access public
     * @return string
     */
    function toString()
    {
        $dump = 'Literal("' . $this->label . '"';
        if ($this->lang != null) {
            $dump .= ', lang="' . $this->lang . '"';
        }
        if ($this->dtype != null) {
            $dump .= ', datatype="' . $this->dtype . '"';
        }
        $dump .= ')';
        return $dump;
    }
} // end: Literal
?>