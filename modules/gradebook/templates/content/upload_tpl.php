<?php
/* -------------------- gradebook class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/************************** Set up header parameters for javascript date picker *****************/
$headerParams=$this->getJavascriptFile('ts_picker.js','htmlelements');
$headerParams.="<script>/*Script by Denis Gritcyuk: tspicker@yahoo.com
Submitted to JavaScript Kit (http://javascriptkit.com)
Visit http://javascriptkit.com for this script*/ </script>";
$this->appendArrayVar('headerParams',$headerParams);
$this->appendArrayVar('headerParams',$this->getJavascriptFile('sorttable.js','groupadmin') );

//set the layout
$this->setLayoutTemplate('gradebook_layout_tpl.php');

//load required form elements
$this->loadClass('form','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('link','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('hiddeninput','htmlelements');
$this->loadClass('label','htmlelements');

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
//icons
$objIcon =& $this->newObject('geticon','htmlelements');
//Popup calendar 
$this->objpopcal =&$this->getObject('datepickajax','popupcalendar');

//create the general form class
$objForm = new form('upload');
$objForm->setAction($this->uri(array()));
$objForm->displayType=3;  //Free form

//set up rules for this particular form
$objForm->addRule('assessmentName',$this->objLanguage->languageText('mod_gradebook_checkAssessments','gradebook'), 'required');

//context management
$contextObject =& $this->getObject('dbcontext', 'context');
$contextCode = $contextObject->getContextCode();
$theCourse=0;

$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=2; //Heading <h3>
//$this->objH->align="center";
$this->objH->str=($contextCode?$contextObject->getMenuText($contextCode):'').' '.$objLanguage->languageText('mod_gradebook_title','gradebook');
$this->objH->str.=' - ';
$this->objH->str.=$objLanguage->languageText('mod_gradebook_uploadMarksTitle','gradebook');
echo $this->objH->show();

//select assignment, essays, onlineworksheets, MCQ tests etc
$this->TableOptions = $this->newObject('htmltable', 'htmlelements');
$this->TableOptions->cellspacing="2";
$this->TableOptions->width="90%";
//$this->TableOptions->attributes="align=\"center\"";

//assessment name
$this->TableOptions->startRow();
$this->TableOptions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_assessmentName','gradebook').":</strong>","30%",NULL,"left");
$objAssessmentName = new textinput('assessmentName');
$objAssessmentName->size="45";
$this->TableOptions->addCell($objAssessmentName->show());
$this->TableOptions->endRow();

//assessment type
$this->TableOptions->startRow();
$this->TableOptions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_assessmentType','gradebook').":</strong>","30%",NULL,"left");
//add the options to the drop down
$objAssessments = 0;
$objAssessments = new dropdown('assessmentType');
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_assignments','gradebook'),$objLanguage->languageText('mod_gradebook_assignments','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_essays','gradebook'),$objLanguage->languageText('mod_gradebook_essays','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_test','gradebook'),$objLanguage->languageText('mod_gradebook_test','gradebook'));
$objAssessments->addOption($objLanguage->languageText('mod_gradebook_worksheet','gradebook'),$objLanguage->languageText('mod_gradebook_worksheet','gradebook'));
$this->TableOptions->addCell($objAssessments->show());
$this->TableOptions->endRow();

//percent final mark
$this->TableOptions->startRow();
$this->TableOptions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_percentFinalMark','gradebook').":</strong>","30%",NULL,"left");
$objFinalMark = new dropdown('percentFinalMark');
$objFinalMark->addOption($objLanguage->languageText('mod_gradebook_selectPercentage','gradebook'),$objLanguage->languageText('mod_gradebook_selectPercentage','gradebook'));
$objFinalMark->setSelected($objLanguage->languageText('mod_gradebook_selectPercentage','gradebook'));
for($i=0;$i<=100;$i++) {
	$objFinalMark->addOption($i,$i."%");
}
$this->TableOptions->addCell($objFinalMark->show());
$this->TableOptions->endRow();

//closing date
$this->TableOptions->startRow();
$this->TableOptions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_closingDate','gradebook').":</strong>","30%",NULL,"left");
$objClosingDate = new textinput('closingDate',date('Y-m-d H:m'));
$objClosingDate->size="20";
$objClosingDate->extra = " readonly = 'READONLY'";
$objIcon->setIcon('select_date');
$objIcon->title = $this->objLanguage->languageText('mod_gradebook_selectDate','gradebook');
$url = 0;

$this->TableOptions->addCell($this->objpopcal->show('closingDate','yes','no',$objClosingDate->value));
$this->TableOptions->endRow(); 
//$url = $this->uri(array('action'=>'', 'field'=>'document.upload.closingDate', 'fieldvalue'=>date('Y-m-d H:m')), 'popupcalendar');
/*$onclick = 0;
$onclick = "javascript:window.open('" .$url."', 'popupcal', 'width=320, height=410, scrollbars=1, resize=yes')";
$objDateLink = new link('#');
$objDateLink->extra = "onclick=\"$onclick\"";
$objDateLink->link = $objIcon->show().' '.$this->objLanguage->languageText('mod_gradebook_selectDate','gradebook');
$this->TableOptions->addCell($objClosingDate->show().' '.$objDateLink->show());
$this->TableOptions->endRow();

//description
$this->TableOptions->startRow();
$this->TableOptions->addCell("<strong>".$objLanguage->languageText('mod_gradebook_description','gradebook').":</strong>","30%",NULL,"left");
$objDescription = new textarea('description');
$this->TableOptions->addCell($objDescription->show());
$this->TableOptions->endRow();
*/
//space
$this->TableOptions->startRow();
$this->TableOptions->addCell("&nbsp;");
$this->TableOptions->addCell("&nbsp;");
$this->TableOptions->endRow();

$objForm->addToForm($this->TableOptions->show());

//select course text, for proper alignment, fit within table
$this->TableInstructions = $this->newObject('htmltable', 'htmlelements');
$this->TableInstructions->cellspacing="2";
$this->TableInstructions->width="90%";
//$this->TableInstructions->attributes="align=\"center\"";

$this->TableInstructions->startHeaderRow();
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_studentNumber','gradebook'),"20%");
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_student','gradebook'),"50%");
$this->TableInstructions->addHeaderCell($objLanguage->languageText('mod_gradebook_yearMark','gradebook'),"30%");
$this->TableInstructions->endHeaderRow();

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
	$this->TableInstructions->addCell($objLanguage->languageText('mod_gradebook_nostudents','gradebook'),NULL,NULL,NULL,NULL," colspan=\"3\"");
	$this->TableInstructions->endRow();
} else {
	for($i=1;$i<=$numberStudents;$i++) {
		$this->TableInstructions->startRow(!($i%2)?"odd":"even");
		$this->TableInstructions->addCell($userId[$i-1]?$userId[$i-1]:'&nbsp;');
		$this->TableInstructions->addCell(($firstName[$i-1]?$firstName[$i-1]:'&nbsp;').' '.($surname[$i-1]?$surname[$i-1]:'&nbsp;'));
		//hidden field with userId
		$objHiddenId = new hiddeninput('userid'.$i,$userId[$i-1]?$userId[$i-1]:'&nbsp;');
		//student mark
		$objStudentMark = new dropdown('studentMark'.$i);
		$objStudentMark->addOption($objLanguage->languageText('mod_gradebook_selectPercentage','gradebook'),$objLanguage->languageText('mod_gradebook_selectPercentage','gradebook'));
		for($j=0;$j<=100;$j++) {
			$objStudentMark->addOption($j,$j."%");
		}		
		$this->TableInstructions->addCell($objStudentMark->show().$objHiddenId->show());
		$this->TableInstructions->endRow();
	}
}
//submit button
$this->TableInstructions->startRow();
//submit button
$objButton = new button('save',$objLanguage->languageText('mod_gradebook_saveMarks','gradebook'));
$objButton->setToSubmit();
//number of students
$objHiddenNumberStudents = new hiddeninput('numberStudents',$numberStudents);
$objHiddenContextCode = new hiddeninput('contextCode',$contextCode);
$objHiddenAction = new hiddeninput('action','saveMarks');
$this->TableInstructions->addCell('&nbsp;');
$this->TableInstructions->addCell($objButton->show().$objHiddenNumberStudents->show().$objHiddenContextCode->show().$objHiddenAction->show(),NULL,NULL,"right");
$this->TableInstructions->addCell('&nbsp;');
$this->TableInstructions->endRow();

//view by students
$this->TableInstructions->startRow();
$objLink = new link($this->uri(array()));
$objLink->link=$objLanguage->languageText('mod_gradebook_goback','gradebook');
$this->TableInstructions->addCell('<br />'.$objLink->show(),NULL,NULL,NULL,NULL," colspan=\"3\"");
$this->TableInstructions->endRow();

$objForm->addToForm($this->TableInstructions->show());
echo $objForm->show();
?>