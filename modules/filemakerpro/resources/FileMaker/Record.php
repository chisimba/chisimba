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
 * Load delegate.
 */
require_once dirname(__FILE__) . '/Implementation/RecordImpl.php';

/**
 * This is the default Record class for representing each member of a
 * result set. Records can have references to their parent or child
 * records, can be edited or deleted, etc. You can also specify a
 * different class to use for Records; that class should be a subclass
 * of this base class, or encapsulate its functionality. In PHP5 this
 * class would implement an interface that alternate classes would be
 * required to implement as well.
 *
 * @package FileMaker
 */
class FileMaker_Record
{
    /**
     * The Implementation that implements this record.
     *
     * @var FileMaker_Record_Implementation
     * @access private
     */
    var $_impl;

    /**
     * Record constructor.
     *
     * @param FileMaker_Layout|FileMaker_RelatedSet Either the layout
     * or related set object this record is a member of.
     */
    function FileMaker_Record(&$layout)
    {
        $this->_impl =& new FileMaker_Record_Implementation($layout);
    }

    /**
     * Return the layout this record is part of.
     *
     * @return FileMaker_Layout This record's layout.
     */
    function &getLayout()
    {
        return $this->_impl->getLayout();
    }

    /**
     * Return a list of the names of all fields in the record. Just
     * the names are returned; if additional information is required
     * then the Layout object provided by the parent
     * FileMaker_Result object's getLayout() method must be
     * consulted.
     *
     * @return array Field names
     */
    function getFields()
    {
        return $this->_impl->getFields();
    }

    /**
     * Get the HTML-encoded value of $field.
     *
     * @param string $field The field name to fetch.
     * @param integer $repetition The repetition number to get,
     *                            defaults to the first repetition.
     *
     * @return string The field value.
     */
    function getField($field, $repetition = 0)
    {
        return $this->_impl->getField($field, $repetition);
    }
    
	/**
     * Get the unencoded value of $field.
     *
     * @param string $field The field name to fetch.
     * @param integer $repetition The repetition number to get,
     *                            defaults to the first repetition.
     *
     * @return string The unencoded field value.
     */
    function getFieldUnencoded($field, $repetition = 0)
    {
        return $this->_impl->getFieldUnencoded($field, $repetition);
    }

    /**
     * Return the value of the specified field (and repetition) as a
     * unix timestamp. If the field is a date field, the timestamp is
     * for the field date at midnight. It the field is a time field,
     * the timestamp is for that time on January 1, 1970. Timestamp
     * (date & time) fields map directly to the unix timestamp. If the
     * specified field is not a date or time field, or if the
     * timestamp generated would be out of range, then we return a
     * FileMaker_Error object instead.
     *
     * @return integer The timestamp value.
     */
    function getFieldAsTimestamp($field, $repetition = 0)
    {
        return $this->_impl->getFieldAsTimestamp($field, $repetition);
    }

    /**
     * Set the value of $field.
     *
     * @param string $field The field to change.
     * @param string $value The new value.
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
     * Get the record id of this object.
     *
     * @return integer The record id.
     */
    function getRecordId()
    {
        return $this->_impl->getRecordId();
    }

    /**
     * Get the modification id of this object.
     *
     * @return integer The modification id.
     */
    function getModificationId()
    {
        return $this->_impl->getModificationId();
    }

    /**
     * Get any objects in the related set (portal) $relatedSet.
     *
     * @param string $relatedSet The name of the related set (portal) to return records for.
     *
     * @return array An array of FileMaker_Record objects from $relatedSet.
     */
    function &getRelatedSet($relatedSet)
    {
        return $this->_impl->getRelatedSet($relatedSet);
    }

    /**
     * Create a new record in the related set (portal) named by $relatedSet.
     *
     * @param string $relatedSet The name of the portal to create a new record in.
     *
     * @return FileMaker_Record The blank record.
     */
    function &newRelatedRecord($relatedSet)
    {
        return $this->_impl->newRelatedRecord($this, $relatedSet);
    }

    /**
     * If this is a child record, return its parent.
     *
     * @return FileMaker_Record The parent record.
     */
    function &getParent()
    {
        return $this->_impl->getParent();
    }

    /**
     * Validates either a single field or the whole record against the
     * validation rules that are enforceable on the PHP side - type
     * rules, ranges, four-digit dates, etc. Rules such as unique or
     * existing, or validation by calculation field, cannot be
     * pre-validated.
     *
     * If the optional $fieldName argument is passed, only that field
     * will be validated. Otherwise the record will be validated just
     * as if commit() were called with prevalidation turned on in the
     * API properties. validate() returns TRUE if validation passes,
     * or a FileMaker_Error_Validation object containing details about
     * what failed to validate.
     *
     * @return boolean|FileMaker_Error_Validation Result of field validation on $value.
     */
    function validate($fieldName = null)
    {
        return $this->_impl->validate($fieldName);
    }

    /**
     * Save any changes to this record back to the server.
     *
     * @return boolean True, or a FileMaker_Error on failure.
     */
    function commit()
    {
        return $this->_impl->commit();
    }

    /**
     * Delete this record from the server.
     *
     * @return FileMaker_Result The response object.
     */
    function delete()
    {
        return $this->_impl->delete();
    }
    
    
    /**
     * Gets a specific related record. 
     *
     * @access private
     *
     * @param string $relatedSetName The name of the related set.
     * @param string $recordId The record id of the record in the related set.
     * 
     * @return FileMaker_Response The response object..
     */
    function getRelatedRecordById($relatedSetName, $recordId)
    {	
    	return $this->_impl->getRelatedRecordById($relatedSetName, $recordId);	
    }

}
