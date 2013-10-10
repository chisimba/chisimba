<?php

$res = $res[0];

$cssLayout = $this->newObject('csslayout', 'htmlelements');

$icon = $this->newObject('geticon', 'htmlelements');
$this->loadClass('href', 'htmlelements');
$image = $res['copyright']."_big";
$icon->setIcon($image, 'gif', 'icons/creativecommons_v3');

$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('href', 'htmlelements');
        
$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading();
$header->str = "@".$res['screen_name']." ".$this->objLanguage->languageText('mod_tweetlic_userlicenses', 'tweetlic')." ".ucwords($res['copyright'])." ".$icon->show();
$header->type = 1;

$link = new href($this->uri(array(''), 'tweetlic'), $this->objLanguage->languageText('mod_tweetlic_licenseyourown', 'tweetlic'));

$middleColumn .= $header->show();
$middleColumn .= $link->show(); 


$leftColumn .= $this->objOps->userSearchBox();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
