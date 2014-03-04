<?php

/**
 * This class interfaces with db to store a list of podcast files uploaded
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
 * @package   podcaster
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright 2011

 */
class dbfileuploads extends dbtable {

    var $tablename = "tbl_podcaster_fileuploads";
    var $userid;

    public function init() {
        parent::init($this->tablename);
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->objUserutils = $this->getObject('userutils');
        $this->resourcePath = $this->objAltConfig->getModulePath();
        $replacewith = "";
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $location = "http://" . $_SERVER['HTTP_HOST'];
        $this->sitePath = $location . '/' . str_replace($docRoot, $replacewith, $this->resourcePath);
    }

    public function setUserId($userid) {
        $this->userid = $userid;
    }

    public function saveFileInfo($data) {
        $result = NULL;
        if ($this->fileExists($data['docid'])) {
            $result = $this->update('docid', $data['docid'], $data);
        } else {
            $result = $this->insert($data);
        }
        return $result;
    }

    public function fileExists($docid) {

        $sql =
                "select id from  " . $this->tablename . " where docid='$docid'";
        $res = $this->getArray($sql);
        if (count($res) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getFileTypes() {
        $sql = "select distinct filetype from " . $this->tablename . " where userid = '" . $this->userid . "'";
        $res = $this->getArray($sql);

        return $res;
    }

    public function getDocs($filetype) {
        $sql = "select * from " . $this->tablename . " where filetype = '" . $filetype . "'" . " and userid = '" . $this->userid . "'";
        $res = $this->getArray($sql);

        return $res;
    }

    /**
     * gets the instances of this file, to avoid duplication, overwriting
     * @param <type> $filename
     * @param <type> $path
     * @return <type>
     */
    public function getFileInstances($filename, $path) {
        $sql = "select  id from " . $this->tablename . " where parent = '" . $filename . "' and filepath = '" . $path . "'";
        $res = $this->getArray($sql);
        return $res;
    }

    public function getAllFiles() {
        $sql = "select * from " . $this->tablename . " where userid = '" . $this->userid . "' order by date_uploaded desc, filename limit 10";
        $res = $this->getArray($sql);

        return $res;
    }

    /*
     * Function to get documents based on passed params
     * @param string $filter the type of parameter to use
     * @param string $filtervalue the value supplied by the user
     * @return array
     */

    public function searchFileInAllNodes($filter, $filtervalue) {
        $sql = "select A.refno,A.telephone, A.docname, A.date_created, A.contact_person, A.userid, B.date_uploaded, B.filename, B.filepath, B.docid
              from tbl_podcaster_documents as A
                join tbl_podcaster_fileuploads as B on A.id = B.docid ";
        //Derermine the where clause based on filter
        switch ($filter) {
            case 'Owner':
                $sql .= "where A.contact_person like '%" . $filtervalue . "%'";
                break;
            case 'Ref No':
                $sql .= "where A.refno like '%" . $filtervalue . "%'";
                break;
            case 'Telephone':
                $sql .= "where A.telephone like '%" . $filtervalue . "%'";
                break;
            case 'Date':
                $sql .= "where A.date_created between '" . $filtervalue['start'] . "' and '" . $filtervalue['end'] . "'";
                break;
            case 'Title':
                $sql .= "where A.docname like '%" . $filtervalue . "%'";
                break;
            default:
                $sql .= "where A.docname like '%" . $filtervalue . "%' or A.telephone like '%" . $filtervalue . "%' or A.refno like '%" . $filtervalue . "%' or A.contact_person like '%" . $filtervalue . "%'";
                break;
        }
        $sql .= " and A.active ='Y' order by A.date_created DESC";

        return $this->getArray($sql);
    }
    /**
     * Function that fetches all the node files within a specified limit
     *
     * @param string $node the node
     * @param array $limit contains the start and end limits
     * @return array
     */

    public function getNodeFiles($node, $limit=Null) {

        $sql = "select A.refno,A.telephone, A.date_created, A.userid, B.date_uploaded, B.filename, B.filepath, B.docid
              from tbl_podcaster_documents as A
                join tbl_podcaster_fileuploads as B on A.id = B.docid
              where B.parent = '$node' and A.active ='Y'
              order by A.date_created DESC";

        //Include limit if specified
        if (is_array($limit))
            $sql .= " limit " . $limit['start'] . ", " . $limit['rows'];

        return $this->getArray($sql);
    }
    /**
     *Function that fetches all the approved and non-approved node files within a specified limit
     *
     * @param string $node the node
     * @param array $limit contains the start and end limits
     * @return array
     */

    public function getAllNodeFiles($node, $limit=Null) {

        $sql = "select A.refno,A.telephone, A.date_created, A.userid, B.date_uploaded, B.filename, B.filepath, B.docid
              from tbl_podcaster_documents as A
                join tbl_podcaster_fileuploads as B on A.id = B.docid
              where A.topic = '$node' and (rejectDoc != 'Y' or rejectDoc is null or rejectDoc = 'N') and (deleteDoc != 'Y')
              order by A.date_created DESC";

        //Include limit if specified
        if (is_array($limit))
            $sql .= " limit " . $limit['start'] . ", " . $limit['rows'];

        return $this->getArray($sql);
    }

    public function deleteFileRecord($id) {
        $this->delete('id', $id);
    }

    public function getFileName($id) {
        $data = $this->getRow('id', $id);
        return $data;
    }

    /**
     *  gets all the details of the file that was uploaded
     * @param <type> $filename
     * @param <type> $filepath
     * @return <type>
     */
    public function getFileInfo($filename, $filepath) {
        $filepath = str_replace("//", "/", $filepath);
        $sql = "select * from $this->tablename  fls,tbl_podcaster_documents docs
                where fls.filename = '$filename' and fls.filepath = '$filepath'
                and fls.docid=docs.id and docs.active='Y'";

        $data = $this->getArray($sql);
        return $data;
    }

    function deleteNAFile($filepath, $filename) {
        $sql =
                "delete from tbl_podcaster_fileuploads where filename ='$filename' and filepath='$filepath'";
        $this->getArray($sql);
    }

    function searchfiles($filter, $advanced=false) {
        $objUserutils = $this->getObject('userutils');
        if (!$advanced) {
            $start = "1";
            $length = "4";
            $today = getdate();

            if ((substr($filter, $start, $length) >= $today['year'] - 10) && (substr($filter, $start, $length) <= $today['year'])) {
                $sql = "  select A.refno, A.date_created, A.userid, A.groupid, B.date_uploaded, B.filename, B.filepath, B.docid
                        from tbl_podcaster_documents as A
                            join tbl_podcaster_fileuploads as B on A.id = B.docid
                        where A.refno like '%$filter%'
                        and A.groupid = 'Public'
                        order by A.date_created DESC";
            } else {
                $sql = "select *
                      from tbl_podcaster_fileuploads
                      where filename like '%$filter%'";

                $sql.=' order by date_uploaded DESC';
            }
            $owner = $objUserutils->getUserId();
            $rows = $this->getArray($sql);
            $files = array();

            foreach ($rows as $row) {
                $size = $this->formatBytes(filesize($dir . $node . '/' . $f), 2);
                echo $f;
                $isowner = $this->objUser->userid() == $file['userid'] ? "true" : "false";
                $files[] = array(
                    'text' => $row['filename'],
                    'id' => $row['filepath'],
                    'docid' => $row['docid'],
                    'refno' => $row['refno'],
                    'owner' => $this->objUser->fullname($row['userid']),
                    //'lastmod'=>$lastmod,
                    'lastmod' => $row['date_uploaded'],
                    'filesize' => $size,
                    'thumbnailpath' => '<img  src="' . $this->sitePath . '/podcaster/resources/images/ext/' . $this->findexts($row['filename']) . '-16x16.png">'
                );
            }

            echo json_encode(array("files" => $files));

            die();
        } else {
            $sql = "select * from $this->tablename where docid = '$filter'";
            return $this->getArray($sql);
        }
    }

    // from php manual page
    function formatBytes($val, $digits = 3, $mode = "SI", $bB = "B") { //$mode == "SI"|"IEC", $bB == "b"|"B"
        $si = array("", "K", "M", "G", "T", "P", "E", "Z", "Y");
        $iec = array("", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi");
        switch (strtoupper($mode)) {
            case "SI" : $factor = 1000;
                $symbols = $si;
                break;
            case "IEC" : $factor = 1024;
                $symbols = $iec;
                break;
            default : $factor = 1000;
                $symbols = $si;
                break;
        }
        switch ($bB) {
            case "b" : $val *= 8;
                break;
            default : $bB = "B";
                break;
        }
        for ($i = 0; $i < count($symbols) - 1 && $val >= $factor; $i++)
            $val /= $factor;
        $p = strpos($val, ".");
        if ($p !== false && $p > $digits)
            $val = round($val);
        elseif ($p !== false)
            $val = round($val, $digits - $p);
        return round($val, $digits) . " " . $symbols[$i] . $bB;
    }

    /**
     *  used to get ext to a file
     * @param <type> $filename
     * @return <type>
     */
    function findexts($filename) {
        $filename = strtolower($filename);
        $exts = split("[/\\.]", $filename);
        $n = count($exts) - 1;
        $ext = $exts[$n];

        //check if icon for this exists, else return unknown
        $filePath = $this->objConfig->getModulePath() . '/podcaster/resources/images/ext/' . $ext . '.png';
        if (file_exists($filePath)) {
            return $ext;
        } else {
            return "unknown";
        }
    }

    public function advancedSearch($data) {
        $first = true;

        if (strlen($data['startDate']) > 0) {
            $filter .= $first ? " where " : " and ";
            $first = false;
            $filter .= "date_created >= '" . strftime('%Y-%m-%d', strtotime($data['startDate'])) . "'";
        }
        if (strlen($data['endDate']) > 0) {
            $filter .= $first ? " where " : " and ";
            $first = false;
            $filter .= "date_created <= '" . strftime('%Y-%m-%d', strtotime($data['endDate'])) . "'";
        }
        /* if(strlen($data['fname']) > 0) {
          $this->users->getsurname();
          $filter .=
          }
          if(strlen($data['lname']) > 0) {

          } */
        if (strlen($data['docname']) > 0) {
            $filter .= $first ? " where " : " and ";
            $first = false;
            $filter .= "docname like '%" . $data['docname'] . "%'";
        }
        if (strlen($data['doctype']) > 0) {
            $filter .= $first ? " where " : " and ";
            $first = false;
            $filter .= "refno like '%" . $data['doctype'] . "%'";
        }
        if (strlen($data['refno']) > 0) {
            $filter .= $first ? " where " : " and ";
            $first = false;
            $filter .= "refno like '%" . $data['refno'] . "%'";
        }
        if (strlen($data['topic']) > 0) {
            $filter .= $first ? " where " : " and ";
            $first = false;
            $filter .= "topic like '%" . $data['topic'] . "%'";
        }
        if (strlen($data['dept']) > 0) {
            $filter .= $first ? " where " : " and ";
            $first = false;
            $filter .= "department like '%" . $data['dept'] . "%'";
        }
        if (strlen($data['active']) > 0) {
            $filter .= $first ? " where " : " and ";
            $first = false;
            $filter .= "active = '" . $data['active'] . "'";
        }

        $sql = "select * from tbl_podcaster_documents " . $filter;
        $sql = $first ? " where " : " and " . " groupid = 'Public'";
        $rows = $this->getArray($sql);

        $files = array();

        foreach ($rows as $docrow) {
            $fileData = $this->searchfiles($docrow['id'], true);
            foreach ($fileData as $row) {
                $size = $this->formatBytes(filesize($dir . $node . '/' . $f), 2);
                $files[] = array(
                    'text' => $row['filename'],
                    'id' => $row['filepath'],
                    'docid' => $row['docid'],
                    'refno' => $row['refno'],
                    'owner' => $this->objUser->fullname($row['userid']),
                    'lastmod' => $lastmod,
                    'filesize' => $size,
                    'thumbnailpath' => $this->sitePath . '/podcaster/resources/images/ext/' . $this->findexts($row['filename']) . '.png'
                );
            }
        }

        echo json_encode(array("files" => $files));
    }

    /**
     * check if the documents have attachments
     * @param <type> $ids
     * @return <type>
     */
    function checkAttachment($ids) {
        $docids = explode(",", $ids);
        $count = count($docids);
        for ($i = 0; $i < $count; $i++) {
            if (strlen(trim($docids[$i])) > 0) {
                $filter = "where docid = '" . $docids[$i] . "'";
                $res = $this->getAll($filter);
                if (count($res) == 0) {
                    return "false";
                }
            }
        }

        return "true";
    }

}

?>