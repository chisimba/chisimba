<?php

/**
 * Class to handle interaction with table tbl_files_metadata_media
 * 
 * This table relates to metadata about media files such as images, audio, video and flash
 * 
 * PHP version 3
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
 * Class to handle interaction with table tbl_files_metadata_media
 * 
 * This table relates to metadata about media files such as images, audio, video and flash
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
class dbmediafileinfo extends dbTable
{
    
    /**
    * Constructor
    */
    function init()
    {
        parent::init('tbl_files_metadata_media');
        $this->objUser = $this->getObject('user', 'security');
    }
    
    /**
    * Method to add metadata info about a file
    * @param  string $fileId    Record Id of the File
    * @param  array  $infoArray Array with details of the metadata
    * @return string Record Id of the Entry
    */
    function addMediaFileInfo($fileId, $infoArray)
    {
        // Add File Id to Array
        $infoArray['fileid'] = $fileId;
        $infoArray['creatorid'] = $this->objUser->userId();
        $infoArray['modifierid'] = $this->objUser->userId();
        $infoArray['datecreated'] = strftime('%Y-%m-%d', mktime());
        $infoArray['timecreated'] = strftime('%H:%M:%S', mktime());
        
        return $this->insert($infoArray);
    }
    
    /**
    * Method to clean up records that have no matching data in tbl_files
    */
    function cleanUpMismatchedMediaFiles()
    {
        $sql = 'SELECT tbl_files_metadata_media.id, tbl_files.id as files_id FROM tbl_files_metadata_media  LEFT JOIN tbl_files ON (tbl_files_metadata_media.fileid = tbl_files.id) WHERE tbl_files.id IS NULL';
        
        $results = $this->getArray($sql);
        
        foreach ($results as $result)
        {
            $this->delete('id', $result['id']);
        }
    }
    
    /**
    * Method to update the width and height info of a file
    * @param string $fileId Record Id of the File
    * @param int $width Width of the File
    * @param int $height Height of the File
    */
    function updateWidthHeight($fileId, $width, $height)
    {
        return $this->update('fileid', $fileId, array('width'=>$width, 'height'=>$height));
    }


}

?>