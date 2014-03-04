<?php

/**
 * Template for viewing a test and adding more questions.
 * @package mcqtests
 * @param $data The test information.
 * @param $questions The details of the questions on the test.
 */
// set up layout template
$this->setLayoutTemplate('mcqtests_layout_tpl.php');

// Classes used in this module
$objHeading = $this->loadClass('htmlheading', 'htmlelements');
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objLayer = $this->loadClass('layer', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objConfirm = $this->loadClass('confirm', 'utilities');
$objMsg = $this->newObject('timeoutmessage', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$viewjs = '<script language="JavaScript" src="' . $this->getResourceUri('js/view.js') . '" type="text/javascript"></script>';
$this->appendArrayVar('headerParams', $viewjs);

// set up language items
$phraseSCQuestions = $this->objLanguage->languageText("mod_mcqtests_simplecalculatedqns", 'mcqtests', "Simple Calculated Questions");
$phraseRandomShortAns = $this->objLanguage->languageText('mod_mcqtests_randomshortans', 'mcqtests');
$testdetailsLabel = $this->objLanguage->languageText('mod_mcqtests_testdetailsLabel', 'mcqtests');
$addqestionslabel = $this->objLanguage->languageText('mod_mcqtests_addquestions', 'mcqtests');
$head = $objLanguage->languageText('mod_mcqtests_test', 'mcqtests');
$editLabel = $objLanguage->languageText('word_edit');
$chapterLabel = $objLanguage->languageText('mod_mcqtests_chapter', 'mcqtests');
$statusLabel = $objLanguage->languageText('mod_mcqtests_status', 'mcqtests');
$percentLabel = $objLanguage->languageText('mod_mcqtests_finalmark', 'mcqtests');
$startdateLabel = $objLanguage->languageText('mod_mcqtests_startdate', 'mcqtests');
$dateLabel = $objLanguage->languageText('mod_mcqtests_closingdate', 'mcqtests');
$totalLabel = $objLanguage->languageText('mod_mcqtests_totalmarks', 'mcqtests');
$backLabel = $objLanguage->languageText('mod_mcqtests_name', 'mcqtests') .' '.$objLanguage->languageText('word_home');
$questionsLabel = $objLanguage->languageText('mod_mcqtests_questions', 'mcqtests');
$questionLabel = $objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
$markLabel = $objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
$numansLabel = $objLanguage->languageText('mod_mcqtests_numanswers', 'mcqtests');
$actionLabel = $objLanguage->languageText('mod_mcqtests_actions', 'mcqtests');
$lbConfirm = $objLanguage->languageText('mod_mcqtests_deletequestion', 'mcqtests');
$wordquestion = $objLanguage->languageText('mod_mcqtests_deletequestionword', 'mcqtests');
$wordmcq = $objLanguage->languageText('mod_mcqtests_mcq', 'mcqtests');
$category = $objLanguage->languageText('mod_mcqtests_addcategory', 'mcqtests');
$simple = $objLanguage->languageText('mod_mcqtests_wordsimpleq', 'mcqtests');
$description = $objLanguage->languageText('mod_mcqtests_addDesc', 'mcqtests');
$shortans = $objLanguage->languageText('mod_mcqtests_shortans', 'mcqtests');
$typeLabel = $objLanguage->languageText('mod_mcqtests_typeofquest', 'mcqtests');

$listLabel = ucwords($objLanguage->code2Txt('mod_mcqtests_liststudents', 'mcqtests', array(
    'readonlys' => 'students'
)));
$editIconLabel = $editLabel.' '.$head;
$deleteLabel = $this->objLanguage->languageText('word_delete') .' '.$wordquestion;
$addLabel = $this->objLanguage->languageText('word_add') .' '.$questionLabel;
$upLabel = $this->objLanguage->languageText('word_up');
$downLabel = $this->objLanguage->languageText('word_down');
$durationLabel = $this->objLanguage->languageText('mod_mcqtests_duration', 'mcqtests');
$hoursLabel = $this->objLanguage->languageText('mod_mcqtests_hours', 'mcqtests');
$minLabel = $this->objLanguage->languageText('mod_mcqtests_minutes', 'mcqtests');
$hourLabel = $this->objLanguage->languageText('mod_mcqtests_hour', 'mcqtests');
$noRecords = $this->objLanguage->languageText('mod_mcqtests_nosetquestions', 'mcqtests');
$testTypeLabel = $this->objLanguage->languageText('mod_mcqtests_testtype', 'mcqtests');
$formativeLabel = $this->objLanguage->languageText('word_formative');
$summativeLabel = $this->objLanguage->languageText('word_summative');
$qSequenceLabel = $this->objLanguage->languageText('mod_mcqtests_questionorder', 'mcqtests');
$aSequenceLabel = $this->objLanguage->languageText('mod_mcqtests_answerorder', 'mcqtests');
$scrambledLabel = $this->objLanguage->languageText('word_scrambled');
$sequentialLabel = $this->objLanguage->languageText('word_sequential');
$computerLabel = $this->objLanguage->languageText('mod_mcqtests_comlab', 'mcqtests');
$anyLabLabel = $this->objLanguage->languageText('mod_mcqtests_labs', 'mcqtests');
$selectqntype = $this->objLanguage->languageText('mod_mcqtests_selectqntype', 'mcqtests', 'Select question type');

// question types descriptions
$descriptionDesc = $this->objLanguage->languageText('mod_mcqtests_descriptiondesc', 'mcqtests');
$categoryDesc = $this->objLanguage->languageText('mod_mcqtests_categorydesc', 'mcqtests');
$simpleDesc = $this->objLanguage->languageText('mod_mcqtests_simpledesc', 'mcqtests');
$numericalDesc = $this->objLanguage->languageText('mod_mcqtests_numericaldesc', 'mcqtests');
$matchingDesc = $this->objLanguage->languageText('mod_mcqtests_matchingdesc', 'mcqtests');
$shortansDesc = $this->objLanguage->languageText('mod_mcqtests_shortansdesc', 'mcqtests');
$wdMCQ = $this->objLanguage->languageText('mod_mcqtests_wordmcq', 'mcqtests');
$descTitle = $wdMCQ." ".$this->objLanguage->languageText('mod_mcqtests_descriptions', 'mcqtests');
$categoryTitle = $wdMCQ." ".$this->objLanguage->languageText('mod_mcqtests_categories', 'mcqtests');

//switch between the question descriptions and adding questions
$mode = $this->getParam('mode');

$answers_tab = $this->newObject('tabbedbox', 'htmlelements');

$tabcontent = $this->newObject('tabcontent', 'htmlelements');

// Heading for test
$editUrl = $this->uri(array(
    'action' => 'edit2',
    'id' => $data['id']
));
$editLink = $objIcon->getEditIcon($editUrl);
$objIcon->title = $listLabel;
$objIcon->setIcon('comment');

$objLink = new link($this->uri(array(
    'action' => 'liststudents2',
    'id' => $data['id']
)));
$objLink->link = $objIcon->show();
$editLink.= '&nbsp;'.$objLink->show();

// Show Heading
$heading = $head.': '.$data['name'].'&nbsp;&nbsp;'.$editLink;
$this->setVarByRef('heading', $heading);

// Create Table for the test information
$objTable = new htmltable();
$objTable->cellpadding = '5';
$objTable->cellspacing = '2';
$objTable->width = '99%';

// Add Context and Name of Chapter
$objTable->startRow();
$objTable->addCell('');//'<b>'.$chapterLabel.'</b>: '.$data['node'], '50%');
$objTable->endRow();

// Add Activity Status and percentage of mark
$objTable->startRow();
$objTable->addCell('<b>'.$statusLabel.'</b>: '.$objLanguage->languageText('mod_mcqtests_'.$data['status'], 'mcqtests'));
$objTable->addCell('<b>% '.$percentLabel.'</b>: '.$data['percentage']);
$objTable->endRow();

// Add Start date
$objTable->startRow();
$objTable->addCell('<b>'.$startdateLabel.'</b>: '.$this->objDate->formatDate($data['startdate']));
$objTable->addCell('<b>'.$totalLabel.'</b>: '.$data['totalmark']);
if ($data['timed']) {
    $duration = (0) .'&nbsp;'.$hoursLabel;
    if ($data['duration'] > 0) {
        $hours = floor($data['duration']/60);
        $mins = $data['duration']%60;
        if ($hours == 1) {
            $hoursLabel = $hourLabel;
        }
        $duration = $hours.'&nbsp;'.$hoursLabel.'&nbsp;&nbsp;';
        $duration.= $mins.'&nbsp;'.$minLabel;
    }
    $objTable->addCell('<b>'.$durationLabel.'</b>: '.$duration);
}
$objTable->endRow();

// Add Cosing date
$objTable->addRow(array(
    '<b>'.$dateLabel.'</b>: '.$this->objDate->formatDate($data['closingdate'])
));

// Add test type
if (isset($data['testtype']) && !empty($data['testtype'])) {
    $testType = $data['testtype'];
} else {
    $testType = $formativeLabel;
}
$objTable->addRow(array(
    "<b>".$testTypeLabel.": </b>".$testType
));

// Add question sequence
if (isset($data['qsequence']) && !empty($data['qsequence'])) {
    $qSequence = $data['qsequence'];
} else {
    $qSequence = $sequentialLabel;
}
$objTable->addRow(array(
    "<b>".$qSequenceLabel.": </b>".$qSequence
));

// Add answer sequence
if (isset($data['asequence']) && !empty($data['asequence'])) {
    $aSequence = $data['asequence'];
} else {
    $aSequence = $sequentialLabel;
}
$objTable->addRow(array(
    "<b>".$aSequenceLabel.": </b>".$aSequence
));

// add computer lab
if (isset($data['comlab']) && !empty($data['comlab'])) {
    $comLab = $data['comlab'];
} else {
    $comLab = $anyLabLabel;
}
$objTable->addRow(array(
    "<b>".$computerLabel.": </b>".$comLab
));

// Description
$objTable->startRow();
$objTable->addCell($data['description'], NULL, "top", NULL, NULL, ' colspan="2"'); // colspans to two
$objTable->endRow();

// Show Table
echo $objTable->show();



$count = count($questions);
if (empty($questions)) {
    $count = 0;
}

$calcdqn = $this->objLanguage->languageText('mod_mcqtests_calcdqn', 'mcqtests', 'Calculated question');
$matchingqn = $this->objLanguage->languageText('mod_mcqtests_matchingqn', 'mcqtests', 'Matching Question');
$numericalqns = $this->objLanguage->languageText('mod_mcqtests_numericalqns', 'mcqtests', 'Numerical Question');
$selectQBLabel = "Choose Question Type";

$existingQuestions = new dropdown('existingQ');
$existingQuestions->setId("existingQ");

$existingQuestions->addOption('-', '[-' . $selectqntype . '-]');
$existingQuestions->addOption('mcq', $simple);
$existingQuestions->addOption('descriptionlist', $descTitle);
$existingQuestions->addOption('category', $categoryTitle);
//$existingQuestions->addOption('calcQ', $calcdqn);
$existingQuestions->addOption('matchQ', $matchingqn);
$existingQuestions->addOption('numericalQ', $numericalqns);
$existingQuestions->addOption('showRSA', $phraseRandomShortAns);
$existingQuestions->addOption('showSCQ', $phraseSCQuestions);
//$existingQuestions->addOption('shortansQ', 'Short Answer Questions');

/*// choose questiontype
$objIcon->title = $addLabel;
$addQUrl = $this->uri(array(
    'action' => 'choosequestiontype',
    'id' => $data['id'],
    'count' => $count
));
$addQ = $objIcon->getAddIcon($addQUrl);*/





//=======================================================SPLIT=========================================================================

$str = null;

// Questions Header
$objHeading = new htmlheading();
$objHeading->type = 3;
$objHeading->str = $questionsLabel.' ('.$count.'):
	&nbsp;&nbsp;&nbsp;&nbsp;'.$existingQuestions->show()."<label style='padding-left: 2em; color:#000099;'>NB: This is still experimental</label>";//$addQ;
$qHeading = $objHeading->show();
$str.= $qHeading;


$goButton = new button ('goButton','Go');
$goButton->setOnClick("goAddQuestion();");

// add descriptions of the different types of questions
$str.="<div id='adddescriptionDesc'><label>".$description.":&nbsp;</label>".$descriptionDesc."<br/>".$goButton->show()."</div>";
$str.="<div id='categoryDesc'><label>".$category.":&nbsp;</label>".$categoryDesc."<br/>".$goButton->show()."</div>";
$str.="<div id='matchQDesc'><label>".$matchingqn.":&nbsp;</label>".$matchingDesc."<br/>".$goButton->show()."</div>";
$str.="<div id='numericalQDesc'><label>".$numericalqns.":&nbsp;</label>".$numericalDesc."<br/>".$goButton->show()."</div>";
$str.="<div id='shortansQDesc'><label>".$shortans.":&nbsp;</label>".$shortansDesc."<br/>".$goButton->show()."</div>";
$str.="<div id='simpleDesc'><label>".$simple.":&nbsp;</label>".$simpleDesc."<br/>".$goButton->show()."</div>";
$str.="<div id='RSA'><label>".$phraseRandomShortAns.":&nbsp;</label>".$phraseRandomShortAns."<br/>".$goButton->show()."</div>";
$str.="<div id='SCQ'><label>".$phraseSCQuestions.":&nbsp;</label>".$phraseSCQuestions."<br/>".$goButton->show()."</div>";


// Confirmation message on saving a question
$confirm = $this->getParam('confirm');
if ($confirm == 'yes') {
    $msg = $this->getSession('confirm');
    $this->unsetSession('confirm');
    $objMsg->setMessage($msg.'&nbsp;&nbsp;'.date('d/m/Y H:i'));
    $str.= '<p>'.$objMsg->show() .'</p>';
}

// Create a New table for the questions
$objTable = new htmltable();
$objTable->cellpadding = 4;
$objTable->cellspacing = 2;
$objTable->width = '99%';
$objTable->startRow();
$objTable->addCell('', '', '', '', 'heading');
$objTable->addCell($questionLabel, '', '', '', 'heading');
$objTable->addCell($markLabel, '', '', '', 'heading');
$objTable->addCell($actionLabel, '', '', '', 'heading', 'colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('', '1%');
$objTable->addCell('');
$objTable->addCell('', '4%');
$objTable->addCell('', '4%');
$objTable->addCell('', '8%');
$objTable->endRow();

// Add questions to table
if (!empty($questions)) {
    $i = 0;
    foreach($questions AS $line) {
        $class = (($i++%2) == 0) ? "odd" : "even";
        // move a question up in the order
        if ($i > 1) {
            $objIcon->title = $upLabel;
            $url = $this->uri(array(
                'action' => 'questionup',
                'questionId' => $line['id'],
                'id' => $data['id']
            ));
            $iconsUD = $objIcon->getLinkedIcon($url, 'mvup') .'&nbsp;';
        } else {
            $iconsUD = '&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        // move a question down in the order
        if ($i < $count) {
            $objIcon->title = $downLabel;
            $url = $this->uri(array(
                'action' => 'questiondown',
                'questionId' => $line['id'],
                'id' => $data['id']
            ));
            $iconsUD.= $objIcon->getLinkedIcon($url, 'mvdown');
        }

        // edit & delete
        $objIcon->title = $editIconLabel;
        $editUrl = $this->uri(array(
            'action' => 'editquestion',
            'questionId' => $line['id'],
            'id' => $this->getParam('id'),
            'type' => $line['questiontype']
        ));
        $icons = $objIcon->getEditIcon($editUrl);
        $objIcon->title = $deleteLabel;
        $objIcon->setIcon('delete');
        $pos = FALSE;
        $len = strlen($line['question']);
        $conQuestion = $line['question'];
        if ($len > 10) {
            $pos = strpos($line['question'], '<', 10);
        }
        if ($len > 20 && ($pos > 20 || $pos === FALSE)) {
            $pos = strpos($line['question'], ' ', 20);
        }
        $conQuestion = substr($line['question'], 0, $pos) .'...';

        $objConfirm = new confirm();
        $objConfirm->setConfirm($objIcon->show() , $this->uri(array(
            'action' => 'deletequestion2',
            'questionId' => $line['id'],
            'id' => $data['id'],
            'mark' => $line['mark'],
            'type' => $line['questiontype']
            )) , $lbConfirm);
        $icons.= $objConfirm->show();
        // link name to edit question - shorten the question to 100 characters or the first line break
        $pos = FALSE;
        if ($len > 10) {
            $pos = strpos($line['question'], '<br />', 10);
        }
        if ($len > 100 && $pos === FALSE) {
            $pos = strpos($line['question'], ' ', 100);
        }
        if (!($pos === FALSE)) {
            $strQuestion = substr($line['question'], 0, $pos) .'...';
        } else {
            $strQuestion = $line['question'];
        }
        $objLink = new link($editUrl);
        $objLink->link = $strQuestion;


        $objTable->startRow();
        $objTable->addCell($i.'.');
        $objTable->addCell($this->dbQuestions->previewQuestion($line));
        //$objTable->addCell($objLink->show());
        $objTable->addCell($line['mark']);
        $objTable->addCell($iconsUD);
        $objTable->addCell($icons);
        $objTable->endRow();
        /*
        $tableRow = array();
        $tableRow[] = $i;
        $tableRow[] = ;
        $tableRow[] = $line['mark'];
        $tableRow[] = $iconsUD;
        $tableRow[] = $icons;
        $objTable->addRow($tableRow, $class);
        */
    }
    $str.= $objTable->show();
} else {
    $str.= '<p align="center" class="noRecordsMessage">'.$noRecords.'</p>';
}
/*$objLink = new link($addQUrl);
$objLink->link = $addLabel;
$homeLink = '<p>'.$objLink->show() .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
$objLink = new link($this->uri(array(
    ''
)));
$objLink->link = $backLabel;*/
//$homeLink.= /*$objLink->show() .*/'</p>';
//
//$objLayer = new layer();
//$objLayer->cssClass = '';
//$objLayer->align = 'center';
//$objLayer->str = $homeLink;
//$back = $objLayer->show();
//$str.= $back;

/*
//echo $str;
if(isset($qNum)){
	$objInput = new textinput('questionId', $questId);
	$objInput->fldType = 'hidden';
	$topStr.= $objInput->show();
}
*/


$objHeading = new htmlheading();
$objHeading->type = 1;
$objHeading->str = $addqestionslabel;

echo $objHeading->show();

echo $str;

$objHeading = new htmlheading();
$objHeading->type = 1;
$objHeading->str = $this->objLanguage->languageText('mod_mcqtests_activatetest', 'mcqtests', 'Activate Test');

echo $objHeading->show();

$form = new form ('activatetest', $this->uri(array('action'=>'activatetest')));

$radio = new radio ('status');
$radio->addOption('inactive', $this->objLanguage->languageText('mod_mcqtests_inactive', 'mcqtests', 'Not Active'));
$radio->addOption('open', $this->objLanguage->languageText('mod_mcqtests_openforentry', 'mcqtests', 'Open For Entry'));
$radio->setBreakSpace(' - ');

if ($data['status'] == 'open') {
    $radio->setSelected('open');
} else {
    $radio->setSelected('inactive');
}

$hiddeninput = new hiddeninput('id', $data['id']);

$form->addToForm($hiddeninput->show().'<p>'.$radio->show().'</p>');

$button = new button ('update', $this->objLanguage->languageText('mod_mcqtests_updatestatus', 'mcqtests', 'Update Status'));
$button->setToSubmit();

$previewButton = new button ('previewButton','Preview');

$previewButton->setOnClick(  "window.open('".$this->uri(array(
    'action' => 'previewtest2',
    'id' => $data['id'],
    'mode' => 'notoolbar'
    )) ."', 'previewtest2', 'fullscreen,scrollbars');");


$form->addToForm($button->show().$previewButton->show());

echo $form->show();

$myJS = '<script type="text/javascript">
            var mcqUrl = "'.str_replace("amp;", "", $this->uri(array("action"=>"choosequestiontype2", "id"=>$this->getParam('id'), "count" => $count, "test"=>$test))).'",
                calqUrl = "'.str_replace("amp;", "", $this->uri(array("action"=>"viewcalcquestions", "id"=>$this->getParam('id'), "count" => $count, "test"=>$test))).'",
                matchingqUrl = "'.str_replace("amp;", "", $this->uri(array("action"=>"viewmatchingquestions", "id"=>$this->getParam('id'), "count" => $count, "test"=>$test))).'",
                numericalqUrl = "'.str_replace("amp;", "", $this->uri(array("action"=>"viewnumericalquestions", "id"=>$this->getParam('id'), "count" => $count, "test"=>$test))).'",
                shortanswerqUrl = "'.str_replace("amp;", "", $this->uri(array("action"=>"viewshortansquestions", "id"=>$this->getParam('id'), "count" => $count, "test"=>$test))).'",
                categoryUrl = "'.str_replace("amp;", "", $this->uri(array("action"=>"categorylisting", "id"=>$this->getParam('id'), "count" => $count, "test"=>$test))).'",
                descriptionUrl = "'.str_replace("amp;", "", $this->uri(array("action"=>"mcqlisting", "id"=>$this->getParam('id'), "count" => $count, "test"=>$test))).'";
                randomSAUrl = "'.str_replace("amp;", "", $this->uri(array("action"=>"rsalisting", "id"=>$this->getParam('id'), "count" => $count, "test"=>$test))).'";
                scqUrl = "'.str_replace("amp;", "", $this->uri(array("action"=>"scqlisting", "id"=>$this->getParam('id'), "test"=>$test, "count" => $count))).'";
        </script>';
echo $myJS;
?>