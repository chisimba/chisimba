<?php
/* -------------------- gradebook class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


//set the layout
$this->setLayoutTemplate('gradebook_layout_tpl.php');

//has the nature of the assessment been determined?
$dropdownAssessments = 0;
$dropdownAssessments = $this->getParam('dropdownAssessments', NULL);
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

//create the general form class
$objForm = new form('selectAssessment');
$objForm->setAction($this->uri(array()));
$objForm->displayType=3;  //Free form

//context management
$contextObject =& $this->getObject('dbcontext', 'context');
$contextCode = $contextObject->getContextCode();
$theCourse=0;

$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1; //Heading <h3>
//$this->objH->align="center";
$this->objH->str=($contextCode?$contextObject->getMenuText($contextCode):'').' '.$objLanguage->languageText('mod_gradebook_title','gradebook');
if($dropdownAssessments && $dropdownAssessments!="View All") {
	$this->objH->str.=' - ';
	$this->objH->str.=$dropdownAssessments;
}
echo $this->objH->show();

//select assignment, essays, onlineworksheets, MCQ tests etc
$this->TableOptions = $this->newObject('htmltable', 'htmlelements');
$this->TableOptions->cellspacing="2";
$this->TableOptions->width=($dropdownAssessments && $dropdownAssessments!="View All"?"80%":"100%");
//$this->TableOptions->attributes="align=\"left\"";

//view by assessment
$objLinkViewByAssessment = new link($this->uri(array('action'=>'viewByAssessment','dropdownAssessments'=>$dropdownAssessments)));
$objLinkViewByAssessment->link=$objLanguage->languageText('mod_gradebook_viewByAssessment','gradebook');
$this->TableOptions->startRow();
$this->TableOptions->addCell($objLinkViewByAssessment->show(),NULL,NULL,NULL,NULL," colspan=\"3\"");
$this->TableOptions->endRow();

$this->TableOptions->startRow();
$this->TableOptions->addCell('&nbsp;&nbsp;'.$objLanguage->languageText('mod_gradebook_viewassessments','gradebook'),"73%",NULL,"right");
//add the options to the drop down
$objAssessments = 0;
$objAssessments = new dropdown('dropdownAssessments');
$objAssessments->extra = ' onchange=" document.getElementById(\'form_selectAssessment\').submit();"' ;
//document.getElementById(\'form_selectAssessment\').submit();"
if($dropdownAssessments) {
	$objAssessments->addOption($dropdownAssessments,$dropdownAssessments);
} else {
	$objAssessments->addOption("",$objLanguage->languageText('mod_gradebook_select_assignment','gradebook'));
}
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_assignments','gradebook'),$objLanguage->languageText('mod_gradebook_assignments','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_essays','gradebook'),$objLanguage->languageText('mod_gradebook_essays','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_test','gradebook'),$objLanguage->languageText('mod_gradebook_test','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_worksheet','gradebook'),$objLanguage->languageText('mod_gradebook_wordworksheet','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_viewAll','gradebook'),$objLanguage->languageText('mod_gradebook_viewAll','gradebook'));
$this->TableOptions->addCell($objAssessments->show(),"20%");
$this->TableOptions->addCell("&nbsp;","27%");
$this->TableOptions->endRow();
$objForm->addToForm($this->TableOptions->show());
echo $objForm->show();

//select course text, for proper alignment, fit within table
$this->TableInstructions = $this->newObject('htmltable', 'htmlelements');
$this->TableInstructions->cellspacing="2";
$this->TableInstructions->width=($dropdownAssessments && $dropdownAssessments!="View All"?"80%":"100%");
//$this->TableInstructions->attributes="align=\"center\"";

$this->TableInstructions->startHeaderRow();
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_studentNumber','gradebook'),"17%");
$this->TableInstructions->addHeaderCell('&nbsp;&nbsp;'.$objLanguage->languageText('mod_gradebook_student','gradebook'),($dropdownAssessments&& $dropdownAssessments!="View All"?"70%":"33%"));
if(!$dropdownAssessments || $dropdownAssessments=="View All") {
	$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_assignments','gradebook'),"5%");
	$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_essays','gradebook'),"5%");
	$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_test','gradebook'),"10%");
	$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_wordworksheet','gradebook'),"20%");
}
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_yearMark','gradebook'),"10%");
$this->TableInstructions->endHeaderRow();

//get the students in this course
$userId=array();
$firstName=array();
$surname=array();

$firstName = $this->objGradebook->getStudentInContextInfo('firstname');
$surname = $this->objGradebook->getStudentInContextInfo('surname');
$userId = $this->objGradebook->getStudentInContextInfo('userid');
$username= $this->objGradebook->getStudentInContextInfo('username');

$numberStudents=0;
$numberStudents=$this->objGradebook->getNumberStudentsInContext();

if(!$numberStudents) {
	$this->TableInstructions->startRow();
	$this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_nostudents','gradebook'),NULL,NULL,NULL,NULL," colspan=\"2\"");
	$this->TableInstructions->endRow();
} else {
	for($i=1;$i<=$numberStudents;$i++) {
		
		$this->TableInstructions->startRow(!($i%2)?"odd":"even");
		$this->TableInstructions->addCell($username[$i-1]);
		$objLink = new link($this->uri(array('action'=>'assessmentDetails','assessment'=>$dropdownAssessments,'studentuserid'=>$userId[$i-1])));
		$objLink->link=$firstName[$i-1].' '.$surname[$i-1];
		$this->TableInstructions->addCell('&nbsp;&nbsp;'.$objLink->show());
		
		if($dropdownAssessments) {
			//based on the assessment, query the relevant results/tables
			switch($dropdownAssessments) {
				case 'Essays':
					//retrieve grades from Essays
					$annualResult=array();
					$iEssayBook=array();
					$iEssayBook=$objEssaybook->getGrades(
							"tbl_essay_book.studentId='".$userId[$i-1]."' and tbl_essay_book.topicid=tbl_essay_topics.id and tbl_essay_book.context='$contextCode' and tbl_essay_topics.context='$contextCode'",
							"sum((tbl_essay_book.mark/100)*tbl_essay_topics.percentage) result",
							"tbl_essay_book,tbl_essay_topics");
							
					if(!empty($iEssayBook)) {
						foreach($iEssayBook as $annualResult) {
								$this->TableInstructions->addCell('&nbsp;&nbsp;'.(round($annualResult["result"],2)?round($annualResult["result"],2):""));
						}
					} else {
						$this->TableInstructions->addCell('&nbsp;');
					}
				break;
				case 'MCQ Tests':
					//retrieve grades from MCQ Tests
					$annualResult=array();
					$iTestresults=array();
					$iTestresults=$objTestresults->getAnnualResults(
							"tbl_test_results.studentId='".$userId[$i-1]."' and tbl_test_results.testId=tbl_tests.id and tbl_tests.context='$contextCode'",
							"sum((tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage) result",
							"tbl_test_results,tbl_tests");

					if(!empty($iTestresults)) {
						foreach($iTestresults as $annualResult) {
								$this->TableInstructions->addCell('&nbsp;&nbsp;'.(round(($annualResult["result"]!=NULL?$annualResult["result"]:0),2)?round($annualResult["result"],2):""));
						}
					} else {
						$this->TableInstructions->addCell('&nbsp;');
					}
				break;
				case 'Online Worksheets':
					//retrieve grades from Online Worksheets
					$annualResult=array();
					$iWorksheetresults=array();
					$iWorksheetresults=$objWorksheetresults->getAnnualResults(
							"tbl_worksheet_results.userid='".$userId[$i-1]."' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id and tbl_worksheet.context='$contextCode'",
							"sum((tbl_worksheet_results.mark/100)*tbl_worksheet.percentage) result",
							"tbl_worksheet_results,tbl_worksheet");
					
					if(!empty($iWorksheetresults)) {
						foreach($iWorksheetresults as $annualResult) {
								$this->TableInstructions->addCell('&nbsp;&nbsp;'.(round($annualResult["result"],2)?round(($annualResult["result"]<0?0:$annualResult["result"]),2):""));
						}
					} else {
						$this->TableInstructions->addCell('&nbsp;');
					}
				break;
				case 'Assignments':
					//retrieve grades from assignments
					$annualResult=array();
					$iAssignmentSubmit=array();
					$iAssignmentSubmit=$objAssignmentSubmit->getSubmittedAssignments(
							"tbl_assignment_submit.userid='".$userId[$i-1]."' and tbl_assignment_submit.assignmentId=tbl_assignment.id and tbl_assignment.context='$contextCode'",
							"sum((tbl_assignment_submit.mark/100)*tbl_assignment.percentage) result",
							"tbl_assignment,tbl_assignment_submit");
							
					if(!empty($iAssignmentSubmit)) {
						foreach($iAssignmentSubmit as $annualResult) {
								$this->TableInstructions->addCell('&nbsp;&nbsp;'.(round($annualResult["result"],2)?round($annualResult["result"],2):""));
						}
					} else {
						$this->TableInstructions->addCell('&nbsp;');
					}
				break;
				default:
					//total grades
					$total=0;
					$totalAssignments=0;
					$totalEssays=0;
					$totalTests=0;
					$totalWorksheets=0;
					//retrieve grades from Essays
					$annualResult1=array();
					$iEssayBook=array();
					$iEssayBook=$objEssaybook->getGrades(
							"tbl_essay_book.studentId='".$userId[$i-1]."' and tbl_essay_book.topicid=tbl_essay_topics.id and tbl_essay_book.context='$contextCode' and tbl_essay_topics.context='$contextCode'",
							"sum((tbl_essay_book.mark/100)*tbl_essay_topics.percentage) result",
							"tbl_essay_book,tbl_essay_topics");
					if(!empty($iEssayBook)) {
						foreach($iEssayBook as $annualResult1) {
								$totalEssays=round($annualResult1["result"],2);
								$total+=round($annualResult1["result"],2);
						}
					}
					//retrieve grades from MCQ Tests
					$annualResult2=array();
					$iTestresults=array();
					$iTestresults=$objTestresults->getAnnualResults(
							"tbl_test_results.studentId='".$userId[$i-1]."' and tbl_test_results.testId=tbl_tests.id and tbl_tests.context='$contextCode'",
							"sum((tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage) result",
							"tbl_test_results,tbl_tests");
					if(!empty($iTestresults)) {
						foreach($iTestresults as $annualResult2) {
								$totalTests=round($annualResult2["result"],2);
								$total+=round($annualResult2["result"],2);
						}
					}
					//retrieve grades from Online Worksheets
					$annualResult3=array();
					$iWorksheetresults=array();
					$iWorksheetresults=$objWorksheetresults->getAnnualResults(
							"tbl_worksheet_results.userid='".$userId[$i-1]."' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id and tbl_worksheet.context='$contextCode'",
							"sum((tbl_worksheet_results.mark/100)*tbl_worksheet.percentage) result",
							"tbl_worksheet_results,tbl_worksheet");
					if(!empty($iWorksheetresults)) {
						foreach($iWorksheetresults as $annualResult3) {
								$totalWorksheets=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
								$total+=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
						}
					}
					//retrieve grades from assignments
					$annualResult4=array();
					$iAssignmentSubmit=array();
					$iAssignmentSubmit=$objAssignmentSubmit->getSubmittedAssignments(
							"tbl_assignment_submit.userid='".$userId[$i-1]."' and tbl_assignment_submit.assignmentId=tbl_assignment.id and tbl_assignment.context='$contextCode'",
							"sum((tbl_assignment_submit.mark/100)*tbl_assignment.percentage) result",
							"tbl_assignment,tbl_assignment_submit");
					if(!empty($iAssignmentSubmit)) {
						foreach($iAssignmentSubmit as $annualResult4) {
								$totalAssignments=round($annualResult4["result"],2);
								$total+=round($annualResult4["result"],2);
						}
					}
					//assignment
					$this->TableInstructions->addCell(($totalAssignments?$totalAssignments:""));
					//essays
					$this->TableInstructions->addCell(($totalEssays?$totalEssays:""));
					//tests
					$this->TableInstructions->addCell(($totalTests?$totalTests:""));
					//worksheets
					$this->TableInstructions->addCell(($totalWorksheets?$totalWorksheets:""));
					//display the total grade
					$this->TableInstructions->addCell('&nbsp;&nbsp;'.($total?$total:""));
				break;
			}
		} else {
			//total grades
			$total=0;
			$totalAssignments=0;
			$totalEssays=0;
			$totalTests=0;
			$totalWorksheets=0;
			//retrieve grades from Essays
			$annualResult1=array();
			$iEssayBook=array();
			$iEssayBook=$objEssaybook->getGrades(
					"tbl_essay_book.studentId='".$userId[$i-1]."' and tbl_essay_book.topicid=tbl_essay_topics.id and tbl_essay_book.context='$contextCode' and tbl_essay_topics.context='$contextCode'",
					"sum((tbl_essay_book.mark/100)*tbl_essay_topics.percentage) result",
					"tbl_essay_book,tbl_essay_topics");
			if(!empty($iEssayBook)) {
				foreach($iEssayBook as $annualResult1) {
					$totalEssays=round($annualResult1["result"],2);
					$total+=round($annualResult1["result"],2);
				}
			}
			//retrieve grades from MCQ Tests
			$annualResult2=array();
			$iTestresults=array();
			$iTestresults=$objTestresults->getAnnualResults(
					"tbl_test_results.studentId='".$userId[$i-1]."' and tbl_test_results.testId=tbl_tests.id and tbl_tests.context='$contextCode'",
					"sum((tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage) result",
					"tbl_test_results,tbl_tests");
			if(!empty($iTestresults)) {
				foreach($iTestresults as $annualResult2) {
					$totalTests=round($annualResult2["result"],2);
					$total+=round($annualResult2["result"],2);
				}
			}
			//retrieve grades from Online Worksheets
			$annualResult3=array();
			$iWorksheetresults=array();
			$iWorksheetresults=$objWorksheetresults->getAnnualResults(
					"tbl_worksheet_results.userid='".$userId[$i-1]."' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id and tbl_worksheet.context='$contextCode'",
					"sum((tbl_worksheet_results.mark/100)*tbl_worksheet.percentage) result",
					"tbl_worksheet_results,tbl_worksheet");
			if(!empty($iWorksheetresults)) {
				foreach($iWorksheetresults as $annualResult3) {
					$totalWorksheets=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
					$total+=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
				}
			}
			//retrieve grades from assignments
			$annualResult4=array();
			$iAssignmentSubmit=array();
			$iAssignmentSubmit=$objAssignmentSubmit->getSubmittedAssignments(
					"tbl_assignment_submit.userid='".$userId[$i-1]."' and tbl_assignment_submit.assignmentId=tbl_assignment.id and tbl_assignment.context='$contextCode'",
					"sum((tbl_assignment_submit.mark/100)*tbl_assignment.percentage) result",
					"tbl_assignment,tbl_assignment_submit");
			if(!empty($iAssignmentSubmit)) {
				foreach($iAssignmentSubmit as $annualResult4) {
					$totalAssignments=round($annualResult4["result"],2);
					$total+=round($annualResult4["result"],2);
				}
			}
			//assignment
			$this->TableInstructions->addCell(($totalAssignments?$totalAssignments:""));
			//essays
			$this->TableInstructions->addCell(($totalEssays?$totalEssays:""));
			//tests
			$this->TableInstructions->addCell(($totalTests?$totalTests:""));
			//worksheets
			$this->TableInstructions->addCell(($totalWorksheets?$totalWorksheets:""));
			//display the total grade
			$this->TableInstructions->addCell('&nbsp;&nbsp;'.($total?$total:""));
		}
		$this->TableInstructions->endRow();
	}
}
$this->TableInstructions->startRow();
$this->TableInstructions->addCell("&nbsp;",NULL,NULL,NULL,NULL," colspan=\"7\"");
$this->TableInstructions->endRow();
//upload marks for offline assessment
$objLinkUpload = new link($this->uri(array('action'=>'uploadMarks')));
$objLinkUpload->link=$objLanguage->languageText('mod_gradebook_uploadMarks','gradebook');

$this->TableInstructions->startRow();
$this->TableInstructions->addCell($objLinkUpload->show()." | ".$objLinkViewByAssessment->show(),NULL,NULL,NULL,NULL," colspan=\"7\"");
$this->TableInstructions->endRow();

if($dropdownAssessments && $dropdownAssessments!="View All") {
	//return to gradebook home
	$this->TableInstructions->startRow();
	$objLink = new link($this->uri(array('action'=>NULL)));
	$objLink->link=$objLanguage->languageText('mod_gradebook_goback','gradebook');
	$this->TableInstructions->addCell($objLink->show(),NULL,NULL,NULL,NULL," colspan=\"7\"");
	$this->TableInstructions->endRow();
}

echo $this->TableInstructions->show();
?>