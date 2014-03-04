<?php

/**
 * This class contains utilities for doing common functions in wicid
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
if (!
        /**
         * Description for $GLOBALS
         * @global string $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class userutils extends object {

    var $heading = "Document Management System";
    var $resourcePath;
    public $xmlutil;

    public function init() {
        //instantiate the language object
        $this->loadClass('fieldset', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('treemenu', 'tree');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('treenode', 'tree');
        $this->loadClass('htmllist', 'tree');
        $this->loadClass('htmldropdown', 'tree');
        $this->loadClass('dhtml', 'tree');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objPermittedTypes = $this->getObject('dbpermittedtypes');
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->resourcePath = $this->objAltConfig->getModulePath();
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objLanguage = $this->getObject('language', 'language');
        // $this->xmlutil=$this->getObject('xmlutil');
        $replacewith = "";
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $location = "http://" . $_SERVER['HTTP_HOST'];
        $this->sitePath = $location . '/' . str_replace($docRoot, $replacewith, $this->resourcePath);
        $this->folderPermissions = $this->getObject('dbfolderpermissions');

        $this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
        $this->modeLabel = "Topic";
    }

    public function getUserId() {
        $this->objUser = $this->getObject('user', 'security');
        $userid = $this->objUser->userId();
        //$userid = 1;
        return $userid;
    }

    public function showPageHeading($page=null) {
        if ($page != null) {
            return $this->heading . " - " . ucfirst($page);
        } else {
            return $this->heading;
        }
    }

    public function searchFiles($url) {
        $script = "
                var url = '" . $url . "';

                showButtons();
                showSearchForm(url);
        ";
        return $script;
    }

    public function showUploadForm($url=null) {
        //instantiate the file upload object
        $this->objUpload = $this->getObject('upload', 'filemanager');
        $script = '
            var url ="' . $url . '";
            showUploadForm(url);
        ';
        return $script;
        //return $this->objUpload->show();
    }

    public function getRecentFiles($userid) {
        $this->objUploadTable->setUserId($userid);
        $myData = $this->objUploadTable->getAllFiles();
        $count = 1;
        $numRows = count($myData);
        $detailsLink = new link();
        $deleteLink = new link();
        $fileData = "[";
        foreach ($myData as $row) {
            // get the description for this file type.
            $name = $this->objPermittedTypes->getFileDesc($row['filetype']);
            $fileData .= "['" . $row['filename'] . "','";
            if ($row['shared'] == 1) {
                $fileData .= "public";
            } else {
                $fileData .= "private";
            }
            $detailsLink->link($this->uri(array('action' => 'viewFileDetails', 'id' => $row['id'])));
            $this->objIcon->setIcon('preview');
            $detailsLink->link = $this->objIcon->show();
            $deleteLink->link($this->uri(array('action' => 'deletefile', 'id' => $row['id'])));
            $this->objIcon->setIcon('delete');
            $deleteLink->link = $this->objIcon->show();
            $date = date_create($row['date_forwaded']);
            $fileData .= "','" . date_format($date, "m/d/Y") . "','" . $row['filetype'] . "','" . $name . "','" . $detailsLink->show() . $deleteLink->show() . "'";
            $fileData .= "]";
            if ($count < $numRows) {
                $fileData .= ",";
            }
            $count++;
        }
        $fileData .= "]";
        $data = "
                showLatestUploads(" . $fileData . ");";

        return $data;
    }

    public function saveFile($path, $docname, $docid) {

        $dir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
        $filepath = $dir . $path;
        $filepath = str_replace("//", "/", $filepath);
        $objUser = $this->getObject('user', 'security');
        $userid = $this->getUserId();
        $this->objUploadTable->setUserId($userid);
        $destinationDir = $filepath;
        $objFileUpload = $this->getObject('wicidupload');
        $objFileUpload->overWrite = TRUE;
        $objFileUpload->uploadFolder = $destinationDir . '/';
        $result = $objFileUpload->doUpload($docname, $docid);

        if ($result['success'] == FALSE) {

            return $result['message'];
        } else {
            $filename = $result['clonename'];
            $ext = $result['extension'];
            $parent = $result['filename'];

            $file = $destinationDir . '/' . $filename;
            if (is_file($file)) {
                @chmod($file, 0777);
            }

            // save the file information into the database
            $data = array(
                'filename' => $docname . '.' . $ext,
                'filetype' => $ext,
                'date_uploaded' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                'userid' => $userid,
                'parent' => $parent,
                'refno' => '1234',
                'docid' => $docid,
                'filepath' => $path);



            $result = $this->objUploadTable->saveFileInfo($data);


            return "success";
        }
    }

    public function createJSONFileData($userid) {
        $this->objUploadTable->setUserId($userid);
        //get distinct file types
        $distinctFT = $this->objUploadTable->getFileTypes();

        $count = 1;
        $numTypes = count($distinctFT);
        $JSONstr = "[";
        foreach ($distinctFT as $data) {
            $docs = $this->objUploadTable->getDocs($data['filetype']);

            // get the file type description from the permittedtypes table
            $fileDesc = $this->objPermittedTypes->getFileDesc($data['filetype']);

            $numRows = count($txtDocs);
            $JSONstr .= "{";

            $numRows = count($docs);
            $JSONstr .= "
            filename:'" . $fileDesc . "',";
            $JSONstr .= "
    duration:'',
    uiProvider:'col',
    cls:'master-task',
    iconCls:'task-folder',
    children:[";

            $counter = 1;
            foreach ($docs as $newdata) {
                $mylink = new link();
                $mylink->link($this->uri(array('action' => 'viewfiledetails', 'id' => $newdata['id'])));
                $mylink->link = "Click here for details";
                $date = date_create($newdata['date_uploaded']);
                if ($newdata['shared'] == '1') {
                    $status = "public";
                } else {
                    $status = "private";
                }


                $JSONstr .= "
    {
        filename:'" . $newdata['filename'] . "',
        duration:'" . $newdata['filetype'] . "',
        details: '" . str_replace("amp;", "", $mylink->show()) . "',
        modified: '" . date_format($date, 'm/d/Y') . "',
        status: '" . $status . "',
        uiProvider:'col',
        leaf:true,
        iconCls:'task'
    }";
                if ($counter < $numRows) {
                    $JSONstr .= ",";
                }
                $counter++;
            }
            $JSONstr .= "
]}";
            if ($count < $numTypes) {
                $JSONstr .= ",";
            }
            $count++;
        }
        $JSONstr .= "]";
        echo $JSONstr;
    }

    public function deleteFile($userid, $id) {
        $fileData = $this->objUploadTable->getFileName($id);

        // get the name of the file
        $filename = $fileData['filename'];
        $permission = $fileData['shared'];

        if ($permission == 1) {
            $myFile = $this->objConfig->getcontentBasePath() . '/wicidUploadFiles/' . $userid . '/shared/' . $filename;
        } else {
            $myFile = $this->objConfig->getcontentBasePath() . '/wicidUploadFiles/' . $userid . '/' . $filename;
        }

        if (file_exists($myFile) && is_file($myFile)) {
            //unlink($myFile);
            return true;
        } else {
            return false;
        }
    }
    /**
     * Function that returns the files of a given node
     * @param string $node the node
     * @param array $limit the start and end limits
     * @param string $rowcount the rowcount
     * @return array
     */

    function getFiles($node, $limit=Null, $rowcount=Null) {
        $objFileUploads = $this->getObject('dbfileuploads');
        $today = getdate();

        $owner = $this->getUserId();
        
        if(empty($rowcount)){
            $rowcount = count($objFileUploads->getNodeFiles($node));
        }

        $rows = $objFileUploads->getNodeFiles($node, $limit);

        $files = array();

        foreach ($rows as $row) {
            //$size = $this->formatBytes(filesize($dir.$node.'/'.$f), 2);
            $isowner = $this->objUser->userid() == $row['userid'] ? "true" : "false";
            $size = "0"; //$this->formatBytes(filesize($dir . $node . '/' . $f), 2);
            // $lastmod = date('M j, Y, g:i a',filemtime($dir.$node.'/'.$f));
            $files[] = array(
                'text' => '<img src="' . $this->sitePath . '/wicid/resources/images/ext/' . $this->findexts($row['filename']) . '-16x16.png">&nbsp;' . $row['filename'],
                'actualfilename' => $row['filename'],
                'id' => $row['filepath'],
                'docid' => $row['docid'],
                'telephone' => $row['telephone'],
                'refno' => $this->getFullRefNo($row['docid']),
                'owner' => $this->objUser->fullname($row['userid']),
                'lastmod' => $row['date_uploaded'],
                'filesize' => $size,
                'thumbnailpath' => '<img src="' . $this->sitePath . '/wicid/resources/images/ext/' . $this->findexts($row['filename']) . '.png" width="22" height="22">',
            );
        }
        $files['count'] = $rowcount;
        return $files;
    }

    /*
     * Function to get documents based on passed params
     * @param string $filter the type of parameter to use
     * @param string or array $filtervalue the value supplied by the user
     * @param array doctype ("approveddocs","unapproveddocs","rejecteddocs")
     * @return array
     */

    public function searchFileInAllNodes($filter, $filtervalue, $doctype=null) {
        $objFileUploads = $this->getObject('dbfileuploads');
        $today = getdate();

        $owner = $this->getUserId();
        $rows = $objFileUploads->searchFileInAllNodes($filter, $filtervalue, $doctype);
        $files = array();

        foreach ($rows as $row) {
            $isowner = $this->objUser->userid() == $row['userid'] ? "true" : "false";
            $size = "0";
            $files[] = array(
                'text' => '<img src="' . $this->sitePath . '/wicid/resources/images/ext/' . $this->findexts($row['filename']) . '-16x16.png">&nbsp;' . $row['filename'],
                'actualfilename' => $row['filename'],
                'id' => $row['filepath'],
                'docid' => $row['docid'],
                'telephone' => $row['telephone'],
                'refno' => $this->getFullRefNo($row['docid']),
                'owner' => $this->objUser->fullname($row['userid']),
                'lastmod' => $row['date_uploaded'],
                'filesize' => $size,
                'thumbnailpath' => '<img src="' . $this->sitePath . '/wicid/resources/images/ext/' . $this->findexts($row['filename']) . '.png" width="22" height="22">',
            );
        }
        return $files;
    }

    /* function getFiles() {
      $this->objUser = $this->getObject("user", "security");
      $dir=$this->objSysConfig->getValue('FILES_DIR', 'wicid');
      $this->objUploadTable = $this->getObject('dbfileuploads');

      $node = isset($_REQUEST['node'])?$_REQUEST['node']:"";

      if(strpos($node, '..') !== false) {
      die('Nice try buddy.');
      }
      $nodes = array();

      $d = dir($dir.$node);
      while($f = $d->read()) {
      if($f == '.' || $f == '..' || substr($f, 0, 1) == '.')continue;
      $lastmod = date('M j, Y, g:i a',filemtime($dir.$node.'/'.$f));

      if(!is_dir($dir.$node.'/'.$f)) {
      $fileinfo=$this->objUploadTable->getFileInfo($f,$node.'/'.$f);
      foreach ($fileinfo as $file) {
      $size = $this->formatBytes(filesize($dir.$node.'/'.$f), 2);
      $isowner=$this->objUser->userid() == $file['userid']?"true":"false";
      $nodes[] = array(
      'text'=>$f,
      'id'=>$node.'/'.$f,
      'docid'=>$file['docid'],
      'refno'=>$file['id'],
      'group'=>$file['groupid'],
      'owner'=>$this->objUser->fullname($file['userid']),
      'lastmod'=>$lastmod,
      'filesize'=>$size,
      'thumbnailpath'=>$this->sitePath.'/wicid/resources/images/ext/'.$this->findexts($f).'.png'
      );
      }
      }

      }
      $d->close();

      echo json_encode(array("files"=>$nodes));

      } */

    /**
     * retrieves folders that this user has access to
     */
    function getFolders($mode, $node="") {
        $objUser = $this->getObject("user", "security");
        $dir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
        $apodir = $this->objSysConfig->getValue('APO_DIR', 'wicid');
        $this->objUser = $this->getObject('user', 'security');
        $size = "";

        $isadmin = $this->objUser->isAdmin() ? "true" : "false";
        $isadmin = "true";
        //is it apo?
        if ($mode == 'apo' && $node == "" && $isadmin != 'true') {
            $node = $apodir;
        }

        if (strpos($node, '..') !== false) {
            die('Nice try buddy.');
        }
        $result = array();
        $cdir = $dir . '/' . $node;
        $cdir = str_replace("//", "/", $cdir);

        $d = dir($cdir);
        while ($f = $d->read()) {

            if ($f == '.' || $f == '..' || substr($f, 0, 1) == '.'

                )continue;

            $lastmod = date('M j, Y, g:i a', filemtime($dir . $node . '/' . $f));
            $rdir = $dir . $node . '/' . $f;
            $rdir = str_replace("//", "/", $rdir);
            if (is_dir($rdir)) {
                //set permission before adding
                $pdir = $node . '/' . $f;
                $pdir = str_replace("//", "/", $pdir);
                $permissions = $this->folderPermissions->getPermmissions($pdir);

                foreach ($permissions as $permission) {

                    $userid = $this->getUserId();
                    $isowner = $userid == $permission['userid'] ? "true" : "false";

                    $cfile = array(
                        "folder" => "true",
                        "name" => $f,
                        "id" => $pdir,
                        "lastmodified" => $lastmod,
                        "size" => $size,
                        "viewfiles" => $permission['viewfiles'],
                        "uploadfiles" => $permission['uploadfiles'],
                        "createfolder" => $permission['createfolder'],
                        "isadmin" => $isadmin,
                        "isowner" => $isowner,
                        "parent" => $node);
                    $result[] = $cfile;
                }
            } else {
                $cfile = array(
                    "folder" => "false",
                    "name" => $f,
                    "id" => $node);
                $result[] = $cfile;
            }
        }

        $d->close();
        return $result;
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
     * allows the user to donwload the selected file
     * @param <type> $filename
     */
    function downloadFile($filepath, $filename) {

        //check if user has access to the parent folder before accessing it

        $baseDir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
        // Detect missing filename
        if (!$filename && !$filepath)
            die("I'm sorry, you must specify a file name to download.");

        // Make sure we can't download files above the current directory location.
        if (eregi("\.\.", $filepath))
            die("I'm sorry, you may not download that file.");
        $file = str_replace("..", "", $filepath);

        // Make sure we can't download .ht control files.
        if (eregi("\.ht.+", $filepath))
            die("I'm sorry, you may not download that file.");

        // Combine the download path and the filename to create the full path to the file.
        $file = $baseDir . $filepath;

        // Test to ensure that the file exists.
        if (!file_exists($file))
            die("I'm sorry, the file doesn't seem to exist.");

        // Extract the type of file which will be sent to the browser as a header
        $type = filetype($file);

        // Get a date and timestamp
        $today = date("F j, Y, g:i a");
        $time = time();


        // Send file headers
        header("Content-type: $type");
        header("Content-Disposition: attachment;filename=" . urlencode($filename));
        header('Pragma: no-cache');
        header('Expires: 0');

        // Send the file contents.
        readfile($file);
    }

    /**
     * creates a folder
     * @param <type> $folderpath
     * @param <type> $foldername
     */
    public function createfolder($folderpath, $foldername) {
        $this->objMkdir = $this->getObject('mkdir', 'files');
        $path = $this->objSysConfig->getValue('FILES_DIR', 'wicid') . '/' . $folderpath . '/' . $foldername;
        $result = $this->objMkdir->mkdirs($path);
        $userid = $this->getUserId();
        $fullpath = $folderpath . '/' . $foldername;
        //Search and replace double //
        $fullpath = str_ireplace("//", "/", $fullpath);
        //if($result != FALSE) {
        $this->folderPermissions->addPermission(
                $userid, $fullpath,
                'true', 'true', 'true');
    }

    /**
     * Check if folder exists
     * @param string $folderpath
     * @param string $foldername
     * @return boolean
     */
    public function folderExistsCheck($folderpath, $foldername) {
        $this->objMkdir = $this->getObject('mkdir', 'files');
        $path = $this->objSysConfig->getValue('FILES_DIR', 'wicid') . '/' . $folderpath . '/' . $foldername;

        //Check if path is an existing directory
        if (is_dir($path)) {
            $result = TRUE;
        } else {
            $result = FALSE;
        }
        return $result;
    }

    /**
     * renames a selected folder
     * @param <type> $folderpath
     * @param <type> $foldername
     * @return <type>
     */
    public function renamefolder($folderpath, $foldername) {
        $folderpath = str_replace("//", "", $folderpath);

        $prevpath = $this->objSysConfig->getValue('FILES_DIR', 'wicid') . '/' . $folderpath;
        $newpath = $this->objSysConfig->getValue('FILES_DIR', 'wicid') . '/' . $foldername;

        // do a move using command line interface from previous location to new location.
        $command = "mv " . $prevpath . " " . $newpath;
        $res = system($command, $retval);

        if ($retval != 0) {
            return "error";
        } else {
            return "success";
        }
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
        $filePath = $this->objConfig->getModulePath() . '/wicid/resources/images/ext/' . $ext . '.png';
        if (file_exists($filePath)) {
            return $ext;
        } else {
            return "unknown";
        }
    }

    /**
     * deletes selected folder
     * @param <type> $folderpath
     * @return <type>
     */
    public function deleteFolder($folderpath) {
        $folderpath = str_replace("//", "", $folderpath);
        $fullpath = $this->objSysConfig->getValue('FILES_DIR', 'wicid') . '/' . $folderpath;

        if (is_dir($fullpath)) {
            $res = rmdir($fullpath);
        }

        if ($res) {
            return "success";
        } else {
            return "error";
        }
    }

    /**
     * returns filename with ext stripped
     */
    function getFileName($filepath) {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];
        //split the string by the literal dot in the filename
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
        //get the last dot position
        $lastdot = $pattern[count($pattern) - 1][1];
        //now extract the filename using the basename function
        $filename = basename(substr($string, 0, $lastdot - 1));
        $exts = split("[/\\.]", $filepath);
        $n = count($exts) - 1;
        $ext = $exts[$n];

        return $filename . '.' . $ext;
    }

    /**
     * loops for a miximum of 10 mins, waiting for upload to complete
     * @param <type> $filename
     * @param <type> $folderpath
     */
    function monitorupload($filename, $folderpath) {
        $micro_seconds = 2000;
        $count = 1;
        $state = "";
        while (true) {
            if (file_exists($folderpath . '/' . $filename)) {
                $state = "succeed";
                break;
            }
            if ($count > 30) {
                break;
            }
            usleep($micro_seconds);
            $count++;
        }
        echo $state;
    }

    function getRefNo($id) {
        $objDocuments = $this->getObject('dbdocuments', 'wicid');
        $refNo = $objDocuments->getRefNo($id);

        return $refNo;
    }

    function getFullRefNo($id) {
        $objDocuments = $this->getObject('dbdocuments', 'wicid');
        $refNo = $objDocuments->getRefFullNo($id);
        return $refNo;
    }

    function listdir($dir='.') {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array();
        $this->listdiraux($dir, $files);

        return $files;
    }

    function listdiraux($dir, &$files) {
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filepath = $dir == '.' ? $file : $dir . '/' . $file;
            if (is_link($filepath))
                continue;
            if (is_dir($filepath)) {
                $cfile = substr($filepath, strlen($this->baseDir));                
                if ($this->folderPermissions->isValidFolder($cfile)) {
                    $files[] = $filepath;
                }
                $this->listdiraux($filepath, $files);
            }
        }        
        closedir($handle);
    }
    function getTree($treeType='dhtml', $selected='', $treeMode='side', $action='') {
        $baseFolder = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
        $folders = $this->listdir($baseFolder);
        
        $icon = "";
        $expandedIcon = "";
        $cssClass = "";
        $defaultIndex = 0;
        sort($folders, SORT_LOCALE_STRING);
        if ($selected == '') {
            $selected = $folders[$defaultIndex];
        }
        $baseFolderId = "0";
        $objfolders = $this->getObject('dbfolderpermissions');
        
        //Add topics node
        if ($treeType == 'htmldropdown') {
            $allFilesNode = new treenode(array('text' => $this->modeLabel . 's', 'link' => $baseFolderId));
        } else {
            $allFilesNode = new treenode(array('text' => $this->modeLabel . 's', 'link' => $this->uri(array('action' => 'viewfolder', 'folder' => $baseFolderId)), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
        }
        $documents = $this->getObject('dbdocuments');
        $count = $documents->getUnapprovedDocsCount();
        if ($treeMode == 'side') {
            $unapprovedDocs = "$count New documents";
            if ($selected == 'unapproved') {
                $unapprovedDocs = '<strong>' . $unapprovedDocs . '</strong>';
                $cssClass = 'confirm';
            } else {
                $cssClass = '';
            }

            $newDocsNode = new treenode(array('text' => $unapprovedDocs, 'link' => $this->uri(array('action' => 'unapproveddocs', 'folder' => $baseFolderId)), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
            
            $count = $documents->getRejectedDocsCount();
            $rejectedDocs = "$count Rejected documents";
            if ($selected == 'rejecteddocuments') {
                $rejectedDocs = '<strong>' . $rejectedDocs . '</strong>';
                $cssClass = 'confirm';
            } else {
                $cssClass = '';
            }
            $rejectedDocsNode = new treenode(array('text' => $rejectedDocs, 'link' => $this->uri(array('action' => 'rejecteddocuments', 'folder' => $baseFolderId)), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));

            if ($treeType != 'htmldropdown') {
                $allFilesNode->addItem($newDocsNode);
                $allFilesNode->addItem($rejectedDocsNode);
            }
        }
//Create a new tree
        $menu = new treemenu();

        $icon = 'folder.gif';
        $expandedIcon = 'folder-expanded.gif';
        $refArray = array();
        $refArray[$baseFolder] = & $allFilesNode;

        if (count($folders) > 0) {
            foreach ($folders as $folder) {
                $folderText = basename($folder);
                $cfile = substr($folder, strlen($this->baseDir));

                $folderShortText = substr(basename($folder), 0, 200) . '...';

                if ($folder == $selected) {
                    $folderText = '<strong>' . $folderText . '</strong>';
                    $cssClass = 'confirm';
                } else {
                    $cssClass = '';
                }
                if ($treeType == 'htmldropdown') {
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $cfile, 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                } else {
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $this->uri(array('action' => 'viewfolder', 'folder' => $cfile)), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                }
                $parent = dirname($folder);

                //echo $folder['folderpath'].' - '.$parent.'<br />';
                if (array_key_exists($parent, $refArray)) {
                    $refArray[dirname($folder)]->addItem($node);
                }
                $refArray[$folder] = & $node;
                //var_dump($refArray);exit;
            }
            
        }

        $menu->addItem($allFilesNode);


        if ($treeType == 'htmldropdown') {
            $treeMenu = &new htmldropdown($menu, array('inputName' => 'parentfolder', 'id' => 'input_parentfolder', 'selected' => $selected));
        } else {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
            $this->setVar('pageSuppressXML', TRUE);
            $objSkin = & $this->getObject('skin', 'skin');
            $treeMenu = &new dhtml($menu, array('images' => 'skins/_common/icons/tree', 'defaultClass' => 'treeMenuDefault'));
        }

        return $treeMenu->getMenu();
    }


    function getManageTree($treeType='dhtml', $selected='', $treeMode='side', $action='') {
        $baseFolder = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
        $folders = $this->listdir($baseFolder);
        $icon = "";
        $expandedIcon = "";
        $cssClass = "";
        $defaultIndex = 0;
        sort($folders, SORT_LOCALE_STRING);
        if ($selected == '') {
            $selected = $folders[$defaultIndex];
        }
        $baseFolderId = "0";
        $objfolders = $this->getObject('dbfolderpermissions');
        //Add manage topics node
        if ($treeType == 'htmldropdown') {
            $manageNode = new treenode(array('text' => $this->objLanguage->languageText('mod_wicid_managetopic', 'wicid', "Manage Topics"), 'link' => ""));
        } else {
            $manageNode = new treenode(array('text' => $this->objLanguage->languageText('mod_wicid_managetopic', 'wicid', "Manage Topics"), 'link' => "", 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
        }
        if ($treeMode == 'side') {
            $createfolders = $this->objLanguage->languageText('mod_wicid_addtopic', 'wicid', "Add Topic");
            if ($selected == 'addfolder') {
                $createfolders = '<strong>' . $createfolders . '</strong>';
                $cssClass = 'confirm';
            } else {
                $cssClass = '';
            }

            $addfolderNode = new treenode(array('text' => $createfolders, 'link' => $this->uri(array('action' => 'addfolder', 'folder' => $baseFolderId)), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
            $removefolder = $this->objLanguage->languageText('mod_wicid_deletetopic', 'wicid', "Delete Topic");
            if ($selected == 'removefolder') {
                $removefolder = '<strong>' . $removefolder . '</strong>';
                $cssClass = 'confirm';
            } else {
                $cssClass = '';
            }
            $delfolderNode = new treenode(array('text' => $removefolder, 'link' => $this->uri(array('action' => 'removefolder', 'folder' => $baseFolderId)), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));

            if ($treeType != 'htmldropdown') {
                $manageNode->addItem($addfolderNode);
                $manageNode->addItem($delfolderNode);
            }
        }
        //Create a new tree
        $menu = new treemenu();

        $icon = 'folder.gif';
        $expandedIcon = 'folder-expanded.gif';
        
        $menu->addItem($manageNode);

        //$menu->addItem($allFilesNode);

        
        if ($treeType == 'htmldropdown') {
            $treeMenu = &new htmldropdown($menu, array('inputName' => 'parentfolder', 'id' => 'input_parentfolder', 'selected' => $selected));
        } else {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
            $this->setVar('pageSuppressXML', TRUE);
            $objSkin = & $this->getObject('skin', 'skin');
            $treeMenu = &new dhtml($menu, array('images' => 'skins/_common/icons/tree', 'defaultClass' => 'treeMenuDefault'));
        }

        return $treeMenu->getMenu();
    }

    
    /*
     * Function to generate a form that allows one to add a wicid folder
     * @param string name value of text input box
     * @return form object
     */

    function showCreateFolderForm($name='') {
        $form = new form('createdepartment', $this->uri(array('action' => 'createfolder')));
        $textinput = new textinput('foldername');
        $textinput->value = $name;

        $label = new label('Name of ' . $this->modeLabel . ': ', 'input_parentfolder');
        $form->addToForm("<br/>Create in " . $this->getTree('htmldropdown'));
        $form->addToForm(' &nbsp; ' . $label->show() . $textinput->show());
        $form->addToForm(' <br /><span id="spanfoldermessage"></span><br /><br />');

        $button = new button('create', 'Create ' . $this->modeLabel);
        $button->cssId = 'savebutton';
        $button->setToSubmit();

        $form->addToForm('<br/>' . $button->show());

        $fs = new fieldset();
        $fs->setLegend($this->modeLabel);
        $fs->addContent($form->show());
        return $fs->show();
    }
    /*
     * Function to generate a form that allows one to delete a wicid folder/topic
     * @param string name value of text input box
     * @return form object
     */

    function showDeleteFolderForm($name='', $message=Null) {
        $form = new form('deletetopic', $this->uri(array('action' => 'deletetopic')));

        $form->addToForm("<br/>".$this->objLanguage->languageText('mod_wicid_selectdeletetopic', 'wicid', "Select the topic you want to delete")." ". $this->getTree('htmldropdown'));

        $button = new button('submit', $this->objLanguage->languageText('mod_wicid_deletetopic', 'wicid', "Delete a topic"));
        $button->cssId = 'savebutton';
        $button->setToSubmit();
        //Show error message if any
        $form->addToForm('<br/>' . $message);
        //Render submit button
        $form->addToForm('<br/>' . $button->show());
        //Create fieldset to hold form
        $fs = new fieldset();
        $fs->setLegend($this->objLanguage->languageText('mod_wicid_deletetopic', 'wicid', "Delete a topic"));
        $fs->addContent($form->show());
        return $fs->show();
    }
}
?>