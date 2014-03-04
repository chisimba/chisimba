<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*! \class chisimba_modules_handler
 *
 *  \brief Abstract Base Class ABC that groups the same of objects together
 *  \brief This class groups the classes with the same properities.
 *  \brief This class groups all interface classes that model objects from the chisimba core modules
 *  \brief This creates less dependancy on chisimba and increases flexibility and maintainability
 *
 *  \author Salman Noor
 *  \author MIU Intern
 *          School of Electrical Engineering, WITS Unversity
 *  \version 0.68
 *  \date    May 3, 2010
 * \warning Do NOT rename the abstract member functions as they have to be renamed in >14 other classes
 * \note This class inherits for the object class of chisimba's core to manage objects with ease.
*/

abstract class chisimba_modules_handler extends object {
    /*!
    * \brief Abstract public data member of that can be implemented in other classes
    * \brief As all chisimba module objects have to be created this AB function
     * \brief provides extensibility
    */
    abstract public function createNewObjectFromModule(
    ///Reimplemented in most child classes
    );

    /*!
    * \brief Abstract public data member of that can be implemented in other classes
     * \brief provides exisitng chisimba modules with alterations and augmentation
    */
    abstract public function EditModule(
    ///Not used in most child classes.
    ///This member function exists if required in the future
    );

}

?>