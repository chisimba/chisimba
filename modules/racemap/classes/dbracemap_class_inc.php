<?php
/**
 * Racemap db controller class
 *
 * Class to control the QRCreator module
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
 * @category  chisimba
 * @package   racemap
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * racemap db controller class
 *
 * Class to control the racemap db module.
 *
 * @category  Chisimba
 * @package   racemap
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class dbracemap extends dbTable
{

	/**
	 * Standard init function - Class Constructor
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		$this->objLanguage = $this->getObject("language", "language");
		$this->objUser = $this->getObject('user', 'security');
		parent::init('tbl_racemap_meta');
	}
	
    /**
     * Method to add a record to the metadata table
     *
     * @param array $insarr Array with record fields
     * @return string Last Insert Id
     */
	public function insertMetaRecord($insarr)
	{
		return $this->insert($insarr, 'tbl_racemap_meta');
	}
	
	/**
     * Method to add a record to the track table
     *
     * @param array $insarr Array with record fields
     * @return string Last Insert Id
     */
	public function insertTrkPoints($insarr) {
	    $this->changeTable('tbl_racemap_tracks');
	    $this->insert($insarr, 'tbl_racemap_tracks');
	    $this->changeTable('tbl_racemap_meta');
	}
	
	/**
	 * Method to get metadata of a track by id
	 * 
	 * @access public
	 * @param string $id id
	 * @return array 
	 */
	public function getMetaFromId($id) {
	    $this->changeTable('tbl_racemap_meta');
	    return $this->getAll("WHERE id = '$id'");
	}
	
	/**
	 * Method to get all tracks of a user by userid
	 * 
	 * @access public
	 * @param string $userid userid
	 * @return array 
	 */
	public function getUserTracks($userid) {
	    $this->changeTable('tbl_racemap_meta');
	    return $this->getAll("WHERE userid = '$userid'");
	}
	
	/**
	 * Method to update metadata of a track
	 * 
	 * @access public
	 * @param array $updatearr the array of updated metadata info
	 * @return void
	 */
	public function updateMeta($updatearr) {
	    $this->changeTable('tbl_racemap_meta');
	    $updatearr['author'] = $this->objUser->fullName();
	    $updatearr['creationtime'] = $this->now();
	    
	    $this->update('id', $updatearr['id'], $updatearr);
	    return;
	}
	
	/**
	 * Method to count the number of points of a track by metaid
	 * 
	 * @access public
	 * @param string $metaid metadata id
	 * @return integer
	 */
	public function countPoints($metaid) {
	    $this->changeTable('tbl_racemap_tracks');
	    $stop = $this->getRecordCount("WHERE metaid = '$metaid'");
	    $this->changeTable('tbl_racemap_meta');
	    return $stop;
	}
	
	/**
	 * Method to get tracks  by metaid
	 * 
	 * @access public
	 * @param string $metaid metadata id
	 * @param integer $start start int
	 * @param integer $stop stop int
	 * @return array 
	 */
	public function getPoints($metaid, $start, $stop) {
	    $this->changeTable('tbl_racemap_tracks');
	    $ret = $this->getAll("WHERE metaid = '$metaid' LIMIT $start, $stop"); 
	    $this->changeTable('tbl_racemap_meta');
	    return $ret;
	}
	
	/**
	 * Method to dynamically change a table
	 * 
	 * @access public
	 * @param string $table table name
	 * @return void
	 */
	public function changeTable($table) {
	    parent::init($table);
	}
}
?>
