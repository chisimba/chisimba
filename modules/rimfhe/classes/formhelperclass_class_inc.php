<?php

class formhelperclass extends object
{
    public $objLanguage;
    public $objUrl;
    public $dbInsert;

    public function init()
    {	//create a language object
        $this->objLanguage=$this->getObject('language', 'language');
        $this->objUrl = $this->getObject('url', 'strings');
        $this->dbInsert =$this->getObject('dbstaffmember', 'rimfhe');
    }//end init

    //Metthod that will load all elemnets required to build the form
    private function loadElements()
    {
        //load the form class
        $this->loadClass('form','htmlelements');
        //load the text input
        $this->loadClass('textinput', 'htmlelements');
        //load drop down class
        $this->loadClass('dropdown', 'htmlelements');
        //load label class
        $this->loadClass('label', 'htmlelements');
        //load button class
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
    }//end loadElements

    //create an instruction for the form
    public function formInstruction()
    {
        $formInstruct = $this->newObject('htmlheading', 'htmlelements');
        $formInstruct->type = '3';
        $formInstruct->str = $this->objLanguage->languageText('mod_staffregistration_forminstruction', 'rimfhe');
        return $formInstruct->show();

    }//end for instruction

    //method to build form and create form elements

    public function sendElements()
    {
        return $this->loadElements();
    }

    //public function explainP
}
?>
