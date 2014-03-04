<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('windowpop', 'htmlelements');
$this->loadClass('formatfilesize', 'files');


$objIcon = $this->newObject('geticon', 'htmlelements');
$objFeatureBox = $this->getObject('featurebox', 'navigation');
$filesize = new formatfilesize();


if ($mode == 'confirm' || $mode == 'editpodcast') {
    
    if ($mode == 'confirm') {
        echo '<h1>'.$this->objLanguage->languageText('mod_podcast_podcastadded', 'podcast').': '.$podcast['title'].'</h1>';
        
        echo '<p>'.$this->objLanguage->languageText('mod_podcast_detailsdetected', 'podcast').':</p>';
        
        $formprocess = 'confirmsave';
    } else {
        $formprocess = 'confirmupdate';
        echo '<h1>'.$this->objLanguage->languageText('mod_podcast_editpodcast', 'podcast').': '.$podcast['title'].'</h1>';
    }
    
    $form = new form ('confirmpodcast', $this->uri(array('action' => 'confirmsave')));
    
    $table = $this->newObject('htmltable', 'htmlelements');
    $table->width = '100%';
    $table->cellpadding = 5;
    $table->startRow();
        $table->addCell($this->objLanguage->languageText('word_filename', 'system').':');
        $table->addCell(htmlentities($podcast['filename']));
    $table->endRow();
    
    $table->startRow();
        if ($mode == 'confirm') {
            $title = new textinput('title', $podcast['metatitle']);
        } else {
            $title = new textinput('title', $podcast['title']);
        }
        
        $title->size = 80;
        $label = new label($this->objLanguage->languageText('word_title', 'system').':', 'input_title');
        $table->addCell($label->show());
        $table->addCell($title->show());
    $table->endRow();
    
    $table->startRow();
        $description = new textinput('description', $podcast['description']);
        $description->size = 80;
        $label = new label($this->objLanguage->languageText('word_description', 'system').':', 'input_description');
        $table->addCell($label->show());
        $table->addCell($description->show());
    $table->endRow();
    
    
    
    $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell('&nbsp;');
    $table->endRow();

	if(!isset($isUpdate)){
		if($this->objUser->isLecturer()){
			$arrCourses = $this->objUtils->getContextList();
			foreach($arrCourses as $course)
			{
				$courseCheck = new checkbox('courses[]',null);
				$courseCheck->setValue($course['contextcode']);
				$label = new label($course['title'],null);
				$table->startRow();
				$table->addCell($label->show());
				$table->addCell($courseCheck->show());
				$table->endRow();
			}
		}
	}else{
		if($this->objUser->isLecturer()){
			$courses = array();
			$courses = $this->objUtils->getContextList();
			$contextIn = array();
			$coursesInPodcasts = $this->objPodcast->listOfContextCode($this->getParam('id'));
			foreach($coursesInPodcasts as $k => $value)
			{
				$contextIn[$value['contextcode']] = $value['contextcode'];
			}

			foreach($courses as $course)
			{
				if($contextIn[$course['contextcode']]==$course['contextcode']){
					$courseCheck = new checkbox('courses[]',null, true);
				}else{	
					$courseCheck = new checkbox('courses[]',null);
				}
				$courseCheck->setValue($course['contextcode']);
				$label = new label($course['title'],null);
				$table->startRow();
				$table->addCell($label->show());
				$table->addCell($courseCheck->show());
				$table->endRow();
				$i++;
			}
		}
	}
    
    $table->startRow();
        $button = new button('submitform', $this->objLanguage->languageText('mod_podcast_updatepodcast', 'podcast'));
        $button->setToSubmit();
        
        $table->addCell('&nbsp;');
        $table->addCell($button->show());
    $table->endRow();
    
    $form->addToForm($table->show());
     
	if(isset($isUpdate) && $isUpdate == "yes"){
		$hiddenInput = new hiddeninput('isUpdate','yes');
		$form->addToForm($hiddenInput->show());
	}
     
    $hiddenInput = new hiddeninput('id', $podcast['id']);
    $form->addToForm($hiddenInput->show());
    
    $hiddenInput = new hiddeninput('process', $formprocess);
    $form->addToForm($hiddenInput->show());
    
    echo $form->show();
}


