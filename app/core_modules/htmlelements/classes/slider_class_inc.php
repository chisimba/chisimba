<?php
/**
 * Slider - An Input Slider where users can drag to select a value
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
 * @author Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id: slider_class_inc.php 3162 2007-12-14 10:25:30Z tohir $
 * @link      http://avoir.uwc.ac.za
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
* Slider - An Input Slider where users can drag to select a value
*
* This class generates a slider enabling users to select a value by dragging
* a handle. It uses Scriptaculous to achieve this, but is based on the example by:
* http://enriquedelgado.com/articles/2007/12/11/html-slider-controls-with-prototype-and-scriptaculous
*
* The images used also comes from the above website
*
* @package   htmlelements
* @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @version   $Id: slider_class_inc.php 3162 2007-12-14 10:25:30Z tohir $;
* @author    Tohir Solomons
*/
class slider extends object
{

    /**
    * @var string $name Name of the form element
    */
    public $name = 'slider';
    /**
    * @var string $value Value of the slider
    */
    public $value = 0;
    
    /**
     * @var int $minValue Minimum Value of the Slider
     */
    public $minValue = 0;
    
    /**
     * @var int $maxValue Maximum Value of the Slider
     */
    public $maxValue = 10;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->loadClass('textinput');
    }
    
    /**
    * Method to show the Slider
    * @return string The Slider
    */
    public function show()
    {
        // Generate JavaScript and send to header
        $this->appendArrayVar('bodyOnLoad', $this->generateJS());
        
        // Create Textinput for Value
        $textinput = new textinput($this->name);
        $textinput->value = $this->value;
        $textinput->cssId = 'slider_'.$this->name;
        $textinput->size = strlen($this->maxValue)+1;
        $textinput->extra = ' readonly="readonly"';
        
        // Create Divs needed
        return '<div id="sliderbkg_'.$this->name.'" class="sliderbackground"><div id="sliderhandle_'.$this->name.'" class="sliderhandle"></div></div>'.$textinput->show();
    }
    
    /**
     * Method to generate the JavaScript for the slider
     * @return string JavaScript
     */
    public function generateJS()
    {
        $values = '';
        $comma = '';
        
        for ($i=$this->minValue; $i<= $this->maxValue; $i++)
        {
            $values .= $comma.$i;
            $comma = ',';
        }
        
        $str = "var slidervar_{$this->name} = new Control.Slider('sliderhandle_{$this->name}','sliderbkg_{$this->name}', {axis:'horizontal', sliderValue:{$this->value}, range: \$R({$this->minValue}, {$this->maxValue}), values: [{$values}]});";
        $str .= "\r\n";
        $str .= "slidervar_{$this->name}.options.onChange = function(value){ $('slider_{$this->name}').value = value; };";
        $str .= "\r\n";
        
        return $str;
    }
}

?>