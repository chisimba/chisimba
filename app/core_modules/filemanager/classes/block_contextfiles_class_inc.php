<?php


class block_contextfiles extends object
{
    /**
    *Initialize by send the table name to be accessed
    */
    public function init()
    {
        $this->objFolder = $this->getObject('dbfolder');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = ucwords($this->objLanguage->code2Txt('mod_filemanager_contextfiles', 'filemanager', NULL, '[-context-] Files').': '.$this->objContext->getTitle());
        $this->loadClass('link', 'htmlelements');
    }
   
    /**
     * Method to render the block
     */
    public function show()
    {
        $contextCode = $this->objContext->getContextCode();
        
        if ($contextCode == '') {
            return '';
        } else {
            return $this->objFolder->getTree('context', $contextCode, 'dhtml');
        }
        
    }
}
?>