<?php
/**
 * Skype API interface class
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
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * Skype XML-RPC Class
 * 
 * Class to provide Chisimba Skype recording functionality via the XML-RPC interface. 
 * The skype python tools will do most of the ghard work here, this class simply accepts the data once processed, and does somwhat useful stuff with it.
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
class skypeapi extends object
{
    
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
            // Some config
            $this->objConfig = $this->getObject('altconfig', 'config');
            // multilingualize responses etc
            $this->objLanguage = $this->getObject('language', 'language');
            // make sure the users are who they say they are!
            $this->objUser = $this->getObject('user', 'security');
            // we will need to do some file manipulations
            $this->objFiles = $this->getObject('dbfile', 'filemanager');
            // index the files once dumped through the API
            $this->objFileIndexer = $this->getObject('indexfileprocessor', 'filemanager');
        }
        catch (customException $e)
        {
            // Bail dude, something went pear shaped!
            customException::cleanUp();
            exit;
        }
    }
    
    /**
     * Method to grab Skype IM (chat) data from a live Skype session with one or many partners (polygamy?)
     * Logs chat messages to the system_errors.log file as of writing, as I don't know what else to do with it as of yet.
     *
     * @param Parameters coming from XML-RPC transaction object $params
     * @return object of XML-RPC response object.
     */
    public function chat($params)
    {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        // finally grok the actual message out of the xmlrpc message encoding
        $msg = $param->scalarval();
        
        // Load up the IM dbtable derived class and place the message in.
        log_debug($msg);
        // say thanks, ala matti... Man I need help!
        $ret = "Nanks dude!";
        
        $val = new XML_RPC_Value($ret, 'string');
        return new XML_RPC_Response($val);
        // Ooops, couldn't open the file so return an error message.
        return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
    }
    
    /**
     * Record a Skype sound bite from the caller or callers (this can be used in concurrent calls). The recorsing is simply dumped to disc, then transported via this API to Chisimba 
     * and the podcast module. Again, because there is no better place for it at the moment.
     *
     * @param object $params - XML-RPC object containing paramaters from the API
     * @return object - XML-RPC object of th return response. In this case a string.
     */
    public function soundbite($params)
    {
        // Gran the file data as a base64_encoded string
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $file = $param->scalarval();
        
        // Get the username of the person doing the upload. (This is hard coded for now in the Skype Python tool API)
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $username = $param->scalarval();
        
        // base64 decode the file and write it down
        $file = base64_decode($file);
        // Grab the user id based on the username parameter.
        $userid = $this->objUser->getUserId($username);
        // Make sure the directory that we are using exists and all.
        if(!file_exists($this->objConfig->getContentBasePath().'users/'.$userid."/"))
        {
            // else create the darn thing!
            @mkdir($this->objConfig->getContentBasePath().'users/'.$userid."/");
            @chmod($this->objConfig->getContentBasePath().'users/'.$userid."/", 0777);
        }
        // Make up a filename. This one is based on timestamp appended by _skypecall. It is a .wav for now, still trying to figurte out a better format for transporting.
        $filename = time()."_skypecall.wav";
        // local file name.
        $localfile = $this->objConfig->getContentBasePath().'users/'.$userid."/".$filename;
        // smash the file data into the filename and forget about it.
        file_put_contents($localfile, $file);
        
        // A quick conversion
        $media = $this->getObject('media', 'utilities');
        // convert the .wav to a .mp3
        $mp3 = $media->convertWav2Mp3($localfile, $this->objConfig->getContentBasePath().'users/'.$userid."/");
        
        // Now add to list of podcasts
        // get the file size
        $filesize = filesize($mp3);
        if(extension_loaded('fileinfo'))
        {
            // hopefully sane folks will have fileinfo installed.
            $finfo = finfo_open(FILEINFO_MIME);
            // MIME type
            $type = finfo_file($finfo, $filename);
        }
        else {
            // fall back to the old skewl method (deprecated btw)
            $type = mime_content_type($filename);
        }

        $mimetype = $type; //mime_content_type($mp3);
        // category? dunno.
        $category = '';
        // version 1 as we just created the file.
        $version =1;
        
        // The file we are going to be working with
        $fmname = basename($localfile, ".wav");
        // end name
        $fmname = $fmname.".mp3"; 
        // some path info
        $fmpath = 'users/'.$userid.'/'.$fmname;
        $path = $this->objConfig->getContentBasePath().'users/'.$userid."/";
        
        // Dunno wtf this is sposed to be...
        $idcomment = NULL;
        
        // add the MP3 to the user's filemanager set
        $fileId = $this->objFiles->addFile($fmname, $fmpath, $filesize, $mimetype, $category, $version, $userid, $idcomment);
        
        // now take the generated FileID and insert the podcast to the podcast module.
        $pod = $this->getObject('dbpodcast', 'podcast');
        $ret = $pod->addPodcast($fileId, $userid, basename($filename, ".wav"));
        
        // return an XML-RPC string response as an object in case anyone is listening.
        $val = new XML_RPC_Value("File saved to $localfile", 'string');
        return new XML_RPC_Response($val);
        // Ooops, couldn't open the file so return an error message.
        return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
    }
}
?>