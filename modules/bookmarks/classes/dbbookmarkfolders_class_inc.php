<?php
/**
 *
 * Database access for bookmarks
 *
 * Database access for bookmarks. This is a sample database model class
 * that you will need to edit in order for it to work.
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
 * @package   bookmarks
 * @author    Kevin Cyster kcyster@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Database access for bookmarks
*
* Database access for bookmarks. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   bookmarks
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dbbookmarkfolders extends dbtable
{

    /**
    *
    * Intialiser for the bookmarks database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_bookmarks_folders');
        $this->table = ('tbl_bookmarks_folders');
        $this->parents = array();
        $this->children = array();
    }

    /**
     *
     * Get the root folders.
     *
     * @access public
     * @return array The root folders
     *
     */
    public function getRootFolders()
    {
        return $this->fetchAll("WHERE `parent_id` IS NULL");
    }

    /**
     *
     * Get the sub folders.
     *
     * @access public
     * @param string $parentId The id of the parent folder
     * @return array The root folders
     *
     */
    public function getSubFolders($parentId)
    {
        return $this->fetchAll("WHERE `parent_id` = '$parentId'");
    }

    /**
     *
     * Save folder to the database.
     *
     * @access public
     * @param array $data The folder data to be inserted
     * @return string The id of the inserted data
     *
     */
    public function saveFolder($data)
    {
        return $this->insert($data);
    }

    /**
     * 
     * Get folder.
     *
     * @access public
     * @param string $id The id of the folder to get
     * @return array The folder data
     *
     */
    public function getFolder($id)
    {
        return $this->getRow('id', $id);
    }

    /**
     *
     * Save Folder.
     *
     * @access public
     * @param string $id The id of the folder to update
     * @param array $data The data of the folder
     * @return array The folder id
     *
     */
    public function updateFolder($id, $data)
    {
        return $this->update('id', $id, $data);
    }

    /**
     *
     * Delete Folder.
     *
     * @access public
     * @param string $id The id of the folder to delete
     * @return VOID
     *
     */
    public function deleteFolder($id)
    {
        return $this->delete('id', $id);
    }

    /**
     *
     * Delete Sub Folder.
     *
     * @access public
     * @param string $id The id of the parent folder to delete sub-folders
     * @param array $data The data of the folder
     * @return VOID
     *
     */
    public function deleteSubFolders($parentId)
    {
        return $this->delete('parent_id', $parentId);
    }
    
    /**
     *
     * Method to get all folders
     * 
     * @access public
     * @param string $userId The id of the user to get folders for
     * @return array The array of folders 
     */
    public function getFolders($userId, $id = NULL)
    {
        if (empty($id))
        {
            return $this->fetchAll("WHERE `user_id` = '$userId'");
        }
        else
        {
            return $this->fetchAll("WHERE `user_id` = '$userId' AND `id` != '$id'");
        }
    }
}
?>