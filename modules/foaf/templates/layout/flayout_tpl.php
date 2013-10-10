<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(3);
$leftMenu = &$this->newObject('usermenu', 'toolbar');
$rightSideColumn = $this->objLanguage->languageText('mod_foaf_instructions', 'foaf');
$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>