<?php
//Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);
$objWidjet = $this->getObject("tweetbox","twitter");




//Right panel
$objBlock = $this->getObject("blocks", "blocks");
$rightBit =  $objBlock->showBlock("followers", "twitter");
//$rightBit="";
$cssLayout->setRightColumnContent($rightBit);


//Left panel
$leftbit = "<img src =\"" .
  $this->getResourceUri("images/twitter.png", "twitter")
  . "\" alt=\"Twitter\" style=\"margin-bottom: 3px; margin-right: 9px;\"/><br />"
  . $objBlock->showBlock("tweetbox", "twitter"); /*
  . $objBlock->showBlock("lasttweet", "twitter")
  . $objBlock->showBlock("followed", "twitter");*/
  
//Show tweets to you
$leftbit .= "<h3>"
  . $this->objLanguage->languageText("mod_twitter_tweetstoyou", "twitter")
  . "</h3>";
$leftbit .= "<div id='touser' class='query'></div>";

$cssLayout->setLeftColumnContent($leftbit);






//Add your tweets

$middleBit = "";
if (isset($searchForm)) {
    $middleBit .= $searchForm;
}

if (isset($jqDiv)) {
    $middleBit .= $jqDiv;
}

$middleBit .= "<h3>"
 . $this->objLanguage->languageText("mod_twitter_tweetsfromyou", "twitter")
 . "<div class='tweet'></div>";
$cssLayout->setMiddleColumnContent($middleBit);


//Render it out
echo $cssLayout->show();
?>