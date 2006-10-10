<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997, 1998, 1999, 2000, 2001 The PHP Group             |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Graeme Merrall <graeme@inetix.com.au>                       |
// |                                                                      |
// +----------------------------------------------------------------------+
//
// $Id$

require_once 'PEAR.php';
require_once 'Cache/Function.php';

/**
 * NetGeo - determine geographic information on an internet address
 *
 * Can accept input of an AS number, an IP address or a host name
 * Input can be individual or an array of addresses
 *
 * $geo = new Net_Geo();
 * $geo->getRecord("php.net");
 * $geo->getRecord(array("php.net", "google.com"));
 *
 * Results returned are a single array of results if a string is passed in
 * or in the case of an array, a multi-dim array with the as the key
 *
 * Query service type (CAIDA or localizer) is not available as a constructer
 * to retain compatibility with existing CAIDA NetGeo classes (perl + java)
 *
 * @version 1.0
 * @package NetGeo
 * @author Graeme Merrall <graeme@inetix.com.au>
 */

define('NETGEO_INPUT_ERROR', 'INPUT_ERROR');
define('NETGEO_HTTP_ERROR', 'HTTP_ERROR');
define('NETGEO_NO_MATCH', 'NO MATCH');
define('NETGEO_NO_COUNTRY', 'NO_COUNTRY');
define('NETGEO_LIMIT_EXCEEDED', 'NETGEO_LIMIT_EXCEEDED');

class Net_Geo
{

    /**
     * Path to local cache file. 
     * Caching is compulsory to reduce load on CAIDA server
     *
     * @var string
     * @access public
     */
    var $cache_path = "/tmp/";

    /**
     * How long to wait befire rechecking cached entries in *days*
     * This should be comething nice and high
     *
     * @var in
     * @access public
     */
    var $cache_ttl = 30;

     /**
     * CAIDA only
     * 
     * Maximum length of time, in seconds, which will be allowed during a whois
     * lookup by the NetGeo server.
     * The actual default value is maintained by the server.
     *
     * @var int
     * @access public
     */
    var $default_timeout = 60;
    
    /**
     * CAIDA only
     * 
     * Location of the default netgeo server
     * If port not speicifed, defaults to 80
     *
     * @var string
     * @access public
     */
    var $default_server = "http://netgeo.caida.org/perl/netgeo.cgi";

    /**
     * localizer only
     * 
     * Location of the localizer data file
     *
     * @var string
     * @access public
     */
    var $localizer_data = "./demo.csv";

    /**
     * Type of service to use. May be either 'caida' or 'localizer'
     * Default is 'caida'
     *
     * @var string
     @ @access private
     */
    var $service;
    
    /**
     * Cache filename prefix
     *
     * @var string
     * @access private
     */
     var $cache_prefix = "netgeo";

    /**
     * CAIDA only
     * 
     * User Agent string.
     *
     * @var string
     * @access private
     */
    var $useragent = "PHP/NetGeo";

    /**
     * CAIDA only
     * 
     * Class version
     *
     * @var string
     * @access private
     */
    var $useragent_version = "1.0";

    /**
     * CAIDA only
     * 
     * How many targets can be read in at once
     * Should be enough for most everyone
     *
     * @var string
     * @access private
     */
    var $array_limit = 100;

    /**
     * Function cache object
     *
     * @var object
     * @access private
     */
     var $cache;

    /**
     * Name of global var for copying $this when calling function cache
     * This is needed for the cache function to operate correctly
     *
     * @var string
     * @access private
     */
     var $netgeo_global = "netgeo_global";


