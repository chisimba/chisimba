<?php


$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->objAltConfig = $this->getObject('altconfig','config');
$modPath=$this->objAltConfig->getModulePath();
$replacewith="";
$docRoot=$_SERVER['DOCUMENT_ROOT'];
$resourcePath=str_replace($docRoot,$replacewith,$modPath);
$contentImgPath="http://" . $_SERVER['HTTP_HOST']."/".$resourcePath.'/learningcontent/resources/img/add.png';
$newImgPath="http://" . $_SERVER['HTTP_HOST']."/".$resourcePath.'/learningcontent/resources/img/new.png';

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

$form = new form ('searchform', $this->uri(array('action'=>'search')));
$form->method = 'GET';

$hiddenInput = new hiddeninput('module', 'learningcontent');
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('action', 'search');
//$form->addToForm($hiddenInput->show());

$textinput = new textinput ('contentsearch', $this->getParam('contentsearch'));
$button = new button ('searchgo', 'Go');
$button->setToSubmit();
//Add toolbar
$toolbar = $this->getObject('contextsidebar', 'context');

//$form->addToForm($textinput->show().' '.$button->show());

$objFieldset = $this->newObject('fieldset', 'htmlelements');
$label = new label ('Search for:', 'input_contentsearch');

//$objFieldset->setLegend($label->show());
//$objFieldset->contents = $form->show();

$header = new htmlHeading();
$header->str = ucwords($this->objLanguage->code2Txt('mod_learningcontent_name', 'learningcontent', NULL, '[-context-] Content'));
$header->type = 2;
$content = "";
//$content .= $header->show();

//$content .= $objFieldset->show();

$content .= '<h3>Chapters:</h3>';
$chapters = $this->objContextChapters->getContextChapters($this->contextCode);

if (count($chapters) > 0) {
    
    $content .= '<ol>';
    
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

            $bookmarkLink = new link("#{$chapter['chapterid']}"); 
            $bookmarkLink->link ='';
            $bookmarkLink->title = $this->objLanguage->languageText('mod_learningcontent_scrolltohapter','learningcontent');
            $contentimg='<img src="'.$contentImgPath.'">';
            $newimg='<img src="'.$newImgPath.'">';
      	     // Get List of Pages in the Chapter

            $ischapterlogged = $this->objContextActivityStreamer->checkRecord($this->objUser->userId(), $chapter['chapterid'], $this->contextCode); 
            if($ischapterlogged == FALSE) {
             $showImg=$newimg;
            }else{
               $showImg="";
            }
            	if ($chapter['scorm'] == 'Y') {
                $link = new link ($this->uri(array('action'=>'viewscorm','mode'=>'chapter', 'folderId'=>$chapter['introduction'], 'chapterid'=>$chapter['chapterid']), $module = 'scorm'));
                $link->link = $chapter['chaptertitle'].$showImg;
                $content .= '<li>'.$link->show();
            }else{
                $link = new link ($this->uri(array('action'=>'viewchapter', 'id'=>$chapter['chapterid'])));
                $link->link = $chapter['chaptertitle'].$showImg;
                $content .= '<li>'.$link->show();
													}
            //}
            
            if (isset($showScrollLinks) && $showScrollLinks) {
                $content .= " ".$bookmarkLink->show().'</li>';
            }           
        }
    }    
    $content .= '</ol>';
}

if ($this->isValid('addchapter')) {
    $link = new link ($this->uri(array('action'=>'addchapter')));
    $link->link = $this->objLanguage->languageText('mod_learningcontent_addanewchapter','learningcontent');
    
    $content .=  '<br /><p>'.$link->show().'</p>';
}
if ($this->hasAccess) {
    $link = new link ($this->uri(array('action'=>'viewlogs')));
    $link->link = $this->objLanguage->languageText('mod_learningcontent_useractivitylogs','learningcontent');
    
    $content .=  '<br /><p>'.$link->show().'</p>';
}
$objFieldset->contents = $toolbar->show();
$cssLayout->setLeftColumnContent($content);
$cssLayout->setMiddleColumnContent($this->getContent());
$cssLayout->setRightColumnContent($objFieldset->show());
echo $cssLayout->show();
?>
