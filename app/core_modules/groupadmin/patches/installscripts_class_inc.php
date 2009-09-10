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
    	//echo '<pre>';
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
                        // re-create the user's groups
                        $sql = "SELECT group_id from tbl_groupadmin_groupuser where user_id='".$user['id']."'";
                        $oldgroups = $this->getArray($sql);
            			//var_dump($oldgroups);
                        foreach($oldgroups as $og) {
                            $oldGroupPath = $this->getOldGroupPath($og['group_id']);
                            if (''==$oldGroupPath) {
                                continue;
                            }
                            $ngrpid = $this->objGroupModel->getId($oldGroupPath);
                            // now we add the user to the group
                            log_debug("adding user with userid $userid to group with id $ngrpid [$oldGroupPath]");
                            // get the users perm user id
                            $usrdata = $this->objGroupOps->getUserByUserId($userid);
                            //$permUserId = $usrdata['perm_user_id'];
                            $this->objGroupModel->addGroupUser( $ngrpid, $usrdata['perm_user_id'] ); //$permUserId
                        }
                    }
                }
                log_debug("preinstall code complete");
                break;
        }
    	//echo '</pre>';
        return 'preinstall done';
    }

    /**
    * Recurse through the old group structure.
    * @param string The old group ID
    * @return string The old group path
    */
    private function getOldGroupPath($groupId)
    {
        // get the group name of the id we have from the old group
        //$ogrpid = $og['group_id'];
        //echo "*$ogrpid\n";
        $rs = $this->getArray("SELECT name, parent_id from tbl_groupadmin_group WHERE id = '$groupId'"); // AND parent_id IS NULL
        //var_dump($oldgroup_rs);
        if (empty($rs))  {
            return '';
        }
        else {
            $row = $rs[0];
            if (NULL == $row['parent_id']) {
                return $row['name'];
            }
            else {
                return $this->getOldGroupPath($row['parent_id']).'^'.$row['name'];
            }
        }
    }

    public function postinstall() {
        return 'postinstall done';
    }
}
?>
