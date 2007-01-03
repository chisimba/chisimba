<?php
/**
* Class to Register File Usage as part of file tracking purposes
*
* @author Tohir Solomons
*/ 
class registerfileusage extends dbTable
{

    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files_usage');
        $this->objUser =& $this->getObject('user', 'security');
    }
    
    /**
    * Method to add the file usage information to the database
    *
    * This is a private function that is called by the other functions for registering file usage
    *
    * @access private
    * @param string $fileId Record Id of File to be tracked
    * @param string $module Module the file will be used in
    * @param string $table Name of the Table holding the file
    * @param string $recordId Record Id of Record Using the File
    * @param string $column Column of Record Using the File
    * @param string $context Context File will be used in
    * @param string $workgroup Workgroup the File will be used in
    * @param boolean $fileLock Flag whether to apply a lock on the file - prevents file from being deleted
    * @return string Color in Hexadecimal format
    */
    private function addItem ($fileId, $module, $table, $recordId, $column, $context='', $workgroup='', $fileLock=FALSE)
    {
        $fileLock = $fileLock ? 'Y' : 'N';
        
        $insertId = $this->insert(array(
                'fileid' => $fileId,
                'module' => $module,
                'tablename' => $table,
                'recordid' => $recordId,
                'columnname' => $column,
                'context' => $context,
                'workgroup' => $workgroup,
                'filelock' => $fileLock,
                'creatorid' => $this->objUser->userId(),
                'datecreated' => strftime('%Y-%m-%d', mktime()),
                'timecreated' => strftime('%H:%M:%S', mktime())
        ));
        
        // Done to Avoid Null Values
        if ($context == '') {
            $this->update('id', $insertId, array('context'=>''));
        }
        
        if ($workgroup == '') {
            $this->update('id', $insertId, array('workgroup'=>''));
        }
        
        return $insertId;
    }
    
    /**
    * Method to Register Use of a File in a Column of a Table
    *
    * It first deregisters the usage, and then re-adds them to prevent duplication
    *
    * @param string $fileId Record Id of File to be tracked
    * @param string $module Module the file will be used in
    * @param string $table Name of the Table holding the file
    * @param string $recordId Record Id of Record Using the File
    * @param string $column Column of Record Using the File
    * @param string $context Context File will be used in
    * @param string $workgroup Workgroup the File will be used in
    * @param boolean $fileLock Flag whether to apply a lock on the file - prevents file from being deleted
    * @return string Color in Hexadecimal format
    */
    public function registerUse($fileId, $module, $table, $recordId, $column, $context='', $workgroup='', $fileLock=FALSE)
    {
        $this->deregisterUse($module, $table, $recordId, $column);
        
        $this->addItem ($fileId, $module, $table, $recordId, $column, $context, $workgroup, $fileLock);
    }
    
    /**
    * Method to Parse through a text, find the files that are being used, and register them.
    * This function is better suited to checks of text that may or may not contain links to
    * files in the file manager.
    *
    * @param string $text Record Id of File to be tracked
    * @param string $module Module the file will be used in
    * @param string $table Name of the Table holding the file
    * @param string $recordId Record Id of Record Using the File
    * @param string $column Column of Record Using the File
    * @param string $context Context File will be used in
    * @param string $workgroup Workgroup the File will be used in
    * @param boolean $fileLock Flag whether to apply a lock on the file - prevents file from being deleted
    * @return string Color in Hexadecimal format
    */
    public function parseFilesUse($text, $module, $table, $recordId, $column, $context='', $workgroup='', $fileLock=FALSE)
    {
        preg_match_all('/index\\.php\\?module=filemanager(?:&amp;|&)action=file(?:&amp;|&)id=(?P<fileid>[_|\\w]*)/', $text, $result, PREG_PATTERN_ORDER);
        
        $this->deregisterUse($module, $table, $recordId, $column);
        
        if (count($result['fileid']) > 0) {
            foreach ($result['fileid'] as $fileId)
            {
                $this->addItem ($fileId, $module, $table, $recordId, $column, $context, $workgroup, $fileLock);
            }
        }
        
        return;
    }
    
    /**
    * Method to deregister File Usage
    * @param string $module Name of the Module using file
    * @param string $table Name of the Table holding the file
    * @param string $recordId Record Id of the Record in the table
    * @param string $columnName Name of the Column in which the file is stored
    */
    public function deregisterUse($module, $table, $recordId, $columnName)
    {
        $list = $this->getAll(' WHERE module=\''.$module.'\' AND tablename=\''.$table.'\' AND recordid=\''.$recordId.'\' AND columnname=\''.$columnName.'\'');
        foreach ($list as $item)
        {
            $this->delete('id', $item['id']);
        }
        
        return;
    }

}

?>