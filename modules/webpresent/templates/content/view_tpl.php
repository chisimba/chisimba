<?php
// Add SlideShow if content is available
if (count($slideContent['slideshow']) > 0) {
    $this->appendArrayVar('headerParams', $this->getJavaScriptFile('slide.js'));

    $jsContent = "
    <script type=\"text/javascript\">
    <!--
      var viewer = new PhotoViewer();

    ";

    foreach ($slideContent['slideshow'] as $jsSlide) {
        $jsContent .= $jsSlide;
    }

    $jsContent .= "

      viewer.disableEmailLink();
      viewer.disablePhotoLink();

    //-->
    </script>";

    $this->appendArrayVar('headerParams', $jsContent);
}

/*
// Add SlideShow if content is available
if (count($slideContent['slideshow']) > 0) {
    $this->appendArrayVar('headerParams', $this->getJavaScriptFile('smoothgalleryslightbox/mootools.js'));
    $this->appendArrayVar('headerParams', $this->getJavaScriptFile('smoothgalleryslightbox/jd.gallery.js'));
    $this->appendArrayVar('headerParams', $this->getJavaScriptFile('smoothgalleryslightbox/slightbox.js'));


    $this->appendArrayVar('headerParams', '<link rel="stylesheet" href="'.$this->getResourceUri('smoothgalleryslightbox/jd.gallery.css').'" type="text/css" media="screen" charset="utf-8" />');
    $this->appendArrayVar('headerParams', '<link rel="stylesheet" href="'.$this->getResourceUri('smoothgalleryslightbox/slightbox.css').'" type="text/css" media="screen" charset="utf-8" />');

    $jsContent = '';

    $this->appendArrayVar('headerParams', $jsContent);
    $this->appendArrayVar('bodyOnLoad', 'var mylightbox = new Lightbox();');
}

*/
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$objBookMarks = $this->getObject('socialbookmarking', 'utilities');
$objBookMarks->includeTextLink = FALSE;

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('edit');

$heading = new htmlheading();

if ($file['title'] == '') {
    $heading->str = $this->objLanguage->languageText("mod_webpresent_viewfile", "webpresent").' - '.$file['filename'];
} else {
    $heading->str = $file['title'];
}

$showDeleteLink = FALSE;

if ($file['creatorid'] == $objUser->userId()) {
    $objSubModalWindow = $this->getObject('submodalwindow', 'htmlelements');

    $editLink = $objSubModalWindow->show($objIcon->show(), $this->uri(array('action'=>'edit', 'id'=>$file['id'], 'mode'=>'submodal')), 'link');

    $heading->str .= ' '.$editLink;

    $objIcon->setIcon('delete');

    $deleteLink = $objSubModalWindow->show($objIcon->show(), $this->uri(array('action'=>'delete', 'id'=>$file['id'], 'mode'=>'submodal')), 'link');

    $heading->str .= ' '.$deleteLink;

    $showDeleteLink = TRUE;

}

if ($showDeleteLink == FALSE && $this->isValid('admindelete')) {
    $objIcon->setIcon('delete');

    $objSubModalWindow = $this->getObject('submodalwindow', 'htmlelements');
    $deleteLink = $objSubModalWindow->show($objIcon->show(), $this->uri(array('action'=>'admindelete', 'id'=>$file['id'], 'mode'=>'submodal')), 'link');
    $objIcon->setIcon('delete');

    $objIcon->setIcon('edit');
    $editLink = $objSubModalWindow->show($objIcon->show(), $this->uri(array('action'=>'edit', 'id'=>$file['id'], 'mode'=>'submodal')), 'link');

    $heading->str .= $editLink.' '.$deleteLink;
}

$heading->type = 1;

// Check if blog is registered
$objModules  = $this->getObject('modules', 'modulecatalogue');
$blogRegistered = $objModules->checkIfRegistered('blog');

// If registered
if ($blogRegistered) {
    $objIcon->setModuleIcon('blog');
    $objIcon->title = $this->objLanguage->languageText("mod_webpresent_blogthispresentation", "webpresent");
    $objIcon->alrt =$this->objLanguage->languageText("mod_webpresent_blogthispresentation", "webpresent");

    // http://localhost/webpresent/index.php?module=blog&action=blogadmin&mode=writepost
    $blogThisLink = new link ($this->uri(array('action'=>'blogadmin', 'mode'=>'writepost', 'text'=>'[WPRESENT: id='.$file['id'].']<br /><br />'), 'blog'));
    $blogThisLink->link = $objIcon->show();

    $heading->str .= ' '.$blogThisLink->show();
}

