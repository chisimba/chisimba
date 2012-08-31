<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: DecoratedList :: HtmlEntities                      |
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
 * I18Nv2::DecoratedList::HtmlEntities
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

require_once 'I18Nv2/DecoratedList.php';

/**
 * I18Nv2_Decorator_HtmlEntities
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @package     I18Nv2
 * @access      public
 */
class I18Nv2_DecoratedList_HtmlEntities extends I18Nv2_DecoratedList
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
            return htmlEntities($value, ENT_QUOTES, $this->list->getEncoding());
        } elseif (is_array($value)) {
            return array_map(array(&$this, 'decorate'), $value);
        }
        return $value;
    }
}
?>
