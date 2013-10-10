<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_element_inserter
 *
 *  \brief Class that models the drop down that contains all possible
 * form elements that can be inserted by the user.
 *   \author Salman Noor
 *  \author BIS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 0.01
 *  \date    July 9, 2010
 * \note If any new form elements are requierd to be added, this class
 * is the pace to do it.
 * \warning This class utilizes the functionality of its parent class
 * object from the chisimba core to manage class objects with ease.
 * If the object class is altered in the future, this class may not function.
 */
class form_element_inserter extends object {

    /*!
     * \brief Private data member of class form_element_inserter
     * \brief This class is composed of one object from the class language belonging
     * to the module language inside the chisimba core modules.
     * \brief This object manages the output of text and variables to the screen
     */
    private $objOuputText;

    /*!
     * \brief Private data member of class form_element_inserter
     * \brief This class is composed of one object from the class dropdown belonging
     * to the module htmlelements inside the chisimba core modules.
     * \brief This object allows the modelling of a simple drop down with ease.
     */
    private $formElementInserterDropDown;

    /*!
     * \brief Private data member of class form_element_inserter
     * \brief This class is composed of one array to store all
     * possible the form elements.
     */
    private $formElementArray;

    /*!
     * \brief Standard Chisimba constructor that instatiates the class's private
     * data members.
     */
    public function init() {

///Instatiate an object from the class language belonging to the module
///language.
        $this->objOuputText = $this->getObject('language', 'language');
///The member function getObject resides in the chisimba core class 'object'.
///Load the class drop down from the module htmlelements. The member function
///loadClass belongs to the chisimba core class 'object'.
        $this->loadClass('dropdown', 'htmlelements');

///Instatiate an object from the class dropdown belonging to the module
///htmlelements.

        $this->formElementInserterDropDown = &new dropdown('add_form_elements_drop_down');

///Call a private member function to initialize a private data member that stores
///all default form elements into an array.
        $this->initializeFormElementArray();
    }

    /*!
     * \brief Private member function that is called by the constructor to
     * set up the array that contains all form elements.
     * \note If a new form element is needed to be added, this is the member
     * function is the place to do it.
     * \warning Do not change the labels or names of the form elements as
     * other classes will not recognize them.
     */
    private function initializeFormElementArray() {
///Define the private data member formElementArray to be an array
///and store all the default possible form elements in the array.
        $this->formElementArray = array(
            "default" => $this->objOuputText->languageText('mod_formbuilder_dddefaultoption', 'formbuilder'),
            "label" => $this->objOuputText->languageText('mod_formbuilder_ddlabel', 'formbuilder'),
            "radio" => $this->objOuputText->languageText('mod_formbuilder_ddradio', 'formbuilder'),
            "drop_down" => $this->objOuputText->languageText('mod_formbuilder_dddropdown', 'formbuilder'),
            "multiselect_drop_down" => $this->objOuputText->languageText('mod_formbuilder_ddmultiselectdropdown', 'formbuilder'),
            "check_box" => $this->objOuputText->languageText('mod_formbuilder_ddcheckbox', 'formbuilder'),
            "text_input" => $this->objOuputText->languageText('mod_formbuilder_ddtextinput', 'formbuilder'),
            "text_area" => $this->objOuputText->languageText('mod_formbuilder_ddtextarea', 'formbuilder'),
            "button" => $this->objOuputText->languageText('mod_formbuilder_ddsubmitbutton', 'formbuilder'),
            "form_heading" => $this->objOuputText->languageText('mod_formbuilder_ddformheading', 'formbuilder'),
// "form_text" => $this->objOuputText->languageText('mod_formbuilder_ddtext', 'formbuilder'),
            "date_picker" => $this->objOuputText->languageText('mod_formbuilder_dddatepicker', 'formbuilder')
        );
///The array index will be the drop down value while actual array value will be the 
///drop down label.
    }

    /*!
     * \brief Private member function that builds the drop down menu.
     *  \return A completed drop down menu ready to be used.
     */
    private function buildFormElementInserterDropDown() {
///Loop through form element array and insert all the possible options
///inside the drop down
        foreach ($this->formElementArray as $dropDownValue => $dropDownLabel) {
            $this->formElementInserterDropDown->addOption($dropDownValue, $dropDownLabel);
        }

///Set the default option 'Select and Insert a Form Entity...' as the selected value
        $this->formElementInserterDropDown->setSelected('default');

///Once completely built, return the drop down to be displayed
        return $this->formElementInserterDropDown->show();
    }

    /*!
     * \brief Public member function that shows the drop down menu.
     * \brief Once the drop down menu has been built, this member function
     * displays the built content.
     * \note This member function has to be called if you want to display
     * the content modelled in this class.
     * \return A member function buildFormElementInserterDropDown() within this class.
     */
    public function showFormElementInserterDropDown() {
        return $this->buildFormElementInserterDropDown();
    }

}

?>