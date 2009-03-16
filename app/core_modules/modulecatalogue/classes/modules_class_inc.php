<?php

/**
 * The class representing the modules table, handling all non-administrative
 * operations on the table.
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   modulecatalogue
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @author    Sean Legassick <fsiu@uwc.ac.za>
 * @author    Jeremy O'Connor <joconnor@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       modulesadmin class for administrative operations
 */

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
 * The modules class which is used to read data from
 * the modules table. This data includes module versions, release dates
 * and other module metadata
 *
 * @category  Chisimba
 * @package   modulecatalogue
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @author    Sean Legassick <fsiu@uwc.ac.za>
 * @author    Jeremy O'Connor <joconnor@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       modulesadmin class for administrative operations
 */

// Constants for $getType parameter of getModules

class modules extends dbTable
{
    /**
     * The language object for multilingualisation
     * @var    object $objlanguage
     * @access private
     */
    private $objLanguage;


    /**
     * The system configuration object
     * @var    object $objConfig
     * @access public
     */
    public $objConfig;

    /**
     * string used for user feedback and error messages
     * @var    string $output
     * @access public
     */
    public $output;

    /**
     * Standard object init function
     */
    public function init() {
        try {
            parent::init('tbl_modules');
            //Config and Language Objects
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objConfig = $this->getObject('altconfig','config');
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
                $_module['title'] = ucwords($this->objLanguage->code2Txt('mod_' . $module['module_id'] . '_name',$module['module_id']));
                $_module['description'] = $this->objLanguage->code2Txt('mod_' . $module['module_id'] . '_desc',$module['module_id']);
                $_modules[] = $_module;
            }
            return !empty($_modules) ? $_modules : FALSE;
        } catch (Exception $e) {
            echo customException::cleanUp('Caught exception: '.$e->getMessage());
            exit();
        }
    }


    /**
     * Method to get the Title of a Module
     * @param string $moduleId Module Id
     * @return string
     */
    public function getModuleTitle($moduleId)
    {
        return ucwords($this->objLanguage->code2Txt('mod_'.$moduleId.'_name', $moduleId));
    }

    /**
     * Method to get the Description of a Module
     * @param string $moduleId Module Id
     * @return string
     */
    public function getModuleDescription($moduleId)
    {
        return $this->objLanguage->code2Txt('mod_'.$moduleId.'_desc', $moduleId);
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
             $row = $this->getRow('module_id',$moduleId, 'tbl_modules');
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
     * Method to return a list of names of all locally installed modules
     *
     * @return array
     */
    public function getModuleNames() {
        try {
            $ret = array();
            $sql = "SELECT module_id, module_version FROM tbl_modules";
            $rs = $this->getArray($sql);
            foreach ($rs as $result) {
                $ret[] = $result['module_id']; //."(".$result['module_version'].")";
            }
            return $rs; //$ret;
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

    public function insertTags($tagarr, $status, $mod)
    {
        // user id hard coded to admin for firsttime reg...
        $userid = '1';
        $itemid = $status;
        $metakey = 'moduletag_'.$mod;
        $module = 'modulecatalogue';
        if(empty($tagarr))
        {
            return;
        }
        foreach($tagarr as $tags)
        {
            $insarr = array('userid' => $userid,
                            'item_id' => $itemid,
                            'meta_key' => $metakey,
                            'meta_value' => $tags,
                            'module' => $module,
                            'uri' => '',
                            );
            $this->insert($insarr, 'tbl_tags');
        }
        return;
    }

    public function removeTags($mod)
    {
        $metakey = 'moduletag_'.$mod;
        $module = 'modulecatalogue';
        parent::init('tbl_tags');
        $tagstodel = $this->getAll("WHERE meta_key = '$metakey'");
        if(empty($tagstodel))
        {
            return;
        }
        foreach($tagstodel as $tags)
        {
            $this->delete('id', $tags['id'], 'tbl_tags');
        }
        return;
    }


    /**
     * Method to get a list of context plugins
     * @param array List of Modules that are context plugins
     */
    public function getContextPlugins()
    {
        $modules = $this->getModules(2);
        $contextPlugins = array();

        $objModuleFile = $this->getObject('modulefile');

        foreach ($modules as $module)
        {
            if ($objModuleFile->contextPlugin($module['module_id'])) {
                $contextPlugins[] = $module['module_id'];
            }
        }

        return $contextPlugins;
    }

    /**
     * Method to get a list of context plugins with information of the modules
     * @param array List of Modules that are context plugins along with information of the modules
     */
    public function getListContextPlugins()
    {
        $modules = $this->getModules(2);
        $contextPlugins = array();

        $objModuleFile = $this->getObject('modulefile');

        foreach ($modules as $module)
        {
            if ($objModuleFile->contextPlugin($module['module_id'])) {
                $contextPlugins[] = $module;
            }
        }

        return $contextPlugins;
    }

    /**
     * Method to check whether a module is contextaware
     * @param string $moduleId Module Id
     * @return boolean
     */
    public function isContextAware($moduleId)
    {
        $row = $this->getRow('module_id',$moduleId);

        if ($row == FALSE) {
            return FALSE;
        } else {
            return $row['iscontextaware'] == 1 ? TRUE : FALSE;
        }
    }

    /**
     * Method to check whether a module depends context
     * i.e. should only be run if a user is in a context
     * @param string $moduleId Module Id
     * @return boolean
     */
    public function dependsContext($moduleId)
    {
        $row = $this->getRow('module_id',$moduleId);

        if ($row == FALSE) {
            return FALSE;
        } else {
            return $row['dependscontext'] == 1 ? TRUE : FALSE;
        }
    }
}
?>