    /**
     * Constructor
     * Both $applicationName and $alternateServerUrl are for compatibility
     * with the perl and java netgeo classes.
     * I don't guarantee to use these variables
     *
     * @param string $applicationName    Application using the NetGeo class.
     * @param string $alternateServerUrl Alternate NetGeo server url
     * @return bool
     * @access public
     */
    function Net_Geo($applicationName="", $alternateServerUrl="")
    {
        $this->applicationName = $applicationName;
        $this->alternateServerUrl = $alternateServerUrl;
      
        // init cache object
        $this->cache = new Cache_Function('file', 
                                          array('cache_dir' => $this->cache_path, 
                                                'filename_prefix' => $this->cache_prefix
                                               ),
                                          $this->cache_ttl * 86400
                                         );

        return true;
    }

    
    function setService($service = "caida") {
        
        if ($service == "localizer") {
            
            if (@localizer_read($this->localizer_data, FALSE) == FALSE) {
                PEAR::raiseError("Can't read localizer data file ".$this->localizer_data);
                return false;
            }

        } elseif ($service == "caida") {
            
            // check to see if an alternate server URL is used
            if (!empty($alternateServerUrl)) {
                $this->default_server = $this->alternateServerUrl;
            }
    
            $this->useragent = sprintf("%s %s", $this->useragent, 
                                                $this->useragent_version
                                      );
    
            // set the custom user agent
            if (!empty($applicationName)) {
                // trim whitespace
                $applicationName = trim($applicationName);
    
                // also set the agent name
                $this->useragent = sprintf("%s/%s", $this->applicationName, 
                                                    $this->useragent
                                          );
            }
        
        } else {
            // return error
            return new PEAR_Error("No service specified");

        }


        $this->service = $service;
        return true;
            
    }
        
    /**
     * Gets a complete record for an address
     * Returns either a single or multidimentional arrray
     * if input is a string or an array respectively
     *
     * @param mixed $target Single or list of addresses
     * @return array
     * @access public
     */
    function getRecord($target)
    {
        return $this->_execute("getRecord", $target);
    }

    /**
     * Returns the 2-letter ISO 3166 country code
     * Returns NO_MATCH if the AS number has been looked up
     * but nothing was found in the whois lookups.
     * Returns NO_COUNTRY if the lookup returned a record 
     * but no country could be found.
     * Returns an empty string if nothing was found in the database
     *
     * @param string $target single address
     * @return array
     * @access public
     */
    function getCountry($target)
    {
        $result = $this->_execute("getCountry", $target);
        if (is_array($result)) {
            return $result["COUNTRY"];
        }

        return $result;
    }

    /**
     * Returns an array with keys LAT, LONG, LAT_LONG_GRAN, and STATUS.
     * Lat/Long will be (0,0) if the target has been looked up but there was no
     * match in the whois lookups, or if no address could be parsed from the
     * whois record, or if the lat/long for the address is unknown.
     * Returns an empty string if nothing was found in the database
     *
     * @param string $target single address
     * @return array
     * @access public
     */
    function getLatLong($target)
    {
        return $this->_execute("getLatLong", $target);
    }

    /**
     * Included here to make the NetGeo class as similar as possible to
     * the NetGeoClient.java interface.
     * It's probably just as easy for the user to extract lat and long directly
     * from the array. 
     *
     * @param string $target single address
     * @return double
     * @access public
     */
    function getLat($latLongRef)
    {
        if (is_array($latLongRef)) {
            $lat = $latLongRef["LAT"];
        } else {
            $lat = 0;
        }

        return sprintf("%.2f", $lat);
    }

    /**
     * Included here to make the NetGeo class as similar as possible to
     * the NetGeoClient.java interface.
     * It's probably just as easy for the user to extract lat and long directly
     * from the array
     *
     * @param string $target single address
     * @return double
     * @access public
     */
    function getLong($latLongHashRef)
    {
        if (is_array($latLongHashRef)) {
            $long = $latLongHashRef["LONG"];
        } else {
            $long = 0;
        }

        return sprintf("%.2f", $long);
    }

