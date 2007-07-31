<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class api extends controller
{

	public $objRPC;

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
    
    public function dispatch($action = Null) 
    {
        switch ($action) {
            default:
            	// cannot require any login, as remote clients use this. Auth is done internally
            	$this->requiresLogin(FALSE);
            	// start the server.
            	$this->objRPC->serve();   
            	// break to be pedantic, although not strictly needed.    
            	break;
        }
    }
    
     public function requiresLogin() 
     {
            return FALSE;
     }
}
?>