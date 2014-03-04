<?php
/**
 * this class contains utilities for sending alerts as emails, tweets, etc
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

define('WINDOWS', 'WINDOWS');
define('LINUX', 'LINUX');
define('UNKNOWN', 'UNKNOWN');

/**
 * Class the records the pages a user has visited.
 *
 * It doesn't contain the content of pages, just the index to track which pages
 * are translations of each other.
 *
 * @category  Chisimba
 * @package   contextcontent
 * @author    Davi Wafula
 * @copyright @2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 */

class alertutils extends object {
    function init() {
        $uname = php_uname('s');
        switch ($uname) {
        	case "Windows":
        	case "Windows NT":
        		define('OS', WINDOWS);
        		break;
        	case "Linux":
        	case "Unix":
        		define('OS', LINUX);
        		break;
        	default:
        	    define('OS', UNKNOWN);
        		//exit ("This program requires Microsoft ® Windows or Linux.");
        }
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser=$this->getObject('user','security');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objGroupOps=$this->getObject('groupops','groupadmin');
        $this->objGroups = $this->getObject('groupadminmodel','groupadmin');
        $this->objManageGroups = $this->getObject('managegroups', 'contextgroups');
    }
    function sendEmailAlert($contextCode, $contextTitle) {
        // Student list
        $students = $this->objManageGroups->contextUsers('Students', $contextCode, array( 'tbl_users.userid','emailaddress', 'firstname', 'surname'));
        // Subject
        $subjectTemplate = $this->objSysConfig->getValue('CONTEXTCONTENT_EMAIL_ALERT_SUB', 'contextcontent');
        $subjectTemplate = $this->objLanguage->abstractText($subjectTemplate);
        $subjectTemplate = str_replace("[-course-]", $contextTitle, $subjectTemplate);
        $subjectTemplate = str_replace('\n', "\n", $subjectTemplate);
        // Body
        $bodyTemplate = $this->objSysConfig->getValue('CONTEXTCONTENT_EMAIL_ALERT_BDY', 'contextcontent');
        //
        $bodyTemplate = $this->objLanguage->abstractText($bodyTemplate);
        //trigger_error($bodyTemplate);
        $contextredirecturi = html_entity_decode($this->uri(array(), 'contextcontent'));
        $url = $this->uri(array('action'=>'joincontextrequirelogin', 'contextcode'=>$contextCode, 'contextredirecturi'=> $contextredirecturi), 'context', '', FALSE, TRUE, TRUE); // , 'passthroughlogin'=>'true' //contextcontent
        //http://..localhost/chisimba/app/index.php?module=contextcontent

        /*
        $contextredirecturi = html_entity_decode($this->uri(array('action'=>'view', 'id'=>$id), 'assignment'));
        $link = new link($this->uri(array('action'=>'joincontext', 'contextcode'=>$this->objContext->getContextCode(), 'contextredirecturi'=> $contextredirecturi), 'context'));
        */

        //$url = str_replace('&amp;', '&', $url);
        $bodyTemplate = str_replace("[-link-]", $url, $bodyTemplate);
        //trigger_error($bodyTemplate);
        $bodyTemplate = str_replace("[-course-]", '\''.$contextTitle.'\'', $bodyTemplate);
        //trigger_error($bodyTemplate);
        $bodyTemplate = str_replace("[-instructor-]", $this->objUser->getTitle($this->objUser->userId()).' '.$this->objUser->fullname(), $bodyTemplate);
        //trigger_error($bodyTemplate);
        $bodyTemplate = str_replace('\n', chr(0x0A), $bodyTemplate); //"\n"
        //trigger_error($bodyTemplate);
        if (OS == WINDOWS) {
            $bodyTemplate = str_replace("\n.", "\n..", $bodyTemplate);
            //trigger_error($bodyTemplate);
        }
        // Send out the emails
        $objMailer = $this->newObject('email', 'mail');
        foreach ($students as $student) {
            $subject = $subjectTemplate;
            $body = $bodyTemplate;
            //
            $body = str_replace("[-firstname-]", $student['firstname'], $body);
            $body = str_replace("[-lastname-]", $student['surname'], $body);
            $body = strip_tags($body);
            //trigger_error($student['emailaddress']);
            //$addressArr = ;
            //trigger_error(var_export($addressArr, TRUE));
            //$objMailer->clearAddresses();
            //$objMailer->clearCCs();
            //$objMailer->clearBCCs();
            $objMailer->setValue('to', array($student['emailaddress']));
            $objMailer->setValue('from', $this->objUser->email());
            $objMailer->setValue('fromName', $this->objUser->fullname());
            $objMailer->setValue('subject', $subject);
            $objMailer->setValue('body', $body);
            $objMailer->setValue('AltBody', $body);
            $objMailer->send();
        }
    }
}
?>