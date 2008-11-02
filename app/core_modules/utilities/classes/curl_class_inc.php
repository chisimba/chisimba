<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check


/**
* Curl is a tool for transferring files with URL syntax
*
* This class is a wrapper for PHP's CURL functions integrated with
* Chisimba's Proxy Configurations. Developers can simply instantiate
* this class and request the page they want.
*
* @category  Chisimba
* @package   utilities
* @author Tohir Solomons
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
* Example:
*   $objCurl = $this->getObject('curl', 'utilities');
*    echo $objCurl->exec('http://ws.geonames.org/search?name_equals=Walvisbaai&style=full');
*/

class curl extends object
{
    /**
    * @var array $proxyInfo Array Containing Proxy Details
    * @access private
    */
    private $proxyInfo;
    
    /**
    * Constructor
    */
    public function init()
    {
        // Load Config Object
        $objConfig = $this->getObject('altconfig', 'config');
        
        // Get Proxy String
        $proxy = $objConfig->getProxy();
        
        // Remove http:// from beginning of string
        $proxy =  preg_replace('%\Ahttp://%i', '', $proxy);
        
        // Create Empty Array
        $this->proxyInfo = array('username'=>'','password'=>'','server'=>'','port'=>'',);
        
        // Check if string has @, indicator of username/password and server/port
        if (preg_match('/@/i', $proxy)) {
            
            // Split string into username and password
            preg_match_all('/(?P<userinfo>.*)@(?P<serverinfo>.*)/i', $proxy, $result, PREG_PATTERN_ORDER);
            
            // If it has user information, perform further split
            if (isset($result['userinfo'][0])) {
                // Split at : to get username and password
                $userInfo = explode(':', $result['userinfo'][0]);
                
                // Record username if it exists
                $this->proxyInfo['username'] = isset($userInfo[0]) ? $userInfo[0] : '';
                // Record password if it exists
                $this->proxyInfo['password'] = isset($userInfo[1]) ? $userInfo[1] : '';
            }
            
            // If it has server information, perform further split
            if (isset($result['serverinfo'][0])) {
                // Split at : to get server and port
                $serverInfo = explode(':', $result['serverinfo'][0]);
                
                // Record server if it exists
                $this->proxyInfo['server'] = isset($serverInfo[0]) ? $serverInfo[0] : '';
                // Record port if it exists
                $this->proxyInfo['port'] = isset($serverInfo[1]) ? $serverInfo[1] : '';
            }
            
        // Else only has server and port details
        } else {
            // Split at : to get server and port
            $serverInfo = explode(':', $proxy);
            
            // Record server if it exists
            $this->proxyInfo['server'] = isset($serverInfo[0]) ? $serverInfo[0] : '';
            // Record port if it exists
            $this->proxyInfo['port'] = isset($serverInfo[1]) ? $serverInfo[1] : '';
        }
    }

    
    /**
    * Method to transfer/get contents of a page
    * @param string $url URL of the Page
    * @return string contents of the page
    */
    public function exec($url, $postargs=FALSE)
    {
        // Setup URL for Curl
        $ch = curl_init($url);
        
        // More Curl settings
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // Add Server Proxy if it exists
        if ($this->proxyInfo['server'] != '') {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyInfo['server']);
        }
        
        // Add Port Proxy if it exists
        if ($this->proxyInfo['port'] != '') {
            curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyInfo['port']);
        }
        
        // Add Username for Proxy if it exists
        if ($this->proxyInfo['username'] != '') {
            $userNamePassword = $this->proxyInfo['username'];
            
            // Add Password Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword .= ':'.$this->proxyInfo['password'];
            }
            
            curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
        }
        
        if($postargs !== FALSE){
            curl_setopt ($ch, CURLOPT_POST, TRUE);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
        }
        
        // Get the page
        $data = curl_exec ($ch);
        
        // Close the CURL
        curl_close($ch);
        
        // Return Data
        return $data;
    }
}
?>