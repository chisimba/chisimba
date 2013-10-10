<?php
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_readinglist
* @author John Abakpa, Juliet Mulindwa
* @author Luis Domingos, Juliet Mulindwa
* @copyright 2005 University of the Western Cape
*/
class dbReadingList_comment extends dbTable
{
    /**
    * Constructor method to define the table
    */
    function init() 
    {
        parent::init('tbl_readinglist_comment');
        $this->table = ('tbl_readinglist_comment');
        //$this->USE_PREPARED_STATEMENTS=True;
        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

	function getByItem($itemId)
	{
	  $sql = "SELECT * FROM ".$this->table;
      $sql .= " WHERE itemid = '" .$itemId . "'";
	  return $this->getArray($sql);
	  
	}

	
	/**
	* Return a single record
	* @param string $id ID
	* @return array The values
	*/	
	function listSingle($id)
	{
		$sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE id = '" . $id . "'";
		return $this->getArray($sql);
		//return $this->getRow("id", $id);
	}

	/**
	* Insert a record
	* @param string $itemd The ID of the item being commented on
	* @param string $comment The comment
	*
	*/
	function insertIntoDB($itemid, $comment)
	{
		$id = $this->insert(array( 
        		'itemid' => $itemid,
        		'comment' => $comment,
        		'userid' => $this->userId,
        		'updated' => date('Y-m-d H:i:s'),
        		
			//'description' => $description
			
			//'userId' => $userId
			//'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
		));
		return $id;	
	}
	
}
?>