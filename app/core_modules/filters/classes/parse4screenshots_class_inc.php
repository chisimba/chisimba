<?php
/**
 * Class to parse a string (e.g. page content) that contains a link
 * and creates an XML-RPC request to the screenshot server to get a thumbnail. 
 * This class will also create a local cache to save bandwidth
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
 * @package   filters
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za     
 */
/**
*
* Class to parse a string (e.g. page content) that contains a link
* and creates an XML-RPC request to the screenshot server to get a thumbnail. 
* This class will also create a local cache to save bandwidth
*
* @author    Paul Scott
* @package   filters
* @access    public
* @copyright AVOIR GNU/GPL
*            
*/

class parse4screenshots extends object
{
    
    /**
     * init
     * 
     * Standard Chisimba init function
     * 
     * @return void  
     * @access public
     */
    function init()
    {
    	$this->objConfig = $this->getObject('altconfig', 'config');
    	//require_once($this->getPearResource('XML/RPC.php'));
    	//require_once($this->getPearResource('XML/RPC/Dump.php'));
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *                
    */
    public function parse($txt)
    {
        preg_match_all('/\[SCREENSHOT\](.*)\[\/SCREENSHOT\]/U', $txt, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $replacement = $this->getShotFromCache($item);
            /*if($replacement == FALSE)
            {
            	$replacement = $this->getShotFromService($item);
            	if($replacement == FALSE)
            	{	
            		$replacement = $this->requestShotFromService($item);
            	}
            }*/
            $txt = str_replace($results[0][$counter], $replacement, $txt);
            $counter++;
        }
        return $txt;
    }
    
    public function getShotFromCache($url)
    {
    	if(!file_exists($this->objConfig->getContentBasePath().'apitmp/cache/'))
		{
			@mkdir($this->objConfig->getContentBasePath().'apitmp/cache/');
			@chmod($this->objConfig->getContentBasePath().'apitmp/cache/', 0777);
			log_debug("getting screenshot of $url from service...");
			return $this->getShotFromService($url);
		}
		else {
			$file = $this->objConfig->getsiteRoot()."usrfiles/apitmp/cache/".md5($url).".png";
			$checkfile = $this->objConfig->getsiteRootPath()."usrfiles/apitmp/cache/".md5($url).".png";
			//echo filesize($this->objConfig->getsiteRootPath()."usrfiles/apitmp/cache/".md5($url).".png"); die();
			if(file_exists($checkfile) && (filesize($checkfile) < 30))
			{
				unlink($checkfile);
				$req = $this->getShotFromService($url);
				if($req == FALSE)
				{
					$this->requestShotFromService($url);
				}
				return $url; //'<img src="'.$this->objConfig->getsiteRoot()."usrfiles/apitmp/cache/".md5($url).".png".'">';
			}
			else {
				//log_debug("getting screenshot of $url from service...");
				$req = $this->getShotFromService($url);
				if($req == FALSE)
				{
					$this->requestShotFromService($url);
				}
				return $url;
				//return $url; //$this->getShotFromService($url);
			}
		}
    	
    }
    
    public function getShotFromService($url)
    {
    	require_once($this->getPearResource('XML/RPC.php'));
    	if(!file_exists($this->objConfig->getContentBasePath().'apitmp/cache/'))
		{
			mkdir($this->objConfig->getContentBasePath().'apitmp/cache/');
			chmod($this->objConfig->getContentBasePath().'apitmp/cache/', 0777);
		}
    	$params = array(new XML_RPC_Value($url, "string"));
    	// Construct the method call (message). 
		$msg = new XML_RPC_Message('screenshot.grabShot', $params);
		// The server is the 2nd arg, the path to the API module is the 1st.
		$cli = new XML_RPC_Client('/app/index.php?module=api', 'chameleon.uwc.ac.za');
		// set the debug level to 0 for no debug, 1 for debug mode...
		$cli->setDebug(0);
		// bomb off the message to the server
		$resp = $cli->send($msg);
		if (!$resp) {
    		return $url;
		}
		if (!$resp->faultCode()) {
			$val = $resp->value();
    		$val = XML_RPC_decode($val);
    		// write the file back to the "cache"
    		file_put_contents($this->objConfig->getContentBasePath().'/apitmp/cache/'.md5($url).'.png', base64_decode($val));
    		$file = $this->objConfig->getsiteRoot()."usrfiles/apitmp/cache/".md5($url).".png";
			$checkfile = $this->objConfig->getsiteRootPath()."usrfiles/apitmp/cache/".md5($url).".png";
			//echo filesize($this->objConfig->getsiteRootPath()."usrfiles/apitmp/cache/".md5($url).".png"); die();
			if(file_exists($checkfile) && (filesize($checkfile) < 30))
			{
				unlink($checkfile);
				$this->requestShotFromService($url);
				return $url;
			}
			else {
				//log_debug("getting screenshot of $url from service...");
				//$this->getShotFromService($url);
				return $url;
				//return $url; //$this->getShotFromService($url);
			}
    		return $url;
		}
		else {
			return $url;
		}
    }
    
    public function requestShotFromService($url)
    {
    	if(!file_exists($this->objConfig->getContentBasePath().'apitmp/cache/'))
		{
			@mkdir($this->objConfig->getContentBasePath().'apitmp/cache/');
			@chmod($this->objConfig->getContentBasePath().'apitmp/cache/', 0777);
		}
    	@$params = array(new XML_RPC_Value($url, "string"));
    	// Construct the method call (message). 
		$msg = new XML_RPC_Message('screenshot.requestShot', $params);
		// The server is the 2nd arg, the path to the API module is the 1st.
		$cli = new XML_RPC_Client('/app/index.php?module=api', 'chameleon.uwc.ac.za');
		// set the debug level to 0 for no debug, 1 for debug mode...
		$cli->setDebug(0);
		// bomb off the message to the server
		$resp = $cli->send($msg);
		if (!$resp) {
    		return $url;
		}
		if (!$resp->faultCode()) {
			$val = $resp->value();
    		$val = XML_RPC_decode($val);
    		// write the file back to the "cache"
    		file_put_contents($this->objConfig->getContentBasePath().'/apitmp/cache/'.md5($url).'.png', base64_decode($val));
    		return $url;
		}
		else {
			return $url;
		}
    }
}
?>