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
class dbbridging extends dbtable
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
        parent::init('tbl_grades_bridging');
        $this->table = 'tbl_grades_bridging';
        
        $this->objDBschools = $this->getObject('dbschools_schools', 'schools');
        $this->objDBgrades = $this->getObject('dbgrades', 'grades');
        $this->objDBsubjects = $this->getObject('dbsubjects', 'grades');
        $this->objDBstrands = $this->getObject('dbstrands', 'grades');
        $this->objDBclasses = $this->getObject('dbclasses', 'grades');
        $this->objDBcontext = $this->getObject('dbcontext', 'context');
    }

    /**
     *
     * Method to get links
     * 
     * @access public
     * @param string $from The current component
     * @param string $to The component linked to
     * @param string $id The id of the component to get links for
     * @return array 
     */
    public function getLinkedItems($from, $to, $id)
    {
        switch ($to)
        {
            case 'school_id':
                $table = $this->objDBschools->table;
                break;
            case 'grade_id':
                $table = $this->objDBgrades->table;
                break;
            case 'subject_id':
                $table = $this->objDBsubjects->table;
                break;
            case 'strand_id':
                $table = $this->objDBstrands->table;
                break;
            case 'class_id':
                $table = $this->objDBclasses->table;
                break;
            case 'context_id':
                $table = $this->objDBcontext->table;
                break;
        }
        $sql = "SELECT *, b.id AS id, c.id AS cid FROM $this->table AS b";
        $sql .= " LEFT JOIN $table AS c ON (b.$to = c.id)";
        $sql .= " WHERE (b.$from = '$id' AND b.$to IS NOT NULL AND c.id IS NOT NULL)";

        return $this->getArray($sql);   
    }
    
    /**
     *
     * Method to get links
     * 
     * @access public
     * @param string $id The id of the item to get links for
     * @return array 
     */
    public function getUnlinkedItems($from, $to, $id)
    {
        switch ($to)
        {
            case 'school_id':
                $dbClass = $this->objDBschools;
                $table = $this->objDBschools->table;
                break;
            case 'grade_id':
                $dbClass = $this->objDBgrades;
                $table = $this->objDBgrades->table;
                break;
            case 'subject_id':
                $dbClass = $this->objDBsubjects;
                $table = $this->objDBsubjects->table;
                break;
            case 'strand_id':
                $dbClass = $this->objDBstrands;
                $table = $this->objDBstrands->table;
                break;
            case 'class_id':
                $dbClass = $this->objDBclasses;
                $table = $this->objDBclasses->table;
                break;
            case 'context_id':
                $dbClass = $this->objDBcontext;
                $table = $this->objDBcontext->table;
                break;
        }

        $links = $this->getLinkedItems($from, $to, $id);
        if (!empty($links))
        {
            $ids = array();
            foreach ($links as $link)
            {
                $ids[] = "'" . $link['cid'] . "'";
            }
            $idString = implode(',', $ids);
        
            $sql = "SELECT * FROM $table ";
            $sql .= "WHERE id NOT IN ($idString)";
            
            return $this->getArray($sql);
        }
        else
        {
            return $dbClass->fetchAll();
        }
    }
    
    /**
     * Method to add a link to the database
     * 
     * @access public
     * @param array @data The array of link data
     * @return string The id of the link added
     */
    public function insertData($data)
    {
        return $this->insert($data);
    }
    
    /**
     *
     * Method to delete a link
     * 
     * @access public
     * @param string $sid The id of the link to delete
     * return boolean 
     */
    public function deleteLink($id)
    {
        return $this->delete('id', $id);
    }

    /**
     *
     * Method to delete links
     * 
     * @access public
     * @param string $field The type of the linked component to delete
     * @param string $sid The id of the component to delete
     * return boolean 
     */
    public function deleteLinks($field, $id)
    {
        return $this->delete($field, $id);
    }
    
    /**
     *
     * Method to get all links to an item.
     * 
     * @access public
     * @param string $field The field to get links from
     * @param string $id The id of the item to get links to 
     * @return array
     */
    public function getLinked($field, $id)
    {
        return $this->fetchAll("WHERE `$field` = '$id'");
    }
    
    /**
     *
     * Method to return unlinked items.
     * 
     * @access public
     * @param string $from Field to check for links
     * @param string $idString A CSV list of ids
     * @param string $to Field to check if links exist
     * @param string $linked The id to check links against
     * @return array 
     */
    public function getUnlinked($from, $idString, $to, $linked)
    {
        $data = $this->fetchAll("WHERE $from IN ($idString) AND $to = '$linked'");

        $idString = str_replace("'", "", $idString);
        $idArray = explode(',', $idString);

        foreach ($data as $line)
        {
            $key = array_search($line[$from], $idArray);
            if ($key !== FALSE)
            {
                unset($idArray[$key]);
            }
        }
        return $idArray;
    }
    
    /**
     *
     * Method to get all links
     * 
     * @return array The array of links 
     */
    public function getAll()
    {
        return $this->fetchAll();
    }
}
?>