<?php
/* -------------------- IFAUTH INTERFACE CLASS ----------------*/

/**
* 
* Abstract class containing methods that can be used in an authentication
* plugin class.
* 
* An authentication plugin must extend this class, and it must set an array of user
* information in $this->_record() as an array of data containing the following:
* 
* title
* firstname
* surname
* username
* userid
* emailAddress
* logins
* password
* 
*
* @author Derek Keats
* @category Chisimba
* @package security
* @copyright AVOIR
* @licence GNU/GPL
*
*/
abstract class abauth extends dbtable
{
    /**
    * 
    * Property to hold the record of data for a user
    * as an array.
    *  
    */
    public $_record;
    public $userLoginHistory;
    public $loggedInUsers;
    
    public function init($dataTable) 
    {
        parent::init($dataTable);
        $this->userLoginHistory = $this->getObject('userLoginHistory', 'security');
        $this->loggedInUsers = $this->getObject('loggedInUsers', 'security');
    }
    
    /**
    * 
    * This method initiates a session if one does not exist.
    * Normally it will have been set the first time the site is 
    * open, but this is a sanity check
    * 
    */
    public function initiateSession()
    {
        if (!isset($_REQUEST[session_name()])) {
            $this->objEngine->sessionStart();
        }
        else {
            // php version must be >=4.3.3 for this to work
            session_regenerate_id();
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
    * Method to store user information in a session
    * User Information is stored in a session to prevent unnecessary database calls
    */
    private function storeUserSession()
    {
        $this->setSession('isLoggedIn',TRUE);
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
    * Return the numeric identifier of the user who
    * is currently logged in
    * This function has been simplified down now that it calls getSession
    */
    public function userId()
    {
        return $this->getSession('userid');
    }
    
}
?>
