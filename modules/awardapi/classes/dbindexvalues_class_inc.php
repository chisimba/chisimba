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
 * @version   CVS: $Id: dbindexvalues_class_inc.php 74 2008-07-31 12:00:45Z nic $
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
 * AWARD index data access class
 * 
 * Class to provide AWARD index information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbindexvalues_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 */

class dbindexvalues extends dbTable
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
                parent::init('tbl_award_index_values');
           } catch (Exception $e){
       		    throw customException($e->getMessage());
        	    exit();
     	   }
        }
        
        /**
         * Method to get the latest value for the given index
         *
         * @param string $id the id of the index
         * @return array containing the latest value and its entry date
         */
        public function getLatestValue($id) {
            $entries = $this->getAll("WHERE typeid = '$id' ORDER BY indexdate DESC");
            if (!empty($entries)) {
                $entry = current($entries);
                $entry['indexdate'] = date("F Y",strtotime($entry['indexdate']));
            } else {
                $entry['indexdate'] = -1;
                $entry['value'] = -1;
            }
            return $entry; 
        }
        
        /**
         * Method to get an array of index values for each month for 5 years starting from startYear 
         *
         * @param string $id the id of the index
         * @param string $startYear the year to start at
         * @return array the array of monthly value arrays
         */
        public function getValues($id,$startYear) {
            $return = array();
            for ($i=0;$i<5;$i++) {
                $return[] = $this->getYearValues($id,$startYear+$i);
            }
            return $return;
        }
        
        /**
         * Method to get index values for each month of a given year
         *
         * @param string $id the id of the index
         * @param string $year the year in question
         * @return array array of the monthly values
         */
        public function getYearValues($id,$year) {
            $return = array();
            for ($i=1;$i<13;$i++) {
                $return[] = $this->getAll("WHERE typeid = '$id' AND EXTRACT(MONTH FROM indexdate) = '$i' AND EXTRACT(YEAR FROM indexdate) = '$year'");
                //log_debug ("WHERE typeid = '$id' AND EXTRACT(MONTH FROM indexdate) = '$i' AND EXTRACT(YEAR FROM indexdate) = '$year'");
            }
            return $return;
        }
        
        /**
         * Method to check whether a given index has an entry for the specified date
         *
         * @param string $indexId the index to check
         * @param string $date the date to check
         * @return false or the id of the entry
         */
        public function valueExists($indexId,$date) {
            $rs = $this->getAll("WHERE typeid='$indexId' AND indexdate='$date'");
            if (empty($rs)) {
                return false;
            } else {
                return $rs[0]['id'];
            }
        }
        
}
?>