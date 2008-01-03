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
	 * The Config object (altconfig)
	 * 
	 * @access public
	 * @var    object
	 */
    public $objConfig;
    
    /**
     * DomTT container object
     *
     * @access  public
     * @var     object
     */
    public $objTT;
    
    /**
     * DB Sysconfig container object
     *
     * @access  public
     * @var     object
     */
    public $sysConfig;
    
    /**
     * Screenshot server
     *
     * @access  public
     * @var     object
     */
    public $shotserv;
    
    /**
     * screenshot url
     *
     * @access  public
     * @var     object
     */
    public $shoturl;
    
    /**
     * init
     * 
     * Standard Chisimba init function
     * 
     * @access public
     */
    function init()
    {
    	$this->objConfig = $this->getObject('altconfig', 'config');
    	$this->objTT = $this->getObject('domtt', 'htmlelements');
    	$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    	$this->shotserv = $this->sysConfig->getValue('screenshot_server', 'filters');
		$this->shoturl = $this->sysConfig->getValue('screenshot_url', 'filters');
    }
    
    /**
     * Method to parse the string
     *
     * @param  String $txt The string to parse
     * @return string $txt The parsed string                
     */
    public function parse($txt)
    {
    	// match all occurrances of the filter text
        preg_match_all('/\[SCREENSHOT\](.*)\[\/SCREENSHOT\]/U', $txt, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        // loop through the resultset
        foreach ($results[1] as $item)
        {
        	// replace the tags with the shots
            $replacement = $this->getShotFromCache($item);
            $txt = str_replace($results[0][$counter], $replacement, $txt);
            $counter++;
        }
        // return the parsed string
        return $txt;
    }
    
    /**
     * Get a screenshot from the local cache
     *
     * @param  string $url the url to be captured
     * @return string $js  The rendered javascript | the next action
     */
    public function getShotFromCache($url)
    {
    	if(!file_exists($this->objConfig->getContentBasePath().'apitmp/cache/'))
		{
			@mkdir($this->objConfig->getContentBasePath().'apitmp/cache/');
			@chmod($this->objConfig->getContentBasePath().'apitmp/cache/', 0777);
			//log_debug("getting screenshot of $url from service...");
			return $this->getShotFromService($url);
		}
		else {
			$file = $this->objConfig->getsiteRoot()."usrfiles/apitmp/cache/".md5($url).".png";
			$checkfile = $this->objConfig->getsiteRootPath()."usrfiles/apitmp/cache/".md5($url).".png";
			if(file_exists($checkfile) && (filesize($checkfile) < 30))
			{
				unlink($checkfile);
				$req = $this->getShotFromService($url);
				if($req == FALSE)
				{
					$this->requestShotFromService($url);
					// shot is NOT in cache and is now in the request queue
					// return a domTT thing with a message...
					$ret = $this->objTT->show();
					//echo "SHOWING TT1";
					return $ret;
				}
				else {
					//shot has been processed on the server, but is not in the local cache
					// return a domTT thing with the image.
					$ret = $this->objTT->show();
					//echo "SHOWING TT2";
					//return $ret;					
					return $url;
				}
			}
			elseif(file_exists($checkfile) && (filesize($checkfile) > 30)) 
			{
				$id = md5($url);
				$turl = '<div id="'.$id.'">'.$url.'</div>';
				//shot is all OK, return the domtt thing with it in
				$script = "<script type='text/javascript'>".$this->getResourceUri('prototip1.2.0_pre1/js/prototip.js')."</script>";
				$script .= '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('prototip1.2.0_pre1/css/prototip.css').'" />';
				
				
				$this->appendArrayVar('headerParams',$script);
				$scripts = '<script type="text/javascript">';
				
				$scripts .= "new Tip($turl, 'content', {title: 'this tooltip has a title'});";
				$scripts .= '</script>';
				//$this->objTT->linkText = $url;
				//$ret = $this->objTT->show('Screenshot!', 'message text');
				return '<a href="'.$url.'"> <img src="'.$file.'"> </a>';
			}
			else {
				$req = $this->getShotFromService($url);
				if($req == FALSE)
				{
					$this->requestShotFromService($url);
					// return a domTT thing with a message...
					$ret = $this->objTT->show();
					//echo "SHOWING TT4";
					//return $ret;
					return $url;
				}
				else {
					//shot has been processed on the server, but is not in the local cache
					// return a domTT thing with the image.
					$ret = $this->objTT->show();
					//echo "SHOWING TT5";
					//return $ret;
					return $url;
				}
			}
		}
    	
    }
    
    /**
     * Method to get an already rendered shot from the service server
     *
     * @param  string $url the url to capture
     * @return string $url the rendered url code
     */
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
		$cli = new XML_RPC_Client($this->shoturl, $this->shotserv);
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
    
    /**
     * Method to request a screenshot from the service
     *
     * @param  string $url the url to be rendered
     * @return string $url the request return (md5 hash of the url)
     */
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
		$cli = new XML_RPC_Client($this->shoturl, $this->shotserv);
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