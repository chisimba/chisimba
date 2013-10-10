<?php

class userextrautils extends object {

    function init() {
        $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUser = $this->getObject('user', 'security');
        $this->dbextra = $this->getObject('dbuserextra');
        $this->objOps = $this->getObject('groupops', 'groupadmin');
    }

    function importStudents($pathtofile, $coursecode) {
        $handle = @fopen($pathtofile, "r");
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle, 4096);
                $info = array();
                $username = "";
                $student = explode(",", $buffer);
                if (count($student) > 2) {
                    $username = $student[0];
                    $info ['surname'] = $student[1];
                    $info ['firstname'] = $student[2];
                    //echo $info['surname'].' '.$info['firstname'].'<br/>';
                }

                $info ['email'] = $username . '@students.wits.ac.za';
                $userid = $this->objUser->getUserId($username);
                if (!$userid) {
                    $userid = mt_rand(1000, 9999) . date('ymd');
                    $info ['userid'] = $userid;
                    $this->createUser($username, $info);
                }

                $this->addStudentToCourse($userid, $coursecode);
            }
            
            return "success";
            fclose($handle);
        } else {
            echo "Import Failed";
        }
    }

    /**
     *
     *
     */
    function createUser($username, $info) {
        if (!$username) {
            return;
        }
        if ($username == '') {
            return;
        }
        // Build up an array of the user's info

        $info ['staffnumber'] = $username;
        $info ['username'] = $username;
        $info ['userId'] = $info ['userid'];
        $info ['sex'] = '';
        $info ['cellnumber'] = '';
        $info ['accessLevel'] = 'guests';
        $info ['howCreated'] = 'LDAP';
        $info ['isactive'] = '1';
        $info ['title'] = '';
        $objConf2 = $this->getObject('altconfig', 'config');
        $info ['country'] = $objConf2->getCountry();
        $objUserAdmin = $this->getObject('useradmin_model2', 'security');
        $objUserAdmin->addUser($info['userid'], $info['username'], '--LDAP--', $info['title'], $info['firstname'], $info['surname'], $info['email'], $info['sex'], $info['country'], $info['cellnumber'], $info['staffnumber'], 'ldap', '1');
    }

    function addStudentToCourse($userid, $unitCode) {
        $contextGroupId = $this->objGroups->getId($unitCode);
        $subGroups = $this->objGroups->getSubgroups($pscontextGroupId);
        $studentGroupId = '';

        if (is_array($subGroups)) {

            foreach ($subGroups[0] as $subGroup) {

                $groupName = $this->objOps->formatGroupName($subGroup['group_define_name']);
                switch ($groupName) {
                    case 'Students':
                        $studentGroupId = $this->objGroups->getId($subGroup['group_define_name']);
                        $puid = $this->dbextra->getUserPuid($userid);
                        $this->objGroups->addGroupUser($studentGroupId, $puid);
                        $puid = $this->dbextra->getUserPuid($userid);
                        break;
                }
            }
        }
    }

}
?>
