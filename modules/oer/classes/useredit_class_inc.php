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
$GLOBALS['kewl_entry_point_run'])
{
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
class useredit extends object
{

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
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        // Serialize language items to Javascript
        $arrayVars['status_success'] = "mod_oer_status_success";
        $arrayVars['status_fail'] = "mod_oer_status_fail";
        $arrayVars['required_field'] = "mod_oer_requiredfield";
        $arrayVars['min2'] = "mod_oer_min2chars";
        $arrayVars['min6'] = "mod_oer_min6chars";
        $arrayVars['min8'] = "mod_oer_min8chars";
        $arrayVars['min100'] = "mod_oer_say100";
        $arrayVars['validemail'] = "mod_oer_validemail";
        $arrayVars['validdate'] = "mod_oer_validdate";
        $arrayVars['makeselection'] = "mod_oer_makeselection";
        $arrayVars['firstchoiceno'] = "mod_oer_firstchoiceno";
        $arrayVars['passnomatch'] = "mod_oer_passwdnotmatch";
        $arrayVars['nofirstchoice'] = "mod_oer_nofirstchoice";
        $arrayVars['usernametaken'] = "mod_oer_usernametaken";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oer');
        $this->objDbUser = $this->getObject('dbuseroer','oer');
         // Load the jquery validate plugin
        $this->appendArrayVar('headerParams',
        $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js',
          'jquery'));
        // Load the helper Javascript
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('useredit.js',
          'oer'));
    }

    /**
     *
     * Render the input form for the user data.
     *
     * @return string The rendered form
     * @access public
     * 
     */
    public function show()
    {
        $action = $this->getParam('action', FALSE);
        $mode = $this->getParam('mode', FALSE);
        if ($action) {
            // This requires login so its OK not to have additional security
            if ($mode == 'edit' || $action=='add') {
                return $this->showForLoggedIn();
            // This is open to not logged in users, so it needs extra security
            } elseif ($action == 'selfregister' && $mode == 'selfregister') {
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
    private function showForLoggedIn()
    {
        return $this->makeHeading() 
          . "<div class='formwrapper'>"
          . $this->buildForm()
          . "</div>";
    }
    
    /**
     *
     * Render the input form for the user data for self registration
     *
     * @return string The rendered form
     * @access public
     * 
     */
    private function showForNotLoggedIn()
    {
        $objUser = $this->getObject('user', 'security');
        if ($objUser->isLoggedIn()) {
            return $this->showForLoggedIn();
        } else  {
            return $this->makeHeading() 
              . "<div class='formwrapper'>"
              // Build the form with a captcha.
              . $this->buildForm(TRUE)
              . "</div>";
        }
        
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
    private function loadData($res)
    {
        foreach($res as $key=>$value) {
            $this->$key = $value;
        }
    }

    /**
     *
     * Make a heading for the form
     *
     * @return string The text of the heading
     * @access private
     *
     */
    private function makeHeading()
    {
        // Load a heading class.
        $this->loadClass('htmlheading', 'htmlelements');
        // Get heading based on whether it is edit or add.
        $this->mode = $this->getParam('mode', 'add');
        switch($this->mode) {
            case 'edit':
                $h = $this->objLanguage->languageText(
                  'mod_oer_user_heading_edit',
                  'oer');
                $ex = "";
                break;
            case 'selfregister':
                $h = $this->objLanguage->languageText(
                  'mod_oer_user_heading_selfreg',
                  'oer');
                $ex = $this->objLanguage->languageText(
                  'mod_oer_user_youcanopenid',
                  'oer');
                $ex = "<br /><span class='infonote'>$ex</span><br /><br /><br />";
                break;
            case 'add':
            default:
                $h = $this->objLanguage->languageText(
                  'mod_oer_user_heading_new',
                  'oer');
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
    private function buildForm($moreSecure=FALSE)
    {
        // Load all the required HTML classes from HTMLElements module
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('htmltable','htmlelements');
        $this->loadClass('textinput','htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        
        
        // If it is an edit, go fetch the data.
        if ($this->mode == 'edit') {
            $id = $this->getParam('id', FALSE);
            if ($id) {
                $res = $this->objDbUsr->getForEdit($id);
                if (is_array($res) && !empty ($res)) {
                    $this->loadData($res);
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
        $titles=array("title_chooseone", "title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
        foreach ($titles as $title) {
            $titleForDd = trim($this->objLanguage->languageText($title));
            if ($title == "title_chooseone") {
                //die($title);
                $titlesDropdown->addOption("none",$titleForDd);
            } else {
                $titlesDropdown->addOption($titleForDd,$titleForDd);
            }
        }
        if ($this->mode == 'edit') {
            $titlesDropdown->setSelected($this->title);
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
            $value = $this->firstname;
            $textinput->setValue($value);
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
            $value = $this->surname;
            $textinput->setValue($value);
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
        $textinput = new textinput('username');
        $textinput->size = 40;
        if ($this->mode == 'edit') {
            $value = $this->username;
            $textinput->setValue($value);
        }
        $textinput->cssId = 'username';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($unLabel);
        
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
        $sexRadio = new radio ('sex');
        $sexRadio->addOption('M', $this->objLanguage->languageText('word_male', 'system'));
        $sexRadio->addOption('F', $this->objLanguage->languageText('word_female', 'system'));
        $sexRadio->setBreakSpace(' &nbsp; ');
        if ($this->mode == 'edit') {
            $sexRadio->setSelected($this->sex);
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
            $value = $this->birthdate;
            $textinput->setValue($value);
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
            $value = $this->address;
            $textinput->setValue($value);
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
            $value = $this->city;
            $textinput->setValue($value);
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
            $value = $this->state;
            $textinput->setValue($value);
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
            $value = $this->postalcode;
            $textinput->setValue($value);
        }
        $textinput->cssId = 'postalcode';
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);
        
        // Country input options
        $table->startRow();
        $objCountries=&$this->getObject('languagecode','language');
        $label = new label($this->objLanguage->languageText('word_country'));
        $table->addCell($label->show());
        if ($this->mode == 'edit') {
            $table->addCell($objCountries->countryAlpha($this->country));
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
            $value = $this->orgcomp;
            $textinput->setValue($value);
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
            $value = $this->jobtitle;
            $textinput->setValue($value);
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
            $value = $this->occupationtype;
            $textinput->setValue($value);
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
            $value = $this->workphone;
            $textinput->setValue($value);
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
            $value = $this->cellnumber;
            $textinput->setValue($value);
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
            $value = $this->website;
            $textinput->setValue($value);
        }
        $table->addCell($textinput->show());
        $table->endRow();
        unset($label);
        
        // About yourself input options.
        $label = new label($this->objLanguage->languageText(
          'phrase_aboutyou'), 'description');
        $table->startRow();
        $table->addCell($label->show());
        $editor = new textarea ('description');
        //$editor->name = 'description';
        //$editor->height = '150px';
        //$editor->width = '500px';
        //$editor->setBasicToolBar();
        if ($this->mode == 'edit') {
            $description = $this->description;
            $editor->setContent($description);
        }
        $table->addCell($editor->show());
        $table->endRow();
        unset($label);
        
        // If we need more security as it is a self register.
        if ($moreSecure == TRUE) {
            // Create a nonce

           
            // Add the captcha to the form
            $img = '<br /><img id="img_captcha" src="index.php?module=oer&action=showcaptcha" />';
            $table->startRow();
            $table->addCell("");
            // Get a text input for the captcha
            $objInput = new textinput('captcha', '', 'text','15');
            $objInput->setId('captcha');
            $table->addCell("<span class='captcha-image'>" . $img 
              . "</span><span class='captcha-input'>" 
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
        
        // Createform, add fields to it and display.
        $formData = new form('edituser', NULL);
        //$formData = new form('edituser', 'index.php?module=oer&action=userdetailssave');
        $formData->addToForm(
            $table->show()
          . $hiddenFields
          . $msgArea);
        return $formData->show();
    }

    /**
     *
     * Get a parameter from the object properties as set by loadData()
     *
     * @param string $paramName The object property to retrieve
     * @return string The parameter value
     * @access private
     *
     */
    private function getValue($paramName)
    {
        if (isset ($this->$paramName)) {
            return $this->$paramName;
        } else {
            return NULL;
        }
    }
}
?>