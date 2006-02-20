<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

 /**
 * Class to access the ContextModules Tables 
 * @package context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @author Wesley  Nitsckie
 * @version $Id$ 
 **/
class dbcontextmodules extends dbTable{
     /**
    *Initialize by send the table name to be accessed 
    */
     function init(){
        parent::init('tbl_contextmodules');
    }
    
    /**
    * Method to lookup if
    * a module is visible to
    * the current context
    * @param $moduleId string The moduleId
    * @param $contextCode $string The context Code
    * @return $ret boolean Returns true if an enty was found or false when not found
    * @access public
    */
    function isVisible($moduleId,$contextCode){
        $rsArr=$this->getAll("WHERE contextCode = '".$contextCode."' AND moduleId='".$moduleId."'");
        $ret=true;
        if ($rsArr){
            foreach($rsArr as $ar)
            {
                $ret=(isset($ar['moduleId']))? true:false; 
            }
        }    
        else
        {
            $ret=false;
        }        
        return $ret;
    }
    
    /**
    * Method to make a
    * module available to a context 
    * @param $moduleId string: The moduleId
    * @param $contextCode $string : The context Code
    * @return string : The new Id
    * @access public
    */
    function setVisible($moduleId,$contextCode){
        return $this->insert(array(
                'moduleId' => $moduleId,
                'contextCode' => $contextCode));    
    }
    
    /**
    * Method to make a module
    * unavailable to a context
    * @param $moduleId string: The moduleId
    * @param $contextCode $string : The context Code
    * @access public
    */
    function setHidden($moduleId,$contextCode){
        
    
    }
     /**
    * Method to delete all the modules
    * for the context
    * @param $contextCode string : the context code
    * @access public
    */
    function deleteModulesForContext($contextCode){
        $this->delete('contextCode',$contextCode);    
    }
 }

?>