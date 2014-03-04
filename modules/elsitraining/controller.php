<?php

/**
 * 
 * eLSI Training Registration
 * 
 * A classic hello world type module to introduce you to chisimba.
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
 * @package   training registration
 * @author    dexters
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: controller.php,v 1.4 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
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
 * Hello Chisimba
 *
 * Controller class for Chisimba for the module hellochisimba
 *
 * @author dexters mlambo
 * @package training registration
 *
 */
class elsitraining extends controller {

    /**
     *
     * Constructor for the hellochisimba controller
     *
     * @access public
     * @return void
     *
     */
    public function init() {
        $this->loadClass('link', 'htmlelements');
        $this->objLanguage = $this->getObject("language", "language");
        $this->objDbRegistration = $this->getObject("dbregistration");
        $this->objDbOneonOne = $this->getObject("dboneonone");
        $this->objDbScheduledRegistration = $this->getObject("dbscheduledregistration");
        $this->objMainContent = $this->getObject("block_reg3");
    }

    /**
     * 
     * The standard dispatch method for the hellochisimba module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     *
     * @access public
     * @return The output of the executed method
     * 
     */
    public function dispatch($action) {
        //Get action from query string and set default to view
        $action = $this->getParam('action', 'view');
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
        //$this->setLayoutTemplate("layout_tpl.php");
        //$method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        //return $this->$method();
        
    }

    /**
     *
     * Method corresponding to the view action. It sets the layout template
     * and fetches the appropriate content template, in this case,
     * default_tpl.php.
     *
     * @access private
     *
     */
    private function __view() {

        $this->setLayoutTemplate('layout_tpl.php');

        return 'default_tpl.php';
        /*        $gifts = $this->objDbGift->getGifts($departmentid);
        $this->setVarByRef("gifts", $gifts);
        return "home_tpl.php";*/
    }

    /**
     *
     *         $objIcon->setIcon('delete');
        $deleteGift = new link($this->uri(array('action' => 'confirmdeletegift', 'id' => $gift['id'])));
        $deleteGift->link = $objIcon->show();
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
            return "__view";
        }
    }

    function __home(){
        return "home_tpl.php";
    }
    function __training() {
        return "addedittraining_tpl.php";
    }

    function __deregister() {
        return "deregistertraining_tpl.php";
    }

    function __registration() {
        return "registration_tpl.php";
    }

    function __oneonone() {
        $staffnum = $this->getParam('staffnum');
        $title = $this->getParam('title');
        $firstname = $this->getParam('firstname');
        $surname = $this->getParam('surname');
        $email = $this->getParam('email');
        $tel = $this->getParam('tel');
        $prefstarttime = $this->getParam('prefstarttime');
        $prefendtime = $this->getParam('prefendtime');
        $venue = $this->getParam('venue');

        $data = array('staffnum' => $staffnum,
            'title' => $title,
            'firstname' => $firstname,
            'surname' => $surname,
            'email' => $email,
            'tel' => $tel,
            'TID' => '45',
            'refnum' => 'X00R89',
            'ovrbook' => 'n',
            'canceled' => 'n',
            'prefstarttime' => $prefstarttime,
            'prefendtime' => $prefendtime,
            'venue' => $venue);

        $objRegistration = $this->getObject('dboneonone', 'elsitraining');
        $result = $objRegistration->recordTraining($data);

        echo "one on one has been created";
    }

    function __createtraining() {
        $starttime = $this->getParam('starttime');
        $endtime = $this->getParam('endtime');
        $venue = $this->getParam('venue');
        $contactperson = $this->getParam('contactperson');
        $maxlimit = $this->getParam('maxlimit');
        $description = $this->getParam('description');

        $data = array('starttime' => $starttime,
            'endtime' => $endtime,
            'venue' => $venue,
            'contactperson' => $contactperson,
            'maxlimit' => $maxlimit,
            'description' => strip_tags($description));
        $objRegistration = $this->getObject('dbregistration', 'elsitraining');
        $result = $objRegistration->recordSchedule($data);
        echo "Training created";
    }

    function __deregistertraining() {
        echo "deregistration successful!!!!!";
    }

    function __scheduled() {

        $staffnum = $this->getParam('staffnum');
        $title = $this->getParam('title');
        $firstname = $this->getParam('firstname');
        $surname = $this->getParam('surname');
        $email = $this->getParam('email');
        $tel = $this->getParam('tel');

        $data = array('staffnum' => $staffnum,
            'title' => $title,
            'firstname' => $firstname,
            'surname' => $surname,
            'email' => $email,
            'tel' => $tel, 'TID' => '45', 'refnum' => 'X00R89', 'ovrbook' => 'n', 'canceled' => 'n');
        $objRegistration = $this->getObject('dbscheduledregistration', 'elsitraining');
        $result = $objRegistration->recordTraining($data);
        //$objBlocReg1=$this->getObject("block_reg1");
        //$objBlocReg1->show();
        //$successmessage='Registration successful!!!!!';
        //$this->setVarByRef("message", $successmessage);
        //return "success_tpl.php";
        echo "Registration successful.";
    }

    function __viewSched(){
        $objDbRegistration = $this->getObject("dbregistration");
        $schedules = $objDbRegistration->getSchedule();
        $this->setVarByRef("schedules", $schedules);
        return "viewschedules_tpl.php";
    }

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
            case 'view':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
    }

}

?>
