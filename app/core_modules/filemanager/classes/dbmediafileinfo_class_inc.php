<?
/**
* Class to handle interaction with table tbl_files_metadata_media
* This table relates to metadata about media files such as images, audio, video and flash
*
* @author Tohir Solomons
*/
class dbmediafileinfo extends dbTable
{
    
    /**
    * Constructor
    */
    function init()
    {
        parent::init('tbl_files_metadata_media');
        $this->objUser =& $this->getObject('user', 'security');
    }
    
    /**
    * Method to add metadata info about a file
    * @param string $fileId Record Id of the File
    * @param array $infoArray Array with details of the metadata
    * @return string Record Id of the Entry
    */
    function addMediaFileInfo($fileId, $infoArray)
    {
        // Add File Id to Array
        $infoArray['fileid'] = $fileId;
        $infoArray['creatorid'] = $this->objUser->userId();
        $infoArray['datecreated'] = strftime('%Y-%m-%d', mktime());
        $infoArray['timecreated'] = strftime('%H:%M:%S', mktime());
        
        return $this->insert($infoArray);
    }


}

?>