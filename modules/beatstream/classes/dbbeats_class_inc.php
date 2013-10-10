<?php
/**
 * beatstream db controller class
 *
 * Class to control the beatstream module
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
 * @package   beatstream
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
 * beatstream db controller class
 *
 * Class to control the beatstream db module.
 *
 * @category  Chisimba
 * @package   beatstream
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class dbbeats extends dbTable
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
		parent::init('tbl_beatstream');
	}
	
	/** 
	 * Method to increment (insert) to the votes table
	 *
	 * @param array $insarr
	 * @return string $id
	 */
	 public function upsertVote($insarr) {
	     $this->changeTable('tbl_beatstream_votes');
	     $insarr['day'] = $this->now();
	     $sid = $insarr['suggestion_id'];
	     if($this->getRecordCount("WHERE suggestion_id = '$sid'") > 0) {
	        $rid = $this->update('suggestion_id', $sid, $insarr, 'tbl_beatstream_votes');
	     }
	     else {
	         if(!empty($insarr)) {
	             $rid = $this->insert($insarr, 'tbl_beatstream_votes');
	         }
	     }
	     // update the vote counts and ratings
	     //$vup = $this->getAll("WHERE id ='$rid'");
	     //$vup = $vup[0];
	     $this->changeTable('tbl_beatstream');
	     $sarr = $this->getAll("WHERE id = '$sid'");
	     $sarr = $sarr[0];
	     if ( $insarr['vote'] == 1 ) {
	         $sarr['votes_up'] = intval($sarr['votes_up']) + 1;
	     }
	     else {
	         $sarr['votes_down'] = intval($sarr['votes_down']) - 1;
	     }
	     $sarr['rating'] = intval($sarr['rating']) + intval($insarr['vote']);
	     // now update the votes table with the new data
 	     return $this->update('id', $sid, $sarr, 'tbl_beatstream');
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
	  * Check if a record exists
	  * 
	  * @param string $id
	  * @return array details
	  */
	  public function checkRecord($id) {
	      $this->changeTable('tbl_beatstream');
	      return $this->getRecordCount("WHERE id = '$id'");
	  }
	  
	  /**
	   * Method to insert a suggestion
	   * 
	   * @param array $insarr
	   * @return string $id
	   */
	   public function insertSuggestion($insarr) {
	       $this->changeTable('tbl_beatstream');
	       $insarr['dt'] = $this->now();
	       return $this->insert($insarr, 'tbl_beatstream');
	   } 
	   
	   public function getSuggestions($ip) {
	       $now = date('Y-m-d');
	       // echo $now;
	       $query = "SELECT s.*, if (v.ip IS NULL,0,1) AS have_voted FROM tbl_beatstream AS s LEFT JOIN tbl_beatstream_votes AS v ON(s.id = v.suggestion_id AND v.day = $now AND v.ip = $ip) 
	                 ORDER BY s.rating DESC, s.id DESC";
	       $res = $this->getArray($query);
	       return $res;
	   }
	   
	   public function getSingle($ip, $id) {
	       $now = date('Y-m-d');
	       // echo $now;
	       $query = "SELECT s.*, if (v.ip IS NULL,0,1) AS have_voted FROM tbl_beatstream AS s LEFT JOIN tbl_beatstream_votes AS v ON(s.id = v.suggestion_id AND v.day = $now AND v.ip = $ip) 
	                 WHERE s.id = '$id' ORDER BY s.rating DESC, s.id DESC";
	       $res = $this->getArray($query);
	       return $res;
	   }
	   
	   public function deleteBeat($beatid) {
	       // get the details of the record so we can clean the podcast table using the suggestion text
	       $details = $this->getAll("WHERE id = '$beatid'");
	       // var_dump($details); die();
	       $podid = $details[0]['suggestion'];
	       
	       // clean up the votes
	       $this->changeTable('tbl_beatstream_votes');
	       $this->delete('suggestion_id', $beatid, 'tbl_beatstream_votes');
	       
	       // clean up the podcast
	       $this->changeTable('tbl_podcast');
	       $this->delete('id', $podid, 'tbl_podcast');
	       
	       // finally clean up the suggestion itelf
	       $this->changeTable('tbl_beatstream');
	       $this->delete('id', $beatid, 'tbl_beatstream');
	   }
	  
	  /**
	   * Method to dynamically change the table we are working with
	   *
	   * @param string $table
	   * @return void
	   */
	  public function changeTable($table) {
	      return parent::init($table);
	  }
}
?>
