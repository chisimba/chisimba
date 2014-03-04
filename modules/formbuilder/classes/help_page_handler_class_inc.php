<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class help_page_handler
 *
 *  \brief This class models all the help cotnent for this module.
 *  \brief It is interface class that that entries in the help content database
 * and constructs meaining content ans spits it out.
 *  \brief This class uses the dbformbuilder_user_help_content class to pull
 * help content out of the database.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
class help_page_handler extends object {

    /*!
     * \brief Private data member from the class \ref dbformbuilder_user_help_content that stores all
     * the properties of this class in an usable object.
     * \note This object is used to get all of the help content from the
     * database.
     */
    private $dbUserHelpContent;

    /*!
     * \brief Standard constructor that sets up all the private data members
     *  of this class.
     */
    public function init() {
        $this->dbUserHelpContent = $this->getObject('dbformbuilder_user_help_content', 'formbuilder');
    }

    /*!
     * \brief This member function contructs the navigation menu for help_main.php
     * template file.
     * \return A constructed navigation menu.
     */
    private function buildHelpNavigationMenu() {
        $mngIntroButton = "<button class='introButton'>Introduction</button>";
        $mngFormMetaDataButton = "<button class='formMetaDataButton'>Form Metadata</button>";
        $mngFormEditorButton = "<button class='formEditorButton'>WYSIWYG Form Editor</button>";
        $mngFormPublisherButton = "<button class='formPublisherButton'>Form Publisher</button>";
        $mngFormOptionsButton = "<button class='formOptionsButton'>Form Listings and Options</button>";
        $mngHTMLElementsButton = "<button class='htmlformelements'>HTML Form Elements</button>";

        $slideMenuUnderConstruction = $mngIntroButton
                . $mngFormMetaDataButton
                . $mngFormEditorButton
                . $mngFormPublisherButton
                . $mngFormOptionsButton
                . $mngHTMLElementsButton;
        return $slideMenuUnderConstruction;
    }

