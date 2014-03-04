     <?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS Admin
*/

/**
* Decent work template for the LRS postlogin
* Author Brent van Rensburg
*/

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');

//Create form
$objDecentWorkForm = new form('award', $this->uri(array('action'=>'decentwork','selected'=>'init_10'),'award'));



//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_decent_work_header','award');

if(isset($currentYr)) {
	$header->str .= '  '.$currentYr;
}

$objDecentWorkForm->addToForm($header->show());

$name = $this->objLanguage->languageText('mod_lrs_decent_work_name', 'award');
$value = $this->objLanguage->languageText('mod_lrs_decent_work_value', 'award');
$unit = $this->objLanguage->languageText('mod_lrs_decent_work_unit', 'award');
$source = $this->objLanguage->languageText('mod_lrs_decent_work_source', 'award');
$year = $this->objLanguage->languageText('mod_lrs_decent_work_year','award');
$note = $this->objLanguage->languageText('mod_lrs_decent_work_note', 'award');
$msg = $this->objLanguage->languageText('mod_lrs_decent_work_no_records','award');

$categoryList = $this->objdecentWorkCategory->getAll("ORDER BY category ASC");

$objCategoryTable = $this->newObject('htmltable','htmlelements');
$objCategoryHeadTable = $this->newObject('htmltable','htmlelements');

$content = '';
$class = '';
if(count($categoryList) > 0) {
	foreach($categoryList as $catList) {
		$objCategoryTable->init();
		$objCategoryTable->cellspacing = 2;
		$objCategoryHeadTable->init();
		$objCategoryHeadTable->cellspacing = 2;
		
		$headerCat = $this->getObject('htmlheading','htmlelements');
		$headerCat->type = 4;
		$headerCat->str = ucfirst(strtolower($catList['category']));
		
		$objCategoryHeadTable->startRow();
		$objCategoryHeadTable->addCell($headerCat->show(), '60%', 'bottom');
		$objCategoryHeadTable->endRow();
		
		if(isset($currentYr)) {
			$valuesList = $this->objdecentWorkValues->getAll("WHERE categoryid = '{$catList['id']}' AND year = '$currentYr' ORDER BY label ASC");
		} else {
			$valuesList = $this->objdecentWorkValues->getAll("WHERE categoryid = '{$catList['id']}' ORDER BY label ASC");
		}
		
		$objCategoryTable->startHeaderRow('odd');
		$objCategoryTable->addHeaderCell($name, '25%');
		$objCategoryTable->addHeaderCell($value, '10%');
		$objCategoryTable->addHeaderCell($unit, '20%');
		$objCategoryTable->addHeaderCell($source, '10%');
		$objCategoryTable->addHeaderCell($year, '10%');
		$objCategoryTable->addHeaderCell($note, '25%');
		$objCategoryTable->endHeaderRow();
		
		if($valuesList == NULL) {
			$message = "<i>".$this->objLanguage->languageText('mod_lrs_decent_work_no_content', 'award')."</i>";
			$objCategoryTable->startRow();
			$objCategoryTable->addCell($message);
			$objCategoryTable->addCell("<br />");
			$objCategoryTable->addCell("<br />");
			$objCategoryTable->addCell("<br />");
			$objCategoryTable->addCell("<br />");
			$objCategoryTable->endRow();
		} else {
			foreach($valuesList as $valList) {
				$valListLabel = ucfirst(strtolower($valList['label']));
				$valListUnit = ucfirst(strtolower($valList['unit']));
				$valListSource = ucfirst(strtolower($valList['source']));
				
				$class = ($class=='odd')? 'even' : 'odd';
				$objCategoryTable->startRow($class);
				$objCategoryTable->addCell($valListLabel);
				$objCategoryTable->addCell($valList['value']);
				$objCategoryTable->addCell($valListUnit);
				$objCategoryTable->addCell($valListSource);
				$objCategoryTable->addCell($valList['year']);
				$objCategoryTable->addCell($valList['notes']);
				$objCategoryTable->endRow();
			}
		}
		$objCategoryTable->startRow();
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->endRow();
		$content .= $objCategoryHeadTable->show().$objCategoryTable->show();
	}
} else {
	$objCategoryTable->init();
	$objCategoryTable->cellspacing = 2;
	
	$message = "<span class = 'noRecordsMessage'>$msg</span>";
	
	$objCategoryTable->startRow();
	$objCategoryTable->endRow();
	
	$objCategoryTable->startRow();
	$objCategoryTable->addCell("<br />");
	$objCategoryTable->endRow();
	
	$content = $objCategoryTable->show();
}
$objDecentWorkForm->addToForm($content);

$objUpdateTable = $this->newObject('htmltable', 'htmlelements');
$objUpdateTable->cellspacing = '2';
$objUpdateTable->cellpadding = '2';

$objyear = new textinput('updateYear', $currentYr, NULL, '4');

$btnUpdateYr = new button('submitYear');
$btnUpdateYr->setToSubmit();
$btnUpdateYr->setValue(' '.$this->objLanguage->languageText("word_update").' ');

$objUpdateTable->startRow();
$objUpdateTable->addCell("<i>".$this->objLanguage->languageText('mod_lrs_decent_work_select_year', 'award')."</i>");
$objUpdateTable->addCell("<br />");
$objUpdateTable->endRow();

$objUpdateTable->startRow();
$objUpdateTable->addCell($objyear->show().'  '.$btnUpdateYr->show());
$objUpdateTable->endRow();

$objDecentWorkForm->addToForm($objUpdateTable->show());

echo $objDecentWorkForm->show();


if($categoryList == NULL) {
	$message = "<span class = 'noRecordsMessage'>$msg</span>";
	echo $message;
}

if ($this->objUser->isAdmin()) {
	$link = new link($this->uri(array('action'=>'decentworkadmin','selected'=>'init_10'),'award'));
	$link->link = $this->objLanguage->languageText('mod_lrs_admin','award');

	$objAdminLinkTable = new htmlTable('award');
	$objAdminLinkTable->cellspacing = 2;
	
	$objAdminLinkTable->startRow();
	$objAdminLinkTable->addCell("<br />");
	$objAdminLinkTable->endRow();
	
	$objAdminLinkTable->startRow();
	$objAdminLinkTable->addCell($link->show());
	$objAdminLinkTable->endRow();
	
	echo $objAdminLinkTable->show();		
}
?>