<?
/**
* Class to handle interaction with table tbl_files
* This table lists all files that were uploaded to the system
*
* @author Tohir Solomons
*/
class dbfile extends dbTable
{
    
    /**
    * @var array $currentFile Record of Current File working on
    * @access private
    */
    private $currentFile;
    
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files');
        $this->objUser =& $this->getObject('user', 'security');
        
        $this->objFileParts =& $this->getObject('fileparts', 'files');
        
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objCleanUrl =& $this->getObject('cleanurl');
        
        $this->objMediaFileInfo =& $this->getObject('dbmediafileinfo');
        $this->objUserFolder =& $this->getObject('userfoldercheck');
        $this->objFileFolder =& $this->getObject('filefolder');
        $this->objMimetypes =& $this->getObject('mimetypes', 'files');
        
        $this->objLanguage =& $this->getObject('language', 'language');
        
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('formatfilesize', 'files');
    }
    
    /**
    * Method to get the record containing file details
    * @access Public
    * @param string $fileId
    * @return array Details of the File
    */
    public function getFile($fileId)
    {
        // The current file is stored in a private variable to prevent possible unnecessary db queries
        if ($this->currentFile != '' && $this->currentFile['id'] == $fileId) {
            return $this->currentFile;
        } else {
            $file = $this->getRow('id', $fileId);
            
            if ($file != FALSE) {
                $this->currentFile =& $file;
            }
            
            return $file;
        }
        
    }
    
    /**
    * Method to get a single piece of information from a file
    * @access private
    * @param string $part Piece to get
    * @param string $fileId Record Id of the File
    * @return mixed
    */
    private function getPart($part, $fileId)
    {
        $file = $this->getFile($fileId);
        
        if ($file == FALSE) {
            return FALSE;
        } else {
            
            if (array_key_exists($part, $file)) {
                return $file[$part];
            } else {
                return FALSE;
            }
            
        }
    }
    
    /**
    * Method to get the filename of a file
    * @access public
    * @param string $fileId Record Id of the File
    * @return string File Name
    */
    public function getFileName($fileId)
    {
        return $this->getPart('filename', $fileId);
    }
    
    /**
    * Method to get the size of a file
    * @access public
    * @param string $fileId Record Id of the File
    * @return int File Size
    */
    public function getFileSize($fileId)
    {
        return $this->getPart('filesize', $fileId);
    }
    
    /**
    * Method to get the version number of a file
    * @access public
    * @param string $fileId Record Id of the File
    * @return int File Version
    */
    public function getFileVersion($fileId)
    {
        return $this->getPart('version', $fileId);
    }
    
    /**
    * Method to get the mimetype of a file
    * @access public
    * @param string $fileId Record Id of the File
    * @return string Mime Type
    */
    public function getFileMimetype($fileId)
    {
        return $this->getPart('mimetype', $fileId);
    }
    
    /**
    * Method to get the local path to a file
    * @access public
    * @param string $fileId Record Id of the File
    * @return string Local Path to File
    */
    public function getFilePath($fileId)
    {
        $path = $this->objConfig->getcontentPath().$this->getPart('path', $fileId);
        
        $this->objCleanUrl->cleanUpUrl($path);
        
        return $path;
    }
    
    /**
    * Method to get the absolute path to a file
    * @access public
    * @param string $fileId Record Id of the File
    * @return string Path to File
    */
    public function getFullFilePath($fileId)
    {
        $path = $this->objConfig->getcontentBasePath().$this->getPart('path', $fileId);
        
        $this->objCleanUrl->cleanUpUrl($path);
        
        return $path;
    }
    
    /**
    * Method to add a file
    * @param string $filename Name of the File
    * @param string $path Path of the File
    * @param int $filesize Size of the File
    * @param string $mimetype Mimetype of the File
    * @param string $category Subfolder/Category file is stored in
    * @param string $version Version of the file
    * @param string $userId User to whom the file belongs to
    * @param string $description Description of the file
    * @param string $license License of the file
    * @return string Record Id of the File
    */
    public function addFile($filename, $path, $filesize, $mimetype, $category, $version=1, $userId=NULL, $description=NULL, $license=NULL)
    {
        // Assume User is Logged in User if not provided
        if ($userId == NULL) {
            $userId = $this->objUser->userId();
        }
        
        // Determine extension
        $datatype = $this->objFileParts->getExtension($filename);
        
        return $this->insert(array(
                'userid' => $userId,
                'filename' => $filename,
                'datatype' => $datatype,
                'path' => $path,
                'filefolder'=>dirname($path),
                'description' => $description,
                'version' => $version,
                'filesize' => $filesize,
                'mimetype' => $mimetype,
                'category' => $category,
                'license' => $license,
                'moduleuploaded' => $this->getParam('module'),
                'creatorid' => $this->objUser->userId(),
                'modifierid' => $this->objUser->userId(),
                'datecreated' => strftime('%Y-%m-%d', mktime()),
                'timecreated' => strftime('%H:%M:%S', mktime())
                )
            );
    }
    
    /**
    * Method to get All the Files of a User
    * @param string $userId User Id of the User
    * @param string $category Optional Category Filter
    * @param array $restrictfiletype Optional File Type Restriction
    * @param boolean $latestVersionOnly List Latest Version Only or All Version
    * @return array List of Files
    */
    public function getUserFiles($userId, $category=NULL, $restrictfiletype=NULL, $latestVersionOnly=FALSE)
    {
        $where = ' WHERE userid=\''.$userId.'\'';
        
        
        if ($category != NULL) {
            $where .= ' AND category=\''.$category.'\'';
        }
        
        if ($restrictfiletype != NULL && is_array($restrictfiletype) && count($restrictfiletype) > 0) {
            
            $where .= ' AND (';
            $or = '';
            
            foreach ($restrictfiletype as $type)
            {
                $where .= $or.'datatype = \''.$type.'\' ';
                $or = ' OR ';
            }
            
            $where .= ')';
        }
        
        $where .= ' ORDER BY version DESC, filename';
        
        $results = $this->getAll($where);
        
        
        // if (!$latestVersionOnly) {
            // $finalResults =& $results;
        // } else {
            // Need to do some processing to get only the latest results
            
            // $finalResults = array();
            
            // foreach ($results as $item)
            // {
                // if (!array_key_exists($item['filename'], $finalResults)) { 
                    // $finalResults[$item['filename']] = $item;
                // } 
            // }
        // }
        
        return ($results);
    }
    
    /**
    * Method to get the total number of files for a user
    *
    * @todo This is not perfect yet. Has to ignore archives files
    * @param string $userId User ID of the User
    * @return int Number of Files
    */
    public function getNumFiles($userId)
    {
        return $this->getRecordCount(' WHERE userid=\''.$userId.'\' AND category != \'temp\'');
    }
    
    /**
    * Method to get the total number of unique files for a user, exclude overwrites
    *
    * @todo This is not perfect yet. Has to ignore archives files
    * @param string $userId User ID of the User
    * @return int Number of Files
    */
    public function getNumUniqueFiles($userId)
    {
        $sql = 'SELECT filename FROM tbl_files WHERE userid=\''.$userId.'\' AND category != \'temp\'';
        $result = $this->getArray($sql);
        return count($result);
    }
    
    /**
    * Method to get the categories of files that have been uploaded
    *
    * @param string $userId User ID of the User
    * @return array List of Categories
    */
    public function getUserCategories($userId)
    {
        $sql = 'SELECT category FROM tbl_files WHERE userid = \''.$userId.'\' AND category != \'temp\' GROUP BY category ORDER BY category';
        
        return $this->getArray($sql);
    }
    
    /**
    * An alternative way to get information about a file by providing the path
    *
    * @param string $path Path of the File
    * @return array Details of the File
    */
    public function getFileDetailsFromPath($path)
    {
        return $this->getRow('path', $path);
    }
    
    /**
    * Method to delete a temporary file
    * THis function deletes the record as well as the file
    * @param string $id Record Id of the Temporary File
    */
    public function deleteTemporaryFile($id)
    {
        $file = $this->getRow('id', $id);
        
        if ($file == FALSE || $file['category'] != 'temp')
        {
            return FALSE;
        } else {
            $filePath = $this->objConfig->getcontentBasePath().$file['path'];
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $thumbnail = $this->objConfig->getcontentBasePath().'/filemanager_thumbnails/'.$file['id'].'.jpg';
            
            if (file_exists($thumbnail)) {
                unlink($thumbnail);
            }
            
            $this->delete('id', $file['id']);
            // Delete Temp Media File Info
            $this->objMediaFileInfo->delete('fileid', $file['id']);
        }
    }
    
    /**
    * Method to delete a temporary file by providing the path to the file
    * THis function deletes the record as well as the file
    * @param string $path Path to File
    */
    public function deleteTemporaryFileInfo($path)
    {
        // Get List of File(s) - Note: User Id is stored in path, hence not required
        $list = $this->getAll(' WHERE path="'.$path.'"');
        
        // Loop through list
        foreach ($list as $item)
        {
            if ($item['category'] == 'temp') { // Security Measure to prevent files from being deleted using this method
                // Delete File Info
                $this->delete('id', $item['id']);
                // Delete Temp Media File Info
                $this->objMediaFileInfo->delete('fileid', $item['id']);
                
                // Todo: Delete Temp Document File Info
                
                // Delete Thumbnail
                $thumbnail = $this->objConfig->getcontentBasePath().'/filemanager_thumbnails/'.$item['id'].'.jpg';
                
                if (file_exists($thumbnail)) {
                    unlink($thumbnail);
                }
            }
        }
        
        return;
    }
    
    /**
    * Method to get a list of temporary files for a user
    * @param string $userId Record Id of the User
    * @return array List of Files
    */
    public function getTemporaryFiles($userId)
    {
        return $this->getAll('WHERE category = \'temp\' AND userid =\''.$userId.'\'');
    }
    
    /**
    * Method to get the latest version of a file
    * It ignores previous versions, as well as temporary files
    * @param string $filename Name of the File
    * @param string $userId User to whom the file belongs
    * @return array details of the file
    */
    public function getOriginalFile($filename, $userId)
    {
        $result = $this->getAll('WHERE filename=\''.$filename.'\' AND category != \'temp\' AND userid =\''.$userId.'\' ORDER BY version DESC');
        if (count($result) > 0) {
            return $result[0];
        } else {
            return FALSE;
        }
    }
    
    /**
    * Method to update the details of a file
    * @param string $fileId Record Id of the File
    * @param int $version Version of the File
    * @param string $path Path to File
    * @param string $category Category of the File
    * @return boolean Result of Update
    */
    public function updateOverwriteDetails($fileId, $version, $path, $category)
    {
        return $this->update('id', $fileId, array(
                'version' => $version,
                'path' => $path,
                'filefolder'=>dirname($path),
                'category' => $category,
                'modifierid' => $this->objUser->userId(),
                'datemodified' => strftime('%Y-%m-%d %H:%M:%S', mktime())
                )
            );
    }
    
    /**
    * Method to get information about a file
    * This function not only gets information about a file,
    * but also looks for details in the metadata tables
    * The results are merged into one.
    * @param string $fileId Record Id of the File
    * @return array Details of the File
    */
    public function getFileInfo($fileId)
    {
        $file = $this->getFile($fileId);
        
        if ($file == FALSE) {
            return FALSE;
        }
        
        $mediaInfo = $this->objMediaFileInfo->getRow('fileId', $fileId);
        
        if ($mediaInfo == FALSE) {
            return $file;
        } else {
            $result = array_merge($file, $mediaInfo);
            $result['id'] = $file['id'];
            return $result;
        } 
    }
    
    /**
    * Method to get information about a file
    * and return the result in table display format
    * @param string $fileId Record Id of the File
    * @return string Information about the file in a table format
    */
    public function getFileInfoTable($fileId)
    {
        $file = $this->getFileInfo($fileId);
        
        if ($file == FALSE) {
            return FALSE;
        }
        
        $objFileSize = new formatfilesize();
        
        $objTable = $this->newObject('htmltable', 'htmlelements');
        
        $objTable->startRow();
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_filetype', 'filemanager', 'File Type').'</strong>', '25%');
        $objTable->addCell(ucwords($file['datatype']), '25%');
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_filesize', 'filemanager', 'File Size').'</strong>', '25%');
        $objTable->addCell($objFileSize->formatsize($file['filesize']), '25%');
        $objTable->endRow();
        
        $objTable->startRow();
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_fileversion', 'filemanager', 'File Version').'</strong>', '25%');
        $objTable->addCell($file['version'], '25%');
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_filecategory', 'filemanager', 'File Category').'</strong>', '25%');
        $objTable->addCell(ucwords($file['category']), '25%');
        $objTable->endRow();
        
        $objTable->startRow();
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_mimetype', 'filemanager', 'Mime Type').'</strong>', '25%');
        $objTable->addCell($file['mimetype'], '25%');
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_dateuploaded', 'filemanager', 'DateUploaded').'</strong>', '25%');
        $objTable->addCell(ucwords($file['datecreated'].' '.$file['timecreated']), '25%');
        $objTable->endRow();
        
        
        return $objTable->show();
    }
    
    /**
    * Method to get media information about a file
    * and return the result in table display format
    * @param string $fileId Record Id of the File
    * @return string Information about the file in a table format
    */
    public function getFileMediaInfoTable($fileId)
    {
        $file = $this->getFileInfo($fileId);
        
        if ($file == FALSE) {
            return FALSE;
        }
        
        if (!array_key_exists('width', $file)) {
            return FALSE;
        }
        
        $objFileSize = new formatfilesize();
        
        $mediaInfoArray = array();
        
        if ($file['width'] != 0) {
            $mediaInfoArray['info_width'] = $file['width'];
        }
        
        if ($file['height'] != 0) {
            $mediaInfoArray['info_height'] = $file['height'];
        }
        
        if ($file['playtime'] != 0) {
            $seconds = $file['playtime'] % 60;
            $minutes = ($file['playtime'] - $seconds) / 60;
            
            if ($minutes > 59) {
                $hour = ($minutes - ($minutes % 60)) / 60;
                $minutes = $minutes % 60;
                $str = $hour.':'.$minutes.':'.$seconds;
            } else {
                $str = $minutes.':'.$seconds;
            }
            $mediaInfoArray['info_playtime'] = $str;
        }
        
        if ($file['framerate'] != 0) {
            $mediaInfoArray['info_framerate'] = $file['framerate'];
        }
        
        if ($file['bitrate'] != 0) {
            $mediaInfoArray['info_bitrate'] = $file['bitrate'];
        }
        
        if ($file['samplerate'] != 0) {
            $mediaInfoArray['info_samplerate'] = $file['samplerate'];
        }
        
        if ($file['title'] != '') {
            $mediaInfoArray['info_title'] = $file['title'];
        }
        
        if ($file['artist'] != '') {
            $mediaInfoArray['info_artist'] = $file['artist'];
        }
        
        if ($file['year'] != '') {
            $mediaInfoArray['info_year'] = $file['year'];
        }
        
        if ($file['url'] != '') {
            $mediaInfoArray['info_url'] = $file['url'];
        }
        
        if (count($mediaInfoArray) < 1) {
            return FALSE;
        } else {
            $objTable = $this->newObject('htmltable', 'htmlelements');
            $objTable->startRow();
            $rowStarted = TRUE;
            
            $count = 0;
            
            foreach ($mediaInfoArray as $item=>$value)
            {
                $objTable->addCell($this->objLanguage->languageText('mod_filemanager_'.$item, 'filemanager'), '25%');
                $objTable->addCell($value, '25%');
                $count++;
                
                if ($count % 2 == 0) {
                    $objTable->endRow();
                    $rowStarted = FALSE;
                    if ((count($mediaInfoArray) - $count) != 0) {
                        $objTable->startRow();
                        $rowStarted = TRUE;
                    }
                }
                
            }
            
            if ($rowStarted) {
                if ($count % 2 == 1) {
                    $objTable->addCell('&nbsp;', '25%');
                    $objTable->addCell('&nbsp;', '25%');
                }
                $objTable->endRow();
            }
            
            return $objTable->show();
        }
        
    }
    
    /**
    * Method to get the versios of a file.
    * @param string $fileId Record Id of the File
    * @return array list of Versions for a file
    */
    public function getFileHistorySQL($fileId)
    {
        $file = $this->getRow('id', $fileId);
        
        if ($file == FALSE) {
            return FALSE;
        }
        return $this->getAll(' WHERE filename=\''.$file['filename'].'\' AND userid=\''.$file['userid'].'\' AND category != \'temp\' ORDER BY version DESC');
    }
    
    /**
    * Method to get information about the version history of a file
    * and return the result in table display format
    * @param string $fileId Record Id of the File
    * @return string Information about the file in a table format
    */
    public function getFileHistory($fileId)
    {
        $historyList = $this->getFileHistorySQL($fileId);
        
        if ($historyList == FALSE) {
            return FALSE;
        }
        
        $objTable = $this->newObject('htmltable', 'htmlelements');
        
        $objTable->startHeaderRow();
        $objTable->addHeaderCell($this->objLanguage->languageText('word_version', 'filemanager', 'Version'), '25%', NULL, 'center');
        $objTable->addHeaderCell($this->objLanguage->languageText('word_size', 'filemanager', 'Size'), '25%', NULL, 'center');
        $objTable->addHeaderCell($this->objLanguage->languageText('phrase_dateuploaded', 'filemanager', 'Date Uploaded'), '25%', NULL, 'center');
        $objTable->addHeaderCell($this->objLanguage->languageText('phrase_timeuploaded', 'filemanager', 'Time Uploaded'), '25%', NULL, 'center');
        $objTable->endHeaderRow();
        
        $objFileSize = new formatfilesize();
        
        foreach ($historyList as $file)
        {
            $objTable->startRow();
            
            $link = new link($this->uri(array('action'=>'fileinfo', 'id'=>$file['id'], 'filename'=>$file['filename'])));
            $link->link = $this->objLanguage->languageText('word_version', 'filemanager', 'Version').' '.$file['version'];
            
            $objTable->addCell($link->show(), '25%', NULL, 'center');
            $objTable->addCell($objFileSize->formatsize($file['filesize']), '25%', NULL, 'center');
            $objTable->addCell($file['datecreated'], '25%', NULL, 'center');
            $objTable->addCell($file['timecreated'], '25%', NULL, 'center');
            $objTable->endRow();
        }
        
        return $objTable->show();
    }
    
    /**
     * Method to delete a file
     *
     * @param string $fileId Record Id of the File
     * @param Boolean $includeArchives Flag on whether to include files extracted from archive if archive
     * @return boolean
     */
    public function deleteFile($fileId, $includeArchives=FALSE)
    {
        $file = $this->getFile($fileId);
        
        if ($file == FALSE) {
            return FALSE;
        }
        
        if ($includeArchives) {
            $otherFiles = $this->getAll('WHERE filename=\''.$file['filename'].'\' AND userid=\''.$file['userid'].'\' AND id != \''.$fileId.'\'');
            
            if (count($otherFiles) > 0) {
                foreach ($otherFiles as $otherfile)
                {
                    $this->removeFile($otherfile['id'], $otherfile['path']);
                }
            }
        }
        
        return $this->removeFile($file['id'], $file['path']);
    }
    
    /**
     * Method to remove a file from the filesystem
     *
     * @param string $fileId Record Id of the File
     * @param string $filePath Path to file
     * @return boolean
     */
    private function removeFile($fileId, $filePath)
    {
        // Get Path to File
        $fullFilePath = $this->objConfig->getcontentBasePath().$filePath;
        $this->objCleanUrl->cleanUpUrl($filePath);
        
        // Delete File if it exists
        if (file_exists($fullFilePath)) {
            unlink($fullFilePath);
        }
        
        // Get thumbnail path
        $thumbnailPath = $this->objConfig->getcontentBasePath().'/filemanager_thumbnails/'.$fileId.'.jpg';
        $this->objCleanUrl->cleanUpUrl($thumbnailPath);
        
        // Delete thumbnail if it exists
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
        
        // Get html preview path
        $htmlPreviewPath = $this->objConfig->getcontentBasePath().'/filemanager_thumbnails/'.$fileId.'.htm';
        $this->objCleanUrl->cleanUpUrl($htmlPreviewPath);
        
        // Delete thumbnail if it exists
        if (file_exists($htmlPreviewPath)) {
            unlink($htmlPreviewPath);
        }
        
        // Delete file record and Metadata
        $this->objMediaFileInfo->delete('fileid', $fileId);
        return $this->delete('id', $fileId);
        
        
    }
    
    /**
    * Method to Change the Mimetype of a File
    * @param string $fileId, Record Id of the File
    * @param string $mimetype New mimetype of the File
    * @return boolean Result of Update
    */
    public function updateMimeType($fileId, $mimetype)
    {
        // First Check that mimetype is valid and not empty
        if ($mimetype != '' && $this->objMimetypes->isValidMimeType($mimetype)) {
            
            // Next Get the filename
            $filename = $this->getFileName($fileId);
            
            // If file exists, continue
            if ($filename != FALSE) {
            
                // Get new category based on new mimetype
                $category = $this->objFileFolder->getFileFolder($filename, $mimetype);
                
                // Update Database
                return $this->update('id', $fileId, array('mimetype'=>$mimetype, 'category'=>$category));
                
            } else { // Return False
                return FALSE;
            }
        } else { // Return False
            return FALSE;
        }
    }
    
    /**
    * Added function to move a file to another category
    * @param string $fileId Record Id of the File
    * @param string $category Name of New Category
    * @return boolean Result of the Move
    */
    public function updateFileCategory($fileId, $category)
    {
        return $this->update('id', $fileId, array('category'=>$category));
    }

    
    /**
    * Method to update the paths of files that do not have the filefolder item set
    * This is due to a patch added
    */
    public function updateFilePath()
    {
        $files = $this->getAll(' WHERE filefolder IS NULL OR filefolder=\'\'');
        
        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->update('id', $file['id'], array('filefolder'=>dirname($file['path'])));
            }
        }
    }
    
    /**
    * Method to get all files in a particular folder
    * @param string $folder folderpath
    * @return array
    */
    public function getFolderFiles($folder)
    {
        return $this->getAll(' WHERE filefolder=\''.$folder.'\'');
    }


}

?>