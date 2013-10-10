<?php
/**
 * tweetlic db controller class
 *
 * Class to control the tweetlic module
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
 * @package   tweetlic
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
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
 * tweetlic db controller class
 *
 * Class to control the tweetlic db module.
 *
 * @category  Chisimba
 * @package   tweetlic
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class dbtweetlic extends dbTable
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
		parent::init('tbl_tweetlic_streams');
	}
	
    /**
     * Method to add a record to the data table
     *
     * @param array $insarr Array with record fields
     * @return string Last Insert Id
     */
	public function upsertRecord($insarr)
	{
	    $scrname = $insarr['screen_name'];
	    if($this->getRecordCount("WHERE screen_name = '$scrname'") > 0) {
	        return $this->update('screen_name', $scrname, $insarr, 'tbl_tweetlic_streams');
	    }
	    else {
	        $insarr['creationtime'] = $this->now();
	        if(!empty($insarr)) {
		        return $this->insert($insarr, 'tbl_tweetlic_streams');
		    }
		}
	}
	
	/**
	 * Method to get a users details by way of their screen name
	 *
	 * @param string name
	 * @return array
	 */
	 public function getUserDetails($name) 
	 {
	     return $this->getAll("WHERE screen_name = '$name'");
	 }
	 
	 /**
	  * Perform a simple search through users to find their license
	  * 
	  * @param license
	  * @return array details
	  */
	  public function searchUsers($license) {
	      
	  }
}
?>
