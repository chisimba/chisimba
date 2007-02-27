<?php

$this->setVar('pageSuppressXML',true);

//Insert a breadcrumb if they came from another module
$objTl =& $this->getObject('tools', 'toolbar');
$comeFrom = $this->getParam('comefrom', NULL);
if ( $comeFrom != NULL ) {
    //Make the aback link
    $bread = $this->uri(array(), $comeFrom);
    $links = array('<a href="' . $bread . '">'
  . $comeFrom . '</a>', $this->objLanguage->languageText("word_edit",'stories'));
  $objTl->addToBreadCrumbs($links);
}

// Check if the text should be changed.
$textModule = 'stories';

//Set up the form processor
$paramArray=array(
    'action'=>'save',
    'mode'=>$mode);
$formAction=$this->uri($paramArray);

// Default Expiration Date set to following date
$expirationDate = date("Y-m-d H:i", mktime(0,0,0,date("m"),date("d")+1,date("Y")));

//Get the categories
$objCat = & $this->getObject('dbstorycategory', 'storycategoryadmin');
$filter = NULL;
$car = $objCat->getAll($filter);

//Get the save button
$saveButton = $this->objButtons->putSaveButton();

//Set the form title depending on what we are doing
$action=$this->getParam('action', Null);

//The name of the site
$site = $this->objConfig->getsiteName();
$rep = array('sitename' => $site);

//Set the title
$formTitle=ucwords($this->objLanguage->code2Txt("mod_".$textModule."_title", "stories"));

//Set the fieldset label
switch ($action) {
    case null:
        $fieldsetLabel = $this->objLanguage->languageText("mod_stories_viewlabel", "stories");
        break;
    case "edit":
        //Get the data
        $ar = $this->objDbStories->getForEdit("edit");
        $id = $ar['id'];
        $category = $ar['category'];
        $language = $ar['language'];
        $parentId = $ar['parentid'];
        $creatorId = $ar['creatorid'];
        $isActive = $ar['isactive'];
        $title = htmlentities(stripslashes($ar['title']));
        $abstract = stripslashes($ar['abstract']);
        $mainText = stripslashes($ar['maintext']);
        $dateCreated = $ar['datecreated'];
        $expirationDate = $ar['expirationdate'];
        $isSticky = $ar['issticky'];
        $rep = array('STORY' => "<i>" . $title ."</i>");
        $fieldsetLabel = $this->objLanguage->code2Txt("mod_".$textModule."_editlabel", "stories");
        break;
    case "add":
        $id = NULL;
        $category = NULL;
        $creatorId = $this->objUser->userId();
        $isActive = 1;
        $title = NULL;
        $English = NULL;
        $abstract = NULL;
        $mainText = NULL;
        $displaytext = NULL;
        $expirationDate = $expirationDate;
        $dateCreated = date('Y-m-d H:m:s');
        $isSticky = 0;
        $fieldsetLabel = $this->objLanguage->code2Txt("mod_".$textModule."_addlabel", "stories");
        break;
    case "translate":
        $id = NULL;
        $parentId = $this->getParam('parentid', NULL);
        $category = $this->getParam('category', NULL);
        $creatorId = $this->objUser->userId();
        $isActive = 1;
        $title = NULL;
        $English = NULL;
        $abstract = NULL;
        $mainText = NULL;
        $displaytext = NULL;
        $expirationDate = $expirationDate;
        $dateCreated = date('Y-m-d H:m:s');
        $isSticky = 0;
        $fieldsetLabel = $this->objLanguage->code2Txt("mod_".$textModule."_translatelabel", "stories");
        break;
    default:
        $fieldsetLabel = "??";
        break;
}


