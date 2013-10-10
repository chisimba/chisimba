<?php

/**
 * Template to display the test to the student for answering.
 * @package mcqtests
 * @param array $test The test to be answered.
 * @param array $data The questions and answers in the test.
 */

$mode = $this->getParam('mode', 'mode');
if ($mode == 'notoolbar') {
    $this->setVar('pageSuppressBanner', TRUE);
    $this->setVar('pageSuppressToolbar', TRUE);
    $this->setVar('pageSuppressIM', TRUE);
} else {
    $this->setLayoutTemplate('mcqtests_layout_tpl.php');
}

// set up html elements
$objTable = &$this->loadClass('htmltable', 'htmlelements');
$objRadio = &$this->loadClass('radio', 'htmlelements');
$objButton = &$this->loadClass('button', 'htmlelements');
$objInput = &$this->loadClass('textinput', 'htmlelements');
$objForm = &$this->loadClass('form', 'htmlelements');
$objImage = &$this->loadClass('image', 'htmlelements');
$objLabel = &$this->loadClass('label', 'htmlelements');
$objLayer = &$this->loadClass('layer', 'htmlelements');
$objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
$ret = $objHighlightLabels->show();

// set up language items
$heading = $this->objLanguage->languageText('mod_mcqtests_answertest', 'mcqtests');
$testLabel = $this->objLanguage->languageText('mod_mcqtests_test', 'mcqtests');
$totalLabel = $this->objLanguage->languageText('mod_mcqtests_totalmarks', 'mcqtests');
$descriptonLabel = $this->objLanguage->languageText('mod_mcqtests_description', 'mcqtests');
$questionLabel = $this->objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
$hintLabel = $this->objLanguage->languageText('mod_mcqtests_hint', 'mcqtests');
$markLabel = $this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
$selectLabel = $this->objLanguage->languageText('mod_mcqtests_selectcorrect', 'mcqtests');
$submitLabel = $this->objLanguage->languageText('word_submit', 'system', 'Submit');
$closeLabel = $this->objLanguage->languageText('mod_mcqtests_closethiswindow', 'mcqtests','Close this window');
$continueLabel = $this->objLanguage->languageText('mod_mcqtests_continue', 'mcqtests');
$durationLabel = $this->objLanguage->languageText('mod_mcqtests_timeleft', 'mcqtests');
$gotoLabel = $this->objLanguage->languageText('mod_mcqtests_gotoquestions', 'mcqtests');
$this->setVarByRef('heading', $heading);
$alpha = array(
    '',
    'a',
    'b',
    'c',
    'd',
    'e',
    'f',
    'g',
    'h',
    'i',
    'j',
    'k',
    'l',
    'm',
    'n',
    'o',
    'p',
    'q',
    'r',
    's',
    't'
);

// Display the test details
$str = '<font size="3"><b>'.$testLabel.':</b>&nbsp;&nbsp;'.$test['name'];
$str.= '<br /><b>'.$totalLabel.':</b>&nbsp;&nbsp;'.$test['totalmark'];
$str.= '<p><b>'.$descriptonLabel.':</b><br />'.$test['description'].'</p></font>';
$counter = '';

$objTable = new htmltable();
$objTable->cellpadding = 5;
$objTable->width = '99%';
$objTable->startRow();
$objTable->addCell('', '10%');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($counter, '', '', '', '', 'colspan="2"');
$objTable->endRow();
$hidden1 = '';
$count = 0;

