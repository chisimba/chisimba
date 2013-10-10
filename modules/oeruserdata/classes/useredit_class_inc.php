<?php

/**
 *
 * User editor functionality for OER module
 *
 * User editor functionality for OER module provides for the
 * creation of the user edit form, which is used by the
 * class block_useredit_class_inc.php
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
 * @package   oer
 * @author    Derek Keats derek@dkeats.com
 * @author    David Wafula
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
 *
 * User editor functionality for OER module
 *
 * User editor functionality for OER module provides for the
 * creation of the user edit form, which is used by the
 * class block_useredit_class_inc.php
 *
 * @package   oer
 * @author    Derek Keats derek@dkeats.com
 *
 */
class useredit extends object {

    public $objLanguage;
    private $mode;

    /**
     *
     * Intialiser for insitution editor UI builder class. It instantiates
     * language object and loads the required classes.
     *
     * @access public
     * @return VOID
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        // Serialize language items to Javascript
        $arrayVars['status_success'] = "mod_oeruserdata_status_success";
        $arrayVars['status_fail'] = "mod_oeruserdata_status_fail";
        $arrayVars['required_field'] = "mod_oeruserdata_requiredfield";
        $arrayVars['min2'] = "mod_oeruserdata_min2chars";
        $arrayVars['min6'] = "mod_oeruserdata_min6chars";
        $arrayVars['min8'] = "mod_oeruserdata_min8chars";
        $arrayVars['min100'] = "mod_oeruserdata_say100";
        $arrayVars['validemail'] = "mod_oeruserdata_validemail";
        $arrayVars['validdate'] = "mod_oeruserdata_validdate";
        $arrayVars['makeselection'] = "mod_oeruserdata_makeselection";
        $arrayVars['firstchoiceno'] = "mod_oeruserdata_firstchoiceno";
        $arrayVars['passnomatch'] = "mod_oeruserdata_passwdnotmatch";
        $arrayVars['nofirstchoice'] = "mod_oeruserdata_nofirstchoice";
        $arrayVars['usernametaken'] = "mod_oeruserdata_usernametaken";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oeruserdata');
        $this->objDbUser = $this->getObject('dboeruserdata', 'oeruserdata');
        // Load the jquery validate plugin
        $this->appendArrayVar('headerParams',
                $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js',
                        'jquery'));
        //Load success dialog window
        $this->loadJScript();
        // Load the helper Javascript
        $this->appendArrayVar('headerParams',
                $this->getJavaScriptFile('useredit.js',
                        'oeruserdata'));
        // Get the mode from the querystring
        $this->mode = $this->getParam('mode', 'add');
    }

    /**
     *
     * Render the input form for the user data.
     *
     * @return string The rendered form
     * @access public
     * 
     */
    public function show() {
        $action = $this->getParam('action', FALSE);
        if ($action) {
            // This requires login so its OK not to have additional security
            if ($action == 'edituser' || $action == 'adduser') {
                return $this->showForLoggedIn();
                // This is open to not logged in users, so it needs extra security
            } elseif ($action == 'selfregister') {
                return $this->showForNotLoggedIn();
            }
        }
    }

