<?php
// load Simple Calculated Questions
$form = $this->objFormManager->createSCQList($testId, Null, $deletemsg, $addmsg);
echo $form;
// load Descriptions
$form = $this->objFormManager->createDescriptionList(Null, $testId);
echo $form;
// load Random Short Answers
$form = $this->objFormManager->createRSAList($this->contextCode, Null, $testId);
echo $form;
?>