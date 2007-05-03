<?php
/* ------ data class extends dbTable for all site permissions database tables ------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the site permissions database tables
* @author Kevin Cyster
*/

class xmlperm extends dbTable
{
    /**
    * @var object $objConfig: The altconfig class in the config module
    * @access public
    */
    public $objConfig;
    
    /**
    * @var object $objXML: The simplexml object
    * @access public
    */
    public $objXML;
    
    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        $this->objConfig = &$this->getObject('altconfig','config');
        $this->objConfig = &$this->getObject('altconfig','config');
        $this->objDbperm = $this->newObject('dbpermissions', 'sitepermissions');        
    }

    /**
    * Method to get the permissions xml file
    *
    * @access private
    * @return string $path: The module path
    */
    private function getXmlFile()
    {
        $moduleName = $this->getSession('module_name');
        if(file_exists($this->objConfig->getModulePath()."/$moduleName")){
        	$path = $this->objConfig->getModulePath()."/$moduleName/permissions.xml";
        }else{
        	$path = $this->objConfig->getSiteRootPath().'core_modules/'.$moduleName."/permissions.xml";
        }
        if(file_exists($path)){
        	return $path;
        }else{
        	return FALSE;
        } 
    }
    
    /**
    * Method to read the XML file
    *
    * @access private
    * @return objject $objXML: The permission XML object
    */
    private function readXmlFile()
    {
        $this->objXML = simplexml_load_file($this->getXmlFile());
        return $this->objXML;
    }
    
    /**
    * Method to update the permissions for a module
    *
    * @access public
    * @param string $moduleName: The name of the module to update permissions for
    * @return void
    */
    public function updatePermissions($moduleName = '')
    {
        if($moduleName == ''){
            $moduleName = $this->getSession('module_name');
        }
        $this->objDbperm->deleteModule($moduleName);
        $moduleId = $this->objDbperm->addModule($moduleName);
        $this->readXmlFile();
        $actions = $this->objXML->actions;
        foreach($actions as $action){
            $this->objDbperm->addRule($action, $moduleId);
        }
    }
}
?>