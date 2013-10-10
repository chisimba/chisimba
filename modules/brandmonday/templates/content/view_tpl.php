<?php
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 3 );

// get the sidebar object
$this->loadClass ( 'htmlheading', 'htmlelements' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
$this->objViewer = $this->getObject('viewer');

$middleColumn = NULL;
$leftColumn = NULL;
$rightColumn = NULL;

// Add in a heading
$header = new htmlHeading ( );
$header->str = $this->objLanguage->languageText ( 'mod_brandmonday_bmtweets', 'brandmonday' );
$header->type = 1;

// CapeTown link
$cptLink = $this->newObject('link', 'htmlelements');
$cptLink->href = "http://twitter.com/CapeTown";
$cptLink->link = "@CapeTown";
$cptLink = $cptLink->show();

$headertag2 = new htmlHeading ( );
$headertag2->str = $this->objLanguage->languageText ( 'mod_brandmonday_bminit', 'brandmonday' )." ".$cptLink." ".$this->objLanguage->languageText ( 'mod_brandmonday_bminit2', 'brandmonday' );
$headertag2->type = 3;

$this->objShare = $this->getObject('share', 'toolbar');
$this->objShare->setup($this->uri(''), '#BrandMonday', '#BrandMonday tweets about brands ');

//$middleColumn .= $header->show().
$middleColumn .= $headertag2->show();
$middleColumn .= $this->objShare->show();
$middleColumn .= $this->objViewer->renderCompView($resPlus, $resMinus, $resMentions);

$rightColumn .= $this->objViewer->aboutBlock();
$rightColumn .= $this->objViewer->disclaimerBlock();
$rightColumn .= $this->objViewer->tweetThisBox();
$rightColumn .= $this->objViewer->adBlocks();

$leftColumn .= $this->objViewer->chisimbaBlock();
$leftColumn .= $this->objViewer->awardsBlock();
$leftColumn .= $this->objViewer->rssBlock();
$leftColumn .= $this->objViewer->loginBlock();
// $leftColumn .= $this->objViewer->tweetBlock();

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setRightColumnContent ( $rightColumn );
echo $cssLayout->show ();
?>