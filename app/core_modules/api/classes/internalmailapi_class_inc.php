<?php

/**
 * xulmail 1.0 interface class
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
 * @author    Brent van Rensburg
 * @copyright 2008
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
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
 * Class to provide internalmail API 1.0 XML-RPC functionality to Chisimba
 * 
 * @category  Chisimba
 * @package   api
 * @author    Brent van Rensburg>
 * @copyright 2008
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class internalmailapi extends object
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
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
			//database abstraction object
			$this->objModules = $this->getObject('modules', 'modulecatalogue');
			$this->isReg = $this->objModules->checkIfRegistered('internalmail');
			if($this->isReg === TRUE)
			{
			    $this->objDbRouting = $this->getObject('dbrouting', 'internalmail');
			    $this->objDbFolders = $this->getObject('dbfolders', 'internalmail');
				$this->objDbEmail = $this->getObject('dbemail', 'internalmail');
			}
			$this->objUser = $this->getObject('user', 'security');
        	
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	


    /**
     * get all mail
     * 
     * Gets a list of all mail for a user
     * 
     * @return object 
     * @access public
     */
	public function internalMailGetAll($params)
	{
	    try{
		$mailStruct = array();
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
		    log_debug($param);
		}
		$username = $param->scalarval();
		$userId = $this->objUser->getUserId($username);
		$mailStruct = array();
		$resarr = $this->objDbRouting->getAllMail("init_1", array(
				    0 => 'messageListTable',
				    1 => 1,
				    2 => 'DESC'
				), null, $userId);
		//var_dump($resarr);
		foreach($resarr as $res)
		{
		    $struct = new XML_RPC_Value(array(
				//emai_id
				new XML_RPC_Value($res['email_id'], "string"),
				//fullname
				new XML_RPC_Value($res['sender_id'], "string"),
				//recipient_list
				new XML_RPC_Value($this->_getUsernamesList($res['sender_id']), "string"),				
				//subject
				new XML_RPC_Value($res['subject'], "string"),
				//message
				new XML_RPC_Value($res['message'], "string"),
				//date sent
				new XML_RPC_Value($res['date_sent'], "string"),
				//folder_id
				new XML_RPC_Value($res['folder_id'], "string"),
				//routing id
				new XML_RPC_Value($res['routing_id'], "string"),
				//sent mail
				new XML_RPC_Value((string)$res['sent_mail'], "string"),
				//read mail
				new XML_RPC_Value((string)$res['read_mail'], "string"),
				//date read
				new XML_RPC_Value($res['date_read'], "string")
				
				),
				
			    "array");
		    $mailStruct[] = $struct;
		}
		$mailArray = new XML_RPC_Value($mailStruct,"array");
		//var_dump($mailArray);
		return new XML_RPC_Response($mailArray);
	    } catch(customException $e) {
		echo customException::cleanUp();
		die($e);
	    }
	}
	
	/**
	* Method to build the recipient list
	* @param string $list
	* @access private
	* @return string
	*/
	private function _getUsernamesList($list)
	{
		$newList = split("\|", $list);
		//var_dump($newList);
		$newArr = "";
		$cnt = 0;
		$max = count($newList);
		foreach($newList as $item)
		{
			$cnt++;
			$newArr .= $this->objUser->userName($item);
			if($max == 1)
			{
				return $newArr;
			}
			elseif ( $cnt != $max)
			{
				$newArr .= "|";
			}
			
		}
		return $newArr;
		
	}
	
	
    /**
     * Get a list of all folders 
     * 
     * Method for listing all rows for the current user
     * 
     * @return object 
     * @access public
     */
	public function mailListFolders($params)
	{
		try{
	    	$mailStruct = array();
	    	$resarr = $this->objDbFolders->listFoldersForUser("1");
	    	//var_dump($resarr);
	      	foreach($resarr as $res)
	    	{
				$struct = new XML_RPC_Value(array(
	    			new XML_RPC_Value($res['id'], "string"),
	    			new XML_RPC_Value($res['user_id'], "string"),
	    			new XML_RPC_Value($res['folder_name'], "string"),
	    			new XML_RPC_Value($res['updated'], "string"),
	    			new XML_RPC_Value($res['puid'], "string"),
	    			new XML_RPC_Value($res['allmail'], "string"),
	    			new XML_RPC_Value($res['unreadmail'], "string")), "array");
	    		$foldersStruct[] = $struct;
	    	}
	    	$foldersArray = new XML_RPC_Value($foldersStruct,"array");
			//var_dump($foldersArray);
	    	return new XML_RPC_Response($foldersArray);
		} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	}
	
	/**
     * Adds a new folder to the database
     * 
     * Method for adding a row to the database.
     * 
     * @return object 
     * @access public
     */
	public function mailAddfolder($params)
	{
		try{
			$param = $params->getParam(0);
			if (!XML_RPC_Value::isValue($param)) {
	            log_debug($param);
	    	}
	    	$folderName = $param->scalarval();
	    	
	    	$mailStruct = array();
	    	$this->objDbFolders->addFolder($folderName);
	    	
	    	$struct = new XML_RPC_Value(array(
	    			new XML_RPC_Value("success", "string")), "array");
	    	$mailStruct[] = $struct;

	    	$responce = new XML_RPC_Value($mailStruct, "array");
			//var_dump($foldersArray);
	    	return new XML_RPC_Response($responce);
		} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	}
	
	
	/**
	* Method to insert  a mail
	* @params array $params  
	* @return int
	* @access public
	*/
	public function sendMail($params)
	{
		try{
            
			$param = $params->getParam(0);
			if (!XML_RPC_Value::isValue($param)) {
				log_debug($param);
			}
			$emailId = $param->scalarval();
			
			$param = $params->getParam(1);
			if (!XML_RPC_Value::isValue($param)) {
				log_debug($param);
			}
			$senderId = $param->scalarval();
			$userId = $this->objUser->getUserId($senderId);
			
			$param = $params->getParam(2);
			if (!XML_RPC_Value::isValue($param)) {
				log_debug($param);
			}
			$recipientList = $param->scalarval();
			
			$param = $params->getParam(3);
			if (!XML_RPC_Value::isValue($param)) {
				log_debug($param);
			}
			$subject = $param->scalarval();
			
			$param = $params->getParam(4);
			if (!XML_RPC_Value::isValue($param)) {
				log_debug($param);
			}
			$message = $param->scalarval();
			
			
			$attachement = "";
			
			$this->objDbEmail->sendMail($recipientList, $subject, $message, $attachment, $userId, $emailId);
			$emailStruct = new XML_RPC_Value(array(
					new XML_RPC_Value($emailId, "string")), "array");
			
			return new XML_RPC_Response($emailStruct);
        } catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	
	
	
	
	}
	
	
}
?>