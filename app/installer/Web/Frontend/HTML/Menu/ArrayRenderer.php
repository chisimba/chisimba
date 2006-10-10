<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author:  Alexey Borzov <avb@php.net>                                 |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once 'HTML/Menu/Renderer.php';

/**
 * The renderer that creates an array of visible menu entries.
 * 
 * The resultant array can be used with e.g. a template engine to produce
 * a completely custom menu look.
 * All menu types except 'rows' are "rendered" into a one-dimensional array
 * of entries:
 * array(
 *    'entry1',
 *    ...
 *    'entryN'
 * )
 * while 'rows' produce a two-dimensional array:
 * array(
 *    array('entry 1 for row 1', ..., 'entry M_1 for row 1'),
 *    ...
 *    array('entry 1 for row N', ..., 'entry M_N for row 1')
 * )
 * Here entry is 
 * array(
 *    'url'    => url element of menu entry
 *    'title'  => title element of menu entry
 *    'level'  => entry's depth in the tree structure
 *    'type'   => type of entry, one of HTML_MENU_ENTRY_* constants
 *    // if the nodes in the original menu array contained keys other
 *    // than 'url', 'title' and 'sub', they will be copied here, too
 * )
 * 
 * @version  $Revision$
 * @author   Alexey Borzov <avb@php.net>
 * @access   public
 * @package  HTML_Menu
 */
class HTML_Menu_ArrayRenderer extends HTML_Menu_Renderer
{
   /**
    * Generated array
    * @var array
    */
    var $_ary = array();

   /**
    * Array for the current "menu", that is moved into $_ary by finishMenu(), 
    * makes sense mostly for 'rows
    * @var array
    */
    var $_menuAry = array();

    function finishMenu($level)
    {
        if ('rows' == $this->_menuType) {
            $this->_ary[] = $this->_menuAry;
        } else {
            $this->_ary   = $this->_menuAry;
        }
        $this->_menuAry = array();
    }


    function renderEntry($node, $level, $type)
    {
        unset($node['sub']);
        $node['level'] = $level;
        $node['type']  = $type;
        $this->_menuAry[] = $node;
    }


   /**
    * Returns the resultant array
    * 
    * @access public
    * @return array
    */
    function toArray()
    {
        return $this->_ary;
    }
}
?>
