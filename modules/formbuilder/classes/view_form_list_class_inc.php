<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class view_form_list
 *
 *  \brief This class models all the content and functionality for the
 * list_all_forms.php template file.
 *  \brief This class contains functionality for publishing forms and offers
 * many options to alter the forms. It also contains html content for
 * the template file.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
class view_form_list extends object {

//dead variable
// public $objLanguage;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_form_list that stores all
     * the properties of this class in an usable object.
     * \note This object is used to get all of the metadata of forms to display in the form
     * list or to use in publishing forms.
     */
    private $objDBFormList;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_publishing_options that stores all
     * the properties of this class in an usable object.
     * \note This object is used to get and save all of the publishing data of forms.
     */
    private $objDBFormPublishingOptions;

    /*!
     * \brief Private data member from the class user from the chisimba core
     * module security that stores all the properties of this class in an usable object.
     * \note This object is used to get details of users that are using this module.
     */
    private $objUser;

    /*!
     * \brief This private data member stores all the publishing options of one
     * form into an array so it can be used anywhere in this class.
     * \note Make sure that this array contains the right publishing content from
     * the right form with right form number.
     */
    private $publishOptionsArray;

    /*!
     * \brief Standard constructor that sets up all the private data members
     *  of this class.
     */
    public function init() {
// //Instantiate the language object
// not being used
// $this->objLanguage = $this->getObject('language','language');

        $this->objDBFormList = $this->getObject('dbformbuilder_form_list', 'formbuilder');
        $this->objDBFormPublishingOptions = $this->getObject('dbformbuilder_publish_options', 'formbuilder');
        $this->objUser = &$this->getObject('user', 'security');
        $this->publishOptionsArray = NULL;
        $this->setNumberOfEntriesInPaginationBatch();
    }

    /*!
     * \brief This function loads all the classes needed to build forms which are
     * within the chisimba core modules.
     */
    private function loadElements() {
///Load the form class
        $this->loadClass('form', 'htmlelements');
///Load the form class
        $this->loadClass('link', 'htmlelements');
///Load the textinput class
        $this->loadClass('textinput', 'htmlelements');
///Load the label class
        $this->loadClass('label', 'htmlelements');
///Load the textarea class
        $this->loadClass('textarea', 'htmlelements');
///Load the button object
        $this->loadClass('button', 'htmlelements');
///Load the radio object
        $this->loadClass('radio', 'htmlelements');
///Load the hidden input object
        $this->loadClass('hiddeninput', 'htmlelements');
///Load the checkbox object
        $this->loadClass('checkbox', 'htmlelements');
    }

    /*!
     * \brief This member function gets publishing data for a form with a form
     * number and stores it in the private data member publishOptionsArray which
     * is used everywhere inside this class.
     * \param formNumber An integer. Make sure you are inserting the
     * right form number as a wrong entry will produce wrong and desasterous results.
     * \warning This member function should be called to set this array before any
     * of the other publishing member functions are used.
     */
    public function getPublishingFormParameters($formNumber) {
        $this->publishOptionsArray = $this->objDBFormPublishingOptions->getFormPublishingData($formNumber);
    }

    /*!
     * \brief This function determines whether or not a form is published.
     * \param formNumber An integer. This argument is not being used inside this
     *  function. However, it is there to
     * make sure that you are inserting the right form number.
     * \note This uses the array publishOptionsArray which should be populated with
     * the right information before you use this member function.
     * \return A boolean whether or note a form is published.
     */
    private function buildViewPublishingIndicator($formNumber) {
        if ($this->publishOptionsArray["0"]['publishoption'] == NULL) {
            return 0;
        } else {
            return 1;
        }
    }

    /*!
     * \brief This function produces the html for the view publishing general
     * details tab inside the view form settings modal window.
     * \param formNumber An integer. This argument is not being used inside this
     *  function. However, it is there to
     * make sure that you are inserting the right form number.
     * \note This uses the array publishOptionsArray which should be populated with
     * the right information before you use this member function.
     * \return View publishing general details html content.
     */
    private function buildViewPublishingGeneralDetails($formNumber) {
        if ($this->publishOptionsArray["0"]['publishoption'] == 'simple') {
            if ($this->publishOptionsArray["0"]['siteurl'] == NULL) {
                $generalDetails = "This form is published with simple parameters.
On successful submission of this form, a link will be provided to
return to the form builder module.";
            } else {
                $generalDetails = "This form is published with simple parameters.
On successful submission of this form, the submitter will be diverted
in " . $this->publishOptionsArray["0"]['chisimbadiverterdelay'] . "
seconds to another site with \"" . $this->publishOptionsArray["0"]['siteurl'] . "\" as
a url.";
            }
        } else {
            if ($this->publishOptionsArray["0"]['chisimbaparameters'] == "yes") {
                $generalDetails = "This form is published with advanced parameters.
On successful submission of this form, this method will call an action
named \"" . $this->publishOptionsArray["0"]['chisimbaaction'] . "\" belonging to
the \"" . $this->publishOptionsArray["0"]['chisimbamodule'] . "\" module within
the chisimba framework. The contents of the form that will be submitted
will be created into a text string in standard URL-encoded notation that
can be used by you ultilizing a \"REQUEST\" or \"GET\" action.";
            } else {
                $generalDetails = "This form is published with advanced parameters.
On successful submission of this form, this method will call an action
named \"" . $this->publishOptionsArray["0"]['chisimbaaction'] . "\" belonging to
the \"" . $this->publishOptionsArray["0"]['chisimbamodule'] . "\" module within
the chisimba framework. No form contents will be submitted.";
            }
        }
        return $generalDetails;
    }

