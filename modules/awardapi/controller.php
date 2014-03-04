<?php
/**
 * Controller for AWARD API
 * 
 * Chisimba controller for the AWARD API System
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
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 125 2008-08-15 14:21:14Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       api
 */

if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * AWARD API controller
 * 
 * AWARD API System controller class for Chisimba
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class awardapi extends controller
{

    /**
     * XML-RPC Object
     * @var    object
     * @access public
     */
	public $objRPC;

    /**
     * Init method
     * 
     * Standard Chisimba Init() method
     * 
     * @return void  
     * @access public
     */
	public function init() 
    {
        try {
        	$this->objRPC = $this->getObject('xmlrpcapi');
        }
        catch(customException $e)
        {
        	customException::cleanUp();
        	exit;
        }
    }
    
    /**
     * dispatch method
     * 
     * Standard Chisimba dispatch method for parsing the querystring
     * 
     * @param  string $action The REQUEST string for action
     * @return void   
     * @access public 
     */
    public function dispatch($action = null) 
    {
        try {
        switch ($action) {
			case "test":
				//$obj = $this->getObject('apiunion');
				//$vibe = $obj->deleteUnion(1815);
				//var_dump($vibe);
				echo "done";
				break;
            case "serveapi":
            default:
            	// start the server.
            	$this->objRPC->serve();   
            	break;
        }
        } catch (customException $e)
        {
        	echo "<br /> $e->uri";
        	customException::cleanUp();
        	exit;
        }
    }
    
    /**
     * Login override
     * 
     * Method to override the login requirement for the framework, as login is done remotely through the API itself
     * 
     * @return boolean Return 
     * @access public 
     */
     public function requiresLogin() 
     {
            return FALSE;
     }
}
?>