<?php
/**
* Template for students Assignment Management Home page.
* @package assignment
*/

/**
* Template for students Assignment Management Home page.
* @param array $essayData The list of essays.
* @param array $wsData The list of worksheets.
* @param array $assignData The list of assignments.
* @param array $testData The list of tests.
*/
$this->setLayoutTemplate('assignment_layout_tpl.php');

// Set up html elements
$this->loadClass('htmltable','htmlelements');
$this->loadClass('htmlheading','htmlelements');
$this->loadClass('link','htmlelements');
$objIcon = $this->newObject('geticon','htmlelements');
$objTimeOut = $this->newObject('timeoutMessage','htmlelements');

$objTrim = $this->getObject('trimstr', 'strings');

$repWords = array('authors'=>'lecturers');

// Set up language items
$assignmentsLabel = $this->objLanguage->languageText('mod_assignment_assignments','assignment');
$nameLabel = $this->objLanguage->languageText('mod_assignment_wordname','assignment');
$descriptionLabel = $this->objLanguage->languageText('mod_assignment_description','assignment');
$lecturerLabel = ucwords($this->objLanguage->languageText('mod_context_author'));
$dueLabel = $this->objLanguage->languageText('mod_assignment_closingdate','assignment');
$statusLabel = $this->objLanguage->languageText('mod_assignment_status','assignment');
$noAssignLabel = $this->objLanguage->languageText('word_no').' '.$assignmentsLabel;
$worksheetLabel = $this->objLanguage->languageText('mod_worksheet_name');
$wsHead = $this->objLanguage->languageText('mod_worksheet_worksheets');
$essayLabel = $this->objLanguage->languageText('mod_essay_name');
$essay = $this->objLanguage->languageText('mod_essay_essay');
$topicLabel = $this->objLanguage->languageText('mod_essay_topic');
$essayHead = $this->objLanguage->languageText('mod_essay_essays');
$markLabel = $this->objLanguage->languageText('mod_assignment_mark','assignment');
$openLabel = $this->objLanguage->languageText('mod_assignment_open','assignment');
$closedLabel = $this->objLanguage->languageText('mod_assignment_closed','assignment');
$viewLabel = $this->objLanguage->languageText('mod_assignment_view','assignment');
$bookLabel = $this->objLanguage->languageText('mod_assignment_bookessay');
$submittedLabel = $this->objLanguage->languageText('mod_assignment_submitted','assignment');
$commentLabel = ucwords($this->objLanguage->code2Txt('mod_assignment_viewlecturerscomment',$repWords));
$typeLabel = $this->objLanguage->languageText('mod_assignment_assignmenttype','assignment');
$uploadLabel = $this->objLanguage->languageText('mod_assignment_upload','assignment');
$downloadLabel = $this->objLanguage->languageText('mod_assignment_downloadassignment');
$onlineLabel = $this->objLanguage->languageText('mod_assignment_online','assignment');
$testLabel = $this->objLanguage->languageText('mod_test_name');
$rubricLabel = $this->objLanguage->languageText('mod_rubric_name');
$completedLabel = $this->objLanguage->languageText('word_completed');

// set up links to essay management, worksheets and test if registered
$leftLinks = '';
$rightLinks = '';
if($this->essay){
    $objIcon->setIcon('modules/essay');
    $objIcon->title = $openLabel.' '.$essayLabel;
    $objLink = new link($this->uri(array(''),'essay'));
    $objLink->link = $objIcon->show();
    $objLink->title = $bookLabel;
    $essayLink = '<br />'.$objLink->show();
    $objLink->link = $essayLabel;
    $essayLink .= '&nbsp;&nbsp;'.$objLink->show();
    $leftLinks .= $essayLink;
}
if($this->ws){
    $objIcon->setIcon('modules/worksheet');
    $objIcon->title = $openLabel.' '.$worksheetLabel;
    $objLink = new link($this->uri(array(''),'worksheet'));
    $objLink->link = $objIcon->show();
    $objLink->title = $openLabel.' '.$worksheetLabel;
    $wsLink = '<p/>'.$objLink->show();
    $objLink->link = $worksheetLabel;
    $wsLink .= '&nbsp;&nbsp;'.$objLink->show();
    $leftLinks .= $wsLink;
}
if($this->test){
    $objIcon->setIcon('modules/test');
    $objIcon->title = $openLabel.' '.$testLabel;
    $objLink = new link($this->uri(array(''),'test'));
    $objLink->link = $objIcon->show();
    $objLink->title = $openLabel.' '.$testLabel;
    $testLink = '<p/>'.$objLink->show();
    $objLink->link = $testLabel;
    $testLink .= '&nbsp;&nbsp;'.$objLink->show();
    if(!$this->essay && !$this->ws){
        $leftLinks .= $testLink;
    }else{
        $rightLinks .= $testLink;
    }
}
if($this->rubric){
    $objIcon->setModuleIcon('rubric');
    $objIcon->title = $openLabel.' '.$rubricLabel;
    $objLink = new link($this->uri(array(''),'rubric'));
    $objLink->link = $objIcon->show();
    $objLink->title = $openLabel.' '.$rubricLabel;
    $rubricLink = $objLink->show();
    $objLink->link = $rubricLabel;
    $rubricLink .= '&nbsp;&nbsp;'.$objLink->show();
    if(!$this->essay && !$this->ws){
        $leftLinks .= '<p>'.$rubricLink.'</p>';
    }else{
        $rightLinks .= '<p>'.$rubricLink.'</p>';
    }
}

