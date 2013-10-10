<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'chisimba_modules_handler_class_inc.php';

class user_module extends chisimba_modules_handler
{

private $objUser;

    public function init()
    {
        // $this->objUser= $this->getObject('user','security');
        $this->createNewObjectFromModule();

    }

    public function createNewObjectFromModule($name_of_class= "user" , $name_of_module = "security")
    {
return $this->objUser = $this->getObject($name_of_class,$name_of_module);
}
public function EditModule()
{
}
public function getUserFullName()
{
    return  $this->objUser->fullname();
}
public function getUserFirstName()
{
    return $this->objUser->getFirstName();
}
public function getUserName()
{
    return $this->objUser->userName();
}



}


?>