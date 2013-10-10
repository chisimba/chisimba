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
    $header->str = $this->objLanguage->languageText('mod_contextcontent_editcontextpages','contextcontent').': '.htmlentities($page['menutitle']);
    $this->setVar('pageTitle', htmlentities($this->objContext->getTitle().' - '.$this->objLanguage->languageText('mod_contextcontent_editcontextpages','contextcontent').': '.$page['menutitle']));
} else {
    $header->str = $this->objLanguage->languageText('mod_contextcontent_addnewcontextpages','contextcontent');
    $this->setVar('pageTitle', htmlentities($this->objContext->getTitle().' - '.$this->objLanguage->languageText('mod_contextcontent_addnewcontextpages','contextcontent')));
}
$header->type = 1;
echo $header->show();

$form = new form('addpage', $this->uri(array('action'=>$formaction)));
$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cssClass = 'ctxtcnt-add-table';

//if ($mode=='add') {
    $label = new label ($this->objLanguage->languageText('mod_contextcontent_parent','contextcontent'), 'input_parentnode');

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

$label = new label ($this->objLanguage->languageText('mod_contextcontent_pagetitle','contextcontent'), 'input_menutitle');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($menuTitle->show());
$formTable->endRow();

$htmlarea = $this->newObject('htmlarea', 'htmlelements');
$htmlarea->setName('pagecontent');
$htmlarea->context = TRUE;
$contenttitleheader = new htmlheading();
$contenttitleheader->type=1;
$contenttitleheader->str=$this->objLanguage->languageText('mod_contextcontent_addtitle','contextcontent');
if ($mode == 'add') {
    $htmlarea->setContent($contenttitleheader->show().'<p class="startcontent">'.$this->objLanguage->languageText('mod_contextcontent_startcontent','contextcontent').'</p>');
} else {
    $htmlarea->setContent($page['pagecontent']);
}

$label = new label ($this->objLanguage->languageText('mod_contextcontent_pagecontent','contextcontent'), 'input_htmlarea');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($htmlarea->show());
$formTable->endRow();

$headerScripts = $this->newObject('multifileselect', 'filemanager');
$headerScripts->name = 'headerscripts';
$headerScripts->restrictFileList = array('js', 'css');
$headerScripts->context = TRUE;
if ($mode=='edit') {
    $headerScripts->setDefaultFiles($page['headerscripts']);
}

$label = new label ('<strong class="metatagstrong">'.$this->objLanguage->languageText('mod_contextcontent_metatags_javascript','contextcontent').'</strong>', 'input_headerscripts');
$jsForHeader = $this->objLanguage->languageText('mod_contextcontent_enterjavascriptmetatags','contextcontent', 'Enter any JavaScript or Meta Tags that you need to be loaded into the [-HEAD-] tags');
$jsForHeader = str_replace('[-HEAD-]', '&lt;head&gt;', $jsForHeader);

$saveButtonText = '<span class=\'save\'>' 
   . $this->objLanguage->languageText('mod_contextcontent_savepage','contextcontent')
   . '</span>';
$button = new button('submitform', $saveButtonText);
$button->cssId = 'ctxtcnt-add-submit';
$button->setToSubmit();

$formTable->startRow();
$formTable->addCell($label->show().'<br /><span class="jsforheader">'.$jsForHeader.'</span>', '240');
$formTable->addCell($headerScripts->show(). $button->show());
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

$form->addRule('menutitle', $this->objLanguage->languageText('mod_contextcontent_pleaseenterpagetitle','contextcontent'), 'required');

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


?>
