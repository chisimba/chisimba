<?php

/*
 * @category  Chisimba
 * @package   apo (Academic Planning Office)
 * @author    Jacqueline Gil and Palesa Mokwena
 */

$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('iframe', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->objformdata = $this->getObject('dbformdata');
$this->setVar('pageSuppressXML', TRUE);
$this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');

$formdata = $this->objformdata->getFormData("comments", $id);
if ($formdata != null){
    $mode = "edit";
}

$action = 'finishdocument';

$form = new form('commentsform', $this->uri(array('action' => $action, 'id' => $id, 'formname' => 'comments')));

$xtitle = $this->objLanguage->languageText('mod_wicid_document', 'wicid', 'Comment Page for the Academic Planning Office');

$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;

echo $header->show();

$doclink = new link($this->uri(array("action" => "editdocument", 'id' => $id, 'formname' => 'comments')));
$doclink->link = "Document";

$overviewlink = new link($this->uri(array("action" => "showoverview")));
$overviewlink->link = "Overview";

$rulesandsyllabusonelink = new link($this->uri(array("action" => "showrulesandsyllabusone", 'id' => $id, 'formname' => 'comments')));
$rulesandsyllabusonelink->link = "Rules and Syllabus - Page One";

$rulesandsyllabustwolink = new link($this->uri(array("action" => "showrulesandsyllabustwo", 'id' => $id, 'formname' => 'comments')));
$rulesandsyllabustwolink->link = "Rules and Syllabus - Page Two";

$subsidyrequirementslink = new link($this->uri(array("action" => "showsubsidyrequirements", 'id' => $id, 'formname' => 'comments')));
$subsidyrequirementslink->link = "Subsidy Requirements";

$outcomesandassessmentonelink = new link($this->uri(array("action" => "showoutcomesandassessmentone", 'id' => $id, 'formname' => 'comments')));
$outcomesandassessmentonelink->link = "Outcomes and Assessment - Page One";

$outcomesandassessmenttwolink = new link($this->uri(array("action" => "showoutcomesandassessmenttwo", 'id' => $id, 'formname' => 'comments')));
$outcomesandassessmenttwolink->link = "Outcomes and Assessment - Page Two";

$outcomesandassessmentthreelink = new link($this->uri(array("action" => "showoutcomesandassessmentthree", 'id' => $id, 'formname' => 'comments')));
$outcomesandassessmentthreelink->link = "Outcomes and Assessment - Page Three";

$resourceslink = new link($this->uri(array("action" => "showresources", 'id' => $id, 'formname' => 'comments')));
$resourceslink->link = "Resources";

$collaborationandcontractslink = new link($this->uri(array("action" => "showcollaborationandcontracts", 'id' => $id, 'formname' => 'comments')));
$collaborationandcontractslink->link = "Collaboration and Contracts";

$reviewlink = new link($this->uri(array("action" => "showreview", 'id' => $id, 'formname' => 'comments')));
$reviewlink->link = "Review";

$contactdetailslink = new link($this->uri(array("action" => "showcontactdetails", 'id' => $id, 'formname' => 'comments')));
$contactdetailslink->link = "Contact Details";

$feedbacklink = new link($this->uri(array("action" => "showfeedback","id"=>$id)));
$feedbacklink->link = "Feedback";

$commentslink = new link($this->uri(array("action" => "showcomments","id"=>$id)));
$commentslink->link = "Comments";


$links = $doclink->show() . '&nbsp;|&nbsp;' . $overviewlink->show() . '&nbsp;|&nbsp;' .
        $rulesandsyllabusonelink->show() . '&nbsp;|&nbsp;' . $rulesandsyllabustwolink->show() . '&nbsp;|&nbsp;' .
        $subsidyrequirementslink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentonelink->show() . '&nbsp;|&nbsp;' .
        $outcomesandassessmenttwolink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentthreelink->show() . '&nbsp;|&nbsp;' .
        $resourceslink->show() . '&nbsp;|&nbsp;' . $collaborationandcontractslink->show() . '&nbsp;|&nbsp;' .
        $reviewlink->show() . '&nbsp;|&nbsp;' . $contactdetailslink->show() . '&nbsp;|&nbsp;' .$feedbacklink->show()  .
        '&nbsp;|&nbsp;' ."<b>Comments</b>"  . '<br/>';

$fs = new fieldset();
$fs->setLegend('<b>Navigation</b>');
$fs->addContent($links);

echo $fs->show() . '<br/>';

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
//$uri = $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'overview', 'toform'=>'rulesandsyllabusone'));
//$button->setOnClick('javascript: window.location=\'' . $uri . '\'');

$form->addToForm($button->show().'&nbsp');


$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showcontactdetails', 'id' => $id, 'formname' => 'comment', 'toform' => 'addeditdocument'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
//$button->setToSubmit();
$form->addToForm($button->show().'&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show().'&nbsp');

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'comments_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());


$table = $this->newObject('htmltable', 'htmlelements');

$textarea = new textarea('apo');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $apo;
}
if ($mode == "edit") {
    $textarea->value = $formdata['apo'];
}
$table->startRow();
$table->addCell("APO Comments:");
$table->endRow();

$table->startRow();
$table->addCell($textarea->show());
$table->endRow();

$textarea = new textarea('subsidy');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $subsidy;
}
if ($mode == "edit") {
    $textarea->value = $formdata['subsidy'];
}
$table->startRow();
$table->addCell("Subsidy Comments:");
$table->endRow();

$table->startRow();
$table->addCell($textarea->show());
$table->endRow();

$textarea = new textarea('library');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $library;
}
if ($mode == "edit") {
    $textarea->value = $formdata['library'];
}
$table->startRow();
$table->addCell("Library Comments (For Library use Only):");
$table->endRow();

$table->startRow();
$table->addCell($textarea->show());
$table->endRow();

$textarea = new textarea('legal');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $legal;
}
if ($mode == "edit") {
    $textarea->value = $formdata['legal'];
}
$table->startRow();
$table->addCell("Legal Office Comments (If neccessary):");
$table->endRow();

$table->startRow();
$table->addCell($textarea->show());
$table->endRow();

$textarea = new textarea('faculty');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $faculty;
}
if ($mode == "edit") {
    $textarea->value = $formdata['faculty'];
}
$table->startRow();
$table->addCell("Teaching and Learning Committe Comments:");
$table->endRow();

$table->startRow();
$table->addCell($textarea->show());
$table->endRow();

$textarea = new textarea('teaching');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $faculty;
}
if ($mode == "edit") {
    $textarea->value = $formdata['teaching'];
}
$table->startRow();
$table->addCell("Faculty Board Comments:");
$table->endRow();

$table->startRow();
$table->addCell($textarea->show());
$table->endRow();

$legend = "<b>Comments</b>";
$fs = new fieldset();
$fs->width = 800;
$fs->setLegend($legend);
$fs->addContent($table->show());
$form->addToForm($fs->show());

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
//$uri = $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'overview', 'toform'=>'rulesandsyllabusone'));
//$button->setOnClick('javascript: window.location=\'' . $uri . '\'');

$form->addToForm('<br/>' . $button->show().'&nbsp');


$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showcontactdetails', 'id' => $id, 'formname' => 'comment', 'toform' => 'addeditdocument'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
//$button->setToSubmit();
$form->addToForm($button->show().'&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show().'&nbsp');

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'sendDoc', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');

/*$sendDocText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Send to APO');

$button = new button('sendDoc', $sendDocText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'comments_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());*/

echo $form->show();
?>
