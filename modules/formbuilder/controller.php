<?php
//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*! \mainpage Form Builder
 *
 * \brief This module is a standalone Chisimba module that permits users to
 *          create HTML forms through a user friendly WYSIWYG interface without
 * having to code anything. This module surrenders control over to the
 * user and provide over eleven different types of form elements; each
 * one that can be built and configured differently.
 *
 * \author Salman Noor
 * \author CNS Intern
 * \author School of Electrical Engineering, WITS Unversity
 * \version 1.0
 * \date    November 20, 2010
 * \bug Possible bugs are text file handling form home page (Not enough error handling).
 * \bug There is a chisimba javascript and jquery conflict. Datepicker, input mask
 * and CKEditor classes do not work in the WYSIWYG form editor.
 *
 * \bug There might be slight variant executions of the same under various internet
 * browsers ie. MS IE and Mozilla Firefox. For satisfactory efficient functioning of this
 * module, use Mozilla firefox or google chrome.
 *
 * \warning This module inherits from core classes in chisimba platform. Chisimba has to be installed.
 * \warning If the chisimba core classes are altered in the future, this module might not work. However, lots of preventative
 * measures have been taken to curb this limitation.
 * \warning This module downloads the CSS for the jQuery UI objects. To use this module, an internet
 * connection is needed.
 *
 * \note If you are new to this module, it is recommended that you start from the \ref formbuilder main class
 */

/*! \class formbuilder
 *
 *  \brief Main class that runs everything.
 *  \brief It is the main class that instantiates the entire module.
 * \brief It can be though of as a client class that uses the actual module
 *  \author Salman Noor
 *  \author CNS Intern
 *          School of Electrical Engineering, WITS Unversity
 *  \version 1.0
 *  \date    November 20, 2010
 *  \warning This module inherits from core classes in chisimba
 *  \warning If the chisimba core class is altered in the future, this module might not work
 */

class formbuilder extends controller {
    /*!
     * \brief Private data member of class language_module that stores an object of another class.
     * \brief This class is composed of one object from the language class in the html elements core module of chisimba.
     * \brief This object models one langauge object that outputs text from the register.conf file.
     */

    public $objlanguage;

    /*!
     * \brief Standard constructor that sets up this class. The constructor
     * is instatiating two object from the chisimba core classes.
     * \note Constructors allow you to create an object of this class.
     */

    public function init() {
///Instatiate an object of the language classes that allows the output of
///some text.
        $this->objLanguage = $this->getObject('language', 'language');
///Instatiate an object the user class belonging to the core chisimba
///module security. Also note that, it is stored as a reference and
///not through class composition.
        $this->objUser = &$this->getObject('user', 'security');
    }

    /*!
     * \brief Standard Chisimba controller dispatch method, the dispatch calls any
     *  method involving logic and hands of the results to the template for display.
     *  \return A member function inside this class.
     */

    public function dispatch() {
///Get action from query string and set default to home
        $action = $this->getParam('action', 'home');
///Convert the action into a method
        $method = $this->__getMethod($action);
///Return the template determined by the method resulting from action
        return $this->$method();
    }

    /*!
     * \brief checks if the action exists
     *  \return A boolean value
     * \param action a string containing the action
     */

    private function __validAction(& $action) {
///Checks if the methods exists and returns a simple boolean value
        if (method_exists($this, "__" . $action)) {
/// Method exists in a member function of the controller class from the chisimba core
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*!
     * \brief Get an action
     * \param action A string
     *  \return the method that corresponds to the action
     */

    private function __getMethod(& $action) {
///Checks using __validAction if the action exists and returns the corresponding method
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
///If methos does not exist return the method __actionError which returns
///a template displaying an error
            return "__actionError";
        }
    }

    /*!
     * \brief This method gets the action that does not exist and passes it
     * to the action_error.php template file
     *  \return A template file \ref action_error.php
     */

    private function __actionError() {
///Get action from query string or REQUEST
        $action = $this->getParam('action');
///Parse the action with an error string and send it to the template file
        $this->setVar('str', "<h3>"
                . $this->objLanguage->languageText("phrase_unrecognizedaction")
                . ": " . $action . "</h3>");
        return 'action_error.php';
    }

    /*!
     * \brief This method is not being used. It will be used if the
     * a developer will implement graph functionality for graphical form
     * submissions viewing.
     *  \return A template file \ref graphtest.php
     */

    private function __graphtest() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        return "graphtest.php";
    }

    /*!
     * \brief This method is not being used. It can be used by developers if they
     * want to test anything
     *  \return A template file \ref test1.php
     */

