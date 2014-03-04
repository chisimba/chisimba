<?php
/**
 *
 * Database access for imagegallery
 *
 * Database access for imagegallery. This is a sample database model class
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
 * @package   imagegallery
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
* Database access for imagegallery
*
* Database access for imagegallery. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   imagegallery
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dbcomments extends dbtable
{
    /**
     * 
     * The name of the table
     *
     * @access public
     * @var string
     */
    public $table;
    
    /**
    *
    * Intialiser for the imagegallery database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();

        parent::init('tbl_imagegallery_comments');
        $this->table = 'tbl_imagegallery_comments';
    }

    /**
     *
     * Method to get all mages comments
     * 
     * @access public
     * @param string $imageId The id of the image to get comments for
     * @return array The array of image comments
     */
    public function getImageComments($imageId)
    {
        return $this->fetchAll("WHERE `image_id` = '$imageId' ORDER BY display_order DESC LIMIT 0, 5");
    }
    
    /**
     *
     * Method to get a comment
     * 
     * @access public
     * @param string $id The id of the comment to get
     * @return array The array of comment data
     */
    public function getComment($id)
    {
        return $this->getRow('id', $id);
    }
    
    /**
     *
     * Method to add a comment
     * 
     * @access public
     * @param array $fields The array of fields to add to the comment table
     * @return string The id of the record created 
     */
    public function addComment($fields)
    {
        $comments = $this->getImageComments($fields['image_id']);
        $count = count($comments);
        
        $fields['display_order'] = ++$count;
        $fields['created_by'] = $this->userId;
        $fields['date_created'] = date('Y-m-d H:i:s');

        return $this->insert($fields);
    }
    
    /**
     *
     * Method to update a comment
     * 
     * @access public
     * @param string $id The id of the comment to update
     * @param array $fields The array of fields to update on the comment table
     * @return boolesn TRUE if the update was successfull | FALSE if not 
     */
    public function updateComment($id, $fields)
    {
        $fields['updated_by'] = $this->userId;
        $fields['date_updated'] = date('Y-m-d H:i:s');
        
        return $this->update('id', $id, $fields);
    }
    
    /**
     *
     * Method to delete a comment
     * 
     * @access public
     * @param string $id The id of the comment to delete
     * @return boolean $result TRUE if the delete was successfull | FALSE if not 
     */
    public function deleteComment($id)
    {
        $comment = $this->getComment($id);
        
        $result = $this->delete('id', $id);
         
        $comments = $this->getImageComments($comment['image_id']);

        if (!empty($comments))
        {
            $i = 0;
            foreach ($comments as $comment)
            {
                $this->update('id', $comment['id'], array('display_order' => ++$i));
            }
        }
        
        return $result;
    }
    
}
?>