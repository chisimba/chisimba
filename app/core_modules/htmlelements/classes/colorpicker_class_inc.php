<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
/**
* Color Picker Chooser
*
* This class generates a text input with a color picker tool next to it.
*
* Adapted from the Color Picker by Matt Kruse
* http://www.mattkruse.com/javascript/colorpicker/index.html
*
* @author Tohir Solomons
*         
*         Example:
*         $objColorPicker = $this->getObject('colorpicker', 'htmlelements');
*         $objColorPicker->setName('color');
*         echo $objColorPicker->show();
*/
class colorpicker extends object
{
    /**
    * @var string $name Name of the Text Input
    */
    public $name = 'color';
    
 
    /**
    * @var string $defaultColor Default Color/Value of the Text Input
    */
    public $defaultColor='';
    
    /**
    * Constructor
    */
    public function init()
    { 
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->setIcon('colorpicker');
        
        $this->loadClass('textinput', 'htmlelements');
    }
    
    /**
    * Method to set the name of the text input
    */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
    * Method to display the color picker
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