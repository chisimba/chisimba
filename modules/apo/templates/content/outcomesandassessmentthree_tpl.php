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

$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('iframe', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->objformdata = $this->getObject('dbformdata');
$this->setVar('pageSuppressXML', TRUE);
$this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
$id = $this->getParam('id');

$formdata = $this->objformdata->getFormData("outcomesandassessmentthree", $id);
if ($formdata != null) {
    $mode = "edit";
}

$calculate = $this->uri(array("action" => "editdocument"));

$action = 'showresources';
$form = new form('outcomesandassessmentthreeform', $this->uri(array('action' => $action, 'id' => $id, 'formname' => 'outcomesandassessmentthree')));

$xtitle = $this->objLanguage->languageText('mod_wicid_document', 'wicid', 'Section D: Outcomes and Assessment');

$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;

echo $header->show();

$doclink = new link($this->uri(array("action" => "editdocument","id"=>$id)));
$doclink->link = "Document";

$overviewlink = new link($this->uri(array("action" => "showoverview","id"=>$id)));
$overviewlink->link = "Overview";

$rulesandsyllabusonelink = new link($this->uri(array("action" => "showrulesandsyllabusone","id"=>$id)));
$rulesandsyllabusonelink->link = "Rules and Syllabus - Page One";

$rulesandsyllabustwolink = new link($this->uri(array("action" => "showrulesandsyllabustwo","id"=>$id)));
$rulesandsyllabustwolink->link = "Rules and Syllabus - Page Two";

$subsidyrequirementslink = new link($this->uri(array("action" => "showsubsidyrequirements","id"=>$id)));
$subsidyrequirementslink->link = "Subsidy Requirements";

$outcomesandassessmentonelink = new link($this->uri(array("action" => "showoutcomesandassessmentone","id"=>$id)));
$outcomesandassessmentonelink->link = "Outcomes and Assessment - Page One";

$outcomesandassessmenttwolink = new link($this->uri(array("action" => "showoutcomesandassessmenttwo","id"=>$id)));
$outcomesandassessmenttwolink->link = "Outcomes and Assessment - Page Two";

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
        $rulesandsyllabusonelink->show() . '&nbsp;|&nbsp;' . $rulesandsyllabustwolink->show() . '&nbsp;|&nbsp;' .
        $subsidyrequirementslink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentonelink->show() . '&nbsp;|&nbsp;' .
        $outcomesandassessmenttwolink->show() . '&nbsp;|&nbsp;' . "<b>Outcomes and Assessment - Page Three</b>" . '&nbsp;|&nbsp;' .
        $resourceslink->show() . '&nbsp;|&nbsp;' . $collaborationandcontractslink->show() . '&nbsp;|&nbsp;' .
        $reviewlink->show() . '&nbsp;|&nbsp;' . $contactdetailslink->show() .'&nbsp;|&nbsp;' . $feedbacklink->show() . '&nbsp;|&nbsp;' .
        $commentslink->show() . '<br/>';

$fs = new fieldset();
$fs->setLegend('<b>Navigation</b>');
$fs->addContent($links);

echo $fs->show() . '<br/>';

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
$form->addToForm( $button->show(). '&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showoutcomesandassessmenttwo', 'id' => $id));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'outcomesandassessmentthree_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

$label1 = new label();
$label1->labelValue = "<b><i>D.5. Specify the notional study hours expected for the duration of the course/unit using the spreadsheet provided.</b></i>";

$table = $this->newObject('htmltable', 'htmlelements');
$table->border = 2;
$table->cellpadding = '2';
$table->cellspacing = '3';

$textinput = new textinput('a');
$textinput->size = 5;
$textinput->value = "0";
if ($mode == "fixup") {
    $textinput->value = $a;
}
if ($mode == "edit") {
    $textinput->value = $formdata['a'];
}
$textinput->setCss("required");
$textinput->onChange = 'onChange = "' . $calculate . '"';
$table->startRow();
$table->addCell("a. Over how many weeks will this course run?",800,"top",null,null,null,1);
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

$textinput = new textinput('b');
$textinput->size = 5;
$textinput->value = "0";
if ($mode == "fixup") {
    $textinput->value = $b;
}
if ($mode == "edit") {
    $textinput->value = $formdata['b'];
}
$textinput->setCss("required");
$textinput->onChange = 'onChange = "' . $calculate . '"';
$table->startRow();
$table->addCell("b. How many hours of teaching will a particular student experience for this specific course in a single week?");
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

$textinput = new textinput('c');
$textinput->size = 5;
$textinput->value = "0";
if ($mode == "fixup") {
    $textinput->value = $c;
}
if ($mode == "edit") {
    $textinput->value = $formdata['c'];
}
$textinput->setCss("required");
$textinput->onChange = 'onChange = "' . $calculate . '"';
$table->startRow();
$table->addCell("c. How many hours of tutorials will a particular student experience for this specific course in a single week?");
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

$textinput = new textinput('d');
$textinput->size = 5;
$textinput->value = "0";
if ($mode == "fixup") {
    $textinput->value = $d;
}
if ($mode == "edit") {
    $textinput->value = $formdata['d'];
}
$textinput->setCss("required");
$textinput->onChange = 'onChange = "' . $calculate . '"';
$table->startRow();
$table->addCell("d. How many lab hours will a particular student experience for this specific course in a single week? (Note: the assumption is that there is only one staff contact hour per lab, the remaining lab time is student self-study)");
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

