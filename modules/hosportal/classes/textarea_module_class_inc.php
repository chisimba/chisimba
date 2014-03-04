<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'chisimba_modules_handler_class_inc.php';

class textarea_module extends chisimba_modules_handler
{

private $objTextArea;

    public function init()
    {
         $this->objTextArea= $this->loadClass('textarea','htmlelements');


    }

    public function createNewObjectFromModule($name_of_textarea= 'NoName' , $value='',$rows=50,$cols=150)
    {
 return   $this->objTextArea = new textarea($name_of_textarea , $value, $rows, $cols);
}
public function EditModule()
{
}



}


?>
