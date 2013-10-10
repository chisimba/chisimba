<?php
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
* A term can have links to websites for more information. This class provides the functionality for this.
*/
class dbGlossaryUrls extends dbTable
{

    /**
    * Constructor method to define the table(default)
    */
    public function init()
    {
        parent::init('tbl_glossary_urls');
    }

    /**
    * Method to fetch all URLs for a term
    *
    * @param string $item: Id of the term
    * @return array List of all Urls
    */
    public function fetchAllRecords($item)
    {
        return $this->getAll(" WHERE item_id='".$item."'");
    }

    /**
    * Method to get the number of URLs existing for a term
    *
    * @param string $item: ID of the term
    * @return int number of urls for a term
    */
    public function getNumRecords($item)
    {
        return $this->getRecordCount(" WHERE item_id='".$item."'");
    }

    /**
    * Method to retrieve a single Record
    *
    * @param string $id: ID of the URL record
    * @return array holding the single URL
    */
    public function listSingle($id)
    {
        return $this->getRow('id', $id);
    }

    /**
    * Method to insert a new record into the table
    *
    * @param string $url: URL - http://etc
    * @param string $item_id: Id of the term
    * @param string $userId: Person making the change
    * @param datetime $dateLastUpdated: Date / Time of the Update
    */
    public function insertSingle($url, $item_id, $userId, $dateLastUpdated)
    {
        $this->insert(array(
        'url'             => $url,
        'item_id'         => $item_id,
        'userid'          => $userId,
        'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
        ));
        
        return;
    }

    /**
    * Method to delete a record from the table
    *
    * @param string $id: ID of the URL
    */
    public function deleteSingle($id) {
    
        $this->delete('item_id', $id);
    
    return;	
    }

    public function deleteSingleUrl($id) {
    
        $this->delete('id', $id);
    
    return;	
    }

}  #end of class

?>