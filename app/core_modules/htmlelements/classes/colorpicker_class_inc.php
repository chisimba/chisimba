<?php

/**
 * colorpicker_class_inc.php
 *
 * This file contains the colorpicker class which is used to generate
 * a text input with a color picker tool next to it
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
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */

/**
 * Color Picker Chooser
 *
 * This class generates a text input with a color picker tool next to it.
 * Adapted from the Color Picker by Matt Kruse
 *
 *
 * @author Tohir Solomons
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @see       Color Picker by Matt Kruse - resources/ColorPicker2.js
 * @link      http://www.mattkruse.com/javascript/colorpicker/index.html
 * @link      http://avoir.uwc.ac.za
 * @example:
 *  $objColorPicker = $this->getObject('colorpicker', 'htmlelements');
 *  $objColorPicker->setName('color');
 *  echo $objColorPicker->show();
 */
class colorpicker extends object
{
    /**
     * Name of the Text Input
     *
     * @var string $name
     */
    public $name = 'color';


    /**
     * Default Color/Value of the Text Input
     *
     * @var string $defaultColor
     */
    public $defaultColor='';

    /**
    * Class Constructor
    */
    public function init()
    {
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->setIcon('colorpicker');

        $this->loadClass('textinput', 'htmlelements');
    }

    /**
    * Method to set the name of the text input
    *
    * @param string $name The new name for the text input
    * @return void
    * @access public
    */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
    * Method to render the color picker as HTML code
    *
    * @return string The HTML code of the rendered textinput and colorpicker
    * @access public
    */
    public function show()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('ColorPicker2.js'));
        //'<script type="text/javascript" SRC="modules/htmlelements/resources/ColorPicker2.js"></SCRIPT>');

        $this->appendArrayVar('headerParams', '<script type="text/javascript">
var colorpicker = new ColorPicker(); // DIV style
</script>');

        $this->setVar('pageSuppressXML', TRUE);

        $input = new textinput($this->name, $this->defaultColor);
        $id = $input->cssId;


        return $input->show().' <a href="javascript:;" onclick="colorpicker.select(document.getElementById(\''.$id.'\'),\'colorpick_'.$id.'\');return false;" NAME="colorpick_'.$id.'" id="colorpick_'.$id.'">'.$this->objIcon->show().'</a><script type="text/javascript">colorpicker.writeDiv();</script>';
    }
}
?>