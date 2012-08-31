<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: DecoratedList :: HtmlSelect                        |
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
 * I18Nv2::DecoratedList::HtmlSelect
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

require_once 'I18Nv2/DecoratedList.php';

/**
 * I18Nv2_DecoratedList_HtmlSelect
 * 
 * Example:
 * <code>
 *   require_once 'I18Nv2/Country.php';
 *   require_once 'I18Nv2/DecoratedList/HtmlSelect.php';
 * 
 *   $country = &new I18Nv2_Country('de', 'iso-8859-1');
 *   $select  = &new I18Nv2_DecoratedList_HtmlSelect($country);
 *   $select->attributes['select']['name'] = 'country';
 *   $select->selected['DE'] = true;
 *   echo $select->getAllCodes();
 * </code>
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @package     I18Nv2
 * @access      public
 */
class I18Nv2_DecoratedList_HtmlSelect extends I18Nv2_DecoratedList
{
    /**
     * HTML attributes of the select and the option tags
     * 
     * <code>
     * $HtmlSelect->attributes['select']['onchange'] = 'this.form.submit()';
     * </code>
     * 
     * @access  public
     * @var     array
     */
    var $attributes = array(
        'select' => array(
            'size' => 1,
        ),
        'option' => array(
        )
    );
    
    /**
     * Selected option(s)
     * 
     * <code>
     * $HtmlSelect->selected[$code] = true;
     * </code>
     * 
     * @access  public
     * @var     array
     */
    var $selected = array();
    
    /** 
     * decorate
     * 
     * @access  protected
     * @return  string
     * @param   mixed   $value
     */
    function decorate($value)
    {
        static $codes;
        
        if (is_scalar($value)) {
            if (!isset($codes)) {
                $codes = $this->list->getAllCodes();
            }
            $key = array_search($value, $codes);
            return
                '<option ' . $this->_optAttr($key) . '>' . 
                    $value .
                '</option>';
        } elseif(is_array($value)) {
            return 
                '<select ' . $this->_getAttr() . '>' . 
                    implode('', array_map(array(&$this, 'decorate'), $value)) . 
                '</select>';
        }
        return $value;
    }
    
    /**
     * Get HTML attributes for the option tag
     * 
     * @access  private
     * @return  string
     * @param   string  $key
     */
    function _optAttr($key)
    {
        $attributes = 'value="' . $key . '" ' . $this->_getAttr('option');
        if (isset($this->selected[$key]) && $this->selected[$key]) {
            $attributes .= 'selected="selected"';
        }
        return $attributes;
    }
    
    /**
     * Get HTML attributes
     * 
     * @access  private
     * @return  string
     * @param   string  $of
     */
    function _getAttr($of = 'select')
    {
        $attributes = '';
        foreach ($this->attributes[$of] as $attr => $value) {
            $attributes .= $attr . '="' . $value .'" ';
        }
        return $attributes;
    }
}
?>
