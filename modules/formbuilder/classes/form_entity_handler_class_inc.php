<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_entity_handler
 *
 *  \brief This class is the parent class of the form entity hierarchy.
 * \brief This class creates form element identifiers, delete exisiting form
 * elements, delete entire forms, update form element orders, construct forms for
 * submission and the WYSIWYG form editor. It also has generic form element inserter
 * forms to be used in the child classes to build their insert forms. It also contains
 * other main functions that most of the classes use to build their respective
 * form elements.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
class form_entity_handler extends object {

    /*!
     * \brief Private data member from the class \ref dbformbuilder_form_elements that stores all
     * the which form elements go in which form in which order.
     * \note This object is used to get and insert all of the metadata of form elements and the
     * form element identifiers for form elements used.
     */
    protected $objDBFormElements;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_form_list that stores all
     * the properties of this class in an usable object.
     * \note This object is used to get all of the metadata of forms to display in the form
     * list or to use in publishing forms.
     */
    protected $objDBFormMetaDataList;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_publishing_options that stores all
     * the properties of this class in an usable object.
     * \note This object is used to get and save all of the publishing data of forms.
     */
    protected $objDBFormPublishingOptions;

    /*!
     * \brief This is composed of a child class \ref form_entity_checkbox
     */
    private $checkboxConstructor;

    /*!
     * \brief This is composed of a child class \ref form_entity_button
     */
    private $buttonConstructor;

    /*!
     * \brief This is composed of a child class \ref form_entity_datepicker
     */
    private $datePickerConstructor;

    /*!
     * \brief This is composed of a child class \ref form_entity_htmlheading
     */
    private $HTMLHeadingConstructor;

    /*!
     * \brief This is composed of a child class \ref form_entity_label
     */
    private $labelConstructor;

    /*!
     * \brief This is composed of a child class \ref form_entity_radio
     */
    private $radioConstructor;

    /*!
     * \brief This is composed of a child class \ref form_entity_textinput
     */
    private $textinputConstructor;

    /*!
     * \brief This is composed of a child class \ref form_entity_textarea
     */
    private $textareaConstructor;

    /*!
     * \brief This is composed of a child class \ref form_entity_dropdown
     */
    private $dropdownConstructor;

    /*!
     * \brief This is composed of a child class \ref form_entity_multiselect_dropdown
     */
    private $multiselectDropDownConstructor;

    /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The form class is from the htmlelements module
     * inside the chisimba core modules.
     */
    public function init() {
        $this->objDBFormElements = $this->getObject('dbformbuilder_form_elements', 'formbuilder');
        $this->objDBFormMetaDataList = $this->getObject('dbformbuilder_form_list', 'formbuilder');
        $this->objDBFormPublishingOptions = $this->getObject('dbformbuilder_publish_options', 'formbuilder');
        $this->loadClass('form', 'htmlelements');

        $this->checkboxConstructor = $this->getObject('form_entity_checkbox', 'formbuilder');
        $this->buttonConstructor = $this->getObject('form_entity_button', 'formbuilder');
        $this->datePickerConstructor = $this->getObject('form_entity_datepicker', 'formbuilder');
        $this->HTMLHeadingConstructor = $this->getObject('form_entity_htmlheading', 'formbuilder');
        $this->labelConstructor = $this->getObject('form_entity_label', 'formbuilder');
        $this->radioConstructor = $this->getObject('form_entity_radio', 'formbuilder');
        $this->textinputConstructor = $this->getObject('form_entity_textinput', 'formbuilder');
        $this->textareaConstructor = $this->getObject('form_entity_textarea', 'formbuilder');
        $this->dropdownConstructor = $this->getObject('form_entity_dropdown', 'formbuilder');
        $this->multiselectDropDownConstructor = $this->getObject('form_entity_multiselect_dropdown', 'formbuilder');
    }

    /*!
     * \brief This member function inserts the metadata for each element inside the
     * form element list database.
     * \note The form element metadata includes what type form element
     * it is. What the form element name or identifier it is. Which form
     * it belongs to in which order it comes in.
     * \return A boolean for successful insertion of the form element
     * metadata.
     */
    public function insertNewFormElement($formNumber, $formName, $formElementType, $formElementName) {

        if ($this->objDBFormElements->checkDuplicateFormElementName($formElementName, $formName)) {

            $this->objDBFormElements->insertSingle($formNumber, $formName, $formElementType, $formElementName);
            $postSuccess = 1;
            return $postSuccess;
         //               }
        } else {
            $postSuccess = 0;
            return $postSuccess;

        }
    }
    /*!
     * \brief This member function gets all the form element metadata entries
     * for one type of form element belong to one form with a form number.
     * \param formElementType A string.
     * \param formElementName A string.
     * \return A multi-dimensional array will all the revelant form element
     * metadata entries.
     */
    protected function getFormElementIdentifierArray($formElementType, $formElementName)
    {
     return  $formNumber= $this->objDBFormElements->getFormNumber($formElementName);
        return $formElementArrayForThisForm =  $this->objDBFormElements->listFormElementsTypeForForm($formNumber,$formElementType);
    }

    /*!
     * \brief This member function updates the order of the form elements belonging to
     * to a form with a form number in the form element metadata
     * database.
     * \param formElementOrderArray A single dimensional array with
     * the form element indentifier in the wanted order.
     * \param formNumber An integer.Make sure you are inserting the
     * right form number as a wrong entry will produce wrong and desasterous results and
     * will try to update form elements of another form.
     * \note The form element metadata includes what type form element
     * it is. What the form element name or identifier it is. Which form
     * it belongs to in which order it comes in.
     */
    public function updateExistingFormElementOrder($formElementOrderArray, $formNumber) {
        $formOrder = 1;
        foreach ($formElementOrderArray as $formElementName) {

            $this->objDBFormElements->updatFormElementOrder($formNumber, $formElementName, $formOrder);
            $formOrder++;
        }
    }

