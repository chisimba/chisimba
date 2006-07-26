<?
/* -------------------- useradmin class extends controller ----------------*/
                                                                                                                                             
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
                                                                                                                                             
/**
* Module class to handle registration and admin of users
* @copyright (c) 2004 KEWL.NextGen
* @version 1.0
* @package useradmin
* @author James Scoble
*
* $Id: controller.php
*/

class useradmin extends controller
{
    var $objConfig;
    var $objButtons;
    var $dropdown;
    var $objUserAdmin; // the handle for an instance of the class sqlUsers, in the security module
    var $objLanguage;
    var $tblusergroups; // not in use
    var $objUser;
    var $isAdmin;

    var $rstatus; // shows whether a function-call did what was wanted or not
    var $rvalue;    // the return-value for the template to be used.
    var $info;  // for passing information around the class
 
    function init()
    {
        $this->objConfig =& $this->getObject('config','config');
        $this->objLanguage =& $this->getObject('language','language');
        $this->objButtons=&$this->getObject('navbuttons','navigation');
        $this->objUserAdmin=&$this->getObject('sqlUsers','security');
        // $this->tblusergroups=&$this->getObject('usergroups','security'); not used in this version
        $this->objUser =& $this->getObject('user', 'security');

        $this->isAdmin=$this->objUser->isAdmin();
        
        if ($this->objUser->isLoggedIn()){
            //Get the activity logger class
            $this->objLog=$this->newObject('logactivity', 'logger'); 
            //Log this module call 
            $this->objLog->log();
        }
        
        $this->setVarByRef('menu', $this->userAdminMenu());
        $this->setVarByRef('alphaBrowseList', $this->alphaBrowseList());
    }


