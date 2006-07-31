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
        $this->objMediaFileInfo =& $this->getObject('dbmediafileinfo');
        $this->objUserFolder =& $this->getObject('userfoldercheck');
        
        $this->objLanguage =& $this->getObject('language', 'language');
        
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('formatfilesize', 'files');
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
                'description' => $description,
                'version' => $version,
                'filesize' => $filesize,
                'mimetype' => $mimetype,
                'category' => $category,
                'license' => $license,
                'moduleuploaded' => $this->getParam('module'),
                'creatorid' => $this->objUser->userId(),
                'datecreated' => strftime('%Y-%m-%d', mktime()),
                'timecreated' => strftime('%H:%M:%S', mktime())
                )
            );
    }
    
    /**
    * Method to get All the Files of a User
    * @param string $userId User Id of the User
    * @param string $category Optional Category Filter
    * @return array List of Files
    */
    public function getUserFiles($userId, $category=NULL)
    {
        $where = ' WHERE userid="'.$userId.'"';
        
        
        if ($category != NULL) {
            $where .= ' AND category="'.$category.'"';
        }
        
        $where .= ' ORDER BY version DESC, filename';
        
        $results = $this->getAll($where);
        
        // Need to do some processing to get only the latest results
        
        $finalResults = array();
        
        foreach ($results as $item)
        {
            if (!array_key_exists($item['filename'], $finalResults)) { 
                $finalResults[$item['filename']] = $item;
            } 
        }
        
        return ($finalResults);
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
        return $this->getRecordCount(' WHERE userid="'.$userId.'" AND category != "temp"');
    }
    
    /**
    * Method to get the categories of files that have been uploaded
    *
    * @param string $userId User ID of the User
    * @return array List of Categories
    */
    public function getUserCategories($userId)
    {
        $sql = 'SELECT category FROM tbl_files WHERE userid = "'.$userId.'" AND category != "temp" GROUP BY category ORDER BY category';
        
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
        return $this->getAll('WHERE category = "temp" AND userid ="'.$userId.'"');
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
        $result = $this->getAll('WHERE filename="'.$filename.'" AND category != "temp" AND userid ="'.$userId.'" ORDER BY version DESC');
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
        $file = $this->getRow('id', $fileId);
        
        if ($file == FALSE) {
            return FALSE;
        }
        
        $mediaInfo = $this->objMediaFileInfo->getRow('fileId', $fileId);
        
        if ($mediaInfo == FALSE) {
            $this->currentFile = $file;
            return $file;
        } else {
            $result = array_merge($file, $mediaInfo);
            $result['id'] = $file['id'];
            $this->currentFile = $result;
            return $result;
        }
    }
    
    /**
    * Method to get information about a file
    * and return the result in table display format
    * @param string $fileId Record Id of the File
    * @return string Information about the file in a table format
    */
    function getFileInfoTable($fileId)
    {
        if (is_array($this->currentFile) && $this->currentFile['id'] == $fileId) {
            $file = $this->currentFile;
        } else {
            $file = $this->getFileInfo($fileId);
        }
        
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
    function getFileMediaInfoTable($fileId)
    {
        if (is_array($this->currentFile) && $this->currentFile['id'] == $fileId) {
            $file = $this->currentFile;
        } else {
            $file = $this->getFileInfo($fileId);
        }
        
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
            $mediaInfoArray['info_playtime'] = $file['playtime'];
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
            
            $count = 0;
            
            foreach ($mediaInfoArray as $item=>$value)
            {
                $objTable->addCell($this->objLanguage->languageText('mod_filemanager_'.$item, 'filemanager'), '25%');
                $objTable->addCell($value, '25%');
                $count++;
                
                if ($count % 2 == 0) {
                    $objTable->endRow();
                    if ((count($mediaInfoArray) - $count) != 0) {
                        $objTable->startRow();
                    }
                }
                
                
            }
            
            return $objTable->show();
        }
        
    }
    
    /**
    * Method to get the versios of a file.
    * @param string $fileId Record Id of the File
    * @return array list of Versions for a file
    */
    function getFileHistorySQL($fileId)
    {
        $file = $this->getRow('id', $fileId);
        
        if ($file == FALSE) {
            return FALSE;
        }
        return $this->getAll(' WHERE filename="'.$file['filename'].'" AND userid="'.$file['userid'].'" AND category != "temp" ORDER BY version DESC');
    }
    
    /**
    * Method to get information about the version history of a file
    * and return the result in table display format
    * @param string $fileId Record Id of the File
    * @return string Information about the file in a table format
    */
    function getFileHistory($fileId)
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
    
    
    


}

?>