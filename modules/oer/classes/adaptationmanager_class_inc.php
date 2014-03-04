<?php

/**
 * Contains util methods for managing adaptations
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
 * @package    oer
 * @author     pwando paulwando@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 *
 * @author pwando
 */
class adaptationmanager extends object {

    private $dbproducts;
    private $dbInstitution;
    private $objLanguage;
    public $objConfig;
    private $objUser;
    private $dbSectionContent;

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject("user", "security");
        $this->dbInstitution = $this->getObject("dbinstitution", "oer");
        $this->dbInstitutionType = $this->getObject("dbinstitutiontypes", "oer");
        $this->dbproducts = $this->getObject("dbproducts", "oer");
        $this->dbOERAdaptations = $this->getObject("dboer_adaptations", "oer");
        $this->dbSectionContent = $this->getObject("dbsectioncontent", "oer");
        $this->dbSectionNodes = $this->getObject("dbsectionnodes", "oer");
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->addJS();
        $this->setupLanguageItems();
    }

    /**
     * Used fo uploading product thumbnail
     *
     */
    function uploadProductThumbnail() {
        $dir = $this->objConfig->getcontentBasePath();

        $generatedid = $this->getParam('id');
        $filename = $this->getParam('filename');

        $objMkDir = $this->getObject('mkdir', 'files');

        $productid = $this->getParam('productid');
        $destinationDir = $dir . '/oer/products/' . $productid;

        $objMkDir->mkdirs($destinationDir);

        $objUpload = $this->newObject('upload', 'files');
        $objUpload->permittedTypes = array(
            'all'
        );
        $objUpload->overWrite = TRUE;
        $objUpload->uploadFolder = $destinationDir . '/';

        $result = $objUpload->doUpload(TRUE, "thumbnail");


        if ($result['success'] == FALSE) {

            $filename = isset($_FILES['fileupload']['name']) ? $_FILES['fileupload']['name'] : '';
            $error = $this->objLanguage->languageText('mod_oer_uploaderror', 'oer');
            return array('message' => $error, 'file' => $filename, 'id' => $generatedid);
        } else {
            $data = array("thumbnail" => "/oer/products/" . $productid . "/thumbnail.png");
            $this->dbproducts->updateOriginalProduct($data, $productid);
            $filename = $result['filename'];

            $params = array('action' => 'showproductthumbnailuploadresults', 'id' => $generatedid, 'fileid' => $id, 'filename' => $filename);

            return $params;
        }
    }

    /**
     * adds essential js
     */
    function addJS() {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('adaptation.js', 'oer'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('makeadaptation.js', 'oer'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('expand.js', 'oer'));
    }

    /**
     * adds essential js
     */
    function makeAdaptationJS() {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('makeadaptation.js', 'oer'));
    }

    /**
     * sets up necessary lang items for use in js
     */
    function setupLanguageItems() {
        // Serialize language items to Javascript
        $arrayVars['status_success'] = "mod_oer_status_success";
        $arrayVars['status_fail'] = "mod_oer_status_fail";
        $arrayVars['confirm_delete_adaptation'] = "mod_oer_confirm_delete_adaptation";
        $arrayVars['loading'] = "mod_oer_loading";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oer');
    }

    /**
     * setup make new adaptation form
     * @param $id The id of the product
     * @param $mode Whether its a new adaptation or editing an existing one
     * return string
     */
    public function makeNewAdaptation($mode, $id = Null, $productid = Null) {
        $objTable = $this->getObject('htmltable', 'htmlelements');
        if ($productid == Null || empty($productid)) {
            return $this->nextAction('adaptationlist');
        }
        $sectionId = $id;

        $objSectionManager = $this->getObject('sectionmanager', 'oer');

        $createInLang = '<div id="createin">' . $this->objLanguage->languageText('mod_oer_currentpath', 'oer') .
                " : (" . $this->objLanguage->languageText('mod_oer_required', 'oer') . ")" . '<div>';
        $selected = '';

        $createInDdown = $objSectionManager->buildSectionsTree($productid, '', "false", 'htmldropdown', $selected) . '</div>';

        //Store the mode
        $hidMode = new hiddeninput('mode');
        $hidMode->cssId = "mode";
        $hidMode->value = $mode;

        $adaptationSection = false;

        if ($id != Null) {
            //Get adaptation section data with sectionnode id
            $adaptationSection = $this->dbSectionContent->getSectionContent($id);

            $hidNodeId = new hiddeninput('node_id');
            if ($adaptationSection) {
                $id = $adaptationSection["id"];
                $mode = "edit";
                $hidNodeId->value = $adaptationSection['node_id'];
            } else {
                $mode = "add";
                $hidNodeId->value = "";
            }
            $objTable->startRow();
            $objTable->addCell($hidNodeId->show());
            $objTable->endRow();
        }
        if ($productid != Null) {
            //Get adapted-product data
            $product = $this->dbproducts->getProduct($productid);
        }
        //the title
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_sectiontitle', 'oer') .
                " : (" . $this->objLanguage->languageText('mod_oer_required', 'oer') . ")");
        $objTable->endRow();

        $objTable->startRow();
        $textinput = new textinput('section_title');
        $textinput->size = 60;
        $textinput->cssClass = 'required';
        if ($adaptationSection) {
            $textinput->value = $adaptationSection['title'];
        } else if ($productid != null) {
            $textinput->value = $product['title'];
        }
        $objTable->addCell($textinput->show());
        $objTable->endRow();

        //Current path lang item
        $objTable->startRow();
        $objTable->addCell($createInLang);
        $objTable->endRow();

        //current path drop down
        $objTable->startRow();
        $objTable->addCell($createInDdown);
        $objTable->endRow();

        //section content
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_sectioncontent', 'oer') .
                " : (" . $this->objLanguage->languageText('mod_oer_required', 'oer') . ")");
        $objTable->endRow();

        $objTable->startRow();
        $description = $this->newObject('htmlarea', 'htmlelements');
        $description->name = 'section_content';
        $description->cssClass = 'required';

        if ($adaptationSection) {
            $description->value = $adaptationSection['content'];
        }
        $description->height = '150px';
        $description->setBasicToolBar();
        $objTable->addCell($description->show());
        $objTable->endRow();

        //published status
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_status', 'oer') .
                " : (" . $this->objLanguage->languageText('mod_oer_required', 'oer') . ")");
        $objTable->endRow();
        $objTable->startRow();
        $published = new dropdown('status');
        $published->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $published->cssClass = "required";
        $published->addOption('disabled', $this->objLanguage->languageText('mod_oer_disabled', 'oer'));
        $published->addOption('draft', $this->objLanguage->languageText('mod_oer_draft', 'oer'));
        $published->addOption('published', $this->objLanguage->languageText('mod_oer_published', 'oer'));

        if ($adaptationSection) {
            $published->setSelected($adaptationSection['status']);
        }
        $objTable->addCell($published->show());
        $objTable->endRow();

        //attach file
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_attachfile', 'oer'));
        $objTable->endRow();

        $hidAttachment = new hiddeninput('attachment');
        $hidAttachment->value = "";
        if ($id != null) {
            //$hidAttachment->value = $adaptationSection['attachment'];
        }
        $objTable->startRow();
        $objTable->addCell($hidAttachment->show());
        $objTable->endRow();

        //keywords
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_keywords', 'oer') . " (" .
                $this->objLanguage->languageText('mod_oer_keywordsInstruction', 'oer') . ")");
        $objTable->endRow();

        $objTable->startRow();
        $textinput = new textinput('keywords');
        $textinput->size = 60;
        $textinput->cssClass = 'required';

        if ($adaptationSection) {
            $textinput->value = $adaptationSection['keywords'];
        } else if ($product) {
            $textinput->value = $product['keywords'];
        }
        $objTable->addCell($textinput->show());
        $objTable->endRow();

        //contributed by
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_contributedby', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $textinput = new textinput('contributed_by');
        $textinput->size = 60;
        $textinput->cssClass = 'required';
        if ($adaptationSection) {
            $textinput->value = $adaptationSection['contributedby'];
        }
        $objTable->addCell($textinput->show());
        $objTable->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        if ($mode == "new") {
            $fieldset->setLegend($this->objLanguage->languageText('mod_oer_sectionviewaddadaptation', 'oer'));
        } else {
            $fieldset->setLegend($this->objLanguage->languageText('mod_oer_sectionvieweditadaptation', 'oer'));
        }
        $fieldset->addContent($objTable->show());


        $action = "addadaptationsection";
        $formData = new form('addadaptationsection', $this->uri(array("action" => $action, "id" => $sectionId, "productid" => $productid, "mode" => $mode)));
        $formData->addToForm($fieldset);

        $button = new button('save', $this->objLanguage->languageText('word_save', 'system', 'Save'));
        $button->setToSubmit();
        $formData->addToForm('<br/>' . $button->show());

        $button = new button('cancel', $this->objLanguage->languageText('word_back'));
        $uri = $this->uri(array("action" => "viewadaptation", "id" => $productid));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "original_product_title";
        if ($adaptationSection) {
            $header->str = $adaptationSection['title'];
        } else {
            $header->str = $product['title'];
        }

        return $header->show() . $formData->show();
    }

    /**
     * Function constructs the  form for managing an adaptation - step 1
     * 
     * @param string $id adaptation id
     * @param string $mode add/edit mode
     * @return form
     */
    public function buildAdaptationFormStep1($id, $mode) {


        $objTable = $this->getObject('htmltable', 'htmlelements');

        if ($id != null) {
            //Get adapted-product data
            $product = $this->dbproducts->getProduct($id);

            $hidId = new hiddeninput('id');
            $hidId->cssId = "id";
            $hidId->value = $id;
            $hidMode = new hiddeninput('mode');
            $hidMode->cssId = "mode";
            $hidMode->value = $mode;
            $objTable->startRow();
            $objTable->addCell($hidId->show() . $hidMode->show());
            $objTable->endRow();
        }

        //the title
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_title', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $textinput = new textinput('title');
        $textinput->size = 60;
        $textinput->cssClass = 'required';
        if ($product != null) {
            $textinput->value = $product['title'];
        }
        $objTable->addCell($textinput->show());
        $objTable->endRow();

        //alternative title
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_alttitle', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $textinput = new textinput('alternative_title');
        $textinput->size = 60;
        if ($product != null) {
            $textinput->value = $product['alternative_title'];
        }
        $objTable->addCell($textinput->show());
        $objTable->endRow();

        //author
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_author', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $textinput = new textinput('author');
        $textinput->size = 60;
        $textinput->cssClass = 'required';
        if ($product != null) {
            $textinput->value = $product['author'];
        }
        $objTable->addCell($textinput->show());
        $objTable->endRow();

        //other contributors
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_othercontributors', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $textarea = new textarea('othercontributors', '', 5, 55);
        $textarea->cssClass = 'required';
        if ($product != null) {
            $textarea->value = $product['othercontributors'];
        }
        $objTable->addCell($textarea->show());
        $objTable->endRow();


        //publisher
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_publisher', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $textinput = new textinput('publisher');
        $textinput->size = 60;
        $textinput->cssClass = 'required';
        if ($product != null) {
            $textinput->value = $product['publisher'];
        }
        $objTable->addCell($textinput->show());
        $objTable->endRow();

        //keywords
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_keywords', 'oer') . " (" . $this->objLanguage->languageText('mod_oer_keywordsInstruction', 'oer') . ")");
        $objTable->endRow();

        $objTable->startRow();
        $textinput = new textinput('keywords');
        $textinput->size = 60;
        $textinput->cssClass = 'required';
        if ($product != null) {
            $textinput->value = $product['keywords'];
        }
        $objTable->addCell($textinput->show());
        $objTable->endRow();

        //groups
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_groups', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $group = new dropdown('group');
        $group->cssClass = 'required';

        $group->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        //Get groups
        $dbGroups = $this->getObject("dbgroups", "oer");
        $currentGroups = $dbGroups->getAllGroups();

        $dbContext = $this->getObject("dbcontext", "context");

        //Generate dropdown from existing groups
        if ($currentGroups != Null) {
            foreach ($currentGroups as $currentGroup) {
                $context = $dbContext->getContext($currentGroup['contextcode']);
                $group->addOption($currentGroup['contextcode'], $context['title']);
            }
        }

        //Set selected group
        if ($product != null) {

            $group->setSelected($product['groupid']);
        }
        $objTable->addCell($group->show());
        $objTable->endRow();

        //region
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_region', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $region = new dropdown('region');
        $region->cssClass = 'required';

        $region->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $region->addOption('Africa', 'Africa');
        $region->addOption('Arab States', 'Arab States');
        $region->addOption('Asia and the Pacific', 'Asia and the Pacific');
        $region->addOption('Europe and North America', 'Europe and North America');
        //Set selected region
        if ($product != null) {
            $region->selected = $product['region'];
        }
        $objTable->addCell($region->show());
        $objTable->endRow();

        //  country.
        $objTable->startRow();

        $objCountries = $this->getObject('languagecode', 'language');
        $objTable->addCell($this->objLanguage->languageText(
                        'word_country', 'system'));
        $objTable->endRow();
        $objTable->startRow();
        if ($product != null) {
            $objTable->addCell($objCountries->countryAlpha($product['country']));
        } else {
            $objTable->addCell($objCountries->countryAlpha());
        }
        $objTable->endRow();


        //Institution
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_group_institution', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $institution = new dropdown('institution');
        // $institution->cssClass = 'required';

        $institution->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        //Get institutions
        $currentInstitutions = $this->dbInstitution->getAllInstitutions();
        //Generate dropdown from existing institutions
        if ($currentInstitutions != Null) {
            foreach ($currentInstitutions as $currentInstitution) {
                $institution->addOption($currentInstitution['id'], $currentInstitution['name']);
            }
        }
        //Set selected
        if ($product != null) {
            $institution->setSelected($product['institutionid']);
        }
        $objTable->addCell($institution->show());
        $objTable->endRow();

        //language
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_language', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $language = new dropdown('language');
        $language->cssClass = 'required';

        if ($product != null) {
            $language->selected = $product['language'];
        } else {
            $language->selected = "en";
        }
        $language->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $language->addOption('en', $this->objLanguage->languageText('mod_oer_english', 'oer'));
        $objTable->addCell($language->show());
        $objTable->endRow();

        //Adaptation notes
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_adaptationotes', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $adaptation_notes = $this->newObject('htmlarea', 'htmlelements');
        $adaptation_notes->name = 'adaptation_notes';
        $adaptation_notes->cssClass = 'required';
        if ($product != null) {
            $adaptation_notes->value = $product['adaptation_notes'];
        }
        $adaptation_notes->height = '150px';
        $adaptation_notes->setBasicToolBar();
        $objTable->addCell($adaptation_notes->show());
        $objTable->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_adaptation_heading_step1', 'oer'));
        $fieldset->addContent($objTable->show());


        $action = "saveadaptationstep1";
        $formData = new form('adaptationForm1', $this->uri(array("action" => $action, "id" => $id, "mode" => $mode)));
        $formData->addToForm($fieldset);

        $button = new button('save', $this->objLanguage->languageText('word_next', 'system', 'Next'));
        $button->setToSubmit();
        $formData->addToForm('<br/>' . $button->show());


        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $uri = $this->uri(array("action" => "adaptationlist"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "original_product_title";
        $header->str = $product['title'];


        return $header->show() . $formData->show();
    }

    /**
     * Function constructs form for managing adapatations - step 2
     *
     * @param string $id adaptation id
     * @return form
     */
    public function buildAdaptationFormStep2($id) {

        $objTable = $this->getObject('htmltable', 'htmlelements');

        if ($id != null) {
            //Check if adaptation has data
            $adaptation = $this->dbproducts->getProduct($id);
            if (empty($adaptation['description']) && empty($adaptation['abstract']) && empty($adaptation['provenonce'])) {
                $product = $this->dbproducts->getParentData($id);
            } else {
                $product = $adaptation;
            }

            $hidId = new hiddeninput('id');
            $hidId->cssId = "id";
            $hidId->value = $id;
            $objTable->startRow();
            $objTable->addCell($hidId->show());
            $objTable->endRow();
        }
        //translation
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_translationof', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $translation = new dropdown('translation');
        $translation->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $translation->addOption('none', $this->objLanguage->languageText('mod_oer_none', 'oer'));

        $originalProducts = $this->dbproducts->getOriginalProducts();
        foreach ($originalProducts as $originalProduct) {
            if ($originalProduct['id'] != $id) {
                $translation->addOption($originalProduct['id'], $originalProduct['title']);
            }
        }

        if ($product != null) {
            $translation->selected = $product['translation_of'];
        }
        $objTable->addCell($translation->show());
        $objTable->endRow();


        //description
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_description', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $description = $this->newObject('htmlarea', 'htmlelements');
        $description->name = 'description';
        if ($product != null) {
            $description->value = $product['description'];
        }
        $description->height = '150px';
        $description->setBasicToolBar();
        $objTable->addCell($description->show());
        $objTable->endRow();


        //abstract
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_abstract', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $abstract = $this->newObject('htmlarea', 'htmlelements');
        $abstract->name = 'abstract';
        $abstract->height = '150px';
        if ($product != null) {
            $abstract->value = $product['abstract'];
        }
        $abstract->setBasicToolBar();
        $objTable->addCell($abstract->show());
        $objTable->endRow();


        //provenonce
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_provenonce', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $provenonce = $this->newObject('htmlarea', 'htmlelements');
        $provenonce->name = 'provenonce';
        $provenonce->height = '150px';
        if ($product != null) {
            $provenonce->value = $product['provenonce'];
        }
        $provenonce->setBasicToolBar();
        $objTable->addCell($provenonce->show());
        $objTable->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_adaptation_heading_step2', 'oer'));
        $fieldset->addContent($objTable->show());

        $formData = new form('adaptationForm2', $this->uri(array("action" => "saveadaptationstep2")));
        $formData->addToForm($fieldset);

        $button = new button('save', $this->objLanguage->languageText('word_next', 'system', 'Next'));
        $button->setToSubmit();
        $formData->addToForm('<br/>' . $button->show());


        $button = new button('back', $this->objLanguage->languageText('word_back'));
        $uri = $this->uri(array("action" => "editadaptationstep1", "id" => $id, "mode" => "edit"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $uri = $this->uri(array("action" => "adaptationlist"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "original_product_title";
        $header->str = $product['title'];


        return $header->show() . $formData->show();
    }

    /**
     * Builds the step 3 manage adaptation form
     * 
     * @param type $id
     * @return form
     */
    public function buildAdaptationFormStep3($id) {

        $objTable = $this->getObject('htmltable', 'htmlelements');
        if ($id != null) {
            //Check if adaptation has data
            $adaptation = $this->dbproducts->getProduct($id);
            if (empty($adaptation['accreditation_date']) &&
                    empty($adaptation['contacts']) &&
                    empty($adaptation['relation_type']) &&
                    empty($adaptation['relation']) &&
                    empty($adaptation['coverage']) &&
                    empty($adaptation['status']) &&
                    empty($adaptation['rights'])) {
                $product = $this->dbproducts->getParentData($id);
            } else {
                $product = $adaptation;
            }

            $hidId = new hiddeninput('id');
            $hidId->cssId = "id";
            $hidId->value = $id;
            $objTable->startRow();
            $objTable->addCell($hidId->show());
            $objTable->endRow();
        }

        //resource type
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_oerresource', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $oerresource = new dropdown('oerresource');
        $oerresource->cssClass = 'required';
        $oerresource->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $oerresource->addOption('curriculum', $this->objLanguage->languageText('mod_oer_curriculum', 'oer'));
        if ($product != null) {
            $oerresource->selected = $product['oerresource'];
        }
        $objTable->addCell($oerresource->show());
        $objTable->endRow();

        //licence
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_licence', 'oer'));
        $objTable->endRow();

        $objDisplayLicense = $this->getObject('licensechooserdropdown', 'creativecommons');

        $license = $product['rights'] == '' ? 'copyright' : $product['rights'];
        $rightCell = $objDisplayLicense->show($license);

        $objTable->startRow();
        $objTable->addCell($rightCell);
        $objTable->endRow();



        //needs accredited
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_accredited', 'oer') . '?');
        $objTable->endRow();

        $radio = new radio('accredited');
        $radio->addOption('yes', $this->objLanguage->languageText('word_yes', 'system'));
        $radio->addOption('no', $this->objLanguage->languageText('word_no', 'system'));
        if ($product != null) {
            $radio->selected = $product['accredited'];
        }
        $objTable->startRow();
        $objTable->addCell($radio->show());
        $objTable->endRow();

        //accreditationbody
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_accreditationbody', 'oer'));
        $objTable->endRow();

        $objTable->startRow();

        $textinput = new textinput('accreditationbody');
        $textinput->size = 60;
        if ($product != null) {
            $textinput->value = $product['accreditation_body'];
        }


        $objTable->addCell($textinput->show());
        $objTable->endRow();

        //accreditationdate
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_accreditationdate', 'oer'));
        $objTable->endRow();

        $objTable->startRow();

        $textinput = new textinput('accreditationdate');
        $textinput->size = 60;
        if ($product != null) {
            $textinput->value = $product['accreditation_date'];
        }

        $objTable->addCell($textinput->show());
        $objTable->endRow();

        //contacts
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_contacts', 'oer'));
        $objTable->endRow();

        $objTable->startRow();

        $textarea = new textarea('contacts', '', 5, 55);
        if ($product != null) {
            $textarea->value = $product['contacts'];
        }
        $objTable->addCell($textarea->show());
        $objTable->endRow();


        //relationtype
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_relationtype', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $relationtype = new dropdown('relationtype');
        $relationtype->addOption('select', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $relationtype->addOption('ispartof', $this->objLanguage->languageText('mod_oer_ispartof', 'oer'));
        $relationtype->addOption('requires', $this->objLanguage->languageText('mod_oer_requires', 'oer'));
        $relationtype->addOption('isrequiredby', $this->objLanguage->languageText('mod_oer_isrequiredby', 'oer'));
        $relationtype->addOption('haspartof', $this->objLanguage->languageText('mod_oer_haspartof', 'oer'));
        $relationtype->addOption('references', $this->objLanguage->languageText('mod_oer_references', 'oer'));
        $relationtype->addOption('isversionof', $this->objLanguage->languageText('mod_oer_isversionof', 'oer'));
        if ($product != null) {
            $relationtype->selected = $product['relation_type'];
        }
        $objTable->addCell($relationtype->show());
        $objTable->endRow();

        //relatedproduct
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_relatedproduct', 'oer'));
        $objTable->endRow();
        $objTable->startRow();
        $relatedproduct = new dropdown('relatedproduct');
        $relatedproduct->addOption('none', $this->objLanguage->languageText('mod_oer_none', 'oer'));

        $originalProducts = $this->dbproducts->getOriginalProducts();
        foreach ($originalProducts as $originalProduct) {
            if ($originalProduct['id'] != $id) {
                $relatedproduct->addOption($originalProduct['id'], $originalProduct['title']);
            }
        }
        if ($product != null) {
            $relatedproduct->selected = $product['relation'];
        }

        $objTable->addCell($relatedproduct->show());
        $objTable->endRow();


        //coverage
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_coverage', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $textarea = new textarea('coverage', '', 5, 55);
        if ($product != null) {
            $textarea->value = $product['coverage'];
        }
        $objTable->addCell($textarea->show());
        $objTable->endRow();

        //published status
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_published', 'oer'));
        $objTable->endRow();
        $objTable->startRow();
        $published = new dropdown('status');
        $published->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $published->cssClass = "required";
        $published->addOption('disabled', $this->objLanguage->languageText('mod_oer_disabled', 'oer'));
        $published->addOption('draft', $this->objLanguage->languageText('mod_oer_draft', 'oer'));
        $published->addOption('published', $this->objLanguage->languageText('mod_oer_published', 'oer'));
        if ($product != null) {
            $published->selected = $product['status'];
        }
        $objTable->addCell($published->show());
        $objTable->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_adaptation_heading_step3', 'oer'));
        $fieldset->addContent($objTable->show());

        $formData = new form('adaptationForm3', $this->uri(array("action" => "saveadaptationstep3")));
        $formData->addToForm($fieldset);

        $button = new button('save', $this->objLanguage->languageText('word_next', 'system', 'Next'));
        $button->setToSubmit();
        $formData->addToForm('<br/>' . $button->show());


        $button = new button('back', $this->objLanguage->languageText('word_back'));
        $uri = $this->uri(array("action" => "editadaptationstep2", "id" => $id));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $uri = $this->uri(array("action" => "adaptationlist"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "original_product_title";
        $header->str = $product['title'];


        return $header->show() . $formData->show();
    }

    /**
     * Builds the step 4 manage adaptation form
     *
     * @param string $id adaptation id
     * @return form
     */
    public function buildAdaptationFormStep4($id) {
        $this->addStep4JS();
        $this->loadClass('link', 'htmlelements');
        $objLanguage = $this->getObject('language', 'language');
        $content = $objLanguage->languageText('mod_oer_updateproductpicture', 'oer');
        $this->loadClass('iframe', 'htmlelements');
        $objAjaxUpload = $this->newObject('ajaxuploader', 'oer');


        $objForm = $this->newObject('form', 'htmlelements');
        $objForm->name = "form1";
        $objForm->action = $this->uri(array('action' => 'saveadaptationstep4', "id" => $id));

        // Create the selectbox object
        $objSelectBox = $this->newObject('selectbox', 'htmlelements');
        // Initialise the selectbox.
        $objSelectBox->create($objForm, 'availableThemes[]', $this->objLanguage->languageText('mod_oer_availablethemes', 'oer'), 'selectedThemes[]', $this->objLanguage->languageText('mod_oer_selectedthemes', 'oer'));

        // Populate the selectboxes
        $objDbTheme = $this->getObject("dbthemes", "oer");
        $objData = $objDbTheme->getThemesFormatted();
        $objSelectBox->insertLeftOptions($objData, 'id', 'theme');

        $product = $this->dbproducts->getProduct($id);

        $selectedThemes = array();
        $existingThemesIds = explode(",", $product['themes']);
        foreach ($existingThemesIds as $existingThemesId) {
            $selectedThemes[] = $objDbTheme->getTheme($existingThemesId);
        }

        $objSelectBox->insertRightOptions($selectedThemes, 'id', 'theme');

        // Insert the selectbox into the form object.
        $objForm->addToForm($objSelectBox->show());

        $objForm->addToForm('<br/><div id="save_results"></div>');


        // Get and insert the save and cancel form buttons
        $arrFormButtons = $objSelectBox->getFormButtons();
        $objForm->addToForm(implode(' / ', $arrFormButtons));

        $content.= $objAjaxUpload->show($id, 'uploadproductthumbnail');
        $link = new link($this->uri(array("")));
        $link->link = $objLanguage->languageText('word_home', 'system');


        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "original_product_title";
        $header->str = $product['title'] . '-' . $this->objLanguage->languageText('mod_oer_step4', 'oer');

        return $header->show() . $content . '<br/>' . $objForm->show();
    }

    /**
     * This includes necessary js for product 4 creation
     */
    function addStep4JS() {
        $this->appendArrayVar('headerParams', "

<script type=\"text/javascript\">
    //<![CDATA[

    function loadAjaxForm(fileid) {
        window.setTimeout('loadForm(\"'+fileid+'\");', 1000);
    }

    function loadForm(fileid) {
        var pars = \"module=oer&action=ajaxprocess&id=\"+fileid;
        new Ajax.Request('index.php',{
            method:'get',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || \"no response text\";
                $('updateform').innerHTML = response;
            },
            onFailure: function(transport){
                var response = transport.responseText || \"no response text\";
                //alert('Could not download module: '+response);
            }
        });
    }

    function processConversions() {
        window.setTimeout('doConversion();', 2000);
    }

    function doConversion() {

        var pars = \"module=oer&action=ajaxprocessconversions\";
        new Ajax.Request('index.php',{
            method:'get',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || \"no response text\";
                //alert(response);
            },
            onFailure: function(transport){
                var response = transport.responseText || \"no response text\";
                //alert('Could not download module: '+response);
            }
        });
    }
    //]]>
</script>            
");
    }

    /**
     * Function constructs a list of current adaptations
     * @return form
     */
    public function getAdaptationsListing($mode, $filter) {
        $pageSize = $this->getParam("pagesize", "15");
        // Set up the page navigation.
        $page = $this->getParam('page', 1);
        if ($filter == null) {
            $filter = "";
        }
        $count = $this->dbproducts->getOriginalProductCount($filter);
        $pages = ceil($count / $pageSize);
        // Set up the sql elements.
        $start = (($page) * $pageSize);
        if ($start < 0) {
            $start = 0;
        }

        $productId = $this->getParam('productid', Null);
        //Get adapted products, if productId not null, fetch for that product only
        if ($productId != Null) {
            $productAdaptations = $this->dbproducts->getProductAdaptations($start, $pageSize, $productId, $filter);
        } else {
            $productAdaptations = $this->dbproducts->getAdaptedProducts($start, $pageSize, $filter);
        }

        $controlBand =
                '<div id="originalproducts_controlband">';

        $controlBand.='<br/>&nbsp;' . $this->objLanguage->languageText('mod_oer_viewas', 'oer') . ': ';
        $gridthumbnail = '<img src="skins/oeru/images/sort-by-grid.png"/>';
        $gridlink = new link($this->uri(array("action" => "adaptationlist", "mode" => "grid")));
        $gridlink->link = $gridthumbnail . '&nbsp;' . $this->objLanguage->languageText('mod_oer_grid', 'oer');
        if ($mode == 'grid') {
            $gridlink->cssClass = 'highlight_grid';
        }
        $controlBand.=$gridlink->show();

        $listthumbnail = '&nbsp;|&nbsp;<img src="skins/oeru/images/sort-by-list.png"/>';
        $listlink = new link($this->uri(array("action" => "adaptationlist", "mode" => "list")));
        if ($mode == 'list') {
            $listthumbnail = "";
            $listlink->cssClass = 'highlight_list';
        }
        $listlink->link = $listthumbnail . '&nbsp;' . $this->objLanguage->languageText('mod_oer_list', 'oer');

        $controlBand.='&nbsp;' . $listlink->show();

        $sortbydropdown = new dropdown('sortby');
        $sortbydropdown->addOption('', $this->objLanguage->languageText('mod_oer_none', 'oer'));

        $controlBand.='<br/><br/>' . $this->objLanguage->languageText('mod_oer_sortby', 'oer');
        $controlBand.=$sortbydropdown->show();

        $controlBand.= '</div> ';
        $startNewRow = TRUE;
        $count = 2;
        $table = $this->getObject('htmltable', 'htmlelements');
        $table->cellspacing = 10;
        $table->cellpadding = 10;
        $table->attributes = "style='table-layout:fixed;'";
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getId("ProductCreators");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $userId = $this->objUser->userId();

        $maxCol = 2;
        if ($mode == 'list') {
            $maxCol = 1;
        }
        foreach ($productAdaptations as $adaptation) {
            if ($startNewRow) {
                $startNewRow = FALSE;
                $table->startRow();
            }
            //Get parent product related data(institution, institution type)
            $parentData = $this->dbproducts->getProduct($adaptation['parent_id']);
            $institutionData = $this->dbInstitution->getInstitutionById($adaptation['institutionid']);

            //Get institution type

            $thumbnail = '<img src="usrfiles/' . $institutionData['thumbnail'] . '"  width="45" height="49"  align="left"/>';
            if ($institutionData['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg" width="45" height="49"  align="left"/>';
            }

            if ($mode == 'list') {
                $thumbnail = '';
            }
            $instName = $institutionData['name'];
            $instNameLink = new link($this->uri(array("action" => "viewinstitution", "id" => $adaptation['institutionid'])));
            $instNameLink->link = $instName;
            $instNameLink->cssClass = "viewinstitutionlink";
            $instNameLk = $thumbnail . $instNameLink->show();

            $institutionTypeName = $this->dbInstitutionType->getInstitutionTypeName($institutionData['type']);
            $thumbnail = '<img src="usrfiles/' . $adaptation['thumbnail'] . '"  width="79" height="101" align="bottom"/>';
            if ($adaptation['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/documentdefault.png"  width="79" height="101" align="bottom"/>';
            }
            if ($mode == 'list') {
                $thumbnail = '';
            }
            $makeAdaptation = "";
            if ($objGroupOps->isGroupMember($groupId, $userId)) {
                $adaptImg = '<img src="skins/oeru/images/icons/add.png">';
                $adaptLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $adaptation['id'], "mode" => "new")));
                $adaptLink->link = $adaptImg;
                $makeAdaptation = $adaptLink->show();
            }
            //Manage links
            $mnglinks = "";
            if ($objGroupOps->isGroupMember($groupId, $userId)) {
                $editImg = '<img src="skins/oeru/images/icons/edit.png">';
                $deleteImg = '<img src="skins/oeru/images/icons/delete.png">';
                $adaptImg = '<img src="skins/oeru/images/icons/add.png">';

                $adaptLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $adaptation['id'], "mode" => "new")));
                $adaptLink->link = $adaptImg;
                $adaptLink->extra = 'alt="' . $this->objLanguage->languageText('mod_oer_makeadaptation', "oer", "Create an adaptation") . '"';
                $adaptLink->title = $this->objLanguage->languageText('mod_oer_createadaptation', "oer", "Create an adaptation");
                $mnglinks.=  $adaptLink->show();

                $editLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $adaptation['id'], "mode" => "edit")));
                $editLink->link = $editImg;
                $editLink->extra = 'alt="' . $this->objLanguage->languageText('word_edit', "system", "Edit") . '"';
                $editLink->title = $this->objLanguage->languageText('word_edit', "system", "Edit");
               // $mnglinks.="&nbsp;" . $editLink->show();

                $delLink = new link($this->uri(array("action" => "deleteadaptation", "id" => $adaptation['id'])));
                $delLink->link = $deleteImg;
                $delLink->cssClass = "confirmdeleteadaptation";
                $delLink->extra = 'alt="' . $this->objLanguage->languageText('word_delete', 'system') . '"';
                $delLink->title = $this->objLanguage->languageText('word_delete', 'system');
               // $mnglinks.="&nbsp;" . $delLink->show() . "<br />";
            }

            $link = new link($this->uri(array("action" => "viewadaptation", "id" => $adaptation['id'])));
            $link->link = $thumbnail; // . $makeAdaptation;
            $product = $link->show();

            $link->link = "<div id='adaptationtitle'>" . $adaptation['title'] .$mnglinks. "</div>";
            $link->cssClass = 'adaptation_listing_title';
            $product.= $link->show();
         
            $product.= "<br/><div id='adaptationtitle'>" . $this->objLanguage->languageText('mod_oer_adaptedby', 'oer') . ":</div><br/>";
            $product.= "<div id='institutionva'>" . $instNameLk . "</div>";
            $product.= "<div id='institutiontype'>" . $institutionTypeName . " | " . $institutionData['country'] . "</div>";

            $adaptionsCount = $this->dbproducts->getProductAdaptationCount($adaptation['id']);
            $adaptationsLink = new link($this->uri(array("action" => "adaptationlist", "id" => $adaptation['id'])));
            $adaptationsLink->link = $adaptionsCount . '&nbsp;' . $this->objLanguage->languageText('mod_oer_adaptationscount', 'oer');
            $adaptationsLink->cssClass = 'adaptationcount';
            $product.="<br/>" . $adaptationsLink->show();

            $table->addCell($product, null, null, null, "view_original_product");
            if ($count > $maxCol) {
                $table->endRow();
                $startNewRow = TRUE;
                $count = 1;
            }
            $count++;
        }
        return $controlBand . $table->show();
    }

    /**
     * Function constructs a paginated list of the products
     * @param string $mode add/edit
     * @param string $filter
     * @return form
     */
    function getAdaptationListingPaginated($mode, $filterOptions, $filter='') {
        $pages = 0;

        $options = explode("!", $filterOptions);
        $pageSize = 15;
        foreach ($options as $option) {
            $optionArray = explode("=", $option);
            if ($optionArray[0] == 'itemsperpage') {
                $pageSize = $optionArray[1];
            }
        }

        $objPagination = $this->newObject('pagination', 'navigation');
        $objPagination->module = 'oer';
        $objPagination->action = 'adaptationlistajax';
        $objPagination->extra = array("mode" => $mode, "filter" => $filter, "pagesize" => $pageSize);
        $objPagination->id = 'productlist_div';
        $objDb = $this->getObject('dbproducts', 'oer');
        $objPagination->currentPage = 0;
        $count = $objDb->getAdaptationCount($filter);
        $pages = ceil($count / $pageSize);
        $objPagination->numPageLinks = $pages;
        return $objPagination->show();
    }

    /**
     * Function creates side navigation links for moving in between forms when managing
     * an adaptation
     *
     * @param string $id adaptation id
     * @param string $step manage adaptation step
     * @return form
     */
    function buildAdaptationStepsNav($id, $step) {

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "build_product_steps_nav";
        $header->str = $this->objLanguage->languageText('mod_oer_jumpto', 'oer');


        $adaptation = $this->dbproducts->getProduct($id);
        $thumbnail = '<img src="usrfiles/' . $adaptation['thumbnail'] . '"  width="79" height="101" align="bottom"/>';
        if ($adaptation['thumbnail'] == '') {
            $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="79" height="101" align="bottom"/>';
        }

        $viewProductLink = new link($this->uri(array("action" => "viewadaptation", "id" => $id)));
        $viewProductLink->link = $thumbnail;
        $content = $viewProductLink->show();
        $content .= $header->show();
        $content.='<ul id="nav-secondary">';

        $class = "";
        $link = new link($this->uri(array("action" => "editadaptationstep1", "id" => $id)));
        $link->link = $this->objLanguage->languageText('mod_oer_step1', 'oer');

        if ($step == '1') {
            $class = "current";
        } else {
            $class = "";
        }
        $content.='<li class="' . $class . '">' . $link->show() . '</li>';


        $link = new link($this->uri(array("action" => "editadaptationstep2", "id" => $id)));
        $link->link = $this->objLanguage->languageText('mod_oer_step2', 'oer');

        if ($step == '2') {
            $class = "current";
        } else {
            $class = "";
        }
        $content.='<li class="' . $class . '">' . $link->show() . '</li>';

        $link = new link($this->uri(array("action" => "editadaptationstep3", "id" => $id)));
        $link->link = $this->objLanguage->languageText('mod_oer_step3', 'oer');

        if ($step == '3') {
            $class = "current";
        } else {
            $class = "";
        }
        $content.='<li class="' . $class . '">' . $link->show() . '</li>';
        $link = new link($this->uri(array("action" => "editadaptationstep4", "id" => $id)));
        $link->link = $this->objLanguage->languageText('mod_oer_step4', 'oer');

        if ($step == '4') {
            $class = "current";
        } else {
            $class = "";
        }
        $content.='<li class="' . $class . '">' . $link->show() . '</li>';

        $content.="</ul>";


        return $content;
    }

    /**
     * Check if user is logged in
     * @return boolean
     */
    function userIsLoggedIn() {
        $hasPerms = false;
        if ($this->objUser->isLoggedIn()) {
            $hasPerms = true;
        }
        return $hasPerms;
    }

    /**
     * Check if user is authorised
     * @return boolean
     */
    function userHasPermissions() {
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUser = $this->getObject("user", "security");
        //Set groupId for site managers
        $groupId = $objGroups->getId("ProductCreators");
        //Get userId
        $userId = $this->objUser->userId();
        //Flag to check if user has perms to manage adaptations
        $hasPerms = false;
        if ($this->objUser->isLoggedIn()) {
            if ($objGroupOps->isGroupMember($groupId, $userId)) {
                $hasPerms = true;
            }
        }
        return $hasPerms;
    }

}

?>