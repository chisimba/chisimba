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
		$this->objLanguage = $this->getObject('language', 'language');
		$this->objGroups = $this->getObject('groupadminmodel');
		
		$objIcon = $this->getObject('geticon', 'htmlelements');
		$objIcon->setIcon('loader');
		$this->loading = $objIcon->show();
    }

//////NEW METHODS
	/**
	*Method to get the groups
	*/
	public function getGroups()
	{		
		$groups = $this->objLuAdmin->perm->getGroups();//$this->objGroups->getGroups();
		//var_dump($groups);
		if(count($groups) > 0)
		{
			$str = '<div id="accordion">';
			foreach($groups as $group)
			{
				$subGroups = $this->objGroups->getSubgroups($group['group_id']);
				//var_dump($subGroups);
				//if($subGroups)
				$str .='<div>
								<h3 id="'.$group['group_id'].'"><a href="#">'.$group['group_define_name'].'</a></h3>
								<div style="height:175px;" id="tab_'.$group['group_id'].'">
									
								<div class="siteadminlist">
										<div id="'.$group['group_id'].'_list">'.$this->loading.'</div>
								</div>
								</div>
						</div>
					';
			}
		}
		return $str.'</div>';
	}
	/**
	* Method to get first groupd Id
	*/
	public function getFirstGroupId()
	{
		$groups = $this->objLuAdmin->perm->getGroups();
		return $groups[0]['group_id'];
	}

	/**
	*Method to get the list of users to be searched
	*/
	public function getSearchableUsers()
	{
		$users = $this->objUser->getAll();
		$arr = array();
		foreach($users as $user)
		{
			$arr[$this->objUser->fullname($user['userid'])] = $user['userid'];
		}
		
		return $arr;
	}
	
	
	/**
	*Method to generate the display for a group
	* @param string $groupId
	*/
	public function loadGroupContent($groupId)
	{
		//show the users list
		//$arr = $this->objGroups->getGroupUsers($groupId);
		$arr = $this->getUsersInGroup($groupId); 		
		
		return $this->generateList($arr);
		$str ='
		<div class="groupadmincontent">
							<div class="siteadminlist">
								<div id="'.$groupId.'_list">'.$this->layoutUsers($arr).'</div>
							</div>
							<div id="siteadmintoolbox" >'.$this->searchUsersBox($groupId).'</div>
						</div>';
		
		//$str = $this->generateList($arr);
		//show the search box
		//$str .= $this->searchUsersBox($groupId);
		//return $this->
		//var_dump($usersGroup);
		return $str;
	}
	
	/**
	* Method to get the left menu
	*/
	public function getLeftMenu()
	{
		//side bar navigation object
		$objSideBar = $this->getObject('sidebar','navigation');
		
		//set the menu items -> Site Groups , Course Groups
		
		$nodes = array();
		
		$nodes[0]['text'] = $this->objLanguage->languageText ( "mod_groupadmin_sitegroups", "groupadmin" );
		$nodes[0]['uri'] = $this->uri(array('action' => 'sitegroups'));
		
		$nodes[1]['text'] = ucwords($this->objLanguage->code2Txt('mod_groupadmin_contextgroups', 'groupadmin', NULL, '[-context-] Groups'));
		$nodes[1]['uri'] = $this->uri(array('action' => 'contextgroups'));
		
		return $objSideBar->show($nodes);
		
	}
	
	
	
	
	/**
	* Method to load the content
	*/
	public function loadContent($groupId)
	{
		
		$str = '<div class="groupadmincontent">
							<div class="siteadminlist">
								<div class="siteadminscontent"  id="'.$groupId.'content">'.$this->loading.'</div>
							</div>
							<div class="siteadmintoolbox" id="'.$groupId.'toolbox" >'.$this->searchUsersBox().'</div>
						</div>';	
		return $str;
	}
	
	/**
	* Method to get the search box
	*/
	public function searchUsersBox()
	{
		
		
		return '<form autocomplete="off">
		
		<p>
			<label>Search Users:</label><br/>
			<textarea id="suggest4"></textarea><br/>
			<input type="button" value="Add to Group" />
		</p>
		</form>';
		$fieldSet->setLegend('Add Users to Group');
		return $fieldSet->show();
		
	}
	
	
	/**
	* Method to generate a list 
	*/
	public function generateList($arr)
	{
		if(count($arr) > 0)
		{
			$objIcon = $this->getObject('geticon', 'htmlelements');
			$objIcon->setIcon('delete','png');
			$str = '<div class="nicelist"><table>';
			foreach($arr as $list)
			{
					$str .= '<tr><td style="margin-right:0px;">'.$username = $this->objUser->fullName($list['auth_user_id']).'</td><td style="margin-left:0px;">'.$objIcon->show().'</td ></tr>';
			}
			$str .='</table></div>';
			return $str;
		} else {
			return '<span class="subdued">'. $this->objLanguage->languageText('mod_groupadmin_nousers','groupadmin').'</span>';
		}
	}


/**
	* Method to get the List of site Admins
	*/
	public function getSiteStudents()
	{
		
		
		return 'list of site students goes here ';
	}





////////////////////////////////////////


















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
        $currentUsers = $this->getUsersInGroup( $grpId );
        foreach ($data as $i => $user) {
            foreach ($currentUsers as $currentUser) {
                if ($currentUser['auth_user_id'] == $user['auth_user_id']) {
                    unset($data[$i]);
                    break 1;
                }
            }
        }
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
