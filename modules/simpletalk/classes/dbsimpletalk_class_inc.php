<?php
/**
 *
 * Database access for simpletalk
 *
 * Database access for Submit a conference talk. This is a database model class
 * that provides data access to the default module table - tbl_simpletalk_abstracts.
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
 * @package   simpletalk
 * @author    Derek Keats derek@dkeats.com
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
 * Database access for simpletalk
 *
 * Database access for Submit a conference talk. This is a database model class
 * that provides data access to the default module table - tbl_simpletalk_abstracts.
*
* @package   simpletalk
* @author    Derek Keats derek@dkeats.com
*
*/
class dbsimpletalk extends dbtable
{
    
    /**
     *
     * @var string Object $objUser String for the user object
     * @access public
     *
     */
    public $objUser;

    /**
    *
    * Intialiser for the simpletalk database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_simpletalk_abstracts');
        // Instantiate the user object.
        $this->objUser = $this->getObject('user', 'security');
    }
    
    /**
     * 
     * Save a talk proposal with abstract
     * 
     * @param string $mode Edit or Add
     * @return integer The Id of the created/updated record
     * @access public
     * 
     */
    public function save($mode=NULL)
    {
        if ($mode == 'edit') {
            $id = TRIM($this->getParam('id', NULL));
            $rsArray = array(
                'datemodified' => $this->now(),
                'userid' => $this->objUser->userId(),
                'emailadr' => $this->getParam('emailadr', NULL),
                'title' => $this->getParam('title', NULL),
                'authors' => $this->getParam('authors', NULL),
                'duration' => $this->getParam('duration', NULL),
                'track' => $this->getParam('track', NULL),
                'abstract' => $this->getParam('abstract', NULL),
                'requirements' => $this->getParam('requirements', NULL)
            );
            $this->update("id", $id, $rsArray);
            return $id;
        } else {
            $userId = $this->objUser->userId();
            if ($userId == NULL) {
                $userId = "NOTLOGGEDIN";
            }
            $rsArray = array(
                'datecreated' => $this->now(),
                'userid' => $userId,
                'emailadr' => $this->getParam('emailadr', NULL),
                'title' => $this->getParam('title', NULL),
                'authors' => $this->getParam('authors', NULL),
                'duration' => $this->getParam('duration', NULL),
                'track' => $this->getParam('track', NULL),
                'abstract' => $this->getParam('abstract', NULL),
                'requirements' => $this->getParam('requirements', NULL)
            );
           return $this->insert($rsArray);
        }
    }
    
    /**
     * 
     * Get an abstract for edit by id
     * 
     */
    public function getAbstractForEdit($id)
    {
        // Get the data for edit
        return $this->getRow('id', $id);
    }

    /**
     *
     * Get the text of the init_overview that we have in the sample database.
     *
     * @return string The text of the init_overview
     * @access public
     *
     */
    public function getAbstracts($whereClause=NULL)
    {
        $sql = '
            SELECT tbl_simpletalk_abstracts.*,
            tbl_simpletalk_tracks.track,
            tbl_simpletalk_tracks.track_label,
            tbl_simpletalk_durations.duration,
            tbl_simpletalk_durations.duration_label
            FROM tbl_simpletalk_abstracts
            INNER JOIN tbl_simpletalk_tracks 
            ON tbl_simpletalk_abstracts.track = tbl_simpletalk_tracks.track
            INNER JOIN tbl_simpletalk_durations 
            ON tbl_simpletalk_abstracts.duration = tbl_simpletalk_durations.duration            
            ' . $whereClause;
        return $this->getArray($sql);
    }

}
?>