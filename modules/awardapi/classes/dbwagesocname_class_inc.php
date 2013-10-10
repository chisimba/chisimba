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
 * @version   $Id$
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
 * AWARD agreement data access class
 * 
 * Class to provide AWARD Party Branch Unit information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class dbwagesocname extends dbTable
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
                parent::init('tbl_award_wage_socname');
                $this->objSocName = $this->getObject('dbsocname');
           } catch (Exception $e){
       		    throw customException($e->getMessage());
        	    exit();
     	   }
        }
        
        public function getWageSoc($id) {
            $wageSocName = $this->getRow('id',$id);
            $socName = $this->objSocName->getRow('id',$wageSocName['socnameid']);
            $socs = $this->getAll("WHERE socnameid = '{$wageSocName['socnameid']}'");
            $socCount = count($socs);
            $a_ret['name'] = "{$socName['name']} - ($socCount)";
            $a_ret['id'] = $socName['id'];
            return $a_ret;
        }
        
        public function getSocList($search) {
            //$sql = "SELECT socname.id AS id, count(wagesocname.id) AS sample, socname.name AS name
            //        FROM tbl_award_wage_socname AS wagesocname, tbl_award_socname AS socname
            //        WHERE socname.id = wagesocname.socnameid AND socname.name LIKE '%$search%'
            //        GROUP BY id
            //        ORDER BY sample DESC";
            $sql = "SELECT id, name
					FROM tbl_award_socname
					WHERE name LIKE '%$search%'";
			$socs = $this->objSocName->getArray($sql);
			$count = 0;
			foreach ($socs as $soc) {
				$sql = "SELECT COUNT(id) AS sample
						FROM tbl_award_wage_socname
						WHERE socnameid = '{$soc['id']}'";
				$sample = $this->getArray($sql);
				$socs[$count]['sample'] = $sample[0]['sample'];
				$count++;
			}
			usort($socs, array("dbwagesocname", "compare"));
            return $socs;
        }
		
		static function compare($a, $b) {
			if ($a['sample'] == $b['sample']) {
				return 0;
			}
			return ($a['sample'] > $b['sample']) ? -1 : 1;
		}
        
}
?>