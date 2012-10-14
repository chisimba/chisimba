<?php


class block_userfiles extends object
{

    /**
    *Initialize by send the table name to be accessed
    */
    public function init()
    {
        $this->objFolder = $this->getObject('dbfolder');
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_filemanager_myfiles', 'filemanager', 'My Files');
    }
   
    /**
     * Method to render the block
     */
    public function show()
    {
        $userId = $this->objUser->userId();
        
        if ($userId == NULL || $userId == '') {
            return '';
        } else {
            return $this->objFolder->getTree('users', $this->objUser->userId(), 'dhtml');
        }
    }
    
}
?>