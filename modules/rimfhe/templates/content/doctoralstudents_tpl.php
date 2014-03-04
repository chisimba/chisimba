<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

if(!isset($mode)){
    $mode = '';
}
$this->loadClass('htmlheading', 'htmlelements');

/*
*The Tilte of The Page
*/
$pageHeading = new htmlheading();
$pageHeading->type = 2;

/*
* New Changes
*/
if($mode =='edit'){
    $editMode= 'update';
    $pageHeading->str = $this->objLanguage->languageText('mod_rimfhe_pgheadingeditdoctoral', 'rimfhe');
}
else{
    $pageHeading->str = $this->objLanguage->languageText('mod_rimfhe_pgheadingdoctoral', 'rimfhe');
    $editMode= '';
}
/*
* End New Changes
*/

echo '<br />'.$pageHeading->show();

/*
*The heading of the Form
*/
$formheader = new htmlheading();
$formheader->type = 3;
$formheader->str = $this->objLanguage->languageText('mod_rimfhe_forminstruction', 'rimfhe');

//All fields are Required
$header2 = $this->objLanguage->languageText('mod_rimfhe_requiredsupervisor', 'rimfhe');

// Show if no Error
if($mode!='fixerror'){
    echo '<br />'.$formheader->show();
    echo '<span style="color:red;font-size:12px;">'.$header2.'<br /><br /></span>';
}
//load the required form elements
$this->formElements->sendElements();


$doctorateStudents = new form ('doctoralstudents', $this->uri(array('action'=>'doctoralstudents', 'editmode' => $editMode), 'rimfhe'));

/* ---------------------- Form Elements--------*/
//assign laguage objects to variables
$surname = $this->objLanguage->languageText('word_surname', 'system');
$initials= $this->objLanguage->languageText('mod_rimfhe_initials', 'rimfhe');
$firstname= $this->objLanguage->languageText('phrase_firstname', 'system');
$gender= $this->objLanguage->languageText('word_gender', 'system', 'Gender');
$nationality= $this->objLanguage->languageText('mod_rimfhe_nationality', 'rimfhe');
$race= $this->objLanguage->languageText('mod_rimfhe_race', 'rimfhe');
$registrationNumber= $this->objLanguage->languageText('mod_rimfhe_studentnumber', 'rimfhe');
$dept= $this->objLanguage->languageText('mod_rimfhe_department', 'rimfhe');
$faculty= $this->objLanguage->languageText('mod_rimfhe_faculty', 'rimfhe');
$thesistitle= $this->objLanguage->languageText('mod_rimfhe_thesistitle', 'rimfhe');
$supervisor1= $this->objLanguage->languageText('mod_rimfhe_supervisor1', 'rimfhe');
$supervisor2= $this->objLanguage->languageText('mod_rimfhe_supervisor2', 'rimfhe');
$supervisor3= $this->objLanguage->languageText('mod_rimfhe_supervisor3', 'rimfhe');
$supervisor1Affiliation= $this->objLanguage->languageText('mod_rimfhe_supervisoraffiliate1', 'rimfhe');
$supervisor2Affiliation= $this->objLanguage->languageText('mod_rimfhe_supervisoraffiliate2', 'rimfhe');
$supervisor3Affiliation= $this->objLanguage->languageText('mod_rimfhe_supervisoraffiliate3', 'rimfhe');
$degree= $this->objLanguage->languageText('mod_rimfhe_degree', 'rimfhe');

//create table
$table =new htmltable('registration');
$table->width ='80%';
$table->startRow();

/*
* New Changes
*/
//hidden id field to be used for update purposes
if($mode == 'edit'){
    $txtId = new textinput("doctoralid", $arrEdit['id'], 'hidden');
    $table->startRow();
    $table->addCell(NULL, 150, NULL, 'left');
    $table->addCell($txtId->show(), 150, NULL, 'left');
    $table->endRow();
}
/*
* End New Changes
*/

