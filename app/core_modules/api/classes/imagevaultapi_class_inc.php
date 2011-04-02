<?php
/**
 * ImageVault interface class
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
 * @version   $Id: podcastapi_class_inc.php 11357 2008-11-06 10:53:04Z paulscott $
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
 * ImageVault XML-RPC Class
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
class imagevaultapi extends object
{
    public $objMedia;
    public $objFiles;
    public $objFileIndexer;
    public $objUser;
    public $objConfig;
    public $objLanguage;

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
            $this->objFiles = $this->getObject('dbfile', 'filemanager');
            $this->objFileIndexer = $this->getObject('indexfileprocessor', 'filemanager');
            $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
        }
        catch (customException $e)
        {
            customException::cleanUp();
            exit;
        }
        if($this->objModuleCat->checkIfRegistered('rackspacecloudfiles')) {
            // pull up the rackspace api module
            $this->objCloudfiles = $this->getObject('cloudfilesops', 'rackspacecloudfiles');
        }
    }

    public function fileDrop($params)
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
        $file = base64_decode($file);

        $userid = $this->objUser->getUserId($username);
        
        // Rackspace Files case
        if($this->objModuleCat->checkIfRegistered('rackspacecloudfiles')) {
            $param = $params->getParam(3);
            if (!XML_RPC_Value::isValue($param)) {
                log_debug($param);
            }
            $filename = $param->scalarval();
            $localfile = $this->objConfig->getContentBasePath().'users/'.$userid."/".$filename;
            file_put_contents($localfile, $file);
            $this->objCloudfiles->uploadFile($userid, $filename, $localfile);

            // send a response
            $val = new XML_RPC_Value("saved", 'string');
            return new XML_RPC_Response($val);
        }
        else { 
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

            $objOverwriteIncrement = $this->getObject('overwriteincrement', 'filemanager');
            $filename = $objOverwriteIncrement->checkfile($filename, 'users/'.$userid);

            $localfile = $this->objConfig->getContentBasePath().'users/'.$userid."/".$filename;
            file_put_contents($localfile, $file);

            $fmname = basename($filename);
            $fmpath = 'users/'.$userid.'/'.$fmname;

            // Add to users fileset
            $fileId = $this->objFileIndexer->processIndexedFile($fmpath, $userid);

            $val = new XML_RPC_Value("saved", 'string');
            return new XML_RPC_Response($val);
        }
    }

}
?>
