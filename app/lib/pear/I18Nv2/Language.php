<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Language                                           |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is available at http://www.php.net/license/3_0.txt              |
// | If you did not receive a copy of the PHP license and are unable      |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Michael Wallner <mike@iworks.at>                  |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * I18Nv2::Language
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

require_once 'I18Nv2/CommonList.php';

/**
 * I18Nv2_Language
 * 
 * List of ISO-639-1 two letter language code to language name mapping.
 * 
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @package     I18Nv2
 * @access      public
 */
class I18Nv2_Language extends I18Nv2_CommonList
{
    /**
     * Load language file
     *
     * @access  proteceted
     * @return  bool
     * @param   string  $language
     */
    function loadLanguage($language)
    {
        return @include 'I18Nv2/Language/' . $language . '.php';
    }

    /**
     * Change case of code key
     *
     * @access  protected
     * @return  string
     * @param   string  $code
     */
    function changeKeyCase($code)
    {
        return strToLower($code);
    }
}
?>
