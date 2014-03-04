<?php
/**
 * 
 * schools
 * 
 * Simple facility to store school basic data
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
 * @package   schools
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
* Controller class for Chisimba for the module schools
*
* @author Kevin Cyster
* @package schools
*
*/
class schools extends controller
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
    * Intialiser for the schools controller
    * @access public
    * 
    */
    public function init()
    {
        $this->objGroupOps = $this->getObject('groupops', 'groupadmin');
        $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUser = $this->getObject('user', 'security');
        $this->objUserAdmin = $this->getObject('useradmin_model2', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        // Create an instance of the database class
        // Load module classes.
        $this->objDBprovinces = $this->getObject('dbschools_provinces', 'schools');
        $this->objDBdistricts = $this->getObject('dbschools_districts', 'schools');
        $this->objDBcontacts = $this->getObject('dbschools_contacts', 'schools');
        $this->objDBschools = $this->getObject('dbschools_schools', 'schools');
        $this->objOps = $this->getObject('schoolsops', 'schools');
        
        $this->objOps = & $this->getObject('schoolsops', 'schools');
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('schools.js',
          'schools'));
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the schools module.
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
        // All the action is in the blocks
        $this->setSession('errors', array());
        return "find_tpl.php";
    }
    
    /**
     *
     * Method to return the data for finding principals
     * @access private
     * @return 
     */
    private function __ajaxFindSchools()
    {
        return $this->objOps->ajaxFindSchools();
    }
         
    /**
    * 
    * Method corresponding to the add school action. It shows the default
    * dynamic canvas template, showing you how to create block based
    * view templates
    * @access private
    * 
    */
    private function __addoredit()
    {
        // All the action is in the blocks
        return "addeditschool_tpl.php";
    }

    /**
    * 
    * Method corresponding to the get districts ajax request. It shows the default
    * dynamic canvas template, showing you how to create block based
    * view templates
    * @access private
    * 
    */
    private function __ajaxGetDistricts()
    {
        // All the action is in the blocks
        return $this->objOps->ajaxGetDistricts();
    }

    /**
    * 
    * Method corresponding to the add school submission form. It shows the default
    * dynamic canvas template, showing you how to create block based
    * view templates
    * @access private
    * 
    */
    private function __validateschool()
    {
        // All the action is in the blocks
        $mode = $this->getParam('mode');
        $sid = $this->getParam('sid');
        $cancel = $this->getParam('cancel', NULL);
        if (!empty($cancel))
        {
            $this->setSession('errors', array());
            if ($mode == 'add')
            {
                return $this->nextAction('view');
            }
            else
            {
                return $this->nextAction('show', array('sid' => $sid));
            }
        }

        $data = array();
        $data['sid'] = $sid;
        $data['province_id'] = $this->getParam('province_id');
        $data['district_id'] = $this->getParam('district_id');
        $data['name'] = $this->getParam('name');
        $data['address_one'] = $this->getParam('address_one');
        $data['address_two'] = $this->getParam('address_two');
        $data['address_three'] = $this->getParam('address_three');
        $data['address_four'] = $this->getParam('address_four');
        $data['email_address'] = $this->getParam('email_address');
        $data['telephone_number'] = $this->getParam('telephone_number');
        $data['fax_number'] = $this->getParam('fax_number');
        
        $errorsFound = $this->objOps->validateSchool($data);

        if ($errorsFound == FALSE)
        {
            if ($mode == 'edit')
            {
                $this->objOps->updateSchool($data);
            }
            else
            {
                $sid = $this->objOps->insertSchool($data);
            }
            $this->setSession('errors', array());
            return $this->nextAction('show', array('sid' => $sid));
        }
        else
        {
            if ($mode == 'edit')
            {
                return $this->nextAction('addoredit', array('mode' => $mode, 'sid' => $sid));
            }
            else
            {
                return $this->nextAction('addoredit', array('mode' => $mode));
            }
        }
    }

    /**
    * 
    * Method corresponding to the show school action. It sets the mode to 
    * edit and returns the edit template.
    * @access private
    * 
    */
    private function __show()
    {
        $this->setSession('errors', array());
        return 'showschool_tpl.php';
    }
            
    /**
    * 
    * Method corresponding to the delet school action. It sets the mode to 
    * edit and returns the edit template.
    * @access private
    * 
    */
    private function __deleteschool()
    {
        $this->objOps->deleteSchool();        
        return $this->nextAction('view');
    }
            
    /**
    * 
    * Method corresponding to the contacts action. It shows the default
    * dynamic canvas template, showing you how to create block based
    * view templates
    * @access private
    * 
    */
    private function __contacts()
    {
        // All the action is in the blocks
        return "contacts_tpl.php";
    }

    /**
    * 
    * Method corresponding to the add contact submission form. It shows the default
    * dynamic canvas template, showing you how to create block based
    * view templates
    * @access private
    * 
    */
    private function __validatecontact()
    {
        // All the action is in the blocks
        $mode = $this->getParam('mode');
        $sid = $this->getParam('sid');
        $cid = $this->getParam('cid');
        $cancel = $this->getParam('cancel', NULL);
        if (!empty($cancel))
        {
            $this->setSession('errors', array());
            return $this->nextAction('show', array('sid' => $sid, 'tab' => 2));
        }

        $data = array();
        $data['cid'] = $cid;
        $data['sid'] = $sid;
        $data['position'] = $this->getParam('position');
        $data['name'] = $this->getParam('name');
        $data['address_one'] = $this->getParam('address_one');
        $data['address_two'] = $this->getParam('address_two');
        $data['address_three'] = $this->getParam('address_three');
        $data['address_four'] = $this->getParam('address_four');
        $data['email_address'] = $this->getParam('email_address');
        $data['telephone_number'] = $this->getParam('telephone_number');
        $data['fax_number'] = $this->getParam('fax_number');
        $data['mobile_number'] = $this->getParam('mobile_number');
        
        $errorsFound = $this->objOps->validateContact($data);

        if ($errorsFound == FALSE)
        {
            if ($mode == 'edit')
            {
                $this->objOps->updateContact($data);
            }
            else
            {
                $cid = $this->objOps->insertContact($data);
            }
            $this->setSession('errors', array());
            return $this->nextAction('show', array('sid' => $sid, 'tab' => 2));
        }
        else
        {
            if ($mode == 'edit')
            {
                return $this->nextAction('contacts', array('mode' => $mode, 'sid' => $sid, 'cid' => $cid));
            }
            else
            {
                return $this->nextAction('contacts', array('mode' => $mode, 'sid' => $sid));
            }
        }
    }

    /**
    * 
    * Method corresponding to the delete contact action. It sets the mode to 
    * edit and returns the edit template.
    * @access private
    * 
    */
    private function __deletecontact()
    {
        $sid = $this->getParam('sid');
        
        $this->objOps->deleteContact();        
        return $this->nextAction('show', array('sid' => $sid, 'tab' => 2));
    }
            
    /**
     *
     * Method to return the templates for managing either districts or principals
     * 
     * @access private
     * @return 
     */
    private function __manage()
    {
        $this->setSession('errors', array());
        $type = $this->getParam('type');
        switch ($type)
        {
            case 's':
                return 'find_tpl.php';
            case 'p':
                return 'province_tpl.php';
            case 'd':
                return 'district_tpl.php';
        }
    }
    
    /**
     *
     * Method to return the templates for managing districts
     * @access private
     * @return 
     */
    private function __ajaxManageDistricts()
    {
        return $this->objOps->ajaxManageDistricts();
    }
    
    /**
     *
     * Method to return the templates for adding/editing districts
     * @access private
     * @return 
     */
    private function __ajaxAddEditDistrict()
    {
        return $this->objOps->ajaxAddEditDistrict();
    }
    
    /**
     *
     * Method to delete districts
     * 
     * @access private
     * @return void 
     */
     private function __deletedistrict()
     {
        $id = $this->getParam('id');
        $pid = $this->getParam('pid');
        $this->objDBdistricts->deleteDistrict($id);

        return $this->nextAction('manage', array('type' => 'd', 'pid' => $pid));
     }
     
     /**
      *
      * Method to add a district
      * 
      * @access private
      * @return VOID 
      */
     private function __district()
     {
        $data = array();
        $id = $this->getParam('id');
        $pid = $this->getParam('province_id');
        $data['province_id'] = $pid;
        $data['name'] = $this->getParam('name');

        if (!empty($id))
        {
            $data['modified_by'] = $this->objUser->PKId();
            $data['date_modified'] = date('Y-m-d H:i:s');
            $this->objDBdistricts->updateDistrict($id, $data);             
        }
        else
        {
            $data['created_by'] = $this->objUser->PKId();
            $data['date_created'] = date('Y-m-d H:i:s');
            $this->objDBdistricts->insertDistrict($data);
        }

        return $this->nextAction('manage', array('type' => 'd', 'pid' => $pid));         
     }
    
    /**
     *
     * Method to return the templates for adding/editing provinces
     * @access private
     * @return 
     */
    private function __ajaxAddEditProvince()
    {
        return $this->objOps->ajaxAddEditProvince();
    }
    
     /**
      *
      * Method to add a district
      * 
      * @access private
      * @return VOID 
      */
     private function __province()
     {
        $data = array();
        $id = $this->getParam('id');
        $data['name'] = $this->getParam('name');

        if (!empty($id))
        {
            $data['modified_by'] = $this->objUser->PKId();
            $data['date_modified'] = date('Y-m-d H:i:s');
            $this->objDBprovinces->updateProvince($id, $data);             
        }
        else
        {
            $data['created_by'] = $this->objUser->PKId();
            $data['date_created'] = date('Y-m-d H:i:s');
            $this->objDBprovinces->insertProvince($data);
        }

        return $this->nextAction('manage', array('type' => 'p'));         
     }
    
    /**
     *
     * Method to delete province
     * 
     * @access private
     * @return void 
     */
     private function __deleteprovince()
     {
        $id = $this->getParam('id');
        $this->objDBprovinces->deleteProvince($id);

        return $this->nextAction('manage', array('type' => 'p'));
     }
     
    /**
     *
     * Method to return the principals template
     * 
     * @access private
     * @return void 
     */
     private function __principals()
     {
        return 'principals_tpl.php';
     }
     
    /**
     *
     * Method to return the data for finding principals
     * @access private
     * @return 
     */
    private function __ajaxFindPrincipals()
    {
        return $this->objOps->ajaxFindPrincipals();
    }
    
    /**
     *
     * Method to link an existing user as a principal
     * 
     * @access private 
     * @return VOID
     */
    private function __addprincipal()
    {
        $sid = $this->getParam('sid');
        $cancel = $this->getParam('add_cancel');
        if ($cancel == 'Cancel')
        {
            return $this->nextAction('show', array('sid' => $sid, 'tab' => 1));
        }

        $id = $this->getParam('id');
        $data['id'] = $sid;
        $data['principal_id'] = $id;
        $data['modified_by'] = $this->objUser->PKId();
        $data['date_modified'] = date('Y-m-d H:i:s');
        $this->objDBschools->updateSchool($sid, $data);
        
        $user = $this->objUserAdmin->getUserDetails($id);
        $puid = $user['puid'];

        $groupId = $this->objGroups->getId('Principals');
        $this->objGroups->addGroupUser($groupId, $puid);
        
        return $this->nextAction('show', array('sid' => $sid, 'tab' => 1));
    }
    
    /**
     *
     * Method to delete a principal
     * 
     * @access private
     * @return VOID 
     */
    private function __deleteprincipal()
    {
        $sid = $this->getParam('sid');
        $data['principal_id'] = '';
        $this->objDBschools->updateSchool($sid, $data);
        
        return $this->nextAction('show', array('sid' => $sid, 'tab' => 1));
    }
    
    /**
     *
     * Method to validate principal data
     * 
     * @access private
     * @return VOID 
     */
    private function __validateprincipal()
    {
        $sid = $this->getParam('sid');
        $cancel = $this->getParam('new_cancel');
        if ($cancel == 'Cancel')
        {
            return $this->nextAction('show', array('sid' => $sid, 'tab' => 1));
        }
        
        $data = array();
        $data['sid'] = $sid;
        $data['country'] = $this->getParam('country');
        $data['email_address'] = $this->getParam('email_address');
        $data['title'] = $this->getParam('title');
        $data['first_name'] = $this->getParam('first_name');
        $data['last_name'] = $this->getParam('last_name');
        $data['gender'] = $this->getParam('gender');
        $data['mobile_number'] = $this->getParam('mobile_number');
        $data['username'] = $this->getParam('username');
        $data['password'] = $this->getParam('password');
        $data['confirm_password'] = $this->getParam('confirm_password');
       
        $errorsFound = $this->objOps->validatePrincipal($data);

        if ($errorsFound == FALSE)
        {
            $this->objOps->savePrincipal($data);
            return $this->nextAction('show', array('sid' => $sid, 'tab' => 1));
        }
        else
        {
            return $this->nextAction('principals', array('sid' => $sid));
        }
    }
         
    /**
     *
     * Method to validate the username
     * @access private
     * @return 
     */
    private function __ajaxUsername()
    {
        return $this->objOps->ajaxUsername();
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
    private function __validAction(& $action)
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
    private function __getMethod(& $action)
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
            default:
                return TRUE;
                break;
        }
     }
}
?>