    function dispatch($cmd) 
    {
		$this->setVar('pageSuppressXML',true);

        // Convert to lowercase here to prevent unnecessary 'case' in switch
        $cmd = strtolower($cmd);
        
        if (!isset($userId))  
        { 
            $userId=$this->getParam('userId');
        }
        
        // Regard User as an 'admin' if they have access rights to the module
        if ($this->isValid('access',FALSE)) {
            $this->isAdmin = TRUE;
        }
        
        // Check if the action requires admin privileges
        // block non-Admins from other functions
        if ((!$this->isAdmin)&&($this->requiresAdmin($cmd)))
        {
            // User only has access to their own details
            $cmd='selfedit';  
        }

        //&& !$this->isAdmin
        if (!$this->requiresAdmin($cmd) ) {
            $this->setLayoutTemplate("user_layout_tpl.php");
        }
        
        
        
        switch ($cmd)
        {
            case 'changepassword':
                $this->rvalue=$this->changePassword();
                break;
            case 'adminchangepassword':
                $this->rvalue=$this->adminChangePassword($this->getParam('userId'));
                break;
            case 'needpassword':
                $this->setLayoutTemplate(NULL);
                $this->rvalue='forgotpassword_tpl.php';
                break;
            case 'resetpassword':
                $this->setLayoutTemplate(NULL);
                $this->rstatus=$this->resetPassword($this->getParam('username'),$this->getParam('email'));
                $this->rvalue='okay_tpl.php';
                break; 
            case 'imageupload':
                $upload=$this->getParam('upload');
                if ($upload==1){
                    $this->doUpload();
                }
                // Return to selfedit action - only users can change their own image
                return $this->nextAction('selfedit');
            case 'imagereset':
                $objImage=$this->newObject('imageupload','useradmin');
                $objImage->resetImage($userId);
                $objImage->resetImage($userId."_small");
                
                // Workaround because permissions is hardcoded
                if ($this->getParam('admin_user') == 1) {
                    $nextaction = 'edit';
                } else {
                    $nextaction = 'selfedit';
                }
                return $this->nextAction($nextaction, array('userId'=>$userId));
                break;
            case 'edit':
                $this->setVar('admin_user',TRUE);
                $this->show4edit($userId);
                break;
            case 'selfedit':
            case 'mydetails':
                $this->setVar('admin_user',FALSE);
                $thisUser=$this->objUser->userId();
                $this->show4edit($thisUser);
                break;
    
            case 'applyselfedit':
            case 'apply changes':
                $check=$this->check4edit();
                if ($check=='Looks Okay')
                {
                    $this->applyedit();
                }
                else
                {
                    $this->rvalue='error_tpl.php';
                    $this->rstatus=$check;
                }
                break;
            case 'add':
            //case 'newuser':
            //case 'New User':
                $this->show4add();
                break;
    
            case 'adduser':
                $check=$this->check4add($this->getParam('username'),$this->getParam('password'),$this->getParam('email'),$this->getParam('passwd'),$this->getParam('userId'));
                if ($check=='Looks Okay'){
                    $this->applyadd();
                    $userdata=$this->ListUsers('creationDate',date('Y-m-d'),'TRUE');
                    $this->setVar('userdata',$userdata);
                    $this->rvalue='list_users_tpl.php';
                    $title = $this->objLanguage->languageText('mod_useradmin_newuseradded');
                    $this->setVarByRef('title', $title);
                } else {
                    $this->rstatus=$check;
                    $this->rvalue='error_tpl.php';
                    $this->rvalue='useradd_tpl.php';
                }
                break;
    
            case 'listusers':
            //case 'List Users':
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
                
                //$userdata=$this->ListUsers($how,$match,'TRUE');
                $userData=$this->objUserAdmin->getUsers($how,$match,FALSE);
                $userdata=$this->makeListUsersTable($userData,TRUE);
                $this->setVar('userdata',$userdata);
                $this->rvalue='list_users_tpl.php';
                break;
            case 'listunused':
                $userData=$this->objUserAdmin->getUsers('notused','','TRUE');
                $userTable=$this->makeListUsersTable($userData,'TRUE');
                $this->setVar('userdata',$userTable);
                
                $title = $this->objLanguage->languageText('mod_useradmin_unusedaccounts');
                $this->setVarByRef('title', $title);
                    
                $this->rvalue='list_users_tpl.php';
                break;
            case 'delete':
                $check=$this->check4delete($userId);
                if ($check=='Looks Okay'){
                    $this->applydelete($userId);
                    $this->rvalue='list_tpl.php';
                } else if ($check!='Making Sure') {
                    $this->rstatus=$check;
                    $this->rvalue='error_tpl.php';
                }
                break;
            case 'batchdelete':
                $this->batchdelete($this->getArrayParam('userArray'));
                $this->rvalue='list_tpl.php';
                return $this->nextAction('listUsers',array('how'=>$this->getParam('how'),'searchField'=>$this->getParam('searchField')));
                break;
            case 'register':
                $this->setLayoutTemplate(NULL);
                $this->rvalue='register_tpl.php';
                break;
            case 'submitregister':
                $this->newregister();
                $this->setLayoutTemplate(NULL);
                break;
            case 'selfdelete':
                    $this->rvalue='selfdelete_tpl.php';
                if ($this->selfDelete($this->objUser->userId())){
                    $this->objUser->logout();
                    $this->rvalue='okay_tpl.php';
                }       
                break;
            default:
                // Default View - show list of users with surname 'A'
                return $this->nextAction('listusers', array('how'=>'surname', 'searchField'=>'A'));
        }
        $this->message=$this->rstatus;
        $this->setvar('adminMessage',$this->message);	
        return $this->rvalue;
    }

    
    /** 
    * This is a method to determine if the user has to be logged in or not
    * It overides that in the parent class
    * @returns boolean TRUE or FALSE
    */
    function requiresLogin() 
    {
        $action=$this->getParam('action','NULL');
        switch ($action)
        {
            case 'register':
            case 'submitregister':
            case 'applyregister':
            case 'needpassword':
            case 'resetpassword':
                $this->setVar('pageSuppressToolbar', TRUE);
                return FALSE;
                break;
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
    function requiresAdmin($cmd)
    {
        
        $cmd = strtolower($cmd);
        
        switch($cmd)
        {
            case 'selfedit':
            case 'applyselfedit':
            case 'selfdelete':
            case 'changepassword':
            case 'apply changes':
            case 'mydetails':
            case 'register':
            case 'submitregister':
            case 'applyregister':
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
    * This is a method to display list of users for admin functions
    * @author James Scoble
    *
    * @param string $how - the method of searching used - username, surname or email
    * @param string $match - the pattern to match for
    * @param bool $adminLinks - whether to display the Add, Edit and Delete links
    */
    function ListUsers($how,$match,$adminLinks)
    {
        // An HTML table object is declared, and used to display the date in the template
        $objTblclass=&$this->newObject('htmltable','htmlelements');
        $objCheck=&$this->newObject('checkbox','htmlelements');
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
        $objTblclass->width='';
        $objTblclass->attributes=" align='center' border=0";
        $objTblclass->cellspacing='2';
        $objTblclass->cellpadding='2';
        $objTblclass->addHeader($field2,'odd');
        unset($field2);
    $r1=$this->objUserAdmin->getUsers($how,$match); // Table-derived functions called here.
        $rowcount='';
    foreach ($r1 as $line)
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
                $d1.=$this->objButtons->linkedButton("delete",$deleteLink);
                // Code for the checkbox - only display if user being listed is not a site-Admin
                // This checkbox allows group deletions of users
                if (!$this->objUser->lookupAdmin($line['userId'])){
                    $objCheck->checkbox('userArray[]'); 
                    $objCheck->setValue($line['userId']); 
                    $checkBox=$objCheck->show(); 
                } else { 
                    $checkBox='&nbsp;';
                    }
                $d1.=$checkBox;                                                                                                    
                $dline[]=$d1;
            }
            $objTblclass->row_attributes=" onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"";
            $objTblclass->addRow($dline,NULL,"class='".$oddOrEven."' onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"");
             unset($dline);
    }
            return $objTblclass->show();
    }  // end of function ListUsers

    /**
    * This is a method to display list of users for admin functions
    * @author James Scoble
    *
    * @param array $userData - the user information to display
    * @param bool $adminLinks - whether to display the Add, Edit and Delete links
    */
    function makeListUsersTable($userData,$adminLinks)
    {
        // An HTML table object is declared, and used to display the data in the template
        $objTblclass=&$this->newObject('htmltable','htmlelements');
        $objCheck=&$this->newObject('checkbox','htmlelements');
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
        $objTblclass->width='';
        $objTblclass->attributes=" align='center' border=0";
        $objTblclass->cellspacing='2';
        $objTblclass->cellpadding='2';
        $objTblclass->addHeader($field2,'odd');
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
                    $objCheck->checkbox('userArray[]'); 
                    $objCheck->setValue($line['userId']); 
                    $checkBox=$objCheck->show(); 
                } else { 
                    $checkBox='&nbsp;';
                    }
                $d1.=$checkBox;                                                                                                    
                $dline[]=$d1;
            }
            $objTblclass->row_attributes=" onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"";
            $objTblclass->addRow($dline,NULL,"class='".$oddOrEven."' onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"");
             unset($dline);
        }
            return $objTblclass->show();
    }

    
    /**
    * This is a method to display user's info for editing
    * @author James Scoble
    *
    * @param numeric $userid - the primary key of the user in the database
    *
    */
    function show4edit($userId)
    {
        $r1=$this->objUserAdmin->getUsers('userId',$userId,TRUE);
        $line=array_shift($r1);
        if (!$line) 
        { 
            $this->rstatus= 'error_no_userid';
            $this->rvalue='error_tpl.php';
        }  
        else
        {
            //$line['accesslevel']=$this->tblusergroups->lookupUser($userId); // don't need this anymore
            $this->setvar('userdata',$line);
            $this->rvalue='useredit_tpl.php';
            $this->setvar('ldapflag',$this->objUserAdmin->isLDAPUser($userId));

            $objImage=$this->getObject('imageupload');
            $this->imagelink=$objImage->userpicture($userId);
        } 
    }  // end of function show4edit


    /**
    * This is a method to check submitted info for any illegal data
    * @author James Scoble    
    * @returns string $message a code for the type of error, or "Looks Okay" if it does.
    */
    function check4edit()
    {
        if ($this->getParam('username')==""){
            return("need_username");  
        }
        if ($this->getParam('email')==""){
            return "need_email"; 
        }      
        if ((!$this->isAdmin)&&($this->getParam('userId')!=$this->objUser->userId())) // non-admin trying to edit someone else?
        {
            return('Not Admin!');
        }
        if ($this->getparam('username')!=$this->getParam('old_username')){
            $result=$this->objUserAdmin->checkDBase('0000',$this->getParam('username'));
            return $result;
        }
        return "Looks Okay";
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
            if ($this->getParam('admin_user', 0) == '1') {
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
    function check4delete($userId)
    {    
        if (isset($_GET['confirm'])&&($_GET['confirm']=='yes'))
        {   
            return('Looks Okay');
        }
        else
        {
            $this->rvalue='confirmdelete_tpl.php';
            $this->userdata=array_shift($this->objUserAdmin->getUsers('userId',$userId,TRUE));
            return ('Making Sure');
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
    * @author James Scoble
    *
    */
    function show4add()
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
    function check4add($username,$password,$email,$passwd,$userId)
    {      
        if ($username=="") { return("need_username"); }
        if ($password=="") { return("need_password"); }
        if ($email=="") {return "need_email"; }
        if ($password!=$passwd) {return "password_not_match";}
        // if all these test pass, the code continues...
        $result=$this->objUserAdmin->checkDbase($userId,$username);
        if ($result!='Looks Okay') { return $result; }
        // passess all other tests, then...
        return "Looks Okay";
    }
        
    /**
    * This is a method to add self-registering users
    * This function assumes that data has been sent from a webpage - the self-register page. 
    * It gets the info from the getParam() function.
    * The class variables $rvalue and $rstatus are used to record the results.
    * @author James Scoble
    */
    function newregister()
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
