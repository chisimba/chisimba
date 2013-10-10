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
class dbgalleries extends dbtable
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

        $this->objDBalbums = $this->getObject('dbalbums', 'imagegallery');
        $this->objDBimages = $this->getObject('dbimages', 'imagegallery');

        parent::init('tbl_imagegallery_galleries');
        $this->table = 'tbl_imagegallery_galleries';
    }

    /**
     *
     * Method to get all galleries
     * 
     * @access public
     * @return array The array of galleries 
     */
    public function getAllGalleries()
    {
        return $this->fetchAll();
    }
    
    /**
     *
     * Method to get all context galleries
     * 
     * @access public
     * @param string $contextCode The code of the context to get galleries for
     * @return array The array of context galleries 
     */
    public function getContextGalleries($contextCode)
    {
        return $this->fetchAll("WHERE `context_code` = '$contextCode' ORDER BY display_order ASC");
    }

    /**
     *
     * Method to get all user galleries
     * 
     * @access public
     * @param string $userId The id of the user to get galleries for
     * @return array The array of user galleries 
     */
    public function getUserGalleries($userId)
    {
        return $this->fetchAll("WHERE `user_id` = '$userId' ORDER BY display_order ASC");
    }
    
    /**
     *
     * Method to get a gallery
     * 
     * @access public
     * @param string $id The id of the gallery to get
     * @return array The array of gallery data
     */
    public function getGallery($id)
    {
        return $this->getRow('id', $id);
    }
    
    /**
     *
     * Method to add a gallery
     * 
     * @access public
     * @param array $fields The array of fields to add to the gallery table
     * @return string The id of the record created 
     */
    public function addGallery($fields)
    {
        if (array_key_exists('user_id', $fields))
        {
            $galleries = $this->getUserGalleries($fields['user_id']);
        }
        else
        {
            $galleries = $this->getContextGalleries($fields['context_code']);
        }
        $count = count($galleries);
        
        $fields['display_order'] = ++$count;
        $fields['created_by'] = $this->userId;
        $fields['date_created'] = date('Y-m-d H:i:s');

        return $this->insert($fields);
    }
    
    /**
     *
     * Method to update a gallery
     * 
     * @access public
     * @param string $id The id of the gallery to update
     * @param array $fields The array of fields to update on the gallery table
     * @return boolesn TRUE if the update was successfull | FALSE if not 
     */
    public function updateGallery($id, $fields)
    {
        $fields['updated_by'] = $this->userId;
        $fields['date_updated'] = date('Y-m-d H:i:s');
        
        return $this->update('id', $id, $fields);
    }
    
    /**
     *
     * Method to delete a gallery
     * 
     * @access public
     * @param string $id The id of the gallery to delete
     * @return boolean $result TRUE if the delete was successfull | FALSE if not 
     */
    public function deleteGallery($id)
    {
        $gallery = $this->getGallery($id);
        
        $result = $this->delete('id', $id);
        $this->objDBalbums->deleteGalleryAlbums('gallery_id', $id);
        $this->objDBimages->deleteGalleryImages('gallery_id', $id);
        
        if (!empty($gallery['user_id']))
        {
            $galleries = $this->getUserGalleries($gallery['user_id']);
        }
        else
        {
            $galleries = $this->getContextGalleries($gallery['context_code']);
        }
        
        if (!empty($galleries))
        {
            $i = 0;
            foreach ($galleries as $gallery)
            {
                $this->update('id', $gallery['id'], array('display_order' => ++$i));
            }
        }
        
        return $result;
    }
}
?>