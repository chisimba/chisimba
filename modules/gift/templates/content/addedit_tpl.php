<?php
$action = $this->getParam('action');
if($action == 'edit') {
    $id  = $this->getParam('id');
    $data = $this->objDbGift->getRow('id',$id);
    $addEditForm = $this->objGift->displayForm($data,$action);
    $addEditForm = '<h1>'.$this->objLanguage->languageText('mod_addedit_editHeading','gift')." ".$this->objUser->fullName().'</h1>'.$addEditForm;
}
else {
    $addEditForm = $this->objGift->displayForm(array(),$action);
    $addEditForm = '<h1>'.$this->objLanguage->languageText('mod_addedit_addHeading','gift')." ".$this->objUser->fullName().'</h1>'.$addEditForm;
}

$toSelect = $this->objLanguage->languageText('mod_home_addLink','gift');
$form = $this->objHome->getLeftContent($toSelect, $this->getParam('action'), $this->getParam('archived'));

// Create an instance of the css layout class
$cssLayout = &$this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$leftSideColumn = $form;
$cssLayout->setLeftColumnContent($leftSideColumn);
$rightSideColumn='<div style="padding:10px;">';

//Add the table to the centered layer
$rightSideColumn .= $addEditForm;
$rightSideColumn .= '</div>';
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();
?>
