<?php
/**
 * This class manages the permisions for folders
 *  PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   wicid (document management system)
 * @author    Nguni Phakela, david wafula
 * @copyright 2010
=
 */
class dbfolderpermissions extends dbtable {
    private $tablename = "tbl_wicid_folderpermissions";
    private $userid;

    public function init() {
        parent::init($this->tablename);
        $this->userobj=$this->getObject('user','security');
        $this->userid=$this->userobj->userid();
    }

    /**
     * adds a new permission. If such permission exists, then its updated
     * @param <type> $userid
     * @param <type> $folderpath
     * @param <type> $viewfiles
     * @param <type> $uploadfiles
     * @param <type> $createfolder
     */
    public function addPermission($userid,$folderpath,$viewfiles,$uploadfiles,$createfolder) {
        
        $data=array(
                'userid'=>$userid,
                'folderpath'=>$folderpath,
                'viewfiles'=>$viewfiles,
                'uploadfiles'=>$uploadfiles,
                'createfolder'=>$createfolder
        );
        if($this->permissionExists($userid,$folderpath)) {
           $sql="update $this->tablename set viewfiles='$viewfiles',
                   uploadfiles='$uploadfiles' createfolder ='$createfolder'
                   where userid = '$userid' and folderpath='$folderpath'";
           $this->getArray($sql);
        }else{
             $this->insert($data);
        }
    }

    public  function getAllFolders(){
        $sql=
        "select * from ".$this->tablename;
        return $this->getArray($sql);
    }

    /**
     *
     * gets all the users and the thier permissions for a specific folder
     * @param <type> $folderpath
     * @return <type>
     */
    public function getPermmissions($folderpath) {
        $this->userid="1";
        $sql="select * from ".$this->tablename." where userid = '".$this->userid."' and folderpath= '".$folderpath."'";
        $rows=$this->getArray($sql);
        return $rows;

    }



    public function isValidFolder($folderpath) {
        $folderpath=str_replace("'", "\'", $folderpath);
        $sql="select * from ".$this->tablename." where folderpath= '".$folderpath."'";
       
        $rows=$this->getArray($sql);

        return count($rows) > 0  ? TRUE: FALSE;

    }
    /**
     * checks where the permission exists
     * @param <type> $userid
     * @param <type> $folderpath
     * @return <type>
     */
    public function permissionExists($userid, $folderpath) {
        $sql="select * from ".$this->tablename." where userid = '".$userid."' and folderpath= '".$folderpath."'";
        $rows=$this->getArray($sql);
        foreach($rows as $row) {
            return TRUE;
        }
        return FALSE;
    }
    /**
     * removes permissions of a user to a specifcci folder
     * @param <type> $userid
     * @param <type> $folderpath
     * @return <type>
     */
    public function removePermission($userid, $folderpath) {
        $sql="delete from ".$this->tablename." where userid = '".$userid."' and folderpath= '".$folderpath."'";
        $rows=$this->getArray($sql);
        return TRUE;
    }
    /**
     * updates a permission of a user to a specific folder
     * @param <type> $userid
     * @param <type> $folderpath
     * @return <type>
     */
    public function updatePermission($userid, $folderpath) {
        $sql="update ".$this->tablename." set folderpath= '$folderpath' where userid = '".$userid."' and folderpath= '".$folderpath."'";
        $rows=$this->getArray($sql);
        return TRUE;
    }

    /**
     * gets a list of users with thier access rights to the supplied folder
     * @param <type> $foldername
     */
    public function  getusers($foldername) {
        $sql="select * from ".$this->tablename." where  folderpath= '".$foldername."'";
        $rows=$this->getArray($sql);
        $users=array();
        foreach ($rows as $row) {
            $users[]=array(
                    'userid'=>$row['userid'],
                    'username'=>$this->userobj->username($row['userid']),
                    'names'=>$this->userobj->fullname($row['userid']),
                    'delete'=>false,
                    'viewfiles'=>true,
                    'uploadfiles'=>$row['uploadfiles']=="true"?1:0,
                    'createfolder'=>$row['createfolder']=="true"?true:false
            );
        }
        echo json_encode(array("users"=>$users));
        die();
    }

    /**
     * this returns all the users based on the filter field. It used to search
     * for new users to assign folder permissions
     * @param <type> $searchfield
     */
    public function  getallusers($searchfield) {
        $sql="select userid,username,firstname,surname from tbl_users where
            firstName like '".$searchfield."%' or surname like '$searchfield%'";
        $rows=$this->getArray($sql);

        $users=array();
        foreach ($rows as $row) {
            $users[]=array(
                    'userid'=> $row['userid'],
                    'username'=>$row['username'],
                    'names'=>$row['firstname'].' '.$row['surname'],
                    'select'=>false
            );
        }
        echo json_encode(array("users"=>$users));
        die();
    }

}
?>