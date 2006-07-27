<?php
/* -------------------- USER CLASS ----------------*/

/**
* User class for KEWL.NextGen. Provides login, logout, and basic user
* related methods. This class provides the core of the user and permissions
* management for KEWL.NextGen.
* @Author Derek Keats, James Scoble
* @Copyright GNU GPL
*/
class user extends dbTable
{
    var $objLanguage;
    var $loggedInUsers;
    var $userGroups;
    var $userLoginHistory;
    var $_record = NULL;
    var $objConfig;

   /*--------------START OF METHODS SECTION ------------------------*/

   /**
   * Initialiser to set the table that is being called.
   */
   function init()
   {
       parent::init("tbl_users");

       $this->objConfig=&$this->getObject('altconfig','config');
       $this->objLanguage =& $this->getObject('language', 'language');
       $this->loggedInUsers =& $this->getObject('loggedInUsers');
       $this->userLoginHistory =& $this->getObject('userLoginHistory');

       //Get an instance of the skin
        $this->objSkin = &$this->getObject('skin', 'skin');
   }

   /**
   * Method to get the desired login type
   * This makes it easy to add login methods
   * @return the login method to use, currently ldap or default
   */
   function loginMethod() {
        // See if they want to login via LDAP
        if (isset($_POST['useLdap'])) {
            $useLdap=$_POST['useLdap'];
            if ($useLdap="yes") {
                $ret="ldap";
            } else {
                $ret="default";
            }
        } else {
            $ret="default";
        }
        return $ret;
   }

   /**
   * Method to log a user into the site
   * Can login via mySQL internal database or via LDAP
   * Login via LDAP is selected from the form on the login
   * page.
   * @param $username The username to authenticate
   * @param $password The password given which should be checked
   * @return TRUE|FALSE Boolean value indicating success of authentication
   */
   function authenticateUser($username, $password) {
        $username = trim($username);
        //$password = sha1(trim($password)); //we don't do this here, we do it later.
        // Login via the chosen method
        switch($this->loginMethod()) {
            case "ldap":
                return $this->loginViaLdap($username, $password);
            case "default":
                return $this->loginViaDatabase($username, $password);
            default:
                die("Unknown login method");
        }
   }

   /**
   * Method to look up user's database info
   * returns an array on success, FALSE on failure.
   * @author James Scoble
   * @param string $username
   */
    function lookupData($username)
    {
        // Build the SQL statement to select the user
        // We have to spell out the fields here, to avoid certain problems
        $sql="SELECT
        tbl_users.username,
        tbl_users.userId,
        tbl_users.title,
        tbl_users.firstName,
        tbl_users.surname,
        tbl_users.pass,
        tbl_users.creationDate,
        tbl_users.emailAddress,
        tbl_users.logins,
        tbl_users.isActive,
        tbl_users.accesslevel
        FROM tbl_users WHERE (username = '".addslashes($username)."')";
        /*
        //Return the recordset
        $rs=$this->query($sql);
        $line=$rs->fetchRow();
        //$line=$this->getRow('username',$username);
        */
        //$sql="SELECT * FROM tbl_users WHERE (username = '".addslashes($username)."')";
        $line=$this->getArray($sql);
        if (isset($line[0]['username']))
        {
            return $line[0];
        }
        else
        {
            return FALSE;
        }
    }

   /**
   * Method to do the database login based on the passed
   * values of username and password
   * @param string $username The username supplied in the login
   * @param string $password The password supplied in the login
   * @return TRUE|FALSE Boolean indication of success of login
   */
    function loginViaDatabase($username, $password)
    {
        $line=$this->lookupData($username);
        if ($line) {
            if ($line['isactive']==0){
                DEFINE('STATUS','inactive');
                return false;
            }
            if ($line['pass']==sha1('--LDAP--')){
                $objldap=&$this->newObject('ldaplogin','security');
                $info=$objldap->tryLogin($username,$password);
                if (is_array($info)){
                    $this->_record = $line;
                    return TRUE;
                    } else {
                    return FALSE;
                }
            } else {
                $password=sha1(trim($password));
                // if the login was successful
                if ( strtolower($line['pass'])==strtolower($password) ){
                    $this->_record = $line;
                    return true;
                }
            }
        }
        return false;
   }

