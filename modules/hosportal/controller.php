<?php
/*! \mainpage Hosportal
 *
 *  \brief The module contains a instant message type system and has
 * \brief capability to output text when a certain links are clicked on.
 *
 *  \author Salman Noor
 *  \author MIU Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 0.68
 *  \date    May 3, 2010
 *  \bug Possible bugs are text file handling (Not enough error handling).
 *  \bug There might be some minor logical bugs hidden when the user does some obsqure things on instant messaging or forum system.
 *  \warning This module inherits from core classes in chisimba platform. Chisimba has to be installed.
 *  \warning If the chisimba core classes are altered in the future, this module might not work. However, lots of preventative
 * measures have been taken to curb this limitation.
 *  \note If you are new to this module, it is recommended that you start from the \ref hosportal main class
*/

/*!  \class hosportal
 *
 *  \brief Main class that runs everything.
 *  \brief It is the main class that instantiates the entire module.
 * \brief It can be though of as a client class that uses the actual module
 *  \author Salman Noor
 *  \author MIU Intern
 *          School of Electrical Engineering, WITS Unversity
 *  \version 0.68
 *  \date    May 3, 2010
 * \note Client class has only 8 lines of code. Client does not have to do a lot of work to run this module.
 *  \warning This class inherits from core classes in chisimba
 *  \warning If the chisimba core class is altered in the future, this module might not work
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("you cannot view this page directly");
}

class hosportal extends controller {




    /*!
    * \brief Private data member of class hosportal to store an object of a class.
    * \brief This class is composed of one object from the class \ref modulehandler.
    */
    private $objModuleHandler;

    /**
     *\brief Standard Chisimba constructor to set the default value of the
     * entire module. It this case, it is just creating a module object.
     */
    public function init() {
///Instatiate an object from the class modulehandler that runs the entire module.
        $this->objModuleHandler = $this->getObject('modulehandler','hosportal');

    }
    /**
     * \brief Standard controller dispatch method, the dispatch calls any
     *  method involving logic and hands of the results to the template for display.
     *  \return A member function in objModuleHandler
     */
    public function dispatch() {
        ///Return a member function from module handler that manages
        /// the output of template or tpl files
        return $this->objModuleHandler->manageEvents();

    }
}
?>