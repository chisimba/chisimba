<?
/**
* User Admin Functionality Class
*
* This class is a revised version of useradmin_model also found in this module.
* DO NOT use this class. It will replace useradmin_model and be removed afterwards
*
* Still work in progress
* 
* @author Tohir Solomons
*/
class useradmin_model2 extends dbtable
{

    public $objConfig;
    private $objUser;
    private $objLanguage;

    /**
    * Constructor
    */
    public function init()
    {
	    parent::init('tbl_users');
        $this->objConfig=&$this->getObject('altconfig','config');
        $this->objUser=&$this->getObject('user','security');
        $this->objLanguage=&$this->getObject('language','language');
    }


    /**
    * Method to get the details of a user by providing the id (not userid)
    * @param string $id Id of the User
    * @return array|boolean Array if the user exists, else FALSE
    */
    public function getUserDetails($id)
    {
        return $this->getRow('id', $id);
    }
    
    /**
    * Method to remove the user's custom image
    * @param string $userId User Id of the User
    */
    public function removeUserImage($userid)
    {
        $image = $this->objConfig->getsiteRootPath().'/user_images/'.$userid.'.jpg';
        
        if (file_exists($image)) {
            unlink($image);
        }
        
        $image = $this->objConfig->getsiteRootPath().'/user_images/'.$userid.'_small.jpg';
        
        if (file_exists($image)) {
            unlink($image);
        }
        
        return;
    }
    
    /**
    * Method to add a user to the system
    * 
    * @param string $userid        User Id
    * @param string $username      Username
    * @param string $password      Unencrypted password - This function will encrypt it
    * @param string $title         Title of the user
    * @param string $firstname     User's Firstname
    * @param string $surname       User's Surname
    * @param string $email         User's Email Address
    * @param string $sex           User's Sex - either M or F
    * @param string $country       User's Country - two letter code
    * @param string $cellnumber    User's Cell number
    * @param string $staffnumber   User's Staff Number
    * @param string $accountType   Type of account - either useradmin or ldap
    * @param string $accountstatus Whether Active or Inactive - 1 or 0
    *
    * @return string User's Primary Key Id
    */
    function addUser($userid, $username, $password, $title, $firstname, $surname, $email, $sex, $country, $cellnumber='', $staffnumber='', $accountType='useradmin', $accountstatus='1')
    {
        //echo $accountType;
        $userArray = array(
                'userid' => $userid,
                'username' => $username,
                'firstname' => $firstname,
                'surname' => $surname,
                'title' => $title,
                'emailaddress' => $email,
                'sex' => $sex,
                'country' => $country,
                'cellnumber' => $cellnumber,
                'staffnumber' => $staffnumber,
                'pass' => sha1($password),
                'howcreated' => $accountType,
                'isactive' => $accountstatus
            );
        
        if ($accountType == '') {
            $userArray['howCreated'] = $accountType;
            
            if ($accountType=='ldap') {
                $userArray['pass'] = sha1('--LDAP--'); // System indentifier to use LDAP Password
                $userArray['howCreated'] = 'LDAP'; // Convert to lowercase
            }
        }
        
        return $this->insert($userArray);
    }
    
    /**
    * Method to update a user's details on the system
    * 
    * @param string $id            Primary Key Id
    * @param string $username      Username
    * @param string $password      Unencrypted password - This function will encrypt it
    * @param string $title         Title of the user
    * @param string $firstname     User's Firstname
    * @param string $surname       User's Surname
    * @param string $email         User's Email Address
    * @param string $sex           User's Sex - either M or F
    * @param string $country       User's Country - two letter code
    * @param string $cellnumber    User's Cell number
    * @param string $staffnumber   User's Staff Number
    * @param string $accountType   Type of account - either useradmin or ldap
    * @param string $accountstatus Whether Active or Inactive - 1 or 0
    *
    * @return boolean Result of Update
    *
    */
    function updateUserDetails($id, $username, $firstname, $surname, $title, $email, $sex, $country, $cellnumber='', $staffnumber='', $password='', $accountType='', $accountstatus='')
    {
        //echo $accountType;
        $userArray = array(
                'username' => $username,
                'firstname' => $firstname,
                'surname' => $surname,
                'title' => $title,
                'emailaddress' => $email,
                'sex' => $sex,
                'country' => $country,
                'cellnumber' => $cellnumber,
                'staffnumber' => $staffnumber
            );
        
        if ($accountstatus != '') {
            $userArray['isactive'] = $accountstatus;
        }
        
        if ($password != '') {
            $userArray['pass'] = sha1($password);
        }
        
        if ($accountType != '') {
            $userArray['howCreated'] = $accountType;
            
            if ($accountType=='ldap') {
                $userArray['pass'] = sha1('--LDAP--'); // System indentifier to use LDAP Password
                $userArray['howCreated'] = 'LDAP'; // Convert to lowercase
            }
        }
        
        return $this->update('id', $id, $userArray);
    }
    