    private function __test() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        return "test1.php";
    }

    /*!
     * \brief This method is not being used. It will be used if the
     * a developer will implement graph functionality for graphical form
     * submissions viewing.
     *  \return A template file \ref graphtest.php
     */

    private function __newtest() {
        $objFlashGraphData = $this->newObject('flashgraphdata', 'utilities');
///The classes inside the chsimba core modules that allow developers to 
///create grpahs DO NOT WORK. So do not use them. Maybe in the future,
///these classes might be rectified.
        $objFlashGraphData->graphType = 'bar';
        $objFlashGraphData->setupXAxisLabels(array('Jan', 'Feb', 'March'));
        $objFlashGraphData->setupYAxis('Rainy Days', NULL, NULL, 10, 5);
        $objFlashGraphData->addDataSet(array(4, 5, 4), '#3334AD', 50, 'bar', 'Cape Town');
        $objFlashGraphData->addDataSet(array(6, 7, 2), '#00ff00', 50, 'glassbar', 'Johannesburg');
        $objFlashGraphData->addDataSet(array(1, 4, 3), '#9900CC', 50, 'sketchbar', 'Durban');
        return $test = $objFlashGraphData->show();
    }

    /*!
     * \brief This action takes you to the home page of this module.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     *  \return A template file \ref home.php
     */

    private function __home() {
///Setting the jquery version greater than 1.4 is vital since the jquery UI
///does not work below a version of 1.4. If this is not set, chisimba will
/// set the version to 1.3.2 by default.
        $this->setVar('SUPPRESS_JQUERY', TRUE);
        return 'home.php';
    }

    /*!
     * \brief This action takes you to the create new form modal window of this module.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     * \note A custom template file is being set that suppresses
     * chisimba head.
     *  \return A template file \ref add_edit_form_parameters.php
     */

    private function __addFormParameters() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_form_parameters.php";
    }

    /*!
     * \brief This action takes you to the update form metadata modal window of this module.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     * \note A custom template file is being set that suppresses
     * chisimba head.
     *  \return A template file \ref add_edit_form_parameters.php
     */

    private function __editFormParameters() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_form_parameters.php";
    }

    /*!
     * \brief This action takes the data supplied in the update form metadata
     * modal window of this module and updates it in the
     * database
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     *  \return A another action listAllForms
     */

    private function __updateExistingFormParameters() {
///Get all the data supplied in the modal window and store them in
///temporary variables
        $currentformNumber = $this->getParam('currentformNumber');
        $formLabel = $this->getParam('formLabel', NULL);
        $formEmail = $this->getParam('formEmail', NULL);
        $submissionOption = $this->getParam('formSubmissionRadio', NULL);
        $formDescription = $this->getParam('formCaption', NULL);

///Instatiate an object of the \ref dbformbuilder_form_list class.
///This class acts as an interface to the form_list database that
///stores all the form metadata
        $objDBUpdateFormParameters = $this->getObject('dbformbuilder_form_list', 'formbuilder');
///Update the form metadata
        $objDBUpdateFormParameters->updateSingle($currentformNumber, $formLabel, $formDescription, $formEmail, $submissionOption);
        $this->setVar('JQUERY_VERSION', '1.4.2');
///retrun another action
        return $this->nextAction("listAllForms");
    }

    /*!
     * \brief This action takes the data supplied in the add form metadata
     * modal window of this module and adds a new entry in the
     * database
     * \note A custom template file is being set that suppresses
     * chisimba head.
     *  \return A template file add_new_form_parameters.php
     */

    private function __addNewFormParameters() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_new_form_parameters.php";
    }

    /*!
     * \brief This action builds a correctly designed form for user
     * submission
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     *  \return A template file construct_current_form.php
     */

    private function __buildCurrentForm() {
        $this->setVar('SUPPRESS_JQUERY', TRUE);
        $formNumber = $this->getParam("formNumber");
        $this->setVar('formNumber', $formNumber);
        return "construct_current_form.php";
    }

    /*!
     * \brief This action builds a designed form for the desinger to
     * preview
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     * \note A custom template file is being set that suppresses
     * chisimba head.
     *  \return A template file construct_a_read_only_form.php
     */

    private function __buildAReadOnlyForm() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "construct_a_read_only_form.php";
    }

    /*!
     * \brief This action provides all the possible form options
     * to be placed in a modal window.
     * \note This action is called via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     * \note A custom template file is being set that suppresses
     * chisimba head.
     *  \return A template file list_current_form_options.php
     */

    private function __listCurrentFormOptions() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "list_current_form_options.php";
    }

    /*!
     * \brief This action provides the content that displays the Current Form
     * General and Publishing Details to be placed in a modal window.
     * \note This action is called via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file list_current_form_general_and_publish_details.php
     */

    private function __listCurrentFormGeneralandPublishingDetails() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "list_current_form_general_and_publish_details.php";
    }

    /*!
     * \brief This action provides the content that displays the Current Form
     * Publishing Details to be placed in a modal window.
     * \note This action is called via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file list_current_form_publishing_data.php
     */

    private function __listCurrentFormPublishingData() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "list_current_form_publishing_data.php";
    }

    /*!
     * \brief This action takes the content provided in the publishing data modal
     * window and adds or updates the entry in the
     * database depending on whether publishing data actually
     * exists.

     * \note This action is called via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_form_publishing_data.php
     */

    private function __addEditFormPublishingData() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_form_publishing_data.php";
    }

    /*!
     * \brief This action lists all the designed forms and their possible options.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     *  \return A template file list_all_forms.php
     */

    private function __listAllForms() {
        $this->setVar('SUPPRESS_JQUERY', TRUE);
        return "list_all_forms.php";
    }

    /*!
     * \brief This action goes to the main module help section.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     *  \return A template file help_main.php
     */

    private function __moduleHelp() {
        $this->setVar('SUPPRESS_JQUERY', TRUE);
        return "help_main.php";
    }
    
       /*!
     * \brief This action goes to the style settings section. It allows you to
     * set a style from a list of predefined styles.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     *  \return A template file style_settings_main.php
     */

    private function __styleSettings() {
        $this->setVar('SUPPRESS_JQUERY', TRUE);
        return "style_settings_main.php";
    }

    /*!
     * \brief This action takes a string for the search bar and search all the
     * forms with that string and lists all the forms with
     * the particular string.
     * \note All form metadata is searched. Not the actual form
     * content.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     *  \return A template file list_all_forms.php
     */

    private function __searchAllForms() {
        $this->setVar('SUPPRESS_JQUERY', TRUE);
        return "list_all_forms.php";
    }

    /*!
     * \brief This action takes an option from a radio button that allows desingers
     * to view a set number of forms per page and lists that
     * certain number per page.
     * \brief This action also gets called when designers want
     * to view the next or previous set of forms.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     *  \return A template file list_all_forms.php
     */

    private function __listAllFormsPaginated() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "list_all_forms_paginated.php";
    }

    /*!
     * \brief This action takes you to the WYSIWYG form editor
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work
     *  \return A template file form_editor.php
     */

    private function __designWYSIWYGForm() {
        $this->setVar('SUPPRESS_JQUERY', TRUE);
        return 'form_editor.php';
    }

    /*!
     * \brief This action takes the form element order and updates it in the
     * database.
     * \note This action gets called via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file update_form_element_order.php
     */

    private function __updateWYSIWYGFormElementOrder() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "update_form_element_order.php";
    }

    /*!
     * \brief This action deletes ALL form content of a particular chosen form.
     * \note This action gets called via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file delete_form.php
     */

    private function __deleteForm() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "delete_form.php";
    }

    /*!
     * \brief This action deletes a particular form element inside the database.
     * \note This action gets called via AJAX through
     * functions beloginging to the WYSIWYG form
     * editor.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file delete_form_element.php
     */

    private function __deleteWYSIWYGFormElement() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "delete_form_element.php";
    }

    /*!
     * \brief This action deletes ALL form submissions of a particular chosen form.
     * \note This action gets called via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file delete_form_submissions.php
     */

    private function __deleteAllFormSubmissions() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "delete_form_submissions.php";
    }

    /*!
     * \brief This action takes you to the create new form modal window that
     * provides fields that the designer has to supply for the form
     * metadata.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file create_new_form_element.php
     */

    private function __createNewFormElement() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "create_new_form_element.php";
    }

    /*!
     * \brief This action inserts a new form element indentifier into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file insert_form_element.php
     */

    private function __insertFormElement() {
        $this->setPageTemplate('ajax_template.php');
        return "insert_form_element.php";
    }
    
    /*!
     * \brief This action inserts a edit form element indentifier into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file edit_form_element.php
     */
    private function __editFormElement(){
        $this->setPageTemplate('ajax_template.php');
        return "edit_form_element.php";
    }

    /*!
     * \brief This action inserts a new radio into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_radio_entity.php
     */

    private function __addEditRadioEntity() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_radio_entity.php";
    }

    /*!
     * \brief This action inserts a new cehckbox into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_checkbox_entity.php
     */

    private function __addEditCheckboxEntity() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_checkbox_entity.php";
    }

    /*!
     * \brief This action inserts a new dropdown into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_dropdown_entity.php
     */

    private function __addEditDropdownEntity() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_dropdown_entity.php";
    }

    /*!
     * \brief This action inserts a new label into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_label_entity.php
     */

    private function __addEditLabelEntity() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_label_entity.php";
    }

    /*!
     * \brief This action inserts a new html heading into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_HTMLheading_entity.php
     */

    private function __addEditHTMLHeadingEntity() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_HTMLheading_entity.php";
    }

    /*!
     * \brief This action inserts a new date picker into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_datepicker_entity.php
     */

    private function __addEditDatePickerEntity() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_datepicker_entity.php";
    }

    /*!
     * \brief This action inserts a new text input into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_textinput_entity.php
     */

    private function __addEditTextInput() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_textinput_entity.php";
    }

    /*!
     * \brief This action inserts a new button into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_button_entity.php
     */

    private function __addEditButton() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "add_edit_button_entity.php";
    }

    /*!
     * \brief This action inserts a new text area into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_textarea_entity.php
     */

    private function __addEditTextArea() {
        $this->setPageTemplate('ajax_template.php');
        $this->setVar('JQUERY_VERSION', '1.4.2');
        return "add_edit_textarea_entity.php";
    }

    /*!
     * \brief This action inserts a new mutli selectable drop down into a form through the
     * the WYSIWYG form editor.
     * \note This action is via AJAX
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     * \note A custom template file is being set that suppresses
     * chisimba head and only spit out the actual content.
     *  \return A template file add_edit_multiselect_dropdown_entity.php
     */

    private function __addEditMultiSelectableDropdownEntity() {
        $this->setPageTemplate('ajax_template.php');
        $this->setVar('JQUERY_VERSION', '1.4.2');
        return "add_edit_multiselect_dropdown_entity.php";
    }

    /*!
     * \brief This allows desingers to edit previous designed forms in the
     * WYSIWYG form editor interface.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     *  \return A template file form_editor.php
     */

    private function __editWYSIWYGForm() {
        $this->setVar('SUPPRESS_JQUERY', TRUE);
        return "form_editor.php";

///If developers want to revert to the old WYSIWYG form editor, just uncomment
///the line that returns the template file form_element_editor.php
//  return "form_element_editor.php";
    }

    /*!
     * \brief This action takes the data supplied by the users of the form 
     * and save it in the database.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     *  \return A another action either formSuccessfulSubmissionIntoDataBase or
     * and an error dialog box.
     */

    private function __saveSubmittedFormDataInDatabase() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $formNumber = $this->getParam('formNumber');

