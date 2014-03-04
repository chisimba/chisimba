<?php
/**
 *
 * Operations for Species
 *
 * Operations for Species, building user interface elements
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
 * @package   species
 * @author    Derek Keats derek@localhost.local
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * Operations for Species
 *
 * Operations for Species, building user interface elements
*
* @package   species
* @author    Derek Keats derek@localhost.local
*
*/
class dbspecies extends dbtable
{

    /**
    *
    * Intialiser for the species operations class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Set the parent table.
        $table = $this->getSession('speciesgroup', 'birds', 'species');
        switch ($table) {
            case 'birds':
                $this->table = 'tbl_birds_list';
                parent::init('tbl_birds_list');
                break;
            case 'plants_proteaceae':
                $this->table = 'tbl_plants_proteaceae_list';
                parent::init('tbl_plants_proteaceae_list');
                break;
            case 'mammals':
                $this->table = 'tbl_mammals_list';
                parent::init('tbl_mammals_list');
                break;
        }
    }
    
    /**
     *
     * Get the text of the init_overview that we have in the sample database.
     *
     * @return string The text of the init_overview
     * @access public
     *
     */
    public function getAllRecords()
    {
        return $this->getAll();
    }
    
    /**
     * 
     * Get a record by the primary key ($id)
     * 
     * @param string $id The PK of the record to return
     * @return string Array The record
     * @access public
     * 
     */
    public function getRecord($id)
    {
        $filter = "WHERE id = '$id'";
        $ret = $this->getAll($filter);
        return $ret[0];
    }
    
    /**
     * 
     * Get a list of records by alphabet letter
     * 
     * @param string $letter The letter
     * @param string $field The field to use
     * @return string Array Array of results data
     * @access public
     * 
     */
    public function getListByLetter($letter, $field)
    {
        $filter = " WHERE $field LIKE '$letter%' ";
        return $this->getAll($filter);
    }
    
    /**
     * 
     * Get a list of records by group name
     * 
     * @param string $group The group
     * @return string Array An array of results data
     * @access public
     * 
     */
    public function getGroup($group)
    {
        $filter = " WHERE alphabeticalname LIKE '$group%' ";
        return $this->getAll($filter);
    }
    
    /**
     * 
     * Get a list of the groupings from the alphabetical name, for example
     * Albatros, Apalis, Avocet....
     * 
     * @param string $letter The letter
     * @return string Array Array of results data
     * @access public
     * 
     */
    public function getGroupings()
    {
        $sql = 'SELECT DISTINCT 
            SUBSTRING_INDEX(alphabeticalname, \',\', 1) AS groupname
            FROM ' . $this->table . '; ';
        return $this->getArray($sql);
    }
    
    
    
    

    public function getScientificName($id)
    {
        $sp = $this->getRecord($id);
        return $sp['scientificname'];
    }
    
    public function getFullName($id)
    {
        $sp = $this->getRecord($id);
        return $sp['fullname'];
    }

}
?>