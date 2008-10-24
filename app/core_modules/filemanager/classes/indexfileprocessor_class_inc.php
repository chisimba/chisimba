<?php

/**
 * Class to index files not yet stored in the database
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
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see
 */


/**
 * Class to index files not yet stored in the database
 *
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see
 */
class indexfileprocessor extends object
{

    public $recurseFolder = TRUE;
    public $debug = FALSE;

    /**
    * Constructor
    */
    function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objMimetype = $this->getObject('mimetypes', 'files');
        $this->objFile = $this->getObject('dbfile');
        $this->objFolders = $this->getObject('dbfolder');
        $this->objMediaFileInfo = $this->getObject('dbmediafileinfo');
        $this->objFileFolder = $this->getObject('filefolder');
        $this->objCleanUrl = $this->getObject('cleanurl');
        $this->objThumbnails = $this->getObject('thumbnails');
        $this->objIndexFiles = $this->getObject('indexfiles');
        $this->objAnalyzeMediaFile = $this->getObject('analyzemediafile');
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $folder Parameter description (if any) ...
     * @param  unknown $userId Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    function indexFolder($type, $id, $folder, $userId)
    {
        $this->objIndexFiles->recurse = $this->recurseFolder;
        $results = $this->objIndexFiles->scanDirectory($folder);
        
        if ($this->debug) {
            echo 'Folder to index';
            var_dump($folder);
            echo 'Scanned Files';
            var_dump($results);
        }
        
        return $this->processResults($type, $id, $results, $userId);
    }

    /**
    * Method to Scan and index the files of a user
    * @param  string $userId User Id whose folder should be scanned
    * @return array  List of Files that were indexed
    */
    function indexUserFiles($userId='1')
    {
        return $this->indexFolder('users', $userId, $this->objConfig->getcontentBasePath().'users/'.$userId.'/', $userId);
    }
    
