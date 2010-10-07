<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
if($mode=='edit'){
    $noLO = count($contextLO);
} else {
    $noLO = 1;
}
//Append JS for creating multiple text input boxes
$this->appendArrayVar('headerParams', '
<script type="text/javascript">
//Script Source: http://bit.ly/cKzGb8
function CreateInputs(selectbox){
    var amountInputs = selectbox.value; // or selectbox.elements[selectbox.selectedIndex].value
    var x = document.getElementById("input_outcomesCount");
    x = Math.abs(x.value);
    var y = Math.abs(amountInputs);
    var z = x + y - 1;
    var txtHTML = "";
    var j='.$noLO.';
    count = 1;
    j = j+x;
    //Do a loop, to create the specified amount of input boxes:
    for (var i=x;i<=z;i++)
    {
        h = i + 1;
        txtHTML += "<tr><td valign=\"top\">"+j+".</td><td valign=\"top\"> <textarea name=\"input_learneroutcome" + count + "\" rows=\"2\" cols=\"100\"></textarea></td></tr>";
        count++;
        j++;
    }
    document.getElementById("dropdown4lo").innerHTML = "<input type=\"hidden\" name=\"lodrops\" id=\"input_lodrops\" value="+z+">";
    document.getElementById("countDiv").innerHTML = "<input type=\"hidden\" name=\"outcomesCount\" id=\"input_outcomesCount\" value="+z+">";
    document.getElementById("textBoxes").innerHTML += "<table cellspacing=\"0\" cellpadding=\"0\" width=\"99%\">"+txtHTML+"</table>";
}
 </script>');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');

$formAction = 'savestep3';
$headerTitle = $context['title'].' - '.$this->objLanguage->code2Txt('mod_contextadmin_outcomes', 'contextadmin', NULL, '[-context-] Outcomes');
$formButton = $this->objLanguage->languageText('mod_contextadmin_gotonextstep', 'contextadmin', 'Go to Next Step');
$deleteLOButton = $this->objLanguage->languageText('mod_contextadmin_deleteselected', 'contextadmin', 'Delete Selected');