    /*!
     * \brief This member function deletes all the form contents.
     * \note This member function deletes all the publishing data,
     * form metadata, form element metadata, individual form element
     * data ie Everything.
     * \param formNumber An integer.Make sure you are inserting the
     * right form number as a wrong entry will produce wrong and desasterous results and
     * will try to delete the wrong form.
     * \return A boolean for successful deletion.
     */
    public function deleteForm($formNumber) {
        $formElements = $this->objDBFormElements->listFormElementsForForm($formNumber);
        $formName = $formElements["0"]['formname'];
        $formMetaDataList = $this->objDBFormMetaDataList->getFormMetaData($formNumber);

        $formMetaName = $formMetaDataList["0"]['name'];

        if ($formName == NULL) {
            return 2;
        }
        if ($formName != $formMetaName) {
            return 2;
        }

        foreach ($formElements as $formElement) {
//Store the values of the array in variables
// $formElementType = $formElement["formelementtpye"];
            $formElementName = $formElement["formelementname"];
            $formElementDeleteSuccess = $this->deleteExisitngFormElement($formElementName, $formNumber);
            if ($formElementDeleteSuccess == 3) {
// return 3;
            }
        }
        $this->objDBFormMetaDataList->deleteSingle($formNumber);
        $this->objDBFormPublishingOptions->deleteSingle($formNumber);
        return true;
    }

    /*!
     * \brief This member function deletes an existing form element.
     * \param formElementName A string containing the form
     * element identifier.
     * \param formNumber An intger that contains in which form
     * the form element exists.
     * \note This member function calls other child member functions
     * to delete form elements.
     * \return An integer. 1 for successful deletion. 2 for a
     * form element type that does not exist. 3 for a form
     * element identifier that does not exist. 0 for a form
     * element that has a form element identifier but it
     * is empty.
     */
    public function deleteExisitngFormElement($formElementName, $formNumber) {
        $formElementType = $this->objDBFormElements->deleteFormElement($formElementName, $formNumber);

        if ($formElementType == false) {
            $deleteSuccess = 3;
        } else {

            switch ($formElementType) {

                case 'radio':
                    $deleteSuccess = $this->radioConstructor->deleteRadioEntity($formNumber,$formElementName);
                    break;

                case 'checkbox':
                    $deleteSuccess = $this->checkboxConstructor->deleteCheckBoxEntity($formNumber,$formElementName);
                    break;

                case 'dropdown':
                    $deleteSuccess = $this->dropdownConstructor->deleteDropDownEntity($formNumber,$formElementName);
                    break;
                case 'label':
                    $deleteSuccess = $this->labelConstructor->deleteLabelEntity($formNumber,$formElementName);
                    break;

                case 'HTML_heading':
                    $deleteSuccess = $this->HTMLHeadingConstructor->deleteHTMLHeadingEntity($formNumber,$formElementName);
                    break;
                case 'datepicker':
                    $deleteSuccess = $this->datePickerConstructor->deleteDatePickerEntity($formNumber,$formElementName);
                    break;
                case 'text_input':
                    $deleteSuccess = $this->textinputConstructor->deleteTextInputEntity($formNumber,$formElementName);
                    break;
                case 'text_area':
                    $deleteSuccess = $this->textareaConstructor->deleteTextAreaEntity($formNumber,$formElementName);
                    break;
                case 'button':
                    $deleteSuccess = $this->buttonConstructor->deleteButtonEntity($formNumber,$formElementName);
                    break;
                case 'multiselectable_dropdown':
                    $deleteSuccess = $this->multiselectDropDownConstructor->deleteMultiSelectDropDownEntity($formNumber,$formElementName);
                    break;
                default:
                    $deleteSuccess = 2;
                    break;
            }
        }
        return $deleteSuccess;
    }
    
    

    /*!
     * \brief This member function contructs a form with all the form elements for
     * the WYSIWYG form editor.
     * \note This member function cannot be used for
     * submitting a form.
     * \param formNumber Make sure to put in the right form
     * number as the worng one will construct a wrong
     * form.
     * \note This member function calls other child member functions
     * to construct WYSIWYG form elements.
     * \return A constructed WYSIWYG form.
     */
    public function buildWYSIWYGForm($formNumber) {


        $formElements = $this->objDBFormElements->listFormElementsForForm($formNumber);
        $WYSIWYGFormUnderConstruction = "";
        foreach ($formElements as $formElement) {

            $formElementType = $formElement["formelementtpye"];
            $formElementName = $formElement["formelementname"];

            
            switch ($formElementType) {

                case 'radio':
                    $constructedRadio = $this->radioConstructor->constructRadioEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .="<div id =$formElementName class='witsCCMSFormElementRadio'>$constructedRadio</div>";
                    break;
                case 'checkbox':
                    $constructedCheckBox = $this->checkboxConstructor->constructCheckBoxEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .="<div id =$formElementName class='witsCCMSFormElementCheckBox'>$constructedCheckBox</div>";
                    break;

                case 'dropdown':
                    $constructedDropdown = $this->dropdownConstructor->constructDropDownEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .="<div id =$formElementName class='witsCCMSFormElementDropDown'>$constructedDropdown</div>";
                    break;
                case 'label':
                    $constructedLabel = $this->labelConstructor->constructLabelEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .="<div id =$formElementName class='witsCCMSFormElementLabel'>$constructedLabel</div>";
                    break;
                case 'HTML_heading':
                    $constructedHTMLHeading = $this->HTMLHeadingConstructor->constructHTMLHeadingEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .= "<div id =$formElementName class='witsCCMSFormElementHTMLHeading'>$constructedHTMLHeading</div>";
                    break;
                case 'datepicker':
//                    $constructedDatePicker = $this->datePickerConstructor->constructDatePickerEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .="<div id =$formElementName class='witsCCMSFormElementDatePicker'><br>[JavaScript Conflict: Date Picker Object can not be displayed.
It \"will\" be displayed in the built form.]<br></div>";
                    break;
                case 'text_input':
                    $constructedTextInput = $this->textinputConstructor->constructTextInputEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .="<div id =$formElementName class='witsCCMSFormElementTextInput'>$constructedTextInput</div>";
                    break;
                case 'text_area':
                    $constructedTextArea = $this->textareaConstructor->constructTextAreaEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .="<div id =$formElementName class='witsCCMSFormElementTextArea'>$constructedTextArea</div>";
                    break;
                case 'button':
                    $constructedButton = $this->buttonConstructor->constructButtonEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .="<div id =$formElementName class='witsCCMSFormElementButton'>$constructedButton</div>";
                    break;
                case 'multiselectable_dropdown':
                    $constructedMultiSelectDropDown = $this->multiselectDropDownConstructor->constructMultiSelectDropDownEntity($formElementName,$formNumber);
                    $WYSIWYGFormUnderConstruction .="<div id =$formElementName class='witsCCMSFormElementMultiSelectDropDown'>$constructedMultiSelectDropDown</div>";
                    break;

                default:
                    $unkwownFormElement = "<br>[Form Parse Error." . $formElementType . " is an unknown form element]<br>";
                    $WYSIWYGFormUnderConstruction .=$unkwownFormElement;
                    break;
            }
        }
        return $WYSIWYGFormUnderConstruction;
    }
    
