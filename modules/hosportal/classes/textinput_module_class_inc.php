<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'chisimba_modules_handler_class_inc.php';

class textinput_module extends chisimba_modules_handler
{
  
private $objTextInput;

    public function init()
    {
         $this->objTextInput= $this->loadClass('textinput','htmlelements');

    }

    public function createNewObjectFromModule($name_of_text_input= 'NoName' , $value=NULL)
    {
 return   $this->objTextInput = new textinput($name_of_text_input , $value);
}
public function EditModule()
{
}



}


?>
