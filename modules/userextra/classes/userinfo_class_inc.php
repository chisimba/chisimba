<?php
/**
 * This script tightly intergrates course creating into the wits elearning.
 * For example, if a student signs into the system the first time, the are
 * auto-enrolled in a course, if it exists. A lecturer is auto given lecturer
 * permissions
 */
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

class userinfo extends object {
    function init() {
        $this->objLanguage=$this->getObject('language','language');
        $this->objUser=$this->getObject('user','security');
        $this->objGroups = $this->getObject('groupadminmodel','groupadmin');
        $this->objOps = $this->getObject ( 'groupops','groupadmin');
        $this->dbextra=$this->getObject('dbuserextra');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->oblAltConfig=$this->getObject('altconfig','config');
    }

    /**
     * this gives new users appropriate permissions and sends appropriate
     * email notifications
     * @param <type> $info
     * @param <type> $groupidarr
     * @param <type> $receivers
     */

    function process($info,$groupidarr,$receivers) {

        $userid=$info['userid'];
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $staffurl=$objSysConfig->getValue('STAFFURL', 'userextra');
        $studenturl=$objSysConfig->getValue('STUDENTURL', 'userextra');
        $studentunitsurl=$objSysConfig->getValue('STUDENTUNITSURL', 'userextra');

        $susername=$objSysConfig->getValue('SUSERNAME', 'userextra');
        $spassword=$objSysConfig->getValue('SPASSWORD', 'userextra');

        $staffurl.="/$username";
        $studenturl.="/".strtoupper($info['username']);
        $studentunitsurl.="/".strtoupper($info['username']);

        $names=$info['firstname'].', '.$info['surname'];
        //first test to see if user is staff

        $ch=curl_init($staffurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$susername:$spassword");
        $r=curl_exec($ch);
        curl_close($ch);
        $jsonArray=json_decode($r);
        $employeeCategory= $jsonArray->objects[0]->employeeCategory;
        if($employeeCategory == 'ACA') {
            $groupid=$this->objGroups->getId('Lecturers');
            $puid=$this->dbextra->getUserPuid($userid);
            $res=$this->objGroups->addGroupUser($groupid, $puid);
            $subject=$objSysConfig->getValue('LECTURER_PERM_MESSAGE_SUBJECT', 'userextra');
            $subject=str_replace('{sitename}', $this->oblAltConfig->getSiteName(), $subject);
            $body=$objSysConfig->getValue('LECTURER_PERM_MESSAGE_BODY', 'userextra');
            $body=str_replace('{names}', $names, $body);
            $body=str_replace('{sitename}', $this->oblAltConfig->getSiteName(), $body);

            $this->sendMailNotification($names,$info['email'],$subject,$body,$groupidarr,$receivers);
        }else {
            $subject=$objSysConfig->getValue('NEWUSER_PERM_MESSAGE_SUBJECT', 'userextra');

            $subject=str_replace('{sitename}', $this->oblAltConfig->getSiteName(), $subject);

            $body=$objSysConfig->getValue('NEWUSER_PERM_MESSAGE_BODY', 'userextra');

            $body=str_replace('{names}', $names, $body);
            $body=str_replace('{sitename}', $this->oblAltConfig->getSiteName(), $body);

            $emaildomain=$objSysConfig->getValue('EMAIL_DOMAIN', 'userextra');
            $email  = $info ['email'];
            $domain = strstr($email, '@');

            if($domain == '@'.$emaildomain) {
                $this->sendMailNotification($names,$email,$subject,$body,$groupidarr,$receivers);

            }

        }

        $ch=curl_init($studenturl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$susername:$spassword");
        $r=curl_exec($ch);
        curl_close($ch);
        $jsonArray=json_decode($r);
        $studentNumber= $jsonArray->objects[0]->studentNumber;

        if($studentNumber) {
            $groupid=$this->objGroups->getId('Students');
            $puid=$this->dbextra->getUserPuid($userid);
            $res=$this->objGroups->addGroupUser($groupid, $puid);
            $this->addStudentToCourse($studentunitsurl,$info['userid']);

        }

    }

    /**
     * sends out email notifications
     * @param <type> $names
     * @param <type> $email
     * @param <type> $subject
     * @param <type> $body
     * @param <type> $groupidarr
     * @param <type> $receivers
     */
    function sendMailNotification($names,$email,$subject,$body,$groupidarr,$receivers) {

        $groupId=$groupidarr[0]['group_id'];
        $receivers[]=array("emailaddress"=>$email);
        //// $this->objGroupOps->getUsersInGroup($groupid);

        $objMailer = $this->getObject('mailer', 'mail');

        foreach($receivers as $receiver) {

            $objMailer->setValue('to',array($receiver['emailaddress']));
            $objMailer->setValue('from', $this->oblAltConfig->getsiteEmail());
            $objMailer->setValue('fromName', $this->oblAltConfig->getSiteName());
            $objMailer->setValue('subject', $subject);
            $objMailer->setValue('body', strip_tags($body));
            $objMailer->setValue('AltBody', strip_tags($body));
            $objMailer->send();


        }

    }
    function addStudentToCourse($studentunitsurl,$userid) {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $susername=$objSysConfig->getValue('SUSERNAME', 'userextra');
        $spassword=$objSysConfig->getValue('SPASSWORD', 'userextra');
        $puid=$this->dbextra->getUserPuid($userid);
        $ch=curl_init($studentunitsurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$susername:$spassword");
        $r=curl_exec($ch);
        curl_close($ch);
        $studentinfo=$r;
        
        $jsonArray=json_decode($studentinfo);
        $index=0;
        $groups =  $this->objGroups->getTopLevelGroups();
        $studentGroupId="";
        if($jsonArray->objects[0]->unitCode) {

            foreach($jsonArray as $row) {
                $unitCode= $jsonArray->objects[$index]->unitCode;

                if($unitCode) {

                    $contextGroupId = $this->objGroups->getId($unitCode);

                    $subGroups = $this->objGroups->getSubgroups($contextGroupId);

                    if(is_array($subGroups)) {

                        foreach($subGroups[0] as $subGroup) {

                            $groupName =  $this->objOps->formatGroupName($subGroup['group_define_name']);
                            switch ($groupName) {
                                case 'Students':
                                    $studentGroupId = $this->objGroups->getId($subGroup['group_define_name']);
                                    break;
                            }

                        }
                    }else {

                    }

                    $this->objGroups->addGroupUser($studentGroupId, $puid);
                    $index++;
                }
            }
        }

    }

}
?>