    public function getFormName($formNumber){
       $formElements = $this->objDBFormElements->listFormElementsForForm($formNumber);
       return $formElements["0"]['formname'];
    }

    /*!
     * \brief This member function contructs a form with all the form elements for
     * the user of the form for actual submission.
     * \param formNumber Make sure to put in the right form
     * number as the worng one will construct a wrong
     * form.
     * \note This member function calls other child member functions
     * to construct form elements.
     * \note This function also adds five hidden
     * inputs into the form. The form number, form
     * name, email submit address, an array of form
     * element names and form element types. This
     * is used as request parameters once a form is
     * posted.
     * \return A constructed form ready for submission.
     */
    public function buildForm($formNumber) {


        $formPublishingDataArray = $this->objDBFormPublishingOptions->getFormPublishingData($formNumber);


        $formElements = $this->objDBFormElements->listFormElementsForForm($formNumber);
        $formName = $formElements["0"]['formname'];
        $formMetaDataList = $this->objDBFormMetaDataList->getFormMetaData($formNumber);

        $formMetaName = $formMetaDataList["0"]['name'];
        $formMetaTitle = $formMetaDataList["0"]['label'];
        $formMetaAuthorName = $formMetaDataList["0"]['author'];
        $formMetaEmail = $formMetaDataList["0"]['submissionemailaddress'];
        $formMetaSubmissionOption = $formMetaDataList["0"]['submissionoption'];

        if ($formPublishingDataArray["0"]['publishoption'] == NULL) {
            $publishErrorMessage = "<h3>This form is not published. Please contact the form designer to publish this form.</h3>";
            $publishErrorMessage .= "<b>Form Designer's Details:</b><br>";
            $publishErrorMessage .= "Name: " . $this->objDBFormMetaDataList->getFormAuthorsFullName($formMetaAuthorName) . "<br>";
            $publishErrorMessage .= "Email Address: <a href=mailto:" . $formMetaEmail . ">$formMetaEmail</a>";
            return $publishErrorMessage;
        }
        if ($formName == NULL) {
            $alertMessage = "This form is empty and has no form elements. Please complete building this form.<br>&nbsp;&nbsp;&nbsp;";
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('link', 'htmlelements');
            $objButton = new button('returnToFormBuilder');
            $objButton->setValue('Return To Form Builder');

            $mnglink = html_entity_decode($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'listAllForms'
                            )));
            $objButton->setOnClick("parent.location='$mnglink '");

            $mnglink = $objButton->show();

