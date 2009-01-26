<?php
 /**
 * User class
 *
 * User class for KEWL.NextGen. Provides login, logout, and basic user
 * related methods. This class provides the core of the user and permissions
 * management for KEWL.NextGen.
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
 * @package   security
 * @author James Scoble <jscoble@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* User class for KEWL.NextGen. Provides login, logout, and basic user
* related methods. This class provides the core of the user and permissions
* management for KEWL.NextGen.
* @author Derek Keats, James Scoble
* @Copyright GNU GPL
*/
class user extends dbTable
{
    public $objConfig;
    private $objLanguage;
    private $loggedInUsers;
    private $userLoginHistory;
    //var $userGroups;
    private $_record = NULL;

    private $imagePath;
    private $imageUrl;
    public $userData = NULL;

    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_users');
        $this->objConfig=$this->getObject('altconfig','config');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loggedInUsers = $this->getObject('loggedInUsers');
        $this->userLoginHistory = $this->getObject('userLoginHistory');
        $this->objSkin = $this->getObject('skin', 'skin');
        $this->objGroups = $this->getObject('groupadminmodel','groupadmin');
        $this->imagePath = $this->objConfig->getsiteRootPath().'/user_images/';
        $this->imageUrl = $this->objConfig->getsiteRoot().'user_images/';
    }

    /**
    * Get the desired login type.
    * This method is currently not in use - 2007-10-11
    * @return the login method to use, currently ldap or default
    */
    public function loginMethod()
    {
        // See if they want to login via LDAP
        if (isset($_POST['useLdap'])) {
            $useLdap = $_POST['useLdap'];
            if ($useLdap = 'yes') {
                $result = 'ldap';
            } else {
                $result = 'default';
            }
        } else {
            $result = 'default';
        }
        return $result;
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
    public function authenticateUser($username, $password, $remember = NULL)
    {
        $username = trim($username);
        $this->objAuth = $this->getObject('authenticate');
        $result = $this->objAuth->authenticateUser($username,$password, $remember);

        return $result;
    }

    /**
    * Look up user's data.
    * @param string $username
    * @return array on success, FALSE on failure.
    */
    public function lookupData($username)
    {
        $sql="SELECT
            tbl_users.username,
            tbl_users.userid,
            tbl_users.title,
            tbl_users.firstname,
            tbl_users.surname,
            tbl_users.pass,
            tbl_users.creationdate,
            tbl_users.emailaddress,
            tbl_users.logins,
            tbl_users.isactive,
            tbl_users.accesslevel
        FROM
            tbl_users
        WHERE
            (username = '".addslashes($username)."')";
        $array=$this->getArray($sql);
        if (!empty($array))
        {
            return $array[0];
        } else {
            return FALSE;
        }
    }

   /**
   * Method to do the database login based on the passed
   * values of username and password
   *
   * Depreciated 2007-10-11
   *
   * @param string $username The username supplied in the login
   * @param string $password The password supplied in the login
   * @return TRUE|FALSE Boolean indication of success of login
   */
    public function loginViaDatabase($username, $password)
    {
        $line=$this->lookupData($username);
        if ($line) {
            if ($line['isactive']=='0'){
                DEFINE('STATUS','inactive');
                return false;
            }
            if ($line['pass']==sha1('--LDAP--')){
                $objldap=$this->newObject('ldaplogin','security');
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

    /**
    * Method to store user information in a session
    * User Information is stored in a session to prevent unnecessary database calls
    */
    private function storeUserSession()
    {
        $this->setSession('isLoggedIn',TRUE);
        // set the line as a stdClass, serialize and store in session to lower db calls
        $user = new stdClass();
        // add the user info to the class
        $user->username = $this->_record['username'];
        $user->userid = $this->_record['userid'];
        $user->title = $this->_record['title'];
        $user->firstname = $this->_record['firstname'];
        $user->surname = $this->_record['surname'];
        $user->pass = NULL;
        $user->creationdate = $this->_record['creationdate'];
        $user->emailaddress = $this->_record['emailaddress'];
        $user->logins = $this->_record['logins'];
        $user->isactive = $this->_record['isactive'];

        // store a local copy
        $this->userData = $user;
        // serialize the object to preserve structure etc
        $user = serialize($user);
        // set it into session to be used elsewhere (objUser mainly)
        $this->setSession('userprincipal', $user);

        $username = $this->_record['username'];
        $this->setSession('username',$username);
        $this->setSession('userid', $this->_record['userid']);

        //$this->setSession('password',$this->getParam('password', ''));

        $title = stripcslashes($this->_record['title']);
        $this->setSession('title',$title);
        $firstname = stripcslashes($this->_record['firstname']);
        $surname = stripcslashes($this->_record['surname']);
        $this->setSession('name',$firstname.' '.$surname);
        $logins = $this->_record['logins'];
        $this->setSession('logins', $this->_record['logins']);
        $email = stripcslashes($this->_record['emailaddress']);
        $this->setSession('email',$email);
    }

    /**
    * Method to Update the Session Details for the Current User
    */
    public function updateUserSession()
    {
        if ($this->isLoggedIn()) { // check if logged in
            $record = $this->getRow('userid', $this->userId()); // Get Fresh Details

            if ($record != FALSE) { // Check against errors
                $this->_record = $record; // Update Details
                $this->storeUserSession();
            }
        }
    }

    /**
    * Method to store User Information in a session upon authentication
    */
    public function storeInSession()
    {
        $this->storeUserSession();

        $logins = $this->_record['logins'];
        $logins=$logins + 1;
        $this->userData->logins = $logins;
        $this->setSession('logins',$logins);

        $this->setSession('context','lobby');
        // Update the login history table
        $this->userLoginHistory->addHistoryEntry($this->userId());
        // Update the users table with the new login count
        $this->update('userid', $this->userId(), array('logins'=>$logins));
        // ---- Insert into the loggedinusers table
        $this->loggedInUsers->insertLogin($this->userId());
        //if ($this->userGroups->isAdministrator($userId)) {
        if ((isset($this->_record['accesslevel']))&&($this->_record['accesslevel']=='1')) {
            $this->setSession('isAdmin',TRUE);
        } else {
            $this->setSession('isAdmin',FALSE);
        }
   }

    /**
    * Method to do the LDAP login against an LDAP database
    * Depreciated 2007-10-11
    * @param string $username The username supplied in the login
    * @param string $password The password supplied in the login
    */
    public function loginViaLdap($username, $password)
    {
        $objldap=$this->newObject('ldaplogin','security');
        $info=$objldap->tryLogin($username,$password);
        if (is_array($info)) // if LDAP has confirmed login
        {
            $data=$this->lookupData($username);
            if (is_array($data) || $this->valueExists('userid',$info['userid']))// if we already have this user
            {
                $this->_record=$data;
            } else { // new user
                // Build up an array of the user's info
                if ($info['userid']==FALSE)
                {
                    $info['userid']=mt_rand(1000,9999).date('ymd');
                    $info['sex']='';
                    $info['accessLevel']='guests';
                    $info['howCreated']='LDAP';
                    $info['isactive']='1';
                    $info['country']=$this->objConfig->getCountry();
                    // Instantiate the sqlusers class and call the adduser() function
                    // To create the new user on the KNG system.
                    $tbl=$this->newObject('sqlusers','security');
                    $id=$tbl->addUser($info);
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
    public function logout()
    {
       $skin = $this->objSkin->getSkin();
       $this->loggedInUsers->doLogout($this->userId());
       // session_unset();
       $this->objLu->logout();
       $this->objSkin->setSession('skin', $skin);
    }

    /**
    * Method to update the curren't user's active timestamp in the
    * tbl_loggedinusers table
    */
    public function updateLogin() {
        $this->loggedInUsers->doUpdateLogin($this->userId());
        // also clear inactive users whilst updating this one
        $this->loggedInUsers->clearInactive();
    }

    /**
    * Method to return the time logged in for the active user
    */
    public function myTimeOn() {
        return $this->loggedInUsers->getMyTimeOn($this->userId());
    }

    /**
    * Method to add a new user to the Lecturer group
    * @param string $id the Primary Key ID of the new user
    */
    public function addLecturer($id)
    {
        $groupId = $this->objGroups->getId('Lecturers');
        $this->objGroups->addGroupUser($groupId,$id);
    }

    /**
    * Property to return login status. It returns TRUE if the
    * user is logged in and FALSE if not. This method has no
    * parameters.
    */
    public function isLoggedIn()
    {
        return $this->objLu->isLoggedIn();
    }


    public function notExpired() {
        return TRUE;
    }
    /**
    * This method determines if the user is an administrator of
    * the website by checking the session values set when the user
    * is logged in. The method returns
    * TRUE if the user is an administrator or FALSE if the
    * user is not an administrator. This method does not have
    * any parameters.
    */
    public function isAdmin()
    {
        // Here we need to distinguish between the session var not being set,
        // and being set to FALSE or NULL
        $isAdmin = $this->getSession('isAdmin','_default');
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
    public function lookupAdmin($userId)
    {
        $user = $this->objLuAdmin->perm->getUsers(array('filters' => array('auth_user_id' => $userId)));
        if(empty($user) || !isset($user) || $user[0]['perm_type'] < 5) {
            return FALSE;
        }
        else {
            return TRUE;
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
    public function inAdminGroup($userId, $group='Site Admin')
    {
        $groupId = $this->objGroups->getId($group);
        $return = $this->objGroups->isGroupMember($userId, $groupId);

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
    public function getUserId($username)
    {
        // get the user that we are interested in...
        //$user = $this->objLuAdmin->getUsers(array('container' => 'auth', 'filter' => array('handle' => $username)));

        //var_dump($user); die();
        $sql="select userid from tbl_users where username='$username'";
        $rs = $this->query($sql);
        if ($rs)
        {

        //    $line = $rs->fetchRow();
         $ret=$rs[0]["userid"];
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
    public function PKId($userId=NULL)
    {
        if ($userId==NULL)
        {
            $userId=$this->userId();
        }
        $sql="select id from tbl_users where userid='$userId'";
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
    public function getItemFromPkId($PKId,$field='userid')
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
    * @modified 29 - 09 - 2006 Megan Watson - removed call to fetchRow()
    * @param string $userId The numeric ID of a user, it defaults
    * to the userId of the current user when $numID is NULL.
    */
    public function userName($userId=FALSE) //use FALSE as the default and evaluate
    {
        if (!$userId) {
            $userName=$this->getSession('username');
            if ($userName) {
                $ret = $userName;
            } else {
                $ret =  $this->objLanguage->languageText("error_notloggedin");
            }
        } else {
            //look up third part numeric ID
            $sql="SELECT username FROM tbl_users WHERE userid='$userId'";
            $rs = $this->query($sql);
            if ($rs) {
//                $line = $rs->fetchRow();
                $ret=$rs[0]["username"];
            } else {
                $ret=$this->objLanguage->languageText("error_datanotfound", 'security');
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
    public function fullname($userId=NULL) //use NULL as the default and evaluate
    {
        if ($userId==NULL) {
            $fullname=$this->getSession('name');
            if ($fullname == NULL) {
                $result = $this->objLanguage->languageText("error_notloggedin");
            } else {
                $result = $fullname;
            }
        } else {
            //look up third part numeric ID
            $line = $this->getRow('userid', $userId);

            if ($line == FALSE)
            {
                $result=$this->objLanguage->languageText("error_datanotfound", 'security');
            } else {
                $result=$line['firstname'].' '.$line['surname'];
            }
        }
        return $result;
    }

    /**
    * This method returns the surname of a given user. It takes
    * the userId of a user as a parameter but defaults to the
    * userId of the currently logged-in user if none is supplied.
    *
    * @param string $userId The numeric ID of a user, it defaults
    * to the userId of the current user by setting it to NULL as
    * default.
    * @author Megan Watson - added functions from kinky
    * @returns string $surname
    */
    public function getSurname($userId = null)
    {
        if (!$userId) {
            $userId = $this->getSession('userid');
        }
        $sql = "SELECT surname FROM tbl_users WHERE userid='" . $userId . "'";
        $rs = $this->query($sql);
        if ($rs) {
            return $rs[0]["surname"];
        } else {
            return false;
        }
    }

    /**
    * This method returns the first name of a given user. It takes
    * the userId of a user as a parameter but defaults to the
    * userId of the currently logged-in user if none is supplied.
    *
    * @param string $userId The numeric ID of a user, it defaults
    * to the userId of the current user by setting it to NULL as
    * default.
    * @author Megan Watson - added functions from kinky
    * @returns string $firstname
    */
    public function getFirstname($userId = NULL)
    {
        if (!$userId) {
            $userId = $this->getSession('userid');
        }
        $sql = "SELECT firstname FROM tbl_users WHERE userid='" . $userId . "'";
        $rs = $this->query($sql);

        if ($rs) {
            return $rs[0]["firstname"];
        } else {
            return false;
        }
    }

    /**
    * This method returns the staff name of a given user. It takes
    * the userId of a user as a parameter but defaults to the
    * userId of the currently logged-in user if none is supplied.
    *
    * @param string $userId The numeric ID of a user, it defaults
    * to the userId of the current user by setting it to NULL as
    * default.
    * @author Megan Watson - added functions from kinky
    * @returns string $firstname
    */
    public function getStaffNumber($userId = NULL)
    {
        if (!$userId) {
            $userId = $this->getSession('userid');
        }
        $sql = "SELECT staffnumber FROM tbl_users WHERE userid='" . $userId . "'";
        $rs = $this->query($sql);

        if ($rs) {
            return $rs[0]["staffnumber"];
        } else {
            return false;
        }
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
    public function email($userId=NULL)//use NULL as the default and evaluate
    {
        if (!$userId){
            $email=$this->getSession('email');
            if ($email) {
                $ret = $email;
            } else {
                $ret = $this->objLanguage->languageText("error_notloggedin");
            }
        } else {
            $sql="SELECT emailaddress FROM tbl_users WHERE userid='$userId'";
            $rs = $this->getArray($sql);
            if (count($rs) > 0) {
                $ret=$rs[0]["emailaddress"];
            } else {
                $ret=$this->objLanguage->languageText("error_datanotfound", 'security');
            }
        }
        return $ret;
    }

    /**
    * Return the numeric identifier of the user who
    * is currently logged in
    * This function has been simplified down now that it calls getSession
    */
    public function userId()
    {
        return $this->getSession('userid');
    }


    /**
    * Return whether or not the specified user is Active
    * @param string $userId
    * @returns TRUE|FALSE
    */
    public function isActive($userId)
    {
        $sql="SELECT isactive from tbl_users where userid='$userId'";
        $rows=$this->getArray($sql);
        if (!empty($rows)&&($rows[0]['isActive']=='1')){
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
    public function howCreated($userId)
    {
        $sql="SELECT howcreated from tbl_users where userid='$userId'";
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
    public function logins($userId=NULL)
    {
        if (!$userId) {
            $logins=$this->getSession('logins');
            if ($logins){
                $ret=$logins;
            } else {
                $ret = $this->objLanguage->languageText("error_notloggedin");
            }
        } else {
            $sql="SELECT logins FROM tbl_users WHERE userid='$userId'";
            $rs = $this->query($sql);
            if ($rs) {
                $line = $rs->fetchRow();
                $ret=$line["logins"];
            } else {
                   $ret=$this->objLanguage->languageText("error_datanotfound", 'security');
            }
       }
       return $ret;
    }

    /**
    * Return the last login date for the current user
    */
    public function getLastLoginDate($userId=NULL)
    {
        if (!$userId) {
            $userId=$this->userId();
        }
        return $this->userLoginHistory->doGetLastLogin($userId);
    }


    /**
    * Method to check whether the user has a custom image or not.
    * @param string $userId User Id of the user
    * @return boolean TRUE/FALSE
    */
    public function hasCustomImage($userId=NULL)
    {
        if (!$userId) {
            $userId=$this->userId();
        }

        return file_exists($this->imagePath.$userId.'.jpg');
    }

    /**
    * Method to return a path to the user's image
    * @param string $userId User Id of the user
    * @return string Image of User
    */
    public function getUserImage($userId=NULL, $forceRefresh=FALSE, $alt=NULL)
    {
        if (!$userId) {
            $userId=$this->userId();
        }

        if ($forceRefresh) {
            $forceRefresh = '?'.mktime();
        }

        if ($alt == NULL) {
            $alt = '';
        } else {
            $alt = ' alt="'.$alt.'" title="'.$alt.'" ';
        }


        if (file_exists($this->imagePath.$userId.'.jpg')){
            return '<img src="'.$this->imageUrl.$userId.'.jpg'.$forceRefresh.'" '.$alt.' />';
        } else {
            return '<img src="'.$this->imageUrl.'default.jpg" '.$alt.' />';
        }
    }

    /**
    * Method to return a path to the user's image
    * @param string $userId User Id of the user
    * @return string Image of User
    */
    public function getUserImageNoTags($userId=NULL, $forceRefresh=FALSE)
    {
        if (!$userId) {
            $userId=$this->userId();
        }

        if ($forceRefresh) {
            $forceRefresh = '?'.mktime();
        }

        if (file_exists($this->imagePath.$userId.'.jpg')){
            return $this->imageUrl.$userId.'.jpg'.$forceRefresh;
        } else {
            return $this->imageUrl.'default.jpg';
        }
    }


    /**
    * Method to return a path to the small user's image
    * @param string $userId User Id of the user
    * @param string $alt Alt Text and Title for the Image
    * @return string Small Image of User
    */
    public function getSmallUserImage($userId=NULL, $alt=NULL)
    {
        if (!$userId) {
            $userId=$this->userId();
        }

        if ($alt == NULL) {
            $alt = '';
        } else {
            $alt = ' alt="'.$alt.'" title="'.$alt.'" ';
        }

        if (file_exists($this->imagePath.$userId.'.jpg')){
            return '<img src="'.$this->imageUrl.$userId.'_small.jpg" '.$alt.'/>';
        } else {
            return '<img src="'.$this->imageUrl.'default_small.jpg" '.$alt.'/>';
        }
    }


    /**
    * Method to check if this user has context Author access
    * @author Jonathan Abrahams
    * @since 9 March 2005
    * @return true|false Return true if this user has context Author access.
    */
    public function isContextAuthor()
    {
        $objContextPermissions = $this->getObject('contextcondition','contextpermissions');
        return $objContextPermissions->hasContextPermission( 'isAuthor' );
    }

    /**
    * Method to check if this user has context Editor access
    * @author Jonathan Abrahams
    * @since 9 March 2005
    * @return true|false Return true if this user has context Editor access.
    */
    public function isContextEditor()
    {
        $objContextPermissions = $this->getObject('contextcondition','contextpermissions');
        return $objContextPermissions->hasContextPermission( 'isEditor' );
    }

    /**
    * Method to check if this user has context Reader access
    * @author Jonathan Abrahams
    * @since 9 March 2005
    * @return true|false Return true if this user is has context Readre access.
    */
    public function isContextReader()
    {
        $objContextPermissions = $this->getObject('contextcondition','contextpermissions');
        return $objContextPermissions->hasContextPermission( 'isReader' );
    }

    /**
    * Method to check if this user is a context Lecturer
    * @author Jonathan Abrahams
    * @param string $userId: The id of the user to check | current user if not given
    * @param string $contextCode: The context to check | current context if not given
    * @return true|false Return true if this user is a member of the context lecturers group.
    */
    public function isContextLecturer($userId = NULL, $contextCode = NULL)
    {
        // get the group with the context code. Group is identified by contextcode (group_define_name)

        return true;
        if($userId == NULL && $contextCode == NULL){
            $objContextPermissions = $this->getObject('contextcondition','contextpermissions');
            return $objContextPermissions->isContextMember( 'Lecturers' );
        }else{
            $userId = isset($userId) ? $userId : $this->userId();
            $contextCode = isset($contextCode) ? $contextCode : $this->getSession('contextCode');
            $objGroupAdmin = $this->getObject('groupadminmodel', 'groupadmin');
            $groupId = $objGroupAdmin->getLeafId(array($contextCode, 'Lecturers'));
            return $objGroupAdmin->isGroupMember($this->PKId($userId), $groupId);
        }
    }

    /**
    * This method checks if a user
    * is a cours administrator ie. eithere
    * a lecturere are an administrator
    */
    public function isCourseAdmin()
    {
        if($this->isContextLecturer() || $this->IsAdmin())
        {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Method to check if this user is a context Student
    * @author Jonathan Abrahams
    * @since 9 March 2005
    * @return true|false Return true if this user is a member of the context Students group.
    */
    public function isContextStudent()
    {
        $objContextPermissions = $this->getObject('contextcondition','contextpermissions');
        return $objContextPermissions->isContextMember( 'Students' );
    }

    /**
    * Method to check if this user is a context Lecturers
    * @author Jonathan Abrahams
    * @since 9 March 2005
    * @return true|false Return true if this user is a member of the context Guest group.
    */
    public function isContextGuest()
    {
        $objContextPermissions = $this->getObject('contextcondition','contextpermissions');
        return $objContextPermissions->isContextMember( 'Guest' );
    }

    /**
    * Method to check if this user is a site Lecturer
    * @author Jonathan Abrahams
    * @since 9 March 2005
    * @return true|false Return true if this user is a member of the site lecturers group.
    */
    public function isLecturer()
    {
        $objContextPermissions = $this->getObject('contextcondition','contextpermissions');
        return $objContextPermissions->isMember( 'Lecturers' );
    }

    /**
    * Method to check if this user is a site Student
    * @author Jonathan Abrahams
    * @since 9 March 2005
    * @return true|false Return true if this user is a member of the site Students group.
    */
    public function isStudent()
    {
        $objContextPermissions = $this->getObject('contextcondition','contextpermissions');
        return $objContextPermissions->isMember( 'Students' );
    }

    /**
    * Method to check if this user is a site Lecturers
    * @author Jonathan Abrahams
    * @since 9 March 2005
    * @return true|false Return true if this user is a member of the site Guest group.
    */
    public function isGuest()
    {
        $objContextPermissions = $this->getObject('contextcondition','contextpermissions');
        return $objContextPermissions->isMember( 'Guest' );
    }

    public function getUserPic()
      {
        $objUserPic = $this->getObject('imageupload', 'useradmin');
        $objBox =  $this->newObjecT('featurebox', 'navigation');
        $str = '<p align="center"><img src="'.$objUserPic->userpicture($this->userId() ).'" alt="User Image" /></p>';
        return $objBox->show($this->fullName(), $str);
      }

    public function getUserDetails($userId)
    {
        return $this->getRow('userid', $userId);
    }

}
?>