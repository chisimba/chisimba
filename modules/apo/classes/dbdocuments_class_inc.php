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
 * @package   apo (docume3nt management system)
 * @author    Nguni Phakela, david wafula
 * @copyright 2010

 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class dbdocuments extends dbtable {

    var $tablename = "tbl_apo_documents";
    var $userid;

    public function init() {
        parent::init($this->tablename);
        $this->objUser = $this->getObject('user', 'security');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->userutils = $this->getObject('userutils');
    }

    public function getdocuments($start, $end, $mode="default", $rejected = "N", $myId = NULL) {
        $sql = "select doc.id, doc.docname,dept.name as department, doc.userid, doc.telephone, doc.date_created from tbl_apo_documents doc, tbl_apo_faculties dept where doc.department = dept.id and deleteDoc = 'N'";
        if (!$this->objUser->isadmin()) {
            $sql.=" and (userid = '" . $this->objUser->userid() . "' or userid='1')";
        } else if (!is_null($myId)) {
            $sql.=" and (userid = '" . $myId . "')";
        }

        $sql.=' order by doc.puid DESC';
        $rows = $this->getArray($sql);
        $docs = array();

        foreach ($rows as $row) {
            if (strlen(trim($row['contact_person'])) == 0) {
                $owner = $this->objUser->fullname($row['userid']);
            } else {
                $owner = $row['contact_person'];
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
                'filename' => $row['docname'],
                'group' => $row['groupid'],
                'id' => $row['id'],
                'topic' => $row['topic'],
                'department' => $row['department'],
                'telephone' => $row['telephone'],
                'date' => $row['date_created'],
                'status' => $status,
                'currentuserid' => $fullname,
                'version' => $row['version'],
            );
        }

        return $docs;
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
    $date, $refno, $department, $contact, $telephone, $title, $groupid, $path, $currentuserid, $mode="apo", $approved="N", $status="0"
    ) {
        $userid = $this->objUser->userId();
        $currentuserid = $userid;

        // using this user id, get the full name and compare it with contact person!
        $fullname = $this->objUser->fullname($userid);
        if (strcmp($fullname, $contact) == 0) {
            $contact = "";
        }
        if ($contact == NULL) {
            $contact = $this->objUser->fullname();
        }

        $data = array(
            'docname' => $title,
            'date_created' => $date,
            'userid' => $this->objUser->userId(),
            'refno' => $refno,
            'groupid' => $groupid,
            'department' => $department,
            'contact_person' => $contact,
            'telephone' => $telephone,
            'topic' => $path,
            'mode' => $mode,
            'deleteDoc' => "N",
            'active' => $approved,
            'status' => $status,
            'currentuserid' => $currentuserid,
        );
        $id = $this->insert($data);
    }

    /**
     * sets active to Y to docs with supplied id
     * @param <type> $docids
     */
    function deleteDocs($docids) {
        $ids = explode(",", $docids);

        foreach ($ids as $id) {
            if (strlen($id) > 0) {
                $doc = $this->getDocument($id);
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

        $document = $this->getRow('id', $id);

        return $document;
    }

    function getUnapprovedDocsCount() {
        $sql = "select count(id) as total from tbl_apo_documents where deleteDoc = 'N' and  active='N'"; /* and rejectDoc= 'N' */

        if (!$this->objUser->isadmin()) {
            $sql.=" and (userid = '" . $this->objUser->userid() . "' or userid='1')";
        }
        $data = $this->getArray($sql);
        foreach ($data as $row) {
            return $row['total'];
        }
        return "0";
    }

    function updateInfo($id, $data) {
        //$version = $this->getVersion($id);
        //$data['version'] = $version;
        $this->update("id", $id, $data);
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

    function getFaculty($docid) {
        $sql = "select department from tbl_apo_documents where id='$docid'";
        $faculty = $this->getArray($sql);
        $faculty = $faculty[0]['name'];

        return $faculty;
    }

    function changeCurrentDocumentUser($docid, $userid) {
        $data = array('currentuserid' => $userid);
        $this->update("id", $docid, $data);
    }

}

?>
