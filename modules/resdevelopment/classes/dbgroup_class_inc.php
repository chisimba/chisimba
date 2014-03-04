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
 * @package   resdev (Residence Development)
 * @author    Nguni Phakela, Matshusa Mulalo, Hleketani Mabasa
 * @copyright 2010
=
 */
class dbgroup extends dbtable {
    private $tablename = "tbl_resdev_group";

    public function init() {
        parent::init($this->tablename);
    }

    public function saveGroupInfo($groupname) {
        $data = array("groupname"=>$groupname,  "date_created"=>strftime('%Y-%m-%d %H:%M:%S',mktime()));
        $this->insert($data);
    }

        public function editGroupInfo($groupname, $id) {
        $data = array("groupname"=>$groupname, "date_created"=>strftime('%Y-%m-%d %H:%M:%S',mktime()));
        $this->update('id', $id, $data);
    }

    public function getGroupData() {
        return $this->getAll();
    }

    public function deleteGroup($groupID) {
        $this->delete("id", $groupID);
    }

    public function editGroup($groupID){
        $sql="update tbl_resdev_group set groupname=jkshgfjhkgj where id=$groupId";
        return $this->getAll();
    }
}
?>