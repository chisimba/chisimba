<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

include_once 'chisimba_modules_handler_class_inc.php';

/*!  \class form_module
 *
 *  \brief Class that models a form object from the chisimba core modules.
 *  \brief It basically an interface class between the hosportal module and the chisimba core modules.
 *  \brief This creates less dependancy on chisimba and increases flexibility and maintainability.
 * \brief This form object lets you add numerous different types of objects and fields. Once you have
 * inputted all the data required in those objects. You can set this form to be submitted. Once submitted,
 * all the inputted data is converted into parameters in which you can use the string query function
 * getParam(variable to be stored,parameter) to do what you like with them in other entities.
 *   \author Salman Noor
 *  \author MIU Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 0.68
 *  \date    May 3, 2010
 * \warning Do NOT pass variable types as parameters that are not specified in this class
*/
class form_module extends chisimba_modules_handler {
    /*!
    * \brief Private data member of class form_module that stores an object of another class.
    * \brief This class is composed of one object from the form class in the html elements core module of chisimba.
     * \brief This object models one form object.
    */
    private $objform;
    
    /**
     *\brief Standard constructor that sets up one object of this class.
     */
    public function init() {
        ///If the class you want to use is in another module,
        ///then you have to load that class from the specific module.
        $this->objform= $this->loadClass('form','htmlelements');
    }

    /**
     *\brief Creates an object from the chisimba core modules
     * \param name_of_form A string with default value set to messages.
     * \param form_action The variable tpye can be a string or an url array default as null. Once the form is
     * submitted, it will do what the variable form action is set to do.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return A new form object to be used.
     */
    public function createNewObjectFromModule($name_of_form= 'messages' , $form_action=NULL) {
        ///Return an new empty form object to add other objects inside it.
        return   $this->objForm = new form($name_of_form, $form_action);
    }

    /**
     *\brief In the future if required, this confirm module can be modified or augmented
     * using this member function. Currently empty.
     */
    public function EditModule() {
    }

    /**
     *\brief Member function to allow you to add whatever object you want inside it.
     * \param object_to_be_added It can be any variable type. Once added it will be displayed wtihin the form.
     * Examples would be objects like links, text areas,drop downs, icons, buttons and even normal strings.
     * \return A beefed up form object that is ready to be displayed and submitted.
     */
    public function addObjectToForm($object_to_be_added) {
        ///Returns a member function from the form class of the chisimba core modules.
        return $this->objForm->addToForm($object_to_be_added);
        /// If you call this method without any arguments, you would not be adding anything.
    }

    /**
     *\brief Method to display the form with all its added elements.
     * \return A member function from the form class of the chisimba core modules.
     */
    public function showBuiltForm () {
        ///Once the form object has been completely modelled,
        ///this can allow you to display it.
        return $this->objForm->show();
    }

}

?>