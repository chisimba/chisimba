<?php

/**
 *
 * Downloader Info editor functionality for OER module
 *
 * Downloader Info editor functionality for OER module provides for the creation of the
 * downloader Info editor form, which is used by the class block_groupedit_class_inc.php
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
 * @author    Paul Mungai paulwando@gmail.com 
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
 * Downloader Info editor functionality for OER module
 *
 * Downloader Info editor functionality for OER module provides for the creation of the
 * downloader Info editor form, which is used by the class block_groupedit_class_inc.php
 *
 * @package   oer
 * @author    Paul Mungai paulwando@gmail.com
 *
 */
class downloaderedit extends object {

    public $objLanguage;
    private $objDBdownloaders;

    /**
     *
     * Intialiser for the oerfixer database connector
     * @access public
     * @return VOID
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objDBdownloaders = $this->getObject('dboer_downloaders');
        // Serialize language items to Javascript
        $arrayVars['status_success'] = "mod_oer_status_success";
        $arrayVars['status_fail'] = "mod_oer_status_fail";
        $objSerialize = $this->getObject('serializevars', 'utilities');
         $objSerialize->languagetojs($arrayVars, 'oer');

        // Load the jquery validate plugin
        $this->appendArrayVar('headerParams',
                $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js',
                        'jquery'));
        // Load the helper Javascript.
        $this->appendArrayVar('headerParams',
                $this->getJavaScriptFile('downloaderedit.js', 'oer'));
        // Load all the required HTML classes from HTMLElements module.
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        // Get edit or add mode from querystring.
        $this->mode = $this->getParam('mode', 'add');
    }

    /**
     * Builds and Renders the downloader edit form
     *
     * @param string $productId
     * @return string
     */
    public function show($productId, $id, $producttype) {        
        return $this->makeHeading()
        . $this->buildForm($productId, $id, $producttype);
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
    private function loadData($id) {
        $arData = $this->objDBdownloaders->listSingle($id);
        if (!empty($arData)) {
            foreach ($arData[0] as $key => $value) {
                $this->$key = $value;
            }
            return TRUE;
        } else {
            return FALSE;
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
    private function makeHeading() {
        // setup and show heading
        $header = new htmlheading();
        $header->type = 1;
        if (isset($this->name)) {
            $header->str = $this->name;
        } else {
            $header->str = $this->objLanguage->languageText(
                            'mod_oer_downloadproduct', 'oer', "Download product");
        }
        return $header->show();
    }
    /**
     * Function builds download form for users who are not logged in
     *
     * @param string $productId
     * @param string $id
     * @param string $producttype
     * @return string
     */

    private function buildForm($productId, $id, $producttype) {
        $mode = $this->getParam('mode', 'add');
        if ($mode == "add") {
            // Create the form.
            $form = new form('downloadereditor');

            // Create a table to hold the layout
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->width = '500px';
            $table->border = '0';
            $tableable->cellspacing = '0';
            $table->cellpadding = '2';

            // first name.
            $fname = new textinput('fname');
            $fname->size = 40;
            $fname->cssClass = 'required';
            if ($this->mode == 'edit') {
                $fname->value = $this->fname;
            } else {
                $fname->value = NULL;
            }
            $table->startRow();
            $table->addCell(
                    $this->objLanguage->languageText('mod_oer_fname',
                            'oer'));
            $table->addCell($fname->show());
            $table->endRow();

            // last name.
            $fname = new textinput('lname');
            $fname->size = 40;
            $fname->cssClass = 'required';
            if ($this->mode == 'edit') {
                $fname->value = $this->lname;
            } else {
                $fname->value = NULL;
            }
            $table->startRow();
            $table->addCell(
                    $this->objLanguage->languageText('mod_oer_lname',
                            'oer'));
            $table->addCell($fname->show());
            $table->endRow();

            // Email.
            $email = new textinput('email');
            $email->size = 40;
            $email->cssClass = 'email required';
            if ($this->mode == 'edit') {
                $email->value = $this->email;
            } else {
                $email->value = NULL;
            }
            $table->startRow();
            $table->addCell(
                    $this->objLanguage->languageText('mod_oer_group_email',
                            'oer'));
            $table->addCell($email->show());
            $table->endRow();

            // Organisation.
            $organisation = new textinput('organisation');
            $organisation->size = 40;
            $organisation->cssClass = 'required';
            if ($this->mode == 'edit') {
                $organisation->value = $this->organisation;
            } else {
                $organisation->value = NULL;
            }
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('phrase_orgcomp', 'system'));
            $table->addCell($organisation->show());
            $table->endRow();

            // occupation
            $occupation = new textinput('occupation');
            $occupation->size = 40;
            $occupation->cssClass = 'required';
            if ($this->mode == 'edit') {
                $occupation->value = $this->occupation;
            } else {
                $occupation->value = NULL;
            }

            $table->startRow();
            $table->addCell($this->objLanguage->languageText(
                            'mod_oer_occupation', 'oer'));
            $table->addCell($occupation->show());
            $table->endRow();

            //Reason for download
            $downloadreason = new textarea('downloadreason');
            $downloadreason->rows = 4;
            $downloadreason->cols = 40;
            $downloadreason->cssClass = 'required';
            if ($this->mode == 'edit') {
                $downloadreason->value = $this->downloadreason;
            } else {
                $downloadreason->value = NULL;
            }

            $table->startRow();
            $table->addCell($this->objLanguage->languageText(
                            'mod_oer_downloadreason', 'oer'));
            $table->addCell($downloadreason->show());
            $table->endRow();

            //Terms of use
            $tosLabel = $this->objLanguage->languageText(
                            'mod_oer_useterms', 'oer');
            $useterms = new checkbox('useterms', "", false);
            $useterms->cssClass = "required";
            if ($this->mode == 'edit') {
                //$useterms->ischecked = $this->useterms;
            } else {
                $useterms->value = NULL;
            }

            $table->startRow();
            $table->addCell("&nbsp;");
            $table->addCell($useterms->show() . "&nbsp;" . $tosLabel);
            $table->endRow();

            //Add table to form
            $form->addToForm($table->show());

            //Submit button
            $button = new button('submit', $this->objLanguage->languageText('mod_oer_next', 'oer'));
            $button->setToSubmit();
            //$form->addToForm($button->show());
            //Cancel
            $buttonCl = new button('cancel', $this->objLanguage->languageText('word_cancel'));
            $uri = $this->uri(array("action" => "viewadaptation", "id" => $productId));
            $buttonCl->setOnClick('javascript: window.location=\'' . $uri . '\'');
            $form->addToForm($button->show() . '&nbsp;&nbsp;' . $buttonCl->show());

            // Add hidden fields for use by JS
            $hiddenFields = "\n\n";
            $hidMode = new hiddeninput('mode');
            $hidMode->cssId = "mode";
            $hidMode->value = $this->getparam("mode");
            $hiddenFields .= $hidMode->show() . "\n";
            $hidId = new hiddeninput('id');
            $hidId->cssId = "id";
            $hidId->value = $this->getParam('id', NULL);
            $hiddenFields .= $hidId->show() . "\n\n";
            $prodId = new hiddeninput('productid');
            $prodId->cssId = "productid";
            $prodId->value = $productId;
            $hiddenFields .= $prodId->show() . "\n\n";
            //
            $producttypeHd = new hiddeninput('producttype');
            $producttypeHd->cssId = "producttype";
            $producttypeHd->value = $producttype;
            $hiddenFields .= $producttypeHd->show() . "\n\n";

            $form->addToForm($hiddenFields);

            // Insert a message area for Ajax result to display.
            $msgArea = "<br /><div id='save_results' class='ajax_results'></div>";            

            $form->addToForm($msgArea);

            // Send the form
            return "<div id='downloaderinfoform'>" . $form->show() . "</div>";
        } else {
            //Get download form
            $buildDownloadForm = $this->buildDownloadForm($productId, $id);
            return "<div id='downloadprodform'>" . $buildDownloadForm . "</div>";
        }
    }
    /**
     * Function builds the download form
     *
     * @param string $productId
     * @param string $id
     * @return string
     */

        function buildDownloadForm($productId, $id) {
            // Create the form.
            $form = new form('downloadproductform');
            $producttype = $this->getParam("producttype","adaptation");

            // Create a table to hold the layout
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->width = '550px';
            $table->border = '0';
            $tableable->cellspacing = '0';
            $table->cellpadding = '2';

            //Download format
            $table->startRow();
            $table->addCell($this->objLanguage->languageText(
                            'mod_oer_selectformat', 'oer'));
            $table->endRow();

            $downloadformat = new radio('downloadformat');
            $downloadformat->breakSpace = "<br />";
            $downloadformat->cssClass = "required";
            //$downloadformat->cssId = "downloadformat";
            $downloadformat->addOption("pdf", $this->objLanguage->languageText(
                            'mod_oer_pdf', 'oer'));
            $downloadformat->addOption("odt", $this->objLanguage->languageText(
                            'mod_oer_odt', 'oer'));
            $downloadformat->addOption("doc", $this->objLanguage->languageText(
                            'mod_oer_msword', 'oer'));

            $table->startRow();
            $table->addCell($downloadformat->show());
            $table->endRow();
            //Spacer
            $table->startRow();
            $table->addCell("&nbsp;");
            $table->endRow();

            //Notify updates original
            $notifyupdateoriginalLabel = $this->objLanguage->languageText(
                            'mod_oer_acceptnotifyupdatesoriginal', 'oer');
            $notifyupdateoriginal = new checkbox('notifyupdateoriginal', "", false);
            $notifyupdateoriginal->value = "notifyoriginal";
            //$useterms->cssClass = "required";
            if ($this->mode == 'edit') {
                //$useterms->ischecked = $this->useterms;
            }

            $table->startRow();
            $table->addCell($notifyupdateoriginal->show() . "&nbsp;" . $notifyupdateoriginalLabel);
            $table->endRow();

            //Notify updates adaptation
            $notifyupdateadaptationLabel = $this->objLanguage->languageText(
                            'mod_oer_acceptnotifyupdatesadaptation', 'oer');
            $notifyupdateadaptation = new checkbox('notifyupdateadaptation', "", false);
            $notifyupdateadaptation->value = "notifyadaptation";
            //$useterms->cssClass = "required";
            if ($this->mode == 'edit') {
                //$useterms->ischecked = $this->useterms;
            }

            $table->startRow();
            $table->addCell($notifyupdateadaptation->show() . "&nbsp;" . $notifyupdateadaptationLabel);
            $table->endRow();

            $form->addToForm($table->show());

            //Submit button
            $button = new button('submit', $this->objLanguage->languageText('mod_oer_download', 'oer'));
            $button->setToSubmit();
            //$form->addToForm($button->show());
            //Cancel
            $buttonCl = new button('cancel', $this->objLanguage->languageText('word_back', 'system'));
            if($producttype == "adaptation") {
            $uri = $this->uri(array("action" => "viewadaptation", "id" => $productId));
            } else {
                $uri = $this->uri(array("action" => "vieworiginalproduct", "identifier" => $productId, "id" => $productId));
            }
            $buttonCl->setOnClick('javascript: window.location=\'' . $uri . '\'');
            $form->addToForm($button->show() . '&nbsp;&nbsp;' . $buttonCl->show());

            // Insert a message area for Ajax result to display.
            $msgArea = "<br /><div id='save_results' class='ajax_results'></div>";
            $form->addToForm($msgArea);

            // Add hidden fields for use by JS
            $hiddenFields = "\n\n";
            $hidProdType = new hiddeninput('producttype');
            $hidProdType->cssId = "producttype";
            $hidProdType->value = $this->getparam("producttype", "adaptation");
            $hiddenFields .= $hidProdType->show() . "\n";
            $hidMode = new hiddeninput('mode');
            $hidMode->cssId = "mode";
            $hidMode->value = $this->getparam("mode");
            $hiddenFields .= $hidMode->show() . "\n";
            $hidUserId = new hiddeninput('id');
            $hidUserId->cssId = "id";
            $hidUserId->value = $this->getParam('id', NULL);
            $hiddenFields .= $hidUserId->show() . "\n\n";
            $prodId = new hiddeninput('productid');
            $prodId->cssId = "productid";
            $prodId->value = $productId;
            $hiddenFields .= $prodId->show() . "\n\n";

            $form->addToForm($hiddenFields);
            // Send the form, dont show to allow addition of form items
            return $form->show();
        }

    }

?>