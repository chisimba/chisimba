<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* class to control the utilty method for the events calendar
*
* @author Wesley Nitsckie
* @copyright (c) 2005 University of the Western Cape
* @package photogallery
* @version 1
*
*
*/
class dbimages extends dbTable
{

    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_photogallery_images');
        $this->_objUser = $this->getObject('user', 'security');

    }

   /**
   * Method to insert images into the database
   * @return boolean|string
   * @access public
   */
   public function insertImageData($fields)
   {
        return $this->insert($fields);


    }

    /**
    * Method to the thumbnail
    */
    public function getThumbNailFromFileId($fileId)
    {
        $objFile = $this->getObject('dbfile', 'filemanager');
        $file = $objFile->getFile($fileId);
        $objThumbnails = $this->getObject('thumbnails', 'filemanager');
        return $objThumbnails->getThumbnail($fileId,$file['filename']);
    }

    /**
    * Method to get the list of images for a user
    * @param string userId
    * @access public
    */
    public function getAlbumImages($albumId)
    {
        return $this->getAll("WHERE album_id='".$albumId."' ORDER BY position ");

    }

    /**
    * Method to update the images
    * @param string $id
    * @param array $fields
    * @access public
    */
    public function updateImage($id, $fields)
    {
        return $this->update('id', $id, $fields);
    }


     /**
    * Increase the hit count for an album
    * @param string $albumId
    * @access public
    */
    public function incrementHitCount($imageId)
    {
         $image = $this->getRow('id', $imageId);
         $views = array('no_views' => intval($image['no_views']) + 1 );
        $this->update('id', $imageId,$views);
    }

    /**
    * Method to reorder the ablums
    *
    */
    public function reOrderImages($albumId)
    {
        $order = str_replace('images[]=','',$this->getParam('imageOrder'));
        $newOrder = split('&',$order);

        $images = $this->getAlbumImages($albumId);

        $cnt = 0;
        foreach($newOrder  as $arr)
        {
             $cnt++;
            $this->update('id', $images[$arr-1]['id'], array('position' => $cnt));
        }
    }

    /**
    * Method to get the count of images
    * for an album
    *@param string $albumId
    */
    public function getImageCount($albumId)
    {
        $images = $this->getAll("WHERE albumId = '.$albumId.'");
        return count($images);
    }


     /**
      * Method to get a random photo for the
      * block
      *
      * @return string
      * @access public
      */
      public function getRandomPhoto()
      {
        $objAlbum = & $this->getObject('dbalbum', 'photogallery');
        $albums = $objAlbum->getAll("WHERE no_pics > 1 AND is_shared=1 ORDER BY rand() LIMIT 1 ");

        if (count($albums) == 0) {
            return FALSE;
        }

        $rec = $this->getAll("WHERE album_id='".$albums[0]['id']."' ORDER BY rand() LIMIT 1 ");

        if (count($rec) == 0) {
            return FALSE;
        } else {
            return $rec[0];
        }
      }

    /**
    * Method to update an image field
    * @param string $id
    * @param string $field
    * @param string $value
    */
    public function saveField($id, $field, $value)
    {
        return $this->update('id', $id, array($field => $value));
    }

    /**
     *
     * Get the most recent image uploads by a given user, hopefully using
     * reasonably optimal SQL method. This just returns the data, not the
     * actual images.
     *
     * @param string $userId The id of the user for whom to return images
     * @param integer $num The number of images to return
     * @return string array An array of image information
     * 
     */
    public function getRecentByUser($userId, $num=5)
    {
        $sql = 'SELECT tbl_photogallery_albums.user_id,
          tbl_photogallery_albums.thumbnail,
          tbl_photogallery_albums.title,
          tbl_photogallery_images.album_id,
          tbl_photogallery_images.file_id,
          tbl_photogallery_images.id AS imagecode,
          tbl_files.id, tbl_files.path, tbl_files.filename,
          tbl_files.datecreated,
          tbl_files.timecreated
        FROM tbl_photogallery_albums, tbl_photogallery_images, tbl_files
        WHERE tbl_photogallery_albums.id = tbl_photogallery_images.album_id
        AND tbl_photogallery_images.file_id = tbl_files.id
        AND user_id=\'' . $userId . '\'
        ORDER BY tbl_files.datecreated, tbl_files.timecreated DESC
        LIMIT ' . $num;
        return $this->getArray($sql);
    }
}
?>