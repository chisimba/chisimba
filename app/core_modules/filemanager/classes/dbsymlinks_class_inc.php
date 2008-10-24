<?php

/**
 * Class to handle symlinks
 * 
 * This handles quota functionality in the file manager, setting custom quotas,
 * how much left users can upload, etc.
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
 * @version   $Id: dbfiletags_class_inc.php 2804 2007-08-03 06:33:05Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @see       
 */


/**
 * Class to handle file quotas
 * 
 * This handles quota functionality in the file manager, setting custom quotas,
 * how much left users can upload, etc.
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
class dbsymlinks extends dbTable
{
    
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files_symlinks');
        $this->objFiles = $this->getObject('dbfile');
        $this->objUser = $this->getObject('user', 'security');
    }
    
    
    public function addSymlink($file, $folder)
    {
        return $this->insert(
                array(
                    'fileid' => $file,
                    'folderid' => $folder,
                    'creatorid' => $this->objUser->userId(),
                    'datemodified' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                    'creatorid' => $this->objUser->userId(),
                    'datemodified' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                )
            );
    }
    
    public function removeSymlink($id)
    {
        return $this->delete('id', $id);
    }
    
    public function deleteSymlinkFile($fileId)
    {
        return $this->delete('fileid', $fileId);
    }
    
    public function deleteSymlinksInFolder($folderId)
    {
        return $this->delete('folderid', $folderId);
    }
    
    public function getFolderSymlinks($folder)
    {
        $sql = "SELECT tbl_files_symlinks.id AS symlinkid, tbl_files.* FROM tbl_files_symlinks
        JOIN tbl_files ON tbl_files_symlinks.fileid = tbl_files.id
        WHERE tbl_files_symlinks.folderid='{$folder}' ORDER BY filename";
        
        return $this->getArray($sql);
    }
    
    public function getSymlink($id)
    {
        return $this->getRow('id', $id);
    }
    
    public function getFullSymlink($id)
    {
        $sql = "SELECT tbl_files_symlinks.id AS symlinkid, tbl_files.* FROM tbl_files_symlinks
        JOIN tbl_files ON tbl_files_symlinks.fileid = tbl_files.id
        WHERE tbl_files_symlinks.id='{$id}' LIMIT 1";
        
        $results = $this->getArray($sql);
        
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }
}

?>