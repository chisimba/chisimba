<?php
/**
* Class textParser extends object.
* @package pbladmin
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die('You cannot view this page directly');
}
// end security check

 /**
 * Class for parsing a text file containing a pbl case
 * This class should provide functionality to parse a .txt file containing
 * a given problem
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbladmin
 * @version 0.9
 */

class textParser extends object
{
    public $caseid;

    /**
     * Constructor method to initialise objects
     */
    public function init()
    {
        $this->dbCase = &$this->getObject('dbcases', 'pbl');
        $this->dbAssocs = &$this->getObject('dbassocs', 'pbl');
        $this->dbScenes = &$this->getObject('dbscenes', 'pbl');
        $this->dbType = &$this->getObject('dbtype', 'pbl');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objContext = &$this->getObject('dbcontext', 'context');
    }

    /**
    * Method to load and parse a specified text file
    * @param string $filename The pbl file to parse
    * @return bool
    */
    public function parseText($filename)
    {
        if(!file_exists($filename)){
            return FALSE;
        }
        $file = file($filename);

        if(!(strpos($file[0], 'CASE:')===FALSE)){
            $casename = substr($file[0], 5);

            if(!(strpos($casename, '[Type') === FALSE)){
                $start = strpos($casename, '[Type');
                $end = strpos($casename, ']');
                substr_replace($casename, '', $start, $end-$start);
            }
            if($casename == ''){
                $casename = 'case'.rand(99);
            }
            $scene[$i]['case'] = $casename;
        }

        $scene = array(); $i = 0; $type = 'case'; $add = FALSE;
        foreach($file as $key=>$line){
            if(!(strpos($line, '[Type') === FALSE)){
                $start = strpos($line, '[Type');
                $end = strpos($line, ']');
                $line = substr_replace($line, '', $start, $end-$start+1);
            }
            if(!(strpos($line, '[To add') === FALSE)){
                $start = strpos($line, '[To add');
                $end = strpos($line, ']');
                $line = substr_replace($line, '', $start, $end-$start+1);
            }
            if(strlen($line) > 1){
                if(!(strpos($line, 'SCENE:')===FALSE)){
                    $type = 'scene';
                    $add = FALSE;
                    $i++;
                }
                if(!(strpos($line, 'TASK:')===FALSE)){
                    $type = 'task';
                    $add = FALSE;
                }
                if($add){
                    if(strlen($line) > 1){
                        $scene[$i][$type] = $scene[$i][$type].$line;
                    }else{
                        $i--;
                    }
                }else{
                    $add=TRUE;
                }
            }
        }

        // Save case name and first scene
        $owner = ''; $context = ''; $caseData = array(); $minfo = '';
        $owner = $this->objUser->userId();
        $context = $this->objContext->getContextCode();
        $sceneData = array();
        $sceneData['scenename'] = 'scene0';
        $sceneData['scene'] = $scene[1]['scene'];
        $sceneData['task'] = $scene[1]['task'];
        $caseData = $this->createCase($owner, $context, $casename, $sceneData);
        $minfo = $caseData['sceneid'];

        // Save the rest of the case
        foreach($scene as $k=>$line){
            if($k != 0 && $k != 1){
                $sceneData = array();
                $sceneData['scenename'] = 'scene'.$k;
                $sceneData['scene'] = $line['scene'];
                $sceneData['task'] = $line['task'];
                $sceneData['minfo'] = $minfo;
                $minfo = $this->setSceneAssocs($caseData['caseid'], $sceneData);
            }
        }
        return TRUE;
    }


    /**
    * Method to create a new case in the database and save the first scene in the case.
    * @param string $owner The user creating the case
    * @param string $context The current context
    * @param string $casename The name of the case. Default=NULL
    * @param array $sceneData The information about the scene. Default=NULL
    * @return string $sceneid The id of the first scene in the case.
    */
    public function createCase($owner, $context, $casename=NULL, $sceneData=NULL)
    {
        if(!$casename){
            $casename = $this->getParam('casename');
        }
        // Save case and get id
        $caseid = $this->dbCase->addCase($casename, '0', $owner, $context);

        // Save first scene and get id
        if(!$sceneData){
            $sceneid = $this->setSceneAssocs($caseid);
        }else{
            $sceneid = $this->setSceneAssocs($caseid, $sceneData);
        }

        // update case with id
        $fields=array();
        $fields['entry_point'] = $sceneid;
        $this->dbCase->updateCase($caseid, $fields);

        $result=array('sceneid'=>$sceneid, 'casename'=>$casename, 'caseid'=>$caseid);
        return $result;
    }

    /**
    * Method to set up scene associations.
    * @param string $caseid The id of the current case.
    * @param array $sceneData The information about the scene. Default=NULL
    * @return string $sceneid The id of the current scene in the case.
    */
    public function setSceneAssocs($caseid, $sceneData = NULL)
    {
        if(!$sceneData){
            $sceneData = $_POST;
        }
        // save scene & return the id
        $fields=array();
        $fields['caseid'] = $caseid;
        $fields['name'] = $sceneData['scenename'];
        $fields['display'] = $sceneData['scene'];
        $sceneid = $this->dbScenes->addScene($fields);

        // set scene associations
        $caseassocid = $this->getAssocTbId('case');
        $sceneassocid = $this->getAssocTbId('scene');
        $this->addSceneAssocs($caseid, $caseassocid, $sceneid, $sceneassocid, $caseid);

        // Check for a task and save it
        if(!empty($sceneData['task'])){
            $fields=array();
            $fields['caseid'] = $caseid;
            $fields['name'] = $sceneData['scenename'].'task';
            $fields['display'] = $sceneData['task'];
            $taskid = $this->dbScenes->addScene($fields);

            // set task associations
            $taskassocid = $this->getAssocTbId('task');
            $this->addSceneAssocs($taskid, $taskassocid, $sceneid, $sceneassocid, $caseid);
        }

        // Check for the previous scene and set the current scene as the next scene in line after it.
        if(!empty($sceneData['minfo'])){
            $minfoid = $sceneData['minfo'];
            $minfoassocid = $this->getAssocTbId('minfo');
            $this->addSceneAssocs($sceneid, $minfoassocid, $minfoid, $sceneassocid, $caseid);
        }
        return $sceneid;
    }

    /**
    * Method to insert scene associations directly into the database.
    * @param string $leftid The left association id.
    * @param string $lefttype The type of the left association id (task, scene, minfo).
    * @param string $rightid The right association id.
    * @param string $righttype The type of the right association id (task, scene, minfo).
    * @param string $caseid The id of the new case.
    * @return
    */
    private function addSceneAssocs($leftid, $lefttype, $rightid, $righttype, $caseid)
    {
        $fields = array();
        $fields['left_assoc_id'] = $leftid;
        $fields['left_assoc_type'] = $lefttype;
        $fields['right_assoc_id'] = $rightid;
        $fields['right_assoc_type'] = $righttype;
        $fields['cid'] = $caseid;
        $this->dbAssocs->addCaseAssocs($fields);
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
}
?>