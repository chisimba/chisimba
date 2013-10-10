<?php
/**
 *
 * Database access for grades
 *
 * Database access for grades. This is a sample database model class
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
 * @package   grades
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
* Database access for grades
*
* Database access for grades. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   grades
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dbgrades extends dbtable
{

    /**
    *
    * Intialiser for the grades database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_grades_grades');
        $this->table = 'tbl_grades_grades';
    }

    /**
     *
     * Method to return all grades
     *
     * @access public
     * @return array $result An array of all the grades
     *
     */
    public function getAll()
    {
        $result = $this->fetchAll();
        
        return $result;
    }

    /**
     *
     * Method to return a grade
     * 
     * @access public
     * @param string $id The id of the grade to retrieve
     * @return array The grade data array 
     */
    public function getGrade($id)
    {
        return $this->getRow('id', $id);
    }

    /**
     * Method to add a grade to the database
     * 
     * @access public
     * @param array @data The array of grade data
     * @return string The id of the grade added
     */
    public function insertData($data)
    {
        return $this->insert($data);
    }
    
    /**
     *
     * Method to delete a grade
     * 
     * @access public
     * @param string $sid The id of the grade to delete
     * return boolean 
     */
    public function deleteData($id)
    {
        return $this->delete('id', $id);
    }

    /**
     * Method to edit a grade on the database
     * 
     * @access public
     * @param array @data The array of grade data
     * @return string The id of the grade edited
     */
    public function updateData($id, $data)
    {
        return $this->update('id', $id, $data);
    }
    
    /**
     *
     * Method to get grade names for use in groups.
     * 
     * @access public
     * @return string $nameString
     */
    public function getGrades()
    {
        $data = $this->fetchAll();
        
        if (!empty($data))
        {
            $nameArray = array();
            foreach ($data as $line)
            {
                $nameArray[] = "'" . $line['name'] . "'";
            }
            $nameString = implode(',', $nameArray);
            return $nameString;
        }
        return FALSE;
    }

    /**
     *
     * Method to return a grade by name
     * 
     * @access public
     * @param string $name The name of the grade to retrieve
     * @return array The grade data array 
     */
    public function getGradeByName($name)
    {
        return $this->getRow('name', $name);
    }

}
?>