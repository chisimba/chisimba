<?php
/**
* Class dbType extends dbTable.
* @author Fernando Martinez
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package pbl
* @version 1
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class for providing access to the pbl_assocs_types table in the database.
 * The table contains the type of associations formed between display scenes and tasks in a case
 *
 * Types include: scene, task, more info (minfo) and case.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 0.9
 */

class dbType extends dbTable
{
    /**
     * Constructor method to define the table and initialise objects
     */
    public function init()
    {
        parent::init('tbl_pbl_assoc_types');
    }

    /**
     * Method to get the id of an association type.
     *
     * @param string $name Type name
     * @return string $id The id of the type
     */
    public function getId($name)
    {
        $rows = array();
        $sql = "select id from tbl_pbl_assoc_types where name='" . $name . "'";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        return $rows[0]['id'];
    }

    /**
     * Method to get the words id for a type.
     *
     * @param string $words Reference to the words
     * @return $words The id
     */
    public function getAssocWords($words)
    {
        $sql = "select name,wordsid from tbl_pbl_assoc_types";
        $words = array();
        $rows = $this->getArray($sql);
        if (!$row){
            return FALSE;
        }
        foreach($rows as $row) {
            $words[$row['name']] = $row['wordsid'];
        }
        return $words;
    }
}

?>