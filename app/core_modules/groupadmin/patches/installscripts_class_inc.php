<?php
class groupadmin_installscripts extends dbTable {
    public function init() {
        parent::init ( 'tbl_users' );
        $this->objGroupOps = $this->getObject('groupops', 'groupadmin');
        $this->objUserModel = $this->getObject('useradmin_model2', 'security');
    }

    public function preinstall($version = NULL) {
        switch ($version) {
        case '2.005':
                $pusers = $this->objGroupOps->getAllUsers();
                $cusers = $this->getAll();
                $perms = $this->objGroupOps->getAllPermUsers();
                foreach ($perms as $p) {
                    $auths[] = $p['auth_user_id'];
                }
                foreach($cusers as $user) {
                    if(in_array($user['userid'], $auths)) {
                        continue;
                    }
                    else {
                        // delete the user from the old system
                        $this->delete('id', $user['id'], 'tbl_users');
                        // now add him back with a perms id
                        $id = $this->objUserModel->addUser($user['userid'], $user['username'], $user['pass'], $user['title'], $user['firstname'],
                                                 $user['surname'], $user['emailaddress'], $user['sex'], $user['country'],
                                                 $user['cellnumber'], $user['staffnumber'], $user['howcreated'], $user['isactive']
                                                 );
                    }
                }

                break;

        default :
            break;
        }

        return 'preinstall done';
    }

    public function postinstall() {
        return 'postinstall done';
    }
}
?>