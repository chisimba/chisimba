<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/*!  \class button_module
 *
 *  \brief Class that models a button from the chisimba core modules
 *  \brief It basically an interface class between the hosportal module and the chisimba core modules.
 *  \brief This creates less dependancy on chisimba and increases flexibility and maintainability
 *  \author Salman Noor
 *  \author MIU Intern
 *          School of Electrical Engineering, WITS Unversity
 *  \version 0.68
 *  \date    May 3, 2010
 * \warning Do NOT pass variable types as parameters that are not specified in this class
*/

include_once 'chisimba_modules_handler_class_inc.php';

class button_module extends chisimba_modules_handler {

    /*!
    * \brief private data member of class button_module
    * \brief This class is composed of one object from the button class in of the html module of chisimba
     * \brief This object models a button
    */
    private $objButton;

    /**
     *\brief Standard Chisimba constructor that loads a class from an external module
     *
     */
    public function init() {
        ///If the class is from an external module it has to be loaded first
        $this->objButton= $this->loadClass('button','htmlelements');
        ///Load the class button from the html elements module
    }

    /**
     *\brief Creates an object from the chisimba core modules
     * \param name_of_button A string with defult value no name
     * \param Id_value A query string that can be passed as a parameter with defult value set to null
     * \param on_click A string that contiains an action that can be passed as a parameter with defult value set to null
     * \warning Do NOT pass variable types as parameters that are not specified in this member function
     * \return A new button empty button object
     */
    public function createNewObjectFromModule($name_of_button= 'NoName' , $Id_value=NULL, $on_click=NULL) {

        ///return an new empty button object
        return   $this->objButton = new button($name_of_button, $Id_value, $on_click);
        ///If arguments are not set, A new button with a label NoName without any functionality will be created
    }

    /**
     *\brief In the future if required, this button module can be modified or augmented
     */
    public function EditModule() {
    }
    /**
     *\brief If called upon, it sets the button object to submit a form
     * \return A member function from the button class of the chisimba core modules
     */
    public function buttonSetToSubmit() {
        ///Returns a member function from the button class of the chisimba core modules
        return $this->objButton->setToSubmit();
    }

    /**
     *\brief Sets the label of the button object
     * \param label_of_button A string
     * \param Id_value A query string that can be passed as a parameter with defult value set to null
     * \param on_click A string that contiains an action that can be passed as a parameter with defult value set to null
     * \warning Do NOT pass variable types as parameters that are not specified in this member function
     * \return A member function from the button class of the chisimba core modules
     */
    public function setButtonLabel($label_of_button) {
        ///Return a member function from the button class of the chisimba core modules
        return $this->objButton->setValue($label_of_button);
    }

    /**
     *\brief Method to display the button
     * \return A member function from the button class of the chisimba core modules
     */
    public function showButton() {
        ///Once the button object has been completely modeled
        ///this can allow you to display it
        return $this->objButton->show();
    }
}

?>