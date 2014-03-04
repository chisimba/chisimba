<?php
/**
* Class dbScenes extends dbTable.
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
 * Class for providing access to the scenes table in the database.
 * The table contains the display scenes and tasks for a case.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 1
 */

class dbScenes extends dbTable
{
    /**
    * @var string $table The table name
    * @access private
    */
    private $table;
    
    /**
     * Constructor method to define the table and initialise objects.
     */
    public function init()
    {
        parent::init('tbl_pbl_scenes');
        $this->table='tbl_pbl_scenes';
    }

    /**
     * Method to add a new scene.
     *
     * @param associative $ array $fields Array containing the table column names and the values to be added
     * @return
     */
    public function addScene($fields)
    {
        if($this->insert($fields)){
            $id = $this->getLastInsertId();
            return $id;
        }
        return FALSE;
    }

    /**
     * Method to get the scene id for a scene.
     *
     * @param string $name Name of scene
     * @param string $caseid The id of the case
     * @return string $id The scene id
     */
    public function getId($name, $caseid)
    {
        $sql = "select id from ".$this->table." where name='" . $name . "' and caseid='" . $caseid . "'";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        return $rows[0]['id'];
    }

    /**
     * Method to get the scene content for display to the user.
     *
     * @param string $filter Determines which scenes to retrieve
     * @return string $display The display scene
     */
    public function getDisplay($filter)
    {
        $sql = "select display from ".$this->table;
        $sql .= "  where $filter";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        foreach($rows as $row){
            $display = $row['display'];
        }
        return $display;
    }

    /**
     * Method to delete a scene.
     *
     * @param string $id Scene id
     * @param string $case Name of case
     * @return
     */
    public function deleteScene($id=NULL, $caseid=NULL)
    {
        if($caseid){
            $sql = 'SELECT id FROM '.$this->table;
            $sql .= " WHERE caseid='" . $caseid . "'";
            $result = $this->getArray($sql);

            if($result){
                foreach($result as $line){
                    $this->delete('id',$line['id']);
                }
            }
        }else {
            $this->delete('id',$id);
        }
    }
}

?>