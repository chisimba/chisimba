<?php
/**
 * This file contains the button class which is used to generate
 * HTML button elements for forms
 *
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   dhtmlgoodies
 * @author    Jeremy O'Connor <joconnor@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       modules
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * dhtmlgoodies_slider Class
 *
 * Class to wrap the dhtmlgoodies_slider widget.
 *
 * @category  Chisimba
 * @package   dhtmlgoodies
 * @author    Jeremy O'Connor <joconnor@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       modules
 */

/*
    Example usage:

    $objSlider = $this->newObject('dhtmlgoodies_slider', 'dhtmlgoodies');
    $objSlider->setTargetId('slider_target');
    $objSlider->setFieldRef('document.form1.field1');
    $objSlider->setWidth(200);
    $objSlider->setMin(0);
    $objSlider->setmax(100);
    echo '<span id=\'slider_target\'></span>'.$objSlider->show();

*/

class dhtmlgoodies_slider extends object
{
    /**
    * @var string The ID of the empty element where the slider should be inserted.
    * @access private
    */
    private $targetId = '';
    /**
    * @var string The reference to the form field the slider should be connected to.
    * @access private
    */
    private $fieldRef = '';
    /**
    * @var integer The width of the slider.
    * @access private
    */
    private $width = 0;
    /**
    * @var integer The minimum value of the slider.
    * @access private
    */
    private $min = 0;
    /**
    * @var integer The maximum value of the slider.
    * @access private
    */
    private $max = 0;
    /**
    * The initialization method.
    * @return void
    * @access public
    */
    public function init()
    {
    }
    /**
    * Inserts CSS & JS tags in header.
    * @return void
    * @access private
    */
    private function insertCSS_JS()
    {
        if (!defined('CHISIMBA_DHTMLGOODIES_SLIDER')) {
            define('CHISIMBA_DHTMLGOODIES_SLIDER',TRUE);
            // CSS for the slider
            $css='<style type="text/css">
.form_widget_amount_slider{
border-top:1px solid #9d9c99;
border-left:1px solid #9d9c99;
border-bottom:1px solid #eee;
border-right:1px solid #eee;
background-color:#f0ede0;
height:3px;
position:absolute;
bottom:0px;
}
</style>';
            $this->appendArrayVar('headerParams', $css);
            // JavaScript for the slider
            $js='<script language="JavaScript" type="text/javascript" src="'.$this->getResourceUri('dhtmlgoodies_slider/dhtmlgoodies_slider.js').'"></script>';
            $this->appendArrayVar('headerParams', $js);
            $js='<script language="JavaScript" type="text/javascript">
function init_dhtmlgoodies_slider()
{
    set_form_widget_amount_slider_handle(\''.$this->getResourceUri('dhtmlgoodies_slider/images/slider_handle.gif').'\');
}
</script>';
            $this->appendArrayVar('headerParams', $js);
            //$this->appendArrayVar('bodyOnLoad', 'init_dhtmlgoodies_slider();');
        }
    }
    /**
    * Sets the target element ID.
    * @param string The target element ID
    * @return void
    * @access public
    */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;
    }
    /**
    * Sets the field element ID.
    * @param string The field element ID
    * @return void
    * @access public
    */
    public function setFieldRef($fieldRef)
    {
        $this->fieldRef = $fieldRef;
    }
    /**
    * Sets the width.
    * @param integer The width
    * @return void
    * @access public
    */
    public function setWidth($width)
    {
        $this->width = $width;
    }
    /**
    * Sets the minimum value.
    * @param integer The minimum value
    * @return void
    * @access public
    */
    public function setMin($min)
    {
        $this->min = $min;
    }
    /**
    * Sets the maximum value.
    * @param integer The maximum value
    * @return void
    * @access public
    */
    public function setMax($max)
    {
        $this->max = $max;
    }
    /**
    * Renders the widget.
    * @return string The widget
    * @access public
    */
    public function show()
    {
        $this->insertCSS_JS();
        return '<script language="JavaScript" type="text/javascript">
init_dhtmlgoodies_slider();
form_widget_amount_slider(
    \''.$this->targetId.'\',
    '.$this->fieldRef.',
    '.$this->width.',
    '.$this->min.',
    '.$this->max.',
    false
);
</script>';
	}
}
?>