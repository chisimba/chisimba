<?php
/* -------------------- useradmin class extends controller ----------------*/
                                                                                                                                             
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


class userdetails extends controller
{
    public $objConfig;
    public $objLanguage;
    public $objButtons;
    public $objUserAdmin;
    public $objUser;
    public $isAdmin;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objConfig =& $this->getObject('altconfig','config');
        $this->objLanguage =& $this->getObject('language','language');
        $this->objUserAdmin =& $this->getObject('useradmin_model2','security');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objFile =& $this->getObject('dbfile', 'filemanager');
        $this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        
        $this->objUrl = $this->getObject('url', 'strings');
    }


    public function dispatch($action) 
    {
        $this->setLayoutTemplate('user_layout_tpl.php');
        
        $this->user = $this->objUserAdmin->getUserDetails($this->objUser->PKId($this->objUser->userId()));
        $this->setVarByRef('user', $this->user);
        
        switch ($action)
        {
            case 'changeimage':
                return $this->changePicture();
            case 'resetimage':
                return $this->resetImage($this->getParam('id'));
            case 'updateuserdetails':
                return $this->updateUserDetails();
            default:
                return $this->showUserDetailsForm();
        }
    }
    
    private function showUserDetailsForm()
    {
        $this->setVar('mode', 'edit');
        
        
        $confirmation = $this->getSession('showconfirmation', FALSE);
        $this->setVar('showconfirmation', $confirmation);
        
        $this->setSession('showconfirmation', FALSE);
        
        return 'userdetails_tpl.php';
    }
    
    private function changePicture()
    {
        $fileId = $this->getParam('imageselect');
        
        if (isset($_POST['resetimage'])) {
            return $this->resetImage();
        }
        
        if ($fileId == '') {
            return $this->nextAction(NULL, array('change'=>'image', 'message'=>'nopicturegiven'));
        }
        
        $filepath = $this->objFile->getFullFilePath($fileId);
        
        if ($fileId == FALSE) {
            return $this->nextAction(NULL, array('change'=>'image', 'message'=>'imagedoesnotexist'));
        }
        
        $mimetype = $this->objFile->getFileMimetype($fileId);
        
        if (substr($mimetype, 0, 5) != 'image') {
            return $this->nextAction(NULL, array('change'=>'image', 'message'=>'fileisnotimage'));
        }
        
        $objImageResize = $this->getObject('imageresize', 'files');
        $objImageResize->setImg($filepath);
        
        //Resize to 100x100 Maintaining Aspect Ratio
        $objImageResize->resize(100, 100, TRUE);
        $storePath = 'user_images/'.$this->objUser->userId().'.jpg';
        $this->objCleanUrl->cleanUpUrl($storePath);
        $result = $objImageResize->store($storePath);
        
        //Resize to 100x100 Maintaining Aspect Ratio
        $objImageResize->resize(35, 35, TRUE);
        $storePath = 'user_images/'.$this->objUser->userId().'_small.jpg';
        $this->objCleanUrl->cleanUpUrl($storePath);
        $result = $objImageResize->store($storePath);
        
        $this->setSession('showconfirmation', TRUE);
        return $this->nextAction(NULL, array('change'=>'image', 'message'=>'imagechanged'));
    }
    
    private function updateUserDetails()
    {
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
        
        $results = array();
        
        // Check Fields
        if (!$this->checkFields($checkFields)) {
            $this->setVar('mode', 'addfixup');
            $this->setSession('showconfirmation', FALSE);
            return 'userdetails_tpl.php';
        }
        
        // Check Email Address
        if (!$this->objUrl->isValidFormedEmailAddress($email) && $email != $this->user['emailaddress']) {
            $this->setVar('mode', 'addfixup');
            $this->setSession('showconfirmation', FALSE);
            return 'userdetails_tpl.php';
        }
        
        $results['detailschanged']=TRUE;
        
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
        $update = $this->objUserAdmin->updateUserDetails($this->user['id'], $firstname, $surname, $title, $email, $sex, $country, $cellnumber, $staffnumber, $password);
        
        if (count($results) > 0) {
            $results['change'] = 'details';
        }
        
        $this->setSession('showconfirmation', TRUE);
        
        $this->objUser->updateUserSession();
        // Process Update Results
        if ($update) {
            return $this->nextAction(NULL, $results);
        } else {
            return $this->nextAction(NULL, array('change'=>'details', 'error'=>'detailscouldnotbeupdated'));
        }
        
    }
    
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
    
    
    
    function resetImage()
    {
        $this->objUserAdmin->removeUserImage($this->objUser->userId());
        $this->setSession('showconfirmation', TRUE);
        return $this->nextAction(NULL, array('change'=>'image', 'message'=>'userimagereset', 'change'=>'image'));
    }
}

?>
