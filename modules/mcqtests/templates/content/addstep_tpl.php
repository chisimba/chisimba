<?php
/**
 * Template for adding a new test or editing an existing one.
 * @package mcqtests
 * @param array $data The details of the test to be edited.
 * @param string $mode Add or edit
 */

// set up html elements
$objTable = $this->loadClass('htmltable', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$objForm = $this->loadClass('form', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');
$objText = $this->loadClass('textarea', 'htmlelements');
$objRadio = $this->loadClass('radio', 'htmlelements');
$objCheck = $this->loadClass('checkbox', 'htmlelements');
$objButton = $this->loadClass('button', 'htmlelements');
$objDropDown = $this->loadClass('dropdown', 'htmlelements');
$objLayer = $this->loadClass('layer', 'htmlelements');
$objLabel = $this->loadClass('label', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objPopupcal = $this->newObject('datepickajax', 'popupcalendar');
$this->objStepMenu = $this->newObject('stepmenu', 'navigation');

// header <-- no kidding! Absolutely brilliant and meaningful comment. Congrats!
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_mcqtests_addtest', 'mcqtests', 'Add a New Test');

$ret = $header->show();

// set up language items
$stepmenudescription = $this->objLanguage->languageText('mod_mcqtests_stepmenudescription', 'mcqtests');
$stepmenusettings = $this->objLanguage->languageText('mod_mcqtests_stepmenusettings', 'mcqtests');
$stepmenurestrictions = $this->objLanguage->languageText('mod_mcqtests_stepmenurestrictions', 'mcqtests');
//sub items
$stepmenusubdescription = $this->objLanguage->languageText('mod_mcqtests_stepmenusubdescription', 'mcqtests');
$stepmenusubsettings = $this->objLanguage->languageText('mod_mcqtests_stepmenusubsettings', 'mcqtests');
$stepmenusubrestrictions = $this->objLanguage->languageText('mod_mcqtests_stepmenusubrestrictions', 'mcqtests');
$addHeading = $this->objLanguage->languageText('mod_mcqtests_addtest', 'mcqtests');
$editHeading = $this->objLanguage->languageText('mod_mcqtests_edittest', 'mcqtests');
$nameLabel = $this->objLanguage->languageText('mod_mcqtests_wordname', 'mcqtests');
$chapterLabel = $this->objLanguage->languageText('mod_mcqtests_contentchapter', 'mcqtests');
$statusLabel = $this->objLanguage->languageText('mod_mcqtests_status', 'mcqtests');
$notactiveLabel = $this->objLanguage->languageText('mod_mcqtests_inactive', 'mcqtests');
$openLabel = $this->objLanguage->languageText('mod_mcqtests_openforentry', 'mcqtests');
$percentLabel = $this->objLanguage->languageText('mod_mcqtests_finalmark', 'mcqtests');
$startLabel = $this->objLanguage->languageText('mod_mcqtests_startdate', 'mcqtests');
$closeLabel = $this->objLanguage->languageText('mod_mcqtests_closingdate', 'mcqtests');
$descriptionLabel = $this->objLanguage->languageText('mod_mcqtests_description', 'mcqtests');
$saveLabel = $this->objLanguage->languageText('word_save');
$exitLabel = $this->objLanguage->languageText('word_cancel');
$setTimedLabel = $this->objLanguage->languageText('mod_mcqtests_settimed', 'mcqtests');
$setDurationLabel = $this->objLanguage->languageText('mod_mcqtests_setduration', 'mcqtests');
$hourLabel = $this->objLanguage->languageText('mod_mcqtests_hours', 'mcqtests');
$minLabel = $this->objLanguage->languageText('mod_mcqtests_minutes', 'mcqtests');
$lbTotalPercent = $this->objLanguage->languageText('mod_mcqtests_totalpercentfortests', 'mcqtests');
$percentExplanation = $this->objLanguage->languageText('mod_mcqtests_percentxplanation', 'mcqtests');
$lbEqualPercent = $this->objLanguage->languageText('mod_mcqtests_settoequalpercent', 'mcqtests');
$testTypeLabel = $this->objLanguage->languageText('mod_mcqtests_testtype', 'mcqtests');
$formativeLabel = $this->objLanguage->languageText('word_formative');
$summativeLabel = $this->objLanguage->languageText('word_summative');
$advancedLabel = $this->objLanguage->languageText('mod_mcqtest_word_advanced', 'mcqtests');
$qSequenceLabel = $this->objLanguage->languageText('mod_mcqtests_questionorder', 'mcqtests');
$aSequenceLabel = $this->objLanguage->languageText('mod_mcqtests_answerorder', 'mcqtests');
$scrambledLabel = $this->objLanguage->languageText('word_scrambled');
$sequentialLabel = $this->objLanguage->languageText('word_sequential');
$restrictLabel = $this->objLanguage->languageText('mod_mcqtests_restrict', 'mcqtests');
$anyLabLabel = $this->objLanguage->languageText('mod_mcqtests_labs', 'mcqtests');
$addLabLabel = $this->objLanguage->languageText('mod_mcqtests_addlab', 'mcqtests');
$errPercent = $this->objLanguage->languageText('mod_mcqtests_numericpercent', 'mcqtests');
$errName = $this->objLanguage->languageText('mod_mcqtests_entername', 'mcqtests');
$errDates = $this->objLanguage->languageText('mod_mcqtests_errordates', 'mcqtests');
$permissionsLabel = $this->objLanguage->languageText('mod_mcqtests_coursepermissionslabel','mcqtests',"Data Bank Type");
$coursePermissionPrivate = $this->objLanguage->languageText('mod_mcqtests_privatecourse','mcqtests', "Private Course");
$coursePermissionPublic = $this->objLanguage->languageText('mod_mcqtests_publiccourse','mcqtests', "Public Course");

if (!empty($data)) {
    $id = $data[0]['id'];
    $name = $data[0]['name'];
    $status = $data[0]['status'];
    $px = explode('.', $data[0]['percentage']);
    $percent = $px[0];
    $decimal = 0;
    if (isset($px[1])) {
        $decimal = $px[1];
    }
    $start = $data[0]['startdate'];
    $close = $data[0]['closingdate'];
    $timed = $data[0]['timed'];
    $duration = $data[0]['duration'];
    if ($duration > 0) {
        $hour = floor($duration/60);
        $min = $duration%60;
    } else {
        $hour = 0;
        $min = 0;
    }
    $testType = $data[0]['testtype'];
    $qSequence = $data[0]['qsequence'];
    $aSequence = $data[0]['asequence'];
    $comLab = $data[0]['comlab'];
    $description = $data[0]['description'];
    $coursePermissions = $data[0]['coursepermissions'];
} else {
    $id = '';
    $name = '';
    $chapter = '';
    $status = '';
    $percent = 0;
    $decimal = 0;
    $start = date('Y-m-d H:i:s');
    $close = date('Y-m-d H:i:s');
    $timed = '';
    $hour = 0;
    $min = 0;
    $description = '';
    $coursePermissions = '';
}

//set a step menu for later use
$this->objStepMenu->addStep($stepmenudescription,$stepmenusubdescription);
$this->objStepMenu->addStep($stepmenusettings,$stepmenusubsettings);
$this->objStepMenu->addStep($stepmenurestrictions,$stepmenusubrestrictions);

//set a html table for display
$objTable = new htmltable();
$objTable->width = '650px';
$objTable->border = "0";
$objTable->attributes = "align = 'center'";
$objTable->cellpadding = 5;

//current will tell us which step we processing
$currentstep = $this->getParam('currentstep');
if($currentstep==null || $currentstep>3) {
    $currentstep = 1;
}else {

}

$objForm = new form('exit', $this->uri(array(
    'action' => 'savestep' , 'currentstep'=>$currentstep+1
)));

//we will try to include a switch condition for step menu:
switch($currentstep) {
    default:
    case '1':
    // Set test name
        $objLabel = new label('<b>'.$nameLabel.':</b>', 'input_name');
        $objInput = new textinput('name', $name);
        $objInput->size = "61";

        $objTable->startRow();
        $objTable->addCell($objLabel->show() , '20%', $valign="top");
        $objTable->addCell($objInput->show() , '80%', $valign="top");
        $objTable->endRow();

        // Set description
        $objLabel = new label('<b>'.$descriptionLabel.':</b>', 'input_description');
        $objText = new textarea('description', $description, "7", "70");
        $objTable->startRow();
        $objTable->addCell($objLabel->show() , '20%', $valign="top");
        $objTable->addCell($objText->show() , '80%', $valign="top");
        $objTable->endRow();
        // Set activity status - not active if in add mode
        $objLabel = new label('<b>'.$statusLabel.':</b>', 'input_status');

        if ($mode == 'edit') {
            $objRadio = new radio('status');
            $objRadio->addOption('inactive', $notactiveLabel);
            $objRadio->addOption('open', $openLabel);
            $objRadio->setSelected($status);
            $objRadio->setBreakSpace('<br />');
            $statusShow = $objRadio->show();
        } else {
            $statusShow = '<b>'.$notactiveLabel.'</b>';
            $objInput = new textinput('status', 'inactive');
            $objInput->fldType = 'hidden';
            $statusShow.= $objInput->show();
        }
        $objTable->addRow(array(
            $objLabel->show() ,
            $statusShow
        ));

        // set up test type
        $objRadio = new dropdown('testType');
        $objRadio->addOption($formativeLabel, $formativeLabel);
        $objRadio->addOption($summativeLabel, $summativeLabel);
        $objRadio->addOption($advancedLabel, $advancedLabel);
        if (isset($testType) && !empty($testType)) {
            $objRadio->setSelected($testType);
        } else {
            $objRadio->setSelected('Formative');
        }
        //$objRadio->setBreakSpace('table');
        $testTypeRadio = $objRadio->show();
        $objTable->addRow(array(
            "<b>".$testTypeLabel.":</b>",
            "<b>".$testTypeRadio."</b>"
        ));
        // set up question sequence
        $objRadio = new dropdown('qSequence');
        $objRadio->addOption($sequentialLabel, $sequentialLabel);
        $objRadio->addOption($scrambledLabel, $scrambledLabel);
        if (isset($qSequence) && !empty($qSequence)) {
            $objRadio->setSelected($qSequence);
        } else {
            $objRadio->setSelected('Sequential');
        }
        //	$objRadio->setBreakSpace('table');
        $qSequenceRadio = $objRadio->show();
        $objTable->addRow(array(
            "<b>".$qSequenceLabel.":</b>",
            "<b>".$qSequenceRadio."</b>"
        ));
        // set up answer sequence
        $objRadio = new dropdown('aSequence');
        $objRadio->addOption($sequentialLabel, $sequentialLabel);
        $objRadio->addOption($scrambledLabel, $scrambledLabel);
        if (isset($aSequence) && !empty($aSequence)) {
            $objRadio->setSelected($aSequence);
        } else {
            $objRadio->setSelected('Sequential');
        }
        //	$objRadio->setBreakSpace('table');
        $aSequenceRadio = $objRadio->show();
        $objTable->addRow(array(
            "<b>".$aSequenceLabel.":</b>",
            "<b>".$aSequenceRadio."</b>"
        ));

        // set up course permission
        $objRadio = new radio('coursePermissions');
        $objRadio->setBreakSpace('&nbsp;&nbsp;/&nbsp;&nbsp;');
        $objRadio->addOption('Private', $coursePermissionPrivate);
        $objRadio->addOption('Public', $coursePermissionPublic);

        if (isset($coursePermissions) && !empty($coursePermissions)) {
            $objRadio->setSelected($coursePermissions);
        }
        else {
            $objRadio->setSelected('Private');
        }

        $objTable->addRow(array(
            "<b>".$permissionsLabel.":</b>",
            "<b>".$objRadio->show()."</b>"
        ));

        //=================================================BUTTONS==============================================================================
        //exit and next button
        $objButton = new button('save', 'Next');
        $objButton->setToSubmit();
        $btnSave = $objButton->show();
        $objButton = new button('save', 'Back');
        $objButton->setOnClick('javascript:document.getElementById(\'form_exit\').submit()');
        $btnExit = $objButton->show();
        $objTable->startRow();
        $objTable->addCell($btnSave, '20%', 'top', 'right');
        $objTable->addCell("&nbsp;", '70%', '', 'left');
        $objTable->endRow();

        $this->objStepMenu->setCurrent(1);

        $objForm->addToForm($objTable->show());
        //echo $this->objStepMenu->show();
        //echo $objTable->show();
        //echo $objForm->show();
        break;
    //=================================================BUTTONS==============================================================================


    case '2a':
    // Set percentage of final mark
        $objLabel = new label('<b>% '.$percentLabel.':</b>', 'input_percent');
        $objDropDown = new dropdown('percent');
        for ($x = 0 ; $x <= 100 ; $x++) {
            $objDropDown->addOption($x, $x);
        }
        $markfield=new textinput("mark",$percent);


        $objDropDown->setSelected($percent);
        $dropStr = $objDropDown->show();
        $objDropDown = new dropdown('decimal');
        for ($x = 0 ; $x < 100 ; $x++) {
            $objDropDown->addOption($x, $x);
        }
        $objDropDown->setSelected($decimal);
       // $dropStr.= '&nbsp;<b>.</b>&nbsp;&nbsp;';//.$objDropDown->show() .'&nbsp;%&nbsp;&nbsp;&nbsp;';
       // $dropStr.= '<font class="warning">'.$percentExplanation.'&nbsp;%</font>';
        $objTable->addRow(array(
            $objLabel->show() ,
            $markfield->show()
        ));
        // Set tests to equal percentage
        $objLabel = new label('<b>'.$lbEqualPercent.':</b>', 'input_setequal');
        $check = FALSE;
        $objCheck = new checkbox('setequal', '', $check);
        $objTable->addRow(array(
            $objLabel->show() ,
            $objCheck->show()
        ));
/* *** start date & time *** */
        // Set start date of test
        $startField = $objPopupcal->show('start', 'yes', 'no', $start);
        $objLabel = new label('<b>'.$startLabel.':</b>', 'input_start');
        $objTable->addRow(array(
            $objLabel->show() ,
            $startField
        ));
        // Set closing date of test
        $closeField = $objPopupcal->show('close', 'yes', 'no', $close);
        $objLabel = new label('<b>'.$closeLabel.':</b>', 'input_close');
        $objTable->addRow(array(
            $objLabel->show() ,
            $closeField
        ));
        // Set a timed test
        $objLabel = new label('<b>'.$setTimedLabel.':</b>', 'input_timed');
        $check = FALSE;
        if (!empty($timed)) {
            $check = TRUE;
        }
        $objCheck = new checkbox('timed', '', $check);
        $objCheck->extra = ' onchange="if(this.checked){document.getElementById(\'input_hour\').disabled=false;document.getElementById(\'input_min\').disabled=false;}else{document.getElementById(\'input_hour\').value=\'0\';document.getElementById(\'input_hour\').disabled=true;document.getElementById(\'input_min\').value=\'0\';document.getElementById(\'input_min\').disabled=true;}"';
        $objTable->addRow(array(
            $objLabel->show() ,
            $objCheck->show()
        ));
        // Set duration of a timed test
        $objLabel = new label('<b>'.$setDurationLabel.':</b>', 'input_hour');
        $objLabelH = new label('<b>'.$hourLabel.'</b>', 'input_hour');
        $objDropDown = new dropdown('hour');
        for ($x = 0 ; $x <= 23 ; $x++) {
            $objDropDown->addOption($x, $x);
        }
        $objDropDown->setSelected($hour);
        if (!$check) {
            $objDropDown->extra = ' disabled="true"';
        }
        $hourDrop = $objDropDown->show();
        $objLabelM = new label('<b>'.$minLabel.'</b>', 'input_min');
        $objDropDown = new dropdown('min');
        for ($y = 0 ; $y <= 59 ; $y++) {
            $objDropDown->addOption($y, $y);
        }
        $objDropDown->setSelected($min);
        if (!$check) {
            $objDropDown->extra = ' disabled="true"';
        }
        $minDrop = $objDropDown->show();
        $input = $hourDrop.'&nbsp;&nbsp;'.$objLabelH->show() .'&nbsp;&nbsp;&nbsp;&nbsp;';
        $input.= $minDrop.'&nbsp;&nbsp;'.$objLabelM->show();
        $objTable->addRow(array(
            $objLabel->show() ,
            $input
        ));
        //=================================================BUTTONS==============================================================================
        //exit and next button
        $objButton = new button('save', 'Next');
        $objButton->setToSubmit();
        $btnSave = $objButton->show();
        $objButton = new button('save', 'Previous');
        $objButton->setOnClick('javascript:history.back()');
        $btnExit = $objButton->show();
        $objTable->startRow();
        $objTable->addCell($btnSave, '30%', 'top', 'right');
        $objTable->addCell($btnExit, '70%', 'top', 'left');
        $objTable->endRow();
        $this->objStepMenu->setCurrent(2);
        $objForm->addToForm($objTable->show());
        //echo $this->objStepMenu->show();
        //echo $objTable->show();
        //echo $objForm->show();
        break;
    //=================================================BUTTONS==============================================================================
    case '3a':
    // set up restricted computer laboratory
        $objDrop = new dropdown('comLab');
        $objDrop->addOption(NULL, $anyLabLabel);
        foreach($this->arrComLabs as $lab) {
            $objDrop->addOPtion($lab, $lab);
        }
        if (isset($comLab) && !empty($comLab)) {
            $objDrop->setSelected($comLab);
        }
        $labDrop = $objDrop->show();
        $objLink = new link($this->uri(array(
            'action' => 'addlab',
            'id' => $id,
            //	'mode' => $mode
        )));
        $objLink->link = $addLabLabel;
        $labLink = $objLink->show();
        $objTable->addRow(array(
            "<b>".$restrictLabel.":</b>",
            $labDrop."&nbsp;&nbsp;".$labLink
        ));
        //=================================================BUTTONS==============================================================================

        //exit and next button
        $objButton = new button('save', 'Next');
        $objButton->setToSubmit();
        $btnSave = $objButton->show();
        $objButton = new button('save', 'Previous');
        $objButton->setOnClick('javascript:history.back()');
        $btnExit = $objButton->show();
        $objTable->startRow();
        $objTable->addCell($btnSave, '30%', '', 'right');
        $objTable->addCell($btnExit, '70%', '', 'left');
        $objTable->endRow();

        $this->objStepMenu->setCurrent(3);
        $objForm->addToForm($objTable->show());

        break;
//=================================================BUTTONS==============================================================================

}//end of switch#


$ret .= $this->objStepMenu->show().'<br />';
$ret .= $objForm->show();

echo "<div class='mcq_main'>$ret</div>";
?>