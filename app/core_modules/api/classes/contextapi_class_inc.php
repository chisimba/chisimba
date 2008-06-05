<?php

/**
 * Context interface class
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
class contextapi extends object
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

			$this->objUser = $this->getObject('user', 'security');

		} 
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}

	/**
	* Method to get the list of context for a user
	* @param string $username The username 
	* @access public
	* @return array
	*/
	public function getContextList($username)
	{

		try{

			if($this->getSession('isauthenticated'))
			{
				$objManageContext = $this->getObject('managegroups', 'contextgroups');
				$contextList = $objManageContext->userContexts($this->objUser->getUserId($username));
				$contextStruct = array();

				foreach($contextList as $context)
				{

					$struct = new XML_RPC_Value(array(			
						new XML_RPC_Value($context['contextcode'], "string"),
						new XML_RPC_Value($context['menutext'], "string")), "array");
		    		$contextStruct[] = $struct;
							
				}

				$contextArray = new XML_RPC_Value($contextStruct,"array");
				var_dump($contextArray);
	    		return new XML_RPC_Response($contextArray);
			}

		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}


	/**
	* Method to join a context
	* @param $userId The userId
	* @access public
	* @return boolean
	*/
	public function joinContext($userId)
	{
		try{

		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}

	/**
	* Method to leave a context
	* @param $userId The userId
	* @access public
	* @return boolean
	*/
	public function leaveContext($userId)
	{
		try{

		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}

	/**
	* Method to the list of module that the context is using
	* @param $contextCode The context Code
	* @access public
	* @return array
	*/
	public function getContextModules($contextCode)
	{
		try{

		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}


}


?>
