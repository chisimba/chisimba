<?php

$this->loadClass('link', 'htmlelements');

$str = $this->objLanguage->languageText('mod_forum_accessdeniedworkgroup', 'forum');
echo '<strong>' . $str . '</strong>';
echo '<p>';
echo $this->objLanguage->languageText('mod_forum_accessdeniedworkgroupmessage', 'forum');
echo '</p>';
echo '<p>';
$backLink = new link($this->URI(array('action' => 'forum')));
$backLink->link = 'Back to Forum';
echo $this->homeAndBackLink . ' - ' . $backLink->show();
echo '</p>';
?>
