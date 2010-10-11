<?php
/* ----------- data class extends dbTable for tbl_context_learningoutcomes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_context_learningoutcomes
 * @author Paul Mungai
 * @copyright 2010 University of the Witwatersrand
 */
class dbContext_learneroutcomes extends dbTable
{
    /**
     * The user Object
     *
     * @var object $objUser
     */
    public $objUser;
    /**
     * The user Id
     *
     * @var object $objUserId
     */
    public $objUserId;
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_context_learneroutcomes');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objDBContext = &$this->getObject('dbcontext', 'context');
        $this->objUserId = $this->objUser->userId();
    }
    /**
     * Return all records
     * @param string $userid The User ID
     * @return array The entries
     */
    function listAll($userid) 
    {
        return $this->getAll("WHERE userid='" . $userid . "'");
    }
    /**
     * Return all context records
     * @param string $contextcode The Context Code
     * @return array The learning outcomes
     */
    function getContextOutcomes($contextCode)
    {
        //Add old learner outcome(goals) to existing LO's
        $contextGoal = $this->objDBContext->getField('goals', $contextCode);
        if($contextGoal!="deleted" && $contextGoal!=Null && $contextGoal!=0){
            //Add goal to outcomes table
            $newLO = $this->insertSingle($contextCode, $contextGoal);
            //Remove goal from context table
            $updateContext = $this->objDBContext->updateContext( $contextCode, $title=FALSE, $status=FALSE, $access=FALSE, $about=FALSE, $goals="deleted", $showcomment='Y', $alerts='');
        }
        return $this->getAll("WHERE contextcode='" . $contextCode . "'");
    }
    /**
     * Return formated list of context outcomes
     * @param string $contextcode The Context Code
     * @return Ordered list of The learning outcomes
     */
    function listContextOutcomes($contextCode) 
    {
      //Get all LO
      $getLO = $this->getContextOutcomes($contextCode);
      if(!empty($getLO)) {
        $str = "<ul>";
        $count = 1;
        foreach($getLO as $thisLO){
          $str .= "<li>".$count.". ".$thisLO["learningoutcome"]."</li>";
          $count = $count + 1;
        }
        $str .= "</ul>";
      } else {
        $str = " ";
      }
      return $str;
    }
    /**
     * Return a single record
     * @param string $id ID
     * @return array The values
     */
    function listSingle($id) 
    {
        return $this->getAll("WHERE id='" . $id . "'");
    }
    /**
     * Insert a record
     * @param string $contextcode The Context Code
     * @param string $learningoutcome The Learning Outcome
     */
    function insertSingle($contextCode, $learningoutcome) 
    {
        //Insert Data
        $id = $this->insert(array(
            'contextcode' => $contextCode,
            'learningoutcome' => $learningoutcome,
            'createdby' => $this->objUserId,
            'createdon' => $this->now(),
        ));
        return $id;
    }
    /**
     * Update a record
     * @param string $id The record Id
     * @param string $learningoutcome The Learning Outcome
     */
    function updateSingle($id, $learningoutcome) 
    {
        $this->update("id", $id, array(
            'learningoutcome' => $learningoutcome,
        ));
    }
    /**
     * Delete a record
     * @param string $id ID
     */
    function deleteSingle($id) 
    {
        $this->delete("id", $id);
    }
}
?>