if ($mode == 'alreadyused') {
    echo '<h1 class="error">'.$this->objLanguage->languageText('word_error', 'system').': '.$this->objLanguage->languageText('mod_podcast_filealreadyusedasapodcast', 'podcast').'</h1>';
    echo '<p>'.$this->objLanguage->languageText('mod_podcast_podcastalreadyadded', 'podcast').'</p>';
    
    $content = '<p>'.htmlentities($podcast['description']).'</p>';
    
    $table = $this->newObject('htmltable', 'htmlelements');
    $table->startRow();
        $table->addCell('<strong>'.$this->objLanguage->languageText('word_by', 'system').':</strong> '.$this->objUser->fullname($podcast['creatorid']), '50%');
        $table->addCell('<strong>'.$this->objLanguage->languageText('word_date', 'system').':</strong> '.$this->objDateTime->formatDate($podcast['datecreated']), '50%');
    $table->endRow();
    $table->startRow();
        $table->addCell('<strong>'.$this->objLanguage->languageText('phrase_filesize', 'system').':</strong> '.$filesize->formatsize($podcast['filesize']), '50%');
        $playtime = $this->objDateTime->secondsToTime($podcast['playtime']);
        $playtime = ($playtime == '0:0') ? '<em>Unknown</em>' : $playtime;
        $table->addCell('<strong>'.$this->objLanguage->languageText('word_playtime', 'system').':</strong> '.$playtime, '50%');
    $table->endRow();
    
    $content .= $table->show();
    
    $downloadLink = new link ($this->uri(array('action'=>'downloadfile', 'id'=>$podcast['id'])));
    $downloadLink->link = htmlentities($podcast['filename']);
    
    $this->objPop=&new windowpop;
    $this->objPop->set('location',$this->uri(array('action'=>'playpodcast', 'id'=>$podcast['id']), 'podcast'));
    $this->objPop->set('linktext', $this->objLanguage->languageText('mod_podcast_listenonline', 'podcast'));
    $this->objPop->set('width','280');
    $this->objPop->set('height','120');
    //leave the rest at default values
    $this->objPop->putJs(); // you only need to do this once per page
    
    $content .= '<br /><p>'.$this->objPop->show().' / <strong>'.$this->objLanguage->languageText('mod_podcast_downloadpodcast', 'podcast').':</strong> '.$downloadLink->show().'</p>';
    
    if ($podcast['creatorid'] == $this->objUser->userId()) {
        $objIcon->setIcon('edit');
        
        $editLink = new link ($this->uri(array('action'=>'editpodcast', 'id'=>$podcast['id'])));
        $editLink->link = $objIcon->show();
        
        $deleteIcon = $objIcon->getDeleteIconWithConfirm($podcast['id'], array('action'=>'deletepodcast', 'id'=>$podcast['id']),
            'podcast', $this->objLanguage->languageText('mod_podcast_confirmdeletepodcast', 'podcast'));
        $icons = ' '.$editLink->show().' '.$deleteIcon;
    } else {
        $icons = '';
    }
    
    echo $objFeatureBox->show(htmlentities($podcast['title']).$icons, $content);
}

echo '<p>';

    $HomeLink = new link($this->uri(NULL));
    $HomeLink->link = $this->objLanguage->languageText('mod_podcast_podcasthome', 'podcast');
    
    echo $HomeLink->show().' / ';
    
    $myPodcastLink = new link($this->uri(array('action'=>'byuser')));
    $myPodcastLink->link = $this->objLanguage->languageText('mod_podcast_mypodcasts', 'podcast');
    
    echo $myPodcastLink->show().' / ';
    
    $link = new link($this->uri(array('action'=>'addpodcast')));
    $link->link = $this->objLanguage->languageText('mod_podcast_addpodcast', 'podcast');
    
    echo $link->show();

echo '</p>&nbsp;';
?>