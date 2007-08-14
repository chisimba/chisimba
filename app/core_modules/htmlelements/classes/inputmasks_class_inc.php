<?php
/**
 * Inputmasks class for Chisimba
 * 
 * This library adds input masks to form fields with unobtrusive JavaScript.
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
 * @version   CVS: $Id$
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

// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
* Input Masks
*
* Have you ever wanted to apply an input mask to an HTML form field? 
* Input masks are common in traditional GUI applications, 
* but HTML has no such feature. This library adds input masks 
* to form fields with unobtrusive JavaScript.
*
* This class works with a javascript file "html-form-input-mask.js" found in the resources folder and Prototype.
*
* http://www.xaprb.com/blog/2006/11/02/how-to-create-input-masks-in-html
*
* To use:
* Add the following CSS Classes to the form elements: input_mask {mask_date_us}
*
* So it will appear: <input type="text" class="text input_mask mask_date_us" />
*
* Options available:
*
* date_iso, date_us, time, phone, ssn, visa, number
*
* More options can be added in the javascript file
*
* @author Tohir Solomons
*         
*         Example:
*         $objInputMasks = $this->getObject('inputmasks', 'htmlelements');
*         echo $objInputMasks->show();
*         
*         $this->loadClass('textinput', 'htmlelements');
*         $textinput = new textinput('myinput');
*         $textinput->setCss('text input_mask mask_date_us');
*/
class inputmasks extends object implements ifhtml
{
    
    /**
    * Constructor
    */
    public function init()
    {  }
    
    /**
    * Method to load the required javascript in a page.
    */
    public function show()
    {
        //scriptaculous and prototype loaded in the default page template
        //$objScriptaculous =& $this->getObject('scriptaculous', 'ajaxwrapper');
        //$objScriptaculous->loadPrototype();
        
        // Send the JavaScript to the header
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('inputmasks/html-form-input-mask.js', 'htmlelements'));
        
        // Setup Body onLoad
        $this->appendArrayVar('bodyOnLoad', 'Xaprb.InputMask.setupElementMasks();');
        
    }
}
?>
