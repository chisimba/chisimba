<?
/* -------------------- useradmin class extends controller ----------------*/
                                                                                                                                             
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
                                                                                                                                             
/**
* Registration and administration of users.
* @copyright (c) 2004 UWC/Avoir
* @version 1.0
* @package useradmin
* @author James Scoble
*
* $Id: controller.php
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
    var $info;  // for passing information around the class
 
    function init()
    {
        $this->objConfig =& $this->getObject('altconfig','config');
        $this->objLanguage =& $this->getObject('language','language');
        $this->objButtons=&$this->getObject('navbuttons','navigation');
        $this->objUserAdmin=&$this->getObject('sqlUsers','security');
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
        if (!$this->isAdmin && $this->requiresAdmin($action))
        {
			die('Access denied')
        }
        if (!$this->requiresAdmin($action) ) {
            //$this->setLayoutTemplate("user_layout_tpl.php");
        }
        switch ($action)
        {
            case 'changepassword':
                $this->rvalue=$this->changePassword();
                break;
            case 'adminchangepassword':
                $this->rvalue=$this->adminChangePassword($this->getParam('userId'));
                break;
            case 'needpassword':
                $this->rvalue='forgotpassword_tpl.php';
                break;
            case 'resetpassword':
                $this->rstatus=$this->resetPassword($this->getParam('username'),$this->getParam('email'));
                $this->rvalue='ok_tpl.php';
                break; 
            case 'imageupload':
                $this->doUpload();
                return $this->nextAction('selfedit');
            case 'imagereset':
                $objImage=$this->newObject('imageupload','useradmin');
                $objImage->resetImage($userId);
                $objImage->resetImage($userId."_small");
                if ($this->getParam('isAdminUser') == '1') {
                    $nextaction = 'edit';
                } else {
                    $nextaction = 'selfedit';
                }
                return $this->nextAction($nextaction, array('userId'=>$this->getParam('userId')));
                break;
            case 'edit':
                $this->setVar('isAdminUser',TRUE);
                $this->editUserDetails($userId);
                break;
            case 'selfedit':
                $this->setVar('isAdminUser',FALSE);
                $this->editUserDetails($userId);
                break;
            case 'editapply':
                $status=$this->checkUserDetails();
                if ($status===true)
                {
                    $this->applyedit();
                }
                else
                {
                    $this->rvalue='error_tpl.php';
                    $this->rstatus=$status;
                }
                break;
            case 'add':
                $this->addUser();
                break;    
            case 'addapply':
                $status=$this->checkAddUser(
					$this->getParam('username'),
					$this->getParam('password'),
					$this->getParam('email'),
					$this->getParam('passwd'),
					$this->getParam('userId')
				);
                if ($status===true)
				{
                    $this->applyadd();
                    $userdata=$this->ListUsers('creationDate',date('Y-m-d'),'TRUE');
                    $this->setVar('userdata',$userdata);
                    $title = $this->objLanguage->languageText('mod_useradmin_newuseradded');
                    $this->setVar('title', $title);
                    $this->rvalue='list_users_tpl.php';
                } 
				else 
				{
                    $this->rstatus=$status;
                    $this->rvalue='error_tpl.php';
                }
                break;
            case 'listusers':
                $how=$this->getParam('how');
                $match=stripslashes($this->getParam('searchField'));                
                if ($this->getParam('search', NULL) != NULL) {
                    $title = $this->objLanguage->languageText('mod_useradmin_searchresultsfor').' ('.$match.')';
                } else {
                    if ($this->getParam('message', NULL) == 'added') {
                        $title = $this->objLanguage->languageText('mod_useradmin_newuseradded');
                    } else if ($match == 'listall') {
                        $title = $this->objLanguage->languageText('mod_useradmin_showingallusers');
                    } else {
                        $title = $this->objLanguage->languageText('mod_useradmin_listingusersbysurname').' ('.$match.')';
                    }
                }
                $this->setVarByRef('title', $title);
                $userData=$this->objUserAdmin->getUsers($how,$match,FALSE);
                $userdata=$this->makeListUsersTable($userData,TRUE);
                $this->setVar('userdata',$userdata);
                $this->rvalue='list_users_tpl.php';
                break;
            case 'listunused':
                $userData=$this->objUserAdmin->getUsers('notused','','TRUE');
                $userdata=$this->makeListUsersTable($userData,'TRUE');
                $this->setVar('userdata',$userdata);
                $title = $this->objLanguage->languageText('mod_useradmin_unusedaccounts');
                $this->setVar('title', $title);
                $this->rvalue='list_users_tpl.php';
                break;
            case 'delete':
                $status=$this->checkDelete($this->getParam('userId'));
                if ($status===true){
                    $this->applydelete($this->getParam('userId'));
                    $this->rvalue='list_tpl.php';
                } 
				else 
				{
					$results = $this->objUserAdmin->getUsers('userId',$userId,TRUE);
		            $this->userdata=results[0];
		            $this->rvalue='confirmdelete_tpl.php';
                }
                break;
            case 'batchdelete':
                $this->batchdelete($this->getArrayParam('userArray'));
                return $this->nextAction(
					'listUsers',
					array(
						'how'=>$this->getParam('how'),
						'searchField'=>$this->getParam('searchField')
					)
				);
                break;
            case 'register':
                $this->setLayoutTemplate(NULL);
                $this->rvalue='register_tpl.php';
                break;
            case 'registerapply':
                $this->setLayoutTemplate(NULL);
                $this->registerApply();
                break;
            case 'selfdelete':
                $this->rvalue='selfdelete_tpl.php';
                if ($this->selfDelete($this->objUser->userId())){
                    $this->objUser->logout();
                    $this->rvalue='okay_tpl.php';
                }       
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
            case 'selfedit':
            case 'applyselfedit':
            case 'selfdelete':
            case 'changepassword':
            case 'apply changes':
            case 'mydetails':
            case 'register':
            case 'submitregister':
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
    * Returns a list of users
    * @param string $how The method of searching - username, surname or email
    * @param string $match The pattern to match for
    * @param bool $adminLinks Whether to display the Add, Edit and Delete links
    */
    function ListUsers($how, $match, $adminLinks)
    {
        $fieldnames=array('userId','username','title','firstName','surname','emailAddress','creationDate','howCreated','isActive');
        $fieldterms=array('word_userid','word_username','word_title','phrase_firstname','word_surname','phrase_emailaddress','phrase_creationdate','phrase_howcreated','phrase_isactive');
		$header = array();
	    foreach($fieldterms as $fieldterm) {
	        $header[]=$this->objLanguage->languageText($fieldterm,'useradmin',$field);
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
			$row = array();
	        $oddOrEven=$oddOrEven=='odd' ? "even" : "odd";
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
                    $checkBox=$objCheck->show(); 
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

    /**
    * Make a table from a list of users.
    * @param array $userData The user information to display
    * @param bool $adminLinks Whether to display the Add, Edit and Delete links
    */
    function makeListUsersTable($userData,$adminLinks)
    {
        $fieldnames=array('userId','username','title','firstName','surname','emailAddress','creationDate','howCreated','isActive');
        $fieldterms=array('word_userid','word_username','word_title','phrase_firstname','word_surname','phrase_emailaddress','phrase_creationdate','phrase_howcreated','phrase_isactive');
        foreach($fieldterms as $field)
        {
            $field2[]=$this->objLanguage->languageText(strtolower($field),$field);
        }
        if ($adminLinks){
            $addlink=$this->uri(array('module'=>'useradmin','action'=>'Add'));
            $field2[]=$this->objButtons->linkedButton("add",$addlink);
        }
        $objTable=&$this->newObject('htmltable','htmlelements');
        $objTable->width='';
        $objTable->attributes=" align='center' border=0";
        $objTable->cellspacing='2';
        $objTable->cellpadding='2';
        $objTable->addHeader($field2,'odd');
        unset($field2);
        
        $rowcount='';
        foreach ($userData as $line)
        {
            $rowcount=($rowcount==0) ? 1 : 0; // with aknowledgements to Derek Keats for this idea
            $oddOrEven=($rowcount==0) ? "odd" : "even";
            foreach($fieldnames as $field)
            {
                $dline[]=$line[$field];
            }
            if ($adminLinks)
            {
                $editLink=$this->uri(array('module'=>'useradmin','action'=>'edit','userId'=>$line['userId']));
                $deleteLink=$this->uri(array('module'=>'useradmin','action'=>'delete','userId'=>$line['userId']));
                $d1=$this->objButtons->linkedButton("edit",$editLink);
                $d1.="&nbsp;".$this->objButtons->linkedButton("delete",$deleteLink);
                // Code for the checkbox - only display if user being listed is not a site-Admin
                // This checkbox allows group deletions of users
                if (!$this->objUser->lookupAdmin($line['userId'])){
			        $objCheck=&$this->newObject('checkbox','htmlelements');
                    $objCheck->checkbox('userArray[]'); 
                    $objCheck->setValue($line['userId']); 
                    $checkBox=$objCheck->show(); 
                } else { 
                    $checkBox='&nbsp;';
                    }
                $d1.=$checkBox;                                                                                                    
                $dline[]=$d1;
            }
            $objTable->row_attributes=" onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"";
            $objTable->addRow($dline,NULL,"class='".$oddOrEven."' onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"");
             unset($dline);
        }
            return $objTable->show();
    }

    
    /**
    * Edit User Details
    * @param string $userid User ID
    */
    function editUserDetails($userId)
    {
        $results=$this->objUserAdmin->getUsers('userId',$userId,TRUE);
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
            $this->rvalue='useredit_tpl.php';

        } 
    }


    /**
    * This is a method to check submitted info for any illegal data
    * @author James Scoble    
    * @returns string $message a code for the type of error, or "Looks Okay" if it does.
    */
    function checkUserDetails()
    {
        if ($this->getParam('username')==""){
            return("need_username");  
        }
        if ($this->getParam('email')==""){
            return "need_email"; 
        }      
		// non-admin trying to edit someone else?
        if ((!$this->isAdmin)&&($this->getParam('userId')!=$this->objUser->userId())) 
        {
            return('Not Admin!');
        }
        if ($this->getparam('username')!=$this->getParam('old_username')){
            $result=$this->objUserAdmin->checkDBase('0000',$this->getParam('username'));
            return $result;
        }
        return true;
    }


    /**
    * This is a method to update database with user info
    * @author James Scoble
    */

    function applyedit()
    {
        $sdata['username']=$this->getParam('username');
        $sdata['title']=$this->getParam('title');
        $sdata['firstname']=$this->getParam('firstname');
        $sdata['surname']=$this->getParam('surname');
        $sdata['emailAddress']=$this->getParam('email');
        $sdata['sex']=$this->getParam('sex');
        $sdata['country']=$this->getParam('country');
        //$sdata['accesslevel']=$this->getParam('accessLevel');

        $userId=$this->getParam('userId');  
        $r1=$this->objUserAdmin->update('userId',$userId,$sdata);
        if (!$r1) 
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
                return $this->nextAction('selfedit', array('message'=>'updated'));
            }
        }
    } // end of function applyedit


    /**
    * This is a method to display yes/no page for deleting
    * @author James Scoble
    *
    * @param numeric $userId - the primary key of the use to be deleted 
    * @returns string $message
    *
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
    * This is a method to delete user data
    * @author James Scoble
    *
    * @param numeric $userId - the primary key of the use to be deleted
    * 
    *
    */
    function applydelete($userId)
     {
        $this->objUserAdmin->delete('userId',$userId);
        //$this->tblusergroups->removeUser($userId); // no tbl_usergroups
    }

    /**
    * Wrapper function for deleting a list of users
    * from a menu, using an array of the userId's
    * @author James Scoble
    * @param array $users
    */
    function batchDelete($users)
    {
        if (is_array($users)){
            $this->objUserAdmin->batchDelete($users); 
        } 
    } 

    /**
    * This is a method to display page for adding new user
    */
    function addUser()
    {
		$this->rvalue='useradd_tpl.php';
    }

    /**
    * This is a method to insert info into database
    * @author James Scoble
    *
    */
    function applyAdd()
    {
        $cdate=date("Y-m-d");
        $sdata['userId']=$this->getParam('userId');
        $sdata['username']=$this->getParam('username');
        $sdata['title']=$this->getParam('title');
        $sdata['firstname']=$this->getParam('firstname');
        $sdata['surname']=$this->getParam('surname');
        $sdata['PASSWORD']=sha1($this->getParam('password'));
        $sdata['CreationDate']=$cdate;
        $sdata['howCreated']='useradmin';
        $sdata['emailAddress']=$this->getParam('email');
        $sdata['sex']=$this->getParam('sex');
        $sdata['country']=$this->getParam('country');
        $r1=$this->objUserAdmin->insert($sdata); // calling dbtable-derived Insert function

        if (!$r1) 
        { 
            $this->rstatus="changes_failed";
            //$this->rvalue='error_tpl.php'; 
            $this->rvalue='useradd_tpl.php'; 
        }
        else
        {
            $this->rstatus="user_added";
            $this->rvalue='okay_tpl.php';
            // Check if its an alumni site, add info if it is.
            //$this->checkAlumni();
        }
    } // end of function applyadd


    /**
    * This is a method to check info before adding into database
    * @author James Scoble
    * @param string $username
    * @param string $password
    * @param string $email
    * @param string $passwd - the second copy of the password
    * @param string $userId
    * @returns string - either 'Looks Okay' or an error code
    */
    function checkAddUser($username,$password,$email,$passwd,$userId)
    {      
        if ($username=="") { return("need_username"); }
        if ($password=="") { return("need_password"); }
        if ($email=="") {return "need_email"; }
        if ($password!=$passwd) {return "password_not_match";}
        // if all these test pass, the code continues...
        $result=$this->objUserAdmin->checkUserExists($userId,$username);
        if ($result!==true) { return $result; }
        // passess all other tests, then...
        return true;
    }
        
    /**
    * This is a method to add self-registering users
    * This function assumes that data has been sent from a webpage - the self-register page. 
    * It gets the info from the getParam() function.
    * The class variables $rvalue and $rstatus are used to record the results.
    * @author James Scoble
    */
    function registerApply()
    {
        $names=array('userId','firstname','surname','username','email','title','sex','country');
        foreach ($names as $line)
        {
            $$line=trim($this->getParam($line)); //copy to local vars
        }
        $result=$this->objUserAdmin->checkDbase($userId,$username); // check to see if username or userId is taken
        if ($result=='Looks Okay')
        {
            //$password=rand(10000,99999);
            $objPassword=&$this->getObject('passwords','useradmin');
            $password=$objPassword->createPassword();
            $cryptpassword=sha1($password);
            $cdate=date("Y-m-d");
            $newdata=array(
                'userId'=>$userId,
                'username'=>$username,
                'title'=>$title,
                'firstName'=>$firstname,
                'surname'=>$surname, 
                'pass'=>$cryptpassword,
                'creationDate'=>$cdate, 
                'howCreated'=>'selfregister', 
                'emailAddress'=>$email, 
                'sex'=>$sex, 
                'country'=>$country
                );
            $this->objUserAdmin->insert($newdata);
            //$this->tblusergroups->newEntry($userId,'guests'); no longer used
            $this->setVar('newdata',$newdata);
            
            // Here we check to see if the site is an "alumni" one, and process more data if it is.
            //$this->checkAlumni();
            
            $this->setVar('newpassword',$password);
            $this->rvalue='registersuccess_tpl.php';
            //--$this->sendRegisterInfo($firstname,$surname,$userId,$username,$title,$email,$password,'GUEST');
            //$this->objUserAdmin->emailPassword($firstname,$surname,$userId,$username,$email,$password);
        }
        else
        {
            $this->rstatus=$result;
            $this->rvalue='register_tpl.php';
        }
    }

    /**
    * Here we check to see if the site is an "alumni" one, and process more data if it is.
    */
    function checkAlumni()
    {
        $systemType = $this->objConfig->getValue("SYSTEM_TYPE", "contextabstract");
        
        if ($systemType=='alumni'){
        //    $objAlumni=&$this->getObject('alumniusers','alumni');
        //    $objAlumni->addAlumniInfo();
            return TRUE;
        } else {
            return FALSE;
        }
    }


    /**
    * This is a method to email a new user the info about the account that's been created
    * takes as params all the data to send.
    * @version 1.1
    * @param string $firstname - data to send
    * @param string $surname - data to send
    * @param string $userId - data to send
    * @param string $username - data to send
    * @param string $title - data to send
    * @param string $email - data to send
    * @param string $password - data to send
    * @param string $accesslevel - depereciated data - but still included in case its needed again in a future version
    * calls the language object a lot, and PHP's built-in email functionality
    */ 
    function sendRegisterInfo($firstname,$surname,$userId,$username,$title,$email,$password,$accesslevel='')
    {
        $info=$this->objUserAdmin->siteURL();
        $emailtext=str_replace('SURNAME',$surname,str_replace('FIRSTNAME',$firstname,$this->objLanguage->languageText('mod_useradmin_greet1')))."\n" 
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
        $subject=$this->objLanguage->languageText('mod_useradmin_greet6'); 
        $emailtext=str_replace('KEWL NextGen',$info['sitename'],$emailtext);
        $subject=str_replace('KEWL NextGen',$info['sitename'],$subject);
        $header="From: ".$this->objLanguage->languageText('mod_useradmin_greet5').'<noreply@'.$info['server'].">\r\n";
        @mail($email,$subject,$emailtext,$header);
    }

    /** 
    * This is a method to change the users' password - actually just a wrapper with some built-in checks.
    * @author James Scoble
    * @param string $userId
    * @returns string template file name
    */
    function changePassword($userId='')
    {
        if ($userId==''){
            $userId=$this->objUser->userId();
        }
        $oldpassword=$this->getParam('oldpassword');
        $newpassword=$this->getParam('newpassword');
        $confirmpassword=$this->getParam('confirmpassword');
        $returntemplate='changepassword_tpl.php';
        if (($oldpassword!='') && ($newpassword!='') && ($newpassword==$confirmpassword))
        {
           $change=$this->objUserAdmin->changePassword($userId,$oldpassword,$newpassword);
           if ($change) {
               $returntemplate=$this->nextAction('mydetails',array('userId'=>$userId));
           } else {
               $this->setVar('change_error','mod_error_passwd');
           }
        }
        return $returntemplate;
    }
    
    /** 
    * This is a method to change the users' password - only for admin users.
    * @author James Scoble
    * @param string $userId
    * @returns string template file name
    */
    function adminChangePassword($userId='')
    {
        if ($userId=='')
        {
            $userId=$this->objUser->userId();
        }
        $this->info['userId']=$userId;
        $this->info['username']=$this->getParam('username');
        $newpassword=$this->getParam('newpassword');
        $confirmpassword=$this->getParam('confirmpassword');
        $returntemplate='adminchangepassword_tpl.php';
        if (($newpassword!='') && ($newpassword==$confirmpassword))
        {
           $change=$this->objUserAdmin->changePassword($userId,'ADMIN',$newpassword);
           if ($change)
           {
               $returntemplate=$this->nextAction('edit',array('userId'=>$userId));
           }
           else
           {
               $this->setVar('change_error','mod_error_passwd');
           }
        }
        return $returntemplate;
    }

    
    /**
    * This is a method to change a user's password to a random setting
    * and email the result. It checks to see if the username and email
    * address match before making any changes.
    * @param string $username
    * @param string $email
    * @returns string $status messagecode 
    */
    function resetPassword($username,$email)
    {
        $username=trim($username);
        $email=trim($email);
        $sql="select userId, username, firstname, surname, password from tbl_users where username='$username' and emailAddress='$email'";
        $result=$this->objUserAdmin->getArray($sql);
        if (isset($result[0])){ 
            // Get the user's info 
            $userId=$result[0]['userId']; 
            $password=$result[0]['password']; 
            $firstname=$result[0]['firstname']; 
            $surname=$result[0]['surname']; 
            if ($password!=(sha1('--LDAP--'))){ 
                $objPassword=&$this->getObject('passwords','useradmin'); 
                $newpassword=$objPassword->createPassword(); 
                $cryptpassword=sha1($newpassword); 
                $this->objUserAdmin->update('userId',$userId,array('password'=>$cryptpassword)); 
                $this->objUserAdmin->emailPassword($userId,$username,$firstname,$surname,$email,$newpassword);
                return "mod_useradmin_passwordreset"; 
            } else { 
                // LDAP USER 
                return "mod_useradmin_ldapnochange"; 
            } 
        } else { 
            // error that no such username/email exists 
            return "mod_useradmin_nomatch"; 
        } 
        return TRUE;
    }
  
    /**
    * Method to compose and send email for resetting of password 
    * @param string $firstname - data to send
    * @param string $surname - data to send
    * @param string $userId - data to send
    * @param string $username - data to send
    * @param string $title - data to send
    * @param string $email - data to send
    * @param string $password - data to send
    */
    function emailPassword($userId,$username,$firstname,$surname,$email,$password)
    {
        $emailtext=str_replace('SURNAME',$surname,str_replace('FIRSTNAME',$firstname,$this->objLanguage->languageText('mod_useradmin_greet1')))."\n" 
        .$this->objLanguage->languageText('mod_useradmin_greet4')."\n"
        .$this->objLanguage->languageText('word_userid').": $userId\n"
        .$this->objLanguage->languageText('phrase_firstname').": $firstname\n"
        .$this->objLanguage->languageText('word_surname').": $surname\n"
        .$this->objLanguage->languageText('word_username').": $username\n"
        .$this->objLanguage->languageText('word_password').": $password\n"
        .$this->objLanguage->languageText('phrase_emailaddress').": $email\n"
        .$this->objLanguage->languageText('word_sincerely')."\n"
        .$this->objLanguage->languageText('mod_useradmin_greet5')."\n";
        $subject=$this->objLanguage->languageText('mod_useradmin_greet6'); 
        @mail($email,$subject,$emailtext);
    }

    
    /**
    * This is a method for upload of files
    * this is used for the user picture. dispatch() calls this, which calles the imageupload class
    */
    function doUpload()
    {
        $objImage=&$this->getObject('imageupload');
        $objImage->doUpload();
        $objImage->doUpload(35,'_small');
    }


    /**
    * This is a method to delete the user's own login
    * @param string $userId
    * @returns Boolean TRUE or FALSE
    */
    function selfDelete($userId)
    {
        if ($this->isAdmin){
            $this->rstatus=$this->objLanguage->languageText('mod_useradmin_adminNoDelete');
            $this->rvalue='error_tpl.php';
            return FALSE;
        }
        $sure=$this->getParam('confirm');
        if ($sure=='yes'){
            $this->applydelete($userId);
            $this->rstatus='mod_useradmin_selfdelete1';
            return TRUE;
        }
        $this->userdata=array_shift($this->objUserAdmin->getUsers('userId',$userId,TRUE));
        return FALSE;
    }
    
    /* method to act as a 'wrapper' for textelement class
    * @author James Scoble
    * @param $name string
    * @param $type string
    * @param $value  string
    * @returns string
    */
    function textinput($name,$type,$value=NULL)
    {
        // In case this is a second-attempt after a
        // username being taken already.
        if (!$value){
            $value=$this->getParam($name);
        }
        $field=new textinput($name,$value);
        $field->fldType=$type;
        return $field->show();
    }
    
    /**
    * Method to generate a menu shown on the bottom.
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
        
        // Start of Table
        $table = $this->getObject('htmltable', 'htmlelements');
        $table->startRow();
        
        /* Column One*/
        $searchform = new form ('searchuser', 'index.php');
        $searchform->method= 'GET';
        
        // Add a P tag to align items to center
        $searchform->addToForm('<div align="center">'); 
        
        $hiddentextinput = new textinput('module', 'useradmin');
        $hiddentextinput->fldType = 'hidden';
        $searchform->addToForm($hiddentextinput->show());
        $hiddentextinput = new textinput('action', 'listusers');
        $hiddentextinput->fldType = 'hidden';
        $searchform->addToForm($hiddentextinput->show());
        
        $textinput = new textinput ('searchField');
        $textinput->size = '40';
        $searchform->addToForm($textinput->show().'<br />');
        
        $radiotype = new radio ('how');
        $radiotype->addOption('username', $this->objLanguage->languageText('word_username'));
        $radiotype->addOption('surname', $this->objLanguage->languageText('word_surname'));
        $radiotype->addOption('emailAddress', $this->objLanguage->languageText('phrase_emailaddress'));
        $radiotype->setSelected('username');
        $searchform->addToForm($radiotype->show().'<br />');
        
        $submitbutton = new button ('search', $this->objLanguage->languageText('heading_customSearch'));
        $submitbutton->setToSubmit();
        $searchform->addToForm($submitbutton->show());
        
        // Close to Div
        $searchform->addToForm('</div>');
        
        $searchFieldset =& $this->getObject('fieldset', 'htmlelements');
        $searchFieldset->setLegend($this->objLanguage->languageText('mod_useradmin_searchforuser'));
        $searchFieldset->addContent($searchform->show());
        
        // ENd Column One
        
        
        // Column Two
        $listFieldset =& $this->newObject('fieldset', 'htmlelements');
        $listFieldset->setLegend($this->objLanguage->languageText('mod_useradmin_browsebysurname'));
        
        $linkarray=array('action'=>'ListUsers','how'=>'surname','searchField'=>'LETTER');
        $url=$this->uri($linkarray,'useradmin');
        
        $listFieldset->addContent('<p>'.$objAlphabet->putAlpha($url).'</p>');
        
        $url=$this->uri(array('action'=>'listUnused'));
        
        $addNewLink = new link($this->uri(array('action'=>'Add')));
        $addNewLink->link = 'Add New User';
        
        $cleanupLink = new link($this->uri(array('action'=>'listUnused')));
        $cleanupLink->link = $this->objLanguage->languageText('mod_useradmin_cleanup');
        
        $bottomPara = '<p>'.$addNewLink->show().' / '.$cleanupLink->show().'</p>';
        // End Column Two
        
        // Add Columns to table
        $table->addCell($listFieldset->show().$bottomPara);
        $table->addCell($searchFieldset->show(), '30%');
        
        return $table->show();
    }
    
    /**
    * Method to generate an alpha list menu - Shown on top
    */
    function alphaBrowseList()
    {
        $objAlphabet=& $this->getObject('alphabet','navigation');
        $linkarray=array('action'=>'ListUsers','how'=>'surname','searchField'=>'LETTER');
        $url=$this->uri($linkarray,'useradmin');
        
        return '<p>'.$this->objLanguage->languageText('mod_useradmin_browsebysurname').' '.$objAlphabet->putAlpha($url, TRUE, $this->objLanguage->languageText('mod_useradmin_listallusers')).'</p>';

	}
} // end of class useradmin

?>
