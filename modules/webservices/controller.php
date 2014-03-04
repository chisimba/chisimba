<?php
/**
 * webservices for Chisimba
 * 
 * webservices control Interface for the Chisimba Framework
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
 * @package   webservices
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11952 2008-12-29 21:29:49Z charlvn $
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
 * webservices class
 * 
 * webservices control class for Chisimba
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <<pscott@uwc.ac.za>>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class webservices extends controller
{

    /**
     * SCA Object
     * 
     * @var    object
     * @access public
     */
	public $objSCA;

    /**
     * Init method
     * 
     * Standard Chisimba Init() method
     *   
     * @access public
     */
	public function init() 
    {
        try {
        	$this->objSCA = $this->getObject('server');
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
    public function dispatch($action = Null) 
    {
        switch ($action) {
            default:
            	// cannot require any login, as remote clients use this. Auth is done internally
            	$this->requiresLogin(FALSE);
            	// start the server.
            	//return $this->objSCA;   
            	// break to be pedantic, although not strictly needed.    
            	break;
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