<?php

class dbdepartments extends dbtable {

    private $objUser;
    private $objGroupAdminModel;
    private $objGroupOps;

    function init() {
        parent::init("tbl_gift_departments");
        $this->objUser = $this->getObject('user', 'security');
        $this->objGroupOps = $this->getObject('groupops', 'groupadmin');
        $this->objGroupAdminModel = $this->getObject('groupadminmodel', 'groupadmin');
    }

    function addDepartment($name, $path) {
         if($this->exists($name)){
             return FALSE;
         }
        $name = str_replace("'", "\'", $name);
        $data = array(
            "name" => $name,
            "deleted" => 'N',
            "path" => $path,
            "level" => count(explode("/", $path))
        );

        $id = $this->insert($data);
        return $id;
    }

    function getDepartments() {
        $sql =
                "select * from tbl_gift_departments where (deleted='N' or deleted is null) order by level";
        $data = $this->getArray($sql);
        if ($this->objUser->isAdmin()) {
            return $data;
        }

        $departments = array();
        $userId = $this->objUser->userid(); // $this->getUserPuid($this->objUser->userid());

        foreach ($data as $department) {
            $groupId = $this->objGroupAdminModel->getId($department['name']);
            if ($this->objGroupOps->isGroupMember($groupId, $userId)) {
                $departments[] = $department;
            }
        }
        return $departments;
    }

    function getParentPath($id) {
        return $this->getRow("id", $id);
    }

    function getDepartment($id) {
        return $this->getRow("id", $id);
    }

    function getDepartmentsLike($path) {
        $sql =
                "select * from tbl_gift_departments where path like '$path%'";
        return $this->getArray($sql);
    }

    function getDepartmentName($id) {
        $row = $this->getRow("id", $id);
        return $row['name'];
    }

    function getSubDepartmentsCount($id) {
        $dept = $this->getDepartment($id);
        $path = $dept['path'];
        $sql =
                "select count(*) as totalsubs from tbl_gift_departments where path like '$path%' and  (deleted='N' or deleted is null)";

        $data = $this->getArray($sql);
        $total = 0;
        foreach ($data as $row) {
            $total = $row['totalsubs'] . ",";
        }
        return $total;
    }

    function exists($name){
        $sql=
        "select * from tbl_gift_departments where name ='$name'";
        $rows=$this->getArray($sql);
        if(count($rows) > 0){
            return TRUE;
        }
        return FALSE;
    }
    function updateDepartment($id, $name) {
        $data = array(
            "name" => $name
        );
        return $this->update("id", $id, $data);
    }

    public function deleteDepartment($id) {
        $data = array("deleted" => "Y");
        $result = $this->update('id', $id, $data);
        return $result;
    }

}

?>
