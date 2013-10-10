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
require_once dirname(__FILE__) . '/Find.php';
require_once dirname(__FILE__) . '/../Implementation/Command/FindAllImpl.php';

/**
 * Find all records in a layout.
 *
 * @package FileMaker
 */
class FileMaker_Command_FindAll extends FileMaker_Command_Find
{
    /**
     * Implementation
     *
     * @var FileMaker_Command_FindAll_Implementation
     * @access private
     */
    var $_impl;

    /**
     * FindAll command constructor.
     *
     * @ignore
     * @param FileMaker_Implementation $fm The FileMaker_Implementation object the command was created by.
     * @param string $layout The layout to find all records in.
     */
    function FileMaker_Command_FindAll($fm, $layout)
    {
        $this->_impl =& new FileMaker_Command_FindAll_Implementation($fm, $layout);
    }

}
