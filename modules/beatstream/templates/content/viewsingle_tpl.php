<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('href', 'htmlelements');
        
$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_beatstream_welcome', 'beatstream');
$header->type = 1;

$middleColumn .= '<div id="heading" class="rounded">'.$header->show().'</div>';
$middleColumn .= $str;
$middleColumn .= $this->objUI->formatUI($str);
if($this->objUser->isLoggedIn()) {
    $middleColumn .= "<ul><li>".$this->objUI->addForm()."</li></ul>";
    $leftColumn .= $this->leftMenu->show();
}
else {
    $leftColumn .= $this->objUI->loginBox(TRUE);
}
$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
