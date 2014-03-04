<?php

//View Topic template
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('radio', 'htmlelements');

$objFields = new fieldset();
$objTable = new htmltable();

echo '&nbsp;<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordforum', 'uwcelearningmobile') . ' : </b>' . $forum['forum_name'] . '<br>';
echo '&nbsp;<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordtopic', 'uwcelearningmobile') . ' : </b>' . $topic['post_title'] . '<br>';

$objFields->setLegend('');
if (!empty($posts)) {
    foreach ($posts as $post) {
        //prepare a reply to post link
        $alt = trim(strip_tags($post['post_text'], '<a>'), "\xA0");

        if (strlen($alt) > 200 && $readmore != $post['id']) {
            $rmlink = new link($this->URI(array('action' => 'topic', 'id' => $topic['topic_id'], 'readmore' => $post['id'])));
            $rmlink->link = 'Read more';
            $alt = substr($alt, 0, 200) . '...' . $rmlink->show();
        }

        //var_dump($alt);
        $link = new link($this->URI(array('action' => 'postreply',
                            'id' => $post['id'])));
        $link->link = $this->objLanguage->languageText('mod_forum_postreply', 'forum');

        //prepare topic heading
        $str = '<div class="newForumContainer">';
        $str .= '<div class="newForumTopic">';
        $str .= '<strong>Subject :</strong>' . $post['post_title'] . '<br/>';
        $str .= '<strong>By :</strong>' . $post['firstname'] . ' ' . $post['surname'] . '<br/>';
        $str .= '</div>';

        //prepare topic content
        $str .= '<div class="newForumContent">';
        $str .= $alt . '<br/>';
        if ($topic['topicstatus'] != 'CLOSE') {
            //$str .= '<br/>'.$link->show();
        }
        $str .= '</div>';
        $str .= '</div>';

        $objFields->addContent($str);
    }

    if (isset($err)) {
        $str = '<p><div class="warning">' . $err . '</div></p>';
        $objFields->addContent($str);
    }

    $replyform = new form('postreply', $this->URI(array('action' => 'topic',
                        'id' => $topicid,
                        'mode' => 'add')));

    /////////////////////////////////////////////////////////////////
    //set up the reply form
    //first setup hidden inputs

    $hiddenTypeInput = new textinput('discussionType');
    $hiddenTypeInput->fldType = 'hidden';
    $hiddenTypeInput->value = $rootpost['type_id'];
    $replyform->addToForm($hiddenTypeInput->show());

    $hiddenTangentInput = new textinput('parent');
    $hiddenTangentInput->fldType = 'hidden';
    $hiddenTangentInput->value = $rootpost['post_id'];
    $replyform->addToForm($hiddenTangentInput->show());

    $topicHiddenInput = new textinput('topic');
    $topicHiddenInput->fldType = 'hidden';
    $topicHiddenInput->value = $rootpost['topic_id'];
    $replyform->addToForm($topicHiddenInput->show());

    $hiddenForumInput = new textinput('forum');
    $hiddenForumInput->fldType = 'hidden';
    $hiddenForumInput->value = $forum['id'];
    $replyform->addToForm($hiddenForumInput->show());

    $hiddenTemporaryId = new textinput('temporaryId');
    $hiddenTemporaryId->fldType = 'hidden';
    $hiddenTemporaryId->value = $temporaryId;
    $replyform->addToForm($hiddenTemporaryId->show());


    //then setup the rest on the inputs
    $txtsubject = new textinput('title');
    if (isset($subject)) {
        $txtsubject->value = $subject;
    }

    $txtmessage = new textarea('message', $message, '', '');
    $txtmessage->setRows(8);
    $objTable->startRow();
    $objTable->addCell($this->objLanguage->languageText('word_subject', 'system') . ':', '', '', '', '', '');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell($txtsubject->show(), '', '', '', '', '');
    $objTable->endRow();

    $rad = new radio('replytype');
    $rad->addOption('reply', $this->objLanguage->languageText('mod_forum_postasreply', 'forum'));
    $rad->addOption('tangent', $this->objLanguage->languageText('mod_forum_postastangent', 'forum'));
    $rad->setSelected('reply');
    $rad->setBreakSpace('<br/>');

    $objTable->startRow();
    $objTable->addCell($this->objLanguage->languageText('word_message', 'system') . ':', '', '', '', '', '');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell($txtmessage->show(), '', '', '', '', '');
    $objTable->endRow();

    //--- Create a submit button
    $objButton = '<input type="submit" value="' . $this->objLanguage->languageText('word_submit', 'system') . '" />';

    $objTable->startRow();
    $objTable->addCell('<p>' . $objButton . '</p>', '', '', '', '', '');
    $objTable->endRow();

    $replyform->addToForm($objTable->show());

    if ($topic['topicstatus'] != 'CLOSE') {
        $objFields->addContent('<br/><strong>' . $this->objLanguage->languageText('mod_forum_replytotopic', 'forum') . '</strong><br/><br/>');
        $objFields->addContent($replyform->show());
    }
} else {
    $norecords = 'No Posts for this Topic';
    $objTable->addCell($norecords, NULL, NULL, '', '', 'colspan="7"');
    $objFields->addContent($objTable->show());
}
echo $objFields->show() . '<br>';

$backLink = new link($this->URI(array('action' => 'forum')));
$backLink->link = 'Back to Forum';
echo $this->homeAndBackLink . ' - ' . $backLink->show();
?>
