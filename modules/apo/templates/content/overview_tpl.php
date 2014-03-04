<?php

/*
 * @category  Chisimba
 * @package   apo (Academic Planning Office)
 * @author    Jacqueline Gil
 */

$validatorjs = '<script type="text/javascript" src="' . $this->getResourceURI('js/jquery.validate.js') . '"></script>';
$sectionsjs = '<script type="text/javascript" src="' . $this->getResourceURI('js/sections.js') . '"></script>';
$sectionscss = '<link rel="stylesheet" type="text/css" href="' . $this->getResourceURI('css/sections.css') . '" media="screen">';

$this->appendArrayVar("headerParams", $validatorjs);
$this->appendArrayVar("headerParams", $sectionsjs);
$this->appendArrayVar("headerParams", $sectionscss);


$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('iframe', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->documents = $this->getObject('dbdocuments');
$this->objformdata = $this->getObject('dbformdata');
$this->setVar('pageSuppressXML', TRUE);
$this->setVar('JQUERY_VERSION', '1.4.2');
$this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');

//$action = 'savingFormData';
//$nextaction="showrulesandsyllabus";
$action = 'showrulesandsyllabusone';

$formdata = $this->objformdata->getFormData("overview", $id);
if ($formdata != null) {
    $mode = "edit";
}

//$form = new form('overviewform', $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'overview','nextaction'=>$nextaction)));
$form = new form('overviewform', $this->uri(array('action' => $action, 'id' => $id, 'formname' => 'overview', 'toform' => 'rulesandsyllabusone')));

$xtitle = $this->objLanguage->languageText('mod_wicid_document', 'wicid', 'Section A: Overview');

$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;

echo $header->show();


$doclink = new link($this->uri(array("action" => "editdocument", 'id' => $id, 'formname' => 'overview')));
$doclink->link = "Document";

$rulesandsyllabusonelink = new link($this->uri(array("action" => "showrulesandsyllabusone", 'id' => $id, 'formname' => 'overview')));
$rulesandsyllabusonelink->link = "Rules and Syllabus - Page One";

$rulesandsyllabustwolink = new link($this->uri(array("action" => "showrulesandsyllabustwo", 'id' => $id, 'formname' => 'overview')));
$rulesandsyllabustwolink->link = "Rules and Syllabus - Page Two";

$subsidyrequirementslink = new link($this->uri(array("action" => "showsubsidyrequirements", 'id' => $id, 'formname' => 'overview')));
$subsidyrequirementslink->link = "Subsidy Requirements";

$outcomesandassessmentonelink = new link($this->uri(array("action" => "showoutcomesandassessmentone", 'id' => $id, 'formname' => 'overview')));
$outcomesandassessmentonelink->link = "Outcomes and Assessment - Page One";

$outcomesandassessmenttwolink = new link($this->uri(array("action" => "showoutcomesandassessmenttwo", 'id' => $id, 'formname' => 'overview')));
$outcomesandassessmenttwolink->link = "Outcomes and Assessment - Page Two";

$outcomesandassessmentthreelink = new link($this->uri(array("action" => "showoutcomesandassessmentthree", 'id' => $id, 'formname' => 'overview')));
$outcomesandassessmentthreelink->link = "Outcomes and Assessment - Page Three";

$resourceslink = new link($this->uri(array("action" => "showresources", 'id' => $id, 'formname' => 'overview')));
$resourceslink->link = "Resources";

$collaborationandcontractslink = new link($this->uri(array("action" => "showcollaborationandcontracts", 'id' => $id, 'formname' => 'overview')));
$collaborationandcontractslink->link = "Collaboration and Contracts";

$reviewlink = new link($this->uri(array("action" => "showreview", 'id' => $id, 'formname' => 'overview')));
$reviewlink->link = "Review";

$contactdetailslink = new link($this->uri(array("action" => "showcontactdetails", 'id' => $id, 'formname' => 'overview')));
$contactdetailslink->link = "Contact Details";

$commentslink = new link($this->uri(array("action" => "showcomments", "id" => $id)));
$commentslink->link = "Comments";

$feedbacklink = new link($this->uri(array("action" => "showfeedback", "id" => $id)));
$feedbacklink->link = "Feedback";

$links = $doclink->show() . '&nbsp;|&nbsp;' . "<b>Overview</b>" . '&nbsp;|&nbsp;' .
        $rulesandsyllabusonelink->show() . '&nbsp;|&nbsp;' . $rulesandsyllabustwolink->show() . '&nbsp;|&nbsp;' .
        $subsidyrequirementslink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentonelink->show() . '&nbsp;|&nbsp;' .
        $outcomesandassessmenttwolink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentthreelink->show() . '&nbsp;|&nbsp;' .
        $resourceslink->show() . '&nbsp;|&nbsp;' . $collaborationandcontractslink->show() . '&nbsp;|&nbsp;' .
        $reviewlink->show() . '&nbsp;|&nbsp;' . $contactdetailslink->show() . '&nbsp;|&nbsp;' . $feedbacklink->show() . '&nbsp;|&nbsp;' .
        $commentslink->show() . '<br/>';

$fs = new fieldset();
$fs->setLegend('<b>Navigation</b>');
$fs->addContent($links);

echo $fs->show();

$table = $this->newObject('htmltable', 'htmlelements');

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
//$uri = $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'overview', 'toform'=>'rulesandsyllabusone'));
//$button->setOnClick('javascript: window.location=\'' . $uri . '\'');

$form->addToForm('<br/>' . $button->show() . '&nbsp');


$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showeditdocument', 'id' => $id, 'formname' => 'overview', 'toform' => 'addeditdocument'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
//$button->setToSubmit();
$form->addToForm($button->show() . '&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show() . '&nbsp');
$form->extra = 'class="sections"';


$forwardText = $this->objLanguage->languageText('mod_apo_forward', 'apo', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action' => 'forwarding', 'from' => 'overview_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

//print_r($document);die();
$textinput = new textinput('a1');
$textinput->size = 100;
$textinput->value = $document['docname'];
//$textinput->extra = 'readonly';
if ($mode == "fixup") {
    $textinput->value = $a1;
}
if ($mode == "edit") {
    $textinput->value = $formdata['a1'];
}
$textinput->setCss("required");
$table->startRow();
$table->addCell("A.1. Name of course/unit:");
$table->endRow();

$table->startRow();
$table->addCell($textinput->show());
$table->endRow();

$radio = new radio('a2');
$radio->addOption('1', "proposal for a new course/unit ");
$radio->addOption('2', "change to the outcomes or credit value of a course/unit");
$radio->setSelected('1');
$radio->setBreakSpace('</p><p>');
//$radio->extra = 'disabled';
if ($mode == 'edit') {
    $radio->setSelected($formdata['a2']);
}
if ($mode == "fixup") {
    $radio->setSelected($a2);
}
$radio->cssClass = "required";
$table->startRow();
$table->addCell("A.2. This is a:");
$table->endRow();

$table->startRow();
$table->addCell($radio->show());
$table->endRow();

$textarea = new textarea('a3');
$textarea->height = '70px';
$textarea->cols = 100;
//$textarea->extra = 'readonly';
if ($mode == "fixup"&& $document['userid'] != $document['currentuserid']) {
    $textarea->value = $a3;
   
}
if ($mode == "edit") {
    $textarea->value = $formdata['a3'];
    //$textarea->extra = 'readonly';
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("A.3. Provide a brief motivation for the introduction/amendment of the course/unit:");
$table->endRow();
$table->startRow();
$table->addCell('<em>*</em>' . $textarea->show());
$table->endRow();

$textarea = new textarea('a4');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols = 100;
//$textarea->extra = 'readonly';
if ($mode == "fixup") {
    $textarea->value = $a4;
}
if ($mode == "edit") {
    $textarea->value = $formdata['a4'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("A.4. Towards which qualification(s) can the course/unit be taken?");
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>' . $textarea->show());
$table->endRow();

$radio = new radio('a5');
$radio->addOption('1', "linked to other recent course/unit proposal/s, or proposal/s currently in development");
$radio->addOption('2', "linked to other recent course/unit amendment/s, or amendment/s currently in development");
$radio->addOption('3', "linked to a new qualification/ programme proposal, or one currently in development");
$radio->addOption('4', "linked to a recent qualification/ programme amendment, or one currently in development");
$radio->addOption('5', "not linked to any other recent academic developments, nor those currently in development");
$radio->setSelected('1');
//$radio->extra = 'disabled';
$radio->setBreakSpace('</p><p>');
if ($mode == "fixup") {
    $radio->setSelected($a5);
}
if ($mode == "edit") {
    $radio->setSelected($formdata['a5']);
}
$radio->cssClass = "required";
$table->startRow();
$table->addCell("A.5. This new or amended course proposal is:", "100");
$table->endRow();

$table->startRow();
$table->addCell($radio->show());
$table->endRow();

$legend = "<b>Section A: Overview</b>";
$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());
$form->addToForm($fs->show());

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
//$uri = $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'overview', 'toform'=>'rulesandsyllabusone'));
//$button->setOnClick('javascript: window.location=\'' . $uri . '\'');

$form->addToForm('<br/>' . $button->show() . '&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showeditdocument', 'id' => $id, 'formname' => 'overview', 'toform' => 'addeditdocument'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
//$button->setToSubmit();
$form->addToForm($button->show() . '&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show() . '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_forward', 'apo', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action' => 'forwarding', 'from' => 'overview_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>
