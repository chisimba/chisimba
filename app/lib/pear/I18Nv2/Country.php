<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Country                                            |
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
 * I18Nv2::Country
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

require_once 'I18Nv2/CommonList.php';

/**
 * I18Nv2_Country
 * 
 * List of ISO-3166 two letter country code to country name mapping.
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @access      public
 * @package     I18Nv2
 */
class I18Nv2_Country extends I18Nv2_CommonList
{
    /**
     * Load language file
     *
     * @access  protected
     * @return  bool
     * @param   string  $language
     */
    function loadLanguage($language)
    {
        return @include 'I18Nv2/Country/' . $language . '.php';
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
        return strToUpper($code);
    }
}
?>
