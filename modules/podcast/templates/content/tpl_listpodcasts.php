<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('windowpop', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('formatfilesize', 'files');

$objIcon = $this->newObject('geticon', 'htmlelements');
$filesize = new formatfilesize();


$objFeatureBox = $this->getObject('featurebox', 'navigation');

$heading = new htmlheading();

if (isset($id)) {
    $heading->str = $this->objLanguage->languageText('mod_podcast_podcastsby', 'podcast').' '.$this->objUser->fullName($id);
} else {
    $heading->str = $this->objLanguage->languageText('mod_podcast_latespodcasts', 'podcast');    
}

$heading->type = 1;
echo $heading->show();
if (count($podcasts) == 0) {
    
    if (isset($id)) {
        echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_podcast_userhasnotaddedanypodcasts', 'podcast').'</div>';
    } else {
        echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_podcast_nopodcastsavailable', 'podcast').'</div>'; 
    }
} else {
    
    $objFile = $this->getObject('dbfile', 'filemanager');
    
    foreach ($podcasts as $podcast)
    {
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
                $authorLink = new link ($this->uri(array('action'=>'byuser', 'id'=>$this->objUser->userName($podcast['creatorid']))));
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
        
        $this->objPop=&new windowpop;
        $this->objPop->set('location',$this->uri(array('action'=>'playpodcast', 'id'=>$podcast['id']), 'podcast'));
        $this->objPop->set('linktext', $this->objLanguage->languageText('mod_podcast_listenonline', 'podcast'));
        $this->objPop->set('width','280');
        $this->objPop->set('height','120');
        //leave the rest at default values
        //$this->objPop->putJs(); // you only need to do this once per page
        
        $objSoundPlayer = $this->newObject('buildsoundplayer', 'files');
        $soundFile = str_replace('&', '&amp;', $objFile->getFilePath($podcast['fileid']));
        $soundFile = str_replace(' ', '%20', $soundFile);
        $objSoundPlayer->setSoundFile($soundFile);
    
        
        $content .= '<br /><p>'.$objSoundPlayer->show().'</p><p><strong>'.$this->objLanguage->languageText('mod_podcast_downloadpodcast', 'podcast').':</strong> ('.$this->objLanguage->languageText('mod_podcast_rightclickandchoose', 'podcast', 'Right Click, and choose Save As').') '.$downloadLink->show().'</p>';
         
         
        if(!empty($courses)){
            $content .= "<strong>".$this->objLanguage->abstractText($this->objLanguage->languageText('mod_podcast_listcourse','podcast'))."</strong>";
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
        
        
        $title = new link ($this->uri(array('action'=>'viewpodcast', 'id'=>$podcast['id'])));
        $title->link = htmlentities($podcast['title']);
        
        echo $objFeatureBox->show($title->show().' '.$icons, $content);
    }
    
}


echo '<p>';

    if (isset($id)) {
        $HomeLink = new link($this->uri(NULL));
        $HomeLink->link = $this->objLanguage->languageText('mod_podcast_podcasthome', 'podcast');
        
        echo $HomeLink->show().' / ';
    } else {
        
        $HomeLink = new link($this->uri(NULL));
        $HomeLink->link = $this->objLanguage->languageText('mod_podcast_exitpodcastreturnsite', 'podcast', 'Exit Podcast and Return to Site');
        
        echo $HomeLink->show().' / ';
        
        
    }
    
    $link = new link($this->uri(array('action'=>'addpodcast')));
    $link->link = $this->objLanguage->languageText('mod_podcast_addpodcast', 'podcast');
    
    echo $link->show();

echo '</p>&nbsp;';

?>
