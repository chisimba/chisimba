<?php
/**
* Class pblParser extends object.
* @package pbladmin
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die('You cannot view this page directly');
}
// end security check

 /**
 * Class for parsing an xml file containing a pbl case
 * This class should provide functionality to parse a .pbl file containing
 * a given problem
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbladmin
 * @version 0.9
 */

class pblParser extends object
{
    public $instrs = array();
    public $ip = 0, $ni = 0;
    public $caseid;

    /**
     * Constructor method to initialise objects
     */
    public function init()
    {
        $this->instruction = &$this->getObject('instruction');
        $this->parser = &$this->getObject('xmlparser');
        $this->dbCase = &$this->getObject('dbcases', 'pbl');
        $this->dbChat = &$this->getObject('dbchat', 'pbl');
        $this->dbClassroom = &$this->getObject('dbclassroom', 'pbl');
        $this->dbAssocs = &$this->getObject('dbassocs', 'pbl');
        $this->dbScenes = &$this->getObject('dbscenes', 'pbl');
        $this->dbType = &$this->getObject('dbtype', 'pbl');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objContext = &$this->getObject('dbcontext', 'context');
    }

    /**
    * Method to call the xml parser to parse the pbl case.
    *
    * @param string $filename The name of the xml file to parse.
    * @return
    */
    public function parse($filename)
    {
        $this->instrs = $this->parser->parse($filename);
        $this->ni = $this->parser->ni;
        $this->dbRender();
        return TRUE;
    }

    /**
     * Method to uninstall a case identified by $id.
     *
     * @param string $name The name of the case
     * @param string $id The id of the case
     * @return bool true if uninstalled
     */
    public function unInstallCase($name, $id)
    {
        $classes = $this->dbClassroom->getClassroom($id);

        if(!empty($classes)){
            foreach($classes as $item){
                $fields['caseid'] = '';
                $this->dbClassroom->updateClass($fields, $item['id']);
            }
        }
        // delete associations between scenes in the case
        $this->dbAssocs->deleteAssocs(NULL, "cid = '{$id}'");
        
        // delete scenes in the case
        $this->dbScenes->deleteScene(NULL, $id);
        
        // delete the case
        $this->dbCase->deleteCase($id);

        $this->unsetSession('caseid');
        
        return TRUE;
    }

    /**
     * Method to save the attributes of the case in the database.
     * @return
     */
    private function saveCaseAttrs()
    {
        $casename = $this->instrs[0]->getAttrVal("name");
        $startsceneid = '0';
        $owner = $this->objUser->userId();
        if ($this->objContext->isInContext()){
            $context = $this->objContext->getContextCode();
        }else{
            $context = 'lobby';
        }
        
        $this->caseid = $this->dbCase->addCase($casename, $startsceneid, $owner, $context);
    }


    /**
     * Method to save the attributes of the case.
     *
     * @param string $caseid The id of the case
     * @return
     */
    private function saveDisplay($index, $caseid)
    {
        if ($this->instrs[$index]->attrs) {
            $name = $this->instrs[$index]->getAttrVal("id");
            $display = $this->instrs[$index]->getAttrVal("display");
            if ($name == -1 || $display == -1){
                return FALSE;
                //die("error in pbl file: scene with either no id or not display attributes on
                //scene name = " . $name . " and display=" . $display);
            }
            $fields = array();
            // change
            $fields['caseid'] = $caseid;
            $fields['name'] = $name;
            $fields['display'] = $display;
            $this->dbScenes->addScene($fields);
        }else{
            if ($this->name == "scene") {
                return FALSE;
            }
        }
    }

    /**
    * Method to save the first scene in the case as the entry point.
    * @return
    */
    private function saveEntryPoint()
    {
        $casestart = $this->instrs[0]->getAttrVal("start");
        $startsceneid = $this->getSceneTbId($casestart, $this->caseid);
        $fields=array();
        $fields['entry_point']=$startsceneid;
        $this->dbCase->updateCase($this->caseid, $fields);
    }

