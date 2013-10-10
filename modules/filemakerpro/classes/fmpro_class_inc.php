<?php
/**
 * Filemaker Pro operations class
 *
 * Do stuff with Filemaker Pro
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
 * @package   filemakerpro
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Filemaker Pro operations class
 *
 * Do stuff with Filemaker Pro
 *
 * @category  Chisimba
 * @package   filemakerpro
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class fmpro extends object {

    public $objUser;
    public $objLanguage;
    public $conn;
    public $objSysConfig;
    public $fm;
    public $scripts;
    public $layouts;
    public $databases;
    public $cgi;
    public $displayDateFormat = '%m/%d/%Y';
    public $displayTimeFormat = '%I:%M %P';
    public $displayDateTimeFormat = '%m/%d/%Y %I:%M %P';
    public $submitDateOrder = 'mdy';
    public $username;
    public $password;
    public $record;
    public $children;

    /**
     * Standard constructor
     *
     * method to retrieve the action from the
     * querystring, and instantiate the user and lanaguage objects
     *
     * @access public
     * @param  void
     * @return void
     */
    public function init() {
        try {
            // Include the needed libs from resources
            include ($this->getResourcePath ( 'FileMaker.php' ));
            // Get the security object
            $this->objUser = $this->getObject ( "user", "security" );
            //Create an instance of the language object
            $this->objLanguage = $this->getObject ( "language", "language" );
            // Get the sysconfig variables for the FMP user to set up the connection.
            $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            // regular config object
            $this->objConfig = $this->getObject ( 'altconfig', 'config' );
            // create an instance and connect to FMP
            $this->connFMP ();

        } catch ( customException $e ) {
            // Bail gracefully
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Method to set up a connection to FMP
     *
     * Simply sets up the connection to the FMP host
     * and returns a bunch of commonly used properties for use later on.
     * All the variables are used from the standard Chisimba sysconfig class
     * so that this class is totally reusable and, of course, user configurable
     * if and when that is ever needed.
     *
     * @access  private
     * @return  void
     */
    public function connFMP() {
        // Create an instance (reusable) of the Filemaker Pro code.
        $this->fm = new FileMaker ( );

        // Get the values we need from sysconfig
        $this->fm->setProperty ( 'database', $this->objSysConfig->getValue ( 'fmdb', 'filemakerpro' ) );
        $this->fm->setProperty ( 'hostspec', $this->objSysConfig->getValue ( 'fmhost', 'filemakerpro' ) );
        $this->fm->setProperty ( 'username', $this->objSysConfig->getValue ( 'fmuser', 'filemakerpro' ) );
        $this->fm->setProperty ( 'password', $this->objSysConfig->getValue ( 'fmpass', 'filemakerpro' ) );

        // Get some basic stuff that we will need later like layouts and scripts lists in arrays.
        $this->scripts = $this->fm->listScripts ();
        $this->layouts = $this->fm->listLayouts ();
        // Meh, probably not needed, but get a list of db's also...
        $this->databases = $this->fm->listDatabases ();

        $this->displayDateFormat = '%m/%d/%Y';
        $this->displayTimeFormat = '%I:%M %P';
        $this->displayDateTimeFormat = '%m/%d/%Y %I:%M %P';
        $this->submitDateOrder = 'mdy';

        return;
    }

    /**
     * Returns a layout by name
     *
     * @access public
     * @param string $layoutName
     * @return array as a public property
     */
    public function grabLayout($layoutName) {
        return $this->fm->getLayout ( $layoutName );
    }

    public function simpleAuth($username, $password) {
        $layoutName = 'Form: Person';
        $findCommand = $this->fm->newFindCommand ( $layoutName );
        $findCommand->addFindCriterion ( 'UserName', $username );
        $findCommand->addFindCriterion ( 'Password', $password );
        $result = $findCommand->execute ();

        if (FileMaker::isError ( $result )) {
            throw new customException ( $result->getMessage () . ' (error ' . $result->code . ')' );
            // return FALSE;
        } else {
            $this->username = $username;
            $this->password = $password;
            $record = $result->getFirstRecord ();
            $this->record = $record;
            return TRUE;
        }
    }

    public function authenticatePerson($username, $password) {
        $layoutName = 'Form: Person';
        $findCommand = $this->fm->newFindCommand ( $layoutName );
        $findCommand->addFindCriterion ( 'UserName', $username );
        $findCommand->addFindCriterion ( 'Password', $password );
        $result = $findCommand->execute ();
        if (FileMaker::isError ( $result )) {
            throw new customException ( $result->getMessage () . ' (error ' . $result->code . ')' );
            // return FALSE;
        } else {
            $this->username = $username;
            $this->password = $password;
            $record = $result->getFirstRecord ();
            $this->record = $record;
            // get the users info
            $recid = $record->getRecordId ();
            $rec = $this->fm->getRecordById ( $layoutName, $recid );
            $userinfo = array ();
            $userinfo ['username'] = $rec->getField ( 'UserName' );
            $userinfo ['surname'] = $rec->getField ( 'LastName' );
            $userinfo ['firstname'] = $rec->getField ( 'FirstName' );
            $userinfo ['emailaddress'] = $rec->getField ( 'Email' );
            $userinfo ['userid'] = $recid;
            $userinfo ['title'] = '';
            $userinfo ['logins'] = '0';
            $userinfo ['password'] = '--FMP--';

            return $userinfo;
        }
    }

    public function getChildrenOf($parent) {
        // This depends on the portal in the current layout
        $relatedSet = $parent->getRelatedSet('Child');
        $children = array();
        foreach ( $relatedSet as $record ) {
            $child = $this->getPersonRecord($record->getRecordId());
            if ( $child != NULL ) {
                array_push($children, $child);
            }
        }

        return $children;

    }

    protected function getPersonRecord($recordId) {
        $findCommand = $this->fm->newFindCommand('Form: Person');
        $findCommand->setRecordId( $recordId );
        $result = $findCommand->execute();
        if (FileMaker::isError($result)) {
            return NULL;
        }

        return $result->getFirstRecord();
    }

    public function getStudentRecord($person) {
        $findCommand = $this->fm->newFindCommand('Form: Student');
        $findCommand->addFindCriterion(
            'Person::FirstName',
            $person->getField('FirstName')
            );
        $findCommand->addFindCriterion(
            'Person::MiddleName',
            $person->getField('MiddleName')
            );
        $findCommand->addFindCriterion(
            'Person::LastName',
            $person->getField('LastName')
            );
        $result = $findCommand->execute();
        if (FileMaker::isError($result)) {
            return NULL;
        }

        return $result->getFirstRecord();
    }

    public function getStudentById($id) {
        $layoutName = 'Form: Student';
        $rec = $this->fm->getRecordById ( $layoutName, $id );
        // create a pretty array of the info...
        // var_dump($rec);
        $student = array();
        $student['dob'] = $rec->getField("Person::DateOfBirth");
        $student['lastname'] = $rec->getField("Person::LastName");
        $student['firstname'] = $rec->getField("Person::FirstName");
        $student['middlename'] = $rec->getField("Person::MiddleName");
        $student['race'] = $rec->getField("Race");
        $student['gender'] = $rec->getField("Person::Gender");

        // Scheduling info
        $student['firstyear'] = $rec->getField("EntryYear");
        $student['lastyear'] = $rec->getField("ExitYear");
        $student['status'] = $rec->getField("Status");
        $student['class'] = $rec->getField("ClassId");
        $student['schedtype'] = $rec->getField("ScheduleTypeId");
        $student['sched'] = $rec->getField("actualSchedule");
        $student['recid'] = $id;

        return $student;
    }

    public function getAuthenticatedPerson($username) {
        // FIXME: Prolly should make a form just for this purpose
        $findCommand = $this->fm->newFindCommand('Form: Person');
        $findCommand->addFindCriterion('UserName', $username);
        $result = $findCommand->execute();
        if (FileMaker::isError($result)) {
            return NULL;
        }

        return $result->getFirstRecord();
    }



    public function getUserInfo() {
        $layoutName = 'Form: Person';
        $recid = $this->record->getRecordId ();
        $rec = $this->fm->getRecordById ( $layoutName, $recid );
        $userinfo = array ();
        $userinfo ['username'] = $rec->getField ( 'UserName' );
        $userinfo ['surname'] = $rec->getField ( 'LastName' );
        $userinfo ['firstname'] = $rec->getField ( 'FirstName' );
        $userinfo ['emailaddress'] = $rec->getField ( 'Email' );
        $userinfo ['userid'] = $recid;
        $userinfo ['recid'] = $recid;
        $userinfo ['title'] = '';
        $userinfo ['logins'] = '0';
        $userinfo ['password'] = '--FMP--';

        return $userinfo;
    }

    public function getUserProfile() {
        $layoutName = 'Form: Person';
        //$layout = $this->grabLayout($layoutName);
        //$relatedsets = $layout->listRelatedSets($layout);
        $findCommand = $this->fm->newFindCommand ( $layoutName );
        $findCommand->addFindCriterion ( 'UserName', $this->username );
        $findCommand->addFindCriterion ( 'Password', $this->password );
        $result = $findCommand->execute ();
        if (FileMaker::isError ( $result )) {
            return FALSE;
        } else {
            $parentRecord = $result->getFirstRecord ();
            $parentRecordLayout = $parentRecord->getLayout ();
            $parentRecordPortals = $parentRecordLayout->getRelatedSets ();
            $portalName = array_shift ( $parentRecordPortals )->getName ();
            //$parentID = $parentRecord->getRecordID();


            //var_dump($portalName); die();


            $kids = $parentRecord->getRelatedSet ( $portalName );
            return array ($parentRecord, $kids );
        }
    }

    public function getUsersIdByUsername($username) {
        $layoutName = 'Form: Person';
        $findCommand = $this->fm->newFindCommand ( $layoutName );
        $findCommand->addFindCriterion ( 'UserName', $username );
        $result = $findCommand->execute ();
        if (FileMaker::isError ( $result )) {
            return FALSE;
        } else {
            $parentRecord = $result->getFirstRecord ();
            $id = $parentRecord->getRecordId ();
            return $id;
        }
    }

    public function makeNewFindCommand($layoutName) {
        return $this->fm->newFindCommand ( $layoutName );
    }

    public function getDetailsById($id) {
        $layoutName = 'Form: Person';
        $rec = $this->fm->getRecordById ( $layoutName, $id );
        $recid = $rec->getRecordId ();
        $userinfo = array ();
        $userinfo ['recid'] = $recid;
        $userinfo ['username'] = $rec->getField ( 'UserName' );
        $userinfo ['surname'] = $rec->getField ( 'LastName' );
        $userinfo ['firstname'] = $rec->getField ( 'FirstName' );
        $userinfo ['emailaddress'] = $rec->getField ( 'Email' );
        $userinfo ['emailpriv'] = $rec->getField ( 'IsEmailPrivate' );
        $userinfo ['street'] = $rec->getField ( 'Address' );
        $userinfo ['cellphone'] = $rec->getField ( 'Cell' );
        $userinfo ['city'] = $rec->getField ( 'City' );
        $userinfo ['employer'] = $rec->getField ( 'Employer' );
        $userinfo ['occupation'] = $rec->getField ( 'Occupation' );
        $userinfo ['state'] = $rec->getField ( 'State' );
        $userinfo ['zip'] = $rec->getField ( 'Zip' );
        $userinfo ['homephone'] = $rec->getField ( 'HomePhone' );
        $userinfo ['workphone'] = $rec->getField ( 'WorkPhone' );
        $userinfo ['cellpriv'] = $rec->getField ( 'IsCellPrivate' );

        return $userinfo;
    }

    public function editRecord($layoutName, $recid, $values) {
        $edit = $this->fm->newEditCommand ( $layoutName, $recid, $values );
        $result = $edit->execute ();
        if (FileMaker::isError ( $result )) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function sendStaffMail($user) {
        // Get the sysconfig variable for staff mail
        $staffmail = $this->objSysConfig->getValue ( 'mod_sis_staffmail', 'sis' );
        $siteEmail = $this->objConfig->getsiteEmail ();
        $siteName = $this->objConfig->getSiteName ();
        $mail = '
Dear Staff<br />
<br />
On [[DATE]], [[FIRSTNAME]] [[LASTNAME]] updated their profile with new information.<br />
<br />
Sincerely,<br />
[[SITENAME]] Notifications <br />
[[SITEADDRESS]]
<br />
<br />';

        $mail = str_replace ( '[[FIRSTNAME]]', $user ['firstname'], $mail );
        $mail = str_replace ( '[[LASTNAME]]', $user ['lastname'], $mail );
        $mail = str_replace ( '[[DATE]]', date ( 'r' ), $mail );
        $mail = str_replace ( '[[SITENAME]]', $siteName, $mail );
        $mail = str_replace ( '[[SITEADDRESS]]', $this->objConfig->getsiteRoot (), $mail );

        $objMailer = $this->getObject ( 'email', 'mail' );
        $objMailer->setValue ( 'to', array ($staffmail ) );
        $objMailer->setValue ( 'from', $siteEmail );
        $objMailer->setValue ( 'fromName', $siteName . ' Notifications' );
        $objMailer->setValue ( 'subject', 'Profile Update: ' . $siteName );
        $objMailer->setValue ( 'body', strip_tags ( $mail ) );
        $objMailer->setValue ( 'AltBody', strip_tags ( $mail ) );

        if ($objMailer->send ()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}