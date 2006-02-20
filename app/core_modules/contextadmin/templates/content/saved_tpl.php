<?php


$str='<h3>&nbsp;&nbsp;'.$this->objLanguage->languageText("mod_context_succsave").'&nbsp;'.$this->objDBContext->getDate().'</h3>';

echo $str;

$link = &$this->newObject('link','htmlelements');
$icon = &$this->newObject('geticon','htmlelements');
$icon->setIcon('prev');

$link->link = $icon->show();
if($this->getParam('fromwhere')=='context')
{
    $link->href=$this->uri(array( ),'context');
    $icon->alt= 'Back to '.$this->objDBContext->getTitle();
    $link->link= '&nbsp;'.$icon->show().'&nbsp;Back to '.$this->objDBContext->getTitle();
    
 }
 else
 {
    $link->href=$this->uri(array(), 'contextadmin' );
    $icon->alt= ucwords($this->objLanguage->code2Txt('mod_contextadmin_name',array('context'=>'course')));    
    $link->link= $icon->show().ucwords($this->objLanguage->code2Txt('mod_contextadmin_name',array('context'=>'course')));    
 }
 
 
 
 echo '<br>'.$link->show();

?>
