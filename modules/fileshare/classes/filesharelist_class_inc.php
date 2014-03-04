<?php
/**
* This class retrieves a list of files of a workgroup (fileshare)
* @author Tohir Solomons
* @copyright 2004,2005
* @license GNU GPL
*/

class filesharelist extends dbtable
{
    
    /**
    * Constructor
    */
    function init()
    {
        parent::init('tbl_fileshare');
        $this->objLanguage = $this->getObject('language','language');
    }
    
    /**
    * Method to get the list of files for a workgroup
    * @param string $workgroup Workgroup ID
    */
    function getWorkgroupFiles($workgroupId)
    {
        return $this->getAll('WHERE workgroupId="'.$workgroupId.'"');
    }
    
    /**
    * Method to get the list of files for a workgroup in a formatted fashion
    * This puts control of the format in this class, without the need for others to do anything
    * At the moment, it only shows an unordered list
    * @param string $workgroup Record Id of the Workgroup
    */
    function getFormattedDisplay($workgroupId)
    {
        $filesList = $this->getWorkgroupFiles($workgroupId);        
        if (count($filesList) == 0) {
            return '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_fileshare_norecords').'</div>';
        } else {
            $this->loadClass('link', 'htmlelements');
            $return = '<ul>';
            foreach ($filesList as $file)
            {
                $downloadLink = new link ($this->uri(array('action'=>'filedownload', 'fileId'=>$file['fileId'])));
                $downloadLink->link = $file['filename'].'&nbsp;'.$file['title'].'&nbsp;'.date("F j, Y, g:i a", $file['uploadtime']);
                $return .= '<li>'.$downloadLink->show().'</li>';
            }
            $return .= '</ul>';          
            return $return;
        }
    }
}
?>