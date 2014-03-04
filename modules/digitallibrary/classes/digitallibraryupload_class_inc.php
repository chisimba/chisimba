<?php

/**
 * Class to Handle Uploads for Digital Library
 *
 * This class handles the upload process for that module.
 * Apart from the upload, this class also places the file in a suitable subfolder, updates the
 * database, parses files for metadata, and creates thumbnails for images.
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
 * @package   filemanager
 * @author   David Wafula davidwaf@gmail.com
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 * @see
 * @todo      Improve Code to Handle Large Files
 */
$this->loadClass('filemanagerobject', 'filemanager');

class digitallibraryupload extends filemanagerobject {

    /**
     * @var string $formaction Form Action to take
     */
    public $formaction;

    /**
     * @var array @bannedExtensions List of Banned Extensions
     */
    public $bannedExtensions = array('exe', 'cgi', 'dll');

    /**
     * @var string $uploadFolder Folder to place uploads in
     */
    private $uploadFolder = '';

    /**
     * @var boolean $useFileSubFolder Flag to set whether uploaded files should be placed in the Upload Folder +  a subfolder matching file type
     */
    private $useFileSubFolder = FALSE;

    /**
     * @var boolean $enableOverwriteIncrement
     * If a file called myinfo.txt is uploaded, but one exists, new file will be called 'myinfo_1.txt'
     */
    public $enableOverwriteIncrement = FALSE;

    /**
     * Constructor
     */
    public function init() {
        $this->name = 'fileupload';
        $this->formaction = $this->uri(array('action' => 'upload'));

        $this->numInputs = 1;
        $this->formExtra = '';

        // Load All Necessary Objects
        $this->objUser = $this->getObject('user', 'security');
        $this->objFileParts = $this->getObject('fileparts', 'files');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objFile = $this->getObject('dbfile', 'filemanager');
        $this->objMediaFileInfo = $this->getObject('dbmediafileinfo', 'filemanager');
        $this->objMetadataScripts = $this->getObject('dbmetadatascripts', 'filemanager');
        $this->objFileFolder = $this->getObject('filefolder', 'filemanager');
        $this->objFileTags = $this->getObject('dbfiletags', 'filemanager');
        $this->objAnalyzeMediaFile = $this->getObject('analyzemediafile', 'filemanager');

        $this->objThumbnails = $this->getObject('thumbnails', 'filemanager');
        $this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $this->objMkdir = $this->getObject('mkdir', 'files');

        // Load the Language Object
        $this->objLanguage = $this->getObject('language', 'language');

        // Check that Upload Folders exist
        $objFolderCheck = $this->getObject('userfoldercheck', 'filemanager');
        $objFolderCheck->checkUserFolder($this->objUser->userId());

        // Path for temp files
        $path = $this->objConfig->getcontentBasePath() . '/filemanager_tempfiles';
        $this->objMkdir->mkdirs($path, 0777);

        // Default Upload Folder is usrfiles/users/{userid}
        $this->uploadFolder = 'users/' . $this->objUser->userId() . '/';
    }

    /**
     * Method to set the upload folder
     *
     * This folder will be created in the usrfiles directory
     *
     * @param string $folder Name of the folder
     */
    public function setUploadFolder($folder) {
        if (trim($folder) != '') {
            $this->uploadFolder = $folder . '/';
            $this->uploadFolder = $this->objCleanUrl->cleanUpUrl($this->uploadFolder); // Clean Up Folder
        }
    }

