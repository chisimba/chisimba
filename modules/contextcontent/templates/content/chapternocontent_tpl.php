<?php
$ret ="";
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->align = 'absmiddle';
$objIcon->setIcon('edit');
$editIcon = $objIcon->show();
$objIcon->setIcon('delete');
$deleteIcon = $objIcon->show();
$objIcon->setIcon('create_page');
$objIcon->alt = $this->objLanguage->languageText('mod_contextcontent_addapagetothischapter','contextcontent');
$objIcon->title = $this->objLanguage->languageText('mod_contextcontent_addapagetothischapter','contextcontent');
$addPageIcon = $objIcon->show();
$objIcon->setIcon('add_multiple');
$objIcon->alt = $this->objLanguage->languageText('mod_contextcontent_createpagefromfile','contextcontent','Create page from file');
$objIcon->title =$this->objLanguage->languageText('mod_contextcontent_createpagefromfile','contextcontent','Create page from file');
$addPageFromFileIcon = $objIcon->show();
$editLink = new link($this->uri(array('action'=>'editchapter', 'id'=>$chapter['chapterid'])));
$editLink->link = $editIcon;
$deleteLink = new link($this->uri(array('action'=>'deletechapter', 'id'=>$chapter['chapterid'])));
$deleteLink->link = $deleteIcon;
$addPageLink = new link($this->uri(array('action'=>'addpage', 'chapter'=>$chapter['chapterid'])));
$addPageLink->link = $addPageIcon;
$addPageFromFileLink = new link($this->uri(array('action'=>'addpagefromfile', 'chapterid'=>$chapter['chapterid'])));
$addPageFromFileLink->link = $addPageFromFileIcon;
$chapters = $this->objContextChapters->getContextChapters($this->contextCode);
$this->setVarByRef('chapters', $chapters);
$this->setLayoutTemplate('layout_firstpage_tpl.php');
$chapterlink=new htmlheading();
$chapterlink->type=1;
$con=$chapter['chaptertitle'];
if ($this->isValid('editchapter')) {
    $con.= ' '.$editLink->show();
}

if ($this->isValid('deletechapter')) {
    $con.= ' '.$deleteLink->show();
}

if ($this->isValid('addpage')) {
    $con.= ' '.$addPageLink->show();//.' / '.$addPageFromFileLink->show();
}
$chapterlink->str=$con;
$ret .= $chapterlink->show();

if ($this->getParam('message') == 'chaptercreated') {
    $ret .= '<p class="warning">'.$errorTitle.'</p>';
} else {
    $ret .= '<p class="error">'.$errorTitle.'. '.$errorMessage.'</p>';
}

/** removed at request of Eteaching customer
$introheader=new htmlheading();
$introheader->type=3;
$introheader->str=$this->objLanguage->languageText('mod_contextcontent_aboutchapter_introduction', 'contextcontent', 'About Chapter (Introduction)');
echo $introheader->show();
**/

$objWashout = $this->getObject('washout', 'utilities');

$ret .= $objWashout->parseText($chapter['introduction']);

$addPageLink = new link ($this->uri(array('action'=>'addpage', 'chapter'=>$chapter['chapterid'])));
$addPageLink->link = $this->objLanguage->languageText('mod_contextcontent_addapagetothischapter','contextcontent');

$addPageFromFileLink = new link ($this->uri(array('action'=>'addpagefromfile', 'context'=>$this->contextCode, 'chapterid'=>$chapter['chapterid'])));
$addPageFromFileLink->link = $this->objLanguage->languageText('mod_contextcontent_createpagefromfile','contextcontent','Create page from file');


if ($this->isValid('addpage')) {
     $ret .= $addPageLink->show().' / ';//.' / '.$addPageFromFileLink->show().' / ';
}

$returnLink = new link ($this->uri(NULL));
$returnLink->link = $this->objLanguage->languageText('mod_contextcontent_returntochapterlist', 'contextcontent', 'Return to Chapter List');

$ret .= $returnLink->show();

$ret = '<div id="context_content">' . $ret . '</div>';
echo $ret;

?>
