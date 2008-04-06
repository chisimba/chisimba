<?php
/**
 * Podcast interface class
 * 
 * XML-RPC (Remote Procedure call) class
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
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: ffmpegapi_class_inc.php 3183 2007-12-19 10:01:02Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * Podcast XML-RPC Class
 * 
 * Class to provide Chisimba podcast XML-RPC functionality
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class podcastapi extends object
{
	public $objMedia;

	/**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init()
	{
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
        	$this->objUser = $this->getObject('user', 'security');
        	//$this->objMedia = $this->getObject('media', 'utilities');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	
	public function grabPodcast($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$password = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$file = $param->scalarval();
    	log_debug($file);
		$file = base64_decode($file);
    	
		$userid = $this->objUser->getUserId($username);
		
		if(!file_exists($this->objConfig->getContentBasePath().'users/'.$userid."/"))
		{
			@mkdir($this->objConfig->getContentBasePath().'users/'.$userid."/");
			@chmod($this->objConfig->getContentBasePath().'users/'.$userid."/", 0777);
		}
		
		$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$filename = $param->scalarval();
    	
    	log_debug("getting $filename from python");
    	//log_debug($file);
		$localfile = $this->objConfig->getContentBasePath().'users/'.$userid."/".$filename;
		file_put_contents($localfile, $file);
		
		// Convert the OGG Vorbis file to an MP3 as a background process, just in case it's huge
		$b = $this->getObject('background', 'utilities');
		$status = $b->isUserConn();
		$callback = $b->keepAlive();
		
		// convert to mp3 via lame and oggenc (please make sure that these are installed on the server.
		$media = $this->getObject('media', 'utilities');
		log_debug("Converting $localfile...");
		$media->convertOgg2Mp3($localfile, $this->objConfig->getContentBasePath().'users/'.$userid."/");
		//sleep(60); //fake a 60 second process
 		
		//fork the process and create the child process and call the callback function when done
		$call2 = $b->setCallBack($this->objUser->email($userid), "Podcast has been processed", "Your podcast is now available on the server.");
		$val = new XML_RPC_Value("File saved to $localfile", 'string');
		return new XML_RPC_Response($val);
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
	}
}
?>