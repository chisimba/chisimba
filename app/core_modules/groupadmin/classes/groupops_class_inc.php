<?php
/**
 * groupops class
 *
 * All the operations for groupadmin encapsulated
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
 *
 * @category  Chisimba
 * @package   groupadmin
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * The group admin operations class
 *
 * @copyright  Paul Scott
 * @package    groupadmin
 * @version    0.1
 * @since      28 Jan 2009
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @filesource
 */

class groupops extends object
{

    /**
     * $_objUsers an association to the userDb object.
     *
     * @access public
     * @var    userDb
     */
    public $objUser;

    /**
     * Method to initialize the group operations object.
     *
     * @access public
     * @param  void
     * @return void
     */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
    }

    public function getAllGroups() {
        $groups = $this->objLuAdmin->getGroups();
        return $groups;
    }

    public function getAllUsers() {
        $users = $this->objLuAdmin->getUsers();
        return $users;
    }

    public function getAllPermUsers() {
        $users = $this->objLuAdmin->perm->getUsers();
        return $users;
    }

    public function getNonGrpUsers() {
        $users = $this->objLuAdmin->getUsers(array('container' => 'auth'));
        return $users;
    }

    public function getUsersInGroup($groupid) {
        $params = array(
            'filters' => array(
                'group_id' => $groupid,
            )
        );
        $usersGroup = $this->objLuAdmin->perm->getUsers($params);

        return $usersGroup;
    }

    public function layoutGroups($groups, $numperRow = 5) {
        $gtable = $this->newObject('htmltable', 'htmlelements');
        $gtable->cellpadding = 5;
        $inners = NULL;
        $row = 0;
        $gtable->startRow();
        foreach($groups as $group) {
            $itable = $this->newObject('htmltable', 'htmlelements');
            $itable->cellpadding = 2;
            $icon = $this->newObject('geticon', 'htmlelements');
            $icon->setIcon('groupadmingrps');
            $href = $this->loadClass('href', 'htmlelements');
            $lnk1 = new href($this->uri(array('action' => 'editgrp', 'id' => $group['group_id'])), $icon->show(), NULL);
            $lnk = new href($this->uri(array('action' => 'editgrp', 'id' => $group['group_id'])),$group['group_define_name'], NULL);
            $grpname = $lnk->show();
            $itable->startRow();
            $itable->addCell($lnk1->show());
            $itable->endRow();
            $itable->startRow();
            $itable->addCell($grpname);
            $itable->endRow();
            // if the $row var is divisible by 4, start a new row
            if(is_int($row/$numperRow)) {
                $gtable->endRow();
                $gtable->startRow();
            }
            $gtable->addCell($itable->show());

            $row++;
        }
        $gtable->endRow();

        return $gtable->show();
    }

    public function layoutUsers($users, $grId, $numperRow = 5) {
        $utable = $this->newObject('htmltable', 'htmlelements');
        $utable->cellpadding = 5;
        $inners = NULL;
        $row = 0;
        $utable->startRow();
        foreach($users as $user) {
            $itable = $this->newObject('htmltable', 'htmlelements');
            $itable->cellpadding = 2;
            $icon = $this->newObject('geticon', 'htmlelements');
            $icon->setIcon('delete');
            $href = $this->loadClass('href', 'htmlelements');
            $lnk = new href($this->uri(array('action' => 'removeuser', 'id' => $user['perm_user_id'], 'grid' => $grId)), $icon->show(), NULL);
            $image = $this->objUser->getUserImage($user['auth_user_id']);
            $username = $this->objUser->fullName($user['auth_user_id']);
            $itable->startRow();
            $itable->addCell($image);
            $itable->startRow();
            $itable->addCell($username." ".$lnk->show());
            $itable->endRow();
            // if the $row var is divisible by 4, start a new row
            if(is_int($row/$numperRow)) {
                $utable->endRow();
                $utable->startRow();
            }
            $utable->addCell($itable->show());

            $row++;
        }
        $utable->endRow();

        return $utable->show();
    }

    public function getGroupInfo($groupid) {
        $groups = $this->objLuAdmin->perm->getGroups(array('filters' => array('group_id' => $groupid)));
        return $groups;
    }

    public function addUserForm($grpId) {
        $this->loadClass('form', 'htmlelements');
        $objForm = new form('adduser', $this->uri ( array( 'action' => 'editgrp', 'id' => $grpId ) )); //,'htmlelements');
        // Create the selectbox object
        // $this->loadClass('selectbox','htmlelements');
        $objSelectBox = $this->newObject('selectbox', 'htmlelements');
        // Initialise the selectbox.
        $objSelectBox->create( $objForm, 'leftList[]', 'Available Users', 'rightList[]', 'Users to add' );

        // Populate the selectboxes
        //$objData = &$this->getObject('data');
        $data = $this->getAllUsers ();
        $userArr = array();
        foreach ($data as $user) {
            $usr['label'] = $this->objUser->fullName($user['auth_user_id']);
            $usr['value'] = $user['perm_user_id'];
            $userArr[] = $usr;
        }
        $objSelectBox->insertLeftOptions( $userArr, 'value', 'label' );
        $objSelectBox->insertRightOptions( array() );

        // Insert the selectbox into the form object.
        $objForm->addToForm( $objSelectBox->show() );

        // Get and insert the save and cancel form buttons
        $arrFormButtons = $objSelectBox->getFormButtons();
        $objForm->addToForm( implode( ' / ', $arrFormButtons ) );

        // Show the form
        return $objForm->show();
    }

    public function addUserToGroup($users, $groupId) {
        $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        if(is_array($users)) {
            foreach ( $users as $user ) {
                $this->objGroups->addGroupUser( $groupId, $user );
            }
        }
        else {
            $this->objGroups->addGroupUser( $groupId, $users );
        }
    }

    public function removeUser($grid, $id) {
        $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        return $this->objGroups->deleteGroupUser( $grid, $id );
    }
}
?>