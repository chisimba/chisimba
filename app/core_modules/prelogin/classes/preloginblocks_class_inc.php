<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for retrieving the block information for display on the prelogin page
*
* @author Nic Appleby
* @copyright GNU/GPL 2006 UWC
* @version $Id
* @package splashscreen
*/

class preloginBlocks extends dbTable {
	
	public $objUser;
	public $TRUE;
	public $FALSE;
	
	/**
	 * Standard chisimba init function
	 *
	 */
	public function init() {
		try {
			parent::init('tbl_prelogin_blocks');
			$this->objUser = &$this->getObject('user','security');
			if ($this->dbType == "pgsql") {
				$this->TRUE = 't';
				$this->FALSE = 'f';
			} else {
				$this->TRUE = TRUE;
				$this->FALSE = 0;
			}
		} catch (customException $e) {
			customException::cleanUp();
		}
	}
	
	/**
	 * Method to retrieve the visible blocks on the prelogin page
	 *
	 * @param string $column left|right
	 * @return array The block data from the table
	 */
	public function getVisibleBlocks($column) {
		try {
			return $this->getAll("WHERE side = '$column' AND visible = 'TRUE' ORDER BY position ASC");
		} catch (customException $e) {
			customException::cleanUp();
		}
	}
	
	/**
	 * Method to retrieve the blocks on the prelogin page
	 *
	 * @param string $column left|right
	 * @return array The block data from the table
	 */
	public function getBlocks($column) {
		try {
			return $this->getAll("WHERE side = '$column' ORDER BY position ASC");
		} catch (customException $e) {
			customException::cleanUp();
		}
	}
	
	/**
	 * Method to change the visibility of a block
	 *
	 * @param string $id the id of the block to change
	 * @param boolean $vis the visibility of the block
	 * @return TRUE|error
	 */
	public function updateVisibility($id,$vis) {
		try {
			return $this->update('id',$id,array('visible'=>$vis,'datelastupdated'=>$this->now(),'updatedby'=>$this->objUser->userId()));
		} catch (customException $e) {
			customException::cleanUp();
		}
	}
	
	/**
    * Function to get the next available position on a navbar
    * @param boolean $left left or right navbar
    * @return int the next available position
    **/

    private function getNextPos($column) {
    	try { 
    		$ret = $this->getArray("SELECT MAX(position) FROM tbl_prelogin_blocks WHERE side = '{$column}'");
    		$r = current($ret);
    		$pos = current($r) + 1;
    		return $pos;
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
    
    /**
     * Method to insert a new record into the table
     *
     * @param array $arrData The data to insert
     * @return TRUE|error
     */
	public function insertBlock($arrData) {
		try {
			$arrData['visible'] = $this->TRUE;
			$arrData['datelastupdated'] = $this->now();
			$arrData['updatedby'] = $this->objUser->userId();
			$arrData['position'] = $this->getNextPos($arrData['side']);
			//var_dump($arrData);
			return $this->insert($arrData);
		} catch (customException $e) {
			customException::cleanUp();
		}
	}
	
	/**
	 * Method to update a record in the table
	 *
	 * @param string $id the id of the record in question
	 * @param array $arrData the data that has changed
	 * @return TRUE|error
	 */
	public function updateBlock($id,$arrData) {
		try {
			$arrData['visible'] = $this->TRUE;
			$arrData['datelastupdated'] = $this->now();
			$arrData['updatedby'] = $this->objUser->userId();	
			return $this->update('id',$id,$arrData);
		} catch (customException $e) {
			customException::cleanUp();
		}
	}
	
	/**
    * Fuction to move a record up in the list by swapping the position value with the record above
    * @param string $id the id of the record to move
    */

    function moveRecUp($id) {
    	try {
    		$rec = $this->getRow('id',$id);
    		if ($rec['position'] > 1) {
    			$sql = "SELECT MAX(position) AS above FROM tbl_prelogin_blocks WHERE side = '{$rec['side']}' AND position < '{$rec['position']}'";
    			$pPos = $this->getArray($sql);
    			$pPos = current($pPos);
    			if ($pPos['above']!=null) {
    				$previous = $this->getAll("WHERE side = '{$rec['side']}' AND position = '{$pPos['above']}'");
    				$previous = current($previous);
    				$previous['position'] = $rec['position'];
    				$rec['position'] = $pPos['above'];
    				$this->update('id',$id,$rec);
    				$this->update('id',$previous['id'],$previous);
    			}

    		} 
    	} catch (customException $e) {
    			customException::cleanUp();
    		}
    }

    /**
    * Fuction to move a record down in the list by swapping the position value with the record below
    * @param string $id the id of the record to move
    */

    function moveRecDown($id) {
    	try { 
    		$rec = $this->getRow('id',$id);
    		$pPos = $this->getArray("SELECT min(position) AS below FROM tbl_prelogin_blocks WHERE side = '{$rec['side']}' AND position > '{$rec['position']}'");
    		$pPos = current($pPos);
    		if ($pPos['below']!=null) {
    			$next = $this->getAll("WHERE side = '{$rec['side']}' AND position = '{$pPos['below']}'");
    			$next = current($next);
    			$next['position'] = $rec['position'];
    			$rec['position'] = $pPos['below'];
    			$this->update('id',$id,$rec);
    			$this->update('id',$next['id'],$next);
    		}
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}
?>