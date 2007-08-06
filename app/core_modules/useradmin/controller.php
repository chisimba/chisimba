<?php
/* -------------------- useradmin class extends controller ----------------*/
                                                                                                                                             
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


class useradmin extends controller
{
    public $objConfig;
    public $objLanguage;
    public $objButtons;
    public $objUserAdmin;
    public $objUser;
    public $isAdmin;
    
    /**
    *
    *
    */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig','config');
        $this->objLanguage = $this->getObject('language','language');
        $this->objButtons=$this->getObject('navbuttons','navigation');
        $this->objUserAdmin=$this->getObject('useradmin_model2','security');
        $this->objUser = $this->getObject('user', 'security');
        
        $this->objFile = $this->getObject('dbfile', 'filemanager');
        $this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        
        $this->objUrl = $this->getObject('url', 'strings');
    }

    /**
    *
    *
    */
    public function dispatch($action) 
    {
        if (!$this->objUser->isAdmin()) {
            return 'noaccess.php';
        }
        
        $this->setLayoutTemplate('useradmin_layout_tpl.php');
        
        // echo '<pre>';
        // print_r($_SESSION);
        // echo '</pre>';
        
        switch ($action)
        {
            case 'viewbyletter':
                return $this->viewByLetter();
            case 'changefield':
                return $this->changeField();
            case 'searchusers':
                return $this->searchUsers($this->getParam('searchfield', 'userid'), $this->getParam('searchquery'), $this->getParam('position', 'startswith'));
            case 'searched':
                return $this->searched();
            case 'adduser':
                return $this->addUser();
            case 'savenewuser':
                return $this->saveNewUser();
            case 'userdetails':
                return $this->userDetails($this->getParam('id'));
            case 'updateuserdetails':
                return $this->updateUserDetails();
            case 'changeimage':
                return $this->changePicture($this->getParam('id'));
            case 'resetimage':
                return $this->resetImage($this->getParam('id'));
            case 'batchprocess':
                return $this->batchProcess();
            default:
                return $this->userAdminHome();
        }
    }
    
    /**
    *
    *
    */
    public function userAdminHome()
    {
        $letter = $this->getSession('letter', 'A');
        $this->setVar('letter', $letter);
        $field = $this->checkField($this->getParam('field', $this->getSession('field')));
        $this->setVar('field', $field);
        
        $inactive = TRUE;
        
        switch ($field)
        {
            case 'firstname' : $orderby = 'firstname, surname'; break;
            case 'surname' : $orderby = 'surname, firstname'; break;
            case 'username' : $orderby = 'username, firstname, surname'; break;
            default : $orderby = 'firstname, surname'; break;
        }
        
        $headerTitle = 'User Admin - Browse by '.$field.' ';
        if ($letter == 'listall') {
            $headerTitle .= '- Listing All Users';
        } else {
            $headerTitle .= '- Letter '.$letter;
        }
        
        $this->setVar('headerTitle', $headerTitle);

        $this->setVar('searchValue', $this->getSession('search'));
        $this->setVar('searchField', $this->getSession('searchField'));
        
        $users = $this->objUserAdmin->getUsers($letter, $field, $orderby, $inactive);
        $this->setVarByRef('users', $users);
        
        $this->setVar('mode', 'useradmin');
        
        return 'useradminhome_tpl.php';
    }
    
    /**
    *
    *
    */
    private function sendVarsToTemplate()
    {
        $letter = $this->getSession('letter', 'A');
        $this->setVar('letter', $letter);
        $field = $this->checkField($this->getParam('field', $this->getSession('field')));
        $this->setVar('field', $field);
    }
    
    /**
    *
    *
    */
    private function viewByLetter()
    {
        $letter = $this->getParam('letter', 'A');
        $field = $this->checkField($this->getParam('field', 'firstname'));
        
        $this->setSession('field', $field);
        $this->setSession('letter', $letter);
        
        return $this->userAdminHome();
    }
    
    /**
    *
    *
    */
    public function changeField()
    {
        $letter = $this->getParam('letter', 'A');
        $field = $this->checkField($this->getParam('field', 'firstname'));
        
        $this->setSession('field', $field);
        $this->setSession('letter', $letter);
        
        return $this->nextAction(NULL);
    }
    
    /**
    *
    *
    */
    private function checkField($field)
    {
        $field = strtolower($field);
        
        $fieldOptions = array('firstname', 'surname', 'username');
        if (!in_array($field, $fieldOptions)) {
            $field = 'firstname';
        }
        
        return $field;
    }
    
    /**
    *
    *
    */
    public function addUser()
    {
        
        $this->setVar('mode', 'add');
        
        return 'adduser_tpl.php';
    }
    
   
    /**
    *
    *
    */
    function saveNewUser()
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
        $country = $this->getParam('country');
        
        $checkFields = array($userId, $username, $firstname, $surname, $email);
        
        $userIdUsernameOk = TRUE;
        
        $problems = array();
        
        if ($this->objUserAdmin->userNameAvailable($username) == FALSE) {
            $problems[] = 'usernametaken';
        }
        
        if ($password != $repeatpassword) {
            $problems[] = 'passwordsdontmatch';
        }
        
        
        
        if (!$this->checkFields($checkFields)) {
            $problems[] = 'missingfields';
        }
        
        
        if (!$this->objUrl->isValidFormedEmailAddress($email)) {
            $problems[] = 'emailnotvalid';
        }
        
        // If there are problems, present from to user to fix
        if (count($problems) > 0) {
            $this->setVar('mode', 'addfixup');
            $this->setVarByRef('problems', $problems);
            return 'adduser_tpl.php';
        } else {
            // Else add to database
            $pkid = $this->objUserAdmin->addUser($userId, $username, $password, $title, $firstname, $surname, $email, $sex, $country, $cellnumber, $staffnumber, 'useradmin', $accountstatus);
            
            if ($pkid != FALSE) {
                $fileId = $this->getParam('imageselect');
                if ($fileId != '') {
                    $filepath = $this->objFile->getFullFilePath($fileId);
                
                    if ($filepath != FALSE) {
                        $mimetype = $this->objFile->getFileMimetype($fileId);
                        
                        if (substr($mimetype, 0, 5) == 'image') {
                            $result = $this->createThumbnail($userId, $filepath);
                        }
                    }
                }
            }
            
            
            return $this->nextAction('userdetails', array('id'=>$pkid, 'message'=>'newusercreated'));
        }
        
    }
    
    protected function explainProblemsInfo($problem)
    {
        switch ($problem)
        {
            case 'usernametaken': return 'The username you have chosen has been taken already.';
            case 'passwordsdontmatch': return 'The passwords you have entered does not match.';
            case 'missingfields': return 'Some of the required fields are missing.';
            case 'emailnotvalid': return 'The email address you enter is not a valid format.';
        }
    }
    
    /**
    *
    *
    */
    private function userDetails($id)
    {
        $user = $this->isValidUser($id, 'userviewdoesnotexist');
        
        $this->setVarByRef('user', $user);
        $this->setVar('mode', 'edit');
        
        
        $confirmation = $this->getSession('showconfirmation', FALSE);
        $this->setVar('showconfirmation', $confirmation);
        
        $this->setSession('showconfirmation', FALSE);
        
        return 'userdetails_tpl.php';
        
    }
    
    private function isValidUser($id, $errorcode='userviewdoesnotexist')
    {
        if ($id == '') {
            return $this->nextAction(NULL, array('error'=>'noidgiven'));
        }
        
        $user = $this->objUserAdmin->getUserDetails($id);
        
        if ($user == FALSE) {
            return $this->nextAction(NULL, array('error'=>$errorcode));
        } else {
            return $user;
        }
    }
    
    /**
    *
    *
    */
    private function updateUserDetails()
    {
        
        $id = $this->getParam('id');
        $user = $this->isValidUser($id, 'userdetailsupdate');
        $this->setVarByRef('user', $user);
        
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
        $country = $this->getParam('country');
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
            
        $this->setSession('userDetails', $userDetails);
        
        // List Compulsory Fields, Cannot be Null
        $checkFields = array($firstname, $surname, $email);
        
        $results = array('id'=>$id);
        
        // Check Fields
        if (!$this->checkFields($checkFields)) {
            $this->setVar('mode', 'addfixup');
            $this->setVar('problem', 'missingfields');
            $this->setSession('showconfirmation', FALSE);
            return 'userdetails_tpl.php';
        }
        
        // Check Email Address
        if (!$this->objUrl->isValidFormedEmailAddress($email) && $email != $this->user['emailaddress']) {
            $this->setVar('mode', 'addfixup');
            $this->setVar('problem', 'notvalidemail');
            $this->setSession('showconfirmation', FALSE);
            return 'userdetails_tpl.php';
        }
        
        if ($username != $user['username']) {
            $available = $this->objUserAdmin->usernameAvailable($username);
            
            if ($available == FALSE) {
                $this->setVar('mode', 'addfixup');
                $this->setVar('problem', 'usernametaken');
                $this->setSession('showconfirmation', FALSE);
                return 'userdetails_tpl.php';
            }
        }
        
        $results['detailschanged']=TRUE;
        
        // If account is switched from LDAP to useradmin, password is compulsory
        if ($user['howcreated'] == 'LDAP' && $accountype = 'useradmin') {
            if (($password == '') || ($repeatpassword=='')) {
                $this->setVar('mode', 'addfixup');
                $this->setVar('problem', 'nopasswordforldap');
                $this->setSession('showconfirmation', FALSE);
                return 'userdetails_tpl.php';
            } else if ($password != $repeatpassword) {
                $this->setVar('mode', 'addfixup');
                $this->setVar('problem', 'ldappasswordnotmatching');
                $this->setSession('showconfirmation', FALSE);
                return 'userdetails_tpl.php';
            }
        }
        
        // check for password changed
        if ($password == '') { // none given, user does not want to change password
            $password = '';
            $results['passwordchanged'] = FALSE;
        } else if ($password != $repeatpassword) { // do not match, user tried to change, but didn't match
            $password = '';
            $results['passwordchanged'] = FALSE;
            $results['passworderror'] = 'passworddonotmatch';
        } else { // OK - user tried, and passwords match
            $results['passwordchanged'] = TRUE;
        }
        
        
        
        // Process Update
        $update = $this->objUserAdmin->updateUserDetails($id, $username, $firstname, $surname, $title, $email, $sex, $country, $cellnumber, $staffnumber, $password, $accounttype, $accountstatus);
        
        if (count($results) > 0) {
            $results['change'] = 'details';
        }
        
        $this->setSession('showconfirmation', TRUE);
        
        $this->objUser->updateUserSession();
        // Process Update Results
        if ($update) {
            return $this->nextAction('userdetails', $results);
        } else {
            return $this->nextAction('userdetails', array('id'=>$id, 'change'=>'details', 'error'=>'detailscouldnotbeupdated'));
        }
        
    }
    
    /**
    *
    *
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
    *
    *
    */
    function resetImage($id)
    {
        $user = $this->isValidUser($id, 'resetimage');
        
        if ($user == FALSE) {
            return $this->nextAction(NULL, array('error'=>'userviewdoesnotexist'));
        }
        
        $this->objUserAdmin->removeUserImage($user['userid']);
        $this->setSession('showconfirmation', TRUE);
        return $this->nextAction('userdetails', array('id'=>$user['id'], 'message'=>'userimagereset', 'change'=>'image'));
    }
    
    /**
    *
    *
    */
    private function changePicture($id)
    {
        $user = $this->isValidUser($id, 'changepicture');
        
        $fileId = $this->getParam('imageselect');
        
        if (isset($_POST['resetimage'])) {
            return $this->resetImage();
        }
        
        if ($fileId == '') {
            return $this->nextAction(NULL, array('change'=>'image', 'message'=>'nopicturegiven'));
        }
        
        $filepath = $this->objFile->getFullFilePath($fileId);
        
        if ($filepath == FALSE) {
            return $this->nextAction(NULL, array('change'=>'image', 'message'=>'imagedoesnotexist'));
        }
        
        $mimetype = $this->objFile->getFileMimetype($fileId);
        
        if (substr($mimetype, 0, 5) != 'image') {
            return $this->nextAction(NULL, array('change'=>'image', 'message'=>'fileisnotimage'));
        }
        
        $result = $this->createThumbnail($user['userid'], $filepath);
        
        $this->setSession('showconfirmation', TRUE);
        return $this->nextAction('userdetails', array('id'=>$id, 'change'=>'image', 'message'=>'imagechanged'));
    }
    
    private function createThumbnail($userId, $filepath)
    {
        $objImageResize = $this->getObject('imageresize', 'files');
        $objImageResize->setImg($filepath);
        
        //Resize to 100x100 Maintaining Aspect Ratio
        $objImageResize->resize(100, 100, TRUE);
        $storePath = 'user_images/'.$userId.'.jpg';
        $this->objCleanUrl->cleanUpUrl($storePath);
        $result = $objImageResize->store($storePath);
        
        //Resize to 100x100 Maintaining Aspect Ratio
        $objImageResize->resize(35, 35, TRUE);
        $storePath = 'user_images/'.$userId.'_small.jpg';
        $this->objCleanUrl->cleanUpUrl($storePath);
        
        return $objImageResize->store($storePath);
    }
    
    /**
    *
    *
    */
    function searchUsers($searchField='userid', $searchValue='', $position='startswith')
    {
        $this->sendVarsToTemplate();
        
        $this->setSession('search', $searchValue);
        $this->setSession('searchField', $searchField);
        $this->setSession('position', $position);
        
        switch ($searchField)
        {
            case 'userid': $orderBy = 'userid, firstname, surname'; break;
            case 'username': $orderBy = 'username, firstname, surname'; break;
            case 'firstname': $orderBy = 'firstname, surname'; break;
            case 'surname': $orderBy = 'surname, firstname'; break;
            default: $orderBy = 'firstname';
        }
        
        $users = $this->objUserAdmin->searchUsers($searchField, $searchValue, $position, $orderBy);
        
        $headerTitle = 'User Admin - Search Results for: "'.$searchValue.'"';
        
        $this->setVarByRef('users', $users);
        $this->setVar('headerTitle', $headerTitle);

        $this->setVar('searchValue', $this->getSession('search'));
        $this->setVar('searchField', $this->getSession('searchField'));
        
        $this->setVar('mode', 'search');
        
        return 'useradminhome_tpl.php';
    }
    
    function searched()
    {
        $searchValue = $this->getSession('search');
        $searchField = $this->getSession('searchField', 'userid');
        $position = $this->getSession('position', 'userid');
        
        return $this->searchUsers($searchField, $searchValue, $position);
    }
    
    
    function batchProcess()
    {
        if ($this->getParam('mode') == 'search') {
            $nextAction = 'searched';
        } else {
            $nextAction = NULL;
        }
        
        if (!$_POST) {
            return $this->nextAction($nextAction);
        }
        
        if ($this->getParam('users') == '') {
            return $this->nextAction($nextAction, array('message'=>'nousersselected'));
        }
        
        if ($this->getParam('option') == '-') {
            return $this->nextAction($nextAction, array('message'=>'nooptionselected'));
        }
        
        $this->objUserAdmin->batchProcessOption($this->getParam('users'), $this->getParam('option'));
        
        return $this->nextAction($nextAction, array('message'=>'batchprocessed', 'option'=>$this->getParam('option')));
    }

}

?>