    /*!
     * \brief This function produces the html for the view publishing simple
     * details tab inside the view form settings modal window.
     * \param formNumber An integer. Make sure that you insert the right
     * form number as the wrong form number will produce wrong html content.
     * \return View publishing simple details html content.
     */
    private function buildViewPublishingSimpleDetails($formNumber) {
        $simpleDetails = "Use the URL below to construct your form for submission:<br>";
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $simpleUrlTextInput = new textarea("simpleUrl", html_entity_decode($this->uri(array('action' => 'buildCurrentForm', 'formNumber' => $formNumber), $this->moduleName)), 3, 80);
        $simpleDetails .=$simpleUrlTextInput->show() . "<br>";
        $simpleDetails.= "Copy this code into HTML to create a link to your form:<br>";
        $simpleLink = "'<a href='" . html_entity_decode($this->uri(array('action' => 'buildCurrentForm', 'formNumber' => $formNumber), $this->moduleName)) . "'>Construct Form</a>'";
        $simpleLinkTextInput = new textarea("simpleUrl", $simpleLink, 3, 80);
        $simpleDetails .=$simpleLinkTextInput->show();
        return $simpleDetails;
    }

    /* !
     * \brief This function produces the html for the view publishing advanced
     * details tab inside the view form settings modal window.
     * \param formNumber An integer. Make sure that you insert the right
     * form number as the wrong form number will produce wrong html content.
     * \return View publishing advanced details html content.
     */

    private function buildViewPublishingAdvancedDetails($formNumber) {
        $this->loadClass('textarea', 'htmlelements');
        $advancedDetails = "<h3>For Chisimba PHP Developers</h3>";
//      $advancedDetails.= "Use the code below to construct your form: <br>";
//      $advancedFormConstructionTextInput = new textarea("simpleUrl",'$this->nextAction("form_entity_handler", "formbuilder");
//echo $objYourForm->buildForm('.$formNumber.');',1,70);
        $advancedDetails.="Use the code below if you want to construct your form anywhere you desire:<br>";

        $advancedFormConstructionTextInput = new textarea("simpleUrl", '$objYourForm = $this->getObject("form_entity_handler", "' . $this->moduleName . '");
echo $objYourForm->buildForm(' . $formNumber . ');', 3, 80);
        $advancedDetails .=$advancedFormConstructionTextInput->show() . "<br>";
        $advancedDetails.= "The Chisimba URI is provided below: <br>";
        $advancedURITextInput = new textarea("simpleUrl", '$this->uri(array(
"module"=>"' . $this->moduleName . '",
"action"=>"buildCurrentForm",
"formNumber" =>' . $formNumber . '
));', 5, 80);
        $advancedDetails.= $advancedURITextInput->show();

        return $advancedDetails;
    }

    /*!
     * \brief This function creates html content for a publishing indicator and allows users
     * to publish and unpublish their forms.
     * \brief This html content is used in the form publishing parameters modal that
     * allows users to publish their forms.
     * \note This uses the array publishOptionsArray which should be populated with
     * the right information before you use this member function.
     * \return A constructed publishing indicator.
     */
    private function buildFormPublishingIndicator() {
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $publishingRadio = new radio('publishingRadio');
        $publishingRadio->addOption('publish', 'Publish This Form');
        $publishingRadio->addOption('unpublish', 'Unpublish This Form');
        if ($this->publishOptionsArray["0"]['publishoption'] == NULL) {
            $publishingRadio->setSelected('unpublish');
        } else {
            $publishingRadio->setSelected('publish');
        }

        $publishingFormIndicator = $publishingRadio->show() . '<br>';
        $simpleOrAdvancedIndicator = new hiddeninput("simpleOrAdvancedHiddenInput", $this->publishOptionsArray["0"]['publishoption']);


        $publishingFormIndicator .=$simpleOrAdvancedIndicator->show();
        return $publishingFormIndicator;
    }

