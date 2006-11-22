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
     public function init(){
        parent::init('tbl_contextmodules');
        
        $this->_objDBContext = & $this->newObject('dbcontext', 'context');
    }
    
    /**
    * Method to lookup if
    * a module is visible to
    * the current context
    * @param $moduleId string The moduleId
    * @param $contextCode $string The context Code
    * @return $ret boolean Returns true if an enty was found or false when not found
    * @access public
    * @deprecated 
    */
    public function isVisible($moduleId,$contextCode){
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
    * @deprecated 
    */
    public function setVisible($moduleId,$contextCode){
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
    * @deprecated 
    */
    public function setHidden($moduleId,$contextCode){
        
    
    }
     /**
    * Method to delete all the modules
    * for the context
    * @param $contextCode string : the context code
    * @access public
    * @deprecated 
    */
    public function deleteModulesForContext($contextCode){
        $this->delete('contextCode',$contextCode);    
    }
    
    /**
     * Method to get a list of context sensitive modules
     * @return array
     */
    public public function getInstallableModules()
    {
        
        
    }
    
    /**
     * Method to add a module to a context
     * @param $contextCode The Context Code
     * @return bool
     * @access public
     */
    public function addModule($contextCode, $moduleId)
    {
        $fields = array('contextcode' => $contextCode,
                         'moduleid' => $moduleId);
        return $this->insert($fields);
    }
    
    /**
     * 
     * Method to get a list of modules for a context
     * @param contextCode The Context Code
     * @return array
     * @access public
     */
    public function getContextModules($contextCode)
    {
        
        return $this->getAll('WHERE contextcode="'.$contextCode.'"');
    }
    
    /**
     * Method to save the context modules
     * 
     */
    public function save()
    {
    	try{
	        $contextCode = $this->_objDBContext->getContextCode();
	        $objModules = & $this->newObject('modules', 'modulecatalogue');
	        $objModuleFile = & $this->newObject('modulefile', 'modulecatalogue');
	        $modList = $objModules->getModules(2);
	        //dump all the modules
	        $this->delete('contextcode', $contextCode);
	       
	        foreach ($modList as $module)
	        {
	            
	            if($objModuleFile->contextPlugin($module['module_id']))
	            {//print $module['module_id'];
	                if($this->getParam('mod_'.$module['module_id']) == $module['module_id'])
	                {
	                    
	                    //add to database
	                    $this->addModule($contextCode, $module['module_id']);
	                    
	                    
	                }
	            }
	            
	        }
        
         }                        
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }
    
    
    
 }

?>