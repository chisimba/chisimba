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
        	$this->objDbRouting = $this->getObject('dbrouting', 'internalmail');
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
	//	try{
			//$param = $params->getParam(0);
		//if (!XML_RPC_Value::isValue($param)) {
          //  log_debug($param);
    	//}
    	//$folderId = $param->scalarval();
    	
    	$mailStruct = array();
    	$resarr = $this->objDbRouting->getAllMail("init_1", null, null);
    	//var_dump($resarr);
      	foreach($resarr as $res)
    	{
			$struct = new XML_RPC_Value(array(
    			new XML_RPC_Value($res['fullName'], "string"),
    			new XML_RPC_Value($res['subject'], "string"),
    			new XML_RPC_Value($res['date_sent'], "string")), "array");
    		$mailStruct[] = $struct;
    	}
    	$mailArray = new XML_RPC_Value($mailStruct,"array");
	//var_dump($mailArray);
    	return new XML_RPC_Response($mailArray);
		/*} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }*/
	}
}
?>