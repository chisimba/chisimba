<?php
$objFile = $this->getObject('dbfile', 'filemanager');
$objHead = $this->newObject('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

if ($this->isValid('addpage')) {
    //A link to adding a page
    $addPageLink = new link($this->uri(array('action' => 'addpage', 'context' => $this->contextCode, 'chapter' =>  $chapterId)));
    $addPageLink->link = $this->objLanguage->languageText('mod_contextcontent_addcontextpages', 'contextcontent');
    $addPage = $addPageLink->show();
} else {
    $addPage = '';
}

$nextPage = "";
$chapterList = "";
$editChapter = "";

if ($firstPage != FALSE) {
    //Create Next Page Link
    $link = new link($this->uri(array('action' => 'viewpage', 'id' => $firstPage, 'prevchapterid' => $chapterId), 'contextcontent'));
    $link->link = $this->objLanguage->languageText('mod_contextcontent_nextpage', 'learningcontent', 'Next Page') . ': ' . htmlentities($page['menutitle']) . ' &#187;';
    $nextPage = $link->show();
}

//Create Return to Chapter List Link
$link = new link($this->uri(array('action' => 'showcontextchapters', 'chapterId' => $chapter['id'], 'prevchapterid' => $chapterId), 'contextcontent'));
$link->link = '&#171; ' . $this->objLanguage->languageText('mod_contextcontent_returntochapterlist', 'learningcontent', 'Return to Chapter List');
$chapterList = $link->show();

if ($this->isValid('addpage')) {
    //Create Edit Chapter Link
    $link = new link($this->uri(array('action' => 'editchapter', 'id' => $chapterId, 'prevaction' => 'viewchapter'), 'contextcontent'));
    $link->link = $this->objLanguage->languageText('mod_contextcontent_editchapter', 'learningcontent', 'Edit Chapter');
    $editChapter = $link->show();
}

$table = $this->newObject('htmltable', 'htmlelements');
//$table->border='1';
$table->startRow();
$table->cssClass = "pagenavigation";
$table->addCell($chapterList, '25%', 'top');
$table->addCell($editChapter, '25%', 'top');
$table->addCell($addPage, '25%', 'top');
$table->addCell($nextPage, '25%', 'top', 'right');
$table->endRow();

$topTable = $this->newObject('htmltable', 'htmlelements');
//$topTable->border='1';
$topTable->startRow();
$topTable->cssClass = "toppagenavigation";
$topTable->addCell($chapterList, '25%', 'top');
$topTable->addCell($editChapter, '25%', 'top');
$topTable->addCell($addPage, '25%', 'top');
$topTable->addCell($nextPage, '25%', 'top', 'right');
$topTable->endRow();

$objWashout = $this->getObject('washout', 'utilities');

$content = "";

$introheader = new htmlheading();
$introheader->type = 3;
$introheader->str = $chapter['chaptertitle'];

$content.= $introheader->show() . $objWashout->parseText($chapter['introduction']);

if ($this->isValid('addpage')) {
    $ret = '<div id="tablenav">' . $topTable->show() . $content . '<hr />' . $table->show() . '</div>';
} else {
    $ret = '<div id="tablenav">' . $topTable->show() . $content . '<hr />' . $table->show() . '</div>';
}
echo "<div id='context_content'>-----------------------------------------" . $ret . "</div>"
?>
