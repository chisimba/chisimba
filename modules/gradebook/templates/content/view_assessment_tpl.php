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
//die($dropdownAssessments);
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

//datetime object
$objDatetime =& $this->getObject('dateandtime','utilities');

//number of students
$numberStudents=0;
$count=0;
$total = 0;
$numberStudents=$this->objGradebook->getNumberStudentsInContext();

//create the general form class
$objForm = new form('selectAssessment');
$objForm->setAction($this->uri(array('action'=>'viewByAssessment')));
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
$this->TableOptions->width=(!$dropdownAssessments || $dropdownAssessments=="View All"?"95%":"75%");
$this->TableOptions->attributes="align=\"center\"";

//view by student
$objLink = new link($this->uri(array('dropdownAssessments'=>$dropdownAssessments)));
$objLink->link=$objLanguage->languageText('mod_gradebook_viewByStudent','gradebook');
$this->TableOptions->startRow();
$this->TableOptions->addCell($objLink->show(),NULL,NULL,NULL,NULL," colspan=\"2\"");
$this->TableOptions->endRow();

$this->TableOptions->startRow();
$this->TableOptions->addCell($objLanguage->languageText('mod_gradebook_viewassessments','gradebook'),"80%",NULL,"right");
//add the options to the drop down
$objAssessments = 0;
$objAssessments = new dropdown('dropdownAssessments');
$objAssessments->extra = ' onchange="document.getElementById(\'form_selectAssessment\').submit();"';
if($dropdownAssessments) {
	$objAssessments->addOption($dropdownAssessments,$dropdownAssessments);
}
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_assignments','gradebook'),$objLanguage->languageText('mod_gradebook_assignments','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_essays','gradebook'),$objLanguage->languageText('mod_gradebook_essays','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_test','gradebook'),$objLanguage->languageText('mod_gradebook_test','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_worksheet','gradebook'),$objLanguage->languageText('mod_gradebook_wordworksheet','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_viewAll','gradebook'),$objLanguage->languageText('mod_gradebook_viewAll','gradebook'));
$this->TableOptions->addCell($objAssessments->show(),"20%");
$this->TableOptions->addCell("&nbsp;","20%");
$this->TableOptions->endRow();
$objForm->addToForm($this->TableOptions->show());
echo $objForm->show();

//select course text, for proper alignment, fit within table
$this->TableInstructions = $this->newObject('htmltable', 'htmlelements');
$this->TableInstructions->cellspacing="2";
$this->TableInstructions->width=($dropdownAssessments && $dropdownAssessments!="View All"?"80%":"100%");
//$this->TableInstructions->attributes="align=\"center\"";

