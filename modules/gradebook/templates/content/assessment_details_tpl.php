<?php
/* -------------------- gradebook class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
//set the layout
$this->setLayoutTemplate('gradebook_layout_tpl.php');

//assignment details
$assignment = 0;
$assignment = $this->getParam('assignment', NULL);
$assignmentId = 0;
$assignmentId = $this->getParam('assignmentId', NULL);
//load required form elements
$this->loadClass('form','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('radio','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('link','htmlelements');

//help
$this->objHelp = $this->newObject('helplink','help');
//context object
$objContext =& $this->getObject('dbcontext','context');
//assignment object
$objAssignment =& $this->getObject('dbassignment_old','assignment');
$objAssignmentSubmit =& $this->getObject('dbassignmentsubmit_old','assignment');
//essay object
$objEssaytopics =& $this->getObject('dbessay_topics','essay');
$objEssaybook =& $this->getObject('dbessay_book','essay');
//testadmin object
$objTestadmin =& $this->getObject('dbtestadmin','mcqtests');
$objTestresults =& $this->getObject('dbresults','mcqtests');
//worksheet object
$objWorksheet =& $this->getObject('dbworksheet','worksheet');
$objWorksheetresults =& $this->getObject('dbworksheetresults','worksheet');

//context management
$contextObject =& $this->getObject('dbcontext', 'context');
$contextCode = $contextObject->getContextCode();
$theCourse=0;

$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1; //Heading <h3>
//$this->objH->align="center";
$this->objH->str=($contextCode?$contextObject->getMenuText($contextCode):'').' '.$objLanguage->languageText('mod_gradebook_title','gradebook');
$this->objH->str.=' - ';
$this->objH->str.=($assignment?$assignment:$objLanguage->languageText('mod_gradebook_assignments','gradebook'));
switch($assignment) {
    case 'Essays':
        $assingmentName=array();
        $assingmentName=$objEssaytopics->getTopic($assignmentId);
        if(!empty($assingmentName)) {
            $this->objH->str.=', '.$assingmentName[0]["name"];
        }
        break;
    case 'MCQ Tests':
        $assingmentName=array();
        $assingmentName=$objTestadmin->getTests($contextCode,'name',$assignmentId);
        if(!empty($assingmentName)) {
            $this->objH->str.=', '.$assingmentName[0]["name"];
        }
        break;
    case 'Online Worksheets':
        $assingmentName=array();
        $assingmentName=$objWorksheet->getWorksheet($assignmentId);
        if(!empty($assingmentName)) {
            $this->objH->str.=', '.$assingmentName[0]["name"];
        }
        break;
    case 'Assignments':
    default:
        $assingmentName=array();
        $assingmentName=$objAssignment->getAssignment($contextCode,"id='$assignmentId'");
        if(!empty($assingmentName)) {
            $this->objH->str.=', '.$assingmentName[0]["name"];
        }
        break;
}
echo $this->objH->show();

//details table
$this->TableDetails = $this->newObject('htmltable', 'htmlelements');
$this->TableDetails->cellspacing="2";
$this->TableDetails->width="90%";
//$this->TableDetails->attributes="align=\"center\"";

//row 1: course and assessment type
$this->TableDetails->startRow();
$this->TableDetails->addCell('<strong>'.$objLanguage->languageText('mod_gradebook_course','gradebook').':</strong> '.($contextCode?$contextObject->getMenuText($contextCode):$objLanguage->languageText('mod_gradebook_nocourse','gradebook')));
$this->TableDetails->addCell('<strong>'.$objLanguage->languageText('mod_gradebook_assessmentType','gradebook').':</strong> '.$assignment.' ('.$assingmentName[0]["name"].')');
$this->TableDetails->endRow();

$classAvg=0;
$finalMark=0;
$lowestMark=0;
$highestMark=0;

switch($assignment) {
    case 'Essays':
        $as=array();
        $aEssaysArray=array();
        $aEssaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
        if(!empty($aEssaysArray)) {
            foreach($aEssaysArray as $as) {
                //min mark
                $min=array();
                $aMinEssaysBookArray=array();
                $aMinEssaysBookArray=$objEssaybook->getGrades("topicid='".$assignmentId."' and context='$contextCode'","distinct min(mark) minmark");
                if(!empty($aMinEssaysBookArray)) {
                    foreach($aMinEssaysBookArray as $min) {
                        $lowestMark=($min["minmark"]?$min["minmark"]:'&nbsp;');
                    }
                } else {
                    $lowestMark='&nbsp;';
                }
                //max mark
                $max=array();
                $aMaxEssaysBookArray=array();
                $aMaxEssaysBookArray=$objEssaybook->getGrades("topicid='".$assignmentId."' and context='$contextCode'","distinct max(mark) maxmark");
                if(!empty($aMaxEssaysBookArray)) {
                    foreach($aMaxEssaysBookArray as $max) {
                        $highestMark=($max["maxmark"]?$max["maxmark"]:'&nbsp;');
                    }
                } else {
                    $highestMark='&nbsp;';
                }
                //avg mark
                $avg=array();
                $aAvgEssaysBookArray=array();
                $aAvgEssaysBookArray=$objEssaybook->getGrades("topicid='".$assignmentId."' and context='$contextCode'","avg(mark) avgmark");
                if(!empty($aAvgEssaysBookArray)) {
                    foreach($aAvgEssaysBookArray as $avg) {
                        $classAvg=(round($avg["avgmark"],2));
                    }
                }
                $finalMark=$as["percentage"];
            }
        }
        break;
    case 'MCQ Tests':
        $as=array();
        $aTestsArray=array();
        $aTestsArray=$objTestadmin->getTests($contextCode);
        if(!empty($aTestsArray)) {
            foreach($aTestsArray as $as) {
                //min mark
                $min=array();
                $aMinTestsArray=array();
                $aMinTestsArray=$objTestresults->getAnnualResults("tbl_test_results.testId='".$assignmentId."' and tbl_test_results.testId=tbl_tests.id","distinct min((tbl_test_results.mark/tbl_tests.totalMark)*100) minmark","tbl_test_results,tbl_tests");
                if(!empty($aMinTestsArray)) {
                    foreach($aMinTestsArray as $min) {
                        $lowestMark=($min["minmark"]!=NULL?$min["minmark"]:'&nbsp;');
                    }
                } else {
                    $lowestMark='&nbsp;';
                }
                //max mark
                $max=array();
                $aMaxTestsArray=array();
                $aMaxTestsArray=$objTestresults->getAnnualResults("tbl_test_results.testId='".$assignmentId."' and tbl_test_results.testId=tbl_tests.id","distinct max((tbl_test_results.mark/tbl_tests.totalMark)*100) maxmark","tbl_test_results,tbl_tests");
                if(!empty($aMaxTestsArray)) {
                    foreach($aMaxTestsArray as $max) {
                        $highestMark=($max["maxmark"]!=NULL?$max["maxmark"]:'&nbsp;');
                    }
                } else {
                    $highestMark='&nbsp;';
                }
                //avg mark
                $avg=array();
                $aAvgTestsArray=array();
                $aAvgTestsArray=$objTestresults->getAnnualResults("tbl_test_results.testId='".$assignmentId."' and tbl_test_results.testId=tbl_tests.id","avg((tbl_test_results.mark/tbl_tests.totalMark)*100) avgmark","tbl_test_results,tbl_tests");
                if(!empty($aAvgTestsArray)) {
                    foreach($aAvgTestsArray as $avg) {
                        $classAvg=(round(($avg["avgmark"]!=NULL?$avg["avgmark"]:0),2));
                    }
                }
                $finalMark=$as["percentage"];
            }
        }
        break;
    case 'Online Worksheets':
        $as=array();
        $aWorksheetsArray=array();
        $aWorksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
        if(!empty($aWorksheetsArray)) {
            foreach($aWorksheetsArray as $as) {
                //min mark
                $min=array();
                $aMinWorksheetsArray=array();
                $aMinWorksheetsArray=$objWorksheetresults->getAnnualResults("worksheet_id='".$assignmentId."'","distinct min(mark) minmark");
                if(!empty($aMinWorksheetsArray)) {
                    foreach($aMinWorksheetsArray as $min) {
                        $lowestMark=($min["minmark"]?($min["minmark"]<0?'0':$min["minmark"]):'&nbsp;');
                    }
                } else {
                    $lowestMark='&nbsp;';
                }
                //max mark
                $max=array();
                $aMaxWorksheetsArray=array();
                $aMaxWorksheetsArray=$objWorksheetresults->getAnnualResults("worksheet_id='".$assignmentId."'","distinct max(mark) maxmark");
                if(!empty($aMaxWorksheetsArray)) {
                    foreach($aMaxWorksheetsArray as $max) {
                        $highestMark=($max["maxmark"]?($max["maxmark"]<0?'0':$max["maxmark"]):'&nbsp;');
                    }
                } else {
                    $highestMark='&nbsp;';
                }
                //avg mark
                $avg=array();
                $aAvgWorksheetsArray=array();
                $aAvgWorksheetsArray=$objWorksheetresults->getAnnualResults("worksheet_id='".$assignmentId."'","avg(mark) avgmark");
                if(!empty($aAvgWorksheetsArray)) {
                    foreach($aAvgWorksheetsArray as $avg) {
                        $classAvg=(round(($avg["avgmark"]<0?0:$avg["avgmark"]),2));
                    }
                }
                $finalMark=$as["percentage"];
            }
        }
        break;
    case 'Assignments':
    default:
        $as=array();
        $aAssignmentsArray=array();
        $aAssignmentsArray=$objAssignment->getAssignment($contextCode);
        if(!empty($aAssignmentsArray)) {
            foreach($aAssignmentsArray as $as) {
                //min mark
                $min=array();
                $aMinAssignmentsArray=array();
                $aMinAssignmentsArray=$objAssignmentSubmit->getSubmittedAssignments("assignmentId='".$assignmentId."'","distinct min(mark) minmark");
                if(!empty($aMinAssignmentsArray)) {
                    foreach($aMinAssignmentsArray as $min) {
                        $lowestMark=($min["minmark"]?$min["minmark"]:'&nbsp;');
                    }
                } else {
                    $lowestMark='&nbsp;';
                }
                //max mark
                $max=array();
                $aMaxAssignmentsArray=array();
                $aMaxAssignmentsArray=$objAssignmentSubmit->getSubmittedAssignments("assignmentId='".$assignmentId."'","distinct max(mark) maxmark");
                if(!empty($aMaxAssignmentsArray)) {
                    foreach($aMaxAssignmentsArray as $max) {
                        $highestMark=($max["maxmark"]?$max["maxmark"]:'&nbsp;');
                    }
                } else {
                    $highestMark='&nbsp;';
                }
                //avg mark
                $avg=array();
                $aAvgAssignmentsArray=array();
                $aAvgAssignmentsArray=$objAssignmentSubmit->getSubmittedAssignments("assignmentId='".$assignmentId."'","avg(mark) avgmark");
                if(!empty($aAvgAssignmentsArray)) {
                    foreach($aAvgAssignmentsArray as $avg) {
                        $classAvg=(round($avg["avgmark"],2));
                    }
                }
                $finalMark=$as["percentage"];
            }
        }
        break;
}

//row 2: class avg and final mark
$this->TableDetails->startRow();
$this->TableDetails->addCell('<strong>'.$objLanguage->languageText('mod_gradebook_classAvg','gradebook').':</strong> '.round($classAvg,2).'%');
$this->TableDetails->addCell('<strong>'.$objLanguage->languageText('mod_gradebook_percentFinalMark','gradebook').':</strong> '.round($finalMark,2).'%');
$this->TableDetails->endRow();

//row 3: lowest mark and highest mark
$this->TableDetails->startRow();
$this->TableDetails->addCell('<strong>'.$objLanguage->languageText('mod_gradebook_lowestMark','gradebook').':</strong> '.round($lowestMark,2).'%');
$this->TableDetails->addCell('<strong>'.$objLanguage->languageText('mod_gradebook_highestMark','gradebook').':</strong> '.round($highestMark,2).'%');
$this->TableDetails->endRow();

echo $this->TableDetails->show();

//select course text, for proper alignment, fit within table
$this->TableInstructions = $this->newObject('htmltable', 'htmlelements');
$this->TableInstructions->cellspacing="2";
$this->TableInstructions->width="90%";
//$this->TableInstructions->attributes="align=\"center\"";

$this->TableInstructions->startHeaderRow();
$this->TableInstructions->addHeaderCell('&nbsp;&nbsp;'.$objLanguage->languageText('mod_gradebook_student','gradebook'),"70%");
$this->TableInstructions->addHeaderCell('&nbsp;&nbsp;'.$objLanguage->languageText('mod_gradebook_yearMark','gradebook'),"30%");
$this->TableInstructions->endHeaderRow();

//get the students in this course
$userId=array();
$firstName=array();
$surname=array();

$userId = $this->objGradebook->getStudentInContextInfo('userid');
if (in_array($this->objUser->userId(), $userId)) {
    $numberStudents = 1;
    $userId = array($this->objUser->userId());
    $firstName = array($this->objUser->getFirstname());
    $surname = array($this->objUser->getSurname());
} else {
    $numberStudents = $this->objGradebook->getNumberStudentsInContext();
    $firstName = $this->objGradebook->getStudentInContextInfo('firstname');
    $surname = $this->objGradebook->getStudentInContextInfo('surname');
}

if(!$numberStudents) {
    $this->TableInstructions->startRow();
    $this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_nostudents','gradebook'),NULL,NULL,NULL,NULL," colspan=\"2\"");
    $this->TableInstructions->endRow();
} else {
    for($i=1;$i<=$numberStudents;$i++) {

        $this->TableInstructions->startRow(!($i%2)?"odd":"even");
        $objLink = new link($this->uri(array('action'=>'assessmentDetails','assessment'=>$assignment?$assignment:'Assignments','studentuserid'=>$userId[$i-1])));
        $objLink->link=$firstName[$i-1].' '.$surname[$i-1];
        $this->TableInstructions->addCell('&nbsp;&nbsp;'.$objLink->show());

        if($assignment) {
            //based on the assessment, query the relevant results/tables
            switch($assignment) {
                case 'Essays':
                //retrieve grades from Essays
                    $annualResult=array();
                    $result=0;
                    $rEssayArray=array();
                    $rEssayArray=$objEssaybook->getGrades("studentId='".$userId[$i-1]."' and topicid='$assignmentId' and context='$contextCode'","mark result");
                    if(!empty($rEssayArray)) {
                        foreach($rEssayArray as $annualResult) {
                            $result=round($annualResult["result"],2);
                        }
                    }
                    $this->TableInstructions->addCell('&nbsp;&nbsp;'.(round($result,2)?round($result,2):'&nbsp;'));
                    break;
                case 'MCQ Tests':
                //retrieve grades from MCQ Tests
                    $annualResult=array();
                    $result=0;
                    $rTestsArray=array();
                    $rTestsArray=$objTestresults->getAnnualResults("tbl_test_results.studentId='".$userId[$i-1]."' and tbl_test_results.testId='$assignmentId' and tbl_test_results.testId=tbl_tests.id","(tbl_test_results.mark/tbl_tests.totalMark)*100 result","tbl_test_results,tbl_tests");
                    if(!empty($rTestsArray)) {
                        foreach($rTestsArray as $annualResult) {
                            $result=round(($annualResult["result"]!=NULL?$annualResult["result"]:0),2);
                        }
                    }
                    $this->TableInstructions->addCell('&nbsp;&nbsp;'.(round($result,2)?round($result,2):'&nbsp;'));
                    break;
                case 'Online Worksheets':
                //retrieve grades from Online Worksheets
                    $annualResult=array();
                    $result=0;
                    $rWorksheetsArray=array();
                    $rWorksheetsArray=$objWorksheetresults->getAnnualResults("userid='".$userId[$i-1]."' and worksheet_id='$assignmentId'","mark result");
                    if(!empty($rWorksheetsArray)) {
                        foreach($rWorksheetsArray as $annualResult) {
                            $result=round(($annualResult["result"]<0?0:$annualResult["result"]),2);
                        }
                    }
                    $this->TableInstructions->addCell('&nbsp;&nbsp;'.(round($result,2)?round($result,2):'&nbsp;'));
                    break;
                case 'Assignments':
                default:
                //retrieve grades from assignments
                    $annualResult=array();
                    $result=0;
                    $rAssignmentsArray=array();
                    $rAssignmentsArray=$objAssignmentSubmit->getSubmittedAssignments("userid='".$userId[$i-1]."' and assignmentId='".$assignmentId."'","mark result");
                    if(!empty($rAssignmentsArray)) {
                        foreach($rAssignmentsArray as $annualResult) {
                            $result=round($annualResult["result"],2);
                        }
                    }
                    $this->TableInstructions->addCell('&nbsp;&nbsp;'.(round($result,2)?round($result,2):'&nbsp;'));
                    break;
            }
        } else {
            //insert grades from assignments
            $annualResult=array();
            $result=0;
            $rAssignmentsArray=array();
            $rAssignmentsArray=$objAssignmentSubmit->getSubmittedAssignments("userid='".$userId[$i-1]."' and assignmentId='".$assignmentId."'","mark result");
            if(!empty($rAssignmentsArray)) {
                foreach($rAssignmentsArray as $annualResult) {
                    $result=round($annualResult["result"],2);
                }
            }
            $this->TableInstructions->addCell('&nbsp;&nbsp;'.(round($result,2)?round($result,2):'&nbsp;'));
        }
        $this->TableInstructions->endRow();
    }
}
//view by assessment
$objLink = new link($this->uri(array()));
$objLink->link=$objLanguage->languageText('mod_gradebook_goback','gradebook');
$this->TableInstructions->startRow();
$this->TableInstructions->addCell('&nbsp;&nbsp;<br />'.$objLink->show(),NULL,NULL,NULL,NULL," colspan=\"2\"");
$this->TableInstructions->endRow();

echo $this->TableInstructions->show();
?>
