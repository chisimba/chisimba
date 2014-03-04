<?php
$this->loadclass('link', 'htmlelements');
$objBlocks = $this->getObject('blocks', 'blocks');
$cssLayout = $this->getObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);
$homeLink = new link($this->uri(array(
    'action' => 'liftclubhome'
) , 'liftclub'));
$homeLink->link = $this->objLanguage->languageText("word_home", "system", "Home");
$homeLink->title = $this->objLanguage->languageText("word_home", "system", "Home");
$exitLink = new link($this->uri(array(
    'action' => 'liftclubsignout'
) , 'liftclub'));
$exitLink->link = $this->objLanguage->languageText("mod_liftclub_signout", "liftclub", "Sign Out");
$exitLink->title = $this->objLanguage->languageText("mod_liftclub_signout", "liftclub", "Sign Out");
$registerLink = new link($this->uri(array(
    'action' => 'showregister'
) , 'liftclub'));
$registerLink->link = $this->objLanguage->languageText("mod_liftclub_register", "liftclub", "Register");
$registerLink->title = $this->objLanguage->languageText("mod_liftclub_register", "liftclub", "Register");
$modifyLink = new link($this->uri(array(
    'action' => 'startregister'
) , 'liftclub'));
$modifyLink->link = $this->objLanguage->languageText("mod_liftclub_addmodify", "liftclub", "Add/Modify Lift");
$modifyLink->title = $this->objLanguage->languageText("mod_liftclub_addmodify", "liftclub", "Add/Modify Lift");
$userDetailsLink = new link($this->uri(array(
    'action' => 'modifyuserdetails'
) , 'liftclub'));
$userDetailsLink->link = $this->objLanguage->languageText("mod_liftclub_modifyregister", "liftclub", "Modify Registration");
$userDetailsLink->title = $this->objLanguage->languageText("mod_liftclub_modifyregister", "liftclub", "Modify Registration");
$findLink = new link($this->uri(array(
    'action' => 'findlift'
) , 'liftclub'));
$findLink->link = $this->objLanguage->languageText("mod_liftclub_viewneeded", "liftclub", "View Needed Lifts");
$findLink->title = $this->objLanguage->languageText("mod_liftclub_viewneeded", "liftclub", "View Needed Lifts");
$offerLink = new link($this->uri(array(
    'action' => 'offeredlifts'
) , 'liftclub'));
$offerLink->link = $this->objLanguage->languageText("mod_liftclub_viewavailable", "liftclub", "View Available Lifts");
$offerLink->title = $this->objLanguage->languageText("mod_liftclub_viewavailable", "liftclub", "View Available Lifts");
$favLink = new link($this->uri(array(
    'action' => 'myfavourites'
) , 'liftclub'));
$favLink->link = $this->objLanguage->languageText("mod_liftclub_myfavourites", "liftclub", "My Favourites");
$favLink->title = $this->objLanguage->languageText("mod_liftclub_myfavourites", "liftclub", "My Favourites");
$actyLink = new link($this->uri(array(
    'action' => 'viewactivities'
) , 'liftclub'));
$actyLink->link = $this->objLanguage->languageText("mod_liftclub_liftclubactivities", "liftclub", "LiftClub Activities");
$actyLink->title = $this->objLanguage->languageText("mod_liftclub_liftclubactivities", "liftclub", "LiftClub Activities");
$msgLink = new link($this->uri(array(
    'action' => 'messages'
) , 'liftclub'));
$msgLink->link = $this->objLanguage->languageText("mod_liftclub_receivedmessages", "liftclub", "Inbox");
$msgLink->title = $this->objLanguage->languageText("mod_liftclub_receivedmessages", "liftclub", "Inbox");
$msgSentLink = new link($this->uri(array(
    'action' => 'sentmessages'
) , 'liftclub'));
$msgSentLink->link = $this->objLanguage->languageText("mod_liftclub_sentmessages", "liftclub", "Sent");
$msgSentLink->title = $this->objLanguage->languageText("mod_liftclub_sentmessages", "liftclub", "Sent");
$msgTrashLink = new link($this->uri(array(
    'action' => 'trashedmessages'
) , 'liftclub'));
$msgTrashLink->link = $this->objLanguage->languageText("mod_liftclub_trashedmessages", "liftclub", "Trash");
$msgTrashLink->title = $this->objLanguage->languageText("mod_liftclub_trashedmessages", "liftclub", "Trash");
$siteAdminLink = new link($this->uri(array(
    'action' => 'default'
) , 'toolbar'));
$siteAdminLink->link = $this->objLanguage->languageText("mod_toolbar_siteadmin", "toolbar", "Site Administration");
$siteAdminLink->title = $this->objLanguage->languageText("mod_toolbar_siteadmin", "toolbar", "Site Administration");
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$pageLink = "<div id='liftclubmenu'><ul>";
$mailFeatBox = "";
if ($this->objUser->userId() !== null) {
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $homeLink->show() . "</li>";
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $modifyLink->show() . "</li>";
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $favLink->show() . "</li>";
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $userDetailsLink->show() . "</li>";
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $offerLink->show() . "</li>";
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $findLink->show() . "</li>";
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $actyLink->show() . "</li>";
    if ($this->objUser->isAdmin() !== null) {
        $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $siteAdminLink->show() . "</li>";
    }
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $exitLink->show() . "</li>";
    $mailLink = "<ul>";
    $mailLink.= "<li>&nbsp;&nbsp;&nbsp;" . $msgLink->show() . "</li>";
    $mailLink.= "<li>&nbsp;&nbsp;&nbsp;" . $msgSentLink->show() . "</li>";
    $mailLink.= "<li>&nbsp;&nbsp;&nbsp;" . $msgTrashLink->show() . "</li>";
    $mailLink.= "</ul>";
    $mailfieldset = $this->newObject('fieldset', 'htmlelements');
    $mailfieldset->contents = $mailLink;
    $mailFeatBox = $objFeatureBox->show($this->objLanguage->languageText("mod_liftclub_mailbox", "liftclub", "Mail Box") , $mailfieldset->show() . "<br />", "mailbox", $blockType = NULL, $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE, $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox', $cssId = '');
    $mailFeatBox = $mailFeatBox . "<br /><br /><br /><br />";
} else {
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $homeLink->show() . "</li>";
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $registerLink->show() . "</li>";
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $offerLink->show() . "</li>";
    $pageLink.= "<li>&nbsp;&nbsp;&nbsp;" . $findLink->show() . "</li>";
}
$pageLink.= "</ul></div>";
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->contents = $pageLink;
$cssLayout->setLeftColumnContent($objFeatureBox->show($this->objLanguage->languageText("mod_liftclub_liftclubname", "liftclub", "Lift Club") , $fieldset->show() , "clubox", $blockType = NULL, $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE, $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox', $cssId = '') . $mailFeatBox . $objBlocks->showBlock('login', 'security'));
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>
