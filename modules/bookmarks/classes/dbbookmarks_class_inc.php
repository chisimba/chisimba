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
class dbbookmarks extends dbtable
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
        parent::init('tbl_bookmarks_bookmarks');
        $this->table = ('tbl_bookmarks_bookmarks');
    }

    /**
     *
     * Get the text of the init_overview that we have in the sample database.
     *
     * @access public
     * @param string $folderId The if of the folder to get bookmarks for
     * @return string The text of the init_overview
     *
     */
    public function getBookmarks($userId, $folderId = NULL)
    {
        if (empty($folderId))
        {
            return $this->fetchAll("WHERE `folder_id` IS NULL AND `user_id` = '$userId'");                        
        }
        else
        {
            return $this->fetchAll("WHERE `folder_id` = '$folderId' AND `user_id` = '$userId'");            
        }
    }

    /**
     *
     * Save bookmark to the database.
     *
     * @access public
     * @param array $data The bookmark data to be inserted
     * @return string The id of the inserted data
     *
     */
    public function saveBookmark($data)
    {
        return $this->insert($data);
    }
    
    /**
     *
     * Update bookmark.
     *
     * @access public
     * @param string $id The id of the bookmark to update
     * @param array $data The data of the folder
     * @return array The folder id
     *
     */
    public function updateBookmark($id, $data)
    {
        return $this->update('id', $id, $data);
    }

    /**
     * 
     * Get bookmark.
     *
     * @access public
     * @param string $id The id of the bookmark to get
     * @return array The folder data
     *
     */
    public function getBookmark($id)
    {
        return $this->getRow('id', $id);
    }

    /**
     *
     * Delete Bookmark.
     *
     * @access public
     * @param string $id The id of the bookmark to delete
     * @return VOID
     *
     */
    public function deleteBookmark($id)
    {
        return $this->delete('id', $id);
    }

    /**
     *
     * Delete folder Bookmarks.
     *
     * @access public
     * @param string $id The id of the folder the bookmarks are to be deleted from
     * @return VOID
     *
     */
    public function deleteFolderBookmarks($id)
    {
        return $this->delete('folder_id', $id);
    }
    
    /**
     *
     * Method to return whether or not a user has bookmarks
     * 
     * @access public
     * @param string $userId The id of the user
     * @return boolean TRUE if the user has bookmarks | FALSE if not
     */
    public function hasBookmarks($userId)
    {
        $data = $this->fetchAll("WHERE `user_id` = '$userId'");
        if (empty($data))
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}
?>