$objStepMenu = $this->newObject('stepmenu', 'navigation');
if ($mode == 'edit') {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), ucwords($this->objLanguage->code2Txt('mod_contextadmin_updatecontextitlesettings', 'contextadmin', NULL, 'Update [-context-] Title and Settings')));
} else {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), $this->objLanguage->code2Txt('mod_contextadmin_checkcontextcodeavailable', 'contextadmin', NULL, 'Enter [-context-] settings and check whether [-context-] code is available'));
}
$objStepMenu->addStep(str_replace('[-num-]', 2, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_contextadmin_contextinformation', 'contextadmin', NULL, '[-context-] Information')), $this->objLanguage->code2Txt('mod_contextadmin_enterinfoaboutcontext', 'contextadmin', NULL, 'Enter more information about your [-context-] and select a [-context-] image'));
$objStepMenu->addStep(str_replace('[-num-]', 3, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_contextadmin_courseoutcomes', 'contextadmin', NULL, 'Course Outcomes')), $this->objLanguage->code2Txt('mod_context_enteroutcomecontext', 'contextadmin', NULL, 'Enter the main Outcomes / Goals of the [-context-]'));
$objStepMenu->addStep(str_replace('[-num-]', 4, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextpluginsabs', 'context', array('plugins'=>'plugins'), '[-context-] [-plugins-]')), $this->objLanguage->code2Txt('mod_contextadmin_selectpluginsforcontextabs', 'contextadmin', array('plugins'=>'plugins'), 'Select the [-plugins-] you would like to use in this [-context-]'));
$objStepMenu->setCurrent(3);
echo $objStepMenu->show();


$header = new htmlheading();
$header->type = 1;
$header->str = ucwords($headerTitle);

echo '<br />'.$header->show();

$objSelectImage = $this->getObject('selectimage', 'filemanager');
/*
$htmlEditor = $this->newObject('htmlarea', 'htmlelements');
$htmlEditor->name = 'goals';
if(!empty($context['goals'])){
	$htmlEditor->value = $context['goals'];
}else{
	$htmlEditor->value = "";
}
*/

//Hidden textinput to store the old outcome
$goalsHiddenTxtInput = $this->newObject('textinput', 'htmlelements');
if(!empty($context['goals'])){
    $goalsHiddenTxtInput->textinput($name="goals", $value=$context['goals'], $type='hidden', $size="10");
}else{
    $goalsHiddenTxtInput->textinput($name="goals", $value="", $type='hidden', $size="10");
}

$table = $this->newObject('htmltable', 'htmlelements');
//Set Table width
$table->width = '1300px';
$table->border = '0';
$table->startRow();
$table->addCell('<p>'.$this->objLanguage->code2Txt('mod_context_enteroutcomecontext', 'contextadmin', NULL, 'Enter the Outcomes/Goals of the [-context-]').':</p>'.$goalsHiddenTxtInput->show());
$table->endRow();
//Spacer
$table->startRow();
$table->addCell(Null);
$table->endRow();

//Add dropdown to determine no of textinput boxes for LO's
$loDrops = $this->newObject('dropdown', 'htmlelements');
$loDrops->name = 'lodrops';
$loDrops->extra = "onchange='CreateInputs(this)'";
//Add options dynamically
$howmany = 20;
$count = 0;
do {
    $loDrops->addOption($value=$count,$label=$count,$extra="");
    $count++;
} while ($count < $howmany);
//Set selected
$loDrops->setSelected(0);
//Add dropdown to table with LO drop down list
$table->startRow();
$table->addCell('<div id="dropdown4lo">'.$loDrops->show()." ".$this->objLanguage->languageText("mod_contextadmin_addlo", "contextadmin","Select the number of outcomes you want to add").'</div>');
$table->endRow();

//Hidden textinput to store the the number of new outcomes
$outcomesHiddenCount = $this->newObject('textinput', 'htmlelements');
$outcomesHiddenCount->textinput($name="outcomesCount", $value=1, $type='hidden', $size="5");
if ($mode=='edit' && (!empty($contextLO))) {
    $table->startRow();
    $table->addCell(Null);
    $count = 1;
    //Create a table to hold the LO's
    $loTable = $this->newObject('htmltable', 'htmlelements');
    $loTableClass = "odd";
$loTable->border = '0';
    //Add row with headings
    $loTable->startRow();
    $loTable->addCell(Null);
    $loTable->addCell("<b>".$this->objLanguage->languageText("mod_contextadmin_lo", "contextadmin","Learning Outcomes")."</b>", $width='60%', $valign="top", $align="left");
    $loTable->addCell("<b>".$this->objLanguage->languageText("mod_contextadmin_select2delete", "contextadmin","Select to Delete")."</b>", $width='40%', $valign="top", $align="left");
    $loTable->endRow();

    foreach ($contextLO as $thisLO){
        //Create TextArea to hold the Learning outcome
        $loTxtArea = new textarea($name='update_learneroutcome_'.$count, $value=$thisLO["learningoutcome"], $rows=2, $cols=100);
        //Checkbox to select LO to delete
        $deleteLO = new checkbox($name='delete_learneroutcome_'.$count, $label=NULL,$ischecked=false);
        //Hidden textinput to store the id of this LO
        $loHiddenTxtInput = $this->newObject('textinput', 'htmlelements');
        $loHiddenTxtInput->textinput($name=$thisLO["id"], $value='_learneroutcome_'.$count, $type='hidden', $size="10");
        //Add Items to table
        $loTable->startRow();
        $loTable->addCell($count.". ");
        $loTable->addCell($loTxtArea->show().$loHiddenTxtInput->show());
        $loTable->addCell($deleteLO->show());
        $loTable->endRow();
        $count++;
    }
    //Hidden textinput to store the no of outcomes for editing
    $loEditCount = $this->newObject('textinput', 'htmlelements');
    $loEditCount->textinput($name='loEditCount', $value=$count, $type='hidden', $size="4");
    $loTable->startRow();
    $loTable->addCell(Null);
    $loTable->addCell($loEditCount->show());
    $loTable->addCell(Null);
    $loTable->endRow();
    $table->startRow();
    $table->addCell('<div id="textBoxes1">'.$loTable->show().'</div>', Null, 'top');
    $table->endRow();
}

$table->startRow();
$table->addCell('<div id="textBoxes"></div>');
$table->endRow();
//Spacer
$table->startRow();
$table->addCell(Null);
$table->endRow();
//Spacer
$table->startRow();
$table->addCell('<div id="countDiv">'.$outcomesHiddenCount->show().'</div>');
$table->endRow();

$button = new button ('savecontext', $formButton);
$button->setToSubmit();

$delButton = new button ('deleteoutcomes', $deleteLOButton);
$delButton->setToSubmit();


$form = new form ('createcontext', $this->uri(array('action'=>$formAction)));

// Fixed Ticket #3128 J C O'Connor
//$backUri = $this->uri(array('action'=>'edit','contextcode'=>$contextCode),'contextadmin');
$backUri = $this->uri(array('action'=>'step2','mode'=>'edit','contextcode'=>$contextCode),'contextadmin');
$backButton = new button('back', $this->objLanguage->languageText('word_back'),"document.location='$backUri'");

$form->addToForm($table->show());
$form->addToForm($backButton->show()." ".$delButton->show()." ".$button->show());

$hiddenInput = new hiddeninput('mode', $mode);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('contextCode', $contextCode);
$form->addToForm($hiddenInput->show());

echo $form->show();

?>
