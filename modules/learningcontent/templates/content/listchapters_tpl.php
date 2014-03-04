<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');

$objModules = $this->getObject('modules', 'modulecatalogue');
$pdfHtmlDoc = $objModules->checkIfRegistered('htmldoc');



$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->align = 'absmiddle';

$objIcon->setIcon('edit');
$editIcon = $objIcon->show();

$objIcon->setIcon('delete');
$deleteIcon = $objIcon->show();

$objIcon->setIcon('add');
$objIcon->alt = $this->objLanguage->languageText('mod_learningcontent_txtformulanpicschapter','learningcontent');
$objIcon->title = $this->objLanguage->languageText('mod_learningcontent_txtformulanpicschapter','learningcontent');
$addIcon = $objIcon->show();

$objIcon->setIcon('create_page');
$objIcon->alt = $this->objLanguage->languageText('mod_learningcontent_addapagetothischapter','learningcontent');
$objIcon->title = $this->objLanguage->languageText('mod_learningcontent_addapagetothischapter','learningcontent');
$addPageIcon = $objIcon->show();

$objIcon->setIcon('scm');
$objIcon->alt = $this->objLanguage->languageText('mod_scorm_addscormchapter','scorm');
$objIcon->title = $this->objLanguage->languageText('mod_scorm_addscormchapter','scorm');
$addScormIcon = $objIcon->show();

$objIcon->setIcon('pdf');
$objIcon->alt = $this->objLanguage->languageText('mod_learningcontent_downloadchapterinpdfformat','learningcontent');
$objIcon->title = $this->objLanguage->languageText('mod_learningcontent_downloadchapterinpdfformat','learningcontent');
$pdfIcon = $objIcon->show();

$objIcon->setIcon('documents');
$objIcon->alt = $this->objLanguage->languageText('mod_learningcontent_justtextchapter','learningcontent');
$objIcon->title = $this->objLanguage->languageText('mod_learningcontent_justtextchapter','learningcontent');
$justTextChapterIcon = $objIcon->show();

$objIcon->setIcon('onlineresume');
$objIcon->alt = $this->objLanguage->languageText('mod_learningcontent_textnpicschapter','learningcontent');
$objIcon->title = $this->objLanguage->languageText('mod_learningcontent_textnpicschapter','learningcontent');
$textnpicsChapterIcon = $objIcon->show();

$objIcon->setIcon('addassignment');
$objIcon->alt = $this->objLanguage->languageText('mod_learningcontent_textnformulachapter','learningcontent');
$objIcon->title = $this->objLanguage->languageText('mod_learningcontent_textnformulachapter','learningcontent');
$txtnformulaChapterIcon = $objIcon->show();
$objIcon->setIcon('reportpostgrad');
$objIcon->alt = $this->objLanguage->languageText('mod_learningcontent_txtformulanpicschapter','learningcontent');
$objIcon->title = $this->objLanguage->languageText('mod_learningcontent_txtformulanpicschapter','learningcontent');
$txtnformulanpicsChapterIcon = $objIcon->show();

//The Activity Streamer Icon
$this->objAltConfig = $this->getObject('altconfig','config');
$modPath=$this->objAltConfig->getModulePath();
$replacewith="";
$docRoot=$_SERVER['DOCUMENT_ROOT'];
$resourcePath=str_replace($docRoot,$replacewith,$modPath);
$imgPath="http://" . $_SERVER['HTTP_HOST']."/".$resourcePath.'/learningcontent/resources/img/new.png';
$streamerimg ='<img src="'.$imgPath.'">';

