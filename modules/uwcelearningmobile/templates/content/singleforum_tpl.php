<?php

//Viewing a list of the forum topics
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('link', 'htmlelements');
$objTableClass = $this->newObject('htmltable', 'htmlelements');
$objfieldset = $this->newObject('fieldset', 'htmlelements');
$objTableClass->startHeaderRow();

$objTableClass->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordtopic', 'uwcelearningmobile') . '</strong>', '40%');
$objTableClass->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordreplies', 'uwcelearningmobile') . '</strong>', '30%');
$objTableClass->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordviews', 'uwcelearningmobile') . '</strong>', '30%');
$objTableClass->endHeaderRow();

echo '<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordforum', 'uwcelearningmobile') . ': </b>' . $forum['forum_name'] . '<br>';
if (!empty($allTopics)) {

    foreach ($allTopics as $topic) {
        $objTableClass->startRow();
        $link = new link($this->URI(array('action' => 'topic', 'id' => $topic['topic_id'])));
        $link->link = $topic['post_title'];
        $objTableClass->addCell($link->show(), '', 'center', 'left', $class);
        $objTableClass->addCell($topic['replies'], '', 'center', 'left', $class);
        $objTableClass->addCell($topic['views'], '', 'center', 'left', $class);
    }
} else {
    $norecords = $this->objLanguage->languageText('mod_uwcelearningmobile_wordnoforum', 'uwcelearningmobile');
    $objTableClass->addCell($norecords, NULL, NULL, 'center', 'noRecordsMessage', 'colspan="7"');
}

$objfieldset->setLegend('');
$objfieldset->addContent($objTableClass->show());
echo $objfieldset->show() . '<br/>';
//echo $objTableClass->show().'</br>';

$backLink = new link($this->URI(array('action' => 'forum')));
$backLink->link = 'Back to Forum';
echo $this->homeAndBackLink . ' - ' . $backLink->show();
?>
