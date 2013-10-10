<?php
/**
 * ePortfolio Blocks
 * 
 * Class for managing eportfolio blocks. Allows one to add, update, or
 * delete a record (block) in eportfolio table -- tbl_eportfolio_blocks_tbl
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
 * @category   Chisimba
 * @package    eportfolio
 * @author     Paul Mungai <someone@example.com>
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @link       http://chisimba.com
 */

// security check
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


class eportfolioBlocks extends dbTable {

    public $objUser;
    public $TRUE;
    public $FALSE;

    /**
     * Standard init function
     *
     * @return NULL
     */
    public function init() {
        try {
            parent::init('tbl_eportfolio_blocks');
            $this->objUser = $this->getObject('user','security');
            if ($this->dbType == "pgsql") {
                $this->TRUE = 't';
                $this->FALSE = 'f';
            } else {
                $this->TRUE = 1;
                $this->FALSE = 0;
            }
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /**
     * Method to retrieve the visible blocks
     * Extra Functionality, adds eportfolio default blocks
     * if non-existent for logged in user
     *
     * @param string $column main|identity
     * @param string $userId user Id
     * @return array The block data from the table
     */
    public function getVisibleBlocks($column, $userId=Null) {
        //Use logged in user Id if empty/Null
        if(empty($userId))
          $userId = $this->objUser->userId();
        //Check if blocks exist for this user
        $hasBlocks = $this->getBlocks($column);
        if(empty($hasBlocks)){
            if ($column=='main'){
                //Create a simple array to hold the block names
                $mainBlocks = array('identification', 'activities', 'affiliation', 'transcripts', 'qualifications', 'goals', 'competencies', 'interests', 'reflections', 'assertions');
                foreach($mainBlocks as $mainBlock){
                    //Create array to hold block data
                    $blockData = array('userid'=>$userId, 'title'=>ucfirst(strtolower($mainBlock)), 'side'=>$column,  'isblock'=>TRUE, 'blockname'=>$mainBlock, 'blockmodule'=>'eportfolio');
                    $this->insertBlock($blockData);
                }
                //Insert for Identity blocks as well
                //Check if blocks exist for this user
                $column='identity';
                $hasBlocks = $this->getBlocks($column);
                if(empty($hasBlocks)){
                    //Create a simple array to hold the block names
                     $mainBlocks = array('address', 'contact', 'email', 'demographics');
                    foreach($mainBlocks as $mainBlock){
                        //Create array to hold block data
                        $blockData = array('userid'=>$userId, 'title'=>ucfirst(strtolower($mainBlock)), 'side'=>$column,  'isblock'=>TRUE, 'blockname'=>$mainBlock, 'blockmodule'=>'eportfolio');
                        $this->insertBlock($blockData);
                    }
                }
            }
        }
        try {
            return $this->getAll("WHERE userid = '".$userId."' AND side = '$column' AND visible = '{$this->TRUE}' ORDER BY position ASC");
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /**
     * Method to retrieve all blocks
     *
     * @param string $column left|right
     * @return array The block data from the table|error
     */
    public function getBlocks($column) {
        try {
            return $this->getAll("WHERE userid = '".$this->objUser->userId()."' AND side = '$column' ORDER BY position ASC");
        } catch (customException $e) {
            customException::cleanUp();
        }
    }
    /**
     * Method to retrieve a single block
     *
     * @param string $id block id
     * @return array The block data from the table|error
     */
    public function getSingleBlock($id) {
        try {
            return $this->getAll("WHERE id = '".$id."'");
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /**
     * Method to retrieve all blocks not updated by the owner
     *
     * @return array The user blocks data from the table|error
     */
    public function getWrongUpdates() {
        try {
            return $this->getAll("WHERE userid = '".$this->objUser->userId()."' AND updatedby != '".$this->objUser->userId()."' ORDER BY position ASC");
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
      $blockData = $this->getSingleBlock($id);
      if ($blockData[0]["userid"]==$this->objUser->userId()){
        try {
            return $this->update('id',$id,array('visible'=>$vis,'datelastupdated'=>$this->now(),'updatedby'=>$this->objUser->userId()));
        } catch (customException $e) {
            customException::cleanUp();
        }
      }
    }
    /**
    * Function to get the next available position on a navbar
    * 
    * @param boolean $left left or right navbar
    * @return int the next available position|error
    **/

    private function getNextPos($column) {
        try {
            $ret = $this->getArray("SELECT MAX(position) FROM tbl_eportfolio_blocks WHERE userid = '".$this->objUser->userId()."' AND side = '{$column}'");
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
    public function insertBlock($arrayData) {
        try {
            $arrData = array();
            $arrData['visible'] = $this->TRUE;
            $arrData['datelastupdated'] = $this->now();
            $arrData['updatedby'] = $this->objUser->userId();
            $arrData['title'] = $arrayData['title'];
            $arrData['side'] = $arrayData['side'];
            $arrData['isblock'] = $arrayData['isblock'];
            $arrData['blockname'] = $arrayData['blockname'];
            $arrData['blockmodule'] = $arrayData['blockmodule'];
            $arrData['position'] = $this->getNextPos($arrayData['side']);
            $arrData['userid'] = $this->objUser->userId();
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
    public function updateBlock($id,$arrayData) {
      $blockData = $this->getSingleBlock($id);
      if ($blockData[0]["userid"]==$this->objUser->userId()){
        try {
            $arrData = array();
            $arrData['visible'] = $this->TRUE;
            $arrData['datelastupdated'] = $this->now();
            $arrData['updatedby'] = $this->objUser->userId();
            return $this->update('id',$id,$arrData);
        } catch (customException $e) {
            customException::cleanUp();
        }
      }
    }

    /**
    * Fuction to move a record up in the list by swapping the position value with the record above
    * @param string $id the id of the record to move|error
    */

    function moveRecUp($id) {
        try {
            $rec = $this->getRow('id',$id);
            if ($rec['position'] >= 1) {
                $sql = "SELECT MAX(position) AS above FROM tbl_eportfolio_blocks WHERE userid = '".$this->objUser->userId()."' AND side = '{$rec['side']}' AND position < '{$rec['position']}'";
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
    * 
    * @param string $id the id of the record to move|error
    */

    function moveRecDown($id) {
        try {
            $rec = $this->getRow('id',$id);
            $pPos = $this->getArray("SELECT min(position) AS below FROM tbl_eportfolio_blocks WHERE userid = '".$this->objUser->userId()."' AND side = '{$rec['side']}' AND position > '{$rec['position']}'");
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
    /*
    *Function to return all visible Block Names
    *
    *return array
    *@access private
    *
    */
    public function getAllVisibleBlockName() {
        //Get Visible MAIN blocks
        $mainBlocks = $this->getVisibleBlocks('main');
        //Get Visible IDENTIFICATION blocks
        $identityBlocks = $this->getVisibleBlocks('identity');
        //Array to store blockname
        $blockname=array();
        foreach ($mainBlocks as $mainBlock){
            $blockname[] = $mainBlock["blockname"];
            if($mainBlock["blockname"]=='identification'){
                foreach ($identityBlocks as $identityBlock){
                    $blockname[] = $identityBlock["blockname"];
                }        
            }
        }
        return $blockname;
    }
}
?>
