<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class confirm_module
 *
 *  \brief Class that models a confrim option window pop up from the chisimba core modules.
 *  \brief It basically an interface class between the hosportal module and the chisimba core modules.
 *  \brief This creates less dependancy on chisimba and increases flexibility and maintainability.
 * \brief This confirm object is a small popup window that asks a question with an OK or Cancel button
 *  \author Salman Noor
 *  \author MIU Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 0.68
 *  \date    May 3, 2010
 * \warning Do NOT pass variable types as parameters that are not specified in this class
*/

include_once 'chisimba_modules_handler_class_inc.php';

class confirm_module extends chisimba_modules_handler {

    /*!
    * \brief Private data member of class confirm_module that stores a object of another class.
    * \brief This class is composed of one object from the confirm class in the utilities module of chisimba.
     * \brief This object models a confirm pop up window object.
    */
    private $objConfirm;

    /**
     *\brief Standard constructor that set up this class. Nothing is being intialised in
     * the constructor.
     *
     */
    public function init() {

    }

    /**
     *\brief Creates an object from the chisimba core modules
     * \param name_of_class A string with default value set to no other than the confirm class.
     * \param name_of_module A string with default value set to the module in which the confirm class is stored.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return A new confirm object to be used.
     */
    public function createNewObjectFromModule($name_of_class = 'confirm',$name_of_module = 'utilities') {
        ///Return an new empty confirm action object.
        return $this->objConfirm = &$this->newObject($name_of_class, $name_of_module);
        ///If you called this method without any arguments (parameters), don't sweat it, the default arguments
        ///will take care of everything.
    }

    /**
     *\brief Member function give the confirm object functionality and substance.
     * \param link It can be any variable type for the link in which the confirm object will be triggered and displayed.
     * Examples would be objects like links, icons, buttons and even normal strings.
     * \param url It is a variable type string or an url array. If the OK button is clicked, then it will go to this url.
     * \param message It is a variable type string. It will set and display whatever string you want to display.
     * \param extra It is a variable any variable type. If the OK button is clicked, then it can perform any function desired.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return A beefed up confirm object that is ready to be displayed when triggered.
     */
    public function setConfirmOptions($link=NULL,$url=NULL,$message=NULL,$extra=NULL) {
        ///Returns a member function from the confirm class of the chisimba core modules.
        return $this->objConfirm->setConfirm($link,$url,$message,$extra);
        /// If you call this method without any arguments, you would not be setting anything.
    }

    /**
     *\brief Method to display the confirm pop up window when it is triggered by the $link variable.
     * \return A member function from the confirm class of the chisimba core modules.
     */
    public function showConfirmMessage() {
        ///Once the confirm message object has been completely modelled,
        ///this can allow you to display it whenever it is triggered.
        return $this->objConfirm->show();
    }
    
    /**
     *\brief In the future if required, this confirm module can be modified or augmented
     * using this member function.
     */
    public function EditModule() {
    }





}


?>