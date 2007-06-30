<?php
/* ----------- data class extends dbTable for tbl_context_usernotes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

 /**
 * Class to access the User Notes Tables 
 * @package context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @author Wesley  Nitsckie
 * @version $Id$ 
 **/
class dbnotes extends dbTable
{

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_context_usernotes');
    }

    /**
    * Save method for editing a record in this table
    *@param string $mode: edit if coming from edit, add if coming from add
    *@param string $userId : The id of the user
    *@return null
    *@access public
    */
    function saveRecord($userId,$mode=null)
    {
        $id=addslashes(TRIM($_POST['id']));
        $nodeId = addslashes(TRIM($_POST['nodeId']));
        //$userId = addslashes(TRIM($_POST['userId']));
        $note = addslashes(TRIM($_POST['note']));
        // if edit use update
        if ($mode=="edit") 
        {
            $this->update("id", $id, array(
            'nodeId' => $nodeId,
            'userId' => $userId,
            'note' => $note));
        }
        // if add use insert
        if ($mode=="add"||$more=null) 
        {
            $this->insert(array(
                'nodeId' => $nodeId,
                'userId' => $userId,
                'note' => $note));
        }
    }
    /**
    *Method to get the note id for a given node and user
    *@param string $nodeId : The Node Id
    *@param string $userId : The user Id
    *@return array : The note 
    *@access public
    */
    function getNote($nodeId,$userId){    
        $ret = $this->getArray("SELECT *  from tbl_context_usernotes WHERE userId=".$userId." AND nodeId='".$nodeId."'");      
        return $ret;
    }
} 
?>