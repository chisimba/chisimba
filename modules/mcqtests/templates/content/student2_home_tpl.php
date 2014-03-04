<?php


//var_dump($data);
//var_dump($_SESSION);

/**
 * Template displaying a list of tests in context to students.
 * @package mcqtests
 * @param array $data The list of tests and their details.
 */
// set up layout template
$this->setLayoutTemplate('mcqtests_layout_tpl.php');

// set up html elements
$objLink = &$this->loadClass('link', 'htmlelements');
$objTable = &$this->loadClass('htmltable', 'htmlelements');
$objIcon = &$this->newObject('geticon', 'htmlelements');
$objLayer = &$this->loadClass('layer', 'htmlelements');

// set up language items
$heading = $objLanguage->languageText('mod_mcqtests_name', 'mcqtests');
//$assignLabel = $objLanguage->languageText('mod_assignment_name', 'assignment');
$nameLabel = $objLanguage->languageText('mod_mcqtests_wordname', 'mcqtests');
$markLabel = $objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
$percentLabel = $objLanguage->languageText('mod_mcqtests_finalmark', 'mcqtests');
$totalLabel = $objLanguage->languageText('mod_mcqtests_totalmarks', 'mcqtests');
$startLabel = $objLanguage->languageText('mod_mcqtests_startdate', 'mcqtests');
$closingLabel = $objLanguage->languageText('mod_mcqtests_closingdate', 'mcqtests');
$completedLabel = $objLanguage->languageText('mod_mcqtests_completed', 'mcqtests');
$closedLabel = $objLanguage->languageText('mod_mcqtests_closed', 'mcqtests');
$testLabel = $objLanguage->languageText('mod_mcqtests_test', 'mcqtests');
$ansLabel = $objLanguage->languageText('mod_mcqtests_answertest', 'mcqtests');
$viewLabel = $objLanguage->languageText('mod_mcqtests_view', 'mcqtests') .' '.$testLabel;
$durationLabel = $objLanguage->languageText('mod_mcqtests_duration', 'mcqtests');
$unspecLabel = $objLanguage->languageText('mod_mcqtests_unspecified', 'mcqtests');
$hrLabel = $objLanguage->languageText('mod_mcqtests_hr', 'mcqtests');
$minLabel = $objLanguage->languageText('mod_mcqtests_min', 'mcqtests');
$this->setVarByRef('heading', $heading);

//Create a table
$objTable = new htmltable();
$objTable->cellpadding = '3';
$objTable->cellspacing = '2';
$objTable->width = '99%';
//Create the table header for display
$tableHd = array();
$tableHd[] = $nameLabel;
$tableHd[] = $markLabel.' (%)';
$tableHd[] = $totalLabel;
$tableHd[] = '% '.$percentLabel;
$tableHd[] = $durationLabel;
$tableHd[] = $startLabel;
$tableHd[] = $closingLabel;
$objTable->addHeader($tableHd, 'heading');
if (!empty($data)) {
    $i = 0;
    foreach($data as $line) {
        if ($line['status'] != 'inactive') {
            $class = (($i++%2) == 0) ? 'odd' : 'even';
            $closed = TRUE;
            $title = '';
            $startDate = '';
            // Display Start date and closing date - display open test link accordingly
            if (isset($line['startdate']) && !empty($line['startdate'])) {
                $startDate = $this->objDate->formatDate($line['startdate']);
            }
            if (isset($line['closingdate']) && !empty($line['closingdate'])) {
                $date = $this->objDate->formatDate($line['closingdate']);
            }
            if ($line['startdate'] < date('Y-m-d H:i')) {
                $closed = FALSE;
                if ($line['closingdate'] < date('Y-m-d H:i')) {
                    $startDate = $closedLabel;
                    $closed = TRUE;
                } else {
                    $title = $ansLabel;
                }
            }
            $dispZero = FALSE;
            // Calculate mark as a percentage for display
            if ($line['mark'] != 'none') {
                if ($line['testtype'] != 'Summative') {
                    if ($line['mark'] == 0) {
                        $mark = 0;
                    } else {
                        $mark = round($line['mark']/$line['totalmark']*100,2);
                    }
                    
                } else {
                    $mark = $completedLabel;
                }
                $action = 'showstudenttest2';
                $title = $viewLabel;
                $startDate = $completedLabel;
                $dispZero = TRUE;
            } else {
                $mark = '';
                $action = 'answertest2';
            }
            // Link to answer test or display completed test
            if (($closed && !$dispZero) || (!$closed && $dispZero)) {
                $openLink = $line['name'];
            } else {
                $objLink->title = $title;
                if ($action == 'answertest2') {
                    $objLink = new link('#');
                    $objLink->extra = "onclick=\"javascript:window.open('".$this->uri(array(
                        'action' => $action,
                        'id' => $line['id'],
                        'mode' => 'notoolbar'
                    )) ."', 'showtest', 'fullscreen,scrollbars')\"";
                } else {
                    $objLink = new link($this->uri(array(
                        'action' => $action,
                        'id' => $line['id']
                    )));
                }
                $objLink->link = $line['name'];
                if ($line['comlab'] == TRUE) {
                    $openLink = $objLink->show();
                } else {
                    $openLink = $line['name'];
                }
            }
            if ($line['timed']) {
                $duration = floor($line['duration']/60) .$hrLabel.'&nbsp;';
                $duration.= ($line['duration']%60) .$minLabel;
            } else {
                $duration = $unspecLabel;
            }
            if ($line['comlab'] == FALSE) {
                $array = array(
                    'lab' => $line['labname']
                );
                $invalidLabel = $this->objLanguage->code2Txt('mod_mcqtests_comlabinvalid', 'mcqtests', $array);
                $objTable->startRow();
                $objTable->addCell('<b><font class="error">'.$invalidLabel.'</font></b>', '', '', '', $class, 'colspan="7"');
                $objTable->endRow();
            }
            $rows = array();
            $rows[] = $openLink;
            $rows[] = $mark;
            $rows[] = $line['totalmark'];
            $rows[] = $line['percentage'];
            $rows[] = $duration;
            $rows[] = $startDate;
            $rows[] = $date;
            $objTable->addRow($rows, $class);
        }
    }
}

echo "<h3 style='color:#000099;'>".$this->objLanguage->languageText('mod_mcqtest_advanced', 'mcqtests')."</h3>";
echo $objTable->show();
// Link to Assignment Management if registered
if ($this->assignment) {
    $objLink = new link($this->uri(array(
        ''
    ) , 'assignment'));
    $objLink->title = $assignLabel;
    $objLink->link = $assignLabel;
    $objLink->extra = '';

    $objLayer = new layer();
    $objLayer->str = '<p />'.$objLink->show();
    $objLayer->align = 'center';
    echo $objLayer->show();
}
?>