    /*!
     * \brief This function creates html form for a simple publishing and allows users
     * to publish their forms with simple publishing parameters.
     * \brief This html content is used in the form publishing parameters modal that
     * allows users to publish their forms.
     * \note This uses the array publishOptionsArray which should be populated with
     * the right information before you use this member function.
     * \return A constructed simple publishing form.
     */
    private function buildSimplePublishingForm() {
        $this->loadElements();
        $simplePublishingFormUnderConstruction = "Select an action to preform after a form submission:<br>";

        $postActionRadio = new radio('simplePostActionRadio');
        $postActionRadio->addOption('internal', 'Provide a link to go the form builder module.');
        $postActionRadio->addOption('external', 'Divert to a url of your choice.
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;');
        if ($this->publishOptionsArray["0"]['siteurl'] != NULL) {
            $postActionRadio->setSelected('external');
        } else {
            $postActionRadio->setSelected('internal');
        }

        $postActionRadio->setBreakSpace("<br>");
        $postActionRadio->show() . '<br>';

        $simplePublishingFormUnderConstruction .= $postActionRadio->show() . '<br>';

        $simplePublishingFormUnderConstruction .= "<div id='urlInserter'>";
        $simplePublishingFormUnderConstruction .= "Insert a url of your choice:<br>";
        if ($this->publishOptionsArray["0"]['siteurl'] == NULL) {
            $urlTextInput = new textinput("urlChoice", '', 'text', '100');
        } else {
            $siteURL = $this->publishOptionsArray["0"]['siteurl'];
            $urlTextInput = new textinput("urlChoice", $siteURL, 'text', '100');
        }
        $simplePublishingFormUnderConstruction .= $urlTextInput->show() . '<br>';
        $simplePublishingFormUnderConstruction .= "Select the time delay before the divert intiates:<br>";
        $divertDelayRadio = new radio('simpleDivertDelayRadio');
        $divertDelayRadio->addOption('5', 'Divert after 5 seconds.
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        $divertDelayRadio->addOption('10', 'Divert after 10 seconds.
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        $divertDelayRadio->addOption('0', 'Divert immediately without any delay.');
        if ($this->publishOptionsArray["0"]['chisimbadiverterdelay'] == NULL) {
            $divertDelayRadio->setSelected('5');
        } else {
            $divertDelayRadio->setSelected($this->publishOptionsArray["0"]['chisimbadiverterdelay']);
        }
        $divertDelayRadio->setBreakSpace("<br>");
        $simplePublishingFormUnderConstruction .= $divertDelayRadio->show() . '<br>';
        $simplePublishingFormUnderConstruction .="</div>";
        return $simplePublishingFormUnderConstruction;
    }

    /*!
     * \brief This function creates html form for a advanced publishing and allows users
     * to publish their forms with simple publishing parameters.
     * \brief This html content is used in the form publishing parameters modal that
     * allows users to publish their forms.
     * \note This uses the array publishOptionsArray which should be populated with
     * the right information before you use this member function.
     * \return A constructed advanced publishing form.
     */
    private function buildAdvancedPublishingForm() {
        $this->loadElements();
        $advancedPublishingFormUnderConstruction = "<b>For Chisimba PHP Developers</b><br><br>";
        $advancedPublishingFormUnderConstruction .= "Insert the next action after the form has been submitted:<br>";
        if ($this->publishOptionsArray["0"]['chisimbaaction'] == NULL) {
            $nextActionTextInput = new textinput("nextAction", '', 'text', '100');
        } else {
            $nextAction = $this->publishOptionsArray["0"]['chisimbaaction'];
            $nextActionTextInput = new textinput("nextAction", $nextAction, 'text', '100');
        }

        $advancedPublishingFormUnderConstruction .= $nextActionTextInput->show() . '<br>';
        $advancedPublishingFormUnderConstruction .= "Enter the module this action belongs to:<br>";
        if ($this->publishOptionsArray["0"]['chisimbamodule'] == NULL) {
            $nextActionModuleTextInput = new textinput("nextActionModule", '', 'text', '100');
        } else {

            $chisimbaModule = $this->publishOptionsArray["0"]['chisimbamodule'];
            $nextActionModuleTextInput = new textinput("nextActionModule", $chisimbaModule, 'text', '100');
        }
        $advancedPublishingFormUnderConstruction .= $nextActionModuleTextInput->show() . '<br>';

        if ($this->publishOptionsArray["0"]['chisimbaparameters'] == NULL) {
            $parametersCheckbox = new checkbox("chisimbaParameters", '', 0);  // this will checked
        } else {
            if ($this->publishOptionsArray["0"]['chisimbaparameters'] == "yes") {
                $parametersCheckbox = new checkbox("chisimbaParameters", '', 1);  // this will checked
            } else {
                $parametersCheckbox = new checkbox("chisimbaParameters", '', 0);  // this will checked
            }
        }
        $parametersCheckboxLabel = new label("Pass Form Element Parameters with this Action", "chismbaParameters");
        $advancedPublishingFormUnderConstruction .=$parametersCheckbox->show() . $parametersCheckboxLabel->show() . "<br>";
        $advancedPublishingFormUnderConstruction .="Select the time delay before the divert intiates:<br>";
        $divertDelayRadio = new radio('advancedDivertDelayRadio');
        $divertDelayRadio->addOption('5', 'Divert after 5 seconds.
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        $divertDelayRadio->addOption('10', 'Divert after 10 seconds
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        $divertDelayRadio->addOption('0', 'Divert immediately without any delay');
        if ($this->publishOptionsArray["0"]['chisimbadiverterdelay'] == NULL) {
            $divertDelayRadio->setSelected('5');
        } else {
            $divertDelayRadio->setSelected($this->publishOptionsArray["0"]['chisimbadiverterdelay']);
        }
        $divertDelayRadio->setBreakSpace("<br>");
        $advancedPublishingFormUnderConstruction .= $divertDelayRadio->show();
        return $advancedPublishingFormUnderConstruction;
    }

    /*!
     * \brief This function creates html content for the form options menu modal
     * window.
     * \brief This menu simple contains buttons and text that give control over to
     * other functions of the module.
     * \param formNumber An integer. Make sure that you insert the right
     * form number as the wrong form number will produce wrong html content.
     * \return A constructed form options menu.
     */
    private function buildFormOptionsMenu($formNumber) {
        $formMetaDataList = $this->objDBFormList->getFormMetaData($formNumber);
        $formMetaName = $formMetaDataList["0"]['name'];

        $mngCreateNewFormlink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'addFormParameters'
                        )));

        $mngEditFormMetaDatalink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'editFormParameters',
                            'formNumber' => $formNumber
                        )));

