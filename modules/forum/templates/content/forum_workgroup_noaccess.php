<?php
//Sending display to 1 column layout
ob_start();

$this->loadClass('htmlheading','htmlelements');
$this->loadClass('link', 'htmlelements');


$header = new htmlheading();
$header->type=1;
$header->str=$this->objLanguage->languageText('mod_forum_accessdeniedworkgroup', 'forum');
echo $header->show();

echo '<p>';
echo $this->objLanguage->languageText('mod_forum_accessdeniedworkgroupmessage', 'forum');
echo '</p>';
echo '<p>';
$prevPage = new link ('javascript: history.go(-1)');
$prevPage->link = $this->objLanguage->languageText('mod_forum_backtoprevpage', 'forum');

echo $prevPage->show();

echo ' / ';

$backtoForumLink = new link ($this->uri(NULL));
$backtoForumLink->link = $this->objLanguage->languageText('mod_forum_backtoforumsincontent', 'forum').' '.$contextTitle;

echo $backtoForumLink->show();

echo '</p>';

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);
?>
