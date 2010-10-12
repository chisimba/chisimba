<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
if ($mode == 'edit') {
    $noLO = count($contextLO);
} else {
    $noLO = 1;
}


$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');

$formAction = 'savestep3';
$headerTitle = $context['title'] . ' - ' . $this->objLanguage->code2Txt('mod_contextadmin_outcomes', 'contextadmin', NULL, '[-context-] Outcomes');
$formButton = $this->objLanguage->languageText('mod_contextadmin_gotonextstep', 'contextadmin', 'Go to Next Step');
$deleteLOButton = $this->objLanguage->languageText('mod_contextadmin_deleteselected', 'contextadmin', 'Delete Selected');
$addLOButton = $this->objLanguage->languageText('mod_contextadmin_addselected', 'contextadmin', 'Add Selected');



$objStepMenu = $this->newObject('stepmenu', 'navigation');
if ($mode == 'edit') {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), ucwords($this->objLanguage->code2Txt('mod_contextadmin_updatecontextitlesettings', 'contextadmin', NULL, 'Update [-context-] Title and Settings')));
} else {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), $this->objLanguage->code2Txt('mod_contextadmin_checkcontextcodeavailable', 'contextadmin', NULL, 'Enter [-context-] settings and check whether [-context-] code is available'));
}
$objStepMenu->addStep(str_replace('[-num-]', 2, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_contextadmin_contextinformation', 'contextadmin', NULL, '[-context-] Information')), $this->objLanguage->code2Txt('mod_contextadmin_enterinfoaboutcontext', 'contextadmin', NULL, 'Enter more information about your [-context-] and select a [-context-] image'));
$objStepMenu->addStep(str_replace('[-num-]', 3, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_contextadmin_courseoutcomes', 'contextadmin', NULL, 'Course Outcomes')), $this->objLanguage->code2Txt('mod_context_enteroutcomecontext', 'contextadmin', NULL, 'Enter the main Outcomes / Goals of the [-context-]'));
$objStepMenu->addStep(str_replace('[-num-]', 4, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_context_contextpluginsabs', 'context', array('plugins' => 'plugins'), '[-context-] [-plugins-]')), $this->objLanguage->code2Txt('mod_contextadmin_selectpluginsforcontextabs', 'contextadmin', array('plugins' => 'plugins'), 'Select the [-plugins-] you would like to use in this [-context-]'));
$objStepMenu->setCurrent(3);
echo $objStepMenu->show();


$header = new htmlheading();
$header->type = 1;
$header->str = ucwords($headerTitle);

echo '<br />' . $header->show();

$objSelectImage = $this->getObject('selectimage', 'filemanager');

//Hidden textinput to store the old outcome
$goalsHiddenTxtInput = $this->newObject('textinput', 'htmlelements');
if (!empty($context['goals'])) {
    $goalsHiddenTxtInput->textinput($name = "goals", $value = $context['goals'], $type = 'hidden', $size = "10");
} else {
    $goalsHiddenTxtInput->textinput($name = "goals", $value = "", $type = 'hidden', $size = "10");
}

$table = $this->newObject('htmltable', 'htmlelements');
//Set Table width
$table->width = '1300px';
$table->border = '0';
$table->startRow();
$table->addCell('<p>' . $this->objLanguage->code2Txt('mod_context_enteroutcomecontext', 'contextadmin', NULL, 'Enter the Outcomes/Goals of the [-context-]') . ':</p>' . $goalsHiddenTxtInput->show());
$table->endRow();
//Spacer
$table->startRow();
$table->addCell(Null);
$table->endRow();

//Add dropdown to determine no of textinput boxes for LO's
$loDrops = $this->newObject('dropdown', 'htmlelements');
$loDrops->name = 'lodrops';
//$loDrops->extra = "onchange='CreateInputs(this)'";
//Add options dynamically
$howmany = 20;
$count = 0;
do {
    $loDrops->addOption($value = $count, $label = $count, $extra = "");
    $count++;
} while ($count < $howmany);
//Set selected
$loDrops->setSelected(0);
//Add button to add fields
$button = new button('addfields', $addLOButton);
$button->setToSubmit();
//Add dropdown to table with LO drop down list
$table->startRow();
$table->addCell('<div id="dropdown4lo">' . $this->objLanguage->languageText("mod_contextadmin_addlo", "contextadmin","Select the number of outcomes you want to add").':    &nbsp;&nbsp;' . $loDrops->show() . " " . $button->show() . '</div>');
$table->endRow();

//Hidden textinput to store the the number of new outcomes
$outcomesHiddenCount = $this->newObject('textinput', 'htmlelements');
$outcomesHiddenCount->textinput($name = "outcomesCount", $value = 1, $type = 'hidden', $size = "5");
//count
$count = 1;
//Create a table to hold the LO's
$loTable = $this->newObject('htmltable', 'htmlelements');
$loTableClass = "odd";
$loTable->border = '0';
if ($outcomesCount > 1 || !empty($contextLO)) {
    //Add row with headings
    $loTable->startRow();
    $loTable->addCell(Null);
    $loTable->addCell("<b>" . $this->objLanguage->languageText("mod_contextadmin_lo", "contextadmin","Learning Outcomes") . "</b>", $width = '60%', $valign = "top", $align = "left");
    $loTable->addCell("<b>" . $this->objLanguage->languageText("mod_contextadmin_select2delete", "contextadmin","Select to Delete") . "</b>", $width = '40%', $valign = "top", $align = "left");
    $loTable->endRow();
}
if ($mode == 'edit' && (!empty($contextLO))) {
    $table->startRow();
    $table->addCell(Null);
    $table->endRow();
    foreach ($contextLO as $thisLO) {
        //Create TextArea to hold the Learning outcome
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'update_learneroutcome_' . $count;
        $editor->height = '80px';
        $editor->width = '750px';
        $editor->setContent($thisLO["learningoutcome"]);
        $editor->setMCQToolBar();
        //$loTxtArea = new textarea($name = 'update_learneroutcome_' . $count, $value = $thisLO["learningoutcome"], $rows = 2, $cols = 100);
        //Checkbox to select LO to delete
        $deleteLO = new checkbox($name = 'delete_learneroutcome_' . $count, $label = NULL, $ischecked = false);
        //Hidden textinput to store the id of this LO
        $loHiddenTxtInput = $this->newObject('textinput', 'htmlelements');
        $loHiddenTxtInput->textinput($name = $thisLO["id"], $value = '_learneroutcome_' . $count, $type = 'hidden', $size = "10");
        //Add Items to table
        $loTable->startRow();
        $loTable->addCell($count . ". ");
        $loTable->addCell($editor->show() . $loHiddenTxtInput->show());
        $loTable->addCell($deleteLO->show());
        $loTable->endRow();
        $count++;
    }
    //Hidden textinput to store the no of outcomes for editing
    $loEditCount = $this->newObject('textinput', 'htmlelements');
    $loEditCount->textinput($name = 'loEditCount', $value = $count, $type = 'hidden', $size = "4");
    $loTable->startRow();
    $loTable->addCell(Null);
    $loTable->addCell($loEditCount->show());
    $loTable->addCell(Null);
    $loTable->endRow();
    $table->startRow();
    $table->addCell('<div id="textBoxes1">' . $loTable->show() . '</div>', Null, 'top');
    $table->endRow();
}
$newcount = 1;
if ($outcomesCount != Null) {
//Create a table to hold the LO's
    $newLOTable = $this->newObject('htmltable', 'htmlelements');
    $loTableClass = "odd";
    $newLOTable->border = '0';
    $table->startRow();
    $table->addCell(Null);
    $table->endRow();
    if ($outcomesCount > 0) {
        while ($newcount <= $outcomesCount) {
            //Create TextArea to hold the Learning outcome
            $editor = $this->newObject('htmlarea', 'htmlelements');
            $editor->name = 'learneroutcome_' . $count;
            $editor->height = '100px';
            $editor->width = '750px';
            $editor->setContent(Null);
            $editor->setMCQToolBar();
            //$loTxtArea = new textarea($name = 'learneroutcome_' . $count, Null, $rows = 2, $cols = 100);
            //Checkbox to select LO to delete
            $deleteLO = new checkbox($name = 'delete_learneroutcome_' . $count, $label = NULL, $ischecked = false);
            //Add Items to table
            $newLOTable->startRow();
            $newLOTable->addCell($count . ". ");
            $newLOTable->addCell($editor->show(), $width = '60%', $valign = "top", $align = "left");
            $newLOTable->addCell($deleteLO->show(), $width = '40%', $valign = "top", $align = "left");
            $newLOTable->endRow();
            $checkcount = $count;
            //Increment the count
            $newcount++;
            $count++;
        }
        //Hidden textinput to store the no of outcomes for editing
        $loAddCount = $this->newObject('textinput', 'htmlelements');
        $newcount = $newcount - 1;
        $loAddCount->textinput($name = 'loAddCount', $value = $outcomesCount, $type = 'hidden', $size = "4");
        $newLOTable->startRow();
        $newLOTable->addCell(Null);
        $newLOTable->addCell($loAddCount->show());
        $newLOTable->addCell(Null);
        $newLOTable->endRow();

        $table->startRow();
        $table->addCell('<div id="textBoxes1">' . $newLOTable->show() . '</div>', Null, 'top');
        $table->endRow();
    }
}
//Hidden textinput to store the count of new LO's
$loHiddenLOCount = $this->newObject('textinput', 'htmlelements');
$loHiddenLOCount->textinput("outcount", $value = $outcomesCount, $type = 'hidden', $size = "10");

//Hidden textinput to store the count of new LO's
$loHiddenLODrops = $this->newObject('textinput', 'htmlelements');

$table->startRow();
$table->addCell('<div id="textBoxes"></div>');
$table->endRow();
//Spacer
$table->startRow();
$table->addCell(Null);
$table->endRow();
//Spacer
$table->startRow();
$table->addCell('<div id="countDiv">' . $loHiddenLOCount->show() . '</div>');
$table->endRow();

$button = new button('savecontext', $formButton);
$button->setToSubmit();

$delButton = new button('deleteoutcomes', $deleteLOButton);
$delButton->setToSubmit();


$form = new form('createcontext', $this->uri(array('action' => $formAction)));

// Fixed Ticket #3128 J C O'Connor
//$backUri = $this->uri(array('action' => 'step2', 'mode' => 'edit', 'contextcode' => $contextCode), 'contextadmin');
$backButton = new button('back', $this->objLanguage->languageText('word_back'));
$backButton->setToSubmit();

$form->addToForm($table->show());
$form->addToForm($backButton->show() . " " . $delButton->show() . " " . $button->show());

$hiddenInput = new hiddeninput('mode', $mode);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('contextCode', $contextCode);
$form->addToForm($hiddenInput->show());

echo $form->show();
?>