            $alertMessage .= $mnglink;
            return$alertMessage;
        }
        if ($formName != $formMetaName) {
            return "Internal Error. Form Name is not equal to Form Meta Name<br>Meta Form Name is: " .
            $formMetaName . " <br> Form Name is: " . $formName;
        }






        $objForm = new form($formName, $this->getFormAction($formMetaSubmissionOption));




        $formElementNameArray = array();
        $formElementTypeArray = array();
        foreach ($formElements as $formElement) {
//Store the values of the array in variables
//$thisFormNumber = $formElement["formnumber"];
//$FormName = $formElement["formname"];
            $formElementType = $formElement["formelementtpye"];
            $formElementName = $formElement["formelementname"];

            switch ($formElementType) {

                case 'radio':
                    $constructedRadio = $this->radioConstructor->constructRadioEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementRadio'>$constructedRadio</div>");
                    $radioName = $this->radioConstructor->getRadioName($formNumber,$formElementName);
                    $formElementNameArray[] = $radioName;
                    $formElementTypeArray[] = $formElementType;
                    break;
                case 'checkbox':
                    $constructedCheckBox = $this->checkboxConstructor->constructCheckBoxEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementCheckBox'>$constructedCheckBox</div>");
                    $checkBoxNameArray = $this->checkboxConstructor->getCheckboxName($formNumber,$formElementName);

                    $numberOfCheckBoxTypes = count($checkBoxNameArray);
                    $formElementNameArray = array_merge($formElementNameArray, $checkBoxNameArray);
                    for ($i = 0; $i < $numberOfCheckBoxTypes; $i++) {
                        $formElementTypeArray[] = $formElementType;
                    }

//  $formElementNameArray[]=$CheckBoxName;
//                         $formElementTypeArray[]=$formElementType;
                    break;

                case 'dropdown':
                    $constructedDropdown = $this->dropdownConstructor->constructDropDownEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementDropDown'>$constructedDropdown</div>");
                    $dropdownName = $this->dropdownConstructor->getDropdownName($formNumber,$formElementName);

                    $formElementNameArray[] = $dropdownName;
                    $formElementTypeArray[] = $formElementType;
                    break;
                case 'label':
                    $constructedLabel = $this->labelConstructor->constructLabelEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementLabel'>$constructedLabel</div>");
//   $formElementNameArray[]=$formElementName;
//              $formElementTypeArray[]=$formElementType;
                    break;
                case 'HTML_heading':
                    $constructedHTMLHeading = $this->HTMLHeadingConstructor->constructHTMLHeadingEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementHTMLHeading'>$constructedHTMLHeading</div>");
// $formElementNameArray[]=$formElementName;
//   $formElementTypeArray[]=$formElementType;
                    break;
                case 'datepicker':
                    $constructedDatePicker = $this->datePickerConstructor->constructDatePickerEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementDatePicker'>$constructedDatePicker</div>");

                    $datePickerNameArray = $this->datePickerConstructor->getDatePickerName($formNumber,$formElementName);
                    $numberOfDatePickerTypes = count($datePickerNameArray);
                    $formElementNameArray = array_merge($formElementNameArray, $datePickerNameArray);
                    for ($i = 0; $i < $numberOfDatePickerTypes; $i++) {
                        $formElementTypeArray[] = $formElementType;
                    }
//$formElementNameArray[]=$formElementName;
//                            $formElementTypeArray[]=$formElementType;
                    break;
                case 'text_input':
                    $constructedTextInput = $this->textinputConstructor->constructTextInputEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementTextInput'>$constructedTextInput</div>");
                    $textInputNameArray = $this->textinputConstructor->getTextInputName($formNumber,$formElementName);
                    $numberOfTextInputTypes = count($textInputNameArray);
                    $formElementNameArray = array_merge($formElementNameArray, $textInputNameArray);
// $formElementNameArray[]=$formElementName;
                    for ($i = 0; $i < $numberOfTextInputTypes; $i++) {
                        $formElementTypeArray[] = $formElementType;
                    }
                    break;
                case 'text_area':
                    $constructedTextArea = $this->textareaConstructor->constructTextAreaEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementTextArea'>$constructedTextArea</div>");
                    $textAreaNameArray = $this->textareaConstructor->getTextAreaName($formNumber,$formElementName);
                    $numberOfTextAreaTypes = count($textAreaNameArray);
                    $formElementNameArray = array_merge($formElementNameArray, $textAreaNameArray);
// $formElementNameArray[]=$formElementName;

                    for ($i = 0; $i < $numberOfTextAreaTypes; $i++) {
                        $formElementTypeArray[] = $formElementType;
                    }
                    break;
                case 'button':
                    $constructedButton = $this->buttonConstructor->constructButtonEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementButton'>$constructedButton</div>");
//   $formElementNameArray[]=$formElementName;
//                  $formElementTypeArray[]=$formElementType;
                    break;
                case 'multiselectable_dropdown':
                    $constructedMultiSelectDropDown = $this->multiselectDropDownConstructor->constructMultiSelectDropDownEntity($formElementName,$formNumber);
                    $objForm->addToForm("<div id =$formElementName class='witsCCMSFormElementMultiSelectDropDown'>$constructedMultiSelectDropDown</div>");
                    $multiSelectDropDownName = $this->multiselectDropDownConstructor->getMultiSelectDropdownName($formNumber,$formElementName);


                    $formElementNameArray[] = $multiSelectDropDownName;
                    $formElementTypeArray[] = $formElementType;
                    break;

                default:
                    $unkwownFormElement = "<br>[Form Parse Error." . $formElementType . " is an unknown form element]<br>";
                    $objForm->addToForm($unkwownFormElement);
                    break;
// $this->clickedAdd=$this->getParam('clickedadd');
//return "home_tpl.php";
            }
        }
        $this->loadClass('hiddeninput', 'htmlelements');

        $formNumberHiddenInput = new hiddeninput("formNumber", $formNumber);
        $objForm->addToForm($formNumberHiddenInput->show() . "<br />");

        $formLabelHiddenInput = new hiddeninput("formLabel", $formMetaTitle);
        $objForm->addToForm($formLabelHiddenInput->show() . "<br />");

