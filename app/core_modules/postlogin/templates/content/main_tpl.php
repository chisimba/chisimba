<?php
//Get yes or no for admin
if ($objUser->isAdmin()) {
    $adminMember = $objLanguage->languageText("word_yes",'postlogin');
} else {
    $adminMember = $objLanguage->languageText("word_no",'postlogin');
} 


// START of the Left Column

// Create an instance of the postlogin menu on the side
$userMenu  = &$this->newObject('postloginmenu','toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
// Add Post login menu to left column
$leftSideColumn ='';;

//add the context box as a feature box
$objFeature = $this->newObject('featurebox', 'navigation');
$leftSideColumn = $userMenu->show();//$objFeature->show(null,$userMenu->show());

//Get the story category from querystring
$storyCategory = $this->getParam('storycategory', 'postlogin');

/******************* BEGIN LEFTSIDE BLOCKS ******************************/
//Put a block to test the blocks module
$objBlocks = & $this->newObject('blocks', 'blocks');
//Add loginhistory block
$leftSideColumn .= $objBlocks->showBlock('loginstats', 'reports');
//Add guestbook block
$leftSideColumn .= $objBlocks->showBlock('guestinput', 'guestbook');
//Add latest search block
$leftSideColumn .= $objBlocks->showBlock('lastsearch', 'websearch');
//Add the whatsnew block
$leftSideColumn .= $objBlocks->showBlock('whatsnew', 'whatsnew');
//Add random quote block
$leftSideColumn .= $objBlocks->showBlock('rquote', 'quotes');
$leftSideColumn .= $objBlocks->showBlock('today_weather','weather');
/******************* END  LEFTSIDE BLOCKS ******************************/
$this->setVar('leftSideColumn', $leftSideColumn);

/******************* BEGIN RIGHTSIDE BLOCKS ******************************/
// Right Column initialize
$rightSideColumn = "";
//Add the getting help block
$rightSideColumn .= $objBlocks->showBlock('gettinghelp', 'help');
//Add the latest in blog as a a block
$rightSideColumn .= $objBlocks->showBlock('latest', 'blog');
//Add the latest in blog as a a block
$rightSideColumn .= $objBlocks->showBlock('latestpodcast', 'podcast');
//Add a block for chat
$rightSideColumn .= $objBlocks->showBlock('chat', 'chat');
//Add a block for the google api search
$rightSideColumn .= $objBlocks->showBlock('google', 'websearch');
//Put the google scholar google search
$rightSideColumn .= $objBlocks->showBlock('scholarg', 'websearch');
//Put a wikipedia search
$rightSideColumn .= $objBlocks->showBlock('wikipedia', 'websearch');
//Put a dictionary lookup
$rightSideColumn .= $objBlocks->showBlock('dictionary', 'dictionary');
/******************* END  RIGHTSIDE BLOCKS ******************************/

//$this->setVar('rightSideColumn', $rightSideColumn);

// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 3
$cssLayout->setNumColumns(3);

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

// Add Right Column
$cssLayout->setRightColumnContent($rightSideColumn);



// Create the middle template area
if($this->objModule->checkIfRegistered('stories','stories')){
	$this->objStories = $this->getObject('sitestories','stories');
    $cnt = $this->objStories->fetchCategory($storyCategory)
      . $this->objStories->putCategoryChooser();
    $cssLayout->setMiddleColumnContent($cnt);
    //echo $cnt;
}

// Pass the string to the footer area
if($this->objModule->checkIfRegistered('stories','stories')){
    $this->setVar('footerStr', $this->objStories->fetchCategory("footer"));
}

echo $cssLayout->show(); 


?>