    /**
     * Interface to the public functions
     *
     * @param string $methodName Lookup method
     * @param mixed  $target     Address(es) to lookup
     * @return array
     * @access private
     */
    function _execute($methodName, $input)
    {

        // if we haven't got a service set, then do it now
        if (empty($this->service)) {
            $this->setService();
        }

        // Test the target strings in the input array.  Any targets not in
        // an acceptable format will have their STATUS field set to INPUT_ERROR.
        // This method will also store the standardized target into the array
        // for use as a key in the cache table.
        $inputArray = $this->_verifyInputFormatArray($methodName, $input);
        if (PEAR::isError($inputArray)) {
            return $inputArray;
        }

        $resultArray = $this->_processArray($methodName, $inputArray);
        
        // if there is only one array, move the whole thing up one
        if (count($resultArray) == 1) {
            $resultArray = $resultArray[0];
        }

        return $resultArray;        
    }   

    
    /**
     * Verify the type of the target argument and verify types of array elements
     * Also converts the input array into the start of the output array
     *
     * @param string $methodName Lookup method
     * @param mixed  $inputArray Address(es) to lookup
     * @return array or pear error object on failure
     * @access private
     */
    function _verifyInputFormatArray($methodName, $inputArray)
    {
        // makes sure that the input is an array
        // if length is > than ARRAY_LIMIT_LENTH then bomb ou
        if (count($inputArray) > $this->array_limit) {
            // raise an error
            $error = new PEAR_Error("Too many entries. Limit is ".$this->array_limit);
            return $error;
        }

        // convert into a useable array
        $inputArray = $this->_convertInputArray($inputArray);
        return $inputArray;
    }

    /**
     * Utility function to check what the input array
     * and to convert to a correct array format for processing
     *
     * @param mixed $inputArray Address array
     * @return array
     * @access private
     */
    function _convertInputArray($inputArray)
    {
        // first check the darn thing is actually an array
        if (!is_array($inputArray)) {
            $inputArray = array($inputArray);
        }
    
        // now convert to the correct array form
        foreach ($inputArray as $entry) {
            $returnArray[]["TARGET"] = $entry;
        }
        
        return $returnArray;

    }


    /** 
     * Main function that processes addresses
     *
     * It might be a good idea to move the caching up one level?
     * 
     * @param string $methodName Lookup method
     * @param array  $inputArray Formatted address array
     * @return array
     * @access private
     */
    function _processArray($methodName, $inputArray)
    {
        $i = 0;
        foreach ($inputArray as $entry) {
            $entry = $this->_verifyInputFormat($entry);
        
            if (isset($entry["TARGET"]) && !isset($entry["INPUT_ERROR"])) {

                // set up the cache work around
                $GLOBALS[$this->netgeo_global] =& $this;
                
                if ($this->service == "localizer") {

                    $response = $this->cache->call('localizer_search', $entry["TARGET"]);

                } else {

                    // else do the HTTP request
                    $url = sprintf("%s?method=%s&target=%s", $this->default_server,
                                                             $methodName, $entry["TARGET"]
                                  );

                    $response =& $this->cache->call($this->netgeo_global.'->_executeHttpRequest', $url);
    
                }

                if (!isset($response)) {
                    $entry["STATUS"] = NETGEO_HTTP_ERROR;
                }

                // parse it all into something useful
                // at this point we should look for NETGEO_LIMIT_EXCEEDED as well
                $dataArray[$i] = $this->_processResult($response);
                
            } else {
                $dataArray[$i] = $entry;
            }

            $i++;
        }
        
        if (is_array($dataArray)) {
            return $dataArray;
        } else {
            return array("STATUS"=>NETGEO_HTTP_ERROR);
        }
    }

