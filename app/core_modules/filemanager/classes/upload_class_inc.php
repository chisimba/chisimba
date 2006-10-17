<?

/**
* Class to Handle Uploads for User Files
*
* This class can be called by any module, and will handle the upload process for that module.
* Apart from the upload, this class also places the file in a suitable subfolder, updates the
* database, parses files for metadata, and creates thumbnails for images.
*
* @todo: Improve Code to Handle Large Files
*
* @author Tohir Solomons
*/
class upload extends object
{

    /**
    * @var string $formaction Form Action to take 
    */
    public $formaction;
    
    /**
    * @var array @bannedExtensions List of Banned Extensions
    */
    public $bannedExtensions = array ('exe', 'cgi', 'dll');
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->name = 'fileupload';
        $this->formaction = $this->uri(array('action'=>'upload'));
        
        $this->numInputs = 2;
        $this->formExtra = '';
        
        
        $this->objUser =& $this->getObject('user', 'security');
        $this->objFileParts =& $this->getObject('fileparts', 'files');
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objFile =& $this->getObject('dbfile');
        $this->objMediaFileInfo =& $this->getObject('dbmediafileinfo');
        $this->objMetadataScripts =& $this->getObject('dbmetadatascripts');
        $this->objFileFolder =& $this->getObject('filefolder');
        $this->objGetId3 =& $this->getObject('getid3analyzer', 'files');
        $this->objXMLSerializer =& $this->getObject('xmlserial', 'utilities');
        $this->objSingleArray = $this->getObject('singlearray');
        $this->objThumbnails =& $this->getObject('thumbnails');
        
        // Check that Upload Folders exist
        $objFolderCheck = $this->getObject('userfoldercheck');
        $objFolderCheck->checkUserFolder($this->objUser->userId());
        
