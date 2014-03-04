<?php
//View Goals
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
    'action' => 'add_goals'
)));
$objLink->link = $iconAdd->show();
$linkAdd = $objLink->show();
// Show the heading
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_goalList", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
echo $objHeading->show();
$goalsList = $this->objDbGoalsList->getByItem($userId);
echo "<br/>";
// Create a table object
$table = &$this->newObject("htmltable", "htmlelements");
$table->border = 0;
$table->cellspacing = '3';
$table->width = "60%";
// Add the table heading.
$table->startRow();
$table->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_Goals", 'eportfolio') . "</b>");
$table->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($goalsList)) {
    $i = 0;
    echo "<ol type='1'>";
    foreach($goalsList as $item) {
        // Display each field for activities
        $table->startRow();
        $table->addCell("<li>" . $item['shortdescription'] . "</li>", "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("mod_eportfolio_edit", 'eportfolio');
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editgoals',
            'id' => $item["id"]
        )));
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
            'action' => 'deletegoals',
            'id' => $item["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        $table->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $table->endRow();
    }
    unset($item);
    echo "</ol>";
} else {
    $table->startRow();
    $table->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
    $table->endRow();
}
echo $table->show();
$addlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_goals'
)));
$addlink->link = $objLanguage->languageText("mod_eportfolio_addGoal", 'eportfolio');
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'main'
)));
$mainlink->link = 'ePortfolio home';
echo '<br clear="left" />' . $addlink->show() . ' / ' . $mainlink->show();
//End View Goals

?>
