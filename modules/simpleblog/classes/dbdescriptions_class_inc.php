<?php
/**
 *
 * Database access for Simple blog descriptions
 *
 * Database access for Simple blog descriptions. This is the class that
 * manages data for the blog descriptions for users, contexts and site.
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
* Database access for Simple blog descriptions
*
* Database access for Simple blog descriptions. This is the class that
* manages data for the blog descriptions for users, contexts and site.
*
* @package   simpleblog
* @author     Derek Keats <derek.keats@wits.ac.za>
*
*/
class dbdescriptions extends dbtable
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
        // Instantiate the user object
        $this->objUser = $this->getObject('user', 'security');
        //Set the parent table to our demo table
        parent::init('tbl_simpleblog_blogs');
    }

    /**
     *
     * Delete a blog post
     *
     * @param string $id The id key of the record to delete
     * @return string An indication of the reuslts ('true' or 'norights')
     *
     */
    public function deleteBlog($id)
    {
        $chSql = "SELECT id, userid FROM tblsimpleblog_blogs WHERE id='$id'";
        $ar = $this->getArray($chSql);
        $me = $this->objUser->userId();
        $bloggerid = $ar[0]['userid'];
        if ($me == $bloggerid) {
            // I can delete
            $this->delete('id', $id);
            return "true";
        } else {
            return 'norights';
        }

    }

    /**
    * Method to retrieve the data for edit and prepare the vars for
    * the edit template.
    *
    * @param string $mode The mode should be edit or add
    */
    public function getForEdit($id)
    {
        // Get the data for edit
        $key="id";
        return $this->getRow('id', $id);
    }


    public function saveBlog()
    {
        $mode = $this->getParam('mode', 'add');
        //The current user
        $userId = $this->objUser->userId();
        $title = trim($this->getParam('blog_name', NULL));
        $description = trim($this->getParam('blog_description', NULL));
        $status = $this->getParam('post_status', NULL);
        $blogId = $this->getParam('blogid', NULL);
        if ($blogId == "") {
            $blogId = $userId;
        }
        $id = TRIM($this->getParam('id', NULL));
        // if edit use update
        if ($mode=="edit") {
            $rsArray=array(
                'blog_name'=>$title,
                'blog_description'=>$description,
                'modifierid'=>$userId,
                'blogid'=>$blogId,
                'datemodified'=>date('Y-m-d H:m:s')
            );
            $this->update("id", $id, $rsArray);
        } elseif ($mode=="add" || $mode="translate") {
            $this->insert(array(
                'blog_name'=>$title,
                'blog_description'=>$description,
                'userid'=>$userId,
                'blogid'=>$blogId,
                'datecreated'=>date('Y-m-d H:m:s')
            ));
        }
    }
}
?>