///Get a list of all the form element names and their types from a hidden
///input and store it into a variable.
        $formElementNameList = $this->getParam('formElementNameList');
        $formElementTypeList = $this->getParam('formElementTypeList');
        $formElementValuesArray = array();

///Get the current submit number of the user
        $objDBFormSubmitResults = $this->getObject('dbformbuilder_submit_results', 'formbuilder');
        $submitNumber = $objDBFormSubmitResults->getNextSubmitNumber($formNumber);

///convert a string seperated by commas of all the form element names
///and their types into two arrays.
        $formElementNameArray = explode(",", $formElementNameList);
        $formElementTypeArray = explode(",", $formElementTypeList);

///Get the number for form element types and names
        $lengthOfFormElementNameArray = count($formElementNameArray);
        $lengthOfFormElementTypeArray = count($formElementTypeArray);


        if ($lengthOfFormElementNameArray == $lengthOfFormElementTypeArray) {
            for ($i = 0; $i <= ($lengthOfFormElementNameArray - 1); $i++) {

                $formElementType = $formElementTypeArray[$i];
                $formElementName = $formElementNameArray[$i];
///Loop through all the form elements by name and get the values
///submited by the user of the form and store them into temporary
///array
                $formElementValue = $this->getParam($formElementName);
                switch ($formElementType) {
///According to the form element type, the form element values
///get saved into the database differently.
                    case 'checkbox';
                        if ($formElementValue == NULL) {
                            $formElementValue = "off";
                        }
                        $formElementValuesArray[] = $formElementValue;
//$objDBFormSubmitResults->insertSingle($formNumber,$submitNumber, $formElementType, $formElementName,$formElementValue);
                        break;
                    case 'datepicker';
                    case 'dropdown';
                    case 'multiselectable_dropdown';
                    case 'radio';
                    case 'text_area';
                    case 'text_input';
                        if ($formElementValue == NULL) {
///If the user has not filled a form field, produce a
///chisimba dialog box with an error and return back to the current
//form
                            $this->setErrorMessage("The " . $formElementType . " named " . $formElementName . " cannot be NULL. Please complete all form fields.", $formElementName);

                            $this->setVar('formNumber', $formNumber);
                            return "construct_current_form.php";
                        }
                        $formElementValuesArray[] = $formElementValue;

                        break;
                    default;
///If a form element type does not exist then spit out a error dialog box.
                        $this->setErrorMessage("Internal Error. The form element type \"" . $formElementType . "\" is not registered to be stored in a database.", $formElementType);
                        break;
                }
            }

            for ($i = 0; $i <= ($lengthOfFormElementNameArray - 1); $i++) {

                $formElementType = $formElementTypeArray[$i];
                $formElementName = $formElementNameArray[$i];
                $formElementValue = $formElementValuesArray[$i];
///Loop through all the form elements by name and value
///submited by the user of the form and store them into the database
                $objDBFormSubmitResults->insertSingle($formNumber, $submitNumber, $formElementType, $formElementName, $formElementValue);
            }
        } else {
            $this->setErrorMessage("Internal Error. Number of form element types and form element names do not match.");
        }

        $this->setVar('formNumber', $formNumber);
        $this->setVarByRef('test', $test);
