<?php

/**
 * Class to handle interaction with table tbl_files_filetags
 * 
 * This table relates to story tags and keywords of files
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
 * Class to handle interaction with table tbl_files_filetags
 * 
 * This table relates to story tags and keywords of files
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
class dbfiletags extends dbTable
{
    
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files_filetags');
    }
    
    /**
    * Method to get the list of tags for a file
    *
    * This function does some processing to strip off all other database columns,
    * and only return the list of tags as an array.
    *
    * @param  string $fileId Record Id of the File
    * @return array  List of Tags
    */
    public function getFileTags($fileId)
    {
        // Get List of Tags for the files
        $tags = $this->getAll(' WHERE fileid=\''.$fileId.'\' ORDER BY tag');
        
        // Prepare return array
        $results = array();
        
        // Check that item has tags
        if (count($tags) > 0) {
            // Loop through tags
            foreach ($tags as $tag)
            {
                // Add tag to return array
                $results[] = $tag['tag'];
            }
        }
        
        // Return results
        return $results;
    }
    
    /**
    * Method to add a comma separated string as tags for a file
    * @param string $fileId    Record Id of the File
    * @param string $tagString Comma Separated List of Tags
    */
    public function addFileTags($fileId, $tagString)
    {
        // Delete existing tags
        $this->removeFileTags($fileId);
        
        // Convert string to array
        $tags = explode(',', $tagString);
        
        // Create an array of tags added to prevent duplication
        $alreadyAdded = array();
        
        // Check that user entered tags
        if (count($tags) > 0) {
            // Loop through each tag
            foreach ($tags as $tag)
            {
                // Trim whitespace from front and end
                $tag = trim($tag);
                
                // If tag is not equal to 'nothing' and has not been added,
                if ($tag != '' && !in_array(strtolower($tag), $alreadyAdded)) {
                    // Add tag to file
                    $this->addFileTag($fileId, $tag);
                }
                
                // Add to list of already added tags
                $alreadyAdded[] = strtolower($tag);
            }
        }
    }
    
    /**
    * Method to delete the tags of a file 
    * @param string $fileId Record ID of the File
    */
    public function removeFileTags($fileId)
    {
        return $this->delete('fileid', $fileId);
    }
    
    /**
    * Private Method to store file tag to database
    * @param string $fileId Record Id of the File
    * @param string $tag    Tag
    */
    private function addFileTag($fileId, $tag)
    {
        return $this->insert(array('fileid'=>$fileId, 'tag'=>$tag));
    }
    
    /**
    * Method to get the list of tags, and their weight for generating a tag cloud
    * for files of a user
    *
    * @param  string $user User Id
    * @return array 
    */
    public function getTagCloudResults($user)
    {
        $sql = 'SELECT tag, count(tag) AS weight FROM tbl_files_filetags INNER JOIN tbl_files ON ( tbl_files.id = tbl_files_filetags.fileid AND tbl_files.userid = \''.$user.'\' ) GROUP BY tag ORDER BY tag';
        return $this->getArray($sql);
    }
    
    /**
    * Method to get the list of files of a user that has a particular tag
    *
    * @param  string $user User Id
    * @param  string $tag  Tag file should have
    * @return array 
    */
    public function getFilesWithTag($user, $tag)
    {
        $sql = 'SELECT DISTINCT tbl_files.id, tbl_files.* FROM tbl_files INNER JOIN tbl_files_filetags ON (tbl_files.id = tbl_files_filetags.fileid AND tbl_files_filetags.tag=\''.$tag.'\' AND tbl_files.userid = \''.$user.'\') ORDER BY filename';
        
        return $this->getArray($sql);
    }

}

?>