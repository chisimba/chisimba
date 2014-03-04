<?php
/**
* dbcollectsubmit class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbcollectsubmit class for managing the data in the tbl_etd_collection_submission table.
* @author Megan Watson
* @copyright (c) 2007 University of the Western Cape
* @version 0.1
*/

class dbcollectsubmit extends dbtable
{
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_etd_collection_submission');
        $this->table = 'tbl_etd_collection_submission';
        $this->thesisTable = 'tbl_etd_metadata_thesis';
        $this->collectTable = 'tbl_etd_collections';
        $this->metaTable = $this->thesisTable;
        
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
    * Method to set the type of metadata - thesis/qualified.
    * Method sets the table to retreive the metadata from.
    */
    public function setMetaType($meta)
    {
        //if($meta == 'qualified'){
        //    $this->metaTable = $this->qualTable;
        //}else{
            $this->metaTable = $this->thesisTable;
        //}
    }

    /**
    * Method to get all collections for a submission.
    * @param string $submitId The id of the submission.
    */
    public function getCollections($submitId)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE submissionId = '$submitId'";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get the collection data for a submission.
    * @param string $submitId The id of the submission.
    */
    public function getCollectionData($submitId)
    {
        $sql = "SELECT * FROM {$this->table} AS s , {$this->collectTable} AS c 
            WHERE c.id = s.collectionId && s.submissionId = '{$submitId}'";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to get all submissions for a collection.
    * @param string $collectId The id of the collection.
    */
    public function getSubmissions($collectId)
    {
        $sql = 'SELECT col.*, meta.dc_title FROM '.$this->table.' AS col ';
        $sql .= 'LEFT JOIN '.$this->metaTable.' AS meta ON col.submissionId = meta.submitId ';
        $sql .= "WHERE col.collectionId = '$collectId'";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to check if a collection is empty.
    * @param string $collectId The id of the collection.
    */
    public function isEmpty($collectId)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE collectionId = '$collectId'";
        $sql .= ' LIMIT 1';

        $data = $this->getArray($sql);
        if(!empty($data)){
            return TRUE;
        }
        return FALSE;
    }

    /**
    * Method to take an array of submissions and add them to a collection.
    * @param string $submissions The array of submissions ids.
    * @param string $collectId The id of the collection.
    */
    public function addSubmitToCollection($submissions, $collectId)
    {
        if(!empty($submissions)){
            foreach($submissions as $item){
                $this->setCollection($item, $collectId);
            }
        }
    }

    /**
    * Method to take a collection and add a submission to it, if the link doesn't already exist.
    * @param string $submitId The id of the submission.
    * @param string $collectId The id of the collection.
    */
    public function setCollection($submitId, $collectId)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE submissionId = '$submitId'";
        $sql .= " AND collectionId = '$collectId'";
        $data = $this->getArray($sql);

        if(!empty($data)){
            return FALSE;
        }else{
            $this->addSubmissionToCollection($submitId, $collectId);
        }
        return TRUE;
    }

    /**
    * Method to add a submission to a collection.
    * The method can update the collection containing a specified submission.
    * @param string $submitId The id of the submission.
    * @param string $collectId The id of the collection.
    * @param string $id The id of the bridge between submission and collection.
    */
    public function addSubmissionToCollection($submitId, $collectId, $id = NULL)
    {
        // Check the submission isn't already in a collection
        $sql = "SELECT * FROM {$this->table} WHERE submissionid = '{$submitId}'";
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            // Check if it's the selected collection
            if($data[0]['collectionid'] == $collectId){
                // do nothing
                return $data[0]['id'];
            }else{
                // remove the bridge and add the new one
                $id = $data[0]['id'];
            }
        }
        
        $fields = array();
        $fields['submissionId'] = $submitId;
        $fields['collectionId'] = $collectId;
        $fields['updated'] = $this->now();

        if(!empty($id)){
            $fields['modifierid'] = $this->userId;
            $this->update('id', $id, $fields);
        }else{
            $fields['datecreated'] = $this->now();
            $fields['creatorid'] = $this->userId;
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
    * Method to remove a submission from a collection.
    * @param string $metaId The id of the metadata (thesis/qualified) for the submission.
    * @param string $collectId The id of the collection.
    */
    public function removeSubmissionFromCollection($metaId, $collectId)
    {
        $sql = 'SELECT bridge.id FROM '.$this->table.' AS bridge ';
        $sql .= 'LEFT JOIN '.$this->metaTable.' AS meta ON bridge.submissionId = meta.submitId ';
        $sql .= "WHERE meta.id = '$metaId' AND bridge.collectionId = '$collectId'";
        $data = $this->getArray($sql);

        if(!empty($data)){
            $this->delete('id', $data[0]['id']);
        }
        return FALSE;
    }
    
    /**
    * Method to delete the connection between a submission and a collection
    *
    * @access public
    */
    public function deleteBridge($submitId)
    {
        $this->delete('submissionId', $submitId);
    }
}
?>