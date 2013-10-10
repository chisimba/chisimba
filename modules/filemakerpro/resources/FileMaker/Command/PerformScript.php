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
require_once dirname(__FILE__) . '/../Implementation/Command/PerformScriptImpl.php';

/**
 * Perform a script.
 *
 * @package FileMaker
 */
class FileMaker_Command_PerformScript extends FileMaker_Command
{
    /**
     * Implementation
     *
     * @var FileMaker_Command_PerformScript_Implementation
     * @access private
     */
    var $_impl;

    /**
     * PerformScript command constructor.
     *
     * @ignore
     * @param FileMaker_Implementation $fm The FileMaker_Implementation object the command was created by.
     * @param string $layout The layout to use for script context.
     * @param string $scriptName The name of the script to run.
     * @param string $scriptParameters Any parameters to pass to the script.
     */
    function FileMaker_Command_PerformScript($fm, $layout, $scriptName, $scriptParameters = null)
    {
        $this->_impl =& new FileMaker_Command_PerformScript_Implementation($fm, $layout, $scriptName, $scriptParameters);
    }

}