   function storeInSession()
   {
        //log the user into the site
        $this->setSession('isLoggedIn',TRUE);
        $username = $this->_record['username'];
        $this->setSession('username',$username);
        $userId=$this->_record['userid'];
        $this->setSession('userid',$userId);
        $this->setSession('password',$this->getParam('password', ''));
        $title = stripcslashes($this->_record['title']);
        $this->setSession('title',$title);
        $firstname = stripcslashes($this->_record['firstname']);
        $surname = stripcslashes($this->_record['surname']);
        $this->setSession('name',$firstname.' '.$surname);
        $logins = $this->_record['logins'];
        $logins=$logins + 1;
        $this->setSession('logins',$logins);
        $email = stripcslashes($this->_record['emailaddress']);
        $this->setSession('email',$email);
        $this->setSession('context','lobby');
        // ---- Update the login history table
        $this->userLoginHistory->addHistoryEntry($userId);
        // ---- Update the users table with the new login count
        $rsArray=array(
            'logins'=>$logins);
        $this->update("userId", $userId, $rsArray);
        // ---- Insert into the loggedinusers table
        $this->loggedInUsers->insertLogin($userId);

        //if ($this->userGroups->isAdministrator($userId)) {
        if ((isset($this->_record['accesslevel']))&&($this->_record['accesslevel']=='1')) {
            $this->setSession('isAdmin',TRUE);
        } else {
            $this->setSession('isAdmin',FALSE);
        }
   }

   /**
   * Method to do the LDAP login against an LDAP database
   * @param string $username The username supplied in the login
   * @param string $password The password supplied in the login
   */
    function loginViaLdap($username, $password)
    {
        $objldap=$this->newObject('ldaplogin','security');
        $info=$objldap->tryLogin($username,$password);
        if (is_array($info)) // if LDAP has confirmed login
        {
            $data=$this->lookupData($username);
            if ($data) // if we already have this user
            {
                $this->_record=$data;
            } else { // new user
                // Build up an array of the user's info
                if ($info['userId']==FALSE)
                {
                    $info['userId']=mt_rand(1000,9999).date('ymd');
                    $info['sex']='';
                    $info['accessLevel']='guests';
                    $info['howCreated']='LDAP';
                    $info['country']=$this->objConfig->getCountry();
                    // Instantiate the sqlusers class and call the adduser() function
                    // To create the new user on the KNG system.
                    $tbl=$this->newObject('sqlusers','security');
                    $id=$tbl->addUser($info);
                    //Check for Alumni status and add to table accordingly
                    if ($this->objConfig->isAlumni()){
                        $objAlumni=$this->getObject('alumniusers','useradmin');
                        $objAlumni->insert(array('userId'=>$info['userId'],'firstName'=>$info['firstName'],'surname'=>$info['surname']));
                    }
                    // If LDAP confirms the user is an Academic,
                    // add as a site-lecturer in KNG groups.
                    if ($objldap->isAcademic($username)){
                        $this->addLecturer($id);
                    }
                }
                $this->_record=$info;
            }
        return TRUE;
        } else {
            return FALSE;
        }
    }

   /**
   * Method to logout user from the site. The method deletes
   * the user from the database table tbl_loggedinusers, destroys
   * the session, and redirects the user to the index page,
   * index.php. This method has no parameters. See comments on insertlogin.
   */
   function logout() {
       $skin = $this->objSkin->getSkin();
       $this->loggedInUsers->doLogout($this->userId());
       session_unset();
       //session_destroy();
       $this->objSkin->setSession('skin', $skin);
   }

   /**
   * Method to update the curren't user's active timestamp in the
   * tbl_loggedinusers table
   */
   function updateLogin() {
        $this->loggedInUsers->doUpdateLogin($this->userId());

        // also clear inactive users whilst updating this one
        $this->loggedInUsers->clearInactive();
   }

   /**
   * Method to return the time logged in for the active user
   */
   function myTimeOn() {
        return $this->loggedInUsers->getMyTimeOn($this->userId());
   }

   /**
   * Method to verify that the user should still be logged in
   */
   function notExpired()
   {
       return TRUE;
       $inactiveTime=$this->loggedInUsers->getInactiveTime($this->userId());
       if (($inactiveTime>$this->objConfig->systemTimeout())){
           return FALSE;
       } else {
           return TRUE;
       }
    }

    /**
    * Method to add a new user to the Lecturer group
    * @param string $id the Primary Key ID of the new user
    */
    function addLecturer($id)
    {
        $this->objGroups=&$this->getObject('groupadminmodel','groupadmin');
        $groupId=$this->objGroups->getLeafId(array('Lecturers'));
        $this->objGroups->addGroupUser($groupId,$id);
    }

   /*--------------END OF METHODS SECTION ------------------------*/


