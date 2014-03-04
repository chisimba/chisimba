<?php

/**
 * 
 * My notes
 * 
 * Take notes, organize them by tags, keep them private or share them with your friends, all user, or the world.
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   mynotes
 * @author    Nguni Phakela nguni52@gmail.com
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         * 
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *         
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * 
 * Controller class for Chisimba for the module mynotes
 *
 * @author Derek Keats
 * @package mynotes
 *
 */
class mynotes extends controller {

    /**
     * 
     * @var string $objConfig String object property for holding the 
     * configuration object
     * @access public;
     * 
     */
    public $objConfig;

    /**
     * 
     * @var string $objLanguage String object property for holding the 
     * language object
     * @access public
     * 
     */
    public $objLanguage;

    /**
     *
     * @var string $objLog String object property for holding the 
     * logger object for logging user activity
     * @access public
     * 
     */
    public $objLog;

    /**
     * 
     * Intialiser for the mynotes controller
     * @access public
     * 
     */
    public function init() {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        // Create an instance of the database class
        $this->objDbmynotes = & $this->getObject('dbmynotes', 'mynotes');
        $this->objDbtags = & $this->getObject('dbtags', 'mynotes');
        //$this->appendArrayVar('headerParams', $this->getJavaScriptFile('mynotes.js', 'mynotes'));
        $this->appendArrayVar('headerParams', "<link href=\"" . $this->getResourceUri('css/mynotes.css', 'mynotes') . "\" rel='stylesheet' type='text/css'/>");
        //Get the activity logger class
        $this->objLog = $this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
        $this->objNoteOps = $this->getObject('noteops', 'mynotes');
        //$this->setVar('SUPPRESS_JQUERY', TRUE);
        //$this->setVar('JQUERY_VERSION', '1.7.1');
        //$this->appendArrayVar('headerParams', $this->getJavaScriptFile('1.7.1/jquery-1.7.1.min.js', 'jquery'));
    }

    /**
     * 
     * The standard dispatch method for the mynotes module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch() {
        //Get action from query string and set default to view
        $action = $this->getParam('action', 'view');
        /*
         * Convert the action into a method (alternative to 
         * using case selections)
         */
        $method = $this->__getMethod($action);
        // Set the layout template to compatible one
        $this->setLayoutTemplate('layout_tpl.php');
        /*
         * Return the template determined by the method resulting 
         * from action
         */
        return $this->$method();
    }

    /* ------------- BEGIN: Set of methods to replace case selection ------------ */

    /**
     * 
     * Method corresponding to the view action. It shows the default
     * dynamic canvas template
     * @access private
     * 
     */
    private function __view() {
        return "main_tpl.php";
    }

    /**
     * 
     * Method corresponding to the view action. It shows the default
     * dynamic canvas template
     * @access private
     * 
     */
    private function __viewall() {
        return "main_tpl.php";
    }

    /**
     * 
     * Method corresponding to the edit action. It sets the mode to 
     * edit and returns the edit template.
     * @access private
     * 
     */
    private function __edit() {
        $this->setVar('mode', 'edit');
        return 'editform_tpl.php';
    }

    /**
     * 
     * Method corresponding to the add action. It sets the mode to 
     * add and returns the edit content template.
     * @access private
     * 
     */
    private function __add() {
        $this->setVar('mode', 'add');
        return 'editform_tpl.php';
    }

    /**
     * 
     * Method to validate whether the information that the user has entered is
     * what they expect it to be before saving their note
     * @access private
     * 
     */
    private function __validatenote() {
        $mode = $this->getParam("mode", NULL);

        return 'validatenote_tpl.php';
    }

    /**
     * 
     * Method corresponding to the save action. It gets the mode from 
     * the querystring to and saves the data then sets nextAction to be 
     * null, which returns the {yourmodulename} module in view mode. 
     * 
     * @access private
     * 
     */
    private function __save() {
        $mode = $this->getParam("mode", NULL);
        $id = $this->getParam('id');

        $data = array();
        $data['userid'] = $this->objUser->userId();
        $data['title'] = $this->getParam('title');
        $data['tags'] = $this->getParam('tags');
        $data['content'] = $this->getParam('content');
        $data['public_note'] = $this->getParam('isPublic');

        if (empty($id) && $mode == 'add') {
            $data['datecreated'] = date('Y-m-d H:i:s');
            $id = $this->objDbmynotes->insertNote($data);
            $this->objDbtags->addTag($data);
            echo $id;
        } else {
            $data['datemodified'] = date('Y-m-d H:i:s');
            $existingTags = $this->objDbmynotes->getExistingTags($id);
            // Remove existing tags if any tags have changed
            if ($data['tags'] !== $existingTags) {
                $this->objDbtags->removeTags($existingTags);
                $this->objDbtags->addTag($data);
            } 
            $status = $this->objDbmynotes->updateNote($data, $id);
            if($status){
                echo "TRUE";
            } else {
                echo "FALSE";
            }
        }
        die();
    }

    /**
     * 
     * Method corresponding to the delete action. It requires a 
     * confirmation, and then delets the item, and then sets 
     * nextAction to be null, which returns the {yourmodulename} module 
     * in view mode. 
     * 
     * @access private
     * 
     */
    private function __delete() {
        // retrieve the confirmation code from the querystring
        $confirm = $this->getParam("confirm", "no");
        $id = $this->getParam('id');
        if ($confirm == "yes") {
            $this->objDbmynotes->deleteNote($id);
            return $this->nextAction(NULL);
        } else {
            $this->nextAction("showNote", array("id" => $id, "error" => "nodelete"));
        }
    }

    /*
     * Method to retrieve all the notes for the current user
     * 
     * @access private
     * 
     */
    private function __ajaxGetNotes() {
        return $this->objNoteOps->getNotes();
    }

    /*
     * Method to search for the keywords that are found in my notes using the
     * tag links
     * 
     * @access private
     */
    private function __search() {
        return 'searchresults_tpl.php';
    }

    /*
     * Method to show individual note with all it's details
     * 
     * @access private
     * 
     */
    private function __showNote() {
        return 'shownote_tpl.php';
    }

    /*
     * A method to retrieve sharing options.
     * 
     * @access private
     * 
     */
    private function __ajaxGetShare() {
        return $this->objNoteOps->getShareOptions();
    }
    
    /**
     * 
     * Method to return an error when the action is not a valid 
     * action method
     * 
     * @access private
     * @return string The dump template populated with the error message
     * 
     */
    private function __actionError() {
        $this->setVar('str', "<h3>"
                . $this->objLanguage->languageText("phrase_unrecognizedaction")
                . ": " . $action . "</h3>");
        return 'dump_tpl.php';
    }

    /**
     * 
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action 
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     * 
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     * 
     */
    function __validAction(& $action) {
        if (method_exists($this, "__" . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * Method to convert the action parameter into the name of 
     * a method of this class.
     * 
     * @access private
     * @param string $action The action parameter passed byref
     * @return stromg the name of the method
     * 
     */
    function __getMethod(& $action) {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }

    /* ------------- END: Set of methods to replace case selection ------------ */

    /**
     *
     * This is a method to determine if the user has to 
     * be logged in or not. Note that this is an example, 
     * and if you use it view will be visible to non-logged in 
     * users. Delete it if you do not want to allow annonymous access.
     * It overides that in the parent class
     *
     * @return boolean TRUE|FALSE
     *
     
    public function requiresLogin() {
        $action = $this->getParam('action', 'NULL');
        switch ($action) {
            case 'view':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
        return TRUE;
    }*/
}

?>