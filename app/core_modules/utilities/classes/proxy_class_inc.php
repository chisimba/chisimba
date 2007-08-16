<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Simple class to manipulate the proxy string
 *
 * @category  Chisimba
 * @author Paul Scott
 * @author Jonathan Abrahams
 * @package utilities
 * @copyright 2001 - 2003 Brent R. Matzelle
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class proxy extends object
{
    /**
    * @var Proxy server name.
    */
    var $server = NULL;
    /**
    * @var Proxy server port.
    */
    var $port = NULL;
    /**
    * @var Proxy server user password.
    */
    var $password = NULL;
    /**
    * @var Proxy server username.
    */
    var $username = NULL;

    /**
 	* Method to split up and return
 	* the components of a proxy string
 	* @param string $proxy - (Optional) the proxy string in std format.
    */
    function init()
    {
       // Automatically get the properties from the database.
       $this->getProxy();
    }
    /**
 	* Method to split up and return 
 	* the components of a proxy string
 	* @param string $proxy - (Optional) the proxy string in std format.
 	* @return array $proxycomponents 
 	* associative array of components
	*/
	function getProxy($proxy=NULL)
	{
        // Get the proxy string from the config object.
        if( is_null($proxy) ) {
            $objConfig = $this->getObject( 'altconfig', 'config' );
            $proxy = $objConfig->getProxy();
        }
        
        // Initialise
        $proxycomponents = array(
            'proxyserver'=>NULL,
            'proxyport'=>NULL,
            'proxypassword'=>NULL,
            'proxyusername'=>NULL );

        // if it exists
        if( !empty($proxy) ) {
            // Extract
    		@$first = explode("@",$proxy);
    		@$a = explode(":",$first[0]);
    		@$b = explode(":",$first[1]);
    		@$c = str_replace("//","",$a[1]);
    		//build the associative array
    		$proxycomponents['proxyserver'] =  $this->port = $b[0];
    		$proxycomponents['proxyport'] =  $this->server = $b[1];
    		$proxycomponents['proxypassword'] =  $this->password = $a[2];
    		$proxycomponents['proxyusername'] =  $this->username = $c;
    	}
		return $proxycomponents;
	}
}//end class
?>