<?php
/* 
 * Responsibl for insterting, updating and deleting schedules table
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die('You cannot view this page directly');
}
class dbregistration extends dbTable{

    public function init()
    {
        parent::init('tbl_fossad_registration');  //super
        $this->table = 'tbl_fossad_registration';
        $this->objUser = $this->getObject ( 'user', 'security' );

    }

 /*public function updateSchedule(
        $title,
        $date,
        $starttime,
        $endtime,
        $id){

        $data = array(
            'title' => $title,
            'meeting_date'=>$date,
            'start_time'=>$starttime,
            'end_time'=>$endtime,
        );
        $scheduleId = $this->update('id',$id, $data);
        }*/

   
    public function addRegistration(

        $firstname,
        $lastname,
        $company,
        $email){
        $data = array(
            'first_name' => $firstname,
            'last_name' => $lastname,
            'registration_date' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            'email'=>$email,
            'company'=>$company,
        );

        if($this->emailExists($email)){
            return FALSE;
        }else{
            $regId = $this->insert($data);
            return $regId;
        }
    }

    public function getRegistrations()
    {
       $sql="select * from ".$this->table." order by first_name";

        $rows=$this->getArray($sql);
        return $rows;
    }

    public function getUserDetails($username)
    {
        $sql="select * from  tbl_users where username= '".$username."'";
        $rows=$this->getArray($sql);

        return $rows;
    }
    public function emailExists($email)
    {
        $sql="select * from " .$this->table." where email = '".$email."'";
        $rows=$this->getArray($sql);
        return count($rows) > 0 ? TRUE:FALSE;
    }
    public function deletemember($id)
    {
        $sql="delete from " .$this->table." where id = '".$id."'";
        $rows=$this->getArray($sql);
        return $rows;
    }

}
?>
