<?php
/**
* Class dbClassroom extends dbTable.
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
 * Class for providing access to the classroom table in the database.
 * The table contains information about the pbl classroom.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 1
 */

class dbClassroom extends dbTable
{
    public $entry;
    public $caseid;

    /**
     * var $table contains table name
     */
    private $table = "tbl_pbl_classroom";

    /**
     * Constructor method to define the table and initialise objects
     */
    public function init()
    {
        parent::init('tbl_pbl_classroom');
        // Create instances of the pbl classes objects.
        $this->dbCase = &$this->getObject('dbcases');
        $this->dbAssocs = &$this->getObject('dbassocs');
        $this->dbScenes = &$this->getObject('dbscenes');
        $this->dbType = &$this->getObject('dbtype');
    }

    /**
     * Method to get a list of classrooms associated with a particular case.
     *
     * @param string $context The current context
     * @param string $filter The sql filter. Default=NULL
     * @return array $rows The classroom details
     */
    public function getClasses($context, $filter=NULL)//$id, $filter)
    {
        $class = array();
        $sql = "select * from " . $this->table . " where context='$context'"; //caseid='$id'";
        $sql .= $filter;
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        return $rows;
    }

    /**
     * Method to get information for a specific classroom where id=$clid.
     *
     * @param string $clid The id of the classroom
     * @return array $class Array containing the classroom information
     */
    public function getClass($clid)
    {
        $class = array();
        $sql = 'select caseid,name,chair,scribe,facilitator,status,opentime from ' . $this->table . " where id='$clid'";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        $class['id'] = $clid;
        $class['caseid'] = $rows[0]['caseid'];
        $class['name'] = $rows[0]['name'];
        $class['chair'] = $rows[0]['chair'];
        $class['scribe'] = $rows[0]['scribe'];
        $class['facilitator'] = $rows[0]['facilitator'];
        $class['status'] = $rows[0]['status'];
        $class['opentime'] = $rows[0]['opentime'];
        return $class;
    }

    /**
     * Method to get the name of a classroom
     *
     * @param string $ filter The sql filter on the request
     * @return array $row Array containing the id, name and start time of the classroom
     */
    public function getName($filter)
    {
        $sql = "select id,name,caseid,opentime from " . $this->table;
        if ($filter){
            $sql .= " where $filter";
        }
        $row = $this->getArray($sql);
        if (!$row){
            return FALSE;
        }else{
            return $row;
        }
    }

    /**
     * Method to get the chair and facilitator assigned to a specific classroom.
     * Method sets the chair session variable if assigned in classroom and the facilitator is virtual
     *
     * @return bool True if set, false if the classroom doesn't exist
     */
    public function setChair()
    {
        $sesClass = $this->getSession('classroom');
        $sesPblUser = $this->getSession('pbl_user_id');

        $sql = "select chair,facilitator from " . $this->table . " where id='" . $sesClass . "'";
        $row = $this->getArray($sql);
        if (!$row){
            return FALSE;
        }
        if ($row[0]['chair'] != 'none' && $row[0]['chair'] == $sesPblUser) {
            $this->setSession('chair', TRUE);
            return TRUE;
        }
        if($row[0]['chair'] != 'none'){
            $this->setSession('chair', FALSE);
        }
        return TRUE;
    }

    /**
     * Method to insert a new classroom into the table or update an existing classroom.
     *
     * @param string $filter Default = NULL if classroom doesn't exist, else contains classroom id
     * @param array $fields The table fields to insert
     * @return string $id The classroom id.
     */
    public function saveClass($fields, $filter = NULL)
    {
        if (!$filter) {
            $id = $this->insert($fields);
            return $id;
        } else {
            $sql = 'SELECT id FROM '.$this->table;
            $sql .= " WHERE $filter";
            $result = $this->getArray($sql);

            if(!empty($result)){
                $this->update('id',$result[0]['id'],$fields);
                return $result[0]['id'];
            }
        }
    }

    /**
    * Method to update a classroom.
    * @param array $fields The table fields to insert
    * @param string $id The classroom id.
    */
    public function updateClass($fields, $id)
    {
        $this->update('id', $id, $fields);
    }

    /**
     * Method to delete a classroom where id=$id.
     *
     * @param string $id The classroom id
     * @return
     */
    public function deleteClass($id)
    {
        $this->delete('id',$id);
    }

    /**
     * Method to check if a specific classroom is active where id=$id.
     *
     * @param string $id The id of the classroom
     * @return string $rows The current scene in the case.
     */
    public function isActive($id)
    {
        $sql = "select caseid,activescene from " . $this->table . " where id='$id'";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        $caseid = $rows[0]['caseid'];
        $this->setSession('caseid', $caseid);
        return $rows[0]['activescene'];
    }

