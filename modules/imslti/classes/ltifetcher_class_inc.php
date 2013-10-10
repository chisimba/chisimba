<?php
/**
 *
 * IMS LTI Fetcher
 *
 * Builds a Fetcher for the IMS LTI content. Currently it sends the XML
 * and returns the URL for the content to be played
 * 
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
 * @package   twitter
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: tweetbox_class_inc.php 8227 2008-03-27 20:05:32Z dkeats $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * IMS LTI Fetcher
 *
 * Builds a Fetcher for the IMS LTI content. Currently it sends the XML
 * and returns the URL for the content to be played
*
* @author Derek Keats
* @package imslti
*
*/
class ltifetcher extends object
{
	
	private $uri;
	private $secret;
	private $xmlPacket;
	
    /**
    *
    * Constructor for the ltiwrapper class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {

    }
    /**
    * 
    * Setter for parameters
    * @param string $param The parameter to set
    * @param string $value The value of the parameter being set
    * @return Boolean TRUE
    * 
    */
    public function set($param, $value)
    {
    	$this->$param = $value;
        return TRUE;
    }
    
    /**
    * 
    * Get the URL from tool host by sending the restUrl with
    * the XML package needed to get back the URL for the tool
    * @param string $restUrl The URL for the restful service
    * @access public
    * @return The URL of the tool
    *  
    */
    public function getUrl($restUrl)
    {
		$objCurl = $this->getObject("curlwrapper", "utilities");
		$objCurl->initializeCurl($restUrl);
		$objCurl->setopt(CURL_POST, 1);
		$objCurl->setopt(CURLOPT_RETURNTRANSFER, 1);
		$objCurl->setopt(CURL_POSTFIELDS, $this->xmlPacket);
        $arHeader = $this->makeHeader();
        $objCurl->setopt(CURLOPT_HTTPHEADER, $arHeader);
		$strXml = $objCurl->getUrl();
		return $this->extractLtiUrl($strXml);
    }
    
    /**
    * 
    * Extract the URL from the pseudoXML returned by the rest API
    * @param string $strXml The pseudoXML that is returned
    * @return string The launch URL that contains the tool being accessed
    * @access private
    *  
    */
    private function extractLtiUrl($strXml)
    {
    	$strXml = $this->makeValidXml($strXml);
    	$xmlObj = simplexml_load_string($strXml);
    	return $xmlObj->launchUrl;
    }
    
    /**
    * 
    * This method just takes the pseudoXML and makes it
    * valid XML so simplexml can work with it
    * @param string $strXml The pseudo XML returned by the tool
    * @return string The valid XML version
    * @access private
    * 
    */
    private function makeValidXml($strXml)
    {
        $verifyKey = 'launchUrl';
        if (strpos($strXml, $verifyKey) !=0) {
        	return "<?xml version='1.0'?><item>$strXml</item>";
        } else {
        	return "<?xml version='1.0'?><item><launchUrl>504</launchUrl></item>";
        }
    	
    }
    
    /**
    * 
    * The nonce is a random string of 40ish characters
    * @return string $nonce A 40 character nonce
    * @access private
    * 
    */
    private function getNonce()
    {
        $objUid = $this->getObject("random", "strings");
        $nonce = $objUid->guid(40);
    	return $nonce;
    }
    
    /**
    * Build the header that is passed for security. 
    * The security approach is based on the WSSE Username Token method
    *   discussed in the following:
    *   http://www.xml.com/pub/a/2003/12/17/dive.html
    *  
    * @return string Array An array of headers to pass to Curl
    * @access private
    */
    private function makeHeader()
    {
    	$nonce = $this->getNonce();
        $creationTimestamp = substr_replace(strftime("%Y-%m-%dT%H:%M:%S%z"), ":", -2, 0);
        // TEST DATA: leave it for now
        //$nonce = "5a10e285-6a52-4dc6-816b-d917c2830269";
        //$creationTimestamp = "2008-06-03T13:51:20-04:00";
        // END OF TEST DATA
        $crap = base64_encode(sha1($nonce . $creationTimestamp . $this->secret, TRUE));
        //curl_setopt($ch, CURLOPT_HTTPHEADERS, array("Content-Length: $postL")); 
        $wsse = "UsernameToken Username=\"ltitc\", " .
                "PasswordDigest=\"$crap\", Nonce=\"$nonce\", " .
                "Created=\"$creationTimestamp\"";
        $arHeader = array(
          "Authorization: WSSE profile=\"UsernameToken\"",
          "X-WSSE: $wsse" );
        return $arHeader;
    }

}
?>