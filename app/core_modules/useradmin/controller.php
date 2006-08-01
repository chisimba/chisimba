<?php
/* -------------------- useradmin class extends controller ----------------*/
                                                                                                                                             
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
                                                                                                                                             
/**
* Administration of users.
* @copyright (c) 2004 University of the Western Cape
*/

class useradmin extends controller
{
    var $objConfig;
    var $objLanguage;
    var $objButtons;
    var $objUserAdmin;
    var $objUser;
    var $isAdmin;
    var $rstatus; // shows whether a function-call did what was wanted or not
    var $rvalue;    // the return-value for the template to be used.
    //var $info;  // for passing information around the class
 
    function init()
    {
        $this->objConfig =& $this->getObject('altconfig','config');
        $this->objLanguage =& $this->getObject('language','language');
        $this->objButtons=&$this->getObject('navbuttons','navigation');
        $this->objUserAdmin=&$this->getObject('useradmin_model','security');
        $this->objUser =& $this->getObject('user', 'security');
        if ($this->objUser->isLoggedIn()){
            //Get the activity logger class
            $this->objLog=$this->newObject('logactivity', 'logger'); 
            //Log this module call 
            $this->objLog->log();
        }
    }


    function dispatch($action) 
    {
		$this->setVar('pageSuppressXML',true);
	    $this->isAdmin=$this->objUser->isAdmin();       
        if (!$this->isAdmin && $this->requiresAdmin($action)) {
			die('Access denied');
        }
        if (!$this->requiresAdmin($action)) {
            //$this->setLayoutTemplate("user_layout_tpl.php");
        }
        switch ($action)
        {
            case 'register':
                $this->setLayoutTemplate(NULL);
                $this->rvalue='register_tpl.php';
                break;
            case 'registerapply':
                $this->setLayoutTemplate(NULL);
                $this->registerApply();
                break;
            case 'add':
                $this->add();
                break;    
            case 'addapply':
                $status=$this->checkAdd(
					$this->getParam('username'),
					$this->getParam('password'),
					$this->getParam('email'),
					$this->getParam('passwd'),
					$this->getParam('userId')
				);
                if ($status===true)
				{
                    $this->addApply();
					$this->rvalue=$this->nextAction('listusers', array('how'=>'surname', 'searchField'=>'A'));
					/*
				    $users=$this->objUserAdmin->getUsers('creationdate',date('Y-m-d'));
                    $userdata=$this->makeTableFromUsers($users,TRUE);
                    $this->setVar('userdata',$userdata);
                    $title = $this->objLanguage->languageText('mod_useradmin_newuseradded','useradmin');
                    $this->setVar('title', $title);
                    $this->rvalue='list_users_tpl.php';
					*/
                } 
				else 
				{
                    $this->rstatus=$status;
                    $this->rvalue='error_tpl.php';
                }
                break;
            case 'edit':
                $this->setVar('isAdminUser',TRUE);
                $this->edit($this->getParam('userId'));
                break;
			case 'mydetails':
                $this->setVar('isAdminUser',FALSE);
                $this->edit($this->objUser->userId());
                break;
            case 'selfedit':
                $this->setVar('isAdminUser',FALSE);
                $this->edit($this->getParam('userId'));
                break;
            case 'editapply':
                $status=$this->checkEdit($this->getParam('userId'));
                if ($status===true)
                {
                    $this->editApply($this->getParam('userId'));
                }
                else
                {
                    $this->rvalue='error_tpl.php';
                    $this->rstatus=$status;
                }
                break;
            case 'delete':
                $status=$this->checkDelete($this->getParam('userId'));
                if ($status===true){
                    $this->deleteApply($this->getParam('userId'));
	                return $this->nextAction('listusers', array('how'=>'surname', 'searchField'=>'A'));
                } 
				else 
				{
					$results = $this->objUserAdmin->getUsers('userId',$userId,TRUE);
		            $this->userdata=$results[0];
		            $this->rvalue='confirmdelete_tpl.php';
                }
                break;
            case 'selfdelete':
                $this->rvalue='selfdelete_tpl.php';
                if ($this->selfDelete($this->objUser->userId())){
                    $this->objUser->logout();
                    $this->rvalue='ok_tpl.php';
                }       
                break;
            case 'batchdelete':
                $this->batchDelete($this->getArrayParam('userArray'));
                return $this->nextAction(
					'listUsers',
					array(
						'how'=>$this->getParam('how'),
						'searchField'=>$this->getParam('searchField')
					)
				);
                break;
            case 'needpassword':
                $this->rvalue='forgotpassword_tpl.php';
                break;
            case 'changepassword':
				$this->setVar('userId',$this->getParam('userId'));
		        $this->rvalue='changepassword_tpl.php';
                break;
			case 'changepasswordapply':
                $this->rvalue=$this->changePassword($this->getParam('userId'));
				break;
			/*
            case 'adminchangepassword':
                $this->rvalue=$this->adminChangePassword($this->getParam('userId'));
                break;
			*/
            case 'resetpassword':
                $this->rstatus=$this->resetPassword($this->getParam('username'),$this->getParam('email'));
                $this->rvalue='ok_tpl.php';
                break; 
            case 'imageupload':
                $this->imageUpload();
                if ($this->getParam('isAdminUser') == '1') {
                    $nextaction = 'edit';
                } else {
                    $nextaction = 'selfedit';
                }
                return $this->nextAction($nextaction, array('userId'=>$this->getParam('userId')));
            case 'imagereset':
                $objImage=$this->newObject('imageupload','useradmin');
                $objImage->resetImage($this->getParam('userId'));
                $objImage->resetImage($this->getParam('userId')."_small");
                if ($this->getParam('isAdminUser') == '1') {
                    $nextaction = 'edit';
                } else {
                    $nextaction = 'selfedit';
                }
                return $this->nextAction($nextaction, array('userId'=>$this->getParam('userId')));
                break;
            case 'listusers':
                $how=$this->getParam('how');
                $match=stripslashes($this->getParam('searchField'));                
                if ($this->getParam('search', NULL) != NULL) {
                    $title = $this->objLanguage->languageText('mod_useradmin_searchresultsfor','useradmin').' ('.$match.')';
                } else {
                    if ($this->getParam('message', NULL) == 'added') {
                        $title = $this->objLanguage->languageText('mod_useradmin_newuseradded','useradmin');
                    } else if ($match == 'listall') {
                        $title = $this->objLanguage->languageText('mod_useradmin_showingallusers','useradmin');
                    } else {
                        $title = $this->objLanguage->languageText('mod_useradmin_listingusersbysurname','useradmin').' ('.$match.')';
                    }
                }
                $this->setVarByRef('title', $title);
                $users=$this->objUserAdmin->getUsers($how,$match,FALSE);
                $usersTable=$this->makeTableFromUsers($users,TRUE);
                $this->setVar('usersTable',$usersTable);
                $this->rvalue='list_users_tpl.php';
                break;
            case 'listunused':
                $userData=$this->objUserAdmin->getUsers('notused','','TRUE');
                $userdata=$this->makeTableFromUsers($userData,TRUE);
                $this->setVar('userdata',$userdata);
                $title = $this->objLanguage->languageText('mod_useradmin_unusedaccounts');
                $this->setVar('title', $title);
                $this->rvalue='list_users_tpl.php';
                break;
            default:
                return $this->nextAction('listusers', array('how'=>'surname', 'searchField'=>'A'));
        }
        $this->message=$this->rstatus;
        $this->setvar('message',$this->message);	
        return $this->rvalue;
    }

    
    /** 
    * This is a method to determine if the user has to be logged in or not
    * It overides that in the parent class
    * @returns boolean
    */
    function requiresLogin() 
    {
        $action=$this->getParam('action','NULL');
        switch ($action)
        {
            case 'register':
            case 'registerapply':
            case 'needpassword':
            case 'resetpassword':
                //$this->setVar('pageSuppressToolbar', TRUE);
                return FALSE;
            default:
                return TRUE;
        }	
     }

