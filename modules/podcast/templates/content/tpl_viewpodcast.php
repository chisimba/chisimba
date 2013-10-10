<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('windowpop', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('formatfilesize', 'files');

$objIcon = $this->newObject('geticon', 'htmlelements');

$filesize = new formatfilesize();


$objFeatureBox = $this->getObject('featurebox', 'navigation');

$heading = new htmlheading();

$content = '<p>'.htmlentities($podcast['description']).'</p>';


$context = array();
$context = $this->objPodcast->getContextCode($podcast['id']);
$courses = array();
if(!empty($context)){
   foreach ($context as $key => $value)
   {
       $courses[] = $this->objPodcast->getPodcastContext($value['contextcode']);
   }
}else{

}


$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
   if ($podcast['artist'] == '') {
       $artist = $this->objUser->fullname($podcast['creatorid']);
   } else {
       $artist = $podcast['artist'];
   }
   if (isset($id)) {  
       $table->addCell('<strong>'.$this->objLanguage->languageText('word_by', 'system').':</strong> '.$artist, '50%');
   } else {
       $authorLink = new link ($this->uri(array('action'=>'byuser', 'id'=>$podcast['creatorid'])));
       $authorLink->link = $artist;
       $table->addCell('<strong>'.$this->objLanguage->languageText('word_by', 'system').':</strong> '.$authorLink->show(), '50%');
   }
   $table->addCell('<strong>'.$this->objLanguage->languageText('word_date', 'system').':</strong> '.$this->objDateTime->formatDate($podcast['datecreated']), '50%');
$table->endRow();
$table->startRow();
   $table->addCell('<strong>'.$this->objLanguage->languageText('phrase_filesize', 'system').':</strong> '.$filesize->formatsize($podcast['filesize']), '50%');
   $playtime = $this->objDateTime->secondsToTime($podcast['playtime']);
   $playtime = ($playtime == '0:0') ? '<em>Unknown</em>' : $playtime;
   $table->addCell('<strong>'.$this->objLanguage->languageText('word_playtime', 'system').':</strong> '.$playtime, '50%');
$table->endRow();

$content .= $table->show();

$downloadLink = new link ($this->objConfig->getcontentPath().$podcast['path']);
$downloadLink->link = htmlentities($podcast['filename']);

$objFile = $this->getObject('dbfile', 'filemanager');
$objSoundPlayer = $this->newObject('buildsoundplayer', 'files');
$soundFile = str_replace('&', '&amp;', $objFile->getFilePath($podcast['fileid']));
$soundFile = str_replace(' ', '%20', $soundFile);
$objSoundPlayer->setSoundFile($soundFile);

$content .= '<br /><p>'.$objSoundPlayer->show().'</p><p><strong>'.$this->objLanguage->languageText('mod_podcast_downloadpodcast', 'podcast').':</strong> '.$downloadLink->show().'</p>';


if(!empty($courses)){
   $content .= "<strong>".$this->objLanguage->languageText('mod_podcast_listcourse','podcast')."</strong>";
   $content .="<ul>";
   foreach ($courses as $key => $value)
   {
       foreach ($value as $course)
       {
           $content .="<li>".$course['title']."</li>";
       }
   }
   $content .="</ul>";
}
    
if ($podcast['creatorid'] == $this->objUser->userId() || $this->objUser->isAdmin()) {
   $objIcon->setIcon('edit');
   
   $editLink = new link ($this->uri(array('action'=>'editpodcast', 'id'=>$podcast['id'])));
   $editLink->link = $objIcon->show();
   
   $deleteIcon = $objIcon->getDeleteIconWithConfirm($podcast['id'], array('action'=>'deletepodcast', 'id'=>$podcast['id']),
       'podcast', $this->objLanguage->languageText('mod_podcast_confirmdeletepodcast', 'podcast'));
   $icons = ' '.$editLink->show().' '.$deleteIcon;
} else {
   $icons = '';
}


echo $objFeatureBox->show($podcast['title'].' '.$icons, $content);

echo '<p>';


$HomeLink = new link($this->uri(NULL));
$HomeLink->link = $this->objLanguage->languageText('mod_podcast_podcasthome', 'podcast');

echo $HomeLink->show().' / ';

$link = new link($this->uri(array('action'=>'byuser', 'id'=>$podcast['creatorid'])));
$link->link = $this->objLanguage->languageText('mod_podcast_podcastsby', 'podcast').' '.$this->objUser->fullName($podcast['creatorid']);

echo $link->show().' / ';

$link = new link($this->uri(array('action'=>'addpodcast')));
$link->link = $this->objLanguage->languageText('mod_podcast_addpodcast', 'podcast');

echo $link->show();

echo '</p>&nbsp;';
