<?php


class block_contextfiles extends object
{
   

    public $userFolder;

    /**
    *Initialize by send the table name to be accessed
    */
    public function init()
    {
        $this->objFolder = $this->getObject('dbfolder');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->title = 'Context Files: '.$this->objContext->getTitle();
        $this->loadClass('link', 'htmlelements');
    }
   
    /**
     * Method to render the block
     */
    public function show()
    {
        $link = new link ($this->uri(array('action'=>'contextfiles')));
        $link->link = 'Context Files';
        
        return $link->show();
    }





}
?>