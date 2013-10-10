<?php
/**
 *
 *
 * Class to interact with the database for the Turnitin  submittedAssignments
 *
 * PHP version 5
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
 * @category  chisimba
 * @package   turnitin
 * @author    david wafula <davidwaf@gmail.com>

 */
class turnitinsubmittedassignments extends dbTable {

    /**
     * Constructor
     *
     */
    public function init() {
        parent::init('tbl_turnitin_assignments_submitted');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objUser=$this->getObject("user","security");

    }

    /**
     * Method to add an assigment detail
     *
     * @param string $contextcode
     * @param array $params
     * @return boolean
     */

    public function addSubmittedAssignment($objectid,$contextcode,$assigntitle,$filename,$returncode) {
        $data=array(
                'objectid'=>$objectid,
                'userid'=>$this->objUser->userid(),
                'contextcode'=>$contextcode,
                'assigntitle'=>$assigntitle,
                'submitted'=>$returncode == "51"?"Y":"N",
                'filename'=>$filename);
        return $this->insert($data);
    }

    public function getUserId($objectid) {
        $data=$this->getRow("objectid", $objectid);
        return $data['userid'];
    }


    public function getUser($email){
        $sql=
        "select * from tbl_users where emailAddress='$email'";
        $rows=$this->getArray($sql);
        return $rows[0];
    }

    public function getFileName($objectid) {
        $data=$this->getRow("objectid", $objectid);
        return $data['filename'];
    }

    public function getTotalSubmissions($contextcode, $title){
        $recs = $this->getAll("where contextcode='$contextcode' and assigntitle = '$title' and submitted='Y'");
        return count($recs);
    }
    public function submissionExists($userid,$contextcode,$assigtitle) {
        $sql="select submitted from tbl_turnitin_assignments_submitted where userid='$userid' and contextcode= '$contextcode'
         and submitted  = 'Y' and assigntitle='$assigtitle'";

        $data=$this->getarray($sql);

        foreach($data as $row) {

            if($row['submitted'] == 'Y') {
                return TRUE;
            }
        }
        return FALSE;

    }

}
