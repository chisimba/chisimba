<?php

/**
 * Class to handle interaction with table tbl_files
 *
 * This table lists all files that were uploaded to the system
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
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see
 */


/**
 * Class to handle interaction with table tbl_files
 *
 * This table lists all files that were uploaded to the system
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
class dbfile extends dbTable
{

    /**
    * @var    array   $currentFile Record of Current File working on
    * @access private
    */
    private $currentFile;

    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files');
        $this->objUser = $this->getObject('user', 'security');

        $this->objFileParts = $this->getObject('fileparts', 'files');

        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objCleanUrl = $this->getObject('cleanurl');

        $this->objMediaFileInfo = $this->getObject('dbmediafileinfo');
        $this->objFileFolder = $this->getObject('filefolder');
        $this->objMimetypes = $this->getObject('mimetypes', 'files');

        $this->objLanguage = $this->getObject('language', 'language');

        $this->loadClass('link', 'htmlelements');
        $this->loadClass('formatfilesize', 'files');
    }

    /**
    * Method to get the record containing file details
    * @access Public
    * @param  string $fileId
    * @return array  Details of the File
    */
    public function getFile($fileId)
    {
        // The current file is stored in a private variable to prevent possible unnecessary db queries
        if ($this->currentFile != '' && $this->currentFile['id'] == $fileId) {
            return $this->currentFile;
        } else {
            $file = $this->getRow('id', $fileId);

            if ($file != FALSE) {
                $this->currentFile = $file;
            }

            return $file;
        }

    }

    /**
    * Method to get a single piece of information from a file
    * @access private
    * @param  string  $part   Piece to get
    * @param  string  $fileId Record Id of the File
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
    * @param  string $fileId Record Id of the File
    * @return string File Name
    */
    public function getFileName($fileId)
    {
        return $this->getPart('filename', $fileId);
    }

    /**
    * Method to get the size of a file
    * @access public
    * @param  string $fileId Record Id of the File
    * @return int    File Size
    */
    public function getFileSize($fileId)
    {
        return $this->getPart('filesize', $fileId);
    }

    /**
    * Method to get the version number of a file
    * @access public
    * @param  string $fileId Record Id of the File
    * @return int    File Version
    */
    public function getFileVersion($fileId)
    {
        return $this->getPart('version', $fileId);
    }

    /**
    * Method to get the mimetype of a file
    * @access public
    * @param  string $fileId Record Id of the File
    * @return string Mime Type
    */
    public function getFileMimetype($fileId)
    {
        return $this->getPart('mimetype', $fileId);
    }

    /**
    * Method to get the local path to a file
    * @access public
    * @param  string $fileId Record Id of the File
    * @return string Local Path to File
    */
    public function getFilePath($fileId)
    {
        $path = $this->objConfig->getcontentPath().$this->getPart('path', $fileId);

        $path = $this->objCleanUrl->cleanUpUrl($path);

        return $path;
    }

    /**
    * Method to get the absolute path to a file
    * @access public
    * @param  string $fileId Record Id of the File
    * @return string Path to File
    */
    public function getFullFilePath($fileId)
    {
        $path = $this->objConfig->getcontentBasePath().$this->getPart('path', $fileId);

        $path = $this->objCleanUrl->cleanUpUrl($path);

        return $path;
    }

    /**
    * Method to add a file
    * @param  string $filename    Name of the File
    * @param  string $path        Path of the File
    * @param  int    $filesize    Size of the File
    * @param  string $mimetype    Mimetype of the File
    * @param  string $category    Subfolder/Category file is stored in
    * @param  string $version     Version of the file
    * @param  string $userId      User to whom the file belongs to
    * @param  string $description Description of the file
    * @param  string $license     License of the file
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
        
        $file = array(
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
                'creatorid' => $userId,
                'modifierid' => $userId,
                'datecreated' => strftime('%Y-%m-%d', mktime()),
                'timecreated' => strftime('%H:%M:%S', mktime())
                );
        $id =  $this->insert($file);
        
        if ($id != FALSE) {
            $file['id'] = $id;
            $this->indexFile($file);
        }
        
        return $id;
    }
    
    /**
     * Method to add a file to the search index
     * @param array $file Array with File Details
     */
    private function indexFile($file)
    {
        $docId = 'filemanager_file_'.$file['id'];
        $docDate = $file['datecreated'];
        $url = $this->uri(array('action'=>'fileinfo', 'id'=>$file['id']), 'filemanager');
        $title = $file['filename'];
        $contents = $file['description'];
        $teaser = $file['description'];
        $module = 'filemanager';
        $userId = $file['creatorid'];
        
        $tags = NULL; // fix up
        
        $license = $file['license'];
        
        $folder = explode('/', $file['filefolder']);
        
        switch ($folder[0])
        {
            case 'context':
                // check
                $context=$folder[1];
                $workgroup='noworkgroup';
                $permissions='contextonly';
                break;
            case 'users':
            default:
                $context='nocontext';
                $workgroup='noworkgroup';
                $permissions='useronly';
                break;
        }
        
        
        $dateAvailable=NULL;
        $dateUnavailable=NULL;
        
        $extra= array('basefolder'=>$folder[0].'/'.$folder[1]);
        
        $objLucene = $this->getObject('indexdata', 'search');
        $objLucene->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, $tags, $license, $context, $workgroup, $permissions, $dateAvailable, $dateUnavailable, $extra);
    }
    
    public function updateFileSearch()
    {
        $files = $this->getAll();
        
        if (count($files) > 0) {
            foreach ($files as $file)
            {
                $this->indexFile($file);
            }
        }
    }

    /**
    * Method to get All the Files of a User
    * @param  string  $userId            User Id of the User
    * @param  string  $category          Optional Category Filter
    * @param  array   $restrictfiletype  Optional File Type Restriction
    * @param  boolean $latestVersionOnly List Latest Version Only or All Version
    * @return array   List of Files
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


        return ($results);
    }

    /**
    * Method to get the total number of files for a user
    *
    * @todo   This is not perfect yet. Has to ignore archives files
    * @param  string $userId User ID of the User
    * @return int    Number of Files
    */
    public function getNumFiles($userId)
    {
        return $this->getRecordCount(' WHERE userid=\''.$userId.'\' AND category != \'temp\'');
    }

    /**
    * Method to get the total number of unique files for a user, exclude overwrites
    *
    * @todo   This is not perfect yet. Has to ignore archives files
    * @param  string $userId User ID of the User
    * @return int    Number of Files
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
    * @param  string $userId User ID of the User
    * @return array  List of Categories
    */
    public function getUserCategories($userId)
    {
        $sql = 'SELECT category FROM tbl_files WHERE userid = \''.$userId.'\' AND category != \'temp\' GROUP BY category ORDER BY category';

        return $this->getArray($sql);
    }

    /**
    * An alternative way to get information about a file by providing the path
    *
    * @param  string $path Path of the File
    * @return array  Details of the File
    */
    public function getFileDetailsFromPath($path)
    {
        return $this->getRow('path', $path);
    }

    /**
    * Method to delete a temporary file
    * @param string $id Record Id of the Original File
    */
    public function deleteTemporaryFile($id)
    {
        $tempFilePath = $this->objConfig->getcontentBasePath().'/filemanager_tempfiles/'.$id;

        if (file_exists($tempFilePath)) {
            unlink($tempFilePath);
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
    * @param  string $userId Record Id of the User
    * @return array  List of Files
    */
    public function getTemporaryFiles($userId)
    {
        return $this->getAll('WHERE category = \'temp\' AND userid =\''.$userId.'\'');
    }

    /**
    * Method to get the latest version of a file
    * It ignores previous versions, as well as temporary files
    * @param  string $filename Name of the File
    * @param  string $userId   User to whom the file belongs
    * @return array  details of the file
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
    * @param  string  $fileId   Record Id of the File
    * @param  int     $version  Version of the File
    * @param  string  $path     Path to File
    * @param  string  $category Category of the File
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
    * @param  string $fileId Record Id of the File
    * @return array  Details of the File
    */
    public function getFileInfo($fileId)
    {
        $file = $this->getFile($fileId);

        if ($file == FALSE) {
            return FALSE;
        }

        $description = $file['description'];

        $mediaInfo = $this->objMediaFileInfo->getRow('fileId', $fileId);

        if ($mediaInfo == FALSE) {
            $file['filedescription'] = $description;
            return $file;
        } else {
            $result = array_merge($file, $mediaInfo);
            $result['id'] = $file['id'];
            $result['filedescription'] = $description;
            return $result;
        }
    }

    /**
    * Method to get information about a file
    * and return the result in table display format
    * @param  string $fileId Record Id of the File
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
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_filetype', 'system', 'File Type').'</strong>', '25%');
        $objTable->addCell(ucwords($file['datatype']), '25%');
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_filesize', 'system', 'File Size').'</strong>', '25%');
        $objTable->addCell($objFileSize->formatsize($file['filesize']), '25%');
        $objTable->endRow();

        $objTable->startRow();
        // $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_fileversion', 'filemanager', 'File Version').'</strong>', '25%');
        // $objTable->addCell($file['version'], '25%');

        $objTable->addCell('<strong>License:</strong>', '25%');

        $licenseDisplay = $this->getObject('displaylicense', 'creativecommons');
        $objTable->addCell($licenseDisplay->show($file['license']), '25%');

        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_filecategory', 'system', 'File Category').'</strong>', '25%');
        $objTable->addCell(ucwords($file['category']), '25%');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_mimetype', 'system', 'Mime Type').'</strong>', '25%');
        $objTable->addCell($file['mimetype'].'&nbsp;', '25%');
        $objTable->addCell('<strong>'.$this->objLanguage->languageText('phrase_dateuploaded', 'system', 'Date Uploaded').'</strong>', '25%');
        $objTable->addCell(ucwords($file['datecreated'].' '.$file['timecreated']), '25%');
        $objTable->endRow();


        return $objTable->show();
    }

    /**
    * Method to get media information about a file
    * and return the result in table display format
    * @param  string $fileId Record Id of the File
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
    * @param  string $fileId Record Id of the File
    * @return array  list of Versions for a file
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
    * @param  string $fileId Record Id of the File
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
     * @param  string  $fileId          Record Id of the File
     * @param  Boolean $includeArchives Flag on whether to include files extracted from archive if archive
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
     * @param  string  $fileId   Record Id of the File
     * @param  string  $filePath Path to file
     * @return boolean
     */
    private function removeFile($fileId, $filePath)
    {
        // Get Path to File
        $fullFilePath = $this->objConfig->getcontentPath().$filePath;
        $filePath = $this->objCleanUrl->cleanUpUrl($filePath);
        $fullFilePath = $this->objCleanUrl->cleanUpUrl($fullFilePath);

        // Delete File if it exists
        if (file_exists($fullFilePath)) {
            unlink($fullFilePath);
        }
        
        $availablePreviews = array('jpg', 'htm', 'pdf', 'swf', 'mp3', 'flv', 'txt');
        
        foreach ($availablePreviews as $format)
        {
            // Get thumbnail path
            $thumbnailPath = $this->objConfig->getcontentBasePath().'/filemanager_thumbnails/'.$fileId.'.'.$format;
            $thumbnailPath = $this->objCleanUrl->cleanUpUrl($thumbnailPath);
    
            // Delete thumbnail if it exists
            if (file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
            }
        }
        
        $objFileTags = $this->getObject('dbfiletags');


        // Delete file record and Metadata
        $this->objMediaFileInfo->delete('fileid', $fileId);
        $result = $this->delete('id', $fileId);
        
        if ($result) {
            $objLucene = $this->getObject('indexdata', 'search');
            $objLucene->removeIndex('filemanager_file_'.$fileId);
        }
        
        return $result;
    }

    /**
    * Method to Change the Mimetype of a File
    * @param  string  $fileId,  Record Id of the File
    * @param  string  $mimetype New mimetype of the File
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
    * @param  string  $fileId   Record Id of the File
    * @param  string  $category Name of New Category
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
    * @param  string $folder folderpath
    * @return array
    */
    public function getFolderFiles($folder)
    {
        return $this->getAll(' WHERE filefolder=\''.$folder.'\' ORDER BY filename');
    }

    /**
    * Method to update the license and description of a file
    * @param string $id Record Id of the File
    * @param string $description Description of the File
    * @param string $license License of the File
    */
    public function updateDescriptionLicense($id, $description, $license)
    {
        $result = $this->update('id', $id, array('description'=>$description, 'license'=>$license));
        
        if ($result) {
            $file = $this->getFile($id);
             $this->indexFile($file);
        }
        
        return $result;
    }

    /**
    * Method to get the list of Open Files for a Site
    *
    * At the moment, it treats open files as those that are not copyrighted.
    * @param string/array $category List of Category(s) files should come from. Either array or string
    * @param string/array $fileTypes List of Filetype(s) files should come from. Either array or string
    * @return array;
    */
    public function getAllOpenFiles($category=NULL, $fileTypes=NULL)
    {
        $categoryWhere = '';

        if ($category != NULL) {
            if (is_array($category)) {
                $categoryWhere = ' AND ( ';
                $divider = '';

                foreach ($category as $item)
                {
                    $categoryWhere .= $divider.' category=\''.$item.'\' ';
                    $divider = ' OR ';
                }

                $categoryWhere .= ' ) ';
            } else {
                $categoryWhere = ' AND category=\''.$category.'\'';
            }
        }

        $fileTypesWhere = '';

        if ($fileTypes != NULL) {
            if (is_array($fileTypes)) {
                $fileTypesWhere = ' AND ( ';
                $divider = '';

                foreach ($fileTypes as $item)
                {
                    $fileTypesWhere .= $divider.' datatype=\''.$item.'\' ';
                    $divider = ' OR ';
                }

                $fileTypesWhere .= ' ) ';
            } else {
                $fileTypesWhere = ' AND datatype=\''.$category.'\'';
            }
        }

        return $this->getAll('WHERE license != \'copyright\' '.$categoryWhere.' '.$fileTypesWhere);
    }

    /**
    * Method to get all the open site images
    * @return array
    */
    public function getAllOpenImages()
    {
        return $this->getAllOpenFiles('images');
    }

    /**
    * Method to get all the open site images that can display in a web browser
    * @return array
    */
    public function getAllOpenWebImages()
    {
        return $this->getAllOpenFiles('images', array('gif', 'jpg', 'jpeg', 'png'));
    }
    
    public function getPathFiles($type, $id)
    {
        $sql = " WHERE path LIKE '{$type}/{$id}/%' ";
        return $this->getAll($sql);
    }

}

?>