    /**
     * Method to check if the current scene is the active scene.
     * If it is different then return the new active scene.
     *
     * @param string $id The id of the classroom
     * @param string $scene The id of the current scene in the case
     * @return string $rows The active scene in the case.
     */
    public function checkActive($scene, $id)
    {
        $sql = "select activescene from " . $this->table . " where id='$id'";
        $row = $this->getArray($sql);
        if (!$row){
            return FALSE;
        }
        if ($scene == $row[0]['activescene']){
            return FALSE;
        }
        return $row[0]['activescene'];
    }

    /**
     * Method to get the entry point into the case associated with the current classroom.
     *
     * @return string $id The id of the first scene in the case
     */
    public function getEntryPoint()
    {
        // get case id from dbclassroom
        $sesClass = $this->getSession('classroom');
        $row = $this->getClass($sesClass);
        if (!$row) {
            // redirect to index page if no entry point
            Header('Location: '.$this->uri(array('')));
        }
        $this->caseid = $row['caseid'];

        if (empty($this->caseid)){
            Header('Location: '.$this->uri(array('')));
        }
        $this->setSession('caseid', $this->caseid);
        // get entry point into case
        $row = $this->dbCase->getEntry($this->caseid);
        $this->entry = $row['entry_point'];
         $id = $this->entry;
        $fields = array();
        if (!$this->testClassroom($this->caseid)) {
            $fields['caseid'] = $this->caseid;
            $fields['activescene'] = $id;
            $this->insert($fields);
        } else {
            $fields['activescene'] = $id;
            $this->update('id', $sesClass, $fields);
        }
        // return entry point
        return $id;
    }

    /**
     * Method to test if a classroom exists.
     *
     * @param string $id The case id
     * @return bool $isthere TRUE if classroom exists for selected case, otherwise FALSE
     */
    public function testClassroom($id)
    {
        $sql = "select id from " . $this->table . " where caseid ='" . $id . "'";
        $isthere = TRUE;
        $row = $this->getArray($sql);
        if (!$row){
            $isthere = FALSE;
        }
        return $isthere;
    }

    /**
     * Method to get the classroom id using the case id.
     *
     * @param string $cid The case id. Default = NULL
     * @return string $id The classroom id
     */
    public function getClassroom($cid = NULL)
    {
        if (!$cid){
            $cid = $this->caseid;
        }
        $sql = "select id from " . $this->table . " where caseid='" . $cid . "'";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        //$id = $rows[0]['id'];
        return $rows;
    }

    /**
     * Method to set the active scene id in the current classroom.
     *
     * @param string $id The scene id
     * @return
     */
    public function setActiveSceneId($id)
    {
        $sesClass = $this->getSession('classroom');
        $fields = array();
        $fields['activescene'] = $id;

        $this->update('id', $sesClass, $fields);
    }

    /**
     * Method to get the active scene id for the current classroom.
     *
     * @return string $id The active scene id
     */
    public function getActiveSceneId()
    {
        $sesClass = $this->getSession('classroom');
        $sql = "select activescene from " . $this->table . " where id='" .$sesClass. "'";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        $id = $rows[0]['activescene'];
        return $id;
    }

    /**
     * Method to get the id of the next scene in the case.
     *
     * @param string $currentSceneId The id of the current scene of the case
     * @return string $id The scene id
     */
    public function getNextSceneId($currentSceneId)
    {
        $sceneassocid = $this->dbType->getId("scene");
        $minfoassocid = $this->dbType->getId("minfo");
        $id = $this->dbAssocs->getNextId($currentSceneId, $minfoassocid, $sceneassocid);
        if (!$id){
            return FALSE;
        }
        return $id;
    }

    /**
     * Method to get the content of the scene for display.
     *
     * @param string $colname The database field to search in
     * @param string $colval The value to search for
     * @return string $display The display
     */
    public function getSceneUI($colname, $colval)
    {
        if ($colname == "name"){
            $filter = "name='" . $colval . "'";
        }else if ($colname == "id"){
            $filter = "id='" . $colval . "'";
        }
        $display = $this->dbScenes->getDisplay($filter);
        if (!$display){
            return FALSE;
        }
        return $display;
    }

    /**
     * Method to get the tasks id associated with current scene of the case.
     *
     * @param string $currentSceneId The id of the current scene
     * @return string $id The task id
     */
    public function getNextTaskId($currentSceneId)
    {
        $sceneassocid = $this->dbType->getId("scene");
        $taskassocid = $this->dbType->getId("task");
        $id = $this->dbAssocs->getNextId($currentSceneId, $taskassocid, $sceneassocid);
        if (!$id){
            return FALSE;
        }
        return $id;
    }
}

?>