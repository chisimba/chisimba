<?php

/* ------------icon request template----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check


//Create template heading with add icon
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objLink = $this->uri(array('action' => 'request'));
$objAddIcon->setIcon("add", "gif");
$objAddIcon->alt = $objLanguage->languageText('mod_add_request');
$add = $objAddIcon->getAddIcon($objLink);
$objH = &$this->newObject('htmlheading','htmlelements');
$objH->type = 1;
//$objH->str = $this->objLanguage->languageText("mod_iconrequest_page_title"). "&nbsp" .$add;
$objH->str = $this->objLanguage->languageText("mod_iconrequest_page_title", 'iconrequest'). "&nbsp" .$add;

//Create text link to add request
$addLink = &$this->newObject('link', 'htmlelements');
$addLink->link($this->uri(array('action' => 'request')));
$addLink->link = $objLanguage->languageText('mod_add_request', 'iconrequest');

//Timeout message informing whether add succeeded or not
$submitMsg = & $this->newObject('timeoutMessage','htmlelements');
switch ($this->getParam('message')) {
	case 'confirm':
		$submitMsg->setMessage($this->objLanguage->languageText("phrase_success", 'iconrequest'));
		break;
	case 'fail':
		$submitMsg->cssId = "error";
		$submitMsg->setMessage($this->objLanguage->languageText("phrase_error", 'iconrequest'));
		break;
	default: break;
}

//display the current icon developer or an appropriate message if none exists yet
$objEdIcon = &$this->newObject('geticon', 'htmlelements');
$edLink = $this->uri(array('action' => 'developer'));
$changeDeveloper = '';
if (($this->objUser->userId() == $this->dbDev->getId()) || ($this->objUser->isAdmin())) {
	$changeDeveloper = $objEdIcon->getEditIcon($edLink);
}
if (!$this->dbDev->isEmpty()) {
    $id = $this->dbDev->getId();
	$email = $this->objUser->email($id);
	$developer = $this->objUser->fullname($id);
	$developerMsg = $this->objLanguage->languageText("dev_msg", 'iconrequest').' '.$developer.' '.$changeDeveloper;
} else {
	$developerMsg = $this->objLanguage->languageText("no_dev_msg", 'iconrequest').' '.$changeDeveloper;
}

//Table for header
$hTable = $this->newObject('htmltable','htmlelements');
$hTable->width = '50%';
$hTable->startRow();
$hTable->addCell($objH->show(),Null,'top','left');
$hTable->endRow();
$hTable->startRow();
$hTable->addCell($submitMsg->show(),Null,'top','left');
$hTable->endRow();
$hTable->startRow();
$hTable->addCell($developerMsg,Null,'top','left');
$hTable->endRow();
$hTable->startRow();
$hTable->addCell($this->objLanguage->languageText('mod_iconrequest_noadmin', 'iconrequest'),null,'top','left');
$hTable->endRow();

// Create a table for the data
$dataTable = $this->getObject('htmltable', 'htmlelements');
$dataTable->cellspacing = "2";
$dataTable->cellpadding = "2";
$dataTable->width = "90%";
$dataTable->attributes = "border='0'";
$tableRow = array();
$tableHd[] = $objLanguage->languageText('form_label1', 'iconrequest');
$tableHd[] = $objLanguage->languageText('form_label2', 'iconrequest');
$tableHd[] = $objLanguage->languageText('form_label3', 'iconrequest');
$tableHd[] = $objLanguage->languageText('form_label4', 'iconrequest');
$tableHd[] = $objLanguage->languageText('form_label11', 'iconrequest');
$tableHd[] = $objLanguage->languageText('heading_complete', 'iconrequest');
$tableHd[] = $objLanguage->languageText('heading_user', 'iconrequest');
$tableHd[] = $objLanguage->languageText('heading_added', 'iconrequest');
$tableHd[] = $objLanguage->languageText('heading_action', 'iconrequest');

// Create the table header for display
$dataTable->addHeader($tableHd, "heading");

//get all icon requests - most recent first
$request = $this->dbReq->getAll('ORDER BY time DESC');

$row = 0;

//loop through data
foreach ($request as $rec) {
	$row++;
	$class = ($row % 2 == 0) ? 'odd' : 'even';
	$Id = $rec['id'];
	$modName = $rec['modName'] ;
	// convert priority to human language
	switch ($rec['priority']) {
		case 'y' : $iconPr = $objLanguage->languageText('word_yesterday');
				break;
		case 'h' : $iconPr = $objLanguage->languageText('word_high');
				break;
		case 'n' : $iconPr = $objLanguage->languageText('word_normal');
				break;
	}
	/*
	*	Author : Dean Van Niekerk
	*  dvanniekerk@uwc.ac.za
	*/	
	// convert priority to human language
	switch ($rec['phptype']) {
		case '4' : $iconPt = $objLanguage->languageText('word_php4');
				break;
		case '5' : $iconPt = $objLanguage->languageText('word_php5');
				break;
		case '1' : $iconPt = $objLanguage->languageText('word_phpunkn');
				break;
	}	

	 
	//convert type to human language
	$iconTy = ($rec['type'] == 'm') ? $objLanguage->languageText('word_module') : $objLanguage->languageText('word_common');
	$iconName = $rec['iconName'] ;
	$iconCmp = $rec['complete'] ;
	$iconUser = $this->objUser->fullName($rec['uploaded']);
	$datefromdb = $rec['time'];
	$reqId = $rec['reqId'];

	//convert timestamp to human readable format
	$year = substr($datefromdb,0,4);
	$mon  = substr($datefromdb,4,2);
	$day  = substr($datefromdb,6,2);
	$hour = substr($datefromdb,8,2);
	$min  = substr($datefromdb,10,2);
	$sec  = substr($datefromdb,12,2);
	
	
	// DONE: snaidoo read date and time directly from database without doing any of above operations
	
	$date_format = $this->newObject('simplecal','datetime');
	
	$orgDate = $date_format->formatDate($datefromdb);
	
	//$orgDate = $datefromdb; //date("d/m/y, H:i",mktime($hour,$min,$sec,$mon,$day,$year));
	
	
	
	//add edit icon
	$objEditIcon = &$this->newObject('geticon', 'htmlelements');
	$editLink = $this->uri(array('action' => 'edit','pkid' => $Id));
    	$ed = $objEditIcon->getEditIcon($editLink);
    	//add delete icon
	$objDelIcon = $this->newObject('geticon', 'htmlelements');
	$delLink = array('action' => 'delete', 'Id' => $Id, 'reqId' => $reqId);
   $deletephrase = $objLanguage->languageText('mod_delete');
 //    	$objConfirm = & $this->newObject('confirm','utilities');
  //    $objConfirm->setConfirm($objDelIcon->show();
      //$conf = $objConfirm->show();    	
      $conf = $objDelIcon->getDeleteIconWithConfirm('', $delLink,'iconrequest', $deletephrase);
    	$actionColumn = $ed.$conf;
    	//populate table
    	$dataTable->startRow();
	$dataTable->addCell($modName,null,null,'left',$class);
	$dataTable->addCell($iconPr,null,null,'left',$class);
	$dataTable->addCell($iconTy,null,null,'left',$class);
	$dataTable->addCell($iconName,null,null,'left',$class);
	$dataTable->addCell($iconPt,null,null,'left',$class);
	$dataTable->addCell($iconCmp,null,null,'left',$class);
	$dataTable->addCell($iconUser,null,null,'left',$class);
	$dataTable->addCell($orgDate,null,null,'left',$class);
	$dataTable->addCell($actionColumn,null, 'top', 'center', $class);
	$dataTable->endRow();

//in case of no data, display appropriate message
if (empty($request)) {
    $dataTable->addCell("<span class='noRecordsMessage'>" . $objLanguage->languageText('mod_norec') . "</span>", null, 'top', 'center', null, 'colspan=10');
}
}
//show the content
$content = $hTable->show();
$content .= $dataTable->show();
$content .= $addLink->show();

echo $content;

?>
