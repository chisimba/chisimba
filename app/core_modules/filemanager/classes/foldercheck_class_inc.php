<?php

/**
 * Check whether User Folder for Upload Exists
 * 
 * This class checks whether a user folder exists for file uploads
 * It also creates a number of subfolders to store files in 
 * dependent on file category.
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
 * Check whether User Folder for Upload Exists
 * 
 * This class checks whether a user folder exists for file uploads
 * It also creates a number of subfolders to store files in 
 * dependent on file category.
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
class foldercheck extends object
{
    /**
    * @var array $subFolders List of Possible Subfolders for storing files
    */
    private $subFolders;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objMkdir = $this->getObject('mkdir', 'files');
        
        // List of Subfolders to be created in Path
        // $this->subFolders = array('images', 'audio', 'video', 'documents', 'flash', 'freemind', 'archives', 'temp', 'other', 'obj3d', 'scripts');
    }
    
    /**
    * Method to check that the user folder for uploads, and subfolders exist
    * @param string $userId UserId of the User
    */
    public function checkUserFolder($userId)
    {
        if (trim($userId) == '') {
            return FALSE;
        } else {
            // Set Up Path
            $path = $this->objConfig->getcontentBasePath().'/users/'.$userId;
            $result = $this->objMkdir->mkdirs($path, 0777);
            
            return $result;
        }
    }
    
    /**
    * Method to check that the context folder for uploads, and subfolders exist
    * @param string $contextCode UContext Code
    */
    public function checkContextFolder($contextCode)
    {
        if (trim($contextCode) == '') {
            return FALSE;
        } else {
            // Set Up Path
            $path = $this->objConfig->getcontentBasePath().'/context/'.$contextCode;
            $result = $this->objMkdir->mkdirs($path, 0777);
            
            return $result;
        }
    }
    
    /**
    * Method to check whether a version subfolder exists
    * This will usually be something like images/1, etc.
    *
    * @param string $userId  UserId of the User
    * @param string $folder  Name of the Folder
    * @param int    $version Version of the Subfolder
    */
    public function checkUserFolderVersionExists($userId, $folder, $version)
    {
        // Take global array and inverse
        $subFolders = array_flip($this->subFolders);
        unset($subFolders['temp']); // Remove Temp
        $subFolders = array_flip($subFolders); // Inverse back
        
        if (in_array($folder, $subFolders)) {
            // Set Up Path
            $path = $this->objConfig->getcontentBasePath().'/users/'.$userId.'/'.$folder.'/'.$version;
            
            return $this->objMkdir->mkdirs($path);
        } else {
            return FALSE;
        }
    }
    
    
    

}

?>