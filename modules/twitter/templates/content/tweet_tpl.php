<?php
//Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);
$objWidjet = $this->getObject("tweetbox","twitter");
//$objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
//$objUserParams->readConfig();
//$userName = $objUserParams->getValue("twittername");
//$password = $objUserParams->getValue("twitterpassword");



//Right panel
$objBlock = $this->getObject("blocks", "blocks");
$rightBit =  $objBlock->showBlock("followers", "twitter");
$cssLayout->setRightColumnContent($rightBit);


//Left panel
$statusUpdate = "<img src =\"" .
  $this->getResourceUri("images/twitter.png", "twitter")
  . "\" alt=\"Twitter\" style=\"margin-bottom: 3px; \"/><br />"
  . $objBlock->showBlock("tweetbox", "twitter")
  . $objBlock->showBlock("lasttweet", "twitter")
  . $objBlock->showBlock("followed", "twitter");
$cssLayout->setLeftColumnContent($statusUpdate);






//Add timeline
$this->loadClass('href', 'htmlelements');
$public = new href($this->uri(array('module'=>'twitter','timeline'=>'public')),$this->objLanguage->languageText('mod_twitter_timeline_public','twitter'),NULL);
$friends = new href($this->uri(array('module'=>'twitter','timeline'=>'friends')),$this->objLanguage->languageText('mod_twitter_timeline_friends','twitter'),NULL);
$user = new href($this->uri(array('module'=>'twitter','timeline'=>'user')),$this->objLanguage->languageText('mod_twitter_timeline_user','twitter'),NULL);
$timelineOutput = $this->objTwitterRemote->showTimeline(false, $timeline);
$middleBit = "<table><tr><td>[ " . $public->show() . " ] [ " . $friends->show() . " ] [ " . $user->show() . " ]</td></tr><tr><td>"
  . "<h3>" . $this->objLanguage->languageText("mod_twitter_timeline_$timeline", "twitter")
  . "</h3>" . $timelineOutput
  . "</td></tr></table>";
$cssLayout->setMiddleColumnContent($middleBit);







//Render it out
echo $cssLayout->show();
?>
