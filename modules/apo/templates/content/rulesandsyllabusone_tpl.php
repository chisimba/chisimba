<?php
/*
 * @category  Chisimba
 * @package   apo (Academic Planning Office)
 * @author    Jacqueline Gil
 */
$validatorjs = '<script type="text/javascript" src="'.$this->getResourceURI('js/jquery.validate.js').'"></script>';
$sectionsjs = '<script type="text/javascript" src="'.$this->getResourceURI('js/sections.js').'"></script>';
$sectionscss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceURI('css/sections.css').'" media="screen">';

$this->appendArrayVar("headerParams", $validatorjs);
$this->appendArrayVar("headerParams", $sectionsjs);
$this->appendArrayVar("headerParams", $sectionscss);

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('iframe', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->objformdata = $this->getObject('dbformdata');
$this->setVar('pageSuppressXML', TRUE);
$this->setVar('JQUERY_VERSION', '1.4.2');
$this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');

$formdata = $this->objformdata->getFormData("rulesandsyllabusone", $id);
if ($formdata != null){
    $mode = "edit";
}

$action = 'showrulesandsyllabustwo';
$form = new form('rulesandsyllabusoneform', $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'rulesandsyllabusone')));

$xtitle = $this->objLanguage->languageText('mod_wicid_document', 'wicid', 'Section B: Rules and Syllabus');

$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;

echo $header->show();

$doclink = new link($this->uri(array("action" => "editdocument","id"=>$id)));
$doclink->link = "Document";

$overviewlink = new link($this->uri(array("action" => "showoverview","id"=>$id)));
$overviewlink->link = "Overview";

$rulesandsyllabustwolink = new link($this->uri(array("action" => "showrulesandsyllabustwo","id"=>$id)));
$rulesandsyllabustwolink->link = "Rules and Syllabus - Page Two";

$subsidyrequirementslink = new link($this->uri(array("action" => "showsubsidyrequirements","id"=>$id)));
$subsidyrequirementslink->link = "Subsidy Requirements";

$outcomesandassessmentonelink = new link($this->uri(array("action" => "showoutcomesandassessmentone","id"=>$id)));
$outcomesandassessmentonelink->link = "Outcomes and Assessment - Page One";

$outcomesandassessmenttwolink = new link($this->uri(array("action" => "showoutcomesandassessmenttwo","id"=>$id)));
$outcomesandassessmenttwolink->link = "Outcomes and Assessment - Page Two";

$outcomesandassessmentthreelink = new link($this->uri(array("action" => "showoutcomesandassessmentthree","id"=>$id)));
$outcomesandassessmentthreelink->link = "Outcomes and Assessment - Page Three";

$resourceslink = new link($this->uri(array("action" => "showresources","id"=>$id)));
$resourceslink->link = "Resources";

$collaborationandcontractslink = new link($this->uri(array("action" => "showcollaborationandcontracts","id"=>$id)));
$collaborationandcontractslink->link = "Collaboration and Contracts";

$reviewlink = new link($this->uri(array("action" => "showreview","id"=>$id)));
$reviewlink->link = "Review";

$contactdetailslink = new link($this->uri(array("action" => "showcontactdetails","id"=>$id)));
$contactdetailslink->link = "Contact Details";

$commentslink = new link($this->uri(array("action" => "showcomments","id"=>$id)));
$commentslink->link = "Comments";

$feedbacklink = new link($this->uri(array("action" => "showfeedback","id"=>$id)));
$feedbacklink->link = "Feedback";

$links = $doclink->show() . '&nbsp;|&nbsp;' . $overviewlink->show() . '&nbsp;|&nbsp;' .
        "<b>Rules and Syllabus - Page One</b>" . '&nbsp;|&nbsp;' . $rulesandsyllabustwolink->show() . '&nbsp;|&nbsp;' .
        $subsidyrequirementslink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentonelink->show() . '&nbsp;|&nbsp;' .
        $outcomesandassessmenttwolink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentthreelink->show() . '&nbsp;|&nbsp;' .
        $resourceslink->show() . '&nbsp;|&nbsp;' . $collaborationandcontractslink->show() . '&nbsp;|&nbsp;' .
        $reviewlink->show() . '&nbsp;|&nbsp;' . $contactdetailslink->show() .'&nbsp;|&nbsp;' . $feedbacklink->show() . '&nbsp;|&nbsp;' .
        $commentslink->show();

