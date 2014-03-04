<?php
/* -------------------- gradebook class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
//set the layout
$this->setLayoutTemplate('gradebook_layout_tpl.php');

//load required form elements
$this->loadClass('link','htmlelements');
$this->loadClass('checkbox','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('hiddeninput','htmlelements');

//help
$this->objHelp = $this->newObject('helplink','help');
//context object
$objContext = $this->getObject('dbcontext','context');
//assignment object
$objAssignment = $this->getObject('dbassignment_old','assignment');
$objAssignmentNew = $this->getObject('dbassignment','assignment');
$objAssignmentSubmit =& $this->getObject('dbassignmentsubmit_old','assignment');
//essay object
$objEssaytopics = $this->getObject('dbessay_topics','essay');
$objEssaybook = $this->getObject('dbessay_book','essay');
//testadmin object
$objTestadmin = $this->getObject('dbtestadmin','mcqtests');
$objTestresults = $this->getObject('dbresults','mcqtests');
//worksheet object
$objWorksheet = $this->getObject('dbworksheet','worksheet');
$objWorksheetresults =& $this->getObject('dbworksheetresults','worksheet');
//datetime object
$objDatetime =& $this->getObject('dateandtime','utilities');
//$objFormattedDate =& $this->getObject('simplecal','datetime');

//context management
$contextObject =& $this->getObject('dbcontext', 'context');
$contextCode = $contextObject->getContextCode();
$theCourse=0;

// Get request parameters.
$action = $this->getParam('action', NULL);
$assessment = $this->getParam('assessment', NULL);
$studentUserId = $this->getParam('studentuserid', NULL);
$check = $this->getParam('check', NULL);

// Ensure an assessment has been specified.
$studentUserIds = $this->objGradebook->getStudentInContextInfo('userid');
if (!is_array($studentUserIds)) {
    die($objLanguage->languageText('mod_gradebook_noassessment', 'gradebook'));
}

// Ensure the current user is not a student attempting to access another student's records.
$currentUserId = $this->objUser->userId();
if (in_array($currentUserId, $studentUserIds) && $studentUserId != $currentUserId) {
    die($objLanguage->languageText('mod_gradebook_noaccess', 'gradebook'));
}

// Create the general form class.
$objForm = new form('upload');
$objForm->setAction($this->uri(array('action'=>$action,'assessment'=>$assessment,'studentUserId'=>$studentUserId)));
$objForm->displayType=3;  //Free form

$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1; //Heading <h3>
//$this->objH->align="center";
$this->objH->str=($contextCode?$contextObject->getMenuText($contextCode):'').' '.$objLanguage->languageText('mod_gradebook_title','gradebook');
$this->objH->str.=' - ';
$this->objH->str.=$this->objUser->fullname($studentUserId);
echo $this->objH->show();
echo '<br />';
echo '<strong>'.$objLanguage->languageText('mod_gradebook_studentNumber','gradebook').':</strong> '.$this->objUser->username($studentUserId);

//select course text, for proper alignment, fit within table
$this->TableInstructions = $this->newObject('htmltable', 'htmlelements');
$this->TableInstructions->cellspacing="2";
$this->TableInstructions->width="100%";
//$this->TableInstructions->attributes="align=\"center\"";

$this->TableInstructions->startHeaderRow();
$this->TableInstructions->addHeaderCell("&nbsp;");
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_closingDate','gradebook'),"15%");
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_assessment','gradebook'),"25%");
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_assessmentType','gradebook'),"15%");
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_mark','gradebook'),"5%");
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_classAvg','gradebook')."(%)","17%");
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_markplain','gradebook'),"10%");
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_percentyearmark','gradebook'),"13%");
$this->TableInstructions->endHeaderRow();

$numberAssignments=0;
$numberAssignments=count($objAssignment->getAssignment($contextCode));
$totalMark=0;
$totalPercentMark=0;
$totalAvgMark=0;
$totalPercentYrMark=0;
$count=0;

if(!$numberAssignments) {
    $this->TableInstructions->startRow();
    $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_noassignments','gradebook'),NULL,NULL,NULL,NULL," colspan=\"4\"");
    $this->TableInstructions->endRow();
} else {
    $iassignment=array();
    switch($assessment) {
        case 'Essays':
        case 'Online Worksheets':
        case 'MCQ Tests':
        case 'Assignments':
        default:
        //check counter
            $checkCount=0;
            //essays
            $c=1;
            $essayResultsArray=array();
            $essayResultsArray=$objEssaytopics->getTopic(NULL,NULL," context='$contextCode'");
            if(!empty($essayResultsArray)) {
                foreach($essayResultsArray as $iassignment) {
                    if($check) {
                        /**
                         * the variable checked against is created through a combination
                         * of the id and the word "Essays" or Worksheets ... etc
                         * the variable is recreated and we check to see if it exists
                         */
                        $xassignmentV=0;
                        $xassignmentV="$iassignment[id]:Essays";
                        $assignmentV=0;
                        $assignmentV=$this->getParam($xassignmentV, NULL);

                        if($assignmentV) {
                            $checkCount++;
                            //link
                            $c++;
                            $this->TableInstructions->startRow(!($c%2)?"odd":"even");
                            //checkbox
                            $objAssignmentCheckBox = new checkbox($iassignment["id"].":Essays");
                            $objAssignmentCheckBox->setValue("1");
                            $this->TableInstructions->addCell($objAssignmentCheckBox->show());
                            //closing date
                            $this->TableInstructions->addCell($objDatetime->formatDate($iassignment["closing_date"]));
                            //assignment name
                            $objAssignmentLink = new link($this->uri(array('action'=>'assignmentDetails','assignment'=>'Essays','assignmentId'=>$iassignment["id"])));
                            $objAssignmentLink->link=$iassignment["name"];
                            $this->TableInstructions->addCell($objAssignmentLink->show());
                            $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_essays','gradebook'));
                            $studentResult=array();
                            $xstudentResult=array();
                            $xstudentResult=$objEssaybook->getGrades("studentid='$studentUserId' and topicid='".$iassignment["id"]."' and context='$contextCode'");
                            $classAvg=array();
                            $classAvg=$objEssaybook->getGrades("topicid='".$iassignment["id"]."' and context='$contextCode'","avg(mark) classAvg");
                            $ca=0;
                            $ca=$classAvg[0]["classavg"];
                            $totalAvgMark+=$ca;
                            if(!empty($xstudentResult)) {
                                foreach($xstudentResult as $studentResult) {
                                    $this->TableInstructions->addCell(round($studentResult["mark"],2));
                                    $this->TableInstructions->addCell(round($ca,2));
                                    $this->TableInstructions->addCell('<font color="red">'.round((($studentResult["mark"]/100)*$iassignment["percentage"]),2).'</font>');
                                    $totalMark+=round(($studentResult["mark"]/100)*$iassignment["percentage"],2);
                                    $totalPercentMark+=$studentResult["mark"];
                                    $count+=1;
                                }
                            } else {
                                $this->TableInstructions->addCell('');
                                $this->TableInstructions->addCell(($ca?round($ca,2):''));
                                $this->TableInstructions->addCell('');
                                $count+=1;
                            }
                            $this->TableInstructions->addCell(round($iassignment["percentage"],2));
                            $totalPercentYrMark+=$iassignment["percentage"];
                            $this->TableInstructions->endRow();
                        }
                    } else {
                        //link
                        $c++;
                        $this->TableInstructions->startRow(!($c%2)?"odd":"even");
                        //checkbox
                        $objAssignmentCheckBox = new checkbox($iassignment["id"].":Essays");
                        $objAssignmentCheckBox->setValue("1");
                        $this->TableInstructions->addCell($objAssignmentCheckBox->show());
                        //closing date
                        $this->TableInstructions->addCell($objDatetime->formatDate($iassignment["closing_date"]));
                        //assignment name
                        $objAssignmentLink = new link($this->uri(array('action'=>'assignmentDetails','assignment'=>'Essays','assignmentId'=>$iassignment["id"])));
                        $objAssignmentLink->link=$iassignment["name"];
                        $this->TableInstructions->addCell($objAssignmentLink->show());
                        $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_essays','gradebook'));
                        $studentResult=array();
                        $xstudentResult=array();
                        $xstudentResult=$objEssaybook->getGrades("studentid='$studentUserId' and topicid='".$iassignment["id"]."' and context='$contextCode'");
                        $classAvg=array();
                        $classAvg=$objEssaybook->getGrades("topicid='".$iassignment["id"]."' and context='$contextCode'","avg(mark) classAvg");
                        $ca=0;
                        $ca=$classAvg[0]["classavg"];
                        $totalAvgMark+=$ca;
                        if(!empty($xstudentResult)) {
                            foreach($xstudentResult as $studentResult) {
                                $this->TableInstructions->addCell(round($studentResult["mark"],2));
                                $this->TableInstructions->addCell(round($ca,2));
                                $this->TableInstructions->addCell('<font color="red">'.round((($studentResult["mark"]/100)*$iassignment["percentage"]),2).'</font>');
                                $totalMark+=round(($studentResult["mark"]/100)*$iassignment["percentage"],2);
                                $totalPercentMark+=$studentResult["mark"];
                                $count+=1;
                            }
                        } else {
                            $this->TableInstructions->addCell('');
                            $this->TableInstructions->addCell(($ca?round($ca,2):''));
                            $this->TableInstructions->addCell('');
                            $count+=1;
                        }
                        $this->TableInstructions->addCell(round($iassignment["percentage"],2));
                        $totalPercentYrMark+=$iassignment["percentage"];
                        $this->TableInstructions->endRow();
                    }
                }
            }

            //worksheets
            $worksheetsResultsArray=array();
            $worksheetsResultsArray=$objWorksheet->getWorksheets("context='$contextCode'");
            if(!empty($worksheetsResultsArray)) {
                foreach($worksheetsResultsArray as $iassignment) {
                    if($check) {
                        /**
                         * the variable checked against is created through a combination
                         * of the id and the word "Essays" or Worksheets ... etc
                         * the variable is recreated and we check to see if it exists
                         */
                        $xassignmentV=0;
                        $xassignmentV="$iassignment[id]:Worksheets";
                        $assignmentV=0;
                        $assignmentV=$this->getParam($xassignmentV, NULL);

                        if($assignmentV) {
                            $checkCount++;
                            //link
                            $c++;
                            $this->TableInstructions->startRow(!($c%2)?"odd":"even");
                            //checkbox
                            $objAssignmentCheckBox = new checkbox($iassignment["id"].":Worksheets");
                            $objAssignmentCheckBox->setValue("1");
                            $this->TableInstructions->addCell($objAssignmentCheckBox->show());
                            //closing date
                            $this->TableInstructions->addCell($objDatetime->formatDate($iassignment["closing_date"]));
                            //assignment name
                            $objAssignmentLink = new link($this->uri(array('action'=>'assignmentDetails','assignment'=>'Online Worksheets','assignmentId'=>$iassignment["id"])));
                            $objAssignmentLink->link=$iassignment["name"];
                            $this->TableInstructions->addCell($objAssignmentLink->show());
                            $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_worksheet','gradebook'));
                            $studentResult=array();
                            $xstudentResult=array();
                            $xstudentResult=$objWorksheetresults->getAnnualResults("userId='$studentUserId' and worksheet_id='".$iassignment["id"]."'");
                            //$test = $objWorksheetresults->getAll("WHERE userId")
                            var_dump($studentUserId);
                            $classAvg=array();
                            $classAvg=$objWorksheetresults->getAnnualResults("worksheet_id='".$iassignment["id"]."'","avg(mark) classAvg");
                            $ca=0;
                            $ca=($classAvg[0]["classavg"]<0?0:$classAvg[0]["classavg"]);
                            $totalAvgMark+=$ca;
                            if(!empty($xstudentResult)) {
                                foreach($xstudentResult as $studentResult) {
                                    $this->TableInstructions->addCell(round(($studentResult["mark"]<0?0:($studentResult["mark"]/$iassignment["total_mark"])*100),2));
                                    $this->TableInstructions->addCell(round($ca,2));
                                    $this->TableInstructions->addCell('<font color="red">'.round(((($studentResult["mark"]<0?0:$studentResult["mark"])/100)*$iassignment["percentage"]),2).'</font>');
                                    $totalMark+=round((($studentResult["mark"]<0?0:$studentResult["mark"])/100)*$iassignment["percentage"],2);
                                    $totalPercentMark+=($studentResult["mark"]<0?0:$studentResult["mark"]);
                                    $count+=1;
                                }
                            } else {
                                $this->TableInstructions->addCell('');
                                $this->TableInstructions->addCell(($ca?round($ca,2):''));
                                $this->TableInstructions->addCell('');
                                $count+=1;
                            }
                            $this->TableInstructions->addCell(round($iassignment["percentage"],2));
                            $totalPercentYrMark+=$iassignment["percentage"];
                            $this->TableInstructions->endRow();
                        }
                    } else {
                        //link
                        $c++;
                        $this->TableInstructions->startRow(!($c%2)?"odd":"even");
                        //checkbox
                        $objAssignmentCheckBox = new checkbox($iassignment["id"].":Worksheets");
                        $objAssignmentCheckBox->setValue("1");
                        $this->TableInstructions->addCell($objAssignmentCheckBox->show());
                        //closing date
                        $this->TableInstructions->addCell($objDatetime->formatDate($iassignment["closing_date"]));
                        //assignment name
                        $objAssignmentLink = new link($this->uri(array('action'=>'assignmentDetails','assignment'=>'Online Worksheets','assignmentId'=>$iassignment["id"])));
                        $objAssignmentLink->link=$iassignment["name"];
                        $this->TableInstructions->addCell($objAssignmentLink->show());
                        $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_worksheet','gradebook'));
                        $studentResult=array();
                        $xstudentResult=array();
                        $xstudentResult=$objWorksheetresults->getAnnualResults("userId='$studentUserId' and worksheet_id='".$iassignment["id"]."'");
                        $classAvg=array();
                        $classAvg=$objWorksheetresults->getAnnualResults("worksheet_id='".$iassignment["id"]."'","avg(mark) classAvg");
                        $ca=0;
                        if($iassignment["total_mark"] > 0 && $classAvg[0] > 0)
                            $ca=($classAvg[0]["classavg"]<0?0:($classAvg[0]["classavg"]/$iassignment["total_mark"])*100);
                        $totalAvgMark+=$ca;
                        if(!empty($xstudentResult)) {
                            foreach($xstudentResult as $studentResult) {
                                $this->TableInstructions->addCell(round(($studentResult["mark"]<0?0:($studentResult["mark"]/$iassignment["total_mark"])*100),2));
                                $this->TableInstructions->addCell(round($ca,2));
                                $this->TableInstructions->addCell('<font color="red">'.round(((($studentResult["mark"]<0?0:$studentResult["mark"])/100)*$iassignment["percentage"]),2).'</font>');
                                $totalMark+=round((($studentResult["mark"]<0?0:$studentResult["mark"])/100)*$iassignment["percentage"],2);
                                $totalPercentMark+=($studentResult["mark"]<0?0:$studentResult["mark"]);
                                $count+=1;
                            }
                        } else {
                            $this->TableInstructions->addCell('');
                            $this->TableInstructions->addCell(($ca?round($ca,2):''));
                            $this->TableInstructions->addCell('');
                            $count+=1;
                        }
                        $this->TableInstructions->addCell(round($iassignment["percentage"],2));
                        $totalPercentYrMark+=$iassignment["percentage"];
                        $this->TableInstructions->endRow();
                    }
                }
            }

            //MCQ Tests
            $testsResultsArray=array();
            $testsResultsArray=$objTestadmin->getTests($contextCode);
            if(!empty($testsResultsArray)) {
                foreach($testsResultsArray as $iassignment) {
                    if($check) {
                        /**
                         * the variable checked against is created through a combination
                         * of the id and the word "Essays" or Worksheets ... etc
                         * the variable is recreated and we check to see if it exists
                         */
                        $xassignmentV=0;
                        $xassignmentV="$iassignment[id]:Tests";
                        $assignmentV=0;
                        $assignmentV=$this->getParam($xassignmentV, NULL);

                        if($assignmentV) {
                            $checkCount++;
                            //link
                            $c++;
                            $this->TableInstructions->startRow(!($c%2)?"odd":"even");
                            //checkbox
                            $objAssignmentCheckBox = new checkbox($iassignment["id"].":Tests");
                            $objAssignmentCheckBox->setValue("1");
                            $this->TableInstructions->addCell($objAssignmentCheckBox->show());
                            //closing date
                            $this->TableInstructions->addCell($objDatetime->formatDate($iassignment["closingdate"]));
                            //assignment name
                            $objAssignmentLink = new link($this->uri(array('action'=>'assignmentDetails','assignment'=>'MCQ Tests','assignmentId'=>$iassignment["id"])));
                            $objAssignmentLink->link=$iassignment["name"];
                            $this->TableInstructions->addCell($objAssignmentLink->show());
                            $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_test','gradebook'));
                            $studentResult=array();
                            $xstudentResult=array();
                            $xstudentResult=$objTestresults->getResult($studentUserId,$iassignment["id"]);
                            $classAvg=array();
                            $classAvg=$objTestresults->getAnnualResults("tbl_test_results.testId='".$iassignment["id"]."' and tbl_test_results.testId=tbl_tests.id","avg((tbl_test_results.mark/tbl_tests.totalMark)*100) classAvg","tbl_test_results,tbl_tests");
                            $ca=0;
                            $ca=$classAvg[0]["classavg"];
                            $sMark=array();
                            $sMark=$objTestresults->getAnnualResults("tbl_test_results.testId='".$iassignment["id"]."' and tbl_test_results.studentId='$studentUserId' and tbl_test_results.testId=tbl_tests.id","((tbl_test_results.mark/tbl_tests.totalmark)*100)  studentMark","tbl_test_results,tbl_tests");
                            $mark=0;
                           // $mark=($sMark[0]["studentMark"]!=NULL?$sMark[0]["studentMark"]:0);
                            $totalAvgMark+=$ca;
                            if(!empty($xstudentResult)) {
                                foreach($xstudentResult as $studentResult) {
                                    $mark=($studentResult['mark']/$iassignment['totalmark'])*100;
                                    $this->TableInstructions->addCell(round($mark,2));
                                    $this->TableInstructions->addCell(round($ca,2));
                                    $this->TableInstructions->addCell('<font color="red">'.round((($mark/100)*$iassignment["percentage"]),2).'</font>');
                                    $totalMark+=round(($mark/100)*$iassignment["percentage"],2);
                                    $totalPercentMark+=$mark;
                                    $count+=1;
                                }
                            } else {
                                $this->TableInstructions->addCell('');
                                $this->TableInstructions->addCell(($ca?round($ca,2):''));
                                $this->TableInstructions->addCell('');
                                $count+=1;
                            }
                            $this->TableInstructions->addCell(round($iassignment["percentage"],2));
                            $totalPercentYrMark+=$iassignment["percentage"];
                            $this->TableInstructions->endRow();
                        }
                    } else {
                        //link
                        $c++;
                        $this->TableInstructions->startRow(!($c%2)?"odd":"even");
                        //checkbox
                        $objAssignmentCheckBox = new checkbox($iassignment["id"].":Tests");
                        $objAssignmentCheckBox->setValue("1");
                        $this->TableInstructions->addCell($objAssignmentCheckBox->show());
                        //closing date
                        $this->TableInstructions->addCell($objDatetime->formatDate(isset($iassignment['closingdate'])?$iassignment['closingdate']:null));
                        //assignment name
                        $objAssignmentLink = new link($this->uri(array('action'=>'assignmentDetails','assignment'=>'MCQ Tests','assignmentId'=>$iassignment["id"])));
                        $objAssignmentLink->link=$iassignment["name"];
                        $this->TableInstructions->addCell($objAssignmentLink->show());
                        $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_test','gradebook'));
                        $studentResult=array();
                        $xstudentResult=array();
                        $xstudentResult=$objTestresults->getResult($studentUserId,$iassignment["id"]);

                        $classAvg=array();
                        $classAvg=$objTestresults->getAnnualResults("tbl_test_results.testId='".$iassignment["id"]."' and tbl_test_results.testId=tbl_tests.id","avg((tbl_test_results.mark/tbl_tests.totalMark)*100) classAvg","tbl_test_results,tbl_tests");
                        $ca=0;
                        $ca=$classAvg[0]["classavg"];
                        $sMark=array();
                        $sMark=$objTestresults->getAnnualResults("tbl_test_results.testId='".$iassignment["id"]."' and tbl_test_results.studentId='$studentUserId' and tbl_test_results.testId=tbl_tests.id","(tbl_test_results.mark/tbl_tests.totalMark)*100 studentMark","tbl_test_results,tbl_tests");
                        $mark=0;
                        //  $mark = isset($sMark[0]['studentMark']) ? $sMark[0]['studentMark'] : 0;
                        $totalAvgMark+=$ca;
                        if(!empty($xstudentResult)) {
                            foreach($xstudentResult as $studentResult) {
                                $mark=($studentResult['mark']/$iassignment['totalmark'])*100;
                                $this->TableInstructions->addCell(round($mark,2));
                                $this->TableInstructions->addCell(round($ca,2));
                                $this->TableInstructions->addCell('<font color="red">'.round((($mark/100)*$iassignment["percentage"]),2).'</font>');
                                $totalMark+=round(($mark/100)*$iassignment["percentage"],2);
                                $totalPercentMark+=$mark;
                                $count+=1;
                            }
                        } else {
                            $this->TableInstructions->addCell('');
                            $this->TableInstructions->addCell(($ca?round($ca,2):''));
                            $this->TableInstructions->addCell('');
                            $count+=1;
                        }
                        $this->TableInstructions->addCell(round($iassignment["percentage"],2));
                        $totalPercentYrMark+=$iassignment["percentage"];
                        $this->TableInstructions->endRow();
                    }
                }
            }

            //Assignments
            $assignmentsResultsArray=array();
            $assignmentsResultsArray=$objAssignment->getAssignment($contextCode);
            if(!empty($assignmentsResultsArray)) {
                foreach($assignmentsResultsArray as $iassignment) {
                    if($check) {
                        /**
                         * the variable checked against is created through a combination
                         * of the id and the word "Essays" or Worksheets ... etc
                         * the variable is recreated and we check to see if it exists
                         */
                        $xassignmentV=0;
                        $xassignmentV="$iassignment[id]:Assignments";
                        $assignmentV=0;
                        $assignmentV=$this->getParam($xassignmentV, NULL);

                        if($assignmentV) {
                            $checkCount++;
                            //link
                            $c++;
                            $this->TableInstructions->startRow(!($c%2)?"odd":"even");
                            //checkbox
                            $objAssignmentCheckBox = new checkbox($iassignment["id"].":Assignments");
                            $objAssignmentCheckBox->setValue("1");
                            $this->TableInstructions->addCell($objAssignmentCheckBox->show());
                            //closing date
                            
                            $this->TableInstructions->addCell($objDatetime->formatDate($iassignment["closing_date"]));
                            //assignment name
                            $objAssignmentLink = new link($this->uri(array('action'=>'assignmentDetails','assignment'=>'Assignments','assignmentId'=>$iassignment["id"])));
                            $objAssignmentLink->link=$iassignment["name"];
                            $this->TableInstructions->addCell($objAssignmentLink->show());
                            $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_assignments','gradebook'));
                            $studentResult=array();
                            $xstudentResult=array();
                            $xstudentResult=$objAssignmentSubmit->getSubmit("userId='$studentUserId' and assignmentId='".$iassignment["id"]."'","mark");
                            $classAvg=array();
                            $classAvg=$objAssignmentSubmit->getSubmittedAssignments("assignmentId='".$iassignment["id"]."'","avg(mark) classAvg");
                            $ca=0;
                            $ca=$classAvg[0]["classavg"];
                            $totalAvgMark+=$ca;

                            $studentResult=$xstudentResult[count($xstudentResult-1)];
                            if(!empty($xstudentResult)) {
                                // foreach($xstudentResult as $studentResult) {
                                $this->TableInstructions->addCell(round(($studentResult["mark"]/$iassignment["mark"])*100,2));
                                $this->TableInstructions->addCell(round($ca,2));
                                $this->TableInstructions->addCell('<font color="red">'.round((($studentResult["mark"]/100)*$iassignment["percentage"]),2).'</font>');
                                $totalMark+=round(($studentResult["mark"]/$iassignment["mark"])*$iassignment["percentage"],2);
                                $totalPercentMark+=$studentResult["mark"];
                                $count+=1;
                                // }
                            } else {
                                $this->TableInstructions->addCell('');
                                $this->TableInstructions->addCell(($ca?round($ca,2):''));
                                $this->TableInstructions->addCell('');
                                $count+=1;
                            }
                            $this->TableInstructions->addCell(round($iassignment["percentage"],2));
                            $totalPercentYrMark+=$iassignment["percentage"];
                            $this->TableInstructions->endRow();
                        }
                    } else {
                        //link
                        $c++;
                        $this->TableInstructions->startRow(!($c%2)?"odd":"even");
                        //checkbox
                        $objAssignmentCheckBox = new checkbox($iassignment["id"].":Assignments");
                        $objAssignmentCheckBox->setValue("1");
                        $this->TableInstructions->addCell($objAssignmentCheckBox->show());
                        //closing date
                        $this->TableInstructions->addCell($objDatetime->formatDate($iassignment["closing_date"]));
                        //assignment name
                        $objAssignmentLink = new link($this->uri(array('action'=>'assignmentDetails','assignment'=>'Assignments','assignmentId'=>$iassignment["id"])));
                        $objAssignmentLink->link=$iassignment["name"];
                        $this->TableInstructions->addCell($objAssignmentLink->show());
                        $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_assignments','gradebook'));
                        $studentResult=array();
                        $xstudentResult=array();
                        $xstudentResult=$objAssignmentSubmit->getSubmit("userId='$studentUserId' and assignmentId='".$iassignment["id"]."'","mark");

                        $classAvg=array();
                        $classAvg=$objAssignmentSubmit->getSubmittedAssignments("assignmentId='".$iassignment["id"]."'","avg(mark) classAvg");
                        $ca=0;
                        if($classAvg[0]["classavg"] > 0 && $iassignment["mark"] > 0)
                            $ca=$classAvg[0]["classavg"]/$iassignment["mark"];
                        $totalAvgMark+=$ca;
                        $studentResult=$xstudentResult[(count($xstudentResult)-1)];
                        if(!empty($xstudentResult)) {
                            // foreach($xstudentResult as $studentResult) {
                            $this->TableInstructions->addCell(round(($studentResult["mark"]/$iassignment["mark"])*100,2));
                            $this->TableInstructions->addCell(round($ca*100,2));
                            $this->TableInstructions->addCell('<font color="red">'.round((($studentResult["mark"]/$iassignment["mark"])*$iassignment["percentage"]),2).'</font>');
                            $totalMark+=round(($studentResult["mark"]/100)*$iassignment["percentage"],2);
                            $totalPercentMark+=$studentResult["mark"];
                            $count+=1;
                            //  }
                        } else {
                            $this->TableInstructions->addCell('');
                            $this->TableInstructions->addCell(($ca?round($ca,2):''));
                            $this->TableInstructions->addCell('');
                            $count+=1;
                        }
                        $this->TableInstructions->addCell(round($iassignment["percentage"],2));
                        $totalPercentYrMark+=$iassignment["percentage"];
                        $this->TableInstructions->endRow();
                    }
                }
            }
            if(!$checkCount && $check) {
                /**
                 * no checkbox selected but the button was clicked
                 */
                $this->TableInstructions->startRow();
                $this->TableInstructions->addCell("&nbsp;");
                $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_nocheckboxes_selected','gradebook'),NULL,NULL,NULL,NULL," colspan=\"7\"");
                $this->TableInstructions->endRow();
            }
            break;
    }
}

