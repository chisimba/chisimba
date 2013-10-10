<?php

/**
 * 
 * Register interest
 * 
 * Allows someone to enter name and email address in order to be contacted at sometime in the future about something of interest. For example, register to be notified about the upcoming API release, Lady GooGoo concert, etc.
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
 * @package   registerinterest
 * @author    Derek Keats derek@dkeats.com
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
 * Controller class for Chisimba for the module registerinterest
 *
 * @author Derek Keats
 * @package registerinterest
 *
 */
class registerinterest extends controller {

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
     * @var object The registerinterest database object
     * @access public
     */
    public $objDB;

    /**
     *
     * @var object The user object
     * @access public
     */
    public $objUser;

    /**
     * 
     * Intialiser for the registerinterest controller
     * @access public
     * 
     */
    public function init() {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        // Create an instance of the database class
        $this->objDB = & $this->getObject('dbregisterinterest', 'registerinterest');
        //Get the activity logger class
        $this->objLog = $this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
        //riops object
        $this->objRiops = $this->getObject('riops', 'registerinterest');
    }

    /**
     * 
     * The standard dispatch method for the registerinterest module.
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
     * dynamic canvas template, showing you how to create block based
     * view templates
     * @access private
     * 
     */
    private function __view() {
        // All the action is in the blocks
        if ($this->objUser->isLoggedIn()) {
            if ($this->objUser->isAdmin()) {
                return "main_tpl.php";
            }
        }
        // Redirect
        header('location: ' . 'index.php');
    }

    private function __writemessage() {
        // All the action is in the blocks
        if ($this->objUser->isLoggedIn()) {
            if ($this->objUser->isAdmin()) {
                return "writemsg_tpl.php";
            }
        }
        // Redirect
        header('location: ' . 'index.php');
    }

    /**
     * 
     * Method corresponding to the save action. 
     * 
     * @param string $tableName The name of the table where the values have to inserted
     * @access private
     * @return NULL 
     * 
     */
    private function __save() {
        //get the table ID
        $tableID = $this->getParam('table', 1);
        //if tableID = 2 then insert values to the interest table
        if ($tableID == 2) {
            if ($this->objUser->isAdmin()) {
                //set table name according to given tableID
                $this->objDB->_tableName = 'tbl_registerinterest';
                //get interest value
                $value = $this->getParam('txtValue', NULL);
                if (!empty($value)) {
                    if (!$this->objDB->valueExists('name', $value)) {
                        //set the values to be inserted into the dfatabase
                        $data = array(
                            'id' => NULL,
                            'datecreated' => $this->objDB->now(),
                            'name' => $value
                        );
                        $this->objDB->save($data);
                    }
                }
            }
        } else {
            //set table
            $this->objDB->_tableName = 'tbl_registerinterest_interested';
            //get the person's fullname
            $fullName = $this->getParam('fullname', NULL);
            //get the person's email address
            $eMail = $this->getParam('email', NULL);
            //remove the equal sign from email address
            $eMail = str_replace('=', '', $eMail);
            if (!empty($eMail) && !empty($fullName)) {
                //generate the ID
                $userid = $this->objDB->_serverName . "_" . rand(1000, 99999) . "_" . time();
                $data = array(
                    'id' => $userid,
                    'datecreated' => $this->objDB->now(),
                    'fullname' => $fullName,
                    'email' => $eMail
                );
                //insert the values to database but only if the supplied email does not exist
                if (!$this->objDB->valueExists('email', $eMail)) {
                    $this->objDB->save($data);
                }
                //change the table name
                $this->objDB->_tableName = 'tbl_registerinterest';
                $valuesList = $this->objDB->getAll();
                //change to records table
                $this->objDB->_tableName = 'tbl_registerinterest_records';
                $index = 0;
                //get all the values from the database
                foreach ($valuesList as $interest) {
                    if ($this->getParam('interest' . $index, NULL) == $interest['id']) {
                        $data = array(
                            'userid' => $userid,
                            'interestid' => $interest['id'],
                            'id' => NULL
                        );
                        //insert the values to the database
                        $this->objDB->save($data);
                        $index++;
                    }
                }
            }
        }
        /*         * END OF INTEREST ADDING* */

        //die();
        return 'main_tpl.php';
    }

    private function __optout() {
        $confirmation = $this->getParam('remove', NULL);
        $id = $this->getParam('id', NULL);
        //convert to liwerstring
        if(!empty($confirmation)){
            $confirmation = strtolower($confirmation);
        }
//        Remove the record on user confirmation
        if (strtolower($confirmation) == 'true') {
            $this->objDB->remove($id);
            header('location: index.php');
        }
        //check if the person's email address is in the database
        if ($this->objDB->valueExists('id', $id) && !empty($id)) {
            return 'optoutconfirm_tpl.php';
        } else {
            header('location: index.php');
        }
    }

    /**
     * Method to remove a redord from the interest list
     * 
     * @access private
     * @param type $id The user/record id
     * @return NULL
     */
    private function __remove() {
        //get the table to execute the query in, set default to 1:tbl_registerinterest_interested
        $tableID = $this->getParam('table', 1);
        $id = $this->getParam('id', NULL);
        $this->objDB->remove($id, $tableID);
        return $this->__view();
    }

    /**
     * Method to update the changes to a email address
     * 
     * @access public
     * @param NULL
     * @return NULL
     */
    public function __update() {
        if ($this->objUser->isAdmin()) {
            $this->objDB->updateMail();
        }
    }

    public function __sendmessage() {
        $message = $this->getParam('emailmessage', NULL);
        $subject = $this->getParam('subject', NULL);
        $userID = $this->objUser->getUserId($this->objUser->userName());
        //get the jquery dialog object
        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        //setting the title
        $successTitleLabel = "<span class='success' >" . $this->objLanguage->languageText('phrase_messagestatus', 'system') . "</span>";
        $this->objDialog->setTitle(ucwords($successTitleLabel));
        $this->objDialog->setCssId('dialog_messagesuccess');
        $this->objDialog->setCloseOnEscape(TRUE);
        $this->objDialog->setResizable(FALSE);
        $this->objDialog->setAutoOpen(TRUE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        if (!empty($message)) {
            if ($this->objRiops->sendMessage($subject, $message, $userID)) {
                //echo "$message"."<script type='text/javascript' >alert(7)l</script>". $this->getJavaScriptFile('imageresize.js','registerinterest');
                $content = $this->objLanguage->languageText('phrase_msgsuccess', 'system');
                $this->objDialog->setContent($content);
                echo $this->objDialog->show();
            } else {
                $content = $this->objLanguage->languageText('phrase_msgerror', 'system');
                $successTitleLabel = "<span class='error' >" . $this->objLanguage->languageText('phrase_messagestatus', 'system') . "</span>";
                $this->objDialog->setContent($content);
                echo $this->objDialog->show();
            }
        } else {
            $content = $this->objLanguage->languageText('mod_registerinterest_emptymsg', 'registerinterest');
            $successTitleLabel = "<span class='error' >" . $this->objLanguage->languageText('phrase_messagestatus', 'system') . "</span>";
            $this->objDialog->setContent($content);
            echo $this->objDialog->show();
        }
        return "writemsg_tpl.php";
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
        return 'main_tpl.php';
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
     */
    public function requiresLogin() {
        $action = $this->getParam('action', 'NULL');
        switch ($action) {
            case 'save':
                return FALSE;
                break;
            case 'optout':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
    }

}

?>
