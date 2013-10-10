<?php
//View category
$hasAccess = $this->objEngine->_objUser->isContextLecturer();
$hasAccess|= $this->objEngine->_objUser->isAdmin();
$this->setVar('pageSuppressXML', true);
if (!$hasAccess) {
    return $this->nextAction('main', array());
    break;
} else {
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
        'action' => 'add_category'
    )));
    $objLink->link = $iconAdd->show();
    $linkAdd = $objLink->show();
    // Show the heading
    $objHeading = &$this->getObject('htmlheading', 'htmlelements');
    $objHeading->type = 1;
    $objHeading->str = $objLanguage->languageText("mod_eportfolio_categoryList", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
    echo $objHeading->show();
    $categoryList = $this->objDbCategoryList->getByItem();
    echo "<br/>";
    // Create a table object
    $table = &$this->newObject("htmltable", "htmlelements");
    $table->border = 0;
    $table->cellspacing = '3';
    $table->width = "50%";
    // Add the table heading.
    $table->startRow();
    $table->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_category", 'eportfolio') . "</b>");
    $table->endRow();
    // Step through the list of addresses.
    $class = NULL;
    if (!empty($categoryList)) {
        foreach($categoryList as $item) {
            // Display each field for activities
            $table->startRow();
            $table->addCell($item['category'], "", NULL, NULL, $class, '');
            // Show the edit link
            $iconEdit = $this->getObject('geticon', 'htmlelements');
            $iconEdit->setIcon('edit');
            $iconEdit->alt = $objLanguage->languageText("word_edit");
            $iconEdit->align = false;
            $objLink = &$this->getObject("link", "htmlelements");
            $objLink->link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'editcategory',
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
            $checkAssociation = $this->objDbCategorytypeList->listCategory($item["id"]);
            if (!empty($checkAssociation)) {
                $deleteLink = new link("javascript:alert('" . $this->objLanguage->languageText('mod_eportfolio_failDelete', 'eportfolio') . ".');");
                $deleteLink->link = $iconDelete->show();
                $table->addCell($linkEdit . $deleteLink->show() , "", NULL, NULL, $class, '');
            } else {
                $objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
                    'module' => 'eportfolio',
                    'action' => 'deletecategory',
                    'id' => $item["id"]
                )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
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
        'module' => 'eportfolio',
        'action' => 'add_category'
    )));
    $addlink->link = $objLanguage->languageText("mod_eportfolio_addCategory", 'eportfolio');
    $mainlink = new link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'main'
    )));
    $mainlink->link = 'ePortfolio home';
    echo '<br clear="left" />' . $addlink->show() . ' / ' . $mainlink->show();
}
//End View category

?>