    /**
     * Method to set scene associations.
     * @return
     */
    private function learnBehaviour()
    {
        // first associate scene with case
        $casename = $this->instrs[0]->getAttrVal("name");
        $instrlst = $this->instrs[$this->ip]->getInstructions();
        $scenename = $this->instrs[$this->ip]->getAttrVal("id");
        $sceneid = $this->getSceneTbId($scenename, $this->caseid);
        $caseassocid = $this->getAssocTbId("case");
        $sceneassocid = $this->getAssocTbId("scene");
        $assocs = array();
        $assocs['left_assoc_id'] = $this->caseid;
        $assocs['left_assoc_type'] = $caseassocid;
        $assocs['right_assoc_id'] = $sceneid;
        $assocs['right_assoc_type'] = $sceneassocid;
        $assocs['cid'] = $this->caseid;
        $this->dbAssocs->addCaseAssocs($assocs);

        $minfoassocid = $this->getAssocTbId("minfo");
        $taskassocid = $this->getAssocTbId("task");

        // now associate current scene nested instructions
        foreach($instrlst as $instr) {
            switch ($this->instrs[$instr]->name) {
                case "minfo":
                    $v = $this->instrs[$instr]->getAttrVal("id");
                    $minfoid = $this->getSceneTbId($v, $this->caseid);
                    $assocs['left_assoc_id'] = $minfoid;
                    $assocs['left_assoc_type'] = $minfoassocid;
                    $assocs['right_assoc_id'] = $sceneid;
                    $assocs['right_assoc_type'] = $sceneassocid;
                    $assocs['cid'] = $this->caseid;
                    $this->dbAssocs->addCaseAssocs($assocs);
                    break;
                case "task":
                    $v = $this->instrs[$instr]->getAttrVal("id");
                    $taskid = $this->getSceneTbId($v, $this->caseid);
                    $assocs['left_assoc_id'] = $taskid;
                    $assocs['left_assoc_type'] = $taskassocid;
                    $assocs['right_assoc_id'] = $sceneid;
                    $assocs['right_assoc_type'] = $sceneassocid;
                    $assocs['cid'] = $this->caseid;
                    $this->dbAssocs->addCaseAssocs($assocs);
                    break;
            }
        }
    }

    /**
     * Method to get an instruction pointer to next scene to process.
     * i.e., search for scene from position $ip till end of instructions buffer
     * @param string $ip The index to start searching from
     * @return string $i The index of the instruction
     */
    private function sceneFound($ip)
    {
        for($i = $ip;$i < $this->ni;$i++) {
            if ($this->instrs[$i]->name == "scene") {
                $this->ip = $i;
                return $i;
            }
        }
        return -1;
    }

    /**
     * Method to get the id of current case from pbl_cases table.
     * @return
     */
    private function getCaseTbId()
    {
        if ($this->instrs[0]->name != "case") {
            return FALSE;
        }
        $name = $this->instrs[0]->getAttrVal("name");
        $rows = array();
        $rows = $this->dbCase->getId($name);
        $this->caseid = $rows[0]['id'];
    }

    /**
     * Method to get the id for a requested scene from pbl_scenes table
     *
     * @param string $name The name of the scene
     * @param string $case The name of the case
     * @return string $id The scene id
     */
    private function getSceneTbId($name, $caseid)
    {
        $id = $this->dbScenes->getId($name, $caseid);
        return $id;
    }

    /**
     * Method to get the id for a specified association type.
     *
     * @param string $name The name of the scene type
     * @return string $id The type id
     */
    private function getAssocTbId($name)
    {
        $id = $this->dbType->getId($name);
        return $id;
    }

    /**
     * Method to delete an old scene from the database table.
     *
     * @param string $id The id of the scene
     * @param string $case The name of the case
     * @return
     */
    private function delOldScene($id, $case)
    {
        $this->dbScenes->deleteScene($id, $case);
    }

    /**
     * Method to render the case to the database.
     * @return
     */
    private function dbRender()
    {
        $this->ip = 0;
        $case = $this->instrs[0]->getAttrVal("name");
        $this->saveCaseAttrs();

        // first save all scenes to create scene id in the database
        while ($this->sceneFound($this->ip) != -1) {
            //$this->instrs[$this->ip]->saveDisplay($this->caseid);
            $this->saveDisplay($this->ip, $this->caseid);
            $this->ip++;
        }
        $this->saveEntryPoint();
        //$this->getCaseTbId();
        $this->ip = 0;
        // first save all scenes to create scene id in the database
        while ($this->sceneFound($this->ip) != -1) {
            // now save attributes and create associations
            $this->learnBehaviour();
            $this->ip++;
        }
    }
}
?>