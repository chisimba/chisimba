<?php

/**
 * This class interfaces with db to store a list of files uploaded
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
 * @package   podcaster (Podcast manager)
 * @author    Paul Mungai
 * @copyright 2011
 *
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class dbdocuments extends dbtable {

    var $tablename = "tbl_podcaster_documents";
    var $userid;

    public function init() {
        parent::init($this->tablename);
        $this->objUser = $this->getObject('user', 'security');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objUploadTable = $this->getObject('dbfileuploads');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->userutils = $this->getObject('userutils');
        $this->resourcePath = $this->objConfig->getModulePath();
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $location = "http://" . $_SERVER['HTTP_HOST'];
        $replacewith = "";
        $this->sitePath = $location . '/' . str_replace($docRoot, $replacewith, $this->resourcePath);
    }

    /*
     * Function to get documents based on passed params
     * @param string $filter the type of parameter to use
     * @param string $filtervalue the value supplied by the user
     * @return array
     */

    public function filterdocuments($filter, $filtervalue) {

        $sql = "select * from tbl_podcaster_documents ";

        //Derermine the where clause based on filter
        switch ($filter) {
            case 'Owner':
                $sql .= "where contact_person like '%" . $filtervalue . "%'";
                break;
            case 'Ref No':
                $sql .= "where refno like '%" . $filtervalue . "%'";
                break;
            case 'Telephone':
                $sql .= "where telephone like '%" . $filtervalue . "%'";
                break;
            case 'Date':
                $sql .= "where date_created like '%" . $filtervalue . "%'";
                break;
            case 'Title':
                $sql .= "where docname like '%" . $filtervalue . "%'";
                break;
            default:
                return Null;
                break;
        }

        if (!$this->objUser->isadmin()) {
            $sql.=" and (userid = '" . $this->objUser->userid() . "' or userid='1')";
        }
        $sql.=' order by puid DESC';


        $rows = $this->getArray($sql);
        $docs = array();
        //print_r($rows);

        foreach ($rows as $row) {
            //$owner=$this->userutils->getUserId();
            if (strlen(trim($row['contact_person'])) == 0) {
                $owner = $this->objUser->fullname($row['userid']);
            } else {
                $owner = $row['contact_person'];
            }

            if ($row['upload'] == '') {
                $attachmentStatus = "No";
            } else {
                //$f = $row['filename'];
                //$attachmentStatus = 'Yes&nbsp;<img  src="' . $this->sitePath . '/wicid/resources/images/ext/' . $this->findexts($f) . '-16x16.png">';
            }

            $statusS = $row['status'];
            switch ($statusS) {
                case 0:
                    $status = "Creator";
                    break;
                case 1:
                    $status = "APO";
                    break;
                case 2:
                    $status = "Subfaculty";
                    break;
                case 3:
                    $status = "Faculty";
                    break;
                case 4:
                    $status = "Senate";
                    break;
            }

            $currentuserid = $row['currentuserid'];
            $fullname = $this->objUser->fullname($currentuserid);

            $docs[] = array(
                'userid' => $row['userid'],
                'owner' => $owner,
                'refno' => $row['refno'] . "-" . $row['ref_version'],
                'filename' => $row['docname'],
                'group' => $row['groupid'],
                'id' => $row['id'],
                'topic' => $row['topic'],
                'department' => $row['department'],
                'telephone' => $row['telephone'],
                'date' => $row['date_created'],
                'attachmentstatus' => $attachmentStatus,
                'status' => $status,
                'currentuserid' => $fullname,
                'version' => $row['version'],
                'ref_version' => $row['ref_version']
            );
        }
        //echo json_encode(array("documents" => $docs));
        return $docs;
    }

    /*
     * Function to get documents based on passed params
     * @param string rejected
     * @param string active
     * @param string mode
     * @param array limit stores the start and end rows
     * @param string rowcount stores the count of records for this selection
     */

    public function getdocuments($mode="default", $rejected = "N", $active="N", $limit=Null, $rowcount=Null) {

        $sql = "select * from tbl_podcaster_documents where (deleteDoc = 'N' or deleteDoc is null) and  (active='$active' or active is null)
        and (rejectDoc= '$rejected' or rejectDoc is null)";

        if (!$this->objUser->isadmin()) {

            $sql.=" and (userid = '" . $this->objUser->userid() . "' or userid='1')";
        }
        $sql.=' order by puid DESC';

        if (empty($rowcount)) {
            $rowcount = count($this->getArray($sql));
        }
        //Add the limit if specified
        if (is_array($limit))
            $sql .= " limit " . $limit['start'] . ", " . $limit['rows'];

        $rows = $this->getArray($sql);

        $docs = array();


        foreach ($rows as $row) {
            //$owner=$this->userutils->getUserId();
            if (strlen(trim($row['contact_person'])) == 0) {
                $owner = $this->objUser->fullname($row['userid']);
            } else {
                $owner = $row['contact_person'];
            }

            if ($row['upload'] == '') {
                $attachmentStatus = "No";
            } else {
                $attachmentStatus = Null;
                //$f = $row['filename'];
                //$attachmentStatus = 'Yes&nbsp;<img  src="' . $this->sitePath . '/wicid/resources/images/ext/' . $this->findexts($f) . '-16x16.png">';
            }

            $statusS = $row['status'];
            switch ($statusS) {
                case 0:
                    $status = "Creator";
                    break;
                case 1:
                    $status = "APO";
                    break;
                case 2:
                    $status = "Subfaculty";
                    break;
                case 3:
                    $status = "Faculty";
                    break;
                case 4:
                    $status = "Senate";
                    break;
            }

            $currentuserid = $row['currentuserid'];
            $fullname = $this->objUser->fullname($currentuserid);

            $docs[] = array(
                'userid' => $row['userid'],
                'owner' => $owner,
                'refno' => $row['refno'] . "-" . $row['ref_version'],
                'filename' => $row['docname'],
                'group' => $row['groupid'],
                'id' => $row['id'],
                'topic' => $row['topic'],
                'department' => $row['department'],
                'telephone' => $row['telephone'],
                'date' => $row['date_created'],
                'attachmentstatus' => $attachmentStatus,
                'status' => $status,
                'currentuserid' => $fullname,
                'version' => $row['version'],
                'ref_version' => $row['ref_version']
            );
        }
        $docs['count'] = $rowcount;

        return $docs;
    }

    /*
     * Function to get rejected documents based on passed params
     * @param string rejected
     * @param string active
     * @param string mode
     * @param array limit
     * @param string rowcount
     */

    public function getRejectedDocuments($mode="default", $rejected = "Y", $limit=Null, $rowcount=Null) {

        $sql = "select * from tbl_podcaster_documents where rejectDoc= '$rejected'";

        if (!$this->objUser->isadmin()) {

            $sql.=" and (userid = '" . $this->objUser->userid() . "' or userid='1')";
        }
        $sql.=' order by puid DESC';

        //Get total no of rows for this user
        if (empty($rowcount)) {
            $rowcount = count($this->getArray($sql));
        }
        //Add the limit if specified
        if (is_array($limit))
            $sql .= " limit " . $limit['start'] . ", " . $limit['rows'];


        $rows = $this->getArray($sql);
        $docs = array();
        //print_r($rows);

        foreach ($rows as $row) {
            //$owner=$this->userutils->getUserId();
            if (strlen(trim($row['contact_person'])) == 0) {
                $owner = $this->objUser->fullname($row['userid']);
            } else {
                $owner = $row['contact_person'];
            }

            if ($row['upload'] == '') {
                $attachmentStatus = "No";
            } else {
                $attachmentStatus = Null;
                //$f = $row['filename'];
                //$attachmentStatus = 'Yes&nbsp;<img  src="' . $this->sitePath . '/wicid/resources/images/ext/' . $this->findexts($f) . '-16x16.png">';
            }

            $statusS = $row['status'];
            switch ($statusS) {
                case 0:
                    $status = "Creator";
                    break;
                case 1:
                    $status = "APO";
                    break;
                case 2:
                    $status = "Subfaculty";
                    break;
                case 3:
                    $status = "Faculty";
                    break;
                case 4:
                    $status = "Senate";
                    break;
            }

            $currentuserid = $row['currentuserid'];
            $fullname = $this->objUser->fullname($currentuserid);

            $docs[] = array(
                'userid' => $row['userid'],
                'owner' => $owner,
                'refno' => $row['refno'] . "-" . $row['ref_version'],
                'filename' => $row['docname'],
                'group' => $row['groupid'],
                'id' => $row['id'],
                'topic' => $row['topic'],
                'department' => $row['department'],
                'telephone' => $row['telephone'],
                'date' => $row['date_created'],
                'attachmentstatus' => $attachmentStatus,
                'status' => $status,
                'currentuserid' => $fullname,
                'version' => $row['version'],
                'ref_version' => $row['ref_version']
            );
        }
        $docs['count'] = $rowcount;
        //echo json_encode(array("documents" => $docs));
        return $docs;
    }

    /*
     * Function to get the number of unapproved documents
     */

    function getUnapprovedDocsCount() {
        $sql = "select count(id) as total from tbl_podcaster_documents where (deleteDoc = 'N' or deleteDoc is null) and  active='N' and rejectDoc!='Y'";

        if (!$this->objUser->isadmin()) {
            $sql.=" and (userid = '" . $this->objUser->userid() . "' or userid='1')";
        }
        $data = $this->getArray($sql);
        $recordcount = $data[0]['total'];
        return $recordcount;
    }

    /*
     * Function to get the number of rejected documents
     */

    function getRejectedDocsCount() {
        $sql = "select count(id) as total from tbl_podcaster_documents where rejectDoc= 'Y'";
        if (!$this->objUser->isadmin()) {
            $sql.=" and (userid = '" . $this->objUser->userid() . "' or userid='1')";
        }
        $data = $this->getArray($sql);
        return $data[0]['total'];
    }

    /**
     * adds new document record
     * @param <type> $date
     * @param <type> $refno
     * @param <type> $department
     * @param <type> $telephone
     * @param <type> $title
     * @param <type> $path
     */
    public function addDocument(
    $date, $refno, $department, $contact, $telephone, $title, $groupid, $path, $currentuserid, $version, $ref_version, $mode="apo", $approved="N", $status="0"
    ) {


        $userid = $this->userutils->getUserId();
        $currentuserid = $userid;

        // using this user id, get the full name and compare it with contact person!
        $fullname = $this->objUser->fullname($userid);
        if (strcmp($fullname, $contact) == 0) {
            $contact = "";
        }
        if ($contact == NULL) {
            $contact = $this->objUser->fullname();
        }
        //Remove double //
        $newpath = str_ireplace("//", "/", $path);
        //$newpath = str_ireplace("/", "%", $newpath);

        $data = array(
            'docname' => $title,
            'date_created' => $date,
            'userid' => $this->objUser->userId(),
            'refno' => $refno,
            'groupid' => $groupid,
            'department' => $department,
            'contact_person' => $contact,
            'telephone' => $telephone,
            'topic' => $newpath,
            'mode' => $mode,
            'active' => $approved,
            'status' => $status,
            'currentuserid' => $currentuserid,
            'deleteDoc' => "N",
            'ref_version' => $ref_version,
            'version' => $version
        );

        $id = $this->insert($data);
        return $refno . "/" . $ref_version;
    }

    function documentExists(
    $department, $refno, $title, $path, $version) {
        $sql =
                "select * from tbl_podcaster_documents where version = '$version' and topic = '$path'
         and docname = '$title' and refno ='$refno' and department = '$department'";
        $data = $this->getArray($sql);
        return count($data) > 0 ? TRUE : FALSE;
    }

    /**
     * sets active to Y and deleteDoc to N for the submitted docs
     * @param <type> $docids
     */
    function approveDocs($docids) {
        $data = array('active' => 'Y', 'deleteDoc' => 'N');
        $ids = explode(",", $docids);
        $userid = $this->userutils->getUserId();
        $ext = '.na';
        $dir = $this->objSysConfig->getValue('FILES_DIR', 'podcaster');
        foreach ($ids as $id) {
            //Check if record has an attachment
            $checkupload = $this->getAll("where id='" . $id . "' and upload='Y'");
            if (!empty($checkupload)) {
                $this->update('id', $id, $data);
                $doc = $this->getDocument($id);
                //print_r($doc);

                $filename = $dir . '/' . $doc['topic'] . '/' . $doc['docname'] . $ext;
                $filename = str_replace("//", "/", $filename);
                $newname = $dir . '/' . $doc['topic'] . '/' . $doc['docname'] . '.' . $doc['ext'];
                $newname = str_replace("//", "/", $newname);

                /*  $fh = fopen("/dwaf/wicidtest/log.txt", 'w') or die("can't open file ".$doc['docname']);
                  $stringData = "renaming on approve $filename\n$newname\n===================";
                  fwrite($fh, $stringData);
                  fclose($fh);
                 */
                if (!file_exists($filename)) {

                    $fh = fopen($filename, 'w') or die("can't open file " . $doc['docname']);
                    $stringData = "\n";
                    fwrite($fh, $stringData);
                    fclose($fh);
                    $data = array(
                        'filename' => $doc['docname'] . $ext,
                        'filetype' => 'txt',
                        'date_uploaded' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                        'userid' => $userid,
                        'parent' => $doc['topic'],
                        'docid' => $id,
                        'refno' => $this->userutils->getRefNo($id),
                        'filepath' => $doc['topic'] . '/' . $doc['docname'] . $ext);
                    $result = $this->objUploadTable->saveFileInfo($data);
                } else {
                    rename($filename, $newname);
                }
            }
        }
    }

    /**
     * sets delete to by setting deleteDoc value to Y to docs with supplied id
     * @param <type> $docids
     */
    function deleteDocuments($docids) {
        $data = array('deleteDoc' => 'Y', 'active' => 'N');
        $ids = explode(",", $docids);
        $userid = $this->userutils->getUserId();
        foreach ($ids as $id) {
            $this->update('id', $id, $data);
        }
    }

    /**
     * sets active to Y to docs with supplied id
     * @param <type> $docids
     */
    function rejectDocs($docids) {

        $ids = explode(",", $docids);
        $ext = '.na';
        $dir = $this->objSysConfig->getValue('FILES_DIR', 'podcaster');
        foreach ($ids as $id) {
            if (strlen($id) > 0) {
                $data = array('rejectDoc' => 'Y');
                $res = $this->update('id', $id, $data);
            }
        }
    }

    /**
     * sets active to Y to docs with supplied id
     * @param <type> $docids
     */
    function deleteDocs($docids) {

        $ids = explode(",", $docids);
        $ext = '.na';
        $dir = $this->objSysConfig->getValue('FILES_DIR', 'podcaster');
        foreach ($ids as $id) {
            if (strlen($id) > 0) {
                $doc = $this->getDocument($id);

                //$filename=$dir.'/'.$doc['topic'].'/'. $doc['docname'].$ext;
                //$filename= str_replace("//", "/", $filename);
                $filename = $dir . '/' . $doc['topic'] . '/' . $doc['docname'] . '.' . $doc['ext'];
                $filename = str_replace("//", "/", $newname);
                //unlink($filename);
                // instead of deleting, set the delete field to Y
                //$this->delete('id',$id);
                $data = array('deleteDoc' => 'Y');
                $res = $this->update('id', $id, $data);
            }
        }
    }

    /**
     * get a documet with specified id
     * @param <type> $id
     * @return <type>
     */
    function getDocument($id) {
        return $this->getRow('id', $id);
    }

    function checkRefNo($number) {
        $sql = "select max(ref_version) as myrefno from ." . $this->tablename;
        $sql .= " where refno like '%" . date("Y") . "%'";
        $sql .= " and SUBSTRING(refno, 1, 1) = '$number'";
        $res = $this->getArray($sql);

        return (int) $res[0]['myrefno'] + 1;
    }

    /*
     * Get Id using the refno
     * @param string refno
     * @return id
     */

    function getIdWithRefNo($refno) {
        $refnumber = explode("-", $refno);
        $res = $this->getRow("refno", $refnumber[0]);
        $res = $this->getAll("where refno='" . $refnumber[0] . "' and ref_version='" . $refnumber[1] . "'");
        return $res[0]['id'];
    }

    function getRefNo($id) {
        $res = $this->getRow("id", $id);

        return $res['refno'];
    }

    function getRefFullNo($id) {
        $res = $this->getRow("id", $id);
        return $res['refno'] . '-' . $res['ref_version'];
    }

    function getRefVersion($id) {
        $res = $this->getRow("id", $id);
        return $res['ref_version'];
    }

    function findexts($filename) {
        $filename = strtolower($filename);
        $exts = split("[/\\.]", $filename);
        $n = count($exts) - 1;
        $ext = $exts[$n];

        //check if icon for this exists, else return unknown
        $filePath = $this->objConfig->getModulePath() . '/wicid/resources/images/ext/' . $ext . '-16x16.png';
        if (file_exists($filePath)) {
            return $ext;
        } else {
            return "unknown";
        }
    }

    function updateInfo($id, $data) {
        $version = $this->getVersion($id);
        $data['version'] = $version;
        return $this->update("id", $id, $data);
    }

    function changeCurrentUser($userid, $docid, $version) {
        print_r($docid . " - " . $version . " - " . $userid);
        $sql = "update tbl_podcaster_documents set currentuserid = '$userid' where id = '$docid' and version = '$version'";
        // $this->sendEmailAlert($userid);
        return $this->getArray($sql);
    }

    function sendEmailAlert($useridto) {
        $toNames = $this->objUser->fullname($useridto);
        $toEmail = $this->objUser->email($useridto);

        $linkUrl = $this->uri(array('action' => 'home'));
        $linkUrl->link = "Link";
        $objMailer = $this->getObject('mailer', 'mail');
        $body = "xyz has forwarded you document titled xrf. To access it, click on link below
        " . $linkUrl->href;
        $subject = "hi";

        $objMailer->setValue('to', array($toEmail));
        $objMailer->setValue('from', $this->objUser->email());
        $objMailer->setValue('fromName', $this->objUser->fullname());
        $objMailer->setValue('subject', $subject);
        $objMailer->setValue('body', strip_tags($body));
        $objMailer->setValue('AltBody', strip_tags($body));

        $objMailer->send();
    }

    function retrieveDocument($userid, $docid) {
        $sql = "update tbl_podcaster_documents set currentuserid = '$userid' where id = '$docid'";
        $this->sendEmailAlert($userid);
        return $this->getArray($sql);
    }

    function checkUsers($docid) {
        $sql = "select userid, currentuserid from tbl_podcaster_documents where id='$docid'";
        $userid = "";
        $currentuserid = "";
        $rows = $this->getArray($sql);

        foreach ($rows as $row) {
            $userid = $row['userid'];
            $currentuserid = $row['currentuserid'];
        }
        echo $userid . ' ' . $currentuserid;
        return $userid;
        $currentuserid;
    }

    function getStatus($docid) {
        $sql = "select status from tbl_podcaster_documents where id='$docid'";
        $rows = $this->getArray($sql);

        foreach ($rows as $row) {
            $status = $row['status'];
        }
        echo $status;
        return $status;
    }

    function setStatus($docid, $status, $version) {
        $sql = "update tbl_podcaster_documents set status = '$status' where id = '$docid' and version = '$version'";
        return $this->getArray($sql);
    }

    function increaseVersion($docid) {
        (int) $versionOld = $this->getVersion($docid);
        $versionNew = $versionOld + 1;

        $sql = "select * from tbl_podcaster_documents where id = '$docid' and version = '$versionOld'";
        $data = $this->getArray($sql);

        $dataNew = $data[0];
        $dataNew['version'] = $versionNew;
        if ($dataNew['ext'] == null) {
            unset($dataNew['ext']);
        }
        if ($dataNew['upload'] == null) {
            unset($dataNew['upload']);
        }
        if ($dataNew['department'] == null) {
            unset($dataNew['department']);
        }

        $sql2 = "select max(puid) as puid from tbl_podcaster_documents";
        $puidA = $this->getArray($sql2);
        $puid = (int) $puidA[0]['puid'];

        $dataNew['puid'] = ((int) $puid) + 1;

        $this->insert($dataNew);

        echo $versionNew;
        return $versionNew;

        // return $versionNew;
    }

    function getVersion($docid) {
        $sql = "select max(version) as version from tbl_podcaster_documents where id='$docid'";
        $version1 = $this->getArray($sql);
        $version = (int) $version1[0]['version'];

        return $version;
    }

    function reclaimDocument($userid, $docid, $version) {
        $sql = "update tbl_podcaster_documents set currentuserid = '$userid' where id = '$docid' and version = '$version'";
        return $this->getArray($sql);
    }

}

?>
