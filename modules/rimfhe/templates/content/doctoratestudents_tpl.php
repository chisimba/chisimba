<?php
$this->loadClass('htmlheading', 'htmlelements');
/*
*The Tilte of The Page
*/
$pageHeading = new htmlheading();
$pageHeading->type = 2;
$pageHeading->str = $this->objLanguage->languageText('mod_rimfhe_pageheading', 'rimfhe', 'Graduated Doctoral Students');
echo '<br />'.$pageHeading->show();

/*
*The heading of the Form
*/
$formheader = new htmlheading();
$formheader->type = 3;
$formheader->str = $this->objLanguage->languageText('mod_staffregistration_forminstruction', 'rimfhe');

//All fields are Required
$header2 = $this->objLanguage->languageText('mod_staffregistration_required', 'rimfhe', '*All fields are Required except Supervisor. Atleast one Supervisor must be entered');

// Show if no Error
if($mode!='fixerror'){
    echo '<br />'.$formheader->show();
    echo '<br /><span style="color:red;font-size:12px;">'.$header2.'<br /></span>';
}

//load the required form elements
$this->formElements->sendElements();

$doctorateStudents = new form ('doctoralstudents', $this->uri(array('action'=>'registerstaff'), 'rimfhe'));
//add instruction to form
$doctorateStudents->addToForm($this->formInstruction());
/* ---------------------- Form Elements--------*/
//assign laguage objects to variables
$surname = $this->objLanguage->languageText('word_surname', 'system');
$initials= $this->objLanguage->languageText('mod_staffregistration_initials', 'rimfhe');
$firstname= $this->objLanguage->languageText('phrase_firstname', 'system');
$gender= $this->objLanguage->languageText('word_gender', 'system', 'Gender');
$registrationNumber= $this->objLanguage->languageText('mod_doctoratestudents_studentnumber', 'rimfhe', 'Student Registration Number');
$dept= $this->objLanguage->languageText('mod_staffregistration_department', 'rimfhe');
$faculty= $this->objLanguage->languageText('mod_staffregistration_faculty', 'rimfhe');
$thesistitle= $this->objLanguage->languageText('phrase_doctoratestudents', 'rimfhe','Title of Thesis');
$supervisor1= $this->objLanguage->languageText('mod_staffregistration_supervisor1', 'rimfhe', 'Supervisor (1)');
$supervisor2= $this->objLanguage->languageText('mod_staffregistration_supervisor1', 'rimfhe', 'Supervisor (2)');
$supervisor3= $this->objLanguage->languageText('mod_staffregistration_supervisor3', 'rimfhe', 'Supervisor (3)');
$degree= $this->objLanguage->languageText('phrase_confirmemail', 'system');

//create table
$table =new htmltable('registration');
$table->width ='80%';
$table->startRow();

//Input and label for Surname
$objSurname = new textinput('surname');
$surnameLabel = new label($surname,'surname');
$table->addCell($surnameLabel->show(), 1500, NULL, 'left');

$table->addCell($objSurname->show(), 1500, NULL, 'left');
$table->endRow();


$table->startRow();
//Input and label for Initials
$objInitials = new textinput ('initials');
$initialsLabel = new label($initials.'&nbsp;', 'initials');
$table->addCell($initialsLabel->show(), 150, NULL, 'left');
$table->addCell($objInitials->show(), 150, NULL, 'left');
$table->endRow();


