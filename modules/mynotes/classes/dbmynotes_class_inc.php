<?php

/**
 *
 * Database access for My notes
 *
 * Database access for My notes. This is a sample database model class
 * that you will need to edit in order for it to work.
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
 * @category  Chisimba
 * @package   mynotes
 * @author    Nguni Phakela nguni52@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Database access for My notes
 *
 * Database access for My notes. This is a sample database model class
 * that you will need to edit in order for it to work.
 *
 * @package   mynotes
 * @author    Nguni Phakela nguni52@gmail.com
 *
 */
class dbmynotes extends dbtable {

    private $table;

    /**
     *
     * Intialiser for the mynotes database connector
     * @access public
     * @return VOID
     *
     */
    public function init() {
        $this->table = 'tbl_mynotes_text';
        //Set the parent table to our demo table
        parent::init($this->table);
    }

    /*
     * Method to save a note to the database
     * 
     * @access public
     * @param $data The array containing the key value pair of the data to be 
     *              inserted into the table.
     * @return $id The id of the data that was just inserted into the database table
     * 
     */

    public function insertNote($data) {
        $id = $this->insert($data);
        return $id;
    }

    /*
     * Method to edit a note in the database
     * 
     * @access public
     * @param $data The array containing the key value pair of the data to be 
     *              inserted into the table.
     * @param $id The id of the row of data to be updated
     * @return TRUE|FALSE
     * 
     */

    public function updateNote($data, $id) {
        return $this->update('id', $id, $data);
    }
    
    /** 
     * 
     * Get the existing tags so they can be deleted
     * and the modified ones added. Important to tag
     * management.
     * 
     * @param string $id The unique id of the record
     * @return string The returned tags, or null if none found
     * 
     */
    public function getExistingTags($id) 
    {
        $sql = 'SELECT tags FROM ' . $this->table
          . ' WHERE id="' . $id . '"';
        $res = $this->getArray($sql);
        if (count($res) > 0) {
            return $res[0]['tags'];
        } else {
            return NULL;
        }
    }

    /**
     * Method to delete a note.
     *
     * @access public
     * @param string $id The id of the note.
     * @return VOID
     * 
     */
    public function deleteNote($id) {
        $this->delete('id', $id);
    }

    /**
     * Method to return latest 2 notes for a user
     * 
     * @access public
     * @param string $id The id of the user's notes
     * @param $limit The number of records that should be retrieved from the table.
     * @return array The note array 
     * 
     */
    public function getNotes($uid, $limit = NULL) {
        $filter = " WHERE `userid` = '$uid' ORDER BY datemodified DESC, datemodified DESC ";
        $sql1 = "SELECT * FROM " . $this->table . $filter;
        $sql2 = "SELECT allDataTable.* FROM (" . $sql1 . ") as allDataTable " . $limit;

        return $this->getArray($sql2);
    }

    /*
     * Method to return note data.
     * 
     * @access public
     * @param string $id The id of the note
     * @return array The note array
     * 
     */

    public function getNote($id) {
        return $this->getRow('id', $id);
    }

    /*
     * Method to search the notes based on a tag
     * 
     * @access public
     * @param $searchKey The tag name that we are using to do the search
     * @return array All the notes that have the tag that is being searched for
     * 
     */

    public function getNotesWitTags($searchKey) {
        return $this->fetchAll(" WHERE `tags` LIKE '%" . $searchKey . "%'");
    }

    /*
     * Method to retrieve a certain number of notes
     * 
     * @access public
     * @param $uid This is the user ID.
     * @param $start This is the the starting row to search from
     * @param $end This is the end row to search to
     * @return array List of items to between the rows specified above
     */
    public function getNotesForList($uid, $start, $end) {
        if ($start < $end) {
            $sql = "Select * from " . $this->table . " where `userid` = '$uid' AND
                puid BETWEEN $start AND $end ORDER BY datemodified DESC ";

            return $this->getArray($sql);
        } else {
            return NULL;
        }
    }

    /*
     * Method to retrieve the number of records for a certain number of notes
     * 
     * @access public
     * @param $uid This is the user ID.
     * @param $start This is the the starting row to search from
     * @param $end This is the end row to search to
     * @return array The number of records that match the search criteria.
     */

    public function getListCount($uid, $start = NULL, $end = NULL) {
        if (empty($start) && empty($end)) {
            $filter = " where `userid` = '$uid'";
        } else {
            $filter = " where `userid` = '$uid' AND puid BETWEEN $start AND $end ORDER BY datemodified DESC ";
        }
        $recordCount = $this->getRecordCount($filter);

        return $recordCount;
    }

    /*
     * Method to retrieve notes when using the previous and next buttons. It will
     * return all the notes for our current page number
     * 
     * @acces public
     * @param $prevPageNum The previous page number
     * @param $nextPageNum The next page number
     * @return array All the notes that are expected for current page
     * 
     */
    public function getNotesUsingPuid($prevPageNum, $nextPageNum) {
        return $this->fetchAll(" WHERE `puid` BETWEEN $prevPageNum AND $nextPageNum");
    }

    /*
     * Method to get a note based on it's puid
     * 
     * @access public
     * @param $puid The current note's record number
     * @return array The data for the current row
     */
    public function getRowData($puid) {
        return $this->getRow('puid', $puid);
    }
}
?>