<?php
/**
 *
 * Database access for schools districts
 *
 * Database access for schools. This is a sample database model class
 * that you will need to edit in order for it to work.
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
 * @package   schools
 * @author    Kevin Cyster kcyster@gmail.com
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
* Database access for schools
*
* Database access for schools. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   schools
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dbschools_districts extends dbtable
{

    /**
    *
    * Intialiser for the schools database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_schools_districts');
        $this->table = 'tbl_schools_districts';
    }
   
    /**
     *
     * Get all of the districts fo the schools module.
     *
     * @access public
     * @return array The array of districts
     */
    public function getAllDistricts()
    {
        return $this->fetchAll();
    }

    /**
     * Method to return the districts for a province
     * 
     * @access public
     * @param integer $id The id of the province to get districts for
     * @return array The array of provinces for a district 
     */
    public function getDistrictsForProvince($pid)
    {
        return $this->fetchAll(" WHERE `province_id` = '$pid'" );
    }

    /**
     * Method to return a district
     * 
     * @access public
     * @param string $id The id of the disctrict to get
     * @return array The array of provinces for a district 
     */
    public function getDistrict($id)
    {
        return $this->getRow('id', $id);
    }

    /**
     *
     * Method to delete school districts
     * 
     * @access public
     * @param string $id The id of the district to delete
     * return boolean 
     */
    public function deleteDistrict($id)
    {
        return $this->delete('id', $id);
    }
    
    /**
     * Method to add a district to the database
     * 
     * @access public
     * @param array @data The array of district data
     * @return string $id The id of the district added
     */
    public function insertDistrict($data)
    {
        return $this->insert($data);
    }    

    /**
     * Method to edit a district on the database
     * 
     * @access public
     * @param array @data The array of district data
     * @return string $id The id of the district edited
     */
    public function updateDistrict($id, $data)
    {
        return $this->update('id', $id, $data);
    }
}
?>