    /*!
     * \brief This member function contructs a button that allows users to download
     * a PDF version of the help content.
     * \return A constructed div with a download link.
     */
    private function buildUserManualDownloadLink() {
        $pageContent = "<div id='downloadLinkContainer' class='ui-accordion-content ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;margin:10px 75px 10px 75px;'> ";
        $pageContent .= "<h3 align='center'>Click on the button below to download a pdf version of this help.</h3>";
        $downLink = html_entity_decode("packages/formbuilder/resources/textfiles/Form Builder - User Manual.pdf");
        $downLink = html_entity_decode($this->getResourceUri('textfiles/Form_Builder_User_Manual.pdf', 'formbuilder'));
        $pageContent .="<p align='center'>
<button class='downloadManualLink' onclick=parent.location='$downLink'>Download Form Builder User Manual</button></p>";
        $pageContent .= "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the introduction
     * page in the main page.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the imaged folder in the resources
     * folder.
     * \return Constructed introduction help content.
     */
    private function buildIntroPageContent() {
        $pageContent = $this->buildHelpNavigationMenu();
        $pageContent .= $this->buildUserManualDownloadLink();
        $content = $this->dbUserHelpContent->listPageContent("introduction");
        $pageContent .= $content["0"]['pagecontent'];

        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/basic_module_flow.png'
alt='A simple block diagram of the major module entities and the flow relationship with each other.'
title='A simple block diagram of the major module entities and the flow relationship with each other.'>";
        $content = $this->dbUserHelpContent->listPageContent("introduction_after_module_flow_image");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= $this->buildHelpNavigationMenu();
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the meta data
     * page in the main page.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the imaged folder in the resources
     * folder.
     * \return Constructed meta data help content.
     */
    private function buildFormMetaDataPageContent($isSeperateHelpBool) {
        $pageContent = NULL;
        if ($isSeperateHelpBool != 1) {
            $pageContent = $this->buildHelpNavigationMenu();
        }
        $content = $this->dbUserHelpContent->listPageContent("metadata_introduction");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/home_page.png'
alt='Home page of the form builder module is shown.'
title='Home page of the form builder module is shown.'>";
        $content = $this->dbUserHelpContent->listPageContent("metadata_submit_results_explanation");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/create_new_form_window.png'
alt='The form metadata modal window is shown.'
title='The form metadata modal window is shown.'>";
        $content = $this->dbUserHelpContent->listPageContent("metadata_text_after_figure");
        $pageContent .= $content["0"]['pagecontent'];
        if ($isSeperateHelpBool != 1) {
            $pageContent.= $this->buildHelpNavigationMenu();
        }
        return $pageContent;
    }

    /*!
     * \brief This member function contructs a div to put interactive help buttons.
     * \return Constructed interactive help div.
     */
    private function buildHTMLFormElementsSection() {
        
        $pageContent = $this->buildHelpNavigationMenu();
        $pageContent .= "<h2>Interactive HTML Form Elements Section</h2>";
        $pageContent .="<div id='middleColumn' class='ui-accordion-content ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 25px 15px 25px;'> " . $this->buildInteractiveFormElementsSection() . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function contructs the interactive button help.
     * \return Constructed interactive button content.
     */
    private function buildInteractiveFormElementsSection() {
        $mngHTMLFormsButton = "<button name='htmlforms' class='interactiveFormElementButtons' id='formElementHTMLFormsButton'>HTML Forms</button>";
        $mngLabelButton = "<button name='label' class='interactiveFormElementButtons' id='formElementLabelButton'>Label</button>";
        $mngRadioButton = "<button name='radio' class='interactiveFormElementButtons' id='formElementRadioButton'>Radio Button</button>";
        $mngDropDownListButton = "<button name='dropdown' class='interactiveFormElementButtons' id='formElementDropDownListButton'>Drop Down List</button>";
        $mngMSDropDownListButton = "<button name='msdropdown' class='interactiveFormElementButtons' id='formElementMSDropDownListButton'>Multi-Selectable Drop Down List</button>";
        $mngCheckBoxElementButton = "<button name='checkbox' class='interactiveFormElementButtons' id='formElementCheckBoxButton'>Check Box Element</button>";
        $mngTextInputButton = "<button name='textinput' class='interactiveFormElementButtons' id='formElementTextInputButton'>Text Input Field</button>";
        $mngTextAreaButton = "<button name='textarea' class='interactiveFormElementButtons' id='formElementtextAreaButton'>HTML and Text Area Fields</button>";
        $mngSubmitButton = "<button name='submitbuttons' class='interactiveFormElementButtons' id='formElementSubmitButtonButton'>Submit and Reset Button</button>";
        $mngHtmHeadingButton = "<button name='htmlheading' class='interactiveFormElementButtons' id='formElementhtmlHeadingButton'>HTML Headings</button>";
        $mngDatePickerButton = "<button name='datepicker' class='interactiveFormElementButtons' id='formElementDatePickerButton'>Date Picker Objects</button>";

        $interactiveButtonMessage = "<h3>These are all form elements provided by the form builder module.
Select any one of these form elements to learn more about them:</h3>";
        $interactiveButtonsSection = $mngHTMLFormsButton
                . $mngLabelButton
                . $mngRadioButton
                . $mngDropDownListButton
                . $mngMSDropDownListButton
                . $mngCheckBoxElementButton
                . $mngTextInputButton
                . $mngTextAreaButton
                . $mngSubmitButton
                . $mngHtmHeadingButton
                . $mngDatePickerButton;
        return $interactiveButtonMessage . $interactiveButtonsSection;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the meta data
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed form editor help content.
     */
    private function buildFormEditorPageContent($isSeperateHelpBool) {
        $pageContent = "";
        if ($isSeperateHelpBool != 1) {
            $pageContent = $this->buildHelpNavigationMenu();
        }
        $content = $this->dbUserHelpContent->listPageContent("form_editor_introduction");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_editor.png'
alt='The form editor layout is shown.'
title='The form editor layout is shown.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_editor_dropdown_list");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent .= "<h2>Interactive  HTML Form Elements Section</h2>";
        $pageContent .="<div id='middleColumn' class='ui-accordion-content ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 25px 15px 25px;'> " . $this->buildInteractiveFormElementsSection() . "</div>";


        $content = $this->dbUserHelpContent->listPageContent("form_editor_text_interactive_button_section");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_editor_with_example_form.png'
alt='An example form editor page is shown with a hypothetical built form.'
title='An example form editor page is shown with a hypothetical built form.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_editor_rearrange_form_elements_section");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_editor_rearrange_form_elements.png'
alt='An example form editor page is shown with a hypothetical built form and the “rearrange form elements” button toggled.'
title='An example form editor page is shown with a hypothetical built form and the “rearrange form elements” button toggled.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_editor_delete_form_elements_section_intro");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_editor_delete_form_elements.png'
alt='An example form editor page is shown with a hypothetical built form and the “delete form elements” button toggled.'
title='An example form editor page is shown with a hypothetical built form and the “delete form elements” button toggled.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_editor_delete_form_elements_section_confirmation_message");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_editor_confirm_element_delete_window.png'
alt='The form element delete confirmation dialog box is depicted.'
title='The form element delete confirmation dialog box is depicted.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_editor_done_button");
        $pageContent .= $content["0"]['pagecontent'];
        if ($isSeperateHelpBool != 1) {
            $pageContent.=$this->buildHelpNavigationMenu();
        }
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the form publishing
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed form publishing help content.
     */
    private function buildFormPublishingPageContent($isSeperateHelpBool) {
        $pageContent = "";
        if ($isSeperateHelpBool != 1) {
            $pageContent = $this->buildHelpNavigationMenu();
        }
        $content = $this->dbUserHelpContent->listPageContent("form_publisher_intro");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/publishing_window_closed.png'
alt='A publishing form modal window is shown.'
title='A publishing form modal window is shown.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_publisher_simple");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/publishing_window_simple_tab.png'
alt='A publishing form modal window is shown with simple publishing parameters.'
title='A publishing form modal window is shown with simple publishing parameters.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_publisher_advanced");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/publishing_window_chisimba_nextaction_function.png'
alt='This si Chisimba core function.'
title='This function comes from the Chisimba core.'><br>";
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/publishing_window_advanced_tab.png'
alt='A publishing form modal window is shown with advanced publishing parameters.'
title='A publishing form modal window is shown with advanced publishing parameters.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_publisher_final");
        $pageContent .= $content["0"]['pagecontent'];
        if ($isSeperateHelpBool != 1) {
            $pageContent.=$this->buildHelpNavigationMenu();
        }
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the form options
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed form options help content.
     */
    private function buildFormOptionsPageContent($isSeperateHelpBool) {
        $pageContent = "";
        if ($isSeperateHelpBool != 1) {
            $pageContent = $this->buildHelpNavigationMenu();
        }
        $content = $this->dbUserHelpContent->listPageContent("form_options_intro");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_listings_numbered.png'
alt='The page that lists all forms is shown with colour coded numbers to help denote each element within this page. '
title='The page that lists all forms is shown with colour coded numbers to help denote each element within this page. '>";
        $content = $this->dbUserHelpContent->listPageContent("form_options_pagination");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_listings_pagination_menu.png'
alt='A modal window that prompts the change of the default number of forms being listed in a page.  '
title='A modal window that prompts the change of the default number of forms being listed in a page.  '>";
        $content = $this->dbUserHelpContent->listPageContent("form_options_accordion_form_listing");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_listings_form_options_window.png'
alt='A modal window that lists all the possible commands for forms. '
title='A modal window that lists all the possible commands for forms. '>";
        $content = $this->dbUserHelpContent->listPageContent("form_options_accordion_form_settings");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_listings_form_general_settings.png'
alt='A modal window that shows designer form settings with the general information tab open.'
title='A modal window that shows designer form settings with the general information tab open.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_options_accordion_form_settings_simple");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_listings_form_simple_publishing_settings.png'
alt='A modal window that shows designer form settings with the simple publishing details tab open.'
title='A modal window that shows designer form settings with the simple publishing details tab open.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_options_accordion_form_settings_advanced");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/form_listings_form_advanced_publishing_settings.png'
alt='A modal window that shows designer form settings with the simple publishing details tab open.'
title='A modal window that shows designer form settings with the simple publishing details tab open.'>";
        $content = $this->dbUserHelpContent->listPageContent("form_options_accordion_form_publishing_and_submissions");
        $pageContent .= $content["0"]['pagecontent'];
        $pageContent.= "<img src='packages/formbuilder/resources/images/userManual/submission_results_overall_layout.png'
alt='The layout of the view submission records page is shown.'
title='The layout of the view submission records page is shown.'>";
        if ($isSeperateHelpBool != 1) {
            $content = $this->dbUserHelpContent->listPageContent("conclusion");
            $pageContent .= $content["0"]['pagecontent'];

            $pageContent.=$this->buildHelpNavigationMenu();
        }
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the text input form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \return Constructed text input form element help content.
     */
    private function buildTextInputFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("textinput");
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_text_input_window.png'
alt='The insert text input modal window is shown.'
title='The insert text input modal window is shown.'>";
        $content = $this->dbUserHelpContent->listPageContent("textinput_inserter");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $content["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the html forms
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \return Constructed html forms help content.
     */
    private function buildHTMLFormsFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("html_forms");
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $pageContent .= "<div id='secondTab'>" . "This is not a form element and therefore does no have a modal window." . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the text area form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed text area form element help content.
     */
    private function buildTextAreaFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("textarea");
        $pic = "<img src='packages/formbuilder/resources/images/userManual/html_area.png'
alt='A text area with some default text and the forms toolbar is shown.'
title='A text area with some default text and the forms toolbar is shown.'>";
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "<br>" . $pic . "</div>";
        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_text_area_window.png'
alt='The insert text area modal window is shown.'
title='The insert text area modal window is shown.'>";

        $content = $this->dbUserHelpContent->listPageContent("textarea_inserter");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $content["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the checkbox form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed checkbox form element help content.
     */
    private function buildCheckBoxFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("cehckbox");
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $pic = "<img src='packages/formbuilder/resources/images/userManual/insert_checkbox_confirmation.png'
alt='The “Add another Check Box Option” modal window is shown.'
title='The “Add another Check Box Option” modal window is shown.'>";

        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_checkbox_window.png'
alt='The insert check box option modal window is shown.'
title='The insert check box option modal window is shown.'>";

        $contentintro = $this->dbUserHelpContent->listPageContent("checkbox_inserter_intro");
        $contentbody = $this->dbUserHelpContent->listPageContent("checkbox_inserter_body");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $contentintro["0"]['pagecontent'] . "<br>" . $pic . "<br>" . $contentbody["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the drop down list form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed drop down list form element help content.
     */
    private function buildDropDownFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("dropdown");
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $pic = "<img src='packages/formbuilder/resources/images/userManual/insert_checkbox_confirmation.png'
alt='The “Add another Check Box Option” modal window is shown.'
title='The “Add another Check Box Option” modal window is shown.'>";

        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_dropdown_window.png'
alt='The insert drop down option modal window is shown.'
title='The insert drop down option modal window is shown.'>";

        $contentintro = $this->dbUserHelpContent->listPageContent("dropdown_inserter_intro");
        $contentbody = $this->dbUserHelpContent->listPageContent("dropdown_inserter_body");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $contentintro["0"]['pagecontent'] . "<br>" . $pic . "<br>" . $contentbody["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the radio button form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed radio button form element help content.
     */
    private function buildRadioFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("radio");
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $pic = "<img src='packages/formbuilder/resources/images/userManual/insert_checkbox_confirmation.png'
alt='The “Add another Check Box Option” modal window is shown.'
title='The “Add another Check Box Option” modal window is shown.'>";

        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_radio_window.png'
alt='The insert radio option modal window is shown.'
title='The insert radio option modal window is shown.'>";

        $contentintro = $this->dbUserHelpContent->listPageContent("radio_inserter_intro");
        $contentbody = $this->dbUserHelpContent->listPageContent("radio_inserter_body");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $contentintro["0"]['pagecontent'] . "<br>" . $pic . "<br>" . $contentbody["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the ms dropdown list form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed ms drop down list form element help content.
     */
    private function buildMSDropDownFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("msdropdown");
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $pic = "<img src='packages/formbuilder/resources/images/userManual/insert_checkbox_confirmation.png'
alt='The “Add another Check Box Option” modal window is shown.'
title='The “Add another Check Box Option” modal window is shown.'>";

        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_msdropdown_window.png'
alt='The insert radio option modal window is shown.'
title='The insert radio option modal window is shown.'>";

        $contentintro = $this->dbUserHelpContent->listPageContent("msdropdown_inserter_intro");
        $contentbody = $this->dbUserHelpContent->listPageContent("msdropdown_inserter_body");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $contentintro["0"]['pagecontent'] . "<br>" . $pic . "<br>" . $contentbody["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the datepicker object form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed datepicker object form element help content.
     */
    private function buildDatePickerFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("datepicker");
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_datepicker_window.png'
alt='The insert date picker object modal window is shown.'
title='The insert date picker object option modal window is shown.'>";

        $content = $this->dbUserHelpContent->listPageContent("datepicker_inserter");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $content["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the html heading form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed html heading form element help content.
     */
    private function buildHTMLHeadingFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("htmlheading");
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_html_heading_window.png'
alt='The insert html heading modal window is shown.'
title='The insert html heading modal window is shown.'>";

        $content = $this->dbUserHelpContent->listPageContent("htmlheading_inserter");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $content["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the label form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed label form element help content.
     */
    private function buildLabelFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("label");
        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_label_window.png'
alt='The insert label modal window is shown.'
title='The insert label modal window is shown.'>";

        $content = $this->dbUserHelpContent->listPageContent("label_inserter");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $content["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function pulls the relavent entries from help content
     * database and the resources folder to construct the button form element
     * help content.
     * \note The database contains text while the resources folder contains
     * images.
     * \warning Do not move or rename the images folder in the resources
     * folder.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed button form element help content.
     */
    private function buildButtonFormElementPageContent() {
        $pageContent = "";
        $content = $this->dbUserHelpContent->listPageContent("button");


        $pageContent .= "<div id='firstTab'>" . $content["0"]['pagecontent'] . "</div>";
        $pic = "<img src='packages/formbuilder/resources/images/userManual/insert_checkbox_confirmation.png'
alt='The “Add another Check Box Option” modal window is shown.'
title='The “Add another Check Box Option” modal window is shown.'>";

        $picContent = "<img src='packages/formbuilder/resources/images/userManual/insert_button_window.png'
alt='The insert button modal window is shown.'
title='The insert button modal window is shown.'>";

        $contentintro = $this->dbUserHelpContent->listPageContent("button_inserter_intro");
        $contentbody = $this->dbUserHelpContent->listPageContent("button_inserter_body");
        $pageContent .= "<div id='secondTab'>" . $picContent . "<br>" . $contentintro["0"]['pagecontent'] . "<br>" . $pic . "<br>" . $contentbody["0"]['pagecontent'] . "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member function provides an interface to all the form help content.
     * This is the only member function that gets called from the
     * outside to get any help content.
     * \param contentType A string. This the content you want. Make sure
     * that it exists and it is correct or else you will get an error.
     * \param isSeperateHelpBool A boolean. Select false if you want
     * to construct this content for the main help page or true of you
     * want to construct it for a seperate page within the form builder.
     * \return Constructed any constructed help content.
     */
    public function showContent($contentType, $isSeperateHelpBool) {
        switch ($contentType) {

            case 'intro':

                return $this->buildIntroPageContent();
                break;
            case 'metadata':
                return $this->buildFormMetaDataPageContent($isSeperateHelpBool);
                break;
            case 'formeditor':
                return $this->buildFormEditorPageContent($isSeperateHelpBool);
                break;
            case 'formpublisher':
                return $this->buildFormPublishingPageContent($isSeperateHelpBool);
                break;
            case 'formoptions':
                return $this->buildFormOptionsPageContent($isSeperateHelpBool);
                break;
            case 'textinput':
                return $this->buildTextInputFormElementPageContent();
                break;
            case 'textarea':
                return $this->buildTextAreaFormElementPageContent();
                break;
            case 'checkbox':
                return $this->buildCheckBoxFormElementPageContent();
                break;
            case 'dropdown':
                return $this->buildDropDownFormElementPageContent();
                break;
            case 'radio':
                return $this->buildRadioFormElementPageContent();
                break;
            case 'msdropdown':
                return $this->buildMSDropDownFormElementPageContent();
                break;
            case 'datepicker':
                return $this->buildDatePickerFormElementPageContent();
                break;
            case 'htmlheading':
                return $this->buildHTMLHeadingFormElementPageContent();
                break;
            case 'label':
                return $this->buildLabelFormElementPageContent();
                break;
            case 'submitbuttons':
                return $this->buildButtonFormElementPageContent();
                break;

            case 'htmlforms':
                return $this->buildHTMLFormsFormElementPageContent();
                break;
            case 'htmlformelements':
                return $this->buildHTMLFormElementsSection();
                break;


            default:
                return "Error. Page Content Type does not exist.";
                break;
        }
    }

}
?>

