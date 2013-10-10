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
class dbReadingList_links extends dbTable
{
    /**
    * Constructor method to define the table
    */
    function init() 
    {
        parent::init('tbl_readinglist_links');
        //$this->USE_PREPARED_STATEMENTS=True;
    }

	function getByItem($itemId)
	{
        $sql = "SELECT * FROM tbl_readinglist_links WHERE itemid = '" .$itemId . "'";
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }else{
            return FALSE;
        }	  
	}

	
	/**
	* Return a single record
	* @param string $id ID
	* @return array The values
	*/	
	function listSingle($id)
	{
		$sql = "SELECT * FROM tbl_readinglist WHERE id = '" . $id . "'";
		return $this->getArray($sql);
		//return $this->getRow("id", $id);
	}

	/**
	* Insert a record
	* @param string $contextId The context ID
	* @param string $author The author
	* @param string $title The title
	* @param string $publisher The publisher
	* @param string $year The year
	* @param string $link The Link
	* -- @param string $userId The user ID
	* -- @param string $dateLastUpdated Date last updated
	*/
	function insertSingle($itemId, $link)
	{
		$this->insert(array( 
        		'itemid' => $itemId,
        		'link' => $link
			//'description' => $description
			
			//'userId' => $userId
			//'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
		));
		return;	
	}

	/**
	* Update a record
	* @param string $id ID
	* @param string $author The author
	* @param string $title The title
	* @param string $publisher The publisher
	* @param string $year The year
	* @param string $link The Link
	* -- @param string $userId The user ID
	* -- @param string $dateLastUpdated Date last updated
	*/
	function updateSingle($id, $itemId, $link, $description)
	{
		$this->update("id", $id, 
			array(
        		'itemId' => $itemId,
        		'link' => $link,
			'description' => $description
			//'userId' => $userId,
			//'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
			)
		);
	}
	
	/**
	* Delete a record
	* @param string $id ID
	*/
	function deleteSingle($id)
	{
		$this->delete('id', $id);
	}
}
?>