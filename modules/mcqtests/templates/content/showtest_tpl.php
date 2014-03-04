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
$objTable = &$this->loadClass('htmltable', 'htmlelements');
$objLayer = &$this->loadClass('layer', 'htmlelements');
$objLink = &$this->loadClass('link', 'htmlelements');
$objIcon = &$this->newObject('geticon', 'htmlelements');


// set up language items
$studentLabel = ucfirst($this->objLanguage->languageText('mod_context_readonly', 'context'));
$heading = $this->objLanguage->languageText('mod_mcqtests_testresults', 'mcqtests');
$testLabel = $this->objLanguage->languageText('mod_mcqtests_test', 'mcqtests');
$totalLabel = $this->objLanguage->languageText('mod_mcqtests_totalmarks', 'mcqtests');
$markLabel = $this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
$questionLabel = $this->objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
$commentLabel = $this->objLanguage->languageText('mod_mcqtests_comment', 'mcqtests');
$correctAnsLabel = $this->objLanguage->languageText('mod_mcqtests_correctans', 'mcqtests');
$altAnsLabel = $this->objLanguage->languageText('mod_mcqtests_altans', 'mcqtests');
$noAnsLabel = $this->objLanguage->languageText('mod_mcqtests_unanswered', 'mcqtests');
$yourAnsLabel = $this->objLanguage->languageText('mod_mcqtests_answer', 'mcqtests');
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
$percent = round($result['mark']/$totalmark*100, 2);
$studentName = $this->objUser->fullName($result['studentid']);

