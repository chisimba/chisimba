<?php
//View categorytype
$hasAccess = $this->objEngine->_objUser->isContextLecturer();
$hasAccess|= $this->objEngine->_objUser->isAdmin();
$this->setVar('pageSuppressXML', true);
if (!$hasAccess) {
    return $this->nextAction('main', array());
    break;
} else {
    //Language Items
    $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
    $categoryList = $this->objDbCategoryList->getByItem();
    $mycategoryList = $this->objDbCategoryList->getByItem();
    $linkAdd = '';
    // Show the add link
    $iconAdd = $this->getObject('geticon', 'htmlelements');
    $iconAdd->setIcon('add');
    $iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
    $iconAdd->align = false;
    $objLink = &$this->getObject('link', 'htmlelements');
    $objLink->link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_categorytype'
    )));
    $objLink->link = $iconAdd->show();
    $linkAdd = $objLink->show();
    // Show the heading
    $objHeading = &$this->getObject('htmlheading', 'htmlelements');
    $objHeading->type = 1;
    //Check for categories
    if (!empty($categoryList)) {
        $objHeading->str = $objLanguage->languageText("mod_eportfolio_categorytypeList", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
    } else {
        $objHeading->str = $objLanguage->languageText("mod_eportfolio_categorytypeList", 'eportfolio');
    }
    echo $objHeading->show();
    $categoryList = $this->objDbCategorytypeList->getByItem();
    echo "<br/>";
    // Create a table object
    $table = &$this->newObject("htmltable", "htmlelements");
    $table->border = 0;
    $table->cellspacing = '3';
    $table->width = "50%";
    // Add the table heading.
    $table->startRow();
    $table->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_category", 'eportfolio') . "</b>");
    $table->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_categoryType", 'eportfolio') . "</b>");
    $table->endRow();
    // Step through the list of addresses.
    $class = NULL;
    if (!empty($categoryList)) {
        foreach($categoryList as $item) {
            // Display each field for activities
            $table->startRow();
            $category = $this->objDbCategoryList->listSingle($item['categoryid']);
            if (!empty($category)) {
                $table->addCell($category[0]['category'], "", NULL, NULL, $class, '');
            }
            $table->addCell($item['type'], "", NULL, NULL, $class, '');
            // Show the edit link
            $iconEdit = $this->getObject('geticon', 'htmlelements');
            $iconEdit->setIcon('edit');
            $iconEdit->alt = $objLanguage->languageText("word_edit");
            $iconEdit->align = false;
            $objLink = &$this->getObject("link", "htmlelements");
            $objLink->link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'editcategorytype',
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
                'action' => 'deletecategorytype',
                'id' => $item["id"]
            )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
            //echo $objConfirm->show();
            $table->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
            $table->endRow();
        }
        unset($item);
    } else {
        $table->startRow();
        $table->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
        $table->endRow();
    }
    echo $table->show();
    $addlink = new link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_categorytype'
    )));
    $addlink->link = $objLanguage->languageText("mod_eportfolio_addCategorytype", 'eportfolio');
    $mainlink = new link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'main'
    )));
    $mainlink->link = 'ePortfolio home';
    //Check for categories
    if (!empty($mycategoryList)) {
        echo '<br clear="left" />' . $addlink->show() . ' / ' . $mainlink->show();
    } else {
        echo '<br clear="left" />' . $mainlink->show();
    }
}
//End View categorytype

?>
