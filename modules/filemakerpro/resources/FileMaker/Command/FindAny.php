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
require_once dirname(__FILE__) . '/../Implementation/Command/FindAnyImpl.php';

/**
 * Find a random record.
 *
 * @package FileMaker
 */
class FileMaker_Command_FindAny extends FileMaker_Command_Find
{
    /**
     * Implementation
     *
     * @var FileMaker_Command_FindAny_Implementation
     * @access private
     */
    var $_impl;

    /**
     * FindAny command constructor.
     *
     * @ignore
     * @param FileMaker_Implementation $fm The FileMaker_Implementation object the command was created by.
     * @param string $layout The layout to find a random record from.
     */
    function FileMaker_Command_FindAny($fm, $layout)
    {
        $this->_impl =& new FileMaker_Command_FindAny_Implementation($fm, $layout);
    }

}
