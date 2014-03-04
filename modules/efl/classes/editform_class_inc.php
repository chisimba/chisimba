<?php

 /**
 * This class creates a form to be used by a student when answering essays
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

 * @author
 * @copyright  2009 AVOIR
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
class editform extends object {
    public $objLanguage;

    public function init() {
    //instantiate the laguage object
        $this->objLanguage = $this->getObject("language","language");
    }

    private function loadElements() {
    //Load the form class
        $this->loadClass("form","htmlelements");
        //Load the textinput class
        $this->loadClass("textinput","htmlelements");

        //Load the textarea class
        $this->loadClass("textarea","htmlelements");
        //Load the text label class
        $this->loadClass("label","htmlelements");
        //Load the button object
        $this->loadClass("button","htmlelements");


    }
    /**
     * this builds a form that can be reusable
     * @return <type>
     */
    private function buildForm($essayid) {
    //load form elements
        $this->loadElements();
        //create a form
        $objForm = new form("essay",$this->uri(array("action" => "addstudentessay","essayid"=>$essayid)));

        //----------TEXTAREA--------------
        //Create a new textarea for the essay content
        $objEssay = new textarea('content','',50,100);
        $essayLabel = new label($this->objLanguage->languageText("mod_efl_essaytxt","EFL"),"essay");
        $objForm->addToForm($essayLabel->show() . "<br />");
        $objForm->addToForm($objEssay->show() . "<br />");

        //----------SUBMIT BUTTON--------------
        //Create a button for submitting the form
        $objButton = new button('save');
        // Set the button type to submit
        $objButton->setToSubmit();
        // Use the language object to label button
        // with the word save
        $objButton->setValue(' '.$this->objLanguage->languageText("mod_efl_saveessay", "EFL").' ');
        $objForm->addToForm($objButton->show());
        return $objForm->show();

    }


    /**
     * dynamically generate form actions
     * @return <type>
     */
    private function getFormAction($essayid) {

        $action = $this->getParam("action", "save");
        if ($action == "edit"){
            $formAction = $this->uri(array("action" => "editessay",'essayid'=>$essayid), "efl");
        } else {
            $formAction = $this->uri(array("action" => "addstudentessay",'essayid'=>$essayid), "efl");
        }
        return $formAction;
    }

   /**
    * used for actual rendering
    * @return <type>
    */
    public function show($essayid) {
        return $this->buildForm($essayid);
    }
}
?>