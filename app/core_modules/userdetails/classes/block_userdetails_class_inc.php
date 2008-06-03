<?php


class block_userdetails extends object
{
    /**
    *Initialize by send the table name to be accessed
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_userdetails_name', 'userdetails', 'My Profile');
        $this->loadClass('link', 'htmlelements');
        
        $this->objUser = $this->getObject('user', 'security');
        $this->objUserAdmin = $this->getObject('useradmin_model2','security');
    }
   
    /**
     * Method to render the block
     */
    public function show()
    {
        $user = $this->objUserAdmin->getUserDetails($this->objUser->PKId($this->objUser->userId()));
        
        $objBizCard = $this->getObject('userbizcard', 'useradmin');
        $objBizCard->setUserArray($user);
        $objBizCard->showResetImage = FALSE;
        $objBizCard->resetModule = 'userdetails';
        
        $link = new link($this->uri(NULL, 'userdetails'));
        $link->link = $this->objLanguage->languageText('mod_userdetails_updateyourprofile', 'userdetails', 'Update Your Profile');
        
        return $objBizCard->show().'<p>'.$link->show().'</p>';
    }
}
?>