$objTable = new htmltable();

$objTable->startRow();
$objTable->addCell($leftLinks, '50%');
$objTable->addCell($rightLinks, '50%');
$objTable->endRow();

echo $objTable->show();

// reinitialise table
$objTable->init();

if(isset($msg)){
    $objTimeOut->setMessage($msg);
    echo '<p/>'.$objTimeOut->show();
}

if($this->essay){
    $objHead = new htmlheading();
    $objHead->str=$essayHead;
    $objHead->type=3;

    echo $objHead->show();

    // List essays in a table
    $objTable->cellpadding=2;
    $objTable->cellspacing=2;

    $tableHd = array();
    $tableHd[] = $topicLabel;
    $tableHd[] = $essay;
    $tableHd[] = $lecturerLabel;
    $tableHd[] = $dueLabel;
    $tableHd[] = $statusLabel;

    $objTable->addHeader($tableHd,'heading');

    if(!empty($essayData)){
        $i = 0; $status = '';
        foreach($essayData as $line){
            $class = ($i++%2 == 0) ? 'odd' : 'even';
            $status = '';

            if(isset($line['unassigned'])){
                $repArray = array('number'=>$line['unassigned']);
                if($line['unassigned'] == 1){
                    $text = $this->objLanguage->code2Txt('mod_assignment_newessaytopic', $repArray);
                }else{
                    $text = $this->objLanguage->code2Txt('mod_assignment_newessaytopics', $repArray);
                }
                $objTable->startRow();
                $objTable->addCell($text,'','','',$class,'colspan=5');
                $objTable->endRow();
            }else{
                if(!empty($line['mark'])){
                    $objIcon->setIcon('comment_view');
                    $objIcon->title=$commentLabel;
                    $objIcon->extra="onclick=\"javascript:window.open('" .$this->uri(array('action'=>'showcomment','book'=>$line['id'],'essay'=>$line['essayName']),'essay')."', 'essaycomment', 'width=400, height=200, scrollbars=1')\" ";
                    $objLink = new link('#');
                    $objLink->link = $objIcon->show();
                    $status .= $markLabel.' = '.$line['mark'].'%&nbsp;'.$objLink->show();
                }else if($line['submitdate'] > 0){
                    $status = $submittedLabel;
                }else if($line['closing_date'] < date('Y-m-d H:i:s', time())){
                    $status = $closedLabel;
                }else{
                    $status = $openLabel;
                }

                $objTable->startRow();
                $objTable->addCell($line['topicName'],'20%','','',$class);
                $objTable->addCell($line['essayName'],'','','',$class);
                $objTable->addCell($this->objUser->fullname($line['lecturer']),'15%','','',$class);
                $objTable->addCell($this->formatDate($line['closing_date']),'15%','','',$class);
                $objTable->addCell($status,'12%','','',$class);
                $objTable->endRow();
            }
        }
    }else {
        $objTable->startRow();
        $objTable->addCell($noAssignLabel,'','','','odd','colspan=5');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell('','20%');
        $objTable->addCell('');
        $objTable->addCell('','15%');
        $objTable->addCell('','15%');
        $objTable->addCell('','12%');
        $objTable->endRow();
    }
    echo $objTable->show();
}

