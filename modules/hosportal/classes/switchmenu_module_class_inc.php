<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'chisimba_modules_handler_class_inc.php';

class switchmenu_module extends chisimba_modules_handler
{
// $switchMenuTopHeading = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_sortheading");
//  $switchmenu = $this->newObject('switchmenu', 'htmlelements');
//
//  $switchmenu->addBlock($switchMenuTopHeading,
//  $link1Manage.'<br/>'.$link2Manage.'<br/>'.$link3Manage.'<br/>'. $link4Manage.'<br/>'. $link5Manage.'<br/>'. $link6Manage.'<br/>'. $link7Manage.'<br/>'. $link8Manage.' <br />');
//  $switchmenu->addBlock('Title 2', 'Block Text 2 <br /> Block Text 2 <br /> Block Text 2', 'confirm');
private $objSwitchMenu;

    public function init()
    {
         $this->objSwitchMenu= $this->loadClass('switchmenu', 'htmlelements');

    }

    public function createNewObjectFromModule($name_of_class= 'switchmenu' , $name_of_module='htmlelements')
    {

    return $this->objSwitchMenu = $this->newObject($name_of_class,  $name_of_module);
 //return   $this->objButton = new button($name_of_button, $Id_value, $on_click);
}
public function EditModule()
{
}

public function addBlockToMenu($main_heading,$block_text)
        {
    return $this->objSwitchMenu->addBlock($main_heading,$block_text);
}
public function showSwitchMenu()
{
    return $this->objSwitchMenu->show();
}



}


?>