   /*--------------START OF PROPERTIES SECTION ------------------------*/


   /**
   * Property to return login status. It returns TRUE if the
   * user is logged in and FALSE if not. This method has no
   * parameters.
   */
   function isLoggedIn() {
      $loggedIn=$this->getSession('isLoggedIn');
      if ($loggedIn){
          if ($this->notExpired()){
              $ret = $loggedIn;
              return $ret;
          } else {
              $this->logout();
              return FALSE;
          }
      } else {
         return FALSE;
      }
   }


    /**
    * This method determines if the user is an administrator of
    * the website by checking the session values set when the user
    * is logged in. The method returns
    * TRUE if the user is an administrator or FALSE if the
    * user is not an administrator. This method does not have
    * any parameters.
    */
    function isAdmin()
    {
        // Here we need to distinguish between the session var not being set,
        // and being set to FALSE or NULL
        $isAdmin=$this->getSession('isAdmin','_default');
        if ($isAdmin!='_default') {
            if ($isAdmin) {
                return TRUE;
            } else {
                //return FALSE;
            }
        }
        // Now we check the database

        if (($this->lookupAdmin($this->userId()))||($this->inAdminGroup($this->userId()))) {

            return TRUE;
        } else {
            return FALSE;
        }
    }

   /**
   * This method looks in the database to see if the user is a site-admin or not.
   * Don't confuse it with the isAdmin() function above, which checks session data.
   * @author James Scoble
   * @param string $userId
   * @returns boolean TRUE or FALSE
   */
    function lookupAdmin($userId)
    {
        $sql="SELECT accesslevel from tbl_users where userId='$userId'";

        $return=$this->getArray($sql);

        if ((isset($return[0]))&&($return[0]['accesslevel']=='1')){
            return TRUE;
        }else{
            return FALSE;
        }
    }

   /**
   * This method consults functions in the groupadmin module's classes to see if the user is a site-admin or not.
   * Don't confuse it with the isAdmin() function above, which checks session data, or the lookupAdmin() function,
   * which looks in the tbl_users table.
   * @author James Scoble
   * @param string $userId
   * @returns boolean TRUE or FALSE
   */
    function inAdminGroup($userId,$group='Site Admin')
    {
        $objGroupModel=&$this->getObject('groupadminmodel','groupadmin');
        $id=$this->PKid($userId);
        $groupId=$objGroupModel->getId($group);
        $return=$objGroupModel->isGroupMember($id,$groupId);
        return $return;
    }

    /**
    * This method returns the userId of a given user. It takes the username
    * as a parameter, and looks up the userId from the database. This function
    * should not be confused with the userId() function, which returns the userId
    * of the current logged-in user.
    * @author James Scoble
    * @param string username
    * @returns strin userId
    */
    function getUserId($username)
    {
        $sql="select userId from tbl_users where userName='".$username."'";
        $rs = $this->query($sql);
        if ($rs)
        {
            $line = $rs->fetchRow();
            $ret=$line["userId"];
        }
        else
        {
            $ret=FALSE;
        }
        return $ret;
    }

    /**
    * Returns the system-generated Primary Key for a given user's sql entry.
    * This info is not usually need, but there might be exceptions
    * @param strong $userId
    * @returns string|FALSE
    */
    function PKId($userId=NULL)
    {
        if ($userId==NULL)
        {
            $userId=$this->userId();
        }
        $sql="select id from tbl_users where userId='".$userId."'";
        $rs = $this->query($sql);
        if ($rs)
        {
            $ret=$rs[0]["id"];
        }
        else
        {
            $ret=FALSE;
        }
        return $ret;
    }


    /**
    * This method returns the userId from the PKId
    * Added 11 August 2005
    * @author James Scoble
    * @parm string $PKId the primary key Id
    * @returns string $userId the user Id
    */
     function getItemFromPkId($PKId,$field='userId')
     {
         $data=$this->getRow('id', $PKId);
         return $data[$field];
     }


   /**
   * This method returns the username of a given user. It takes
   * the userId of a user as a parameter but defaults to the
   * userId of the currently logged-in user if none is supplied.
   * This replaces the lookup capabilities of the PEOPLE object of
   * KEWL 1.2. It can thus be used to lookup the userName of another
   * user.
   * @param string $userId The numeric ID of a user, it defaults
   * to the userId of the current user when $numID is NULL.
   */
   function userName($userId=FALSE) { //use FALSE as the default and evaluate
       if (!$userId) {
           $userName=$this->getSession('username');
           if ($userName) {
               $ret = $userName;
           } else {
               $ret =  $this->objLanguage->languageText("error_notloggedin");
           }
       } else {
           //look up third part numeric ID
           $sql="SELECT userName FROM tbl_users WHERE userId='".$userId."'";
           $rs = $this->query($sql);
           if ($rs) {
               $line = $rs->fetchRow();
               $ret=$line["userName"];
           } else {
               $ret=$this->objLanguage->languageText("error_datanotfound");
           }
       }
       return $ret;
   }

