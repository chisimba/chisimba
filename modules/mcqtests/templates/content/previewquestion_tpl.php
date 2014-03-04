<?php
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('pageSuppressIM', TRUE);

$closeLabel = $this->objLanguage->languageText('mod_mcqtests_close', 'mcqtests');

// set up html elements
$this->loadClass('radio', 'htmlelements');

$cfieldset = $this->getObject('fieldset', 'htmlelements');
$cfieldset->legend = "Preview Question";
$objTable = $this->newObject('htmltable', 'htmlelements');

$objTable->cellpadding = 5;
// get the question for this id
$data = $this->dbQuestions->getQuestion($id);

//get the answers for this question
$answers = $this->dbAnswers->getAnswers($id);//print_r($answers);
$lbEnable1 = "a";
$lbEnable2 = "b";
$objTable->startRow();
$questionLabel = $data[0]['question'];
$headStr.= $questionLabel;
$objTable->addCell($headStr);
$objTable->endRow();

$objTable->startRow();
$objRadio = new radio('question');
$objRadio->setBreakSpace('<br>');
foreach($answers as $row){
    $objRadio->addOption($row['id'], $row['answer']);
    if($row['correct'] == 1) {
        $objRadio->setSelected($row['id']);
    }
}
$objTable->addCell($objRadio->show());
$objTable->endRow();

$objTable->startRow();
$objButton=new button('close',$closeLabel, "window.close()");
$objTable->addCell($objButton->show());
$objTable->endRow();
$cfieldset->addContent($objTable->show());
echo $cfieldset->show();

?>