    /**
     *
     * Render the input form for the user data for logged in users.
     *
     * @return string The rendered form
     * @access public
     * 
     */
    private function showForLoggedIn() {
        $form = $this->buildForm(FALSE);
        if ($form) {
            return $this->makeHeading()
            . "<div class='formwrapper'>"
            . $form
            . "</div>";
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Render the input form for the user data for self registration
     *
     * @return string The rendered form
     * @access public
     * 
     */
    private function showForNotLoggedIn() {
        $userId = $this->objUser->userId();

        $objGa = $this->getObject('gamodel', 'groupadmin');
        $edGroup = $objGa->isGroupMember($userId, "Usermanagers");
        if ($this->objUser->isLoggedIn()) {
            if (!$this->objUser->isAdmin() && !$edGroup) {
                return FALSE;
            }
        }
        return $this->makeHeading()
        . "<div class='formwrapper'>"
        // Build the form with a captcha.
        . $this->buildForm(TRUE)
        . "</div>";
    }

    /**
     *
     * For editing, load the data according to the ID provided. It
     * loads the data into object properties.
     *
     * @param string $id The id of the record to load
     * @return boolean TRUE|FALSE
     * @access private
     *
     */
    private function loadData($res) {
        foreach ($res as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * JS an CSS for save registration status
     */
    function loadJScript() {
        $dialogCSS = '<link rel="stylesheet" type="text/css" href="skins/oer/download-dialog.css">';

        $uiAllCSS = '<link rel="stylesheet" type="text/css" href="' . $this->getResourceUri('plugins/ui/development-bundle/themes/base/jquery.ui.all.css', 'jquery') . '"/>';
        $this->appendArrayVar('headerParams', $uiAllCSS);
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.core.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.widget.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.mouse.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.draggable.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.position.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.resizable.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.dialog.js', 'jquery'));



        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('downloader.js', 'oer'));
        $this->appendArrayVar('headerParams', $dialogCSS);
    }

    /**
     *
     * Make a heading for the form
     *
     * @return string The text of the heading
     * @access private
     *
     */
    private function makeHeading() {
        // Load a heading class.
        $this->loadClass('htmlheading', 'htmlelements');
        // Depends on the mode from the querystring.
        switch ($this->mode) {
            case 'edit':
                $h = $this->objLanguage->languageText(
                                'mod_oeruserdata_hd_edit',
                                'oeruserdata');
                $ex = "";
                break;
            case 'selfregister':
                $h = $this->objLanguage->languageText(
                                'mod_oeruserdata_hd_selfreg',
                                'oeruserdata');
                $ex = $this->objLanguage->languageText(
                                'mod_oeruserdata_youcanopenid',
                                'oeruserdata');
                $ex = "<br /><span class='infonote'>$ex</span><br /><br /><br />";
                break;
            case 'add':
            default:
                $h = $this->objLanguage->languageText(
                                'mod_oeruserdata_hd_new',
                                'oeruserdata');
                $ex = "";
                break;
        }
        // Setup and show heading.
        $header = new htmlHeading();
        $header->str = $h;
        $header->type = 2;
        return $header->show() . $ex;
    }

    /**
     *
     * Build a form for inputting the institution data
     *
     * @return string The formatted form
     * @access private
     * 
     */
    private function buildForm($moreSecure=FALSE) {
        // Load all the required HTML classes from HTMLElements module
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $id = $this->getParam('id', FALSE);

        // If it is an edit, go fetch the data.
        if ($this->mode == 'edit') {
            if ($id) {
                // See if they are allowed to edit it
                $eUserId = $this->objUser->getItemFromPkId($id, 'userid');
                $myUserId = $this->objUser->userId();
                if ($eUserId == $myUserId || $this->objUser->isAdmin()) {
                    $objDbUsr = $this->getObject('dboeruserdata', 'oeruserdata');
                    $res = $objDbUsr->getForEdit($id);
                    if (is_array($res) && !empty($res)) {
                        $this->loadData($res);
                    }
                } else {
                    // Return false if they are not allowed to edit.
                    return FALSE;
                }
            }
        }


        // Setup table and table headings with input options.
        $table = $this->newObject('htmltable', 'htmlelements');
        // The user title.
        $titlesDropdown = new dropdown('title');
        $titlesLabel = new label(
                        $this->objLanguage->languageText('word_title', 'system'),
                        'input_title');
        $titles = array("title_chooseone", "title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
        foreach ($titles as $title) {
            $titleForDd = trim($this->objLanguage->languageText($title));
            if ($title == "title_chooseone") {
                //die($title);
                $titlesDropdown->addOption("none", $titleForDd);
            } else {
                $titlesDropdown->addOption($titleForDd, $titleForDd);
            }
        }
        if ($this->mode == 'edit') {
            if (isset($this->title)) {
                $titlesDropdown->setSelected($this->title);
            }
        }
        $table->startRow();
        $table->addCell($titlesLabel->show());
        $table->addCell($titlesDropdown->show());
        $table->endRow();

        // First name input options.
        $fnLabel = new label($this->objLanguage->languageText(
                                'phrase_firstname'), 'firstname');
        $table->startRow();
        $table->addCell($fnLabel->show());
        $textinput = new textinput('firstname');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->firstname)) {
                $value = $this->firstname;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'firstname';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($fnLabel);

        // Surname input options.
        $snLabel = new label($this->objLanguage->languageText(
                                'word_surname'), 'surname');
        $table->startRow();
        $table->addCell($snLabel->show());
        $textinput = new textinput('surname');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->surname)) {
                $value = $this->surname;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'surname';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($snLabel);
        // Username input options.
        $unLabel = new label($this->objLanguage->languageText(
                                'word_username'), 'username');
        $table->startRow();
        $table->addCell($unLabel->show());
        if ($this->mode !== 'edit') {
            $textinput = new textinput('username');
            $textinput->size = 40;
            if ($this->mode == 'edit') {
                if (isset($this->username)) {
                    if ($this->username != "admin") {
                        $value = $this->username;
                        $textinput->setValue($value);
                    }
                }
            }
            $textinput->cssId = 'username';
            $un = $textinput->show();
        } else {
            $un = '<div class="fake_input">' . $this->username . "</div>";
        }
        $table->addCell($un.'<label class="errors" for="usernamerrors" generated="false" id="usernameerror"></label>');
        $table->endRow();
        unset($unLabel);
        if ($this->mode !== 'edit') {
            // Password input options.
            $label = new label($this->objLanguage->languageText(
                                    'word_password'), 'password');
            $table->startRow();
            $table->addCell($label->show());
            $textinput = new textinput('password');
            $textinput->size = 40;
            $textinput->fldType = 'password';
            $textinput->cssId = 'password';
            $table->addCell($textinput->show());
            $table->endRow();
            unset($label);

            // Password confirmation input options.
            $label = new label($this->objLanguage->languageText(
                                    'phrase_confirmpassword'), 'confirmpassword');
            $table->startRow();
            $table->addCell($label->show());
            $textinput = new textinput('confirmpassword');
            $textinput->size = 40;
            $textinput->fldType = 'password';
            $textinput->cssId = 'confirmpassword';
            $table->addCell($textinput->show());
            $table->endRow();
            unset($label);
        }

        // Email input options.
        $label = new label($this->objLanguage->languageText(
                                'phrase_emailaddress'), 'email');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('email');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            $value = $this->emailaddress;
            $textinput->setValue($value);
        }
        $textinput->cssId = 'email';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // Sex input (what is called gender these days)
        $sexRadio = new radio('sex');
        $sexRadio->addOption('M', $this->objLanguage->languageText('word_male', 'system'));
        $sexRadio->addOption('F', $this->objLanguage->languageText('word_female', 'system'));
        $sexRadio->setBreakSpace(' &nbsp; ');
        if ($this->mode == 'edit') {
            if (isset($this->sex)) {
                $sexRadio->setSelected($this->sex);
            }
        } else {
            $sexRadio->setSelected('M');
        }
        $label = new label($this->objLanguage->languageText(
                                'word_sex'), 'sex');
        $table->startRow();
        $table->addCell($label->show());
        $table->addCell($sexRadio->show());
        $table->endRow();

        // Birthdate input options.
        $label = new label($this->objLanguage->languageText(
                                'phrase_birthdate'), 'birthdate');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('birthdate');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->birthdate)) {
                $value = $this->birthdate;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'birthdate';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // Address input options.
        $label = new label($this->objLanguage->languageText(
                                'word_address'), 'address');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('address');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->address)) {
                $value = $this->address;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'address';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // City input options.
        $label = new label($this->objLanguage->languageText(
                                'word_city'), 'city');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('city');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->city)) {
                $value = $this->city;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'city';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // State / province input options.
        $label = new label($this->objLanguage->languageText(
                                'word_state'), 'state');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('state');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->state)) {
                $value = $this->state;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'state';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // Postal code input options.
        $label = new label($this->objLanguage->languageText(
                                'phrase_postalcode'), 'postalcode');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('postalcode');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->postalcode)) {
                $value = $this->postalcode;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'postalcode';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // Country input options
        $table->startRow();
        $objCountries = &$this->getObject('languagecode', 'language');
        $label = new label($this->objLanguage->languageText('word_country'));
        $table->addCell($label->show());
        if ($this->mode == 'edit') {
            if (isset($this->country)) {
                $table->addCell($objCountries->countryAlpha($this->country));
            } else {
                $table->addCell($objCountries->countryAlpha());
            }
        } else {
            $table->addCell($objCountries->countryAlpha());
        }
        $table->endRow();
        unset($label);

        // Organization or company input options.
        $label = new label($this->objLanguage->languageText(
                                'phrase_orgcomp'), 'orgcomp');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('orgcomp');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->orgcomp)) {
                $value = $this->orgcomp;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'orgcomp';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // Job title input options.
        $label = new label($this->objLanguage->languageText(
                                'phrase_jobtitle'), 'jobtitle');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('jobtitle');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->jobtitle)) {
                $value = $this->jobtitle;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'jobtitle';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // Occupation input options.
        $label = new label($this->objLanguage->languageText(
                                'phrase_occupationtype'), 'occupationtype');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('occupationtype');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->occupationtype)) {
                $value = $this->occupationtype;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'occupationtype';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // Workphone input options.
        $label = new label($this->objLanguage->languageText(
                                'phrase_workphone'), 'workphone');
        $table->startRow();
        $phoneIcon = "<span class='phone'></span>";
        $table->addCell($label->show());
        $textinput = new textinput('workphone');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->workphone)) {
                $value = $this->workphone;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'workphone';
        $table->addCell($textinput->show() . $phoneIcon);
        $table->endRow();
        unset($label);

        // Mobile phone input options.
        $label = new label($this->objLanguage->languageText(
                                'phrase_mobilephone'), 'mobilephone');
        $table->startRow();
        $phoneIcon = "<span class='phone'></span>";
        $table->addCell($label->show());
        $textinput = new textinput('mobilephone');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->cellnumber)) {
                $value = $this->cellnumber;
                $textinput->setValue($value);
            }
        }
        $textinput->cssId = 'mobilephone';
        $table->addCell($textinput->show() . $phoneIcon);
        $table->endRow();
        unset($label);

        // Website URL input options.
        $label = new label($this->objLanguage->languageText(
                                'word_website'), 'website');
        $table->startRow();
        $table->addCell($label->show());
        $textinput = new textinput('website');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            if (isset($this->website)) {
                $value = $this->website;
                $textinput->setValue($value);
            }
        }
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);

        // About yourself input options.
        $label = new label($this->objLanguage->languageText(
                                'phrase_aboutyou'), 'description');
        $table->startRow();
        $table->addCell($label->show());
        $editor = new textarea('description');
        $editor->cols = 39;
        //$editor->name = 'description';
        //$editor->height = '150px';
        //$editor->width = '500px';
        //$editor->setBasicToolBar();
        if ($this->mode == 'edit') {
            if (isset($this->description)) {
                $description = $this->description;
                $editor->setContent($description);
            }
        }
        $table->addCell($editor->show());
        $table->endRow();
        unset($label);

        // If we need more security as it is a self register.
        if ($moreSecure == TRUE) {
            // Create a nonce
            // Add the captcha to the form
            $img = '<br /><img id="img_captcha" src="index.php?module=oeruserdata&action=showcaptcha" />';
            $table->startRow();
            // About yourself input options.
            $label = new label($this->objLanguage->languageText(
                                    'mod_oeruserdata_caplab', 'oeruserdata'), 'captcha');
            $table->addCell($label->show());
            // Get a text input for the captcha
            $objInput = new textinput('captcha', '', 'text', '15');
            $objInput->setId('captcha');
            $objInput->size = 8;
            $table->addCell("<span class='captcha-image'>" . $img
                    . "</span><br /><span class='captcha-input'>"
                    . $objInput->show() . "</span>");
            $table->endRow();
        }

        // Save button.
        $table->startRow();
        $table->addCell("&nbsp;");
        $buttonTitle = $this->objLanguage->languageText('word_save');
        $button = new button('submitUser', $buttonTitle);
        $button->setToSubmit();
        //$button->cssId = "submitInstitution";
        $table->addCell($button->show());
        $table->endRow();



        // Insert a message area for Ajax result to display.
        $msgArea = "<br /><div id='save_results' class='ajax_results'></div>";

        // Default success message upon saving. Shown on a dialog window
        $saveResultsMsg = '<div id="register_success"  title="' .
                $this->objLanguage->languageText(
                        'mod_oeruserdata_regstatus', 'oeruserdata') . '">' .
                $this->objLanguage->languageText(
                        'mod_oeruserdata_regsuccessmsg', 'oeruserdata') . '</div>';
        $saveResultsMsg .= '<div id="register_fail"  title="' .
                $this->objLanguage->languageText(
                        'mod_oeruserdata_regstatus', 'oeruserdata') . '">' .
                $this->objLanguage->languageText(
                        'mod_oeruserdata_regfailmsg', 'oeruserdata') . '</div>';
        $saveResultsMsg .= '<div id="update_success"  title="' .
                $this->objLanguage->languageText(
                        'mod_oeruserdata_regstatus', 'oeruserdata') . '">' .
                $this->objLanguage->languageText(
                        'mod_oeruserdata_updatesuccessmsg', 'oeruserdata') . '</div>';

        // Add hidden fields for use by JS
        $hiddenFields = "\n\n";
        $hidMode = new hiddeninput('mode');
        $hidMode->cssId = "mode";
        $hidMode->value = $this->mode;
        $hiddenFields .= $hidMode->show() . "\n";
        $hidId = new hiddeninput('id');
        $hidId->cssId = "id";
        if ($this->mode == 'edit') {
            $hidId->value = $this->id;
        }
        $hiddenFields .= $hidId->show() . "\n\n";
        // Hidden for mode to turn off validation of username on edit
        $hidMode = new hiddeninput('edmode');
        $hidMode->cssId = "edmode";
        if ($this->mode == 'edit') {
            $hidMode->value = 'edit';
        }
        $hiddenFields .= $hidMode->show() . "\n\n";
        // Hidden field for username since we should not change it here
        if ($this->mode == 'edit') {
            $hidMode = new hiddeninput('username');
            $hidMode->cssId = "username";
            $hidMode->value = $this->username;
            $hiddenFields .= $hidMode->show() . "\n\n";
        }
        // Createform, add fields to it and display.
        $formData = new form('edituser', NULL);
        //$formData = new form('edituser', 'index.php?module=oer&action=userdetailssave');
        $formData->addToForm(
                $table->show()
                . $hiddenFields
                . $msgArea
                . $saveResultsMsg);
        return $formData->show();
    }

}

?>