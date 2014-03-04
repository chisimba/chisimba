<?php
//$this->setVar('pageSuppressXML',true);
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$header = new htmlheading();
if ($mode=='edit') {
    $header->str = $this->objLanguage->languageText('mod_learningcontent_editcontextpages','learningcontent').': '.htmlentities($page['menutitle']);
    $this->setVar('pageTitle', htmlentities($this->objContext->getTitle().' - '.$this->objLanguage->languageText('mod_learningcontent_editcontextpages','learningcontent').': '.$page['menutitle']));
} else {
    $header->str = $this->objLanguage->languageText('mod_learningcontent_addnewcontextpages','learningcontent');
    $this->setVar('pageTitle', htmlentities($this->objContext->getTitle().' - '.$this->objLanguage->languageText('mod_learningcontent_addnewcontextpages','learningcontent')));
}
$header->type = 1;
echo $header->show();

$form = new form('addpage', $this->uri(array('action'=>$formaction)));
$formTable = $this->newObject('htmltable', 'htmlelements');

//if ($mode=='add') {
    $label = new label ($this->objLanguage->languageText('mod_learningcontent_parent','learningcontent'), 'input_parentnode');

    $formTable->startRow();
    $formTable->addCell($label->show());
    $formTable->addCell($tree);
    $formTable->endRow();
//}
$menuTitle = new textinput('menutitle');
$menuTitle->size = '80%';

if ($mode=='edit') {
    $menuTitle->value = htmlentities($page['menutitle']);
}

$label = new label ($this->objLanguage->languageText('mod_learningcontent_pagetitle','learningcontent'), 'input_menutitle');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($menuTitle->show());
$formTable->endRow();

$htmlarea = $this->newObject('htmlarea', 'htmlelements');
$htmlarea->setName('pagecontent');
$htmlarea->context = TRUE;
if ($mode == 'add') {
    $htmlarea->setContent('<h1>'.$this->objLanguage->languageText('mod_learningcontent_addtitle','learningcontent').'</h1>'.'<p>'.$this->objLanguage->languageText('mod_learningcontent_startcontent','learningcontent').'</p>');
} else {
    $htmlarea->setContent($page['pagecontent']);
}

$label = new label ($this->objLanguage->languageText('mod_learningcontent_pagecontent','learningcontent'), 'input_htmlarea');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($htmlarea->show());
$formTable->endRow();
//Metatags Textinput
$headerScripts = $this->newObject('multifileselect2', 'filemanager');
$headerScripts->name = 'headerscripts';
$headerScripts->restrictFileList = array('js', 'css');
$headerScripts->context = TRUE;
if ($mode=='edit') {
    $headerScripts->setDefaultFiles($page['headerscripts']);
}


$label = new label ('<strong>'.$this->objLanguage->languageText('mod_learningcontent_metatags_javascript','learningcontent').'</strong>', 'input_headerscripts');

$jsForHeader = $this->objLanguage->languageText('mod_learningcontent_enterjavascriptmetatags','learningcontent', 'Enter any JavaScript or Meta Tags that you need to be loaded into the [-HEAD-] tags');

$jsForHeader = str_replace('[-HEAD-]', '&lt;head&gt;', $jsForHeader);

$formTable->startRow();
$formTable->addCell($label->show().'<p>'.$jsForHeader.'</p>', '240');
$formTable->addCell($headerScripts->show());
$formTable->endRow();
//Picture Textinput
$headerPicScripts = $this->newObject('multifileselect2', 'filemanager');
$headerPicScripts->name = 'headerpicscripts';
//$headerPicScripts->restrictFileList = array('jpg', 'png', 'gif');
$headerPicScripts->context = TRUE;
if ($mode=='edit') {
    $headerPicScripts->setDefaultFiles($page['pagepicture']);
}

$label = new label ('<strong>'.$this->objLanguage->languageText('mod_learningcontent_insertpic','learningcontent').'</strong>', 'input_headerpicscripts');

$formTable->startRow();
$formTable->addCell($label->show(), '240');
$formTable->addCell($headerPicScripts->show());
$formTable->endRow();
//Formula Textinput
$headerFormulaScripts = $this->newObject('multifileselect2', 'filemanager');
$headerFormulaScripts->name = 'headerformulascripts';
//$headerFormulaScripts->restrictFileList = array('jpg', 'png', 'gif');
$headerFormulaScripts->context = TRUE;
if ($mode=='edit') {
    $headerFormulaScripts->setDefaultFiles($page['pageformula']);
}

$label = new label ('<strong>'.$this->objLanguage->languageText('mod_learningcontent_insertformula','learningcontent').'</strong>', 'input_headerformulascripts');

$formTable->startRow();
$formTable->addCell($label->show(), '240');
$formTable->addCell($headerFormulaScripts->show());
$formTable->endRow();

$formTable->startRow();
$formTable->addCell('&nbsp;');
$formTable->addCell('&nbsp;');
$formTable->endRow();

$button = new button('submitform', $this->objLanguage->languageText('mod_learningcontent_savepage','learningcontent'));
$button->setToSubmit();

$formTable->startRow();
$formTable->addCell('&nbsp;');
$formTable->addCell($button->show());
$formTable->endRow();

$form->addToForm($formTable->show());

if ($mode == 'edit') {
    $hiddeninput = new hiddeninput('id', $page['id']);
    $form->addToForm($hiddeninput->show());
    $hiddeninput = new hiddeninput('context', $this->contextCode);
    $form->addToForm($hiddeninput->show());
} else {
    $hiddeninput = new hiddeninput('chapter', $chapter);
    $form->addToForm($hiddeninput->show());

}

// Rules

$form->addRule('menutitle', $this->objLanguage->languageText('mod_learningcontent_pleaseenterpagetitle','learningcontent'), 'required');


  /*   $menutitle = stripslashes($this->getParam('menutitle'));
        $headerscripts = stripslashes($this->getParam('headerscripts'));
        $language = 'en';
        $pagecontent = stripslashes($this->getParam('pagecontent'));
        $parent = stripslashes($this->getParam('parentnode'));
        $chapter = stripslashes($this->getParam('chapter'));
        $chapterTitle = $this->objContextChapters->getContextChapterTitle($chapter);
        $titleId = $this->objContentTitles->addTitle('', $menutitle, $pagecontent, $language, $headerscripts);
   *         $pageId = $this->getParam('id');
        $contextCode = $this->getParam('context');
        $menutitle = stripslashes($this->getParam('menutitle'));
        $headerScripts = stripslashes($this->getParam('headerscripts'));
        $pagecontent = stripslashes($this->getParam('pagecontent'));
        $parentnode = stripslashes($this->getParam('parentnode'));
   *
   *
   *
*/
echo $form->show();
$autosave = 'jQuery(document).ready(function() {
var saved=false;
var id="";
 window.setInterval(
   function(){
     var url;
     if(saved){
      url=\''.str_replace("amp;", "",$this->uri(array('action'=>'updatepage'))).'\';
     }else{
      url=\''.str_replace("amp;", "",$this->uri(array('action'=>'savepage'))).'\';
     }
    data = jQuery("form").serialize();
    menutitle="";
     jQuery.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(msg) {
                saved=true;
        }
    });
}, 10000);

});
';

//echo "<div id=\"search-xwin\"><script type='text/javascript'>".$autosave."</script></div>";;
?>
