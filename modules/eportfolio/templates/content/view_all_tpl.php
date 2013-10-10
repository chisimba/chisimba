<?php
$this->loadClass('checkbox', 'htmlelements');
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
    'action' => 'add_address',
)));
$objLink->link = $iconAdd->show();
$linkAdd = $objLink->show();
//}
//$objUser;
// Show the heading
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_heading", 'eportfolio') . ' ' . $objUser->fullname() . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
echo $objHeading->show();
//echo $objUser->userId();
//echo $userId;
//	$addressList = $this->objDbAddressList->getByItem($userId);
echo "<br/>";
// Create a table object
$addressTable = &$this->newObject("htmltable", "htmlelements");
$addressTable->border = 0;
$addressTable->cellspacing = '12';
$addressTable->cellpadding = '12';
$addressTable->width = "100%";
// Add the table heading.
$addressTable->startRow();
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_select", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_streetno", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_streetname", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_locality", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_city", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_postcode", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_postaddress", 'eportfolio') . "</b>");
$addressTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($addressList)) {
    $i = 0;
    foreach($addressList as $addressItem) {
        $class = ($class == (($i++%2) == 0)) ? 'even' : 'odd';
        // Display each field for addresses
        $cattype = $this->objDbCategorytypeList->listSingle($addressItem['type']);
        $addressTable->startRow();
        //$addressTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $addressTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $addressTable->addCell($addressItem['street_no'], "", NULL, NULL, $class, '');
        $addressTable->addCell($addressItem['street_name'], "", NULL, NULL, $class, '');
        $addressTable->addCell($addressItem['locality'], "", NULL, NULL, $class, '');
        $addressTable->addCell($addressItem['city'], "", NULL, NULL, $class, '');
        $addressTable->addCell($addressItem['postcode'], "", NULL, NULL, $class, '');
        $addressTable->addCell($addressItem['postal_address'], "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("mod_eportfolio_edit", 'eportfolio');
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editaddress',
            'id' => $addressItem["id"]
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
            'action' => 'deleteConfirm',
            'id' => $addressItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $addressTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $addressTable->endRow();
    }
    unset($addressItem);
} else {
    $addressTable->startRow();
    $addressTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="8"');
    $addressTable->endRow();
}
echo $addressTable->show();
$addresslink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'main'
)));
$addresslink->link = 'ePortfolio home';
echo '<br clear="left" />' . $addresslink->show();
?>
