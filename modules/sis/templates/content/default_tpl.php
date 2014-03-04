<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$objSF = $this->getObject('sisforms');
$objheader = $this->getObject('htmlheading', 'htmlelements');
// Set columns to 3
$cssLayout->setNumColumns(3);
$leftMenu = NULL;
$leftCol = NULL;
$middleColumn = NULL;
$rightSideColumn = $objSF->parentMenu(TRUE);

$leftCol .= $objSideBar->show();
$tomsg = $this->getObject('timeoutmessage', 'htmlelements');
$tomsg->message = $message;
$middleColumn .=  $tomsg->show()."<br />";

$middleColumn .= $objSF->countKids($children);
$details = $objSF->listKids($children);
if (isset($details) && !empty($details)) {
    foreach ($details as $kids) {
        $name = $kids[0];
        // make into a link
        $nlink = new href ( $this->uri ( array ('action' => 'viewstudent', 'recid' => $kids[1]) ), $name );
        $middleColumn .= $nlink->show()."<br />";
    }
}
else {
    $objNKHead = $this->newObject('htmlheading', 'htmlelements');
    $objNKHead->str = "No students found!";
    $objNKHead->type = 3;
    $middleColumn .= $objNKHead->show();
}


$objheader->str = $this->objLanguage->languageText("mod_sis_filesheader", "sis");
$objheader->type = 2;
$middleColumn .= $objheader->show();
$middleColumn .= $objSF->listFiles();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>