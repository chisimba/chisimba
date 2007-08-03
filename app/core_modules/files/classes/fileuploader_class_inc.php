<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

/**
 * File Upload Class
 *
 * This class is aimed at developers who need file uploading capability, but one that is not
 * integrated with file manager. It provides similar functionality, with further control options
 * 
 * @author Tohir Solomons
 *         
 *         Usage:
 *         
 *         $fileUploader = $this->getObject('fileuploader', 'files');
 *         
 *         $fileUploader->allowedCategories = array('documents', 'images');
 *         // OR
 *         $fileUploader->allowedExtensions = array('pdf', 'gif', 'png');
 *         
 *         $fileUploader->savePath = '/uploader/'; // This will then be saved in usrfiles/uploader
 *         $fileUploader->overwriteExistingFile = TRUE;
 *         
 *         $results = $fileUploader->uploadFile('fileupload1');
 *         
 *         Sample Result From Successful Upload:
 *         
 *         Array
 *         (
 *         [result] => 1
 *         [filename] => kbook.pdf
 *         [mime] => application/pdf
 *         [size] => 4352885
 *         [path] => usrfiles/uploader/kbook.pdf
 *         [absolutepath] => /opt/lampp/htdocs/5ive_november/app/usrfiles/uploader/kbook.pdf
 *         )
 *         
 */
class fileuploader extends object 
{
    /**
    * @var array @bannedExtensions List of Banned Extensions. Files with these extensions are not allowed to be uploaded
    */
    private $bannedExtensions = array ('exe', 'cgi', 'dll');
    
    /**
     * Optional: List of Extensions the uploaded files should have. Any file, not meeting the extension will not be saved.
     * Can be passed as a string if one extension, or array if multiple extensions
     *
     * @var array
     */
    public $allowedExtensions;
    
    /**
     * Optional: Restrict that uploaded files should be of a certain category. See variable below
     * Can be passed as a string if one category, or array if multiple categories
     *
     * @var array
     */
    public $allowedCategories;
    
    /**
     * List of possible categories. The file is checked against the filefolder class in the filemanager module.
     *
     * @var array
     */
    private $categories = array('images', 'audio', 'video', 'documents', 'flash', 'freemind', 'archives', 'other', 'obj3d', 'scripts');
    
    /**
     * Path to Save the Files to. Developers need only provide the path after the /usrfiles/
     *
     * @var string
     */
    public $savePath;
    
    /**
     * Should the uploader overwrite existing files
     *
     * @var boolean
     */
    public $overwriteExistingFile = FALSE;
    
    
    /**
     * Constructor
     */
    public function init()
    {
        $this->objFileParts =& $this->getObject('fileparts', 'files');
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objFileFolder =& $this->getObject('filefolder', 'filemanager');
        $this->objCleanUrl =& $this->getObject('cleanurl', 'filemanager');
        $this->objMkdir =& $this->getObject('mkdir', 'files');
    }
    
    /**
     * Method to upload a file
     *
     * @param  string $fileInputName Name of the File Input
     * @return array  Result of the Upload
     */
    public function uploadFile($fileInputName)
    {
        if ($this->savePath == '') {
            return array('result'=>FALSE, 'errormessage'=>'savepathnotset');
        }
        
        // First Check if File Input key exists
        if (array_key_exists($fileInputName, $_FILES)) {
            $file = $_FILES[$fileInputName];
        } else { // If not, return FALSE
            return array('result'=>FALSE, 'errormessage'=>'noinputwiththatname');
        }
        
        // Handle PHP Upload Errors
        if ($file['error'] > 0) {
            switch ($file['error'])
            {
                case 1: $error = 'exceedphpmaxfilesize'; break;
                case 2: $error = 'exceedformmaxfilesize'; break;
                case 3: $error = 'partialupload'; break;
                case 4: $error = 'nofilegiven'; break;
                default: $error = 'unknown'; break;
            }
            
            return array('result'=>FALSE, 'errormessage'=>$error);
        }
        
        // Convert List of Extensions to an Array if it is not an array.
        if ($this->allowedExtensions != '' && !is_array($this->allowedExtensions)) {
            $this->allowedExtensions = array($this->allowedExtensions);
        }
        
        // Convert List of Categories to an Array if it is not an array.
        if ($this->allowedCategories != '' && !is_array($this->allowedCategories)) {
            $this->allowedCategories = array($this->allowedCategories);
        }
        
        // Check If Extension is Valid if Extension requirement has been setup
        if (is_array($this->allowedExtensions) && count($this->allowedExtensions) > 0) {
            if (!in_array($this->objFileParts->getExtension($file['name']), $this->allowedExtensions)) {
                return array('result'=>FALSE, 'errormessage'=>'doesnotmatchrequiredextensions');
            }
        } 
        
        // Check If Category matches if Category requirement has been setup
        if (is_array($this->allowedCategories) && count($this->allowedExtensions) > 0) {
            
            // Get File Folder
            $filefolder = $this->objFileFolder->getFileFolder($file['name'], $file['type']);
            
            // Check if Match
            if (!in_array($filefolder, $this->allowedCategories)) {
                return array('result'=>FALSE, 'errormessage'=>'doesnotmatchrequiredcategories');
            }
        }
        
        // Prepare the Folder name
        $folder = $this->objConfig->getcontentBasePath().$this->savePath;
        $absolutePath = $this->objConfig->getcontentBasePath().'/'.$this->savePath.'/'.$file['name'];
        $relativePath = $this->objConfig->getcontentRoot().'/'.$this->savePath.'/'.$file['name'];
        
        // Clean URLs
        $this->objCleanUrl->cleanUpUrl($folder);
        $this->objCleanUrl->cleanUpUrl($absolutePath);
        $this->objCleanUrl->cleanUpUrl($relativePath);
        
        // Check that directory exists
        $this->objMkdir->mkdirs($folder);
        
        // Check if file exists and overwrite is turned off.
        if (file_exists($absolutePath) && !$this->overwriteExistingFile) {
            return array('result'=>FALSE, 'errormessage'=>'fileexists');
        }
        
        // Move Uploaded File
        if (move_uploaded_file($file['tmp_name'], $absolutePath)) {
            return array(
                    'result'=>TRUE,                      // Success
                    'filename'=>$file['name'],           // Name of File
                    'mime'=>$file['type'],               // Mime Type
                    'size'=>$file['size'],               // Size of File
                    'path'=>$relativePath,               // Relative Path to File
                    'absolutepath'=>$absolutePath        // Absolute Path to File
                );
        } else {
            return array('result'=>FALSE, 'errormessage'=>'couldnotmoveuploadedfile');
        }
        
    }
    

}


?>