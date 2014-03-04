<?php
//THIS CLASS IS NOT USED WITH IN THIS MODULE
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'core_chisimba_modules_handler_class_inc.php';

class core_object_module extends core_chisimba_modules_handler
{

private $objObject;

    public function init()
    {
         //$this->objButton= $this->loadClass('button','htmlelements');

    }

    public function createNewObjectFromModule($name_of_button= 'NoName' , $Id_value=NULL, $on_click=NULL)
    {
// return   $this->objButton = new button($name_of_button, $Id_value, $on_click);
}
public function EditModule()
{
}

public function loadClassFromModule($name_of_class ='',$name_module='hosportal')
        {
    return $this->loadClass($name_of_class,$name_module);
        }

public function instantiateObjectFromClass($name_of_class ='',$name_module='hosportal')
        {
    $this->getObject($name_of_class,$name_module);
        }
        
public function instantiateNewObjectFromClass($name_of_class ='',$name_module='hosportal')
        {
    return $this->newObject($name_of_class,$name_module);
        }


}


?>