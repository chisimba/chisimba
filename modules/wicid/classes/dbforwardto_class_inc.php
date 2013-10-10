<?php
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
class dbforwardto extends dbtable {
    var $tablename = "tbl_wicid_forward";
    var $userid;

    public function init() {
        parent::init($this->tablename);

    }

    public function forwardTo($link, $email,$docid) {
        $this->objUser=$this->getObject('user','security');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objUploadTable = $this->getObject('dbfileuploads');
        $this->userutils=$this->getObject('userutils');
        $data=array(

                'link'=>$link,
                'email'=>$email,
                'docid'=>$docid
        );

        $id=$this->insert($data);
        echo 'success';
        return $id;
    }


    public function  getUsers($filter) {
        $sql="select userid,firstname,surname,emailaddress from tbl_users where
            firstName like '%".$filter."%' or surname like '%".$filter."%' or emailaddress like'%".$filter."%'";
        $rows=$this->getArray($sql);

        $users=array();
        foreach ($rows as $row) {
            $users[]=array(
                    'userid'=>$row['userid'],
                    'firstname'=> $row['firstname'],
                    'surname'=>$row['surname'],
                    'emailaddress'=>$row['emailaddress']
            );
        }
        echo json_encode(array("users"=>$users));
        return $users;
    }




}

?>
