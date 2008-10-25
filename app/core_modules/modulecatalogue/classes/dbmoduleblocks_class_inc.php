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
 * @version   $Id$
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
* @version   $Id$
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
    public function init()
    {
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
     * @param  string $widrh (optional) The width of blocks to be
     *          searched for (normal or wide) leaving this out
     *          returns all blocks
     * @param  string $type (optional) The type of blocks to be
     *          searched for (site, context, workgroup, etc) 
     *          leaving this out returns all blocks
     *          Another option is to separate by pipe.
     *          E.g. site|user - This will return all site and user blocks
     * @return array An array of all the blocks
     * @access public
     */
    public function getBlocks($width=NULL, $type=NULL)
    {
        $filter = array();
        
        if ($width != NULL) {
            $filter[] = "blockwidth = '$width'";
        }
        
        if ($type != NULL) {
            $type = explode('|', $type);
            
            $typeFilter = '(';
            $divider = '';
            foreach ($type as $param)
            {
                $typeFilter .= $divider." blocktype = '{$param}'";
                $divider = ' OR ';
            }
            
            $typeFilter .= ')';
            
            $filter[] = $typeFilter;
        }
        
        $filterStr = '';
        
        if (count($filter) > 0) {
            
            $filterStr = 'WHERE';
            $divider = '';
            
            foreach ($filter as $item) {
                $filterStr .= $divider." {$item}";
                $divider = ' AND ';
            }
        }
        
        return $this->getAll($filterStr);
    }

    /**
     * This function registers a block with the Chisimba system
     *
     * @param  string $moduleid  The id of the owning module
     * @param  string $blockName A unique name for the block
     * @param  string $width     The width of block (wide|normal)
     * @param  string $type     The type of block, like site, user, context, workgroup, etc.
     * @return void
     * @access public
     */
    public function addBlock($moduleid,$blockName,$width, $type='site')
    {
    	$arrData = array('moduleid'=>$moduleid, 'blockname'=>$blockName, 'blockwidth'=>$width, 'blocktype'=>$type);
        $exists = $this->getAll(" WHERE moduleid = '$moduleid' AND blockname = '$blockName' AND blockwidth = '$width' AND blocktype='$type'");
        if (count($exists) < 1) {
            
            $this->insert($arrData);
        }
        else {
        	$rec = $this->getAll("WHERE blockname = '$blockName'");
        	$this->update('id', $rec[0]['id'], $arrData);
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
    public function deleteModuleBlocks($moduleid)
    {
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
    public function deleteBlock($moduleid,$blockname)
    {
        $record = $this->getAll("WHERE moduleid = '$moduleid' AND blockname = '$blockname'");
        if (is_array($record)) {
            $record = current($record);
        }
        $this->delete('id',$record['id']);
    }
}
?>