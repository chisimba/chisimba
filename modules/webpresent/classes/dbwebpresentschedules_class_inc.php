<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end of security

/**
* 
* This class interacts with the database to store the details of the  schedule
*/
class dbwebpresentschedules extends dbtable
{

    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_webpresent_schedules');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
    }

    /**
    * Method to get the details of a file
    * @param string $id Record ID of the file
    * @return array Details of the file
    */
    public function getFile($fileId)
    {
        return $this->getRow('fileid', $fileId);
    }


    public function getSchedules()
    {
         $sql="select fl.title,fl.filename,fl.creatorid,fl.id,sch.fileid,sch.schedule_date,sch.status".
              " from tbl_webpresent_files fl,tbl_webpresent_schedules sch".
              " where fl.id=sch.fileid and sch.status = 'pre' or sch.status = 'on' order by sch.schedule_date";
         return $this->getArray($sql);
       // return $this->getAll(" where status = 'pre' or status = 'on'");
    }


    /**
     *method to insert a schedule
     */
     public function schedulePresentation($fileId, $date, $status)
    {
    
     return $this->insert(array(
                'fileid' => $fileId,
                'schedule_date' => $date,
                'status' => $status
                
            ));
     }

     public function updateSchedule($id, $date, $status)
    {
    
   //     $sql = "UPDATE tbl_webpresent_schedules set schedule_date = ".$date." and status = '".$status."' where id = '".$id."'";

 //       $this->_execute($sql, '');

     //   return $this->getArray($sql);
    return $this->update('id', $id, array(
                'schedule_date' => $date,
                'title' => $status
            ));
   
  }
}