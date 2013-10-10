<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 include_once 'chisimba_modules_handler_class_inc.php';

class language_module extends chisimba_modules_handler
{
        private $objLanguage;

    public function init()
    {
//        $this->objLanguage = $this->getObject('language','language');
       $this->createNewObjectFromModule();
    }

public function createNewObjectFromModule()
    {
    $this->objLanguage = $this->newObject('language','language');
}
public function EditModule()
{
}


    public function insertTextFromConfigFile($string_name = "mod_hosportal_argumentundefined", $module_name = "hosportal")
    {
     //    $this->objLanguage = $this->getObject('language','language');
        return $this->objLanguage->languageText($string_name,$module_name);
    }

    public function insertTextFromVariables($string_name = "mod_hosportal_argumentundefined", $module_name = "hosportal",$varaiable_or_array = NULL)
     {
       //  $this->objLanguage = $this->getObject('language','language');
     return $this->objLanguage->code2Txt($string_name,$module_name,$varaiable_or_array);
     }

     //there is also an abstract function that uses the database and not the language elements

}

?>
