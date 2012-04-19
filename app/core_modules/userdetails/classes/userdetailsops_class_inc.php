<?php
/**
 * Class to handle userdetails.
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
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
 * @version    0.001
 * @package    schools
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
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
 * Class to handle user profile elements
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @version    0.001
 * @package    schools
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 */
class userdetailsops extends object
{
    /**
     * 
     * Variable to hold then check for schoolusers
     * 
     * @access proteced
     * @var boolean
     */
    protected $check = FALSE;
    
    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init()
    {
        try {
            // Load core system objects.
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->PKId();
            $this->objUserAdmin = $this->getObject('useradmin_model2', 'security');
            $this->objSvars = $this->getObject('serializevars', 'utilities');
            $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            $this->objCountries = $this->getObject('languagecode' , 'language');            
            $this->objBizCard = $this->getObject('userbizcard', 'useradmin');
            $this->objFile = $this->getObject('dbfile', 'filemanager');
            $this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
            
            $objModules = $this->getObject('modules', 'modulecatalogue');
            $this->check = $objModules->checkIfRegistered('schoolusers');

            // Load html elements.
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->objTable = $this->loadClass('htmltable', 'htmlelements');
            $this->objInput = $this->loadClass('textinput', 'htmlelements');
            $this->objDropdown = $this->loadClass('dropdown', 'htmlelements');
            $this->objForm = $this->loadClass('form', 'htmlelements');
            $this->objLayer = $this->loadClass('layer', 'htmlelements');
            $this->objRadio = $this->loadClass('radio', 'htmlelements');
            $this->objText = $this->loadClass('textarea', 'htmlelements');
            //$this->objButton = $this->loadClass('button', 'htmlelements');

            // Load db classes,
            if ($this->check)
            {
                $this->objDBgrades = $this->getObject('dbgrades', 'grades');
                $this->objDBdata = $this->getObject('dbdata', 'schoolusers');
                $this->objDBusers = $this->getObject('dbusers', 'schoolusers');
                $this->objDBschools = $this->getObject('dbschools_schools' , 'schools');
            }
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
     *
     * Method to generate an error string for display
     * 
     * @access private
     * @param string $errorText The error string
     * @return string $string The formated error string
     */
    private function error($errorText)
    {
        $error = $this->objLanguage->languageText('word_error', 'system', 'WORD: word_error, not found');
        
        $this->objIcon->title = $error;
        $this->objIcon->alt = $error;
        $this->objIcon->setIcon('exclamation', 'png');
        $errorIcon = $this->objIcon->show();
        
        $string = '<span style="color: red">' . $errorIcon . '&nbsp;<b>' . $errorText . '</b></span>';
        return $string;
    }
    
    /**
     *
     * Method to generate the html for the form template
     * 
     * @access public
     * @return string $string The html string to be sent to the template 
     */
    public function showMain()
    {
        if ($this->check)
        {
            $userArray = $this->objDBusers->getUser($this->userId);
        }
        else
        {
            $userArray = $this->objUserAdmin->getUserDetails($this->userId);
        }

        $titleValue = $userArray['title'];
        $firstNameValue = $userArray['firstname'];
        $lastNameValue = $userArray['surname'];
        $genderValue = $userArray['sex'];            
        $countryValue = $userArray['country'];
        $emailAddressValue = $userArray['emailaddress'];
        $contactNumberValue = $userArray['cellnumber'];
        $passwordValue = NULL;
        $confirmPasswordValue = NULL;
        
        if ($this->check)
        {
            $middleNameValue = $userArray['middle_name'];
            $dayValue = !empty($userArray['date_of_birth']) ? date('j', strtotime($userArray['date_of_birth'])) : NULL;
            $monthValue = !empty($userArray['date_of_birth']) ? date('n', strtotime($userArray['date_of_birth'])) : NULL;
            $yearValue = !empty($userArray['date_of_birth']) ? date('Y', strtotime($userArray['date_of_birth'])) : NULL;
            if (!empty($userArray['address']))
            {
                $address = explode('|', $userArray['address']);
                $addressOneValue = $address[0];
                $addressTwoValue = $address[1];
            }
            else
            {
                $addressOneValue = NULL;
                $addressTwoValue = NULL;
            }
            $cityValue = $userArray['city'];
            $stateValue = $userArray['state'];
            $postalCodeValue = $userArray['postal_code'];
            $schoolIdValue = $userArray['school_id'];
            $schoolArray = $this->objDBschools->getSchool($schoolIdValue);
            $schoolValue = $schoolArray['name'];
            $descriptionValue = $userArray['description'];
        }
        else
        {
            $staffNumberValue = $userArray['staffnumber'];
        }

        $errors = $this->getSession('errors');

        $titleValue = !empty($errors) ? $errors['data']['title'] : $titleValue;
        $firstNameValue = !empty($errors) ? $errors['data']['first_name'] : $firstNameValue;
        $lastNameValue = !empty($errors) ? $errors['data']['last_name'] : $lastNameValue;
        $genderValue = !empty($errors) ? $errors['data']['gender'] : $genderValue;
        $countryValue = !empty($errors) ? $errors['data']['country'] : $countryValue;
        $emailAddressValue = !empty($errors) ? $errors['data']['email_address'] : $emailAddressValue;
        $contactNumberValue = !empty($errors) ? $errors['data']['contact_number'] : $contactNumberValue;
        $passwordValue = !empty($errors) ? $errors['data']['password'] : $passwordValue;
        $confirmPasswordValue = !empty($errors) ? $errors['data']['confirm_password'] : $confirmPasswordValue;

        $titleError = (!empty($errors) && array_key_exists('title', $errors['errors'])) ? $errors['errors']['title'] : NULL;
        $firstNameError = (!empty($errors) && array_key_exists('first_name', $errors['errors'])) ? $errors['errors']['first_name'] : NULL;
        $lastNameError = (!empty($errors) && array_key_exists('last_name', $errors['errors'])) ? $errors['errors']['last_name'] : NULL;
        $genderError = (!empty($errors) && array_key_exists('gender', $errors['errors'])) ? $errors['errors']['gender'] : NULL;
        $emailAddressError = (!empty($errors) && array_key_exists('email_address', $errors['errors'])) ? $errors['errors']['email_address'] : NULL;
        $passwordError = (!empty($errors) && array_key_exists('password', $errors['errors'])) ? $errors['errors']['password'] : NULL;

        if ($this->check)
        {
            $middleNameValue = !empty($errors) ? $errors['data']['middle_name'] : $middleNameValue;
            $dayValue = !empty($errors) ? $errors['data']['date_of_birth'][0] : $dayValue;
            $monthValue = !empty($errors) ? $errors['data']['date_of_birth'][1] : $monthValue;
            $yearValue = !empty($errors) ? $errors['data']['date_of_birth'][2] : $yearValue;
            $addressOneValue = !empty($errors) ? $errors['data']['address'][0] : $addressOneValue;
            $addressTwoValue = !empty($errors) ? $errors['data']['address'][1] : $addressTwoValue;
            $cityValue = !empty($errors) ? $errors['data']['city'] : $cityValue;
            $stateValue = !empty($errors) ? $errors['data']['state'] : $stateValue;
            $postalCodeValue = !empty($errors) ? $errors['data']['postal_code'] : $postalCodeValue;
            $schoolIdValue = !empty($errors) ? $errors['data']['school_id'] : $schoolIdValue;
            if (!is_null($schoolIdValue))
            {
                $schoolArray = $this->objDBschools->getSchool($schoolIdValue);
                $schoolValue = $schoolArray['name'];
            }
            $descriptionValue = !empty($errors) ? $errors['data']['description'] : $descriptionValue;
    
            $schoolError = (!empty($errors) && array_key_exists('school_id', $errors['errors'])) ? $errors['errors']['school_id'] : NULL;
            $dateOfBirthError = (!empty($errors) && array_key_exists('date_of_birth', $errors['errors'])) ? $errors['errors']['date_of_birth'] : NULL;
            $addressError = (!empty($errors) && array_key_exists('address', $errors['errors'])) ? $errors['errors']['address'] : NULL;
            $cityError = (!empty($errors) && array_key_exists('city', $errors['errors'])) ? $errors['errors']['city'] : NULL;
            $stateError = (!empty($errors) && array_key_exists('state', $errors['errors'])) ? $errors['errors']['state'] : NULL;
            $postalCodeError = (!empty($errors) && array_key_exists('postal_code', $errors['errors'])) ? $errors['errors']['postal_code'] : NULL;
            $descriptionError = (!empty($errors) && array_key_exists('description', $errors['errors'])) ? $errors['errors']['description'] : NULL;
        }
        
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $mr = $this->objLanguage->languageText('title_mr', 'system', 'TITLE: title_mr');
        $miss = $this->objLanguage->languageText('title_miss', 'system', 'TITLE: title_miss');
        $mrs = $this->objLanguage->languageText('title_mrs', 'system', 'TITLE: title_mrs');
        $ms = $this->objLanguage->languageText('title_ms', 'system', 'TITLE: title_ms');
        $dr = $this->objLanguage->languageText('title_dr', 'system', 'TITLE: title_dr');
        $rev = $this->objLanguage->languageText('title_rev', 'system', 'TITLE: title_rev');
        $prof = $this->objLanguage->languageText('title_prof', 'system', 'TITLE: title_prof');
        $assocprof = $this->objLanguage->languageText('title_assocprof', 'system', 'TITLE: title_assocprof');
        $sir = $this->objLanguage->languageText('title_sir', 'system', 'TITLE: title_sir');
        $dame = $this->objLanguage->languageText('title_dame', 'system', 'TITLE: title_dame');
        $firstNameLabel = $this->objLanguage->languageText('phrase_firstname', 'system', 'ERROR: phrase_firstname');
        $lastNameLabel = $this->objLanguage->languageText('phrase_lastname', 'system', 'ERROR: phrase_lastname');
        $genderLabel = $this->objLanguage->languageText('word_gender', 'system', 'ERROR: word_gender');
        $maleLabel = $this->objLanguage->languageText('word_male', 'system', 'ERROR: word_male');
        $femaleLabel = $this->objLanguage->languageText('word_female', 'system', 'ERROR: word_female');
        $dateOfBirthLabel = $this->objLanguage->languageText('phrase_dateofbirth', 'system', 'ERROR: phrase_dateofbirth');
        $addressLabel = $this->objLanguage->languageText('word_address', 'system', 'ERROR: word_address');
        $cityLabel = $this->objLanguage->languageText('word_city', 'system', 'ERROR: word_city');
        $stateLabel = $this->objLanguage->languageText('word_state', 'system', 'ERROR: word_state');
        $countryLabel = $this->objLanguage->languageText('word_country', 'system', 'ERROR: word_country');
        $postalCodeLabel = $this->objLanguage->languageText('phrase_postalcode', 'system', 'ERROR: phrase_postalcode');
        $emailAddressLabel = $this->objLanguage->languageText('phrase_emailaddress', 'system', 'ERROR: phrase_emailaddress');
        $contactNumberLabel = $this->objLanguage->languageText('phrase_contactnumber', 'system', 'ERROR: phrase_contactnumber');
        $usernameLabel = $this->objLanguage->languageText('word_username', 'system', 'ERROR: word_username');
        $passwordLabel = $this->objLanguage->languageText('word_password', 'system', 'ERROR: word_password');
        $confirmPasswordLabel = $this->objLanguage->languageText('phrase_confirmpassword', 'system', 'ERROR: phrase_confirmpassword');
        $passwordNotAlike = $this->objLanguage->languageText('mod_userdetails_passwordsnotalike', 'userdetails', 'ERROR: mod_userdetails_passwordsnotalike');
        $updateLabel = $this->objLanguage->languageText('mod_userdetails_updatedetails', 'userdetails', 'ERROR: mod_userdetails_updatedetails');
        $successTitleLabel = $this->objLanguage->languageText('word_success', 'system', 'ERROR: word_success');
        $successImageLabel = $this->objLanguage->languageText('mod_userdetails_userimagereset', 'userdetails', 'ERROR: mod_userdetails_userimagereset');
        $resetLabel = $this->objLanguage->languageText('phrase_resetform', 'system', 'ERROR: phrase_resetform');
        $successDetailsLabel = $this->objLanguage->languageText('mod_userdetails_detailssuccessfullyupdate', 'userdetails', 'ERROR: mod_userdetails_detailssuccessfullyupdate');
        $successPasswordLabel = $this->objLanguage->languageText('mod_userdetails_passwordupdated', 'userdetails', 'ERROR: mod_userdetails_passwordupdated');
        $errorLabel = $this->objLanguage->languageText('word_error', 'system', 'ERROR: word_error');
        $noChangeLabel = $this->objLanguage->languageText('mod_userdetails_nochange', 'userdetails', 'ERROR: mod_userdetails_nochange');

        if ($this->check)
        {
            $middleNameLabel = $this->objLanguage->languageText('mod_schoolusers_middlename', 'schoolusers', 'ERROR: mod_schoolusers_middlename');
            $schoolLabel = $this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'ERROR: mod_schools_school');
            $descriptionLabel = $this->objLanguage->languageText('mod_schoolusers_aboutyourself', 'schoolusers', 'ERROR: mod_schoolusers_aboutyourself');
        }
        else
        {
            $staffNumberLabel = $this->objLanguage->languageText('phrase_staffstudnumber', 'system', 'ERROR: phrase_staffstudnumber');
        }
        
        $arrayVars = array();
        $arrayVars['password_not_alike'] = $passwordNotAlike;
       
        // pass password error to javascript.
        $this->objSvars->varsToJs($arrayVars);
        
        $bizcard = $this->showBizCard();
        
        $objLayer = new layer();
        $objLayer->id = 'bizcard';
        $objLayer->str = $bizcard;
        $bizcardLayer = $objLayer->show();

        $string = $bizcardLayer;
        
        // set up html elements
        $objDrop = new dropdown('title');
        $objDrop->addOption($mr, $mr);
        $objDrop->addOption($miss, $miss);
        $objDrop->addOption($mrs, $mrs);
        $objDrop->addOption($ms, $ms);
        $objDrop->addOption($dr, $dr);
        $objDrop->addOption($rev, $rev);
        $objDrop->addOption($prof, $prof);
        $objDrop->addOption($assocprof, $assocprof);
        $objDrop->addOption($sir, $sir);
        $objDrop->addOption($dame, $dame);
        $objDrop->setSelected($titleValue);
        $titleDrop = $objDrop->show();

        $objInput = new textinput('first_name', $firstNameValue, '', '50');
        $firstNameInput = $objInput->show();
        
        $objInput = new textinput('last_name', $lastNameValue, '', '50');
        $lastNameInput = $objInput->show();
        
        $objRadio = new radio('gender');
        $objRadio->addOption('M', $maleLabel);
        $objRadio->addOption('F', $femaleLabel);
        $objRadio->setSelected($genderValue);
        $genderRadio = $objRadio->show();
        
        $countryDrop = $this->objCountries->countryAlpha($countryValue);

        $objInput = new textinput('email_address', $emailAddressValue, '', '50');
        $emailAddressInput = $objInput->show();

        $objInput = new textinput('contact_number', $contactNumberValue, '', '50');
        $contactNumberInput = $objInput->show();

        $objInput = new textinput('password', $passwordValue, 'password', '50');
        $passwordInput = $objInput->show();
        
        $objInput = new textinput('confirm_password', $confirmPasswordValue, 'password', '50');
        $confirmPasswordInput = $objInput->show();
        
        $objButton = new button('update', $updateLabel);
        $objButton->setToSubmit();
        $updateButton = $objButton->show();
        
        $objButton = new button('reset', $resetLabel);
        $objButton->setToSubmit();
        $resetButton = $objButton->show();
        
        if ($this->check)
        {
            $objInput = new textinput('school', $schoolValue, '', '50');
            $schoolInput = $objInput->show();

            $objInput = new textinput('school_id', $schoolIdValue, 'hidden', '');
            $schoolIdInput = $objInput->show();

            $objInput = new textinput('middle_name', $middleNameValue, '', '50');
            $middleNameInput = $objInput->show();

            $day = range(1, 31);
            array_unshift($day, '-');
            $days = array_combine($day, $day);
            $months = array(
                '-' => '-',
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'May',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Aug',
                9 => 'Sep',
                10 => 'Oct',
                11 => 'Nov',
                12 => 'Dec',
            );
            $year = range(date('Y'), date('Y')-85);
            array_unshift($year, '-');
            $years = array_combine($year, $year);

            $objDrop = new dropdown('date_of_birth[]');
            $objDrop->extra = 'style="width: auto;"';
            $objDrop->addFromArray($days);
            $objDrop->setSelected($dayValue);
            $dayDrop = $objDrop->show();

            $objDrop = new dropdown('date_of_birth[]');
            $objDrop->extra = 'style="width: auto;"';
            $objDrop->addFromArray($months);
            $objDrop->setSelected($monthValue);
            $monthDrop = $objDrop->show();

            $objDrop = new dropdown('date_of_birth[]');
            $objDrop->extra = 'style="width: auto;"';
            $objDrop->addFromArray($years);
            $objDrop->setSelected($yearValue);
            $yearDrop = $objDrop->show();

            $objInput = new textinput('address[]', $addressOneValue, '', '50');
            $addressOneInput = $objInput->show();

            $objInput = new textinput('address[]', $addressTwoValue, '', '50');
            $addressTwoInput = $objInput->show();

            $objInput = new textinput('city', $cityValue, '', '50');
            $cityInput = $objInput->show();

            $objInput = new textinput('state', $stateValue, '', '50');
            $stateInput = $objInput->show();

            $objInput = new textinput('postal_code', $postalCodeValue, '', '50');
            $postalCodeInput = $objInput->show();

            $objText = new textarea('description', $descriptionValue);
            $descriptionText = $objText->show();
        }
        else
        {
            $objInput = new textinput('staffnumber', $staffNumberValue, '', '50');
            $staffNumberInput = $objInput->show();
        }
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        if ($this->check)
        {
            $objTable->startRow();
            $objTable->addCell('<b>' . ucfirst(strtolower($schoolLabel)) . ': </b>', '', '', '', '', '', '');
            $objTable->addCell($schoolError . $schoolIdInput . $schoolInput, '', '', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($titleError . $titleDrop, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $firstNameLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell($firstNameError . $firstNameInput, '', '', '', '', '', '');
        $objTable->endRow();
        if ($this->check)
        {
            $objTable->startRow();
            $objTable->addCell('<b>' . $middleNameLabel . ': </b>', '', '', '', '', '', '');
            $objTable->addCell($middleNameInput, '', '', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell('<b>' . $lastNameLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell($lastNameError . $lastNameInput, '', '', '', '', '', '');
        $objTable->endRow();
        if (!$this->check)
        {
            $objTable->startRow();
            $objTable->addCell('<b>' . $staffNumberLabel . ': </b>', '', '', '', '', '', '');
            $objTable->addCell($staffNumberInput, '', '', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell('<b>' . $genderLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell($genderError . $genderRadio, '', '', '', '', '', '');
        $objTable->endRow();
        if ($this->check)
        {
            $objTable->startRow();
            $objTable->addCell('<b>' . $dateOfBirthLabel. ': </b>', '', '', '', '', '', '');
            $objTable->addCell($dateOfBirthError . $dayDrop . ' / ' . $monthDrop . ' / ' . $yearDrop, '', '', '', '', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell('<b>' . $addressLabel . ': </b>', '', '', '', '', '', '');
            $objTable->addCell($addressError . $addressOneInput, '', '', '', '', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell('' , '', '', '', '', '', '');
            $objTable->addCell($addressTwoInput, '', '', '', '', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell('<b>' . $cityLabel . ': </b>', '', '', '', '', '', '');
            $objTable->addCell($cityError . $cityInput, '', '', '', '', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell('<b>' . $stateLabel . ': </b>', '', '', '', '', '', '');
            $objTable->addCell($stateError . $stateInput, '', '', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell('<b>' . $countryLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell($countryDrop, '', '', '', '', '', '');
        $objTable->endRow();
        if ($this->check)
        {
            $objTable->startRow();
            $objTable->addCell('<b>' . $postalCodeLabel . ': </b>', '', '', '', '', '', '');
            $objTable->addCell($postalCodeError . $postalCodeInput, '', '', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell('<b>' . $emailAddressLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell($emailAddressError . $emailAddressInput, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $contactNumberLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell($contactNumberInput, '', '', '', '', '', '');
        $objTable->endRow();
        if ($this->check)
        {
            $objTable->startRow();
            $objTable->addCell('<b>' . $descriptionLabel . ': </b>', '', '', '', '', '', '');
            $objTable->addCell($descriptionError . $descriptionText, '', '', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell('<b>' . $usernameLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell('<strong>' . $userArray['username'] . '</strong>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $passwordLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell($passwordError . $passwordInput, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $confirmPasswordLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell($confirmPasswordInput, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($updateButton . '&nbsp;' . $resetButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $userTable = $objTable->show();
        
        $objForm = new form('user', $this->uri(array(
            'action' => 'validate'
        ), 'userdetails'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($userTable);
        $addForm = $objForm->show();

        $string .= '<br />' . $addForm;
        
        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_imagereset');
        $this->objDialog->setTitle(ucwords($successTitleLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent('<span class="success">' . $successImageLabel . '</span>');
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $dialog = $this->objDialog->show();
        
        $success = $this->getSession('success', NULL);
        if (!empty($success))
        {
            if (in_array('no change', $success))
            {
                $this->objDialog = $this->newObject('dialog', 'jquerycore');
                $this->objDialog->setCssId('dialog_nochange');
                $this->objDialog->setTitle(ucwords($errorLabel));
                $this->objDialog->setCloseOnEscape(FALSE);
                $this->objDialog->setContent('<span class="error">' . $noChangeLabel . '</span>');
                $this->objDialog->setAutoOpen(TRUE);
                $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
                $this->objDialog->setBeforeClose('resetSession(this)');
                $dialog .= $this->objDialog->show();
            }
            else
            {
                $content = '';
                if (in_array('details', $success))
                {
                    $content .= '<span class="success">' . $successDetailsLabel . '</span><br />';

                }
                if (in_array('password', $success))
                {
                    $content .= '<span class="success">' . $successPasswordLabel . '</span>';
                }
                $this->objDialog = $this->newObject('dialog', 'jquerycore');
                $this->objDialog->setCssId('dialog_updatesuccess');
                $this->objDialog->setTitle(ucwords($successTitleLabel));
                $this->objDialog->setCloseOnEscape(FALSE);
                $this->objDialog->setContent($content);
                $this->objDialog->setAutoOpen(TRUE);
                $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
                $this->objDialog->setBeforeClose('resetSession(this)');
                $dialog .= $this->objDialog->show();
            }            
        }
        
        $string .= $dialog;

        return $string;        
    }

    /**
     *
     * Method to return the schools data for autocomplete
     * 
     * @access public
     * @return VOID
     */
    public function ajaxFindSchools()
    {
        $search = $this->getParam('term');
        $result = $this->objDBschools->autocomplete($search);
        
        echo json_encode($result);
        die();
    }
    
    /**
     *
     * Method to validate the user data
     * 
     * @access public
     * @param array $data The data to validate
     * @return  
     */
    public function validate($data)
    {
        $errors = array();
        foreach ($data as $fieldname => $value)
        {
            if ($fieldname != 'school_id' && $fieldname != 'title' && $fieldname != 'staffnumber'
                && $fieldname != 'middle_name' && $fieldname != 'gender' && $fieldname != 'date_of_birth'
                && $fieldname != 'address' && $fieldname != 'country' && $fieldname != 'contact_number'
                && $fieldname != 'password' && $fieldname != 'confirm_password')
            {
                if (empty($value))
                {
                    $name = explode('_', $fieldname);
                    $name = implode(' ', $name);
                    $array = array('fieldname' => $name);
                    $errorText = $this->objLanguage->code2Txt('mod_userdetails_error_1', 'userdetails', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';                                    
                }
                elseif ($fieldname == 'email_address')
                {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL) == FALSE)
                    {
                        $errorText = $this->objLanguage->languageText('mod_userdetails_invalidemail', 'userdetails', 'TEXT: mod_schoolusers_invalidemail, not found');
                        $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                    }
                }
            }
            elseif ($fieldname == 'school_id')
            {
                if (empty($value))
                {
                    $array = array('fieldname' => $this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'ERROR: mod_schools_school'));
                    $errorText = $this->objLanguage->code2Txt('mod_userdetails_error_1', 'userdetails', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'title' || $fieldname == 'gender')
            {
                if (empty($value))
                {
                    $array = array('fieldname' => $fieldname);
                    $errorText = $this->objLanguage->code2Txt('mod_userdetails_error_2', 'userdetails', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'date_of_birth')
            {
                if ($value[0] == '-' || $value[1] == '-' || $value[2] == '-')
                {
                    $name = explode('_', $fieldname);
                    $name = implode(' ', $name);
                    $array = array('fieldname' => $name);
                    $errorText = $this->objLanguage->code2Txt('mod_userdetails_error_1', 'userdetails', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'address')
            {
                if (empty($value[0]) && empty($value[1]))
                {
                    $array = array('fieldname' => $fieldname);
                    $errorText = $this->objLanguage->code2Txt('mod_userdetails_error_1', 'userdetails', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'password')
            {
                if ($value != $data['confirm_password'])
                {
                    $errorText = $this->objLanguage->languageText('mod_userdetails_passwordsnotalike','userdetails', 'ERROR: mod_schoolusers_passwordsnotalike');
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
        }
        $errorArray = array();
        $errorArray['data'] = $data;
        $errorArray['errors'] = $errors;
        $this->setSession('errors', $errorArray);

        if (empty($errors))
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }        
    }    

    /**
     *
     * Method to save the details on adding.
     * 
     * @access public
     * @param array $data The array of data to save
     * @return void 
     */
    public function save($data)
    {
        $userArray = $this->objUserAdmin->getUserDetails($this->userId);
        $success = array();
            
        $user = array();
        $user['firstname'] = $data['first_name'];
        $user['surname'] = $data['last_name'];
        $user['title'] = $data['title'];
        $user['emailaddress'] = $data['email_address'];
        $user['sex'] = $data['gender'];
        $user['country'] = $data['country'];
        $user['cellnumber'] = $data['contact_number'];
        $user['staffnumber'] = $data['staffnumber'];

        $changed = FALSE;
        foreach ($userArray as $field => $value)
        {
            if (array_key_exists($field, $user))
            {
                if ($user[$field] != $value )
                {
                    $success[] = 'details';
                    $changed = TRUE;
                    break;
                }
            }
        }
        if (!empty($data['password']))
        {
            $success[] = 'password';
            $changed = TRUE;
        }
        if ($changed)
        {
            $this->objUserAdmin->updateUserDetails($userArray['id'], $userArray['username'], $user['firstname'],
                $user['surname'], $user['title'], $user['emailaddress'], $user['sex'],
                $user['country'], $user['cellnumber'], $user['staffnumber'], $data['password']);
            $this->objUser->updateUserSession();
        }

        if ($this->check)
        {
            $userData = $this->objDBdata->getData($this->userId);

            $extra = array();
            $extra['middle_name'] = $data['middle_name'];
            $extra['date_of_birth'] = date('Y-m-d', strtotime($data['date_of_birth'][0] . '-' . $data['date_of_birth'][1] . '-' . $data['date_of_birth'][2]));
            $extra['address'] = implode('|', $data['address']);
            $extra['city'] = $data['city'];
            $extra['state'] = $data['state'];
            $extra['postal_code'] = $data['postal_code'];
            $extra['school_id'] = $data['school_id'];
            $extra['description'] = $data['description'];
            
            $changed = FALSE;
            if (!empty($userData))
            {
                foreach ($userData as $field => $value)
                {
                    if (array_key_exists($field, $extra))
                    {
                        if ($extra[$field] != $value)
                        {
                            $success[] = 'details';
                            $changed = TRUE;
                            break;
                        }
                    }
                }
            }
            else
            {
                $success[] = 'details';
                $changed = TRUE;
            }

            if ($changed)
            {
                if (!empty($userData))
                {
                    $extra['modified_by'] = $this->objUser->PKId();
                    $extra['date_modified'] = date('Y-m-d H:i:s');
                    $this->objDBdata->updateData($userArray['id'], $extra);
                }
                else
                {
                    $extra['user_id'] = $userArray['id'];
                    $extra['created_by'] = $this->objUser->PKId();
                    $extra['date_created'] = date('Y-m-d H:i:s');
                    $this->objDBdata->saveData($extra);
                }
            }
        }
        
        $success = array_unique($success);
        if (empty($success))
        {
            $success[] = 'no change';
        }
        $this->setSession('success', $success);
    }
    
    /**
     *
     * Method to show the user bizcard.
     * 
     * @access public
     * @return string $string The html string to be outputed 
     */
    public function showBizCard($reset = TRUE)
    {
        $user = $this->objUserAdmin->getUserDetails($this->userId);

        $this->objBizCard->setUserArray($user);
        $this->objBizCard->showResetImage = $reset;
        $this->objBizCard->resetModule = 'userdetails';

        return $this->objBizCard->show();
    }
    
    /**
     *
     * Method to show the user selectable grades
     * 
     * @acces public
     * @return VOID 
     */
    public function showGrades()
    {
        $selectGradeLabel = $this->objLanguage->code2Txt('mod_grades_grade', 'grades', NULL, 'ERROR: mod_grades_grade');        
        $selectLabel = $this->objLanguage->languageText('word_select', 'system', 'ERROR: word_select');
        $successTitleLabel = $this->objLanguage->languageText('word_success', 'system', 'ERROR: word_success');
        $successLabel = $this->objLanguage->code2Txt('mod_userdetails_gradesuccess', 'userdetails', NULL, 'ERROR: mod_userdetails_gradesuccess');
        $errorTitleLabel = $this->objLanguage->languageText('word_error', 'system', 'ERROR: word_error');
        $errorLabel = $this->objLanguage->code2Txt('mod_userdetails_gradeerror', 'userdetails', NULL, 'ERROR: mod_userdetails_gradeerror');
        
        $grades = $this->objDBgrades->getAll();
        $userGroups = $this->objGroups->getUserGroups($this->objUser->userId());                
        $name = NULL;
        if (!empty($userGroups))
        {
            foreach ($userGroups as $group)
            {
                foreach ($grades as $grade)
                {
                    if ($group['group_define_name'] == $grade['name'])
                    {
                        $name = $grade['name'];
                        break;
                        break;
                    }
                }
            }
        }
        
        $objDrop = new dropdown('new_name');
        $objDrop->addFromDB($grades, 'name', 'name');
        $objDrop->setSelected($name);
        $gradeDrop = $objDrop->show();
        
        $objInput = new textinput('old_name', $name, 'hidden', '50');
        $nameInput = $objInput->show();

        $objButton = new button('select', $selectLabel);
        $objButton->setId('grade_select');
        $selectButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . ucfirst(strtolower($selectGradeLabel)) . ': </b>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($nameInput . $gradeDrop, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($selectButton, '', '', '', '', '', '');
        $objTable->endRow();
        $gradeTable = $objTable->show();

        $objForm = new form('user', $this->uri(array(
            'action' => 'change_grade'
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($gradeTable);
        $gradeForm = $objForm->show();

        $string = $gradeForm;
        
        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_grade_success');
        $this->objDialog->setTitle(ucwords($successTitleLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent('<span class="success">' . $successLabel . '</span>');
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $dialog = $this->objDialog->show();

        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_grade_error');
        $this->objDialog->setTitle(ucwords($errorTitleLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent('<span class="error">' . $errorLabel . '</span>');
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $dialog .= $this->objDialog->show();

        return $string.$dialog;
    }  
    
    /**
     *
     * Method to change the users grade via ajax
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxChangeGrade()
    {
        $user = $this->objUserAdmin->getUserDetails($this->userId);

        $newName = $this->getParam('new_name');
        $groupId = $this->objGroups->getId($newName);
        $ret = $this->objGroups->addGroupUser($groupId, $user['puid']);
        
        $oldName = $this->getParam('old_name');
        if (!empty($oldName))
        {
            $groupId = $this->objGroups->getId($oldName);
            $ret = $this->objGroups->deleteGroupUser($groupId, $user['puid']);
        }

        echo $ret;
        die();
    }
    
    /**
     *
     * Method to display the user image
     * 
     * @access public
     * @return string $string The html string for display 
     */
    public function showUserImage()
    {
        $updateLabel = $this->objLanguage->languageText('phrase_updateimage', 'system', 'ERROR: phrase_updateimage');
        $errorTitleLabel = $this->objLanguage->languageText('word_error', 'system', 'ERROR: word_error');
        $successTitleLabel = $this->objLanguage->languageText('word_success', 'system', 'ERROR: word_success');
        $successLabel = $this->objLanguage->languageText('mod_userdetails_userimagechanged', 'userdetails', 'ERROR: mod_userdetails_userimagechanged');
        $errorNoFileLabel = $this->objLanguage->languageText('mod_userdetails_imagedoesnotexist', 'userdetails', 'ERROR: mod_userdetails_imagedoesnotexist');
        $errorNoImageLabel = $this->objLanguage->languageText('mod_userdetails_noimageprovided', 'userdetails', 'ERROR: mod_userdetails_noimageprovided');
        $errorNotImageLabel = $this->objLanguage->languageText('mod_userdetails_filenotimage', 'userdetails', 'ERROR: mod_userdetails_filenotimage');
        
        $objSelectFile = $this->newObject('selectimage', 'filemanager');
        $objSelectFile->name = 'imageselect';
        $objSelectFile->restrictFileList = array('jpg', 'gif', 'png', 'jpeg', 'bmp');
        $image = $objSelectFile->show();

        $objButton = new button('update', $updateLabel);
        $objButton->setId('update_image');
        $updateButton = $objButton->show();
                
        $objForm = new form('user_image', $this->uri(array(
            'action' => 'ajaxChangeImage'
        ), 'userdetails'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($image . '<br />' . $updateButton);
        $imageForm = $objForm->show();
        
        $string = $imageForm;
        
        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_imagechanged');
        $this->objDialog->setTitle(ucwords($successTitleLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent('<span class="success">' . $successLabel . '</span>');
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $dialog = $this->objDialog->show();

        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_nopicturegiven');
        $this->objDialog->setTitle(ucwords($errorTitleLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent('<span class="error">' . $errorNoImageLabel . '</span>');
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $dialog .= $this->objDialog->show();

        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_imagedoesnotexist');
        $this->objDialog->setTitle(ucwords($errorTitleLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent('<span class="error">' . $errorNoFileLabel . '</span>');
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $dialog .= $this->objDialog->show();

        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_fileisnotimage');
        $this->objDialog->setTitle(ucwords($errorTitleLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent('<span class="error">' . $errorNotImageLabel . '</span>');
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $dialog .= $this->objDialog->show();

        return $string . $dialog;
    }  
    
    /**
     *
     * Method to change the user's image via ajax
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxChangeImage()
    {
        $fileId = $this->getParam('imageselect');
        if ($fileId == '') {
            echo 'nopicturegiven';
            die();
        }
        
        $filepath = $this->objFile->getFullFilePath($fileId); 
        $extension = substr($filepath, -3);
        $ext = ($extension == 'png' || $extension == 'gif') ? 'png' : 'jpg';
        if ($fileId == FALSE) {
            echo 'imagedoesnotexist';
            die();
        }
        
        $mimetype = $this->objFile->getFileMimetype($fileId);        
        if (substr($mimetype, 0, 5) != 'image') {
            echo 'fileisnotimage';
            die();
        }
        
        $objImageResize = $this->getObject('imageresize', 'files');
        $objImageResize->setImg($filepath);
        
        //Resize to 100x100 Maintaining Aspect Ratio
        $objImageResize->resize(100, 100, TRUE);
        $storePath = 'user_images/' . $this->objUser->userId() . '.' . $ext;
        $this->objCleanUrl->cleanUpUrl($storePath);
        $result = $objImageResize->store($storePath);
        
        //Resize to 100x100 Maintaining Aspect Ratio
        $objImageResize->resize(35, 35, TRUE);
        $storePath = 'user_images/' . $this->objUser->userId() . '_small.' . $ext;
        $this->objCleanUrl->cleanUpUrl($storePath);
        $result = $objImageResize->store($storePath);
        
        $array = array(
            'card' => $this->showBizCard(),
            'image' => $this->objUser->getUserImage(),
        );
        echo json_encode($array);
        die();
    }
    
    /**
     *
     * Method to reset the user's profile image
     * 
     * @access public
     * @param boolean $isAjax TRUE if this method was called via ajax | FALSE if not
     * @return VOID 
     */
    public function ajaxResetImage($isAjax = TRUE)
    {
        $this->objUserAdmin->removeUserImage($this->objUser->userId());
        
        if ($isAjax)
        {
            $array = array(
                'card' => $this->showBizCard(),
                'image' => $this->objUser->getUserImage(),
            );
            echo json_encode($array);
            die();
        }
    }
    
    /**
     *
     * Method to show a block for the userdetails module
     * 
     * @access public
     * @return string $string The html string for the block display 
     */
    public function showBlock()
    {
        $bizcard = $this->showBizCard(FALSE);
        
        $objLink = new link($this->uri(NULL, 'userdetails'));
        $link = $objLink->show();
        
        $string = $bizcard . '<br />' . $link;
        
        return $string;
    }
}
?>