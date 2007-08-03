<?php
/* ------------------- modules class extends dbTable ------------- */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* The class representing the modules table, handling all non-administrative
* operations on the table.
* @see      modulesadmin class for administrative operations
* @author   Nic Appleby
* @author   Derek Keats
* @author   Sean Legassick
* @author   Jeremy O'Connor
* @category Chisimba
* @package  Modulecatalogue
* @version  $Id$
*/

// Constants for $getType parameter of getModules

class modules extends dbTable
{
    //no more constants
	//const GET_ALL = 1;
    //const GET_VISIBLE = 2;
    //const GET_USERVISIBLE = 3;


    /**
     * Description for private
     * @var    object 
     * @access private
     */
    private $objLanguage;
    //private $objConfig;


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
    public $objConfig;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
    public $output;

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    public function init() {
    	try {
    		parent::init('tbl_modules');
    		//Config and Language Objects
    		$this->objLanguage =& $this->getObject('language', 'language');
    		$this->objConfig =& $this->getObject('altconfig','config');
    	} catch (Exception $e) {
    		echo customException::cleanUp('Caught exception: '.$e->getMessage());
    		exit();
    	}
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
    * @return array    List of modules
    */
    public function getModules($getType) {
    	try {
    		switch ($getType) {
    			case 3:
    				$filter = "WHERE isVisible=1 AND isAdmin!=1 ";
    				break;
    			case 2:
    				$filter = "WHERE isVisible=1 ";
    				break;
    			case 1:
    				$filter = '';
    				break;
    			default:
    				throw new customException("Invalid getType in modules::getModules");
    		}
    		$filter .= "ORDER BY module_id";
    		$modules = $this->getAll($filter);
    		$_modules = array();
    		foreach ($modules as $module) {
    			$_module = array();
    			$_module['module_id'] = $module['module_id'];
    			$_module['module_path'] = $module['module_path'];
    			$_module['title'] = $this->objLanguage->languagetext('mod_' . $module['module_id'] . '_name',$module['module_id']);
    			$_module['description'] = $this->objLanguage->languagetext('mod_' . $module['module_id'] . '_desc',$module['module_id']);
    			$_modules[] = $_module;
    		}
    		return !empty($_modules) ? $_modules : FALSE;
    	} catch (Exception $e) {
    		echo customException::cleanUp('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * Method to check if a module is Admin-only or not.
    * @param  string  $moduleId
    * @return Boolean TRUE|FALSE
    */
    public function isAdminModule($moduleId) {
    	try {
    		$row=$this->getRow('module_id',$moduleId);
    		return !empty($row) ? $row['isAdmin'] : FALSE;
    	} catch (Exception $e) {
    		echo customException::cleanUp('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * This is a method to check if the module is registered already.
    * Returns TRUE if the module is registered and FALSE if
    * it is not registered.
    * @param  string $moduleId The identifier of the module.
    * @return boolen TRUE|FALSE
    */
     public function checkIfRegistered($moduleId) {
     	try {
     		$row = $this->getRow('module_id',$moduleId);
     		return !empty($row);
     	} catch (Exception $e) {
     		echo customException::cleanUp('Caught exception: '.$e->getMessage());
     		exit();
     	}
     }

    /**
    * This method returns the version of a module in the database
    * ie: The version level of the emodule at the time it was registered.
    * @param  string $module the module to lookup
    * @return string $version the version in the database | FALSE
    */
    public function getVersion($moduleId)
    {
    	try {
    		$row=$this->getRow('module_id',$moduleId);
    		return !empty($row) ? $row['module_version'] : FALSE;
    	} catch (Exception $e) {
    		echo customException::cleanUp('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
     * Method to return the dependents of a module
     * @param  string $moduleId the module to check
     * @return mixed  the modules dependents
     */
    public function getDependencies($moduleId) {
    	try {
    		$sql = "SELECT module_id FROM tbl_modules_dependencies WHERE dependency='$moduleId'";
    		$rs = $this->getArray($sql);
    		$dep = array();
    		foreach ($rs as $rec) {
    			$dep[] = $rec['module_id'];
    		}
    		return $dep;
    	} catch (Exception $e) {
    		echo customException::cleanUp('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
     * Method to return a list of names of all locall installed modules
     *
     * @return array
     */
    public function getModuleNames() {
        try {
            $ret = array();
            $sql = "SELECT module_id FROM tbl_modules";
            $rs = $this->getArray($sql);
            foreach ($rs as $result) {
                $ret[] = $result['module_id'];
            }
            return $ret;
        } catch (Exception $e) {
    		echo customException::cleanUp('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * This is a method to check if a module is registered and turn the result as an array
    * @param   string $moduleId
    * @returns array $result
    */
    public function getModuleInfo($moduleId) {
    	try {
    		if ($this->checkIfRegistered($moduleId)){
    			$result = array('isreg'=>TRUE,
    			'name'=>$this->objLanguage->code2Txt('mod_'.$moduleId.'_name',"{$moduleId}"));
    		} else {
    			$result = array('isreg'=>FALSE,'name'=>'');
    		}
    		return $result;
    	} catch (Exception $e) {
    		echo customException::cleanUp('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }
}
?>