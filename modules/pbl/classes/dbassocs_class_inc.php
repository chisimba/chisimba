<?php
/**
* Class dbAssocs extends dbTable.
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
 * Class for providing access to the tbl_pbl_assocs table in the database.
 * The table contains associations between different scenes in a case and the task for a scene.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 1
 */

class dbAssocs extends dbTable
{
    /**
    * @var string $table The name of the database table being accessed
    * @access private
    */
    private $table = 'tbl_pbl_assocs';
    
    /**
     * Constructor method to define the table
     */
    public function init()
    {
        parent::init('tbl_pbl_assocs');
        $this->table='tbl_pbl_assocs';
    }

    /**
     * Method to insert the scene associations for a new case.
     *
     * @param array $fields The table fields and contents to be added to the table
     * @return
     */
    public function addCaseAssocs($fields)
    {
        $this->insert($fields);
    }

    /**
     * Method to get the associated scene or task id (right assoc id).
     *
     * @param string $leftid The case id
     * @param string $lefttype The case association id
     * @param string $righttype The scene association id
     * @return string $rightid The scene id
     */
    public function getAssocId($leftid, $lefttype, $righttype)
    {
        $sql = "select right_assoc_id from tbl_pbl_assocs where left_assoc_id = '" . $leftid . "' and left_assoc_type = '" . $lefttype . "' and right_assoc_type = '" . $righttype
         . "' order by id";
        $rows = $this->getArray($sql);
        echo $leftid;
        print_r($rows);
        if(!empty($rows)){
            $rightid = $rows[0]['right_assoc_id'];
            return $rightid;
        }
        return FALSE;
    }

    /**
     * Method to get the next scene id (left assoc id).
     *
     * @param string $rightid The scene id
     * @param string $lefttype The case association id
     * @param string $righttype The scene association id
     * @return string $leftid The case id
     */
    public function getNextId($rightid, $lefttype, $righttype)
    {
        $sql = "select left_assoc_id from tbl_pbl_assocs where right_assoc_id = '" . $rightid . "' and left_assoc_type = '" . $lefttype . "' and right_assoc_type = '" . $righttype . "'";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        $leftid = $rows[0]['left_assoc_id'];
        return $leftid;
    }

    /**
    * Method to delete an association.
    * @param string $id The id of the association
    * @param string $filter The filter to find the id of the association
    * @return
    */
    public function deleteAssocs($id=NULL,$filter=NULL)
    {
        if($filter){
            $sql = 'SELECT id FROM '.$this->table;
            $sql .= " WHERE $filter";
            $result = $this->getArray($sql);

            if($result){
                foreach($result as $line){
                    $this->delete('id',$line['id']);
                }
            }
        }else{
            $this->delete('id',$id);
        }
    }
}

?>