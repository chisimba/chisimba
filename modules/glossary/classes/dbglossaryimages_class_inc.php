<?php
/* ----------- data class extends dbTable for tbl_glossary_images------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Glossary Terms Table
* This class controls all functionality relating to the tbl_glossary table
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package glossary
* @version 1
*/
/**
* This class returns arrays of recordset from the database table 'tbl_glossary_images'
* which stores all images related to a particular term
*/
class dbglossaryimages extends dbTable
{

    /**
    * Constructor method to define the table
    */
    public function init() {
        parent::init('tbl_glossary_images');
    }

    /**
	* Method to get a list of images for a particular term
	*
	* @param string $id: Record ID of the Term
    * @return array All Images for a particular term
	*/
    public function getListImage($id)
    {
        $sql = "SELECT *, tbl_glossary_images.id as imageid FROM tbl_glossary_images
        INNER JOIN tbl_files ON ( tbl_glossary_images.image = tbl_files.id )
        WHERE item_id = '".$id."'";

        return $this->getArray ($sql);
    }

    /**
	* Method to fetch a row from the database
	*
	* @param string $item_id: Record ID of the Image
    * @param string $image: Record ID of the Image (received from filemanager module)
    * @param string $caption: Caption of the Image
    * @param string $userId: User inserting the image
    * @param string $dateLastUpdated: A timestamp of when this operation was performed
	*/
    public function insertImage($item_id, $image, $caption, $userId, $dateLastUpdated)
    {
        $this->insert(array(
                'item_id' => $item_id,
                'image' => $image,
                'caption' => $caption,
                'userid' => $userId,
                'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
        ));

        return;
    }

    /**
	* Method to delete an image from the database
    *
    * Images are stored as blobs using the filemanager module
    * The controller ensures that images are deleted from this table as well as from filemanager's table
	*
	* @param string $id: Record ID of the Image
	*/
    public function deleteImage ($id)
    {
        return $this->delete('id', $id);
    }



} #end of class
?>