    /**
    * Method to get a list of users from the system based on input criteria
    *
    * @param string $letter     Starting Letter in Field
    * @param string $field      Field to get results from
    * @param string $orderby    How to sort results
    * @param boolean $inactive  Whether to include inactive users or not
    *
    * @return array
    */
    function getUsers($letter, $field='firstname', $orderby='', $inactive=TRUE)
    {
        $whereArray = array();
        if ($letter != 'listall') {
            $whereArray[] = $field.' LIKE \''.$letter.'%\'';
        }
        
        if (!$inactive) {
            $whereArray[] = ' isactive=\'1\'';
        }
        
        if (count($whereArray) == 0) {
            $where = '';
        } else {
            $and = '';
            $where = ' WHERE ';
            
            foreach ($whereArray as $clause)
            {
                $where .= $and.' ('.$clause.')';
                $and = ' AND ';
            }
        }
        
        if ($orderby != '') {
            $orderby = ' ORDER BY '.$orderby;
        }
        
        return $this->getAll($where.$orderby);
    }
    
    /**
    * Method to search for users
    *
    * @param string $field    Field to search
    * @param string $value    Search Query Value
    * @param string $orderby  How to order/sort results
    */
    function searchUsers($field, $value, $orderby)
    {
        $sql = ' WHERE '.$field.' LIKE \'%'.$value.'%\' ORDER BY '.$orderby;
        
        return $this->getAll($sql);
    }
    
    /**
    * Method to check whether a username is available or not
    * 
    * @param string $username Username to check for availability
    * @return boolean TRUE if available, else FALSE
    */
    function usernameAvailable($username)
    {
        $recordCount = $this->getRecordCount('WHERE username=\''.$username.'\'');
        // echo $recordCount;
        if ($recordCount == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * Method to check whether a userId is available or not
    * 
    * @param string $userid userId to check for availability
    * @return boolean TRUE if available, else FALSE
    */
    function useridAvailable($userid)
    {
        $recordCount = $this->getRecordCount('WHERE userid=\''.$userid.'\'');
        
        if ($recordCount == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * Method to generate a userid
    * @return string
    */
    function generateUserId()
    {
        $available = FALSE;
        
        $userId = '';
        
        // while ($available != TRUE) 
        // {
            $userId = mt_rand(1000,9999).date('ymd');
            
            // $available = $this->useridAvailable($userId);
        // }
        
        return $userId;
    }
    
    /**
    * Method to get the details of a user by providing the username and password
    * @param string $username Username of the User
    * @param string $email Email Address of the User
    */
    function getUserNeedPassword($username, $email)
    {
        $result = $this->getAll(" WHERE username='$username' AND emailaddress='$email' ");
        
        if (count($result) == 0) {
            return FALSE;
        } else {
            return $result[0];
        }
    }
    
    /**
    * Method to change the password of the user
    * 
    * @param string $id Primary Key Id of the User
    * @param string $password Unencrypted password
    * @return boolean Result of Update
    */
    private function resetPassword($id, $password)
    {
        return $this->update('id', $id, array('pass'=>sha1($password)));
    }
    
    /**
    * Method to process the request for a new password
    *
    * This function:
    *  - Creates a new password
    *  - Updates the database
    *  - Sends the user an email
    *
    */
    function newPasswordRequest($id)
    {
        $user = $this->getUserDetails($id);
        $siteName = $this->objConfig->getSiteName();
        $siteEmail = $this->objConfig->getsiteEmail();
        
        $objPassword = $this->getObject('passwords', 'useradmin');
        $newPassword = $objPassword->createPassword();
        
        $this->resetPassword($id, $newPassword);
        
        $message =
'
Dear [[FIRSTNAME]] [[SURNAME]]<br />
<br />
On [[DATE]], you requested a new password for the [[SITENAME]] website. The details are here below:<br />
<br />
Username: [[USERNAME]]<br />
New Password: [[PASSWORD]] <br />
Email Address: [[EMAIL]]<br />
<br />
Sincerely,<br />
[[SITENAME]] Registration System<br />
[[SITEADDRESS]]
<br />
<br />
IP Address of Request: '.$_SERVER['REMOTE_ADDR'];

        $message = str_replace('[[FIRSTNAME]]', $user['firstname'], $message);
        $message = str_replace('[[SURNAME]]', $user['surname'], $message);
        $message = str_replace('[[USERNAME]]', $user['username'], $message);
        $message = str_replace('[[EMAIL]]', $user['emailaddress'], $message);
        $message = str_replace('[[SITENAME]]', $siteName, $message);
        $message = str_replace('[[PASSWORD]]', $newPassword, $message);
        $message = str_replace('[[SITEADDRESS]]', $this->objConfig->getsiteRoot(), $message);
        $message = str_replace('[[DATE]]', date('l dS \of F Y h:i:s A'), $message);
        
        $objMailer = $this->getObject('email', 'mail');
        $objMailer->setValue('to', array('newuser@localhost'));
        $objMailer->setValue('from', $siteEmail);
        $objMailer->setValue('fromName', $siteName.' Registration System');
        $objMailer->setValue('subject', 'Password Request: '.$siteName);
        $objMailer->setValue('body', strip_tags($message));
        $objMailer->setValue('AltBody', strip_tags($message));
        
        if ($objMailer->send()) {
           return TRUE;
        } else {
           return FALSE;
        }
    }
} // end of class sqlUsers

?>