/****** Set up header parameters for javascript date picker ********/
$headerParams=$this->getJavascriptFile('ts_picker.js','htmlelements');
$headerParams.="\n\n<script>\n/*Script by Denis Gritcyuk: tspicker@yahoo.com
Submitted to JavaScript Kit (http://javascriptkit.com)
Visit http://javascriptkit.com for this script*/\n</script>\n\n";
$this->appendArrayVar('headerParams',$headerParams);

//Load the form elements that I need
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('radio','htmlelements');

//Create the form class
$objForm = new form('storyinput');
$objForm->setAction($formAction);
$objForm->displayType=3;  //Free form

//Add the form title to the form
$objForm->addToForm('<div align="center">');
$objForm->addToForm('<h3 align="center">'.$formTitle.'</h3>');

//Create an instance of the fieldset object
$objFieldset = & $this->getObject('fieldset', 'htmlelements');
$objFieldset->legend=$fieldsetLabel;
$objFieldset->legendalign='CENTER';
$objFieldset->width="77%";


//Create a table to layout the form elements
$objFmTable = $this->newObject("htmltable", "htmlelements");
$objFmTable->width = "70%";

//Add a row for the ID
$objFmTable->startRow();
//$objFmTable->addCell("Id:&nbsp;", NULL, "top", "right");

if ($action=='edit') {
    $objTextInput = new textinput('id', $id);
    $objTextInput->fldType="hidden";
    $objFmTable->addCell($objTextInput->show(), NULL, "top", "left");
} else {
    $objFmTable->addCell($id, NULL, "top", "left");
    $language="";
    if ($action != 'translate') {
        $parentId="";
    }
}
$objFmTable->endRow();

//Create a dropdown for the category selector
$objCat = $this->newObject("dropdown", "htmlelements");
$objCat->name = 'category_selector';
$objCat->extra=" onchange=\"document.forms['storyinput'].category.value=document.forms['storyinput'].category_selector.value;\"";
$objCat->addOption("","Clear input");
$objCat->addFromDB($car, 'title', 'category', $category);

//Add a row for the category
$objFmTable->startRow();
$objFmTable->addCell($objLanguage->languageText("word_category").":&nbsp;", NULL, "top", "right");
$objTextInput = new textinput('category', $category);
$objTextInput->extra = 'readonly="READONLY"';
$objForm->addRule('category',$objLanguage->languageText("mod_stories_val_catnotnull", "stories"),'required');
$objFmTable->addCell($objTextInput->show() . " " . $objCat->show(), NULL, "top", "left");
$objFmTable->endRow();

//Add a row for the language
$objFmTable->startRow();
$objFmTable->addCell($objLanguage->languageText("word_language").":&nbsp;", NULL, "top", "right");
$objTextInput = new textinput('language', $language);
$objTextInput->extra = 'readonly="READONLY"';
$objForm->addRule(array('name'=>'language', 'length'=>2), $objLanguage->languageText("mod_stories_val_lang2chargt", "stories"), 'maxlength');
$objForm->addRule('language',$objLanguage->languageText("mod_stories_val_langnotnull", "stories"),'required');

//Create a dropdown for the language selector
$objCat = $this->newObject("dropdown", "htmlelements");
$objCat->name = 'language_selector';
$objCat->extra=" onchange=\"document.forms['storyinput'].language.value=document.forms['storyinput'].language_selector.value;\"";
$objCat->addOption("","Clear input");
$objLangList = & $this->newObject('languagecode','language');
$objCat->selected = $language;
foreach ($objLangList->iso_639_2_tags->codes as $key => $value) {
    $objCat->addOption($key, $value);
}
$objFmTable->addCell($objTextInput->show(). " " . $objCat->show(), NULL, "top", "left");
$objFmTable->endRow();

//Add a row for the parentId
$objFmTable->startRow();
//$objFmTable->addCell($objLanguage->languageText("mod_stories_parentId").":&nbsp;", NULL, "top", "right");
$objTextInput = new textinput('parentId', $parentId);
$objTextInput->fldType = 'hidden';
if ($parentId != 'base') {
    $parentIdInput = $objTextInput->show();
} else {
    $objTextInput->fldType="hidden";
    $parentIdInput = $objTextInput->show() ;
}
$objFmTable->addCell($parentIdInput, NULL, "top", "left");
$objFmTable->endRow();

//Add a row for the make sticky
$objRadioElement = new radio('isSticky');
$objRadioElement->addOption('1', $objLanguage->languageText("word_yes"));
$objRadioElement->addOption('0', $objLanguage->languageText("word_no"));
$objRadioElement->setSelected($isSticky);

$objFmTable->startRow();
$objFmTable->addCell($objLanguage->languageText("mod_stories_alwaysontop", "stories").":&nbsp;", NULL, "top", "right");
$objFmTable->addCell($objRadioElement->show(), NULL, "top", "left");
$objFmTable->endRow();

//Add a row for the author name
$objFmTable->startRow();
$objFmTable->addCell($objLanguage->languageText("word_author").":&nbsp;", NULL, "top", "right");
$objFmTable->addCell($this->objUser->fullName(), NULL, "top", "left");
$objFmTable->endRow();

//Add a row for the story title
$objFmTable->startRow(NULL, "top", NULL, NULL, " colspan=\"2\"");
$objFmTable->addCell($objLanguage->languageText("word_title").":&nbsp;", NULL, "top", "right");
$objTextInput = new textinput('title', $title);
$objForm->addRule('title',$objLanguage->languageText("mod_stories_val_titnotnull", "stories"),'required');
$objTextInput->size="78";
$objTextInput->id='title';
$objFmTable->addCell($objTextInput->show(), NULL, "top", "left");
$objFmTable->endRow();

//Add a row for the abstract
$objFmTable->startRow();
$objFmTable->addCell($objLanguage->languageText("word_abstract").":&nbsp;", NULL, "top", "right");
$objTextArea = new textarea('abstract', $abstract);
$objTextArea->cols=77;
$objFmTable->addCell($objTextArea->show(), NULL, "top", "left");
$objFmTable->endRow();

//Create a new table for the three other things
$objFmTable2 = $this->newObject("htmltable", "htmlelements");
$objFmTable2->width = "100%";

//Add a row for the labels
$objFmTable2->startRow();
$objFmTable2->addCell($objLanguage->languageText("phrase_isactive"), NULL, "top", "center");
$objFmTable2->addCell($objLanguage->languageText("phrase_dateposted"), NULL, "top", "center");
$objFmTable2->addCell($objLanguage->languageText("phrase_expirationdate"), NULL, "top", "center");
$objFmTable2->endRow();

//Add a row for the inputs
$objFmTable2->startRow();
$objTextInput = new textinput('isActive', $isActive);

//Is the story active
if (!isset($isActive)) {
    $isActive = '1';
}

$objRadioElement = new radio('isActive');
$objRadioElement->addOption('1', $objLanguage->languageText("word_yes"));
$objRadioElement->addOption('0', $objLanguage->languageText("word_no"));
$objRadioElement->setSelected($isActive);


$objFmTable2->addCell($objRadioElement->show(), NULL, "top", "center");
$objFmTable2->addCell($this->formatDate($dateCreated), NULL, "top", "center");
//Correct the date format for expirationDate for the popup cal
if (strlen($expirationDate)==19) {
    $expirationDate=substr($expirationDate, 0, 16); #this is a dirty hack
}
$objTextInput = new textinput('expirationDate', $expirationDate);
$cell=$objTextInput->show();
$objIcon=$this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('modules/calendar');
$objIcon->alt=$this->objLanguage->code2Txt('mod_'.$textModule.'_dpick');

$objLink=$this->newObject('link', 'htmlelements');
$objLink->link("javascript:show_calendar('document.storyinput.expirationDate', document.storyinput.expirationDate.value);");
$objLink->link=$objIcon->show();
$cell .= $objLink->show();
$objFmTable2->addCell($cell . NULL, "top", "center");
$objFmTable2->endRow();

//Add the output
$objFmTable->startRow();
$objFmTable->addCell($objFmTable2->show(), NULL, "top", "left", NULL, "colspan=\"2\"");
$objFmTable->endRow();

//Add the WYSWYG editor label
$storyLabel = ucfirst($objLanguage->code2Txt("mod_".$textModule."_story", "stories"));
$objFmTable->startRow();
$objFmTable->addCell($storyLabel.":&nbsp;", NULL, "top", "left", NULL, "colspan=\"2\"");
$objFmTable->endRow();
$objFmTable->startRow();

//See if there is a plain text input
$inputType = $this->getParam('inputtype', NULL);

if ($inputType == 'plaintext') {
    //Make the link to switch to WYSWYG
    $switchArray=array(
      'action'=>$action,
      'id'=>$this->getParam('id', NULL),
      'comefrom'=>$this->getParam('comefrom', NULL),
      'inputtype'=>'wyswyg');
    $switchLink = $this->uri($switchArray);
    $switchLink = "<br /><a href=\"" . $switchLink . "\">"
      . $this->objLanguage->languageText('mod_stories_wyswyg', "stories")
      . "</a>";
    //Add the plain text input
    $editor = $this->newObject('textarea', 'htmlelements');
    $editor->setName('mainText');
    $editor->value = $mainText;
    $editor->cols = 80;
    $editor->rows = 20;
    $objFmTable->addCell($editor->show() . $switchLink, NULL, "top", "center", NULL, "colspan=\"2\"");
    $objFmTable->endRow();
} else {
    //Make the link to switch to WYSWYG
    $switchArray=array(
      'action'=>$action,
      'id'=>$this->getParam('id', NULL),
      'comefrom'=>$this->getParam('comefrom', NULL),
      'inputtype'=>'plaintext');
    $switchLink = $this->uri($switchArray);
    $switchLink = "<a href=\"" . $switchLink . "\">"
      . $this->objLanguage->languageText('mod_stories_plaintext', "stories")
      . "</a>";
    //Add the WYSWYG editor
    $editor = $this->newObject('htmlarea', 'htmlelements');
    $editor->name = 'mainText';
    $editor->height = '300px';
    $editor->width = '550px';
    //To set the basic toolbar
    //$editor->setBasicToolBar();
    $editor->setContent($mainText);
    //$objFmTable->addCell($editor->show(). $switchLink, NULL, "top", "center", NULL, "colspan=\"2\"");
    $objFmTable->addCell($editor->showFCKEditor(). $switchLink, NULL, "top", "center", NULL, "colspan=\"2\"");
    $objFmTable->endRow();

}

$objFieldset->contents=$objFmTable->show();
//Add the current table to the form
$objForm->addToForm($objFieldset->show());

//Add a save button
$objButton = $this->newObject('button', 'htmlelements');
$objButton->button('save',$this->objLanguage->languageText('word_save'));
$objButton->setToSubmit();
$objForm->addToForm($objButton->show()."<br /><br /></div>");

//Show the form
echo $objForm->show();

?>