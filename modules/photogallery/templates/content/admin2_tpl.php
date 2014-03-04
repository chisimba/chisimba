<?php


$tabs = & $this->newObject('tabpane', 'htmlelements');

$tabs->addTab(array('name'=>'Overview','url'=>'http://localhost','content' => 'overview','default' => true));
 $tabs->addTab(array('name'=>'Comments','url'=>'http://localhost','content' => 'comments'));
 $tabs->addTab(array('name'=>'Upload','url'=>'http://localhost','content' => $this->_objUtils->getUploadBox()));
 $tabs->addTab(array('name'=>'Edit','url'=>'http://localhost','content' => 'edit','height' => '300','width' => '600'));


 echo $tabs->show();
?>