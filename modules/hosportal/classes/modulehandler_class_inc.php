<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("you cannot view this page directly");
}
/*!  \class modulehandler
*
*  \brief Class that manages the module
*  \brief It basically either gives control to the text file output command or
*  \brief gives control to the forum handler.
*  \author Salman Noor
*  \author MIU Intern
*          School of Electrical Engineering, WITS Unversity
*  \version 0.68
*  \date    May 3, 2010
*  \bug Possible bugs are text file handling.
*  \bug If a text file does not exist, chisimba will output warnings.
*  \warning Make sure that all text files are in the text files folder.
*  \warning Do NOT rename or delete the text files as their names are arguments to this class.
*  \warning Do NOT change the text files path.
* \warning Make sure that the text files are in HTML format or it will display gargligok.
*/

class modulehandler extends controller {

    /*!
* \brief private data member of class module handler
* \brief This class is composed of one object from the class \ref messages_handler
* \brief This object manages the forum
    */
    private $objMessagesHandler;

    /*!
* \brief private data member of class module handler
* \brief This class is composed of one object from the class \ref language_module
    */
    private $objLanguage;

    /**
     *\brief Standard Chisimba constructor that instatiates it private
     * data members
     * \brief This object manages the output of text to the screen
     */
    public function init() {
///Instatiate on object from the class \ref language_module
        $this->objLanguage = $this->getObject('language_module','hosportal');
///Instatiate on object from the class \ref messages_handler
        $this->objMessagesHandler = $this->getObject('messages_handler','hosportal');
    }

    /**
     * \brief public member function that is solely used in \ref hosportal
     *  class that gets actions from various classes
     *  \return A template determined by the method resulting from action
     */
    public function manageEvents() {
///Get action from query string and set default to display content
        $action=$this->getParam('action', 'displayContent');
///Convert the action into a method
        $method = $this->__getMethod($action);
///Return the template determined by the method resulting from action

        return $this->$method($action);
    }

    /**
     * \brief checks if the action exists
     *  \return A boolean value
     * \param action a string
     */
    private function __validAction(& $action) {
///Checks if the methods exists and returns a simple boolean value

        if (method_exists($this, "__".$action)) {
/// Method exists in a member function of the controller class from the chisimba core
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * \brief Get an action
     * \param action A string
     *  \return the method that corresponds to the action
     */
    private function __getMethod( $action) {
///Checks using __validAction if the action exists and returns the corresponding method
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
///If methos does not exist return the method __view which takes you to the messages handler class
            return "__viewForum";
        }
    }

    /**
     * \brief Get text file and use a template file to display it
     * \param action A string
     *  \returns template file displayMiddleColumnContent_tpl.php
     */
    private function __displayContent($action) {

///Get text file from query string from the side_navigation_links_handler_class_inc.php and set default to home.txt if it does not exits
        if ($this->getParam('textFile')=='')
///The member function getParam is a member function of the controller class
        {
            $txtFile = home;
        }
        else {
            $txtFile=$this->getParam('textFile');
        }
///Set the file path
        $myFile = "packages/hosportal/text files/$txtFile.txt";
///Open the file
        $fh = fopen($myFile, 'r');
///Read from the entire file until its end and store the data into a temporary variable
        $theData = fread($fh, filesize($myFile));
///Close the file
        fclose($fh);
//Set the temporary variable into a query string so the template file can use it

        $this->setVar('theData', $theData);
///The member function setVar is from the controller class of chisimba's core
        return 'displayMiddleColumnContent_tpl.php';
    }

    /**
     * \brief Member function that hands over control to the messages_handler class
     * \param action A string
     *  \returns a public member function of the class messages_handler
     */
    private function __viewForum($action) {

///Returns the forum's current action and converts it into a method
        return $this->objMessagesHandler->messagesCurrentAction($action);
    }

}

?>