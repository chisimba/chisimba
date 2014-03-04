<?php

if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * libraryforms
 *
 * libraryforms allows students or distant user to request books online
 *
 * @category  Chisimba
 * @package   libraryforms
 * @author    Brenda Mayinga brendamayinga@ymail.com
 */
class editform extends dbTable {

    public $objLanguage;

    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        parent::init('tbl_distanceform');
    }

    private function loadElements() {
        //Load the form class
        $this->loadClass('form', 'htmlelements');
        //Load the textinput class
        $this->loadClass('textinput', 'htmlelements');
        //Load the dropdownbox
        $this->loadClass('dropdown', 'htmlelements');
        //Load the label class
        $this->loadClass('label', 'htmlelements');
        //Load the button object
        $this->loadClass('button', 'htmlelements');

        $strjs = '<script type="text/javascript">
		//<![CDATA[

     
   
		/***********************************************
        *                                              *
        *              EDITFORM CLASS                  *
        *                                              *
        ***********************************************/
        //<![CDATA[

		function init () {
			$(\'input_editformredraw\').onclick = function () {
				editformredraw();
			}
		}
		function editformredraw () {
			var url = \'index.php\';
			var pars = \'module=security&action=generatenewcaptcha\';
			var myAjax = new Ajax.Request( url, {method: \'get\', parameters: pars, onComplete: editformShowResponse} );
		}
		function showLoad () {
			$(\'load\').style.display = \'block\';
		}
		function editformShowResponse (originalRequest) {
			var newData = originalRequest.responseText;
			$(\'editformcaptchaDiv\').innerHTML = newData;
		}
		//]]>
		</script>';

        $this->appendArrayVar('headerParams', $strjs);
    }

    private function buildForm() {
        //Load the required form elements that we need
        $this->loadElements();

        //Create the form
        $objForm = new form('distanceform', $this->getFormAction());
        $table = $this->newObject('htmltable', 'htmlelements');
        //*****//
        $this->loadClass('htmlheading', 'htmlelements');
        $pageHeading = new htmlheading();
        $pageHeading->type = 2;
        $pageHeading->str = $this->objLanguage->languageText("mod_libraryforms_commenttitleform", "libraryforms", "title");
        $objForm->addToForm($pageHeading->show() . "<br/>" . "<br/>");


        //Create a new textinput for the surname
        $objsurname = new textinput('surname');
        $surnameLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentname", "libraryforms"), "surname");
        $table->startRow();
        $table->addCell($surnameLabel->show(), '', 'center', 'left', '');
        $table->addCell($objsurname->show(), '', 'center', 'left', '');
        $objForm->addRule('surname', $this->objLanguage->languageText
                        ("mod_surname_required", "libraryforms", 'Please enter a surname. Surname missing.'), 'required');

        //Create a new textinput for the surname
        $objinitials = new textinput('initials');
        $initailsLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentinitials", "libraryforms"), "initials");

        $table->addCell($initailsLabel->show(), '', 'center', 'left', '');
        $table->addCell($objinitials->show(), '', 'center', 'left', '');
        $objForm->addRule('initials', $this->objLanguage->languageText
                        ("mod_initials_required", "libraryforms", 'Please enter a initials.initials is missing.'), 'required');
        $table->endRow();


        //Create a new textinput for the title
        $titlesDropdown = new dropdown('select_title');
        $titleLabel = new label($this->objLanguage->languageText("mod_libraryforms_commenttitle", "libraryforms"), "title");
        $titles = array("title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");

        foreach ($titles as $title) {
            $_title = trim($this->objLanguage->languageText($title));
            $titlesDropdown->addOption($_title, $_title);
        }
        if ($mode == 'addfixup') {
            $titlesDropdown->setSelected($this->getParam('select_title'));
        }

        $table->startRow();
        $table->addCell($titleLabel->show(), '', 'center', 'left');
        $table->addCell($titlesDropdown->show());
        $table->endRow();
        $objForm->addRule('select_title', $this->objLanguage->languageText("mod_title_required", "libraryforms", 'Please select a title. A title missing.'), 'required');

        //Create a new textinput for the title
        $objstudno = new textinput('studentno');
        $studnoLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentstudno", "libraryforms"), "stud no");

        $table->addCell($studnoLabel->show(), '', 'center', 'left', '');
        $table->addCell($objstudno->show(), '', 'center', 'left', '');
        $objForm->addRule(array('name' => 'studentno', 'length' => 10), 'Your Studentno is too long', 'maxlength');
        $table->endRow();

        //Create a new textinput for postal
        $objpostal = new textinput('postal');
        $postalLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentpostaladdress", "libraryforms"), "postaladdress");
        $table->startRow();
        $table->addCell($postalLabel->show(), '', 'center', 'left', '');
        $table->addCell($objpostal->show(), '', 'center', 'left', '');
        $objForm->addRule('postal', $this->objLanguage->languageText("mod_postaladdress_required", "libraryforms", 'Please enter a postal address. A postal address is missing .'), 'required');


        //Create a new textinput for postalcode
        $objpostalcode = new textinput('postalcode');
        $codeLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentpostalcode", "libraryforms"), "postalcode");
        $table->addCell($codeLabel->show(), '', 'center', 'left', '');
        $table->addCell($objpostalcode->show(), '', 'center', 'left', '');
        $objForm->addRule(array('name' => 'postalcode', 'length' => 4), 'Your code is too long', 'maxlength');

        //Create a new textinput for physical
        $objphysical = new textinput('physical');
        $pysicallLabel = new label($this->objLanguage->languageText("mod_libraryforms_commenphysicaladdress", "libraryforms"), "physicaladdress");
        $table->startRow();
        $table->addCell($pysicallLabel->show(), '', 'center', 'left', '');
        $table->addCell($objphysical->show(), '', 'center', 'left', '');
        $objForm->addRule('physical', $this->objLanguage->languageText("mod_physicaladdress_required", "libraryforms", 'Please enter a physicaladdress. A physical address is missing.'), 'required');



        //Create a new textinput for postalcode
        $objpostalcode2 = new textinput('postalcode2');
        $code2lLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentpostalcode", "libraryforms"), "postalcode");
        $table->addCell($code2lLabel->show(), '', 'center', 'left', '');
        $table->addCell($objpostalcode2->show(), '', 'center', 'left', '');
        $objForm->addRule(array('name' => 'postalcode2', 'length' => 4), 'Your code is too long', 'maxlength');

        $table->endRow();


        $objtel = new textinput('tel');
        $telLabel = new label($this->objLanguage->languageText("mod_libraryforms_commenttel", "libraryforms"), "tel");
        $table->startRow();
        $table->addCell($telLabel->show(), '', 'center', 'left', '');
        $table->addCell($objtel->show(), '', 'center', 'left', '');


        //Create a new textinput for tel2
        $objtelw = new textinput('telw');
        $telwLabel = new label($this->objLanguage->languageText("mod_libraryforms_commenttel2", "libraryforms"), "telw");
        $table->addCell($telwLabel->show(), '', 'center', 'left', '');
        $table->addCell($objtelw->show(), '', 'center', 'left', '');
        $table->endRow();

        //Create a new textinput for cell

        $table->startRow();
        $objcell = new textinput('cell');
        $cellLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentcell", "libraryforms"), "cell");
        $table->addCell($cellLabel->show(), '', 'center', 'left', '');
        $table->addCell($objcell->show(), '', 'center', 'left', '');
        $objForm->addRule('cell', 'Cell Must contain valid numbers', 'numeric');



        //Create a new textinput for fax
        $objfax = new textinput('fax');
        $faxLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentfax", "libraryforms"), "fax");
        $table->addCell($faxLabel->show(), '', 'center', 'left', '');
        $table->addCell($objfax->show(), '', 'center', 'left', '');
        $table->endRow();


        //Create a new textinput for email
        $objemail = new textinput('emailaddress');
        $emailLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentemail", "libraryforms"), "emailaddress");
        $table->startRow();
        $table->addCell($emailLabel->show(), '', 'center', 'left', '');
        $table->addCell($objemail->show(), '', 'center', 'left', '');
        $objForm->addRule('emailaddress', 'Not a valid Email', 'email');
        $table->endRow();

        //Create a new textinput for course
        $objcourse = new textinput('course');
        $courseLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentcourse", "libraryforms"), "course");
        $table->startRow();
        $table->addCell($courseLabel->show(), '', 'center', 'left', '');
        $table->addCell($objcourse->show(), '', 'center', 'left', '');
        $table->endRow();
        $objForm->addRule('course', $this->objLanguage->languageText("mod_course_unrequired", "libraryforms", 'Please enter a course.Course is missing.'), 'required');

        //Create a new textinput for department
        $objdepartment = new textinput('department');
        $departmentLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentdepartment", "libraryforms"), "department");
        $table->startRow();
        $table->addCell($departmentLabel->show(), '', 'center', 'left', '');
        $table->addCell($objdepartment->show(), '', 'center', 'left', '');
        $objForm->addRule('department', $this->objLanguage->languageText("mod_dept_required", "libraryforms", 'Please enter a department. department is missing.'), 'required');

        //Create a new textinput for department
        $objsuper = new textinput('supervisor');
        $superLabel = new label($this->objLanguage->languageText("mod_libraryforms_commentsupervisor", "libraryforms"), "supervisor");
        $table->addCell($superLabel->show(), '', 'center', 'left', '');
        $table->addCell($objsuper->show(), '', 'center', 'left', '');
        $table->endRow();
        $objForm->addRule('supervisor', $this->objLanguage->languageText("mod_hod_required", "libraryforms", 'Please enter your superviser. A superviser or HOD is required.'), 'required');

        //----------SUBMIT BUTTON--------------
        //Create a button for submitting the form
        $objButton = new button('save');
        // Set the button type to submit
        $objButton->setToSubmit();
        // Use the language object to label button
        // with the word save
        $objButton->setValue(' ' . $this->objLanguage->languageText("mod_libraryforms_savecomment", "libraryforms") . ' ');
        $objForm->addToForm($table->show());


        $objCaptcha = $this->getObject('captcha', 'utilities');
        $captcha = new textinput('editformrequest_captcha');
        $captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request'), 'input_editformrequest_captcha');

        $strutil = stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.')) . '<br /><div id="editformcaptchaDiv">' . $objCaptcha->show() . '</div>' . $captcha->show() . $required . '  <a href="javascript:editformredraw();">' . $this->objLanguage->languageText('word_redraw', 'security', 'Redraw') . '</a>';

        $objForm->addToForm('<br/><br/>' . $strutil . '<br/><br/>');
        $objForm->addRule('editformrequest_captcha', $this->objLanguage->languageText("mod_request_captcha_unrequired", 'security', 'Captcha cant be empty.Captcha is missing.'), 'required');
        $objForm->addToForm($objButton->show());

        return $objForm->show();
    }

     
    function insertRecord($surname, $initials, $title, $studentno, $postaladdress, $physicaladdress, $postalcode, $postalcode2, $telnoh, $telnow, $cell, $fax, $emailaddress, $course, $department, $supervisor) {
        $id = $this->insert(array(
                    'surname' => $surname,
                    'initials' => $initials,
                    'title' => $title,
                    'studentno' => $studentno,
                    'postaladdress' => $postaladdress,
                    'physicaladdress' => $physicaladdress,
                    'postalcode' => $postalcode,
                    'postalcode2' => $postalcode2,
                    'telnoh' => $telnoh,
                    'telnow' => $telnow,
                    'cell' => $cell,
                    'fax' => $fax,
                    'emailaddress' => $emailaddress,
                    'course' => $course,
                    'department' => $department,
                    'supervisor' => $supervisor,
                ));
        return $id;
    }

private function getFormAction() {
        $formAction = $this->uri(array("action" => "save_addedit"), "libraryforms");
        return $formAction;
    }

    public function show() {
        return $this->buildForm();
    }

}

