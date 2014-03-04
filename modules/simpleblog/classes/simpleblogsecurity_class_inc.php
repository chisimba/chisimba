<?php
/**
 *
 * Security class for Simple Blog
 *
 * Security class for Simple Blog which allows checking of rights before
 * performing some action
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
 * @author     Derek Keats <derek.keats@wits.ac.za>
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
 * Security class for Simple Blog
 *
 * Security class for Simple Blog which allows checking of rights before
 * performing some action
*
* @package   simpleblog
* @author    Derek Keats <derek.keats@wits.ac.za>
*
*/
class simpleblogsecurity extends object
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
    * Intialiser for the simpleblog ops class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Instantiate the user object.
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     *
     * Check for the user having the rights specified in the $chRights array
     *
     * @param string $blogId The id of the current blog
     * @param string $userId The userid of the user to look up
     * @param boolean $chRights Whether or not to check for group rights
     * @return boolean TRUE if the user has the rights, false if not
     * 
     */
    public function checkRights($bloggerId, $userId, $blogType)
    {
        if ($userId == $bloggerId) {
            return TRUE;
        } elseif ($blogType == 'site') {
            // If they are admin they can edit site posts by others
            if ($this->objUser->isAdmin()) {
                return TRUE;
            } else {
                return FALSE;
            }
        } elseif ($blogType == 'context') {
            // Edit context posts if they are in a context & are admin of it.
            return FALSE; // LEAVE IT FALSE NOW until I can come up with a better way
        } else {
            return FALSE;
        }
    }
    
    /**
     *
     * Check if a user should be able to create a blog based on
     * is admin or membership of the SimpleBlogger group
     * 
     * @return boolean If they have rights or not 
     * @access public
     */
    public function checkBloggingRights()
    {
        $ret=FALSE;
        $objUser = $this->getObject('user', 'security');
        $userId = $objUser->userId();
        // Admins can make blogs
        $objGa = $this->getObject('gamodel','groupadmin');
        $groupId = $objGa->getId("SimpleBloggers");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $edGroup = $objGroupOps->isGroupMember($groupId, $userId);     
        if ($objUser->isLoggedIn()) {
            if ($objUser->isAdmin() || $edGroup ) {
                $ret = TRUE;
            }
        }
        return $ret;
    }
}
?>