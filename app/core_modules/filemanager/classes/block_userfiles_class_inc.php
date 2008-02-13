<?php


class block_userfiles extends object
{
   

    public $userFolder;

    /**
    *Initialize by send the table name to be accessed
    */
    public function init()
    {
        $this->objFolder = $this->getObject('dbfolder');
        $this->title = 'User Files';
    }
   
    /**
     * Method to render the block
     */
    public function show()
    {
        return $this->objFolder->showUserFolders($this->userFolder);
    }





}
?>