<?php
/**
 * FileMaker PHP API.
 *
 * @package FileMaker
 *
 * Copyright � 2005-2006, FileMaker, Inc.� All rights reserved.
 * NOTE:� Use of this source code is subject to the terms of the FileMaker
 * Software License which accompanies the code.� Your use of this source code
 * signifies your agreement to such license terms and conditions.� Except as
 * expressly granted in the Software License, no other copyright, patent, or
 * other intellectual property license or right is granted, either expressly or
 * by implication, by FileMaker.
 */

/**
 * Include delegate.
 */
require_once dirname(__FILE__) . '/Implementation/CommandImpl.php';

/**
 * Base Command object.
 *
 * @package FileMaker
 */
class FileMaker_Command
{
    /**
     * Implementation. This is the object that actually implements the
     * command base.
     *
     * @var FileMaker_Command_Implementation
     * @access private
     */
    var $_impl;

    /**
     * Request the result to be returned in a layout different than
     * the one being queried against.
     *
     * @param string $layout The layout to return results using.
     */
    function setResultLayout($layout)
    {
        $this->_impl->setResultLayout($layout);
    }

    /**
     * Set a script to be run after the result set is generated and sorted.
     *
     * @param string $scriptName The name of the script to run.
     * @param string $scriptParameters Any parameters to pass to the script.
     */
    function setScript($scriptName, $scriptParameters = null)
    {
        $this->_impl->setScript($scriptName, $scriptParameters);
    }

    /**
     * Set a script to be run before performing the find and sorting the result set.
     *
     * @param string $scriptName The name of the script to run.
     * @param string $scriptParameters Any parameters to pass to the script.
     */
    function setPreCommandScript($scriptName, $scriptParameters = null)
    {
        $this->_impl->setPreCommandScript($scriptName, $scriptParameters);
    }

    /**
     * Set a script to be run after performing the find, but before sorting the result set.
     *
     * @param string $scriptName The name of the script to run.
     * @param string $scriptParameters Any parameters to pass to the script.
     */
    function setPreSortScript($scriptName, $scriptParameters = null)
    {
        $this->_impl->setPreSortScript($scriptName, $scriptParameters);
    }

    /**
     * Set the PHP class that will be instantiated to represent
     * records returned in any result set. The default is to use the
     * provided FileMaker_Record class. Any substitute classes must
     * provide the same API that FileMaker_Record does, either by
     * extending it or re-implementing the necessary methods. The user
     * is responsible for defining any custom class before the API
     * will need to instantiate it.
     *
     * @param string $className
     */
    function setRecordClass($className)
    {
        $this->_impl->setRecordClass($className);
    }

    /**
     * Validates either a single field or the whole command against
     * the validation rules that are enforceable on the PHP side -
     * type rules, ranges, four-digit dates, etc. Rules such as unique
     * or existing, or validation by calculation field, cannot be
     * pre-validated.
     *
     * If the optional $fieldName argument is passed, only that field
     * will be validated. Otherwise the command will be validated just
     * as if execute() were called with prevalidation turned on in the
     * API properties. validate() returns TRUE if validation passes,
     * or a FileMaker_Error_Validation object containing details about
     * what failed to validate.
     *
     * @param string $fieldName Only validate this field. If empty, validate the whole record.
     * @return boolean|FileMaker_Error_Validation Result of field validation on $value.
     */
    function validate($fieldName = null)
    {
        return $this->_impl->validate($fieldName);
    }

    /**
     * Run the command.
     *
     * @return FileMaker_Result A result object.
     */
    function execute()
    {
        return $this->_impl->execute();
    }

    /**
     * Set the record id for this command. For Edit, Delete, and
     * Duplicate commands a record id must be specified. It is also
     * possible to find a single record by specifying its record
     * id. This method will be ignored by Add and FindAny commands.
     *
     * @param integer $recordId The record id.
     */
    function setRecordId($recordId)
    {
        $this->_impl->setRecordId($recordId);
    }

}
