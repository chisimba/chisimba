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
require_once dirname(__FILE__) . '/Implementation/RelatedSetImpl.php';

/**
 * Related set description class. Contains all the information about a
 * specific related set.
 *
 * @package FileMaker
 */
class FileMaker_RelatedSet
{
    /**
     * Implementation. This is the object that actually implements the
     * related set functionality.
     *
     * @var FileMaker_RelatedSet_Implementation
     * @access private
     */
    var $_impl;

    /**
     * Related set constructor.
     *
     * @param FileMaker_Layout &$layout The FileMaker_Layout object this related set is part of.
     */
    function FileMaker_RelatedSet(&$layout)
    {
        $this->_impl =& new FileMaker_RelatedSet_Implementation($layout);
    }

    /**
     * Returns the name of the related set this object describes.
     *
     * @return string Related set name.
     */
    function getName()
    {
        return $this->_impl->getName();
    }

    /**
     * Return an array with the string names of all fields in this
     * related set.
     *
     * @return array Simple list of string field names.
     */
    function listFields()
    {
        return $this->_impl->listFields();
    }

    /**
     * Returns the FileMaker_Field object describing $fieldName.
     *
     * @return FileMaker_Field|FileMaker_Error Either a Field object or an error.
     */
    function &getField($fieldName)
    {
        return $this->_impl->getField($fieldName);
    }

    /**
     * Return an associative array with the names of all fields as
     * keys, and the array values will be the associated
     * FileMaker_Field objects.
     *
     * @return array Array of FileMaker_Field objects.
     */
    function &getFields()
    {
        return $this->_impl->getFields();
    }

    /**
     * Load extended (FMPXMLLAYOUT) layout information.
     *
     * @return boolean|FileMaker_Error True or an error.
     */
    function loadExtendedInfo()
    {
        return $this->_impl->loadExtendedInfo();
    }

}