        $mngEditWYSIWYGFormslink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'editWYSIWYGForm',
                            'formNumber' => $formNumber,
                            'formTitle' => $formMetaName
                        )));
        $mngBuildFormlink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'buildCurrentForm',
                            'formNumber' => $formNumber
                        )));
        $mngViewSubmitResultslink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'viewSubmittedResults',
                            'formNumber' => $formNumber
                        )));

        $formOptionsMenuUnderConstruction = "<div >New Commands</div>";
        $formOptionsMenuUnderConstruction .= "<button class='createNewFormButton' onclick=parent.location='$mngCreateNewFormlink'>Create A New Form</button><br>";
        $formOptionsMenuUnderConstruction .= "<div >Edit Commands</div>";
        $formOptionsMenuUnderConstruction .= "<button class='editFormMetaData' onclick=parent.location='$mngEditFormMetaDatalink'>Edit Form Metadata</button>";
        $formOptionsMenuUnderConstruction .= "<button class='editWYSIWYGForm' onclick=parent.location='$mngEditWYSIWYGFormslink'>Edit Form in WYSIWYG Interface</button>";
        $formOptionsMenuUnderConstruction .= "<div >Build Commands</div>";
        $formOptionsMenuUnderConstruction .= "<button class='previewFormButton' name='$formNumber'>Preview Form Contents</button>";
        $formOptionsMenuUnderConstruction .= "<button class='constructFormButton' name='constructFormButton' onclick=parent.location='$mngBuildFormlink'>Construct Form For Submission</button>";
        $formOptionsMenuUnderConstruction .= "<div >Delete Commands</div>";
        $formOptionsMenuUnderConstruction .= "<button class='deleteFormSubmissions' name='$formNumber'>Delete Form Submissions</button>";
        $formOptionsMenuUnderConstruction .= "<button class='deleteForm' name='$formNumber'>Delete All Form Contents</button>";
        $formOptionsMenuUnderConstruction .= "<div >Form Submission Commands</div>";
        $formOptionsMenuUnderConstruction .= "<button class='constructFormButtonForSubmission' name='constructFormButton' onclick=parent.location='$mngBuildFormlink'>Construct Form For Submission</button>";
        $formOptionsMenuUnderConstruction .= "<button class='viewSubmitResultsButton' name='viewSubmitResultsButton' onclick=parent.location='$mngViewSubmitResultslink'>View Form Submissions</button>";
