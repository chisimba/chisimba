<?php
/**
* dbProcess class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbProcess class for managing the data in the tbl_etd_process table.
* The table contains the steps of the submission process.
*
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class dbProcess extends dbtable
{
    /**
    * Constructor
    */
    public function init()
    {
        try{
            parent::init('tbl_etd_process');
            $this->table = 'tbl_etd_process';
            $this->objUser = $this->getObject('user', 'security');
        }catch(Exception $e){
            throw customException($e->message());
        }
    }

    /**
    * Method to save a step in the submission process
    * The steps available are: submit to supervisor for approval; - to do
    * submit to external examiners; - to do
    * submit to examination board (persons to be selected); 
    * submit for metadata editing;
    * submit to manager for final approval and archiving to repository.
    *
    * @access public
    * @param string $step The step to be added
    * @return void
    */
    public function addStep($stepId)
    {
        $fields = array();
        $fields['is_active'] = '1';
        $this->update('id', $stepId, $fields);
    }
    
    /**
    * Method to get the next step in the submission process
    *
    * @access public
    * @param integer $step The current step number.
    * @return integer $next The next step number.
    */
    public function getNextStep($step)
    {
        $sql = "SELECT * FROM {$this->table} 
        WHERE step_num > '{$step}' AND is_active = '1' 
        ORDER BY step_num ASC";
        
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0]['step_num'];
        }
        return $step;
    }

    /**
    * Method to get the current process configuration
    *
    * @access public
    * @return array $data The steps
    */
    public function getSteps()
    {
        $sql = "SELECT * FROM {$this->table}
        ORDER BY step_num";
        
        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * Method to remove an process step.
    * 
    * @access public
    * @param string $step The step to be removed
    * @return void
    */
    public function removeStep($stepId)
    {
        $fields = array();
        $fields['is_active'] = '0';
        $this->update('id', $stepId, $fields);
    }
}
?>