$this->TableInstructions->startHeaderRow();//starthead1 
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_studentNumber','gradebook'),"17%");
$this->TableInstructions->addHeaderCell('&nbsp;&nbsp;'.$objLanguage->languageText('mod_gradebook_student','gradebook'),($dropdownAssessments&& $dropdownAssessments!="View All"?"70%":"33%"));
//get the number of assessments
$numberAssessments=0;
switch($dropdownAssessments) {
	case 'Essays':
		$numberAssessments=count($objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'"));
	break;
	case 'MCQ Tests':
		$numberAssessments=count($objTestadmin->getTests($contextCode));
	break;
	case 'Online Worksheets':
		$numberAssessments=count($objWorksheet->getWorksheetsInContext($contextCode));
	break;
	case 'Assignments':
		$numberAssessments=count($objAssignment->getAssignment($contextCode));
	break;
	default:
		$numberAssessments=count($objAssignment->getAssignment($contextCode));
		$numberAssessments+=count($objWorksheet->getWorksheetsInContext($contextCode));
		$numberAssessments+=count($objTestadmin->getTests($contextCode));
		$numberAssessments+=count($objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'"));
	break;
}
if(!$numberAssessments) {
	$this->TableInstructions->startHeaderRow();
	$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_nostudents','gradebook'),NULL,NULL,NULL,NULL," colspan=\"2\"");
	$this->TableInstructions->endHeaderRow();
} else {
	switch($dropdownAssessments) {
		case 'Essays':
			$as=array();
			$essaysArray=array();
			$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
			if(!empty($essaysArray)) {
				foreach($essaysArray as $as) {
					$count+=1;
					$this->TableInstructions->addHeaderCell($as["name"],"10%");
				}
			}
		break;
		case 'MCQ Tests':
			$as=array();
			$testsArray=array();
			$testsArray=$objTestadmin->getTests($contextCode);
			if(!empty($testsArray)) {
				foreach($testsArray as $as) {
					$count+=1;
					$this->TableInstructions->addHeaderCell($as["name"],"10%");
				}
			}
		break;
		case 'Online Worksheets':
			$as=array();
			$worksheetsArray=array();
			$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
			if(!empty($worksheetsArray)) {
				foreach($worksheetsArray as $as) {
					$count+=1;
					$this->TableInstructions->addHeaderCell($as["name"],"10%");
				}
			}
		break;
		case 'Assignments':
			$as=array();
			$assignmentsArray=array();
			$assignmentsArray=$objAssignment->getAssignment($contextCode);
			if(!empty($assignmentsArray)) {
				foreach($assignmentsArray as $as) {
					$count+=1;
					$this->TableInstructions->addHeaderCell($as["name"],"10%");
				}
			}
		break;
		default:
			//Essays
			$as=array();
			$essaysArray=array();
			$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
			if(!empty($essaysArray)) {
				foreach($essaysArray as $as) {
					$count+=1;
					$this->TableInstructions->addHeaderCell($as["name"],"10%");
				}
			}
			//MCQ tests
			$as=array();
			$testsArray=array();
			$testsArray=$objTestadmin->getTests($contextCode);
			if(!empty($testsArray)) {
				foreach($testsArray as $as) {
					$count+=1;
					$this->TableInstructions->addHeaderCell($as["name"],"10%");
				}
			}
			//online worksheets
			$as=array();
			$worksheetsArray=array();
			$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
			if(!empty($worksheetsArray)) {
				foreach($worksheetsArray as $as) {
				$count+=1;
					$this->TableInstructions->addHeaderCell($as["name"],"10%");
				}
			}
			//assignments
			$as=array();
			$assignmentsArray=array();
			$assignmentsArray=$objAssignment->getAssignment($contextCode);
			if(!empty($assignmentsArray)) {
				foreach($assignmentsArray as $as) {
					$count+=1;
					$this->TableInstructions->addHeaderCell($as["name"],"10%");
				}
			}
		break;
	}
}

$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_yearMark','gradebook'),"10%");
$this->TableInstructions->endHeaderRow();


//Ok code 
//get the students in this course
$userId=array();
$firstName=array();
$surname=array();

$firstName = $this->objGradebook->getStudentInContextInfo('firstname');
$surname = $this->objGradebook->getStudentInContextInfo('surname');
$userId = $this->objGradebook->getStudentInContextInfo('userid');

$numberStudents=0;
$numberStudents=$this->objGradebook->getNumberStudentsInContext();

if(!$numberStudents) {
	$this->TableInstructions->startRow();
	$this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_nostudents','gradebook'),NULL,NULL,NULL,NULL," colspan=\"2\"");
	$this->TableInstructions->endRow();
} else {
	for($i=1;$i<=$numberStudents;$i++) {
		$this->TableInstructions->startRow(!($i%2)?"odd":"even");
		$this->TableInstructions->addCell($userId[$i-1]);
		$objLink = new link($this->uri(array('action'=>'assessmentDetails','assessment'=>$dropdownAssessments,'studentuserid'=>$userId[$i-1])));
		$objLink->link=$firstName[$i-1].' '.$surname[$i-1];
		$this->TableInstructions->addCell('&nbsp;&nbsp;'.$objLink->show());
		
			
		if($dropdownAssessments) {
			//based on the assessment, query the relevant results/tables
			switch($dropdownAssessments) {
				case 'Essays':
					//retrieve grades from Essays
					$as=array();
					$essaysArray=array();
					$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
					if(!empty($essaysArray)) {
						foreach($essaysArray as $as) {
							$count+=1;
							$annualResult1=array();
							$iEssayBook=array();
							$iEssayBook=$objEssaybook->getGrades(
									"tbl_essay_book.studentId='".$userId[$i-1]."' and tbl_essay_book.context='$contextCode' and tbl_essay_book.topicid='$as[id]' and tbl_essay_book.context=tbl_essay_topics.context and tbl_essay_book.topicid=tbl_essay_topics.id",
									"(tbl_essay_book.mark/100)*tbl_essay_topics.percentage result",
									"tbl_essay_book,tbl_essay_topics");
							if(!empty($iEssayBook)) {
								foreach($iEssayBook as $annualResult1) {
									$totalEssays=round($annualResult1["result"],2);
									$total+=round($annualResult1["result"],2);
									//essays
									$this->TableInstructions->addCell(($totalEssays?$totalEssays:""));
								}
							} else {
								$this->TableInstructions->addCell("&nbsp;");
							}
						}
					}
					$this->TableInstructions->addCell('&nbsp;&nbsp;'.($total?$total:""));
					//$this->TableInstructions->endRow();

				break;
				case 'MCQ Tests':
					//retrieve grades from MCQ Tests
					$as=array();
					$testsArray=array();
					$testsArray=$objTestadmin->getTests($contextCode);                                        
					if(!empty($testsArray)) {
						foreach($testsArray as $as) {
							$count+=1;
							$annualResult2=array();
							$iTestresults=array();
							$iTestresults=$objTestresults->getAnnualResults(
								"tbl_test_results.studentid='".$userId[$i-1]."' and 
                                                                tbl_test_results.testId=tbl_tests.id and tbl_tests.id='$as[id]'
                                                                and tbl_tests.context='$contextCode'",
								"(tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage result",
								"tbl_test_results,tbl_tests");
							if(!empty($iTestresults)) {
								foreach($iTestresults as $annualResult2) {
                                                                    $result = $annualResult2["result"];
                                                                    if(isset ($result)){
									$totalTests=round($result,2);
									$total+=round($result,2);
                                                                        
									//tests
									$this->TableInstructions->addCell(($totalTests?$totalTests:""));
                                                                    }
								}
							} else {
								$this->TableInstructions->addCell("&nbsp;");
							}
						}
					}
					$this->TableInstructions->addCell('&nbsp;&nbsp;'.($total?$total:""));
					//$this->TableInstructions->endRow();
				break;
				case 'Online Worksheets':
					//retrieve grades from Online Worksheets
					$as=array();
					$worksheetsArray=array();
					$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
					if(!empty($worksheetsArray)) {
						foreach($worksheetsArray as $as) {
							$count+=1;
		
							$annualResult3=array();
							$iWorksheetresults=array();
							$iWorksheetresults=$objWorksheetresults->getAnnualResults(
									"tbl_worksheet_results.userid='".$userId[$i-1]."' and tbl_worksheet.context='$contextCode' and tbl_worksheet.id='$as[id]' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id",
									"(tbl_worksheet_results.mark/100)*tbl_worksheet.percentage result",
									"tbl_worksheet_results,tbl_worksheet");
							if(!empty($iWorksheetresults)) {
								foreach($iWorksheetresults as $annualResult3) {
									$totalWorksheets=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
									$total+=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
									//worksheets
									$this->TableInstructions->addCell(($totalWorksheets?$totalWorksheets:""));
								}
							} else {
								$this->TableInstructions->addCell("&nbsp;");
							}
						}
					}
					$this->TableInstructions->addCell('&nbsp;&nbsp;'.($total?$total:""));
					//$this->TableInstructions->endRow();
				break;
				case 'Assignments':
					//retrieve grades from assignments
					$as=array();
					$assignmentsArray=array();
					$assignmentsArray=$objAssignment->getAssignment($contextCode);
					if(!empty($assignmentsArray)) {
						foreach($assignmentsArray as $as) {
							$count+=1;
		
							$annualResult4=array();
							$iAssignmentSubmit=array();
							$iAssignmentSubmit=$objAssignmentSubmit->getSubmittedAssignments(
									"tbl_assignment_submit.userid='".$userId[$i-1]."' and tbl_assignment.context='$contextCode' and tbl_assignment.id='$as[id]' and tbl_assignment_submit.assignmentId=tbl_assignment.id",
									"(tbl_assignment_submit.mark/100)*tbl_assignment.percentage result",
									"tbl_assignment,tbl_assignment_submit");
							if(!empty($iAssignmentSubmit)) {
								foreach($iAssignmentSubmit as $annualResult4) {
									$totalAssignments=round($annualResult4["result"],2);
									$total+=round($annualResult4["result"],2);
									//assignment
									$this->TableInstructions->addCell(($totalAssignments?$totalAssignments:""));
								}
							} else {
								$this->TableInstructions->addCell("&nbsp;");
							}
						}
					}
					$this->TableInstructions->addCell('&nbsp;&nbsp;'.($total?$total:""));
			//		$this->TableInstructions->endRow();
				break;
				default:
					//total grades
	
					$total=0;
					$totalAssignments=0;
					$totalEssays=0;
					$totalTests=0;
					$totalWorksheets=0;
					//retrieve grades from Essays
					$as=array();
					$essaysArray=array();
					$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
					if(!empty($essaysArray)) {
						foreach($essaysArray as $as) {
							$count+=1;
							$annualResult1=array();
							$iEssayBook=array();
							$iEssayBook=$objEssaybook->getGrades(
									"tbl_essay_book.studentId='".$userId[$i-1]."' and tbl_essay_book.context='$contextCode' and tbl_essay_book.topicid='$as[id]' and tbl_essay_book.context=tbl_essay_topics.context and tbl_essay_book.topicid=tbl_essay_topics.id",
									"(tbl_essay_book.mark/100)*tbl_essay_topics.percentage result",
									"tbl_essay_book,tbl_essay_topics");
							if(!empty($iEssayBook)) {
								foreach($iEssayBook as $annualResult1) {
									$totalEssays=round($annualResult1["result"],2);
									$total+=round($annualResult1["result"],2);
									//essays
									$this->TableInstructions->addCell(($totalEssays?$totalEssays:""));
								}
							} else {
								$this->TableInstructions->addCell("&nbsp;");
							}
						}
					}
			$this->TableInstructions->endRow();		
					//retrieve grades from MCQ Tests
					$as=array();
					$testsArray=array();
					$testsArray=$objTestadmin->getTests($contextCode);
					if(!empty($testsArray)) {
						foreach($testsArray as $as) {
							$count+=1;
							$annualResult2=array();
							$iTestresults=array();
							$iTestresults=$objTestresults->getAnnualResults(
								"tbl_test_results.studentId='".$userId[$i-1]."' and tbl_test_results.testId=tbl_tests.id and tbl_tests.id='$as[id]' and tbl_tests.context='$contextCode'",
								"(tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage result",
								"tbl_test_results,tbl_tests");
							if(!empty($iTestresults)) {
								foreach($iTestresults as $annualResult2) {
									$totalTests=round($annualResult2["result"],2);
									$total+=round($annualResult2["result"],2);
									//tests
									$this->TableInstructions->addCell(($totalTests?$totalTests:""));
								}
							} else {
								$this->TableInstructions->addCell("&nbsp;");
							}
						}
					}
					
					//retrieve grades from Online Worksheets
					$as=array();
					$worksheetsArray=array();
					$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
					if(!empty($worksheetsArray)) {
						foreach($worksheetsArray as $as) {
							$count+=1;
		
							$annualResult3=array();
							$iWorksheetresults=array();
							$iWorksheetresults=$objWorksheetresults->getAnnualResults(
									"tbl_worksheet_results.userid='".$userId[$i-1]."' and tbl_worksheet.context='$contextCode' and tbl_worksheet.id='$as[id]' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id",
									"(tbl_worksheet_results.mark/100)*tbl_worksheet.percentage result",
									"tbl_worksheet_results,tbl_worksheet");
							if(!empty($iWorksheetresults)) {
								foreach($iWorksheetresults as $annualResult3) {
									$totalWorksheets=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
									$total+=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
									//worksheets
									$this->TableInstructions->addCell(($totalWorksheets?$totalWorksheets:""));
								}
							} else {
								$this->TableInstructions->addCell("&nbsp;");
							}
						}
					}
					
					//retrieve grades from assignments
					$as=array();
					$assignmentsArray=array();
					$assignmentsArray=$objAssignment->getAssignment($contextCode);
					if(!empty($assignmentsArray)) {
						foreach($assignmentsArray as $as) {
							$count+=1;
		
							$annualResult4=array();
							$iAssignmentSubmit=array();
							$iAssignmentSubmit=$objAssignmentSubmit->getSubmittedAssignments(
									"tbl_assignment_submit.userid='".$userId[$i-1]."' and tbl_assignment.context='$contextCode' and tbl_assignment.id='$as[id]' and tbl_assignment_submit.assignmentId=tbl_assignment.id",
									"(tbl_assignment_submit.mark/100)*tbl_assignment.percentage result",
									"tbl_assignment,tbl_assignment_submit");
							if(!empty($iAssignmentSubmit)) {
								foreach($iAssignmentSubmit as $annualResult4) {
									$totalAssignments=round($annualResult4["result"],2);
									$total+=round($annualResult4["result"],2);
									//assignment
									$this->TableInstructions->addCell(($totalAssignments?$totalAssignments:""));
								}
							} else {
								$this->TableInstructions->addCell("&nbsp;");
							}
						}
					}
					
					//display the total grade
					$this->TableInstructions->addCell('&nbsp;&nbsp;'.($total?$total:""));
					$this->TableInstructions->endRow();
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
						
			$as=array();
			$essaysArray=array();
			$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
			if(!empty($essaysArray)) {
				foreach($essaysArray as $as) {
					$count+=1;
					$annualResult1=array();
					$iEssayBook=array();
					$iEssayBook=$objEssaybook->getGrades(
							"tbl_essay_book.studentId='".$userId[$i-1]."' and tbl_essay_book.context='$contextCode' and tbl_essay_book.topicid='$as[id]' and tbl_essay_book.context=tbl_essay_topics.context and tbl_essay_book.topicid=tbl_essay_topics.id",
							"(tbl_essay_book.mark/100)*tbl_essay_topics.percentage result",
							"tbl_essay_book,tbl_essay_topics");
					if(!empty($iEssayBook)) {
						foreach($iEssayBook as $annualResult1) {
							$totalEssays=round($annualResult1["result"],2);
							$total+=round($annualResult1["result"],2);
							//essays
							$this->TableInstructions->addCell(($totalEssays?$totalEssays:""));
						}
					} else {
						$this->TableInstructions->addCell("&nbsp;");
					}
				}
			}
		
			//retrieve grades from MCQ Tests
			$as=array();
			$testsArray=array();
			$testsArray=$objTestadmin->getTests($contextCode);
			if(!empty($testsArray)) {
				foreach($testsArray as $as) {
					$count+=1;
					$annualResult2=array();
					$iTestresults=array();
					$iTestresults=$objTestresults->getAnnualResults(
						"tbl_test_results.studentId='".$userId[$i-1]."' and tbl_test_results.testId=tbl_tests.id and tbl_tests.id='$as[id]' and tbl_tests.context='$contextCode'",
						"(tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage result",
						"tbl_test_results,tbl_tests");
					if(!empty($iTestresults)) {
						foreach($iTestresults as $annualResult2) {
							$totalTests=round($annualResult2["result"],2);
							$total+=round($annualResult2["result"],2);
							//tests
							$this->TableInstructions->addCell(($totalTests?$totalTests:""));
						}
					} else {
						$this->TableInstructions->addCell("&nbsp;");
					}
				}
			}

			//retrieve grades from Online Worksheets
			$as=array();
			$worksheetsArray=array();
			$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
			if(!empty($worksheetsArray)) {
				foreach($worksheetsArray as $as) {
					$count+=1;

					$annualResult3=array();
					$iWorksheetresults=array();
					$iWorksheetresults=$objWorksheetresults->getAnnualResults(
							"tbl_worksheet_results.userid='".$userId[$i-1]."' and tbl_worksheet.context='$contextCode' and tbl_worksheet.id='$as[id]' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id",
							"(tbl_worksheet_results.mark/100)*tbl_worksheet.percentage result",
							"tbl_worksheet_results,tbl_worksheet");
					if(!empty($iWorksheetresults)) {
						foreach($iWorksheetresults as $annualResult3) {
							$totalWorksheets=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
							$total+=round(($annualResult3["result"]<0?0:$annualResult3["result"]),2);
							//worksheets
							$this->TableInstructions->addCell(($totalWorksheets?$totalWorksheets:""));
						}
					} else {
						$this->TableInstructions->addCell("&nbsp;");
					}
				}
			}
			
			//retrieve grades from assignments
			$as=array();
			$assignmentsArray=array();
			$assignmentsArray=$objAssignment->getAssignment($contextCode);
			if(!empty($assignmentsArray)) {
				foreach($assignmentsArray as $as) {
					$count+=1;

					$annualResult4=array();
					$iAssignmentSubmit=array();
					$iAssignmentSubmit=$objAssignmentSubmit->getSubmittedAssignments(
							"tbl_assignment_submit.userid='".$userId[$i-1]."' and tbl_assignment.context='$contextCode' and tbl_assignment.id='$as[id]' and tbl_assignment_submit.assignmentId=tbl_assignment.id",
							"(tbl_assignment_submit.mark/100)*tbl_assignment.percentage result",
							"tbl_assignment,tbl_assignment_submit");
					if(!empty($iAssignmentSubmit)) {
						foreach($iAssignmentSubmit as $annualResult4) {
							$totalAssignments=round($annualResult4["result"],2);
							$total+=round($annualResult4["result"],2);
							//assignment
							$this->TableInstructions->addCell(($totalAssignments?$totalAssignments:""));
						}
					} else {
						$this->TableInstructions->addCell("&nbsp;");
					}
				}
			}
			
			//display the total grade
			$this->TableInstructions->addCell('&nbsp;&nbsp;'.($total?$total:""));
		}
		
		$this->TableInstructions->endRow();
		
	}
}

//highest mark
$this->TableInstructions->startRow();
$this->TableInstructions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_highestMark','gradebook')."</strong>",NULL,NULL,NULL,NULL," colspan=\"2\"");
switch($dropdownAssessments) {
	case 'Essays':
		$as=array();
		$essaysArray=array();
		$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
		if(!empty($essaysArray)) {
			foreach($essaysArray as $as) {
				//max mark
				$max=array();
				$maxEssayArray=array();
				$maxEssayArray=$objEssaybook->getGrades(
					"tbl_essay_book.context='$contextCode' and tbl_essay_book.topicid='$as[id]' and tbl_essay_book.context=tbl_essay_topics.context and tbl_essay_book.topicid=tbl_essay_topics.id",
					"max((tbl_essay_book.mark/100)*tbl_essay_topics.percentage) maxmark",
					"tbl_essay_book,tbl_essay_topics");
				if(!empty($maxEssayArray)) {
					foreach($maxEssayArray as $max) {
						$this->TableInstructions->addCell($max["maxmark"]?round($max["maxmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	case 'MCQ Tests':
		$as=array();
		$testsArray=array();
		$testsArray=$objTestadmin->getTests($contextCode);
		if(!empty($testsArray)) {
			foreach($testsArray as $as) {
				//max mark
				$max=array();
				$maxTestsArray=array();
				$maxTestsArray=$objTestresults->getAnnualResults(
						"tbl_test_results.testid=tbl_tests.id and tbl_tests.id='$as[id]' and tbl_tests.context='$contextCode'",
						"max((tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage) maxmark",
						"tbl_test_results,tbl_tests");
				if(!empty($maxTestsArray)) {
					foreach($maxTestsArray as $max) {
						$this->TableInstructions->addCell($max["maxmark"]?round($max["maxmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	case 'Online Worksheets':
		$as=array();
		$worksheetsArray=array();
		$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
		if(!empty($worksheetsArray)) {
			foreach($worksheetsArray as $as) {
				//max mark
				$max=array();
				$maxWorksheetArray=array();
				$maxWorksheetArray=$objWorksheetresults->getAnnualResults(
						"tbl_worksheet.context='$contextCode' and tbl_worksheet.id='$as[id]' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id",
						"max((tbl_worksheet_results.mark/100)*tbl_worksheet.percentage) maxmark",
						"tbl_worksheet_results,tbl_worksheet");
				if(!empty($maxWorksheetArray)) {
					foreach($maxWorksheetArray as $max) {
						$this->TableInstructions->addCell($max["maxmark"]?round($max["maxmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	case 'Assignments':
		$as=array();
		$assignmentsArray=array();
		$assignmentsArray=$objAssignment->getAssignment($contextCode);
		if(!empty($assignmentsArray)) {
			foreach($assignmentsArray as $as) {
				//max mark
				$max=array();
				$maxAssignmentArray=array();
				$maxAssignmentArray=$objAssignmentSubmit->getSubmittedAssignments(
						"tbl_assignment.context='$contextCode' and tbl_assignment.id='$as[id]' and tbl_assignment_submit.assignmentId=tbl_assignment.id",
						"max((tbl_assignment_submit.mark/100)*tbl_assignment.percentage) maxmark",
						"tbl_assignment,tbl_assignment_submit");
				if(!empty($maxAssignmentArray)) {
					foreach($maxAssignmentArray as $max) {
						$this->TableInstructions->addCell($max["maxmark"]?round($max["maxmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	default:
		//Essays
		$as=array();
		$essaysArray=array();
		$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
		if(!empty($essaysArray)) {
			foreach($essaysArray as $as) {
				//max mark
				$max=array();
				$maxEssayArray=array();
				$maxEssayArray=$objEssaybook->getGrades(
					"tbl_essay_book.context='$contextCode' and tbl_essay_book.topicid='$as[id]' and tbl_essay_book.context=tbl_essay_topics.context and tbl_essay_book.topicid=tbl_essay_topics.id",
					"max((tbl_essay_book.mark/100)*tbl_essay_topics.percentage) maxmark",
					"tbl_essay_book,tbl_essay_topics");
				if(!empty($maxEssayArray)) {
					foreach($maxEssayArray as $max) {
						$this->TableInstructions->addCell($max["maxmark"]?round($max["maxmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
		
		//MCQ Tests
		$as=array();
		$testsArray=array();
		$testsArray=$objTestadmin->getTests($contextCode);
		if(!empty($testsArray)) {
			foreach($testsArray as $as) {
				//max mark
				$max=array();
				$maxTestsArray=array();
				$maxTestsArray=$objTestresults->getAnnualResults(
						"tbl_test_results.testId=tbl_tests.id and tbl_tests.id='$as[id]' and tbl_tests.context='$contextCode'",
						"max((tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage) maxmark",
						"tbl_test_results,tbl_tests");
				if(!empty($maxTestsArray)) {
					foreach($maxTestsArray as $max) {
						$this->TableInstructions->addCell($max["maxmark"]?round($max["maxmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
		
		//online worksheets
		$as=array();
		$worksheetsArray=array();
		$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
		if(!empty($worksheetsArray)) {
			foreach($worksheetsArray as $as) {
				//max mark
				$max=array();
				$maxWorksheetArray=array();
				$maxWorksheetArray=$objWorksheetresults->getAnnualResults(
						"tbl_worksheet.context='$contextCode' and tbl_worksheet.id='$as[id]' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id",
						"max((tbl_worksheet_results.mark/100)*tbl_worksheet.percentage) maxmark",
						"tbl_worksheet_results,tbl_worksheet");
				if(!empty($maxWorksheetArray)) {
					foreach($maxWorksheetArray as $max) {
						$this->TableInstructions->addCell($max["maxmark"]?round($max["maxmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}

		//assignments
		$as=array();
		$assignmentsArray=array();
		$assignmentsArray=$objAssignment->getAssignment($contextCode);
		if(!empty($assignmentsArray)) {
			foreach($assignmentsArray as $as) {
				//max mark
				$max=array();
				$maxAssignmentArray=array();
				$maxAssignmentArray=$objAssignmentSubmit->getSubmittedAssignments(
						"tbl_assignment.context='$contextCode' and tbl_assignment.id='$as[id]' and tbl_assignment_submit.assignmentId=tbl_assignment.id",
						"max((tbl_assignment_submit.mark/100)*tbl_assignment.percentage) maxmark",
						"tbl_assignment,tbl_assignment_submit");
				if(!empty($maxAssignmentArray)) {
					foreach($maxAssignmentArray as $max) {
						$this->TableInstructions->addCell($max["maxmark"]?round($max["maxmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
}

$this->TableInstructions->endRow();

//lowest mark
$this->TableInstructions->startRow();
$this->TableInstructions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_lowestMark','gradebook')."</strong>",NULL,NULL,NULL,NULL," colspan=\"2\"");
switch($dropdownAssessments) {
	case 'Essays':
		$as=array();
		$essaysArray=array();
		$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
		if(!empty($essaysArray)) {
			foreach($essaysArray as $as) {
				//min mark
				$min=array();
				$minEssayArray=array();
				$minEssayArray=$objEssaybook->getGrades(
					"tbl_essay_book.context='$contextCode' and tbl_essay_book.topicid='$as[id]' and tbl_essay_book.context=tbl_essay_topics.context and tbl_essay_book.topicid=tbl_essay_topics.id",
					"min((tbl_essay_book.mark/100)*tbl_essay_topics.percentage) minmark",
					"tbl_essay_book,tbl_essay_topics");
				if(!empty($minEssayArray)) {
					foreach($minEssayArray as $min) {
						$this->TableInstructions->addCell($min["minmark"]?round($min["minmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	case 'MCQ Tests':
		$as=array();
		$testsArray=array();
		$testsArray=$objTestadmin->getTests($contextCode);
		if(!empty($testsArray)) {
			foreach($testsArray as $as) {
				//min mark
				$min=array();
				$minTestsArray=array();
				$minTestsArray=$objTestresults->getAnnualResults(
						"tbl_test_results.testId=tbl_tests.id and tbl_tests.id='$as[id]' and tbl_tests.context='$contextCode'",
						"min((tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage) minmark",
						"tbl_test_results,tbl_tests");
				if(!empty($minTestsArray)) {
					foreach($minTestsArray as $min) {
						$this->TableInstructions->addCell($min["minmark"]?round($min["minmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	case 'Online Worksheets':
		$as=array();
		$worksheetsArray=array();
		$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
		if(!empty($worksheetsArray)) {
			foreach($worksheetsArray as $as) {
				//min mark
				$min=array();
				$minWorksheetArray=array();
				$minWorksheetArray=$objWorksheetresults->getAnnualResults(
						"tbl_worksheet.context='$contextCode' and tbl_worksheet.id='$as[id]' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id",
						"min((tbl_worksheet_results.mark/100)*tbl_worksheet.percentage) minmark",
						"tbl_worksheet_results,tbl_worksheet");
				if(!empty($minWorksheetArray)) {
					foreach($minWorksheetArray as $min) {
						$this->TableInstructions->addCell($min["minmark"]?round($min["minmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	case 'Assignments':
		$as=array();
		$assignmentsArray=array();
		$assignmentsArray=$objAssignment->getAssignment($contextCode);
		if(!empty($assignmentsArray)) {
			foreach($assignmentsArray as $as) {
				//min mark
				$min=array();
				$minAssignmentArray=array();
				$minAssignmentArray=$objAssignmentSubmit->getSubmittedAssignments(
						"tbl_assignment.context='$contextCode' and tbl_assignment.id='$as[id]' and tbl_assignment_submit.assignmentId=tbl_assignment.id",
						"min((tbl_assignment_submit.mark/100)*tbl_assignment.percentage) minmark",
						"tbl_assignment,tbl_assignment_submit");
				if(!empty($minAssignmentArray)) {
					foreach($minAssignmentArray as $min) {
						$this->TableInstructions->addCell($min["minmark"]?round($min["minmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	default:
		//Essays
		$as=array();
		$essaysArray=array();
		$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
		if(!empty($essaysArray)) {
			foreach($essaysArray as $as) {
				//min mark
				$min=array();
				$minEssayArray=array();
				$minEssayArray=$objEssaybook->getGrades(
					"tbl_essay_book.context='$contextCode' and tbl_essay_book.topicid='$as[id]' and tbl_essay_book.context=tbl_essay_topics.context and tbl_essay_book.topicid=tbl_essay_topics.id",
					"min((tbl_essay_book.mark/100)*tbl_essay_topics.percentage) minmark",
					"tbl_essay_book,tbl_essay_topics");
				if(!empty($minEssayArray)) {
					foreach($minEssayArray as $min) {
						$this->TableInstructions->addCell($min["minmark"]?round($min["minmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
		
		//MCQ Tests
		$as=array();
		$testsArray=array();
		$testsArray=$objTestadmin->getTests($contextCode);
		if(!empty($testsArray)) {
			foreach($testsArray as $as) {
				//min mark
				$min=array();
				$minTestsArray=array();
				$minTestsArray=$objTestresults->getAnnualResults(
						"tbl_test_results.testId=tbl_tests.id and tbl_tests.id='$as[id]' and tbl_tests.context='$contextCode'",
						"min((tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage) minmark",
						"tbl_test_results,tbl_tests");
				if(!empty($minTestsArray)) {
					foreach($minTestsArray as $min) {
						$this->TableInstructions->addCell($min["minmark"]?round($min["minmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
		
		//online worksheets
		$as=array();
		$worksheetsArray=array();
		$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
		if(!empty($worksheetsArray)) {
			foreach($worksheetsArray as $as) {
				//min mark
				$min=array();
				$minWorksheetArray=array();
				$minWorksheetArray=$objWorksheetresults->getAnnualResults(
						"tbl_worksheet.context='$contextCode' and tbl_worksheet.id='$as[id]' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id",
						"min((tbl_worksheet_results.mark/100)*tbl_worksheet.percentage) minmark",
						"tbl_worksheet_results,tbl_worksheet");
				if(!empty($minWorksheetArray)) {
					foreach($minWorksheetArray as $min) {
						$this->TableInstructions->addCell($min["minmark"]?round($min["minmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}

		//assignments
		$as=array();
		$assignmentsArray=array();
		$assignmentsArray=$objAssignment->getAssignment($contextCode);
		if(!empty($assignmentsArray)) {
			foreach($assignmentsArray as $as) {
				//min mark
				$min=array();
				$minAssignmentArray=array();
				$minAssignmentArray=$objAssignmentSubmit->getSubmittedAssignments(
						"tbl_assignment.context='$contextCode' and tbl_assignment.id='$as[id]' and tbl_assignment_submit.assignmentId=tbl_assignment.id",
						"min((tbl_assignment_submit.mark/100)*tbl_assignment.percentage) minmark",
						"tbl_assignment,tbl_assignment_submit");
				if(!empty($minAssignmentArray)) {
					foreach($minAssignmentArray as $min) {
						$this->TableInstructions->addCell($min["minmark"]?round($min["minmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
}

$this->TableInstructions->endRow();

//average mark
$this->TableInstructions->startRow();
$this->TableInstructions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_classAvg','gradebook')."</strong>",NULL,NULL,NULL,NULL," colspan=\"2\"");
switch($dropdownAssessments) {
	case 'Essays':
		$as=array();
		$essaysArray=array();
		$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
		if(!empty($essaysArray)) {
			foreach($essaysArray as $as) {
				//avg mark
				$avg=array();
				$avgEssayArray=array();
				$avgEssayArray=$objEssaybook->getGrades(
					"tbl_essay_book.context='$contextCode' and tbl_essay_book.topicid='$as[id]' and tbl_essay_book.context=tbl_essay_topics.context and tbl_essay_book.topicid=tbl_essay_topics.id",
					"avg((tbl_essay_book.mark/100)*tbl_essay_topics.percentage) avgmark",
					"tbl_essay_book,tbl_essay_topics");
				if(!empty($avgEssayArray)) {
					foreach($avgEssayArray as $avg) {
						$this->TableInstructions->addCell($avg["avgmark"]?round($avg["avgmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	case 'MCQ Tests':
		$as=array();
		$testsArray=array();
		$testsArray=$objTestadmin->getTests($contextCode);
		if(!empty($testsArray)) {
			foreach($testsArray as $as) {
				//avg mark
				$avg=array();
				$avgTestsArray=array();
				$avgTestsArray=$objTestresults->getAnnualResults(
						"tbl_test_results.testId=tbl_tests.id and tbl_tests.id='$as[id]' and tbl_tests.context='$contextCode'",
						"avg((tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage) avgmark",
						"tbl_test_results,tbl_tests");
				if(!empty($avgTestsArray)) {
					foreach($avgTestsArray as $avg) {
						$this->TableInstructions->addCell($avg["avgmark"]?round($avg["avgmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	case 'Online Worksheets':
		$as=array();
		$worksheetsArray=array();
		$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
		if(!empty($worksheetsArray)) {
			foreach($worksheetsArray as $as) {
				//avg mark
				$avg=array();
				$avgWorksheetArray=array();
				$avgWorksheetArray=$objWorksheetresults->getAnnualResults(
						"tbl_worksheet.context='$contextCode' and tbl_worksheet.id='$as[id]' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id",
						"avg((tbl_worksheet_results.mark/100)*tbl_worksheet.percentage) avgmark",
						"tbl_worksheet_results,tbl_worksheet");
				if(!empty($avgWorksheetArray)) {
					foreach($avgWorksheetArray as $avg) {
						$this->TableInstructions->addCell($avg["avgmark"]?round($avg["avgmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	case 'Assignments':
		$as=array();
		$assignmentsArray=array();
		$assignmentsArray=$objAssignment->getAssignment($contextCode);
		if(!empty($assignmentsArray)) {
			foreach($assignmentsArray as $as) {
				//avg mark
				$avg=array();
				$avgAssignmentArray=array();
				$avgAssignmentArray=$objAssignmentSubmit->getSubmittedAssignments(
						"tbl_assignment.context='$contextCode' and tbl_assignment.id='$as[id]' and tbl_assignment_submit.assignmentId=tbl_assignment.id",
						"avg((tbl_assignment_submit.mark/100)*tbl_assignment.percentage) avgmark",
						"tbl_assignment,tbl_assignment_submit");
				if(!empty($avgAssignmentArray)) {
					foreach($avgAssignmentArray as $avg) {
						$this->TableInstructions->addCell($avg["avgmark"]?round($avg["avgmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
	default:

		//Essays
		$as=array();
		$essaysArray=array();
		$essaysArray=$objEssaytopics->getTopic(NULL,NULL,"context='$contextCode'");
		if(!empty($essaysArray)) {
			foreach($essaysArray as $as) {
				//avg mark
				$avg=array();
				$avgEssayArray=array();
				$avgEssayArray=$objEssaybook->getGrades(
					"tbl_essay_book.context='$contextCode' and tbl_essay_book.topicid='$as[id]' and tbl_essay_book.context=tbl_essay_topics.context and tbl_essay_book.topicid=tbl_essay_topics.id",
					"avg((tbl_essay_book.mark/100)*tbl_essay_topics.percentage) avgmark",
					"tbl_essay_book,tbl_essay_topics");
				if(!empty($avgEssayArray)) {
					foreach($avgEssayArray as $avg) {
						$this->TableInstructions->addCell($avg["avgmark"]?round($avg["avgmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
		
		//MCQ Tests
		$as=array();
		$testsArray=array();
		$testsArray=$objTestadmin->getTests($contextCode);
		if(!empty($testsArray)) {
			foreach($testsArray as $as) {
				//avg mark
				$avg=array();
				$avgTestsArray=array();
				$avgTestsArray=$objTestresults->getAnnualResults(
						"tbl_test_results.testId=tbl_tests.id and tbl_tests.id='$as[id]' and tbl_tests.context='$contextCode'",
						"avg((tbl_test_results.mark/tbl_tests.totalMark)*tbl_tests.percentage) avgmark",
						"tbl_test_results,tbl_tests");
				if(!empty($avgTestsArray)) {
					foreach($avgTestsArray as $avg) {
						$this->TableInstructions->addCell($avg["avgmark"]?round($avg["avgmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
		
		//online worksheets
		$as=array();
		$worksheetsArray=array();
		$worksheetsArray=$objWorksheet->getWorksheetsInContext($contextCode);
		if(!empty($worksheetsArray)) {
			foreach($worksheetsArray as $as) {
				//avg mark
				$avg=array();
				$avgWorksheetArray=array();
				$avgWorksheetArray=$objWorksheetresults->getAnnualResults(
						"tbl_worksheet.context='$contextCode' and tbl_worksheet.id='$as[id]' and tbl_worksheet_results.worksheet_id=tbl_worksheet.id",
						"avg((tbl_worksheet_results.mark/100)*tbl_worksheet.percentage) avgmark",
						"tbl_worksheet_results,tbl_worksheet");
				if(!empty($avgWorksheetArray)) {
					foreach($avgWorksheetArray as $avg) {
						$this->TableInstructions->addCell($avg["avgmark"]?round($avg["avgmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}

		//assignments
		$as=array();
		$assignmentsArray=array();
		$assignmentsArray=$objAssignment->getAssignment($contextCode);
		if(!empty($assignmentsArray)) {
			foreach($assignmentsArray as $as) {
				//avg mark
				$avg=array();
				$avgAssignmentArray=array();
				$avgAssignmentArray=$objAssignmentSubmit->getSubmittedAssignments(
						"tbl_assignment.context='$contextCode' and tbl_assignment.id='$as[id]' and tbl_assignment_submit.assignmentId=tbl_assignment.id",
						"avg((tbl_assignment_submit.mark/100)*tbl_assignment.percentage) avgmark",
						"tbl_assignment,tbl_assignment_submit");
				if(!empty($avgAssignmentArray)) {
					foreach($avgAssignmentArray as $avg) {
						$this->TableInstructions->addCell($avg["avgmark"]?round($avg["avgmark"],2):'&nbsp;');
					}
				} else {
					$this->TableInstructions->addCell('&nbsp;');
				}
			}
		}
	break;
}

$this->TableInstructions->endRow();

$this->TableInstructions->startRow();
$this->TableInstructions->addCell("&nbsp;",NULL,NULL,NULL,NULL," colspan=\"7\"");
$this->TableInstructions->endRow();
//upload marks for offline assessment
$objLinkUpload = new link($this->uri(array('action'=>'uploadMarks')));
$objLinkUpload->link=$objLanguage->languageText('mod_gradebook_uploadMarks','gradebook');
$this->TableInstructions->startRow();
$this->TableInstructions->addCell($objLinkUpload->show(),NULL,NULL,NULL,NULL," colspan=\"7\"");
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