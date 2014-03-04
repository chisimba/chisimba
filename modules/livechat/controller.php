<?php

/**
 *
 *  livechat 
 *
 *  livechat allows instructors to add a live chat component into the course as a block.
 *  livechat uses the chat component of the realtime system
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
 * @author    David Wafula
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
 * Controller class for Chisimba for the module livechat
 *
 *
 */
class livechat extends controller {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;

    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     *
     * The standard dispatch method for the twitter module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     */
    public function dispatch($action) {
        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */

        $method = $this->__getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    // --------------------------------------------------------

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
    private function __validAction(& $action) {
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
    function __getMethod($action) {

        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__home";
        }
    }

    function __home() {
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);
        $this->setVar('pageSuppressToolbar', TRUE);
        return "home_tpl.php";
    }

    function __sendinvite() {
        $dblivechat = $this->getObject("dblivechat");
        $message = $this->objUser->fullnames . " wants to chat";
        $from = $this->objUser->userid();
        $usersParam = $this->getParam('users');
        $users = explode(",", $usersParam);
        foreach ($users as $user) {
            if ($user != '') {
                $dblivechat->addMessage($message, $from, $user);
            }
        }
        echo "success";
        die();
    }

    /* ------------- END: Set of methods to replace case selection ------------ */
}

?>