//Input and label for Surname
$objSurname = new textinput('surname');
$surnameLabel = new label($surname,'surname');
$table->addCell($surnameLabel->show(), 1500, NULL, 'left');
if($mode == 'fixerror'){
    $objSurname->value =$this->getParam('surname');
}
if($mode == 'edit'){
    $objSurname->value =$arrEdit['surname'];
}
$table->addCell($objSurname->show(), 1500, NULL, 'left');
$table->endRow();


$table->startRow();
//Input and label for Initials
$objInitials = new textinput ('initials');
$initialsLabel = new label($initials.'&nbsp;', 'initials');
if($mode == 'fixerror'){
    $objInitials->value =$this->getParam('initials');
}
if($mode == 'edit'){
    $objInitials->value = $arrEdit['initials'];
}
$table->addCell($initialsLabel->show(), 150, NULL, 'left');
$table->addCell($objInitials->show(), 150, NULL, 'left');
$table->endRow();


//Input and label for Firtstname
$table->startRow();
$objFirstname = new textinput('firstname');
$objFirstname->label='Name(must be filled out)';
$firsnameLabel = new label($firstname,'firstname');
if($mode == 'fixerror'){
    $objFirstname->value =$this->getParam('firstname');
}
if($mode == 'edit'){
    $objFirstname->value =$arrEdit['firstname'];
}
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
    if($mode == 'fixerror'){
        $objGender->setSelected($this->getParam('gender'));
    }
    if($mode == 'edit'){
        $objGender->setSelected($arrEdit['gender']);
    }
}
$table->addCell($genderLabel->show(), 150, NULL, 'left');
$table->addCell($objGender->show(), 150, NULL, 'left');
$table->endRow();
//Nationality
$table->startRow();
$objNationality = new dropdown('nationality');
$nationalityLabel = new label($nationality.'&nbsp;', 'nationality');
$types=array("South African", "Non-South African");
foreach ($types as $type)
{
    $objNationality->addOption($type,$type);
    if($mode == 'fixerror'){
        $objNationality->setSelected($this->getParam('nationality'));
    }
    if($mode == 'edit'){
        $objNationality->setSelected($arrEdit['nationality']);
    }
}
$table->addCell($nationalityLabel->show(), 150, NULL, 'left');
$table->addCell($objNationality->show(), 150, NULL, 'left');
$table->endRow();
//race
$table->startRow();
$objRace = new dropdown('race');
$raceLabel = new label($race.'&nbsp;', 'race');
$types=array("Black", "Coloured","Indian","White","Other");
foreach ($types as $type)
{
    $objRace->addOption($type,$type);
    if($mode == 'fixerror'){
        $objRace->setSelected($this->getParam('race'));
    }
    if($mode == 'edit'){
        $objRace->setSelected($arrEdit['race']);
    }
}
$table->addCell($raceLabel->show(), 150, NULL, 'left');
$table->addCell($objRace->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Department/Scool/Division
$table->startRow();
$objDepartment = new dropdown ('department');
$departmentLabel = new label($dept.'&nbsp;', 'department');
$departments=array("Academic Development", "Accounting", "Afrikaans", "Anthropology & Sociology", "Biodiversity & Conservation", "Biotechnology", "Chemistry", "Computer Sciences", "Dietetics", "Earth Sciences", "Economics", "English", "Foreign Languages", "Geography", "History", "Human Ecology", "Industrial Psychology", "Information Systems", "Library & Info.", "Linguistics", "Management", "Mathematics", "Medical Bioscience", "Nursing", "Occupational Therapy", "Pharmacy", "Philosophy", "Physics", "Physiotherapy", "Political Studies", "Psychology", "Public Administration", "Religion & Theology", "Social Work", "Sport, Recreation & Exercise", "Statistics", "Women & Gender", "Xhosa", "School of Government", "School of Natural Medicine", "School of Pharmacy", "School of Public Health");
foreach ($departments as $department)
{
    $objDepartment->addOption($department,$department);
    if($mode == 'fixerror'){
        $objDepartment->setSelected($this->getParam('department'));
    }
    if($mode == 'edit'){
        $objDepartment->setSelected($arrEdit['deptschoool']);
    }
}
$table->addCell($departmentLabel->show(), 150, NULL, 'left');
$table->addCell($objDepartment ->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Faculty
$table->startRow();
$objFaculty = new dropdown ('faculty');
$facultyLabel = new label($faculty.'&nbsp;', 'faculty');
$faculties=array("Arts", "Community & Health", "Dentistry", "Economic & Management", "Education", "Law", "Natural Science");
foreach ($faculties as $faculty)
{
    $objFaculty->addOption($faculty,$faculty);
    if($mode == 'fixerror'){
        $objFaculty->setSelected($this->getParam('faculty'));
    }
    if($mode == 'edit'){
        $objFaculty->setSelected($arrEdit['faculty']);
    }
}
$table->addCell($facultyLabel->show(), 150, NULL, 'left');
$table->addCell($objFaculty ->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Student Number
$table->startRow();
$objStudNumber = new textinput ('studnumber');
$studNumberLabel = new label($registrationNumber.'&nbsp;', 'studnumber');
if($mode == 'fixerror'){
    $objStudNumber->value =$this->getParam('studnumber');
}
if($mode == 'edit'){
    $objStudNumber->value =$arrEdit['regnumber'];
}
$table->addCell($studNumberLabel->show(), 150, NULL, 'left');
$table->addCell($objStudNumber->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Title of Thesis
$table->startRow();
$objthesis = new textinput ('thesis');
$thesisLabel = new label($thesistitle.'&nbsp;', 'thesis');
$table->addCell($thesisLabel->show(), 150, NULL, 'left');
if($mode == 'fixerror'){
    $objthesis->value =$this->getParam('thesis');
}
if($mode == 'edit'){
    $objthesis->value =$arrEdit['thesistitle'];
}
$table->addCell($objthesis->show(), 150, NULL, 'left');
$table->endRow();

/*
* the Supervisor names are stored with HTML tgas in the Database
* based on the Supervisors Affiliate.
* carry out some PHP operations to reverse tags and
* distinguish affiliates
*/

// split supervisors
if($mode == 'edit'){
    list($Supervisor1, $Supervisor2, $Supervisor3) = explode("<br />", $arrEdit['supervisorname']);

    // Match HTML tags (<b>)
    if (preg_match("/<b>/",$Supervisor1)) {
        $Supervisor1 = strip_tags($Supervisor1);
        $supAffiliate1 = 'UWC Staff Member';
    } else {
        $supAffiliate1 = 'External Supervisor';
    }

    if (preg_match("/<b>/",$Supervisor2)) {
        $Supervisor2 = strip_tags($Supervisor2);
        $supAffiliate2 = 'UWC Staff Member';
    } else {
        $supAffiliate2 = 'External Supervisor';
    }
    if (preg_match("/<b>/",$Supervisor3)) {
        $Supervisor3 = strip_tags($Supervisor3);
        $supAffiliate3 = 'UWC Staff Member';
    } else {
        $supAffiliate3 = 'External Supervisor';
    }
}

//Input and label for 1st Supervisor
$table->startRow();
$objSupervisor1 = new textinput ('supervisor1');
$supervisor1Label = new label($supervisor1.'&nbsp;', 'supervisor1');
if($mode == 'fixerror'){
    $objSupervisor1->value =$this->getParam('supervisor1');
}
if($mode == 'edit'){
    $objSupervisor1->value =$Supervisor1;
}
$table->addCell($supervisor1Label->show(), 150, NULL, 'left');
$table->addCell($objSupervisor1->show(), 150, NULL, 'left');
$table->endRow();

// 1st Supervisor's affiliation
$table->startRow();
$objSupAffiliate1 = new dropdown('supaffiliate1');
$affLabel1 = new label($supervisor1Affiliation, 'supaffiliate1');
$options=array("UWC Staff Member", "External Supervisor");
foreach ($options as $option)
{
    $objSupAffiliate1->addOption($option,$option);
    if($mode == 'fixerror'){
        $objSupAffiliate1->setSelected($this->getParam('supaffiliate1'));
    }
    if($mode == 'edit'){
        $objSupAffiliate1->setSelected($supAffiliate1 );
    }
}
$table->addCell($affLabel1->show(), 150, NULL, 'left');
$table->addCell($objSupAffiliate1->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for 2nd Supervisor
$table->startRow();
$objSupervisor2 = new textinput ('supervisor2');
$supervisor2Label = new label($supervisor2.'&nbsp;', 'supervisor2');
if($mode == 'fixerror'){
    $objSupervisor2->value =$this->getParam('supervisor2');
}
if($mode == 'edit'){
    $objSupervisor2->value =$Supervisor2;
}
$table->addCell($supervisor2Label->show(), 150, NULL, 'left');
$table->addCell($objSupervisor2->show(), 150, NULL, 'left');
$table->endRow();

// 2nd Supervisor's affiliation
$table->startRow();
$objSupAffiliate2 = new dropdown('supaffiliate2');
$affLabel2 = new label($supervisor2Affiliation, 'supaffiliate2');
$options=array("UWC Staff Member", "External Supervisor");
foreach ($options as $option)
{
    $objSupAffiliate2->addOption($option,$option);
    if($mode == 'fixerror'){
        $objSupAffiliate2->setSelected($this->getParam('supaffiliate2'));
    }
    if($mode == 'edit'){
        $objSupAffiliate2->setSelected($supAffiliate2);
    }
}
$table->addCell($affLabel2->show(), 150, NULL, 'left');
$table->addCell($objSupAffiliate2->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Supervisors
$table->startRow();
$objSupervisor3 = new textinput ('supervisor3');
$supervisor3Label = new label($supervisor3.'&nbsp;', 'supervisor3');
if($mode == 'fixerror'){
    $objSupervisor3->value =$this->getParam('supervisor3');
}
if($mode == 'edit'){
    $objSupervisor3->value =$Supervisor3;
}
$table->addCell($supervisor3Label->show(), 150, NULL, 'left');
$table->addCell($objSupervisor3->show(), 150, NULL, 'left');
$table->endRow();

// 3rd Supervisor's affiliation
$table->startRow();
$objSupAffiliate3 = new dropdown('supaffiliate3');
$affLabel3 = new label($supervisor3Affiliation, 'supaffiliate3');
$options=array("UWC Staff Member", "External Supervisor");
foreach ($options as $option)
{
    $objSupAffiliate3->addOption($option,$option);
    if($mode == 'fixerror'){
        $objSupAffiliate3->setSelected($this->getParam('supaffiliate3'));
    }
    if($mode == 'edit'){
        $objSupAffiliate3->setSelected($supAffiliate3);
    }
}
$table->addCell($affLabel3->show(), 150, NULL, 'left');
$table->addCell($objSupAffiliate3->show(), 150, NULL, 'left');
$table->endRow();

//Input and label for Degree
$table->startRow();
$objDegree = new textinput ('degree');
$degreeLabel = new label($degree.'&nbsp;', 'degree');
if($mode == 'fixerror'){
    $objDegree->value =$this->getParam('degree');
}
if($mode == 'edit'){
    $objDegree->value =$arrEdit['degree'];
}
$table->addCell($degreeLabel->show(), 150, NULL, 'left');
$table->addCell($objDegree->show(), 150, NULL, 'left');
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
$table->addCell('<div id="captchaDiv">'.$objCaptcha->show().'</div>', 150, NULL, 'left');
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
$table->addCell($button->show(), '', NULL, 'left');
$table->endRow();

//display table
$doctorateStudents->addToForm($table->show());

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
<script type="text/javascript">
//<![CDATA[
function init () {
    $('input_redraw').onclick = function () {
        redraw();
    }
}
function redraw () {
    var url = 'index.php';
    var pars = 'module=security&action=generatenewcaptcha';
    var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onComplete: showResponse} );
}
function showLoad () {
    $('load').style.display = 'block';
}
function showResponse (originalRequest) {
    var newData = originalRequest.responseText;
    $('captchaDiv').innerHTML = newData;
}
//]]>
</script>
