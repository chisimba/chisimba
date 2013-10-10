<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

$this->objConfig = $this->getObject('altconfig', 'config');
$this->objLanguage = $this->getObject('language', 'language');
$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$this->objUser = $this->getObject('user', 'security');

$this->loadClass('htmlarea', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');

$qpform = new form('qpadd', $this->uri(array(
        'action' => 'sendnews',
        )));
//$qpform->addRule('postcontent', $this->objLanguage->languageText("mod_mailmannews_phrase_pcontreq", "mailmannews") , 'required');
$qptitletxt = $this->objLanguage->languageText("mod_mailmannews_posttitle", "mailmannews") . "<br />";
$qptitle = new textinput('posttitle');
// post content textarea
$qpcontenttxt = $this->objLanguage->languageText("mod_mailmannews_pcontent", "mailmannews") . "<br />";

$qpcontent = $this->newObject('htmlarea', 'htmlelements');
$qpcontent->setName('postcontent');
//$qpcontent->height = 400;
//$qpcontent->width = 420;
$qpcontent->setDefaultToolbarSet();

$qpform->addToForm($qptitletxt . $qptitle->show());
$qpform->addToForm("<br />");
$qpform->addToForm($qpcontenttxt . $qpcontent->showFCKEditor());
$qpform->addToForm("<br />");
$this->objqpCButton = &new button('postit');
$this->objqpCButton->setValue($this->objLanguage->languageText('mod_mailmannews_word_sendnewsletter', 'mailmannews'));
$this->objqpCButton->setToSubmit();
$qpform->addToForm($this->objqpCButton->show());
$qpform = $qpform->show();
$objFeatureBox = $this->getObject('featurebox', 'navigation');
$ret = $objFeatureBox->show($this->objLanguage->languageText("mod_mailmannews_postletter", "mailmannews") , $qpform);

$middleColumn = NULL;
$leftCol = NULL;
$rightCol = NULL;

if ($this->objUser->isLoggedIn()) {
	$leftMenu = $this->newObject('usermenu', 'toolbar');
	$leftCol .= $leftMenu->show();
	$middleColumn .= $ret;
}


$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol);
$cssLayout->setRightColumnContent($rightCol);
echo $cssLayout->show();

?>