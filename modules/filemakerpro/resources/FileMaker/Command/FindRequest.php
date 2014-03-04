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
require_once dirname(__FILE__) . '/../Implementation/Command/FindRequestImpl.php';

/**
 * Individual find requests that belong to a compound find.
 *
 * @package FileMaker
 */
class FileMaker_Command_FindRequest
{
    /**
     * Implementation
     *
     * @var FileMaker_Command_Find_Implementation
     * @access private
     */
    var $_impl;

    /**
     * Find request constructor.
     *
     * @ignore
     * @param FileMaker_Implementation $fm The FileMaker_Implementation object the request was created by.
     * @param string $layout The layout to find records in.
     */
    function FileMaker_Command_FindRequest($fm, $layout)
    {
        $this->_impl =& new FileMaker_Command_FindRequest_Implementation($fm, $layout);
    }

    /**
     * Sets if the find request is an omit request.
     *
     * @param boolean $value true or false.
     */
    function setOmit($value)
    {
        $this->_impl->setOmit($value);
    }

    /**
     * Add a find criterion.
     *
     * @param string $fieldname The field being tested.
     * @param string $testvalue The value to test against.
     */
    function addFindCriterion($fieldname, $testvalue)
    {
        $this->_impl->addFindCriterion($fieldname, $testvalue);
    }
    
    /**
     * Clear all existing find criteria.
     */
    function clearFindCriteria()
    {
        $this->_impl->clearFindCriteria();
    }

	   
}