$textinput = new textinput('e');
$textinput->size = 5;
$textinput->value = "0";
if ($mode == "fixup") {
    $textinput->value = $e;
}
if ($mode == "edit") {
    $textinput->value = $formdata['e'];
}
$textinput->setCss("required");
$textinput->onChange = 'onChange = "' . $calculate . '"';
$table->startRow();
$table->addCell("e. How many other contact sessions are there each week including periods used for testd or other assessments which have not been included in the number of lecture, tutorial or laboratory sessions.");
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

$label = new label();
//$label->name = "totalcontacttime";
$label->forId = 'totalcontacttime';
$label->labelValue = "0";
if ($mode == "fixup") {
    $label->labelValue = $totalContactTime;
}

$table->startRow();
$table->addCell("<b>Total contact time</b>");
$table->addCell("<b>" . $label->labelValue . "</b>");
$table->endRow();

$textinput = new textinput('f');
$textinput->size = 5;
$textinput->value = "0";
if ($mode == "fixup") {
    $textinput->value = $f;
}
if ($mode == "edit") {
    $textinput->value = $formdata['f'];
}
$textinput->setCss("required");
$textinput->onChange = 'onChange = "' . $calculate . '"';
$table->startRow();
$table->addCell("f. For every hour of lectures or contact with a staff member, how many hours should the student spend studying by her/himself?");
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

$label = new label();
$label->forId = 'studyhoursnoexam';
$label->labelValue = "0";
if ($mode == "fixup") {
    $label->labelValue = $totalstudyhoursNoexam;
}
$table->startRow();
$table->addCell("<b>Total notional study hours (excluding the exams)</b>");
$table->addCell("<b>" . $label->labelValue . "</b>");
$table->endRow();

$textinput = new textinput('g');
$textinput->size = 5;
$textinput->value = "0";
if ($mode == "fixup") {
    $textinput->value = $g;
}
if ($mode == "edit") {
    $textinput->value = $formdata['g'];
}
$textinput->setCss("required");
$textinput->onChange = 'onChange = "' . $calculate . '"';
$table->startRow();
$table->addCell("g. How many exams are there per year?");
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

$textinput = new textinput('h');
$textinput->size = 5;
$textinput->value = "0";
if ($mode == "fixup") {
    $textinput->value = $h;
}
if ($mode == "edit") {
    $textinput->value = $formdata['h'];
}
$textinput->setCss("required");
$textinput->onChange = 'onChange = "' . $calculate . '"';
$table->startRow();
$table->addCell("h. How long is each exam?");
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

$label = new label();
$label->forId = 'totalexamtime';
$label->labelValue = "0";
if ($mode == "fixup") {
    $label->labelValue = $totalExamTime;
}
$table->startRow();
$table->addCell("<b>Total exam time per year</b>");
$table->addCell("<b>" . $label->labelValue . "</b>");
$table->endRow();

$textinput = new textinput('i');
$textinput->size = 5;
$textinput->value = "0";
if ($mode == "fixup") {
    $textinput->value = $i;
}
if ($mode == "edit") {
    $textinput->value = $formdata['i'];
}
$textinput->setCss("required");
$textinput->onChange = 'onChange = "' . $calculate . '"';
$table->startRow();
$table->addCell("i. How many hours of preparation for the exams is the student expected to undertake?");
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

$label = new label();
$label->forId = 'totalstudyhours';
$label->labelValue = "0";
if ($mode == "fixup") {
    $label->labelValue = $totalstudyhoursExam;
}
$table->startRow();
$table->addCell("<b>Total notional study hours</b>");
$table->addCell("<b>" . $label->labelValue . "</b>");
$table->endRow();

$label = new label();
$label->forId = 'saqa';
$label->labelValue = "0";
if ($mode == "fixup") {
    $label->labelValue = $totalSAQAcredits;
}
$table->startRow();
$table->addCell("<b>Total SAQA Credits</b>");
$table->addCell("<b>" . $label->labelValue . "</b>");
$table->endRow();

$button = new button('calculate', "Calculate");
$uri = $this->uri(array('action' => 'calculatespreedsheet', 'id' => $id, 'formname' => 'outcomesandassessmentthree'));
$action = 'javascript: window.location=\'' . $uri . '\'';
$button->setToSubmit();
$table->startRow();
$table->addCell(" ");
$table->addCell($button->show());
$table->endRow();

$efs = new fieldset();
$efs->setLegend('Errors');
if (count($errormessages) > 0) {

    $errorstr = '<ul>';

    foreach ($errormessages as $errormessage) {
        $errorstr.='<li class="error">' . $errormessage; //. '<li/>';
    }
    $errorstr.='</li>';
    $efs->addContent($errorstr);
    $form->addToForm($efs);
}

$legend = "<b>Section D: Outcomes and Assessment - Page Three</b>";
$fs = new fieldset();
$fs->width = 700;
$fs->setLegend($legend);
$fs->addContent($label1->show());
$fs->addContent($table->show());
$form->addToForm($fs->show());

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
$form->addToForm('<br/>' . $button->show(). '&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showoutcomesandassessmenttwo', 'id' => $id));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'outcomesandassessmentthree_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>

