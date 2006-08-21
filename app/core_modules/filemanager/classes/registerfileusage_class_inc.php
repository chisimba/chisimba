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
    * Method to Register Use of a File in a Column of a Table
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
    * Method to Parse through a text, find the files that are being used, and register them
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
        // ToDo
    }
    
    /**
    * Method to deregister File Usage
    * @param string $module Name of the Module using file
    * @param string $table Name of the Table holding the file
    * @param string $recordId Record Id of the Record in the table
    */
    public function deregisterUse($module, $table, $recordId)
    {
        $list = $this->getAll(' WHERE module="'.$module.'" AND tablename="'.$table.'" AND recordid="'.$recordId.'"');
        foreach ($list as $item)
        {
            $this->delete('id', $item['id']);
        }
        
        return;
    }

}

?>