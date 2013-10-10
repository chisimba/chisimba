<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class htmlarea_module
*
*  \brief Class that models a html area object from the chisimba core modules.
*  \brief It basically an interface class between the hosportal module and the chisimba core modules.
*  \brief This creates less dependancy on chisimba and increases flexibility and maintainability.
* \brief This html area object produces a small simple word proccessor type object that lets
* type in text and select numerous options that are inate to most other word proccesors.
*   \author Salman Noor
*  \author MIU Intern
*  \author School of Electrical Engineering, WITS Unversity
*  \version 0.68
*  \date    May 3, 2010
* \warning Do NOT pass variable types as parameters that are not specified in this class
*/

include_once 'chisimba_modules_handler_class_inc.php';

class htmlarea_module extends chisimba_modules_handler {
    /*!
* \brief Private data member of class htmlarea_module that stores an object of another class.
* \brief This class is composed of one object from the html area class in the html elements core module of chisimba.
* \brief This object models one html area object.
    */
    private $HTMLArea;

    /**
     *\brief Standard constructor that set up this class. Nothing is being intialised in
     * the constructor.
     *
     */
    public function init() {
    }

    /**
     *\brief Creates an object from the chisimba core modules
     * \param name_of_class A string with default value set to no other than the html area class.
     * \param name_of_module A string with default value set to the module in which the confirm class is stored ie html elements.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return A new html area object to be used.
     */
    public function createNewObjectFromModule($name_of_class = 'htmlarea',$name_of_module = 'htmlelements') {
///Return an new empty confirm action object.
        return $this->HTMLArea = $this->newObject($name_of_class, $name_of_module);
///If you called this method without any arguments (parameters), don't sweat it, the default arguments
///will take care of everything.
    }

    /**
     *\brief Member function to set the name of the variable the text will be eqauted to.
     * Once this is set, then it can be used as a parameter anywhere else.
     * \param name_of_input_variable A string. The name of the variable will become a parameter.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return Text that is inputted will be stored in this parameter.
     */
    public function setInputVariableForHTMLArea($name_of_input_variable) {
///Returns a member function from the confirm class of the chisimba core modules.
        return  $this->HTMLArea->setContent($name_of_input_variable);
/// If you call this method without any arguments, you would not be setting anything.
    }

    /**
     *\brief Member function to set the name of the object the text will be eqauted to.
     * Once this is set, then it can be used as a parameter anywhere else.
     * \param name_of_HTML_area A string. The name of the variable will become a parameter.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return Text that is inputted will be stored in this parameter.
     */
    public function setHTMLAreaName($name_of_HTML_area) {
///Returns a member function from the confirm class of the chisimba core modules.
        return  $this->HTMLArea->setName($name_of_HTML_area);
/// If you call this method without any arguments, you would not be setting anything.
    }

    /**
     *\brief Member function to set the tool bar type. The default tool bar that
     * has the most options is selected. If you want to change this toolbar
     * then choose another member function from the html area class in
     * the html elements module.
     * \return A toolbar that will be displayed on top of the text area.
     */
    public function setToolBarType() {
///Returns a member function from the confirm class of the chisimba core modules.
        return  $this->HTMLArea->setDefaultToolBarSet();
    }

    /**
     *\brief Method to display the html area object.
     * \return A member function from the html area class of the chisimba core modules.
     */
    public function showHTMLArea() {
///Once the html area object has been completely modelled,
/// then called this method.
        return $this->HTMLArea->showFCKEditor();
    }

    /**
     *\brief In the future if required, this html area module can be modified or augmented
     * using this member function.
     */
    public function EditModule() {
///Nothing is implemented currently.
    }

}


?>