//Input and label for Firtstname
$table->startRow();
$objFirstname = new textinput('firstname');
$objFirstname->label='Name(must be filled out)';
$firsnameLabel = new label($firstname,'firstname');
$table->addCell($firsnameLabel->show(), 150, NULL, 'left');
$table->addCell($objFirstname->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Gender
$table->startRow();
$objGender = new dropdown('gender');
$genderLabel = new label($gender.'&nbsp;', 'gender');
$types=array("Male", "Female");
foreach ($types as $type)
{
    $objGender->addOption($type,$type);
}
$table->addCell($genderLabel->show(), 150, NULL, 'left');
$table->addCell($objGender->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Department/Scool/Division
$table->startRow();
$objDepartment = new textinput ('department');
$departmentLabel = new label($dept.'&nbsp;', 'department');
$table->addCell($departmentLabel->show(), 150, NULL, 'left');
$table->addCell($objDepartment ->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Faculty
$table->startRow();
$objFaculty = new textinput ('faculty');
$facultyLabel = new label($faculty.'&nbsp;', 'faculty');
$table->addCell($facultyLabel->show(), 150, NULL, 'left');
$table->addCell($objFaculty ->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Student Number
$table->startRow();
$objStudNumber = new textinput ('studnumber');
$studNumberLabel = new label($registrationNumber.'&nbsp;', 'studnumber');
$table->addCell($studNumberLabel->show(), 150, NULL, 'left');
$table->addCell($objStudNumber->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Title of Thesis
$table->startRow();
$objthesis = new textinput ('thesis');
$thesisLabel = new label($thesistitle.'&nbsp;', 'thesis');
$table->addCell($thesisLabel->show(), 150, NULL, 'left');
$table->addCell($objthesis->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for 1st Supervisor
$table->startRow();
$objSupervisor1 = new textinput ('supervisor1');
$supervisor1Label = new label($supervisor1.'&nbsp;', 'supervisor1');
$table->addCell($supervisor1Label->show(), 150, NULL, 'left');
$table->addCell($objSupervisor1->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Supervisors
$table->startRow();
$objSupervisor2 = new textinput ('supervisor2');
$supervisor2Label = new label($supervisor2.'&nbsp;', 'supervisor2');
$table->addCell($supervisor2Label->show(), 150, NULL, 'left');
$table->addCell($objSupervisor2->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Supervisors
$table->startRow();
$objSupervisor3 = new textinput ('supervisor3');
$supervisor3Label = new label($supervisor3.'&nbsp;', 'supervisor3');
$table->addCell($supervisor3Label->show(), 150, NULL, 'left');
$table->addCell($objSupervisor3->show(), 150, NULL, 'left');
$table->endRow();


//captcha
$table->startRow();
$objCaptcha = $this->getObject('captcha', 'utilities');
$captcha = new textinput('request_captcha');
$captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request'), 'request_captcha');

$table->addCell($captchaLabel ->show(), 150, NULL, 'left');
$content = stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.'));
$table->addCell($content, 150, NULL, 'left');
$table->endRow();
$table->startRow();
$table->addCell(NULL, 150, NULL, 'left');
$table->addCell($objCaptcha->show(), 150, NULL, 'left');
$table->endRow();

$table->startRow();
$table->addCell(NULL, 150, NULL, 'left');
$table->addCell($captcha->show().'  <a href="javascript:redraw();">'.$this->objLanguage->languageText('word_redraw', 'security', 'Redraw').'</a>', 150, NULL, 'left');
$table->endRow();


//submit button
$table->startRow();
$table->addCell(NULL, 150, NULL, 'left');
$button = new button ('submitform', 'Submit Entery');
$button->setToSubmit();
$table->addCell($button->show(), 150, NULL, 'left');
$table->endRow();


//display table
$doctorateStudents->addToForm($table->show());
//display form

//captcha
$table->startRow();
$objCaptcha = $this->getObject('captcha', 'utilities');
$captcha = new textinput('request_captcha');
$captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request'), 'request_captcha');

$table->addCell($captchaLabel ->show(), 150, NULL, 'left');
$content = stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.'));
$table->addCell($content, 150, NULL, 'left');
$table->endRow();
$table->startRow();
$table->addCell(NULL, 150, NULL, 'left');
$table->addCell('<div id="captchaDiv">'.$objCaptcha->show().'</div>', 150, NULL, 'left');
$table->endRow();

$table->startRow();
$table->addCell(NULL, 150, NULL, 'left');
$table->addCell($captcha->show().'  <a href="javascript:redraw();">'.$this->objLanguage->languageText('word_redraw', 'security', 'Redraw').'</a>', 150, NULL, 'left');
$table->endRow();


//submit button
$table->startRow();
$table->addCell(NULL, 150, NULL, 'left');
$button = new button ('submitform', 'Submit Details');
$button->setToSubmit();
$table->addCell($button->show(), 150, NULL, 'left');
$table->endRow();


//display table
$chapterinbook->addToForm($table->show());

//Code to display errors
$messages=array();

if ($mode == 'fixerror') {

    foreach ($problems as $problem)
    {
        $messages[] = $this->explainProblemsInfo($problem);
    }
}
if ($mode == 'fixerror' && count($messages) > 0) {

    echo '<ul><li><span class="error">'.$this->objLanguage->languageText('mod_userdetails_infonotsavedduetoerrors', 'userdetails').'</span>';

    echo '<ul>';
    foreach ($messages as $message)
    {
        if ($message != '') {
            echo '<li class="error">'.$message.'</li>';
        }
    }

    echo '</ul></li></ul>';
}

//display form
echo $doctorateStudents->show();
?>
