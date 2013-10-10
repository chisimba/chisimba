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
 * Include delegate.
 */
require_once dirname(__FILE__) . '/Implementation/ResultImpl.php';

/**
 * Base Result object.
 *
 * @package FileMaker
 */
class FileMaker_Result
{
    /**
     * The delegate that implements this response.
     *
     * @var FileMaker_Result_Implementation
     * @access private
     */
    var $_impl;

    /**
     * Result constructor.
     *
     * @param FileMaker_Implementation &$fm The FileMaker_Implementation object this response came from.
     */
    function FileMaker_Result(&$fm)
    {
        $this->_impl =& new FileMaker_Result_Implementation($fm);
    }

    /**
     * Get the FileMaker_Layout object describing the layout of this
     * response.
     *
     * @return FileMaker_Layout The layout description.
     */
    function &getLayout()
    {
        return $this->_impl->getLayout();
    }

    /**
     * Returns an array containing each record in the result set. Each
     * member of the array is a FileMaker_Record object, or an
     * instance of the class name set in the API for instantiating
     * Records. The array may be empty if the response contains no
     * records.
     *
     * @return array The record objects.
     */
    function &getRecords()
    {
        return $this->_impl->getRecords();
    }

    /**
     * Return a list of the names of all fields in the records that
     * are part of this response. Just the names are returned; if
     * additional information is required then Layout object provided
     * by getLayout() must be consulted.
     *
     * @return array String field names.
     */
    function getFields()
    {
        return $this->_impl->getFields();
    }

    /**
     * Return the names of all related sets present in this record.
     *
     * @return array String related set names.
     */
    function getRelatedSets()
    {
        return $this->_impl->getRelatedSets();
    }

    /**
     * Returns the number of records in the table that was accessed.
     *
     * @return integer Table count.
     */
    function getTableRecordCount()
    {
        return $this->_impl->getTableRecordCount();
    }

    /**
     * Returns the number of records in the entire found set.
     *
     * @return integer Found count.
     */
    function getFoundSetCount()
    {
        return $this->_impl->getFoundSetCount();
    }

    /**
     * Returns the number of records in the set that was actually
     * returned. If no range parameters were specified this will be
     * equal to the result of getFoundSetCount(). It will always be
     * equal to the value of count($response->getRecords()).
     *
     * @return integer Fetch count.
     */
    function getFetchCount()
    {
        return $this->_impl->getFetchCount();
    }
    
    /**
     * Returns the first record in this result set.
     *
     * @return FileMaker_Record object The first record in the result set.
     */
    function getFirstRecord()
    {
    	return $this->_impl->getFirstRecord();
    }
    
    /**
     * Returns the last record in this result set.
     *
     * @return FileMaker_Record object The last record in the result set.
     */
   	function getLastRecord()
    {
    	return $this->_impl->getLastRecord();
    }

}
