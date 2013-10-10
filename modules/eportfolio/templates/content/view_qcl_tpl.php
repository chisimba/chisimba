<?php
//View Qcl
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
$linkAdd = '';
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_qcl',
)));
$objLink->link = $iconAdd->show();
$linkAdd = $objLink->show();
// Show the heading
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_qclheading", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
echo $objHeading->show();
$qclList = $this->objDbQclList->getByItem($userId);
echo "<br/>";
// Create a table object
$qclTable = &$this->newObject("htmltable", "htmlelements");
$qclTable->border = 0;
$qclTable->cellspacing = '3';
$qclTable->width = "100%";
// Add the table heading.
$qclTable->startRow();
$qclTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$qclTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_wordtitle", 'eportfolio') . "</b>");
$qclTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
$qclTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_level", 'eportfolio') . "</b>");
$qclTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
//$qclTable->addHeaderCell("<b>".$objLanguage->languageText("mod_eportfolio_shortdescription",'eportfolio')."</b>");
$qclTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($qclList)) {
    foreach($qclList as $qclItem) {
        // Display each field for addresses
        $qclTable->startRow();
        $qclTable->addCell($qclItem['qcl_type'], "", NULL, NULL, $class, '');
        $qclTable->addCell($qclItem['qcl_title'], "", NULL, NULL, $class, '');
        $qclTable->addCell($qclItem['organisation'], "", NULL, NULL, $class, '');
        $qclTable->addCell($qclItem['qcl_level'], "", NULL, NULL, $class, '');
        $qclTable->addCell($this->objDate->formatDate($qclItem['award_date']) , "", NULL, NULL, $class, '');
        //$qclTable->addCell($qclItem['shortdescription'], "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editqcl',
            'id' => $qclItem["id"]
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
            'action' => 'deleteqcl',
            'id' => $qclItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $qclTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $qclTable->endRow();
    }
    unset($qclItem);
} else {
    $qclTable->startRow();
    $qclTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
    $qclTable->endRow();
}
echo $qclTable->show();
$addlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_qcl'
)));
$addlink->link = $objLanguage->languageText("mod_eportfolio_addQualification", 'eportfolio');
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'main'
)));
$mainlink->link = 'ePortfolio home';
echo '<br clear="left" />' . $addlink->show() . ' / ' . $mainlink->show();
//End View Qcl

?>
