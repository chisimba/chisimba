<?php
class db extends dbtable
{
    public function init() {
        parent::init("tbl_playerinfo");
    }
		
    public function addInfo($firstname,$lastname,$age,$pos,$fee,$other,$status) {
        $data=array("firstname"=>$firstname,"lastname"=>$lastname,"age"=>$age,"position"=>$pos,"transferfee"=>$fee,"otherinfo"=>$other,"status"=>$status);
        $this->insert($data);
    }

    public function getInfo() {
        $data=$this->getAll();
        return $data;
    }
    
    public function search($firstname,$lastname) {

        return $this->getArray("Select * FROM tbl_playerinfo Where firstname LIKE '$firstname%' AND lastname LIKE '$lastname%'");
    }
		
    public function updateInfo($academicName,$schoolName,$headSign,$telNumber,$emailAdd,$courseId) {
        $data=array("academicname"=>$academicName,"schoolname"=>$schoolName,"headsign"=>$headSign,"telnum"=>$telNumber,
        "emailadd"=>$emailAdd);
        $this->update("courseId",$courseId,$data);
    }
}
?>
