<?php
 /**
 * User login history class
 * 
 * Class to handle login history of the user.
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
 * 
 * @category  Chisimba
 * @package   security
 * @author FSIU
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security che


/* -------------------- UserLoginHistory CLASS ----------------*/

/**
* Class to handle login history of the user
*/
class userLoginHistory extends dbTable {

    /**
    * Constructor method to define the table
    */
    public function init()
    {
        parent::init('tbl_userloginhistory');
    }

    /**
    * Method to add a login history entry to the tbl_userloginhistory 
    * table
    * @param string $userId: The Unique userId of the user who is logging in.
    * @access public
    * @return 
    */
    public function addHistoryEntry($userId)
    {
        $now = $this->now();
        $this->insert(
			array(
				'userid' => $userId,
                'lastLoginDateTime' => $now
			)
		);
    }

    /**
    * Property returns the last login date for the user
    * denoted by $userId
    * @modified by Megan Watson 29-09-2006 Removed call to fetchRow()
    * @param string $userId: The Unique userId of the user being looked up
    */
    public function doGetLastLogin($userId)
    {
        $sql="SELECT 
			MAX(lastLoginDateTime) AS laston 
		FROM 
			tbl_userloginhistory 
		WHERE 
			userid='$userId'
		";
        $rs = $this->query($sql);
        //$line = $rs->fetchRow();
        return $rs[0]['laston'];
    }
    /**
    * Property returns the last login date for the user
    * denoted by $userId
    * @param string $time: The time of the user being logged in
    * @access public
    * @return array
    */
    public function getLastLogin($time)
    {
    	$sql="SELECT user.username,user.userId,
			MAX(last.lastLoginDateTime) AS laston 
		FROM 
			tbl_userloginhistory as last, tbl_users as user
		WHERE 
			 last.lastLoginDateTime <'$time' AND user.userId=last.userId Group by userId
		";
        $rs = $this->query($sql);
       
        return $rs;
    }
    /**
    * Property returns the last login date for the user
    * denoted by $userId
    * @param none
    * @access public
    * @return array
    */
    public function getnowLogin()
    {
    	$now = date('Y-m-d H:');
        $sql="SELECT users.username,users.userId,
			MAX(last.lastLoginDateTime) AS laston 
		FROM 
			tbl_userloginhistory as last, tbl_users as users
		WHERE 
			 last.lastLoginDateTime >'$now'  AND users.userId=last.userId Group by users.username,users.userId
		";
        $rs = $this->query($sql);
       
        return $rs;
    }
}

?>
