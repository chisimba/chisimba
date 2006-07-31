<?
/**
* Class to handle interaction with table tbl_files_metadata_scripts
* This table relates to metadata about scripts
*
* @author Tohir Solomons
*/
class dbmetadatascripts extends dbTable
{
    
    /**
    * Constructor
    */
    function init()
    {
        parent::init('tbl_files_metadata_scripts');
        $this->objUser =& $this->getObject('user', 'security');
    }
    
    /**
    * Method to add a file
    * @param string $fileId Record Id of the File
    * @param string $script Geshi-highlighted Script
    * @return string Record Id of the entry
    */
    function addScriptHighlight($fileId, $script)
    {
        // Add File Id to Array
        $infoArray['fileid'] = $fileId;
        $infoArray['geshihighlight'] = $script;
        $infoArray['creatorid'] = $this->objUser->userId();
        $infoArray['datecreated'] = strftime('%Y-%m-%d', mktime());
        $infoArray['timecreated'] = strftime('%H:%M:%S', mktime());
        
        return $this->insert($infoArray);
    }

    
    
    

}

?>