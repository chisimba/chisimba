<?php
/**
* Class to parse a string (e.g. page content) and fetch relevant data and links to wikipedia pages
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
* Class to parse a string (e.g. page content) and fetch relevant data and links to wikipedia pages
*
* @author Paul Scott
*         
*/

class parse4wikipediawords extends object
{
	/**
	* 
	* String to hold an error message
	* @accesss private 
	*/
	private $errorMessage;
	
	private $enableparser;
    
    /**
     * 
     * Constructor for the wikipedia parser
     * 
     * @return void  
     * @access public
     * 
     */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        //TESTING HERE -- don't forget the check for api being installed.<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        //$this->objMwQuery = $this->getObject("mwquery", "mediawikiapi");
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The parsed string
    *                
    */
    public function parse($txt)
    {
    	// check that the sysadmin has enabled this filter...
    	$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->enableparser = $this->sysConfig->getValue('mod_filters_wikipediaparser', 'filters');
    	
        if($this->enableparser == 'ON')
        {
        	$tarr = explode(" ", strip_tags($txt));
        	foreach($tarr as $words)
        	{
        		$words = trim($words);
        		if($words != '' || !isset($words) || !empty($words))
        		{
        			// do the wikipedia lookup
        			$url = 'http://en.wikipedia.org/w/api.php?action=query&prop=revisions&titles='.trim($words).'&rvprop=content&format=xml';
        			
                    // set up for a cURL...THERE IS A CURL WRAPPER THAT DOES THIS IN ONE LINE
        			$this->objProxy = $this->getObject('proxyparser', 'utilities');
        			$proxyArr = $this->objProxy->getProxy();
        			$ch = curl_init();
        			curl_setopt($ch, CURLOPT_URL, $url);
        			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        			if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
            			curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'] . ":" . $proxyArr['proxy_port']);
            			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'] . ":" . $proxyArr['proxy_pass']);
        			}
        			$code = curl_exec($ch);
        			curl_close($ch);
                    
        			// use simplexml to load the string...
        			$xml = simplexml_load_string($code);
        			// $xml->error will exist on bad pages (i.e. no page exists)
        			if($xml->error)
        			{
        				continue;
        			}
        			else {
        				$page = $xml->query;
        				if($page->normalized || $page->pages)
        				{
        					$title = $page->pages->page['title'];
        					$linker = new href('http://en.wikipedia.org/wiki/'.$title, $words, " target=_blank");
        					$link[] = array('word' => $words, 'url' => $linker->show());
        				}
        			}
        			
        		}
        		else {
        			continue;
        		}
        	}
        	// look for and replace the word (linked) in the text.
        	foreach($link as $thing)
        	{
        		$orig = $thing['word'];
        		$txt = preg_replace("/ $orig /", " ".$thing['url']." ", $txt);
        	}
        }
        return $txt;
    }
}
?>