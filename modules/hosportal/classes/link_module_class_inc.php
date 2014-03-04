<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'chisimba_modules_handler_class_inc.php';

class link_module extends chisimba_modules_handler
{

private $objLink;


    public function init()
    {
         $this->objLink = $this->loadClass('link','htmlelements');

    }
//   $mngedlink = new link($this->uri(array(
//    'module'=>'hosportal',
//    'action'=>'edit',
//    'id' => $id
//   )));
//   $mngedlink->link = $iconEdSelect = $this->objIcon->showIcon();
//   $linkEdManage = $mngedlink->show();
    public function createNewObjectFromModule($url_in_array_format= NULL , $a = NULL)
    {
        return $this->objLink = new link($url_in_array_format);
        //return $this->objConfirm = &$this->newObject($name_of_class, $name_of_module);
    }

    public function embedLinkToObject($name_of_object_to_link)
    {
       return $this->objLink->link = $name_of_object_to_link;
    }
    public function showLink()
    {
    return $this->objLink->show();
    }

    

public function EditModule()
{
}





}


?>