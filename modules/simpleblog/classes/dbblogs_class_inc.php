<?php
/**
 *
 * Database access for Simple blog tbl_simpleblog_blogs
 *
 * Database access for Simple blog. It allow access to the table
 * tbl_simpleblog_blogs which contains lists and descriptions of blogs.
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
 * @package   simpleblog
 * @author    Derek Keats <derek.keats@wits.ac.za>
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
 * Database access for Simple blog tbl_simpleblog_blogs
 *
 * Database access for Simple blog. It allow access to the table
 * tbl_simpleblog_blogs which contains lists and descriptions of blog
*
* @package   simpleblog
* @author     Derek Keats <derek.keats@wits.ac.za>
*
*/
class dbblogs extends dbtable
{

    /**
    *
    * Intialiser for the simpleblog database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_simpleblog_blogs');
    }

    /**
     *
     * Get more older posts for appending to the bottom of existing posts
     * by Ajax
     *
     * @param integer $wallType The wall type (1,2,3)
     * @param integer $page The starting page
     * @param string $keyName The name of the key (contextcode usually)
     * @param string $keyValue The value of the key (usually contextcode)
     * @param integer $num The number of records to return (pagesize)
     * @return string array An array of posts if any
     * @access public
     *
     */
    public function getAllBlogsRecentFirst($blogId, $page=1, $pageSize=10)
    {
        // Subtract 1 from page since the first page is 0
        $page=$page-1;
        // The base SQL, uses joins to avoid going back and forth to the db
        $baseSql = 'SELECT * FROM tbl_simpleblog_blogs ORDER BY datecreated DESC';
        $startPoint = $page * $pageSize;
        $posts = $this->getArrayWithLimit($baseSql, $startPoint, $pageSize);
        return $posts;
    }

    /**
     *
     * Get the blog id of a particular user
     *
     * @param string $userId The userid of the user to look up
     * @return string The blog id of the user
     * @access public
     *
     */
    public function getUserBlogId($userId) {
        if (!$userId == NULL) {
            $sql = 'SELECT blogid FROM tbl_simpleblog_blogs WHERE userid=\''
              . $userId . '\' AND blogtype=\'personal\'';
            $result = $this->getArrayWithLimit($sql, 0, 1);
            if (is_array($result)) {
                if (count($result) > 0) {
                    $blogId = $result[0]['blogid'];
                    return $blogId;
                } else {
                    return NULL;
                }
            }
        }
        return NULL;
    }

    /**
     *
     * Look up the user id of the owner of a blog identified by its blog id
     *
     * @param string $blogId The blog id to look up
     * @return string The userid of the person owning the blog
     * @access public
     *
     */
    public function getOwnerId($blogId)
    {
        $sql = 'SELECT userid FROM tbl_simpleblog_blogs WHERE blogid=\''
          . $blogId . '\' AND blogtype=\'personal\'';
        $result = $this->getArrayWithLimit($sql, 0, 1);
        if ($result) {
            $userId = $result[0]['userid'];
            return $userId;
        } else {
            return NULL;
        }
    }
    
    /**
    * Method to retrieve a record for edit
    *
    * @param string $id The id of the record to retrieve
    * @return string Array A one row array containing the record data
    * @access public
    *
    */
    public function getForEdit($id)
    {
        // Get the data for edit
        return $this->getRow('id', $id);
    }

    /**
    *
    * Method to retrieve the data for edit by blogId
    *
    * @param string $mode The mode should be edit or add string Array A one row array containing the record data
    * @access public
    *
    */
    function getBlogInfo($blogId)
    {
        // Get the data for edit
        return $this->getRow('blogid', $blogId);
    }

}
?>