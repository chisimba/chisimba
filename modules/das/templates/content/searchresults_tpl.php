<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
header("Content-Type: text/html;charset=utf-8");
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$this->objFeatureBox = $this->getObject('featurebox', 'navigation');
$objWashout = $this->getObject('washout', 'utilities');
$this->objImOps = $this->getObject('imops', 'im');

$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_das_searchresults', 'das');
$header->type = 1;

$middleColumn .= $header->show();

foreach($data as $msg)
{
    // whip out a content featurebox and plak the messages in
    $from = explode('/', $msg['msgfrom']);
    $sentat = $this->objLanguage->languageText('mod_im_sentat', 'im');
    $fromuser = $this->objLanguage->languageText('mod_im_sentfrom', 'im');
    $middleColumn .= $this->objFeatureBox->showContent($fromuser.": ".$from[0].", ".$sentat.": ".$msg['datesent'], $objWashout->parseText(htmlentities($msg['msgbody'])));
}

if (!$this->objUser->isLoggedIn()) {
    $leftColumn.= $this->objImOps->loginBox(TRUE);
}
else {
    $leftColumn .= $this->leftMenu->show();
}

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
