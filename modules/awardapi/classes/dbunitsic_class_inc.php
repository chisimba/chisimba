<?php
/**
 * AWARD index data access class
 * 
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
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbunitsic_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core,api
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * AWARD unit branch data access class
 * 
 * Class to provide AWARD Party Branch Unit information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbunitsic_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 */

class dbunitsic extends dbTable
{

	   /**
	    * Class Constructor
	    *
	    * @access public
	    * @return void
	    */
        public function init()
        {
        	try {
                parent::init('tbl_award_unit_sic');
				$this->objMajorDiv = $this->getObject('dbsicmajordiv');
				$this->objDiv = $this->getObject('dbsicdiv');
				$this->objMajorGroup = $this->getObject('dbsicmajorgroup');
				$this->objGroup = $this->getObject('dbsicgroup');
				$this->objSubGroup = $this->getObject('dbsicsubgroup');
           } catch (Exception $e){
       		    throw customException($e->getMessage());
        	    exit();
     	   }
        }
		
		public function getSicStr($unitId) {
			$unitSic = $this->getRow('unitid',$unitId);
			$sicStr = array();
			
			$majorDiv = $this->objMajorDiv->getRow('id',$unitSic['major_divid']);
			$code = $majorDiv['code'];
			$sicStr[0] = "{$code}0000 - {$majorDiv['description']}";
			
			$div = $this->objDiv->getRow('id',$unitSic['divid']);
			$code .= $div['code'];
			$sicStr[1] = "{$code}000 - {$div['description']}";
			
			if ($unitSic['major_groupid'] != "init_0") {
				$majorGroup = $this->objMajorGroup->getRow('id',$unitSic['major_groupid']);
				$code .= $majorGroup['code'];
				$sicStr[2] = "{$code}00 - {$majorGroup['description']}";
			} else {
				$sicStr[2] = null;
			}
			
			if ($unitSic['groupid'] != "init_0") {
				$group = $this->objGroup->getRow('id',$unitSic['groupid']);
				$code .= $group['code'];
				$sicStr[3] = "{$code}0 - {$group['description']}";
			} else {
				$sicStr[3] = null;
			}
			
			if ($unitSic['sub_groupid'] != "init_0") {
				$subGroup = $this->objSubGroup->getRow('id',$unitSic['sub_groupid']);
				$code .= $subGroup['code'];
				$sicStr[4] = "{$code} - {$subGroup['description']}";
			} else {
				$sicStr[4] = null;
			}
			
			return $sicStr;
		}
}
?>