echo $heading->show();

// Show the flash file using the viewer class
$objView = $this->getObject("viewer", "webpresent");
$flashContent = $objView->showFlash($file['id']);

$rightCell = '<div style="float:right">'.$objBookMarks->diggThis().'</div>';



//$rightCell = '<p><strong>Title of Presentation:</strong> '.$file['title'].'</p>';

if ($file['description'] != '') {
    $rightCell .= '<p><strong>'
            . $this->objLanguage->languageText("word_description")
            . ':</strong><br /> '
            .nl2br(htmlentities($file['description']))
            .'</p>';
}

$rightCell .=  '<p><strong>'
        . $this->objLanguage->languageText("word_tags")
        . ':</strong> ';

if (count($tags) == 0) {
    $rightCell .=  '<em>'
            . $this->objLanguage->languageText("mod_webpresent_notags", "webpresent")
            . ' </em>';
} else {
    $divider = '';
    foreach ($tags as $tag) {
        $tagLink = new link ($this->uri(array('action'=>'tag', 'tag'=>$tag['tag'])));
        $tagLink->link = $tag['tag'];
        $rightCell .=  $divider.$tagLink->show();
        $divider = ', ';
    }
}
$rightCell .=  '</p>';

$objDisplayLicense = $this->getObject('displaylicense', 'creativecommons');
$objDisplayLicense->icontype = 'big';

$license = ($file['cclicense'] == '' ? 'copyright' : $file['cclicense']);

$rightCell .=  '<p>'.$objDisplayLicense->show($license).'</p>';

$rightCell .=  '<h3>'
        . $this->objLanguage->languageText("word_download")
        . '</h3>';

$fileTypes = array('odp'=>'OpenOffice Impress Presentation', 'ppt'=>'PowerPoint Presentation', 'pdf'=>'PDF Document');

$objFileIcons = $this->getObject('fileicons', 'files');

$rightCell .= '<ul>';

foreach ($fileTypes as $fileType=>$fileName) {
    $ext = pathinfo($file['filename']);
    $ext = $ext['extension'];
    $fullPath = $this->objConfig->getcontentBasePath().'webpresent/'.$file['id'].'/'.$file['id'].'.'.$fileType;

    if (file_exists($fullPath)) {
        //$relLink = $this->objConfig->getcontentPath().'webpresent/'.$file['id'].'/'.$file['id'].'.'.$fileType;
        $link = new link($this->uri(array('action'=>'download', 'id'=>$file['id'], 'type'=>$fileType)));
        $link->link = $objFileIcons->getExtensionIcon($fileType).' '.$fileName;

        $rightCell .= '<li>'.$link->show().'</li>';
    }

}

$rightCell .= '</ul>';

$uploaderLink = new link ($this->uri(array('action'=>'byuser', 'userid'=>$file['creatorid'])));
$uploaderLink->link = $objUser->fullname($file['creatorid']);

$rightCell .= '<p><strong>'.$this->objLanguage->languageText("mod_webpresent_uploadedby", "webpresent").':</strong> '.$uploaderLink->show().'</p>';

// Output filter code for local and remote filter.
$this->loadClass('textinput','htmlelements');
$filterBox=new textinput('filter');
$filterBox->size=38;

$flashUrl = $this->uri(array('action'=>'getflash', 'id'=>$file['id']));


$flashUrl =  $this->objConfig->getsiteRoot()
        . $this->objConfig->getcontentPath()
        .'webpresent/'  .$file['id'] .'/'. $file['id'].'.swf';


$filterText = "[WPRESENT: type=byurl, url=" . $flashUrl . "]";
$filterBox->setValue($filterText);
$rightCell  .= "<p><strong>" . $this->objLanguage->languageText("mod_webpresent_filterbyurl", "webpresent")
        . "</strong>: " . $filterBox->show() . "<br />"
        . $this->objLanguage->languageText("mod_webpresent_filterbyurlexplained", "webpresent")
        . "</p>";
unset($filterText);

$snippetText = '<div style="border: 1px solid #000; width: 534px; height: 402px; text-align: center;"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="540" height="400">
  <param name="movie" value="' . $flashUrl . '">
  <param name="quality" value="high">
  <embed src="'.$flashUrl.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="534" height="402"></embed>
  </object></div>
';

$this->loadClass('textarea', 'htmlelements');
$snippetBox=new textarea('snippet', $snippetText, 4, 43);
$rightCell  .= "<p><strong>"
        . $this->objLanguage->languageText("mod_webpresent_snippet", "webpresent")
        . "</strong>:" . $snippetBox->show() . "<br />"
        .  $this->objLanguage->languageText("mod_webpresent_snippetexplained", "webpresent")
        . "</p>";


