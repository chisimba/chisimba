<?php
/* ------------------- modules class extends dbTable ------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

/**
* The class representing the modules table, handling all non-administrative
* operations on the table.
* @see modulesadmin class for administrative operations
* @author Nic Appleby
* @author Derek Keats 
* @author Sean Legassick
* @author Jeremy O'Connor
$Id$ 
*/

// Constants for $getType parameter of getModules

class modules extends dbTable
{
    const GET_ALL = 1;
    const GET_VISIBLE = 2;
    const GET_USERVISIBLE = 3;

    private $objLanguage;
    //private $objConfig;
    public $objConfig;

    public function init()
    {
        parent::init('tbl_modules');
        //Config and Language Objects
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->objConfig =& $this->getObject('altconfig','config');
    } 

    /**
    * This method retrieves  all existing modules by querying the database
    * table tbl_modules. If the user is an administrator, it 
    * returns all visible modules, otherwise it returns all modules
    * where the value of isAdmin is FALSE. It only returns modules
    * where the value of isVisible is TRUE, thus modules can be
    * used to provide functionality to other modules without needing
    * to have a user interface. Modules that do not need to be visible
    * are thus not exposed to the user.
    * @param  $gettype int Type of request
    * @return array List of modules
    */
    public function getModules($getType)
    { 
        // DEREK: This is a pretty shaky method. Can anyone find a better way?
        // SEAN: I've changed things so that we do two joins on tbl_languagetexts
        // and thus everything for a module comes back in one row
        // I think this is better...?
        /* $sql="SELECT tbl_modules.module_id, tbl_modules.module_path, 
				tbl_languagetexts_name.id as nameId, tbl_languagetexts_name.English as name, 
                tbl_languagetexts_desc.id as descId, tbl_languagetexts_desc.English as description
				FROM tbl_modules 
                INNER JOIN bridge_lang_to_mod
				ON tbl_modules.module_id = bridge_lang_to_mod.moduleId 
				INNER JOIN tbl_languagetexts AS tbl_languagetexts_name ON 
				bridge_lang_to_mod.codeName = tbl_languagetexts_name.code 
                INNER JOIN tbl_languagetexts AS tbl_languagetexts_desc ON
                bridge_lang_to_mod.codeDesc = tbl_languagetexts_desc.code ";
         */
        // JAMES: The triple join is no longer needed. Only tbl_modules and tbl_languagetexts are needed.
        switch ($getType) {
            case GET_USERVISIBLE:
                $filter = "WHERE tbl_modules.isVisible=1 AND tbl_modules.isAdmin!=1 ";
                break;
            case GET_VISIBLE:
                $filter = "WHERE tbl_modules.isVisible=1 ";
                break;
            case GET_ALL:
            	$filter = '';
                break;
            default:
                die("Invalid getType in modules::getModules");
        } 
        $filter .= "ORDER BY module_id";
        $modules = $this->getArray($sql);
        $_modules = array();
        foreach ($modules as $module) {
            $_module = array();
            $_module['module_id'] = $module['module_id'];
            $_module['module_path'] = $module['module_path'];
            $_module['title'] = $this->objLanguage->languagetext('mod_' . $module['module_id'] . '_name');
            $_module['description'] = $this->objLanguage->languagetext('mod_' . $module['module_id'] . '_desc');
            $_modules[] = $_module;
        }
        return !empty($_modules) ? $_modules : FALSE;
    } 

    /**
    * Method to check if a module is Admin-only or not.
    * @param string $moduleId
    * @return Boolean TRUE|FALSE
    */
    public function isAdminModule($moduleId)
    {
        $row=$this->getRow('module_id',$moduleId);
        return !empty($row) ? $row['isAdmin'] : FALSE;
    }

    /**
    * This is a method to check if the module is registered already.
    * Returns TRUE if the module is registered and FALSE if
    * it is not registered.
    * @param string $moduleId The identifier of the module.
    * @return boolen TRUE|FALSE
    */
     public function checkIfRegistered($moduleId) 
     {
        $row = $this->getRow('module_id',$moduleId);
        return !empty($row);
     }
     
    /** 
    * This method returns the version of a module in the database 
    * ie: The version level of the emodule at the time it was registered.  
    * @param string $module the module to lookup 
    * @return string $version the version in the database | FALSE
    */ 
    public function getVersion($moduleId)
    {
        $row=$this->getRow('module_id',$moduleId);
        return !empty($row) ? $row['module_version'] : FALSE;
    }
    
    /**
     * Method to return the dependents of a module
     * @param string $moduleId the module to check
     * @return mixed the modules dependents
     */
    public function getDependencies($moduleId) {
    	$sql = "SELECT module_id FROM tbl_modules_dependencies WHERE dependency='$moduleId'";
        $rs = $this->getArray($sql);
        $dep = array();
        foreach ($rs as $rec) {
        	$dep[] = $rec['module_id'];
         }
        return $dep;
    }
    
    /**
    * This is a method to check if a module is registered and turn the result as an array
    * @param string $moduleId
    * @returns array $result
    */
    public function getModuleInfo($moduleId) {
        if ($this->checkIfRegistered($moduleId)){
            $result = array('isreg'=>TRUE,
                'name'=>$this->objLanguage->code2Txt('mod_'.$moduleId.'_name'));
        } else {
            $result = array('isreg'=>FALSE,'name'=>'');
        }
        return $result;
    }
}
?>