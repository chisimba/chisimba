<?php
/* ----------- data class extends dbTable for tbl_comment------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
*
* Model class for the table tbl_comment as used
* in the comment module
*
* @author Derek Keats
* @package comment
*
*/
class dbcomment extends dbTable
{
    /**
    *
    * @var object $objUser String to hold instance of the user object
    *
    */
   public $objUser;

    /**
    * Constructor method to define the table
    */
   public function init() {
        parent::init('tbl_comment');
        $this->objUser = & $this->getObject("user", "security");
    }

    /**
    *
    * Save method for editing a record in this table
    *
    * @param string $mode: edit if coming from edit, add if coming from add
    *
    */
    public function saveRecord($mode, $userId)
    {
        $tableName = $this->getParam('tableName', NULL);
        $sourceId = $this->getParam('sourceId', NULL);
        $type = $this->getParam('type', NULL);
        $comment = $this->getParam('comment', NULL);
        $moduleCode = $this->getParam('moduleCode', NULL);
        $approved = $this->getParam('approved', '0');

        //Added 2006/07/21 Serge Meunier - Id was not assigned
        $id = $this->getParam('id', NULL);

        // if edit use update
        if ($mode=="edit") {
            $this->update("id", $id, array(
            'tablename' => $tableName,
            'sourceid' => $sourceId,
            'type' => $type,
            'commenttext' => $comment,
            'datemodified' => date("Y/m/d H:i:s"),
            'modifierId' => $userId,
            'approved' => $approved));

        }#if

        // if add use insert
        if ($mode=="add" && $comment!=NULL) {
            $this->insert(array(
            'tablename' => $tableName,
            'sourceid' => $sourceId,
            'type' => $type,
            'commenttext' => $comment,
            'datecreated' => date("Y/m/d H:i:s"),
            'creatorid' => $userId,
            'approved' => $approved));

            $this->updateCounter($tableName, $sourceId, $moduleCode);
        }
    }
    /**
    *
    * Set approval status of comment in this table
    *
    * @param string $id: id of comment
    * @param string $approved: 1 if true, 0 if false
    * @author Serge Meunier
    * Added 2006/09/11
    *
    */
    public function setApproval($id, $approved)
    {
       $this->update("id", $id, array(
            'approved' => $approved));
    }

    /**
    *
    * Function to delete a comment from the database
    *
    * @param string $id: The id of the comment to delete
    *
    */
    public function deleteRecord($id)
    {
        $tableName = $this->getParam('tableName', NULL);
        $sourceId = $this->getParam('sourceid', NULL);
        $moduleCode = $this->getParam('moduleCode', NULL);

        if ($id!=NULL) {
            $this->delete('id', $id);

            $this->updateCounter($tableName, $sourceId, $moduleCode);
        }
    }

    /**
    *
    * Method to get a record for a table/id combination
    *
    * @param string $tableName The name of the table that the comment applies to
    * @param string $sourceId The id field in the source table
    * @return array: The array containing the comments
    *
    */
    public function getComment($tableName, $sourceId)
    {
        $where = " WHERE tablename='" . $tableName
        . "' AND sourceid='" . $sourceId . "'";
        return $this->getAll($where);
    }

    /**
    *
    * Method to get most recent records for a table/id combination
    *
    * @param string $tableName: The name of the table that the comment applies to
    * @param string $sourceId: The id field in the source table
    * @param int $count: The number of records to return
    * @param int $offset: The starting point to return from. To return frem start, set $offset = 0
    * @return array  The array containing the comments
    * @author Serge Meunier
    * Added 2006/07/18
    *
    */
    public function getMostRecentComment($tableName, $sourceId, $count = 10000000, $offset = 0)
    {
        $where = " WHERE tablename='" . $tableName
        . "' AND sourceid='" . $sourceId . "' ORDER BY modified DESC LIMIT " . $offset . ", " . $count;
        return $this->getAll($where);
    }

    /**
    *
    * Method to get records for a table/id/type combination
    *
    * @param string $tableName The name of the table that the comment applies to
    * @param string $sourceId The id field in the source table
    * @param string $type The type of record to return
    * @param int $count: The number of records to return
    * @param int $offset: The starting point to return from. To return frem start, set $offset = 0
    * @return array  The array containing the comments
    * @author Serge Meunier
    * Added 2006/07/18
    *
    */
    public function getCommentByType($tableName, $sourceId, $type, $count = 10000000, $offset = 0)
    {
        $where = " WHERE tablename='" . $tableName
        . "' AND sourceid='" . $sourceId . "' AND type = '" . $type . "' LIMIT " . $offset . ", " . $count;
        return $this->getAll($where);
    }

    /**
    * Method to get all comments on a specific table - not specific to a record
    *
    * @author Megan Watson Added 02/10/2006
    * @param string $tableName The given table
    * @return array $data
    */
    public function getCommentsByTableName($tableName)
    {
        $where = " WHERE tablename='" . $tableName."'";
        return $this->getAll($where);
    }

    /**
    *
    * Method to get records for a type
    *
    * @param string $type The type of record to return
    * @param int $count: The number of records to return
    * @param int $offset: The starting point to return from. To return frem start, set $offset = 0
    * @return array  The array containing the comments
    * @author Serge Meunier
    * Added 2006/07/18
    *
    */
    public function getAllCommentsByType($type, $count = 10000000, $offset = 0)
    {
        $where = " WHERE type = '" . $type . "' LIMIT " . $offset . ", " . $count;
        return $this->getAll($where);
    }
    /**
    *
    * Method to update the comment counter
    *
    * @param string $tableName The name of the table that the comment applies to
    * @param string $sourceId The id field in the source table
    * @param string $sourceModule The module that is the owner of the table
    *
    */
    public function updateCounter($tableName, $sourceId, $sourceModule)
    {
        if(!empty($tableName) && !empty($sourceModule)){
            $dtClass = 'db' . substr($tableName, 4);
            $objDb2Update = & $this->getObject($dtClass, $sourceModule);
            $cSql = "SELECT id, commentcount FROM " . $tableName
              . " WHERE id = '" . $sourceId . "'";
            $ar = $objDb2Update->getArray($cSql);

            //----------
            //Modified by Serge Meunier 19/07/2006 to correct a bug in the comment count
            //caused by commentCount being out of sync with the tbl_commment table

             //$comments = $ar[0]['commentCount'];
             //$comments++;

            $where = " WHERE tablename='" . $tableName  . "' AND sourceid='" . $sourceId . "'";
            $comments = $this->getRecordCount($where);
            //----------

            $objDb2Update->update("id", $ar[0]['id'], array(
              'commentcount' => $comments));
        }

    }
    /**
    *
    * Method to get number of approved comments for a table
    *
    * @param string $tableName The name of the table that the comment applies to
    * @param string $sourceId The id field in the source table
    * @param string $sourceModule The module of the source table
    * @param bool $moderator TRUE if user is a moderator
    * @return int : The number of approved comments
    *
    */
    public function getApprovedCount($tableName, $sourceId, $sourceModule, $moderator)
    {
        $where = " WHERE tablename='" . $tableName  . "' AND sourceid='" . $sourceId . "'";
        if ($moderator == FALSE){
            $where .= " AND approved=1";
        }
        $comments = $this->getRecordCount($where);
        return $comments;
    }


} #end of class
?>
