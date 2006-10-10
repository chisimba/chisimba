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
// | Author: Alexey Borzov <avb@php.net>                                  |
// +----------------------------------------------------------------------+
//
// $Id$
//

/**
 * An abstract base class for HTML_Menu renderers
 *
 * @package  HTML_Menu
 * @version  $Revision$
 * @author   Alexey Borzov <avb@php.net>
 * @abstract
 */
class HTML_Menu_Renderer
{
   /**
    * Type of the menu being rendered
    * @var string
    */
    var $_menuType;

   /**
    * Sets the type of the menu being rendered.
    *
    * This method will throw an error if the renderer is not designed
    * to render a specific menu type.
    *
    * @access public
    * @param  string menu type
    * @throws PEAR_Error
    */
    function setMenuType($menuType)
    {
        $this->_menuType = $menuType;
    }


   /**
    * Finish the menu
    *
    * @access public
    * @param  int    current depth in the tree structure
    */
    function finishMenu($level)
    {
    }


   /**
    * Finish the tree level (for types 'tree' and 'sitemap')
    *  
    * @access public
    * @param  int    current depth in the tree structure
    */
    function finishLevel($level)
    {
    }


   /**
    * Finish the row in the menu
    *
    * @access public
    * @param  int    current depth in the tree structure
    */
    function finishRow($level)
    {
    }


   /**
    * Renders the element of the menu
    *
    * @access public
    * @param array   Element being rendered
    * @param int     Current depth in the tree structure
    * @param int     Type of the element (one of HTML_MENU_ENTRY_* constants)
    */
    function renderEntry($node, $level, $type)
    {
    }
}

?>
