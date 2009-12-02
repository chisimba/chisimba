<?php
/**
 *
 *
 * PHP version 5.1.0+
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
 * @package   UserAdmin 
 * @author    Qhamani Fenama
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check

class utility extends object {


	public $objUserAdmin;
    /**
     * Constructor
     *
     * @access public
     *
     */
    public function init() {

	$this->objUserAdmin = $this->getObject('useradmin_model2','security');   
	$this->objLanguage = $this->getObject('language','language');     
    }




public function jsongetusers($start, $limit)
	{
		$params["start"] = ($this->getParam("start")) ? $this->getParam("start") : null;
		$params["limit"] = ($this->getParam("limit")) ? $this->getParam("limit") : null;
		$params["search"] = ($this->getParam("fields")) ? json_decode(stripslashes($this->getParam("fields"))) : null;
		$params["query"] = ($this->getParam("query")) ? $this->getParam("query") : null;
		$params["sort"] = ($this->getParam("sort")) ? $this->getParam("sort") : null;
	
		$where = "";		
	
		if(is_array($params['search'])){
			$max = count($params['search']);
		
			$cnt = 0;
		
			foreach($params['search'] as $field){
				$cnt++;
			
							
				$where .= $field.' LIKE "'.$params['query'].'%"';
						
				if($cnt < $max){
					$where .= " OR ";
				}
			}
		
			$where = ' WHERE '.$where;
		
		}
	
		$arr = array();
		
			$filter = " LIMIT $start , ".$params["limit"];
		    $userCount = count($this->objUserAdmin->getAll());
			$var_users = $this->objUserAdmin->getAll($where." ORDER BY title ".$filter);

			if(count($var_users) > 0){
				
				$arr = array();
				$users = array();
				foreach($var_users as $user){
						$arr = array();
						$arr['id'] = $user['id'];
						$arr['userid'] = $user['userid'];
						$arr['staffnumber'] = $user['staffnumber'];
						$arr['username'] = $user['username'];
						$arr['title'] = $user['title'];
						$arr['firstname'] = $user['firstname'];
						$arr['surname'] = $user['surname'];
						$arr['emailaddress'] = $user['emailaddress'];

						if ($user['howcreated'] == 'LDAP') {
						    $arr['howcreated'] = true;
						} else {
						    $arr['howcreated'] = false;
						}
						if ($user['isactive'] == '0') {
						   $arr['isactive'] = false;
						} else {
						    $arr['isactive'] = true;
						}

						/*Prepare Delete Link
						$this->loadclass('link','htmlelements');
						$objIcon = $this->newObject('geticon', 'htmlelements');
						$delLink = array(
							'action' => 'deleteuser',
							'id' => $user['id'],
							'module' => 'useradmin',
							'confirm' => 'yes'
						);
						$deletephrase = $this->objLanguage->languageText('delete_user_confirm', 'useradmin');
						$conf = $objIcon->getDeleteIconWithConfirm('', $delLink, 'useradmin', $deletephrase);
						$arr['delete'] = $conf;
						//End of Prepared Delete Link

						//Prepare Edit Link
						$sid = $user['id'];
						$editUserLink = new link();
						$editUserLink->link("javascript:showForm('$sid')");
						$objIcon->setIcon('edit');
						$editUserLink->link=$objIcon->show();
				        $arr['edit'] = $editUserLink->show();
						//End of Prepared Edit Link*/

				    	$users[] = $arr;
					}	    	
					return json_encode(array('usercount' => $userCount, 'users' =>  $users));
				}
				else {
					$arr['usercount'] = "0";
					$arr['users'] = array();
					return json_encode($arr);
			}
	}

	public function jsonSaveNewUser()
    {
		$userId = $this->objUserAdmin->generateUserId();
        $username = $this->getParam('useradmin_username');
        $password = $this->getParam('useradmin_password');
        $repeatpassword = $this->getParam('useradmin_repeatpassword');
        $title = $this->getParam('useradmin_title');
        $firstname = $this->getParam('useradmin_firstname');
        $surname = $this->getParam('useradmin_surname');
        $email = $this->getParam('useradmin_email');
        $sex = $this->getParam('useradmin_sex');
		$cellnumber = $this->getParam('useradmin_cellnumber');
        $staffnumber = $this->getParam('useradmin_staffnumber');
        $accountstatus = $this->getParam('accountstatus');
        $country = $this->getParam('countryId');
       
		$pkid = $this->objUserAdmin->addUser($userId, $username, $password, $title, $firstname, $surname, $email, $sex, $country, $cellnumber, $staffnumber, 'useradmin', $accountstatus);
       
     }

	public function jsonUsertaken($username)
	{
		$extjs = '0';	
		$status = $this->objUserAdmin->userNameAvailable($username);
		error_log(var_export('Status = '.$status, true));
		if($status == 1 )
		{
			$extjs = '1';
		}
		
		return json_encode(array('success' => true, 'data' => $extjs));
	}

	private function isValidUser($id, $errorcode='userviewdoesnotexist')
		 	    {
					if ($id == '') {
		 	            //return $this->nextAction(NULL, array('error'=>'noidgiven'));
		 	        }
		 	        
		 	        $user = $this->objUserAdmin->getUserDetails($id);
		 	        
					if ($user == FALSE) {
		 	            //return $this->nextAction(NULL, array('error'=>$errorcode));
		 	        } else {
		 	            return $user;
		 	        }
		 	    }

	public function jsonUpdateUserDetails()
    {
        
        $id = $this->getParam('id');
		error_log(var_export('id = '.$id, true));
        $user = $this->isValidUser($id, 'userdetailsupdate');
        //$this->setVarByRef('user', $user);
        
        // Fix up proper redirection
        if (!$_POST) {
            return $this->nextAction(NULL);
        }
        
        // Get Details from Form
        $password = $this->getParam('useradmin_password');
        $repeatpassword = $this->getParam('useradmin_repeatpassword');
        $title = $this->getParam('useradmin_title');
        $firstname = $this->getParam('useradmin_firstname');
        $surname = $this->getParam('useradmin_surname');
        $email = $this->getParam('useradmin_email');
        $cellnumber = $this->getParam('useradmin_cellnumber');
        $staffnumber = $this->getParam('useradmin_staffnumber');
        $sex = $this->getParam('useradmin_sex');
        $country = $this->getParam('countryId');
        $username = $this->getParam('useradmin_username');
        $accounttype = $this->getParam('accounttype');
        $accountstatus = $this->getParam('accountstatus');
        
        $userDetails = array(
            'password'=>$password,
            'repeatpassword'=>$repeatpassword,
            'title'=>$title,
            'firstname'=>$firstname,
            'surname'=>$surname,
            'email'=>$email,
            'sex'=>$sex,
            'country'=>$country
            );
            
           $update = $this->objUserAdmin->updateUserDetails($id, $username, $firstname, $surname, $title, $email, $sex, $country, $cellnumber, $staffnumber, $password, $accounttype, $accountstatus);
    }


	public function getSingleUser($id)
	{
		$where = " WHERE id = '".$id."'";		
	
		
		$arr = array();
		$singleuser = array();
		
		$users = $this->objUserAdmin->getAll($where);
		$totalCount = count($users);
		if(count($users) > 0){
		foreach($users as $user){
        //error_log(var_export('Test+'.$user['id'], true));
			$arr['id'] = $user['id'];
			$arr['userid'] = $user['userid'];
			$arr['useradmin_staffnumber'] = $user['staffnumber'];
			$arr['useradmin_username'] = $user['username'];
			$arr['useradmin_title'] = $user['title'];
			$arr['useradmin_firstname'] = $user['firstname'];
			$arr['useradmin_surname'] = $user['surname'];
			$arr['useradmin_email'] = $user['emailaddress'];
			$arr['useradmin_cellnumber'] = $user['cellnumber'];
			$arr['countryId'] = $user['country'];
			$arr['useradmin_sex'] = $user['sex'];
			$arr['accountstatus'] = $user['isactive'];
	}
		return json_encode(array('success' => true, 'data' =>  $arr));
	}
	else {
		$arr[] = array();
		return json_encode(array('success' => false, 'data' => $arr));
		}
	}
}
?>
