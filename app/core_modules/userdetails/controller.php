<?php
/**
 * 
 * userdetails
 * 
 * Provides an interface for users to update their details, as well as modify their user parameters (forthcoming feature)
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
 * @package   userdetails
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
* Controller class for Chisimba for the module userdetails
*
* @author Kevin Cyster
* @package userdetails
*
*/
class userdetails extends controller
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
    * Intialiser for the userdetails controller
    * @access public
    * 
    */
    public function init()
    {
        $this->objOps = $this->getObject('userdetailsops', 'userdetails');
        $this->objUser = $this->getObject('user', 'security');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        $this->appendArrayVar('headerParams',
            $this->getJavaScriptFile('userdetails.js',
            'userdetails'));
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the userdetails module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'main');
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
    private function __main()
    {
        // All the action is in the blocks
        return "main_tpl.php";
    }   
    
    /**
     *
     * Method corresponding to the ajaxChangeGradeAction
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxChangeGrade()
    {
        return $this->objOps->ajaxChangeGrade();
    }
    
    /**
     * 
     * Method corresponding to the ajaxChangeImage action
     * 
     * @access private
     * @return VOID
     */
    private function __ajaxChangeImage()
    {
        return $this->objOps->ajaxChangeImage();
    }
    
    /**
     * 
     * Method corresponding to the imagereset action.
     * Resets the user's image to the default
     * 
     * @access private
     * @return VOID
     */
    private function __ajaxResetImage()
    {
        return $this->objOps->ajaxResetImage();
    }
    
    /**
     * 
     * Method corresponding to the imagereset action.
     * Resets the user's image to the default
     * 
     * @access private
     * @return VOID
     */
    private function __resetimage()
    {
        $this->objOps->ajaxResetImage(FALSE);
        return $this->nextAction(NULL);
    }
    
    /**
     * 
     * Method corresponding to the validate action.
     * 
     * @access private
     * @return VOID
     */
    private function __validate()
    {
        $reset = $this->getParam('reset', NULL);
        if (!empty($reset))
        {
            $this->setSession('errors', array());
            $this->setSession('success', array());
            return $this->nextAction('main');
        }
        
        $check = $this->objModules->checkIfRegistered('schoolusers');
        
        $data = array();
        $data['title'] = $this->getParam('title');
        $data['first_name'] = $this->getParam('first_name');
        $data['last_name'] = $this->getParam('last_name');
        $data['gender'] = $this->getParam('gender');
        $data['country'] = $this->getParam('country');
        $data['email_address'] = $this->getParam('email_address');
        $data['contact_number'] = $this->getParam('contact_number');
        $data['password'] = $this->getParam('password');
        $data['confirm_password'] = $this->getParam('confirm_password');
        if ($check)
        {
            $data['school_id'] = $this->getParam('school_id');
            $data['middle_name'] = $this->getParam('middle_name');
            $data['date_of_birth'] = $this->getParam('date_of_birth');
            $data['address'] = $this->getParam('address');
            $data['city'] = $this->getParam('city');
            $data['state'] = $this->getParam('state');
            $data['postal_code'] = $this->getParam('postal_code');
            $data['description'] = $this->getParam('description');
        }
        else
        {
            $data['staffnumber'] = $this->getParam('staffnumber');
        }
     
        $errorsFound = $this->objOps->validate($data);
        if ($errorsFound == FALSE)
        {
            $this->setSession('errors', array());
            $this->objOps->save($data);
            return $this->nextAction('main');
        }
        else
        {
            return $this->nextAction('main');
        }        
    }
    
    /**
     *
     * Method corresponding to ajaxResetSession sction.
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxResetSession()
    {
        $this->setSession('success', array());
        echo NULL;
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
        $action=$this->getParam('action', 'NULL');
        switch ($action)
        {
            default:
                return TRUE;
                break;
        }
     }
}
?>
