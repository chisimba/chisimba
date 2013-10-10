<?php

/* ------------icon request template----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check


//preload neccessary html element classes
$this->loadClass('radio','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('iframe', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objH = &$this->newObject('htmlheading','htmlelements');
$objH->type = 1;
$objH->str = $this->objLanguage->languageText("mod_request_page_title", 'iconrequest');

$hTable = &$this->newObject('htmltable','htmlelements');
$hTable->width = '100%';
$hTable->startRow();
$hTable->addCell($objH->show(),Null,'top','left');
$hTable->addCell(Null);
$hTable->endRow();
echo $hTable->show();

$case = $this->getParam('action');
if (!$this->dbDev->isEmpty()) {		//check that icon developer has been set
if ($this->objUser->userId() != 1) {
$objForm = new form('icon_form',$this->uri(array('action'=>'submit')));
$modName = '';
$iconName = '';
$iconDesc = '';
$iconPerc = '';
$link1 = 'http://';
$link2 = 'http://';
$reqId = $this->objUser->userName().time();
$pSelected = 'n';
$tSelected = 'm';
$ptSelected = '5';
$example = '';
$updatePerc = new textinput('percentage',$iconPerc,'hidden',3);
$index = new textinput('pk',0,'hidden',null);
$ic = 0;
$updateLabel = new label('','');
$title = $objLanguage->languageText('mod_iconrequest_form_title');

if ($case=='edit') {
	$objForm = new form('icon_form',$this->uri(array('action'=>'update')));
	$icon = $this->dbReq->getRow('id',$this->getParam('pkid'));
	$example = $this->dbFile->getRow('reqId',$icon['reqId']);
	if ($example != null) {
		$ic = 1;
	}
	$modName = $icon['modName'];
	$iconName = $icon['iconName'];
	$iconDesc = $icon['description'];
	$link1 = $icon['uri1'];
	$link2 = $icon['uri2'];
	$reqId = $icon['reqId'];
	$updatedBy = $this->objUser->fullName($icon['uploaded']);
	$tSelected = $icon['type'];
	$pSelected = $icon['priority'];
	/*
	*	Auhtor : Dean Van Niekerk
	*  dvanniekerk@uwc.ac.za
	*/
	$ptSelected = $icon['phptype'];
		
	$iconPerc = $icon['complete'];
	$updatePerc = new textinput('percentage',$iconPerc,Null,3);
	$index = new textinput('pk',$icon['id'],'hidden',Null);
	$updateLabel = new label($objLanguage->languageText('form_update_label'),'percentage');
	$title = $objLanguage->languageText('mod_edit_page_title');
	$reqId = $icon['reqId'];
}

