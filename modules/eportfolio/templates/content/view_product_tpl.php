<?php
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
$linkAdd = '';
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_product'
)));
$objLink->link = $iconAdd->show();
$linkAdd = $objLink->show();
// Show the heading
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_productlist", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
echo $objHeading->show();
$productlist = $this->objDbProductList->getByItem($userId);
echo "<br/>";
// Create a table object
$table = &$this->newObject("htmltable", "htmlelements");
$table->border = 0;
$table->cellspacing = '12';
$table->cellpadding = '12';
$table->width = "100%";
// Add the table heading.
$table->startRow();
$table->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_wordComment", 'eportfolio') . "</b>");
$table->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
$table->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$table->endRow();
// Step through the list of addresses.
if (!empty($productlist)) {
    $i = 0;
    foreach($productlist as $item) {
        // Display each field for products
        $table->startRow();
        $table->addCell($item['comment'], "", NULL, NULL, NULL, '');
        $table->addCell($this->objDate->formatDate($item['creation_date']) , "", NULL, NULL, NULL, '');
        $table->addCell($item['shortdescription'], "", NULL, NULL, NULL, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editproduct',
            'id' => $item["id"]
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
            'action' => 'deleteproduct',
            'id' => $item["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $table->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, NULL, '');
        $table->endRow();
    }
    unset($item);
} else {
    $table->startRow();
    $table->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
    $table->endRow();
}
echo $table->show();
$addlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_product'
)));
$addlink->link = $objLanguage->languageText("mod_eportfolio_addProduct", 'eportfolio');
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'main'
)));
$mainlink->link = 'ePortfolio home';
echo '<br clear="left" />' . $addlink->show() . ' / ' . $mainlink->show();
?>
