<?php

$objH =  $this->newObject('htmlheading','htmlelements');
$objForm =  $this->newObject('form','htmlelements');
$inpButton =  $this->newObject('button','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('textinput','htmlelements');

foreach($dbData as $dataOld)
{
$id = new textinput('id',$dataOld['id']);
$idLab = new label('id',$dataOld['id']);
$contextcode = new textinput('contextcode',$dataOld['contextcode']);
$contextcodeLab = new label('contextcode',$dataOld['contextcode']);
$title = new textinput('title',$dataOld['title']);
$titleLab = new label('title',$dataOld['title']);
$menutext = new textinput('menutext',$dataOld['menutext']);
$menutextLab = new label('menutext',$dataOld['menutext']);
$about = new textinput('about',$dataOld['about']);
$aboutLab = new label('about',$dataOld['about']);
$userid = new textinput('userid',$dataOld['userid']);
$useridLab = new label('userid',$dataOld['userid']);
$datecreated = new textinput('datecreated',$dataOld['datecreated']);
$datecreatedLab = new label('datecreated',$dataOld['datecreated']);
$metadata_id = new textinput('metadata_id',$dataOld['metadata_id']);
$metadata_idLab = new label('metadata_id',$dataOld['metadata_id']);
$access="";
if($dataOld['isclosed'] == 0)
{
	$access = "Public";
}else{
	$access = "Private";
}
$isclosed = new textinput('access',$access);
$isclosedLab = new label('access',$access);
$active="";
if($dataOld['isactive'] == 1)
{
	$active = "Published";
}else{
	$active = "UnPublished";
}
$isactive = new textinput('isactive',$active);
$isactiveLab = new label('isactive',$active);
$status = new textinput('status',"Published");
$statusLab = new label('status',"Published");
}

//Button
$inpButton->cssClass = 'f-submit';
$inpButton->setValue('Pass Information');
$inpButton->setToSubmit();

//setup the form
$objForm->name = 'impfrm';
$objForm->action = $this->uri(array('action' => 'passcourse'));

$objForm->addToForm($idLab->show()." : ".$id->show().'<br/>');
$objForm->addToForm($contextcodeLab->show()." : ".$contextcode->show().'<br/>');
$objForm->addToForm($titleLab->show()." : ".$title->show().'<br/>');
$objForm->addToForm($menutextLab->show()." : ".$menutext->show().'<br/>');
$objForm->addToForm($aboutLab->show()." : ".$about->show().'<br/>');
$objForm->addToForm($useridLab->show()." : ".$userid->show().'<br/>');
$objForm->addToForm($datecreatedLab->show()." : ".$datecreated->show().'<br/>');
$objForm->addToForm($metadata_idLab->show()." : ".$metadata_id->show().'<br/>');
$objForm->addToForm($isclosedLab->show()." : ".$isclosed->show().'<br/>');
$objForm->addToForm($isactiveLab->show()." : ".$isactive->show().'<br/>');
$objForm->addToForm($statusLab->show()." : ".$status->show().'<br/>');
$objForm->addToForm($inpButton->show().'<br/>');

print $objForm->show().'<br/>';

?>