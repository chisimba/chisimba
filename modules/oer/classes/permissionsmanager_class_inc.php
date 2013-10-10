<?php

/*
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
 * @version    0.001
 * @package    oer
 * @author     JCSE
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 */

/**
 * This class contains methods to check three types of permission levels: anonymous,
 * member and edit
 *
 * @author davidwaf
 */
class permissionsmanager extends object {

    
    private $EDITOR = "OER_EDITORS";

    function init() {
        $this->objUser = $this->getObject("user", "security");
    }

    /**
     * this tests if thos user is member of editor group
     * @return type 
     */
    public function isEditor() {
        return $this->hasPermissions($this->EDITOR);
    }

    /**
     * this simply tests if a the current user is logged in or not, effectively
     * testing whether the user is a member or not
     * @return type 
     */
    public function isMember() {
        return $this->objUser->isLoggedIn();
    }

    /**
     * this tests if the current user is a member of the group. This is used to
     * check whether to grant or revoke permissions onto a specific action. If
     * the current user is an admin, it always returns true
     * @param type $groupName The group to test 
     * @return type boolean
     */
    private function hasPermissions($groupName) {
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getId($groupName);
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $userId = $this->objUser->userId();
        if ($objGroupOps->isGroupMember($groupId, $userId) || $this->objUser->isAdmin()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>
