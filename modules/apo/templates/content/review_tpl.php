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

$this->setVar('pageSuppressXML', TRUE);

$formdata = $this->objformdata->getFormData("review", $id);
if ($formdata != null){
    $mode = "edit";
}

$action = 'showcontactdetails';
$form = new form('reviewform', $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'review')));

$xtitle = $this->objLanguage->languageText('mod_wicid_newdocument', 'wicid', 'Section G: Review');

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

$outcomesandassessmentthreelink = new link($this->uri(array("action" => "showoutcomesandassessmentthree","id"=>$id)));
$outcomesandassessmentthreelink->link = "Outcomes and Assessment - Page Three";

$resourceslink = new link($this->uri(array("action" => "showresources","id"=>$id)));
$resourceslink->link = "Resources";

$collaborationandcontractslink = new link($this->uri(array("action" => "showcollaborationandcontracts","id"=>$id)));
$collaborationandcontractslink->link = "Collaboration and Contracts";

$contactdetailslink = new link($this->uri(array("action" => "showcontactdetails","id"=>$id)));
$contactdetailslink->link = "Contact Details";

$commentslink = new link($this->uri(array("action" => "showcomments","id"=>$id)));
$commentslink->link = "Comments";

$feedbacklink = new link($this->uri(array("action" => "showfeedback","id"=>$id)));
$feedbacklink->link = "Feedback";

$links = $doclink->show() . '&nbsp;|&nbsp;' . $overviewlink->show() . '&nbsp;|&nbsp;' .
        $rulesandsyllabusonelink->show() . '&nbsp;|&nbsp;' . $rulesandsyllabustwolink->show() . '&nbsp;|&nbsp;' .
        $subsidyrequirementslink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentonelink->show() . '&nbsp;|&nbsp;' .
        $outcomesandassessmenttwolink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentthreelink->show() . '&nbsp;|&nbsp;' .
        $resourceslink->show() . '&nbsp;|&nbsp;' . $collaborationandcontractslink->show() . '&nbsp;|&nbsp;' .
        "<b>Review</b>" . '&nbsp;|&nbsp;' . $contactdetailslink->show() .'&nbsp;|&nbsp;' . $feedbacklink->show() . '&nbsp;|&nbsp;' .
        $commentslink->show() . '<br/>';

$fs = new fieldset();
$fs->setLegend('<b>Navigation</b>');
$fs->addContent($links);

echo $fs->show() . '<br/>';

$legend = "<b>Section G: Review</b>";


//$form = new form('reviewform', $this->uri(array('action' => 'showcontactdetails')));

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
$form->addToForm($button->show().'&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showcollaborationandcontracts', 'id' => $id));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show().'&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show().'&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'review_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

$table = $this->newObject('htmltable', 'htmlelements');

$textarea = new textarea('g1a');
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $g1a;
}
if ($mode == "edit") {
    $textarea->value = $formdata['g1a'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('G.1.a How will the course/unit syllabus be reviewed?:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('g1b');
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $g1b;
}
if ($mode == "edit") {
    $textarea->value = $formdata['g1b'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('G.1.b How often will the course/unit syllabus be reviewed?:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('g2a');
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $g2a;
}
if ($mode == "edit") {
    $textarea->value = $formdata['g2a'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('G.2.a How will integration of course/unit outcome, syllabus, teaching methods and assessment methods be evaluated?:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('g2b');
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $g2b;
}
if ($mode == "edit") {
    $textarea->value = $formdata['g2b'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('G.2.b How often will the above integration be reviewed?:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('g3a');
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $g3a;
}
if ($mode == "edit") {
    $textarea->value = $formdata['g3a'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('G.3.a How will the course/unit through-put rate be evaluated?:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('g3b');
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $g3b;
}
if ($mode == "edit") {
    $textarea->value = $formdata['g3b'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('G.3.b How often will the course/unit through-put be reviewed?:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('g4a');
$textarea->cols = 100;
if ($mode == "fixup") {
    $textarea->value = $g4a;
}
if ($mode == "edit") {
    $textarea->value = $formdata['g4a'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('G.4.a How will theteaching on the course/unit be evaluated from a students perspective and from a lectures perspective?:');
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$textarea = new textarea('g4b');
$textarea->cols = 100;

if ($mode == "fixup") {
    $textarea->value = $g4b;
}
if ($mode == "edit") {
    $textarea->value = $formdata['g4b'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell('G.4.b How often will the teaching on the course/unit be evaluated from these two perspectives?:');
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

$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());
$form->addToForm($fs->show());

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
$form->addToForm('<br/>' .$button->show().'&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
$uri = $this->uri(array('action' => 'showcollaborationandcontracts', 'id' => $id));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show().'&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show().'&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'review_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();

?>
