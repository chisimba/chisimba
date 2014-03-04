<?php

/**
 * Test Blocks
 * 
 * Class for managing test blocks. Allows one to add, update, or
 * delete a question or reposition the item in the blocks table -- tbl_test_blocks
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
 * @package    mcqtests
 * @author     Paul Mungai <paulwando@gmail.com>
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

class dbBlocks extends dbTable {

    public $objUser;
    public $TRUE;
    public $FALSE;

    /**
     * Standard init function
     *
     * @return NULL
     * @access public
     */
    public function init() {
        try {
            parent::init('tbl_test_blocks');
            $this->objUser = $this->getObject('user', 'security');
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
     *
     * @param string $column main|identity
     * @param string $categoryId Category Id
     * @return array The block data from the table
     * @access public
     */
    public function getVisibleBlocks($column, $categoryId) {
        try {
            return $this->getAll("WHERE categoryid = '" . $categoryId . "' AND side = '$column' AND visible = '{$this->TRUE}' ORDER BY position ASC");
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /**
     * Method to retrieve all blocks
     *
     * @param string $column left|right
     * @param string $categoryId Category Id
     * @return array The block data from the table|error
     * @access public
     */
    public function getBlocks($column, $categoryId) {
        try {
            return $this->getAll("WHERE categoryid = '" . $categoryId . "' AND side = '$column' ORDER BY position ASC");
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /**
     * Method to retrieve a single block
     *
     * @param string $id block id
     * @return array The block data from the table|error
     * @access public
     */
    public function getSingleBlock($id) {
        try {
            return $this->getAll("WHERE id = '" . $id . "'");
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /**
     * Method to retrieve all blocks not updated by the owner
     *
     * @param string $categoryId Category Id
     * @return array The user blocks data from the table|error
     * @access public
     */
    public function getWrongUpdates($categoryId) {
        try {
            return $this->getAll("WHERE categoryid = '" . $categoryId . "' AND userid = '" . $this->objUser->userId() . "' AND updatedby != '" . $this->objUser->userId() . "' ORDER BY position ASC");
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
     * @access public
     */
    public function updateVisibility($id, $vis) {
        $blockData = $this->getSingleBlock($id);
        try {
            return $this->update('id', $id, array('visible' => $vis, 'datelastupdated' => $this->now(), 'updatedby' => $this->objUser->userId()));
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /**
     * Function to get the next available position on a navbar
     * 
     * @param boolean $left left or right navbar
     * @param string $categoryId Category Id
     * @return int the next available position|error
     * @access public
     * */
    public function getNextPos($column, $categoryId) {
        try {
            $ret = $this->getArray("SELECT MAX(position) FROM tbl_test_blocks WHERE categoryid = '" . $categoryId . "' AND side = '{$column}'");
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
     * @access public
     */
    public function insertBlock($arrayData) {
        try {
            $arrData = array();
            $arrData['categoryid'] = $arrayData['categoryid'];
            $arrData['visible'] = $this->TRUE;
            $arrData['datelastupdated'] = date('Y-m-d H:i:s');
            $arrData['updatedby'] = $this->objUser->userId();
            $arrData['title'] = $arrayData['title'];
            $arrData['side'] = $arrayData['side'];
            $arrData['isblock'] = $arrayData['isblock'];
            $arrData['blockname'] = $arrayData['blockname'];
            $arrData['content'] = $arrayData['content'];
            $arrData['blockmodule'] = $arrayData['blockmodule'];
            $arrData['position'] = $this->getNextPos($arrayData['side'],$arrayData['categoryid']);
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
     * @access public
     */
    public function updateBlock($id, $arrayData) {
        $blockData = $this->getSingleBlock($id);
        if (!empty($arrayData["categoryid"])) {
            try {
                $arrData = array();
                $arrData['visible'] = $this->TRUE;
                $arrData['datelastupdated'] = $this->now();
                $arrData['updatedby'] = $this->objUser->userId();
                return $this->update('id', $id, $arrData);
            } catch (customException $e) {
                customException::cleanUp();
            }
        }
    }

    /**
     * Fuction to move a record up in the list by swapping the position value with the record above
     * @param string $id the id of the record to move|error
     * @param string $categoryId the Category Id
     * @access public
     */
    public function moveRecUp($id, $categoryId) {
        try {
            $rec = $this->getRow('id', $id);
            if ($rec['position'] >= 1) {
                $sql = "SELECT MAX(position) AS above FROM tbl_test_blocks WHERE categoryid = '" . $categoryId . "' AND side = '{$rec['side']}' AND position < '{$rec['position']}'";
                $pPos = $this->getArray($sql);
                $pPos = current($pPos);
                if ($pPos['above'] != null) {
                    $previous = $this->getAll("WHERE side = '{$rec['side']}' AND position = '{$pPos['above']}'");
                    $previous = current($previous);
                    $previous['position'] = $rec['position'];
                    $rec['position'] = $pPos['above'];
                    $this->update('id', $id, $rec);
                    $this->update('id', $previous['id'], $previous);
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
     * @param string $categoryId the Category Id
     * @access public
     */
    public function moveRecDown($id, $categoryId) {
        try {
            $rec = $this->getRow('id', $id);
            $pPos = $this->getArray("SELECT min(position) AS below FROM tbl_test_blocks WHERE categoryid = '" . $categoryId . "' AND side = '{$rec['side']}' AND position > '{$rec['position']}'");
            $pPos = current($pPos);
            if ($pPos['below'] != null) {
                $next = $this->getAll("WHERE side = '{$rec['side']}' AND position = '{$pPos['below']}'");
                $next = current($next);
                $next['position'] = $rec['position'];
                $rec['position'] = $pPos['below'];
                $this->update('id', $id, $rec);
                $this->update('id', $next['id'], $next);
            }
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /*
     * Function to return all visible Block Names
     *
     * @param string $bname Block name
     * @param string $categoryId the Category Id
     * return array
     * @access public
     *
     */

    public function getAllVisibleBlockName($categoryId, $bname='main') {
        //Get Visible MAIN blocks
        $mainBlocks = $this->getVisibleBlocks($bname, $categoryId);
        //Array to store blockname
        $blockname = array();
        foreach ($mainBlocks as $mainBlock) {
            $blockname[] = $mainBlock["blockname"];
        }
        return $blockname;
    }

}

?>