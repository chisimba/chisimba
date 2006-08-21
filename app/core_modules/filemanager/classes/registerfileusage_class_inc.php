<?php
/**

*/ 
class registerfileusage extends object
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
    * Method to generate a random color
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
    
    public function deregisterUse($module, $table, $recordId)
    {
        //$list = $this->getAll(' WHERE 
    }

}

?>