//$formOptionsMenuUnderConstruction .= "<div >Form Publishing Commands</div>";
//$formOptionsMenuUnderConstruction .= "<button class='viewPublishingDataButton' name='$formNumber'>View Publishing Data</button>";
// $formOptionsMenuUnderConstruction .= "<button class='editPublishingDataButton' name='$formNumber'>Edit Publishing Parameters</button>";
        return $formOptionsMenuUnderConstruction;
    }

    /*!
     * \brief This function creates html content for the search toolbar on top
     * of the list_all_forms.php template file.
     * \brief This menu simple contains a text input and button.
     * \return A constructed search toolbar.
     */
    private function buildSearchMenu() {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $objForm = new form('searchFormListForm', $this->uri(array("action" => "searchAllForms"), "formbuilder"));
        $searchTextinput = new textinput("searchFormList", '', 'text', "40");
        $objForm->addToForm($searchTextinput->show() . "&nbsp;");

//dead code to insert a reset button
        $objResetFormListButton = new button('resetFormListButton');
        $objResetFormListButton->setValue('Reset');
        $objResetFormListButton->setToReset();
        $objResetFormListButton->setCSS("searchFormListButton");
//$objForm->addToForm($objResetFormListButton->show());

        $objsearchFormListButton = new button('searchFormListButton');
        $objsearchFormListButton->setValue('Search');
        $objsearchFormListButton->setToSubmit();
        $objsearchFormListButton->setCSS("searchFormListButton");
        $objForm->addToForm($objsearchFormListButton->showDefault() . '<br>');

        return "<div id='searchFormListMenu' style='float:right;clear:right;'>" . $objForm->show() . "</div>";
    }

    /*!
     * \brief This function creates html content for the pagination buttons on top
     * of the list_all_forms.php template file.
     * \brief This menu simply contains two buttons ie Next and Previous to
     * paginate to the next or previous page.
     * \param paginationRequestNumber An integer. This number stores which
     * page number content is loaded. It is a dead argument, you dont need to
     * put it in.
     * \return A constructed pagination menu.
     */
    private function buildPaginationMenu($paginationRequestNumber) {
//Some dead code here
//  $formNumberStart = $this->determinePaginationEntryOffset($paginationRequestNumber);
//  $formNumberStop =   $formNumberStart + $this->getNumberOfEntriesInPaginationBatch();
//  $totalNumberOfEntries = $this->objDBFormList->getNumberOfForms();
//$numberOfPaginationRequests = ceil($totalNumberOfEntries/$this->getNumberOfEntriesInPaginationBatch());
//end of dead code
        $paginationMenuUnderConstruction = "<div id='paginationMenu' style='float:right;clear:right;'>";
        $paginationMenuUnderConstruction .= "<button class='previousButton'>Previous</button>";
// $paginationMenuUnderConstruction .= "<button>".($formNumberStart+1)." - ".$formNumberStop." of ";
// $paginationMenuUnderConstruction .=$totalNumberOfEntries;
//$paginationMenuUnderConstruction .=" forms</button>";
        $paginationMenuUnderConstruction .= "<button class='nextButton'>Next</button>";
        $paginationMenuUnderConstruction .="</div>";

        return $paginationMenuUnderConstruction;
    }

    /*!
     * \brief This function determine the number of pagination requests. Simply
     * put, how many times the user presses the next button to load more results
     * until there are not more results to load.
     * \param searchValue A string. If a search parameter is inserted then the
     * number of pagination reuests are different since there will be a different
     * number of forms to load. It is defaulted to null.
     * \return An integer.
     */
    public function getNumberofPaginationRequests($searchValue=NULL) {
        if ($searchValue != NULL) {
            $totalNumberOfEntries = $this->objDBFormList->getNumberofSearchedEntries($searchValue);
        } else {
            $totalNumberOfEntries = $this->objDBFormList->getNumberOfForms();
        }

        return $numberOfPaginationRequests = ceil($totalNumberOfEntries / $this->getNumberOfEntriesInPaginationBatch());
    }

    /*!
     * \brief This function creates html content for the pagination menu on top
     * of the list_all_forms.php template file.
     * \brief This indicator is a button is that shows which forms are being
     * shown. It is placed between the two buttons of the pagination menu.
     * \param paginationRequestNumber An integer. This number stores which
     * page number content is loaded.
     * \param searchValue A string. If a search parameter is inserted then the
     * number of pagination reuests are different since there will be a different
     * number of forms to load. It is defaulted to null.
     * \return A constructed pagination indicator.
     */
    private function buildPaginationIndicator($paginationRequestNumber, $searchValue=NULL) {

        $formNumberStart = $this->determinePaginationEntryOffset($paginationRequestNumber);
        $formNumberStop = $formNumberStart + $this->getNumberOfEntriesInPaginationBatch();
//     $action = $this->getParam("action", "searchAllForms");
        if ($searchValue != NULL) {

            $totalNumberOfEntries = $this->objDBFormList->getNumberofSearchedEntries($searchValue);
        } else {

            $totalNumberOfEntries = $this->objDBFormList->getNumberOfForms();
        }
        $numberOfPaginationRequests = ceil($totalNumberOfEntries / $this->getNumberOfEntriesInPaginationBatch());

        if ($numberOfPaginationRequests == $paginationRequestNumber + 1) {
            $paginationIndicatorUnderConstruction = "<button id='paginationIndicator'>" . ($formNumberStart + 1) . " - " . $totalNumberOfEntries . " of ";
            $paginationIndicatorUnderConstruction .=$totalNumberOfEntries;
            $paginationIndicatorUnderConstruction .=" forms</button>";
        } else {
            $paginationIndicatorUnderConstruction = "<button id='paginationIndicator'>" . ($formNumberStart + 1) . " - " . $formNumberStop . " of ";
            $paginationIndicatorUnderConstruction .=$totalNumberOfEntries;
            $paginationIndicatorUnderConstruction .=" forms</button>";
        }

        return $paginationIndicatorUnderConstruction;
    }

    /*!
     * \brief This function allows you to set how many forms can be viewed in
     * one pagination page.
     * \brief noOfEntries An integer that specifies how many forms can be viewed.
     * It is defaulted to three.
     * \warning Dont put negetive values.
     */
    public function setNumberOfEntriesInPaginationBatch($noOfEntries=3) {
        $this->noOfEntriesInPaginationBatch = $noOfEntries;
    }

    /*!
     * \brief This function gets the number of how many forms can be viewed in
     * one pagination page.
     */
    public function getNumberOfEntriesInPaginationBatch() {

        return $this->noOfEntriesInPaginationBatch;
    }

    /*!
     * \brief This function gets the the offset for which database entry to start
     * loading.
     * \return An integer.
     */
    private function determinePaginationEntryOffset($paginationRequestNumber) {
        return $this->noOfEntriesInPaginationBatch * $paginationRequestNumber;
    }

    /*!
     * \brief This function builds the html content for form list in accordion
     * format.
     * \brief It also builds the form options toolbar adn displays
     * form metadata for each form. It also input access restriction
     * parameters for each form in the list.
     * \param paginationRequestNumber An integer. This number stores which
     * page number content is loaded.
     * \param searchValue A string. If a search parameter is inserted then the
     * number of pagination reuests are different since there will be a different
     * number of forms to load. It is defaulted to null.
     * \note $accessInt= 1 means the user is admin and has all user
     * previledges. $accessInt= 1 means the authors of the forms have
     * all the previledges for theri forms. $accessInt= 3 No access
     * previladges are given
     * \return An a built form list.
     */
    private function buildFormList($paginationRequestNumber, $searchValue=NULL) {
        $this->loadElements();

//  $action = $this->getParam("action", "listAllForms");
        if ($searchValue != NULL) {
//  $searchValue = $this->getParam("searchFormList", NULL);
            $formListMetaDataArray = $this->objDBFormList->searchFormList($searchValue, $this->getNumberOfEntriesInPaginationBatch(), $this->determinePaginationEntryOffset($paginationRequestNumber));
        } else {
            $formListMetaDataArray = $this->objDBFormList->getPaginatedEntries($this->getNumberOfEntriesInPaginationBatch(), $this->determinePaginationEntryOffset($paginationRequestNumber));
        }

        if ($formListMetaDataArray == NULL) {
            return "<h3>There are no forms that can be displayed.</h3>";
        }
        $accordianUnderConstrunction = "";
        $accordianUnderConstrunction .= '<div id="accordion" style="clear:both;">';
        foreach ($formListMetaDataArray as $thisformListMetaData) {
//Store the values of the array in variables

          //  $id = $thisformListMetaData["id"];
            $formNumber = $thisformListMetaData["formnumber"];
          //  $formName = $thisformListMetaData["name"];
            $formLabel = $thisformListMetaData["label"];
            $formDetails = $thisformListMetaData["details"];
            $formAuthorID = $thisformListMetaData["author"];
            $formCreated = $thisformListMetaData["created"];
            $accordianUnderConstrunction .="<h3><a href=" . $formNumber . ">" . $formNumber . "   " . $formLabel . "</a></h3>";


            $accordianUnderConstrunction .= "<div style ='margin-bottom:0px;'>";
            $formMetadataTable = &$this->newObject("htmltable", "htmlelements");
//Define the table border
            $formMetadataTable->border = 0;
//Set the table spacing
            $formMetadataTable->cellspacing = '15';
//Set the table width
            $formMetadataTable->width = "100%";

            $accordianUnderConstrunction .= "<div class='ui-widget ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 25px 15px 25px;'>";
            $accordianUnderConstrunction .= '<span id="toolbar" class="ui-widget-header ui-corner-all">';

            $objBuildFormButton = new button('constructFormButton');
            $objBuildFormButton->setValue('Render Designed Form For Submission');
            $objBuildFormButton->setCSS("constructFormButton");
            $mngBuildFormlink = html_entity_decode($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'buildCurrentForm',
                                'formNumber' => $formNumber
                            )));
            $objBuildFormButton->setOnClick("parent.location='$mngBuildFormlink'");
            $mngBuildFormlink = $objBuildFormButton->showDefault();
            $linkBuildFormManage = $mngBuildFormlink;


            $objPreviewFormButton = new button($formNumber);
            $objPreviewFormButton->setValue('Preview Form');
            $objPreviewFormButton->setCSS("previewFormButton");
            $mngPreviewFormlink = html_entity_decode($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'previewCurrentForm',
                                'formNumber' => $formNumber
                            )));


            $mngPreviewFormlink = $objPreviewFormButton->showDefault();
            $linkPreviewFormManage = $mngPreviewFormlink;

            $objFormOptionsButton = new button($formNumber);
            $objFormOptionsButton->setValue('Form Options');
            $objFormOptionsButton->setCSS("formOptionsButton");
            $mngFormOptionslink = new link($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'formOptionsOfCurrentForm',
                                'formNumber' => $formNumber
                            )));
            $mngFormOptionslink = $objFormOptionsButton->showDefault();
            $linkFormOptionsManage = $mngFormOptionslink;

            $objViewPublishingDataButton = new button($formNumber);
            $objViewPublishingDataButton->setValue('View Form Settings');
            $objViewPublishingDataButton->setCSS("viewPublishingDataButton");
            $mngViewPublishingDatalink = new link($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'viewPublishingDataOfCurrentForm',
                                'formNumber' => $formNumber
                            )));
            $mngViewPublishingDatalink = $objViewPublishingDataButton->showDefault();
            $linkViewPublishingDataManage = $mngViewPublishingDatalink;

            $objEditPublishingDataButton = new button($formNumber);
            $objEditPublishingDataButton->setValue('Publishing and Submit Options');
            $objEditPublishingDataButton->setCSS("editPublishingDataButton");
            $mngEditPublishingDatalink = new link($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'editPublishingDataOfCurrentForm',
                                'formNumber' => $formNumber
                            )));
            $mngEditPublishingDatalink = $objEditPublishingDataButton->showDefault();
            $linkEditPublishingDataManage = $mngEditPublishingDatalink;

            $objViewSubmitResultsButton = new button('viewSubmitResultsButton');
            $objViewSubmitResultsButton->setValue('View Submit Result Records');
            $objViewSubmitResultsButton->setCSS("viewSubmitResultsButton");
            $mngViewSubmitResultslink = html_entity_decode($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'viewSubmittedResults',
                                'formNumber' => $formNumber
                            )));
            $objViewSubmitResultsButton->setOnClick("parent.location='$mngViewSubmitResultslink'");
            $mngViewSubmitResultslink = $objViewSubmitResultsButton->showDefault();
            $linkViewSubmitResultsManage = $mngViewSubmitResultslink;


            $accordianUnderConstrunction .= $linkPreviewFormManage
                    . $linkBuildFormManage
                    . $linkFormOptionsManage
                    . $linkViewPublishingDataManage
                    . $linkEditPublishingDataManage
                    . $linkViewSubmitResultsManage;

            if ($this->objUser->isAdmin()) {
                $accessInt = 1;
            } else if ($this->objUser->userId() == $formAuthorID) {
                $accessInt = 2;
            } else {
                $accessInt = 3;
            }
            $accessRestrictionHidden = new hiddeninput("accessRestriction", $accessInt);
            $accessRestrictionHidden->extra = "class=userAccessRestriction";
            $accordianUnderConstrunction .=$accessRestrictionHidden->show();
            $accordianUnderConstrunction .= '</span>';

            $formMetadataTable->startRow();
            $formMetadataTable->addCell("<B>Form Name: </B>" . $formLabel);
            $formMetadataTable->addCell("<B>Date Created: </B>" . $formCreated);
            $formMetadataTable->endRow();
            $formMetadataTable->startRow();
            $formMetadataTable->addCell("<B>Author: </B>" . $this->objUser->fullname($formAuthorID));
            $formMetadataTable->addCell("<B>Email: </B>" . $this->objUser->email($formAuthorID));
