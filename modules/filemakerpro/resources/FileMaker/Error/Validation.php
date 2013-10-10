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
 * Include parent class.
 */
require_once dirname(__FILE__) . '/../Error.php';

/**
 * Extension of FileMaker_Error for adding additional information
 * about validation errors.
 *
 * @package FileMaker
 */
class FileMaker_Error_Validation extends FileMaker_Error
{
    /**
     * Error array.
     *
     * @var array
     * @access private
     */
    var $_errors = array();

    /**
     * Add an error.
     *
     * @param FileMaker_Field $field The field object that validation failed on.
     * @param integer $rule The validation rule that failed.
     * @param string $value The value that failed validation.
     */
    function addError($field, $rule, $value)
    {
        $this->_errors[] = array($field, $rule, $value);
    }

    /**
     * Indicates whether or not the error is a detailed validation
     * error message, or if it is a server message.
     *
     * @return boolean True.
     */
    function isValidationError()
    {
        return true;
    }

    /**
     * Return the number of validation rules that failed.
     *
     * @return integer Number of failures.
     */
    function numErrors()
    {
        return count($this->_errors);
    }

    /**
     * Returns an array of arrays describing the validation errors
     * that occurred. Each entry in the outer array represents an
     * individual validation failure. Each failure is represented by a
     * three-element array with the following members:
     *
     * 0 => The field object for the field that had the validation problem.
     * 1 => The validation rule that failed for that field (a FILEMAKER_RULE_* constant).
     * 2 => The invalid value.
     *
     * Multiple validation rules can fail on a single field. If the
     * optional $fieldName parameter is set, then only failures for
     * that individual field will be returned.
     *
     * @param string $fieldName Get errors only for this field name.
     *
     * @return array Validation error details.
     */
    function getErrors($fieldName = null)
    {
        if ($fieldName === null) {
            return $this->_errors;
        }

        $errors = array();
        foreach ($this->_errors as $error) {
            if ($error[0]->getName() == $fieldName) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

}
