<?php
/**
 * Template for editing matching question types.
 * @package mcqtests
 */

$numericaljs = '<script language="JavaScript" src="' . $this->getResourceUri('js/numerical.js') . '" type="text/javascript"></script>';
$this->appendArrayVar('headerParams', $numericaljs);

// set up layout template
$this->setLayoutTemplate('mcqtests_layout_tpl.php');
$numericalformmanager = $this->getObject('numerical_question');
$qtype = "<h1>".$this->objLanguage->languageText('mod_mcqtests_phraseadda', 'mcqtests'). "&nbsp;";
$qtype .= $this->objLanguage->languageText('mod_mcqtests_numericalqns', 'mcqtests');
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
$questionContentStr.='<div id="numericalquestions">'.$numericalformmanager->numericalQForm($testid, $data, $edit, $questionId).'</div>';

echo $questionContentStr;
?>