//$formMetadataTable->addCell($accessRestrictionHidden->show());
            $formMetadataTable->endRow();
            $formMetadataTable->startRow();
            $formMetadataTable->addCell("<B>Form Description: </B>");
            $formMetadataTable->endRow();
            $formMetadataTable->startRow();
            $formMetadataTable->addCell($formDetails);
            $formMetadataTable->endRow();
            $accordianUnderConstrunction .= $formMetadataTable->show();
            $accordianUnderConstrunction .= "</div>";
            $accordianUnderConstrunction .= "</div>";
        };

        $accordianUnderConstrunction .= "</div>";
        return $accordianUnderConstrunction;
    }

    /*!
     * \brief This function builds the html content for form list simple old
     * table format.
     * \note This is a dead member function which is not being used.
     * If you want to build a form list in a simple table format, then
     * use this member function.
     * \note Additionally, no pagination is taken into account. All
     * forms get lisyed at once.
     * \return An a built form list.
     */
    private function buildForm() {
        $this->loadElements();
//Create the form
        $objForm = new form('formList', $this->getFormAction());
//Fetch the comments from DB
        $allComments = $this->objDBFormList->listAll();
// Create a table object
        $commentsTable = &$this->newObject("htmltable", "htmlelements");
//Define the table border
        $commentsTable->border = 0;
//Set the table spacing
        $commentsTable->cellspacing = '12';
//Set the table width
        $commentsTable->width = "40%";

//Create the array for the table header
        $tableHeader = array();
        $tableHeader[] = "ID";
        $tableHeader[] = "Formnumber";
        $tableHeader[] = "Form Name";
        $tableHeader[] = "Form Label";
        $tableHeader[] = "Form Desciortion";
        $tableHeader[] = "Aithour";
        $tableHeader[] = "Date Cerated";
        $tableHeader[] = "View Form";
        $tableHeader[] = "Edit Form";
        $tableHeader[] = "View Form Results";
// Create the table header for display
        $commentsTable->addHeader($tableHeader, "heading");
//Render each comment in a table.
        foreach ($allComments as $thisComment) {
//Store the values of the array in variables

            $id = $thisComment["id"];
            $formNumber = $thisComment["formnumber"];
            $formName = $thisComment["name"];
            $formLabel = $thisComment["label"];
            $formDetails = $thisComment["details"];
            $formAuthor = $thisComment["author"];
            $formCreated = $thisComment["created"];
//Edit Row
// eval($title);
            $iconEdSelect = $this->getObject('geticon', 'htmlelements');
            $iconEdSelect->setIcon('edit');
            $iconEdSelect->alt = "Edit Comment";
            $mngedlink = new link($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'buildCurrentForm',
                                'formNumber' => $formNumber
                            )));
            $mngedlink->link = $iconEdSelect->show();
            $linkEdManage = $mngedlink->show();
