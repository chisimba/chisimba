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
class dbimages extends dbtable
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
        $this->objFileMan = $this->getObject('dbfile', 'filemanager');        
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();

        parent::init('tbl_imagegallery_images');
        $this->table = 'tbl_imagegallery_images';
    }

    /**
     *
     * Method to get all images
     * 
     * @access public
     * @return array The array of images
     */
    public function getAllImages()
    {
        return $this->fetchAll();
    }
    
    /**
     *
     * Method to get all gallery images
     * 
     * @access public
     * @param string $galleryId The id of the gallery to get images for
     * @return array The array of gallery images
     */
    public function getGalleryImages($galleryId)
    {
        return $this->fetchAll("WHERE `gallery_id` = '$galleryId' ORDER BY display_order ASC");
    }
    
    /**
     *
     * Method to get all album images
     * 
     * @access public
     * @param string $albumId  The id of the album to get images for
     * @return array The array of album images
     */
    public function getAlbumImages($albumId)
    {
        return $this->fetchAll("WHERE `album_id` = '$albumId' ORDER BY display_order ASC");
    }
    
    /**
     *
     * Method to get all user images
     * 
     * @access public
     * @param string $userId The id of the user to get images for
     * @return array The array of user images
     */
    public function getUserImages($userId)
    {
        return $this->fetchAll("WHERE `user_id` = '$userId' ORDER BY display_order ASC");
    }
    
    /**
     *
     * Method to get all context images
     * 
     * @access public
     * @param string $contextCode The code of the context to get images for
     * @return array The array of context images
     */
    public function getContextImages($contextCode)
    {
        return $this->fetchAll("WHERE `context_code` = '$contextCode' ORDER BY display_order ASC");
    }

    /**
     *
     * Method to get an image
     * 
     * @access public
     * @param string $id The id of the image to get
     * @return array The array of image data
     */
    public function getImage($id)
    {
        return $this->getRow('id', $id);
    }
    
    /**
     *
     * Method to add an image
     * 
     * @access public
     * @param array $fields The array of fields to add to the image table
     * @return string The id of the record created 
     */
    public function addImage($fields)
    {
        $images = $this->getAlbumImages($fields['album_id']);
        $count = count($images);
        
        $fields['display_order'] = ++$count;
        $fields['created_by'] = $this->userId;
        $fields['date_created'] = date('Y-m-d H:i:s');

        return $this->insert($fields);
    }
    
    /**
     *
     * Method to update an image
     * 
     * @access public
     * @param string $id The id of the image to update
     * @param array $fields The array of fields to update on the image table
     * @return boolesn TRUE if the update was successfull | FALSE if not 
     */
    public function updateImage($id, $fields)
    {
        $fields['updated_by'] = $this->userId;
        $fields['date_updated'] = date('Y-m-d H:i:s');
        
        return $this->update('id', $id, $fields);
    }
    
    /**
     *
     * Method to delete an image
     * 
     * @access public
     * @param string $id The id of the image to delete
     * @return boolean $result TRUE if the delete was successfull | FALSE if not 
     */
    public function deleteImage($id)
    {
        $image = $this->getImage($id);
        
        $result = $this->delete('id', $id);
        $this->objFileMan->deleteFile($image['file_id'], TRUE);
         
        $images = $this->getAlbumImages($image['album_id']);

        if (!empty($images))
        {
            $i = 0;
            foreach ($images as $image)
            {
                $this->update('id', $image['id'], array('display_order' => ++$i));
            }
        }
        
        return $result;
    }
    
    /**
     *
     * Method to delete gallery images
     * 
     * @access public
     * @param string $galleryId The id of the gallery to delete images from
     * @return boolean TRUE if the delete was successfull | FALSE if not 
     */
    public function deleteGalleryImages($galleryId)
    {
        $images = $this->getGalleryImages($galleryId);
        
        $result = $this->delete('gallery_id', $galleryId);
        
        if (!empty($images))
        {
            foreach ($images as $image)
            {
                $this->objFileMan->deleteFile($image['file_id'], TRUE); 
            }
        }
        
        return $result;
    }

    /**
     *
     * Method to delete album images
     * 
     * @access public
     * @param string $albumId The id of the album to delete images from
     * @return boolean TRUE if the delete was successfull | FALSE if not 
     */
    public function deleteAlbumImages($albumId)
    {
        $images = $this->getAlbumImages($albumId);
        
        $result = $this->delete('album_id', $albumId);
        
        if (!empty($images))
        {
            foreach ($images as $image)
            {
                $this->objFileMan->deleteFile($image['file_id'], TRUE); 
            }
        }
        
        return $result;
    }
    
    /**
     *
     * Method to get all shared images 
     * 
     * @access public
     * @return array $sharedImages The array of shared images 
     */
    public function getSharedImages()
    {
        $sql = "SELECT *, i.id AS image_id FROM `$this->table` AS i";
        $sql .= " LEFT JOIN `tbl_imagegallery_galleries` AS g ON i.gallery_id = g.id";
        $sql .= " WHERE g.is_shared = '1' AND (i.user_id != '$this->userId' OR i.user_id IS NULL)";
        
        $galleryImages = $this->getArray($sql);

        $sql = "SELECT *, i.id AS image_id FROM `$this->table` AS i";
        $sql .= " LEFT JOIN `tbl_imagegallery_albums` AS a ON i.album_id = a.id";
        $sql .= " WHERE a.is_shared = '1' AND (i.user_id != '$this->userId' OR i.user_id IS NULL)";
        
        $albumImages = $this->getArray($sql);

        $sql = "SELECT *, id AS image_id FROM `$this->table`";
        $sql .= " WHERE `is_shared` = '1' AND (`user_id` != '$this->userId' OR `user_id` IS NULL)";
        
        $images = $this->getArray($sql);

        $data = array_merge($galleryImages, $albumImages, $images);

        $imageIdArray = array();
        foreach ($data as $image)
        {
            $imageIdArray[] = $image['image_id'];
        }
        $uniqueImages = array_unique($imageIdArray);
        
        $sharedImages = array();
        foreach ($uniqueImages as $unique)
        {
            foreach ($data as $image)
            {
                if ($unique == $image['image_id'])
                {
                    $sharedImages[] = $image;
                    break;
                }
            }
        }
        
        return $sharedImages;
    }
    
    /**
     *
     * Method to update the view count
     * 
     * @access public
     * @pae=ram string $imageId The id of the image to update 
     * @return VOID
     */
    public function updateViewCount($imageId)
    {
        $image = $this->getImage($imageId);
        
        $viewCount = $image['view_count'] + 1;
        
        return $this->update('id', $imageId, array('view_count' => $viewCount));
    }
}
?>