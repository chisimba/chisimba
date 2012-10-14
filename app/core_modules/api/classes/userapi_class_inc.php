<?php

/**
 * User interface class
 * 
 * XML-RPC (Remote Procedure call) class
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
 * @package   api
 * @author    Wesley Nitsckie
 * @copyright 2008
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * xulmail 1.0 XML-RPC Class
 * 
 * Class to provide forum API 1.0 XML-RPC functionality to Chisimba
 * 
 * @category  Chisimba
 * @package   api
 * @author    Wesley Nitsckie
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class userapi extends object
{

    /**
    * init method
    * 
    * Standard Chisimba init method
    * 
    * @return void  
    * @access public
    */
    public function init()
    {
        try{

            $this->objUser = $this->getObject('user', 'security');
            $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
        }
        catch (customException $e)
        {
            customException::cleanUp();
            exit;
        }
        if($this->objModuleCat->checkIfRegistered('rackspacecloudfiles')) {
            // pull up the rackspace api module
            $this->objCloudfiles = $this->getObject('cloudfilesops', 'rackspacecloudfiles');
        }
    }

    /**
    * Try to login the user
    * @param string $username The username 
    * @param
    * @access public
    * @return array
    */
    public function tryLogin($params)
    {
        try {
            $param = $params->getParam(0);
            if (!XML_RPC_Value::isValue($param)) {
                log_debug($param);
            }
            $username = $param->scalarval();
            
            $param = $params->getParam(1);
            if (!XML_RPC_Value::isValue($param)) {
                log_debug($param);
            }
            $password = $param->scalarval();
            
            //Authenticate the user
            $result = (int) $this->objUser->authenticateUser($username, $password);
            $userid = $this->objUser->getUserId($username);
            if($result == 1) {
                //check that the cloudfiles cdn is available 
                if($this->objModuleCat->checkIfRegistered('rackspacecloudfiles')) {
                    // get a list of containers
                    $containerarr = $this->objCloudfiles->containerList();
                    // check if userid is in the list
                    if(!in_array($username, $containerarr)) {
                        // optionally create a container for this user
                        $this->objCloudfiles->createContainer($userid);
                        $postStruct = new XML_RPC_Value($result, "int");
                        return new XML_RPC_Response($postStruct);
                    }
                    else {
                        $postStruct = new XML_RPC_Value($result, "int");
                        return new XML_RPC_Response($postStruct);
                    }
                }
                $postStruct = new XML_RPC_Value($result, "int");
                return new XML_RPC_Response($postStruct);
            }
            else { 
                $postStruct = new XML_RPC_Value($result, "int");
                return new XML_RPC_Response($postStruct);
            }
        }
        catch (customException $e)
        {
            customException::cleanUp();
            exit;
        }
    }


    public function getUserIdFromName($params)
    {
        $objAuth = $this->getObject('user', 'security');
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $username = $param->scalarval();
            
        $uid = $objAuth->getUserId($username);
        
        $val = new XML_RPC_Value($uid, 'string');
        return new XML_RPC_Response($val);
    }
    
    /**
    * Method to get the user details
    * @params array $params
    * @access public
    * @return array
    */
    public function getUserDetails($params)
    {
        try{
            
            $param = $params->getParam(0);
            if (!XML_RPC_Value::isValue($param)) {
                log_debug($param);
            }
            $username = $param->scalarval();
            
            $res = $this->objUser->lookupData($username);
            
           
            $userStruct = new XML_RPC_Value(array(
                new XML_RPC_Value($res['username'], "string"),
                new XML_RPC_Value($res['userid'], "string"),
                new XML_RPC_Value($res['title'], "string"),
                new XML_RPC_Value($res['firstname'], "string"),                
                new XML_RPC_Value($res['surname'], "string"),
                new XML_RPC_Value($res['pass'], "string"),
                new XML_RPC_Value($res['emailaddress'], "string")
               // new XML_RPC_Value($res['isactive'], "string"),
                //new XML_RPC_Value($res['accesslevel'], "string")
                ), "array");
              
            return new XML_RPC_Response($userStruct);
        }
        catch (customException $e)
        {
            customException::cleanUp();
            exit;
        }
    
    }

    public function regUser($params) {
        $objUAModel = $this->getObject('useradmin_model2', 'security');
        $objLangCode = $this->getObject('languagecode', 'language');
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $userid = $param->scalarval();

        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $username = $param->scalarval();

        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $password = $param->scalarval();

        $param = $params->getParam(3);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $title = $param->scalarval();

        $param = $params->getParam(4);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $firstname = $param->scalarval();

        $param = $params->getParam(5);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $surname = $param->scalarval();

        $param = $params->getParam(6);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $email = $param->scalarval();

        $param = $params->getParam(7);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $sex = $param->scalarval();

        $param = $params->getParam(8);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $country = $param->scalarval();
        // expecting a country code, so lets look that up now...
        $list = $objLangCode->countryListArr();
        $c = array_keys($list, $country);

        $param = $params->getParam(9);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $cellnumber = $param->scalarval();

        $param = $params->getParam(10);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $staffnumber = $param->scalarval();

        if(!isset($userid) || $userid == '') {
            $userid = $objUAModel->generateUserId();
        }
        // check if the username is available
        if( $objUAModel->usernameAvailable($username) == TRUE && $objUAModel->emailAvailable($email) == TRUE && $objUAModel->useridAvailable($userid) == TRUE) {
            $res = $objUAModel->addUser($userid, $username, $password, $title, $firstname, $surname, $email, $sex, $c[0], $cellnumber, $staffnumber, $accountType='api', $accountstatus='1');
            //check that the cloudfiles cdn is available 
            if($this->objModuleCat->checkIfRegistered('rackspacecloudfiles')) {
                // get a list of containers
                $containerarr = $this->objCloudfiles->containerList();
                // check if userid is in the list
                if(!in_array($username, $containerarr)) {
                    // optionally create a container for this user
                    $this->objCloudfiles->createContainer($username);
                    log_debug("created container for user $username");
                }
            }
            $val = new XML_RPC_Value($res, 'string');
        }
        else { 
            $res = "FALSE";
            $val = new XML_RPC_Value($res, 'string');
        }
        
        return new XML_RPC_Response($val);
    }

    public function getUserList() {
        $objGrpOps = $this->getObject('groupops', 'groupadmin');
        $objUser = $this->getObject('user', 'security');
        $users = $objGrpOps->getAllPermUsers();
        // get a list of usernames to return to the client only. 
        // @see $this->getUserDetails($username)
        foreach($users as $user) {
            $userarr[] = new XML_RPC_VALUE($objUser->username($user['auth_user_id'])." (".$objUser->fullname($user['auth_user_id']).")", "string");
        }
        // return the XML_RPC array type
        $val = new XML_RPC_Value($userarr, "array");
        return new XML_RPC_Response($val);
    }

    public function getUserListString() {
        $objGrpOps = $this->getObject('groupops', 'groupadmin');
        $users = $objGrpOps->getAllPermUsers();
        // get a list of usernames to return to the client only. 
        // @see $this->getUserDetails($username) 
        $list = NULL;
        foreach($users as $user) {
            $userarr[] = new XML_RPC_VALUE($objUser->username($user['auth_user_id'])." (".$objUser->fullname($user['auth_user_id']).")", "string");
        }
        // return the XML_RPC array type
        $val = new XML_RPC_Value($list, "string");
        return new XML_RPC_Response($val);
    }

    public function getCountryList() {
        $objLangCode = $this->getObject('languagecode', 'language');
        $arrOfCountries = $objLangCode->countryListArr();
        foreach($arrOfCountries as $count) {
            $countarr[] = new XML_RPC_VALUE($count, "string");
        }
        // return the XML_RPC array type
        $val = new XML_RPC_Value($countarr, "array");
        return new XML_RPC_Response($val);
    }

    public function getCommaCountryList() {
        $objLangCode = $this->getObject('languagecode', 'language');
        $arrOfCountries = $objLangCode->countryListArr();
        $list = NULL;
        foreach($arrOfCountries as $count) {
            $list .= $count.",";
        }
        // return the XML_RPC array type
        $val = new XML_RPC_Value($list, "string");
        return new XML_RPC_Response($val);
    }

    public function userInactivate($params) {
        $objUAModel = $this->getObject('useradmin_model2', 'security');
        $objUser = $this->getObject('user', 'security');
        
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $username = $param->scalarval();
        $username = explode("(", $username);
        
        if(!isset($username) || empty($username) ) {
            $val = new XML_RPC_Value("FALSE", "string");
        }
        else { 
            $username = trim($username[0]);
            $userdetails = $objUser->lookupData($username);
            $id = $objUser->PKId($userdetails['userid']);
            $res = $objUAModel->setUserAsInActive($id);
            log_debug($res);
            if($res) {
                $val = new XML_RPC_Value("TRUE", "string");
            }
            else {
                $val = new XML_RPC_Value("FALSE", "string");
            }
        }

        return new XML_RPC_Response($val);
    }

    public function userDelete($params) {
        $objUAModel = $this->getObject('useradmin_model2', 'security');
        $objUser = $this->getObject('user', 'security');
        
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $username = $param->scalarval();
        $username = explode("(", $username);
        
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $adminuser = $param->scalarval();

        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $adminpass = $param->scalarval();

        $adminuserdetails = $objUser->lookupData($adminuser);
        $adUserId = $adminuserdetails['userid'];
        if(!isset($username) || empty($username) ) {
            $val = new XML_RPC_Value("FALSE", "string");
        }
        // we check if the user and pass is 100% esse, by logging in
        elseif(!$objUser->authenticateUser($adminuser, $adminpass, FALSE)) {
            // fail
            $val = new XML_RPC_Value("FALSE", "string");
        }
        // finally check if the user is in the admin group to be deleting folks!
        elseif(!$objUser->inAdminGroup($adUserId)) {
            // fail
            $val = new XML_RPC_Value("FALSE", "string");
        }
        else { 
            $username = trim($username[0]);
            $userdetails = $objUser->lookupData($username);
            $id = $objUser->PKId($userdetails['userid']);
            $res = $objUAModel->apiUserDelete($id);
            if($res) {
                $val = new XML_RPC_Value("TRUE", "string");
            }
            else {
                $val = new XML_RPC_Value("FALSE", "string");
            }
        }

        return new XML_RPC_Response($val);
    }
}
?>
