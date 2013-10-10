<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
// Set columns to 3
$cssLayout->setNumColumns(3);

//$rightSideColumn = NULL; //$this->objLanguage->languageText('mod_blog_instructions', 'blog');
$cssLayout->setLeftColumnContent($left); //$leftMenu->show());
$cssLayout->setRightColumnContent($right);
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();