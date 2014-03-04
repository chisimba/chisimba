<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*! \class add_form_parameters_form
 *
 *  \brief This class provides the actual form for the form metadata modal window.
 *
 * \breif This form contains the form title, description fields that desingers have
 * to fill in when designing their forms. This form also needs the designers email
 * address and whether or not to save all the submit results to the email address
 * or save them in the database for future viewing.

 *  \author Salman Noor
 *  \author CNS Intern
 *          School of Electrical Engineering, WITS Unversity
 *  \version 1.0
 *  \date    November 20, 2010
 *  \warning This module inherits from core classes in chisimba
 *  \warning If the chisimba core class is altered in the future, this classes might
 * need some refactoring.
 */

class add_form_parameters_form extends object {
    /*!
     * \brief Public data member of class language_module that stores an object of this class.
     * \brief This class is composed of one object from the language class in the html elements core module of chisimba.
     * \brief This object models one langauge object that outputs text from the register.conf file.
     * \note This data member is public and can be used by other classes within this folder
     */

    public $objLanguage;

    /*!
     * \brief Private data member of class dbformbuilder_form_list that stores an object of this class.
     * \brief This class is composed of one object from the dbformbuilder_form_list class in the formbuilder.
     * \brief This object is basically an interface to the form list database that stores the metadata
     * of all forms. It provides funcions to insert, update , delete entries in this table as
     * well as many other functions.
     */
    private $objDBFormList;

    /*!
     * \brief Standard Chisimba constructor that sets all the data members of this class
     *
     */

    public function init() {
///Instantiate the language object
        $this->objLanguage = $this->getObject('language', 'language');
///Load the DB form list object
        $this->objDBFormList = $this->getObject('dbformbuilder_form_list', 'formbuilder');
    }

    /*!
     * \brief this function loads all the classes needed to build a form which are
     * within the chisimba core modules.
     *
     */

    private function loadElements() {
///Load the form class
        $this->loadClass('form', 'htmlelements');
///Load the textinput class
        $this->loadClass('textinput', 'htmlelements');
///Load the label class
        $this->loadClass('label', 'htmlelements');
///Load the textarea class
        $this->loadClass('textarea', 'htmlelements');
///Load the button object
        $this->loadClass('button', 'htmlelements');
///Load the validator class from the module html elements
        $this->loadClass('validator', 'htmlelements');
///Load the html heading class from the module html elements
        $this->loadClass('htmlheading', 'htmlelements');
///Load the geticon class from the module html elements
        $this->loadClass('geticon', 'htmlelements');
///Load the hiddeninput class from the module html elements
        $this->loadClass('hiddeninput', 'htmlelements');
///Load the radio class from the module html elements
        $this->loadClass('radio', 'htmlelements');
///Load the dropdown class from the module html elements
        $this->loadClass('dropdown', 'htmlelements');
    }

    /*!
     * \brief This function assembles the actual metadata form using the datamembers
     * and the loaded external classes.
     * \note This member function is called by the public member function
     * show()
     * \warning Some of the code in this member function is dormant and does not
     * affect anything. This dormant code contains extra form fields and content
     * that is currently not required. It might be required int the future in
     * which case it can be easily be added.
     * \return A completely built 'form metadata' form
     */

