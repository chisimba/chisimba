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
 * The renderer that uses HTML_Template_Sigma instance for menu output.
 * 
 * @version  $Revision$
 * @author   Alexey Borzov <avb@php.net>
 * @access   public
 * @package  HTML_Menu
 */
class HTML_Menu_SigmaRenderer extends HTML_Menu_Renderer
{
   /**
    * Template object used for output
    * @var object HTML_Template_Sigma
    */
    var $_tpl;

   /**
    * Mapping from HTML_MENU_ENTRY_* constants to template block names
    * @var array
    */
    var $_typeNames = array(
        HTML_MENU_ENTRY_INACTIVE    => 'inactive',
        HTML_MENU_ENTRY_ACTIVE      => 'active',
        HTML_MENU_ENTRY_ACTIVEPATH  => 'activepath',
        HTML_MENU_ENTRY_PREVIOUS    => 'previous',
        HTML_MENU_ENTRY_NEXT        => 'next',
        HTML_MENU_ENTRY_UPPER       => 'upper',
        HTML_MENU_ENTRY_BREADCRUMB  => 'breadcrumb'
    );

   /**
    * Prefix for template blocks and placeholders
    * @var string
    */
    var $_prefix;

   /**
    * Class constructor.
    * 
    * Sets the template object to use and sets prefix for template blocks
    * and placeholders. We use prefix to avoid name collisions with existing 
    * template blocks and it is customisable to allow output of several menus 
    * into one template.
    *
    * @access public
    * @param  object HTML_Template_Sigma    template object to use for output
    * @param  string    prefix for template blocks and placeholders
    */
    function HTML_Menu_SigmaRenderer(&$tpl, $prefix = 'mu_')
    {
        $this->_tpl    =& $tpl;
        $this->_prefix =  $prefix;
    }

    function finishMenu($level)
    {
        if ('rows' == $this->_menuType && $this->_tpl->blockExists($this->_prefix . ($level + 1) . '_menu_loop')) {
            $this->_tpl->parse($this->_prefix . ($level + 1) . '_menu_loop');
        } elseif ($this->_tpl->blockExists($this->_prefix . 'menu_loop')) {
            $this->_tpl->parse($this->_prefix . 'menu_loop');
        }
    }
    
    function finishRow($level)
    {
        if ('rows' == $this->_menuType && $this->_tpl->blockExists($this->_prefix . ($level + 1) . '_row_loop')) {
            $this->_tpl->parse($this->_prefix . ($level + 1) . '_row_loop');
        } elseif ($this->_tpl->blockExists($this->_prefix . 'row_loop')) {
            $this->_tpl->parse($this->_prefix . 'row_loop');
        }
    }

    function renderEntry($node, $level, $type)
    {
        if (in_array($this->_menuType, array('tree', 'sitemap', 'rows'))
            && $this->_tpl->blockExists($this->_prefix . ($level + 1) . '_' . $this->_typeNames[$type])) {

            $blockName = $this->_prefix . ($level + 1) . '_' . $this->_typeNames[$type];
        } else {
            $blockName = $this->_prefix . $this->_typeNames[$type];
        }
        if (('tree' == $this->_menuType || 'sitemap' == $this->_menuType) &&
             $this->_tpl->blockExists($blockName . '_indent')) {

            for ($i = 0; $i < $level; $i++) {
                $this->_tpl->touchBlock($blockName . '_indent');
                $this->_tpl->parse($blockName . '_indent');
            }
        }
        foreach ($node as $k => $v) {
            if ('sub' != $k && $this->_tpl->placeholderExists($this->_prefix . $k, $blockName)) {
                $this->_tpl->setVariable($this->_prefix . $k, $v);
            }
        }
        $this->_tpl->parse($blockName);
        if ('rows' == $this->_menuType 
            && $this->_tpl->blockExists($this->_prefix . ($level + 1) . '_entry_loop')) {
            
            $this->_tpl->parse($this->_prefix . ($level + 1) . '_entry_loop');
        } else {
            $this->_tpl->parse($this->_prefix . 'entry_loop');
        }
    }
}
?>
