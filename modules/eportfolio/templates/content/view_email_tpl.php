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
    'action' => 'add_email'
)));
$objLink->link = $iconAdd->show();
$linkAdd = $objLink->show();
//}
//$objUser;
// Show the heading
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_emailList", 'eportfolio') . ' ' . $objUser->fullname() . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
echo $objHeading->show();
//echo $objUser->userId();
//echo $userId;
$emailList = $this->objDbEmailList->getByItem($userId);
echo "<br/>";
// Create a table object
$mytable = &$this->newObject("htmltable", "htmlelements");
$mytable->border = 0;
$mytable->cellspacing = '12';
$mytable->cellpadding = '12';
$mytable->width = "100%";
// Add the table heading.
$mytable->startRow();
$mytable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_emailtype", 'eportfolio') . "</b>");
$mytable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_email", 'eportfolio') . "</b>");
$mytable->endRow();
// Step through the list of addresses.
$class = 'even';
if (!empty($emailList)) {
    $i = 0;
    foreach($emailList as $myitem) {
        $class = ($class == (($i++%2) == 0)) ? 'even' : 'odd';
        // Display each field for addresses
        $mytable->startRow();
        $mytable->addCell($myitem['type'], "", NULL, NULL, $class, '');
        $mytable->addCell($myitem['email'], "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("mod_eportfolio_edit", 'eportfolio');
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editemail',
            'id' => $myitem["id"]
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
            'action' => 'deleteEmail',
            'id' => $myitem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $mytable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $mytable->endRow();
    }
    unset($myitem);
} else {
    $mytable->startRow();
    $mytable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
    $mytable->endRow();
}
echo $mytable->show();
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'main'
)));
$mainlink->link = 'ePortfolio home';
echo '<br clear="left" />' . $mainlink->show();
?>
