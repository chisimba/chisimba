<?php

//View category if admin
$hasAccess = $this->objUser->isAdmin();
$this->setVar('pageSuppressXML', true);
if (!$hasAccess) {
    return $this->nextAction('home', array());
    break;
} else {
    //Language Items
    $notestsLabel = $this->objLanguage->languageText('mod_podcaster_norecords', 'podcaster', 'There are no records found');
    $linkAdd = '';
    // Show the add link
    $iconAdd = $this->getObject('geticon', 'htmlelements');
    $iconAdd->setIcon('add');
    $iconAdd->alt = $this->objLanguage->languageText("mod_podcaster_addcategory", 'podcaster', "Add category");
    $iconAdd->align = false;
    $objLink = &$this->getObject('link', 'htmlelements');
    $objLink->link($this->uri(array(
                'module' => 'podcaster',
                'action' => 'addcategory'
            )));
    $objLink->link = $iconAdd->show();
    $linkAdd = $objLink->show();
    // Show the heading
    $objHeading = &$this->getObject('htmlheading', 'htmlelements');
    $objHeading->type = 1;
    $objHeading->str = $this->objLanguage->languageText("mod_podcaster_listofcategories", 'podcaster', "List of categories") . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
    echo $objHeading->show();
    $categoryList = $this->objDbCategoryList->getAllCategories();
    echo "<br/>";
    // Create a table object
    $table = &$this->newObject("htmltable", "htmlelements");
    $table->border = 0;
    $table->cellspacing = '3';
    $table->width = "50%";
    // Add the table heading.
    $table->startRow();
    $table->addHeaderCell("<b>" . $this->objLanguage->languageText("mod_podcaster_category", 'podcaster', "Category") . "</b>");
        $table->addHeaderCell("<b>" . $this->objLanguage->languageText("word_description", 'system', "Description") . "</b>");
            $table->addHeaderCell("");
    $table->endRow();
    // Step through the list of addresses.
    $class = NULL;
    if (!empty($categoryList)) {
        foreach ($categoryList as $item) {
            // Display each field for activities
            $table->startRow();
            $table->addCell($item['category'], "", NULL, NULL, $class, '');
            $table->addCell($item['description'], "", NULL, NULL, $class, '');
            // Show the edit link
            $iconEdit = $this->getObject('geticon', 'htmlelements');
            $iconEdit->setIcon('edit');
            $iconEdit->alt = $this->objLanguage->languageText("word_edit", "system", "Edit");
            $iconEdit->align = false;
            $objLink = &$this->getObject("link", "htmlelements");
            $objLink->link($this->uri(array(
                        'module' => 'podcaster',
                        'action' => 'editcategory',
                        'id' => $item["id"]
                    )));
            //if( $this->isValid( 'edit' ))
            $objLink->link = $iconEdit->show();
            $linkEdit = $objLink->show();
            // Show the delete link
            $iconDelete = $this->getObject('geticon', 'htmlelements');
            $iconDelete->setIcon('delete');
            $iconDelete->alt = $this->objLanguage->languageText("word_delete", "system", "Delete");
            $iconDelete->align = false;
            $objConfirm = &$this->getObject("link", "htmlelements");
            $objConfirm = &$this->newObject('confirm', 'utilities');
            $checkAssociation = $this->objDbEvents->listByCategory($item["id"]);
            if (!empty($checkAssociation)) {
                $deleteLink = new link("javascript:alert('" . $this->objLanguage->languageText('mod_podcaster_deleteeventsincategory', 'podcaster', 'You need to delete the events in this category before deleting it') . ".');");
                $deleteLink->link = $iconDelete->show();
                $table->addCell($linkEdit . $deleteLink->show() , "", NULL, NULL, $class, '');
            } else {
                $objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
                    'module' => 'podcaster',
                    'action' => 'deletecategory',
                    'id' => $item["id"]
                )) , $objLanguage->languageText('mod_podcaster_confirmdeletecategory', 'podcaster', 'Are you sure you want to delete this category?'));
                $table->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
            }
            $table->endRow();
        }
        unset($item);
    } else {
        $table->startRow();
        $table->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
        $table->endRow();
    }
    echo $table->show();
    $addlink = new link($this->uri(array(
                        'module' => 'podcaster',
                        'action' => 'addcategory'
                    )));
    $addlink->link = $this->objLanguage->languageText("mod_podcaster_addcategory", 'podcaster', "Add category");
    $mainlink = new link($this->uri(array(
                        'module' => 'podcaster',
                        'action' => 'home'
                    )));

    $mainlink->link = $this->objLanguage->languageText("mod_podcaster_backtohome", 'podcaster', "Back to home");
    echo '<br clear="left" />' . $addlink->show() . ' / ' . $mainlink->show();
}
//End View category
?>