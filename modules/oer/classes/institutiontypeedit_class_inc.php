<?php
/**
 *
 * Institution type editor functionality for OER module
 *
 * Institution tyep editor functionality for OER module provides for the
 * creation of the institution type editor form, which is used by the
 * class block_institutionttypeedit_class_inc.php
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
* Institution type editor functionality for OER module
*
* Institution tyep editor functionality for OER module provides for the
* creation of the institution type editor form, which is used by the
* class block_institutionttypeedit_class_inc.php
*
* @package   oer
* @author    Derek Keats derek@dkeats.com
*
*/
class institutiontypeedit extends object
{

    public $objLanguage;
    private $mode;

    private $objThumbUploader;

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
        $objSerialize = $this->getObject('serializevars', 'utilities');
         $objSerialize->languagetojs($arrayVars, 'oer');
        $this->objDbInstitutionTypes = $this->getObject('dbinstitutiontypes');
         // Load the jquery validate plugin
        $this->appendArrayVar('headerParams',
        $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js',
          'jquery'));
        // Load the helper Javascript
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('institutiontypeedit.js',
          'oer'));
        // Load all the required HTML classes from HTMLElements module
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmltable','htmlelements');
        $this->loadClass('textinput','htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
    }

    /**
     *
     * Render the input form for the institutional data.
     *
     * @return string The rendered form
     * @access public
     * 
     */
    public function show()
    {
        return $this->makeHeading() 
            . "<div class='formwrapper'>"
            . $this->buildForm()
            . "</div>";
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
        // Get heading based on whether it is edit or add.
        $this->mode = $this->getParam('mode', 'add');
        if ($this->mode == 'edit') {
            $h = $this->objLanguage->languageText(
              'mod_oer_institutiontype_heading_edit',
              'oer');
            $id = $this->getParam('id');
            $this->loadData($id);
        } else {
            $h = $this->objLanguage->languageText(
              'mod_oer_institutiontype_heading_new',
              'oer');
        }
        // Setup and show heading.
        $header = new htmlHeading();
        $header->str = $h;
        $header->type = 2;
        return $header->show();
    }

    /**
     *
     * Build a form for inputting the institution data
     *
     * @return string The formatted form
     * @access private
     * 
     */
    private function buildForm()
    {
        // Setup table and table headings with input options.
        $table = $this->newObject('htmltable', 'htmlelements');
        // Institution type input options.
        $title = $this->objLanguage->languageText(
          'mod_oer_institutiontype_name', 'oer');
        $table->startRow();
        $table->addCell($title);
        $textinput = new textinput('type');
        $textinput->size = 60;
        if ($this->mode == 'edit') {
            $value = $this->getParam('type', NULL);
            $textinput->setValue($value);
        }
        $textinput->cssClass = 'required';
        $table->addCell($textinput->show());
        $table->endRow();
        // Save button.
        $table->startRow();
        $table->addCell("&nbsp;");
        $buttonTitle = $this->objLanguage->languageText('word_save');
        $button = new button('submitType', $buttonTitle);
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
        $hidId->value = $this->getParam('id', NULL);
        $hiddenFields .= $hidId->show() . "\n\n";
        
        // Createform, add fields to it and display.
        $formData = new form('institutionTypeEditor', NULL);
        $formData->addToForm(
            $table->show()
          . $hiddenFields
          . $msgArea);
        return $formData->show();
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
    private function loadData($id)
    {

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