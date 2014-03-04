<?php
/**
* profiles class extends controller
* @package profiles
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Controller class for the profiles module
*
* @author Megan Watson
* @copyright (c) 2007 University of the Western Cape
* @version 0.1
*/

class profiles extends controller
{
    /**
    * Method to construct the class.
    */
    public function init()
    {
        try{
            $this->profileTools = $this->getObject('profileTools', 'profiles');
            $this->dbProfiles = $this->getObject('dbprofiles', 'profiles');
            
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->userId();
            $this->userPkId = $this->objUser->PKId();
            
            $this->objLangCode = $this->getObject('languagecode', 'language');
            $this->objLanguage = $this->getObject('language', 'language');
                   
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
    }

    /**
    * Standard dispatch function
    *
    * @access public
    * @param string $action The action to be performed
    * @return string Template to be displayed
    */
    public function dispatch($action)
    {
        switch($action){
            case 'dosearch':
                $col = $this->getParam('type');
                $val = $this->getParam('user');
                $data = $this->dbProfiles->searchProfiles($col, $val);
                $display = $this->profileTools->listProfiles($data);
                $right = $this->profileTools->getRightSide();
                $this->setVarByRef('display', $display);
                $this->setVarByRef('right', $right);
                return 'home_tpl.php';
                break;
                
            case 'editprofile':
                $data = $this->dbProfiles->getProfile($this->userId);
                $display = $this->profileTools->editProfile($data);
                $right = $this->profileTools->getRightSide();
                $this->setVarByRef('display', $display);
                $this->setVarByRef('right', $right);
                return 'home_tpl.php';
                break;
                
            case 'saveprofile':
                $save = $this->getParam('save');
                if(isset($save) && !empty($save)){
                    $id = $this->getParam('id');
                    $this->dbProfiles->saveProfile($id);
                }
                return $this->nextAction('');
                
            case 'viewprofile':
                $userId = $this->getParam('userid');
                $data = $this->dbProfiles->getProfile($userId);
                $display = $this->profileTools->viewProfile($data);
                $right = $this->profileTools->getViewRightSide($data);
                $this->setVarByRef('display', $display);
                $this->setVarByRef('right', $right);
                return 'home_tpl.php';
                break;

            default:
                $data = $this->dbProfiles->getProfiles();
                $display = $this->profileTools->listProfiles($data);
                $right = $this->profileTools->getRightSide();
                $this->setVarByRef('display', $display);
                $this->setVarByRef('right', $right);
                return 'home_tpl.php';
        }
    }
} // end of controller class
?>