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
$this->setVar('JQUERY_VERSION', '1.4.2');
$this->objformdata = $this->getObject('dbformdata');
$this->setVar('pageSuppressXML', TRUE);
$this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');

$formdata = $this->objformdata->getFormData("rulesandsyllabustwo", $id);
if ($formdata != null){
    $mode = "edit";
}

$action = 'showsubsidyrequirements';
$form = new form('rulesandsyllabustwoform', $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'rulesandsyllabustwo')));

$xtitle = $this->objLanguage->languageText('mod_wicid_document', 'wicid', 'Section B: Rules and Syllabus');

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
        $rulesandsyllabusonelink->show() . '&nbsp;|&nbsp;' . "<b>Rules and Syllabus - Page Two</b>" . '&nbsp;|&nbsp;' .
        $subsidyrequirementslink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentonelink->show() . '&nbsp;|&nbsp;' .
        $outcomesandassessmenttwolink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentthreelink->show() . '&nbsp;|&nbsp;' .
        $resourceslink->show() . '&nbsp;|&nbsp;' . $collaborationandcontractslink->show() . '&nbsp;|&nbsp;' .
        $reviewlink->show() . '&nbsp;|&nbsp;' . $contactdetailslink->show() .'&nbsp;|&nbsp;' . $feedbacklink->show() . '&nbsp;|&nbsp;' .
        $commentslink->show() . '<br/>';

$fs = new fieldset();
$fs->setLegend('<b>Navigation</b>');
$fs->addContent($links);

echo $fs->show();

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
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'rulesandsyllabustwo_tpl.php', 'id'=>$id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

$table = $this->newObject('htmltable', 'htmlelements');

$radio = new radio ('b5a');
$radio->addOption('1'," a 1st year unit");
$radio->addOption('2',"a 2nd year unit");
$radio->addOption('3',"a 3rd year unit");
$radio->addOption('4',"a 4th year unit");
$radio->addOption('5',"a 5th year unit");
$radio->addOption('6',"a 6th year unit");
$radio->addOption('7',"an honours unit");
$radio->addOption('8',"a postgraduate diploma unit");
$radio->addOption('9',"a masters unit");
$radio->setSelected('1');
$radio->setBreakSpace('</p><p>');
if ($mode == "fixup") {
    $radio->setSelected($b5a);
}
if ($mode == "edit") {
    $radio->setSelected($formdata['b5a']);
}
$table->startRow();
$table->addCell("B.5.a. At what level is the course/unit taught?");
$table->endRow();

$table->startRow();
$table->addCell($radio->show());
$table->endRow();

$textarea = new textarea('b5b');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols=100;
if ($mode == "fixup") {
    $textarea->value = $b5b;
}
if ($mode == "edit") {
    $textarea->value = $formdata['b5b'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("B.5.b. In which year/s of study is the course/unit to be taught? ");
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$radio = new radio ('b6a');
$radio->addOption('1',"full year unit offered in semester 1 and 2");
$radio->addOption('2',"half year unit offered in semester 1");
$radio->addOption('3',"half year unit offered in semester 2");
$radio->addOption('4',"half year unit offered in semester 1 and 2");
$radio->addOption('5',"block unit offered in block 1");
$radio->addOption('6',"block unit offered in block 2");
$radio->addOption('7',"block unit offered in block 3");
$radio->addOption('8',"block unit offered in block 4");
$radio->addOption('9',"attendance course/unit");
$radio->addOption('9',"other");
$radio->setSelected('1');
$radio->setBreakSpace('</p><p>');
if ($mode == "fixup") {
    $radio->setSelected($b6a);
}
if ($mode == "edit") {
    $radio->setSelected($formdata['b6a']);
}
$table->startRow();
$table->addCell("B.6.a. This is a:");
$table->endRow();

$table->startRow();
$table->addCell($radio->show());
$table->endRow();

$textarea = new textarea('b6b');
$textarea->height = '70px';
$textarea->width = '500px';
$textarea->cols=100;
if ($mode == "fixup") {
    $textarea->value = $b6b;
}
if ($mode == "edit") {
    $textarea->value = $formdata['b6b'];
}
$textarea->setCssClass("required");
$table->startRow();
$table->addCell("B.6.b. If ‘other’, provide details of the course/unit duration and/or the number of lectures which comprise the course/unit:");
$table->endRow();

$table->startRow();
$table->addCell('<em>*</em>'.$textarea->show());
$table->endRow();

$radio = new radio ('b6c');
$radio->addOption('y',"yes");
$radio->addOption('n',"no");
$radio->setSelected('y');
$radio->setBreakSpace('</p><p>');
if ($mode == "fixup") {
    $radio->setSelected($b6c);
}
if ($mode == "edit") {
    $radio->setSelected($formdata['b6c']);
}
//$radio->cssClass = "required";
$table->startRow();
$table->addCell("B.6.c.Is the unit assessed:");
$table->endRow();

$table->startRow();
$table->addCell(/*'<em>*</em>'.*/$radio->show());
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

$legend = "<b>Section B: Rules and Syllabus - Page Two</b>";
$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());
$form->addToForm('<br/>'.$fs->show());

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
$uri = $this->uri(array('action'=>'forwarding', 'from' => 'rulesandsyllabustwo_tpl.php', 'id'=>$id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>

