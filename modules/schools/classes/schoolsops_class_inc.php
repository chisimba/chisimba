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
class schoolsops extends object
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
            $this->objTab = $this->newObject('tabber', 'htmlelements');
            
            // Load db classes,
            $this->objDBprovinces = $this->getObject('dbschools_provinces', 'schools');
            $this->objDBdistricts = $this->getObject('dbschools_districts', 'schools');
            $this->objDBcontacts = $this->getObject('dbschools_contacts', 'schools');
            $this->objDBschools = $this->getObject('dbschools_schools', 'schools');
            $this->objCountries = $this->getObject('languagecode' , 'language');
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
     * Method to generate the html for the find schools template
     *
     * @access public
     * @return string $string The html string to be sent to the template
     */
    public function findSchool()
    {
        $this->appendArrayVar('headerParams',
            $this->getJavaScriptFile('plugins/ui/js/jquery-ui-1.8.7.custom.min.js',
            'jquery'));
        $cssUri = $this->getResourceUri('plugins/ui/css/ui-lightness/jquery-ui-1.8.7.custom.css',
            'jquery');
        $this->appendArrayVar('headerParams', 
            "<link href='$cssUri' rel='stylesheet' type='text/css'/>");

        $count = $this->objDBschools->getCount();
 
        // set up language elements.
        $selectLabel = $this->objLanguage->languageText('word_select', 'system', 'WORD: word_select, not found');
        $schoolLabel = ucfirst($this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'TEXT: mod_schools_school, not found'));
        $schoolNameLabel = ucfirst($this->objLanguage->code2Txt('mod_schools_schoolname', 'schools', NULL, 'TEXT: mod_schools_schoolname, not found'));
        $addSchoolLabel = $this->objLanguage->code2Txt('mod_schools_addschool', 'schools', NULL, 'TEXT: mod_schools_addchool, not found');
        $noSchoolsLabel = $this->objLanguage->code2Txt('mod_schools_noschools', 'schools', NULL, 'TEXT: mod_schools_noschools, not found');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'WORD: word_add, not found');
        $selectSchoolLabel = $this->objLanguage->code2Txt('mod_schools_selectschool', 'schools', NULL, 'TEXT: mod_schools_selectschool|, not found');
        
        $arrayVars = array();
        $arrayVars['no_school'] = $selectSchoolLabel;
        
        // pass error to javascript.
        $this->objSvars->varsToJs($arrayVars);

        $string = '';
        
        if ($count > 0)
        {
            // set up htmlelements.
            $objInput = new textinput('schools', '', '', '50');
            $schoolInput = $objInput->show();

            $objInput = new textinput('sid', '', 'hidden', '50');
            $schoolInputId = $objInput->show();

            $objButton = new button('select', $selectLabel);
            $selectButton = $objButton->show();

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell($schoolNameLabel, '200px', '', '', '');
            $objTable->addCell($schoolInput . $schoolInputId, '', '', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($selectButton, '', '', '', '');
            $objTable->endRow();
            $schoolTable = $objTable->show();

            $objForm = new form('detail', $this->uri(array(
                'action' => 'show'
            )));
            $objForm->extra = ' enctype="multipart/form-data"';
            $objForm->addToForm($schoolTable);
            $addForm = $objForm->show();

            $this->objIcon->title = $addLabel;
            $this->objIcon->alt = $addLabel;
            $this->objIcon->setIcon('add', 'png');
            $addIcon = $this->objIcon->show();

            $objLink = new link($this->uri(array('action' => 'addoredit', 'mode' => 'add')));
            $objLink->link = $addIcon . '&nbsp;' . $addSchoolLabel;
            $addLink = $objLink->show();

            $objFieldset = new fieldset();
            $objFieldset->legend = '<b>' . $schoolLabel . '</b>';
            $objFieldset->contents = $addForm;
            $schoolFieldset = $objFieldset->show();
            
            $string .= $schoolFieldset . '<br />' . $addLink;
        }
        else
        {
            $error = $this->error($noSchoolsLabel);
            
            $this->objIcon->title = $addLabel;
            $this->objIcon->alt = $addLabel;
            $this->objIcon->setIcon('add', 'png');
            $addIcon = $this->objIcon->show();

            $objLink = new link($this->uri(array('action' => 'addoredit', 'mode' => 'add')));
            $objLink->link = $addIcon . '&nbsp;' . $addSchoolLabel;
            $addLink = $objLink->show();
            
            $string .= $error . '<br />' . $addLink;            
        }
        return $string;
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
     * Method to generate the html for the add and edit school form template
     *
     * @access public
     * @param strint $mode The mode of the action form request
     * @return string $string The html string to be sent to the template
     */
    public function addEditSchool($mode)
    {
        if ($mode == 'add')
        {
            $provinceDropValue = NULL;
            $districtDropValue = NULL;
            $nameInputValue = NULL;
            $addressOneInputValue = NULL;
            $addressTwoInputValue = NULL;
            $addressThreeInputValue = NULL;
            $addressFourInputValue = NULL;
            $emailAddressInputValue = NULL;
            $telephoneNumberInputValue = NULL;
            $faxNumberInputValue = NULL;

            $idInput = NULL;
        }
        else
        {
            $sid = $this->getParam('sid');
            $schoolArray = $this->objDBschools->getSchool($sid);
            $districtArray = $this->objDBdistricts->getDistrict($schoolArray['district_id']);

            $idInputValue = $sid;
            $provinceDropValue = $districtArray['province_id'];
            $districtDropValue = $districtArray['id'];
            $nameInputValue = $schoolArray['name'];
            $addressArray = explode('|', $schoolArray['address']);
            $addressOneInputValue = $addressArray[0];
            $addressTwoInputValue = $addressArray[1];
            $addressThreeInputValue = $addressArray[2];
            $addressFourInputValue = $addressArray[3];
            $emailAddressInputValue = $schoolArray['email_address'];
            $telephoneNumberInputValue = $schoolArray['telephone_number'];
            $faxNumberInputValue = $schoolArray['fax_number'];

            $objInput = new textinput('sid', $idInputValue, 'hidden', '50');
            $idInput = $objInput->show();
        }
        
        $errorArray = $this->getSession('errors');
        
        $provinceDropValue = !empty($errorArray) ? $errorArray['data']['province_id'] : $provinceDropValue;
        $districtDropValue = !empty($errorArray) ? $errorArray['data']['district_id'] : $districtDropValue;
        $nameInputValue = !empty($errorArray) ? $errorArray['data']['name'] : $nameInputValue;
        $addressOneInputValue = !empty($errorArray) ? $errorArray['data']['address_one'] : $addressOneInputValue;
        $addressTwoInputValue = !empty($errorArray) ? $errorArray['data']['address_two'] : $addressTwoInputValue;
        $addressThreeInputValue = !empty($errorArray) ? $errorArray['data']['address_three'] : $addressThreeInputValue;
        $addressFourInputValue = !empty($errorArray) ? $errorArray['data']['address_four'] : $addressFourInputValue;
        $emailAddressInputValue = !empty($errorArray) ? $errorArray['data']['email_address'] : $emailAddressInputValue;
        $telephoneNumberInputValue = !empty($errorArray) ? $errorArray['data']['telephone_number'] : $telephoneNumberInputValue;
        $faxNumberInputValue = !empty($errorArray) ? $errorArray['data']['fax_number'] : $faxNumberInputValue;

        $provinceDropError = (!empty($errorArray) && array_key_exists('province_id', $errorArray['errors'])) ? $errorArray['errors']['province_id'] : NULL;
        $districtDropError = (!empty($errorArray) && array_key_exists('district_id', $errorArray['errors'])) ? $errorArray['errors']['district_id'] : NULL;
        $nameInputError = (!empty($errorArray) && array_key_exists('name', $errorArray['errors'])) ? $errorArray['errors']['name'] : NULL;
        $addressOneInputError = (!empty($errorArray) && array_key_exists('address_one', $errorArray['errors'])) ? $errorArray['errors']['address_one'] : NULL;
        $emailAddressInputError = (!empty($errorArray) && array_key_exists('email_address', $errorArray['errors'])) ? $errorArray['errors']['email_address'] : NULL;
        $telephoneNumberInputError = (!empty($errorArray) && array_key_exists('telephone_number', $errorArray['errors'])) ? $errorArray['errors']['telephone_number'] : NULL;
        
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'WORD: word_save, not found');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'WORD: word_cancel, not found');
        $provinceLabel = $this->objLanguage->languageText('mod_schools_province', 'schools', 'TEXT: mod_schools_province, not found');
        $selectProvinceLabel = $this->objLanguage->languageText('mod_schools_selectprovince', 'schools', 'TEXT:mod_schools_selectprovince, not found');
        $districtLabel = $this->objLanguage->languageText('mod_schools_district', 'schools', 'TEXT: mod_schools_district, not found');
        $selectDistrictLabel = $this->objLanguage->languageText('mod_schools_selectdistrict', 'schools', 'TEXT:mod_schools_selectdistrict, not found');
        $schoolNameLabel = ucfirst($this->objLanguage->code2Txt('mod_schools_schoolname', 'schools', NULL, 'TEXT: mod_schools_schoolname, not found'));
        $addressLabel = $this->objLanguage->languageText('word_address', 'system', 'WORD: word_address, not found');
        $emailAddressLabel = $this->objLanguage->languageText('phrase_emailaddress', 'system', 'phrase_emailaddress, not found');
        $telephoneNumberLabel = $this->objLanguage->languageText('phrase_telephonenumber', 'system', 'phrase_telephonenumber, not found');
        $faxNumberLabel = $this->objLanguage->languageText('phrase_faxnumber', 'system', 'phrase_faxnumber, not found');
        $noDistrictsLabel = $this->objLanguage->languageText('mod_schools_nodistricts', 'schools', 'TEXT: mod_schools_nodistricts, not found');

        $provincesArray = $this->objDBprovinces->getAllProvinces();
        
        // set up htmlelements.
        $objDrop = new dropdown('province_id');
        $objDrop->addOption('', $selectProvinceLabel);
        $objDrop->addFromDB($provincesArray, 'name', 'id');
        $objDrop->setSelected($provinceDropValue);
        $provinceDrop = $objDrop->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($provinceLabel . ': ', '200px', '', '', '', '');
        $objTable->addCell($provinceDropError . $provinceDrop, '', '', '', '', '');
        $objTable->endRow();
        $provinceTable = $objTable->show();

        if (!empty($provinceDropValue))
        {
            $districtArray = $this->objDBdistricts->getDistrictsForProvince($provinceDropValue);

            if (!empty($districtArray))
            {
                // Set up htmlelements.
                $objDrop = new dropdown('district_id');
                $objDrop->addOption('', $selectDistrictLabel);
                $objDrop->addFromDB($districtArray, 'name', 'id');
                $objDrop->setSelected($districtDropValue);
                $districtDrop = $objDrop->show();

                $objTable = new htmltable();
                $objTable->cellpadding = '4';
                $objTable->startRow();
                $objTable->addCell($districtLabel . ': ', '200px', '', '', '', '');
                $objTable->addCell($districtDropError . $districtDrop, '', '', '', '', '');
                $objTable->endRow();
                $districtTable = $objTable->show();

                $string = $districtTable;
            }
            else
            {
                $error = $this->error($noDistrictsLabel);

                $objTable = new htmltable();
                $objTable->cellpadding = '4';
                $objTable->startRow();
                $objTable->addCell($districtLabel . ': ', '200px', '', '', '', '');
                $objTable->addCell($error, '', '', '', '', '');
                $objTable->endRow();
                $districtTable = $objTable->show();            
            }
        }
        else
        {
            $districtTable = NULL;
        }

        $objLayer = new layer();
        $objLayer->id = 'province';
        $objLayer->str = $provinceTable;
        $provinceLayer = $objLayer->show();

        $objLayer = new layer();
        $objLayer->id = 'district';
        $objLayer->str = $districtTable;
        $districtLayer = $objLayer->show();

        $objInput = new textinput('name', $nameInputValue, '', '50');
        $nameInput = $objInput->show();
        
        $objInput = new textinput('address_one', $addressOneInputValue, '', '50');
        $addressOneInput = $objInput->show();

        $objInput = new textinput('address_two', $addressTwoInputValue, '', '50');
        $addressTwoInput = $objInput->show();
        
        $objInput = new textinput('address_three', $addressThreeInputValue, '', '50');
        $addressThreeInput = $objInput->show();
        
        $objInput = new textinput('address_four', $addressFourInputValue, '', '50');
        $addressFourInput = $objInput->show();

        $objInput = new textinput('email_address', $emailAddressInputValue, '', '50');
        $emailAddressInput = $objInput->show();
        
        $objInput = new textinput('telephone_number', $telephoneNumberInputValue, '', '50');
        $telephoneNumberInput = $objInput->show();
        
        $objInput = new textinput('fax_number', $faxNumberInputValue, '', '50');
        $faxNumberInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setToSubmit();
        $saveButton = $objButton->show();

        $objButton = new button('cancel', $cancelLabel);
        $objButton->setToSubmit();
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($schoolNameLabel . ': ', '200px', '', '', '', '');
        $objTable->addCell($nameInputError . $nameInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($addressLabel . ': ', '', '', '', '', '');
        $objTable->addCell($addressOneInputError . $addressOneInput, '', '', '', '', '');
        $objTable->endRow(); 
        $objTable->startRow();
        $objTable->addCell('', '', '', '', '', '');
        $objTable->addCell($addressTwoInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('', '', '', '', '', '');
        $objTable->addCell($addressThreeInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('', '', '', '', '', '');
        $objTable->addCell($addressFourInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($emailAddressLabel . ': ', '', '', '', '', '');
        $objTable->addCell($emailAddressInputError . $emailAddressInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($telephoneNumberLabel . ': ', '', '', '', '', '');
        $objTable->addCell($telephoneNumberInputError . $telephoneNumberInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($faxNumberLabel . ': ', '', '', '', '', '');
        $objTable->addCell($faxNumberInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp;' . $cancelButton, '', '', '', '', 'colspan="2"');
        $objTable->endRow();
        $schoolTable = $objTable->show();
        
        $objLayer = new layer();
        $objLayer->id = 'school';
        $objLayer->str = $schoolTable;
        $schoolLayer = $objLayer->show();

        $objForm = new form('school', $this->uri(array(
            'action' => 'validateschool',
            'mode' => $mode,
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($provinceLayer);
        $objForm->addToForm($districtLayer);
        $objForm->addToForm($schoolLayer);
        $saveForm = $objForm->show();
        
        $string = $saveForm;
        
        return $string;
    }

    /**
     * Method to return the html for an ajax call for districts for a province
     * 
     * @access public
     * @return VOID
     */
    public function ajaxGetDistricts()
    {
        // Set up text elements.
        $noDistrictsLabel = $this->objLanguage->languageText('mod_schools_nodistricts', 'schools', 'TEXT: mod_schools_nodistricts, not found');
        $districtLabel = $this->objLanguage->languageText('mod_schools_district', 'schools', 'TEXT: mod_schools_district, not found');
        $selectDistrictLabel = $this->objLanguage->languageText('mod_schools_selectdistrict', 'schools', 'TEXT: mod_schools_selectdistrct, not found');
        
        // Get parameter.
        $pid = $this->getParam('pid', FALSE);
        
        // Get data
        $districtArray = $this->objDBdistricts->getDistrictsForProvince($pid);

        if (!empty($districtArray))
        {
            // Set up htmlelements.
            $objDrop = new dropdown('district_id');
            $objDrop->addOption('', $selectDistrictLabel);
            $objDrop->addFromDB($districtArray, 'name', 'id');
            $districtDrop = $objDrop->show();
            
            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell($districtLabel . ': ', '200px', '', '', '', '');
            $objTable->addCell($districtDrop, '', '', '', '', '');
            $objTable->endRow();
            $districtTable = $objTable->show();
            
            $string = $districtTable;
        }
        else
        {
            $error = $this->error($noDistrictsLabel);

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell($districtLabel . ': ', '200px', '', '', '', '');
            $objTable->addCell($error, '', '', '', '', '');
            $objTable->endRow();
            $districtTable = $objTable->show();
            
            $string = $districtTable;
        }
        
        echo $string;
        die();
    }

    /**
     *
     * Method to validate the input of the add school form 
     * 
     * @access public
     * @param array $data The data to validate
     * @return boolean TRUE on validation succes | FALSE on failure
     */
    public function validateSchool($data)
    {
        $errors = array();
        foreach ($data as $fieldname => $value)
        {
            if ($fieldname != 'sid' && $fieldname != 'province_id' && $fieldname != 'district_id'
                && $fieldname != 'address_one' && $fieldname != 'address_two' && $fieldname != 'address_three'
                && $fieldname != 'address_four' && $fieldname != 'fax_number')
            {
                if ($value == NULL)
                {
                    $name = explode('_', $fieldname);
                    $name = implode(' ', $name);
                    $array = array('fieldname' => $name);
                    $errorText = $this->objLanguage->code2Txt('mod_schools_error_1', 'schools', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
                elseif ($fieldname == 'email_address')
                {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL) == FALSE)
                    {
                        $errorText = $this->objLanguage->languageText('mod_schools_invalidemail', 'schools', 'TEXT: mod_schools_invalidemail, not found');
                        $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                    }
                }
            }
            elseif ($fieldname == 'address_one')
            {
                if ($data['address_one'] == NULL && $data['address_two'] == NULL
                    && $data['address_three'] == NULL && $data['address_three'] == NULL)
                {
                    $address = $this->objLanguage->languageText('word_address', 'system', 'WORD: word_address, not found');
                    $array = array('fieldname' => $address);
                    $errorText = $this->objLanguage->code2Txt('mod_schools_error_1', 'schools', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'province_id')
            {
                if ($value == NULL)
                {
                    $province = $this->objLanguage->languageText('mod_schools_province', 'schools', 'TEXT: mod_schools_province, not found');
                    $array = array('fieldname' => $province);
                    $errorText = $this->objLanguage->code2Txt('mod_schools_error_2', 'schools', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
                else
                {
                    if ($data['district_id'] == NULL)
                    {
                        $district = $this->objLanguage->languageText('mod_schools_district', 'schools', 'TEXT: mod_schools_province, not found');
                        $array = array('fieldname' => $district);
                        $errorText = $this->objLanguage->code2Txt('mod_schools_error_2', 'schools', $array);
                        $errors['district_id'] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
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
     * Method tho save the school data on adding a school
     * 
     * @access public
     * @param array $data The data array to be saved
     * @return string $sid The id of the school 
     */
    public function insertSchool($data)
    {
        $data['address'] = implode('|', array($data['address_one'], $data['address_two'], $data['address_three'], $data['address_four']));
        $data['created_by'] = $this->objUser->PKId();
        $data['date_created'] = date('Y-m-d H:i:s');
        unset($data['sid']);
        unset($data['address_one']);
        unset($data['address_two']);
        unset($data['address_three']);
        unset($data['address_four']);
        unset($data['province_id']);

        $sid = $this->objDBschools->insertSchool($data);
        
        return $sid;
    }

    /**
     *
     * Method tho save the school data on editing a school
     * 
     * @access public
     * @param array $data The data array to be saved
     * @return string $sid The id of the school 
     */
    public function updateSchool($data)
    {
        $sid = $data['sid'];
        $data['address'] = implode('|', array($data['address_one'], $data['address_two'], $data['address_three'], $data['address_four']));
        $data['modified_by'] = $this->objUser->PKId();
        $data['date_modified'] = date('Y-m-d H:i:s');
        unset($data['sid']);
        unset($data['address_one']);
        unset($data['address_two']);
        unset($data['address_three']);
        unset($data['address_four']);
        unset($data['province_id']);

        $sid = $this->objDBschools->updateSchool($sid, $data);
        
        return $sid;
    }

    /**
     * Method to generate the html for the show schools template
     *
     * @access public
     * @return string $string The html string to be sent to the template
     */
    public function showSchool()
    {
        $schoolLabel = ucfirst($this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'TEXT: mod_schools_school, not found'));               
        $principalLabel = $this->objLanguage->languageText('mod_schools_principal', 'schools', 'TEXT: mod_schools_principal, not found');               
        $contactsLabel = $this->objLanguage->languageText('mod_schools_contacts', 'schools', 'TEXT: mod_schools_contacts, not found');               
        $schoolNameLabel = ucfirst($this->objLanguage->code2Txt('mod_schools_schoolname', 'schools', NULL, 'TEXT: mod_schools_schoolname, not found'));
        $provinceLabel = $this->objLanguage->languageText('mod_schools_province', 'schools', 'TEXT: mod_schools_province, not found');
        $districtLabel = $this->objLanguage->languageText('mod_schools_district', 'schools', 'TEXT: mod_schools_district, not found');
        $addressLabel = $this->objLanguage->languageText('word_address', 'system', 'WORD: word_address, niot found');
        $emailAddressLabel = $this->objLanguage->languageText('phrase_emailaddress', 'system', 'PHRASE: phrase_eamiladdress, not found');
        $telephoneNumberLabel = $this->objLanguage->languageText('phrase_telephonenumber', 'system', 'PHRASE: phrase_telephonenumber, not found');
        $faxNumberLabel = $this->objLanguage->languageText('phrase_faxnumber', 'system', 'PHRASE: phrase_faxnumber');
        $editSchoolLabel = $this->objLanguage->code2Txt('mod_schools_editschool', 'schools', NULL, 'TEXT: mod_schools_editschool, not found');
        $deleteSchoolLabel = $this->objLanguage->code2Txt('mod_schools_deleteschool', 'schools', NULL, 'TEXT: mod_schools_deleteschool, not found');
        $noPrincipalLabel = ucfirst($this->objLanguage->code2Txt('mod_schools_noprincipal', 'schools', NULL, 'TEXT: mod_schools_noprincipal, not found'));
        $addPrincipalLabel = $this->objLanguage->languageText('mod_schools_addprincipal', 'schools', 'TEXT: mod_schools_addprincipal, not found');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'WORD: word_title, not found');
        $firstNameLabel = $this->objLanguage->languageText('phrase_firstname', 'system', 'PHRASE: phrase_firstname, not found');
        $lastNameLabel = $this->objLanguage->languageText('phrase_lastname', 'system', 'PHRASE: phrase_lastname, not found');
        $genderLabel = $this->objLanguage->languageText('word_gender', 'system', 'WORD: word_gender, not found');
        $maleLabel = $this->objLanguage->languageText('word_male', 'system', 'WORD: word_male, not found');
        $femaleLabel = $this->objLanguage->languageText('word_female', 'system', 'WORD: word_female, not found');
        $mobileNumberLabel = $this->objLanguage->languageText('phrase_mobilenumber', 'system', 'PHRASE: phrase_mobilenumber, not found');
        $fullNameLabel = $this->objLanguage->languageText('mod_schools_fullname', 'schools', 'TEXT: mod_schools_fullname, not found');
        $noContactsLabel = ucfirst($this->objLanguage->code2Txt('mod_schools_nocontacts', 'schools', NULL, 'TEXT: mod_schools_nocontacts, not found'));
        $addContactLabel = $this->objLanguage->languageText('mod_schools_addcontact', 'schools', 'TEXT: mod_schools_addcontact, not found');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'WORD: word_add, not found');
        $editLabel = $this->objLanguage->languageText('word_edit', 'system', 'WORD: word_edit, not found');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'WORD: word_delete, not found');
        $editContactLabel = $this->objLanguage->languageText('mod_schools_editcontact', 'schools', 'TEXT: mod_schools_editcontact, not found');
        $deleteContactLabel = $this->objLanguage->languageText('mod_schools_deletecontact', 'schools', 'TEXT: mod_schools_deletecontact, not found');
        $deleteConfirmLabel = $this->objLanguage->languageText('mod_schools_deleteconfirm', 'schools', 'TEXT: mod_schools_deleteconfirm, not found');
        $deletePrincipalLabel = $this->objLanguage->languageText('mod_schools_deleteprincipal', 'schools', 'TEXT: mod_schools_deleteprincipal, not found');
        $countryLabel = $this->objLanguage->languageText('word_country', 'system', 'WORD: word_country, not found');

        $sid = $this->getParam('sid');
        
        $schoolArray = $this->objDBschools->getSchool($sid);
        $array = explode('|', $schoolArray['address']);
        $addressArray = array();
        foreach($array as $line)
        {
            if (!empty($line))
            {
                $addressArray[] = $line;
            }
        }
        $addressString = implode(',<br />', $addressArray);
        $districtArray = $this->objDBdistricts->getDistrict($schoolArray['district_id']);
        $provinceArray = $this->objDBprovinces->getProvince($districtArray['province_id']);
        $principalArray = $this->objUserAdmin->getUserDetails($schoolArray['principal_id']);
        $contactArray = $this->objDBcontacts->getContacts($sid);        
        
        $this->objIcon->setIcon('delete', 'png');
        $this->objIcon->title = $deleteLabel;
        $this->objIcon->alt = $deleteLabel;
        $icon = $this->objIcon->show() . '&nbsp;' . $deleteSchoolLabel;

        $location = $this->uri(array('action' => 'deleteschool', 'sid' => $sid));

        $this->objConfirm->setConfirm($icon, $location, $deleteConfirmLabel);
        $deleteSchoolIcon = $this->objConfirm->show();

        $this->objIcon->title = $editLabel;
        $this->objIcon->alt = $editLabel;
        $this->objIcon->setIcon('edit', 'png');
        $editIcon = $this->objIcon->show();

        $objLink = new link($this->uri(array('action' => 'addoredit', 'sid' => $sid, 'mode' => 'edit')));
        $objLink->link = $editIcon . '&nbsp;' . $editSchoolLabel;
        $editSchoolLink = $objLink->show();
            
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($provinceLabel . ': ', '200px', '', '', 'odd', '', '');
        $objTable->addCell($provinceArray['name'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($districtLabel . ': ', '200px', '', '', 'even', '', '');
        $objTable->addCell($districtArray['name'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($schoolNameLabel . ': ', '200px', '', '', 'odd', '', '');
        $objTable->addCell($schoolArray['name'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($addressLabel . ': ', '', 'top', '', 'even', '', '');
        $objTable->addCell($addressString, '', '', '', 'even', '', '');
        $objTable->endRow(); 
        $objTable->startRow();
        $objTable->addCell($emailAddressLabel . ': ', '', '', '', 'odd', '', '');
        $objTable->addCell($schoolArray['email_address'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($telephoneNumberLabel . ': ', '', '', '', 'even', '', '');
        $objTable->addCell($schoolArray['telephone_number'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($faxNumberLabel . ': ', '', '', '', 'odd', '', '');
        $objTable->addCell($schoolArray['fax_number'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($editSchoolLink . '&nbsp;&nbsp;' . $deleteSchoolIcon, '', '', '', '', 'colspan="2"');
        $objTable->endRow();
        $schoolTable = $objTable->show();

        $schoolTab = array(
            'name' => $schoolLabel,
            'content' => $schoolTable,
        );

        if (!empty($principalArray))
        {
            $country = $this->objCountries->getName($principalArray['country']);
            
            $sex = $principalArray['sex'] == 'M' ? $maleLabel : $femaleLabel;

            $this->objIcon->setIcon('user_minus', 'png');
            $this->objIcon->title = $deletePrincipalLabel;
            $this->objIcon->alt = $deletePrincipalLabel;
            $icon = $this->objIcon->show() . '&nbsp;' . $deletePrincipalLabel;

            $location = $this->uri(array('action' => 'deleteprincipal', 'sid' => $sid));

            $this->objConfirm->setConfirm($icon, $location, $deleteConfirmLabel);
            $deletePrincipalIcon = $this->objConfirm->show();

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell($titleLabel . ': ', '200px', '', '', 'odd', '', '');
            $objTable->addCell($principalArray['title'], '', '', '', 'odd', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($firstNameLabel . ': ', '', '', '', 'even', '', '');
            $objTable->addCell($principalArray['firstname'], '', '', '', 'even', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($lastNameLabel . ': ', '', '', '', 'odd', '', '');
            $objTable->addCell($principalArray['surname'], '', '', '', 'odd', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($genderLabel . ': ', '', '', '', 'even', '', '');
            $objTable->addCell($sex, '', '', '', 'even', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($emailAddressLabel . ': ', '', '', '', 'odd', '', '');
            $objTable->addCell($principalArray['emailaddress'], '', '', '', 'odd', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($mobileNumberLabel . ': ', '', '', '', 'even', '', '');
            $objTable->addCell($principalArray['cellnumber'], '', '', '', 'even', '', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->startRow();
            $objTable->addCell($countryLabel . ': ', '', '', '', 'odd', '', '');
            $objTable->addCell($country, '', '', '', 'odd', '', '');
            $objTable->endRow();
            $objTable->addCell($deletePrincipalIcon, '', '', '', 'even', 'colspan="2"', '');
            $objTable->endRow();
            $principalTable = $objTable->show();
            
            $principalString = $principalTable;
            
        }
        else
        {
            $principalString = $this->error($noPrincipalLabel);

            $this->objIcon->title = $addLabel;
            $this->objIcon->alt = $addLabel;
            $this->objIcon->setIcon('user_plus', 'png');
            $addIcon = $this->objIcon->show();

            $objLink = new link($this->uri(array('action' => 'principals', 'sid' => $sid)));
            $objLink->link = $addIcon . '&nbsp;' . $addPrincipalLabel;
            $addLink = $objLink->show();

            $principalString .= '<br />' . $addLink;       
        }
        
        $principalTab = array(
            'name' => $principalLabel,
            'content' => $principalString,
        );
        
        $contactString = '';
        if (!empty($contactArray))
        {
            foreach ($contactArray as $key => $contact)
            {
                $array = explode('|', $contact['address']);
                $addressArray = array();
                foreach($array as $line)
                {
                    if (!empty($line))
                    {
                        $addressArray[] = $line;
                    }
                }
                $addressString = implode(',<br />', $addressArray);

                $this->objIcon->setIcon('user_minus', 'png');
                $this->objIcon->title = $deleteLabel;
                $this->objIcon->alt = $deleteLabel;
                $icon = $this->objIcon->show() . '&nbsp;' . $deleteContactLabel;

                $location = $this->uri(array('action' => 'deletecontact', 'cid' => $contact['id'], 'sid' => $sid));

                $this->objConfirm->setConfirm($icon, $location, $deleteConfirmLabel);
                $deleteContactIcon = $this->objConfirm->show();

                $this->objIcon->title = $editLabel;
                $this->objIcon->alt = $editLabel;
                $this->objIcon->setIcon('user_pencil', 'png');
                $editIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'contacts', 'cid' => $contact['id'], 'sid' => $sid, 'mode' => 'edit')));
                $objLink->link = $editIcon . '&nbsp;' . $editContactLabel;
                $editContactLink = $objLink->show();
            
                $objTable = new htmltable();
                $objTable->cellpadding = '4';
                $objTable->startRow();
                $objTable->addCell($fullNameLabel . ': ', '200px', '', '', 'odd', '', '');
                $objTable->addCell($contact['name'], '', '', '', 'odd', '', '');
                $objTable->endRow();
                $objTable->startRow();
                $objTable->addCell($addressLabel . ': ', '', 'top', '', 'even', '', '');
                $objTable->addCell($addressString, '', '', '', 'even', '', '');
                $objTable->endRow();
                $objTable->startRow();
                $objTable->addCell($emailAddressLabel . ': ', '', '', '', 'odd', '', '');
                $objTable->addCell($contact['email_address'], '', '', '', 'odd', '', '');
                $objTable->endRow();
                $objTable->startRow();
                $objTable->addCell($telephoneNumberLabel . ': ', '', '', '', 'even', '', '');
                $objTable->addCell($contact['telephone_number'], '', '', '', 'even', '', '');
                $objTable->endRow();
                $objTable->startRow();
                $objTable->addCell($mobileNumberLabel . ': ', '', '', '', 'odd', '', '');
                $objTable->addCell($contact['mobile_number'], '', '', '', 'odd', '', '');
                $objTable->endRow();
                $objTable->startRow();
                $objTable->addCell($faxNumberLabel . ': ', '', '', '', 'even', '', '');
                $objTable->addCell($contact['fax_number'], '', '', '', 'even', '', '');
                $objTable->endRow();
                $contactTable = $objTable->show();

                $objFieldset = new fieldset();
                $objFieldset->legend = '<b>' . $contact['position'] . '</b>';
                $objFieldset->contents = $contactTable . '<br />' . $editContactLink . '&nbsp;&nbsp;' . $deleteContactIcon;
                $contactFieldset = $objFieldset->show();
        
                $contactString .= $contactFieldset . '<br />';
            }

            $this->objIcon->title = $addLabel;
            $this->objIcon->alt = $addLabel;
            $this->objIcon->setIcon('user_plus', 'png');
            $addIcon = $this->objIcon->show();

            $objLink = new link($this->uri(array('action' => 'contacts', 'mode' => 'add', 'sid' => $sid)));
            $objLink->link = $addIcon . '&nbsp;' . $addContactLabel;
            $addLink = $objLink->show();
            
            $contactString .= $addLink;
        }
        else
        {
            $contactString = $this->error($noContactsLabel);

            $this->objIcon->title = $addLabel;
            $this->objIcon->alt = $addLabel;
            $this->objIcon->setIcon('user_plus', 'png');
            $addIcon = $this->objIcon->show();

            $objLink = new link($this->uri(array('action' => 'contacts', 'mode' => 'add', 'sid' => $sid)));
            $objLink->link = $addIcon . '&nbsp;' . $addContactLabel;
            $addLink = $objLink->show();

            $contactString .= '<br />' . $addLink;       
        }

        $contactsTab = array(
            'name' => $contactsLabel,
            'content' => $contactString,
        );

        $this->objTab->init();
        $this->objTab->tabId = 'schools_tab';
        $this->objTab->setSelected = $this->getParam('tab', 0);
        $this->objTab->addTab($schoolTab);
        $this->objTab->addTab($principalTab);
        $this->objTab->addTab($contactsTab);
        $schoolTab = $this->objTab->show();
        
        return $schoolTab;
    }
    
    /**
     *
     * Method to delete a school record
     * 
     * @access public
     * @return boolean TRUE on success | FALSE on failure 
     */
    public function deleteSchool()
    {
        $sid = $this->getParam('sid');
        $this->objDBschools->deleteSchool($sid);
        $this->objDBcontacts->deleteSchoolContacts($sid);
        
        return TRUE;
    }

    /**
     * Method to generate the html for the add and edit school form template
     *
     * @access public
     * @param string $mode The mode of the action form request
     * @return string $string The html string to be sent to the template
     */
    public function contacts($mode)
    {
        if ($mode == 'add')
        {
            $positionInputValue = NULL;
            $nameInputValue = NULL;
            $addressOneInputValue = NULL;
            $addressTwoInputValue = NULL;
            $addressThreeInputValue = NULL;
            $addressFourInputValue = NULL;
            $emailAddressInputValue = NULL;
            $telephoneNumberInputValue = NULL;
            $faxNumberInputValue = NULL;
            $mobileNumberInputValue = NULL;

            $sidInputValue = $this->getParam('sid');
            $cidInput = NULL;
        }
        else
        {
            $sid = $this->getParam('sid');
            $cid = $this->getParam('cid');
            $contactArray = $this->objDBcontacts->getContact($cid);

            $sidInputValue = $sid;
            $cidInputValue = $cid;
            $positionInputValue = $contactArray['position'];
            $nameInputValue = $contactArray['name'];
            $addressArray = explode('|', $contactArray['address']);
            $addressOneInputValue = $addressArray[0];
            $addressTwoInputValue = $addressArray[1];
            $addressThreeInputValue = $addressArray[2];
            $addressFourInputValue = $addressArray[3];
            $emailAddressInputValue = $contactArray['email_address'];
            $telephoneNumberInputValue = $contactArray['telephone_number'];
            $faxNumberInputValue = $contactArray['fax_number'];
            $mobileNumberInputValue = $contactArray['mobile_number'];

            $objInput = new textinput('cid', $cidInputValue, 'hidden', '50');
            $cidInput = $objInput->show();
        }
        
        $errorArray = $this->getSession('errors');
        
        $positionInputValue = !empty($errorArray) ? $errorArray['data']['position'] : $positionInputValue;
        $nameInputValue = !empty($errorArray) ? $errorArray['data']['name'] : $nameInputValue;
        $addressOneInputValue = !empty($errorArray) ? $errorArray['data']['address_one'] : $addressOneInputValue;
        $addressTwoInputValue = !empty($errorArray) ? $errorArray['data']['address_two'] : $addressTwoInputValue;
        $addressThreeInputValue = !empty($errorArray) ? $errorArray['data']['address_three'] : $addressThreeInputValue;
        $addressFourInputValue = !empty($errorArray) ? $errorArray['data']['address_four'] : $addressFourInputValue;
        $emailAddressInputValue = !empty($errorArray) ? $errorArray['data']['email_address'] : $emailAddressInputValue;
        $telephoneNumberInputValue = !empty($errorArray) ? $errorArray['data']['telephone_number'] : $telephoneNumberInputValue;
        $faxNumberInputValue = !empty($errorArray) ? $errorArray['data']['fax_number'] : $faxNumberInputValue;
        $mobileNumberInputValue = !empty($errorArray) ? $errorArray['data']['mobile_number'] : $mobileNumberInputValue;

        $positionInputError = (!empty($errorArray) && array_key_exists('name', $errorArray['errors'])) ? $errorArray['errors']['position'] : NULL;
        $nameInputError = (!empty($errorArray) && array_key_exists('name', $errorArray['errors'])) ? $errorArray['errors']['name'] : NULL;
        $addressOneInputError = (!empty($errorArray) && array_key_exists('address_one', $errorArray['errors'])) ? $errorArray['errors']['address_one'] : NULL;
        $emailAddressInputError = (!empty($errorArray) && array_key_exists('email_address', $errorArray['errors'])) ? $errorArray['errors']['email_address'] : NULL;
        $telephoneNumberInputError = (!empty($errorArray) && array_key_exists('telephone_number', $errorArray['errors'])) ? $errorArray['errors']['telephone_number'] : NULL;
        
        $positionLabel = $this->objLanguage->languageText('mod_schools_position', 'schools', 'TEXT: mod_schools_position, not found');
        $fullNameLabel = $this->objLanguage->languageText('mod_schools_fullname', 'schools', 'TEXT: mod_schools_fullname, not found');
        $addressLabel = $this->objLanguage->languageText('word_address', 'system', 'WORD: word_address, niot found');
        $emailAddressLabel = $this->objLanguage->languageText('phrase_emailaddress', 'system', 'PHRASE: phrase_eamiladdress, not found');
        $telephoneNumberLabel = $this->objLanguage->languageText('phrase_telephonenumber', 'system', 'PHRASE: phrase_telephonenumber, not found');
        $faxNumberLabel = $this->objLanguage->languageText('phrase_faxnumber', 'system', 'PHRASE: phrase_faxnumber');
        $mobileNumberLabel = $this->objLanguage->languageText('phrase_mobilenumber', 'system', 'PHRASE: phrase_mobilenumber, not found');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'WORD: word_save, not found');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'WORD: word_cancel, not found');

        $objInput = new textinput('position', $positionInputValue, '', '50');
        $positionInput = $objInput->show();

        $objInput = new textinput('name', $nameInputValue, '', '50');
        $nameInput = $objInput->show();

        $objInput = new textinput('address_one', $addressOneInputValue, '', '50');
        $addressOneInput = $objInput->show();

        $objInput = new textinput('address_two', $addressTwoInputValue, '', '50');
        $addressTwoInput = $objInput->show();

        $objInput = new textinput('address_three', $addressThreeInputValue, '', '50');
        $addressThreeInput = $objInput->show();

        $objInput = new textinput('address_four', $addressFourInputValue, '', '50');
        $addressFourInput = $objInput->show();

        $objInput = new textinput('email_address', $emailAddressInputValue, '', '50');
        $emailAddressInput = $objInput->show();

        $objInput = new textinput('telephone_number', $telephoneNumberInputValue, '', '50');
        $telephoneNumberInput = $objInput->show();

        $objInput = new textinput('mobile_number', $mobileNumberInputValue, '', '50');
        $mobileNumberInput = $objInput->show();

        $objInput = new textinput('fax_number', $faxNumberInputValue, '', '50');
        $faxNumberInput = $objInput->show();

        $objInput = new textinput('sid', $sidInputValue, 'hidden', '50');
        $sidInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_contact');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_contact');
        $objButton->setToSubmit();
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($positionLabel . ': ', '200px', '', '', '', '');
        $objTable->addCell($positionInputError . $positionInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($fullNameLabel . ': ', '', '', '', '', '');
        $objTable->addCell($nameInputError . $nameInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($addressLabel . ': ', '', '', '', '', '');
        $objTable->addCell($addressOneInputError . $addressOneInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('', '', '', '', '', '');
        $objTable->addCell($addressTwoInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('', '', '', '', '', '');
        $objTable->addCell($addressThreeInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('', '', '', '', '', '');
        $objTable->addCell($addressFourInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($emailAddressLabel . ': ', '', '', '', '', '');
        $objTable->addCell($emailAddressInputError . $emailAddressInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($telephoneNumberLabel . ': ', '', '', '', '', '');
        $objTable->addCell($telephoneNumberInputError . $telephoneNumberInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($mobileNumberLabel . ': ', '', '', '', '', '');
        $objTable->addCell($mobileNumberInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($faxNumberLabel . ': ', '', '', '', '', '');
        $objTable->addCell($faxNumberInput, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($cidInput . $sidInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', '', '');
        $objTable->endRow();
        $contactTable = $objTable->show();

        $objForm = new form('contact', $this->uri(array(
            'action' => 'validatecontact',
            'mode' => $mode,
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($contactTable);
        $addForm = $objForm->show();
        
        return $addForm;
    }

    /**
     *
     * Method to validate the contact data
     * 
     * @access public
     * @param array $data The data to validate
     * @return  
     */
    public function validateContact($data)
    {
        $errors = array();
        foreach ($data as $fieldname => $value)
        {
            if ($fieldname != 'cid' && $fieldname != 'sid' && $fieldname != 'address_one'
                && $fieldname != 'address_two' && $fieldname != 'address_three'
                && $fieldname != 'address_four' && $fieldname != 'fax_number' 
                && $fieldname != 'telephone_number' && $fieldname != 'mobile_number')
            {
                if ($value == NULL)
                {
                    $name = explode('_', $fieldname);
                    $name = implode(' ', $name);
                    $array = array('fieldname' => $name);
                    $errorText = $this->objLanguage->code2Txt('mod_schools_error_1', 'schools', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
                elseif ($fieldname == 'email_address')
                {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL) == FALSE)
                    {
                        $errorText = $this->objLanguage->languageText('mod_schools_invalidemail', 'schools', 'TEXT: mod_schools_invalidemail, not found');
                        $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                    }
                }
            }
            elseif ($fieldname == 'address_one')
            {
                if ($data['address_one'] == NULL && $data['address_two'] == NULL
                    && $data['address_three'] == NULL && $data['address_three'] == NULL)
                {
                    $address = $this->objLanguage->languageText('word_address', 'system', 'WORD: word_address, not found');
                    $array = array('fieldname' => $address);
                    $errorText = $this->objLanguage->code2Txt('mod_schools_error_1', 'schools', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'telephone_number')
            {
                if ($data['telephone_number'] == NULL && $data['mobile_number'] == NULL)
                {
                    $errorText = $this->objLanguage->languageText('mod_schools_contactnumbers', 'schools', 'TEXT: mod_schools_contactnumbers, not found');
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'mobile_number')
            {
                if ($data['telephone_number'] == NULL && $data['mobile_number'] == NULL)
                {
                    $errorText = $this->objLanguage->languageText('mod_schools_contactnumbers', 'schools', 'TEXT: mod_schools_contactnumbers, not found');
                    $errors['contact_telephone_number'] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
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
     * Method tho save the contact data on adding a contact
     * 
     * @access public
     * @param array $data The data array to be saved
     * @return string $sid The id of the contact
     */
    public function insertContact($data)
    {
        $data['address'] = implode('|', array($data['address_one'], $data['address_two'], $data['address_three'], $data['address_four']));
        $data['created_by'] = $this->objUser->PKId();
        $data['date_created'] = date('Y-m-d H:i:s');
        $data['school_id'] = $data['sid'];
        unset($data['cid']);
        unset($data['sid']);
        unset($data['address_one']);
        unset($data['address_two']);
        unset($data['address_three']);
        unset($data['address_four']);

        $cid = $this->objDBcontacts->insertContact($data);
        
        return $cid;
    }
    
    /**
     *
     * Method to delete a contact record
     * 
     * @access public
     * @return boolean TRUE on success | FALSE on failure 
     */
    public function deleteContact()
    {
        $cid = $this->getParam('cid');
        $this->objDBcontacts->deleteContact($cid);
        
        return TRUE;
    }
    
    /**
     *
     * Method tho save the contact data on editing a contact
     * 
     * @access public
     * @param array $data The data array to be saved
     * @return string $sid The id of the contact
     */
    public function updateContact($data)
    {
        $cid = $data['cid'];
        $data['address'] = implode('|', array($data['address_one'], $data['address_two'], $data['address_three'], $data['address_four']));
        $data['modified_by'] = $this->objUser->PKId();
        $data['date_modified'] = date('Y-m-d H:i:s');
        $data['school_id'] = $data['sid'];
        unset($data['sid']);
        unset($data['cid']);
        unset($data['address_one']);
        unset($data['address_two']);
        unset($data['address_three']);
        unset($data['address_four']);

        $cid = $this->objDBcontacts->updateContact($cid, $data);
        
        return $cid;
    }
    
    /**
     *
     * Method to show the left block to manage schools components
     * 
     * @access public 
     * @return string The string to display in the block 
     */
    public function showManage()
    {
        $manageSchools = $this->objLanguage->code2Txt('mod_schools_manageschools', 'schools', NULL, 'TEXT: mod_schools_manageschools, not found');
        $manageProvinces = $this->objLanguage->languageText('mod_schools_manageprovinces', 'schools', 'TEXT: mod_schools_manageprovinces, not found');
        $manageDistricts = $this->objLanguage->languageText('mod_schools_managedistricts', 'schools', 'TEXT: mod_schools_managedistricts, not found');
        
        $this->objIcon->title = $manageSchools;
        $this->objIcon->alt = $manageSchools;
        $this->objIcon->setIcon('house_two', 'png');
        $manageIcon = $this->objIcon->show();
        
        $objLink = new link($this->uri(array('action' => 'manage', 'type' => 's')));
        $objLink->link = $manageIcon . '&nbsp' . $manageSchools;
        $schoolsLink = $objLink->show();

        $this->objIcon->title = $manageProvinces;
        $this->objIcon->alt = $manageProvinces;
        $this->objIcon->setIcon('world', 'png');
        $manageIcon = $this->objIcon->show();
        
        $objLink = new link($this->uri(array('action' => 'manage', 'type' => 'p')));
        $objLink->link = $manageIcon . '&nbsp' . $manageProvinces;
        $provinceLink = $objLink->show();

        $this->objIcon->title = $manageDistricts;
        $this->objIcon->alt = $manageDistricts;
        $this->objIcon->setIcon('map', 'png');
        $manageIcon = $this->objIcon->show();
        
        $objLink = new link($this->uri(array('action' => 'manage', 'type' => 'd')));
        $objLink->link = $manageIcon . '&nbsp' . $manageDistricts;
        $districtLink = $objLink->show();

        $objLayer = new layer();
        $objLayer->id = 'manage';
        $objLayer->str = $schoolsLink . '<br /><br />' . $provinceLink . '<br /><br />' . $districtLink;
        $manageLayer = $objLayer->show();

        $objFieldset = new fieldset();
        $objFieldset->contents = $manageLayer;
        
        return $objFieldset->show();
    }

    /**
     *
     * Method to show the manage schools districts template
     * 
     * @access puclic
     * @return string The template string
     */
    public function manageDistricts()
    {
        // set up language elements.
        $provinceLabel = $this->objLanguage->languageText('mod_schools_province', 'schools', 'TEXT: mod_schools_province, not found');
        $selectProvinceLabel = $this->objLanguage->languageText('mod_schools_selectprovince', 'schools', 'TEXT: mod_schools_selectprovince, not found');
        $districtNameLabel = $this->objLanguage->languageText('mod_schools_districtname', 'schools', 'TEXT: mod_schools_districtname, not found');
        
        $noName = $this->objLanguage->code2Txt('mod_schools_error_1', 'schools', array('fieldname' => $districtNameLabel));
        
        $arrayVars = array();
        $arrayVars['no_district'] = $noName;

        // pass error to javascript.
        $this->objSvars->varsToJs($arrayVars);

        $pid = $this->getParam('pid');
        
        // get data from the database.
        $provincesArray = $this->objDBprovinces->getAllProvinces();        

        // set up htmlelements.
        $objDrop = new dropdown('province');
        $objDrop->addOption('', $selectProvinceLabel);
        $objDrop->addFromDB($provincesArray, 'name', 'id');
        $objDrop->setSelected($pid);
        $provinceDrop = $objDrop->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $provinceLabel . '</b>';
        $objFieldset->contents = $provinceDrop;
        $provinceFieldset = $objFieldset->show();

        $str = '';
        if ($pid != NULL)
        {
            $str = $this->ajaxManageDistricts(FALSE);
        }
 
        $objLayer = new layer();
        $objLayer->id = 'district';
        $objLayer->str = $str;
        $districtLayer = $objLayer->show();

        $objLayer = new layer();
        $objLayer->id = 'manage';
        $objLayer->str = $provinceFieldset . '<br />' . $districtLayer ;
        $manageLayer = $objLayer->show();
        
        return $manageLayer;
    }

    /**
     *
     * Method to display the ajax call on province change for managing districts
     * 
     * @access public
     * @param boolean $isAjax TRUE if this is called via ajax | FALSE if not
     * @return void 
     */
    public function ajaxManageDistricts($isAjax = TRUE)
    {
        $districtNameLabel = $this->objLanguage->languageText('mod_schools_districtname', 'schools', 'TEXT: mod_schools_districtname, not found');
        $noDistrictsLabel = $this->objLanguage->languageText('mod_schools_nodistricts', 'schools', 'TEXT: mod_schools_nodistricts, not found');
        $addDistrictLabel = $this->objLanguage->languageText('mod_schools_adddistrict', 'schools', 'TEXT: mod_schools_adddistrict, not found');
        $editDistrictLabel = $this->objLanguage->languageText('mod_schools_editdistrict', 'schools', 'TEXT: mod_schools_editdistrict, not found');
        $deleteDistrictLabel = $this->objLanguage->languageText('mod_schools_deletedistrict', 'schools', 'TEXT: mod_schools_deletedistrict, not found');
        $deleteConfirmLabel = $this->objLanguage->languageText('mod_schools_deleteconfirm', 'schools', 'TEXT: mod_schools_deleteconfirm, not found');
        $districtsLabel = $this->objLanguage->languageText('mod_schools_districts', 'schools', 'TEXT: mod_schools_districts, not found');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'WORD: word_add, not found');
        $editLabel = $this->objLanguage->languageText('word_edit', 'system', 'WORD: word_edit, not found');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'WORD: word_delete, not found');        

        $pid = $this->getParam('pid');
        
        $districtArray = $this->objDBdistricts->getDistrictsForProvince($pid);
        
        $this->objIcon->title = $addLabel;
        $this->objIcon->alt = $addLabel;
        $this->objIcon->setIcon('map_add', 'png');
        $addIcon = $this->objIcon->show();

        $addLink = '<a href="#" id="adddistrict">' . $addIcon . '&nbsp' . $addDistrictLabel . '</a>';
            
        $objLayer = new layer();
        $objLayer->id = 'adddistrictdiv';
        $addLayer = $objLayer->show();

        if (empty($districtArray))
        {
            $str = $this->error($noDistrictsLabel);
            $str .= '<br />' . $addLink . '<br />';
        }
        else
        {
            $str = $addLink . '<br />';
            
            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . $districtNameLabel . '</b>', '', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $editLabel . '</b>', '', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            $i = 0;
            foreach ($districtArray as $key => $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                $this->objIcon->setIcon('map_delete', 'png');
                $this->objIcon->title = $deleteDistrictLabel;
                $this->objIcon->alt = $deleteDistrictLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletedistrict', 'id' => $value['id'], 'pid' => $value['province_id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteConfirmLabel);
                $deleteIcon = $this->objConfirm->show();

                $this->objIcon->title = $editDistrictLabel;
                $this->objIcon->alt = $editDistrictLabel;
                $this->objIcon->setIcon('map_edit', 'png');
                $editIcon = '<a href="#" id="editdistrict" class="' . $value['id'] . '">' . $this->objIcon->show() . '</a>';

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($editIcon, '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $districtTable = $objTable->show();   
            $str .= $districtTable;
        }
                
        $objLayer = new layer();
        $objLayer->id = 'districtdiv';
        $objLayer->str = $str;
        $districtLayer = $objLayer->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $districtsLabel . '</b>';
        $objFieldset->contents = $addLayer . $districtLayer;
        $districtFieldset = $objFieldset->show();
        
        if ($isAjax)
        {           
            echo $districtFieldset;
            die();
        }
        else
        {
            return $districtFieldset;
        }
    }    

    /**
     *
     * Method to display the add district form
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxAddEditDistrict()
    {
        $id = $this->getParam('id');
        $pid = $this->getParam('pid');
        $districtNameValue = NULL;
        if (!empty($id))
        {
            $districtArray = $this->objDBdistricts->getDistrict($id);
            $districtNameValue = $districtArray['name'];
            $pid = $districtArray['province_id'];
        }
        
        $districtNameLabel = $this->objLanguage->languageText('mod_schools_districtname', 'schools', 'TEXT: mod_schools_districtname, not found');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'WORD: word_save, not found');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'WORD: word_cancel, not found');

        $objInput = new textinput('name', $districtNameValue, '', '50');
        $districtInput = $objInput->show();
  
        $objInput = new textinput('province_id', $pid, 'hidden', '50');
        $provinceInput = $objInput->show();

        $objInput = new textinput('id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_district');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_district');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($districtNameLabel, '200px', '', '', '');
        $objTable->addCell($districtInput, '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $provinceInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'colspan="7"');
        $objTable->endRow();
        $addTable = $objTable->show();

        $objForm = new form('district', $this->uri(array(
            'action' => 'district'
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($addTable);
        $addForm = $objForm->show();
        
        echo $addForm;
        die();
    }

    /**
     *
     * Method to display the ajax call on province change for managing districts
     * 
     * @access public
     * @param boolean $isAjax TRUE if this is called via ajax | FALSE if not
     * @return void 
     */
    public function manageProvinces()
    {
        $provinceNameLabel = $this->objLanguage->languageText('mod_schools_provincename', 'schools', 'TEXT: mod_schools_provincename, not found');
        $noProvincesLabel = $this->objLanguage->languageText('mod_schools_noprovinces', 'schools', 'TEXT: mod_schools_noprovinces, not found');
        $addProvinceLabel = $this->objLanguage->languageText('mod_schools_addprovince', 'schools', 'TEXT: mod_schools_addprovince, not found');
        $editProvinceLabel = $this->objLanguage->languageText('mod_schools_editprovince', 'schools', 'TEXT: mod_schools_editprovince, not found');
        $deleteProvinceLabel = $this->objLanguage->languageText('mod_schools_deleteprovince', 'schools', 'TEXT: mod_schools_deleteprovince, not found');
        $deleteConfirmLabel = $this->objLanguage->languageText('mod_schools_deleteconfirm', 'schools', 'TEXT: mod_schools_deleteconfirm, not found');
        $provincesLabel = $this->objLanguage->languageText('mod_schools_provinces', 'schools', 'TEXT: mod_schools_provinces, not found');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'WORD: word_add, not found');
        $editLabel = $this->objLanguage->languageText('word_edit', 'system', 'WORD: word_edit, not found');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'WORD: word_delete, not found');        

        $noName = $this->objLanguage->code2Txt('mod_schools_error_1', 'schools', array('fieldname' => $provinceNameLabel));

        $arrayVars = array();
        $arrayVars['no_province'] = $noName;

        // pass error to javascript.
        $this->objSvars->varsToJs($arrayVars);

        $provinceArray = $this->objDBprovinces->getAllProvinces();

        $this->objIcon->title = $addLabel;
        $this->objIcon->alt = $addLabel;
        $this->objIcon->setIcon('world_add', 'png');
        $addIcon = $this->objIcon->show();

        $addLink = '<a href="#" id="addprovince">' . $addIcon . '&nbsp' . $addProvinceLabel . '</a>';
            
        $objLayer = new layer();
        $objLayer->id = 'addprovincediv';
        $addLayer = $objLayer->show();

        if (empty($provinceArray))
        {
            $str = $this->error($noProvincesLabel);
            $str .= '<br />' . $addLink . '<br />';
        }
        else
        {
            $str = $addLink . '<br />';
            
            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . $provinceNameLabel . '</b>', '', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $editLabel . '</b>', '', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($provinceArray as $key => $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                $this->objIcon->setIcon('world_delete', 'png');
                $this->objIcon->title = $deleteProvinceLabel;
                $this->objIcon->alt = $deleteProvinceLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deleteprovince', 'id' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteConfirmLabel);
                $deleteIcon = $this->objConfirm->show();

                $this->objIcon->title = $editProvinceLabel;
                $this->objIcon->alt = $editProvinceLabel;
                $this->objIcon->setIcon('world_edit', 'png');
                $editIcon = '<a href="#" id="editprovince" class="' . $value['id'] . '">' . $this->objIcon->show() . '</a>';

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($editIcon, '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $provinceTable = $objTable->show();   
            $str .= $provinceTable;
        }
                
        $objLayer = new layer();
        $objLayer->id = 'provincediv';
        $objLayer->str = $str;
        $provinceLayer = $objLayer->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $provincesLabel . '</b>';
        $objFieldset->contents = $addLayer . $provinceLayer;
        $provinceFieldset = $objFieldset->show();
        
        return $provinceFieldset;
    }
    
    /**
     *
     * Method to display the add province form
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxAddEditProvince()
    {
        $id = $this->getParam('id');
        $provinceNameValue = NULL;
        if (!empty($id))
        {
            $provinceArray = $this->objDBprovinces->getProvince($id);
            $provinceNameValue = $provinceArray['name'];
        }
        
        $provinceNameLabel = $this->objLanguage->languageText('mod_schools_provincename', 'schools', 'TEXT: mod_schools_provincename, not found');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'WORD: word_save, not found');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'WORD: word_cancel, not found');

        $objInput = new textinput('name', $provinceNameValue, '', '50');
        $provinceInput = $objInput->show();
  
        $objInput = new textinput('id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_province');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_province');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($provinceNameLabel, '200px', '', '', '');
        $objTable->addCell($provinceInput, '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'colspan="7"');
        $objTable->endRow();
        $addTable = $objTable->show();

        $objForm = new form('province', $this->uri(array(
            'action' => 'province'
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($addTable);
        $addForm = $objForm->show();
        
        echo $addForm;
        die();
    }
    
    /**
     *
     * Method to manage principals
     * 
     * @access public
     * @return string The template string
     */
    public function managePrincipals()
    {
        $this->appendArrayVar('headerParams',
            $this->getJavaScriptFile('plugins/ui/js/jquery-ui-1.8.7.custom.min.js',
            'jquery'));
        $cssUri = $this->getResourceUri('plugins/ui/css/ui-lightness/jquery-ui-1.8.7.custom.css',
            'jquery');
        $this->appendArrayVar('headerParams', 
            "<link href='$cssUri' rel='stylesheet' type='text/css'/>");

        $errorArray = $this->getSession('errors');
        
        $sid = $this->getParam('sid');

        $titleValue = !empty($errorArray) ? $errorArray['data']['title'] : NULL;
        $firstNameValue = !empty($errorArray) ? $errorArray['data']['first_name'] : NULL;
        $lastNameValue = !empty($errorArray) ? $errorArray['data']['last_name'] : NULL;
        $genderValue = !empty($errorArray) ? $errorArray['data']['gender'] : NULL;
        $mobileNumberValue = !empty($errorArray) ? $errorArray['data']['mobile_number'] : NULL;
        $userEmailAddressValue = !empty($errorArray) ? $errorArray['data']['email_address'] : NULL;
        $usernameValue = !empty($errorArray) ? $errorArray['data']['username'] : NULL;
        $passwordValue = !empty($errorArray) ? $errorArray['data']['password'] : NULL;
        $confirmPasswordValue = !empty($errorArray) ? $errorArray['data']['confirm_password'] : NULL;
        $countryValue = !empty($errorArray) ? $errorArray['data']['country'] : NULL;

        $titleError = (!empty($errorArray) && array_key_exists('title', $errorArray['errors'])) ? $errorArray['errors']['title'] : NULL;
        $firstNameError = (!empty($errorArray) && array_key_exists('first_name', $errorArray['errors'])) ? $errorArray['errors']['first_name'] : NULL;
        $lastNameError = (!empty($errorArray) && array_key_exists('last_name', $errorArray['errors'])) ? $errorArray['errors']['last_name'] : NULL;
        $genderError = (!empty($errorArray) && array_key_exists('gender', $errorArray['errors'])) ? $errorArray['errors']['gender'] : NULL;
        $userEmailAddressError = (!empty($errorArray) && array_key_exists('email_address', $errorArray['errors'])) ? $errorArray['errors']['email_address'] : NULL;
        $usernameError = (!empty($errorArray) && array_key_exists('username', $errorArray['errors'])) ? $errorArray['errors']['username'] : NULL;
        $passwordError = (!empty($errorArray) && array_key_exists('password', $errorArray['errors'])) ? $errorArray['errors']['password'] : NULL;
        $countryError = (!empty($errorArray) && array_key_exists('country', $errorArray['errors'])) ? $errorArray['errors']['country'] : NULL;
        
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'WORD: word_title, not found');
        $selectTitleLabel = $this->objLanguage->languageText('phrase_selecttitle', 'system', 'PHRASE: phrase_selecttitle, not found');
        $mr = $this->objLanguage->languageText('title_mr', 'system', 'TITLE: title_mr, not found');
        $miss = $this->objLanguage->languageText('title_miss', 'system', 'TITLE: title_miss, not found');
        $mrs = $this->objLanguage->languageText('title_mrs', 'system', 'TITLE: title_mrs, not found');
        $ms = $this->objLanguage->languageText('title_ms', 'system', 'TITLE: title_ms, not found');
        $dr = $this->objLanguage->languageText('title_dr', 'system', 'TITLE: title_dr, not found');
        $rev = $this->objLanguage->languageText('title_rev', 'system', 'TITLE: title_rev, not found');
        $prof = $this->objLanguage->languageText('title_prof', 'system', 'TITLE: title_prof, not found');
        $assocprof = $this->objLanguage->languageText('title_assocprof', 'system', 'TITLE: title_assocprof, not found');
        $firstNameLabel = $this->objLanguage->languageText('phrase_firstname', 'system', 'PHRASE: phrase_firstname, not found');
        $lastNameLabel = $this->objLanguage->languageText('phrase_lastname', 'system', 'PHRASE: phrase_lastname, not found');
        $genderLabel = $this->objLanguage->languageText('word_gender', 'system', 'WORD: word_gender, not found');
        $maleLabel = $this->objLanguage->languageText('word_male', 'system', 'WORD: word_male, not found');
        $femaleLabel = $this->objLanguage->languageText('word_female', 'system', 'WORD: word_female, not found');
        $usernameLabel = $this->objLanguage->languageText('word_username', 'system', 'WORD: word_username, not found');
        $passwordLabel = $this->objLanguage->languageText('word_password', 'system', 'WORD: word_password, not found');
        $confirmPasswordLabel = $this->objLanguage->languageText('phrase_confirmpassword', 'system', 'PHRASE: phrase_confirmpassword, not found');
        $emailAddressLabel = $this->objLanguage->languageText('phrase_emailaddress', 'system', 'PHRASE: phrase_emailaddress, not found');
        $mobileNumberLabel = $this->objLanguage->languageText('phrase_mobilenumber', 'system', 'PHRASE: phrase_mobilenumber, not found');
        $countryLabel = $this->objLanguage->languageText('word_country', 'system', 'WORD: word_country, not found');
        $passwordNotAlike = $this->objLanguage->languageText('mod_schools_passwordsnotalike', 'schools', 'TEXT: mod_schools_passwordsnotalike, not found');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'WORD: word_save, not found');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'WORD: word_cancel, not found');
        $addUserLabel = $this->objLanguage->languageText('mod_schools_addprincipaluser', 'schools', 'TEXTR: mod_schools_addprincipaluser, not found');
        $findPrincipalLabel = $this->objLanguage->languageText('mod_schools_searchprincipal', 'schools', 'TEXTR: mod_schools_searchprincipal, not found');
        $searchLabel = $this->objLanguage->languageText('word_search', 'system', 'WORD: word_search, not found');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'WORD: word_add, not found');
        $selectPrincipalLabel = $this->objLanguage->languageText('mod_schools_selectprincipal', 'schools', 'TEXT: mod_schools_selectprincipal, not found');
        $firstNameLabel = $this->objLanguage->languageText('phrase_firstname', 'system', 'PHRASE: phrase_firstname, not found');
        $lastNameLabel = $this->objLanguage->languageText('phrase_lastname', 'system', 'PHRASE: phrase_lastname, not found');
        $fieldLabel = $this->objLanguage->languageText('word_field', 'system', 'WORD: word_field, not found');
        
        $arrayVars = array();
        $arrayVars['password_not_alike'] = $passwordNotAlike;
        $arrayVars['select_principal'] = $selectPrincipalLabel;
       
        // pass password error to javascript.
        $this->objSvars->varsToJs($arrayVars);
        
        $objRadio = new radio('field');
        $objRadio->addOption('firstname', $firstNameLabel);
        $objRadio->addOption('surname', $lastNameLabel);
        $objRadio->setSelected('surname');
        $fieldRadio = $objRadio->show();        
        
        $objInput = new textinput('principal', '', '', '50');
        $searchInput = $objInput->show();

        $objInput = new textinput('id', '', 'hidden', '50');
        $idInput = $objInput->show();
        
        $objInput = new textinput('sid', $sid, 'hidden', '50');
        $sidInput = $objInput->show();
        
        $objButton = new button('add_save', $addLabel);
        $addButton = $objButton->show();
        
        $objButton = new button('add_cancel', $cancelLabel);
        $objButton->setToSubmit();
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($fieldLabel . ': ', '200px', '', '', 'odd', '', '');
        $objTable->addCell($fieldRadio, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($searchLabel . ': ', '200px', '', '', 'even', '', '');
        $objTable->addCell($searchInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($sidInput . $idInput . $addButton . '&nbsp;' . $cancelButton, '', '', '', 'odd', 'colspan="2"', '');
        $objTable->endRow();
        $findTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $findPrincipalLabel. '</b>';
        $objFieldset->contents = $findTable;
        $findFieldset = $objFieldset->show();
        
        $this->objIcon->title = $addUserLabel;
        $this->objIcon->alt = $addUserLabel;
        $this->objIcon->setIcon('user_plus', 'png');
        $addIcon = $this->objIcon->show();

        $addLink = '<a href="#" id="addprincipal">' . $addIcon . '&nbsp' . $addUserLabel . '</a>';
        
        $objForm = new form('findprincipal', $this->uri(array(
            'action' => 'addprincipal'
        )));
        if (empty($errorArray))
        {
            $objForm->extra = ' enctype="multipart/form-data"';
        }
        else
        {
            $objForm->extra = ' enctype="multipart/form-data"  style="display: none;" ';
        }
        $objForm->addToForm($findFieldset);
        $objForm->addToForm('<br />' . $addLink);
        $findForm = $objForm->show();

        $string = $findForm;

        // set up html elements
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
        $objDrop->setSelected($titleValue);
        $titleDrop = $objDrop->show();

        $objInput = new textinput('first_name', $firstNameValue, '', '50');
        $firstNameInput = $objInput->show();
        
        $objInput = new textinput('last_name', $lastNameValue, '', '50');
        $lastNameInput = $objInput->show();
        
        $countryDrop = $this->objCountries->countryAlpha($countryValue);

        $objRadio = new radio('gender');
        $objRadio->addOption('M', $maleLabel);
        $objRadio->addOption('F', $femaleLabel);
        $objRadio->setSelected($genderValue);
        $genderRadio = $objRadio->show();
        
        $objInput = new textinput('mobile_number', $mobileNumberValue, '', '50');
        $mobileNumberInput = $objInput->show();

        $objInput = new textinput('email_address', $userEmailAddressValue, '', '50');
        $userEmailAddressInput = $objInput->show();

        $objInput = new textinput('username', $usernameValue, '', '50');
        $usernameInput = $objInput->show();
        
        $objInput = new textinput('password', $passwordValue, 'password', '50');
        $passwordInput = $objInput->show();
        
        $objInput = new textinput('confirm_password', $confirmPasswordValue, 'password', '50');
        $confirmPasswordInput = $objInput->show();
        
        $objButton = new button('new_save', $saveLabel);
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('new_cancel', $cancelLabel);
        $objButton->setToSubmit();
        $cancelButton = $objButton->show();

        $objLayer = new layer();
        $objLayer->id = 'username';
        $usernameLayer = $objLayer->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($countryLabel . ': ', '200px', '', '', 'even', '', '');
        $objTable->addCell($countryError . $countryDrop, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($titleLabel . ': ', '200px', '', '', 'odd', '', '');
        $objTable->addCell($titleError . $titleDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($firstNameLabel . ': ', '', '', '', 'even', '', '');
        $objTable->addCell($firstNameError . $firstNameInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($lastNameLabel . ': ', '', '', '', 'odd', '', '');
        $objTable->addCell($lastNameError . $lastNameInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($genderLabel . ': ', '', '', '', 'even', '', '');
        $objTable->addCell($genderError . $genderRadio, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($emailAddressLabel . ': ', '', '', '', 'odd', '', '');
        $objTable->addCell($userEmailAddressError . $userEmailAddressInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($mobileNumberLabel . ': ', '', '', '', 'even', '', '');
        $objTable->addCell($mobileNumberInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($usernameLabel . ': ', '', '', '', 'odd', '', '');
        $objTable->addCell($usernameError . $usernameLayer . $usernameInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($passwordLabel . ': ', '', '', '', 'even', '', '');
        $objTable->addCell($passwordError . $passwordInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($confirmPasswordLabel . ': ', '', '', '', 'odd', '', '');
        $objTable->addCell($confirmPasswordInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($sidInput . $saveButton . '&nbsp;' . $cancelButton, '', '', '', 'even', 'colspan="2"', '');
        $objTable->endRow();
        $principalTable = $objTable->show();
        
        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $addUserLabel . '</b>';
        $objFieldset->contents = $principalTable;
        $principalFieldset = $objFieldset->show();
        
        $this->objIcon->title = $findPrincipalLabel;
        $this->objIcon->alt = $findPrincipalLabel;
        $this->objIcon->setIcon('magnifier', 'png');
        $findIcon = $this->objIcon->show();

        $findLink = '<a href="#" id="findprincipal">' . $findIcon . '&nbsp' . $findPrincipalLabel . '</a>';
        
        $objForm = new form('addprincipal', $this->uri(array(
            'action' => 'validateprincipal'
        )));
        if (empty($errorArray))
        {
            $objForm->extra = ' enctype="multipart/form-data" style="display: none;"';
        }
        else
        {
            $objForm->extra = ' enctype="multipart/form-data"';
        }
        $objForm->addToForm($principalFieldset);
        $objForm->addToForm('<br />' . $findLink);
        $addForm = $objForm->show();

        $string .= $addForm;

        return $string;        
    }    
    
    /**
     *
     * Method to return the principals data for the autocomplete
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxFindPrincipals()
    {
        $search = $this->getParam('term');
        $field = $this->getParam('field');
        
        $userArray = $this->objUserAdmin->searchUsers($field, $search, 'contains', 'firstname');

        foreach ($userArray as $key => $user)
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
     * Method to validate the schools details data
     * 
     * @access public
     * @param array $data The data to validate
     * @return  
     */
    public function validatePrincipal($data)
    {
        $errors = array();
        foreach ($data as $fieldname => $value)
        {
            if ($fieldname != 'sid' && $fieldname != 'country' && $fieldname != 'title' && $fieldname != 'country'
                && $fieldname != 'gender' && $fieldname != 'password' && $fieldname != 'confirm_password'
                && $fieldname != 'mobile_number')
            {
                if ($value == NULL)
                {
                    $name = explode('_', $fieldname);
                    $name = implode(' ', $name);
                    $array = array('fieldname' => $name);
                    $errorText = $this->objLanguage->code2Txt('mod_schools_error_1', 'schools', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';                }
                elseif ($fieldname == 'username')
                {
                    $error = $this->ajaxUsername(FALSE, $value);
                    if (!empty($error))
                    {
                        $errors[$fieldname] = $error;
                    }
                }
                elseif ($fieldname == 'email_address')
                {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL) == FALSE)
                    {
                        $errorText = $this->objLanguage->languageText('mod_schools_invalidemail', 'schools', 'TEXT: mod_schools_invalidemail, not found');
                        $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                    }
                }
            }
            elseif ($fieldname == 'title' || $fieldname == 'gender' || $fieldname == 'country')
            {
                if ($value == NULL)
                {
                    $array = array('fieldname' => $fieldname);
                    $errorText = $this->objLanguage->code2Txt('mod_schools_error_2', 'schools', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
            elseif ($fieldname == 'password')
            {
                if ($value == NULL && $data['confirm_password'] == NULL)
                {
                    $array = array('fieldname' => $fieldname);
                    $errorText = $this->objLanguage->code2Txt('mod_schools_error_1', 'schools', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
                if ($value != $data['confirm_password'])
                {
                    $errorText = $this->objLanguage->languageText('mod_schools_passwordsnotalike','schools', $array);
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
    public function savePrincipal($data)
    {
        $sid = $data['sid'];
        unset($data['sid']);

        $userId = $this->objUserAdmin->generateUserId();
        $id = $this->objUserAdmin->addUser($userId, $data['username'], $data['password'], $data['title'], 
            $data['first_name'], $data['last_name'], $data['email_address'], $data['gender'],
            $data['country'], $data['mobile_number'], '', $accountType='useradmin', $accountstatus='1');
        $user = $this->objUserAdmin->getUserDetails($id);
        $puid = $user['puid'];

        $groupId = $this->objGroups->getId('Principals');
        $this->objGroups->addGroupUser($groupId, $puid);
       
        $school['principal_id'] = $id;
        $school['modified_by'] = $this->objUser->PKId();
        $school['date_modified'] = date('Y-m-d H:i:s');

        $this->objDBschools->updateSchool($sid, $school);
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
        $usernameExists = $this->objLanguage->languageText('mod_schools_usernameexists', 'schools', 'TEXT: mod_schools_usernameexists, not found');
        $invalidUsername = $this->objLanguage->languageText('mod_schools_invalidusername', 'schools', 'TEXT: mod_schools_invalidusername, not found');
        $usernameShort = $this->objLanguage->languageText('mod_schools_usernameshort', 'schools', 'TEXT: mod_schools_usernameshort, not found');
        $usernameAvaliable = $this->objLanguage->languageText('mod_schools_usernameavailable', 'schools', 'TEXT: mod_schools_usernameavailable, not found');
        $success = $this->objLanguage->languageText('word_success', 'system', 'WORD: word_success, not found');
        
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
}
?>