<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/*
$objBox = $this->newObject('jqboxy', 'jquery');
$objBox->setHtml($this->objUi->getAddMappingForm());
$objBox->setTitle('Add URL');
$objBox->attachClickEvent('box_01');
*/

/*
$leftContent = "<a id='box_01' href='#' onclick='javascript:void(0)'>New Dialog</a><br/>";
$leftContent .= "<a id='refresh_grid' href='#' onclick='javascript:void(0)'>Refresh</a>";
*/

$leftContent = '<img src="skins/_common/icons/shorturl_big.png" style="padding:20px;"/>';

$middleContent = $this->objUi->showTopNav() . $this->objUi->showList();

$this->setVar('leftContent', $leftContent);
$this->setVar('middleContent', $middleContent);

?>
