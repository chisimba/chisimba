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
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
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

		try{
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
            $objAuth = $this->getObject('user', 'security');

			//Authenticate the user
            
            //$username = 'aaa'; $password = 'dd';
			$result = (int) $objAuth->authenticateUser($username, $password);
            //var_dump($result);
			//$res = ($result) ? "some" : "thing";
            //var_dump($res);
			//set the session if the the user is authenticated
			//$this->setSession('isauthenticated', $result);
			
			$postStruct = new XML_RPC_Value($result, "int");
		//var_dump($postStruct);    	

  			return new XML_RPC_Response($postStruct);
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
                new XML_RPC_Value($res['userid'], "string"),
                new XML_RPC_Value($res['title'], "string"),
                new XML_RPC_Value($res['firstname'], "string"),    			
                new XML_RPC_Value($res['surname'], "string"),
                new XML_RPC_Value($res['pass'], "string"),
                new XML_RPC_Value($res['emailaddress'], "string"),
                new XML_RPC_Value($res['isactive'], "string"),
                new XML_RPC_Value($res['accesslevel'], "string")), "array");
              
            return new XML_RPC_Response($userStruct);
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}}
	
	}

}


?>
