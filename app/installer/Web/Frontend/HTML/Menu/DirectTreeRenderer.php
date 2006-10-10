<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Alexey Borzov <avb@php.net>                                 |
// |          Uwe Mindrup <uwe@mindrup.de>                                |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once 'HTML/Menu/Renderer.php';

/**
 * The 'direct' renderer for 'tree' and 'sitemap' menu types where level is
 * represented by tags nesting.
 * 
 * Thanks to Uwe Mindrup for the idea and initial implementation.
 * 
 * @version $Revision$
 * @author  Alexey Borzov <avb@php.net>
 * @author  Uwe Mindrup <uwe@mindrup.de>
 * @access  public
 * @package HTML_Menu
 */
class HTML_Menu_DirectTreeRenderer extends HTML_Menu_Renderer
{
   /**
    * Generated HTML for the menu
    * @var string
    */
    var $_html = '';

   /**
    * Generated HTML for the current branches
    * @var string
    */
    var $_levelHtml = array();

   /**
    * Generated HTML for the current menu items
    * @var string
    */
    var $_itemHtml = array();

   /**
    * The HTML that will wrap around a complete (sub)menu
    * @see setLevelTemplate()
    * @var array
    */
    var $_levelTemplate = array('<ul>', '</ul>');

   /**
    * The HTML that will wrap around menu item
    * @see setItemTemplate()
    * @var array
    */
    var $_itemTemplate = array('<li>', '</li>');

   /**
    * Templates for menu entries
    * @see setEntryTemplate()
    * @var array
    */
    var $_entryTemplates = array(
        HTML_MENU_ENTRY_INACTIVE    => '<a href="{url}">{title}</a>',
        HTML_MENU_ENTRY_ACTIVE      => '<strong>{title}</strong>',
        HTML_MENU_ENTRY_ACTIVEPATH  => '<a href="{url}"><em>{title}</em></a>'
    );


    function setMenuType($menuType)
    {
        if ('tree' == $menuType || 'sitemap' == $menuType) {
            $this->_menuType = $menuType;
        } else {
            require_once 'PEAR.php';
            return PEAR::raiseError("HTML_Menu_DirectTreeRenderer: unable to render '$menuType' type menu");
        }
    }


    function finishLevel($level)
    {
        isset($this->_levelHtml[$level]) or $this->_levelHtml[$level] = '';
        $this->_levelHtml[$level] .= $this->_itemTemplate[0] . $this->_itemHtml[$level] . $this->_itemTemplate[1];
        if (0 < $level) {
            $this->_itemHtml[$level - 1] .= $this->_levelTemplate[0] . $this->_levelHtml[$level] . $this->_levelTemplate[1];
        } else {
            $this->_html = $this->_levelTemplate[0] . $this->_levelHtml[$level] . $this->_levelTemplate[1];
        }
        unset($this->_itemHtml[$level], $this->_levelHtml[$level]);
    }


    function renderEntry($node, $level, $type)
    {
        if (!empty($this->_itemHtml[$level])) {
            isset($this->_levelHtml[$level]) or $this->_levelHtml[$level] = '';
            $this->_levelHtml[$level] .= $this->_itemTemplate[0] . $this->_itemHtml[$level] . $this->_itemTemplate[1];
        }
        $keys = $values = array();
        foreach ($node as $k => $v) {
            if ('sub' != $k) {
                $keys[]   = '{' . $k . '}';
                $values[] = $v;
            }
        }
        $this->_itemHtml[$level] = str_replace($keys, $values, $this->_entryTemplates[$type]);
    }


   /**
    * returns the HTML generated for the menu
    *
    * @access public
    * @return string
    */
    function toHtml()
    {
        return $this->_html;
    } // end func toHtml


   /**
    * Sets the item template (HTML that wraps around entries)
    * 
    * @access public
    * @param  string    this will be prepended to the entry HTML
    * @param  string    this will be appended to the entry HTML
    */
    function setItemTemplate($prepend, $append)
    {
        $this->_itemTemplate = array($prepend, $append);
    }


   /**
    * Sets the level template (HTML that wraps around the submenu)
    * 
    * @access public
    * @param  string    this will be prepended to the submenu HTML
    * @param  string    this will be appended to the submenu HTML
    */
    function setLevelTemplate($prepend, $append)
    {
        $this->_levelTemplate = array($prepend, $append);
    }


   /**
    * Sets the template for menu entry.
    * 
    * The template should contain at least the {title} placeholder, can also contain
    * {url} and {indent} placeholders, depending on entry type.
    * 
    * @access public
    * @param  mixed     either type (one of HTML_MENU_ENTRY_* constants) or an array 'type' => 'template'
    * @param  string    template for this entry type if $type is not an array
    */
    function setEntryTemplate($type, $template = null)
    {
        if (is_array($type)) {
            // array_merge() will not work here: the keys are numeric
            foreach ($type as $typeId => $typeTemplate) {
                if (isset($this->_entryTemplates[$typeId])) {
                    $this->_entryTemplates[$typeId] = $typeTemplate;
                }
            }
        } else {
            $this->_entryTemplates[$type] = $template;
        }
    }
}
?>