if($checkCount || !$check) {
    //totals
    $this->TableInstructions->startRow();
    $this->TableInstructions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_total','gradebook')."</strong>",NULL,NULL,NULL,NULL," colspan=\"3\"");
    $this->TableInstructions->addCell('&nbsp;');
    $this->TableInstructions->addCell('<strong>'.number_format(round($totalPercentMark,2),2).'</strong>');
    $this->TableInstructions->addCell('<strong>'.number_format(round($totalAvgMark,2),2).'</strong>');
    $this->TableInstructions->addCell('<strong><font color="red">'.number_format(round($totalMark,2),2).'</font></strong>');
    $this->TableInstructions->addCell('<strong>'.number_format(round($totalPercentYrMark,2),2).'</strong>');
    $this->TableInstructions->endRow();

    //averages
    $this->TableInstructions->startRow();
    $this->TableInstructions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_average','gradebook')."</strong>",NULL,NULL,NULL,NULL," colspan=\"3\"");
    $this->TableInstructions->addCell('&nbsp;');
    $this->TableInstructions->addCell('<strong>'.round(round(($totalPercentMark/($count?$count:1)),2),2).'</strong>');
    $this->TableInstructions->addCell('<strong>'.round(round(($totalAvgMark/($count?$count:1)),2),2).'</strong>');
    //$this->TableInstructions->addCell('<strong>'.round(round(($totalMark/$count),2),2).'</strong>');
    $this->TableInstructions->addCell('&nbsp;');
    //$this->TableInstructions->addCell('<strong>'.round(round(($totalPercentYrMark/$count),2),2).'</strong>');
    $this->TableInstructions->addCell('&nbsp;');
    $this->TableInstructions->endRow();
}

$this->TableInstructions->startRow();
//display selected button
$objButton = new button('check',$objLanguage->languageText('mod_gradebook_displaySelected','gradebook'));
$objButton->setToSubmit();
//view all button
$objButtonAll = new button('viewAllChecks',$objLanguage->languageText('mod_gradebook_viewAll','gradebook'));
$objButtonAll->setToSubmit();
$this->TableInstructions->addCell(((!$checkCount && $check)?'':$objButton->show()).' '.$objButtonAll->show(),NULL,NULL,"left",NULL," colspan=\"8\"");
$this->TableInstructions->endRow();

//back to gradebook home
$this->TableInstructions->startRow();
$objLink = new link($this->uri(array('action'=>NULL)));
$objLink->link=$objLanguage->languageText('mod_gradebook_goback','gradebook');
$this->TableInstructions->addCell($objLink->show(),NULL,NULL,NULL,NULL," colspan=\"8\"");
$this->TableInstructions->endRow();

$objForm->addToForm($this->TableInstructions->show());
echo $objForm->show();
?>
