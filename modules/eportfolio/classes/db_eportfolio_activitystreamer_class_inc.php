<?php

/**
 * Class that records the eportfolio pages a user has visited/created/modified.
 *
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
 * @package   eportfolio
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright @2010 University of the Witwatersrand
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: db_contextcontent_titles_class_inc.php 11385 2008-11-07 00:52:41Z charlvn $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class that records the eportfolio pages a user has visited/created/modified.
 *
 * It doesn't contain the content of pages, just the index to track which pages
 * are translations of each other.
 *
 * @category  Chisimba
 * @package   eportfolio
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright @2010 University of the Witwatersrand
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */

class db_eportfolio_activitystreamer extends dbtable
{

    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_eportfolio_activitystreamer');
        $this->objUser =& $this->getObject('user', 'security');
        //Load classes
        $this->objFiles = $this->getObject('dbfile','filemanager');        
        // Load Context Object
        $this->objContext = $this->getObject('dbcontext', 'context');            
        //Load Language Object
        $this->objLanguage = &$this->getObject('language', 'language');
        //Fetch and Store Context Code
        $this->contextCode = $this->objContext->getContextCode();
        //Fetch and Store the session Id
        $this->sessionId = session_Id();
        //Load ePortfolio DB Objects
        $this->objDbAddressList = &$this->getObject('dbeportfolio_address', 'eportfolio');
        $this->objDbContactList = &$this->getObject('dbeportfolio_contact', 'eportfolio');
        $this->objDbDemographicsList = &$this->getObject('dbeportfolio_demographics', 'eportfolio');
        $this->objDbActivityList = &$this->getObject('dbeportfolio_activity', 'eportfolio');
        $this->objDbAffiliationList = &$this->getObject('dbeportfolio_affiliation', 'eportfolio');
        $this->objDbTranscriptList = &$this->getObject('dbeportfolio_transcript', 'eportfolio');
        $this->objDbEmailList = &$this->getObject('dbeportfolio_email', 'eportfolio');
        $this->objDbQclList = &$this->getObject('dbeportfolio_qcl', 'eportfolio');
        $this->objDbGoalsList = &$this->getObject('dbeportfolio_goals', 'eportfolio');
        $this->objDbCompetencyList = &$this->getObject('dbeportfolio_competency', 'eportfolio');
        $this->objDbInterestList = &$this->getObject('dbeportfolio_interest', 'eportfolio');
        $this->objDbReflectionList = &$this->getObject('dbeportfolio_reflection', 'eportfolio');
        $this->objDbAssertionList = &$this->getObject('dbeportfolio_assertion', 'eportfolio');
        $this->objDbProductList = &$this->getObject('dbeportfolio_product', 'eportfolio');
    }
    
    /**
     * Method to add a record.
     *
     * @access public
     * @param string $userId User ID
     * @param string $ownerUserId eportfolio owner UserId
     * @param string $groupId eportfolio group ID
     * @param string $sessionId session Id
     * @param string $contextcode Context Code
     * @param string $moduleCode module Code
     * @param string $partName eportfolio part name i.e. identification or goals
     * @param string $recordId eportfolio record id
     * @param string $description Brief description of the activity
     * @param string $sessionendtime session end time
     */
   public function addRecord($userId, $ownerUserId, $groupId, $contextCode, $moduleCode, $partName, $recordId, $description)
    {
        $row = array();
        $row['userid'] = $userId;
        $row['owneruserid'] = $ownerUserId;
        $row['groupid'] = $groupId;
        $row['sessionid'] = $this->sessionId;
        $row['contextcode'] = $contextCode;
        $row['modulecode'] = $moduleCode;
        $row['partname'] = $partName;
        $row['recordid'] = $recordId;
        $row['datecreated'] = strftime('%Y-%m-%d %H:%M:%S', mktime());
        $row['description'] = $description;
        $row['starttime'] = strftime('%Y-%m-%d %H:%M:%S', mktime());
        $row['endtime'] = strftime('%Y-%m-%d %H:%M:%S', mktime());

        return $this->insert($row);
    }

    /**
     * Checks if record exists.
     *
     * @access public
     * @param string $id The activitystreamer id.
     * @return boolean
     */
    public function idExists($id)
    {
        return $this->valueExists('id', $id);
    }
    /**
     * Method to check if record exists according to userId, recordId and sessionId
     *
     * @access public
     * @param string $userId User ID
     * @param string $recordId Record Id
     * @param string $contextCode Context Code
     * @return TRUE|FALSE
     */
    public function recordExists($userId, $recordId, $sessionId)
    {
        $where = "WHERE userid = '$userId' AND recordid = '$recordId' AND sessionid = '$sessionid'";
        $results = $this->getAll($where);
        if (isset($results[0]['id'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Method fetch record data according to userId, recordId and sessionId
     *
     * @access public
     * @param string $userId User ID
     * @param string $recordId Record Id
     * @param string $contextCode Context Code
     * @return Array|FALSE
     */
    public function getRecord($userId, $recordId, $sessionId)
    {
        $where = "WHERE userid = '$userId' AND recordid = '$recordId' AND sessionid = '$sessionid'";
        $results = $this->getAll($where);
        if (isset($results[0]['id'])) {
            return $results[0];
        } else {
            return FALSE;
        }
    }
    /**
     * Method to fetch user groups from groupadmin
     *
     * @access public
     * @param string $permUserId $perm User Id from tbl_perms_perm_users
     * @return TRUE
     */
    public function getAuthUserId($userId)
    {
        $sql = "SELECT pu.perm_user_id FROM tbl_perms_perm_users as pu where pu.auth_user_id='".$userId."'";
        $results = $this->getArray($sql);
        if (isset($results[0])) {
            return $results[0]["perm_user_id"];
        } else {
            return FALSE;
        }
    }
    /**
     * Method to fetch user groups from groupadmin
     *
     * @access public
     * @param string $permUserId $perm User Id from tbl_perms_perm_users
     * @return TRUE
     */
    public function getUserGroups($permUserId)
    {
        $sql = "SELECT gu.perm_user_id, gu.group_id FROM tbl_perms_groupusers as gu where gu.perm_user_id='".$permUserId."'";
        $results = $this->getArray($sql);
        if (isset($results[0])) {
            return $results;
        } else {
            return FALSE;
        }
    }
    /**
     * Method to fetch record
     *
     * @access public
     * @param string $Id ID
     * @return TRUE
     */
    public function getRecordById($Id)
    {
        $where = "WHERE id = '$Id'";
        $results = $this->getAll($where);
        if (isset($results[0])) {
            return $results[0];
        } else {
            return FALSE;
        }
    }
    /**
     * Method to fetch active context members.
     *
     * @access public
     * @return Array
     */
    public function getActiveMembers()
    {
        $getDistinct = "SELECT distinct userid  FROM tbl_eportfolio_activitystreamer";
        $results = $this->getArray($getDistinct);
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }
    /**
     * Method to fetch distinct user sessions.
     *
     * @access public
     * @param string $userId User ID
     * @return Array
     */
    public function getUserSessions($userId)
    {
        $getDistinct = "SELECT distinct sessionid  FROM tbl_eportfolio_activitystreamer WHERE userid = '$userId'";
        $results = $this->getArray($getDistinct);
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results;
        }
    }
    /**
     * Method to fetch all user activities.
     *
     * @access public
     * @param string $userId User ID
     * @return Array
     */
    public function getAllUserActivities($userId)
    {
        $getDistinct = "SELECT * FROM tbl_eportfolio_activitystreamer WHERE userid = '$userId'";
        $results = $this->getArray($getDistinct);
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results;
        }
    }
    /**
     * Method to get all the user activities for a particular session
     *
     * @access public
     * @param string $sessionId Session Id
     * @return TRUE
     */
    public function getSessionActivities($sessionId)
    {
        $where = "WHERE sessionid = '$sessionId'";
        $results = $this->getAll($where);
        if (isset($results[0])) {
            return $results;
        } else {
            return FALSE;
        }
    }
    /**
     * Method to check if session exists
     *
     * @access public
     * @param string $sessionId Session Id
     * @return TRUE
     */
    public function sessionExists($sessionId)
    {
        $where = "WHERE sessionid = '$sessionId'";
        $results = $this->getAll($where);
        if (isset($results[0])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Method to fetch the first record for the session using min start time.
     *
     * @access public
     * @param string $sessionId session Id
     * @return Array
     */
    public function getMinSessionTime($sessionId)
    {
        $getDistinct = "SELECT id, endtime, min(starttime) as minstarttime FROM tbl_eportfolio_activitystreamer WHERE sessionid = '$sessionId'";
        $results = $this->getArray($getDistinct);
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }
    /**
     * Method to fetch the last record for the session using max start time.
     *
     * @access public
     * @param string $sessionId session Id
     * @return Array
     */
    public function getMaxSessionTime($sessionId)
    {
        $getDistinct = "SELECT id, endtime, max(starttime) as maxstarttime  FROM tbl_eportfolio_activitystreamer WHERE sessionid = '$sessionId'";
        $results = $this->getArray($getDistinct);
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }
    /**
     * Method to Compute session duration.
     *
     * @access public
     * @param string $sessionId session Id
     * @return Array
     */
    public function computeSessionDuration($sessionId)
    {
        $minData = $this->getMinSessionTime($sessionId);
        $minTime = $minData["minstarttime"];
        $maxData = $this->getMaxSessionTime($sessionId);
        if(empty($maxData['endtime'])){
            $maxTime = $maxData["maxstarttime"];
        } else {
            $maxTime = $maxData["endtime"];
        }
        $minmaxtime = array();
        $minmaxtime['mintime'] = $minTime;
        $minmaxtime['maxtime'] = $maxTime;
        //Create dateTime Objects
        /*$minTime = date_create($minTime);
        $maxTime = date_create($maxTime);*/
        $dateDiff = $this->date_diff($minTime, $maxTime);
        $minmaxtime['duration'] = $dateDiff;
        return $minmaxtime;
    }
    /**
     * Method to Compute Time taken in a transaction.
     *
     * @access public
     * @param string $Id Id
     * @return Array
     */
    public function computeTimeTakenPerTrans($rowdata)
    {
        $minTime = $rowdata["starttime"];
        if(empty($rowdata['endtime'])){
            $maxTime = $rowdata["starttime"];
        } else {
            $maxTime = $rowdata["endtime"];
        }
        $minmaxtime = array();
        $minmaxtime['mintime'] = $minTime;
        $minmaxtime['maxtime'] = $maxTime;
        $dateDiff = $this->date_diff($minTime, $maxTime);
        $minmaxtime['duration'] = $dateDiff;
        return $minmaxtime;
    }
    public function date_diff($start, $end="NOW") {
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;
        if($time>=0 && $time<=59) {
                // Seconds
                $timeshift = $time.' seconds ';

        } elseif($time>=60 && $time<=3599) {
                // Minutes + Seconds
                $pmin = ($edate - $sdate) / 60;
                $premin = explode('.', $pmin);
               
                $presec = $pmin-$premin[0];
                $sec = $presec*60;
               
                $timeshift = $premin[0].' min '.round($sec,0).' sec ';

        } elseif($time>=3600 && $time<=86399) {
                // Hours + Minutes
                $phour = ($edate - $sdate) / 3600;
                $prehour = explode('.',$phour);
               
                $premin = $phour-$prehour[0];
                $premin = $premin*60;
                //Check if . exists in string

                $secExist = strpos('.',$premin);
                if ($secExist === false) {
                    $min = 0;
                    $sec = 0;
                } else {
                    $min = explode('.',$premin);
                    $presec = '0.'.$min[1];
                    $sec = $presec*60;
                }
                $timeshift = $prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';

        } elseif($time>=86400) {
                // Days + Hours + Minutes
                $pday = ($edate - $sdate) / 86400;
                $preday = explode('.',$pday);

                $phour = $pday-$preday[0];
                $prehour = explode('.',$phour*24);

                $premin = ($phour*24)-$prehour[0];
                $min = explode('.',$premin*60);
               
                $presec = '0.'.$min[1];
                $sec = $presec*60;
               
                $timeshift = $preday[0].' days '.$prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';

        }
        return $timeshift;
    }
    /**
     * Method to compute the total time taken in contextcontent by a user.
     *
     * @access public
     * @param string $userId User ID
     * @return string
     */
    public function computeUserSessionsDuration($userId)
    {
        $allRecords = $this->getUserSessions($userId);
        $totalDuration = Null;
        $sumHrs = 0;
        $sumMin = 0;
        $sumSec = 0;

        foreach ($allRecords as $thisrecord) {
          if($thisrecord["userid"]==$userId) {
            $tempMin = 0;
            $tempHrs = 0;
            $timeTaken = $this->computeTimeTakenPerTrans($thisrecord);
            $hrsStr = "hrs";
            //Check if str hrs exists using php strpos function
            $hrsExist = strpos($timeTaken["duration"],$hrsStr);
            if ($hrsExist === false) {
                $hrs = 0;
                $minStr = "min";
                $minExist = strpos($timeTaken["duration"],$minStr);
                if ($minExist === false) {
                    $min = 0;
                    $secStr = "sec";
                    $secExist = strpos($timeTaken["duration"],$secStr);
                    if ($secExist === false) {
                        $sec = 0;
                    } else {
                        $seconds = explode(" sec ",$timeTaken["duration"]);
                        $sec = $seconds[0];
                    }
                } else {
                    $minutes = explode(" min ",$timeTaken["duration"]);
                    $min = $minutes[0];
                    $secStr = "sec";
                    $secExist = strpos($timeTaken["duration"],$secStr);
                    if ($secExist === false) {
                        $sec = 0;
                    } else {
                        $seconds = explode(" sec ",$minutes[1]);
                        $sec = $seconds[0];
                    }
                }
            } else {
                $hours = explode(" hrs ",$timeTaken["duration"]);
                $hrs = $hours[0];
                $minStr = "min";
                $minExist = strpos($timeTaken["duration"],$minStr);
                if ($minExist === false) {
                    $min = 0;
                    $secStr = "sec";
                    $secExist = strpos($timeTaken["duration"],$secStr);
                    if ($secExist === false) {
                        $sec = 0;
                    } else {
                        $seconds = explode(" sec ",$hours[1]);
                        $sec = $seconds[0];
                    }
                } else {
                    $minutes = explode(" min ",$hours[1]);
                    $min = $minutes[0];
                    $secStr = "sec";
                    $secExist = strpos($timeTaken["duration"],$secStr);
                    if ($secExist === false) {
                        $sec = 0;
                    } else {
                        $seconds = explode(" sec ",$minutes[1]);
                        $sec = $seconds[0];
                    }
                }
            }
            //$sec = explode(" sec ",$min[1]);

            //$sec = $sec[0];
            //Add seconds
            $sumSec = $sumSec + $sec;
            //Ifsum Seconds is greater than 60, add to min
            while ($sumSec>60) {
              $tempMin = $tempMin + 1;
              $sumSec = $sumSec - 60;
            }
            //Add minutes
            $sumMin = $sumMin + $min + $tempMin;
            //Ifsum Seconds is greater than 60, add to min
            while ($sumMin>60) {
              $tempHrs = $tempHrs + 1;
              $sumMin = $sumMin - 60;
            }
            //Add only when tempHrs have increased
            if($tempHrs>0)
                $sumHrs = $sumHrs + $tempHrs;
          }
        }
        if ($sumHrs>1) {
            $totalDuration = $sumHrs." Hrs ".$sumMin." Min ".$sumSec." Sec";
        } else {
            $totalDuration = $sumHrs." Hr ".$sumMin." Min ".$sumSec." Sec";
        }
        return $totalDuration;
    }
    /**
     * Method to compute time taken by each active eportfolio user.
     *
     * @access public
     * @return Array
     */
    public function getTimeTakenByEachMember()
    {
        $activeMembers = $this->getActiveMembers();
        $userData = array();
        foreach ($activeMembers as $member) {
             $thisArray = array();
             $duration = $this->computeUserSessionsDuration($member['userid']);
             $username = $this->objUser->username($member['userid']);
             $thisArray['userid'] = $member['userid'];
             $thisArray['username'] = $username;
             $thisArray['duration'] = $duration;
             $userData[] = $thisArray;
        }
        return $userData;
    }
    /**
     * Update a record with the end time
     * @param string $id ID
     * -- @param string $userId The user ID
     */
    function updateSingle($id) 
    {
        $userid = $this->objUser->userId();
        $this->update("id", $id, array(
            'endtime' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Method to retrieve a record id according to userId, contextItemId and sessionid.
     *
     * @access public
     * @param string $userId User ID
     * @param string $contextItemId Context Item Id
     * @param string $contextCode Context Code
     * @return string Record ID
     */
    public function getRecordId($userId, $recordId, $sessionid)
    {
        $where = "WHERE userid = '$userId' AND recordid = '$recordId' AND sessionid = '$sessionid'";
        $results = $this->getAll($where);
        if(!empty($results)){
          if(empty($results[0]['endtime']))
            return $results[0]['id'];
          else 
            return FALSE;
        } else {
          return FALSE;
        }
    }
    /**
     * Return a all records
     * @return array The values
     */
    function getEportfolioLogs() 
    {
        return $this->getAll();
    }
    /**
     * Return json for eportfolio usage logs
     * @return json The logs
     */
    function jsonGetEportfolioUsage() 
    {
        $logs = $this->getTimeTakenByEachMember();
        $logCount = (count($logs));
        $str = '{"logcount":"' . $logCount . '","availableLogs":[';
        $logArray = array();

        foreach ( $logs as $log ) {
          if(!empty($log['userid'])) {
              $infoArray = array();
              $infoArray['userid'] = $log['userid'];
              $infoArray['username'] = $log['username'];
              $userNames = "";
              $userNames = $userNames.$this->objUser->fullname( $log['userid'] );
              $infoArray['fullname'] = $userNames;
              $ownerName = "";
              $ownerName = $ownerName.$this->objUser->fullname( $log['owneruserid'] );
              $infoArray['ownerfullname'] = $ownerName;
              $infoArray['contextcode'] = $this->contextCode;
              $infoArray['duration'] = $log['duration'];
              $logArray[] = $infoArray;
          }
        }
        return json_encode(array(
            'logcount' => $logCount,
            'eportfoliologs' => $logArray
        ));
    }
    /**
     * Return json for contextcontent usage
     * @param string $contextcode Context Code
     * @return json The logs
     */
    function jsonEportfolioLogs( ) 
    {
        $logs = $this->getEportfolioLogs();

        $logCount = (count($logs));
        $str = '{"logcount":"' . $logCount . '","availableLogs":[';
        $logArray = array();

        foreach ( $logs as $log ) {
         //if( !empty ( $log['endtime'] ) || $log['endtime'] != NULL ) {
          $infoArray = array();
          $infoArray['id'] = $log['id'];
          $infoArray['userid'] = $log['userid'];
          $infoArray['username'] = $log['username'];
          $userNames = "";
          $userNames = $userNames.$this->objUser->fullname( $log['userid'] );
          $infoArray['fullname'] = $userNames;
          $ownerName = "";
          $ownerName = $ownerName.$this->objUser->fullname( $log['owneruserid'] );
          $infoArray['ownerfullname'] = $ownerName;
          $infoArray['contextcode'] = $this->contextCode;
          $infoArray['modulecode'] = $log['modulecode'];
          $infoArray['recordid'] = $log['recordid'];
          //Get context item title (page or chapter)
          if ( $log['partname'] == 'address' ) {
           $list = $this->objDbAddressList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['street_no']." ".$list[0]['street_name'];
          } elseif ( $log['partname'] == 'contact' ) {
           $list = $this->objDbContactList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $this->objLanguage->languageText("mod_eportfolio_countrycode", 'eportfolio')." : ".$list[0]['country_code'].", ".$this->objLanguage->languageText("mod_eportfolio_areacode", 'eportfolio')." : ".$list[0]['area_code'];
          } elseif ( $log['partname'] == 'email' ) {
           $list = $this->objDbEmailList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['email'];
          } elseif ( $log['partname'] == 'demographics' ) {
           $list = $this->objDbDemographicsList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio')." : ".$list[0]['nationality'];
          } elseif ( $log['partname'] == 'activities' ) {
           $list = $this->objDbActivityList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'affiliation' ) {
           $list = $this->objDbAffiliationList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'transcripts' ) {
           $list = $this->objDbTranscriptList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'qualifications' ) {
           $list = $this->objDbQclList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'goals' ) {
           $list = $this->objDbGoalsList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'competencies' ) {
           $list = $this->objDbCompetencyList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'interests' ) {
           $list = $this->objDbInterestList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'reflections' ) {
           $list = $this->objDbReflectionList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'assertions' ) {
           $list = $this->objDbAssertionList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } else {
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = " ";
          }
          $infoArray['datecreated'] = $log['datecreated'];
          $infoArray['description'] = $log['description'];
          $infoArray['starttime'] = $log['starttime'];
          $infoArray['endtime'] = $log['endtime'];
          $logArray[] = $infoArray;
         //}
        }

        return json_encode(array(
            'logcount' => $logCount,
            'eportfoliologs' => $logArray
        ));
    }
    /*Returns two datetime values by calculating time randomly from two datetime values. leaves date intact
     * @param datetime $startdate Start datetime
     * @param datetime $endate End datetime
     */
    public function calculateRandomTime( $startdate, $endate ) {
        //Check if any image record exists where startime and endtime are within the page startime and endtime -- Can be done later
        $newstartdate = 0;
        $newenddate = 0;
        $startdate = explode (" ", '2010-05-11 14:18:54');
        $endate = explode (" ", '2010-05-11 14:21:08');

        $startime = explode (":", $startdate['1']);
        $endtime = explode (":", $endate['1']);
        //Check if its the same hour
        if ($startime[0] == $endtime[0]){
          //Check if its the same minute -- Very easy
          if ($startime[1] == $endtime[1]){
            do {
            $sec1 = rand($startime[2], $endtime[2]);
            $sec2 = rand($startime[2], $endtime[2]);
            } while ( $sec1 == $sec2 || $sec2 < $sec1 );
            $newstartdate = $startdate['0']." ".$startime[0].":".$startime[1].":".$sec1;
            $newenddate = $startdate['0']." ".$startime[0].":".$startime[1].":".$sec2;
            //Calculate a random seconds value thats greater than startime and less than endtime
            
          }else{
            //If endtime[1] is greater than startime[1]
            //Generate random value between the two
            do {
            $min1 = rand($startime[1], $endtime[1]);
            $min2 = rand($startime[1], $endtime[1]);
            } while ($min2 < $min1);
            if ( $min1 == $min2 ){
              //Check if min1 is same as $startime[1]
              if ( $min1 == $startime[1] ){
               //Generate a random value that is between $startime[2] and 60
                $sec1 = $startime[2];
                do { $sec1 = rand($sec1, 60); $sec2 = rand($sec1, 60); } while ( $sec2 < $sec1 || $sec2 == 60 );
                
                $newstartdate = $startdate['0']." ".$startime[0].":".$min1.":".$sec1;
                $newenddate = $startdate['0']." ".$startime[0].":".$min1.":".$sec1;
              } elseif ( $min1 == $endtime[1] ){
                //Get the start datetime
                $sec1 = $endtime[2];
                do { $sec1 = rand($sec1, 60); $sec2 = rand($sec1, 60); } while ( $sec2 < $sec1 || $sec2 == 60 );
                $newstartdate = $endate['0']." ".$endtime[0].":".$min1.":".$sec1;
                $newenddate = $endate['0']." ".$endtime[0].":".$min1.":".$sec2;
              } 
            } else {
             //If min1 == $startime find seconds btwn $startime[2] and 60
             if ( $min1 == $startime[1] && $min2 != $endtime[1] ) {
              do { $sec1 = rand($startime[1], 60); } while ( $sec1 == $startime[1] || $sec1 == 60 );
              //Then get min2 seconds by getting a random btwn 0 and 60
              do { $sec2 = rand(0, 60); } while ( $sec2 == 60 );
              $newstartdate = $endate['0']." ".$startime[0].":".$min1.":".$sec1;
              $newenddate = $endate['0']." ".$endtime[0].":".$min2.":".$sec2;
             } elseif ( $min1 != $startime[1] && $min2 != $endtime[1] ) {
              //Get min1 seconds by getting a random btwn 0 and 60
              do { $sec1 = rand(0, 60); } while ( $sec1 == 60 );
              //Then get min2 seconds by getting a random btwn $sec1 and 60
              do { $sec2 = rand(0, 60); } while ( $sec2 == 60 );
              $newstartdate = $endate['0']." ".$startime[0].":".$min1.":".$sec1;
              $newenddate = $endate['0']." ".$endtime[0].":".$min2.":".$sec2;
             } else {
              //If min2 == $endtime find seconds btwn $endtime[2] and 60
              do { $sec2 = rand($endtime[1], 60); } while ( $sec2 == $endtime[1] || $sec2 == 60 );
              //Then get min1 seconds by getting a random btwn 0 and 60
              do { $sec1 = rand(0, 60); } while ( $sec1 == 60 );
              $newstartdate = $endate['0']." ".$startime[0].":".$min1.":".$sec1;
              $newenddate = $endate['0']." ".$endtime[0].":".$min2.":".$sec2;
             }
            }
          }
        }
        if ($newstartdate != 0 || $newenddate != 0)
         return array ("newstartdate"=>$newstartdate,"newenddate"=>$newenddate);
        else return False;
    }

    /**
     * Return comma separated values(CSV) for context logs
     * @param string $contextcode Context Code
     * @return csv of the logs
     */
    function csvContextLogs() 
    {
        $where = "";
        $logs = $this->getEportfolioLogs();

        $logCount = (count($logs));
        $list = array();
        $list = array('id, userid, username, fullname, ownerfullname, contextcode, modulecode, recordid, partname, parttitle, datecreated, description, starttime, endtime');
        $csvFile = "logs.csv"; 
        $Handle = fopen($csvFile, 'w');
        //fwrite($Handle, $list);
        foreach ($list as $line) {
            fputcsv($Handle, split(',', $line));
        }
        foreach ( $logs as $log ) {
         //Put $log['endtime'] to == null to return empty fields
         if( !empty ( $log['endtime'] ) || $log['endtime'] == NULL ) {
          $infoArray['id'] = $log['id'];
          $infoArray['userid'] = $log['userid'];
          $infoArray['username'] = $log['username'];
          $userNames = "";
          $userNames = $userNames.$this->objUser->fullname( $log['userid'] );
          $infoArray['fullname'] = $userNames;
          $ownerName = "";
          $ownerName = $ownerName.$this->objUser->fullname( $log['owneruserid'] );
          $infoArray['ownerfullname'] = $ownerName;
          $infoArray['contextcode'] = $this->contextCode;
          $infoArray['modulecode'] = $log['modulecode'];
          $infoArray['recordid'] = $log['recordid'];

          //Get context item title (page or chapter)
          if ( $log['partname'] == 'address' ) {
           $list = $this->objDbAddressList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['street_no']." ".$list[0]['street_name'];
          } elseif ( $log['partname'] == 'contact' ) {
           $list = $this->objDbContactList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $this->objLanguage->languageText("mod_eportfolio_countrycode", 'eportfolio')." : ".$list[0]['country_code'].", ".$this->objLanguage->languageText("mod_eportfolio_areacode", 'eportfolio')." : ".$list[0]['area_code'];
          } elseif ( $log['partname'] == 'email' ) {
           $list = $this->objDbEmailList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['email'];
          } elseif ( $log['partname'] == 'demographics' ) {
           $list = $this->objDbDemographicsList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio')." : ".$list[0]['nationality'];
          } elseif ( $log['partname'] == 'activities' ) {
           $list = $this->objDbActivityList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'affiliation' ) {
           $list = $this->objDbAffiliationList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'transcripts' ) {
           $list = $this->objDbTranscriptList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'qualifications' ) {
           $list = $this->objDbQclList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'goals' ) {
           $list = $this->objDbGoalsList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'competencies' ) {
           $list = $this->objDbCompetencyList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'interests' ) {
           $list = $this->objDbInterestList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'reflections' ) {
           $list = $this->objDbReflectionList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } elseif ( $log['partname'] == 'assertions' ) {
           $list = $this->objDbAssertionList->listSingle( $log['recordid'] );
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = $list[0]['shortdescription'];
          } else {
           $infoArray['partname'] = $log['partname'];
           $infoArray['parttitle'] = " ";
          }
          $infoArray['datecreated'] = $log['datecreated'];
          $infoArray['description'] = $log['description'];
          $infoArray['starttime'] = $log['starttime'];
          $infoArray['endtime'] = $log['endtime'];
          $list = array($infoArray['id'].','.$infoArray['userid'].','.$infoArray['username'].','.$infoArray['fullname'].','.$infoArray['ownerfullname'].','.$infoArray['contextcode'].','.$infoArray['modulecode'].','.$infoArray['recordid'].','.$infoArray['partname'].','.$infoArray['parttitle'].','.$infoArray['datecreated'].','.$infoArray['description'].','.$infoArray['starttime'].','.$infoArray['endtime']);
          foreach ($list as $line) {
            fputcsv($Handle, split(',', $line));
          }
         }
        }
        fclose($Handle);
        return $csvFile;
    }
    /**
     * Return comma separated values(CSV) for context logs
     * @param string $contextcode Context Code
     * @return csv of the logs
     */
    function csvEportfolioContentUsage() 
    {
        $logs = $this->getTimeTakenByEachMember();

        $logCount = (count($logs));
        $list = array();
        $list = array('userid,username,fullname,contextcode,duration');
        $csvFile = "eportfolio-usage.csv"; 
        $Handle = fopen($csvFile, 'w');
        //fwrite($Handle, $list);
        foreach ($list as $line) {
            fputcsv($Handle, split(',', $line));
        }
        foreach ( $logs as $log ) {
          //Return empty till its fixed
          $userNames = "";
          $userNames = $userNames.$this->objUser->fullname( $log['userid'] );
          $list = array($log['userid'].','.$log['username'].','.$userNames.','.$this->contextCode.','.$log['duration']);
          foreach ($list as $line) {
            fputcsv($Handle, split(',', $line));
          }
        }
        fclose($Handle);
        return $csvFile;
    }
    /**
     * Method to delete a record
     * @param string $recordId Context Item Id
     */
    function deleteRecord($recordId)
    {
        // Delete the Record
        $this->delete('recordid', $recordId);
    }
}
?>
