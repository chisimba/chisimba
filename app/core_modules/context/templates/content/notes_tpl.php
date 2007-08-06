<?php

$table=$this->newObject('htmltable','htmlelements');
$form=$this->newObject('form','htmlelements');
$ta=$this->newObject('textarea','htmlelements');
$saveBut=$this->newObject('button','htmlelements');
$idInput=$this->newObject('textinput','htmlelements');
$modeInput=$this->newObject('textinput','htmlelements');
$nodeIdInput=$this->newObject('textinput','htmlelements');
//echo $ta->value;

$ta->setColumns(40);
$ta->setRows(4);
$ta->name='note';
$ta->value=$noteValue;
$ar=array($ta->show());
$table->addRow($ar, "even", "align=\"center\"");

$saveBut->value=$this->objLanguage->languageText("mod_contextadmin_save",'context').'/Close';
//$saveBut->setOnClick('this.form.submit();window.close()');
$saveBut->setToSubmit();
$ar=array($saveBut->show());
$table->addRow($ar, "even", "align=\"center\"");

$nodeIdInput->name='nodeId';
$nodeIdInput->value=$nodeId;
$nodeIdInput->fldType='hidden';

$idInput->name='id';
$idInput->value=$noteId;
$idInput->fldType='hidden';


$modeInput->name='mode';
$modeInput->fldType='hidden';
if (empty($noteId))
	$modeInput->value='add';
else
	$modeInput->value='edit';


$form->setAction($this->uri(array('action'=>'savenote')));
$form->name='Notes';
$form->addToForm($idInput);
$form->addToForm($nodeIdInput);
$form->addToForm($modeInput);
$form->addToForm($table);

echo $form->show();
//echo $this->nodeId;
?>