///If everything well and good return the next action.
        return $this->nextAction("formSuccessfulSubmissionIntoDataBase", array('formNumber' => $formNumber, 'submitNumber' => $submitNumber));
    }

    /*!
     * \brief This action takes the data supplied by the users of the form
     * and sends it to the designer of the form via email.
     * \note Setting the jquery version greater than 1.4 is
     * vital since the jquery UI does not work.
     *  \return A another action either formSuccessfulSubmissionToEmail or
     * errorSubmissionToEmail depending on successful or failed conditions.
     */

    private function __sendSubmittedFormDataViaEmail() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
///Get a list of all the form element names and their types from a hidden
///input and store it into a variable.
        $formNumber = $this->getParam('formNumber');
        $formLabel = $this->getParam('formLabel');
        $formElementTypeList = $this->getParam('formElementTypeList');
        $formElementNameList = $this->getParam('formElementNameList');
        $formEmail = $this->getParam('formEmail');

///Get the designers details for email.
        $userid = $this->objUser->userId();
        $nameOfSubmitter = $this->objUser->fullname($userid);
        $emailOfSubmitter = $this->objUser->email($userid);

        $objDBFormSubmitResults = $this->getObject('dbformbuilder_form_list', 'formbuilder');
        $submitTime = $objDBFormSubmitResults->getSubmitTime();

