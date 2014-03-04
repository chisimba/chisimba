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
class turnitinops extends object {

    //required
    public $gmtime, $encrypt, $md5, $aid, $diagnostic, $uem, $ufn, $uln, $utp;
    //optional
    public $said, $upw, $dis;
    //unique ids
    public $uid, $cid, $assignid;
    //function specific
    public $fid, $fcmd;
    public $ctl, $cpw, $tem, $assign, $dtstart, $dtdue, $ainst, $newassign, $ptl, $pdata, $ptype, $pfn, $pln;
    public $oid, $newupw, $username;
    //session
    public $sessionid;
    //config
    public $remote_host, $shared_secret_key;
    public $filepath;
    //optional params
    public $internet_check,
    $report_gen_speed,
    $exclude_biblio,
    $exclude_quoted,
    $exclude_value,
    $late_accept_flag,
    $submit_papers_to,
    $s_paper_check,
    $journal_check;

    /**
     * Constructor for the twitterlib class
     * @access public
     * @return VOID
     */
    public function init() {
        // Retrieve system configuration
        $this->_objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->aid = $this->_objSysConfig->getValue('accountid', 'jturnitin');
        $this->remote_host = $this->_objSysConfig->getValue('apihost', 'jturnitin');
        $this->shared_secret_key = $this->_objSysConfig->getValue('sharedkey', 'jturnitin');
        $this->uem = $this->_objSysConfig->getValue('email', 'jturnitin');
        $this->upw = $this->_objSysConfig->getValue('password', 'jturnitin');


        //setup defaults
        $this->gmtime = $this->getGMT();
        $this->encrypt = 0;
        $this->dis = 0;
        $this->diagnostic = 0;

        //additional properties
        $this->idsync = 1;
        $this->s_view_report = 1; // allow students to view the Originality Report (optional, default is set to not allow students to view Originality Reports)
        //success codes
        $this->successCodes = array();
        $this->successCodes['2'] = array(20, 21, 22);
        $this->successCodes['1'] = array(10, 11);
        $this->submitted = $this->getObject("turnitinsubmittedassignments");
    }

    /**
     * Method to generate a an MD5 string
     *
     * @return unknown
     */
    public function getMD5() {
        $md5string = $this->aid .
                $this->assign .
                $this->assignid .
                $this->cid .
                $this->cpw .
                $this->ctl .
                $this->diagnostic .
                $this->dis .
                $this->dtdue .
                $this->dtstart .
                $this->encrypt .
                $this->fcmd .
                $this->fid .
                $this->gmtime .
                $this->newassign .
                $this->newupw .
                $this->oid .
                $this->pfn .
                $this->pln .
                $this->ptl .
                $this->ptype .
                $this->said .
                $this->tem .
                $this->uem .
                $this->ufn .
                $this->uid .
                $this->uln .
                $this->upw .
                $this->utp .
                $this->shared_secret_key;
        //error_log($md5string);
        return md5($md5string);
    }

    /**
     * Get the time in a formatted GMT sting
     *
     * @return string
     */
    public function getGMT() {
        return substr(gmdate('YmdHi'), 0, -1);
    }

    /**
     * Method to get the returned xml result
     * and format it into a readable message
     *
     * @param string $xml
     * @return array
     */
    public function getXMLResult($xmlStr) {
        if ($this->diagnostic == 0) {

            try {
                $xml = new SimpleXMLElement($xmlStr);
            } catch (Exception $e) {

                return array('message' => 'An error occured, cannot continue processing this request. Please
                     contact system administrator',
                    'code' => '413',
                    'object' => "",
                    'xmlobject' => "");
            }


            $message = $xml->rmessage;
            $rcode = $xml->rcode;
            $object = ($xml->object) ? $xml->object : null;
            $objectID = ($xml->objectID) ? $xml->objectID : null;


            return array('message' => $message,
                'code' => $rcode,
                'object' => $object,
                'xmlobject' => $xml,
                'objectid' => $objectID);
        } else {
            return $xmlStr;
        }
    }