    /**
    * Method to Scan and index the files of a user
    * @param  string $userId User Id whose folder should be scanned
    * @return array  List of Files that were indexed
    */
    function indexFiles($type, $id='1')
    {
        return $this->indexFolder($type, $id, $this->objConfig->getcontentBasePath().$type.'/'.$id.'/', $id);
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  array   $results Parameter description (if any) ...
     * @param  string  $userId  Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    function processResults($type, $id, $results, $userid)
    {
        // Split Folders from Results
        $folders = $results[1];
        // Add User Folder
        $folders[] = $this->objConfig->getcontentBasePath().$type.'/'.$id.'/';
        // Process Files
        $this->processFolderResults($folders);
        
        // Split files from results
        $files = $results[0];
        // Process Files
        $indexedFiles = $this->processFileResults($files, $id, $userid, $type);
        
        // Clean up any broken records
        $this->objMediaFileInfo->cleanUpMismatchedMediaFiles();
        
        return $indexedFiles;
    }
    
    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  array   $folders Parameter description (if any) ...
     * @return void
     * @access private
     */
    private function processFolderResults($folders)
    {
        if (count($folders) > 0) {
            foreach ($folders as $folder)
            {
                $this->objFolders->indexFolder($folder);
            }
        }
    }
    
    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $userId Parameter description (if any) ...
     * @return array   Return description (if any) ...
     * @access private
     */
    private function generateFilesArray($userId, $type)
    {
        $files = $this->objFile->getPathFiles($type, $userId);
        
        if ($this->debug) {
            var_dump($files);
        }
        
        $list = array();
        
        if (count($files) > 0) {
            foreach ($files as $file)
            {
                $list[] = $file['path'];
            }
        }
        
        if ($this->debug) {
            echo ' List of Files';
            var_dump($list);
        }
        
        return $list;
    }
    
    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  array   $files  Parameter description (if any) ...
     * @param  unknown $userId Parameter description (if any) ...
     * @return array   Return description (if any) ...
     * @access private
     */
    private function processFileResults($files, $id, $userId, $type)
    {
        $indexedFiles = array();
        $userFiles = $this->generateFilesArray($id, $type);
        
        if (count($files) > 0) {
            foreach ($files as $file)
            {
                preg_match('/(?<=usrfiles(\\\|\/)).*/', $file, $regs);
                $path = $regs[0];
                $path = $this->objCleanUrl->cleanUpUrl($path);
                
                if (!in_array($path, $userFiles)) {
                    $indexedFiles[] = $this->processIndexedFile($path, $userId);
                }
            }
        }
        return $indexedFiles;
    }
    
    /**
    * Method to take a file that is not in the index, process its data
    * and add it to the database
    * @param  string $filePath Path to File
    * @param  string $userId   UserId of the Person to whom the file should belong to
    * @param  string $mimetype Mimetype of the File (Optional)
    * @return string File Id
    */
     function processIndexedFile($filePath, $userId, $mimetype='')
    {
        // Clean Up the File Path
        $filePath = $this->objCleanUrl->cleanUpUrl($filePath);
        // Create the Full Path to the File
        $savePath = $this->objConfig->getcontentBasePath().'/'.$filePath;
        // Clean up the Full Path to the File
        $savePath = $this->objCleanUrl->cleanUpUrl($savePath);

        log_debug($savePath);

        // Take filename, and create cleaned up version (no punctuation, etc.)
        $cleanFilename = $this->objCleanUrl->cleanFilename($filePath);
        // Create the Full Path to the File based on cleaned up filename
        $cleanFilenameSavePath = $this->objConfig->getcontentBasePath().'/'.$cleanFilename;
        // Clean up the Full Path to the File based on cleaned up filename
        $cleanFilenameSavePath = $this->objCleanUrl->cleanUpUrl($cleanFilenameSavePath);

        // Attempt Rename
        $renameAttempt = rename($savePath, $cleanFilenameSavePath);

        // If rename is successful, swop dirty filename with clean one for database
        if ($renameAttempt == TRUE) {
            $filePath = $cleanFilename;
            $savePath = $cleanFilenameSavePath;
        }

        // Determine filename
        $filename = basename($filePath);

        // Get mimetype if not given
        if ($mimetype == '') {
            $mimetype = $this->objMimetype->getMimeType($filePath);
        }

        // Get Category
        $category = $this->objFileFolder->getFileFolder($filename, $mimetype);

        // File Size
        $fileSize = filesize($savePath);

        // 1) Add to Database
        $fileId = $this->objFile->addFile($filename, $filePath, $fileSize, $mimetype, $category, '1', $userId);

        // 2) Start Analysis of File
        if ($category == 'images' || $category == 'audio' || $category == 'video' || $category == 'flash') {
            // Get Media Info
            $fileInfo = $this->objAnalyzeMediaFile->analyzeFile($savePath);
            $fileInfo[0]['creatorid'] = $userId;
            $fileInfo[0]['modifierid'] = $userId;
            // Add Information to Databse
            $this->objMediaFileInfo->addMediaFileInfo($fileId, $fileInfo[0]);

            // Check if alternative mimetype is provided
            if ($fileInfo[1] != '') {
                $this->objFile->updateMimeType($fileId, $fileInfo[1]);
            }

            // Create Thumbnail if Image
            // Thumbnails are not created for temporary files
            if ($category == 'images') {
                $this->objThumbnails->createThumbailFromFile($savePath, $fileId);
            }
            // Check if Timeline
        } else if ($category == 'scripts' && $mimetype == 'application/xml') {
                /*
                $objCatalogueConfig = $this->getObject('catalogueconfig', 'modulecatalogue');
                
                if ($objCatalogueConfig->getModuleName('timeline') != FALSE) {
                    // Load Timeline Parser
                    $objTimeline = $this->getObject('timelineparser', 'timeline');
                    
                    // Check if Valid
                    if ($objTimeline->isValidTimeline($savepath)) {
                        // If yes, change category to timeline
                        $this->objFile->updateFileCategory($fileId, 'timeline');
                    }
                }*/
        }
        
        return $fileId;
    }
} // end class
?>