if($this->ws){
    $objHead = new htmlheading();
    $objHead->str=$wsHead;
    $objHead->type=3;

    echo $objHead->show();

    $tableHd = array();
    $tableHd[] = $nameLabel;
    $tableHd[] = $descriptionLabel;
    $tableHd[] = $lecturerLabel;
    $tableHd[] = $dueLabel;
    $tableHd[] = $statusLabel;

    $objTable1 = new htmltable();
    $objTable1->addHeader($tableHd,'heading');

    // List worksheets in a table
    $objTable1->cellpadding=2;
    $objTable1->cellspacing=2;

    if(!empty($wsData)){
        $i = 0; $status = ''; $description = '';
        foreach($wsData as $line){
            if(!($line['activity_status'] == 'inactive')){
                $class = ($i++%2 == 0) ? 'odd' : 'even';

                if(strlen($line['description'])>100){
                    $description = substr($line['description'],0,100).'...';
                }else $description = $line['description'];

                if($line['activity_status'] == 'marked'){
                    $status = $markLabel.' = '.$line['mark'].'%';
                }else if($line['completed'] == 'Y'){
                    $status = $submittedLabel;
                }else if($line['closing_date'] < date('Y-m-d H:i:s', time())){
                    $status = $closedLabel;
                }else{
                    $status = $this->objLanguage->languageText('mod_worksheet_activity'.$line['activity_status']);
                }

                $objTable1->startRow();
                $objTable1->addCell($line['name'],'20%','','',$class);
                $objTable1->addCell($objTrim->strTrim(strip_tags($description), 50),'','','',$class);
                $objTable1->addCell($this->objUser->fullname($line['userid']),'15%','','',$class);
                $objTable1->addCell($this->formatDate($line['closing_date']),'15%','','',$class);
                $objTable1->addCell($status,'12%','','',$class);
                $objTable1->endRow();
            }
        }
    }else {
        $objTable1->startRow();
        $objTable1->addCell($noAssignLabel,'','','','odd','colspan=5');
        $objTable1->endRow();

        $objTable1->startRow();
        $objTable1->addCell('','20%');
        $objTable1->addCell('');
        $objTable1->addCell('','15%');
        $objTable1->addCell('','15%');
        $objTable1->addCell('','12%');
        $objTable1->endRow();
    }
    echo $objTable1->show();
}

if($this->test){
    $objHead = new htmlheading();
    $objHead->str = $testLabel;
    $objHead->type = 3;

    echo $objHead->show();

    $tableHd = array();
    $tableHd[] = $nameLabel;
    $tableHd[] = $descriptionLabel;
    $tableHd[] = $lecturerLabel;
    $tableHd[] = $dueLabel;
    $tableHd[] = $statusLabel;

    $objTable3 = new htmltable();
    $objTable3->addHeader($tableHd, 'heading');

    $objTable3->cellpadding=2;
    $objTable3->cellspacing=2;

    if(!empty($testData)){
        $i = 0;
        foreach($testData as $line){
            if(!($line['status'] == 'inactive')){
                $class = (($i++ % 2) == 0) ? 'odd':'even';

                if(strlen($line['description'])>100){
                    $description = substr($line['description'],0,100).'...';
                }else $description = $line['description'];

                $mark = '';
                if($line['mark'] != 'none'){
                    if($line['testType']=='summative'){
                        $mark = $completedLabel;
                    }else{
                        $mark = $markLabel.' = '.round(($line['mark'] / $line['totalMark'] *    100)).'%';
                    }
                }else if($line['closing_date'] < date('Y-m-d H:i:s')){
                        $mark = $closedLabel;
                }else{
                        $mark = $this->objLanguage->languageText('mod_testadmin_'.$line['status']);

                }
                $objTable3->startRow();
                $objTable3->addCell($line['name'],'20%','','',$class);
                $objTable3->addCell($objTrim->strTrim(strip_tags($description), 50),'','','',$class);
                $objTable3->addCell($this->objUser->fullname($line['userid']),'15%','','',$class);
                $objTable3->addCell($this->formatDate($line['closing_date']),'15%','','',$class);
                $objTable3->addCell($mark,'12%','','',$class);
                $objTable3->endRow();
            }
        }
    }else {
        $objTable3->startRow();
        $objTable3->addCell($noAssignLabel,'','','','odd','colspan="6"');
        $objTable3->endRow();

        $objTable3->startRow();
        $objTable3->addCell('','20%');
        $objTable3->addCell('');
        $objTable3->addCell('','15%');
        $objTable3->addCell('','15%');
        $objTable3->addCell('','12%');
        $objTable3->endRow();
    }
    echo $objTable3->show();
}

