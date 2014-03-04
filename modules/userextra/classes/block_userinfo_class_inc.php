<?php

// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class block_userinfo extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass("link", "htmlelements");

        $this->title = $this->objLanguage->languageText('mod_userextra_title', 'userextra', 'User Activation');
        $this->objUser = $this->getObject('user', 'security');
        $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objOps = $this->getObject('groupops', 'groupadmin');
        $this->dbextra = $this->getObject('dbuserextra');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objAltConfig = $this->getObject('altconfig', 'config');

        $modPath = $this->objAltConfig->getModulePath();
        $replacewith = "";
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $resourcePath = str_replace($docRoot, $replacewith, $modPath);
        $protocol = "http";
        if ($this->is_https()) {
            $protocol = "https";
        }
        $this->codebase = "$protocol://" . $_SERVER['HTTP_HOST'] . "/" . $resourcePath . '/userextra/resources/images/';

        $this->okicon = '<img src="' . $this->codebase . '/accept.png">';
        $this->noicon = '<img src="' . $this->codebase . '/cancel.png">';
    }

    function is_https() {
        return strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? true : false;
    }

    function show() {
        $popupjs = '<script language="JavaScript" src="' . $this->getResourceUri('js/popup.js') . '" type="text/javascript"></script>';

        $activated = '<a href="#" OnClick="showActivationMessage();return false;">' . $this->objLanguage->languageText('mod_userextra_activated', 'userextra', 'Activated') . '</a>';
        $notactivated = '<a href="#" OnClick="showActivationMessage();return false;">' . $this->objLanguage->languageText('mod_userextra_notactivated', 'userextra', 'Not activated') . '</a>';
        $userid = $this->objUser->userid();


        $help1 = $this->objLanguage->languageText('mod_userextra_help', 'userextra', "<h3>STAFF</h3>If you signed in as a lecturer for the first time, please click on the activation link to get lecturer permissions.");
        $help2 = $this->objLanguage->languageText('mod_userextra_help', 'userextra', "<h3>STUDENTS</h3>If you signed in as a student and cannot see the courses you registered for in the list below, click on the activation link to synchronize with the oracle course system.");
        $link = new link($this->uri(array("action" => activate)));
        $link->link = $this->objLanguage->languageText('mod_userextra_synchronize', 'userextra', "Click here to synchronize.");
        /*  if($this->dbextra->isUserActivated($userid)) {
          return $this->okicon.' '.$activated;
          }else {
          return $popupjs.$this->noicon.'<font sytle="color:red;"><b>'.$notactivated.'</b></font>';
          } */
        return $help1 . '&nbsp;' . $link->show() . '' . $help2 . '&nbsp;' . $link->show() . '';
    }

    function activate() {
        $objActivate = $this->getObject('dbuseractivation');
        $username = $this->objUser->username();

        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $staffurl = $objSysConfig->getValue('STAFFURL', 'userextra');
        $studenturl = $objSysConfig->getValue('STUDENTURL', 'userextra');
        $studentunitsurl = $objSysConfig->getValue('STUDENTUNITSURL', 'userextra');
        $susername = $objSysConfig->getValue('SUSERNAME', 'userextra');
        $spassword = $objSysConfig->getValue('SPASSWORD', 'userextra');
        $displaymessage = $objSysConfig->getValue('WELCOME_MESSAGE', 'userextra');
        $staffurl.="/$username";
        $studenturl.="/$username";
        $studentunitsurl.="/$username";
        //first test to see if user is staff
        //if(!$this->getSession('academicstatus')) {
        $ch = curl_init($staffurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$susername:$spassword");

        $r = curl_exec($ch);
        curl_close($ch);
        $jsonArray = json_decode($r);

        $employeeCategory = $jsonArray->objects[0]->employeeCategory;
        if ($employeeCategory == 'ACA') {
            $groupid = $this->objGroups->getId('Lecturers');
            $userid = $this->objUser->userid();
            $puid = $this->dbextra->getUserPuid($userid);
            $res = $this->objGroups->addGroupUser($groupid, $puid);
            $this->setSession("academicstatus", "true");
        }
        // }
        //test if student
        //if(!$this->getSession('studentstatus')) {
        $ch = curl_init($studenturl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$susername:$spassword");
        $r = curl_exec($ch);
        curl_close($ch);
        $jsonArray = json_decode($r);

        $studentNumber = $jsonArray->objects[0]->studentNumber;


        if ($studentNumber) {
            $groupid = $this->objGroups->getId('Students');
            $userid = $this->objUser->userid();
            $puid = $this->dbextra->getUserPuid($userid);
            $res = $this->objGroups->addGroupUser($groupid, $puid);
            $this->setSession("studentstatus", "true");
            $displaymessage.= $this->addStudentToCourse($studentunitsurl);
        }
        //}
        //return  $displaymessage;
        $objActivate->addUser($this->objUser->userid());
    }

    function addStudentToCourse($studentunitsurl) {
        $username = "0714760H";
        $this->objUser->username();
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

        $susername = $objSysConfig->getValue('SUSERNAME', 'userextra');
        $spassword = $objSysConfig->getValue('SPASSWORD', 'userextra');
        $userid = $this->objUser->userid();
        $puid = $this->dbextra->getUserPuid($userid);
        $ch = curl_init($studentunitsurl);
        curl_setopt($ch, CURLOPT_USERPWD, "$susername:$spassword");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $r = curl_exec($ch);
        curl_close($ch);
        $myFile = "/tmp/userextra.txt";
        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh, $r);
        fclose($fh);
        $studentinfo = $r;
        $jsonArray = json_decode($studentinfo);
        $index = 0;
        $groups = $this->objGroups->getTopLevelGroups();
        $studentGroupId = "";
        $unitCodes = " [";


        if ($jsonArray->objects[0]->unitCode) {

            foreach ($jsonArray as $row) {
                $unitCode = $jsonArray->objects[$index]->unitCode;
                if ($unitCode) {
                    $unitCodes.=$unitCode . " ";

                    $contextGroupId = $this->objGroups->getId($unitCode);

                    $subGroups = $this->objGroups->getSubgroups($contextGroupId);

                    if (is_array($subGroups)) {

                        foreach ($subGroups[0] as $subGroup) {

                            $groupName = $this->objOps->formatGroupName($subGroup['group_define_name']);
                            switch ($groupName) {
                                case 'Students':
                                    $studentGroupId = $this->objGroups->getId($subGroup['group_define_name']);
                                    $unitCodes.=$this->okicon . " ";
                                    break;
                            }
                        }
                    } else {
                        $unitCodes.=$this->noicon . " ";
                    }
                    $this->objGroups->addGroupUser($studentGroupId, $puid);
                    $index++;
                }
            }
        }
        $unitCodes.="]";
        /* $myFile = "/tmp/debugx.txt";
          $fh = fopen($myFile, 'w') or die("can't open file");
          $stringData = $unitCodes;
          fwrite($fh, $stringData);
          fclose($fh);
          die(); */
        return $unitCodes;
    }

    function determinIfAcademic() {
        
    }

}
?>
