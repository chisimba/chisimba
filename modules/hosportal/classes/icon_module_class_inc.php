<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'chisimba_modules_handler_class_inc.php';

class icon_module extends chisimba_modules_handler
{

private $objIcon;

    public function init()
    {
        // $this->objform= $this->loadClass('form','htmlelements');
//          $this->$objForm = new form($name_of_form, $form_action);
       // $this->objLanguage = $this->getObject('language','language');
        $this->objIcon = $this->getObject('geticon','htmlelements');
    }
 //    public function setIcon($name, $type = 'gif', $iconfolder='icons/')
    public function setIconType($name_of_icon,$type_of_icon = 'gif', $icon_folder = 'icons/')
             {
        return  $this->objIcon->setIcon($name_of_icon,$type_of_icon, $icon_folder);
     }
     public function setAltTextForIcon($alt_text)
     {
        return  $this->objIcon->alt = $alt_text;
     }
     public function showIcon()
     {
         return  $this->objIcon->show();
     }
     public function setIconAlignment($type_of_alignment)
     {
             return  $this->objIcon->align = $type_of_alignment;
     }
    //$iconDelete->align = false
//       $iconEdSelect = $this->getObject('geticon','htmlelements');
//   $iconEdSelect->setIcon('edit');
//   $iconEdSelect->alt = "Edit Comment";
    public function createNewObjectFromModule()
    {
// return   $this->objForm = new form($name_of_form, $form_action);

}

public function EditModule()
{
}





}


?>
