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
        $infoArray['modifierid'] = $this->objUser->userId();
        $infoArray['datecreated'] = strftime('%Y-%m-%d', mktime());
        $infoArray['timecreated'] = strftime('%H:%M:%S', mktime());
        
        return $this->insert($infoArray);
    }
    
    function cleanUpMismatchedMediaFiles()
    {
        $sql = 'SELECT tbl_files_metadata_media.id, tbl_files.id as files_id FROM tbl_files_metadata_media  LEFT JOIN tbl_files ON (tbl_files_metadata_media.fileid = tbl_files.id) WHERE tbl_files.id IS NULL';
        
        $results = $this->getArray($sql);
        
        foreach ($results as $result)
        {
            $this->delete('id', $result['id']);
        }
    }


}

?>