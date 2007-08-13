<?php
/**
 * The class used to connect to the modules blocks table. This class is used to
 * write to and read from the table which stores information about which
 * modules have functionality in the form of blocks which can be inserted into
 * various content areas within the Chisimba system
 *
 * This class connects to the module blocks table which stores information
 * about module blocks
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
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
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
* The class used to connect to the modules blocks table. This class is used to
* write to and read from the table which stores information about which
* modules have functionality in the form of blocks which can be inserted into
* various content areas within the Chisimba system
*
* @category  Chisimba
* @package   modulecatalogue
* @author    Nic Appleby <nappleby@uwc.ac.za>
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id$
* @link      http://avoir.uwc.ac.za
*/

class dbmoduleblocks extends dbTable
{

    /**
     * The language object used for multilingualisation
     * @var    object $objLanguage
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
     * standard init function
     *
     */
    public function init() {
    	try {
    		parent::init('tbl_module_blocks');
    		//Config and Language Objects
    		$this->objLanguage = $this->getObject('language', 'language');
    		$this->objConfig = $this->getObject('altconfig','config');
    	} catch (Exception $e) {
    		echo customException::cleanUp();
    		exit();
    	}
    }

    /**
     * This method returns a list of all the blocks currently
     * registered with the system
     *
     * @param  string $type (optional) The type of blocks to be
     *          searched for (normal or wide) leaving this out
     *          returns all blocks returned
     * @return array An array of all the blocks
     * @access public
     */
    public function getBlocks($type = NULL) {
    	if ($type == NULL) {
    		$filter = '';
    	} else {
    		$filter = "WHERE blockwidth = '$type'";
    	}
    	return $this->getAll($filter);
    }

    /**
     * This function registers a block with the Chisimba system
     *
     * @param  string $moduleid  The id of the owning module
     * @param  string $blockName A unique name for the block
     * @param  string $width     The type of block (wide|normal)
     * @return void
     * @access public
     */
    public function addBlock($moduleid,$blockName,$width) {
        $exists = $this->getAll(" WHERE moduleid = '$moduleid' AND blockname = '$blockName' AND blockwidth = '$width'");
    	if (count($exists) < 1) {
    		$arrData = array('moduleid'=>$moduleid,'blockname'=>$blockName,'blockwidth'=>$width);
    		$this->insert($arrData);
    	}
    }

    /**
     * This method deletes all the blocks assosciated with a module
     * To be used when a module is uninstalled
     *
     * @param  string $moduleid The id of the owning module
     * @return void
     * @access public
     */
    public function deleteModuleBlocks($moduleid) {
    	$record = $this->getAll("WHERE moduleid = '$moduleid'");
    	foreach($record as $block) {
    		$this->delete('id',$block['id']);
    	}
    }

    /**
     * This method deletes a single block
     *
     * @param  string $moduleid  The id of the parent module
     * @param  string $blockname The name of the block to delete
     * @return void
     * @access public
     */
    public function deleteBlock($moduleid,$blockname) {
    	$record = $this->getAll("WHERE moduleid = '$moduleid' AND blockname = '$blockname'");
    	if (is_array($record)) {
    		$record = current($record);
    	}
    	$this->delete('id',$record['id']);
    }
}
?>