<?php
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
$linkAdd = '';
//if( $this->isValid( 'add' ) ){
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_demographics'
)));
$objLink->link = $iconAdd->show();
$linkAdd = $objLink->show();
//}
//$objUser;
// Show the heading
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_demographics", 'eportfolio') . ' ' . $objUser->fullname() . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
echo $objHeading->show();
//echo $objUser->userId();
//echo $userId;
$demographicsList = $this->objDbDemographicsList->getByItem($userId);
echo "<br/>";
// Create a table object
$demographicsTable = &$this->newObject("htmltable", "htmlelements");
$demographicsTable->border = 0;
$demographicsTable->cellspacing = '12';
$demographicsTable->cellpadding = '12';
$demographicsTable->width = "100%";
// Add the table heading.
$demographicsTable->startRow();
$demographicsTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$demographicsTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_birth", 'eportfolio') . "</b>");
$demographicsTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio') . "</b>");
$demographicsTable->endRow();
// Step through the list of addresses.
$class = 'even';
if (!empty($demographicsList)) {
    $i = 0;
    foreach($demographicsList as $demographicsItem) {
        $class = ($class == (($i++%2) == 0)) ? 'even' : 'odd';
        // Display each field for Demographics
        $demographicsTable->startRow();
        $demographicsTable->addCell($demographicsItem['type'], "", NULL, NULL, $class, '');
        $demographicsTable->addCell($this->objDate->formatDate($demographicsItem['birth']) , "", NULL, NULL, $class, '');
        $demographicsTable->addCell($demographicsItem['nationality'], "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("mod_eportfolio_edit", 'eportfolio');
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editdemographics',
            'id' => $demographicsItem["id"]
        )));
        //if( $this->isValid( 'edit' ))
        $objLink->link = $iconEdit->show();
        $linkEdit = $objLink->show();
        // Show the delete link
        $iconDelete = $this->getObject('geticon', 'htmlelements');
        $iconDelete->setIcon('delete');
        $iconDelete->alt = $objLanguage->languageText("mod_eportfolio_delete", 'eportfolio');
        $iconDelete->align = false;
        $objConfirm = &$this->getObject("link", "htmlelements");
        $objConfirm = &$this->newObject('confirm', 'utilities');
        $objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
            'module' => 'eportfolio',
            'action' => 'deletedemographics',
            'id' => $demographicsItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $demographicsTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $demographicsTable->endRow();
    }
    unset($demographicsItem);
} else {
    $demographicsTable->startRow();
    $demographicsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
    $demographicsTable->endRow();
}
echo $demographicsTable->show();
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'main'
)));
$mainlink->link = 'ePortfolio home';
echo '<br clear="left" />' . $mainlink->show();
?>