//Get the icon object
            $iconDelete = $this->getObject('geticon', 'htmlelements');
//Set the icon name
            $iconDelete->setIcon('delete');
//Set the alternative text of the icon
            $iconDelete->alt = "Delete Form";
//Set align to default
            $iconDelete->align = false;
//Create a new link Object
            $objConfirm = &$this->getObject("link", "htmlelements");
//Create a new confirm object.
            $objConfirm = &$this->newObject('confirm', 'utilities');
//Set object to confirm and the path for the confirm implementation and confirm text
            $objConfirm->setConfirm($iconDelete->show(), $this->uri(array(
                        'module' => 'formbuilder',
                        'action' => 'editWYSIWYGForm',
                        'formNumber' => $formNumber,
                        'formLabel' => $formLabel,
                        'formCaption' => $formDetails,
                        'formTitle' => $formName
                    )));

            $viewResultsSelect = $this->getObject('geticon', 'htmlelements');
            $viewResultsSelect->setIcon('view');
            $viewResultsSelect->alt = "View Submitted Results";
            $mngviewlink = new link($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'viewSubmittedResults',
                                'formNumber' => $formNumber
                            )));
            $mngviewlink->link = $viewResultsSelect->show();
            $mngviewlinkManage = $mngviewlink->show();

// Add the table rows.

            $commentsTable->startRow();
            $commentsTable->addCell($id);
            $commentsTable->addCell($formNumber);
            $commentsTable->addCell($formName);
            $commentsTable->addCell($formLabel);
            $commentsTable->addCell($formDetails);
            $commentsTable->addCell($formAuthor);
            $commentsTable->addCell($formCreated);
            $commentsTable->addCell($linkEdManage);
            $commentsTable->addCell($objConfirm->show());
            $commentsTable->addCell($mngviewlinkManage);
            $commentsTable->endRow();
        }
//Get the icon object
        $iconSelect = $this->getObject('geticon', 'htmlelements');
//Set the name of the icon
        $iconSelect->setIcon('add');
//Set the alternative text of the icon
        $iconSelect->alt = "Add New Comment";