///Construct the header email message.
        $emailMessageContent = "<h1>Submission Results of Form Name: <b>" . $formLabel . "</b></h1>";
        $emailMessageContent .= "<b>Name of Person Submitting Form:  </b>" . $nameOfSubmitter . "<br>";
        $emailMessageContent .= "<b>Email Address of Person Submitting Form:  </b>" . $emailOfSubmitter . "<br>";
        $emailMessageContent .= "<b>Time of Submission:  </b>" . $submitTime . "<br>" . "<br>";
        $emailMessageContent .= "<h2>Results</h2>";

///Convert a string to seperated by commas into an array
        $formElementNameArray = explode(",", $formElementNameList);
        $formElementTypeArray = explode(",", $formElementTypeList);

        $lengthOfFormElementNameArray = count($formElementNameArray);
        $lengthOfFormElementTypeArray = count($formElementTypeArray);

        if ($lengthOfFormElementNameArray == $lengthOfFormElementTypeArray) {
///loop through all the form element names and types and store the values of
///supplied by the user to a temporary array
            for ($i = 0; $i <= ($lengthOfFormElementNameArray - 1); $i++) {

                $formElementType = $formElementTypeArray[$i];
                $formElementName = $formElementNameArray[$i];
                $formElementValue = $this->getParam($formElementName);
                switch ($formElementType) {
///According to the form element type, the form element values
///get saved into the email message differently.
                    case 'checkbox';
                        if ($formElementValue == NULL) {
                            $formElementValue = "off";
                        }
                        $formElementValuesArray[] = $formElementValue;
//$objDBFormSubmitResults->insertSingle($formNumber,$submitNumber, $formElementType, $formElementName,$formElementValue);
                        break;
                    case 'datepicker';
                    case 'dropdown';
                    case 'multiselectable_dropdown';
                    case 'radio';
                    case 'text_area';
                    case 'text_input';
                        if ($formElementValue == NULL) {
                            $this->setErrorMessage("The " . $formElementType . " named " . $formElementName . " cannot be NULL. Please complete all form fields.", $formElementName);
                            $this->setVar('formNumber', $formNumber);
                            return "construct_current_form.php";
                        }
                        $formElementValuesArray[] = $formElementValue;
                        break;
                    default;
///If a form element type does not exist then spit out a error dialog box.
                        $this->setErrorMessage("Internal Error. The form element type \"" . $formElementType . "\" is not registered to be stored in a database.", $formElementType);
                        break;
                }
            }
            for ($i = 0; $i <= ($lengthOfFormElementNameArray - 1); $i++) {
//echo  $formElementNameArray[$i]."&nbsp;&nbsp;&nbsp;&nbsp;".$formElementTypeArray[$i]."<br>";
                $formElementType = $formElementTypeArray[$i];
                $formElementName = $formElementNameArray[$i];
                $formElementValue = $formElementValuesArray[$i];
                $emailMessageContent .= "<b>" . $formElementName . " (Form Element Type : " . $formElementType . ") : </b>" . $formElementValue . "<br>";
///Insert all the results from the various arrays into the body of the email mesage.
//$objDBFormSubmitResults->insertSingle($formNumber,$submitNumber, $formElementType, $formElementName,$formElementValue);
            }
// $this->setErrorMessage("Form Successfully Submitted.");
        } else {

            $this->setErrorMessage("Internal Error. Number of form element types and form element names do not match.");
        }