    /** 
    * This is a method to test if user needs to be admin
    * @author James Scoble
    * @param string $cmd - the action send by the URL
    * @return boolean TRUE or FALSE
    */
    function requiresAdmin($action)
    {
        switch($action)
        {
            case 'register':
            case 'registerapply':
            case 'mydetails':
            case 'selfedit':
            case 'editapply':
            case 'selfdelete':
            case 'changepassword':
            case 'changepasswordapply':
            case 'imageupload':
            case 'imagereset':
            case 'needpassword':
            case 'resetpassword':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
    }
    
    /**
    * Check to see if the site is an "alumni" one, and process more data if it is.
    */
    function checkAlumni()
    {
        $systemType = $this->objConfig->getValue("SYSTEM_TYPE", "contextabstract");        
        if ($systemType=='alumni'){
	        //$objAlumni=&$this->getObject('alumniusers','alumni');
	        //$objAlumni->addAlumniInfo();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    * Add a self-registering user.
    */
    function registerApply()
    {
        $fields=array('userId','firstname','surname','username','email','title','sex','country');
        foreach ($fields as $field)
        {
            $$field=$this->getParam($field);
        }
        $result=$this->objUserAdmin->checkUserIdAvailable($userId);
        if ($result!==true)
        {
            $this->rstatus=$result;
            $this->rvalue='register_tpl.php';
        }
        $result=$this->objUserAdmin->checkUsernameAvailable($username);
        if ($result!==true)
        {
            $this->rstatus=$result;
            $this->rvalue='register_tpl.php';
        }
        //$password=rand(10000,99999);
        $objPassword=&$this->getObject('passwords','useradmin');
        $password=$objPassword->createPassword();
        $cryptpassword=sha1($password);
        $data=array(
	        'userid'=>$userId,
	        'username'=>$username,
	        'title'=>$title,
	        'firstname'=>$firstname,
	        'surname'=>$surname, 
	        'pass'=>$cryptpassword,
	        'creationdate'=>date("Y-m-d"),
	        'howcreated'=>'selfregister', 
	        'emailaddress'=>$email, 
	        'sex'=>$sex, 
	        'country'=>$country
	        );
        $this->objUserAdmin->insert($data);
        $this->setVar('newdata',$data);
        $this->setVar('newpassword',$password);
        $this->rvalue='registersuccess_tpl.php';
        //--$this->sendRegisterInfo($firstname,$surname,$userId,$username,$title,$email,$password,'GUEST');
        //$this->objUserAdmin->emailPassword($firstname,$surname,$userId,$username,$email,$password);
    }

    /**
    * Email a new user the information about the account that's been created.
    * Calls the language object, and PHP's built-in email functionality.
    * @param string $firstname
    * @param string $surname
    * @param string $userId
    * @param string $username
    * @param string $title
    * @param string $email
    * @param string $password
    * @param string $accesslevel - depereciated but still included in case its needed again in a future version
    */ 
    function sendRegisterInfo($firstname,$surname,$userId,$username,$title,$email,$password,$accesslevel='')
    {
        $subject=$this->objLanguage->languageText('mod_useradmin_greet6'); 
        $subject=str_replace('KEWL NextGen',$info['sitename'],$subject);
        $info=$this->objUserAdmin->siteURL();
		$greet1 = $this->objLanguage->languageText('mod_useradmin_greet1')."\n";
		$greet1 = str_replace('FIRSTNAME',$firstname,$greet1);
		$greet1 = str_replace('SURNAME',$surname,$greet1);
        $content=
		$greet1
        .$this->objLanguage->languageText('mod_useradmin_greet2')."\n"
        .$this->objLanguage->languageText('mod_useradmin_greet3')."\n"
        .$this->objLanguage->languageText('mod_useradmin_greet4')."\n"
        .$this->objLanguage->languageText('word_userid').": $userId\n"
        .$this->objLanguage->languageText('word_surname').": $surname\n"
        .$this->objLanguage->languageText('phrase_firstname').": $firstname\n"
        .$this->objLanguage->languageText('word_title').": $title\n"
        .$this->objLanguage->languageText('word_username').": $username\n"
        .$this->objLanguage->languageText('word_password').": $password\n"
        .$this->objLanguage->languageText('phrase_emailaddress').": $email\n"
        //."Group membership: $accesslevel\n"
        .$this->objLanguage->languageText('mod_useradmin_greet7','To login, go to')." "
        .$info['link']." (".$info['url'].")\n"
        .$this->objLanguage->languageText('word_sincerely')."\n"
        .$this->objLanguage->languageText('mod_useradmin_greet5')."\n";
        $content=str_replace('KEWL NextGen',$info['sitename'],$content);
        $header="From: ".$this->objLanguage->languageText('mod_useradmin_greet5').'<noreply@'.$info['server'].">\r\n";
        @mail($email,$subject,$content,$header);
    }

    /**
    * Add new user.
    */
    function add()
    {
		$this->setVar('mode','add');
		$this->rvalue='addedit_tpl.php';
    }

    /**
    * Check user information.
    * @param string $username
    * @param string $password
    * @param string $email
    * @param string $passwd - the second copy of the password
    * @param string $userId
    * @returns boolean
    */
    function checkAdd($username,$password,$email,$passwd,$userId)
    {      
        if ($username=="") { return("need_username"); }
        if ($password=="") { return("need_password"); }
        if ($email=="") { return "need_email"; }
        if ($password!=$passwd) { return "password_not_match";}
        $result=$this->objUserAdmin->checkUserIdAvailable($userId);
        if ($result!==true) { return $result; }
        $result=$this->objUserAdmin->checkUsernameAvailable($username);
        if ($result!==true) { return $result; }
        // passess all other tests, then...
        return true;
    }

    /**
    * This is a method to insert info into database
    * @author James Scoble
    *
    */
    function addApply()
    {
        $data['userid']=$this->getParam('userId');
        $data['username']=$this->getParam('username');
        $data['title']=$this->getParam('title');
        $data['firstname']=$this->getParam('firstname');
        $data['surname']=$this->getParam('surname');
        $data['pass']=sha1($this->getParam('password'));
        $data['creationdate']=date("Y-m-d");
        $data['howcreated']='useradmin';
        $data['emailaddress']=$this->getParam('email');
        $data['sex']=$this->getParam('sex');
        $data['country']=$this->getParam('country');
        $result=$this->objUserAdmin->insert($data);
        if (!$result) 
        { 
            $this->rstatus="changes_failed";
            //$this->rvalue='error_tpl.php'; 
            $this->rvalue='useradd_tpl.php'; 
        }
        else
        {
            $this->rstatus="user_added";
            $this->rvalue='ok_tpl.php';
        }
    } // end of function applyadd
      
    /**
    * Edit.
    * @param string $userid User ID
    */
    function edit($userId)
    {
        $results=$this->objUserAdmin->getUsers('userid',$userId,TRUE);
        if (empty($results)) 
        { 
            $this->rstatus= 'error_no_userid';
            $this->rvalue='error_tpl.php';
        }  
        else
        {
			$userDetails = $results[0];
            $this->setvar('userDetails',$userDetails);
            $this->setvar('isLDAPUser',$this->objUserAdmin->isLDAPUser($userId));
			$this->setVar('mode','edit');
            $this->rvalue='addedit_tpl.php';
        } 
    }


    /**
    * Check for illegal values.
    */
    function checkEdit($userId)
    {
        if ($this->getParam('username')==""){
            return("need_username");  
        }
        if ($this->getParam('email')==""){
            return "need_email"; 
        }      
		// non-admin trying to edit someone else?
        if ((!$this->isAdmin)&&($this->getParam('userId')!=$this->objUser->userId())) {
            return('Not Admin!');
        }
        if ($this->getparam('username')!=$this->getParam('oldUsername')){
            $result=$this->objUserAdmin->checkUsernameAvailable($this->getParam('username'));
            return $result;
        }
        return true;
    }


    /**
    * Apply edit.
    */
    function editApply($userId)
    {
        $data['username']=$this->getParam('username');
        $data['title']=$this->getParam('title');
        $data['firstname']=$this->getParam('firstname');
        $data['surname']=$this->getParam('surname');
        $data['emailaddress']=$this->getParam('email');
        $data['sex']=$this->getParam('sex');
        $data['country']=$this->getParam('country');
        //$sdata['accesslevel']=$this->getParam('accessLevel');
        $result=$this->objUserAdmin->update('userid',$userId,$data);
        if (!$result) 
        { 
            $this->rstatus="changes_failed";
            $this->rvalue='error_tpl.php';             
        }
        else
        {
            $this->objUserAdmin->makeUserFolder($this->getParam('userId'));
            $this->rstatus="changes_made";            
            // Detect which way to redirect based on the users status on a page
            if ($this->getParam('isAdminUser', 0) == '1') {
                return $this->nextAction('edit', array('userId'=>$userId, 'message'=>'updated'));
            } else {
                return $this->nextAction('selfedit', array('userId'=>$userId, 'message'=>'updated'));
            }
        }
    } // end of function applyedit


    /**
    * Confirm delete.
    * @param numeric $userId User ID to be deleted 
    * @returns boolean
    */
    function checkDelete($userId)
    {    
        if (isset($_GET['confirm'])&&($_GET['confirm']=='yes'))
        {   
            return true;
        }
		else
		{
			return false;
		}
    } 

    /**
    * Delete a user.
    * @param numeric $userId User ID to be deleted
    */
    function deleteApply($userId)
     {
        $this->objUserAdmin->delete('userId',$userId);
    }

    /**
    * Delete yourself.
    * @param string $userId
    * @returns boolean
    */
    function selfDelete($userId)
    {
        if ($this->isAdmin){
            $this->rstatus=$this->objLanguage->languageText('mod_useradmin_adminNoDelete');
            $this->rvalue='error_tpl.php';
            return FALSE;
        }
        $sure=$this->getParam('confirm','no');
        if ($sure=='yes'){
            $this->deleteApply($userId);
            $this->rstatus='mod_useradmin_selfdelete1';
            return TRUE;
        }
		$array = $this->objUserAdmin->getUsers('userId',$userId,TRUE);
        $this->userdata=$array[0];
        return FALSE;
    }
    
    /**
    * Delete a list of users using an array of the userId's.
    * @param array $users The user ID's 
    */
    function batchDelete($users)
    {
        if (is_array($users)){
            $this->objUserAdmin->batchDelete($users); 
        } 
    } 

    /** 
    * Change the user's password.
    * @param string $userId
    * @returns string template
    */
    function changePassword($userId)
    {
        $oldpassword=$this->getParam('oldpassword');
        $newpassword=$this->getParam('newpassword');
        $confirmpassword=$this->getParam('confirmpassword');
        if (($oldpassword!='') && ($newpassword!='') && ($newpassword==$confirmpassword))
        {
           $change=$this->objUserAdmin->changePassword($userId,$oldpassword,$newpassword);
           if ($change) {
               $template=$this->nextAction('mydetails',array('userId'=>$userId));
           } else {
			   $template = 'changepassword_tpl.php';
               $this->setVar('change_error','mod_error_passwd');
           }
        }
		else {
			$template = 'changepassword_tpl.php';
		}
        return $template;
    }
    
    /** 
    * Change the user's password for admin users.
    * @param string $userId
    * @returns string template
    */
	/*
    function adminChangePassword($userId)
    {
        //$this->info['userId']=$userId;
        //$this->info['username']=$this->getParam('username');
        $newpassword=$this->getParam('newpassword');
        $confirmpassword=$this->getParam('confirmpassword');
        $template='adminchangepassword_tpl.php';
        if (($newpassword!='') && ($newpassword==$confirmpassword))
        {
           $change=$this->objUserAdmin->changePassword($userId,'ADMIN',$newpassword);
           if ($change)
           {
               $template=$this->nextAction('edit',array('userId'=>$userId));
           }
           else
           {
               $this->setVar('change_error','mod_error_passwd');
           }
        }
        return $template;
    }
	*/

    /**
    * Reset a user's password to a random setting
    * and email the result. It checks to see if the username and email
    * address match before making any changes.
    * @param string $username
    * @param string $email
    * @returns string Status
    */
    function resetPassword($username,$email)
    {
        $username=$username;
        $email=$email;
        $sql="select userid, username, firstname, surname, pass from tbl_users where username='$username' and emailaddress='$email'";
        $result=$this->objUserAdmin->getArray($sql);
        if (!empty($result)){ 
            $userId=$result[0]['userid']; 
            $password=$result[0]['pass']; 
            $firstname=$result[0]['firstname']; 
            $surname=$result[0]['surname']; 
            if ($password!=sha1('--LDAP--')){ 
                $objPassword=&$this->getObject('passwords','useradmin'); 
                $newpassword=$objPassword->createPassword(); 
                $cryptpassword=sha1($newpassword); 
                $this->objUserAdmin->update('userid',$userId,array('password'=>$cryptpassword)); 
                $this->objUserAdmin->emailPassword($userId,$username,$firstname,$surname,$email,$newpassword);
                return "mod_useradmin_passwordreset"; 
            } else { 
                // LDAP
                return "mod_useradmin_ldapnochange"; 
            } 
        } else { 
            // No such username/email exists 
            return "mod_useradmin_nomatch"; 
        } 
        return TRUE;
    }
  
    /**
    * Email new password after resetting it.
    * @param string $firstname
    * @param string $surname
    * @param string $userId
    * @param string $username
    * @param string $title
    * @param string $email
    * @param string $password
    */
    function emailPassword($userId,$username,$firstname,$surname,$email,$password)
    {
        $subject=$this->objLanguage->languageText('mod_useradmin_greet6'); 
		$greet1 = $this->objLanguage->languageText('mod_useradmin_greet1');
		$greet1 = str_replace('SURNAME',$surname,$greet1);
		$greet1 = str_replace('FIRSTNAME',$firstname,$greet1);
        $content=$greet1."\n" 
        .$this->objLanguage->languageText('mod_useradmin_greet4')."\n"
        .$this->objLanguage->languageText('word_userid').": $userId\n"
        .$this->objLanguage->languageText('phrase_firstname').": $firstname\n"
        .$this->objLanguage->languageText('word_surname').": $surname\n"
        .$this->objLanguage->languageText('word_username').": $username\n"
        .$this->objLanguage->languageText('word_password').": $password\n"
        .$this->objLanguage->languageText('phrase_emailaddress').": $email\n"
        .$this->objLanguage->languageText('word_sincerely')."\n"
        .$this->objLanguage->languageText('mod_useradmin_greet5')."\n";
        @mail($email,$subject,$content);
    }
    
    /**
    * Upload the user image.
    */
    function imageUpload()
    {
        $objImage=&$this->getObject('imageupload');
        $objImage->doUpload($this->getParam('userId'));
        $objImage->doUpload($this->getParam('userId'),35,'_small');
    }

    /**
    * Returns a list of users
    * @param string $how The method of searching - username, surname or email
    * @param string $match The pattern to match for
    * @param bool $adminLinks Whether to display the Add, Edit and Delete links
	* @deprecated
    */
	/*
    function ListUsers($how, $match, $adminLinks)
    {
        $fieldnames=array('userid','username','title','firstname','surname','emailaddress','creationdate','howcreated','isactive');
        $fieldterms=array('word_userid','word_username','word_title','phrase_firstname','word_surname','phrase_emailaddress','phrase_creationdate','phrase_howcreated','phrase_isactive');
		$header = array();
	    foreach($fieldterms as $fieldterm) {
	        $header[]=$this->objLanguage->languageText($fieldterm,'useradmin',$fieldterm);
	    }
	    if ($adminLinks){
	        $header[]=$this->objButtons->linkedButton("add",$this->uri(array('action'=>'Add'),'useradmin'));
	    }
        $objTable=&$this->newObject('htmltable','htmlelements');
        $objTable->width='';
        $objTable->attributes=" align='center' border=0";
        $objTable->cellspacing='2';
        $objTable->cellpadding='2';
        $objTable->addHeader($header,'odd');
	    $users=$this->objUserAdmin->getUsers($how,$match);
        $oddOrEven='odd';
	    foreach ($users as $user)
	    {
	        $oddOrEven=$oddOrEven=='odd' ? "even" : "odd";
			$row = array();
	        foreach($fieldnames as $field)
	        {
                $row[]=$user[$field];
            }
	        if ($adminLinks)
	        {
                $element='';
                $editLink=$this->uri(array('module'=>'useradmin','action'=>'edit','userId'=>$line['userId']));
				$element.$this->objButtons->linkedButton("edit",$editLink);
                $deleteLink=$this->uri(array('module'=>'useradmin','action'=>'delete','userId'=>$line['userId']));
                $element.=$this->objButtons->linkedButton("delete",$deleteLink);
                if (!$this->objUser->lookupAdmin($user['userId'])){
			        $objCheckbox=&$this->getObject('checkbox','htmlelements');
                    $objCheckbox->checkbox('userArray[]'); 
                    $objCheckbox->setValue($user['userId']); 
                    $checkBox=$objCheckbox->show(); 
                } 
				else { 
                    $checkBox='&nbsp;';
                }
                $element.=$checkBox;                                                                                                    
                $row[]=$element;
            }
            $objTable->row_attributes=" onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"";
            $objTable->addRow($row,NULL,"class='".$oddOrEven."' onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"");
	    }
        return $objTable->show();
    }  // end of function ListUsers
	*/

    /**
    * Make a table from a list of users.
    * @param array $users The user information to display
    * @param bool $adminLinks Whether to display the Add, Edit and Delete links
    */
    function makeTableFromUsers($users,$adminLinks)
    {
        $fieldnames=array('userid','username','title','firstname','surname','emailaddress','creationdate','howcreated','isactive');
        $fieldterms=array('word_userid','word_username','word_title','phrase_firstname','word_surname','phrase_emailaddress','phrase_creationdate','phrase_howcreated','phrase_isactive');
		$header = array();
        foreach($fieldterms as $fieldterm)
        {
            $header[]=$this->objLanguage->languageText($fieldterm,'useradmin',$fieldterm);
        }
        if ($adminLinks)
		{
            $header[]=$this->objButtons->linkedButton("add",$this->uri(array('module'=>'useradmin','action'=>'add')));
        }
        $objTable=&$this->newObject('htmltable','htmlelements');
        $objTable->width='';
        $objTable->attributes=" align='center' border=0";
        $objTable->cellspacing='2';
        $objTable->cellpadding='2';
        $objTable->addHeader($header,'odd');
		$oddOrEven = 'odd';
        foreach ($users as $user)
        {
            $oddOrEven=$oddOrEven=='odd' ? "even" : "odd";
			$row = array();
            foreach($fieldnames as $field)
            {
                $row[]=$user[$field];
            }
            if ($adminLinks)
            {
                $editLink=$this->uri(array('module'=>'useradmin','action'=>'edit','userId'=>$user['userid']));
                $deleteLink=$this->uri(array('module'=>'useradmin','action'=>'delete','userId'=>$user['userid']));
				$element = '';
                $element.=$this->objButtons->linkedButton("edit",$editLink);
                $element.=$this->objButtons->linkedButton("delete",$deleteLink);
                // Code for the checkbox - only display if user being listed is not a site-Admin
                // This checkbox allows group deletions of users
                if (!$this->objUser->lookupAdmin($user['userid'])){
			        $objCheckbox=&$this->getObject('checkbox','htmlelements');
                    $objCheckbox->checkbox('userArray[]'); 
                    $objCheckbox->setValue($user['userid']); 
                    $checkBox=$objCheckbox->show(); 
                } 
				else 
				{
                    $checkBox='&nbsp;';
                }
                $element.=$checkBox;                                                                                                    
                $row[]=$element;
            }
            $objTable->row_attributes=" onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"";
            $objTable->addRow($row,NULL,"class='".$oddOrEven."' onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"");
        }
        return $objTable->show();
    }

    /**
	* Wrapper for the textinput class in htmlelements.
    * @param $name string
    * @param $type string
    * @param $value  string
    * @returns string
	* @deprecated
    */
	/*
    function textinput($name,$type,$value=NULL)
    {
        if (is_null($value)){
            $value=$this->getParam($name);
        }
        $field=new textinput($name,$value);
        $field->fldType=$type;
        return $field->show();
    }
	*/
    
    /**
    * Generate an alphabet browser.
    */
    function alphabetBrowser()
    {
        $objAlphabet=& $this->getObject('alphabet','navigation');
        $linkarray=array('action'=>'ListUsers','how'=>'surname','searchField'=>'LETTER');
        $url=$this->uri($linkarray,'useradmin');        
        return '<p>'
			.$this->objLanguage->languageText('mod_useradmin_browsebysurname','useradmin')
			.' '
			.$objAlphabet->putAlpha(
				$url, 
				TRUE, 
				$this->objLanguage->languageText('mod_useradmin_listallusers','useradmin')
			)
			.'</p>';

	}

    /**
    * Generate a menu.
    */
    function userAdminMenu()
    {
        // Classes being Used
        $this->loadclass('form', 'htmlelements');
        $this->loadclass('textinput', 'htmlelements');
        $this->loadclass('radio', 'htmlelements');
        $this->loadclass('button', 'htmlelements');
        $this->loadclass('link', 'htmlelements');
        $objAlphabet=& $this->getObject('alphabet','navigation');      
        $searchform = new form ('searchuser', 'index.php');
        $searchform->method= 'GET';
        $searchform->addToForm('<div align="center">'); 
        $textinput = new textinput('module', 'useradmin');
        $textinput->fldType = 'hidden';
        $searchform->addToForm($textinput->show());
        $textinput = new textinput('action', 'listusers');
        $textinput->fldType = 'hidden';
        $searchform->addToForm($textinput->show());
        $textinput = new textinput ('searchField');
        $textinput->size = '40';
        $searchform->addToForm($textinput->show().'<br />');
        $radio = new radio ('how');
        $radio->addOption('username', $this->objLanguage->languageText('word_username'));
        $radio->addOption('surname', $this->objLanguage->languageText('word_surname'));
        $radio->addOption('emailaddress', $this->objLanguage->languageText('phrase_emailaddress'));
        $radio->setSelected('username');
        $searchform->addToForm($radio->show().'<br />');        
        $submitbutton = new button ('search', $this->objLanguage->languageText('heading_customSearch'));
        $submitbutton->setToSubmit();
        $searchform->addToForm($submitbutton->show());
        $searchform->addToForm('</div>');        
        $searchFieldset =& $this->getObject('fieldset', 'htmlelements');
        $searchFieldset->setLegend($this->objLanguage->languageText('mod_useradmin_searchforuser','useradmin'));
        $searchFieldset->addContent($searchform->show());
        $listFieldset =& $this->newObject('fieldset', 'htmlelements');
        $listFieldset->setLegend($this->objLanguage->languageText('mod_useradmin_browsebysurname','useradmin'));
        $linkarray=array('action'=>'ListUsers','how'=>'surname','searchField'=>'LETTER');
        $url=$this->uri($linkarray,'useradmin');        
        $listFieldset->addContent('<p>'.$objAlphabet->putAlpha($url).'</p>');        
        $url=$this->uri(array('action'=>'listUnused'));        
        $addNewLink = new link($this->uri(array('action'=>'Add')));
        $addNewLink->link = 'Add New User';        
        $cleanupLink = new link($this->uri(array('action'=>'listUnused')));
        $cleanupLink->link = $this->objLanguage->languageText('mod_useradmin_cleanup','useradmin');        
        $bottom = '<p>'.$addNewLink->show().' / '.$cleanupLink->show().'</p>';
        $table = $this->getObject('htmltable', 'htmlelements');
        $table->startRow();
        $table->addCell($listFieldset->show().$bottom);
        $table->addCell($searchFieldset->show(), '30%');
        $table->endRow();
        return $table->show();
    }
}

?>