$objHead = new htmlheading();
$objHead->str=$assignmentsLabel;
$objHead->type=3;

echo $objHead->show();

$tableHd = array();
$tableHd[] = $nameLabel;
$tableHd[] = $typeLabel;
$tableHd[] = $descriptionLabel;
$tableHd[] = $lecturerLabel;
$tableHd[] = $dueLabel;
$tableHd[] = $statusLabel;

$objTable2 = new htmltable();
 
$objTable2->addHeader($tableHd,'heading');

// List worksheets in a table
$objTable2->cellpadding=2;
$objTable2->cellspacing=2;

if(!empty($assignData)){
    $i = 0; $status = ''; $description = '';
    foreach($assignData as $line){
        $class = ($i++%2 == 0) ? 'odd' : 'even';
        $noLink = TRUE;

        // Check the status of the assignment
        if(!empty($line['studentMark'])){
            // Display mark if exists
            $objIcon->setIcon('comment_view');
            $objIcon->title=$commentLabel;
            $objIcon->extra="onclick=\"javascript:window.open('" .$this->uri(array(
            'action'=>'showcomment', 'id'=>$line['submitid'], 'name'=>$line['name']))
            ."', 'assignmentcomment', 'width=400, height=200, scrollbars=1')\" ";
            $objLink = new link('#');
            $objLink->link = $objIcon->show();
            $status = $markLabel.' = '.$line['studentMark'].'%&nbsp;'.$objLink->show();

            // if uploaded assignment, allow student to download marked assignment
            if($line['format']){
                $objIcon->setIcon('download');
                $objIcon->title=$downloadLabel;
                $objIcon->extra='';
                $objLink = new link($this->uri(array('action'=>'download','fileid'=>$line['fileid'])));
                $objLink->link = $objIcon->show();
                $status .= '&nbsp;'.$objLink->show();
            }
        }else if($line['closing_date'] < date('Y-m-d H:i')){
            $status = $closedLabel;
            $noLink = TRUE;
        }else if(!empty($line['datesubmitted'])){
            // Status = submitted without resubmission
            $status = $submittedLabel;
            if($line['resubmit']){
                $noLink = FALSE;
            }
        }else{
            $noLink = FALSE;
            $status = $openLabel;
        }


        // Link the name for viewing the assignment and submitting
        if($noLink){
            $name = $line['name'];
        }else{
            $objLink = new link($this->uri(array('action'=>'view', 'id'=>$line['id'])));
            $objLink->title = $viewLabel.' '.$line['name'];
            $objLink->link = $line['name'];
            $name = $objLink->show();
        }

        // Truncate the description if more than 100 characters
        //if(strlen($line['description'])>100){
        //    $description = substr($line['description'],0,100).'...';
        //}else{
            $description = $line['description'];
        //}

        // Display whether the assignment is online or uploadable
        if($line['format']){
            $format = $uploadLabel;
        }else{
            $format = $onlineLabel;
        }

        $objTable2->startRow();
        $objTable2->addCell($name,'20%','','',$class);
        $objTable2->addCell($format,'13%','','',$class);
        $objTable2->addCell($objTrim->strTrim(strip_tags($description), 50),'','','',$class);
        $objTable2->addCell($this->objUser->fullname($line['userid']),'15%','','',$class);
        $objTable2->addCell($this->objDate->formatDate($line['closing_date']),'15%','','',$class);
        $objTable2->addCell($status,'12%','','',$class);
        $objTable2->endRow();
    }
}else {
    $objTable2->startRow();
    $objTable2->addCell($noAssignLabel,'','','','odd','colspan="6"');
    $objTable2->endRow();

    $objTable2->startRow();
    $objTable2->addCell('','20%');
    $objTable2->addCell('', '13%');
    $objTable2->addCell('');
    $objTable2->addCell('','15%');
    $objTable2->addCell('','15%');
    $objTable2->addCell('','12%');
    $objTable2->endRow();
}
echo $objTable2->show();


if ($objUser->isCourseAdmin()) {
    $link = new link ($this->uri(NULL, 'assignmentadmin'));
    $link->link = $this->objLanguage->languageText('mod_assignmentadmin_name', 'assignmentadmin', 'Assignment Management');
    
    echo '<p>'.$link->show().'</p>';

}

?>