        $this->objLanguage =& $this->getObject('language', 'language');
    }
    
    /**
    * Method to show an upload form
    * @todo: use htmlelements
    */
    public function show()
    {
        $form = '<form name="form1" id="form1" enctype="multipart/form-data" method="post" action="'.$this->formaction.'">';
        
        if (!is_int($this->numInputs) || $this->numInputs < 1) {
            $this->numInputs = 2;
        }
        
        $break = '';
        
        for ($i = 1; $i <= $this->numInputs; $i++)
        {
            $form .= $break.'<input type="file" name="'.$this->name.$i.'" size="60" />';
            $break = '<br />';
        }
        
        if ($this->numInputs == 1) {
            $form .= '<input type="submit" name="submitform" value="'.$this->objLanguage->languageText('phrase_uploadfile', 'filemanager', 'Upload File').'" />';
        } else {
            $form .= '<input type="submit" name="submitform" value="'.$this->objLanguage->languageText('phrase_uploadfiles', 'filemanager', 'Upload Files').'" />';
        }
        
        $form .= $this->formExtra;
        $form .= '</form>';
        
        return $form;
    }
    
    /**
    * Method to Upload ALL Posted Files
    *
    * This method does not require you to give the name of the fileinput.
    * It references the $_FILES directly, and handles uploads from there.
    *
    * It returns an array with details of files uploaded, as well as errors.
    * @access Public
    * @param array $ext Extensions to restrict to
    * @return array Results of Upload
    */
    public function uploadFiles($ext=NULL)
    {
        if (count($_FILES) == 0) { // Checked that Files were Uploaded
            return FALSE;
        } else {
            
            $fileUploadResultsArray = array(); // Create Array for Results
            
            ini_set('upload_max_filesize', '200M');
            set_time_limit(0);
            
            
            foreach ($_FILES as $file=>$name)
            {
                $this->uploadFile($file, $ext, $fileUploadResultsArray);
            }
            
            // Return List of Files Uploaded
            return $fileUploadResultsArray;
        }
    }
    
    /**
    * Method to Upload a Single File
    * @param string $fileInputName Name of the File Input. Eg. To upload $_FILES['file1'], simply give 'file1'
    * @param array $ext Extension to restrict file type to
    * @param array $fileUploadResultsArray File Upload Array to check against for multiple file uploads
    * This is neccessary for multi file uploads. It serves two purposes:
    * 1) It checks that the same file is not uploaded twice.
    * 2) It adds the result of the file upload to that array.
    * @return array Result of the File Upload
    */
    public function uploadFile($fileInputName, $ext=NULL, &$fileUploadResultsArray=NULL)
    {
        // First Check if array key exists
        if (array_key_exists($fileInputName, $_FILES)) {
            $file = $_FILES[$fileInputName];
        } else { // If not, return FALSE
            return FALSE;
        }
        
        if ($ext != NULL && !is_array($ext)) {
            $ext = array($ext);
        }
        
        // Check if Second Parameter is an array
        if (is_array($fileUploadResultsArray)) {
            $doubleUpload = array_key_exists($file['name'], $fileUploadResultsArray);
        } else {
            $doubleUpload = FALSE;
        }
        
        
        $fileInfoArray = array();
        
        // Check that file is not forbidden
        if ($this->isBannedFile($file['name'], $file['type'])) 
            {
                $fileInfoArray  = array ('success'=>FALSE, 'reason'=>'bannedfile', 'name'=>$file['name'], 'size'=>$file['size'], 'mimetype'=>$file['type'], 'errorcode'=>$file['error']);
            }
            
        // Check that file was not partially uploaded
        else if ($file['error'] == 3) 
            {
                $fileInfoArray  = array ('success'=>FALSE, 'reason'=>'partialuploaded');
            }
        
        // No File Provided
        else if ($file['error'] == 4) 
            { 
                $fileInfoArray  = array ('success'=>FALSE, 'reason'=>'nouploadedfileprovided', 'errorcode'=>$file['error']);
                $file['name'] = 'nofileprovided';
            }
            
        else if (is_array($ext) && !in_array($this->objFileParts->getExtension($file['name']), $ext)) {
            $fileInfoArray  = array ('success'=>FALSE, 'reason'=>'doesnotmeetextension', 'name'=>$file['name'], 'size'=>$file['size'], 'mimetype'=>$file['type'], 'errorcode'=>$file['error']);
        }
            
            

        // Prepare to Move File to Location and add database entry
        // Also check that same file is not upload twice in a multiple upload environment
        else if ($file['error'] < 3 && $doubleUpload == FALSE)
            {
                // Get Subfolder file will be stored in
                $subfolder = $this->objFileFolder->getFileFolder($file['name'], $file['type']);
                
                // Duplicate Subfolder in case file needs to go into tempFolder
                $originalsubfolder = $subfolder;
                
                // Create Array about file that will go to Results
                $fileInfoArray = array('overwrite' => FALSE);
                
                // Version Defaults to One
                $version = 1;
                
                // Implement Security Measures on Filename
                $filename = $this->secureFileName($file['name']);
                
                // Create Full Server Path to Uploaded File
                $savepath = $this->objConfig->getcontentBasePath().'users/'.$this->objUser->userId().'/'.$subfolder.'/'.$filename;
                
                // Create Path to File withour usrfiles prefix
                $path = 'users/'.$this->objUser->userId().'/'.$subfolder.'/'.$filename;
                
                // Check if File Exists
                if (file_exists($savepath)) {
                    
                    // Check if the file details are recorded
                    $originalFile = $this->objFile->getFileDetailsFromPath($path);
                    
                    // If file details are recorded, move file to temp file so long
                    if (is_array($originalFile)) {
                        // Subfolder now becomes temp
                        $subfolder = 'temp';
                        
                        // Reset paths to temp subfolder
                        $savepath = $this->objConfig->getcontentBasePath().'users/'.$this->objUser->userId().'/temp/'.$filename;
                        $path = 'users/'.$this->objUser->userId().'/temp/'.$filename;
                        
                        // Delete Information on Previous Temporary Version
                        $this->objFile->deleteTemporaryFileInfo($path);
                        
                        // Note: Version is not increased here. Only when user requests to overwrite
                        $fileInfoArray['overwrite'] = TRUE;
                    }
                }
                
                // Check If File was successfully uploaded
                if (move_uploaded_file($file['tmp_name'], $savepath)) {
                    
                    // 1) Add to Database
                    $fileId = $this->objFile->addFile($filename, $path, $file['size'], $file['type'], $subfolder, $version);
                    
                    // 2) Start Analysis of File
                    if ($subfolder == 'images' || $subfolder == 'audio' || $subfolder == 'video' || $subfolder == 'flash' || $originalsubfolder == 'images') {
                        // Get Media Info
                        $fileInfo = $this->analyzeMediaFile($savepath);
                        
                        // Add Information to Databse
                        $this->objMediaFileInfo->addMediaFileInfo($fileId, $fileInfo);
                        
                        // Create Thumbnail if Image
                        // Thumbnails are not created for temporary files
                        if ($subfolder == 'images' || $originalsubfolder == 'images') {
                            $this->objThumbnails->createThumbailFromFile($savepath, $fileId);
                        }
                    }
                    
                    /*
                    if ($subfolder == 'scripts' || $originalsubfolder == 'scripts') {
                        // Get Extension
                        $filetype = $this->objFileParts->getExtension($this->file['filename']);
                        
                        // Convert Extension to Language
                        switch ($filetype)
                        {
                            case 'phps': $filetype = 'php'; break;
                            case 'pl': $filetype = 'perl'; break;
                            case 'js': $filetype = 'javascript'; break;
                            case 'py': $filetype = 'python'; break;
                        }
                        
                        // Open File, Read Contents, Close
                        $handle = fopen ($savepath, "r"); 
                        $contents = fread ($handle, filesize ($savepath)); 
                        fclose ($handle);
                        
                        $objGeshi = $this->getObject('geshiwrapper', 'wrapgeshi');
                        $objGeshi->source = $contents;
                        $objGeshi->language = $filetype;
                        
                        $objGeshi->startGeshi();
                        $objGeshi->enableLineNumbers(2);
                        
                        $script = addSlashes($objGeshi->show());
                        
                        $this->objMetadataScripts->addScriptHighlight($fileId, $script);
                    }*/
                    
                    // Update Return Array Details
                    $fileInfoArray['success'] = TRUE;
                    $fileInfoArray['fileid'] = $fileId;
                    $fileInfoArray['path'] = $path;
                    $fileInfoArray['fullpath'] = $savepath;
                    $fileInfoArray['subfolder'] = $subfolder;
                    $fileInfoArray['originalfolder'] = $originalsubfolder;
                    
                } else { // Else Failed to Upload
                    $fileInfoArray['success'] = FALSE;
                    $fileInfoArray['reason'] = 'filecouldnotbesaved';
                    
                }
                
                // Update Standard File Details
                $fileInfoArray['name'] = $filename;
                $fileInfoArray['mimetype'] = $file['type'];
                $fileInfoArray['errorcode'] = $file['error'];
                $fileInfoArray['size'] = $file['size'];
                
            }
        else {
            // Attempted to upload file twice
        }
        
        // Only Add Info if now a double upload
        if (is_array($fileUploadResultsArray) && $doubleUpload == FALSE) {
            // Add Result to Upload Results Array
            $fileUploadResultsArray[$file['name']] = $fileInfoArray;
        }
        return $fileInfoArray;
    }
    
    /**
    * Method to Secure the filename of a file.
    * E.g. to allow PHP scripts to be uploaded, but not executed, they are renamed to .phps
    *      Filenames with spaces are converted to underscores (_)
    * Further 'security' can be added here
    * 
    * @param string $filename Filename to secure
    * @return string Secured Filename
    */
    public function secureFileName($filename)
    {
        $filename = str_replace("'", '', $filename);
        $filename = str_replace('"', '', $filename);
        
        // Security Measure - Rename file to phps if is a .php file
        $filename = preg_replace('/^(.*)\.php$/i', '\\1.phps', $filename);
        
        // Replace spaces in filename with underscores
        $filename = str_replace(' ', '_', $filename);
        
        return $filename;
    }
    
    /**
    * Method to check whether file is banned or not
    *
    * @todo Build in functionality to check banned files via mimetype
    *
    * @param string $filename Name of the File
    * @param string $mimetype Mimetype of the File
    * @return boolean True if banned, False if not
    */
    public function isBannedFile($filename, $mimetype)
    {
        // Set default to true
        $bannedType = FALSE;
        
        // Check by extension if banned
        if (in_array($this->objFileParts->getExtension($filename), $this->bannedExtensions)) {
            $bannedType = TRUE;
        }
        
        // Check via mimetype
        
        return $bannedType;
    }
    
    /**
    * Method to get media information about a file using getId3
    * This function takes the information and converts it into a single array
    * instead of going through it as a multi-dimensional array
    *
    * @param string $filePath Path to File
    * @return array Details of the File
    */
    public function getId3Info($filepath)
    {
        // Get Details and Convert to SingleArray
        return $this->objSingleArray->convertArray($this->objGetId3->analyze($filepath));
    }
    
    /**
    * Function to analyze getId3 Media Info
    * 
    * This function takes a file, and requests getID3 to analyze the file.
    * It then processes the analysis, focussing on information it requires.
    * This information is stored in an array and returned.
    *
    * @param string $filepath Path to File
    * @return array Processed Information of the File
    */
    public function analyzeMediaFile($filepath)
    {
        // Get Details and Convert to SingleArray
        $analysis = $this->getId3Info($filepath);
        
        // Remove Useless Information
        foreach ($analysis as $item=>$value)
        {
            // Remove if Key is a number
            if (is_int($item)) {
                unset($analysis[$item]);
            }
            
            // Remove if Item has no Value
            if (trim($value) == '' || trim($value) == '?=') {
                unset($analysis[$item]);
            }
            
        }
        
        // Create Array of Details
        $mediaInfo = array('width'=>'', 'height'=>'', 'playtime'=>'', 'format'=>'', 'framerate'=>'', 'bitrate'=>'', 'samplerate'=>'', 'title'=>'', 'artist'=>'', 'year'=>'', 'url'=>'');
        
        // Width
        if (array_key_exists('resolution_x', $analysis)) {
            $mediaInfo['width'] = $analysis['resolution_x'];
        }
        
        // Height
        if (array_key_exists('resolution_y', $analysis)) {
            $mediaInfo['height'] = $analysis['resolution_y'];
        }
        
        // Play Time
        if (array_key_exists('playtime_seconds', $analysis)) {
            $mediaInfo['playtime'] = floor($analysis['playtime_seconds']);
        }
        
        // Format
        if (array_key_exists('dataformat', $analysis)) {
            $mediaInfo['format'] = $analysis['dataformat'];
            
            // If JPEG, attempt to get width and height via Exif
            if ($format = 'jpg') {
                $info = getimagesize($filepath);
                $mediaInfo['width'] = $info[0]; // Width
                $mediaInfo['height'] = $info[1]; // Height
            }
        }
        
        // Frame Rate
        if (array_key_exists('framerate', $analysis)) {
            $mediaInfo['framerate'] = $analysis['framerate'];
        }
        
        // Bit Rate
        if (array_key_exists('bitrate', $analysis)) {
            $mediaInfo['bitrate'] = $analysis['bitrate'];
        }
        
        // Sample Rate
        if (array_key_exists('sample_rate', $analysis)) {
            $mediaInfo['samplerate'] = $analysis['sample_rate'];
        }
        
        // Title
        if (array_key_exists('title', $analysis)) {
            $mediaInfo['title'] = $analysis['title'];
        }
        
        // Artist
        if (array_key_exists('artist', $analysis)) {
            $mediaInfo['artist'] = $analysis['artist'];
        }
        
        // Year
        if (array_key_exists('year', $analysis)) {
            $mediaInfo['year'] = $analysis['year'];
        }
        
        // URL
        if (array_key_exists('url', $analysis)) {
            $mediaInfo['url'] = $analysis['url'];
        }
        
        // Convert rest of the data to XML for storage
        // NOTE: this xml may not be well formed, but keep so long
        $mediaInfo['getid3info'] = $this->objXMLSerializer->writeXML($analysis);
        
        return $mediaInfo;
    }
    

    
    
    

}

?>