    /**
     * Method to get all the parameters for
     * the url
     *
     * @return string
     */
    public function getParams() {
        $url = "gmtime=" . $this->gmtime;
        $url .= "&fid=" . $this->fid;
        $url .= "&fcmd=" . $this->fcmd;
        $url .= "&encrypt=" . $this->encrypt;
        $url .= "&md5=" . $this->getMD5();
        $url .= "&aid=" . $this->aid;
        $url .= "&said=" . $this->said;
        $url .= "&diagnostic=" . $this->diagnostic;
        $url .= "&uem=" . urlencode($this->uem);
        $url .= "&upw=" . urlencode($this->upw);
        $url .= "&ufn=" . urlencode($this->ufn);
        $url .= "&uln=" . urlencode($this->uln);
        $url .= "&utp=" . $this->utp;
        $url .= "&ctl=" . urlencode($this->ctl);
        $url .= "&cpw=" . urlencode($this->cpw);
        $url .= "&tem=" . $this->tem;
        $url .= "&oid=" . $this->oid;
        $url .= "&newupw=" . urlencode($this->newupw);
        $url .= "&assign=" . urlencode($this->assign);
        $url .= "&dis=" . $this->dis;
        $url .= "&uid=" . urlencode($this->uid);
        $url .= "&cid=" . urlencode($this->cid);
        $url .= "&idsync=" . urlencode($this->idsync);
        $url .= "&assignid=" . urlencode($this->assignid);
        $url .= "&dtstart=" . urlencode($this->dtstart);
        $url .= "&dtdue=" . urlencode($this->dtdue);
        $url .= "&ptl=" . urlencode($this->ptl);
        $url .= "&s_view_report=" . urlencode($this->s_view_report);
        $url .= "&ptype=" . urlencode($this->ptype);
        $url .= "&filepath=" . urlencode($this->filepath);
        if ($this->sessionid) {
            $url .= "&session-id=" . urlencode($this->sessionid);
        }

        error_log($url);
        return $url;
    }

    /**
     * Method to redirect the url
     *
     * @return unknown
     */
    public function getRedirectUrl() {
        $this->utp = 3;
        return $this->remote_host . '?' . $this->getParams();
    }

    public function getRemoteHost() {
        return $this->remote_host;
    }

    /**
     * Method to get the results from turnitin
     *
     */
    public function doGet() {
        header('location:' . $this->remote_host . '?' . $this->getParams());
    }

    /**
     * To use the doPost function as written, CURL must be installed.
     *
     * @return result
     */
    public function doPost($headers="") {


        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";

        //get the proxy info if set
        $objProxy = $this->getObject('proxyparser', 'utilities');
        $proxyArr = $objProxy->getProxy();

        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_POST,1);

