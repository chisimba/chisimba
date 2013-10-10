<?php
/**
 * AWARD data access class
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
 * @version   CVS: $Id: dbbenefitnames_class_inc.php 74 2008-07-31 12:00:45Z nic $
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
 * AWARD benefits data access class
 * 
 * Class to provide AWARD Party Branch information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbbenefitnames_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 */

class dbbenefitnames extends dbTable
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

            parent::init('tbl_award_benefit_names');
        } catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }

public function benefitExists($agreeId,$nameId) {
        $check = $this->getAll("WHERE agreeid = '$agreeId' AND nameid = '$nameId'");
        if (!empty($check)) {
            return $check[0]['id'];
        } else {
            return false;
        }
    }

//public function getBenefitName($nameId)
  public function getBenefitName($id) {
        $objBenefitNames = &$this->getObject('dbbenefitnames','awardapi');
        $benefit = $objBenefitNames->getRow('id',$id);
        return $benefit['name'];
    }
 public function getHoursPerWeek($agreeId) {
   $a_ret = $this->getAll("WHERE agreeid = '$agreeId' AND nameid = 'init_7'");
   $ret = current($a_ret);
   return $ret['value'];
    }

}
?>