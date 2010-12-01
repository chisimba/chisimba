<?php
/**
 *
 * A form class using DOM extension
 *
 * This file contains the form class which is used to generate
 * HTML formn elements for forms. This class extends object and must be
 * instantiated using $this->newObject('htmlform', 'htmldom'). It implements
 * the following form elements:
 * <form> 	Defines an HTML form for user input
 * <input /> 	Defines an input control
 * <textarea> 	Defines a multi-line text input control
 * <label> 	Defines a label for an input element
 * <fieldset> 	Defines a border around elements in a form
 * <legend> 	Defines a caption for a fieldset element
 * <select> 	Defines a select list (drop-down list)
 * <optgroup> 	Defines a group of related options in a select list
 * <option> 	Defines an option in a select list
 * <button> 	Defines a push button
 *
 * Examples of use are shown below
 *       $objForm = $this->newObject('htmlform', 'htmldom');
 *       $formAction = str_replace("&amp;", "&", $this->uri(array("action" => "formresults"), "codetest"));
 *       $objForm->createForm("testform", "myform", $formAction);
 *       $objForm->addElement("input", "blabla",  "blablabla", "Enter you's name, yea", array("maxlength" =>"2", "type" => "text", "class" => "sexybutton#search"));
 *       $objForm->addElement("textarea", "something", "something2", "Enter your life story");
 *       $optionsArray = array(array("One","FirstOne", FALSE), array("Two","SecondOne",TRUE), array("Three","Three",FALSE));
 *       $objForm->addElement("select", "myselecttest", "wadawada", "Select your selection", array("options" => $optionsArray, "multiple" => TRUE, "size" => "2"));
 *       $objForm->addButton("Le button de moi");
 *       $str = $objForm->show();
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
 * 
 * A form class using DOM extension
 *
 * This file contains the form class which is used to generate
 * HTML formn elements for forms. This class extends object and must be
 * instantiated using $this->newObject('htmlform', 'htmldom')
 *
 * @category  Chisimba
 * @package   htmldom
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @author    Derek Keats <derek@dkeats.com>
 *
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: button_class_inc.php 16438 2010-01-22 15:38:42Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @example:
 *       $objButt = $this->newObject('form', 'htmldom');
 *
 */
class htmlform extends object
{

    /**
     *
     * Object to hold the dom document
     *
     * @var string Object $objDom
     * @access private
     */
    private $objDom;

    /**
     *
     * Object to hold the form
     *
     * @var string Object $objDom
     * @access private
     */
    private $objForm;


    /**
    *
    * Intialiser for the htmldom Form object. It instantiates the DOM and
    * that's all.
    *
    * @access public
    * @return void
    *
    */
    public function init()
    {
        // Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
    }

    /**
     *
     * Create the base form object for the DOM. This is necessary as the addElement
     * method adds elements to this form.
     *
     * @param string $id The form's unique ID for the page (needed when using templates)
     * @param string $formName A name for the form
     * @param string $formAction The script to which the form is posted
     * @param string $formMethod The method, defaulting to HTTP GET (get)
     * @param string array $attributes An array of attribute key and value pairs.
     */
    public function createForm($id, $formName, $formAction, $formMethod="get", $attributes=array())
    {
        // Create the form element
        $this->objForm = $this->objDom->createElement('form');
        $this->objForm->setAttribute('id',$id);
        $this->objForm->setAttribute('name',$formName);
        $this->objForm->setAttribute('action',$formAction);
        $this->objForm->setAttribute('method',$formMethod);

    }

    public function show()
    {
        $this->objDom->appendChild($this->objForm);
        $ret = $this->objDom->saveHTML();
        die($ret);
        return $ret;
    }

    public function addElement($element, $id,  $name, $label=NULL, $attributes=array())
    {
        $elementType = $this->objDom->createElement($element);
        $elementType->setAttribute('id',$id);
        $elementType->setAttribute('name',$name);
        if ($label) {
            $label = $this->objDom->createTextNode($label);
            $this->objForm->appendChild($label);
        }
        if (!empty($attributes)) {
            foreach ($attributes as $key=>$value) {
                if (strtolower($key) == "options") {
                    if (is_array($value)) {
                        if (!empty ($value)) {
                            // For the special case of building a dropdown (select)
                            if ($element == "select") {
                                foreach ($value as $val) {
                                    $curOpt = $this->objDom->createElement("option");
                                    $curOpt->setAttribute('value',$val[0]);
                                    if ($val[2] == TRUE) {
                                        $curOpt->setAttribute('selected', TRUE);
                                    }
                                    $optVal = $this->objDom->createTextNode($val[1]);
                                    $curOpt->appendChild($optVal);
                                    $elementType->appendChild($curOpt);
                                }
                            }
                        }
                    } 
                } else {
                    $elementType->setAttribute($key,$value);
                }
                
            }
        }
        $this->objForm->appendChild($elementType);
        return TRUE;
    }


    /**
     *
     * Render a button - this can also be done using addElement, but it is
     * provided here as a means to easily create a submit / reset button
     *
     * @param string $caption The button caption
     * @return string 
     * @access public
     *
     */
    public function addButton($id, $caption='Submit', $type='submit', $name=NULL, $onClick=NULL) {
        $button = $this->objDom->createElement('input');
        $button->setAttribute('value',$caption);
        $button->setAttribute('type', $type);
        // If there is a name, then use it.
        if ($name) {
            $button->setAttribute('name',$name);
        }
        $button->setAttribute('id',$id);
        // If there is an onclick event specified, add it as an attribute.
        if ($onClick) {
            $button->setAttribute('onclick',$this->onclick);
        }
        $button = $this->objForm->appendChild($button);
    }   
}
?>