$objTable = new htmltable();
$objTable->width = '450px';
$objTable->cellpadding = 4;
if($this->objUser->userId() != $result['studentid'])
{
    $objTable->startRow();
    $objTable->addCell($this->objUser->getUserImage($result['studentid'], TRUE), '20%', '', '', '', 'rowspan = 4', '');
    $objTable->addCell('<b>'.$studentLabel.':</b>', '20%', '', '', '', 'style="white-space: nowrap"', '');
    $objTable->addCell($studentName, '', '', '', '', 'style="white-space: nowrap"', '');
    $objTable->endRow();
}
$objTable->startRow();
$objTable->addCell('<b>' . $testLabel . ':</b>', '', '', '', '', 'style="white-space: nowrap"', '');
$objTable->addCell($result['name'], '', '', '', '', 'style="white-space: nowrap"', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>' . $totalLabel . ':</b>', '', '', '', '', 'style="white-space: nowrap"', '');
$objTable->addCell($totalmark, '', '', '', '', 'style="white-space: nowrap"', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>' . $markLabel . ':</b>', '', '', '', '', 'style="white-space: nowrap"', '');
$objTable->addCell($result['mark'] . '&nbsp;&nbsp;(' . $percent . ' %)' , '', '', '', '', 'style="white-space: nowrap"', '');
$objTable->endRow();

$str = '<div style="width:450px; border: 1px solid black;">' . $objTable->show() . '</div>';

$objTable = new htmltable();
$objTable->cellpadding = 5;
$objTable->width = '99%';
$objTable->startRow();
$objTable->addCell('', '15%');
$objTable->endRow();
if (!empty($data)) {
    $qNum = $data[0]['questionorder'];
    $objIcon->setIcon('accept', 'png');
    $tickIcon = $objIcon->show();
    $objIcon->setIcon('delete', 'png');
    $crossIcon = $objIcon->show();
    foreach($data as $line) {
//        if ($line['questiontype'] == 'freeform' && $line['answerorder'] != 1) {
//            // Skip row as it is an alternative answer
//            continue;
//        }
        switch ($line['questiontype']) {
            case 'mcq':
            case 'tf':
                $ansNum = '&nbsp;&nbsp;&nbsp;'.$alpha[$line['answerorder']].')';
                $content = '<b>'.$correctAnsLabel.':'.$ansNum.'</b>&nbsp;&nbsp;&nbsp;'.$line['answer'].'<br />';
                break;
            case 'freeform':
                //$ansNum = '&nbsp;&nbsp;&nbsp;'.$alpha[$line['answerorder']].')';
                $content = '<b>'.$correctAnsLabel.':'./*$ansNum.*/'</b>&nbsp;&nbsp;&nbsp;'.$line['answer'].'<br />';
                if ($line['alternativeanswers']) {
                    foreach ($line['alternativeanswers'] as $altAnswer) {
                        $content .= '<b>'.$altAnsLabel.':</b>&nbsp;&nbsp;&nbsp;'.$altAnswer['answer'].'<br />';
                    }
                }
                break;
            default:
                ;
        }
        switch ($line['questiontype']) {
            case 'mcq':
            case 'tf':
                if (!$line['studcorrect']) {
                    if (!empty($line['studorder']) && !empty($line['studans'])) {
                        $ansNum = '&nbsp;&nbsp;&nbsp;'.$alpha[$line['studorder']].')';
                        $content.= '<b>'.$yourAnsLabel.':'.$ansNum.'</b>&nbsp;&nbsp;&nbsp;'.$line['studans'].'<br />';
                    } else {
                        $content.= $noAnsLabel;
                    }
                    $icon = $crossIcon;
                } else {
                    $icon = $tickIcon;
                }
                break;
            case 'freeform':
                //if (!empty($line['answered'])) {
                if (is_null($line['answered'])) {
                    $content.= $noAnsLabel;
                } else {
                    //$ansNum = '&nbsp;&nbsp;&nbsp;'.$alpha[$line['studorder']].')';
                    $content.= '<b>'.$yourAnsLabel.':'./*$ansNum.*/'</b>&nbsp;&nbsp;&nbsp;'.$line['answered'].'<br />';
                }
                //if (!$line['studcorrect']) {
                if (isset($line['studcorrect']) && $line['studcorrect'] == '1') {
                    $icon = $tickIcon;
                } else {
                    $icon = $crossIcon;
                }
                break;
            default:
                ;
        }
//        echo '<pre>';
//        var_dump($line);
//        echo '</pre>';
        if (!empty($line['commenttext'])) { //studcomment
            $content.= '<b>'.$commentLabel.':</b>&nbsp;&nbsp;&nbsp;'.$line['commenttext'].'<br />';
        }

        //$objLayer = new layer();
        //$objLayer->align = 'left';
        //$objLayer->left = '; float:left'; //margin-right: 20px;
        //$objLayer->cssClass = 'forumTopic';
        $parsedQuestion = $this->objWashout->parseText($line['question']);
        //$objLayer->str = '<b>'.$questionLabel.' '.$line['questionorder'].':</b>'.$parsedQuestion.$content;
        ; //&nbsp;&nbsp;&nbsp;
        //$contentLayer = $objLayer->show();
        $contentLayer = '<div class="viewquiz"><div class="coolwidget_100"><b>' . $questionLabel . '&nbsp;' . $line['questionorder'] . ':</b>' . $parsedQuestion . $content . '</div></div>';

//        $objLayer = new layer();
//        $objLayer->cssClass = 'forumContent';
//        $objLayer->str = $question;
//        $answers = $objLayer->show();
//
//        $objLayer = new layer();
//        $objLayer->cssClass = 'topicContainer';
//        $objLayer->str = $answers;
//        $str.= $objLayer->show();
//        $objLayer = new layer();

//        $objLayer->cssClass = 'forumBase';
//        $objLayer->str = '';
//        $str.= $objLayer->show() .'<br />';

        $objLayer = new layer();
        //$objLayer->align = 'right';
        //$objLayer->cssClass = 'forumTopic';
        $objLayer->str = $icon;
        $iconLayer = $objLayer->show();

        $str .= '<table width="100%"><tr><td width="90%" valign="top">'.$contentLayer.'</td><td width="10%" valign="top">'.$iconLayer.'</td></tr></table>';

        $qNum = $line['questionorder'];
    }
}
if ($this->contextUsers->isContextLecturer())
{
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

    $objLink = new link($this->uri(array(
        'action' => 'liststudents',
        'id' => $result['testid']
    )));
    $objLink->link = $exitLabel;
    $links.= $objLink->show();
}
else
{
    $objLink = new link($this->uri(array(
        'action' => 'newhome',
    ), 'mcqtests'));
    $objLink->link = $exitLabel;
    $links = $objLink->show();
}
$objLayer = new layer();
$objLayer->str = '<p />'.$links;
$objLayer->cssClass = '';
$objLayer->align = 'center';
$str.= $objLayer->show();
echo $str;
?>
