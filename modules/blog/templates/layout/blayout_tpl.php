<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
// Set columns to 3
$cssLayout->setNumColumns(2);
$leftMenu = "RSS FEEDS"; //&$this->newObject('usermenu', 'toolbar');
$rightSideColumn = NULL; //$this->objLanguage->languageText('mod_blog_instructions', 'blog');
$cssLayout->setLeftColumnContent($leftMenu); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>