///Set email parameters

        $objMailer = $this->getObject('mailer', 'mail');
        $objMailer->setValue('to', $formEmail);
        $objMailer->setValue('from', 'noreply@formbuilder.wits.ac.za');
        $objMailer->setValue('fromName', 'Wits CCMS Form Builder');
        $objMailer->setValue('subject', "Submission of Form By " . $nameOfSubmitter . " at Time " . $submitTime);
        $objMailer->setValue('body', $emailMessageContent);
//$objMailer->setValue('cc', '');
//$objMailer->setValue(bcc, '');
//$objMailer->attach('/var/www/app/config/config_inc.php', 'config_inc.php');
//$objMailer->attach('/var/www/app/index.php');
///Send the email message
        if ($objMailer->send(true)) {
            $mailSuccess = "Success";
        } else {
            $mailSuccess = "Failiure";
        }
        if ($mailSuccess == "Success") {
///Depending on the success of the email being sent go to the relevant
///next action.
            return $this->nextAction("formSuccessfulSubmissionToEmail", array('formNumber' => $formNumber, 'submitNumber' => $submitNumber, 'mailSuccess' => $mailSuccess));
        } else {
            return $this->nextAction("errorInSubmissionToEmail", array('mailSuccess' => $mailSuccess));
        }
    }

    /*!
     * \brief This action takes the data supplied by the users of the form
     * and sends it to the designer of the form via email and stores it
     * into a database for future viewing.
     * \note Setting the jquery version greater than 1.4 is
     * \note This action is an amalgamtion of the other two
     * seperate submission actions. See them to find out about this
     * action.
     * vital since the jquery UI does not work.
     *  \return A another action either formSuccessfulSubmissionToEmail or
     * errorSubmissionToEmail depending on successful or failed conditions.
     */

    private function __saveandsendSubmittedFormDataInDatabaseandViaEmail() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $formNumber = $this->getParam('formNumber');
        $formLabel = $this->getParam('formLabel');
        $formElementTypeList = $this->getParam('formElementTypeList');
        $formElementNameList = $this->getParam('formElementNameList');
        $formEmail = $this->getParam('formEmail');
        $userid = $this->objUser->userId();
        $nameOfSubmitter = $this->objUser->fullname($userid);
        $emailOfSubmitter = $this->objUser->email($userid);

        $objDBFormSubmitResults = $this->getObject('dbformbuilder_submit_results', 'formbuilder');
        $submitNumber = $objDBFormSubmitResults->getNextSubmitNumber($formNumber);

        $objDBFormList = $this->getObject('dbformbuilder_form_list', 'formbuilder');
        $submitTime = $objDBFormList->getSubmitTime();


        $emailMessageContent = "<h1>Submission Results of Form Name: <b>" . $formLabel . "</b></h1>";
        $emailMessageContent .= "<b>Name of Person Submitting Form:  </b>" . $nameOfSubmitter . "<br>";
        $emailMessageContent .= "<b>Email Address of Person Submitting Form:  </b>" . $emailOfSubmitter . "<br>";
        $emailMessageContent .= "<b>Time of Submission:  </b>" . $submitTime . "<br>" . "<br>";
        $emailMessageContent .= "<h2>Results</h2>";


        $formElementNameArray = explode(",", $formElementNameList);
        $formElementTypeArray = explode(",", $formElementTypeList);

        $lengthOfFormElementNameArray = count($formElementNameArray);
        $lengthOfFormElementTypeArray = count($formElementTypeArray);

        if ($lengthOfFormElementNameArray == $lengthOfFormElementTypeArray) {
            for ($i = 0; $i <= ($lengthOfFormElementNameArray - 1); $i++) {
//echo  $formElementNameArray[$i]."&nbsp;&nbsp;&nbsp;&nbsp;".$formElementTypeArray[$i]."<br>";
                $formElementType = $formElementTypeArray[$i];
                $formElementName = $formElementNameArray[$i];
                $formElementValue = $this->getParam($formElementName);
                switch ($formElementType) {
                    case 'checkbox';
                        if ($formElementValue == NULL) {
                            $formElementValue = "off";
                        }
                        $formElementValuesArray[] = $formElementValue;
//$objDBFormSubmitResults->insertSingle($formNumber,$submitNumber, $formElementType, $formElementName,$formElementValue);
                        break;
                    case 'datepicker';
                    case 'dropdown';
                    case 'multiselectable_dropdown';
                    case 'radio';
                    case 'text_area';
                    case 'text_input';
                        if ($formElementValue == NULL) {
                            $this->setErrorMessage("The " . $formElementType . " named " . $formElementName . " cannot be NULL. Please complete all form fields.", $formElementName);
                            $this->setVar('formNumber', $formNumber);
                            return "construct_current_form.php";
                        }
                        $formElementValuesArray[] = $formElementValue;
                        break;
                    default;
                        $this->setErrorMessage("Internal Error. The form element type \"" . $formElementType . "\" is not registered to be stored in a database.", $formElementType);
                        break;
                }
            }
            for ($i = 0; $i <= ($lengthOfFormElementNameArray - 1); $i++) {
//echo  $formElementNameArray[$i]."&nbsp;&nbsp;&nbsp;&nbsp;".$formElementTypeArray[$i]."<br>";
                $formElementType = $formElementTypeArray[$i];
                $formElementName = $formElementNameArray[$i];
                $formElementValue = $formElementValuesArray[$i];
                $emailMessageContent .= "<b>" . $formElementName . " (Form Element Type : " . $formElementType . ") : </b>" . $formElementValue . "<br>";
                $objDBFormSubmitResults->insertSingle($formNumber, $submitNumber, $formElementType, $formElementName, $formElementValue);
            }
// $this->setErrorMessage("Form Successfully Submitted.");
        } else {
            $this->setErrorMessage("Internal Error. Number of form element types and form element names do not match.");
        }




        $objMailer = $this->getObject('mailer', 'mail');
        $objMailer->setValue('to', $formEmail);
        $objMailer->setValue('from', 'noreply@formbuilder.wits.ac.za');
        $objMailer->setValue('fromName', 'Wits CCMS Form Builder');
        $objMailer->setValue('subject', "Submission of Form By " . $nameOfSubmitter . " at Time " . $submitTime);
        $objMailer->setValue('body', $emailMessageContent);