if ($this->isValid('addchapter')) {
    $link = new link ($this->uri(array('action'=>'addtfpicschapter')));
    $link->link = $addIcon;
    
    $addChapter = $link->show();
} else {
    $addChapter = '';
}
if($this->objModuleCatalogue->checkIfRegistered('scorm') && $this->isValid('addchapter')) {
    $link = new link ($this->uri(array('action'=>'addscorm')));
    $link->link = $addScormIcon;
    $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    $enableScorm=$objSysConfig->getValue('ENABLE_SCORM', 'learningcontent');

    $addScormChapter =$enableScorm == 'true'? $link->show():'';
} else {
    $addScormChapter = '';
}
if ($this->isValid('addchapter')) {
    //Add Chapter with Text Only
    $link = new link ($this->uri(array('action'=>'addtxtchapter')));
    $link->link = $justTextChapterIcon;
    //Add Chapter with Text and Pictures
    $tnplink = new link ($this->uri(array('action'=>'addtxtnpicschapter')));
    $tnplink->link = $textnpicsChapterIcon;
    //Add Chapter with Text and Formula
    $tnflink = new link ($this->uri(array('action'=>'addtxtnformulachapter')));
    $tnflink->link = $txtnformulaChapterIcon;
    //Add Chapter with Text Formula and Pictures
    $tnfnplink = new link ($this->uri(array('action'=>'addtfpicschapter')));
    $tnfnplink->link = $txtnformulanpicsChapterIcon;
    $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    $enableOther=$objSysConfig->getValue('ENABLE_JUSTTEXT', 'learningcontent');

    //$addOtherChapter =$enableOther == 'true'? $link->show()." ".$tnplink->show()." ".$tnflink->show()." ".$tnfnplink->show():'';
} else {
    $addOtherChapter = '';
}

echo '<h1>'.$this->objLanguage->languageText("mod_learningcontent_contextpagesfor",'learningcontent')." ".$this->objContext->getTitle().' '.$addChapter.' '.$addScormChapter.'</h1>';

$counter = 1;
$notVisibleCounter=0;
$addedCounter=0;

// Form for Quick Jump to a Chapter
$form = new form($this->uri(array('action'=>'viewchapter')));
$form->method = 'GET';

$module = new hiddeninput('module', 'learningcontent');
$form->addToForm($module->show());

$action = new hiddeninput('action', 'viewchapter');
$form->addToForm($action->show());

$label = new label($this->objLanguage->languageText('mod_learningcontent_jumptochapter','learningcontent').': ', 'input_id');
$form->addToForm($label->show());

$dropdown = new dropdown('id');

// End Form

$chapterList = '<div id="allchapters">';

$objWashout = $this->getObject('washout', 'utilities');

