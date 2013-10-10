<?php
 /*
 * @category  Chisimba
 * @package   apo (Academic Planning Office)
 * @author    Palesa Mokwena
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
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('radio','htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('href', 'htmlelements');
$this->loadClass('iframe', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->setVar('JQUERY_VERSION', '1.4.2');
$this->loadClass('dropdown', 'htmlelements');

$this->setVar('pageSuppressXML', TRUE);

$formdata = $this->objformdata->getFormData("subsidyrequirements", $id);
if ($formdata != null){
    $mode = "edit";
}

$faculty = $this->documents->getFaculty($id);
if ($faculty == "Science") {
    $action = 'showoutcomesandassessmentoneScience';
} else {
    $action = 'showoutcomesandassessmentone';
}

$form = new form('subsidyrequirementsform', $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'subsidyrequirements')));

$xtitle = $this->objLanguage->languageText('mod_wicid_document', 'wicid', 'Section C: Subsidy Requirements');

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
        $rulesandsyllabusonelink->show() . '&nbsp;|&nbsp;' . $rulesandsyllabustwolink->show() . '&nbsp;|&nbsp;' .
        "<b>Subsidy Requirements</b>" . '&nbsp;|&nbsp;' . $outcomesandassessmentonelink->show() . '&nbsp;|&nbsp;' .
        $outcomesandassessmenttwolink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentthreelink->show() . '&nbsp;|&nbsp;' .
        $resourceslink->show() . '&nbsp;|&nbsp;' . $collaborationandcontractslink->show() . '&nbsp;|&nbsp;' .
        $reviewlink->show() . '&nbsp;|&nbsp;' . $contactdetailslink->show() .'&nbsp;|&nbsp;' . $feedbacklink->show() . '&nbsp;|&nbsp;' .
        $commentslink->show() . '<br/>';

$fs = new fieldset();
$fs->setLegend('<b>Navigation</b>');
$fs->addContent($links);

echo $fs->show();

/* if ($mode == 'edit') {
  $legend = "Edit document";
  } */

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
$form->addToForm('<br/>'.$button->show().'&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showrulesandsyllabusone', 'id' => $id, 'formname'=>'rulesandsyllabustwo'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show().'&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home', 'id' => $id, 'formname'=>'overview'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show().'&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'subsidyrequirements_tpl.php', 'id'=>$id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

$table = $this->newObject('htmltable', 'htmlelements');

$textarea = new textarea('c1');
$textarea->cols=100;
if ($mode == "fixup") {
    $textarea->value = $c1;
}
if ($mode == "edit") {
    $textarea->value = $formdata['c1'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('C.1. The mode of instruction is understood to be contact/face-to-face lecturing. Provide details if any other mode of delivery is to be used:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$radio = new radio ('c2a');
$radio->addOption('1',"off-campus");
$radio->addOption('2',"on-campus");
$radio->setSelected('1');
if ($mode == "fixup") {
    $radio->setSelected($c2a);
}
if ($mode == "edit") {
    $radio->setSelected($formdata['c2a']);
}
$radio->setBreakSpace('</p><p>');
if ($mode == "fixup") {
    $textarea->value = $c2a;
}
if ($mode == "edit") {
    $textarea->value = $formdata['c2a'];
}
$table->startRow();
$table->addCell("C.2.a. The course/unit is taught:");
$table->endRow();

$table->startRow();
$table->addCell($radio->show());
$table->endRow();


$textarea = new textarea('c2b');
$textarea->cols=100;
if ($mode == "fixup") {
    $textarea->value = $c2b;
}
if ($mode == "edit") {
    $textarea->value = $formdata['c2b'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('C.2.b. If the course/unit is taught off-campus provide details:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

//Section C.3.

$textinput= new textinput('c3');
$maxlength = "6";
$c3->label='CEMS (must be 6 characters)';
$form->addRule(array('name'=>'c3','length'=>6), 'Check CESM manual','maxlength');
$textinput->size = 100;
if ($mode == "fixup") {
    $textinput->value = $c3;
}
if ($mode == "edit") {
    $textinput->value = $formdata['c3'];
}
//$textinput->setExtra("maxlength = 6");
$textinput->setCss("required");
//$cesmLink= $this->href('http://intranet.wits.ac.za/Academic/APO/CESMs.html');

$table->startRow();
$table->addCell('C.3. What is the third order CESM (Classification of Education Subject Matter) category for the course/unit? (The CESM manual can be downloaded from http://intranet.wits.ac.za/Academic/APO/CESMs.html', '100');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textinput->show());
$table->endRow();

//Section C.4.
$radio = new radio ('c4a');
$radio->addOption('1',"Yes");
$radio->addOption('2',"No");
$radio->setSelected('1');
if ($mode == "fixup") {
    $radio->setSelected($c4a);
}
if ($mode == "edit") {
    $radio->setSelected($formdata['c4a']);
}
$radio->setBreakSpace('</p><p>');
if ($mode == "fixup") {
    $textarea->value = $c4a;
}
if ($mode == "edit") {
    $textarea->value = $formdata['c4a'];
}
$table->startRow();
$table->addCell("C.4.a. Is any other School/Entity involved in teaching this unit?:");
$table->endRow();

$table->startRow();
$table->addCell($radio->show());
$table->endRow();


$textarea = new textarea('c4b1');
$textarea->size = 60;
$textarea->cols=100;
if ($mode == "fixup") {
    $textarea->value = $c4b1;
}
if ($mode == "edit") {
    $textarea->value = $formdata['c4b1'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('C.4.b. If yes, state the name of the School/Entity:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('c4b2');
$textarea->size = 60;
$textarea->cols=100;
if ($mode == "fixup") {
    $textarea->value = $c4b2;
}
if ($mode == "edit") {
    $textarea->value = $formdata['c4b2'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('Percentage each teaches.:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$legend = "<b>Section C: Subsidy Requirements</b>";

$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());
//echo $fs->show();
$form = new form('subsidyrequirementsform', $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'subsidyrequirements')));

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

$form->addToForm($fs->show());


$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
$form->addToForm('<br/>'.$button->show(). '&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showrulesandsyllabustwo', 'id' => $id));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'subsidyrequirements_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>