//preload neccessary html element classes
$this->loadClass('radio','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('iframe', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

//initialise text inputs
$objInput1 = new textinput('module_name',$modName,Null,40);
$objInput2 = new textinput('icon_name',$iconName,Null,40);
$objInput2 = new textinput('icon_name',$iconName,Null,40);
$objInput3 = new textarea('icon_description',$iconDesc,6,37);
$objUri1 = new textinput('idea_uri1',$link1,Null,40);
$objUri2 = new textinput('idea_uri2',$link2,Null,40);

$hiddenId = new hiddeninput('reqId',$reqId);

//initialise radio buttons
$objRadioType = new radio('icon_type');
$objRadioType->addOption('m',$objLanguage->languageText('word_module'));
$objRadioType->addOption('c',$objLanguage->languageText('word_common'));
$objRadioType->setSelected($tSelected);

$objRadioPriority = new radio('priority');
$objRadioPriority->addOption('y',$objLanguage->languageText('word_yesterday'));
$objRadioPriority->addOption('h',$objLanguage->languageText('word_high'));
$objRadioPriority->addOption('n',$objLanguage->languageText('word_normal'));
$objRadioPriority->setSelected($pSelected);

$objRadioPhpType = new radio('rdbtphptype');
$objRadioPhpType->addOption('4',$objLanguage->languageText('word_php4'));
$objRadioPhpType->addOption('5',$objLanguage->languageText('word_php5'));
$objRadioPhpType->setSelected($ptSelected);


//initialise labels
$label1 = new label($objLanguage->languageText('form_label1'),'module_name');
$label2 = new label($objLanguage->languageText('form_label2'),'priority');
$label3 = new label($objLanguage->languageText('form_label3'),'icon_type');


//made change to add the radio button
// Author of changes      : Dean Van Niekerk
//Email address of author : dvanniekerk@uwc.ac.za

$label11 = new label($objLanguage->languageText('form_label11'),'php_type');

$label4 = new label($objLanguage->languageText('form_label4'),'icon_name');
$label5 = new label($objLanguage->languageText('form_label5'),'icon_description');
$label6 = new label($objLanguage->languageText('form_label7'),'idea_upload');
$label7 = new label($objLanguage->languageText('form_label8'),'idea_uri1');
$label8 = new label($objLanguage->languageText('form_label9'),'idea_uri2');


//initialise buttons
$objSubmit = new button('form_submit',$objLanguage->languageText('word_submit'));
$objSubmit->setToSubmit();

$objClear = new button('form_clear',$objLanguage->languageText('word_cancel'));
$returnUrl = $this->uri(null);
$objClear->setOnClick("window.location = '$returnUrl'");

$objUpButton = new button('upload',$objLanguage->languageText('word_upload'));
$objUpButton->setOnClick(null);

//main table
$table = &$this->newObject('htmltable','htmlelements');
$table->width = '600';
$table->cellspacing = '3';
$table->cellpadding = '2';

//header row
$table->startHeaderRow();
$table->addHeaderCell($title);
$table->addHeaderCell(Null);
$table->endHeaderRow();

//row 1
$table->startRow();
$table->addCell($label1->show(),null,'top','left');
$table->addCell($objInput1->show(),null,'top','left');
$table->endRow();

//row 2
$table->startRow();
$table->addCell($label2->show(),null,'top','left');
$table->addCell($objRadioPriority->show(),null,'top','left');
$table->endRow();

//row 3
$table->startRow();
$table->addCell($label3->show(),null,'top','left');
$table->addCell($objRadioType->show(),null,'top','left');
$table->endRow();


/*
	Author of changes : Dean Van Niekerk
	E-mail : dvanniekerk@uwc.ac.za
	Adding to controlls requested for change
*/
//row 4
$table->startRow();
$table->addCell($label11->show(),null,'top','left');
$table->addCell($objRadioPhpType->show(),null,'top','left');
$table->endRow();


//row 5
$table->startRow();
$table->addCell($label4->show(),null,'top','left');
$table->addCell($objInput2->show(),null,'top','left');
$table->endRow();

//row 5
$table->startRow();
$table->addCell($label5->show(),null,'top','left');
$table->addCell($objInput3->show(),null,'top','left');
$table->endRow();

//row 6
$table->startRow();
$table->addCell($objLanguage->languageText('form_label6'),null,'top','left');
$table->addCell(Null,null,'top','left');
$table->endRow();

//row 7&8
//Iframe Attachments
$iframe = new iframe ();
$iframe->src = $this->uri(array('action'=>'tempframe', 'Id'=>$reqId, 'icon'=>$ic, 'case' =>$case));
$iframe->width = 400;
$iframe->height = 130;
$iframe->frameborder = 0;

$table->startRow();
$table->addCell($label6->show(),null,'top','left');
$table->addCell($iframe->show(), NULL, 'top', 'left', NULL, 'colspan="2"');
$table->endRow();

//row 9
$table->startRow();
$table->addCell($label7->show(),null,'top','left');
$table->addCell($objUri1->show(),null,'top','left');
$table->endRow();

//row 10
$table->startRow();
$table->addCell($label8->show(),null,'top','left');
$table->addCell($objUri2->show(),null,'top','left');
$table->endRow();

if ($case = 'edit') {
	$table->startRow();
	$table->addCell($updateLabel->show(),null,'top','left');
	$table->addCell($updatePerc->show(),null,'top','left');
	$table->endRow();
}


//main form
$objForm->setDisplayType(4);
$objForm->addToFormEx($table,$hiddenId->show().$index->show());
$objForm->addToFormEx($objSubmit->show().' &nbsp; '.$objClear->show());

//data validation rules
$objForm->addRule('module_name',$objLanguage->languageText('mod_name_req_msg'),'required');
$objForm->addRule('icon_name',$objLanguage->languageText('icon_name_req_msg'),'required');
$objForm->addRule('icon_description',$objLanguage->languageText('icon_desc_req_msg'),'required');

//display the form containing the whole page
echo $objForm->show();
} else {
$back = $this->getObject('link','htmlelements');
$back->link($this->uri(Null));
$back->link = $this->objLanguage->languageText('word_back', 'iconrequest');
$back->extra = "class=pseudobutton";
$content = $this->objLanguage->languageText('mod_iconrequest_notadmin', 'iconrequest').'<br>';
$content .= $back->show();
echo $content;
}
} else {	//display message prompting user to set icon developer information
$back = $this->getObject('link','htmlelements');
$back->link($this->uri(Null));
$back->link = $this->objLanguage->languageText('word_back', 'iconrequest');
$back->extra = "class=pseudobutton";
$content = $this->objLanguage->languageText('mod_set_dev_inf', 'iconrequest').'<br>';
$content .= $back->show();
echo $content;
}


?>
