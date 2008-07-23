<?php

/**
 * Class to handle file quotas
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
 * @version   CVS: $Id: dbfiletags_class_inc.php 2804 2007-08-03 06:33:05Z paulscott $
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
class dbquotas extends dbTable
{
    
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files_quotas');
        $this->objFiles = $this->getObject('dbfile');
        $this->objUser = $this->getObject('user', 'security');
    }
    
    
    public function getQuota($path)
    {
        $path = $this->checkPath($path);
        
        // Reject quota request if path is not valid
        if ($path == FALSE) {
            return FALSE;
        }
        
        $quota = $this->getRow('path', $path);
        
        if ($quota == FALSE) {
            $this->setupQuotaFirstRun($path);
        }
        
        return $quota;
    }
    
    private function setupQuotaFirstRun($path)
    {
        var_dump($path);
        
        $usage = $this->getFileUsage($path);
    }
    
    private function addQuota($path, $usage)
    {
        return $this->insert(
                array(
                    'path' => $path,
                    'usedefault' => 'Y',
                    'quota' => 0,
                    'quotausage' => $usage,
                    'creatorid' => $this->objUser->userId(),
                    'datemodified' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                    'creatorid' => $this->objUser->userId(),
                    'datemodified' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                ));
    }
    
    private function getFileUsage($path)
    {
        $folderParts = explode('/', $path);
        
        $fileSize = 0;
        
        $fileList = $this->objFiles->getPathFiles($folderParts[0], $folderParts[1]);
        
        if (count($fileList) > 0) {
            foreach ($fileList as $file)
            {
                $fileSize += $file['filesize'];
            }
        }
        return $fileSize;
    }
    
    /**
     * Method to check path for quota
     * Quotas is based on the first two parts. Eg. users/1, contexts/bio101
     *
     * If path is longer, return the first two. If less than 1, return FALSE
     * as path is not correct
     */
    private function checkPath($path)
    {
        $folderParts = explode('/', $path);
        
        if (count($folderParts) == 2) {
            return $path;
        } else if ($folderParts == 1) {
            return FALSE;
        } else {
            return $folderParts[0].'/'.$folderParts[1];
        }
    }

}

?>