$fs = new fieldset();
$fs->setLegend('<b>Navigation</b>');
$fs->addContent($links);

echo $fs->show();

$table = $this->newObject('htmltable', 'htmlelements');

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
$form->addToForm('<br/>'.$button->show(). '&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showoverview', 'id' => $id, 'formname'=>'rulesandsyllabusone'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));;
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'rulesandsyllabusone_tpl.php', 'id'=>$id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show().'<br/>');

$textarea = new textarea('b1');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols=100;
//$textarea->extra = 'readonly';
if ($mode == "fixup") {
    $textarea->value = $b1;
}
if ($mode == "edit") {
    $textarea->value = $formdata['b1'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("B.1. How does this course/unit change the rules for the curriculum?");
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('b2');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols=100;
//$textarea->extra = 'readonly';
if ($mode == "fixup") {
    $textarea->value = $b2;
}
if ($mode == "edit") {
    $textarea->value = $formdata['b2'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("B.2. Describe the course/unit syllabus:");
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>' . $textarea->show());
$table->endRow();

$textarea = new textarea('b3a');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols = 100;
//$textarea->extra = 'readonly';
if ($mode == "fixup") {
    $textarea->value = $b3a;
}
if ($mode == "edit") {
    $textarea->value = $formdata['b3a'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("B.3. a. What are the pre-requisites for the course/unit if any?");
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>' . $textarea->show());
$table->endRow();

$textarea = new textarea('b3b');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols = 100;
//$textarea->extra = 'readonly';
if ($mode == "fixup") {
    $textarea->value = $b3b;
}
if ($mode == "edit") {
    $textarea->value = $formdata['b3b'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("B.3.b. What are the co-requisites for the course/unit if any?");
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>' . $textarea->show());
$table->endRow();

$radio = new radio('b4a');
$radio->addOption('1', "a compulsory course/unit");
$radio->addOption('2', "an optional course/unit");
$radio->addOption('3', "both compulsory and optional as the course/unit is offered toward qualifications/ programmes with differing curriculum structures ");
$radio->setSelected('1');
$radio->setBreakSpace('</p><p>');
//$radio->extra = 'disabled';
if ($mode == "fixup") {
    $radio->setSelected($b4a);
}
if ($mode == "edit") {
    $radio->setSelected($formdata['b4a']);
}
$table->startRow();
$table->addCell("B.4.a. This is:");
$table->endRow();

$table->startRow();
$table->addCell($radio->show());
$table->endRow();

$textarea = new textarea('b4b');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols=100;
//$textarea->extra = 'readonly';
if ($mode == "fixup") {
    $textarea->value = $b4b;
}
if ($mode == "edit") {
    $textarea->value = $formdata['b4b'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("B.4.b. If it is a compulsory course/unit, which course/unit is it replacing, or is the course/unit to be taken by students in addition to the current workload of courses/unit?");
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('b4c');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols=100;
//$textarea->extra = 'readonly';
if ($mode == "fixup") {
    $textarea->value = $b4c;
}
if ($mode == "edit") {
    $textarea->value = $formdata['b4c'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("B.4.c. If it is both a compulsory and optional course/unit, provide details explaining for which qualifications/ programmes the course/unit would be optional and for which it would be compulsory:");
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$efs = new fieldset();
$efs->setLegend('Errors');
if (count($errormessages) > 0) {

    $errorstr = '<ul>';

    foreach ($errormessages as $errormessage) {
        $errorstr.='<li class="error">' . $errormessage . '<li/>';
    }
    $errorstr.='</li>';
    $efs->addContent($errorstr);
    $form->addToForm($efs);
}

$legend = "<b>Section B: Rules and Syllabus - Page One</b>";
$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());
$form->addToForm('<br/>'.$fs->show());



$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
$form->addToForm('<br/>'.$button->show(). '&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showoverview', 'id' => $id, 'formname'=>'rulesandsyllabusone'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));;
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'rulesandsyllabusone_tpl.php', 'id'=>$id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>