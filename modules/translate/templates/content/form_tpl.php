<?php

/* ------------icon request template----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

$this->loadClass('button','htmlelements');
$this->loadClass('radio','htmlelements');
$this->loadClass('textinput','htmlelements');

// mint template heading
$objH = &$this->newObject('htmlheading','htmlelements');
$objH->type = 1;
$objH->str = $this->objLanguage->languageText("mod_translate_maintitle","translate");

$submitMsg = &$this->newObject('timeoutMessage','htmlelements');
$confirm ='';
switch ($this->getParam('feedback')) {
	case null:
		break;
	case 'downloaded':
        $submitMsg->setMessage($this->objLanguage->languageText('mod_translate_downloaded',"translate"));
        $confirm = $submitMsg->show().'<br />';
        break;
    
    case 'import':
        $submitMsg->setMessage($this->objLanguage->languageText('mod_translate_imported',"translate"));
        $confirm = $submitMsg->show().'<br />';
        break;
    
    case 'uploadfailed':
        $submitMsg->setMessage($this->objLanguage->languageText('mod_translate_noupload',"translate"));
        $confirm = $submitMsg->show().'<br />';
        break;
        
    case 'filetype':
        $submitMsg->setMessage($this->objLanguage->languageText('mod_translate_badfiletype',"translate"));
        $confirm = $submitMsg->show().'<br />';
        break;
}

$langDrop = $this->getObject('dropdown','htmlelements');
$langDrop->name = "language";
$langDrop->addFromDB($this->objLanguage->getAll("ORDER BY languagename"),'languagename','id','1');

$modDrop = $this->newObject('dropdown','htmlelements');
$modDrop->setId("modDrop");
$modDrop->name = "modDrop";
$modDrop->addOption("system",$this->objLanguage->languageText("word_system"));
$modDrop->addFromDB($this->objModules->getAll("ORDER BY module_id"),'module_id','module_id',$this->getParam('mod'));

$objButton = new button("ex_but",$this->objLanguage->languageText('word_export'));
$objButton->setToSubmit();

$selection = new radio("exportSelection");
$selection->addOption("0",$this->objLanguage->languageText("mod_translate_exportone","translate"));
$selection->addOption("1",$this->objLanguage->languageText("mod_translate_exportall","translate"));
$selection->breakSpace = " &nbsp;&nbsp;";
$selection->extra = "onchange='exportChange(this.value)'";

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->width="500px";
$objTable->cellpadding = $objTable->cellspacing = "4";
$objTable->startRow();
$objTable->addCell($objLanguage->languageText("mod_translate_exportlang","translate").":");
$objTable->addCell($langDrop->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($selection->show(),null,null,null,null,"colspan='2'");
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("<div id='modStr'>".$objLanguage->languageText("mod_translate_selectmod","translate").":</div>");
$objTable->addCell($modDrop->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("&nbsp;");
$objTable->addCell($objButton->show());
$objTable->endRow();

$objH2 = $this->newObject('htmlheading','htmlelements');
$objH2->str = $this->objLanguage->languageText('phrase_exportpo');
$objH2->type = "3";

$objExportForm = $this->getObject("form","htmlelements");
$objExportForm->action = $this->uri(array('action'=>'export'));
$objExportForm->addToForm($objTable->show());

$export = $objH2->show().$objExportForm->show();

$objH2->str = $this->objLanguage->languageText('phrase_importpo');

$objTextInputname= new textinput();
$objTextInputname->setName("name");

$objTextInputmeta= new textinput();
$objTextInputmeta->setName("meta");

$objTextInputerror_text= new textinput();
$objTextInputerror_text->setName("error_text");
//$langInput = new textinput('error_text',$this->getParam('error_text'));

$langInput = new textinput('imlanguage',$this->getParam('imlanguage'));

$selection = new radio("importSelection");
$selection->addOption("0",$this->objLanguage->languageText("mod_translate_importone","translate"));
$selection->addOption("1",$this->objLanguage->languageText("mod_translate_importall","translate"));
$selection->breakSpace = " &nbsp;&nbsp;";
$selection->extra = "onchange='importChange(this.value)'";

$modDrop = $this->newObject('dropdown','htmlelements');
$modDrop->setId("importDrop");
$modDrop->name = "importDrop";
$modDrop->addOption("system",$this->objLanguage->languageText("word_system"));
$modDrop->addFromDB($this->objModules->getAll("ORDER BY module_id"),'module_id','module_id',$this->getParam('mod'));

$fileSelect = new textinput('pofile',null,'file');

$objButton = new button("im_but",$this->objLanguage->languageText('word_import'));
$objButton->setToSubmit();
//$objButton->extra = "disabled='false'";

$MAX_FILE_SIZE_input = new textinput('MAX_FILE_SIZE','10000000','hidden');

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->width="500px";
$objTable->cellpadding = $objTable->cellspacing = "4";
$objTable->startRow();
$objTable->addCell($objLanguage->languageText("mod_translate_importlang","translate").":");
$objTable->addCell($langInput->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($objLanguage->languageText("mod_translate_langname","translate").":");
$objTable->addCell($objTextInputname->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($objLanguage->languageText("mod_translate_metadescription","translate").":");
$objTable->addCell($objTextInputmeta->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($objLanguage->languageText("mod_translate_errortext","translate").":");
$objTable->addCell($objTextInputerror_text->show());
$objTable->endRow();


$objTable->startRow();
$objTable->addCell($selection->show(),null,null,null,null,"colspan='2'");
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("<div id='importStr'>".$objLanguage->languageText("mod_translate_importmod","translate").":</div>");
$objTable->addCell($modDrop->show());
$objTable->endRow();
$objTable->addCell($objLanguage->languageText("mod_translate_importfile","translate").":");
$objTable->addCell($MAX_FILE_SIZE_input->show().$fileSelect->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("&nbsp;");
$objTable->addCell($objButton->show());
$objTable->endRow();


$objForm = $this->newObject('form','htmlelements');
$objForm->addToForm($objTable->show());
$objForm->action = $this->uri(array('action'=>'import'));
$objForm->extra = "enctype='multipart/form-data'";
$objForm->addRule('imlanguage',$this->objLanguage->languageText("mod_translate_langreq","translate"),'required');
$objForm->addRule('pofile',$this->objLanguage->languageText("mod_translate_filereq","translate"),'required');


$import = $objH2->show().$objForm->show();


$strLang = "$export<hr /><span class='error'></span>$import";
// generate content
$content = $objH->show().$confirm."<hr />".$strLang;


$this->appendArrayVar('headerParams',"<script type='text/javascript' src='".$this->objConfig->getModuleURI()."translate/resources/translate.js'></script>");

// display content
echo $content;
?>