//$objMailer->setValue('cc', '');
//$objMailer->setValue(bcc, '');
//$objMailer->attach('/var/www/app/config/config_inc.php', 'config_inc.php');
//$objMailer->attach('/var/www/app/index.php');
        if ($objMailer->send(true)) {
            $mailSuccess = "Success";
        } else {
            $mailSuccess = "Failiure";
        }

        if ($mailSuccess == "Success") {
            return $this->nextAction("formSuccessfulSubmissionToEmailandDatabase", array('formNumber' => $formNumber, 'submitNumber' => $submitNumber, 'mailSuccess' => $mailSuccess));
        } else {
            return $this->nextAction("errorInSubmissionToEmailandDatabase", array('mailSuccess' => $mailSuccess));
        }
    }

    /*!
     * \brief This action is called when there is a successful submission to
     * the database. It also preforms the actions the designer
     * specifies after each submission.
     *  \return A template file form_successful_submission.php
     */

    private function __formSuccessfulSubmissionIntoDataBase() {
        $formNumber = $this->getParam('formNumber');
        $submitNumber = $this->getParam('submitNumber');

        $this->setVar('formNumber', $formNumber);
        $this->setVar('submitNumber', $submitNumber);

        return "form_successful_submission.php";
    }

    /*!
     * \brief This action is called when there is a successful submission to
     * via email. It also preforms the actions the designer
     * specifies in the publishing setting after each submission.
     *  \return A template file form_successful_submission.php
     */

    private function __formSuccessfulSubmissionToEmail() {
        $mailSuccess = $this->getParam('mailSuccess');
        $formNumber = $this->getParam('formNumber');
        $submitNumber = $this->getParam('submitNumber');
        $this->setVar('formNumber', $formNumber);
        $this->setVar('submitNumber', $submitNumber);
        $this->setVar('mailSuccess', $mailSuccess);
        return "form_successful_submission.php";
    }

    /*!
     * \brief This action is called when the form builder next to
     * divert to another URL specified
     * the designer after a succesful submission.
     * \note This action is called VIA ajax from the
     * javascript in the template file  form_successful_submission.php
     *  \return A template file form_successful_submission_diverter.php
     */

    private function __successfulSubmissionDivertToNextAction() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "form_successful_submission_diverter.php";
    }

    /*!
     * \brief This action is called when there is a error in submission to
     * via email. It displays an error message with a certain error.
     *  \return A template file form_successful_submission.php
     */

    private function __errorInSubmissionToEmail() {
        $mailSuccess = $this->getParam('mailSuccess');
        $this->setVar('mailSuccess', $mailSuccess);
        return "form_successful_submission.php";
    }

    /*!
     * \brief This action is called when there is a successsful submission to
     * the database and the results are sent via email. It also preforms the actions the designer
     * specifies after each submission.
     *  \return A template file form_successful_submission.php
     */

    private function __formSuccessfulSubmissionToEmailandDatabase() {
        $mailandDatabaseSuccess = $this->getParam('mailSuccess');
        $formNumber = $this->getParam('formNumber');
        $submitNumber = $this->getParam('submitNumber');
        $this->setVar('formNumber', $formNumber);
        $this->setVar('submitNumber', $submitNumber);
        $this->setVar('mailandDatabaseSuccess', $mailandDatabaseSuccess);
        return "form_successful_submission.php";
    }

    /*!
     * \brief This action is called when there is a error in submission to
     * via email and database. It displays an error message with a certain error.
     *  \return A template file form_successful_submission.php
     */

    private function __errorInSubmissionToEmailandDatabase() {
        $mailSuccess = $this->getParam('mailSuccess');

        $this->setVar('mailSuccess', $mailSuccess);

        return "form_successful_submission.php";
    }

    /*!
     * \brief This action displays the list of submit results for a particular
     * form.
     *  \return A template file view_submitted_results.php
     */

    private function __viewSubmittedresults() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $formNumber = $this->getParam('formNumber');

        $this->setVar('formNumber', $formNumber);

        return "view_submitted_results.php";
    }

        /*!
     * \brief This action will allow designers to download all form submit results
         * into a CSV format text file.
     *  \return A template file download_submitted_results.php
     */
    private function __downloadCSVSubmitResultsFile()
    {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $formNumber = $this->getParam('formNumber');
        $this->setVar('formNumber', $formNumber);
        $this->setPageTemplate('ajax_template.php');
        return "download_submitted_results.php";
    }

    /*!
     * \brief This action displays a selected submit result for a list of sumbit
     * results.
     * \note This action is called via AJAX through
     * the javascript in the view_submitted_results.php
     * template file.
     *  \return A template file view_selected_submit_result.php
     */

    private function __viewSubmitNumberResult() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        $submitNumber = $this->getParam('submitNumber');

        $this->setVar('submitNumber', $submitNumber);
        return "view_selected_submit_result.php";
    }

    /*!
     * \brief This action will load more submit results as the user scrolls down
     * the page.
     * \note This action is called via AJAX through
     * the javascript in the view_submitted_results.php
     * template file.
     *  \return A template file view_submitted_results_paginated.php
     */

    private function __getMorePaginatedSubmitResults() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
