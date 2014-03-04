<?php
$baseFolder = $this->objSysConfig->getValue('FILES_DIR', 'podcaster');
$nav = $this->objUtils->getTree($baseFolder, $selected);
$managenav = $this->objUtils->getManageTree($baseFolder, $selected);

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

// Create an Instance of the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');

$header = new htmlheading();
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_podcaster_podcast', 'podcaster', 'podcast');

$leftColumn = $header->show();
$leftColumn .= '<div class="filemanagertree">' . $managenav. $nav . '</div>';

//New Search
$rightColumn = "";
$cssLayout->numColumns = 2;
$cssLayout->setLeftColumnContent($leftColumn);
$cssLayout->setMiddleColumnContent($rightColumn.$this->getContent());

//$cssLayout->setRightColumnContent($rightColumn);
// Display the Layout
echo $cssLayout->show();
?>