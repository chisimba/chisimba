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
* @author Derek Keats
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
* Example:
*   $objCurl = $this->getObject('curl', 'utilities');
*   $objCurl->initializeCurl($url);
*   echo $objCurl->getData();
*   $objCurl->closeCurl();
* Example:
*    echo $objCurl->exec('http://ws.geonames.org/search?name_equals=Walvisbaai&style=full');
*/

class curlwrapper extends object
{
    /**
    * @var array $proxyInfo Array Containing Proxy Details
    * @access private
    */
    private $proxyInfo;

    public $ch;
    public $options = array();

    /**
    * Constructor
    */
    public function init()
    {
        $this->setupProxy();
    }

    /**
    *
    * Method to extract the proxy settings from the chisimba settings
    * and prepare them for use in curl.
    *
    */
    public function setupProxy()
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

    public function initializeCurl($url)
    {
        // Setup URL for Curl
        $this->ch = curl_init($url);
    }

    public function closeCurl()
    {
        // Close the CURL
        curl_close($this->ch);
    }

    /**
    * Set a curl option.
    *
    * @link http://www.php.net/curl_setopt
    * @param mixed $theOption One of the valid CURLOPT defines.
    * @param mixed $theValue the value of the curl option.
    *
    */
    public function setopt($theOption, $theValue)
    {
        curl_setopt($this->ch, $theOption, $theValue) ;
        $this->options[$theOption] = $theValue ;
    }

    public function setProxy()
    {
        // Add Server Proxy if it exists
        if ($this->proxyInfo['server'] != '') {
            $this->setopt($this->ch, CURLOPT_PROXY, $this->proxyInfo['server']);
        }
        // Add Port Proxy if it exists
        if ($this->proxyInfo['port'] != '') {
            $this->setopt($this->ch, CURLOPT_PROXYPORT, $this->proxyInfo['port']);
        }
        // Add Username for Proxy if it exists
        if ($this->proxyInfo['username'] != '') {
            $userNamePassword = $this->proxyInfo['username'];
            // Add Password Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword .= ':'.$this->proxyInfo['password'];
            }
            $this->setopt ($this->ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
        }
    }

    /**
     *
     * Make sure all the options are set first
     *
     */
    public function getUrl()
    {
        // Get the page
        //curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
        //curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($this->ch);
        return $data;
    }

    public function sendData($url, $postargs=FALSE, $username, $password)
    {
        $this->ch = curl_init($url);
        //$this->setProxy();
        if($postargs !== FALSE){
            curl_setopt ($this->ch, CURLOPT_POST, 1);
            curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $postargs);
        }
        if($username !== FALSE && $password !== FALSE) {
            log_debug("Setting $username and $password");
            curl_setopt($this->ch, CURLOPT_USERPWD, $username.':'.$password);
        }
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_NOBODY, 0);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        $response = curl_exec($this->ch);
        log_debug($response);
        $this->responseInfo=curl_getinfo($this->ch);
        curl_close($this->ch);

        if(intval($this->responseInfo['http_code'])==200){
            if(class_exists('SimpleXMLElement')){
                $xml = new SimpleXMLElement($response);
                return $xml;
            }else{
                return $response;
            }
        }else{
            return FALSE;
        }
    }

    /**
     * Performs an HTTP POST via CURL.
     *
     * @access public
     * @param  string  $url          The URL to post to.
     * @param  mixed   $postargs     The raw post data as a string or an associative array of arguments.
     * @param  boolean $responseCode Return the HTTP Response Code instead of the body of the HTTP Response.
     * @return string  Either the HTTP Response Code or the body of the HTTP response (default).
     */
    public function postCurl($url, $postargs, $responseCode=FALSE) {
        $this->userName = FALSE;
        $this->password = FALSE;
        $this->user_agent = 'Chisimba';
        $ch = curl_init($url);
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
            $this->userName = $this->proxyInfo['username'];

            // Add Password Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword .= ':'.$this->proxyInfo['password'];
                $this->password = $this->proxyInfo['password'];
            }

            curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_USERAGENT, "Chisimba (http://avoir.uwc.ac.za)");
        curl_setopt($ch, CURLOPT_REFERER, $this->uri(''));
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt ($ch, CURLOPT_POST, TRUE);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);

        $response = curl_exec($ch);

        if ($responseCode) {
            $return = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } elseif ($response != '') {
            $return = $response;
        } else {
            $return = curl_error($ch);
        }

        curl_close($ch);

        return $return;
    }

    public function process($url,$postargs=FALSE)
    {
        $this->userName = FALSE;
        $this->password = FALSE;
        $this->user_agent = 'Chisimba';
        $ch = curl_init($url);

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
            $this->userName = $this->proxyInfo['username'];

            // Add Password Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword .= ':'.$this->proxyInfo['password'];
                $this->password = $this->proxyInfo['password'];
            }

            curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
        }

        if($postargs !== FALSE){
            curl_setopt ($ch, CURLOPT_POST, TRUE);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
        }

        if($this->userName !== FALSE && $this->password !== FALSE)
            curl_setopt($ch, CURLOPT_USERPWD, $this->userName.':'.$this->password);

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $response = curl_exec($ch);

        $this->responseInfo=curl_getinfo($ch);
        curl_close($ch);


        if(intval($this->responseInfo['http_code'])==200){
            if(class_exists('SimpleXMLElement')){
                $xml = new SimpleXMLElement($response);
                return $xml;
            }else{
                return $response;
            }
        }else{
            return FALSE;
        }
    }


    /**
    * Method to transfer/get contents of a page
    * @param string $url URL of the Page
    * @return string contents of the page
    */
    public function exec($url)
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

        // Get the page
        $data = curl_exec ($ch);

        // Close the CURL
        curl_close($ch);

        // Return Data
        return $data;
    }
}
?>