//Create a new link for the add link
        $mnglink = new link($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'addForm'
                        )));
//Set the link text/image
        $mnglink->link = $iconSelect->show();
//Build the link
        $linkManage = $mnglink->show();
//Add the add button to the table
// Add the table rows.
        $commentsTable->startRow();
//Note we are using column span. The other four parameters are set to default
        $commentsTable->addCell($linkManage, '', '', '', '', 'colspan="2"');
        $commentsTable->endRow();

        $objForm->addToForm($commentsTable->show());
        return $objForm->show();
    }

    /*!
     * \brief This function is used in the membe function that
     *  builds the html content for form list simple old table format.
     * \note This is a dead member function which is not being used.
     * If you want to build a form list in a simple table format, then
     * this member function will be used.
     * \return A form POST action.
     */
    private function getFormAction() {
        $action = $this->getParam("action", "add");
        if ($action == "edit") {
            $formAction = $this->uri(array("action" => "update"), "formbuilder");
        } else {
            $formAction = $this->uri(array("action" => "add"), "formbuilder");
        }
        return $formAction;
    }

    /*!
     * \brief This member function allows you to get the view publishing indicator
     * html content for displaying.
     * \param formNumber An integer. Make sure that you insert the right
     * form number as the wrong form number will produce wrong html content.
     * \return View Publishing indicator html content.
     */
    public function showViewPublishingIndicator($formNumber) {
        return $this->buildViewPublishingIndicator($formNumber);
    }

    /*!
     * \brief This member function allows you to get the view publishing general details
     * html content for displaying.
     * \param formNumber An integer. Make sure that you insert the right
     * form number as the wrong form number will produce wrong html content.
     * \return View publishing general details html content.
     */
    public function showViewPublishingGeneralDetails($formNumber) {
        return $this->buildViewPublishingGeneralDetails($formNumber);
    }

    /*!
     * \brief This member function allows you to get the view publishing simple details
     * html content for displaying.
     * \param formNumber An integer. Make sure that you insert the right
     * form number as the wrong form number will produce wrong html content.
     * \return View publishing simple details html content.
     */
    public function showViewPublishingSimpleDetails($formNumber) {
        return $this->buildViewPublishingSimpleDetails($formNumber);
    }

    /*!
     * \brief This member function allows you to get the view publishing advanced details
     * html content for displaying.
     * \param formNumber An integer. Make sure that you insert the right
     * form number as the wrong form number will produce wrong html content.
     * \return View publishing advanced details html content.
     */
    public function showViewPublishingAdvancedDetails($formNumber) {
        return $this->buildViewPublishingAdvancedDetails($formNumber);
    }

    /*!
     * \brief This member function allows you to get the simple publishing form
     * html content for displaying.
     * \return Simple publishing form html content.
     */
    public function showSimplePublishingForm() {
        return $this->buildSimplePublishingForm();
    }

    /*!
     * \brief This member function allows you to get the advanced publishing form
     * html content for displaying.
     * \return Advanced publishing form html content.
     */
    public function showAdvancedPublishingForm() {
        return $this->buildAdvancedPublishingForm();
    }

    /*!
     * \brief This member function allows you to get the form publishing indicator
     * html content for displaying.
     * \return Form publishing indicator html content.
     */
    public function showFormPublishingIndicator() {
        return $this->buildFormPublishingIndicator();
    }

    /*!
     * \brief This member function allows you to get the form options menu
     * html content for displaying.
     * \param formNumber An integer. Make sure that you insert the right
     * form number as the wrong form number will produce wrong html content.
     * \return Form options menu html content.
     */
    public function showFormOptionsMenu($formNumber) {
        return $this->buildFormOptionsMenu($formNumber);
    }

    /*!
     * \brief This member function allows you to get the search menu
     * html content for displaying.
     * \param formNumber An integer. Make sure that you insert the right
     * form number as the wrong form number will produce wrong html content.
     * \return Search menu html content.
     */
    public function showSearchMenu() {
        return $this->buildSearchMenu();
    }

    /*!
     * \brief This member function allows you to get the search menu
     * html content for displaying.
     * \param paginationRequestNumber An integer. This number stores which
     * page number content is loaded.
     * \param searchValue A string. If a search parameter is inserted then the
     * number of pagination reuests are different since there will be a different
     * number of forms to load. It is defaulted to null.
     * \return Search menu html content.
     */
    public function showPaginationIndicator($paginationRequestNumber, $searchValue) {
        return $this->buildPaginationIndicator($paginationRequestNumber, $searchValue);
    }

    /*!
     * \brief This member function allows you to get the pagination menu
     * html content for displaying.
     * \param paginationRequestNumber An integer. This number stores which
     * page number content is loaded.
     * \return Pagination menu html content.
     */
    public function showPaginationMenu($paginationRequestNumber) {
        return $this->buildPaginationMenu($paginationRequestNumber);
    }

    /*!
     * \brief This member function allows you to get the form list in accordion
     * html content for displaying.
     * \param paginationRequestNumber An integer. This number stores which
     * page number content is loaded.
     * \param searchValue A string. If a search parameter is inserted then the
     * number of pagination reuests are different since there will be a different
     * number of forms to load. It is defaulted to null.
     * \return Form list in accordion html content.
     */
    public function show($paginationRequestNumber, $searchValue) {
        return $this->buildFormList($paginationRequestNumber, $searchValue);
    }

}

?>
