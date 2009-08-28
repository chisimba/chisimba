<?php
class groupadmin_installscripts extends dbTable {
    public function init() {
        parent::init ( 'tbl_users' );
        $this->objGroupOps = $this->getObject('groupops', 'groupadmin');
        $this->objGroupModel = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUserModel = $this->getObject('useradmin_model2', 'security');
    }

    public function preinstall($version = NULL) {
        switch ($version) {
            default :
                $pusers = $this->objGroupOps->getAllUsers();
                $cusers = $this->getAll();
                $perms = $this->objGroupOps->getAllPermUsers();
                $auths=array();
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
                        // set the password back
                        $this->query("UPDATE tbl_users SET pass='".$user['pass']."' WHERE id='$id'");

                        // re-create the users groups
                        $sql="SELECT group_id from tbl_groupadmin_groupuser where user_id='".$user['id']."'";
                        $oldgroups = $this->getArray($sql);
                        foreach($oldgroups as $og) {
                            // get the group name of the id we have from the old group
                            $ogrpid = $og['group_id'];
                            // parent::init('tbl_groupadmin_group');
                            $oldname = $this->getAll("SELECT name from tbl_groupadmin_group WHERE id = '$ogrpid'");
                            // check if the name exists in the new groups, else add it
                            $ngrpid = $this->objGroupModel->getId($oldname)
                            if($ngrpid == NULL) {
                                // create the group
                                $ngrpid = $this->objGroupModel->addGroup( $oldname, NULL, null );
                            }  
                            
                            // now we add the user to the group
                            $this->objGroupModel->addGroupUser( $ngrpid, $id );
                        }
                       /* $newdata=$this->objGroupOps->getUserByUserid($user['userid']);
                        // jsc says: The following loop doesn't seem to work right - still trying to figure out why.
                        foreach ($oldgroups as $line){
                            $this->objLuAdmin->perm->addUserToGroup(array('perm_user_id' => $newdata['perm_user_id'], 'group_id' => $line['group_id']));
                        }
                        $this->query("UPDATE tbl_groupadmin_groupuser set user_id='$id' where user_id='".$user['id']."'"); */
                    }
                }

                break;
        }

        return 'preinstall done';
    }

    public function postinstall() {
        return 'postinstall done';
    }
}
?>