//$arraylist = array('title','test','commenttxt');
        $commaSeparatedFormElementNames = implode(",", $formElementNameArray);
        $formElementNamesHiddenInput = new hiddeninput("formElementNameList", $commaSeparatedFormElementNames);
        $objForm->addToForm($formElementNamesHiddenInput->show() . "<br />");

        $commaSeparatedFormElementTypes = implode(",", $formElementTypeArray);
        $formElementTypesHiddenInput = new hiddeninput("formElementTypeList", $commaSeparatedFormElementTypes);
        $objForm->addToForm($formElementTypesHiddenInput->show() . "<br />");

        $formSubmissionEmailAddress = new hiddeninput("formEmail", $formMetaEmail);
        $objForm->addToForm($formSubmissionEmailAddress->show() . "<br />");

        return $objForm->show();
    }

    /*!
     * \brief This member function is used by the buildFrom member function to
     * ascertain the post actions for each form.
     * \note Three possibilites exist. On submission, either
     * the results are saved in the database or emailed to
     * the designer of the form or both.
     * \param formAction A string that is taken from the
     * form meta data. Three possiblities exist. Either
     * 'save_in_database, send_email or 'both'.
     * \warning Any other post option will result in
     * the form not sumbitting and results in a php
     * error.
     * \return A correct form POST action.
     */
    private function getFormAction($formAction) {


        if ($formAction == 'save_in_database') {

            $formAction = $this->uri(array("action" => "saveSubmittedFormDataInDatabase"), "formbuilder");
        }
        if ($formAction == 'send_email') {

            $formAction = $this->uri(array("action" => "sendSubmittedFormDataViaEmail"), "formbuilder");
        }
        if ($formAction == 'both') {

            $formAction = $this->uri(array("action" => "saveandsendSubmittedFormDataInDatabaseandViaEmail"), "formbuilder");
        }

        return $formAction;
    }

    /*!
     * \brief This member function converts a string that denotes a tpye of spacing
     * and converts it into an actual html space.
     * \param breakSpaceType A string that denotes the break space
     * type.
     * \note This member function is used by child
     * class to construct their respective form
     * elements.
     * \return The actual html break space.
     */
    protected function getBreakSpaceType($breakSpaceType) {
        switch ($breakSpaceType) {
            case "tab":
                return "&nbsp;&nbsp;&nbsp;";
                break;
            case "new line":
                return "<br>";
                break;
            case "normal":
                return "";
                break;
            default :
                return "";
                break;
        }
    }

    /*!
     * \brief This member function creates html content for a form to insert
     * an ID or form element idenfier for a form element.
     * \note This mini form consists of text and a
     * text input.
     * \return A constructed mini form to insert an ID for form elements.
     */
    protected function buildInsertIdForm($formElementType, $value, $textInputWidth) {
        $this->loadClass('textinput', 'htmlelements');
        if ($formElementType == NULL) {
            $insertIdFormUnderConstruction = "Enter an id:<br>";
        } else {
            $insertIdFormUnderConstruction = "Enter an id for your " . $formElementType . ":<br>";
        }

        $idTextInput = new textinput("uniqueFormElementID", $formElementType . rand(1000, 99999) . time(), 'text', $textInputWidth);
        $insertIdFormUnderConstruction .= $idTextInput->show();
        return $insertIdFormUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert
     * an html name for a form element.
     * \note This mini form consists of text and a
     * text input.
     * \return A constructed mini form to insert the html name for the form elements.
     */
    protected function buildInsertFormElementNameForm($formElementType, $textInputWidth,$existingFormElementName) {
        $this->loadClass('textinput', 'htmlelements');
        if ($formElementType == NULL) {
            $insertFormElementNameUnderConstruction = "Enter a form element name:<br>";
        } else {
            $insertFormElementNameUnderConstruction = "Enter a name for your " . $formElementType . ":<br>";
        }
        if (isset($existingFormElementName)){
           $FormElementNameTextInput = new textinput("uniqueFormElementName", $existingFormElementName, 'text', $textInputWidth); 
        }else{
           $FormElementNameTextInput = new textinput("uniqueFormElementName", '', 'text', $textInputWidth); 
        }
        
        $insertFormElementNameUnderConstruction .= $FormElementNameTextInput->show();
        return $insertFormElementNameUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to the break space before
     * a pacticular form element option.
     * \note This mini form consists of text and a
     * radio element.
     * note This form works differently under IE under the jQuery UI and this
     * is taken into account in this member function.
     * \return A constructed mini form to insert the layout for a form element option.
     */
    protected function buildLayoutForm($formElementType, $value, $formElement,$editValue) {
        $this->loadClass('radio', 'htmlelements');
        $objElement = new radio('formElementLayout');
        if ($formElementType == NULL || $this->detectIEBrowser() == TRUE) {
            $layoutUnderConstruction = "Select layout:<br>";
            $objElement->addOption('tab', 'Insert a single space before element <br>
');
            $objElement->addOption('new line', 'Place element in a new line.');
            $objElement->addOption('normal', 'Use normal layout (No Spaces)');
        } else {
            $layoutUnderConstruction = "Select layout for  " . $formElementType . ":<br>";
            $objElement->addOption('tab', 'Insert a space before the ' . $formElementType . " <br>
<img src='packages/formbuilder/resources/images/" . $formElement . "_tab_space.png' alt='single space'>");
            $objElement->addOption('new line', 'Place ' . $formElementType . ' in a new line<br>' .
                    "<img src='packages/formbuilder/resources/images/" . $formElement . "_carriage_space.png' alt='single space'>");
            $objElement->addOption('normal', 'Use normal layout (No Spaces)<br>' .
                    "<img src='packages/formbuilder/resources/images/" . $formElement . "_no_space.png' alt='single space'>");
        }
        if (isset($editValue)){
            $objElement->setSelected($editValue);
        } else {
            $objElement->setSelected('tab');
        }
        
//$objElement->setBreakSpace("<br>");
        $layoutUnderConstruction .= $objElement->show();
        return $layoutUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert default text for a text input
     * or area.
     * \note This mini form consists of text and a
     * text area.
     * \return A constructed mini form to insert default text.
     */
    protected function insertTextForm($formElementType, $textAreaRowSize, $textAreaColumnSize,$editValue) {
        $this->loadClass('textarea', 'htmlelements');
        if ($formElementType == NULL) {
            $layouUnderConstruction = "Enter the desired text:<br>";
        } else if ($formElementType == 'text input' || $formElementType == 'text area') {
            $layouUnderConstruction = "<label id='defaultTextLabel'>Set the default text for " . $formElementType . ":</label<br>";
        } else {
            $layouUnderConstruction = "Enter the desired text for " . $formElementType . ":<br>";
        }
        if (isset($editValue)){
            $desiredTextInput = new textarea("formElementDesiredText", $editValue, $textAreaRowSize, $textAreaColumnSize);
        }else{
          $desiredTextInput = new textarea("formElementDesiredText", null, $textAreaRowSize, $textAreaColumnSize);  
        }
        
        $layouUnderConstruction .= $desiredTextInput->show();
        return $layouUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert the font size
     * of html headings.
     * \note This mini form consists of text and a
     * radio element.
     * \return A constructed mini form to insert font sizes.
     */
    protected function insertFontSizeForm($editSize) {
        $this->loadClass('radio', 'htmlelements');
        $fontSizeUnderConstruction = "Select Font Size:<br>";
        $objElement = new radio('fontSize');
        $objElement->addOption('1', '<font size="+2"><b>Size 1</b></font>');
        $objElement->addOption('2', '<font size="4"><b>Size 2</b></font>');
        $objElement->addOption('3', '<font size="3"><b>Size 3</b></font>');
        $objElement->addOption('4', '<font size="2"><b>Size 4</b></font>');
        $objElement->addOption('5', '<font size="1"><b>Size 5</b></font>');
        $objElement->addOption('6', '<font size="-2"><SUB>Size 6</SUB></font>');
        if (isset($editSize)){
            $objElement->setSelected($editSize);
        }else{
           $objElement->setSelected('3'); 
        }
        
        $objElement->setBreakSpace("<br>");
        $fontSizeUnderConstruction .= $objElement->show();
        return $fontSizeUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to set the text alignment
     * of html headings and labels.
     * \note This mini form consists of text and a
     * radio element.
     * \return A constructed mini form to insert text alignment
     */
    protected function insertTextAlignmentType($editAlignment) {
        $this->loadClass('radio', 'htmlelements');
        $textAlignmentUnderConstruction = "Select Alignment Type:<br>";
        $objElement = new radio('textAlignment');
        $objElement->addOption('left', 'Left Align');
        $objElement->addOption('center', 'Center Align');
        $objElement->addOption('right', 'Right Align');
        if (isset($editAlignment)){
            $objElement->setSelected($editAlignment);
        }else{
         $objElement->setSelected('left');   
        }
        
        $objElement->setBreakSpace("<br>");
        $textAlignmentUnderConstruction .= $objElement->show();
        return $textAlignmentUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert the width of
     * text inputs.
     * \note This mini form consists of text and a
     * text input.
     * \return A constructed mini form to insert widths of form elements.
     */
    protected function insertCharacterSizeForm($editTextInputSize) {
        $characterSizeUnderConstruction = "Set character or field size for text input (between 1-150):&nbsp&nbsp&nbsp";
        $this->loadClass('textinput', 'htmlelements');
        if (isset($editTextInputSize)){
          $characterLengthTextInput = new textinput("textInputLength", $editTextInputSize, 'text', "4");
        }else{
          $characterLengthTextInput = new textinput("textInputLength", "25", 'text', "4");  
        }
        
        $characterSizeUnderConstruction .=$characterLengthTextInput->show();
        return $characterSizeUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert the parameters of
     * text inputs.
     * \note This mini form consists of text, a
     * text input and two radio elements.
     * \return A constructed mini form to insert parameters for text inputs.
     */
    protected function insertTextInputOptionsForm($textAreaRowSize, $textAreaColumnSize,$editTIType,$editValidationType,$editDefaultText) {
        $TIOptionsUnderConstruction = "Select Text Input Type:<br>";
        $this->loadClass('radio', 'htmlelements');
        $objElement = new radio('textOrPasswordRadio');
        $objElement->addOption('text', 'Insert Text');
        $objElement->addOption('password', 'Insert Password');
        if (isset($editTIType)){
            $objElement->setSelected($editTIType);
        } else {
            $objElement->setSelected('text');
        }
        
//  $objElement->setBreakSpace("<br>");
        $TIOptionsUnderConstruction .= $objElement->show() . "<br>";
        $TIOptionsUnderConstruction .="<div id=additionalTextProperties>";
        $TIOptionsUnderConstruction .= "<br>Select and Set Validation Option:<br>";
        $objElement = new radio('maskedInputChoice');
        $objElement->addOption('default', 'No Validation');
        $objElement->addOption('numeric', 'Numeric : Whole Numbers');
        $objElement->addOption('decimal', 'Decimal Numbers : 123.1234');
        $objElement->addOption('alphanumeric', 'Alphanumeric: Alphabets and numbers');
        $objElement->addOption('alpha', 'Alpha: Only alphabets');    
        $objElement->addOption('date_us', 'Date (US Format) : day/month/year');
        $objElement->addOption('date_iso', 'Date (ISO Format) : year-month-day');
        $objElement->addOption('time', 'Time : hour:minute');
        $objElement->addOption('phone', 'Phone Number : 000-000-0000');
        $objElement->addOption('document', 'Document Filenames : file.(pdf,doc,csv,txt)');
        $objElement->addOption('image', 'Image Filenames : image.(jpg,gif,png)');
        $objElement->addOption('ip', 'IP Address : 192.168.0.1');
        $objElement->addOption('html_hex', 'HTML Color Codes : #00ccff');
        $objElement->addOption('email', 'Email Address : salman.noor@wits.ac.za');
        $objElement->addOption('url', 'Internet Address : http://www.google.co.za');
        if (isset($editValidationType)){
          $objElement->setSelected($editValidationType);  
        }else{
          $objElement->setSelected('default');  
        }
        
//$objElement->setBreakSpace("<br>");
        $TIOptionsUnderConstruction .= $objElement->show();

        $TIOptionsUnderConstruction .= "<div id='defaultTextInput'>";
        $TIOptionsUnderConstruction .= "<br>Set the default text for text input:<br>";
        if(isset($editDefaultText)){
           $desiredTextInput = new textarea("formElementDesiredText", $editDefaultText, $textAreaRowSize, $textAreaColumnSize); 
        }else{
           $desiredTextInput = new textarea("formElementDesiredText", '', $textAreaRowSize, $textAreaColumnSize); 
        }
        
        $TIOptionsUnderConstruction .= $desiredTextInput->show();
        $TIOptionsUnderConstruction .= "</div></div>";
        return $TIOptionsUnderConstruction;
    }

    /*!
     * \brief This member function detects whether or not the IE browser is used.
     * \return A boolean, true if the IE browser is being used.
     */
    private function detectIEBrowser() {
        if (isset($_SERVER['HTTP_USER_AGENT']) &&
                (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
            return true;
        else
            return false;
    }

    /*!
     * \brief This member function creates html content for a form to insert the labels
     * of the form elements.
     * \note This mini form consists of text, a
     * text input and a radio element.
     * \return A constructed mini form to insert labels for form elements
     */
    protected function insertFormLabelOptions($formElementType, $radioName,$editLabelParam,$editLabelOrientationParam) {
        $LabelMenuConstruction = "Insert a label or leave this field blank to add no label: <br>";
        if (isset($editLabelParam)){
            $desiredTextInput = new textarea("formElementLabel", $editLabelParam, 1, 68);
        } else {
          $desiredTextInput = new textarea("formElementLabel", '', 1, 68);  
        }
        
        $LabelMenuConstruction .= $desiredTextInput->show() . "<br>";
        $LabelMenuConstruction .= "Label Orientation:<br>";
        $objElement = new radio($radioName);
        $objElement->addOption('left', "Place label on the left.<br><img src='packages/formbuilder/resources/images/" . $formElementType . "_label_left.png' alt='left'>");
        $objElement->addOption("right", "Place label on the right.<br><img src='packages/formbuilder/resources/images/" . $formElementType . "_label_right.png' alt='right'>");
        $objElement->addOption('top', "Place label on the top.<br><img src='packages/formbuilder/resources/images/" . $formElementType . "_label_top.png' alt='right'>");
        $objElement->addOption('bottom', "Insert label at the bottom.<br><img src='packages/formbuilder/resources/images/" . $formElementType . "_label_bottom.png' alt='right'><br>");
        if (isset($editLabelOrientationParam)){
            $objElement->setSelected($editLabelOrientationParam);
        } else {
          $objElement->setSelected('left');  
        }
        
// $objElement->setBreakSpace("<br>");

        if ($this->detectIEBrowser()) {
            $LabelMenuConstruction.= "<input type='radio' id='radio1' name=" . $radioName . " checked='checked' value='left'/><label for='radio1'>Place label on the left.</label><img class='myimg' src='packages/formbuilder/resources/images/" . $formElementType . "_label_left.png' alt='left'><br>
<input type='radio' id='radio2' name=" . $radioName . " value='top'/><label for='radio2'>Place label on top.</label><img class='myimg' src='packages/formbuilder/resources/images/" . $formElementType . "_label_top.png' alt='top'><br>
<input type='radio' id='radio3' name=" . $radioName . "  value='right'/><label for='radio3'>Place label on the right.</label><img class='myimg' src='packages/formbuilder/resources/images/" . $formElementType . "_label_right.png' alt='right'/><br>
<input type='radio' id='radio4' name=" . $radioName . " value='bottom'/><label for='radio4'>Insert label at the bottom.</label><img class='myimg' src='packages/formbuilder/resources/images/" . $formElementType . "_label_bottom.png' alt='bottom'><br>";
        } else {
            $LabelMenuConstruction .=$objElement->show() . "<br>";
        }




        return $LabelMenuConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert the
     * size for text areas.
     * \note This mini form consists of text and two
     * text inputs
     * \return A constructed mini form to insert a size for text areas.
     */
    protected function insertTextAreaSizeParameters($editColumnSize,$editRowSize) {
        $TASizeParametersUnderConstruction = "Set horizontal or column size for text area (between 1-140):  ";
        $this->loadClass('textinput', 'htmlelements');
        if (isset($editColumnSize)){
           $columnLengthTextArea = new textinput("textAreaLength", $editColumnSize, 'text', "4");   
        } else{
          $columnLengthTextArea = new textinput("textAreaLength", "70", 'text', "4");  
        }
        
        $TASizeParametersUnderConstruction .=$columnLengthTextArea->show() . "<br>";
        $TASizeParametersUnderConstruction .="Set vertical or row size for text area (between 1-240):  ";
        if (isset($editRowSize)){
            $rowLengthTextArea = new textinput("textAreaHeight", $editRowSize, 'text', "4");
        }else{
           $rowLengthTextArea = new textinput("textAreaHeight", "10", 'text', "4"); 
        }
        
        $TASizeParametersUnderConstruction .=$rowLengthTextArea->show() . "<br>";
        return $TASizeParametersUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert the type of
     * toolbar chosen for an advanced text area.
     * \note This mini form consists of text and a radio
     * element.
     * \return A constructed mini form to insert the toolbar type on text areas.
     */
    protected function insertToolbarChoiceTextAreaOptions() {
        $TAToolbarChoiceUnderConstruction = "Select Tool Bar for Text Area: <br>";
        $objElement = new radio('toolBarChoice');
        $objElement->addOption('simple', 'Basic Tool Bar<br>
<img src="packages/formbuilder/resources/images/simpletoolbar.png" alt="Basic Tool Bar"><br>');
        $objElement->addOption('DefaultWithoutSave', 'Default Tool Bar Without Save<br>
<img src="packages/formbuilder/resources/images/defaultwithoutsavetoolbar.png" alt="Default Without Save Tool Bar"><br>');
        $objElement->addOption('advanced', 'Advanced Tool Bar<br>
<img src="packages/formbuilder/resources/images/advancedtoolbar.png" alt="Advanced Tool Bar"><br>');
        $objElement->addOption('cms', 'Content Management System (CMS) Tool Bar<br>
<img src="packages/formbuilder/resources/images/cmstoolbar.png" alt="CMS Tool Bar"><br>');
        $objElement->addOption('forms', 'Forms Tool Bar<br>
<img src="packages/formbuilder/resources/images/formstoolbar.png" alt="Forms Tool Bar"><br>');
        $objElement->setSelected('simple');
        $objElement->setBreakSpace("<br>");
        $TAToolbarChoiceUnderConstruction .=$objElement->show() . "<br>";
        return $TAToolbarChoiceUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert the
     * date picker paramters
     * \note This mini form consists of text, a radio
     * element, a dropdown list and text input.
     * \return A constructed mini form to insert the datepicker parameters.
     */
    protected function insertDatePickerFormParameters($ditDateFormat,$editDefaultDate) {
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $DPParametersUnderConstruction = "Please Select Date Format: <br>";

        $possibleDateFomats = array(
            'YYYYMMDD', 'YYYY-MM-DD', 'YYYY-DD-MM',
            'YYYY/MM/DD', 'YYYY/DD/MM', 'YYYY-DD-MON',
            'YYYY-MON-DD', 'MM-DD-YYYY', 'MM/DD/YYYY',
            'MON-DD-YYYY');
        $defaultDateFormatRadio = new dropdown("dateFormat");
        foreach ($possibleDateFomats as $thisDateFormat) {
            $defaultDateFormatRadio->addOption($thisDateFormat, $thisDateFormat);
        }
        if (isset($ditDateFormat)) {
            $defaultDateFormatRadio->setSelected($ditDateFormat);
        } else {
            $defaultDateFormatRadio->setSelected('YYYY-MM-DD');
        }

        $DPParametersUnderConstruction.= $defaultDateFormatRadio->show() . "<br><br>";
        $DPParametersUnderConstruction .="Please Select Choice of a Default Set Date:<br>";
        $defaultDateRadio = new radio("defaultDateChoice");
        $defaultDateRadio->addOption("Real Date", "Set the default selected date to real time");
        $defaultDateRadio->addOption("Custom Date", "Customize the default selected date");
        $defaultDateRadio->setBreakSpace("<br>");
        if (isset($editDefaultDate) && $editDefaultDate != "Real Time Date") {
            $defaultDateRadio->setSelected('Custom Date');
        } else {
            $defaultDateRadio->setSelected('Real Date');
        }

        $DPParametersUnderConstruction .= $defaultDateRadio->show() . "<br>";

        $DPParametersUnderConstruction .= "<div id='selectDefaultDate'>";
        $DPParametersUnderConstruction .="Select a Default Date: ";
        if (isset($editDefaultDate)) {
            $DPParametersUnderConstruction .="<input type='text' name='datepickerInput' id='datepicker' value='$editDefaultDate'>";
        } else {
            $DPParametersUnderConstruction .="<input type='text' name='datepickerInput' id='datepicker'>";
        }

        $DPParametersUnderConstruction .="</div>";
        return $DPParametersUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert the option
     * value and label for each form element option
     * \note This mini form consists of text, two text inputs and
     * a check box.
     * \return A constructed mini form to insert the value and label of a form element option.
     */
    protected function insertOptionAndValueForm($formElementType, $defaultOptionChosenBool) {
        $optionNValueFormUnderConstruction = "Enter a value for your " . $formElementType . " option:<br>";
        $this->loadClass('textinput', 'htmlelements');
        $optionValue = new textinput("optionValue", '', 'text', "70");
        $optionNValueFormUnderConstruction .= $optionValue->show() . "<br>";
        $optionNValueFormUnderConstruction .= "Enter option text for your " . $formElementType . ":<br>";
        $labelValue = new textinput("optionLabel", '', 'text', "70");
        $optionNValueFormUnderConstruction .= $labelValue->show() . "<br>";
        if ($defaultOptionChosenBool == 1) {
            $optionNValueFormUnderConstruction .="Deafult option has been already chosen";
        } else {
            $optionNValueFormUnderConstruction .="<input type='checkbox' id='defaultOptionButton' /><label for='defaultOptionButton'>Set this option selected as default.</label>";
        }
        return $optionNValueFormUnderConstruction;
    }

    /*!
     * \brief This member function creates html content for a form to insert the drop down
     * size.
     * \note This mini form consists of text, a text input and
     * a radio element.
     * \return A constructed mini form to insert the size of a ms drop down.
     */
    protected function insertMSDropDownSizeForm($predefinedSize) {
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $MSDropDownSize = "Multi-Selectable Drop Down Menu Size :<br>";

        $objElement = new radio('menuSize');
        $objElement->addOption('autofit', 'Set menu size to auto-fit all menu values');
        $objElement->addOption('custom', 'Specify Custom Size');
        if (isset($predefinedSize) && $predefinedSize>1){
          $objElement->setSelected('custom');  
        }else{
           $objElement->setSelected('autofit'); 
        }
        
        $objElement->setBreakSpace("<br>");
        $MSDropDownSize .= $objElement->show() . "<br>";
        $MSDropDownSize .="<div id='setCustomMenuSize'>";
        $MSDropDownSize .="Set Custom Menu Size (Choose any number greater than 2):  ";
        if (isset($predefinedSize)){
         $menuSizeParameter = new textinput("menuSizeParameter", $predefinedSize, 'text', "4");   
        }else{
         $menuSizeParameter = new textinput("menuSizeParameter", "1", 'text', "4");   
        }
        
        $MSDropDownSize .=$menuSizeParameter->show() . "";
        $MSDropDownSize .="</div>";
        return $MSDropDownSize;
    }

    /*!
     * \brief This member function creates html content for a form to insert the parameters
     * for a button
     * \note This mini form consists of text, a text input and
     * a radio element.
     * \return A constructed mini form to insert the parameters for a button element.
     */
    protected function insertButtonParametersForm() {
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $buttonParameters = "Select Button Type:<br>";
        $objElement = new radio('resetOrSubmitButton');
        $objElement->addOption('submit', 'Submit Button (Submit Form)');
        $objElement->addOption('reset', 'Reset Button (Reset All Fields in Form)');
        $objElement->setSelected('submit');
        $objElement->setBreakSpace("<br>");
        $buttonParameters .= $objElement->show() . "<br><br>";
        $buttonParameters .= "Insert Label for Button:<br>";
        $buttonLabel = new textinput("buttonLabel", '', 'text', "70");
        $buttonParameters .= $buttonLabel->show();
        return $buttonParameters;
    }

}

?>