// End of output the filter code.

$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();

/*
 Removed by Nic Appleby -> not sure what the intended logic was here
 
$objModule = $this->getObject('modules','modulecatalogue');
//See if the mathml module is registered and set params
$isRegistered = $objModule->checkIfRegistered('disqus');
if ($isRegistered) {
    $disqus=$this->getObject('disquselems','disqus');
    $leftContents = $flashContent.'<br/>'.$disqus->addWidget();
    ;
}
//$this->objComments = $this->getObject('commentapi', 'blogcomments');
//$this->objblogPosts->addCommentForm($postid, $userid, $captcha = TRUE, $comment, $useremail);

*/

$leftContents= $flashContent;
$leftContents .= '<br /><p>'.$objBookMarks->addThis();
$divider = ' &nbsp;';

foreach ($objBookMarks->options as $option) {
    if ($option != 'diggThis' && $option != 'addThis') {
        $leftContents .= $divider.$objBookMarks->$option();
    }
}

$leftContents .= '</p>';

$table->addCell($leftContents, 550);
$table->addCell($rightCell);

$objTabs = $this->newObject('tabcontent', 'htmlelements');

/**
 *      * We need the agenda, so find it. If the presentation was not given any specific
 * title (which is used as default agenda), then use the file name.
 * However, the presenter will be given a chance to modify this (just temporarily
 * for the presentation) before starting a live presentation
 */


$agenda='';
if (trim($file['title']) == '') {
    $agenda = $file['filename'];
} else {
    $agenda = htmlentities($file['title']);
}


//set up the tabs
$objTabs->addTab($this->objLanguage->languageText("mod_webpresent_presentation", "webpresent"), $table->show());
$objTabs->addTab($this->objLanguage->languageText("mod_webpresent_slides", "webpresent"), $slideContent['slides']);
$objTabs->addTab($this->objLanguage->languageText("mod_webpresent_transcript", "webpresent"), $slideContent['transcript']);


$script_src = '<script type="text/javascript" language="javascript" src="/chisimba_modules/webpresent/resources/gwt/avoir.realtime.base.gwt.Invite.nocache.js"></script>';
$this->appendArrayVar('headerParams', $script_src);

$objModule = $this->getObject('modules','modulecatalogue');
//See if the mathml module is registered and set params
$isRegistered = $objModule->checkIfRegistered('realtime');
if ($isRegistered) {
//    $sessionmanager= $this->getObject("sessionmanager", "realtime");
 //   $objTabs->addTab($this->objLanguage->languageText("mod_webpresent_livepresentation", "webpresent"), $sessionmanager->showSessionList($file['id'],$agenda,$this->objUser->fullname()));

}

$objTabs->width = '95%';
//then display the tabs
echo $objTabs->show();

$homeLink = new link ($this->uri(NULL));
$homeLink->link = $this->objLanguage->languageText("phrase_backhome");

$bottomLinks = array();

$bottomLinks[] = $homeLink->show();

if ($this->isValid('regenerate')) {
    $flashLink = new link ($this->uri(array('action'=>'regenerate', 'type'=>'flash', 'id'=>$file['id'])));
    $flashLink->link = $this->objLanguage->languageText("mod_webpresent_regenerateflash", "webpresent");
    $bottomLinks[] = $flashLink->show();

    $slidesLink = new link ($this->uri(array('action'=>'regenerate', 'type'=>'slides', 'id'=>$file['id'])));
    $slidesLink->link = $this->objLanguage->languageText("mod_webpresent_slides", "webpresent");
    $bottomLinks[] = $slidesLink->show();

    $pdfLink = new link ($this->uri(array('action'=>'regenerate', 'type'=>'pdf', 'id'=>$file['id'])));
    $pdfLink->link = $this->objLanguage->languageText("mod_webpresent_pdf", "webpresent");
    $bottomLinks[] = $pdfLink->show();


}

if ($blogRegistered) {
    $blogThisLink = new link ($this->uri(array('action'=>'blogadmin', 'mode'=>'writepost', 'text'=>'[WPRESENT: id='.$file['id'].']<br /><br />'), 'blog'));
    $blogThisLink->link = $this->objLanguage->languageText("mod_webpresent_blogthispresentation", "webpresent");

    $bottomLinks[] = $blogThisLink->show();
}


echo '<p>';
$divider = '';
foreach ($bottomLinks as $link) {
    echo $divider.$link;
    $divider = ' | ';
}

echo '</p>';

?>