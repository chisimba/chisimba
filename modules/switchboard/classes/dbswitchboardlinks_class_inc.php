<?php
/**
 *
 * Database access for Switchboard links
 *
 * Database access for Switchboard links, use to edit and show the 
 * switchboard links themselves.
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
 * @package   switchboard
 * @author    Derek Keats derek@dkeats.com
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
* Database access for Switchboard links
*
* Database access for Switchboard links, use to edit and show the 
* switchboard links themselves.
*
* @package   switchboard
* @author    Derek Keats derek@dkeats.com
*
*/
class dbswitchboardlinks extends dbtable
{

    /**
    *
    * Intialiser for the switchboard database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_switchboard_links');
    }

    /**
     *
     * Get the links and related info to build the switchboard.
     *
     * @return string The text of the init_overview
     * @access public
     *
     */
    public function getLinks()
    {
        return $this->getAll();
    }
    
    /**
     *
     * Save a link when coming from edit
     * 
     * @param string $id The record id
     * @param string $iconurl The URL for the icon image
     * @param string $link The URL for the switch
     * @param string $title The title for the switch item
     * @param string $description The description of the switch item
     * @access public
     * @return boolean TRUE|FALSE
     * 
     */
    public function saveLink($id, $iconurl, $link, $title, $description)
    {
        $result = $this->update(
          'id', $id, array(
          'iconurl' => $iconurl,
          'link' => $link,
          'title' => $title,
          'description' => $description)
        );
        return $result;
    }
    
    /**
     *
     * Save a link when coming from add
     * 
     * @param string $iconurl The URL for the icon image
     * @param string $link The URL for the switch
     * @param string $title The title for the switch item
     * @param string $description The description of the switch item
     * @access public
     * @return string The id of the saved record
     * 
     */
    public function addLink($iconurl, $link, $title, $description)
    {
        $data = array(
          'iconurl' => $iconurl,
          'link' => $link,
          'title' => $title,
          'description' => $description
        );
        return $this->insert($data);
    }
    
    
    public function getLinkById($id)
    {
        $filter = "WHERE id = '$id'";
        return $this->getAll($filter);
    }
}
?>