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
// | Authors: Ulf Wendel <ulf.wendel@phpdoc.de>                           |
// |          Sebastian Bergmann <sb@sebastian-bergmann.de>               |
// |          Alexey Borzov <avb@php.net>                                 |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once 'HTML/Menu/Renderer.php';

/**
 * The renderer that generates HTML for the menu all by itself.
 * 
 * Inspired by HTML_Menu 1.0 code
 * 
 * @version  $Revision$
 * @author   Ulf Wendel <ulf.wendel@phpdoc.de>
 * @author   Alexey Borzov <avb@php.net>
 * @access   public
 * @package  HTML_Menu
 */
class HTML_Menu_DirectRenderer extends HTML_Menu_Renderer
{
   /**
    * Generated HTML for the menu
    * @var string
    */
    var $_html = '';

   /**
    * Generated HTML for the current menu "table"
    * @var string
    */
    var $_tableHtml = '';
    
   /**
    * Generated HTML for the current menu "row"
    * @var string
    */
    var $_rowHtml = '';

   /**
    * The HTML that will wrap around menu "table"
    * @see setMenuTemplate()
    * @var array
    */
    var $_menuTemplate = array('<table border="1">', '</table>');

   /**
    * The HTML that will wrap around menu "row"
    * @see setRowTemplate()
    * @var array
    */
    var $_rowTemplate = array('<tr>', '</tr>');

   /**
    * Templates for menu entries
    * @see setEntryTemplate()
    * @var array
    */
    var $_entryTemplates = array(
        HTML_MENU_ENTRY_INACTIVE    => '<td>{indent}<a href="{url}">{title}</a></td>',
        HTML_MENU_ENTRY_ACTIVE      => '<td>{indent}<b>{title}</b></td>',
        HTML_MENU_ENTRY_ACTIVEPATH  => '<td>{indent}<b><a href="{url}">{title}</a></b></td>',
        HTML_MENU_ENTRY_PREVIOUS    => '<td><a href="{url}">&lt;&lt; {title}</a></td>',
        HTML_MENU_ENTRY_NEXT        => '<td><a href="{url}">{title} &gt;&gt;</a></td>',
        HTML_MENU_ENTRY_UPPER       => '<td><a href="{url}">^ {title} ^</a></td>',
        HTML_MENU_ENTRY_BREADCRUMB  => '<td><a href="{url}">{title}</a> &gt;&gt; </td>'
    );

    function finishMenu($level)
    {
        $this->_html     .=  $this->_menuTemplate[0] . $this->_tableHtml . $this->_menuTemplate[1];
        $this->_tableHtml = '';
    }

    function finishRow($level)
    {
        $this->_tableHtml .= $this->_rowTemplate[0] . $this->_rowHtml . $this->_rowTemplate[1];
        $this->_rowHtml    = '';
    }

    function renderEntry($node, $level, $type)
    {
        $keys = array('{indent}');
        if ('tree' == $this->_menuType || 'sitemap' == $this->_menuType) {
            $values = array(str_repeat('&nbsp;&nbsp;&nbsp;', $level));
        } else {
            $values = array('');
        }
        foreach ($node as $k => $v) {
            if ('sub' != $k) {
                $keys[]   = '{' . $k . '}';
                $values[] = $v;
            }
        }
        $this->_rowHtml .= str_replace($keys, $values, $this->_entryTemplates[$type]);
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
    * Sets the menu template (HTML that wraps around rows)
    *  
    * @access public
    * @param  string    this will be prepended to the rows HTML
    * @param  string    this will be appended to the rows HTML
    */
    function setMenuTemplate($prepend, $append)
    {
        $this->_menuTemplate = array($prepend, $append);
    }


   /**
    * Sets the row template (HTML that wraps around entries)
    *  
    * @access public
    * @param  string    this will be prepended to the entries HTML
    * @param  string    this will be appended to the entries HTML
    */
    function setRowTemplate($prepend, $append)
    {
        $this->_rowTemplate = array($prepend, $append);
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
