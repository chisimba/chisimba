<?php
/* -------------------- useradmin class extends controller ----------------*/
                                                                                                                                             
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


class userregistration extends controller
{
    /**
    * @var object $objConfig Config Object
    */
    public $objConfig;
    
    /**
    * @var object $objLanguage Language Object
    */
    public $objLanguage;
    
    /**
    * @var object $objUserAdmin User Administration \ Object
    */
    public $objUserAdmin;
    
    /**
    * @var object $objUser User Object Object
    */
    public $objUser;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig','config');
        $this->objLanguage = $this->getObject('language','language');
        $this->objUserAdmin = $this->getObject('useradmin_model2','security');
        $this->objUser = $this->getObject('user', 'security');
        
        $this->objUrl = $this->getObject('url', 'strings');
    }

    /**
    * Method to turn off login requirement for all actions in this module
    */
    public function requiresLogin()
    {
        return FALSE;
    }

    /**
    * Dispatch Method
    * @param string $action Action to be taken
    */
    public function dispatch($action) 
    {
        
        // If user is logged in, they should not have access to this page
        // Also destroy data
        // if ($this->objUser->isLoggedIn()) {
            // $this->setSession('id', '');
            // $this->setSession('password', '');
            // $this->setSession('time', '');
            
            // return $this->nextAction(NULL, NULL, '_default');
        // }
        
        $this->setLayoutTemplate('login_layout_tpl.php');
        switch ($action)
        {
            case 'showregister':
            default:
                return $this->registrationHome();
            case 'confirm':
                $id = $this->getParam('newId');
                if(!empty($id)){
                    $this->setSession('id', $id);
                    return $this->nextAction('detailssent');
                }
                return $this->nextAction('');
            case 'register':
                return $this->saveNewUser();
            case 'detailssent':
                return $this->detailsSent();
            case 'invitefriend':
            	$this->setLayoutTemplate(NULL);
            	return 'invite_tpl.php';
            	break;
        }
    }
    
    /**
    * Method to show the registration page
    */
    protected function registrationHome()
    {
    	$userstring = $this->getParam('user');
    	$this->setVar('userstring', $userstring);
        $this->setVar('mode', 'add');
        return 'registrationhome_tpl.php';
    }
    
    /**
    * Method to add a new user
    */
    protected function saveNewUser()
    {
        if (!$_POST) { // Check that user has submitted a page
            return $this->nextAction(NULL);
        }
        
        // Generate User Id
        $userId = $this->objUserAdmin->generateUserId();
        
        // Capture all Submitted Fields
        $captcha = $this->getParam('request_captcha');
        $username = $this->getParam('register_username');
        $password = $this->getParam('register_password');
        $repeatpassword = $this->getParam('register_confirmpassword');
        $title = $this->getParam('register_title');
        $firstname = $this->getParam('register_firstname');
        $surname = $this->getParam('register_surname');
        $email = $this->getParam('register_email');
        $repeatemail = $this->getParam('register_confirmemail');
        $sex = $this->getParam('register_sex');
        $cellnumber = $this->getParam('register_cellnum');
        $staffnumber = $this->getParam('register_staffnum');
        $country = $this->getParam('country');
        
        $accountstatus = 1; // Default Status Active
        
        // Create an array of fields that cannot be empty
        $checkFields = array($captcha, $username, $firstname, $surname, $email, $repeatemail, $password, $repeatpassword);
        
        // Create an Array to store problems
        $problems = array();
        
        // Check that username is available
        if ($this->objUserAdmin->userNameAvailable($username) == FALSE) {
            $problems[] = 'usernametaken';
        }
        //check that the email address is unique
        if($this->objUserAdmin->emailAvailable($email) == FALSE) {
        	$problems[] = 'emailtaken';
        }
        
        
        // Check for any problems with password
        if ($password == '') {
            $problems[] = 'nopasswordentered';
        } else if ($repeatpassword == '') {
            $problems[] = 'norepeatpasswordentered';
        } else if ($password != $repeatpassword) {
            $problems[] = 'passwordsdontmatch';
        }
        
        
        // Check that all required field are not empty
        if (!$this->checkFields($checkFields)) {
            $problems[] = 'missingfields';
        }
        
        // Check that email address is valid
        if (!$this->objUrl->isValidFormedEmailAddress($email)) {
            $problems[] = 'emailnotvalid';
        }
        
        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha')) {
            $problems[] = 'captchadoesntmatch';
        }
        
        // If there are problems, present from to user to fix
        if (count($problems) > 0) {
            $this->setVar('mode', 'addfixup');
            $this->setVarByRef('problems', $problems);
            return 'registrationhome_tpl.php';
        } else {
            // Else add to database
            $pkid = $this->objUserAdmin->addUser($userId, $username, $password, $title, $firstname, $surname, $email, $sex, $country, $cellnumber, $staffnumber, 'useradmin', $accountstatus);

            // Email Details to User
            $this->objUserAdmin->sendRegistrationMessage($pkid, $password);
            
            $this->setSession('id', $pkid);
            //$this->setSession('password', $password);
            $this->setSession('time', $password);
            
            return $this->nextAction('detailssent');
        }
    }
    
    /**
    * Method to display the error messages/problems in the user registration
    * @param string $problem Problem Code
    * @return string Explanation of Problem
    */
    protected function explainProblemsInfo($problem)
    {
        switch ($problem)
        {
            case 'usernametaken': return 'The username you have chosen has been taken already.';
            case 'emailtaken': return 'The supplied email address has been taken already.';
            case 'passwordsdontmatch': return 'The passwords you have entered do not match.';
            //case 'missingfields': return 'Some of the required fields are missing.';
            case 'emailnotvalid': return 'The email address you enter is not a valid format.';
            case 'captchadoesntmatch': return 'The image code you entered was incorrect';
            case 'nopasswordentered': return 'No password was entered';
            case 'norepeatpasswordentered': return 'No Repeat password was entered';
        }
    }
    
    /**
    * Method to check that all required fields are entered
    * @param array $checkFields List of fields to check
    * @return boolean Whether all fields are entered or not
    */
    private function checkFields($checkFields)
    {
        $allFieldsOk = TRUE;
        $this->messages = array();
        
        foreach ($checkFields as $field)
        {
            if ($field == '') {
                $allFieldsOk = FALSE;
            }
        }
        
        return $allFieldsOk;
    }
    
    /**
    * Method to inform the user that their registration has been successful
    */
    protected function detailsSent()
    {
        $user = $this->objUserAdmin->getUserDetails($this->getSession('id'));
        
        if ($user == FALSE) {
            return $this->nextAction(NULL, NULL, '_default');
        } else {
            $this->setVarByRef('user', $user);
        }
        
        return 'confirm_tpl.php';
    }
    
}

?>