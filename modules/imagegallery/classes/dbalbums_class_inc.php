<?php
/**
 *
 * Database access for imagegallery
 *
 * Database access for imagegallery. This is a sample database model class
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
 * @package   imagegallery
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
* Database access for imagegallery
*
* Database access for imagegallery. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   imagegallery
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dbalbums extends dbtable
{
    /**
     * 
     * The name of the table
     *
     * @access public
     * @var string
     */
    public $table;
    
    /**
    *
    * Intialiser for the imagegallery database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();

        $this->objDBimages = $this->getObject('dbimages', 'imagegallery');

        parent::init('tbl_imagegallery_albums');
        $this->table = 'tbl_imagegallery_albums';
    }

    /**
     *
     * Method to get all albums
     * 
     * @access public
     * @return array The array of albums
     */
    public function getAllAlbums()
    {
        return $this->fetchAll();
    }
    
    /**
     *
     * Method to get all gallery albums
     * 
     * @access public
     * @param string $galleryId The id of the gallery to get albums for
     * @return array The array of gallery albums
     */
    public function getGalleryAlbums($galleryId)
    {
        return $this->fetchAll("WHERE `gallery_id` = '$galleryId' ORDER BY display_order ASC");
    }
    
    /**
     *
     * Method to get all user albums
     * 
     * @access public
     * @param string $userId The id of the user to get albums for
     * @return array The array of user albums
     */
    public function getUserAlbums($userId)
    {
        return $this->fetchAll("WHERE `user_id` = '$userId' ORDER BY display_order ASC");
    }
    
    /**
     *
     * Method to get all context albums
     * 
     * @access public
     * @param string $contextCode The code of the context to get albums for
     * @return array The array of context albums
     */
    public function getContextAlbums($contextCode)
    {
        return $this->fetchAll("WHERE `context_code` = '$contextCode' ORDER BY display_order ASC");
    }

    /**
     *
     * Method to get an album
     * 
     * @access public
     * @param string $id The id of the album to get
     * @return array The array of album data
     */
    public function getAlbum($id)
    {
        return $this->getRow('id', $id);
    }
    
    /**
     *
     * Method to add an album
     * 
     * @access public
     * @param array $fields The array of fields to add to the album table
     * @return string The id of the record created 
     */
    public function addAlbum($fields)
    {
        $albums = $this->getGalleryAlbums($fields['gallery_id']);
        $count = count($albums);
        
        $fields['display_order'] = ++$count;
        $fields['created_by'] = $this->userId;
        $fields['date_created'] = date('Y-m-d H:i:s');

        return $this->insert($fields);
    }
    
    /**
     *
     * Method to update an album
     * 
     * @access public
     * @param string $id The id of the album to update
     * @param array $fields The array of fields to update on the album table
     * @return boolesn TRUE if the update was successfull | FALSE if not 
     */
    public function updateAlbum($id, $fields)
    {
        $fields['updated_by'] = $this->userId;
        $fields['date_updated'] = date('Y-m-d H:i:s');

        return $this->update('id', $id, $fields);
    }
    
    /**
     *
     * Method to delete an album
     * 
     * @access public
     * @param string $id The id of the album to delete
     * @return boolean $result TRUE if the delete was successfull | FALSE if not 
     */
    public function deleteAlbum($id)
    {
        $album = $this->getAlbum($id);
        
        $result = $this->delete('id', $id);
        $this->objDBimages->deleteAlbumImages($id);
        
        $albums = $this->getGalleryAlbums($album['gallery_id']);

        if (!empty($albums))
        {
            $i = 0;
            foreach ($albums as $album)
            {
                $this->update('id', $album['id'], array('display_order' => ++$i));
            }
        }
        
        return $result;
    }
    
    /**
     *
     * Method to delete gallery albums
     * 
     * @access public
     * @param string $galleryId The id of the gallery to delete albums from
     * @return boolean TRUE id the delete was successfull | FALSE if not 
     */
    public function deleteGalleryAlbums($galleryId)
    {
        return $this->delete('gallery_id', $galleryId);
    }
}
?>