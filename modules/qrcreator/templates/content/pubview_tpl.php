<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$middleColumn = NULL;
$leftColumn = NULL;

// get the sidebar object
if($this->objUser->isLoggedIn()) {
    $this->leftMenu = $this->newObject('usermenu', 'toolbar');
    $leftColumn .= $this->leftMenu->show();
}
else {
    // show login and register box
    $text = $this->objLanguage->languageText("mod_qrcreator_signintocreate", "qrcreator");
    $leftColumn .= $text."<br />";
    $leftColumn .= $this->objQrOps->showSignInBox();
}
$this->loadClass('htmlheading', 'htmlelements');

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_qrcreator_codeheader', 'qrcreator');
$header->type = 1;

$middleColumn .= $header->show();
if($imgsrc != NULL) {
    $middleColumn .= '<img src="'.$imgsrc.'" />';
}
else {
    $middleColumn .= $message;
}

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