    /**
     * Test the input and make sure it is in an acceptable format.  The input
     * can be an AS number (with or without a leading "AS"), an IP address in
     * dotted decimal format, or a domain name.  Stores the standardized targe
     * string into the hash if input target is valid format, otherwise stores
     * undef into hash.
     * 
     * @param array  $inputArray Address(es) to lookup
     * @return array
     * @access private

     */
    function _verifyInputFormat($inputArray)
    {
        $target = trim($inputArray["TARGET"]);
        
        // look for AS|as
        if (preg_match('/^(?:AS|as)?\s?(\d{1,})$/', $target, $matches)) {
            // check the AS number. Btwn 1 and 65536
            if ($matches[1] >= 1 && $matches[1] < 65536) {
                $standardizedTarget = $matches[0];
            } else {
                $inputArray["INPUT_ERROR"] = NETGEO_INPUT_ERROR;
                // raise some error tex
                // Bad format for input. AS number must be between 1 and 65536
                return $inputArray;
            }

        // IP number
        } elseif (preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $target, $matches)) {
            if ($matches[1] <= 255 && $matches[2] <= 255 && $matches[3] <= 255 && $matches[4] <= 255) {
                $standardizedTarget = $target;
            } else {
                $inputArray["INPUT_ERROR"] = NETGEO_INPUT_ERROR;
                // raise some error tex
                // Bad format for input. each octet in IP address must be between 0 and 255
                return $inputArray;
            }

        // TLD
        } elseif (preg_match('/^(?:[\w\-]+\.)*[\w\-]+\.([A-Za-z]{2,3})$/', $target, $matches)) {
            $tld = $matches[1];
            
            // TLD length is either 2 or 3.  If length is 2 we just accept it,
            // otherwise we test the TLD against the list.
            if (strlen($tld) == 2 || preg_match('/^(com|net|org|edu|gov|mil|int)/i', $tld)) {
                $standardizedTarget = $target;
            } else {
                $inputArray["INPUT_ERROR"] = NETGEO_INPUT_ERROR;
                // raise some error tex
                // Bad TLD in domain name. 3-letter TLDs must be one of com,net,org,edu,gov,mil,in
                return $inputArray;
            }
        } else {
            $inputArray["INPUT_ERROR"] = NETGEO_INPUT_ERROR;
            // raise some error text
            // unrecognized format for input
            return $inputArray;
        }
        
        return $inputArray;
                
    }
    
    /**
     * Executes a request to the netgeo server
     *
     * @param array $inputArray Address(es) to lookup
     * @return string Response from netgeo server
     * @access private
     */
    function _executeHttpRequest($url)
    {
        $response = "";

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            // split the server url
            $urlinfo = parse_url($url);
            if (!isset($urlinfo["port"])) {
                $urlinfo["port"] = 80;
            }
        
            $sp = @fsockopen($urlinfo["host"], $urlinfo["port"], $errno, $errstr, $this->default_timeout);
            if (!$sp) {
                return false;
            }
    
            fputs($sp, "GET " . $urlinfo["path"] ."?". $urlinfo["query"] . " HTTP/1.0\r\n");
            fputs($sp, "User-Agent: " . $this->useragent . "\r\n\r\n");
            while (!feof($sp)) {
                $response .= fgets($sp,128);
            }
            fclose ($sp);
        }

        return $response;
    }
   
    /**
     * Parses the results from the server into an array
     * 
     * @param string $response Response from netgeo server
     * @return array 
     * @access private
     */
    function _processResult($response)
    {
        // process the localizer result differently
        // since we already have an array
        if ($this->service == "localizer") {

            foreach ($response as $key=>$val) {

                $retarray[strtoupper($key)] = $val;
            }

        } elseif ($this->service == "caida") {
       
            $lineArray = preg_split("/\n/", $response);
            $line = array_shift($lineArray);

            // first check for anything icky from the server
            if (preg_match("/".NETGEO_HTTP_ERROR."/", $line) || preg_match('/^\s*$/', $response)) {

                // empty empty empty
                if (preg_match('/^\s*$/', $text)) {
                    $text = "Empty content string";
                    return array("STATUS"=>$text);
                }

            } elseif (preg_match("/".NETGEO_LIMIT_EXCEEDED."/", $line)) {
                return array("STATUS"=>$text);
            }

            // now loop through. This should being us out at TARGET
            while (isset($line) && !preg_match("/^TARGET:/", $line)) {
                $line = array_shift($lineArray);
            }

            // keep going
            while (isset($line)) {
                if (preg_match("/^TARGET:\s+(.*\S)\s*<br>/", $line, $matches)) {
                    $retarray["TARGET"] = $matches[1];
                } elseif (preg_match("/^STATUS:\s+([\w\s]+\S)\s*<br>/", $line, $matches)) {
                    $retarray["STATUS"] = $matches[1];
                } elseif (preg_match("/^(\w+):\s+(.*\S)\s*<br>/", $line, $matches)) {
                    $retarray[$matches[1]] = $matches[2];
                }
                $line = array_shift($lineArray);
            }

        }

        return $retarray;   

    }

}


?>
