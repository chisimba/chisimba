<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$this->loadClass('htmlheading', 'htmlelements');
$addicon = $this->newObject('geticon', 'htmlelements');

$add = $addicon->getAddIcon($this->uri(array('action' => 'editgrp', 'id' => $groupinfo[0]['group_id'], 'adduser' => 'true')));

// Set columns to 2
$cssLayout->setNumColumns(2);
$leftMenu = NULL;
$rightSideColumn = NULL;
$leftCol = NULL;
$middleColumn = NULL;
$leftCol .= $objSideBar->show();

$grpName = $groupinfo[0]['group_define_name'];
$grpId = $groupinfo[0]['group_id'];
// set up a heading for the group name
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText("mod_groupadmin_lblName", "groupadmin").": ".$grpName." ".$add;

$middleColumn .= $header->show();
// check for an empty group
if(empty($usersin)) {
    $headerempty = new htmlheading();
    $headerempty->type = 3;
    $headerempty->str = "<em>".$this->objLanguage->languageText("mod_groupadmin_nousers", "groupadmin")."</em>";
    $middleColumn .= $headerempty->show();
}
else {
    $middleColumn .= $usersin;
}

// check if the user would like to add users to this group
if($adduser === TRUE) {
    // start a form with a selectbox to add the users
    $middleColumn .= $this->objOps->addUserForm($grpId);
}

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);

echo $cssLayout->show();