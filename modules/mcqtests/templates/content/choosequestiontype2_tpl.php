<?php

//set the layout of the choosequestiontype template
$this->setLayoutTemplate('mcqtests_layout_tpl.php');

//String of links -- to be removed
$str = "";
$objDescLink = &$this->getObject("link", "htmlelements");
$objDescLink->link($this->uri(array(
            'module' => 'mcqtests',
            'action' => 'mcqlisting'
        )));
$objDescLink->link = "MCQ Descriptions";
$str .= "<h4>".$objDescLink->show()."</h4>";
$objDescLink = &$this->getObject("link", "htmlelements");
$objDescLink->link($this->uri(array(
            'module' => 'mcqtests',
            'action' => 'categorylisting'
        )));
$objDescLink->link = "MCQ Category";
$str .= "<h4>".$objDescLink->show()."</h4>";

$objDescLink = &$this->getObject("link", "htmlelements");
$objDescLink->link($this->uri(array(
            'module' => 'mcqtests',
            'action' => 'addrandomshortans'
        )));
$objDescLink->link = "Add Random Short Answer Question";
$str .= "<h4>".$objDescLink->show()."</h4>";

echo $str;
//Load the classes for the template

$this->loadclass('htmltable', 'htmlelements');
$this->loadclass('htmlheading', 'htmlelements');
$this->loadclass('geticon', 'htmlelements');
$this->loadclass('link', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('fieldsetex', 'htmlelements');

$objExt = $this->getObject("extjs", "ext");
echo $objExt->show();

$mainjs = '<script language="JavaScript" src="' . $this->getResourceUri('js/main.js') . '" type="text/javascript"></script>';
$buttoncss = '<link rel="stylesheet" type="text/css" href="' . $this->getResourceUri('css/buttons.css') . '"/>';
$numericalcss = '<link rel="stylesheet" type="text/css" href="' . $this->getResourceUri('css/numerical.css') . '"/>';

$this->appendArrayVar('headerParams', $mainjs);
//$this->appendArrayVar('headerParams', $mcqdb);
$this->appendArrayVar('headerParams', $buttoncss);
$this->appendArrayVar('headerParams', $numericalcss);

$this->dbQuestions = $this->newObject('dbquestions');


//Set the language items
$choosetype = $this->objLanguage->languageText('mod_mcqtests_choosetype', 'mcqtests');
$typeLabel = $this->objLanguage->languageText('mod_mcqtests_typelabel', 'mcqtests');
$mcqtestLabel = $this->objLanguage->languageText('mod_mcqtests_mcqtestlabel', 'mcqtests');
$clozetestLabel = $this->objLanguage->languageText('mod_mcqtests_clozetestlabel', 'mcqtests');
$freeformLabel = $this->objLanguage->languageText('mod_mcqtests_freeformlabel', 'mcqtests');
$selectLabel = $this->objLanguage->languageText('mod_mcqtests_selectlabel', 'mcqtests');
$selectQBLabel = $this->objLanguage->languageText('mod_mcqtests_selectqblabel', 'mcqtests', 'Select question bank');
$addDescription = $this->objLanguage->languageText('mod_mcqtests_addDesc', 'mcqtests', 'Add description');
$selectqntype = $this->objLanguage->languageText('mod_mcqtests_selectqntype', 'mcqtests', 'Select question type');
$newqns = $this->objLanguage->languageText('mod_mcqtests_newqns', 'mcqtests', 'New questions');
$choosefromdb = $this->objLanguage->languageText('mod_mcqtests_choosefromdb', 'mcqtests', 'Choose from database');
$calcdqn = $this->objLanguage->languageText('mod_mcqtests_calcdqn', 'mcqtests', 'Calculated question');
$matchingqn = $this->objLanguage->languageText('mod_mcqtests_matchingqn', 'mcqtests', 'Matching Question');
$numericalqns = $this->objLanguage->languageText('mod_mcqtests_numericalqns', 'mcqtests', 'Numerical Question');
$phraseRandomShortAns = $this->objLanguage->languageText('mod_mcqtests_randomshortans', 'mcqtests');
$phraseAddA = $this->objLanguage->languageText('mod_mcqtests_phraseadda', 'mcqtests');
$phraseAddingShortAnswerQn = $this->objLanguage->languageText('mod_mcqtests_addingshortanswerqn', 'mcqtests');
//get the addicon
$objIcon = $this->newObject('geticon', 'htmlelements');
$count = count($questions);
if (empty($questions)) {
    $count = 0;
}


$mainForm = '<div id="mainform">';
echo '<strong><h1>' . $test['name'] . '</h1></strong>';

$existingQuestions = new dropdown('existingQ');
$existingQuestions->setId("existingQ");

$existingQuestions->addOption('-', '[-' . $selectQBLabel . '-]');
$existingQuestions->addOption('newQ', $newqns);
$existingQuestions->addOption('oldQ', $choosefromdb);
/*$existingQuestions->addOption('calcQ', $calcdqn);
$existingQuestions->addOption('matchQ', $matchingqn);
$existingQuestions->addOption('numericalQ', $numericalqns);
$existingQuestions->addOption('shortansQ', 'Short Answer Questions');*/
$existingQuestionsLabel = new label($selectQBLabel . " ", 'existingQ');

$batchOptions = new dropdown('qnoption');
$batchOptions->setId("qnoption");
$batchOptions->addOption('-', '[-' . $selectqntype . '-]');
$batchOptions->addOption('mcq', $selectQBLabel);
$batchOptions->addOption('freeform', $freeformLabel);
/*$batchOptions->addOption('addDescription', $addDescription);
$batchOptions->addOption('addShortAns', $phraseAddingShortAnswerQn);
$batchOptions->addOption('addRandomShortAnsMatching', $phraseAddA . " " . $phraseRandomShortAns);*/
$batchLabel = new label($selectqntype . ' ', 'input_qnoptionlabel');

$fd = $this->getObject('fieldsetex', 'htmlelements');

$fd->addLabel('<strong>' . $existingQuestionsLabel->show() . '</strong>' . $existingQuestions->show());
$fd->addLabel('<div id="qtype"><strong name=qtype>' . $batchLabel->show() . '</strong>' . $batchOptions->show() . "</div>");
$fd->setLegend($selectqntype);
$formmanager = $this->getObject('formmanager');
$calcqformmanager = $this->getObject('question_calculated_formmanager');
$numericalformmanager = $this->getObject('numerical_question');
$shortansformmanager = $this->getObject('short_answer_question');

/*$questionContentStr = '<div id="randomshortansmatching">' . $formmanager->createRandomShortAnsForm($test, $testid) . '</div>';
$questionContentStr.= '<div id="shortans">' . $formmanager->createAddShortAnswerForm($test, $testid) . '</div>';*/
$questionContentStr.='<div id="addDescription">' . $formmanager->createAddDescriptionForm($test, $testid) . '</div>';
$questionContentStr.= '<div id="addquestion">' . $formmanager->createAddQuestionForm($test) . '</div>';
$questionContentStr.='<div id="freeform">' . $formmanager->createAddFreeForm($test) . '</div>';
$questionContentStr.='<div id="dbquestions">' . $formmanager->createDatabaseQuestions($oldQuestions, $testid) . '</div>';
/*$questionContentStr.='<div id="calcquestions">' . $calcqformmanager->calcQForm($testid) . '</div>';
$questionContentStr.='<div id="matchingquestions">' . $calcqformmanager->matchingQForm($testid) . '</div>';
$questionContentStr.='<div id="numericalquestions">' . $numericalformmanager->numericalQForm($testid) . '</div>';*/

//$questionContentStr.='<div id="mcqGrid"></div>';

$fd->addLabel($questionContentStr);
$mainForm .= $fd->show() . '</div>';

echo $mainForm;
echo '<div id="mcqGrid"></div>';
$mcqdb = '<script language="JavaScript" src="' . $this->getResourceUri('js/mcqdb.js') . '" type="text/javascript"></script>';
$calcQ = '<script language="JavaScript" src="' . $this->getResourceUri('js/calculateQ.js') . '" type="text/javascript"></script>';
echo $mcqdb;
echo $calcQ;
?>