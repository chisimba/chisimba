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
 * Include parent and delegate classesa.
 */
require_once dirname(__FILE__) . '/../Command.php';
require_once dirname(__FILE__) . '/../Implementation/Command/EditImpl.php';

/**
 * Edit a single record and/or some of its child records.
 *
 * @package FileMaker
 */
class FileMaker_Command_Edit extends FileMaker_Command
{
    /**
     * Implementation
     *
     * @var FileMaker_Command_Edit_Implementation
     * @access private
     */
    var $_impl;

    /**
     * Edit command constructor.
     *
     * @ignore
     * @param FileMaker_Implementation $fm The FileMaker_Implementation object the command was created by.
     * @param string $layout The layout the record is part of.
     * @param integer $recordId The id of the record to edit.
     * @param array $values A hash of fieldname => value pairs. Repetions can be set
     * by making the value for a field a numerically indexed array, with the numeric keys
     * corresponding to the repetition number to set.
     */
    function FileMaker_Command_Edit($fm, $layout, $recordId, $updatedValues = array())
    {
        $this->_impl =& new FileMaker_Command_Edit_Implementation($fm, $layout, $recordId, $updatedValues);
    }

    /**
     * Set the new value for a field.
     *
     * @param string $field The field to set.
     * @param string $value The value for the field.
     * @param integer $repetition The repetition number to set,
     *                            defaults to the first repetition.
     */
    function setField($field, $value, $repetition = 0)
    {
        return $this->_impl->setField($field, $value, $repetition);
    }

    /**
     * Set the new value for a date, time, or timestamp field from a
     * unix timestamp value. If the field is not a date or time field,
     * then an error is returned. Otherwise returns true.
     *
     * If we haven't already loaded layout data for the target of this
     * command, calling this method will cause it to be loaded so that
     * the type of the field can be checked.
     *
     * @param string $field The field to set.
     * @param string $timestamp The timestamp value.
     * @param integer $repetition The repetition number to set,
     *                            defaults to the first repetition.
     */
    function setFieldFromTimestamp($field, $timestamp, $repetition = 0)
    {
        return $this->_impl->setFieldFromTimestamp($field, $timestamp, $repetition);
    }

    /**
     * Set the modification id for this command.
     *
     * @param integer $modificationId The modification id.
     */
    function setModificationId($modificationId)
    {
        $this->_impl->setModificationId($modificationId);
    }

}
