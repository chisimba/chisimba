<?php
/**
 * SIS forms class
 *
 * Forms for the SIS system
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
 * @package   sis
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
 * SIS Forms class
 *
 * Forms for the SIS system
 *
 * @category  Chisimba
 * @package   sis
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class sisforms extends object {
    public $objLanguage;
    public $objUser;
    public $sysConfig;
    public $objFMPro;
    public $required;

    /**
     * Standard init function to __construct the class
     *
     * @param void
     * @return void
     * @access public
     */
    public function init() {
        try {
            $this->objLanguage = $this->getObject ( 'language', 'language' );
            $this->objUser = $this->getObject ( "user", "security" );
            $this->sysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objFMPro = $this->getObject('fmpro', 'filemakerpro');
            // Load up the HTML elemnts we may need
            $this->loadClass ( 'form', 'htmlelements' );
            $this->loadClass ( 'textinput', 'htmlelements' );
            $this->loadClass ( 'textarea', 'htmlelements' );
            $this->loadClass ( 'button', 'htmlelements' );
            $this->loadClass ( 'checkbox', 'htmlelements' );
            $this->loadClass ( 'dropdown', 'htmlelements' );
            $this->loadClass ( 'label', 'htmlelements' );
            $this->loadClass ( 'href', 'htmlelements' );
            $this->loadClass ( 'radio', 'htmlelements' );
            $this->objJqTabs = $this->getObject('jqtabs', 'htmlelements');
            $objCaptcha = $this->getObject ( 'captcha', 'utilities' );
            $this->required = '<span class="warning"> * ' . $this->objLanguage->languageText ( 'word_required', 'system', 'Required' ) . '</span>';
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }

    }

    /**
     * Method to edit a profile.
     *
     * This function simply builds a form for the edit and then submits the form back to
     * the controller
     *
     * @todo put in some validation!
     * @access public
     * @param recid
     * @param optional featurebox
     */
    public function profileForm($username, $featurebox = FALSE) {
        // get the record id of the person editing the form
        $details = $this->objFMPro->getDetailsById($this->objFMPro->getUsersIdByUsername($username));
        //var_dump($details);
        $prform = new form ( 'updateprofile', $this->uri ( array ('module' => 'sis', 'action' => 'updateprofile' ) ) );
        $prfieldset = $this->getObject ( 'fieldset', 'htmlelements' );
        $prfieldset->setLegend ( $this->objLanguage->languageText ( 'mod_sis_updateprofile', 'sis' ) );

        $prtbl = $this->newObject ( 'htmltable', 'htmlelements' );
        $prtbl->cellpadding = 3;

        //start the inputs
        $personalfieldset = $this->newObject ( 'fieldset', 'htmlelements' );
        $personalfieldset->setLegend ( $this->objLanguage->languageText ( 'mod_sis_personaldetails', 'sis' ) );

        // textinput for last name (required)
        $ln = new textinput ( 'lastname' );
        if(isset($details['surname'])) {
            $ln->setValue($details['surname']);
        }
        $lnlabel = new label ( $this->objLanguage->languageText ( "mod_sis_lastname", "sis" ) . ':', 'comm_input_ln' );

        // First name
        $fn = new textinput ( 'firstname' );
        if(isset($details['firstname'])) {
            $fn->setValue($details['firstname']);
        }
        $fnlabel = new label ( $this->objLanguage->languageText ( "mod_sis_firstname", "sis" ) . ':', 'comm_input_fn' );

        // Middle name
        $mn = new textinput ( 'midname' );
        if(isset($details['middlename'])) {
            $mn->setValue($details['middlename']);
        }
        $mnlabel = new label ( $this->objLanguage->languageText ( "mod_sis_middlename", "sis" ) . ':', 'comm_input_mn' );

        // Occupation
        $oc = new textinput ( 'occupation' );
        if(isset($details['occupation'])) {
            $oc->setValue($details['occupation']);
        }
        $oclabel = new label ( $this->objLanguage->languageText ( "mod_sis_occupation", "sis" ) . ':', 'comm_input_oc' );

        // Employer
        $em = new textinput ( 'employer' );
        if(isset($details['employer'])) {
            $em->setValue($details['employer']);
        }
        $emlabel = new label ( $this->objLanguage->languageText ( "mod_sis_employer", "sis" ) . ':', 'comm_input_em' );

        // Username (required)
        $un = new textinput ( 'username' );
        $un->extra = 'READONLY';
        if(isset($details['username'])) {
            $un->setValue($details['username']);
        }
        $unlabel = new label ( $this->objLanguage->languageText ( "mod_sis_username", "sis" ) . ':', 'comm_input_un' );

        // Nested table for the personal details fieldset
        $perstbl = $this->newObject ( 'htmltable', 'htmlelements' );
        $perstbl->cellpadding = 3;
        $perstbl->startRow ();
        $perstbl->addCell ( $lnlabel->show () );
        $perstbl->addCell ( $mnlabel->show () );
        $perstbl->addCell ( $fnlabel->show () );
        $perstbl->endRow ();
        $perstbl->startRow ();
        $perstbl->addCell ( $ln->show () . $this->required );
        $perstbl->addCell ( $mn->show () ); // not required
        $perstbl->addCell ( $fn->show () . $this->required );
        $perstbl->endRow ();
        $perstbl->startRow ();
        $perstbl->addCell ( $oclabel->show () );
        $perstbl->addCell ( $emlabel->show () );
        $perstbl->addCell ( $unlabel->show () );
        $perstbl->endRow ();
        $perstbl->startRow ();
        $perstbl->addCell ( $oc->show () );
        $perstbl->addCell ( $em->show () );
        $perstbl->addCell ( $un->show () . $this->required );
        $perstbl->endRow ();

        // add the table to the fieldset
        $personalfieldset->addContent ( $perstbl->show () );

        $prtbl->startRow ();
        $prtbl->addCell ( $personalfieldset->show () );
        $prtbl->endRow ();

        // Address fields (all required!)
        $addfieldset = $this->newObject ( 'fieldset', 'htmlelements' );
        $addfieldset->setLegend ( $this->objLanguage->languageText ( 'mod_sis_address', 'sis' ) );

        $str = new textinput ( 'street' );
        if(isset($details['street'])) {
            $str->setValue($details['street']);
        }
        $strlabel = new label ( $this->objLanguage->languageText ( "mod_sis_street", "sis" ) . ':', 'comm_input_str' );

        // City
        $city = new textinput ( 'city' );
        if(isset($details['city'])) {
            $city->setValue($details['city']);
        }
        $citylabel = new label ( $this->objLanguage->languageText ( "mod_sis_city", "sis" ) . ':', 'comm_input_city' );

        // State
        $state = new textinput ( 'state' );
        if(isset($details['state'])) {
            $state->setValue($details['state']);
        }
        $statelabel = new label ( $this->objLanguage->languageText ( "mod_sis_state", "sis" ) . ':', 'comm_input_state' );
        $state->maxlength = 2;
        $state->size = 2;

        // Zip
        $zip = new textinput ( 'zip' );
        if(isset($details['zip'])) {
            $zip->setValue($details['zip']);
        }
        $ziplabel = new label ( $this->objLanguage->languageText ( "mod_sis_zip", "sis" ) . ':', 'comm_input_zip' );
        $state->maxlength = 5;

        $prtbl->startRow ();
        // we need to make a nested table here
        $adtbl = $this->newObject ( 'htmltable', 'htmlelements' );
        $adtbl->cellpadding = 3;

        $adtbl->startRow ();
        $adtbl->addCell ( $strlabel->show () );
        $adtbl->addCell ( $str->show () . $this->required );
        $adtbl->endRow ();

        $adtbl->startRow ();
        $adtbl->addCell ( $citylabel->show () );
        $adtbl->addCell ( $city->show () . $this->required );
        $adtbl->endRow ();

        $adtbl->startRow ();
        $adtbl->addCell ( $statelabel->show () );
        $adtbl->addCell ( $state->show () . $this->required );
        $adtbl->endRow ();

        $adtbl->startRow ();
        $adtbl->addCell ( $ziplabel->show () );
        $adtbl->addCell ( $zip->show () . $this->required );
        $adtbl->endRow ();

        // stick the address content into the address fieldset
        $addfieldset->addContent ( $adtbl->show () );
        //$prform->addToForm($addfieldset->show());
        // add the address table info
        $prtbl->addCell ( $addfieldset->show () );
        $prtbl->endRow ();

        // Now we need the email address info also (required)
        $email = new textinput ( 'email' );
        if(isset($details['emailaddress'])) {
            $email->setValue($details['emailaddress']);
        }
        $emaillabel = new label ( $this->objLanguage->languageText ( "mod_sis_emailaddress", "sis" ) . ':', 'comm_input_email' );
        $primail = new checkbox ( 'emailpriv' );
        if(isset($details['emailpriv']) && $details['emailpriv'] == '1') {
            $primail->ischecked = 'checked';
        }
        $primaillabel = new label ( $this->objLanguage->languageText ( "mod_sis_private", "sis" ) . ':', 'comm_check_email' );

        // add the email stuff to the next cell
        $mailfieldset = $this->newObject ( 'fieldset', 'htmlelements' );
        $mailfieldset->setLegend ( $this->objLanguage->languageText ( 'mod_sis_email', 'sis' ) );
        // stick the email address content into the mail fieldset
        // nested table for the email info
        $mtbl = $this->newObject ( 'htmltable', 'htmlelements' );
        $mtbl->cellpadding = 3;
        $mtbl->startRow ();
        $mtbl->addCell ( $emaillabel->show () );
        $mtbl->addCell ( $email->show () . $this->required );
        $mtbl->endRow ();
        $mtbl->startRow ();
        $mtbl->addCell ( $primaillabel->show () );
        $mtbl->addCell ( $primail->show () );
        $mtbl->endRow ();

        $mailfieldset->addContent ( $mtbl->show () );

        // add to the parent table
        $prtbl->startRow ();
        $prtbl->addCell ( $mailfieldset->show () );
        $prtbl->endRow ();

        // Now we need the phone info (required)
        // home phone (required)
        $hphone = new textinput ( 'hphone' );
        if(isset($details['homephone'])) {
            $hphone->setValue($details['homephone']);
        }
        $hphonelabel = new label ( $this->objLanguage->languageText ( "mod_sis_homephone", "sis" ) . ':', 'comm_input_hphone' );

        // cell phone
        $cellpriv = new checkbox ( 'cellpriv' );
        if(isset($details['cellpriv']) && $details['cellpriv'] == '1') {
            $cellpriv->ischecked = 'checked';
        }
        $cellprivlabel = new label ( $this->objLanguage->languageText ( "mod_sis_private", "sis" ) . ':', 'comm_check_cell' );
        $cphone = new textinput ( 'cphone' );
        if(isset($details['cellphone'])) {
            $cphone->setValue($details['cellphone']);
        }
        $cphonelabel = new label ( $this->objLanguage->languageText ( "mod_sis_cellphone", "sis" ) . ':', 'comm_input_cphone' );

        // work phone (required)
        $wphone = new textinput ( 'wphone' );
        if(isset($details['workphone'])) {
            $wphone->setValue($details['workphone']);
        }
        $wphonelabel = new label ( $this->objLanguage->languageText ( "mod_sis_workphone", "sis" ) . ':', 'comm_input_wphone' );

        // add the phone fieldset
        $phonefieldset = $this->newObject ( 'fieldset', 'htmlelements' );
        $phonefieldset->setLegend ( $this->objLanguage->languageText ( 'mod_sis_phonedetails', 'sis' ) );

        // nested table for the phone info
        $phtbl = $this->newObject ( 'htmltable', 'htmlelements' );
        $phtbl->cellpadding = 3;

        $phtbl->startRow ();
        $phtbl->addCell ( $hphonelabel->show () );
        $phtbl->addCell ( $hphone->show () . $this->required );
        $phtbl->endRow ();
        $phtbl->startRow ();
        $phtbl->addCell ( $wphonelabel->show () );
        $phtbl->addCell ( $wphone->show () . $this->required );
        $phtbl->endRow ();
        $phtbl->startRow ();
        $phtbl->addCell ( $cphonelabel->show () );
        $phtbl->addCell ( $cphone->show () . " " . $cellprivlabel->show () . " " . $cellpriv->show () );
        $phtbl->endRow ();

        // stick the phone content into the table
        $phonefieldset->addContent ( $phtbl->show () );

        $recid = new textinput ( 'recid' );
        $recid->extra = 'READONLY';
        $recid->fldType = 'hidden';
        if(isset($details['recid'])) {
            $recid->setValue($details['recid']);
        }

        $prtbl->startRow ();
        $prtbl->addCell ( $phonefieldset->show ().$recid->show() );
        $prtbl->endRow ();

        // Add the validation rules
        $prform->addRule('lastname', $this->objLanguage->languageText("mod_sis_lastnameempty", "sis"), 'required');
        $prform->addRule('firstname', $this->objLanguage->languageText("mod_sis_firstnameempty", "sis"), 'required');
        $prform->addRule('street', $this->objLanguage->languageText("mod_sis_streetempty", "sis"), 'required');
        $prform->addRule('city', $this->objLanguage->languageText("mod_sis_cityempty", "sis"), 'required');
        $prform->addRule('state', $this->objLanguage->languageText("mod_sis_stateempty", "sis"), 'required');
        $prform->addRule('zip', $this->objLanguage->languageText("mod_sis_zipempty", "sis"), 'required');
        $prform->addRule('email', $this->objLanguage->languageText("mod_sis_emailempty", "sis"), 'required');
        $prform->addRule('hphone', $this->objLanguage->languageText("mod_sis_hphoneempty", "sis"), 'required');
        $prform->addRule('wphone', $this->objLanguage->languageText("mod_sis_wphoneempty", "sis"), 'required');

        //end off the form and add the buttons
        $this->objPrButton = new button ( $this->objLanguage->languageText ( 'word_save', 'system' ) );
        $this->objPrButton->setValue ( $this->objLanguage->languageText ( 'word_save', 'system' ) );
        $this->objPrButton->setToSubmit ();

        $prfieldset->addContent ( $prtbl->show () );
        $prform->addToForm ( $prfieldset->show () );
        $prform->addToForm ( $this->objPrButton->show () );

        // return the form for display
        if ($featurebox == TRUE) {
            $objFeaturebox = $this->getObject ( 'featurebox', 'navigation' );
            return $objFeaturebox->showContent ( $this->objLanguage->languageText ( "mod_sis_prformheader", "sis" ), $prform->show () );
        } else {
            return $prform->show ();
        }
    }

    public function listKids($children) {
         $count = sizeof($children);
         $counter = 0;
         while ($counter < $count) {
             $firstname = $children[$counter]->getField("FirstName");
             $lastname = $children[$counter]->getField("LastName");
             $oid = $children[$counter]->getRecordId();
             $data = $this->objFMPro->getStudentRecord($children[$counter]);
             $id = $data->getRecordId();
             $name = $firstname." ".$lastname;
             $kids[$counter] = array($name, $id, $data);
             $counter++;
         }
         return $kids;
    }

    public function countKids($children) {
        $count = sizeof($children);
        $objheader = $this->getObject('htmlheading', 'htmlelements');
        $objheader->str = "There are $count students associated with this profile:";
        $objheader->type = 1;
        return $objheader->show();
    }

    /**
     * Method to display the parent menu
     *
     * @access public
     */
    public function parentMenu($featurebox = FALSE) {
        // Add some links to the other bits and doodads
        $home = new href ( $this->uri ( array ('action' => 'default' ) ), $this->objLanguage->languageText ( "word_home", "system" ) );
        $profile = new href ( $this->uri ( array ('action' => 'showprofile' ) ), $this->objLanguage->languageText ( "mod_sis_editprofile", "sis" ) );
        $student = new href ( $this->uri ( array ('action' => 'viewstudent' ) ), $this->objLanguage->languageText ( "mod_sis_viewstudent", "sis" ) );

        // Add the links to the linklist
        $linklist = $home->show() . "<br />";
        $linklist .= $profile->show() . "<br />";
        //$linklist .= $student->show() . "<br />";

        // return the link list for display
        if ($featurebox == TRUE) {
            $objFeaturebox = $this->getObject ( 'featurebox', 'navigation' );
            return $objFeaturebox->show ( $this->objLanguage->languageText ( "mod_sis_parentmenu", "sis" ), $linklist, 'pmenu' );
        } else {
            return $linklist;
        }
    }

    public function listFiles() {
        $folder = $this->sysConfig->getValue ( 'mod_sis_filefolder', 'sis' );
        $username = $this->sysConfig->getValue ( 'mod_sis_fileuser', 'sis' );

        $this->objWashout = $this->getObject('washout', 'utilities');
        $files = $this->objWashout->parseText("[FILES: username=$username, folder=$folder]");

        return $files;
    }

    public function studentForm($record) {
        // Student information and edit form
        $this->objJqTabs->addTab($this->objLanguage->languageText("mod_sis_student", "sis"), $this->studentBioDataForm($record));
        $this->objJqTabs->addTab($this->objLanguage->languageText("mod_sis_schedule", "sis"), $this->studentSchedule($record));
        // Tabs out of scope according to user story
        //$this->objJqTabs->addTab($this->objLanguage->languageText("mod_sis_medical", "sis"), 'Medical stuff');
        //$this->objJqTabs->addTab($this->objLanguage->languageText("mod_sis_contacts", "sis"), 'Contacts...');

        return $this->objJqTabs->show();
    }

    private function getStudentBioData($studentid) {
        $data = $this->objFMPro->getStudentById($studentid);
        return $data;
    }

    private function studentBioDataForm($details = NULL, $featurebox = FALSE) {
        $bioform = new form ( 'updatestudentbio', $this->uri ( array ('module' => 'sis', 'action' => 'updatestudentbio', 'recid' => $details['recid'] ) ) );
        $biofieldset = $this->getObject ( 'fieldset', 'htmlelements' );
        $biofieldset->setLegend ( $this->objLanguage->languageText ( 'mod_sis_updatestudentbio', 'sis' ) );

        $biotbl = $this->newObject ( 'htmltable', 'htmlelements' );
        $biotbl->cellpadding = 3;

        // OK lets set up all the form bits first
        // Last name
        $ln = new textinput ( 'lastname' );
        if(isset($details['lastname'])) {
            $ln->setValue($details['lastname']);
        }
        $lnlabel = new label ( $this->objLanguage->languageText ( "mod_sis_lastname", "sis" ) . ':', 'comm_input_ln' );

        // First name
        $fn = new textinput ( 'firstname' );
        if(isset($details['firstname'])) {
            $fn->setValue($details['firstname']);
        }
        $fnlabel = new label ( $this->objLanguage->languageText ( "mod_sis_firstname", "sis" ) . ':', 'comm_input_fn' );

        // Middle name
        $mn = new textinput ( 'midname' );
        if(isset($details['middlename'])) {
            $mn->setValue($details['middlename']);
        }
        $mnlabel = new label ( $this->objLanguage->languageText ( "mod_sis_middlename", "sis" ) . ':', 'comm_input_mn' );

        // Race/Ethnicity
        $ra = new textinput ( 'race' );
        if(isset($details['race'])) {
            $ra->setValue($details['race']);
        }
        $ralabel = new label ( $this->objLanguage->languageText ( "mod_sis_race", "sis" ) . ':', 'comm_input_ra' );

        // Gender radio set
        $gender = new radio('gender');
        $gender->addOption('Male','Male');
        $gender->addOption('Female','Female');
        if(isset($details['gender'])) {
             if($details['gender'] == 'Female') {
                $gender->setSelected('Female');
             }
             else {
                 $gender->setSelected('Male');
             }
        }


        $genderlabel = new label ( $this->objLanguage->languageText ( "mod_sis_gender", "sis" ) . ':', 'comm_input_gender' );
        // Race/Ethnicity
        $dob = new textinput ( 'dob' );
        if(isset($details['dob'])) {
            $dob->setValue($details['dob']);
        }
        else {
            $dob->setValue('mm/dd/YYYY');
        }
        $doblabel = new label ( $this->objLanguage->languageText ( "mod_sis_dob", "sis" ) . ':', 'comm_input_dob' );

        // Okidokes, lets lay this thing out now...
        $biotbl->startRow ();
        $biotbl->addCell($lnlabel->show());
        $biotbl->addCell($mnlabel->show());
        $biotbl->addCell($fnlabel->show());
        $biotbl->endRow();

        $biotbl->startRow();
        $biotbl->addCell($ln->show().$this->required);
        $biotbl->addCell($mn->show());
        $biotbl->addCell($fn->show().$this->required);
        $biotbl->endRow();

        $biotbl->startRow();
        $biotbl->addCell($doblabel->show());
        $biotbl->addCell($genderlabel->show());
        $biotbl->addCell($ralabel->show());
        $biotbl->endRow();

        $biotbl->startRow();
        $biotbl->addCell($dob->show().$this->required);
        $biotbl->addCell($gender->show());
        $biotbl->addCell($ra->show());
        $biotbl->endRow();

        // Some validation (client side)
        $bioform->addRule('lastname', $this->objLanguage->languageText("mod_sis_lastnameempty", "sis"), 'required');
        $bioform->addRule('firstname', $this->objLanguage->languageText("mod_sis_firstnameempty", "sis"), 'required');
        $bioform->addRule('dob', $this->objLanguage->languageText("mod_sis_dobnonvalid", "sis"), 'required');

        // Now the regular formy bits like buttons...
        $this->objBioButton = new button ( $this->objLanguage->languageText ( 'word_save', 'system' ) );
        $this->objBioButton->setValue ( $this->objLanguage->languageText ( 'word_save', 'system' ) );
        $this->objBioButton->setToSubmit ();

        $biofieldset->addContent ( $biotbl->show () );
        $bioform->addToForm ( $biofieldset->show () );
        $bioform->addToForm ( $this->objBioButton->show () );

        // return the form for display
        if ($featurebox == TRUE) {
            $objFeaturebox = $this->getObject ( 'featurebox', 'navigation' );
            return $objFeaturebox->showContent ( $this->objLanguage->languageText ( "mod_sis_bioformheader", "sis" ), $bioform->show () );
        } else {
            return $bioform->show ();
        }
    }

    public function studentSchedule($details = NULL, $featurebox = FALSE) {
        // var_dump($details);
        $schedform = new form ( 'updatestudentsched', $this->uri ( array ('module' => 'sis', 'action' => 'updatestudentsched', 'recid' => $details['recid'] ) ) );
        $schedfieldset = $this->newObject ( 'fieldset', 'htmlelements' );
        $schedfieldset->setLegend ( $this->objLanguage->languageText ( 'mod_sis_updatestudentsched', 'sis' ) );

        $schedtbl = $this->newObject ( 'htmltable', 'htmlelements' );
        $schedtbl->cellpadding = 3;

        $statuslabel = new label ( $this->objLanguage->languageText ( "mod_sis_sstatus", "sis" ), 'status');
        $classlabel = new label ( $this->objLanguage->languageText ( "mod_sis_sclass", "sis" ), 'class');
        $fylabel = new label ( $this->objLanguage->languageText ( "mod_sis_sfirstyear", "sis" ). ':', 'comm_input_fy' );
        $lylabel = new label ( $this->objLanguage->languageText ( "mod_sis_sly", "sis" ), 'lastyear');
        $schedtypelabel = new label ( $this->objLanguage->languageText ( "mod_sis_sschedtype", "sis" ), 'schedtype');
        $schedlabel = new label ( $this->objLanguage->languageText ( "mod_sis_ssched", "sis" ), 'sched');

        // Text input for fisrt year (not required)
        $fy = new textinput ( 'firstyear' );
        if(isset($details['firstyear'])) {
            $fy->setValue($details['firstyear']);
        }

        // Okidokes, lets lay this thing out now...
        $schedtbl->startRow ();
        $schedtbl->addCell($statuslabel->show());
        $schedtbl->addCell($classlabel->show());
        $schedtbl->addCell($fylabel->show());
        $schedtbl->addCell($lylabel->show());
        $schedtbl->endRow();

        $schedtbl->startRow ();
        $schedtbl->addCell($details['status']);
        $schedtbl->addCell($details['class']);
        $schedtbl->addCell($fy->show());
        $schedtbl->addCell($details['lastyear']);
        $schedtbl->endRow();

        $schedtbl->startRow ();
        $schedtbl->addCell($schedtypelabel->show());
        $schedtbl->addCell($schedlabel->show());
        $schedtbl->endRow();

        $schedtbl->startRow ();
        $schedtbl->addCell($details['schedtype']);
        $schedtbl->addCell($details['sched']);
        $schedtbl->endRow();

        // Now the regular formy bits like buttons...
        $this->objSchedButton = new button ( $this->objLanguage->languageText ( 'word_save', 'system' ) );
        $this->objSchedButton->setValue ( $this->objLanguage->languageText ( 'word_save', 'system' ) );
        $this->objSchedButton->setToSubmit ();

        $schedfieldset->addContent ( $schedtbl->show () );
        $schedform->addToForm ( $schedfieldset->show () );
        $schedform->addToForm ( $this->objSchedButton->show () );

        // return the form for display
        if ($featurebox == TRUE) {
            $objFeaturebox = $this->getObject ( 'featurebox', 'navigation' );
            return $objFeaturebox->showContent ( $this->objLanguage->languageText ( "mod_sis_schedformheader", "sis" ), $schedform->show () );
        } else {
            return $schedform->show ();
        }
    }

}
?>