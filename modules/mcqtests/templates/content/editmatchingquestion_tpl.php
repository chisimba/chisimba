<?php
/**
 * Template for editing matching question types.
 * @package mcqtests
 */

// set up layout template
$this->setLayoutTemplate('mcqtests_layout_tpl.php');
$matchingformmanager = $this->getObject('question_calculated_formmanager');
$qtype =  "<h1>";
$qtype .= $this->objLanguage->languageText('mod_mcqtests_phraseadda', 'mcqtests'). "&nbsp;";
$qtype .= $this->objLanguage->languageText('mod_mcqtests_matchingqn', 'mcqtests');
$qtype .= "</h1>";
echo $qtype;

$testid = $this->getParam('id');
if($mode == 'edit') {
    $edit = true;
    $questionId = $this->getParam('questionId');
} else {
    $edit = false;
}
// display the form for editing this question
$questionContentStr.='<div id="matchingquestions">'.$matchingformmanager->matchingQForm($testid, $data, $edit, $questionId).'</div>';

echo $questionContentStr;
?>