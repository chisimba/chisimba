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
 * Make sure that the PEAR base class is loaded. We fall back to a
 * bundled version if it's not found in the include_path.
 */
@include_once 'PEAR.php';
if (!class_exists('PEAR_Error')) {
    include_once 'FileMaker/PEAR.php';
}

/**
 * Extension of PEAR_Error for use in all FileMaker classes.
 *
 * @package FileMaker
 */
class FileMaker_Error extends PEAR_Error
{
    /**
     * FileMaker object the error was generated from.
     *
     * @var FileMaker
     * @access private
     */
    var $_fm;

    /**
     * Overloaded constructor.
     *
     * @param FileMaker_Delegate &$fm The FileMaker_Delegate object this error came from.
     * @param string $message Error message.
     * @param integer $code Error code.
     */
    function FileMaker_Error(&$fm, $message = null, $code = null)
    {
        $this->_fm =& $fm;
        parent::PEAR_Error($message, $code);

        // Log the error.
        $fm->log($this->getMessage(), FILEMAKER_LOG_ERR);
    }

    /**
     * Overload getMessage() to return XML error equivalents if no
     * message is explicitly set and we have an error code.
     *
     * @return string Error message.
     */
    function getMessage()
    {
        if ($this->message === null && $this->getCode() !== null) {
            return $this->getErrorString();
        }
        return parent::getMessage();
    }

    /**
     * Return the string representation of $this->code, in the
     * language currently set on $this->_fm. You should call
     * getMessage() in most cases if you are not sure that the error
     * is an XML error with an error code.
     *
     * @return string The error description.
     */
    function getErrorString()
    {
        // Default to English.
        $lang = basename($this->_fm->getProperty('locale'));
        if (!$lang) {
            $lang = 'en';
        }

        static $strings = array();
        if (empty($strings[$lang])) {
            if (!@include_once dirname(__FILE__) . '/Error/' . $lang . '.php') {
                include_once dirname(__FILE__) . '/Error/en.php';
            }
            $strings[$lang] = $__FM_ERRORS;
        }

        if (isset($strings[$lang][$this->getCode()])) {
            return $strings[$lang][$this->getCode()];
        }

        return $strings[$lang][-1];
    }

    /**
     * Indicates whether or not the error is a detailed validation
     * error message, or if it is a server message.
     *
     * @return boolean False.
     */
    function isValidationError()
    {
        return false;
    }

}
