<?php

/**
 * Class the records the pages a user has visited.
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
 * @package   contextcontent
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright @2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: db_contextcontent_titles_class_inc.php 11385 2008-11-07 00:52:41Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       core
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
 * Class the records the pages a user has visited.
 *
 * It doesn't contain the content of pages, just the index to track which pages
 * are translations of each other.
 *
 * @category  Chisimba
 * @package   contextcontent
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright @2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

class db_learningcontent_activitystreamer extends dbtable
{

    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_learningcontent_activitystreamer');
        $this->objUser =& $this->getObject('user', 'security');
        //Load content pages
        $this->objContentTitles = $this->getObject('db_learningcontent_titles');
        $this->objContentChapter = $this->getObject('db_learningcontent_contextchapter');
        $this->objContentPages = $this->getObject('db_learningcontent_pages');
        $this->objFiles = $this->getObject('dbfile','filemanager');        
        // Load Context Object
        $this->objContext = $this->getObject('dbcontext', 'context');            

        // Store Context Code
        $this->contextCode = $this->objContext->getContextCode();
    }
    
    /**
     * Method to add a record.
     *
     * @access public
     * @param string $userId User ID
     * @param string $sessionid session ID
     * @param string $contextItemId context Item ID
     * @param string $contextCode context Code
     * @param string $moduleCode module Code
     * @param string $datecreated date created
     * @param string $pageorchapter record whether its a context page or chapter
     * @param string $description description of activity
     * @param string $sessionstarttime session start time
     * @param string $sessionendtime session end time
     */
   public function addRecord($userId, $sessionid, $contextItemId, $contextCode, $modulecode, $datecreated,$pageorchapter=NULL, $description=NULL, $sessionstarttime=NULL, $sessionendtime=NULL)
    {
        $row = array();
        $row['userid'] = $userId;
        $row['contextcode'] = $contextCode;
        $row['contextitemid'] = $contextItemId;
        $row['datecreated'] = strftime('%Y-%m-%d %H:%M:%S', mktime());
        $row['sessionid'] = $sessionid;
        $row['modulecode'] = $modulecode;
        $row['pageorchapter'] = $pageorchapter;
        $row['description'] = $description;
        $row['starttime'] = $sessionstarttime;
        $row['endtime'] = $sessionendtime;

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
     * Method to check if record exists according to userId, contextItemId and sessionid.
     *
     * @access public
     * @param string $userId User ID
     * @param string $contextItemId Context Item Id
     * @param string $contextCode Context Code
     * @return TRUE
     */
    public function getRecord($userId, $contextItemId, $sessionid)
    {
        $where = "WHERE userid = '$userId' AND contextitemid = '$contextItemId' AND sessionid = '$sessionid' AND endtime = NULL";
        $results = $this->getAll($where);
        if (isset($results[0]['id'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Method to check if record exists according to userId, contextItemId and sessionid.
     *
     * @access public
     * @param string $userId User ID
     * @param string $contextItemId Context Item Id
     * @param string $contextCode Context Code
     * @return TRUE
     */
    public function checkRecord($userId, $contextItemId, $contextCode)
    {
        $where = "WHERE userid = '$userId' AND contextitemid = '$contextItemId' AND contextcode = '$contextCode'";
        $results = $this->getAll($where);
        if (isset($results[0]['id'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Update a record
     * @param string $id ID
     * @param string $start The start date
     * @param string $longdescription The long description
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
    public function getRecordId($userId, $contextItemId, $sessionid)
    {
        $where = "WHERE userid = '$userId' AND contextitemid = '$contextItemId' AND sessionid = '$sessionid' AND endtime is NULL";
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
    * Method to get a content page
    * @param string $pageId Record Id of the Page
    * @param string $contextCode Context the Page is In
    * @return array Details of the Page, FALSE if does not exist
    * @access public
    */
    public function getPage($pageId, $contextCode)
    {
        $sql = 'SELECT tbl_learningcontent_order.id, tbl_learningcontent_order.chapterid, tbl_learningcontent_order.parentid,tbl_learningcontent_pages.scorm, tbl_learningcontent_pages.menutitle, pagecontent, headerscripts, pagepicture, pageformula, lft, rght, tbl_learningcontent_pages.id as pageid, tbl_learningcontent_order.titleid, isbookmarked
        FROM tbl_learningcontent_order 
        INNER JOIN tbl_learningcontent_titles ON (tbl_learningcontent_order.titleid = tbl_learningcontent_titles.id) 
        INNER JOIN tbl_learningcontent_pages ON (tbl_learningcontent_pages.titleid = tbl_learningcontent_titles.id AND original=\'Y\') 
        WHERE tbl_learningcontent_order.id=\''.$pageId.'\' AND contextcode=\''.$contextCode.'\'
        ORDER BY lft LIMIT 1';
        
        $results = $this->getArray($sql);
        
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }
    /**
     * Return a single record
     * @param string $contextcode Context Code
     * @return array The values
     */
    function getContextLogs($contextcode, $where ) 
    {
        return $this->getAll("WHERE contextcode='" . $contextcode . "'".$where);
    }
    /**
     * Return json for context logs
     * @param string $contextcode Context Code
     * @return json The logs
     */
    function jsonContextLogs( $contextcode, $start, $limit ) 
    {
        if ( !empty($start) && !empty($limit) ) 
         $where = " LIMIT " . $start . " , " . $limit;
        else
         $where = "";
        $logs = $this->getContextLogs( $contextcode, $where );

        $logCount = (count($logs));
        $str = '{"logcount":"' . $logCount . '","availableLogs":[';
        $logArray = array();
        foreach ( $logs as $log ) {
         if( !empty ( $log['endtime'] ) || $log['endtime'] != NULL ) {
          $infoArray = array();
          $infoArray['id'] = $log['id'];
          $infoArray['userid'] = $log['userid'];
          //Function has bug
          //$userNames = $this->objUser->getTitle( $log['userid'] ).". ";
          //Return empty till its fixed
          $userNames = "";
          $userNames = $userNames.$this->objUser->fullname( $log['userid'] );
          $infoArray['usernames'] = $userNames;
          $infoArray['contextcode'] = $this->contextCode;
          $infoArray['modulecode'] = $log['modulecode'];
          $infoArray['contextitemid'] = $log['contextitemid'];
          //Get context item title (page or chapter)
          if ( $log['pageorchapter'] == 'page' ) {
           $pageDetails = $this->getPage( $log['contextitemid'], $contextcode );
           $pageInfo = $this->objContentPages->pageInfo( $pageDetails['titleid'] );
           $infoArray['pageorchapter'] = $log['pageorchapter'];
           $infoArray['contextitemtitle'] = $pageInfo['menutitle'];
          } elseif ( $log['pageorchapter'] == 'chapter' ) {
           $chapterTitle = $this->objContentChapter->getContextChapterTitle( $log['contextitemid'] );
           $infoArray['pageorchapter'] = $log['pageorchapter']; 
           $infoArray['contextitemtitle'] = $chapterTitle;
          } elseif ( $log['pageorchapter'] == 'viewPicture' )  {
           $picdesc = $this->objFiles->getFileInfo($log['contextitemid']);
           if(empty($picdesc['filedescription'])){
             $picdescr = $picdesc["filename"];
           }else{
             $picdescr = $picdesc['filedescription'];    
           }

           $infoArray['pageorchapter'] = 'Picture';
           $infoArray['contextitemtitle'] = $picdescr;
          } elseif ( $log['pageorchapter'] == 'viewFormula' )  {
           $fmladesc = $this->objFiles->getFileInfo($log['contextitemid']);
           if(empty($fmladesc['filedescription'])){
             $fmladescr = $fmladesc["filename"];
           }else{
             $fmladescr = $fmladesc['filedescription'];    
           }
           $infoArray['pageorchapter'] = 'Formula';
           $infoArray['contextitemtitle'] = $fmladescr;
          } else {
           $infoArray['pageorchapter'] = $log['pageorchapter'];
           $infoArray['contextitemtitle'] = " ";
          }
          $infoArray['datecreated'] = $log['datecreated'];
          $infoArray['description'] = $log['description'];
          $infoArray['starttime'] = $log['starttime'];
          $infoArray['endtime'] = $log['endtime'];
          $logArray[] = $infoArray;
         }
        }
        return json_encode(array(
            'logcount' => $logCount,
            'contextlogs' => $logArray
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
    function csvContextLogs( $contextcode ) 
    {
        $where = "";
        $logs = $this->getContextLogs( $contextcode, $where );

        $logCount = (count($logs));
        $list = array();
        $list = array('id,userid,usernames,contextcode,modulecode,contextitemid,type,contextitemtitle,datecreated,description,starttime,endtime');
        $csvFile = "logs.csv"; 
        $Handle = fopen($csvFile, 'w');
        //fwrite($Handle, $list);
        foreach ($list as $line) {
            fputcsv($Handle, split(',', $line));
        }
        foreach ( $logs as $log ) {
         //Put $log['endtime'] to == null to return empty fields
         if( !empty ( $log['endtime'] ) || $log['endtime'] == NULL ) {
          //Return empty till its fixed
          $userNames = "";
          $userNames = $userNames.$this->objUser->fullname( $log['userid'] );
          //Get context item title (page or chapter)
          $title = '';
          $pageorchapter = '';
          if ( $log['pageorchapter'] == 'page' ) {
           $pageDetails = $this->getPage( $log['contextitemid'], $contextcode );
           $pageInfo = $this->objContentPages->pageInfo( $pageDetails['titleid'] );
           $pageorchapter = $log['pageorchapter'];
           $title = $pageInfo['menutitle'];
          } elseif ( $log['pageorchapter'] == 'chapter' ) {
           $chapterTitle = $this->objContentChapter->getContextChapterTitle( $log['contextitemid'] );
           $pageorchapter = $log['pageorchapter'];
           $title = $chapterTitle;
          } elseif ( $log['pageorchapter'] == 'viewPicture' )  {
           $picdesc = $this->objFiles->getFileInfo($log['contextitemid']);
           if(empty($picdesc['filedescription'])){
             $picdescr = $picdesc["filename"];
           }else{
             $picdescr = $picdesc['filedescription'];    
           }
           $pageorchapter = 'Picture';
           $title = $picdescr;
          } elseif ( $log['pageorchapter'] == 'viewFormula' )  {
           $fmladesc = $this->objFiles->getFileInfo($log['contextitemid']);
           if(empty($fmladesc['filedescription'])){
             $fmladescr = $fmladesc["filename"];
           }else{
             $fmladescr = $fmladesc['filedescription'];    
           }
           $title = $fmladescr;
          } else {
           $title = "";
          }
          $list = array($log['id'].','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.$pageorchapter.','.$title.','.$log['datecreated'].','.$log['description'].','.$log['starttime'].','.$log['endtime']);
          foreach ($list as $line) {
            fputcsv($Handle, split(',', $line));
          }
          //fwrite($Handle, $list);
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
    function csvContextLogsFake( $contextcode ) 
    {
        $where = "";
        //Use limit, script takes forever if rows exceed 30
        //$where = "LIMIT 390, 20";
        $logs = $this->getContextLogs( $contextcode, $where );

        $logCount = (count($logs));
        $list = array();
        $list = array('id,userid,usernames,contextcode,modulecode,contextitemid,type,contextitemtitle,datecreated,description,starttime,endtime');
        $csvFile = "logs.csv"; 
        $Handle = fopen($csvFile, 'w');
        //fwrite($Handle, $list);
        foreach ($list as $line) {
            fputcsv($Handle, split(',', $line));
        }
        foreach ( $logs as $log ) {
         if( !empty ( $log['endtime'] ) || !$log['endtime'] = NULL ) {
          //Return empty till its fixed
          $userNames = "";
          $userNames = $userNames.$this->objUser->fullname( $log['userid'] );
          //Get context item title (page or chapter)
          $title = '';
          $pageorchapter = '';
          if ( $log['pageorchapter'] == 'page' ) {
           //Imagine User viewed an image for 001
           if ( $contextcode == '001' ) {
            //Start of Page
            if ($log['contextitemid'] == 'gen11Srv30Nme41_21144_1269681692') {
             //Enter record for each image
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_55386_1269604704');
//var_dump($picdesc);
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_55386_1269604704'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
             //Enter record for each image
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_72372_1269604822');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_72372_1269604822'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_81307_1269604319') {
             //Enter record for each image
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_73767_1269605472');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_73767_1269605472'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_55386_1269604704
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_33707_1269605537');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_33707_1269605537'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_87618_1269604349') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_55386_1269604704
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_15142_1269605687');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_15142_1269605687'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_55386_1269604704
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_79322_1269605785');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_79322_1269605785'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_9301_1269604392') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_55386_1269604704
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_75938_1269606000');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_75938_1269606000'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_55386_1269604704
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_66832_1269606208');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_66832_1269606208'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_54504_1269604435') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_55386_1269604704
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_66832_1269606208');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_66832_1269606208'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_55386_1269604704
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_78425_1269606208');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_78425_1269606208'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            }
           //End of Context 001
           } elseif ( $contextcode == '002' ) { 
            //Start of Page
            if ($log['contextitemid'] == 'gen11Srv30Nme41_71805_1269777463') {
             //Enter record for each image
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_55386_1269604704');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             do { $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] ); } while ($fakeDatetime==false);
             if (  $fakeDatetime != false ) {
               $list = array('gen11Srv30Nme41_55386_1269604704'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_33194_1269778206') {
             //Enter record for each image
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_73767_1269605472');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_73767_1269605472'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_78476_1269778547') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_55386_1269604704
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_15142_1269605687');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_15142_1269605687'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_72980_1269779481') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_55386_1269604704
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_75938_1269606000');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_75938_1269606000'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_54504_1269604435') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_54504_1269604435
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_66832_1269606208');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_66832_1269606208'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Picture'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Picture'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            }
           //End of Context 002
           } elseif ( $contextcode == '003' ) { 
            //Start of Page
            if ($log['contextitemid'] == 'gen11Srv30Nme41_89699_1269777596') {
             //Enter record for each image
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_72372_1269604822');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_72372_1269604822'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_36433_1269778353') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_36433_1269778353
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_33707_1269605537');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_33707_1269605537'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_76029_1269779116') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_76029_1269779116
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_79322_1269605785');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_79322_1269605785'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            } elseif ($log['contextitemid'] == 'gen11Srv30Nme41_40204_1269779594') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_40204_1269779594
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_66832_1269606208');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_66832_1269606208'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            }elseif ($log['contextitemid'] == 'gen11Srv30Nme41_74356_1269780005') {
             //Enter record for each image
             //Image 1 gen11Srv30Nme41_74356_1269780005
             $picdesc = $this->objFiles->getFileInfo('gen11Srv30Nme41_78425_1269606208');
             if(empty($picdesc['filedescription'])){
               $picdescr = $picdesc["filename"];
             }else{
               $picdescr = $picdesc['filedescription'];    
             }
             $fakeDatetime = $this->calculateRandomTime( $log['starttime'], $log['endtime'] );
             if ( !empty ( $fakeDatetime ) ) {
               $list = array('gen11Srv30Nme41_78425_1269606208'.','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.'Formula'.','.$picdescr.','.$fakeDatetime["newstartdate"].','.'View Formula'.','.$fakeDatetime["newstartdate"].','.$fakeDatetime["newenddate"]);
              foreach ($list as $line) {
               fputcsv($Handle, split(',', $line));
              }
             }
            }
           //End of Context 003
           }
           $pageDetails = $this->getPage( $log['contextitemid'], $contextcode );
           $pageInfo = $this->objContentPages->pageInfo( $pageDetails['titleid'] );
           $pageorchapter = $log['pageorchapter'];
           $title = $pageInfo['menutitle'];
          } elseif ( $log['pageorchapter'] == 'chapter' ) {
           $chapterTitle = $this->objContentChapter->getContextChapterTitle( $log['contextitemid'] );
           $pageorchapter = $log['pageorchapter'];
           $title = $chapterTitle;
          } elseif ( $log['pageorchapter'] == 'viewPicture' )  {
           $picdesc = $this->objFiles->getFileInfo($log['contextitemid']);
           if(empty($picdesc['filedescription'])){
             $picdescr = $picdesc["filename"];
           }else{
             $picdescr = $picdesc['filedescription'];    
           }
           $pageorchapter = 'Picture';
           $title = $picdescr;
          } elseif ( $log['pageorchapter'] == 'viewFormula' )  {
           $fmladesc = $this->objFiles->getFileInfo($log['contextitemid']);
           if(empty($fmladesc['filedescription'])){
             $fmladescr = $fmladesc["filename"];
           }else{
             $fmladescr = $fmladesc['filedescription'];    
           }
           $title = $fmladescr;
          } else {
           $title = "";
          }
          $list = array($log['id'].','.$log['userid'].','.$userNames.','.$this->contextCode.','.$log['modulecode'].','.$log['contextitemid'].','.$pageorchapter.','.$title.','.$log['datecreated'].','.$log['description'].','.$log['starttime'].','.$log['endtime']);
          foreach ($list as $line) {
            fputcsv($Handle, split(',', $line));
          }
          //fwrite($Handle, $list);
         }
        }
        fclose($Handle);
        return $csvFile;
    }
    /**
     * Method to delete a record
     * @param string $contextItemId Context Item Id
     */
    function deleteRecord($contextItemId)
    {
        // Delete the Record
        $this->delete('contextitemid', $contextItemId);
    }
}
?>
