<?php

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
$this->loadClass('checkbox', 'htmlelements');
$this->setVar('pageSuppressXML', TRUE);

$formdata = $this->objformdata->getFormData("outcomesandassessmenttwo", $id);

if ($formdata['id1'] == '0' && $formdata['id2'] == '0' && $formdata['id3'] == '0' && $formdata['id4'] == '0' && $formdata['id5'] == '0' && $formdata['id6'] == '0' && $formdata['id7'] == '0' && $formdata['id8'] == '0') {
    $mode = "new";
} else {
    $mode = "edit";
}


///if ($formdata['id1'] = '0' & $formdata['id2'] = '0' & $formdata['id3'] = '0'& $formdata['id4'] = '0'&& $formdata['id5'] = '0'&& $formdata['id6'] = '0'&& $formdata['id7'] = '0'& $formdata['id8'] = '0'){
// $mode = "new";
//}/else{
// $mode = "edit";
//}
//foreach ($formdata as $data) {
//            $data["id" . $formdata] = "0";
//        }

print_r($mode);
//die();

$faculty = $this->documents->getFaculty($id);
if ($faculty == "Science") {
    $action = 'showoutcomesandassessmentthreeScience';
} else {
    $action = 'showoutcomesandassessmentthree';
}

$form = new form('outcomesandassessmenttwoform', $this->uri(array('action' => $action, 'id' => $id, 'formname' => 'outcomesandassessmenttwo')));

$xtitle = $this->objLanguage->languageText('mod_wicid_newdocument', 'wicid', 'Section D: Outcomes and Assessments - Page Two');

$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;

echo $header->show();

$doclink = new link($this->uri(array("action" => "editdocument", "id" => $id)));
$doclink->link = "Document";

$overviewlink = new link($this->uri(array("action" => "showoverview", "id" => $id)));
$overviewlink->link = "Overview";

$rulesandsyllabusonelink = new link($this->uri(array("action" => "showrulesandsyllabusone", "id" => $id)));
$rulesandsyllabusonelink->link = "Rules and Syllabus - Page One";

$rulesandsyllabustwolink = new link($this->uri(array("action" => "showrulesandsyllabustwo", "id" => $id)));
$rulesandsyllabustwolink->link = "Rules and Syllabus - Page Two";

$subsidyrequirementslink = new link($this->uri(array("action" => "showsubsidyrequirements", "id" => $id)));
$subsidyrequirementslink->link = "Subsidy Requirements";

$outcomesandassessmentonelink = new link($this->uri(array("action" => "showoutcomesandassessmentone", "id" => $id)));
$outcomesandassessmentonelink->link = "Outcomes and Assessment - Page One";

$outcomesandassessmentthreelink = new link($this->uri(array("action" => "showoutcomesandassessmentthree", "id" => $id)));
$outcomesandassessmentthreelink->link = "Outcomes and Assessment - Page Three";

$resourceslink = new link($this->uri(array("action" => "showresources", "id" => $id)));
$resourceslink->link = "Resources";

$collaborationandcontractslink = new link($this->uri(array("action" => "showcollaborationandcontracts", "id" => $id)));
$collaborationandcontractslink->link = "Collaboration and Contracts";

$reviewlink = new link($this->uri(array("action" => "showreview", "id" => $id)));
$reviewlink->link = "Review";

$contactdetailslink = new link($this->uri(array("action" => "showcontactdetails", "id" => $id)));
$contactdetailslink->link = "Contact Details";

$commentslink = new link($this->uri(array("action" => "showcomments", "id" => $id)));
$commentslink->link = "Comments";

$feedbacklink = new link($this->uri(array("action" => "showfeedback", "id" => $id)));
$feedbacklink->link = "Feedback";

$links = $doclink->show() . '&nbsp;|&nbsp;' . $overviewlink->show() . '&nbsp;|&nbsp;' .
        $rulesandsyllabusonelink->show() . '&nbsp;|&nbsp;' . $rulesandsyllabustwolink->show() . '&nbsp;|&nbsp;' .
        $subsidyrequirementslink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentonelink->show() . '&nbsp;|&nbsp;' .
        "<b>Outcomes and Assessment - Page Two</b>" . '&nbsp;|&nbsp;' . $outcomesandassessmentthreelink->show() . '&nbsp;|&nbsp;' .
        $resourceslink->show() . '&nbsp;|&nbsp;' . $collaborationandcontractslink->show() . '&nbsp;|&nbsp;' .
        $reviewlink->show() . '&nbsp;|&nbsp;' . $contactdetailslink->show() . '&nbsp;|&nbsp;' . $feedbacklink->show() . '&nbsp;|&nbsp;' .
        $commentslink->show() . '<br/>';

