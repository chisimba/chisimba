<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

include_once 'chisimba_modules_handler_class_inc.php';
/*!  \class dropdown_module
 *
 *  \brief Class that models a drop down object from the chisimba core modules.
 *  \brief It basically an interface class between the hosportal module and the chisimba core modules.
 *  \brief This creates less dependancy on chisimba and increases flexibility and maintainability.
 * \brief This drop down object lets you create a drop down box with options. Once you have
 * selected an option. You can pass its name as a parameter so you can use the
 * getParam(variable to be stored,parameter) to do what you like with that selected option in other entities.
 *   \author Salman Noor
 *  \author MIU Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 0.68
 *  \date    May 3, 2010
 * \warning Do NOT pass variable types as parameters that are not specified in this class
*/

class dropdown_module extends chisimba_modules_handler
{

     /*!
    * \brief Private data member of dropdown form_module that stores an object of another class.
    * \brief This class is composed of one object from the dropdown class in the html elements core module of chisimba.
     * \brief This object models one drop down box object.
    */
private $objDropDownMenu;

    /**
     *\brief Standard constructor that sets up this class. The contrustor is
     * creating the class's private data members.
     *
     */
    public function init()
    {
        //Get an object of the drop down class in the html elements module.
        ///The object is still has not been instatiated yet.
        ///We need to name this object so we use the new operator in the
        ///createNewObjectFromModule member function.
        $this->objDropDownMenu = $this->getObject('dropdown', 'htmlelements');
         //$this->objButton= $this->loadClass('button','htmlelements');

    }

        /**
     *\brief Creates an object from the chisimba core modules
     * \param name_of_drop_down A string with default value noOfMessagesDropDown.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return A new drop down object with a specific name to be used.
     */
    public function createNewObjectFromModule($name_of_drop_down= 'noOfMessagesDropDown')
    {
    return $this->objDropDownMenu=&new dropdown('noOfMessagesDropDown');
}

    /**
     *\brief In the future if required, this drop down module can be modified or augmented
     * using this member function.
     */
public function EditModule()
{
    ///Currently it is not implemented.
}

    /**
     *\brief Member function to insert items in the drop down object.
     * \param name_of_option A string. Can be used as a parameter in other entities.
     * \param label_for_option A string. The string will be displayed as an option.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return An inserted option in the drop down object.
     */
public function insertOptionIntoDropDown($name_of_option,$label_for_option)
        {
    ///Returns a member function from the drop down class of the chisimba core modules.
return $this->objDropDownMenu->addOption($name_of_option,$label_for_option);
      /// If you call this method without any arguments, you would not be setting anything.
        }

     /**
     *\brief Member function to set the default option for the drop down box.
     * \param default_option A string. This argument is the same the $name_of_option
      * from the insertOptionIntoDropDown member function.
     * \warning Do NOT pass option that is not already stored in the drop down box.
     * \return An default selected option in the drop down object.
     */
public function setDefaultOptionForDropDown($default_option)
        {
       ///Returns a member function from the drop down class of the chisimba core modules.
    ///When the drop down is display. It will show the default option always unless
    ///the user changes it.
return $this->objDropDownMenu->setSelected($default_option);
    /// If you call this method without any arguments, you would not be setting anything.
        }

            /**
     *\brief Method to display the drop down box.
     * \return A member function from the drop down class of the chisimba core modules.
     */
public function showBuildDropDownMenu()
{
     ///Once the drop down object has been completely modelled,
        ///this can allow you to display it.
    return $this->objDropDownMenu->show();
}


}


?>