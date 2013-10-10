<?php
/**
 * 
 * schoolusers
 * 
 * Provides a means to capture and maintain the extra user data required for managing schools on the system. It can also be useful to any other project that needs this extra data.
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
 * @package   schoolusers
 * @author    Kevin Cyster kcyster@gmail.com
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
* Controller class for Chisimba for the module schoolusers
*
* @author Kevin Cyster
* @package schoolusers
*
*/
class schoolusers extends controller
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
    * Intialiser for the schoolusers controller
    * @access public
    * 
    */
    public function init()
    {
        $this->objGroupOps = $this->getObject('groupops', 'groupadmin');
        $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objDBusers = $this->getObject('dbusers', 'schoolusers');
        $this->objDBdata = $this->getObject('dbdata', 'schoolusers');
        $this->objOps = $this->getObject('schoolusersops', 'schoolusers');
        $this->objCaptcha = $this->getObject('captcha', 'utilities');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');

        // Create an instance of the database class
//        $this->objDbschoolusers = & $this->getObject('dbschoolusers', 'schoolusers');
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('schoolusers.js',
          'schoolusers'));
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the schoolusers module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $this->action = $this->getParam('action', 'view');
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($this->action);
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
     * 
     * @access private
     */
    private function __view()
    {
        // All the action is in the blocks
        $this->setSession('errors', array());
        
        return "main_tpl.php";
    }
        
    /**
     * 
     * Method corresponding to the ajaxFindUser action
     *
     * @access private
     */
    private function __ajaxFindUser()
    {
        // All the action is in the blocks
        return $this->objOps->ajaxFindUser();
    }
        
    /**
     * 
     * Method corresponding to the show action. It shows the default
     * dynamic canvas template, showing you how to create block based
     * view templates
     * 
     * @access private
     */
    private function __show()
    {
        // All the action is in the blocks
        return 'show_tpl.php';
    }
        
    /**
     * 
     * Method corresponding to the delete action. It shows the default
     * dynamic canvas template, showing you how to create block based
     * view templates
     * 
     * @access private
     */
    private function __delete()
    {
        $id = $this->getParam('id');
        $data = array();
        $data['isactive'] = 0;
        $data['updated'] = date('Y-m-d');
        
        $this->objDBusers->updateUser($id, $data);
        
        unset($data['updated']);
        $data['modified_by'] = $this->objUser->PKId();
        $data['date_modified'] = date('Y-m-d');
        $this->objDBdata->updateData($id, $data);
        
        return $this->nextAction(NULL);
    }
        
    /**
     * 
     * Method corresponding to the form action. It shows the default
     * dynamic canvas template, showing you how to create block based
     * view templates
     * 
     * @access private
     */
    private function __form()
    {
        return 'form_tpl.php';
    }

    /**
     * 
     * Method corresponding to the ajaxUsername action
     *
     * @access private
     */
    private function __ajaxUsername()
    {
        // All the action is in the blocks
        return $this->objOps->ajaxUsername();
    }
        
    /**
     *
     * Method corresponding to the ajaxFindSchools action
     * @access private
     * @return 
     */
    private function __ajaxFindSchools()
    {
        return $this->objOps->ajaxFindSchools();
    }
         
    /**
     *
     * Method to validate user data
     * 
     * @access private
     * @return VOID 
     */
    private function __validate()
    {
        $id = $this->getParam('id');
        $cancel = $this->getParam('cancel', NULL);
        if (!empty($cancel))
        {
            $this->setSession('errors', array());
            return $this->objUser->isLoggedIn() ? $this->nextAction('view') : $this->nextAction('view', '', 'prelogin');            
        }
        
        $data = array();
        $data['id'] = $id;
        $data['school_id'] = $this->getParam('school_id');
        $data['title'] = $this->getParam('title');
        $data['first_name'] = $this->getParam('first_name');
        $data['middle_name'] = $this->getParam('middle_name');
        $data['last_name'] = $this->getParam('last_name');
        $data['gender'] = $this->getParam('gender');
        $data['date_of_birth'] = $this->getParam('date_of_birth');
        $data['address'] = $this->getParam('address');
        $data['city'] = $this->getParam('city');
        $data['state'] = $this->getParam('state');
        $data['country'] = $this->getParam('country');
        $data['postal_code'] = $this->getParam('postal_code');
        $data['email_address'] = $this->getParam('email_address');
        $data['contact_number'] = $this->getParam('contact_number');
        $data['description'] = $this->getParam('description');
        $data['username'] = $this->getParam('username');
        $data['password'] = $this->getParam('password');
        $data['confirm_password'] = $this->getParam('confirm_password');
        $data['captcha'] = $this->getParam('captcha');
        $data['request_captcha'] = $this->getParam('request_captcha');
       
        $errorsFound = $this->objOps->validate($data);

        if ($errorsFound == FALSE)
        {
            $this->objOps->save($data);
            $this->setSession('errors', array());
            return $this->objUser->isLoggedIn() ? $this->nextAction('view') : $this->nextAction('success');
        }
        else
        {
            return $this->objUser->isLoggedIn() ? $this->nextAction('form', array('id' => $id)) : $this->nextAction('selfregister');
        }
    }
         
    /**
     * 
     * Method corresponding to the selfregister action. It shows the default
     * dynamic canvas template, showing you how to create block based
     * view templates
     * 
     * @access private
     */
    private function __selfregister()
    {
        return 'selfregister_tpl.php';
    }

    /**
     * 
     * Method corresponding to the success action. It shows the default
     * dynamic canvas template, showing you how to create block based
     * view templates
     * 
     * @access private
     */
    private function __success()
    {
        return 'success_tpl.php';
    }

    /**
     *
     * Method to return the data for finding principals
     * @access private
     * @return 
     */
    private function __ajaxCaptcha()
    {
        echo $this->objCaptcha->show();
        die();
    }
         
    /**
     *
     * Method corresponding to the ajaxFlexigridUsers action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxFlexigridUsers()
    {
        echo $this->objOps->ajaxFlexigridUsers();
        die();
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
        if ($this->objUser->isLoggedIn())
        {
            $groupId = $this->objGroups->getId("School Managers");
            $userId = $this->objUser->userId();
            if ($this->objUser->isAdmin() || 
                $this->objGroupOps->isGroupMember($groupId, $userId ))
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }

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
    * This is a method to determine if the user has to 
    * be logged in or not. Note that this is an example, 
    * and if you use it view will be visible to non-logged in 
    * users. Delete it if you do not want to allow annonymous access.
    * It overides that in the parent class
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        $action=$this->getParam('action','NULL');
        switch ($action)
        {
            case 'selfregister':
                return FALSE;
                break;
            case 'validate':
                return FALSE;
                break;
            case 'ajaxFindSchools':
                return FALSE;
                break;
            case 'ajaxUsername':
                return FALSE;
                break;
            case 'ajaxCaptcha':
                return FALSE;
                break;
            case 'success':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
}
?>
