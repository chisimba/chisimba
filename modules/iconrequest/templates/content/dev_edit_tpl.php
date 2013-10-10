<?php
/* ------------iconrequest class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

//Create template heading
$objH = $this->newObject('htmlheading','htmlelements');
$objH->type = 1;
//$objH->str = $this->objLanguage->languageText("mod_dev_page_title");
//$objH->str = $this->objLanguage->languageText('mod_iconrequest_page_title', 'iconrequest');
$objH->str = $this->objLanguage->languageText('mod_dev_page_title', 'iconrequest');

//Display current developer if set, else appropraite message
if (!$this->dbDev->isEmpty()) {
    $id = $this->dbDev->getId();
	$email = $this->objUser->email($id);
	$developer = $this->objUser->fullname($id);
	$developerMsg = $this->objLanguage->languageText("dev_msg", 'iconrequest').' '.$developer;
} else {
	$developerMsg = $this->objLanguage->languageText("no_dev_msg", 'iconrequest');
}

//Table for header
$hTable = $this->newObject('htmltable','htmlelements');
$hTable->width = '50%';
$hTable->startRow();
$hTable->addCell($objH->show(),Null,'top','left');
$hTable->endRow();
$hTable->startRow();
$hTable->addCell($developerMsg,Null,'top','left');
$hTable->endRow();

//load some useful classes
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('dropdown','htmlelements');

$users = $this->objUser->getArray("SELECT firstName, surname, userId FROM tbl_users WHERE 1");
$objElement = new dropdown('dev_id');
foreach ($users as $u) {
    $objElement->addOption($u['userId'],$u['firstName'].' '.$u['surname']);
}


//initialise the lables and inputs
$label1 = new label($objLanguage->languageText('mod_iconrequest_changedeveloper', 'iconrequest'),'dev_name');

//initialise buttons
$objSubmit = new button('form_submit',$objLanguage->languageText('word_submit', 'iconrequest'));
$objSubmit->setToSubmit();
$objClear = new button('form_clear',$objLanguage->languageText('word_cancel', 'iconrequest'));
$returnUrl = $this->uri(null);
$objClear->setOnClick("window.location = '$returnUrl'");

//main form
$objForm = new form('icon_form',$this->uri(array('action'=>'changedev')));
$objForm->setDisplayType(4);
$objForm->addToFormEx($label1->show().': &nbsp;',$objElement->show());
$objForm->addToFormEx($objSubmit->show(),$objClear->show());

//generate and display the page
if (($this->objUser->userId() == $this->dbDev->getId()) || ($this->objUser->isAdmin())) {
	$content = $hTable->show().$objForm->show();
} else {
	$msg = $this->objLanguage->languageText('mod_iconrequest_permission', 'iconrequest');
	$content = $hTable->show()."<p><span class=noRecordsMessage>$msg</span><br />&nbsp;</p>";
}
echo $content;
?>
