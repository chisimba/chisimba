<?php
/**
 * 
 * OER User Data
 * 
 * Provides a means to capture and maintain the extra user 
 * data required by the OER module. It can also be useful 
 * to any other project that needs this extra data.
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
 * @package   oeruserdata
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
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
* 
* Controller class for Chisimba for the module oeruserdata
*
* @author Derek Keats
* @package oeruserdata
*
*/
class oeruserdata extends controller
{
    
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
    * Intialiser for the oeruserdata controller
    * @access public
    * 
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        // Create an instance of the database class
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('oeruserdata.js',
          'oeruserdata'));
        // Set the jQuery version to the one required
        $this->setVar('JQUERY_VERSION', '1.6.1');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the oeruserdata module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'view');
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
    
    
    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * 
    * Method corresponding to the view action. It shows the default
    * dynamic canvas template, showing you how to create block based
    * view templates
    * @access private
    * 
    */
    private function __view()
    {
        return "main_tpl.php";
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
    private function __actionError()
    {
        $this->setVar('str', "<h3>"
          . $this->objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $this->getParam('action', 'No action defined') 
          . "</h3>");
        $this->setLayoutTemplate(NULL);
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
    function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
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
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }
    
    /*------------- END: Set of methods to replace case selection ------------*/
    
    /**
     *
     * Open the edit/add form for users, includes the additional data
     * that the OER project needs, as well as the data from tbl_users
     *
     * @return string Template
     * @access public
     * 
     */
    public function __editUser() {
        return 'useredit_tpl.php';
    }

    /**
     *
     * Open the edit/add form for self-register to provide the 
     * additional data that the OER project needs, as well as 
     * the data from tbl_users
     *
     * @return string Template
     * @access public
     * 
     */
    public function __selfregister() {
        return 'useredit_tpl.php';
    }
    
    /**
     *
     * Open the edit/add form for admin to add user to provide the 
     * additional data that the OER project needs, as well as 
     * the data from tbl_users
     *
     * @return string Template
     * @access public
     * 
     */
    public function __adduser() {
        return 'useredit_tpl.php';
    }

    /**
     * 
     * Save the userdetails data and return resulting Id to be 
     * used by Ajax
     * 
     * @access public
     * @return VOID
     * 
     */
    public function __userdetailssave() {
        $objDb = $this->getObject('dboeruserdata', 'oeruserdata');
        $mode = $this->getParam('mode', 'add');
      
        if ($mode == 'add' || $mode == 'selfregister') {
            $result = $objDb->addSave();
        } elseif ($mode == 'edit') {
            $result = $objDb->editSave();
        } else {
            $result = 'INVALID_MODE';
        }
        die($result);
    }
    
    /**
     * 
     * Render a captcha on the form using the captcha from
     * the login module
     * 
     * @access public
     * @return void
     * 
     */
    public function __showcaptcha()
    {
        $objCaptcha = $this->getObject('captcha', 'login');
        echo $objCaptcha->show();
        die();
    }
    
    /**
     * 
     * Verify the captcha using the login module's captcha
     * system
     * 
     * @access public
     * @return void
     * 
     */
    public function __verifycaptcha()
    {
        $objCaptcha = $this->getObject('captcha', 'login');
        echo $objCaptcha->verifyCaptcha();
        die();
    }

    /**
     * 
     * Check availability of a username and return results to Ajax
     * 
     * @access public
     * @return VOID
     * 
     */
    public function __checkusernameajax() {
        $userName = $this->getParam('username', FALSE);
       if ($userName) {
            $objUserAdmin = $this->getObject('useradmin_model2', 'security');
            if ($objUserAdmin->userNameAvailable($userName) == FALSE) {
                die('true');
            } else {
                die('false');
            }
        } else {
            die('errornousername');
        }
    }
    
    /**
     * 
     * Delete a user using ajax method
     * 
     * @return void
     * @access public
     * 
     */
    public function __delete()
    {
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getId("Usermanagers");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $userId = $this->objUser->userId();
        if ($this->objUser->isAdmin() || 
          $objGroupOps->isGroupMember($groupId, $userId )) {
            $objDb = $this->getObject('dboeruserdata', 'oeruserdata');
            $id = $this->getParam('id', FALSE);
            if ($id) {
                die($objDb->deleteUser($id));
            } else {
                die('ERROR_NO_ID');
            }
          } else {
              die('ATTEMPT_BREACH');
          }
    }
    
    /**
     * 
     * Userlisting for ajax call
     * 
     * @return void
     * @access public
     * 
     */
    public function __userlistajax()
    {
        $objList = $this->getObject('userblocks', 'oeruserdata');
        echo $objList->showUserList(FALSE);
        die();
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
    function requiresLogin($action = 'home') {
        $allowedActions = array(NULL, "checkusernameajax", 
            "userdetailssave", "showcaptcha", 
            "verifycaptcha", "selfregister");
        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
?>