   /**
   * This method returns the full name of a given user. It takes
   * the userId of a user as a parameter but defaults to the
   * userId of the currently logged-in user if none is supplied.
   * This replaces the lookup capabilities of the PEOPLE object of
   * KEWL 1.2. It can thus be used to lookup the userName of another
   * user.
   * @param string $userId The numeric ID of a user, it defaults
   * to the userId of the current user by setting it to NULL as
   * default.
   */
   function fullname($userId=NULL) {  //use NULL as the default and evaluate
          if (!$userId) {
            $fullname=$this->getSession('name');
            if ($fullname) {
                   $ret = $fullname;
           } else {
                   $ret =  $this->objLanguage->languageText("error_notloggedin");
           }
          } else {
           //look up third part numeric ID
            $sql="SELECT firstName, surname FROM tbl_users WHERE userId='".$userId."'";
            $rs = $this->_execute($sql);
            if ($rs) {
                $line = $rs->fetchOne();
                $ret=$line["firstName"]." ".$line["surname"];
            } else {
                   $ret=$this->objLanguage->languageText("error_datanotfound");
            }
       }
       return $ret;
   }

   /**
   * This method returns the email address of a given user. It takes
   * the userId of a user as a parameter but defaults to the
   * userId of the currently logged-in user if none is supplied.
   * This replaces the lookup capabilities of the PEOPLE object of
   * KEWL 1.2. It can thus be used to lookup the userName of another
   * user.
   * @param string $userId The numeric ID of a user, it defaults
   * to the userId of the current user by setting it to NULL as
   * default.
   */
   function email($userId=NULL) {  //use NULL as the default and evaluate
          if (!$userId){
            $email=$this->getSession('email');
            if ($email) {
                   $ret = $email;
            } else {
                   $ret = $this->objLanguage->languageText("error_notloggedin");
           }
          } else {
               $sql="SELECT emailAddress FROM tbl_users WHERE userId='".$userId."'";
            $rs = $this->query($sql);
            if ($rs) {
                $line = $rs->fetchRow();
                $ret=$line["emailAddress"];
            } else {
                   $ret=$this->objLanguage->languageText("error_datanotfound");
            }
       }
       return $ret;
    }

    /**
    * Return the numeric identifier of the user who
    * is currently logged in
    * This function has been simplified down now that it calls getSession
    */
    function userId() {
        return $this->getSession('userid');
    }


   /**
   * Return whether or not the specified user is Active
   * @param string $userId
   * @returns TRUE|FALSE
   */
   function isActive($userId)
   {
        $sql="SELECT isActive from tbl_users where userId='$userId'";
        $return=$this->getArray($sql);
        if ((isset($return[0]))&&($return[0]['isActive']=='1')){
            return TRUE;
        } else {
            return FALSE;
        }
   }

   /**
   * Return how the specified user account was created
   * @param string $userId
   * @returns string $howCreated
   */
   function howCreated($userId)
   {
        $sql="SELECT howCreated from tbl_users where userId='$userId'";
        $return=$this->getArray($sql);
        if (isset($return[0])){
            return $return[0]['howCreated'];
        } else {
            return FALSE;
        }
   }

   /**
   * Return the number of times a current user has logged
   * into the site.
   * @param string $userId The numeric ID of a user, it defaults
   * to the userId of the current user by setting it to NULL as
   * default.
   */
   function logins($userId=NULL) {
        if (!$userId) {
            $logins=$this->getSession('logins');
            if ($logins){
                $ret=$logins;
            } else {
                $ret = $this->objLanguage->languageText("error_notloggedin");
            }
        } else {
            $sql="SELECT logins FROM tbl_users WHERE userId='".$userId."'";
            $rs = $this->query($sql);
            if ($rs) {
                $line = $rs->fetchRow();
                $ret=$line["logins"];
            } else {
                   $ret=$this->objLanguage->languageText("error_datanotfound");
            }
       }
       return $ret;
    }

