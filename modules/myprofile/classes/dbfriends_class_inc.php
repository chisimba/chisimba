<?php
/**
 *
 * A model class for working with FOAF friends
 *
 * A model class for working with FOAF friends, which are part of the FOAF
 * module, and stored in the table tbl_foaf_friends.
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
 * @package   wall
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbwall.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
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
 * A model class for working with FOAF friends
 *
 * A model class for working with FOAF friends, which are part of the FOAF
 * module, and stored in the table tbl_foaf_friends.
*
* @author Derek Keats
* @package wall
*
*/
class dbfriends extends dbtable
{
    /**
    *
    * @var string object The user object
    * @access public
    * 
    */
    public $objUser;
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
    *
    * Intialiser for the wall database connector
    * @access public
    *
    */
    public function init()
    {
        //Set the parent table here
        parent::init('tbl_wall_posts');
        $this->objUser = $this->getObject('user', 'security');
        // Instantiate the language object.
        $this->objLanguage = $this->getObject('language', 'language');

    }

    /**
     *
     * Default method to get the friends data as an array. Note that it includes
     * a count of each friend's friends as well.
     *
     * @param string $wallType The wall type (0=site wall, 1=personal or user wall, 2=context wall)
     * @param integer $num The number of results to return, defaulting to 10
     * @return string array An array of posts
     *
     */
    public function getFriends($userId, $page=1)
    {
        // Since we start counting from zero
        $page=$page-1;
        // We are allowing 42 users per page like FB
        $startPoint = $page * 42;
        // The base SQL, uses joins to avoid going back and forth to the db
        $sql = 'SELECT tbl_foaf_friends.*,
              tbl_users.userid AS user_userid,
              tbl_users.firstname,
              tbl_users.surname,
              tbl_users.username,
              tbl_users.emailaddress,
              (SELECT COUNT(tbl_foaf_friends.userid)
                   FROM tbl_foaf_friends
                   WHERE tbl_foaf_friends.userid = tbl_users.userid
              ) AS friends
              FROM tbl_foaf_friends, tbl_users
              WHERE tbl_foaf_friends.userid = \'' .$userId . '\'
              AND tbl_foaf_friends.fuserid = tbl_users.userid
              ORDER BY tbl_users.firstname';
        $friendsArray = $this->getArrayWithLimit($sql, $startPoint, 42);
        return $friendsArray;
    }
}
?>