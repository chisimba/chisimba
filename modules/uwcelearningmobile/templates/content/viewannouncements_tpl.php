<?php

//View a single announcement
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

//Heading
$objHead = new htmlheading();
$objHead->str = '&nbsp;' . $announcement['title'];
$objHead->type = 4;
echo $objHead->show();
//All my courses announcement
$objFields = new fieldset();
$objFields->setLegend('');
$str = '<strong>By:</strong> ' . $this->objUser->fullName($announcement['createdby']);
$str .= '<br/><strong>' . $this->objLanguage->code2Txt('word_date', 'system') . ': </strong>' . $this->objDate->formatDate($announcement['createdon']);

$str .= '<br />' . $announcement['message'];
$objFields->addContent('<p>' . $str . '</p>');
echo $objFields->show();

$backLink = new link($this->URI(array('action' => 'announcements')));
$backLink->link = $this->objLanguage->code2Txt('mod_uwcelearningmobile_wordbacktoannouncement', 'uwcelearningmobile');
echo $this->homeAndBackLink . ' - ' . $backLink->show();
?>