   /**
   * Return the last login date for the current user
   */
   function getLastLoginDate($userId=NULL) {
        if (!$userId) {
            $userId=$this->userId();
        }
        return $this->userLoginHistory->doGetLastLogin($userId);
   }


   /**
   * Return the URL for a user's image, if one exists
   */
   function getUserImagePath($userId=NULL){
       if (!$userId) {
           $userId=$this->userId();
       }
       return $this->objConfig->userfiles().'/'.$userId.'.jpg';
   }

   /**
   *Method to return a path to the to user's image
   */
   function getUserImage($height=NULL,$width=NULL,$userId=NULL){
       if($height){
               $height=' Height="'.$height.'"  ';
       }
       if($width){
           $width=' width="'.$width.'"  ';
       }
       return '<img '.$height.$width.' src="'
           .$this->getUserImagePath($userId)
           .'" alt="'
           .$this->fullName($userId).'">';
   }


   /**
   * Method to check if this user has context Author access
   * @author Jonathan Abrahams
   * @since 9 March 2005
   * @return true|false Return true if this user has context Author access.
   */
   function isContextAuthor()
   {
       $objContextPermissions = &$this->getObject('contextcondition','contextpermissions');
       return $objContextPermissions->hasContextPermission( 'isAuthor' );
   }
   /**
   * Method to check if this user has context Editor access
   * @author Jonathan Abrahams
   * @since 9 March 2005
   * @return true|false Return true if this user has context Editor access.
   */
   function isContextEditor()
   {
       $objContextPermissions = &$this->getObject('contextcondition','contextpermissions');
       return $objContextPermissions->hasContextPermission( 'isEditor' );
   }
   /**
   * Method to check if this user has context Reader access
   * @author Jonathan Abrahams
   * @since 9 March 2005
   * @return true|false Return true if this user is has context Readre access.
   */
   function isContextReader()
   {
       $objContextPermissions = &$this->getObject('contextcondition','contextpermissions');
       return $objContextPermissions->hasContextPermission( 'isReader' );
   }

   /**
   * Method to check if this user is a context Lecturer
   * @author Jonathan Abrahams
   * @since 9 March 2005
   * @return true|false Return true if this user is a member of the context lecturers group.
   */
   function isContextLecturer()
   {
       $objContextPermissions = &$this->getObject('contextcondition','contextpermissions');
       return $objContextPermissions->isContextMember( 'Lecturers' );
   }

   /**
   * This method checks if a user
   * is a cours administrator ie. eithere
   * a lecturere are an administrator
   */
   function isCourseAdmin()
   {
       if($this->isContextLecturer || $this->IsAdmin())
       {
           return true;
        }
       else
       {
           return false;
        }
    }
   /**
   * Method to check if this user is a context Student
   * @author Jonathan Abrahams
   * @since 9 March 2005
   * @return true|false Return true if this user is a member of the context Students group.
   */
   function isContextStudent()
   {
       $objContextPermissions = &$this->getObject('contextcondition','contextpermissions');
       return $objContextPermissions->isContextMember( 'Students' );
   }
   /**
   * Method to check if this user is a context Lecturers
   * @author Jonathan Abrahams
   * @since 9 March 2005
   * @return true|false Return true if this user is a member of the context Guest group.
   */
   function isContextGuest()
   {
       $objContextPermissions = &$this->getObject('contextcondition','contextpermissions');
       return $objContextPermissions->isContextMember( 'Guest' );
   }

   /**
   * Method to check if this user is a site Lecturer
   * @author Jonathan Abrahams
   * @since 9 March 2005
   * @return true|false Return true if this user is a member of the site lecturers group.
   */
   function isLecturer()
   {
       $objContextPermissions = &$this->getObject('contextcondition','contextpermissions');
       return $objContextPermissions->isMember( 'Lecturers' );
   }
   /**
   * Method to check if this user is a site Student
   * @author Jonathan Abrahams
   * @since 9 March 2005
   * @return true|false Return true if this user is a member of the site Students group.
   */
   function isStudent()
   {
       $objContextPermissions = &$this->getObject('contextcondition','contextpermissions');
       return $objContextPermissions->isMember( 'Students' );
   }
   /**
   * Method to check if this user is a site Lecturers
   * @author Jonathan Abrahams
   * @since 9 March 2005
   * @return true|false Return true if this user is a member of the site Guest group.
   */
   function isGuest()
   {
       $objContextPermissions = &$this->getObject('contextcondition','contextpermissions');
       return $objContextPermissions->isMember( 'Guest' );
   }

}
?>
