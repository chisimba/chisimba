<?php
$baseFolder = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
$nav = $this->objUtils->getTree($baseFolder, $selected);
$managenav = $this->objUtils->getManageTree($baseFolder, $selected);

$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('tabcontent', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');


//Create a dropdown to hold the search parameters
/*$filterdrops = new dropdown('filter11');

$filterdrops->addOption("Ref No");
$filterdrops->addOption("Title");
$filterdrops->addOption("Owner");
$filterdrops->addOption("Telephone");
$filterdrops->addOption("Date");
*/
//Create a submit button
$filterbutton = new button('filtersearch', "Search");
$filterbutton->setToSubmit();



$textinput = new textinput('filtervalue');
$textinput->size = 17;

$table = &$this->newObject('htmltable', 'htmlelements');
$table->width = '600px';
$objDateTime = $this->getObject('dateandtime', 'utilities');
$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->name = 'startdate';
$objDatePicker2 = $this->newObject('datepicker', 'htmlelements');
$objDatePicker2->name = 'enddate';

//Radio button Group
$objElementDefault = new radio('filter');
$objElementDefault->addOption('Default',$this->objLanguage->languageText('mod_wicid_gsearch', 'wicid', 'General search')." *");
//$objElement->setBreakSpace($table->show());
$objElementDefault->setSelected('Default');
$objElementDefault = $objElementDefault->show();

//Radio button Group
$objElementDate = new radio('filter');
$objElementDate->addOption('Date',$this->objLanguage->languageText('mod_wicid_phrasesearchby', 'wicid', 'Search by')." ".$this->objLanguage->languageText('mod_wicid_searchbydate', 'wicid', 'Date'));
//$objElement->setBreakSpace($table->show());
$objElementDate =  $objElementDate->show();

$table->startRow();
$table->addCell($objElementDefault.": ", "30%", "top", "left");
$table->addCell($textinput->show(), "35%", "top", "left");
//$table->addCell("<b>".$this->objLanguage->languageText('mod_wicid_documenttype', 'wicid', 'Document type')." : </b>", "35%", "top", "left");
$table->endRow();
//Check Box for approved doc
$objSearchDocType = new checkbox('approveddocs', $this->objLanguage->languageText('mod_wicid_approveddocs','wicid','Approved documents'));
$objSearchDocType->setValue('Y');
$table->startRow();
$table->addCell($objElementDate . ": ", "30%", "top", "left");
$table->addCell(" ", "35%", "top", "left");
//$table->addCell($objSearchDocType->show()." ".$this->objLanguage->languageText('mod_wicid_approveddocs','wicid','Approved documents'), "35%", "top", "left");
$table->endRow();
//Check Box for unapproveddocs
$objSearchDocType = new checkbox('unapproveddocs', $this->objLanguage->languageText('mod_wicid_approveddocs','wicid','Approved documents'));
$objSearchDocType->setValue('Y');
$table->startRow();
$table->addCell("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->objLanguage->languageText('mod_wicid_startdate', 'wicid', 'Start date') . ": ", "30%", "top", "left");
$table->addCell($objDatePicker->show(), "35%", "top", "left");
//$table->addCell($objSearchDocType->show()." ".$this->objLanguage->languageText('mod_wicid_unapproveddocs','wicid','Unapproved documents'), "35%", "top", "left");
$table->endRow();
//Check Box for rejecteddocs
$objSearchDocType = new checkbox('rejecteddocs', $this->objLanguage->languageText('mod_wicid_approveddocs','wicid','Approved documents'));
$objSearchDocType->setValue('Y');
$table->startRow();
$table->addCell("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->objLanguage->languageText('mod_wicid_enddate', 'wicid', 'End date') . ": ", "30%", "top", "left");
$table->addCell($objDatePicker2->show(), "35%", "top", "left");
//$table->addCell($objSearchDocType->show()." ".$this->objLanguage->languageText('mod_wicid_rejecteddocs','wicid','Rejected documents'), "35%", "top", "left");
$table->endRow();
$table->startRow();
$table->addCell($filterbutton->show(), "30%", "top", "left");
$table->addCell(" ", "35%", "top", "left");
//$table->addCell("", "35%", "top", "left");
$table->endRow();
$table->startRow();
$table->addCell("* "." ".$this->objLanguage->languageText('mod_wicid_thisincludes', 'wicid', 'This includes').": ".$this->objLanguage->languageText('mod_wicid_searchby', 'wicid', 'Ref No., Title, Owner or Telephone'), "100%", "top", "left","","colspan='2'");
$table->endRow();

//Add date to fieldset
$otset = new fieldset();
$otset->setLegend("");
$otset->addContent($table->show());

$fsetdateother = $otset->show();

//Add a form to contain the search feature
$form = new form('searchdocs', $this->uri(array('action' => 'filterbyparam')));
//$form->addToForm($fsetdateother);
$form->addToForm($table->show());
//Add search table to fieldset
$filterset = new fieldset();
$filterset->setLegend($this->objLanguage->languageText('mod_wicid_searchdocs', 'wicid', 'Search documents'));
$filterset->addContent($form->show());

$filters = $filterset->show();


$searchForm = new form('filesearch', $this->uri(array('action' => 'search')));
$searchForm->method = 'GET';
$hiddenInput = new hiddeninput('module', 'filemanager');
$searchForm->addToForm($hiddenInput->show());


$hiddenInput = new hiddeninput('action', 'search');
$searchForm->addToForm($hiddenInput->show());

$textinput = new textinput('filequery', $this->getParam('filequery'));
$searchForm->addToForm($textinput->show());

$button = new button('search', $this->objLanguage->languageText('word_search', 'system', 'Search'));
$button->setToSubmit();
$searchForm->addToForm($button->show());



//get file list
// Create an Instance of the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');

$header = new htmlheading();
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_wicid_name', 'wicid', 'WICID');

$leftColumn = $header->show();

//Disable old search
//$leftColumn .= $searchForm->show();

$filters = $filterset->show();
$leftColumn .= '<div class="filemanagertree">' . $managenav. $nav . '</div>';

//New Search
$rightColumn = $filters;
$cssLayout->numColumns = 2;
$cssLayout->setLeftColumnContent($leftColumn);
$cssLayout->setMiddleColumnContent($rightColumn.$this->getContent());
//$cssLayout->setRightColumnContent($rightColumn);
// Display the Layout
echo $cssLayout->show();
?>