    /**
     * Method to show an upload form
     * @todo: use htmlelements
     */
    public function show($folderId = '', $fileSizeLimit = '') {
        $form = '<form name="uploadform" id="form1" enctype="multipart/form-data" method="post" action="' . $this->formaction . '">';

        if (!is_int($this->numInputs) || $this->numInputs < 1) {
            $this->numInputs = 2;
        }

        $break = '<br />';


        $objLicense = $this->newObject('licensechooserdropdown', 'creativecommons');
        $i = 1;
        $objLicense->inputName = 'creativecommons_' . $this->name . $i;


        $form .= $this->objLanguage->languageText('word_choosefile', 'system', 'Choose file') . $break . '<input type="file" name="'
                . $this->name . $i . '" '
                . 'id="' . $this->name . $i . '" '
                . 'size="40" /> ' . $objLicense->show() . $break;
        $form.=$this->objLanguage->languageText('word_tags', 'system', 'Tags') . $break . '<textarea class="required" cols="80" rows="3" id="tagsfield" name="tagsfield"></textarea>' . $break;
        //$break = '<br />';


        if ($this->numInputs == 1) {
            $form .= ' <input type="submit" name="submitform" class="button" value="' . $this->objLanguage->languageText('phrase_uploadfile', 'filemanager', 'Upload File') . '" />';
        } else {
            $form .= ' <input type="submit" name="submitform" class="button" value="' . $this->objLanguage->languageText('phrase_uploadfiles', 'system', 'Upload Files') . '" />';
        }

        if ($folderId != '') {
            $form .= ' <input type="hidden" name="folder" value="' . $folderId . '" />';
        }

        if ($fileSizeLimit != '') {
            $form .= ' <input type="hidden" name="MAX_FILE_SIZE" value="' . $fileSizeLimit . '" />';
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
     * @param  array  $ext Extensions to restrict to
     * @return array  Results of Upload
     */
    public function uploadFiles($ext = NULL) {
        if (count($_FILES) == 0) { // Checked that Files were Uploaded
            return FALSE;
        } else {

            $fileUploadResultsArray = array(); // Create Array for Results

            ini_set('upload_max_filesize', '200M');
            set_time_limit(0);


            foreach ($_FILES as $file => $name) {
                $this->uploadFile($file, $ext, $fileUploadResultsArray);
            }

            // Return List of Files Uploaded
            return $fileUploadResultsArray;
        }
    }

    /**
     * Method to Upload ALL Files Posted as an Array of files
     *
     * e.g. When posting like <input type='FILE' name='file[]'/>
     *
     * It returns an array with details of files uploaded, as well as errors.
     * @access Public
     * @param  array  $ext Extensions to restrict to
     * @return array  Results of Upload
     */
    public function uploadFilesArray($fileInputName, $ext = NULL, $fileUploadResultsArray = array()) {
        // First Check if array key exists
        //if (array_key_exists($fileInputName, $_FILES)) {
        //$file = $_FILES[$fileInputName];
        //} else { // If not, return FALSE
        //    return FALSE;
        //}

        if ($ext != NULL && !is_array($ext)) {
            $ext = array($ext);
        }

        $counter = -1;

        //Charl Mert : Capturing the array of files
        if (is_array($_FILES[$fileInputName]['name'])) {
            foreach ($_FILES[$fileInputName]['name'] as $fname) {
                $counter++;

                $file['name'] = $fname;
                $file['type'] = $_FILES[$fileInputName]['type'][$counter];
                $file['error'] = $_FILES[$fileInputName]['error'][$counter];
                $file['tmp_name'] = $_FILES[$fileInputName]['tmp_name'][$counter];
                $file['size'] = $_FILES[$fileInputName]['size'][$counter];

                //if ($counter > 3) {var_dump($file); exit;}
                // Check if Second Parameter is an array
                if (is_array($fileUploadResultsArray)) {
                    $doubleUpload = array_key_exists($file['name'], $fileUploadResultsArray);
                } else {
                    $doubleUpload = FALSE;
                }


                $fileInfoArray = array();

                // Check that file is not forbidden
                if ($this->isBannedFile($file['name'], $file['type'])) {
                    $fileInfoArray = array('success' => FALSE, 'reason' => 'bannedfile', 'name' => $file['name'], 'size' => $file['size'], 'mimetype' => $file['type'], 'errorcode' => $file['error']);
                }

                // Check that file was not partially uploaded
                else if ($file['error'] == 3) {
                    $fileInfoArray = array('success' => FALSE, 'reason' => 'partialuploaded');
                }

                // No File Provided
                else if ($file['error'] == 4) {
                    $fileInfoArray = array('success' => FALSE, 'reason' => 'nouploadedfileprovided', 'errorcode' => $file['error']);
                    $file['name'] = 'nofileprovided';
                } else if (is_array($ext) && !in_array($this->objFileParts->getExtension($file['name']), $ext)) {
                    $fileInfoArray = array('success' => FALSE, 'reason' => 'doesnotmeetextension', 'name' => $file['name'], 'size' => $file['size'], 'mimetype' => $file['type'], 'errorcode' => $file['error']);
                }



                // Prepare to Move File to Location and add database entry
                // Also check that same file is not upload twice in a multiple upload environment
                else if ($file['error'] < 3 && $doubleUpload == FALSE) {
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

                    // Determine whether to include subfolders
                    if ($this->useFileSubFolder) {
                        // Create Full Server Path to Uploaded File
                        $savepath = $this->objConfig->getcontentBasePath() . '/' . $this->uploadFolder . $subfolder . '/';

                        // Create Path to File withour usrfiles prefix
                        $path = $this->uploadFolder . $subfolder . '/';
                    } else {
                        // Create Full Server Path to Uploaded File
                        $savepath = $this->objConfig->getcontentBasePath() . '/' . $this->uploadFolder . '/';

                        // Create Path to File withour usrfiles prefix
                        $path = $this->uploadFolder . '/';
                    }

                    // Clean Up Paths
                    $savepath = $this->objCleanUrl->cleanUpUrl($savepath);
                    $path = $this->objCleanUrl->cleanUpUrl($path);

                    // Create Directory
                    $this->objMkdir->mkdirs($savepath, 0777);

                    // Add File Name
                    $savepath .= $filename;
                    $path .= $filename;

                    ////////////////////////////////////////////////////////////////////
                    // Create a Flag whether file has been save to database
                    $addToDatabaseAndIndex = FALSE;

                    // Check if File Exists
                    if (file_exists($savepath)) {

                        // Check if the file details are recorded
                        $originalFile = $this->objFile->getFileDetailsFromPath($path);

                        // If file details are recorded, move file to temp file so long
                        if (is_array($originalFile)) {

                            // Change Save Path 
                            $savePath = $this->objConfig->getcontentBasePath() . '/filemanager_tempfiles/' . $originalFile['id'];

                            // Check if Overwrite Increment is enabled
                            if ($this->enableOverwriteIncrement) {


                                // Create Full Server Path to Uploaded File
                                $savepath = $this->objConfig->getcontentBasePath() . '/' . $this->uploadFolder . '/';
                                // Create Path to File withour usrfiles prefix
                                $path = $this->uploadFolder . '/';

                                // Clean Up Paths
                                $savepath = $this->objCleanUrl->cleanUpUrl($savepath);
                                $path = $this->objCleanUrl->cleanUpUrl($path);

                                $objOverwriteIncrement = $this->getObject('overwriteincrement', 'filemanager');
                                $filename = $objOverwriteIncrement->checkfile($filename, $path);

                                $savepath .= $filename;
                                $path .= $filename;


                                // Move to new destination, mark as eligible to go into database
                                if (move_uploaded_file($file['tmp_name'], $savepath)) {
                                    $addToDatabaseAndIndex = TRUE;
                                } else {
                                    $addToDatabaseAndIndex = FALSE;
                                }

                                // Move to Save Path
                            } else if (move_uploaded_file($file['tmp_name'], $savePath)) {
                                $fileInfoArray['overwrite'] = TRUE;
                                $fileInfoArray['success'] = FALSE;
                                $fileInfoArray['fileid'] = $originalFile['id'];
                                $fileInfoArray['reason'] = 'needsoverwrite';

                                $addToDatabaseAndIndex = FALSE;
                            }
                        } else { // Overwrite and Index
                            if (move_uploaded_file($file['tmp_name'], $savepath)) {
                                $addToDatabaseAndIndex = TRUE;
                            }
                        }

                        // Check If File was successfully uploaded
                    } else if (move_uploaded_file($file['tmp_name'], $savepath)) {
                        $addToDatabaseAndIndex = TRUE;
                    } else {// Else Failed to Upload
                        $fileInfoArray['success'] = FALSE;
                        $fileInfoArray['reason'] = 'filecouldnotbesaved';
                    }


                    if ($addToDatabaseAndIndex) {

                        // 1) Add to Database
                        $fileId = $this->objFile->addFile($filename, $path, $file['size'], $file['type'], $subfolder, $version, $this->objUser->userId(), NULL, $this->getParam('creativecommons_' . $fileInputName, ''));

                        //tag it
                        $keywords = $this->getParam("tagsfield");


                        $this->objFileTags->addFileTags($fileId, $keywords);


                        // 2) Start Analysis of File
                        if ($subfolder == 'images' || $subfolder == 'audio' || $subfolder == 'video' || $subfolder == 'flash' || $originalsubfolder == 'images') {

                            // Get Media Info
                            $fileInfo = $this->objAnalyzeMediaFile->analyzeFile($savepath);

                            // Add Information to Databse
                            $this->objMediaFileInfo->addMediaFileInfo($fileId, $fileInfo[0]);

                            // Check whether mimetype needs to be updated
                            if ($fileInfo[1] != '') {
                                $this->objFile->updateMimeType($fileId, $fileInfo[1]);
                            };

                            // Create Thumbnail if Image
                            // Thumbnails are not created for temporary files
                            if ($subfolder == 'images' || $originalsubfolder == 'images') {
                                $this->objThumbnails->createThumbailFromFile($savepath, $fileId);
                            }
                        } else if ($subfolder == 'scripts' && ($file['type'] == 'application/xml' || $file['type'] == 'text/xml')) {

                            /*
                              $objCatalogueConfig = $this->getObject('catalogueconfig', 'modulecatalogue');
                              echo $objCatalogueConfig->getModuleName('timeline');
                              if ($objCatalogueConfig->getModuleName('timeline') != FALSE) {
                              // Load Timeline Parser
                              $objTimeline = $this->getObject('timelineparser', 'timeline');

                              // Check if Valid
                              if ($objTimeline->isValidTimeline($savepath)) {
                              // If yes, change category to timeline
                              $this->objFile->updateFileCategory($fileId, 'timeline');
                              }
                              } */
                        }


                        // Update Return Array Details
                        $fileInfoArray['success'] = TRUE;
                        $fileInfoArray['fileid'] = $fileId;
                        $fileInfoArray['path'] = $path;
                        $fileInfoArray['fullpath'] = $savepath;
                        $fileInfoArray['subfolder'] = $subfolder;
                        $fileInfoArray['originalfolder'] = $originalsubfolder;
                        //array_push($resultsArray, $fileInfoArray);
                    }

                    // Update Standard File Details
                    $fileInfoArray['name'] = $filename;
                    $fileInfoArray['mimetype'] = $file['type'];
                    $fileInfoArray['errorcode'] = $file['error'];
                    $fileInfoArray['size'] = $file['size'];
                } else {
                    // Attempted to upload file twice
                }
                // Only Add Info if now a double upload
                if (is_array($fileUploadResultsArray)) {
                    // Add Result to Upload Results Array
                    $fileUploadResultsArray[$file['name']] = $fileInfoArray;
                }
            }
        } else {
            return FALSE; //Something wrong with the POST array (Maybe forgot a [] on 1 of the input names)
        }
        return $fileUploadResultsArray;
    }

    /**
     * Method to Upload a Single File
     * @param  string $fileInputName          Name of the File Input. Eg. To upload $_FILES['file1'], simply give 'file1'
     * @param  array  $ext                    Extension to restrict file type to
     * @param  array  $fileUploadResultsArray File Upload Array to check against for multiple file uploads
     *                                        This is neccessary for multi file uploads. It serves two purposes:
     *                                        1) It checks that the same file is not uploaded twice.
     *                                        2) It adds the result of the file upload to that array.
     * @return array  Result of the File Upload
     */
    public function uploadFile($fileInputName, $ext = NULL, &$fileUploadResultsArray = NULL) {

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
        if ($this->isBannedFile($file['name'], $file['type'])) {
            $fileInfoArray = array('success' => FALSE, 'reason' => 'bannedfile', 'name' => $file['name'], 'size' => $file['size'], 'mimetype' => $file['type'], 'errorcode' => $file['error']);
        }

        // Check that file was not partially uploaded
        else if ($file['error'] == 3) {
            $fileInfoArray = array('success' => FALSE, 'reason' => 'partialuploaded');
        }

        // No File Provided
        else if ($file['error'] == 4) {
            $fileInfoArray = array('success' => FALSE, 'reason' => 'nouploadedfileprovided', 'errorcode' => $file['error']);
            $file['name'] = 'nofileprovided';
        } else if (is_array($ext) && !in_array($this->objFileParts->getExtension($file['name']), $ext)) {
            $fileInfoArray = array('success' => FALSE, 'reason' => 'doesnotmeetextension', 'name' => $file['name'], 'size' => $file['size'], 'mimetype' => $file['type'], 'errorcode' => $file['error']);
        }



        // Prepare to Move File to Location and add database entry
        // Also check that same file is not upload twice in a multiple upload environment
        else if ($file['error'] < 3 && $doubleUpload == FALSE) {
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

            // Determine whether to include subfolders
            if ($this->useFileSubFolder) {
                // Create Full Server Path to Uploaded File
                $savepath = $this->objConfig->getcontentBasePath() . '/' . $this->uploadFolder . $subfolder . '/';

                // Create Path to File withour usrfiles prefix
                $path = $this->uploadFolder . $subfolder . '/';
            } else {
                // Create Full Server Path to Uploaded File
                $savepath = $this->objConfig->getcontentBasePath() . '/' . $this->uploadFolder . '/';

                // Create Path to File withour usrfiles prefix
                $path = $this->uploadFolder . '/';
            }

            // Clean Up Paths
            $savepath = $this->objCleanUrl->cleanUpUrl($savepath);
            $path = $this->objCleanUrl->cleanUpUrl($path);

            // Create Directory
            $this->objMkdir->mkdirs($savepath, 0777);

            // Add File Name
            $savepath .= $filename;
            $path .= $filename;

////////////////////////////////////////////////////////////////////
            // Create a Flag whether file has been save to database
            $addToDatabaseAndIndex = FALSE;

            // Check if File Exists
            if (file_exists($savepath)) {

                // Check if the file details are recorded
                $originalFile = $this->objFile->getFileDetailsFromPath($path);

                // If file details are recorded, move file to temp file so long
                if (is_array($originalFile)) {

                    // Change Save Path 
                    $savePath = $this->objConfig->getcontentBasePath() . '/filemanager_tempfiles/' . $originalFile['id'];

                    // Check if Overwrite Increment is enabled
                    if ($this->enableOverwriteIncrement) {


                        // Create Full Server Path to Uploaded File
                        $savepath = $this->objConfig->getcontentBasePath() . '/' . $this->uploadFolder . '/';
                        // Create Path to File withour usrfiles prefix
                        $path = $this->uploadFolder . '/';

                        // Clean Up Paths
                        $savepath = $this->objCleanUrl->cleanUpUrl($savepath);
                        $path = $this->objCleanUrl->cleanUpUrl($path);

                        $objOverwriteIncrement = $this->getObject('overwriteincrement', 'filemanager');
                        $filename = $objOverwriteIncrement->checkfile($filename, $path);

                        $savepath .= $filename;
                        $path .= $filename;


                        // Move to new destination, mark as eligible to go into database
                        if (move_uploaded_file($file['tmp_name'], $savepath)) {
                            $addToDatabaseAndIndex = TRUE;
                        } else {
                            $addToDatabaseAndIndex = FALSE;
                        }

                        // Move to Save Path
                    } else if (move_uploaded_file($file['tmp_name'], $savePath)) {
                        $fileInfoArray['overwrite'] = TRUE;
                        $fileInfoArray['success'] = FALSE;
                        $fileInfoArray['fileid'] = $originalFile['id'];
                        $fileInfoArray['reason'] = 'needsoverwrite';

                        $addToDatabaseAndIndex = FALSE;
                    }
                } else { // Overwrite and Index
                    if (move_uploaded_file($file['tmp_name'], $savepath)) {
                        $addToDatabaseAndIndex = TRUE;
                    }
                }

                // Check If File was successfully uploaded
            } else if (move_uploaded_file($file['tmp_name'], $savepath)) {
                $addToDatabaseAndIndex = TRUE;
            } else {// Else Failed to Upload
                $fileInfoArray['success'] = FALSE;
                $fileInfoArray['reason'] = 'filecouldnotbesaved';
            }


            if ($addToDatabaseAndIndex) {

                // 1) Add to Database
                $fileId = $this->objFile->addFile($filename, $path, $file['size'], $file['type'], $subfolder, $version, $this->objUser->userId(), $this->getParam('cite_' . $fileInputName, ''), $this->getParam('creativecommons_' . $fileInputName, ''));

                $keywords = $this->getParam("tagsfield");
                $this->objFileTags->addFileTags($fileId, $keywords);
                // 2) Start Analysis of File
                if ($subfolder == 'images' || $subfolder == 'audio' || $subfolder == 'video' || $subfolder == 'flash' || $originalsubfolder == 'images') {

                    // Get Media Info
                    $fileInfo = $this->objAnalyzeMediaFile->analyzeFile($savepath);

                    // Add Information to Databse
                    $this->objMediaFileInfo->addMediaFileInfo($fileId, $fileInfo[0]);

                    // Check whether mimetype needs to be updated
                    if ($fileInfo[1] != '') {
                        $this->objFile->updateMimeType($fileId, $fileInfo[1]);
                    };

                    // Create Thumbnail if Image
                    // Thumbnails are not created for temporary files
                    if ($subfolder == 'images' || $originalsubfolder == 'images') {
                        $this->objThumbnails->createThumbailFromFile($savepath, $fileId);
                    }
                } else if ($subfolder == 'scripts' && ($file['type'] == 'application/xml' || $file['type'] == 'text/xml')) {

                    /*
                      $objCatalogueConfig = $this->getObject('catalogueconfig', 'modulecatalogue');
                      echo $objCatalogueConfig->getModuleName('timeline');
                      if ($objCatalogueConfig->getModuleName('timeline') != FALSE) {
                      // Load Timeline Parser
                      $objTimeline = $this->getObject('timelineparser', 'timeline');

                      // Check if Valid
                      if ($objTimeline->isValidTimeline($savepath)) {
                      // If yes, change category to timeline
                      $this->objFile->updateFileCategory($fileId, 'timeline');
                      }
                      } */
                }


                // Update Return Array Details
                $fileInfoArray['success'] = TRUE;
                $fileInfoArray['fileid'] = $fileId;
                $fileInfoArray['path'] = $path;
                $fileInfoArray['fullpath'] = $savepath;
                $fileInfoArray['subfolder'] = $subfolder;
                $fileInfoArray['originalfolder'] = $originalsubfolder;

                $viewlink=new link($this->uri(array("action"=>"fileinfo","id"=>$fileId)));
                $url=$viewlink->href;
              
                $objLucene = $this->getObject('indexdata', 'search');
                $objLucene->luceneIndex($fileId, strftime('%Y-%m-%d', mktime()), $url, $filename, NULL, NULL, digitallibrary, $this->objUser->userId(), NULL, NULL, NULL);
                 }

            // Update Standard File Details
            $fileInfoArray['name'] = $filename;
            $fileInfoArray['mimetype'] = $file['type'];
            $fileInfoArray['errorcode'] = $file['error'];
            $fileInfoArray['size'] = $file['size'];
        } else {
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
     * @param  string $filename Filename to secure
     * @return string Secured Filename
     */
    public function secureFileName($filename) {
        return $this->objCleanUrl->cleanFilename($filename);
    }

    /**
     * Method to check whether file is banned or not
     *
     * @todo   Build in functionality to check banned files via mimetype
     *
     * @param  string  $filename Name of the File
     * @param  string  $mimetype Mimetype of the File
     * @return boolean True if banned, False if not
     */
    public function isBannedFile($filename, $mimetype) {
        // Set default to true
        $bannedType = FALSE;

        // Check by extension if banned
        if (in_array($this->objFileParts->getExtension($filename), $this->bannedExtensions)) {
            $bannedType = TRUE;
        }

        // Check via mimetype

        return $bannedType;
    }

}

?>