        curl_setopt($ch, CURLOPT_URL, $this->remote_host);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
            error_log('Using proxy......');
            error_log(var_export($proxyArr, true));
            //setup proxy
            //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyArr['proxy_port']);
            curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_protocol'] . '://' . $proxyArr['proxy_host']);
        }
        $params = $this->getParams();
        // print $params;
        //die($params);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        ob_start();
        $result = curl_exec($ch);
        curl_close($ch);
        error_log(var_export($result, true));
        return $this->getXMLResult($result);
    }

    /**
     * Method to post an assessment to Turnitin
     *
     * @return boolean
     */
    public function doPostAssessment($params) {
        $objProxy = $this->getObject('proxyparser', 'utilities');
        $proxyArr = $objProxy->getProxy();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $this->remote_host);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
            //setup proxy
            //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyArr['proxy_port']);
            curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_protocol'] . $proxyArr['proxy_host']);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        //return $this->getXMLResult($result);
        //print $params;
        return $result;
    }

    /**
     * Do a login with the details provided
     *
     * @return boolean
     */
    public function APILogin($params) {
        $this->fcmd = 1;
        $this->fcmd = 2;

        $this->uem = $params['email'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->upw = $params['password'];
        return $this->doPost();
    }

    /**
     * Method to create a Lecturer on Turnitin
     *
     * @param array $params
     */
    function createLecturer($params) {
        return $this->createUser($params, 2);
    }

    /**
     * Method to create a user on Turnitin
     *
     * @param array $params
     */
    function createStudent($params) {
        return $this->createUser($params, 1);
    }

    public function createAssessment($params) {
        $this->fid = 4;
        $this->fcmd = 2;
        $this->utp = 2;
        $this->ctl = $params['classtitle'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->tem = $params['email'];
        $this->assign = $params['assignmenttitle'];
        $this->ainst = $params['assignmentinstruct'];
        $this->dtstart = $params['assignmentdatestart'];
        $this->dtdue = $params['assignmentdatedue'];

        $this->internet_check = $params['internet_check'];
        $this->report_gen_speed = $params['report_gen_speed'];
        $this->exclude_biblio = $params['exclude_biblio'];
        $this->exclude_quoted = $params['exclude_quoted'];
        $this->exclude_value = $params['exclude_value'];
        $this->late_accept_flag = $params['late_accept_flag'];
        $this->submit_papers_to = $params['submit_papers_to'];

        $this->internet_check = $params['internet_check'];
        $this->report_gen_speed = $params['report_gen_speed'];
        $this->exclude_biblio = $params['exclude_biblio'];
        $this->exclude_quoted = $params['exclude_quoted'];
        $this->exclude_value = $params['exclude_value'];
        $this->late_accept_flag = $params['late_accept_flag'];
        $this->submit_papers_to = $params['submit_papers_to'];
        $this->journal_check = $params['journal_check'];
        $this->s_paper_check = $params['s_paper_check'];
        $this->s_view_report = $params['s_view_report'];

        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=3" "-fid=4" "-utp=' . $this->utp . '"   "-dis=1"  "-cpw=classpass" "-ctl=' . $this->ctl . '"  "-dtstart=' . $this->dtstart . '" "-dtdue=' . $this->dtdue . '" ';
        $actionArgs.='"-tem=' . $this->tem . '" "-assign=' . $this->assign . '"  "-ainst=' . $this->ainst . '" ';

        $optionalArgs.=' "-internet_check=' . $this->internet_check . '" ';
        $optionalArgs.=' "-studentviewreport=' . $this->s_view_report . '" ';
        $optionalArgs.=' "-report_gen_speed=' . $this->report_gen_speed . '" ';
        $optionalArgs.=' "-exclude_biblio=' . $this->exclude_biblio . '" ';
        $optionalArgs.=' "-exclude_quoted=' . $this->exclude_quoted . '" ';
        $optionalArgs.=' "-exclude_type=' . $this->exclude_value . '" ';
        $optionalArgs.=' "-late_accept_flag=' . $this->late_accept_flag . '" ';
        $optionalArgs.=' "-submit_papers_to=' . $this->submit_papers_to . '" ';
        $optionalArgs.=' "-s_paper_check=' . $this->s_paper_check . '" ';
        $optionalArgs.=' "-journal_check=' . $this->journal_check . '" ';

        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs . ' ' . $optionalArgs;

        $results = shell_exec($command);
        try {
            $xml = new SimpleXMLElement($results);
        } catch (Exception $e) {

            return array('message' => 'An error occured, cannot continue processing this request. Please
                     contact system administrator',
                'code' => '413',
                'object' => "",
                'xmlobject' => "");
        }


        $message = $xml->rmessage;
        $rcode = $xml->rcode;
        $object = ($xml->object) ? $xml->object : null;
        $objectID = ($xml->objectID) ? $xml->objectID : null;

        return $rcode.'|'.$message;
        
    }

    private function writeToFile() {
        $myFile = $this->debugfilename;
        $fh = fopen($myFile, 'w') or die("can't open file");
        $stringData = $command;
        fwrite($fh, $stringData);
        fclose($fh);
    }

    /**
     * Method to create a user on Turnitin
     *
     * @param array $params
     * @param integer $type
     *
     */
    function createUser($params, $type = 1) {
        $this->fid = 1;
        $this->fcmd = 2;
        $this->utp = $type;
        $this->uid = $params['username'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];

        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=1" "-fid=1" "-utp=' . $this->utp . '"';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;

        $this->debug($command);
        //$results = shell_exec($command);

        return $this->getXMLResult($results);
    }

    function changePassword($params) {
        $this->fid = 9;
        $this->fcmd = 2;
        $this->utp = 2;

        $this->npw = 'nitsckie'; //$params['password'];
        $this->newupw = 'nitsckie'; //$params['password'];
        $this->upw = '123456'; //$params['password'];
        $this->uid = $params['username'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];

        return $this->doPost();
    }

    public function downloadPaper($params) {
        $this->fid = 7;
        $this->fcmd = 2;
        $this->utp = 1;

        $this->upw = $params['password'];
        $this->uid = $params['username'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->tem = $params['instructoremail'];
        $this->assign = $params['assign'];
        $this->oid = $params['objectid'];
        $this->ctl = $params['classtitle'];
        $this->cpw = $params['classpassword'];
        $this->diagnostic = 0;
        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=11" "-fid=7" "-utp=' . $this->utp . '" "-tem=' . $this->tem . '"  "-assign=' . $this->assign . '" "-oid=' . $this->oid . '" "-ctl=' . $this->ctl . '" ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;
        echo $command;
        $results = shell_exec($command);
        echo $results;
        //   return $this->getXMLResult($results);
    }

    public function debug($message) {
        $myFile = "/var/www/kim/wip/elearning/turnitin-uploads/debug.txt";
        $fh = fopen($myFile, 'a') or die("can't open file");
        fwrite($fh, $message . '\n');
        fclose($fh);
    }

    public function submitPaper($params) {
        $this->fid = 5;
        $this->fcmd = 2;
        $this->utp = 1;
        $this->ptype = 2; //file
        $this->upw = $params['password'];
        $this->uid = $params['username'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->tem = $params['instructoremail'];
        $this->assign = $params['assign'];
        $this->ptl = $params['papertitle'];
        $this->filepath = $params['filepath'];
        $ie = $params['instructoremail'];
        if ($ie == '') {
            $this->tem = 'undefined';
        }
        $xctl = $params['classtitle'];

        if ($xctl == '') {
            $xctl = 'Undefined';
        }
        $this->ctl = $xctl;

        //$this->cid = $params['classid'];
        $this->diagnostic = 0;
        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=6" "-fid=5" "-utp=' . $this->utp . '" "-ptl=' . $this->ptl . '" "-filepath=' . $this->filepath . '"  "-ptype=' . $this->ptype . '" "-ctl=' . $this->ctl . '" "-tem=' . $this->tem . '"  "-assign=' . $this->assign . '"  ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;
        //echo $command;
        //die();
        /*
          $myFile = "/home/davidwaf/tttestFile.txt";
          $fh = fopen($myFile, 'w') or die("can't open file");
          fwrite($fh, $command);
          fclose($fh);

          die(); */

        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    public function submitPaperAsLecturer($params) {
        $this->fid = 5;
        $this->fcmd = 2;
        $this->utp = 2;
        $this->ptype = 2; //file
        $this->upw = $params['password'];
        $this->uid = $params['username'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];

        $this->pfn = $params['pfirstname'];
        $this->pln = $params['plastname'];
        $this->tem = $params['instructoremail'];
        $this->assign = $params['assign'];
        $this->ptl = $params['papertitle'];
        $this->filepath = $params['filepath'];
        $ie = $params['instructoremail'];
        if ($ie == '') {
            $this->tem = 'undefined';
        }
        $xctl = $params['classtitle'];

        if ($xctl == '') {
            $xctl = 'Undefined';
        }
        $this->ctl = $xctl;

        //$this->cid = $params['classid'];
        $this->diagnostic = 0;
        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=15" "-fid=5" "-utp=' . $this->utp . '" "-ptl=' . $this->ptl . '" "-filepath=' . $this->filepath . '"  "-ptype=' . $this->ptype . '" "-ctl=' . $this->ctl . '" "-tem=' . $this->tem . '"  "-assign=' . $this->assign . '" "-pfn=' . $this->pfn . '" "-pln=' . $this->pln . '" ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;
        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    /**
     * Method for a student to be
     * assigned to a class
     *
     * @param array $params
     * @return boolean
     */
    public function joinClass($params) {
        $this->fid = 3;
        $this->fcmd = 2;
        $this->utp = 1;

        $this->upw = $params['password'];
        $this->uid = $params['username'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->tem = $params['instructoremail'];

        $ie = $params['instructoremail'];
        if ($ie == '') {
            $this->tem = 'undefined';
        }
        $xctl = $params['classtitle'];

        if ($xctl == '') {
            $xctl = 'Undefined';
        }
        $this->ctl = $xctl;

        //$this->cid = $params['classid'];
        $this->diagnostic = 0;
        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=5" "-fid=3" "-utp=' . $this->utp . '"  "-ctl=' . $this->ctl . '" "-tem=' . $this->tem . '"';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;
        $results = shell_exec($command);

        return $this->getXMLResult($results);
    }

    /**
     * Method to get the admin stats
     *
     * @param array $params
     * @return array
     */
    public function adminStats($params) {
        $this->fid = 12;
        $this->fcmd = 1;
        $this->utp = 3;

        return $this->doGet();
    }

    /////////////////////////
    /// LECTURER FUNCTIONS///
    /////////////////////////

    public function createClass($params) {
        $this->fid = 2;
        $this->fcmd = 2;
        $this->utp = 2;

        $this->ctl = $params['classtitle'];
        $this->cpw = $params['classpassword'];

        $this->cid = $params['classid'];
        $this->dtstart = $params['dtstart'];
        $this->dtdue = $params['dtdue'];
        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';

        $actionArgs = '"-action=2" "-fid=2" "-utp=' . $this->utp . '"    "-cpw=classpass" "-ctl=' . $this->ctl . '"  "-dtstart=' . $this->dtstart . '" "-dtdue=' . $this->dtdue . '" ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;

        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    public function editAssessment($params) {
        $this->fid = 4;
        $this->fcmd = 7;
        $this->utp = 2;
        $this->ctl = $params['classtitle'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->tem = $params['email'];
        $this->assign = $params['assignmenttitle'];
        $this->ainst = $params['assignmentinstruct'];
        $this->dtstart = $params['assignmentdatestart'];
        $this->dtdue = $params['assignmentdatedue'];

        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';


        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=3" "-fid=4" "-utp=' . $this->utp . '"   "-ctl=' . $this->ctl . '" ';
        $actionArgs.='"-tem=' . $this->tem . '" "-assign=' . $this->assign . '" ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs . ' ' . $optionalArgs;
        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    public function updateAssessment($params) {
        $this->fid = 4;
        $this->fcmd = 3;
        $this->utp = 2;
        $this->ctl = $params['classtitle'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->tem = $params['instructoremail'];
        $this->assign = $params['assignmenttitle'];
        $this->ainst = $params['assignmentinstruct'];
        $this->dtstart = $params['assignmentdatestart'];
        $this->dtdue = $params['assignmentdatedue'];

        $this->internet_check = $params['internet_check'];
        $this->report_gen_speed = $params['report_gen_speed'];
        $this->exclude_biblio = $params['exclude_biblio'];
        $this->exclude_quoted = $params['exclude_quoted'];
        $this->exclude_value = $params['exclude_value'];
        $this->late_accept_flag = $params['late_accept_flag'];
        $this->submit_papers_to = $params['submit_papers_to'];

        $this->internet_check = $params['internet_check'];
        $this->report_gen_speed = $params['report_gen_speed'];
        $this->exclude_biblio = $params['exclude_biblio'];
        $this->exclude_quoted = $params['exclude_quoted'];
        $this->exclude_value = $params['exclude_value'];
        $this->late_accept_flag = $params['late_accept_flag'];
        $this->submit_papers_to = $params['submit_papers_to'];
        $this->journal_check = $params['journal_check'];
        $this->s_paper_check = $params['s_paper_check'];
        $this->s_view_report = $params['s_view_report'];

        $objUser = $this->getObject("user", "security");


        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=3" "-fid=4" "-utp=' . $this->utp . '"   "-dis=1"  "-cpw=classpass" "-ctl=' . $this->ctl . '"  "-dtstart=' . $this->dtstart . '" "-dtdue=' . $this->dtdue . '" ';
        $actionArgs.='"-tem=' . $this->tem . '" "-assign=' . $this->assign . '"  "-ainst=' . $this->ainst . '" ';

        $optionalArgs.=' "-internet_check=' . $this->internet_check . '" ';
        $optionalArgs.=' "-studentviewreport=' . $this->s_view_report . '" ';
        $optionalArgs.=' "-report_gen_speed=' . $this->report_gen_speed . '" ';
        $optionalArgs.=' "-exclude_biblio=' . $this->exclude_biblio . '" ';
        $optionalArgs.=' "-exclude_quoted=' . $this->exclude_quoted . '" ';
        $optionalArgs.=' "-exclude_type=' . $this->exclude_value . '" ';
        $optionalArgs.=' "-late_accept_flag=' . $this->late_accept_flag . '" ';
        $optionalArgs.=' "-submit_papers_to=' . $this->submit_papers_to . '" ';
        $optionalArsdfgs.=' "-s_paper_check=' . $this->s_paper_check . '" ';
        $optionalArgs.=' "-journal_check=' . $this->journal_check . '" ';

        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs . ' ' . $optionalArgs;
        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    public function deleteAssessment($params) {
        $this->fid = 4;
        $this->fcmd = 4;
        $this->utp = 2;
        $this->ctl = $params['classtitle'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->tem = $params['email'];
        $this->assign = $params['assignmenttitle'];
        $this->ainst = $params['assignmentinstruct'];
        $this->dtstart = $params['assignmentdatestart'];
        $this->dtdue = $params['assignmentdatedue'];

        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=3" "-fid=4" "-utp=' . $this->utp . '"   "-dis=1"  "-cpw=classpass" "-ctl=' . $this->ctl . '"  "-dtstart=' . $this->dtstart . '" "-dtdue=' . $this->dtdue . '" ';
        $actionArgs.='"-tem=' . $this->tem . '" "-assign=' . $this->assign . '"  "-ainst=' . $this->ainst . '" ';

        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs . ' ' . $optionalArgs;

        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    /*  public function deleteAssessment($params) {
      $this->fid = 4;
      $this->fcmd = 4;
      $this->utp = 2;
      $this->ufn = $params['firstname'];
      $this->uln = $params['lastname'];
      $this->uem = $params['email'];
      $this->assign = $params['assignmenttitle'];
      $this->ctl = $params['classtitle'];
      $baseArgs='"-aid='.$this->aid.'" "-sharedsecretekey='.$this->shared_secret_key.'" "-diagnostic='.$this->diagnostic;
      $baseArgs.=   '" "-encrypt='.$this->encrypt.'" "-fcmd='.$this->fcmd.'" "-uem='.$this->uem.'" "-upw='.$this->upw.'" "-ufn='.$this->ufn.'" "-uln='.$this->uln.'"';
      $actionArgs='"-action=13" "-fid=4" "-utp='.$this->utp.'"';
      $actionArgs.='"-assign='.$this->assign.'" "-ctl='.$this->ctl .'"  ';
      $command='java -jar '.$this->getResourcePath('turnitin.jar').' '.$baseArgs.' '.$actionArgs;

      $results = shell_exec($command);
      return $this->getXMLResult($results);
      } */

    /**
     * Method to get a list of assessments
     *
     * @param array $params
     */
    public function listSubmissions($params) {
        $this->fid = 10;
        $this->fcmd = 2;
        $this->utp = 2;
        $this->oid = '100461236';
        $this->ctl = $params['classtitle'];
        $this->assign = $params['assignmenttitle'];
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        return $this->doPost();
    }

    public function checkForSubmissionAsLecturer($params) {
        $this->fid = 11;
        $this->fcmd = 2;
        $this->utp = 2; //student
        //var_dump($params);die;
        $this->assign = $params['assignmenttitle'];
        $this->tem = $params['instructoremail'];
        $this->ctl = $params['classtitle'];
        //$this->cid = null;
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];

        $this->diagnostic = 0;

        error_log("going to redirect to TII now...");

        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';

        $actionArgs = '"-action=8" "-fid=11" "-utp=' . $this->utp . '"  "-ctl=' . $this->ctl . '" ';
        $actionArgs.=' "-assign=' . $this->assign . '"  "-tem=' . $this->tem . '"  ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;

        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    public function checkForSubmission($params) {
        $this->fid = 11;
        $this->fcmd = 2;
        $this->utp = 1; //student
        //var_dump($params);die;
        $this->assign = $params['assignmenttitle'];
        $this->tem = $params['instructoremail'];
        $this->ctl = $params['classtitle'];
        //$this->cid = null;
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];

        $this->diagnostic = 0;

        error_log("going to redirect to TII now...");

        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';

        $actionArgs = '"-action=8" "-fid=11" "-utp=' . $this->utp . '"  "-ctl=' . $this->ctl . '" ';
        $actionArgs.=' "-assign=' . $this->assign . '"  "-tem=' . $this->tem . '"  ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;
        /* $myFile = "/dwaf/turnitin/nxa4.txt";
          $fh = fopen($myFile, 'w') or die("can't open file");
          $stringData = $command;
          fwrite($fh, $stringData);
          fclose($fh);
         */
        ////die();
        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    /////////////////////////
    /// STUDENT FUNCTIONS////
    /////////////////////////

    public function redirectSubmit($params) {
        $this->fid = 5;
        $this->fcmd = 1;
        $this->utp = 1; //student
        //var_dump($params);die;
        $this->assign = $params['assignmenttitle'];
        $this->tem = $params['instructoremail'];
        $this->ctl = $params['classtitle'];
        //$this->cid = null;
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->upw = $params['password'];
        //$this->sessionid = $params['sessionid'];


        $this->diagnostic = 0;

        //var_dump($params);
        //var_dump($this->ctl);die;
        error_log("going to redirect to TII now...");
        //print $this->getParams();
        //var_dump($params);die;
        $this->doGet();
    }

    public function getCode($xml) {
        $tiixml = new SimpleXMLElement($xml);
        return $tiixml->rcode;
    }

    public function getReport($params, $contextcode) {
        $objUser = $this->getObject("user", "security");
        $this->fid = 6;
        $this->fcmd = 1;
        $this->diagnostic = 1;
        $this->oid = $this->getParam('objectid');
        $this->utp = $objUser->isContextLecturer($params['userid'], $contextcode) ? 2 : 1;
        $this->diagnostic = 0;

        $this->upw = $this->upw = $this->_objSysConfig->getValue('password', 'turnitin');
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->uid = $params['userid'];
        $this->tem = $params['instructoremail'];
        $this->assign = $params['assignmenttitle'];
        $this->ctl = $params['classtitle'];


//print_r($params);
        return $this->doGet();
        //print $this->doPost();
        //$result = $this->doPost();
        //    print $result;//->rmessage;
    }

    public function deleteSubmission($params) {
        $this->fid = 8;
        $this->fcmd = 2;
        $this->utp = 1; //student
        //var_dump($params);die;
        $this->assign = $params['assignmenttitle'];
        $this->tem = $params['instructoremail'];
        $this->ctl = $params['classtitle'];
        //$this->cid = null;
        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->oid = $params['oid'];
        $this->diagnostic = 0;

        $this->assign = $params['assignmenttitle'];
        $this->ctl = $params['classtitle'];


        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';

        $actionArgs = '"-action=14" "-fid=8" "-utp=' . $this->utp . '"  "-ctl=' . $this->ctl . '" ';
        $actionArgs.=' "-oid=' . $this->oid . '" "-assign=' . $this->assign . '" "-tem=' . $this->tem . '"  ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;

        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    public function viewSubmission($param) {
        
    }

    public function isSuccess($fid, $code) {
        return in_array($code, $this->successCodes[$fid]);
    }

    public function getSubmissions($params, $contextcode) {

        $this->fid = 10;
        $this->fcmd = 2;
        $objUser = $this->getObject("user", "security");
        $this->utp = 2; // $objUser->isContextLecturer($params['userid'],$contextcode)?2:1;
        //var_dump($params);die;
        $this->assign = $params['assignmenttitle'];
        $this->tem = $params['instructoremail'];
        $this->ctl = $params['classtitle'];

        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];

        if ($params['instructoremail'] != $objUser->email()) {
            $user = $this->submitted->getUser($params['instructoremail']);
            $this->ufn = $user['firstname'];
            $this->uln = $user['surname'];
            $this->uem = $params['instructoremail'];
        }


        $this->diagnostic = 0;


        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';

        $actionArgs = '"-action=10" "-fid=10" "-utp=' . $this->utp . '"  "-tem=' . $this->tem . '" "-ctl=' . $this->ctl . '" ';
        $actionArgs.=' "-assign=' . $this->assign . '"   ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;
        /* $myFile = "/dwaf/turnitin/nxa3.txt";
          $fh = fopen($myFile, 'w') or die("can't open file");
          $stringData = $command;
          fwrite($fh, $stringData);
          fclose($fh);
          die(); */
        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    public function getScore($params) {
        $this->fid = 6;
        $this->fcmd = 2;
        $this->utp = 1;

        $this->assign = $params['assignmenttitle'];
        //$this->tem = $params['instructoremail'];
        $this->ctl = $params['classtitle'];


        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->upw = $params['password'];
        $this->oid = $params['objectid'];
        //var_dump($params);die;
        $this->diagnostic = 0;

        error_log("get score for $this->ctl -> $this->assign -> $this->oid");
        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';

        $actionArgs = '"-action=7" "-fid=6" "-utp=' . $this->utp . '"  "-oid=' . $this->oid . '" ';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;

        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    /**
     * Login Session
     *
     * The API function will log in the user
     * described by the user email, first name,
     * last name, and user type
     */
    function loginSession($params) {
        $this->fid = 17;
        $this->fcmd = 2;
        $this->utp = 2;

        $this->ufn = $params['firstname'];
        $this->uln = $params['lastname'];
        $this->uem = $params['email'];
        $this->upw = ""; //$params['password'];
        //$this->userid = 18882189;
        //$this->sessionid = "7de114699da17b7df28605e403c7f275";
//    	print_r($this->getParams());
        $this->log('logSession', $params['firstname'] . ' ' . $params['lastname']);
        $baseArgs = '"-aid=' . $this->aid . '" "-sharedsecretekey=' . $this->shared_secret_key . '" "-diagnostic=' . $this->diagnostic;
        $baseArgs.= '" "-encrypt=' . $this->encrypt . '" "-fcmd=' . $this->fcmd . '" "-uem=' . $this->uem . '" "-upw=' . $this->upw . '" "-ufn=' . $this->ufn . '" "-uln=' . $this->uln . '"';
        $actionArgs = '"-action=9" "-fid=17" "-utp=' . $this->utp . '"  "-fcmd=' . $this->fcmd . '"';
        $command = 'java -jar ' . $this->getResourcePath('turnitin.jar') . ' ' . $baseArgs . ' ' . $actionArgs;
        $results = shell_exec($command);
        return $this->getXMLResult($results);
    }

    function log($function, $message) {
        error_log("\nTURNITIN DEBUG: $function() -> :::: -> $message");
    }

}
