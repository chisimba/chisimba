<?php
/**
 * Template for displaying a completed test to a student.
 * The test is displayed with the question, the students answer and the correct answer.
 * Along with the lecturers comment on the students choice.
 * @package mcqtests
 * @param array $result The students mark and the test details.
 * @param array $data The test questions with the correct answer and the students answer.
 */
// set up layout template
$this->setLayoutTemplate('mcqtests_layout_tpl.php');

// set up html elements
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objLayer = $this->loadClass('layer', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');

// set up language items
$studentLabel = ucfirst($this->objLanguage->languageText('mod_context_readonly', 'context'));
$heading = $this->objLanguage->languageText('mod_mcqtests_testresults', 'mcqtests');
$testLabel = $this->objLanguage->languageText('mod_mcqtests_test', 'mcqtests');
$totalLabel = $this->objLanguage->languageText('mod_mcqtests_totalmarks', 'mcqtests');
$markLabel = $this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
$questionLabel = $this->objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
$commentLabel = $this->objLanguage->languageText('mod_mcqtests_comment', 'mcqtests');
$correctAnsLabel = $this->objLanguage->languageText('mod_mcqtests_correctans', 'mcqtests');
$yourAnsLabel = $this->objLanguage->languageText('mod_mcqtests_yourans', 'mcqtests');
$noAnsLabel = $this->objLanguage->languageText('mod_mcqtests_unanswered', 'mcqtests');
$exitLabel = $this->objLanguage->languageText('word_exit');
$nextLabel = $this->objLanguage->languageText('mod_mcqtests_next', 'mcqtests');
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
$percent = round($result['mark']/$result['totalmark']*100, 2);
$str = '<font size="3"><b>'.$testLabel.':</b>&nbsp;&nbsp;&nbsp;'.$result['name'].'<br />';
$str.= '<b>'.$totalLabel.':</b>&nbsp;&nbsp;&nbsp;'.$result['totalmark'].'<br />';
$str.= '<b>'.$markLabel.':</b>&nbsp;&nbsp;&nbsp;'.$result['mark'].'&nbsp;&nbsp;('.$percent.'%)<p /></font>';
$i=0;
$objTable = new htmltable();
$objTable->cellpadding = 5;
$objTable->width = '99%';
$objTable->startRow();
$objTable->addCell('', '15%');
$objTable->endRow();
if (!empty($data)) {
    $qNum = $data[0]['questionorder'];
    $objIcon->setIcon('greentick');
    $tickIcon = $objIcon->show();
    $objIcon->setIcon('redcross');
    $crossIcon = $objIcon->show();

    foreach($data as $line) {
        $ansNum = '&nbsp;&nbsp;&nbsp;'.$alpha[$line['answerorder']].')';
        if ($line['questiontype'] == 'freeform'){
         $content = '<b>'.$correctAnsLabel.':</b>&nbsp;&nbsp;&nbsp;'.$line['studfreecorrect'].'<br />';
        }else{
        $content = '<b>'.$correctAnsLabel.':'.$ansNum.'</b>&nbsp;&nbsp;&nbsp;'.$line['answer'].'<br />';
        }
        if (!$line['studcorrect']) {
            if (!empty($line['studorder']) && !empty($line['studans'])) {
                $ansNum = '&nbsp;&nbsp;&nbsp;'.$alpha[$line['studorder']].')';
                $content.= '<b>'.$yourAnsLabel.':'.$ansNum.'</b>&nbsp;&nbsp;&nbsp;'.$line['studans'].'<br />';

           }else{

                $content.= $noAnsLabel;

            }
                $icon = $crossIcon;
        } else {
            $icon = $tickIcon;
        }
        if($line['questiontype'] == 'freeform'){
                $content.= '<b>'.$yourAnsLabel.':</b>&nbsp;&nbsp;&nbsp;'.$line['studfreeans'].'<br />';
                $icon = $tickIcon;
     }
        if (!empty($line['studcomment'])) {
            $content.= '<b>'.$commentLabel.':</b>&nbsp;&nbsp;&nbsp;'.$line['studcomment'].'<br />';
        }

        $objLayer = new layer();
        $objLayer->str = $icon;
        $objLayer->align = 'right';
        $objLayer->cssClass = 'forumTopic';
        $iconLayer = $objLayer->show();

        $parsed = stripslashes($line['question']);
        $parsed = $this->objWashout->parseText($parsed);

        $objLayer = new layer();

        $objLayer->left = '; margin-right: 20px; float:left';
        $objLayer->cssClass = 'forumTopic';

        $objLayer->str = '<b>'.$questionLabel.' '.$line['questionorder'].':</b>&nbsp;&nbsp;&nbsp;'.$parsed;

        $question = $objLayer->show() .$iconLayer;

        $objLayer = new layer();
        $objLayer->cssClass = 'forumContent';
        $objLayer->str = $content;
        $answers = $objLayer->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'topicContainer';
        $objLayer->str = $question.$answers;
        $str.= $objLayer->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'forumBase';
        $objLayer->str = '';
        $str.= $objLayer->show() .'<br />';
        $qNum = $line['questionorder'];
    }
}
$links = '';
if ($qNum < $data[0]['count']) {
    $objLink = new link($this->uri(array(
        'action' => 'showtest',
        'id' => $result['testid'],
        'qnum' => $qNum,
        'studentId' => $result['studentid']
    )));
    $objLink->link = $nextLabel;
    $links = $objLink->show() .'&nbsp;&nbsp;|&nbsp;&nbsp;';
}

/*
$objLink = new link($this->uri(array(
    'action' => 'liststudents',
    'id' => $result['testid']
)));
$objLink->link = $exitLabel;
$links.= $objLink->show();
*/
$objLink = new link($this->uri(array()));
$objLink->link = $exitLabel;
$links.= $objLink->show();

$objLayer = new layer();
$objLayer->str = '<p />'.$links;
$objLayer->cssClass = '';
$objLayer->align = 'center';
$str.= $objLayer->show();
echo $str;
//for($q=0;$q<$i;$q++)
//}
?>