<?php
/**
 *
 * A news ops class
 *
 * An ops class to provide various rendering methods for the news module.
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
 * @package   News
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
 * A news ops class
 *
 * An ops class to provide various rendering methods for the news module.
*
* @package   news
* @author    Derek Keats <derek@dkeats.com>
*
*/
class newsops extends object
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
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
    *
    * Intialiser for the simpleblog ops class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the user object
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     *
     * Check if they are news authors
     *
     * @param string $groupId The group to look up (typically 'News author')
     * @return boolean TRUE|FALSE
     * @access public
     *
     */
    public function hasAuthorRights($groupId = 'News author')
    {
        if (!$this->objUser->isLoggedIn()) {
            $ret = FALSE;
        } else {
            $userId = $this->objUser->userId();
            $objGroup = $this->getObject('groupadminmodel', 'groupadmin');
            // If they are admin or in News author then they are News admin
            if ($this->objUser->isAdmin() || $objGroup->isGroupMember($userId, $groupId)) {
                $ret = TRUE;
            }
        }

    }

}
?>