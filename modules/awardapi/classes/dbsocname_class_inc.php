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

class dbsocname extends dbTable
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
                parent::init('tbl_award_socname');
           } catch (Exception $e){
       		    throw customException($e->getMessage());
        	    exit();
     	   }
        }

    /**
     * Method to search table and return an array of related entries
     *
     * @param string $searchTerm The string to search for
     * @param string $group The level from which to search eg. major group, sub major group, minor group, etc
     * @param string $groupId The id(pk) of the group to be searched
     * @return array $socNames An array of all related entries
     */
     function search($searchTerm, $group,$groupId = NULL)
     {

        switch($group){
            case 'major':
                $sql = "SELECT * FROM tbl_award_socname WHERE name LIKE '%" . $searchTerm . "%'";
                $socNames = $this->getArray($sql);
                break;
                
            case 'submajor':
                $sql = "SELECT * FROM tbl_award_socname WHERE name LIKE '%" . $searchTerm . "%' AND major_groupid = '$groupId'"; 
                $socNames = $this->getArray($sql);
                break;   

            case 'minor':
                $sql = "SELECT * FROM tbl_award_socname WHERE name LIKE '%" . $searchTerm . "%' AND sub_major_groupid = '$groupId'"; 
                $socNames = $this->getArray($sql);
                break; 

            case 'unit':
                $sql = "SELECT * FROM tbl_award_socname WHERE name LIKE '%" . $searchTerm . "%' AND minor_groupid = '$groupId'"; 
                $socNames = $this->getArray($sql);
                break; 

        }          

        return $socNames;                
     }


}
?>