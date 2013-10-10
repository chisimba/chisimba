<?php

/**
 * Methods which intergrates the Turnitin API
 * into the Chisimba framework
 *
 * This module requires a valid Turnitin account/license which can
 * purhase at http://www.turnitin.com
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
 * @category  Chisimba
 * @package   turnitin
 * @author    Wesley Nitsckie
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to supply an easy API for use from this module or even other modules.
 * @author Wesley Nitsckie
 * @package turnitin
 */
class utilities extends object {

    /**
     * The constructor
     *
     */
    public function init() {
        try {

            $this->objTOps = $this->getObject('turnitinops');
            $this->objUser = $this->getObject('user', 'security');
            $this->objDBContext = $this->getObject('dbcontext', 'context');
            $this->objForms = $this->getObject('forms');
            $this->objTAssDB = $this->getObject('turnitindbass');
            $this->objEmails = $this->getObject('turnitinemails');
            $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            // Supressing Prototype and Setting jQuery Version with Template Variables
            $this->setVar('SUPPRESS_PROTOTYPE', true); //Can't stop prototype in the public space as this might impact blocks
            //$this->setVar('SUPPRESS_JQUERY', true);
            //$this->setVar('JQUERY_VERSION', '1.3.2');
            $this->loadClass('link', 'htmlelements');
            $this->userSessionID = "";
            $this->objSubmitted = $this->getObject("turnitinsubmittedassignments");
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }

    public function formatScore($recs) {
        if (!$recs) {
            return false;
        }

        $cnt = 0;
        foreach ($recs as $rec) {
            $assScore = $this->getFullScoreReport($rec['submissionid'], $rec);

            $report = "";
            if (is_array($assScore)) {
                $report .='Student Paper Overlap:' . $assScore['student_paper_overlap'] . '<br/>';
                $report .='Web Overlap:' . $assScore['web_overlap'] . '<br/>';
                $report .='Originality Score:' . $assScore['originalityscore'] . '<br/>';
                $report .='Publication Overlap:' . $assScore['publication_overlap'] . '<br/>';
            }

            $cnt++;
        }

        $arr['totalCount'] = $cnt;
        $arr['report'] = $report;

        return json_encode($arr);
    }

    public function formmatStudentAssessments($recs) {
        if (!$recs) {
            return false;
        }

        $cnt = 0;
        foreach ($recs as $rec) {
            $assScore = $this->getScore($rec['submissionid'], $rec);

            $score = "";
            $objectid = "";
            if (is_array($assScore)) {
                $score = $assScore['score'];
                $objectid = $assScore['objectid'];
                $userid = $assScore['userid'];
            }
            $assRec[] = array("title" => $rec['title'],
                "score" => $score,
                "duedate" => $rec['duedate'],
                "datestart" => $rec['datestart'],
                "objectid" => $objectid,
                "assid" => $rec['duedate'],
                "userid" => $userid,
                "contextcode" => $rec['contextcode'],
                "instructoremail" => $rec['instructoremail'],
                "assid" => $rec['assid'],
                "submitted" => $rec['submitted'],
                "resubmit" => $rec['resubmit'],
                "instructions" => $rec['instructions']
            );
            $cnt++;
        }

        $arr['totalCount'] = $cnt;
        $arr['assignments'] = $assRec;

        return json_encode($arr);
    }

    /**
     * Format the submissions
     *
     * @return array
     */
    public function formatSubmissions($contextcode) {
        //$arr = array();
        $subList = $this->objTOps->getSubmissions(array_merge(
                                $this->getUserParams(),
                                $this->getClassParams(),
                                $this->getAssessmentParams()), $contextcode);
        if ($subList['code'] == 72) {
            //loop the array
            $i = 0;
            $totalcnt = 0;
            if (count($subList) < 1) {
                $arr['totalCount'] = "0";
                $arr['submissions'] = array();
                return json_encode($arr);
            }
            foreach ($subList['object'] as $rec) {
                $submissions[$i] = $this->formatSingleSubmission($rec);
                $i++;
                $totalcnt++;
            }

            $arr['totalCount'] = (string) $totalcnt;
            $arr['submissions'] = $submissions;
            return json_encode($arr);
        } else {
            $arr['totalCount'] = "0";
            $arr['submissions'] = array();
            return json_encode($arr);
        }
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $object
     * @return unknown
     */
    public function formatSingleSubmission($object) {
        //print $object->firstname;
        $title = (string) $object->title;
        $filename = $this->objSubmitted->getFileName((int) $object->objectID);
        $userid = $this->objSubmitted->getUserId((int) $object->objectID);
        $downloadLink = new link($this->uri(array('action' => 'downloadfile', 'filename' => $filename, "userid" => $userid)));
        $downloadLink->link = $title;
        return array("username" => (string) $object->userid,
            "firstname" => (string) $object->firstname,
            "lastname" => (string) $object->lastname,
            "title" => $downloadLink->show(),
            "score" => (string) $object->overlap,
            "objectid" => (int) $object->objectID,
            "userid" => (int) $object->userid,
            "dateposted" => (string) $object->date_submitted
        );
    }

    /**
     * Call the correct template
     *
     * @return unknown
     */
    public function userTemplate() {


        $this->setVar('errorMessage', '');
        $objContextGroups = $this->getObject('managegroups', 'contextgroups');
        if ($this->objUser->isAdmin() || $objContextGroups->isContextLecturer()) {

            //create user on TII if he does not exist
            $res = $this->objTOps->createLecturer($this->getUserParams());

            //var_dump($res);die;
            //take the userid that TII returned and save it to the database
            //create the course on TII if it does not exist and
            //make this user the instructor
            //insert the instructor email
            $this->objEmails->addEmail($this->objDBContext->getContextCode(), $this->objUser->email());

            $res = $this->objTOps->createClass(array_merge(
                                    $this->getUserParams(),
                                    $this->getClassParams(),
                                    array('instructoremail' => $this->objUser->email())));


            if (is_array($res)) {
                if (!$this->objTOps->isSuccess(2, $res['code'])) {
                    $this->setVar('errorMessage', $res['message']);
                    error_log($res['message']);
                }
            }
            return "lectmain_tpl.php";
        } else {

            $res = $this->objTOps->createStudent($this->getUserParams());

            //var_dump($res);die;
            $res2 = $this->objTOps->joinClass(array_merge(
                                    $this->getUserParams(),
                                    $this->getClassParams()));


            //print $res2;
            return "student_tpl.php";
        }
    }

    function getUserSession() {
        $res = $this->objTOps->loginSession(array_merge($this->getUserParams(), $this->getClassParams()));
        if ($res['code'] == 92) {
            return (string) $res['xmlobject']->sessionid;
        } else {
            return false;
        }
    }

    function getUserSession2() {
        //var_dump($_SESSION);
        // $this->setSession('tii_session', "");
        if (!$this->getSession('tii_session')) {

            //try to login
            $res = $this->objTOps->loginSession(array_merge($this->getUserParams(), $this->getClassParams()));

            // var_dump((string)$res['xmlobject']->sessionid);
            //process the result
            if ($res['code'] == 92) {
                $this->setSession('tii_session', (string) $res['xmlobject']->sessionid);
                return $this->getSession('tii_session');
            } else {
                print '4';
                error_log("TURNITIN LOGIN FAILED :::::: code:" . $res['xmlobject']->rcode . " :::::: " . $res['xmlobject']->rmessage);
            }
        } else {

            return $this->getSession('tii_session');
        }
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $assigments
     * @return unknown
     */
    public function formatJsonAssignments($assigments) {

        //get extra info from turnitin
        //var_dump($assigments);
        if ($assigments) {
            $index = 0;
            foreach ($assigments as $ass) {
                $count = $this->objSubmitted->getTotalSubmissions($ass['contextcode'], $ass['title']);
                $ass['submissions'] = $count;
                // array_push(array('submissions' => $count),$assigments[$ass] );
                //  print_r($ass);
                $assigments[$index] = $ass;
                $index++;
            }


            $arr['totalCount'] = strval(count($assigments));
            $arr['assignments'] = $assigments;
        } else {
            return false;
        }

        return trim(json_encode($arr));
    }

    /**
     * Method to disable the login
     * feature
     */
    public function requiresLogin() {
        return TRUE;
    }

    /**
     * MEthod to get the user parameters
     *
     * @return array
     */
    public function getUserParams() {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $defaultpass = $objSysConfig->getValue('defaultpass', 'jturnitin');

        $params = array();
        $params['password'] = $defaultpass;
        $params['username'] = $this->objUser->userName();
        $params['firstname'] = $this->objUser->getFirstname();
        $params['lastname'] = $this->objUser->getSurname();
        $params['userid'] = $this->objUser->userid();
        $params['email'] = $this->objUser->email();
        $params['instructoremail'] = $this->getInstructorEmail();


        $objUser = $this->getObject("user", "security");
        if ($objUser->isCourseAdmin($this->objDBContext->getContextCode())) {
            if ($params['instructoremail'] != $objUser->email() && $params['instructoremail'] != '') {
                $user = $this->objSubmitted->getUser($params['instructoremail']);
                $params['firstname'] = $user['firstname'];
                $params['lastname'] = $user['surname'];
                $params['email'] = $params['instructoremail'];
            }
        }


        return $params;
    }

    public function getUserParamsUsingId($userid) {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $defaultpass = $objSysConfig->getValue('defaultpass', 'jturnitin');

        $params = array();
        $params['password'] = $defaultpass;
        $params['userid'] = $userid;
        $params['username'] = $this->objUser->userName($userid);
        $params['firstname'] = $this->objUser->getFirstname($userid);
        $params['lastname'] = $this->objUser->getSurname($userid);
        $params['email'] = $this->objUser->email($userid);
        $params['instructoremail'] = $this->objUser->email($userid);
        return $params;
    }

    /**
     * MEthod to get the class parameters
     *
     * @return array
     */
    public function getClassParams() {
        $params = array();

        if ($this->objDBContext->isInContext()) {
            $params['classid'] = $this->objDBContext->getContextCode();
            $params['classtitle'] = $this->objDBContext->getTitle();
            $params['classpassword'] = 'classpass';
            $params['instructoremail'] = $this->getInstructorEmail();
            // $params['dtstart']='20100406';
            // $params['dtdue']='20111230';
        }

        return $params;
    }

    /**
     * Get the instructors email
     *
     * @return unknown
     */
    public function getInstructorEmail() {
        $email = $this->objEmails->getEmail($this->objDBContext->getContextCode());

        return $email;
    }

    /**
     * Method to get the assessment parameters
     *
     * @return array
     */
    public function getAssessmentParams() {
        $params = array();

        $params['assignmentid'] = $this->getParam('assignmentid');
        $params['assignmenttitle'] = $this->getParam('title');
        $params['assignmentinstruct'] = strip_tags($this->getParam('instructions'));
        $params['assignmentdatestart'] = $this->formatTIIDate($this->getParam('startdt'));
        $params['assignmentdatedue'] = $this->formatTIIDate($this->getParam('duedt'));
        $params['instructoremail'] = $this->getInstructorEmail();

        return $params;
    }

    /**
     * Format date for TII
     *
     * @param string $date
     * @return string
     */
    public function formatTIIDate($date) {
        return str_replace("-", "", $date);
    }

    public function doAddAssignment() {
        $successcodes = array(40, 41, 42, 43);
        $assParams = $this->getAssessmentParams();

        $optionalParams = array(
            "internet_check" => $this->getParam('internet_check'),
            "report_gen_speed" => $this->getParam('report_gen_speed'),
            "exclude_biblio" => $this->getParam('exclude_biblio'),
            "exclude_quoted" => $this->getParam('exclude_quoted'),
            "exclude_value" => $this->getParam('exclude_value'),
            "late_accept_flag" => $this->getParam('late_accept_flag'),
            "submit_papers_to" => $this->getParam('submit_papers_to')
        );



        $xres = $this->objTOps->createAssessment(array_merge(
                                $this->getUserParams(),
                                $this->getClassParams(),
                                $assParams,
                                $optionalParams));
        $res = explode("|", $xres);
        $code = $res[0];
        $message = $res[1];
        if (in_array($code, $successcodes)) {
            //add to local database
            if ($this->objTAssDB->addAssignment($this->objDBContext->getContextCode(), $assParams, $optionalParams)) {
                return json_encode(array('success' => 'true', 'msg' => 'A new assigment entitled <b>"' . $this->getParam('title') . '"</b> was successfully created'));
            } else {
                return json_encode(array('success' => false, 'msg' => 'The assigment was create on Turnitin but an error occurred while inserting the details into the database'));
            }
        } else {

            return json_encode(array('success' => false, 'msg' => "Error: " . $message));
        }
    }

    /**
     * Add an assessment to TII
     *
     * @return unknown
     */
    public function doUpdateAssignment() {
        $successcodes = array(40, 41, 42, 43);
        $assParams = $this->getAssessmentParams();
        
        $optionalParams = array(
            "internet_check" => $this->getParam('internet_check'),
            "report_gen_speed" => $this->getParam('report_gen_speed'),
            "exclude_biblio" => $this->getParam('exclude_biblio'),
            "exclude_quoted" => $this->getParam('exclude_quoted'),
            "exclude_value" => $this->getParam('exclude_value'),
            "late_accept_flag" => $this->getParam('late_accept_flag'),
            "submit_papers_to" => $this->getParam('submit_papers_to')
        );


        error_log(var_export($assParams, true));

        $res = $this->objTOps->updateAssessment(
                        array_merge(
                                $this->getUserParams(),
                                $this->getClassParams(),
                                $assParams,
                                $optionalParams));

        error_log(var_export($res, true));

        if (in_array($res['code'], $successcodes)) {
            $contextcode = $this->objDBContext->getContextCode();


            $this->objTAssDB->updateAssignment($contextcode, $assParams, $optionalParams);
            return json_encode(array('success' => 'true', 'msg' => 'Assigment entitled <b>"' . $this->getParam('title') . '"</b> was successfully edited'));
        } else {
            $message = $res['message'];
            return json_encode(array('success' => false, 'msg' => "Error: " . $message[0]));
        }
    }

    /**
     * Get Submission data
     *
     * @return unknown
     */
    public function getSubmissionInfo() {
        $params = array();
        $assId = $this->getParam('assid');
        if ($assId != "") {
            $rec = $this->objTAssDB->getRow('id', $assId);
            //var_dump($rec);die;
            $params['instructoremail'] = $rec['instructoremail'];
        }
        //$params['papertitle'] = "This is submmitted title";//$this->getParam('papertitle');
        //$params['papertype'] = 2;
        //$params['paperdata'] = $this->getParam("filedata");

        return $params;
    }

    function send_alert($message, $users = array('wesleynitsckie@gmail.com')) {
        $objModCat = $this->getObject('modules', 'modulecatalogue');
        if ($objModCat->checkIfRegistered('im')) {

            // email or SMS code goes here
            include ($this->getResourcePath('XMPPHP/XMPP.php', "im"));
            include ($this->getResourcePath('XMPPHP/XMPPHP_Log.php', "im"));
            //include ('../im/classes/XMPPHP/XMPPHP_Log.php' );

            $jserver = "talk.google.com";
            $jport = "5222";
            $juser = "eteaching2009";
            $jpass = "3t3ach1ng2009";
            $jclient = "ChisimbaIM";
            $jdomain = "gmail.com";

            $conn2 = new XMPPHP_XMPP($jserver, intval($jport), $juser, $jpass, $jclient, $jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR);
            $conn2->connect();
            $conn2->processUntil('session_start');
            foreach ($users as $user) {
                $conn2->message($user, $message);
            }
            $conn2->disconnect();
        }
    }

    public function saveFile($path, $docname) {
        $dir = $this->objSysConfig->getValue('FILES_DIR', 'jturnitin');
        $filepath = $dir . $path . '/' . $this->objUser->userid();
        $filepath = str_replace("//", "/", $filepath);
        $destinationDir = $filepath;
        $objMkDir = $this->getObject('mkdir', 'files');
        $objMkDir->mkdirs($destinationDir);

        $objFileUpload = $this->getObject('turnitinupload');
        $objFileUpload->overWrite = TRUE;
        $objFileUpload->uploadFolder = $destinationDir . '/';
        $result = $objFileUpload->doUpload($docname);

        if ($result['success'] == FALSE) {

            return $result['message'];
        } else {
            $filename = $result['clonename'];
            $ext = $result['extension'];
            $parent = $result['filename'];

            $file = $dir . '/' . $this->objUser->userid() . '/' . $docname . '.' . $ext;
            if (is_file($file)) {
                @chmod($file, 0777);
            }
            $res = array("filename" => $docname . '.' . $ext, "file" => $file);
            return $res;
        }
    }

    public function doFileUpload() {
        //var_dump($_FILES);
        $allowedExtensions = array("txt", "csv", "htm", "html", "xml",
            "css", "docx", "doc", "xls", "rtf", "ppt", "pdf", "swf", "flv", "avi", "odt",
            "wmv", "mov", "jpg", "jpeg", "gif", "png");


        $file = $_FILES["file"];
        $allowedSize = $file['size'];
        if ($file["error"] > 0) {
            return json_encode(array('success' => 'false', 'msg' => 'Error: ' . $this->fileUploadErrorMessage($_FILES["file"]["error"])));
        } else {
            //if (!in_array(end(explode(".", strtolower($file['name']))),$allowedExtensions)) {}
            $extension = end(explode(".", strtolower($file['name'])));
            //	var_dump($extension);
            $filename = $file['tmp_name'];
            switch ($extension) {
                case 'pdf':
                    $content = shell_exec('pdftotext ' . $filename . ' -');
                    break;
                case 'doc':
                case 'docx':
                    $content = shell_exec('antiword ' . $filename . ' -');
                    break;
                case 'odt':
                    $content = shell_exec('odt2txt ' . $filename);
                    break;
                default:
                    $content = file_get_contents($filename, true);
                    break;
            }
            //hand the content off to TII
            //var_dump($content);
            //$msg = "<br /><br />Upload: " . $file["name"] . "<br />";
            //$msg .= "Type: " . $file["type"] . "<br />";
            //$msg .= "Size: " . ($file["size"] / 1024) . " Kb<br />";
            $msg = $content;
            //$content = shell_exec('pdftotext '.$filename.' -');
            return '{"success":"true", "msg": "' . htmlentities($msg) . '"}';
        }
    }

    /**
     * Error codes explained
     *
     * @param unknown_type $error_code
     * @return unknown
     */
    function fileUploadErrorMessage($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    public function isTIIavailable() {
        $result = $this->objTOps->APILogin(array('firstname' => $this->objUser->getFirstname(),
                    'lastname' => $this->objUser->getSurname(),
                    'password' => '123456', //make this a config variable
                    'email' => $this->objUser->email()
                ));

        return true;
    }

    public function getScore($submissionId, $assRec) {
        $check = $this->objTOps->checkForSubmission(array_merge(
                                $this->getUserParams(),
                                $this->getClassParams(),
                                array('assignmenttitle' => $assRec['title'], 'instructoremail' => $assRec['instructoremail'])
                ));



        error_log(var_export($check, true));
        //check if the user has submitted an assessment
        if ($check['xmlobject']->objectID != 0) {
            //get the score
            $scoreObject = $this->objTOps->getScore(array_merge(
                                    $this->getUserParams(),
                                    $this->getClassParams(),
                                    array('assignmenttitle' => $assRec['title'],
                                        'objectid' => (string) $check['xmlobject']->objectID
                                    )
                    ));
            $arr = array('score' => (int) $scoreObject['xmlobject']->originalityscore,
                'objectid' => (string) $check['xmlobject']->objectID,
                'userid' => (string) $scoreObject['xmlobject']->userid);

            return $arr;
        } else {
            return "";
        }
        print $check;
        return "";
    }

    public function getFullScoreReport($submissionId, $assRec) {
        $check = $this->objTOps->checkForSubmission(array_merge(
                                $this->getUserParams(),
                                $this->getClassParams(),
                                array('assignmenttitle' => $assRec['title'], 'instructoremail' => $assRec['instructoremail'])
                ));


        error_log(var_export($check, true));
        //check if the user has submitted an assessment
        if ($check['xmlobject']->objectID != 0) {
            //get the score
            $scoreObject = $this->objTOps->getScore(array_merge(
                                    $this->getUserParams(),
                                    $this->getClassParams(),
                                    array('assignmenttitle' => $assRec['title'],
                                        'objectid' => (string) $check['xmlobject']->objectID)
                    ));
            $arr = array(
                'student_paper_overlap' => (int) $scoreObject['xmlobject']->student_paper_overlap,
                'web_overlap' => (int) $scoreObject['xmlobject']->web_overlap,
                'originalityscore' => (int) $scoreObject['xmlobject']->originalityscore,
                'publication_overlap' => (int) $scoreObject['xmlobject']->publication_overlap,
                'objectid' => (string) $check['xmlobject']->objectID);

            return $arr;
        } else {
            return "";
        }
        print $check;
        return "";
    }

    /**
     * allows the user to donwload the selected file
     * @param <type> $filename
     */
    function downloadFile($filename, $userid) {
        //check if user has access to the parent folder before accessing it

        $download_path = $this->objSysConfig->getValue('FILES_DIR', 'jturnitin');
        // Detect missing filename
        if (!$filename)
            die("I'm sorry, you must specify a file name to download.");

        // Make sure we can't download files above the current directory location.
        if (eregi("\.\.", $filename))
            die("I'm sorry, you may not download that file.");
        $file = str_replace("..", "", $filename);

        // Make sure we can't download .ht control files.
        if (eregi("\.ht.+", $filename))
            die("I'm sorry, you may not download that file.");

        // Combine the download path and the filename to create the full path to the file.
        $file = "$download_path/$userid/$filename";

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
        header("Content-Disposition: attachment;filename=" . $this->getFileName($filename));
        header('Pragma: no-cache');
        header('Expires: 0');

        // Send the file contents.
        readfile($file);
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

}