    private function buildForm() {
        $this->loadElements();
///Create the form with its relavent POST action
        $objForm = new form('formDetails', $this->getFormAction());
        $formNumber = $this->getParam('formNumber');
///If form number id is not empty, get the form meta data details
        if (!empty($formNumber)) {
///Fetch the data form the database
            $formMetaData = $this->objDBFormList->listSingle($formNumber);
            $formExistingDatabaseName = $formMetaData[0]["name"];
            $formExistingTitle = $formMetaData[0]["label"];
            $formExistingDescription = $formMetaData[0]["details"];
            $existingUsersEmailAddress = $formMetaData[0]["submissionemailaddress"];
            $existingSubmissionOption = $formMetaData[0]["submissionoption"];
        } else {
            $formExistingDatabaseName = "";
            $formExistingTitle = "";
            $formExistingDescription = "";
            $existingUsersEmailAddress = "";
            $existingSubmissionOption = "";
        }
///create and insert a form heading
        $formTitle = new htmlheading($this->objLanguage->languagetext("mod_formbuilder_addformtitle", "formbuilder"), 3);
        $objForm->addToForm($formTitle->show());
///create and insert some indicator text
        if ($formNumber == NULL) {
            $temp_form_number_array = array('FORMNUMBER' => $this->objDBFormList->getCurrentFormNumber());
        } else {
            $temp_form_number_array = array('FORMNUMBER' => $formNumber);
        }
        $formNumberIndentifier = $this->objLanguage->code2Txt("mod_formbuilder_addformnumberindentifier", "formbuilder", $temp_form_number_array, "Error. Number Not Available.");
        $objForm->addToForm($formNumberIndentifier . "<br><Br>");

//$formNumberNotifier = $this->objLanguage->languagetext("mod_formbuilder_addformnumbermessage","formbuilder");
//$objForm->addToForm($formNumberNotifier. "<br><br>");
///create and insert a form title field into the form
        $formDatabaseNameLabel = new label($this->objLanguage->languagetext("mod_formbuilder_addformname", "formbuilder"), "formTitle");
        $formDataBaseName = new textinput('formTitle', $formExistingDatabaseName, 'text', '70');
        $formDataBaseName->setCss("text ui-widget-content ui-corner-all");
        $iconFormName = $this->getObject('geticon', 'htmlelements');
        $iconFormName->setIcon('help_small');
        $iconFormName->extra = "id=formNameIcon";
        $iconFormName->alt = "This name will be used as a unique identifier for your form.";


//$objForm->addToForm($formDatabaseNameLabel->show() . "<br />");
//$objForm->addToForm($formDataBaseName->show() ."&nbsp;&nbsp;".$iconFormName->show()."<br />");

        $formTitleLabel = new label("Form Title:", "formLabel");
        $formTitle = new textinput('formLabel', $formExistingTitle, 'text', '70');
        $formTitle->setCss("text ui-widget-content ui-corner-all");
        $iconFormLabel = $this->getObject('geticon', 'htmlelements');
        $iconFormLabel->setIcon('failed');
        $iconFormLabel->extra = "id=formLabelIcon";
        $iconFormLabel->alt = "A null field is not allowed.";
        $objForm->addToForm($formTitleLabel->show() . "<br />");
        $objForm->addToForm($formTitle->show() . "<br />");

///create and insert a email address field into the form
        $formEmailLabel = new label("Enter your e-mail address:", "formEmail");
        $formEmail = new textinput('formEmail', $existingUsersEmailAddress, 'text', '70');
        $formEmail->setCss("text ui-widget-content ui-corner-all");
        $iconFormEmail = $this->getObject('geticon', 'htmlelements');
        $iconFormEmail->setIcon('failed');
        $iconFormEmail->extra = "id=formLabelIcon";
        $iconFormEmail->alt = "A null field is not allowed.";
        $objForm->addToForm($formEmailLabel->show() . "<br />");
        $objForm->addToForm($formEmail->show() . "<br />");

///create and insert a submission dropdown into the form
        $formSubmissionLabel = new label("Select what to do with the submit results from your form:", "formSubmissionRadio");
        $formSubmissionRadio = new dropdown('formSubmissionRadio');
        $formSubmissionRadio->addOption('save_in_database', $this->objLanguage->languagetext("mod_formbuilder_addformsubmitresultsindatabase", "formbuilder"));
        $formSubmissionRadio->addOption('send_email', "Email the results to email address entered above");
        $formSubmissionRadio->addOption('both', "Save the results in the database AND email the results to me");
        if ($existingSubmissionOption == "") {
            $formSubmissionRadio->setSelected('both');
        } else {
            $formSubmissionRadio->setSelected($existingSubmissionOption);
        }
//$formSubmissionRadio->setBreakSpace("<br>");
        $objForm->addToForm($formSubmissionLabel->show() . "<br />");
        $objForm->addToForm("<div id='formSubmissionRadio'>" . $formSubmissionRadio->show() . "</div><br>");

//	$objForm->addToForm("	<div id='formSubmissionRadio'>
//
//<input type='radio' id='radio1' name='formSubmissionResults' /><label for='radio1'>Choice 1</label><br>
//			<input type='radio' id='radio2' name='formSubmissionResults' checked='checked' /><label for='radio2'>Choice 2</label><br>
//			<input type='radio' id='radio3' name='formSubmissionResults' /><label for='radio3'>Choice 3</label><br>
//		</div><br>");
///create and insert a form description field into the form
        $formDescriptionLabel = new label("Briefly describe your form below.<br> This will be the first thing displayed to users who fill in the form.", "formCaption");
        $formDesciption = new textarea('formCaption', $formExistingDescription, 5, 70);
        $formDesciption->setCssClass("text ui-widget-content ui-corner-all");
        $iconFormDescription = $this->getObject('geticon', 'htmlelements');
        $iconFormDescription->setIcon('failed');
        $iconFormDescription->extra = "id=formDescriptionIcon";
        $iconFormDescription->alt = "A null field is not allowed.";
        $objForm->addToForm($formDescriptionLabel->show() . "<br />");
        $objForm->addToForm($formDesciption->show() . "&nbsp;&nbsp;" . "<br />");

///create and insert a hidden field containing the form number into the form
        $formNumber = new hiddeninput('formNumber', null);
        $formNumber->extra = "id=formNumberHiddenInput";
        $objForm->addToForm($formNumber->show() . "<br />");
        $submitButton = new button('submitNewFormDetails');
        $submitButton->setIconClass('decline');
        $submitButton->setValue('Submit General Form Details');
//$submitButton->setToSubmit();
//$objForm->addToForm($submitButton->show() . "<br />");
///retrun the constructed meta data form
        return $objForm->show();
    }

    /*!
     * \brief This private member function is used by the buildform function.
     * \brief This member function contains logic to determine what action to choose
     * for the form and which parameters to send depending on what action invoked this
     * form.
     * \note This member function is private and can only be called by other member
     * functions within this class. This member function only used as an argument
     * or parameter when the form object is created.
     */
    private function getFormAction() {
///Get the action to determine if its add or edit with the default being add.
        $action = $this->getParam("action", "addFormParameters");

///If the action is edit then update the exisitng entry in the database.
        if ($action == "editFormParameters") {
            $currentformNumber = $this->getParam('formNumber');
            $formAction = $this->uri(array("action" => "updateExistingFormParameters", "currentformNumber" => $currentformNumber), "formbuilder");
        } else {
///If the action is add then insert a new entry into the database and then
///proceed to the WYSIWYG form editor.
            $formAction = $this->uri(array("action" => "designWYSIWYGForm"), "formbuilder");
        }

        return $formAction;
    }

    /*!
     * \brief This public member function is allows you to display the built form.
     * \brief All you have to do is create an object of this class and call this member
     * function to display an operational form.
     * \note Besides the constructor for this class, this is the only other public member
     * function.
     * \return A built form metadata form
     */
    public function show() {
        return $this->buildForm();
    }

}
?>


