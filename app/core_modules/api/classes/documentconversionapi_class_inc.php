<?php
/**
 * Document Conversion API interface class
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
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: ffmpegapi_class_inc.php 3183 2007-12-19 10:01:02Z paulscott $
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
 * Document Conversion XML-RPC Class
 * 
 * Class to provide document conversion capability functionality via the XML-RPC interface. 
 * 
 * @category  Chisimba
 * @package   api
 * @author    Tohir Solomons <pscott@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class documentconversionapi extends object
{

    /**
     * Config object
     *
     * @var object
     */
    public $objConfig;
    
    /**
     * @var string $convertFilename Filename to convert file to
     */
    private $convertFilename;
    
    /**
     * @var string $result Result key of the file conversion
     */
    private $result;

    /**
     * Standard init function
     *
     * @param void
     * @return void
     */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
    }
    
    /**
     * Method to convert the document
     */
    public function convertDoc($params)
    {
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $moduleRegistered = $this->objModules->checkIfRegistered('documentconverter');
        
        // If module is not registered, return 0, can't do anything
        if (!$moduleRegistered) {
            $noResponse = new XML_RPC_Value('0', 'string');
            return new XML_RPC_Response($noResponse);
        }
        
        // get password
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $password = $param->scalarval();
        
        // get Required Password
        $requiredPassword = $this->objSysconfig->getValue('REMOTEPASSWORD', 'documentconverter');
        
        // Check if match
        if ($password != $requiredPassword) {
            $noResponse = new XML_RPC_Value('0', 'string');
            return new XML_RPC_Response($noResponse);
        }
        
        // get current filename
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $filename = $param->scalarval();
        
        // get contents of file
        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $contents = $param->scalarval();
        
        // get filename of converted file
        $param = $params->getParam(3);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $this->convertFilename = $param->scalarval();
        
        
        // Create Temp Directory
        $dirName = md5($filename.$this->convertFilename.time());
        
        // Full Path to Temp Directory
        $dirToSave = $this->objConfig->getContentBasePath().'/remoteconversion/'.$dirName;
        
        // Load Classes
        $objMkdir = $this->getObject('mkdir', 'files');
        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        
        // Clean Paths
        $dirToSave = $objCleanUrl->cleanUpUrl($dirToSave);
        // Make Directory
        $objMkdir->mkdirs($dirToSave);
        
        // Store File Contents
        file_put_contents($dirToSave.'/'.$filename, base64_decode($contents));
        
        // Convert Files
        $objDocumentConverter = $this->getObject('convertdoc', 'documentconverter');
        $objDocumentConverter->convert($dirToSave.'/'.$filename, $dirToSave.'/'.$this->convertFilename);
        
        // Delete Original File
        unlink($dirToSave.'/'.$filename);
        
        $convertedFile = new XML_RPC_Value($this->returnFile($dirToSave), 'string');
        
        return new XML_RPC_Response($convertedFile);
    }
    
    /**
     * Method to prepare the converted file for return
     * @param string $path Path where converted file(s) are stored
     * @return string File to return
     */
    private function returnFile($path)
    {
        $objScanDoc = $this->getObject('scanconverteddocs', 'documentconverter');
        
        $files = $objScanDoc->scanDirectory($path);
        
        if (count($files) == '0') {
            $this->result = 'unable to convert file';
            return '0';
        } else if (count($files) == '1') {
            $filetosend = file_get_contents($files[0]);
            $filetosend = '1'.base64_encode($filetosend);
            $this->result = 'converted';
        } else {
            $filetosend = '2'.$this->zipFilesAndReturn($path, $files);
            $this->result = 'zippedfile';
        }
        
        // Cleanup - Delete files and remove directory
        foreach ($files as $file)
        {
            @unlink($file);
        }
        @rmdir($path);
        
        return $filetosend;
    }
    
    /**
     * Method to zip up the files before returning it to the user
     * @param string $path Path where files will be stored
     * @param array $files List of Files to be zipped
     * @return string zip file
     */
    private function zipFilesAndReturn($path, $files)
    {
        $zipFilename = $path.'/files.zip';
        
        $objZip = $this->getObject('wzip', 'utilities');
        
        $objZip->packFilesZip($zipFilename, $files);
        
        $filetosend = file_get_contents($zipFilename);
        $filetosend = base64_encode($filetosend);
        
        unlink($zipFilename);
        
        return $filetosend;
    }

}

?>