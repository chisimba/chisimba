<?php

/**
 * Context modules
 *
 * Class to control and manipulate context module plugins
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
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Context modules
 *
 * Class to control and manipulate context module plugins
 *
 * @category  Chisimba
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class dbcontextmodules extends dbTable {
    /**
     * Constructor
     */
    public function init() {
        parent::init ( 'tbl_contextmodules' );
        $this->_objModule = $this->newObject ( 'modules', 'modulecatalogue' );
    }

    /**
     * Method to lookup if a module is visible to the current context
     *
     * @param      $moduleId    string  The moduleId
     * @param      $contextCode $string The context Code
     * @return     $ret         boolean Returns true if an enty was found or false when not found
     * @access     public
     * @deprecated
     */
    public function isVisible($moduleId, $contextCode) {
        $rsArr = $this->getAll ( "WHERE contextcode = '" . $contextCode . "' AND moduleid='" . $moduleId . "'" );
        $ret = true;
        if ($rsArr) {
            foreach ( $rsArr as $ar ) {
                $ret = (isset ( $ar ['moduleId'] )) ? true : false;
            }
        } else {
            $ret = false;
        }

        return $ret;
    }

    /**
     * Method to make a
     * module available to a context
     *
     * @param string $moduleId The moduleId
     * @param string $contextCode The Context Code
     * @return string  The new Id
     * @access     public
     * @deprecated
     */
    public function setVisible($moduleId, $contextCode) {
        return $this->insert ( array ('moduleId' => $moduleId, 'contextCode' => $contextCode ) );
    }

    /**
     * Method to make a module
     *
     * unavailable to a context
     *
     * @param      $moduleId    string: The moduleId
     * @param      $contextCode $string : The context Code
     * @access     public
     * @deprecated
     */
    public function setHidden($moduleId, $contextCode) {

    }

    /**
     * Method to delete all the modules
     * for the context
     *
     * @param      $contextCode string : the context code
     * @access     public
     * @deprecated
     */
    public function deleteModulesForContext($contextCode) {
        $this->delete ( 'contextCode', $contextCode );
    }

    /**
     * Method to get a list of context sensitive modules
     *
     * @return array
     */
    public function getInstallableModules() {

    }

    /**
     * Method to add a module to a context
     *
     * @param  $contextCode The Context Code
     * @return bool
     * @access public
     */
    public function addModule($contextCode, $moduleId) {
        $fields = array ('contextcode' => $contextCode, 'moduleid' => $moduleId );
        return $this->insert ( $fields );
    }

    /**
     * Method to get a list of modules for a context
     *
     * @param  contextCode The Context Code
     * @return array
     * @access public
     */
    public function getContextModules($contextCode) {
        $contextModules = $this->getAll ( "WHERE contextcode='" . $contextCode . "'" );
        $newArray = array ();
        foreach ( $contextModules as $module ) {
            $newArray [] = $module ['moduleid'];
        }

        return $newArray;
    }

    /**
     * Method to save the context modules
     *
     */
    public function save() {
        try {
            $contextCode = $this->_objDBContext->getContextCode ();
            $objModules = $this->newObject ( 'modules', 'modulecatalogue' );
            $objModuleFile = $this->newObject ( 'modulefile', 'modulecatalogue' );
            $modList = $objModules->getModules ( 2 );
            //dump all the modules
            $this->delete ( 'contextcode', $contextCode );

            foreach ( $modList as $module ) {

                if ($objModuleFile->contextPlugin ( $module ['module_id'] )) { //print $module['module_id'];
                    if ($this->getParam ( 'mod_' . $module ['module_id'] ) == $module ['module_id']) {

                        //add to database
                        $this->addModule ( $contextCode, $module ['module_id'] );

                    }
                }

            }

        } catch ( customException $e ) {
            echo customException::cleanUp ( $e );
            die ();
        }
    }

    /**
     * Method to check if a module is registered as a plugin
     *
     * @param  string  $moduleId
     * @return boolean
     * @access public
     */
    public function isContextPlugin($contextCode, $moduleId) {
        $arr = $this->getAll ( "WHERE contextcode = '" . $contextCode . "' AND moduleid = '" . $moduleId . "'" );

        if (array_key_exists ( 0, $arr )) {
            if (count ( $arr [0] ) > 0) {

                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Method to get the module name
     *
     * @param  string $moduleId
     * @return string
     * @access public
     *
     */
    public function getModuleName($moduleId) {
        $modInfo = $this->_objModule->getModuleInfo ( $moduleId );
        return $modInfo ['name'];
    }

}

?>