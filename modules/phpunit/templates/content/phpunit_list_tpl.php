<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/*
$objBox = $this->newObject('jqboxy', 'htmlelements');
$objBox->setHtml($this->objUi->getAddMappingForm());
$objBox->setTitle('Add URL');
$objBox->attachClickEvent('box_01');
*/

/*
$leftContent = "<a id='box_01' href='#' onclick='javascript:void(0)'>New Dialog</a><br/>";
$leftContent .= "<a id='refresh_grid' href='#' onclick='javascript:void(0)'>Refresh</a>";
*/

//$middleContent = $this->objUi->showTopNav() . $this->objUi->showCreateTestCaseForm();
$middleContent = $this->objUi->showCreateTestCaseForm();

$this->setVar('leftContent', $leftContent);
$this->setVar('middleContent', $middleContent);

?>
