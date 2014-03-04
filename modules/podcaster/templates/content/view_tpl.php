<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$objBookMarks = $this->getObject('socialbookmarking', 'utilities');
$objBookMarks->options = array('stumbleUpon', 'delicious', 'newsvine', 'reddit', 'muti', 'facebook', 'addThis');
$objBookMarks->includeTextLink = FALSE;

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('edit');

$heading = new htmlheading();

if ($file['filedata']['title'] == '') {
    $heading->str = $this->objLanguage->languageText("mod_podcaster_viewfile", "podcaster") . ' - ' . $file['filedata']['title'];
} else {
    $heading->str = $file['filedata']['title'];
}


$showDeleteLink = FALSE;

if ($file['filedata']['creatorid'] == $objUser->userId()) {
    $editLink = new link($this->uri(array('action' => 'describepodcast', 'fileid' => $file["filedata"]['fileid'])));
    $editLink->link = $objIcon->show();
    $editLink = $editLink->show();

    $heading->str .= ' ' . $editLink;
    $showDeleteLink = TRUE;
}

$heading->type = 1;
$altText = $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
//Add RSS Link
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('rss');
$objIcon->alt = $altText;


$rssLink = new link($this->uri(array('action' => 'getlatestfeeds')));
$rssLink->link = $objIcon->show();
$rssLink = ' ' . $rssLink->show();

//Append RSS icon to the heading
$heading->str .= ' ' . $rssLink;
echo $heading->show();

// Show the flash file using the viewer class
$objView = $this->getObject("viewer", "podcaster");

$rightCell = "";

if ($file['filedata']['description'] != '') {
    $rightCell .= '<p><strong>'
            . $this->objLanguage->languageText("word_description")
            . ':</strong><br /> '
            . nl2br($file['filedata']['description'])
            . '</p>';
}
$rightCell .= '<p><strong>'
        . $this->objLanguage->languageText("word_tags")
        . ':</strong> ';

if (count($tags) == 0) {
    $rightCell .= '<em>'
            . $this->objLanguage->languageText("mod_podcaster_notags", "podcaster")
            . ' </em>';
} else {
    $divider = '';
    foreach ($tags as $tag) {
        $tagLink = new link($this->uri(array('action' => 'tag', 'tag' => $tag['tag'])));
        $tagLink->link = $tag['tag'];
        $rightCell .= $divider . $tagLink->show();
        $divider = ', ';
    }
}
$rightCell .= '</p>';

$fileTypes = array('mp3' => 'mp3');

$objFileIcons = $this->getObject('fileicons', 'files');

$rightCell .= '<ul>';

foreach ($fileTypes as $fileType => $fileName) {
    $ext = pathinfo($file['filename']);
    $ext = $ext['extension'];
    $fullPath = $this->objConfig->getcontentBasePath() . 'podcaster/' . $file['id'] . '/' . $file['id'] . '.' . $fileType;

    if (file_exists($fullPath)) {
        $link = new link($this->uri(array('action' => 'download', 'id' => $file['id'], 'type' => $fileType)));
        $link->link = $objFileIcons->getExtensionIcon($fileType) . ' ' . $fileName;
    }
}

$rightCell .= '</ul>';

$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();

//Get social bookmarks
$markers = $objBookMarks->show();

$leftContents = "<p>" . $file['podinfo'] . "</p>";
$table->addCell($leftContents, 550);
$table->addCell($rightCell . "<br />" . $markers);
$table->endRow();
$table->startRow();

$leftContents .= '</p>';


//Display table
echo $table->show();

$objModule = $this->getObject('modules', 'modulecatalogue');

$homeLink = new link($this->uri(NULL));
$homeLink->link = $this->objLanguage->languageText("phrase_backhome");

$bottomLinks = array();

$bottomLinks[] = $homeLink->show();

echo '<p>';
$divider = '';
foreach ($bottomLinks as $link) {
    echo $divider . $link;
    $divider = ' | ';
}

echo '</p>';
?>