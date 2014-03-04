<?php
/**
 *
 * Database access for Page annotations
 *
 * Database access for Page notes, which allow users
 * to add annotations the the margin on any page containing 
 * the annotation controller block
 *
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
 * @package   pagenotes
 * @author    Derek Keats <derek@dkeats.com>
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
 * Database access for Page annotations
 *
 * Database access for Page notes, which allow users
 * to add annotations the the margin on any page containing 
 * the annotation controller block
*
* @package   pagenotes
* @author    Derek Keats <derek@dkeats.com>
*
*/
class dbpageannotations extends dbtable
{
    
    /**
    * 
    * @var string $objUser String object property for holding the user object
    * @access public
    * 
    */
    public $objUser;

    /**
    *
    * Intialiser for the pagenotes database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_pagenotes_annotations');
        $this->fieldsAr = array('id', 'hash', 'annotation_mode', 'annotation');
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     *
     * Save the note against a particularpage.
     *
     * @return boolean
     * @access public
     *
     */
    public function save()
    {
        if ($this->objUser->isLoggedIn()) {
            $userId = $this->objUser->userId();
            $this->loadData();
            if ($this->annotation_mode == 'edit') {
                // Make sure only the user can update their annotations
                if ($this->validUser($userId)) {
                    // Update the note
                    $data = array(
                        'datemodified' => $this->now(),
                        'annotation' => $this->annotation
                    );
                    $res = $this->update('id', $this->id, $data);
                    return $this->id;
                } else {
                    return FALSE;
                }
            } else {
                // Add the data notes data.
                $data = array(
                    'hash' => $this->hash, 
                    'annotation' => $this->annotation, 
                    'datecreated' => $this->now(),
                    'userid' => $userId
                );
                $res = $this->insert($data);
                return $res;
            }
        } else {
            return FALSE;
        }
    }
    
    public function getById($id)
    {
        $filter = "WHERE id = '$id'";
        return $this->getAll($filter);
    }
    
    private function deleteItem($id) {
        if ($this->objUser->isLoggedIn()) {
            $userId = $this->objUser->userId();
            $arItem = $this->getById($id);
            $ownerId = $arItem[0]['userid'];
            if ($userId == $ownerId) {
                $ret = $this->delete('id', $id);
                if ($ret) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }
    }
    
    /**
     *
     * Check if the user attempting to save is the owner of the record
     * 
     * @param string $userId The userId of the current user
     * @return boolean TRUE|FALSE
     * @access private
     * 
     * 
    */
    private function validUser($userId)
    {
        $res = $this->getRow('hash', $this->hash);
        if ($res['userid'] == $userId) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * 
     * Load the data where each field becomes a property of this class
     * 
     * @param boolean $sanityCheck Whether or to perform security parsing
     * @access private
     * @return VOID
     * 
     */
    private function loadData($sanityCheck=FALSE)
    {
        if ($sanityCheck) {
            $objSanity = $this->getObject('sanitizevars', 'security');
        }
        foreach($this->fieldsAr as $field) {
            if ($sanityCheck) {
                $strValue = $this->getParam($field, NULL);
                if ($strValue!==NULL) {
                    // No fields in the querystring
                    $objSanity->disallowQuerystringFormElements($this->fieldsAr);
                    $this->$field = $objSanity->sanitize($strValue, FALSE, TRUE);
                }
            } else {
                $this->$field = $this->getParam($field, NULL);
            }
        }
    }
    
    /**
     *
     * Get the annotations based on the current page hash and the current
     * logged in user
     * 
     * @return string A JSON string of all the annotations
     * @access public 
     * 
     */
    public function getAnnotations()
    {
        $hash = $this->getHash();
        $userId = $this->objUser->userId();
        $filter = " WHERE hash = '" . $hash . "' AND userid = '" . $userId . "'";
        $ar = $this->getAll($filter);
        return $ar;
    }
    
    /**
     * 
     * Get a hash of the current page URL minuse any passthrough login 
     * parameters if they exist.
     * 
     * @return string The MD5 hash of the current URL
     * @access private
     * 
     */
    private function getHash()
    {
        $objUrl = $this->getObject('urlutils', 'utilities');
        $page = $objUrl->curPageURL();
        $page = str_replace('&passthroughlogin=true', NULL, $page);
        return md5($page);
    }
}
?>