foreach ($chapters as $chapter)
{
    $showChapter = TRUE;
    
    if ($chapter['visibility'] == 'N') {
        $showChapter = FALSE;
    }
    
    if ($this->isValid('viewhiddencontent')) {
        $showChapter = TRUE;
    }
    
    if ($showChapter) {
	$addedCounter++;
	if ($chapter['scorm'] == 'Y') {

		// Get List of Pages in the Chapter
		$chapterPages = $this->objContentOrder->getTree($this->contextCode, $chapter['chapterid'], 'htmllist');

		if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
		        $hasPages = FALSE;
		        $dropdown->addOption($chapter['chapterid'], $chapter['chaptertitle'], ' disabled="disabled" title="'.$this->objLanguage->languageText('mod_learningcontent_chapterhasnopages','learningcontent').'"');
		        $notVisibleCounter++;
		} else {
		        $hasPages = TRUE;
		        $dropdown->addOption($chapter['chapterid'], $chapter['chaptertitle']);
		}

		$editLink = new link($this->uri(array('action'=>'editscorm', 'id'=>$chapter['chapterid'])));
		$editLink->link = $editIcon;

		$deleteLink = new link($this->uri(array('action'=>'deletechapter', 'id'=>$chapter['chapterid'])));
		$deleteLink->link = $deleteIcon;

		$addPageLink = new link($this->uri(array('action'=>'addpage', 'chapter'=>$chapter['chapterid'])));
		$addPageLink->link = $addPageIcon;

		$chapterLink = new link($this->uri(array('action'=>'viewscorm', 'folderId'=>$chapter['introduction'], 'chapterid'=>$chapter['chapterid']), $module = 'scorm'));
		$chapterLink->link = $chapter['chaptertitle'];
		$ischapterlogged = $this->objContextActivityStreamer->checkRecord($this->userId, $chapter['chapterid'], $this->contextCode);
		if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
       		if ($ischapterlogged == FALSE) {
		         $content = '<h1> '.$streamerimg." ".$chapterLink->show();;
		        }else{
		         $content = '<h1>'.$chapterLink->show();
		        }
		} else {
       		if ($ischapterlogged == FALSE) {
		         $content = '<h1> '.$streamerimg." ".$chapterLink->show();;
		        }else{
		         $content = '<h1>'.$chapterLink->show();
		        }
		}

		if ($this->isValid('editchapter')) {
		        $content .= ' '.$editLink->show();
		}

		if ($this->isValid('deletechapter')) {
		        $content .= ' '.$deleteLink->show();
		}

/*
		if ($this->isValid('addpage')) {
		        $content .= ' '.$addPageLink->show();
		}
*/
		$content .= '</h1><hr />';

	} else {
		

		// Get List of Pages in the Chapter
		$chapterPages = $this->objContentOrder->getTree($this->contextCode, $chapter['chapterid'], 'htmllist');

		if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
		        $hasPages = FALSE;
		        $dropdown->addOption($chapter['chapterid'], $chapter['chaptertitle'], ' disabled="disabled" title="'.$this->objLanguage->languageText('mod_learningcontent_chapterhasnopages','learningcontent').'"');
		        $notVisibleCounter++;
		} else {
		        $hasPages = TRUE;
		        $dropdown->addOption($chapter['chapterid'], $chapter['chaptertitle']);
		}

		$editLink = new link($this->uri(array('action'=>'editchapter', 'id'=>$chapter['chapterid'])));
		$editLink->link = $editIcon;

		$deleteLink = new link($this->uri(array('action'=>'deletechapter', 'id'=>$chapter['chapterid'])));
		$deleteLink->link = $deleteIcon;

		$addPageLink = new link($this->uri(array('action'=>'addpage', 'chapter'=>$chapter['chapterid'])));
		$addPageLink->link = $addPageIcon;

		$chapterLink = new link($this->uri(array('action'=>'viewchapter', 'id'=>$chapter['chapterid'])));
		$chapterLink->link = $chapter['chaptertitle'];

		$ischapterlogged = $this->objContextActivityStreamer->checkRecord($this->userId, $chapter['chapterid'], $this->contextCode);
		if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
       		if ($ischapterlogged == FALSE) {
		         $content = '<h1> '.$streamerimg." ".$chapter['chaptertitle'];
		        }else{
		         $content = '<h1>'.$chapter['chaptertitle'];
		        }

		} else {
       		if ($ischapterlogged == FALSE) {
		         $content = '<h1> '.$streamerimg." ".$chapterLink->show();;
		        }else{
		         $content = '<h1>'.$chapterLink->show();
		        }
		}

		if ($this->isValid('editchapter')) {
		        $content .= ' '.$editLink->show();
		}

		if ($this->isValid('deletechapter')) {
		        $content .= ' '.$deleteLink->show();
		}

		if ($this->isValid('addpage')) {
		        $content .= ' '.$addPageLink->show();
		}


		if ($pdfHtmlDoc && trim($chapterPages) != '<ul class="htmlliststyle"></ul>') {
		        
		        $pdfLink = new link($this->uri(array('action'=>'viewprintchapter', 'id'=>$chapter['chapterid'])));
		        $pdfLink->link = $pdfIcon;
		        
		        $content .= ' '.$pdfLink->show();
		}

		$content .= '</h1>';

		//print_r($chapter);

		if ($this->isValid('viewhiddencontent') && $chapter['visibility'] != 'Y') {
		        
		        switch ($chapter['visibility'])
		        {
		        case 'I': $notice = $this->objLanguage->code2Txt('mod_learningcontent_studentcanonlyviewintro','learningcontent'); break;
		        case 'N': $notice = $this->objLanguage->code2Txt('mod_learningcontent_chapternotvisibletostudents','learningcontent'); break;
		        default: $notice = ''; break;
		        }
		        $content .= '<p class="warning"><strong>'.$this->objLanguage->languageText('mod_learningcontent_note','learningcontent').': </strong>'.$notice.'</p>';
		}

		$content .= $objWashout->parseText($chapter['introduction']);

		$chapterOptions = array();

		if ($chapter['visibility'] == 'I' && !$this->isValid('viewhiddencontent')) {
		        $content .= '<p class="warning">'.ucfirst($this->objLanguage->code2Txt('mod_learningcontent_studentscannotaccesscontent','learningcontent')).'.</p>';
		        
		        // Empty variable for use later on
		        $chapterPages = '';

		} else {
		        
		        if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>' && $this->isValid('viewhiddencontent')) {
		        $content .= '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_learningcontent_chapterhasnocontentpages','learningcontent').'</div>';
		        
		        // Empty variable for use later on
		        $chapterPages = '';
		        
		        } else if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
		        $content .= '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_learningcontent_chapterhasnocontentpages','learningcontent').'</div>';
		        
		        // Empty variable for use later on
		        $chapterPages = '';
		        } else {		        
		         $chapterOptions[] = '<div id="toc_'.$chapter['chapterid'].'"  style="display:none">'.$chapterPages.'</div><a href="#" onclick="Effect.toggle(\'toc_'.$chapter['chapterid'].'\', \'slide\'); return false;"><strong>'.$this->objLanguage->languageText('mod_learningcontent_showhidecontents','learningcontent').' ...</strong></a>';
		        }
		} 
	       
	        
	        $addPageLink = new link ($this->uri(array('action'=>'addpage', 'chapter'=>$chapter['chapterid'])));
	        $addPageLink->link = $this->objLanguage->languageText('mod_learningcontent_addapagetothischapter','learningcontent');
	        
	        $moveUpLink = new link ($this->uri(array('action'=>'movechapterup', 'id'=>$chapter['contextchapterid'])));
	        $moveUpLink->link = $this->objLanguage->languageText('mod_learningcontent_movechapterup','learningcontent');
	        
	        $moveDownLink = new link ($this->uri(array('action'=>'movechapterdown', 'id'=>$chapter['contextchapterid'])));
	        $moveDownLink->link = $this->objLanguage->languageText('mod_learningcontent_movechapterdown','learningcontent');
	        
	        //$content .= '<br />';
	        
	        if ($this->isValid('addpage')) {
	            //$content .= $addPageLink->show();
	        }
	        
	        if (count($chapters) > 1 && $counter > 1 && $this->isValid('movechapterup')) {
	            $chapterOptions[] = $moveUpLink->show();
	        }
	        
	        if ($counter < count($chapters) && $this->isValid('movechapterdown')) {
	            $chapterOptions[] = $moveDownLink->show();
	        }
	        
	        if (count($chapterOptions) > 0) {
	            
	            $divider = '';
	            foreach ($chapterOptions as $option)
	            {
	                $content .= $divider.$option;
	                $divider = ' / ';
	            }
	            
	        }
	}       
        $chapterList .= '<div>'.$content.'</div><hr />';
    }
    
    $counter++;
}

$chapterList .= '</div>';


if (count($chapters) > 1) {
    $form->addToForm($dropdown->show());

    $button = new button ('', 'Go');
    $button->setToSubmit();
    
    if ($notVisibleCounter == $addedCounter) {
        $button->extra = ' disabled="disabled" ';
    }
    
    $form->addToForm(' '.$button->show());
    
    echo $form->show();
}

echo $chapterList;

if ($this->isValid('addchapter')) {
    $link = new link ($this->uri(array('action'=>'addchapter')));
    $link->link = $this->objLanguage->languageText('mod_learningcontent_addanewchapter','learningcontent');
    
    echo $link->show();
}

    
    $link = new link($this->uri(array(
    		'action' => 'rss', 'title' => $this->objContext->getTitle(), 'rss_contextcode' => $this->contextCode)));
    $objIcon->setIcon('rss');
    $objIcon->alt = null;
    $objIcon->title = null;
    $link->link = $this->objLanguage->languageText('mod_learningcontent_feedstext','learningcontent');
    echo '<br/><br clear="left" />'.$objIcon->show().' '.$link->show();


?>
