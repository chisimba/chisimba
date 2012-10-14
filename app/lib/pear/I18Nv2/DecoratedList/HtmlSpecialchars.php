<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: DecoratedList :: HtmlSpecialchars                  |
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
 * I18Nv2::DecoratedList::HtmlSpecialchars
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

require_once 'I18Nv2/DecoratedList.php';

/**
 * I18Nv2_Decorator_HtmlSpecialchars
 * 
 * When you are going to serve XHTML as XML or XHTML+XML then you will get 
 * problems while displaying umlauts etc. as their HTML entities.
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @package     I18Nv2
 * @access      public
 */
class I18Nv2_DecoratedList_HtmlSpecialchars extends I18Nv2_DecoratedList
{
    /** 
     * decorate
     * 
     * @access  protected
     * @return  mixed
     * @param   mixed   $value
     */
    function decorate($value)
    {
        if (is_string($value)) {
            return htmlSpecialchars($value, ENT_QUOTES, 
                $this->list->getEncoding());
        } elseif (is_array($value)) {
            return array_map(array(&$this, 'decorate'), $value);
        }
        return $value;
    }
}
?>
