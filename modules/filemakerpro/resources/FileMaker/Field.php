<?php
/**
 * FileMaker PHP API.
 *
 * @package FileMaker
 *
 * Copyright © 2005-2006, FileMaker, Inc.Ê All rights reserved.
 * NOTE:Ê Use of this source code is subject to the terms of the FileMaker
 * Software License which accompanies the code.Ê Your use of this source code
 * signifies your agreement to such license terms and conditions.Ê Except as
 * expressly granted in the Software License, no other copyright, patent, or
 * other intellectual property license or right is granted, either expressly or
 * by implication, by FileMaker.
 */

/**
 * Load delegate.
 */
require_once dirname(__FILE__) . '/Implementation/FieldImpl.php';

/**
 * Field description class. Contains all the information about a
 * specific field in a layout.
 *
 * @package FileMaker
 */
class FileMaker_Field
{
    /**
     * Implementation. This is the object that actually implements the
     * layout functionality.
     *
     * @var FileMaker_Layout_Implementation
     * @access private
     */
    var $_impl;

    /**
     * Field Constructor.
     *
     * @param FileMaker_Layout &$layout The parent Layout object.
     */
    function FileMaker_Field(&$layout)
    {
        $this->_impl =& new FileMaker_Field_Implementation($layout);
    }

    /**
     * Return the name of this field.
     *
     * @return string Field name.
     */
    function getName()
    {
        return $this->_impl->getName();
    }

    /**
     * Return the FileMaker_Layout object that contains this field.
     *
     * @return FileMaker_Layout Layout object.
     */
    function &getLayout()
    {
        return $layout =& $this->_impl->getLayout();
    }

    /**
     * Return TRUE if this field is auto-entered and FALSE if it is
     * set manually.
     *
     * @return boolean Auto-entered status of this field.
     */
    function isAutoEntered()
    {
        return $this->_impl->isAutoEntered();
    }

    /**
     * Return TRUE if this field is global and FALSE if it is not.
     *
     * @return boolean Global status of this field.
     */
    function isGlobal()
    {
        return $this->_impl->isGlobal();
    }

    /**
     * Return the maximum number of repetitions for this field.
     *
     * @return integer Maximum repetitions of this field.
     */
    function getRepetitionCount()
    {
        return $this->_impl->getRepetitionCount();
    }

    /**
     * Returns TRUE if $value is valid for this field, or a
     * FileMaker_Error_Validation object describing how validation
     * failed.
     *
     * @param mixed $value Value to validate.
     * @param FileMaker_Error_Validation $error If validation is being done on more than
     * one field, you may pass an existing error object to validate() to be added to.
     * $error is not passed by reference, though, so you must catch the return value
     * of validate() and use it as the new $error object. An existing $error object will
     * never be overwritten with boolean true.
     *
     * @return boolean|FileMaker_Error_Validation Result of field validation on $value.
     */
    function validate($value, $error = null)
    {
        return $this->_impl->validate($value, $error);
    }

    /**
     * Returns an array of FILEMAKER_RULE_* constants for each rule
     * set on this field that can be evaluated by PHP. Rules such as
     * "unique" and "exists" can only be validated on the server and
     * are not included in this list.
     *
     * @return array Local rule array.
     */
    function getLocalValidationRules()
    {
        return $this->_impl->getLocalValidationRules();
    }

    /**
     * Returns an array of FILEMAKER_RULE_* constants for each rule
     * set on this field.
     *
     * @return array Rule array.
     */
    function getValidationRules()
    {
        return $this->_impl->getValidationRules();
    }

    /**
     * Returns the full additive bitmask of validation rules for this
     * field.
     *
     * @return integer Rule bitmask.
     */
    function getValidationMask()
    {
        return $this->_impl->getValidationMask();
    }

    /**
     * Returns TRUE if the given FILEMAKER_RULE_* constant matches the
     * field's validation bitmask, FALSE otherwise.
     *
     * @param integer $validationRule The validation rule constant to test presence of.
     *
     * @return boolean
     */
    function hasValidationRule($validationRule)
    {
        return $this->_impl->hasValidationRule($validationRule);
    }

    /**
     * Returns any additional information for a given rule. Used for
     * range rules and other rules that have additional validation
     * parameters.
     *
     * @param integer $validationRule The validation rule constant to get info for.
     *
     * @return array Any extra information for $validationRule.
     */
    function describeValidationRule($validationRule)
    {
        return $this->_impl->describeValidationRule($validationRule);
    }

    /**
     * Return an array of arrays containing the extra information for
     * all validation rules on this field that can be evaluated by
     * PHP. Rules such as "unique" and "exists" can only be validated
     * on the server and are not included in this list. Indexes of the
     * outer array are validation constants, and values are the same
     * array returned by describeValidationRule().
     *
     * @return array An associative array of all extra validation info,
     *               with rule constants as keys and extra info as the
     *               values.
     */
    function describeLocalValidationRules()
    {
        return $this->_impl->describeLocalValidationRules();
    }

    /**
     * Returns all additional information for all validation rules.
     *
     * @return array An associative array of all extra validation info,
     *               with rule constants as keys and extra info as the
     *               values.
     */
    function describeValidationRules()
    {
        return $this->_impl->describeValidationRules();
    }

    /**
     * Get the result type of this field - for example, 'text' or
     * 'number'.
     *
     * @return string Result type.
     */
    function getResult()
    {
        return $this->_impl->getResult();
    }

    /**
     * Returns the type of this field. Examples: 'normal',
     * 'calculation'.
     *
     * @return string Type.
     */
    function getType()
    {
        return $this->_impl->getType();
    }

    /**
     * Loads FMPXMLLAYOUT data. If this field is associated with a
     * value list, return the list of choices in that value
     * list. Otherwise returns NULL.
     *
     * @param integer $recid Record from which the value list should be displayed.
     * 
     * @return array The value list array.
     */
    function getValueList($recid = null)
    {
        return $this->_impl->getValueList($recid);
    }

    /**
     * Loads FMPXMLLAYOUT data. Returns the type of this field - for
     * example, 'EDITTEXT'.
     *
     * @return string Style type.
     */
    function getStyleType()
    {
        return $this->_impl->getStyleType();
    }

}
