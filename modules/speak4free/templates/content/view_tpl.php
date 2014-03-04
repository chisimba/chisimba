<?php
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$objBookMarks = $this->getObject('socialbookmarking', 'utilities');
$objBookMarks->includeTextLink = FALSE;

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('edit');

$heading = new htmlheading();

if ($file['title'] == '') {
    $heading->str = $this->objLanguage->languageText("mod_speak4free_viewfile", "speak4free").' - '.$file['filename'];
} else {
    $heading->str = $file['title'];
}

$showDeleteLink = FALSE;

if ($file['creatorid'] == $objUser->userId()) {
    $objSubModalWindow = $this->getObject('submodalwindow', 'htmlelements');

    //$editLink = $objSubModalWindow->show($objIcon->show(), $this->uri(array('action'=>'edit', 'id'=>$file['id'], 'mode'=>'submodal')), 'link');

    //$heading->str .= ' '.$editLink;

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
    $objIcon->title = $this->objLanguage->languageText("mod_speak4free_blogthispresentation", "speak4free");
    $objIcon->alrt =$this->objLanguage->languageText("mod_speak4free_blogthispresentation", "speak4free");

    // http://localhost/speak4free/index.php?module=blog&action=blogadmin&mode=writepost
    $blogThisLink = new link ($this->uri(array('action'=>'blogadmin', 'mode'=>'writepost', 'text'=>'[WPRESENT: id='.$file['id'].']<br /><br />'), 'blog'));
    $blogThisLink->link = $objIcon->show();

    $heading->str .= ' '.$blogThisLink->show();
}

echo $heading->show();




$rightCell = '';



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
            . $this->objLanguage->languageText("mod_speak4free_notags", "speak4free")
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




$objFileIcons = $this->getObject('fileicons', 'files');

$rightCell .= '<ul>';



$rightCell .= '</ul>';

$uploaderLink = new link ($this->uri(array('action'=>'byuser', 'userid'=>$file['creatorid'])));
$uploaderLink->link = $objUser->fullname($file['creatorid']);

$rightCell .= '<p><strong>'.$this->objLanguage->languageText("mod_speak4free_uploadedby", "speak4free","Uploaded by").':</strong> '.$uploaderLink->show().'</p>';

// Output filter code for local and remote filter.
$this->loadClass('textinput','htmlelements');

$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
  
$ext = pathinfo($file['filename']);
$ext = $ext['extension'];
$fullPath = $this->objConfig->getcontentBasePath().'speak4free/'.$file['id'].'/'.$file['id'].'.'.$ext;

$objAltConfig = $this->getObject('altconfig','config');
$replacewith="";
$docRoot=$_SERVER['DOCUMENT_ROOT'];
$resourcePath=str_replace($docRoot,$replacewith,$fullPath);
$codebase="http://" . $_SERVER['HTTP_HOST'].'/'.$resourcePath;

$fileTypes = array(
    'png'=>'image',
    'flv'=>'flv',
    'mp3'=>'audio',
    'mov'=>'quicktime',
     'wmv'=>'wmv',
    'ogg'=>'ogg',
    'mpg'=>'mpg',
    'mpeg'=>'mpeg',
    'mp4'=>'mp4'

    );
foreach ($fileTypes as $fileType=>$fileName) {
if($fileType == $ext) {
$leftContents =$this->objFileEmbed->embed($codebase, $fileName);
}

}
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



$script_src = '<script type="text/javascript" language="javascript" src="/chisimba_modules/speak4free/resources/gwt/avoir.realtime.base.gwt.Invite.nocache.js"></script>';
$this->appendArrayVar('headerParams', $script_src);

$sessionmanager= $this->getObject("sessionmanager", "realtime");
echo $table->show();

$homeLink = new link ($this->uri(NULL));
$homeLink->link = $this->objLanguage->languageText("phrase_backhome",'Back Home');

$bottomLinks = array();

$bottomLinks[] = $homeLink->show();



if ($blogRegistered) {
    $blogThisLink = new link ($this->uri(array('action'=>'blogadmin', 'mode'=>'writepost', 'text'=>'[WPRESENT: id='.$file['id'].']<br /><br />'), 'blog'));
    $blogThisLink->link = $this->objLanguage->languageText("mod_speak4free_blogthispresentation", "speak4free","Blog this file");

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