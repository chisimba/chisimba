<?php
//Sending display to 1 column layout
ob_start();

$this->loadClass('htmlheading','htmlelements');
$this->loadClass('link', 'htmlelements');


$header = new htmlheading();
$header->type=1;
$header->str=$this->objLanguage->languageText('mod_discussion_accessdeniedworkgroup', 'discussion');
echo $header->show();

echo '<p>';
echo $this->objLanguage->languageText('mod_discussion_accessdeniedworkgroupmessage', 'discussion');
echo '</p>';
echo '<p>';
$prevPage = new link ('javascript: history.go(-1)');
$prevPage->link = $this->objLanguage->languageText('mod_discussion_backtoprevpage', 'discussion');

echo $prevPage->show();

echo ' / ';

$backtoDiscussionLink = new link ($this->uri(NULL));
$backtoDiscussionLink->link = $this->objLanguage->languageText('mod_discussion_backtodiscussionsincontent', 'discussion').' '.$contextTitle;

echo $backtoDiscussionLink->show();

echo '</p>';

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);
?>
