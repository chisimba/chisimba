<?php
/**
* dbvideos class extends object
* @package hivaids
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbvideos class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class dbvideos extends dbtable
{
    /**
    * Constructor method
    */
    public function init()
    {
        parent::init('tbl_hivaids_videos');
        $this->table = 'tbl_hivaids_videos';
        $this->tblFiles = 'tbl_files';
        
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        
        $this->objFileRegister = $this->getObject('registerfileusage', 'filemanager');
    }
    
    /**
    * Method to add a video to the repository
    *
    * @access public
    * @param string $id The row id of the video
    * @return void
    */
    public function addVideo($id = NULL)
    {
        $fileId = $this->getParam('video');
        
        $fields = array();
        $fields['file_id'] = $fileId;
        $fields['file_name'] = $this->getParam('selectfile_video');
        $fields['description'] = $this->getParam('description');
        $fields['updated'] = $this->now();
        
        if(!empty($id)){
            $fields['modifierid'] = $this->userId;
            $this->update('id', $id, $fields);
        }else{
            $fields['creatorid'] = $this->userId;
            $fields['datecreated'] = $this->now();
            $id = $this->insert($fields);
        }
            
        // Register the file usage in the file manager
        $this->objFileRegister->registerUse($fileId, 'hivaids', $this->table, $id, 'file_id', '', '', TRUE);
    }
    
    /**
    * Method to get a list of videos
    *
    * @access public
    * @return array $data The video list
    */
    public function getVideos()
    {
        $sql = "SELECT *, v.description AS descript, v.id AS vid_id FROM {$this->table} AS v, {$this->tblFiles} AS f 
            WHERE v.file_id = f.id";
        $data = $this->getArray($sql);
        
        return $data;
    }
    
    /**
    * Method to get a video
    *
    * @access public
    * @return array $data The video
    */
    public function getVideo($id)
    {
        $sql = "SELECT * FROM {$this->table} 
            WHERE id = '{$id}'";
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0];
        }
        return array();
    }
    
    /**
    * Method to delete a video
    *
    * @access public
    * @param string $id The row id of the video
    * @return void
    */
    public function deleteVideo($id)
    {
        $this->objFileRegister->deregisterUse('hivaids', $this->table, $id, 'file_id');
        $this->delete('id', $id);
    }
}
?>