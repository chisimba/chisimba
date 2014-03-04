<?php
/**
 *
 * A simple wall module
 *
 * A simple wall module that makes use of OEMBED and that tries to look a bit like Facebook's wall
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
* Database accesss class for Chisimba for the module wall
*
* @author Derek Keats
* @package wall
*
*/
class dbcomment extends dbtable
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
    * Intialiser for the wall database connector
    * @access public
    *
    */
    public function init()
    {
        //Set the parent table here
        parent::init('tbl_wall_comments');
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     *
     * Default method to get the comment data as an array
     *
     * @param string $wallType The wall type (0=site wall, 1=personal or user wall, 2=context wall)
     * @param integer $num The number of results to return, defaulting to 10
     * @return string array An array of posts
     *
     */
    public function getComments($id, $num=10, $startPosition=0)
    {
        $sql = 'SELECT tbl_wall_comments.*,
          tbl_users.userid,
          tbl_users.firstname,
          tbl_users.surname,
          tbl_users.username,
          tbl_wall_posts.walltype,
          tbl_wall_posts.identifier,
          tbl_wall_posts.ownerid AS wallowner
        FROM tbl_wall_comments, tbl_users, tbl_wall_posts
        WHERE tbl_wall_comments.posterId = tbl_users.userid
        AND tbl_wall_comments.parentid = \'' .$id . '\'
        AND tbl_wall_posts.id = tbl_wall_comments.parentid
        ORDER BY datecreated DESC
        LIMIT ' . $startPosition . ", " . $num;

        return $this->getArray($sql);

    }

    /**
    *
    * Save a comment and return something to send back to the ajax request.
    *
    *
    * @return string The results of the save (true, empty, false)
    *
    */
    public function saveComment()
    {
        if ($this->objUser->isLoggedIn()) {
            $parentId = $this->getParam('id', NULL);
            // Trim off the bit added for sanity in Javascript
            $parentId = str_replace('cb_', NULL, $parentId);
            $wallComment = $this->getParam('comment_text', FALSE);
            $posterId = $this->objUser->userId();
            try
            {
                if ($wallComment) {
                    $this->insert(array(
                        'wallcomment' => $wallComment,
                        'posterid' => $posterId,
                        'parentid' => $parentId,
                        'datecreated' => $this->now()));
                    return 'true';
                } else {
                    return 'empty';
                }
            }
            catch (customException $e)
            {
                echo customException::cleanUp($e);
                die();
            }
        } else {
            return 'spoofattemptfailure';
        }
    }


    /**
     *
     * Delete all comments associated with a wall post
     *
     * @param string $id The id key of the record to delete
     * @return string An indication of the reuslts ('true' or 'norights')
     *
     */
    public function deleteAssociatedComments($id)
    {
        $this->delete('parentid', $id);
        return "true";
    }

    public function deleteComment($id) {
        $this->delete('id', $id);
        return "true";
    }
}
?>