//            $paginationRequestNumber = $this->getParam('paginationRequestNumber');
//
//$this->setVar('paginationRequestNumber', $paginationRequestNumber);
        return "view_submitted_results_paginated.php";
    }

    /*!
     * \brief This action will load a specific cotent within the entire help
     * \note This action is called via AJAX
     *  \return A template file get_user_help_content.php with the neccessary help
     * content.
     */

    private function __getHelpContent() {
        $this->setVar('JQUERY_VERSION', '1.4.2');
        $this->setPageTemplate('ajax_template.php');
        return "get_user_help_content.php";
    }
    
    /*!
     * \brief This action can allow you to view a selected style without setting it.
     * \note This action is called via AJAX
     * \return A template file that includes the relevant css in the header.
     */
    private function __viewStyle(){
        $this->setPageTemplate('ajax_template.php');
                return "view_style.php";
        
    }
    
    /*!
     * \brief This action can allow you to set the style by modifying the xml config file.
     * \note This action is called via AJAX
     * \return A template file that includes the relevant css in the header.
     */
    private function __setStyle(){
         $this->setPageTemplate('ajax_template.php');
         return "set_style.php";
    }
    
    /*!
     * \brief This action can allow you to update parameters of each form element option of any
     * form element type.
     * \note This action is called via AJAX
     * \return A template file that includes the relevant css in the header.
     */
    private function __updateFormElementOption(){
         $this->setPageTemplate('ajax_template.php');
         return "updateFormElementOption.php";
    }
    
    
    /*!
     * \brief This action can allow you to delete a form element option of any
     * form element type.
     * \note This action is called via AJAX
     * \return A template file that includes the relevant css in the header.
     */
    private function __deleteFormElementOption(){
         $this->setPageTemplate('ajax_template.php');
         return "deleteFormElementOption.php";
    }

}
?>
