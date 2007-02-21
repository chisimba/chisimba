<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class
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
* Example:
*       $objInputMasks = $this->getObject('inputmasks', 'htmlelements');
*       echo $objInputMasks->show();
*
*       $this->loadClass('textinput', 'htmlelements');
*       $textinput = new textinput('myinput');
*       $textinput->setCss('text input_mask mask_date_us');
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
        $objScriptaculous =& $this->getObject('scriptaculous', 'ajaxwrapper');
        $objScriptaculous->loadPrototype();
        
        // Send the JavaScript to the header
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('inputmasks/html-form-input-mask.js', 'htmlelements'));
        
        // Setup Body onLoad
        $this->appendArrayVar('bodyOnLoad', 'Xaprb.InputMask.setupElementMasks();');
        
    }
}
?>