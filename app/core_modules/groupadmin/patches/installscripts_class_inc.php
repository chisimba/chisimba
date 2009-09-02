<?php
class groupadmin_installscripts extends dbTable {
    public function init() {
        parent::init ( 'tbl_users' );
        $this->objGroupOps = $this->getObject('groupops', 'groupadmin');
        $this->objGroupModel = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUserModel = $this->getObject('useradmin_model2', 'security');
    }

    public function preinstall($version = NULL) {
        log_debug("Doing preinstall code");
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
                        $userid = $user['userid'];
                        // set the password back
                        $this->query("UPDATE tbl_users SET pass='".$user['pass']."' WHERE id='$id'");
                        // re-create the top level groups
                        $allogrps = $this->getArray("SELECT name, parent_id from tbl_groupadmin_group WHERE parent_id IS NULL");
                        foreach($allogrps as $ogs) {
                            $ogsname = $ogs['name'];
                            $ongrpid = $this->objGroupModel->getId($ogsname);
                            if( !isset($ongrpid) || $ongrpid == NULL ) {
                                if($ogsname != '' || $ogsname != NULL || !empty($ogsname)) {
                                    // create the group
                                    log_debug("group doesnt exist, creating $ogsname");
                                    $ongrpid = $this->objGroupModel->addGroup( $ogsname, NULL, null );
                                    $ongrpid = $this->objGroupModel->getId($ogsname); 
                                    log_debug("Adding subgroups now");
                                    $this->objGroupModel->addSubGroups($ogsname, $ongrpid);
                                }
                            }
                        }  
                        // re-create the users groups
                        $sql = "SELECT group_id from tbl_groupadmin_groupuser where user_id='".$user['id']."'";
                        $oldgroups = $this->getArray($sql);
                        foreach($oldgroups as $og) {
                            // get the group name of the id we have from the old group
                            $ogrpid = $og['group_id'];
                            $oldname = $this->getArray("SELECT name, parent_id from tbl_groupadmin_group WHERE id = '$ogrpid' AND parent_id IS NULL");
                            if(!empty($oldname))  {
                                $oldgrpname = $oldname[0]['name'];
                                $ngrpid = $this->objGroupModel->getId($oldgrpname."^Students");
                                // now we add the user to the group
                                log_debug("adding user with userid $userid to group with id $ngrpid");
                                // get the users perm user id
                                $usrdata = $this->objGroupOps->getUserByUserId($userid);
                                $userid = $usrdata['perm_user_id'];
                                $this->objGroupModel->addGroupUser( $ngrpid, $userid );
                            }
                        }
                    }
                }
                log_debug("preinstall code complete");
                break;
        }

        return 'preinstall done';
    }

    public function postinstall() {
        return 'postinstall done';
    }
}
?>