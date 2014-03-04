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
 * Load delegate and field classes.
 */
require_once dirname(__FILE__) . '/Implementation/LayoutImpl.php';
require_once dirname(__FILE__) . '/Field.php';

/**
 * Layout description class. Contains all the information about a
 * specific layout. Can be requested directly, or returned as part of
 * a result set.
 *
 * @package FileMaker
 */
class FileMaker_Layout
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
     * Layout constructor.
     *
     * @param FileMaker_Implementation &$fm The FileMaker_Implementation object this layout was created through.
     */
    function FileMaker_Layout(&$fm)
    {
        $this->_impl =& new FileMaker_Layout_Implementation($fm);
    }

    /**
     * Returns the name of the layout this object describes.
     *
     * @return string Layout name.
     */
    function getName()
    {
        return $this->_impl->getName();
    }

    /**
     * Return the name of the database this layout is in.
     *
     * @return string Database name.
     */
    function getDatabase()
    {
        return $this->_impl->getDatabase();
    }

    /**
     * Return an array with the string names of all fields in this layout.
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
     * Return an array with the string names of all related sets in
     * this layout.
     *
     * @return array Simple list of string related set names.
     */
    function listRelatedSets()
    {
        return $this->_impl->listRelatedSets();
    }

    /**
     * Returns the FileMaker_RelatedSet object describing $relatedSet.
     *
     * @return FileMaker_RelatedSet|FileMaker_Error Either a RelatedSet object or an error.
     */
    function &getRelatedSet($relatedSet)
    {
        return $this->_impl->getRelatedSet($relatedSet);
    }

    /**
     * Return an associative array with the names of all related sets
     * as keys, and the array values will be the associated
     * FileMaker_RelatedSet objects.
     *
     * @return array Array of FileMaker_RelatedSet objects.
     */
    function &getRelatedSets()
    {
        return $this->_impl->getRelatedSets();
    }

    /**
     * Return the names of any value lists associated with this
     * layout.
     *
     * @return array Simple list of string value list names.
     */
    function listValueLists()
    {
        return $this->_impl->listValueLists();
    }

    /**
     * Return the list of options in the named value list.
     *
     * @param integer $recid Record from which the value list should be displayed.
     *
     * @return array List of options in $valueList.
     */
    function getValueList($valueList, $recid = null)
    {
        return $this->_impl->getValueList($valueList, $recid);
    }

    /**
     * Return a multi-level associative array. The top-level array has
     * names of value lists as keys and arrays as values. The second
     * level arrays are the lists of choices from each value list.
     *
     * @param integer $recid Record from which the value list should be displayed.
     * 
     * @return array Array of value-list arrays.
     */
    function getValueLists($recid = null)
    {
        return $this->_impl->getValueLists($recid);
    }

    /**
     * Load extended (FMPXMLLAYOUT) layout information.
     *
     * @access private
     *
     * @param integer $recid Record from which extended info should be loaded. Currently, this is used for grabbing related value lists. 
     *
     * @return boolean|FileMaker_Error True or an error.
     */
    function loadExtendedInfo($recid = null)
    {
        return $this->_impl->loadExtendedInfo($recid);
    }

}
