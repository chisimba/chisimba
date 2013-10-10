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
$header->str = $this->objLanguage->languageText('mod_tweetlic_welcome', 'tweetlic');
$header->type = 1;

$middleColumn .= $header->show();
$middleColumn .= $this->objLanguage->languageText('mod_tweetlic_explainer', 'tweetlic')."<br />";
$cclicurl = new href("http://creativecommons.org/licenses/", $this->objLanguage->languageText('mod_tweetlic_explanationurl', 'tweetlic'), 'target="_blank"');
$middleColumn .= $this->objLanguage->languageText('mod_tweetlic_viewlic', 'tweetlic')." ".$cclicurl->show()."<br />";

$middleColumn .= $this->objOps->licForm();

$leftColumn .= $this->objOps->userSearchBox();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
