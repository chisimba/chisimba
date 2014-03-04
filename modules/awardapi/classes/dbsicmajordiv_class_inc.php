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
 * @version   CVS: $Id: dbsicmajordiv_class_inc.php 74 2008-07-31 12:00:45Z nic $
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
 * AWARD XML_RPC & data access class
 * 
 * Class to provide AWARD SIC Major Div information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbsicmajordiv_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 */

class dbsicmajordiv extends dbTable
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
                parent::init('tbl_award_sicmajordiv');
                $this->objUser =& $this->getObject('user', 'security');
                
           } catch (Exception $e){
       		    throw customException($e->getMessage());
        	    exit();
     	   }
        }
        
        function getName($id) {
            $res = $this->getRow('id',$id);
            return $res['description'];
        }
	
        function getSubDivs($majorId,$divId) {
            return $this->getArray("SELECT sgroup.description AS description, sgroup.id AS id, sgroup.code AS code
            						FROM tbl_award_sicmajorgroup AS sgroup, tbl_award_sicdiv AS sdiv
            						WHERE sgroup.divid = '$divId' AND sgroup.divid = sdiv.id AND sdiv.major_divid = '$majorId'");
        }
	
        /**
         * Method to return all SIC minor divsions within a given SIC major division
         *
         * @param string $majorId the id of the SIC major division
         * @return array containing all the SIC sub divsions within the specified major division
         */
        function getMinorDivs($majorId) {
        	return $this->getArray("SELECT * FROM tbl_award_sicdiv WHERE major_divid = '$majorId'");
        }
        
}
?>