$fs = new fieldset();
$fs->setLegend('<b>Navigation</b>');
$fs->addContent($links);

echo $fs->show() . '<br/>';

$legend = "<b>Section D: Outcomes and Assessment - Page Two</b>";

$button = new button('next', $this->objLanguage->languageText('word_next'));
$button->setToSubmit();
$form->addToForm($button->show(). '&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
if ($faculty == "Science") {
    $uri = $this->uri(array('action' => 'showoutcomesandassessmentoneScience', 'id' => $id));
} else {
    $uri = $this->uri(array('action' => 'showoutcomesandassessmentone', 'id' => $id));
}
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding',  'from' => 'outcomesandassessmenttwo_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

$table = $this->newObject('htmltable', 'htmlelements');

$groupstoselect = array();
$groups = array();
$groups[] = array('id' => '1', 'description' => " Identify and solve problems in which responses display that responsible decisions using critical and creative thinking have been made.<br>");
$groups[] = array('id' => '2', 'description' => "Work effectively with others as a member of a team, group, organisation, community.<br>");
$groups[] = array('id' => '3', 'description' => "Organise and manage oneself and one’s activities responsibly and effectively.<br>");
$groups[] = array('id' => '4', 'description' => "Collect, analyse, organise and critically evaluate information.<br>");
$groups[] = array('id' => '5', 'description' => "Communicate effectively using visual, mathematical and/or language skills in the modes of oral and/ or written presentation.<br>");
$groups[] = array('id' => '6', 'description' => "Use science and technology effectively and critically, showing responsibility towards the environment and health of others.<br>");
$groups[] = array('id' => '7', 'description' => "Demonstrate an understanding of the world as a set of related systems by recognising that problem-solving contexts do not exist in isolation.<br>");
$groups[] = array('id' => '8', 'description' => "	In order to contribute to the full personal development of each learner and the social economic development of the society at large, it must be the intention underlying any programme of learning to make an individual aware of the importance of:
<br>- Reflecting on and exploring a variety of strategies to learn more effectively;
<br>- Participating as responsible citizens in the life of local, national and global communities;
<br>- Being culturally and aesthetically sensitive across a range of social contexts;
<br>- Exploring education and career opportunities; and
<br>- Developing entrepreneurial opportunities.");
foreach ($groups as $group) {
    $checkbox = new checkbox('groups[]', $group['id']);
    $checkbox->value = $group['id'];
    $checkbox->cssId = 'group_' . $group['id'];
    $checkbox->cssClass = 'group_option';
    if ($mode == 'edit') {

        if (in_array($group['id'], $groupstoselect)) {
            $checkbox->ischecked = TRUE;
        }
    }
    $label = new label(' ' . $group['description'], 'group_' . $group['id']);

    $groupsList .= ' ' . $checkbox->show() . $label->show() . '<br />';
}


$table->border = 2;
$table->cellpadding = '2';
$table->cellspacing = '3';

$table->startRow();
$table->addCell("D.4 Specify the critical cross-field outcomes (CCFOs) integrated into the course/unit using the list provided.:<br>");
//$table->addCell("<b>".$label->labelValue."</b>");
$table->endRow();

$table->startRow();
$table->addCell($groupsList);
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
$form->addToForm('<br/>'.$button->show(). '&nbsp');

$button = new button('back', $this->objLanguage->languageText('word_back'));
if ($faculty == "Science") {
    $uri = $this->uri(array('action' => 'showoutcomesandassessmentoneScience', 'id' => $id));
} else {
    $uri = $this->uri(array('action' => 'showoutcomesandassessmentone', 'id' => $id));
}
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show(). '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Forward');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action'=>'forwarding',  'from' => 'outcomesandassessmenttwo_tpl.php', 'id' => $id, 'mode'=> $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>

