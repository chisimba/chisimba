<?php
//Sending display to 1 column layout
ob_start();

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$header = new htmlheading();
$header->type=1;

$languageCodes = & $this->newObject('languagecode','language');

$link = new link($this->uri(array('action'=>'forum', 'id'=>$post['forum_id'])));
$link->link = $forum['forum_name'];
$headerString = $link->show().' &gt; '.stripslashes($post['post_title']);
$headerString .= ' - <em>'.$languageCodes->getLanguage($post['language']).'</em>';

$header->str=$headerString;

echo $header->show();

if ($this->getParam('message') == 'translationsaved') {
    $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
    $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_translationsaved', 'forum'));
    $timeoutMessage->setTimeout(10000);
    echo ('<p>'.$timeoutMessage->show().'</p>');
}

echo $postDisplay;

$link = new link ($this->uri(array('action'=>'viewtopic', 'id'=>$post['topic_id'], 'post'=>$post['post_id'])));
$link->link = 'Return to Topic';

echo '<p align="center">'.$link->show().'</p>';

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);
?>