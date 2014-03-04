<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'chisimba_modules_handler_class_inc.php';

class label_module extends chisimba_modules_handler
{

private $objLabel;

    public function init()
    {
         $this->objLabel= $this->loadClass('label','htmlelements');


    }

    public function createNewObjectFromModule($name_of_label= 'NoName' , $IdValue=NULL)
    {
 return   $this->objLabel = new label($name_of_label, $IdValue);
}
public function EditModule()
{
}



}


?>