if (empty($data)) {
 $questionCounter = -1;
}
// Display questions
if (!empty($data)) {
    $i = $data[0]['questionorder'];
    $count = count($data) +$i-1;
    $questionCounter = $i;
    $qnum = $i;
    foreach($data as $line) {
        $row = array();
        $row[] = '<nobr><b>'.$questionLabel.' '.$questionCounter.':</b></nobr>';
        $parsed = stripslashes($line['question']);
        $parsed = $this->objWashout->parseText($parsed);
        $row[] = '<b>'.$parsed.'</b>';
        $objTable->addRow($row, 'odd" valign="top');
        $row = array();
        $row[] = '<b>'.$markLabel.':</b>';
        $row[] = '<b>'.$line['mark'].'</b>';
        $objTable->addRow($row, 'odd');
        if (!empty($line['hint'])) {
            $row = array();
            $row[] = '<b>'.$hintLabel.':</b>';
            $row[] = '<b>'.$line['hint'].'</b>';
            $objTable->addRow($row, 'odd');
        }
        // hidden elements for the question
        $objInput = new textinput('questionId'.$questionCounter, $line['id']);
        $objInput->fldType = 'hidden';
        $hidden = $objInput->show();


        // Display answers
         if (!empty($line['answers'])) {
             if ($line['questiontype'] == 'freeform' ){
                 $simple = array();
                 foreach($line['answers'] as $key => $cloze ){

                 $simple[] = $cloze['answer'];
                 }
                 $stringOut = implode(';',$simple);
                 $objInput->textinput('freeform'.$line['questionorder'], $stringOut );
                 $objInput->fldType = 'hidden';
                 $hidden.= $objInput->show();
                 $objRadio = new textinput('ans'.$line['questionorder'], '');

                 $objInput = new textinput('qtype'.$line['questionorder'], 'freeform');
                 $objInput->fldType = 'hidden';
                 $hidden.= $objInput->show();

                 }else{

                  $objRadio = new radio('ans'.$questionCounter);
                   //$objRadio = new radio('ans'.$line['id']);
                   $objRadio->setBreakSpace('<br />');

                 foreach($line['answers'] as $key => $val) {
                       $ansNum = '<b>&nbsp;'.$alpha[($key+1) ].')</b>&nbsp;&nbsp;';
                       $objRadio->addOption($val['id'], $ansNum.$val['answer']);
                       if (isset($val['selected']) && !empty($val['selected'])) {
                       $objRadio->setSelected($val['id']);
                       $objInput->textinput('selected'.$line['questionorder'], $val['selected']);
                       $objInput->fldType = 'hidden';
                       $hidden.= $objInput->show();
                       $objInput = new textinput('qtype'.$line['questionorder'], '');
                     $objInput->fldType = 'hidden';
                     $hidden.= $objInput->show();

                        }
                      }
                 }
        }
        $row = array();
        $row[] = $hidden;
        $row[] = $objRadio->show();
        $objTable->addRow($row, 'even');
        $questionCounter++;
    }
      $qnum=--$questionCounter;
    // hidden element for the first question displayed
    $objInput = new textinput('first', $i);
    $objInput->fldType = 'hidden';
    $hidden1 = $objInput->show();

    $objInput = new textinput('qnum', $qnum);
    $objInput->fldType = 'hidden';
    $hidden1.= $objInput->show();
}
// hidden element for the test id
$objInput = new textinput('id', $test['id']);
$objInput->fldType = 'hidden';
$hidden = $objInput->show() .$hidden1;

$objInput = new textinput('count', $count);
$objInput->fldType = 'hidden';
$hidden.= $objInput->show();

$objInput = new textinput('mode', $mode);
$objInput->fldType = 'hidden';
$hidden.= $objInput->show();



$objInput = new textinput('resultId', $resultId);
$objInput->fldType = 'hidden';
$hidden.= $objInput->show();
// Submit buttons

	if (!empty($data) && $questionCounter<$data[0]['count']) {
	    $objButton = new button('savebutton', $continueLabel);
// after the onclick
//document.getElementById(\'input_savebutton\').disabled=true;

	    $objButton->extra = ' ondblclick="javascript:return false" onclick="document.getElementById(\'form_submittest\').submit();"';
	    $action = 'continuetest';

	} else{
	    $objButton = new button('savebutton', $submitLabel);
	    $objButton->extra = ' ondblclick="javascript:return false" onclick= document.getElementById(\'form_submittest\').submit(); "';
	//	$objButton->setToSubmit();
	    $action = 'marktest';
	}

$objInput = new textinput('action', $action);
$objInput->fldType = 'hidden';
$hidden.= $objInput->show();

// form to submit the test
$objForm = new form('submittest', $this->uri(''));
$objForm->addToForm($objTable->show());
$str.= $objForm->show();
// navigation & submission
$javascript = "<script language=\"javascript\" type=\"text/javascript\">
    //<![CDATA[
    function submitform(val){
        window.location.href = '".str_replace("amp;", "", $this->uri(array('action' => 'previewtest','id' => $this->getParam('id'),
                    'mode' => 'notoolbar')))."&num='+val;
        //document.submittest.submit();
    }
    //]]>
    </script>";
//$url = str_replace("amp;", "", $url);
echo $javascript;
$nav = '<p align="center"><b>'.$gotoLabel.'</b></p><p align="center">';
$nav.= $this->generateLinks($data[0]['questionorder'], $data[0]['count'], 10) .'</p>';
$str.= $nav;
$objLayer = new layer();
$objLayer->padding = '10px';
$objLayer->str = $str.'<br/><a href="javascript:window.close();">'.$closeLabel.'</a>';
$pageLayer = $objLayer->show();
echo "<div class='mcq_main'>" . $ret . $pageLayer . "</div>";

?>
