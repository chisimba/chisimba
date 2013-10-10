<?php
/**
 * Class to handle schools elements.
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
 * Class to handle blog elements
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
class schoolusersops extends object
{
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
            $this->objUserAdmin = $this->getObject('useradmin_model2', 'security');
            $this->objSvars = $this->getObject('serializevars', 'utilities');
            $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            $this->objConfirm = $this->newObject('confirm', 'utilities');
            $this->objCaptcha = $this->getObject('captcha', 'utilities');

            // Load html elements.
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->objTable = $this->loadClass('htmltable', 'htmlelements');
            $this->objLink = $this->loadClass('link', 'htmlelements');
            $this->objInput = $this->loadClass('textinput', 'htmlelements');
            $this->objFieldset = $this->loadClass('fieldset', 'htmlelements');
            $this->objDropdown = $this->loadClass('dropdown', 'htmlelements');
            $this->objForm = $this->loadClass('form', 'htmlelements');
            $this->objLayer = $this->loadClass('layer', 'htmlelements');
            $this->objRadio = $this->loadClass('radio', 'htmlelements');
            $this->objText = $this->loadClass('textarea', 'htmlelements');
            
            // Load db classes,
            $this->objDBdata = $this->getObject('dbdata', 'schoolusers');
            $this->objDBusers = $this->getObject('dbusers', 'schoolusers');
            $this->objCountries = $this->getObject('languagecode' , 'language');
            $this->objDBschools = $this->getObject('dbschools_schools' , 'schools');
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
     * Method to generate the html for the left block template
     * 
     * @access public
     * @return string $string The html string to be sent to the template 
     */
    public function showLeft()
    {
        $firstNameLabel = $this->objLanguage->languageText('phrase_firstname', 'system', 'ERROR: phrase_firstname');
        $lastNameLabel = $this->objLanguage->languageText('phrase_lastname', 'system', 'ERROR: phrase_lastname');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'ERROR: word_add');
        $listLabel = $this->objLanguage->languageText('word_list', 'system', 'ERROR: word_list');
        $addUserLabel = $this->objLanguage->languageText('phrase_adduser', 'system', 'ERROR: phrase_adduser');
        $selectLabel = $this->objLanguage->languageText('word_select', 'system', 'ERROR: word_select');
        $findUserLabel = $this->objLanguage->languageText('phrase_finduser', 'system', 'ERROR: phrase_finduser');
        $listUsersLabel = $this->objLanguage->languageText('phrase_listusers', 'system', 'ERROR: phrase_listusers');

        $this->objIcon->title = $addLabel;
        $this->objIcon->alt = $addLabel;
        $this->objIcon->setIcon('user_plus', 'png');
        $addIcon = $this->objIcon->show();

        $objLink = new link($this->uri(array('action' => 'form')));
        $objLink->link = $addIcon . '&nbsp' . $addUserLabel;
        $addLink = $objLink->show();
            
        $objLayer = new layer();
        $objLayer->id = 'add';
        $objLayer->str = $addLink;
        $addLayer = $objLayer->show();
        
        $this->objIcon->title = $listLabel;
        $this->objIcon->alt = $listLabel;
        $this->objIcon->setIcon('group', 'png');
        $listIcon = $this->objIcon->show();

        $objLink = new link($this->uri(array('action' => 'view')));
        $objLink->link = $listIcon . '&nbsp' . $listUsersLabel;
        $listLink = $objLink->show();
            
        $objLayer = new layer();
        $objLayer->id = 'add';
        $objLayer->str = $listLink;
        $listLayer = $objLayer->show();
        
        $objRadio = new radio('field');
        $objRadio->addOption('firstname', $firstNameLabel);
        $objRadio->addOption('surname', $lastNameLabel);
        $objRadio->setSelected('surname');
        $fieldRadio = $objRadio->show();        
        
        $objInput = new textinput('name', '', '', '');
        $nameInput = $objInput->show();
        
        $objInput = new textinput('id', '', 'hidden', '');
        $idInput = $objInput->show();        
        
        $objButton = new button('select', $selectLabel);
        $objButton->setId('select');
        $selectButton = $objButton->show();
        
        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $findUserLabel. '</b>';
        $objFieldset->contents =  $fieldRadio . '<br />' . $idInput . $nameInput . '<br />' . $selectButton;
        $findFieldset = $objFieldset->show();

        $objForm = new form('find', $this->uri(array(
            'action' => 'show'
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($findFieldset);
        $findForm = $objForm->show();

        $objLayer = new layer();
        $objLayer->id = 'find';
        $objLayer->str = $findForm;
        $findLayer = $objLayer->show();
        
        $objLayer = new layer();
        $objLayer->id = 'bar';
        $objLayer->overflow = 'auto';
        $objLayer->str =$addLayer . '<br />' . $listLayer . '<br />' . $findLayer;
        $barLayer = $objLayer->show();        
        
        $string = $barLayer;
        
        return $string;
    }
    
    /**
     *
     * Method to return the user data for the autocomplete
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxFindUser()
    {
        $search = $this->getParam('term');
        $field = $this->getParam('field');
        
        $userArray = $this->objUserAdmin->searchUsers($field, $search, 'contains', 'firstname');

        $list = array();
        foreach ($userArray as $user)
        {
            $array['label'] = $user['firstname'] . ' ' . $user['surname'];
            $array['value'] = $user['id'];
            $list[] = $array;
        }
        
        echo json_encode($list);
        die();
    }    
    
    /**
     *
     * Method to generate the html for the left block template
     * 
     * @access public
     * @return string $string The html string to be sent to the template 
     */
    public function showUser()
    {
        $usernameLabel = $this->objLanguage->languageText('word_username', 'system', 'ERROR: word_username');
        $schoolLabel = $this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'ERROR: mod_schools_school');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $firstNameLabel = $this->objLanguage->languageText('phrase_firstname', 'system', 'ERROR: phrase_firstname');
        $middleNameLabel = $this->objLanguage->languageText('mod_schoolusers_middlename', 'schoolusers', 'ERROR: mod_schoolusers_middlename');
        $lastNameLabel = $this->objLanguage->languageText('phrase_lastname', 'system', 'ERROR: phrase_lastname');
        $genderLabel = $this->objLanguage->languageText('word_gender', 'system', 'ERROR: word_gender');
        $maleLabel = $this->objLanguage->languageText('word_male', 'system', 'ERROR: word_male');
        $femaleLabel = $this->objLanguage->languageText('word_female', 'system', 'ERROR: word_female');
        $dateOfBirthLabel = $this->objLanguage->languageText('phrase_dateofbirth', 'system', 'ERROR: phrase_dateofbirth');
        $addressLabel = $this->objLanguage->languageText('word_address', 'system', 'ERROR: word_address');
        $cityLabel = $this->objLanguage->languageText('word_city', 'system', 'ERROR: word_city');
        $stateLabel = $this->objLanguage->languageText('word_address', 'system', 'ERROR: word_address');
        $countryLabel = $this->objLanguage->languageText('word_country', 'system', 'ERROR: word_country');
        $postalCodeLabel = $this->objLanguage->languageText('phrase_postalcode', 'system', 'ERROR: phrase_postalcode');
        $emailAddressLabel = $this->objLanguage->languageText('phrase_emailaddress', 'system', 'ERROR: phrase_eamiladdress');
        $contactNumberLabel = $this->objLanguage->languageText('phrase_contactnumber', 'system', 'ERROR: phrase_contactnumber');
        $descriptionLabel = $this->objLanguage->languageText('mod_schoolusers_aboutyourself', 'schoolusers', 'ERROR: mod_schoolusers_aboutyourself');
        $editLabel = $this->objLanguage->languageText('word_edit', 'system', 'ERROR: word_edit');
        $editUserLabel = $this->objLanguage->languageText('phrase_edituser', 'system', 'ERROR: phrase_edituser');

        $id = $this->getParam('id');
        
        $userArray = $this->objDBusers->getUser($id);
        
        if ($userArray['school_id'])
        {
            $schoolArray = $this->objDBschools->getSchool($userArray['school_id']);
            $school = $schoolArray['name'];
        }
        else
        {
            $school = NULL;
        }
                
        $sex = $userArray['sex'] == 'M' ? $maleLabel : $femaleLabel;
        
        

        $array = explode('|', $userArray['address']);
        $addressArray = array();
        foreach($array as $line)
        {
            if (!empty($line))
            {
                $addressArray[] = $line;
            }
        }
        $addressString = implode(',<br />', $addressArray);

        $country = $this->objCountries->getName($userArray['country']);

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $usernameLabel . ': </b>', '200px', '', '', 'even', '', '');
        $objTable->addCell($userArray['username'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . ucfirst(strtolower($schoolLabel)). ': </b>', '200px', '', '', 'odd', '', '');
        $objTable->addCell($school, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '200px', '', '', 'even', '', '');
        $objTable->addCell($userArray['title'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $firstNameLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($userArray['firstname'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $middleNameLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($userArray['middle_name'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $lastNameLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($userArray['surname'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $genderLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($sex, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $dateOfBirthLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($userArray['date_of_birth'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $addressLabel . ': </b>', '', 'top', '', 'even', '', '');
        $objTable->addCell($addressString, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $cityLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($userArray['city'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $stateLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($userArray['state'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $countryLabel. ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($country, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $postalCodeLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($userArray['postal_code'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $emailAddressLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($userArray['emailaddress'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $contactNumberLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($userArray['cellnumber'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descriptionLabel . ': </b>', '', 'top', '', 'odd', '', '');
        $objTable->addCell($userArray['description'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $userTable = $objTable->show();
        
        $this->objIcon->title = $editLabel;
        $this->objIcon->alt = $editLabel;
        $this->objIcon->setIcon('user_pencil', 'png');
        $editIcon = $this->objIcon->show();

        $objLink = new link($this->uri(array('action' => 'form', 'id' => $userArray['id'])));
        $objLink->link = $editUserLabel. '&nbsp;' . $editIcon;
        $editLink = $objLink->show();
        
        $objLayer = new layer();
        $objLayer->id = 'users';
        $objLayer->str = $userTable . '<br />' . $editLink;
        $userLayer = $objLayer->show();
        
        $string = $userLayer;

        return $string;
    }
    
    /**
     *
     * Method to generate the html for the form template
     * 
     * @access public
     * @return string $string The html string to be sent to the template 
     */
    public function showForm()
    {
        $idValue = $this->getParam('id', NULL);
        
        if (empty($idValue))
        {
            $titleValue = NULL;
            $firstNameValue = NULL;
            $middleNameValue = NULL;
            $lastNameValue = NULL;
            $genderValue = NULL;
            $dayValue = NULL;
            $monthValue = NULL;
            $yearValue = NULL;
            $addressOneValue = NULL;
            $addressTwoValue = NULL;
            $cityValue = NULL;
            $stateValue = NULL;
            $countryValue = NULL;
            $postalCodeValue = NULL;
            $emailAddressValue = NULL;
            $contactNumberValue = NULL;
            $schoolIdValue = NULL;
            $schoolValue = NULL;
            $descriptionValue = NULL;
            $usernameValue = NULL;
            $passwordValue = NULL;
            $confirmPasswordValue = NULL;
        }
        else
        {
            $userArray = $this->objDBusers->getUser($idValue);
            
            $titleValue = $userArray['title'];
            $firstNameValue = $userArray['firstname'];
            $middleNameValue = $userArray['middle_name'];
            $lastNameValue = $userArray['surname'];
            $genderValue = $userArray['sex'];            
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
            $countryValue = $userArray['country'];
            $postalCodeValue = $userArray['postal_code'];
            $emailAddressValue = $userArray['emailaddress'];
            $contactNumberValue = $userArray['cellnumber'];
            $schoolIdValue = $userArray['school_id'];
            $schoolArray = $this->objDBschools->getSchool($schoolIdValue);
            $schoolValue = $schoolArray['name'];
            $descriptionValue = $userArray['description'];
            $usernameValue = NULL;
            $passwordValue = NULL;
            $confirmPasswordValue = NULL;
        }
        
        $errors = $this->getSession('errors');

        $titleValue = !empty($errors) ? $errors['data']['title'] : $titleValue;
        $firstNameValue = !empty($errors) ? $errors['data']['first_name'] : $firstNameValue;
        $middleNameValue = !empty($errors) ? $errors['data']['middle_name'] : $middleNameValue;
        $lastNameValue = !empty($errors) ? $errors['data']['last_name'] : $lastNameValue;
        $genderValue = !empty($errors) ? $errors['data']['gender'] : $genderValue;
        $dayValue = !empty($errors) ? $errors['data']['date_of_birth'][0] : $dayValue;
        $monthValue = !empty($errors) ? $errors['data']['date_of_birth'][1] : $monthValue;
        $yearValue = !empty($errors) ? $errors['data']['date_of_birth'][2] : $yearValue;
        $addressOneValue = !empty($errors) ? $errors['data']['address'][0] : $addressOneValue;
        $addressTwoValue = !empty($errors) ? $errors['data']['address'][1] : $addressTwoValue;
        $cityValue = !empty($errors) ? $errors['data']['city'] : $cityValue;
        $stateValue = !empty($errors) ? $errors['data']['state'] : $stateValue;
        $countryValue = !empty($errors) ? $errors['data']['country'] : $countryValue;
        $postalCodeValue = !empty($errors) ? $errors['data']['postal_code'] : $postalCodeValue;
        $emailAddressValue = !empty($errors) ? $errors['data']['email_address'] : $emailAddressValue;
        $contactNumberValue = !empty($errors) ? $errors['data']['contact_number'] : $contactNumberValue;
        $schoolIdValue = !empty($errors) ? $errors['data']['school_id'] : $schoolIdValue;
        if (!is_null($schoolIdValue))
        {
            $schoolArray = $this->objDBschools->getSchool($schoolIdValue);
            $schoolValue = $schoolArray['name'];
        }
        $descriptionValue = !empty($errors) ? $errors['data']['description'] : $descriptionValue;
        $usernameValue = !empty($errors) ? $errors['data']['username'] : $usernameValue;
        $passwordValue = !empty($errors) ? $errors['data']['password'] : $passwordValue;
        $confirmPasswordValue = !empty($errors) ? $errors['data']['confirm_password'] : $confirmPasswordValue;

        $schoolError = (!empty($errors) && array_key_exists('school_id', $errors['errors'])) ? $errors['errors']['school_id'] : NULL;
        $titleError = (!empty($errors) && array_key_exists('title', $errors['errors'])) ? $errors['errors']['title'] : NULL;
        $firstNameError = (!empty($errors) && array_key_exists('first_name', $errors['errors'])) ? $errors['errors']['first_name'] : NULL;
        $lastNameError = (!empty($errors) && array_key_exists('last_name', $errors['errors'])) ? $errors['errors']['last_name'] : NULL;
        $genderError = (!empty($errors) && array_key_exists('gender', $errors['errors'])) ? $errors['errors']['gender'] : NULL;
        $dateOfBirthError = (!empty($errors) && array_key_exists('date_of_birth', $errors['errors'])) ? $errors['errors']['date_of_birth'] : NULL;
        $addressError = (!empty($errors) && array_key_exists('address', $errors['errors'])) ? $errors['errors']['address'] : NULL;
        $cityError = (!empty($errors) && array_key_exists('city', $errors['errors'])) ? $errors['errors']['city'] : NULL;
        $stateError = (!empty($errors) && array_key_exists('state', $errors['errors'])) ? $errors['errors']['state'] : NULL;
        $postalCodeError = (!empty($errors) && array_key_exists('postal_code', $errors['errors'])) ? $errors['errors']['postal_code'] : NULL;
        $emailAddressError = (!empty($errors) && array_key_exists('email_address', $errors['errors'])) ? $errors['errors']['email_address'] : NULL;
        $descriptionError = (!empty($errors) && array_key_exists('description', $errors['errors'])) ? $errors['errors']['description'] : NULL;
        $usernameError = (!empty($errors) && array_key_exists('username', $errors['errors'])) ? $errors['errors']['username'] : NULL;
        $passwordError = (!empty($errors) && array_key_exists('password', $errors['errors'])) ? $errors['errors']['password'] : NULL;
        $captchaError = (!empty($errors) && array_key_exists('captcha', $errors['errors'])) ? $errors['errors']['captcha'] : NULL;
        
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $selectTitleLabel = $this->objLanguage->languageText('phrase_selecttitle', 'system', 'ERROR: phrase_selecttitle');
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
        $middleNameLabel = $this->objLanguage->languageText('mod_schoolusers_middlename', 'schoolusers', 'ERROR: mod_schoolusers_middlename');
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
        $schoolLabel = $this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'ERROR: mod_schools_school');
        $descriptionLabel = $this->objLanguage->languageText('mod_schoolusers_aboutyourself', 'schoolusers', 'ERROR: mod_schoolusers_aboutyourself');
        $usernameLabel = $this->objLanguage->languageText('word_username', 'system', 'ERROR: word_username');
        $passwordLabel = $this->objLanguage->languageText('word_password', 'system', 'ERROR: word_password');
        $confirmPasswordLabel = $this->objLanguage->languageText('phrase_confirmpassword', 'system', 'ERROR: phrase_confirmpassword');
        $passwordNotAlike = $this->objLanguage->languageText('mod_schoolusers_passwordsnotalike', 'schoolusers', 'TEXT: mod_schoolusers_passwordsnotalike');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $redrawLabel = $this->objLanguage->languageText('mod_schoolusers_redraw', 'schoolusers', 'ERROR: mod_schoolusers_redraw');
        $verifyLabel = $this->objLanguage->languageText('mod_schoolusers_verify', 'schoolusers', 'ERROR: mod_schoolusers_verify');

        $arrayVars = array();
        $arrayVars['password_not_alike'] = $passwordNotAlike;
       
        // pass password error to javascript.
        $this->objSvars->varsToJs($arrayVars);
        
        // set up html elements
        $objInput = new textinput('school', $schoolValue, '', '50');
        $schoolInput = $objInput->show();
        
        $objInput = new textinput('school_id', $schoolIdValue, 'hidden', '');
        $schoolIdInput = $objInput->show();
        
        $objDrop = new dropdown('title');
        $objDrop->addOption('', $selectTitleLabel);
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
        
        $objInput = new textinput('middle_name', $middleNameValue, '', '50');
        $middleNameInput = $objInput->show();
        
        $objInput = new textinput('last_name', $lastNameValue, '', '50');
        $lastNameInput = $objInput->show();
        
        $objRadio = new radio('gender');
        $objRadio->addOption('M', $maleLabel);
        $objRadio->addOption('F', $femaleLabel);
        $objRadio->setSelected($genderValue);
        $genderRadio = $objRadio->show();
        
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

        $countryDrop = $this->objCountries->countryAlpha($countryValue);

        $objInput = new textinput('postal_code', $postalCodeValue, '', '50');
        $postalCodeInput = $objInput->show();

        $objInput = new textinput('email_address', $emailAddressValue, '', '50');
        $emailAddressInput = $objInput->show();

        $objInput = new textinput('contact_number', $contactNumberValue, '', '50');
        $contactNumberInput = $objInput->show();

        $objText = new textarea('description', $descriptionValue);
        $descriptionText = $objText->show();

        $objInput = new textinput('username', $usernameValue, '', '50');
        $usernameInput = $objInput->show();
        
        $objInput = new textinput('password', $passwordValue, 'password', '50');
        $passwordInput = $objInput->show();
        
        $objInput = new textinput('confirm_password', $confirmPasswordValue, 'password', '50');
        $confirmPasswordInput = $objInput->show();
        
        $objInput = new textinput('id', $idValue, 'hidden', '');
        $idInput = $objInput->show();
        
        $objLayer = new layer();
        $objLayer->id = 'username';
        $usernameLayer = $objLayer->show();

        $objLink = new link('#input_captcha');
        $objLink->cssId = 'redraw';
        $objLink->link = $redrawLabel;
        $redrawLink = $objLink->show();
            
        $objLayer = new layer();
        $objLayer->id = 'captcha';
        $objLayer->str = $this->objCaptcha->show();
        $captchaLayer = $objLayer->show();

        $objInput = new textinput('request_captcha', '', '', '50');
        $captchaInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setToSubmit();
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . ucfirst(strtolower($schoolLabel)) . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($schoolError . $schoolIdInput . $schoolInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '200px', '', '', 'odd', '', '');
        $objTable->addCell($titleError . $titleDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $firstNameLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($firstNameError . $firstNameInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $middleNameLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($middleNameInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $lastNameLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($lastNameError . $lastNameInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $genderLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($genderError . $genderRadio, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $dateOfBirthLabel. ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($dateOfBirthError . $dayDrop . ' / ' . $monthDrop . ' / ' . $yearDrop, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $addressLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($addressError . $addressOneInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('' , '', '', '', 'even', '', '');
        $objTable->addCell($addressTwoInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $cityLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($cityError . $cityInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $stateLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($stateError . $stateInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $countryLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($countryDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $postalCodeLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($postalCodeError . $postalCodeInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $emailAddressLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($emailAddressError . $emailAddressInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $contactNumberLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($contactNumberInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descriptionLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($descriptionError . $descriptionText, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $usernameLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($usernameError . $usernameLayer . $usernameInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $passwordLabel . ': </b>', '', '', '', 'odd', '', '');
        $objTable->addCell($passwordError . $passwordInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $confirmPasswordLabel . ': </b>', '', '', '', 'even', '', '');
        $objTable->addCell($confirmPasswordInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        if (!$this->objUser->isLoggedIn())
        {
            $objTable->startRow();
            $objTable->addCell('<span style="font-weight: bold; color: brown;">' . $verifyLabel . '</span>', '', '', '', '', 'colspan="2"', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($captchaError, '', '', '', '', 'colspan="2"', '');
            $objTable->endRow();
            $objTable->startRow();           
            $objTable->addCell($captchaLayer . '<b>' . $redrawLink . '</b>', '', '', '', '', 'colspan="2"', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($captchaInput, '', '', '', '', 'colspan="2"', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp;' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $userTable = $objTable->show();
        
        $objForm = new form('user', $this->uri(array(
            'action' => 'validate'
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($userTable);
        $addForm = $objForm->show();

        $string = $addForm;
        
        return $string;        
    }

    /**
     * Method to return the html for an ajax call for check for unique username
     * 
     * @access public
     * @return string The string to display 
     */
    public function ajaxUsername($isAjax = TRUE, $username = NULL)
    {
        // Set up text elements.
        $usernameExists = $this->objLanguage->languageText('mod_schoolusers_usernameexists', 'schoolusers', 'ERROR: mod_schoolusers_usernameexists');
        $invalidUsername = $this->objLanguage->languageText('mod_schoolusers_invalidusername', 'schoolusers', 'ERROR: mod_schoolusers_invalidusername');
        $usernameShort = $this->objLanguage->languageText('mod_schoolusers_usernameshort', 'schoolusers', 'ERROR: mod_schoolusers_usernameshort');
        $usernameAvaliable = $this->objLanguage->languageText('mod_schoolusers_usernameavailable', 'schoolusers', 'ERROR: mod_schoolusers_usernameavailable');
        $success = $this->objLanguage->languageText('word_success', 'system', 'ERROR: word_success');
        
        // Get parameter.
        if (!$username)
        {
            $username = $this->getParam('username', FALSE);
        }
        
        $users = FALSE;
        if (strlen($username) >= 3)
        {
            if (preg_match('/[^0-9A-Za-z]/',$username) != 0)
            {
                $string = $this->error($invalidUsername);
            }
            else
            {
                // Get data
                $users = $this->objUserAdmin->usernameAvailable($username);
                if ($users === TRUE)
                {
                    $this->objIcon->title = $success;
                    $this->objIcon->alt = $success;
                    $this->objIcon->setIcon('accept', 'png');
                    $successIcon = $this->objIcon->show();

                    $string = '<span style="color: green;">' . $successIcon . '&nbsp;<b>' . $usernameAvaliable . '</b></span>';
                }
                else
                {
                    $string = $this->error($usernameExists);
                }
            }
        }
        else
        {
            $string = $this->error($usernameShort);
        }
        if ($isAjax)
        {
            echo $string;
            die();
        }
        else
        {
            if (!$users)
            {
                return '<div id="username_error">' . $string . '</div>';
            }
        }
    }

    /**
     *
     * Method to return the schools data for autocomplet
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
            if ($fieldname != 'id' && $fieldname != 'school_id' && $fieldname != 'title'
                && $fieldname != 'middle_name' && $fieldname != 'gender' && $fieldname != 'date_of_birth'
                && $fieldname != 'address' && $fieldname != 'country' && $fieldname != 'contact_number'
                && $fieldname != 'username' && $fieldname != 'password' && $fieldname != 'confirm_password'
                && $fieldname != 'captcha' && $fieldname != 'request_captcha')
            {
                if (empty($value))
                {
                    $name = explode('_', $fieldname);
                    $name = implode(' ', $name);
                    $array = array('fieldname' => $name);
                    $errorText = $this->objLanguage->code2Txt('mod_schoolusers_error_1', 'schoolusers', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';                                    
                }
                elseif ($fieldname == 'email_address')
                {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL) == FALSE)
                    {
                        $errorText = $this->objLanguage->languageText('mod_schoolusers_invalidemail', 'schoolusers', 'TEXT: mod_schoolusers_invalidemail, not found');
                        $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                    }
                }
            }
            elseif ($fieldname == 'school_id')
            {
                if (empty($value))
                {
                    $array = array('fieldname' => $this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'ERROR: mod_schools_school'));
                    $errorText = $this->objLanguage->code2Txt('mod_schoolusers_error_1', 'schoolusers', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'title' || $fieldname == 'gender')
            {
                if (empty($value))
                {
                    $array = array('fieldname' => $fieldname);
                    $errorText = $this->objLanguage->code2Txt('mod_schoolusers_error_2', 'schoolusers', $array);
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
                    $errorText = $this->objLanguage->code2Txt('mod_schoolusers_error_1', 'schoolusers', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'address')
            {
                if (empty($value[0]) && empty($value[1]))
                {
                    $array = array('fieldname' => $fieldname);
                    $errorText = $this->objLanguage->code2Txt('mod_schoolusers_error_1', 'schoolusers', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'username')
            {
                if (empty($data['id']))
                {
                    if (empty($value))
                    {
                        $array = array('fieldname' => $fieldname);
                        $errorText = $this->objLanguage->code2Txt('mod_schoolusers_error_1', 'schoolusers', $array);
                        $errors[$fieldname] = '<div id="username_error">' . $this->error(ucfirst(strtolower($errorText))) . '</div>';                
                    }
                    else
                    {
                        $error = $this->ajaxUsername(FALSE, $value);
                        if (!empty($error))
                        {
                            $errors[$fieldname] = $error;
                        }
                    }
                }
                else
                {
                    if (!empty($value))
                    {
                        $error = $this->ajaxUsername(FALSE, $value);
                        if (!empty($error))
                        {
                            $errors[$fieldname] = $error;
                        }
                    }
                }
            }
            elseif ($fieldname == 'password')
            {
                if (empty($data['id']))
                {
                    if (empty($value) && empty($data['confirm_password']))
                    {
                        $array = array('fieldname' => $fieldname);
                        $errorText = $this->objLanguage->code2Txt('mod_schoolusers_error_1', 'schoolusers', $array);
                        $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                    }
                }
                if ($value != $data['confirm_password'])
                {
                    $errorText = $this->objLanguage->languageText('mod_schoolusers_passwordsnotalike','schoolusers', 'ERROR: mod_schoolusers_passwordsnotalike');
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            if (!$this->objUser->isLoggedIn())
            {
                if ($fieldname == 'captcha')
                {
                    if (md5(strtoupper($data['request_captcha'])) != $data['captcha'])
                    {
                        $errorText = $this->objLanguage->languageText('mod_schoolusers_imageincorrect','schoolusers', 'ERROR: mod_schoolusers_imageincorrect');
                        $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                    }
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
        if (empty($data['id']))
        {        
            $userId = $this->objUserAdmin->generateUserId();
            $id = $this->objUserAdmin->addUser($userId, $data['username'], $data['password'], $data['title'], 
                $data['first_name'], $data['last_name'], $data['email_address'], $data['gender'],
                $data['country'], $data['contact_number'], '', 'useradmin', '1');
            
            $extra = array();
            $extra['user_id'] = $id;
            $extra['middle_name'] = $data['middle_name'];
            $extra['date_of_birth'] = date('Y-m-d', strtotime($data['date_of_birth'][0] . '-' . $data['date_of_birth'][1] . '-' . $data['date_of_birth'][2]));
            $extra['address'] = implode('|', $data['address']);
            $extra['city'] = $data['city'];
            $extra['state'] = $data['state'];
            $extra['postal_code'] = $data['postal_code'];
            $extra['school_id'] = $data['school_id'];
            $extra['description'] = $data['description'];
            $extra['created_by'] = $this->objUser->PKId();
            $extra['date_created'] = date('Y-m-d H:i:s');

            $this->objDBdata->saveData($extra);
        }
        else
        {
            $user = array();
            if (!empty($data['username']))
            {
                $user['username'] = $data['username'];
            }
            $user['firstname'] = $data['first_name'];
            $user['surname'] = $data['last_name'];
            $user['title'] = $data['title'];
            $user['emailaddress'] = $data['email_address'];
            $user['sex'] = $data['gender'];
            $user['country'] = $data['country'];
            $user['cellnumber'] = $data['contact_number'];
            if (!empty($data['password']))
            {
                $user['password'] = $data['password'];
            }
            $user['updated'] = date('Y-m-d');
            $this->objDBusers->updateUser($data['id'], $user);
            
            $extra = array();
            $extra['middle_name'] = $data['middle_name'];
            $extra['date_of_birth'] = date('Y-m-d', strtotime($data['date_of_birth'][0] . '-' . $data['date_of_birth'][1] . '-' . $data['date_of_birth'][2]));
            $extra['address'] = implode('|', $data['address']);
            $extra['city'] = $data['city'];
            $extra['state'] = $data['state'];
            $extra['postal_code'] = $data['postal_code'];
            $extra['school_id'] = $data['school_id'];
            $extra['description'] = $data['description'];
            $extra['modified_by'] = $this->objUser->PKId();
            $extra['date_modified'] = date('Y-m-d H:i:s');

            $this->objDBdata->updateData($data['id'], $extra);
        }
    }
    
    public function showFlexigrid()
    {
        $objFlex = $this->newObject ('flexigrid', 'jquerycore');

        $userListLabel = $this->objLanguage->languageText('phrase_listusers', 'system', 'ERROR: phrase_listusers');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $firstNameLabel = $this->objLanguage->languageText('phrase_firstname', 'system', 'ERROR: phrase_firstname');
        $lastNameLabel = $this->objLanguage->languageText('phrase_lastname', 'system', 'ERROR: phrase_lastname');
        $usernameLabel = $this->objLanguage->languageText('word_username', 'system', 'ERROR: word_username');
        $emailAddressLabel = $this->objLanguage->languageText('phrase_emailaddress', 'system', 'ERROR: phrase_emailaddress');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'ERROR: word_add');
        $editLabel = $this->objLanguage->languageText('word_edit', 'system', 'ERROR: word_edit');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $displayLabel = $this->objLanguage->languageText('word_display', 'system', 'ERROR: word_display');

        $objFlex->setCssId('grid_users');
        $objFlex->setUrl('index.php?module=schoolusers&action=ajaxFlexigridUsers');
        $objFlex->setTitle($userListLabel);
        $objFlex->addColumn($usernameLabel, 'username', 125);
        $objFlex->addColumn($titleLabel, 'title', 50);
        $objFlex->addColumn($firstNameLabel, 'firstname', 150);
        $objFlex->addColumn($lastNameLabel, 'surname', 150);
        $objFlex->addColumn($emailAddressLabel, 'emailaddress', 150);
        $objFlex->addColumn($displayLabel, 'display', 50, FALSE, 'center');
        $objFlex->addColumn($editLabel, 'edit', 50, FALSE, 'center');
        $objFlex->addColumn($deleteLabel, 'delete', 50, FALSE, 'center');
        $objFlex->addButton($addLabel, 'doAdd', 'add');
        $objFlex->addSearchitem($usernameLabel, 'username');
        $objFlex->addSearchitem($firstNameLabel, 'firstname');
        $objFlex->addSearchitem($lastNameLabel, 'surname', TRUE);
        $objFlex->setSortname('surname');
        $objFlex->setSortorder('ASC');
        $objFlex->setUsepager(TRUE);
        $objFlex->setUseRp(TRUE);
        $objFlex->setRp(10);
        $objFlex->setResizable(TRUE);
        $objFlex->setShowTableToggleBtn(TRUE);
        $objFlex->setHeight(325);
        $objFlex->setWidth(880);
        $objFlex->setSingleSelect(TRUE);
        $flexigrid = $objFlex->show();
        
         return $flexigrid;
    }
    
    /**
     *
     * Method to add the links to the user data before display in the grid
     * 
     * @access public
     * @return object $data The json encoded data 
     */
    public function ajaxFlexigridUsers()
    {
        $editUserLabel = $this->objLanguage->languageText('phrase_edituser', 'system', 'ERROR: phrase_edituser');
        $deleteUserLabel = $this->objLanguage->languageText('phrase_deleteuser', 'system', 'ERROR: phrase_deleteuser');
        $displayUserLabel = $this->objLanguage->languageText('phrase_displayuser', 'system', 'ERROR: phrase_displayuser');
        $deleteConfirmLabel = $this->objLanguage->languageText('mod_schoolusers_deleteconfirm', 'schoolusers', 'ERROR: mod_schoolusers_deleteconfirm');

        $page = $this->getParam('page', 1);
        $sortname = $this->getParam('sortname', 'surname');
        $sortorder = $this->getParam('sortorder', 'ASC');
        $qtype = $this->getParam('qtype', NULL);
        $query = $this->getParam('query', NULL);
        $rp = $this->getParam('rp', 10);
        
        $data = $this->objDBusers->getFlexigridUsers($page, $sortname, $sortorder, $qtype, $query, $rp);
        
        foreach ($data['rows'] as $key => $line)
        {
            $this->objIcon->title = $displayUserLabel;
            $this->objIcon->alt = $displayUserLabel;
            $this->objIcon->setIcon('user_go', 'png');
            $displayIcon = $this->objIcon->show();

            $objLink = new link($this->uri(array('action' => 'show', 'id' => $line['id'])));
            $objLink->link = $displayIcon;
            $displayLink = $objLink->show();

            $this->objIcon->title = $editUserLabel;
            $this->objIcon->alt = $editUserLabel;
            $this->objIcon->setIcon('user_pencil', 'png');
            $editIcon = $this->objIcon->show();

            $objLink = new link($this->uri(array('action' => 'form', 'id' => $line['id'])));
            $objLink->link = $editIcon;
            $editLink = $objLink->show();

            $this->objIcon->setIcon('user_minus', 'png');
            $this->objIcon->title = $deleteUserLabel;
            $this->objIcon->alt = $deleteUserLabel;
            $icon = $this->objIcon->show();

            $location = $this->uri(array('action' => 'delete', 'id' => $line['id']));

            $this->objConfirm->setConfirm($icon, $location, $deleteConfirmLabel);
            $deleteLink = $this->objConfirm->show();

            $data['rows'][$key]['cell']['display'] = $displayLink;
            $data['rows'][$key]['cell']['edit'] = $editLink;
            $data['rows'][$key]['cell']['delete'] = $deleteLink;
        }
        
        return json_encode($data);
    }
}
?>