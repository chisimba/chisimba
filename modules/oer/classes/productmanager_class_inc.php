<?php

/**
 * Contains util methods for managing product
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
 * @author     Davidwadf davidwaf@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author davidwaf
 */
class productmanager extends object {

    private $dbproducts;
    private $objLanguage;
    public $objConfig;
    private $objUser;

    /**
     * this function is called whenever a new object of this class is created
     * we initialize necessary things here, for later use i the code
     */
    function init() {
        $this->dbproducts = $this->getObject('dbproducts', 'oer');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject("user", "security");
        $this->permissionsManager = $this->getObject("permissionsmanager", "oer");
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');
        $this->loadCSSandJS();
        $this->setupLanguageItems();
    }

    /**
     * Step 1 details of a new product are saved. It is assumed that these
     * have already been validaded already
     * @return type 
     */
    function saveNewProductStep1() {

        $data = array(
            "title" => $this->getParam("title"),
            "alternative_title" => $this->getParam("alternative_title"),
            "author" => $this->getParam("author"),
            "othercontributors" => $this->getParam("othercontributors"),
            "publisher" => $this->getParam("publisher"),
            "language" => $this->getParam("language"),
            "keywords" => $this->getParam("keywords"),
            "translation_of" => "",
            "description" => "",
            "abstract" => "",
            "oerresource" => "",
            "provenonce" => "",
            "accredited" => "",
            "accreditation_body" => "",
            "accreditation_date" => "",
            "contacts" => "",
            "relation_type" => "",
            "relation" => "",
            "coverage" => "",
            "status" => "",
        );

        return $this->dbproducts->saveOriginalProduct($data);
    }

    /**
     * Step 1 details of an existing product are updated. It is assumed that these
     * have already been validaded already
     * @return type 
     */
    function updateProductStep1() {
        $id = $this->getParam("id");
        $data = array(
            "title" => $this->getParam("title"),
            "alternative_title" => $this->getParam("alternative_title"),
            "author" => $this->getParam("author"),
            "othercontributors" => $this->getParam("othercontributors"),
            "publisher" => $this->getParam("publisher"),
            "language" => $this->getParam("language"),
            "keywords" => $this->getParam("keywords")
        );

        $this->dbproducts->updateOriginalProduct($data, $id);
        return $id;
    }

    /**
     * used for deleting an original product. It is assummed that the delete
     * confirmation has already been done
     */
    function deleteOriginalProduct() {
        $id = $this->getParam("id");
        $this->dbproducts->deleteOriginalProduct($id);
    }

    /**
     * Updates the product's step 2 details
     * @return type 
     */
    function updateProductStep2() {
        $id = $this->getParam("id");
        $data = array(
            "description" => $this->getParam("description"),
            "abstract" => $this->getParam("abstract"),
            "provenonce" => $this->getParam("provenonce"),
        );

        $this->dbproducts->updateOriginalProduct($data, $id);
        return $id;
    }

    /**
     * Updates the product's step 3 details
     * @return type 
     */
    function updateProductStep3() {
        $id = $this->getParam("id");
        $data = array(
            "accreditation_date" => $this->getParam("accreditationdate"),
            "accreditation_body" => $this->getParam("accreditation_body"),
            "contacts" => $this->getParam("contacts"),
            "relation_type" => $this->getParam("relationtype"),
            "relation" => $this->getParam("relatedproduct"),
            "coverage" => $this->getParam("coverage"),
            "oerresource" => $this->getParam("oerresource"),
            "accredited" => $this->getParam("accredited"),
            "status" => $this->getParam("status"),
            "rights" => $this->getParam("creativecommons")
        );

        $this->dbproducts->updateOriginalProduct($data, $id);
        return $id;
    }

    /**
     * Updates the product's step 4 details
     * @return type 
     */
    function updateProductStep4() {
        $id = $this->getParam("id");
        $selectedThemes = $this->getParam("selectedThemes");
        $themesStr = '';
        foreach ($selectedThemes as $theme) {
            $themesStr.=$theme . ',';
        }

        $data = array(
            "themes" => $themesStr,
        );

        $this->dbproducts->updateOriginalProduct($data, $id);


        return $id;
    }

    /**
     * Used fo uploading product thumbnail
     * @todo this will be renamed to a meaningful name
     */
    function doajaxupload() {
        $dir = $this->objConfig->getcontentBasePath();

        $generatedid = $this->getParam('id');
        $filename = $this->getParam('filename');

        $objMkDir = $this->getObject('mkdir', 'files');

        $productid = $this->getParam('itemid');
        $destinationDir = $dir . '/oer/products/' . $productid;

        $objMkDir->mkdirs($destinationDir);
        // @chmod($destinationDir, 0777);

        $objUpload = $this->newObject('upload', 'files');
        $objUpload->permittedTypes = array(
            'all'
        );
        $objUpload->overWrite = TRUE;
        $objUpload->uploadFolder = $destinationDir . '/';

        $result = $objUpload->doUpload(TRUE, $filename);


        if ($result['success'] == FALSE) {

            $filename = isset($_FILES['fileupload']['name']) ? $_FILES['fileupload']['name'] : '';
            $error = $this->objLanguage->languageText('mod_oer_uploaderror', 'oer');
            return array('message' => $error, 'file' => $filename, 'id' => $generatedid);
        } else {
            $filename = $result['filename'];
            $data = array("thumbnail" => "/oer/products/" . $productid . "/" . $filename);
            $this->dbproducts->updateOriginalProduct($data, $productid);


            $params = array('action' => 'showthumbnailuploadresults', 'id' => $generatedid, 'fileid' => $id, 'filename' => $filename);

            return $params;
        }
    }

    /**
     * adds essential js and css
     */
    function loadCSSandJS() {
        $uiAllCSS = '<link rel="stylesheet" type="text/css" href="' . $this->getResourceUri('plugins/ui/development-bundle/themes/base/jquery.ui.all.css', 'jquery') . '"/>';
        $this->appendArrayVar('headerParams', $uiAllCSS);
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('1.7.1/jquery-1.7.1.min.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.core.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.widget.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.tabs.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('tabber.js', 'oer'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('expand.js', 'oer'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('originalproduct.js', 'oer'));
    }

    /**
     * sets up necessary lang items for use in js
     */
    function setupLanguageItems() {
        // Serialize language items to Javascript
        $arrayVars['status_success'] = "mod_oer_status_success";
        $arrayVars['status_fail'] = "mod_oer_status_fail";
        $arrayVars['confirm_delete_original_product'] = "mod_oer_confirm_delete_original_product";
        $arrayVars['loading'] = "mod_oer_loading";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oer');
    }

    /**
     * this constructs the  form for creating a new product. First, we check permissions
     * @return type FORM
     */
    public function buildProductFormStep1($id) {

        $objTable = $this->getObject('htmltable', 'htmlelements');
        $product = null;
        if ($id != null) {
            $product = $this->dbproducts->getProduct($id);
            $hidId = new hiddeninput('id');
            $hidId->cssId = "id";
            $hidId->value = $id;
            $objTable->startRow();
            $objTable->addCell($hidId->show());
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


        //language
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_language', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $language = new dropdown('language');
        $language->cssClass = 'required';

        $language->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $language->addOption('en', $this->objLanguage->languageText('mod_oer_english', 'oer'));

        $langs = $this->objLanguage->getLangs();
        $hiddenlangs = $this->getObject("dbhiddenlangs", "langadmin");

        foreach ($langs as $id => $name) {
            if (!$hiddenlangs->isHidden($id)) {
                $language->addOption($id, $name);
            }
        }


        if ($product != null) {
            $language->setSelected($product['language']);
        }

        $objTable->addCell($language->show());
        $objTable->endRow();



        //keywords
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_keywords', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $textarea = new textarea('keywords', '', 5, 55);
        if ($product != null) {
            $textarea->value = $product['keywords'];
        }
        $objTable->addCell($textarea->show());
        $objTable->endRow();


        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_originalproduct_heading_new_step1', 'oer'));
        $fieldset->addContent($objTable->show());


        $action = "saveoriginalproductstep1";
        $jsFunction = "javascript:saveStep1();";
        if ($product != null) {
            $action = "updateoriginalproductstep1";
            $jsFunction = "javascript:updateStep1('" . $id . "');";
        }
        $formData = new form('originalProductForm1', $this->uri(array("action" => $action, "id" => $id)));
        $formData->addToForm($fieldset);

        $formData->addToForm('<br/><div id="save_results"></div>');
        $button = new button('saveStep1Button', $this->objLanguage->languageText('word_save', 'system', 'Save'));
        //  $button->setToSubmit();
        $button->setOnClick($jsFunction);
        $formData->addToForm('<br/>' . $button->show());


        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $uri = $this->uri(array("action" => "home"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $title = $product['title'];
        if ($title == '') {
            $title = $this->objLanguage->languageText('mod_oer_originalproduct_heading_new_step1', 'oer');
        }

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "original_product_title";
        $header->str = $title . '-' . $this->objLanguage->languageText('mod_oer_step1', 'oer');


        return $header->show() . $formData->show();
    }

    /**
     * this build the second form when creating an original product
     * @param type $id
     * @return type 
     */
    public function buildProductFormStep2($id) {

        $objTable = $this->getObject('htmltable', 'htmlelements');
        if ($id != null) {
            $product = $this->dbproducts->getProduct($id);
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

        $translationId = $product['translation_of'];
        $translationTitle = $this->dbproducts->getProduct($translationId);
        $translation->setSelected($translationTitle);

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
        //$description->setBasicToolBar();
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
        //$abstract->setBasicToolBar();
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
        // $provenonce->setBasicToolBar();
        $objTable->addCell($provenonce->show());
        $objTable->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_originalproduct_heading_new_step2', 'oer'));
        $fieldset->addContent($objTable->show());

        $formData = new form('originalProductForm2', $this->uri(array("action" => "saveoriginalproductstep2")));
        $formData->addToForm($fieldset);

        $formData->addToForm('<br/><div id="save_results"></div>');
        $button = new button('saveStep2Button', $this->objLanguage->languageText('word_save', 'system', 'Save'));
        $button->setToSubmit();
        $formData->addToForm('<br/>' . $button->show());


        $button = new button('back', $this->objLanguage->languageText('word_back'));
        $uri = $this->uri(array("action" => "editoriginalproductstep1", "id" => $id));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $uri = $this->uri(array("action" => "home"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "original_product_title";
        $header->str = $product['title'] . '-' . $this->objLanguage->languageText('mod_oer_step2', 'oer');


        return $header->show() . $formData->show();
    }

    /**
     * Builds the step 3 original product form
     * @param type $id 
     */
    public function buildProductFormStep3($id) {

        $objTable = $this->getObject('htmltable', 'htmlelements');
        if ($id != null) {
            $product = $this->dbproducts->getProduct($id);
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

        $oerresource->setSelected($product['oerresource']);

        $objTable->addCell($oerresource->show());
        $objTable->endRow();

        //licence
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_licence', 'oer'));
        $objTable->endRow();

        $objDisplayLicense = $this->getObject('licensechooserdropdown', 'creativecommons');

        $license = $product['rights'] == '' ? 'copyright' : $product['rights'];
        $objDisplayLicense->defaultValue = $license;
        $rightCell = $objDisplayLicense->show();


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
            $radio->setSelected($product['accredited']);
        }
        $objTable->startRow();
        $objTable->addCell($radio->show());
        $objTable->endRow();

        //accreditationbody
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_accreditationbody', 'oer'));
        $objTable->endRow();

        $objTable->startRow();

        $textinput = new textinput('accreditation_body');
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
            $published->setSelected($product['status']);
        }
        $objTable->addCell($published->show());
        $objTable->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_originalproduct_heading_new_step3', 'oer'));
        $fieldset->addContent($objTable->show());

        $formData = new form('originalProductForm3', $this->uri(array("action" => "saveoriginalproductstep3")));
        $formData->addToForm($fieldset);

        $formData->addToForm('<br/><div id="save_results"></div>');
        $button = new button('saveStep3Button', $this->objLanguage->languageText('word_save', 'system', 'Save'), 'javascript:saveStep3();');
        $button->setToSubmit();
        $formData->addToForm('<br/>' . $button->show());


        $button = new button('back', $this->objLanguage->languageText('word_back'));
        $uri = $this->uri(array("action" => "editoriginalproductstep2", "id" => $id));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $uri = $this->uri(array("action" => "home"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "original_product_title";
        $header->str = $product['title'] . '-' . $this->objLanguage->languageText('mod_oer_step3', 'oer');


        return $header->show() . $formData->show();
    }

    /**
     * Builds the step 4 original product form
     * @param type $id 
     */
    public function buildProductFormStep4($id) {
        $this->addStep4JS();
        $this->loadClass('link', 'htmlelements');
        $objLanguage = $this->getObject('language', 'language');
        $content = $objLanguage->languageText('mod_oer_updateproductpicture', 'oer');
        $this->loadClass('iframe', 'htmlelements');
        $objAjaxUpload = $this->newObject('ajaxuploader', 'oer');


        $objForm = $this->newObject('form', 'htmlelements');
        $objForm->name = "form1";
        $objForm->action = $this->uri(array('action' => 'saveoriginalproductstep4', "id" => $id));

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
     * creates a table and returns the list of current products. This method is actually
     * what constructs the first page, but is not called directly. Rather, the paginator
     * calls it via ajax, depending on the  page the user is navigating to
     * @return type 
     */
    public function getOriginalProductListing($mode, $filter='') {
        $pageSize = $this->getParam("pagesize", "15");
        // Set up the page navigation.
        $page = $this->getParam('page', 1);

        $count = $this->dbproducts->getOriginalProductCount($filter);
        $pages = ceil($count / $pageSize);
        // Set up the sql elements.
        $start = (($page) * $pageSize);
        if ($start < 0) {
            $start = 0;
        }

        $originalProducts = $this->dbproducts->getOriginalProducts($filter, $start, $pageSize);
        $newproductlink = new link($this->uri(array("action" => "newproductstep1")));
        $newproductlink->link = $this->objLanguage->languageText('mod_oer_newproduct', 'oer');
        $controlBand =
                '<div id="originalproducts_controlband">';

        $controlBand.='<br/>&nbsp;' . $this->objLanguage->languageText('mod_oer_viewas', 'oer') . ': ';
        $gridthumbnail = '<img src="skins/oeru/images/sort-by-grid.png"/>';
        $gridlink = new link($this->uri(array("action" => "home")));
        $gridlink->link = $gridthumbnail . '&nbsp;' . $this->objLanguage->languageText('mod_oer_grid', 'oer');
        if ($mode == 'grid') {
            $gridlink->cssClass = 'highlight_grid';
        }
        $controlBand.=$gridlink->show();


        $listthumbnail = '&nbsp;|&nbsp;<img src="skins/oeru/images/sort-by-list.png"/>';
        $listlink = new link($this->uri(array("action" => "showproductlistingaslist")));
        $listlink->link = $listthumbnail . '&nbsp;' . $this->objLanguage->languageText('mod_oer_list', 'oer');
        if ($mode == 'list') {
            $listlink->cssClass = 'highlight_list';
        }


        $controlBand.='&nbsp;' . $listlink->show();

        if ($this->permissionsManager->isEditor()) {
            $newthumbnail = '&nbsp;<img src="skins/oeru/images/document-new.png" width="19" height="15"/>';
            $controlBand.= '&nbsp;|&nbsp;' . $newthumbnail . $newproductlink->show();
        }


        $sortbydropdown = new dropdown('sortby');
        $sortbydropdown->addOption('', $this->objLanguage->languageText('mod_oer_none', 'oer'));

        $controlBand.='<br/><br/>' . $this->objLanguage->languageText('mod_oer_sortby', 'oer');
        $controlBand.=$sortbydropdown->show();

        $controlBand.= '</div> ';
        $startNewRow = TRUE;
        $count = 1;
        $table = $this->getObject('htmltable', 'htmlelements');
        $table->attributes = "style='table-layout:fixed;'";
        $table->cellspacing = 10;
        $table->cellpadding = 10;

        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getId("ProductCreators");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $userId = $this->objUser->userId();
        $maxCol = 3;
        if ($mode == 'list') {
            $maxCol = 1;
        }
        foreach ($originalProducts as $originalProduct) {
            if ($startNewRow) {
                $startNewRow = FALSE;
                $table->startRow();
            }


            $titleLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $originalProduct['id'], 'module' => 'oer', "mode" => $mode, "id" => $originalProduct['id'])));
            $titleLink->cssClass = 'original_product_listing_title';
            $titleLink->link = $originalProduct['title'];
            $product = $titleLink->show();

            if ($mode == 'grid') {
                $thumbnail = '<img src="usrfiles/' . $originalProduct['thumbnail'] . '"  width="79" height="101" align="bottom"/>';
                if ($originalProduct['thumbnail'] == '') {
                    $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="79" height="101" align="bottom"/>';
                }
                $makeAdaptation = "";
                if ($objGroupOps->isGroupMember($groupId, $userId)) {
                    $adaptImg = '<img src="skins/oeru/images/icons/add.png">';
                    $adaptLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $originalProduct['id'], "mode" => "new")));
                    $adaptLink->link = $adaptImg;
                    $adaptLink->extra = 'alt="' . $this->objLanguage->languageText('mod_oer_makeadaptation', "oer", "Create an adaptation") . '"';
                    $adaptLink->title = $this->objLanguage->languageText('mod_oer_createadaptation', "oer", "Create an adaptation");
                    $makeAdaptation = $adaptLink->show();
                }

                $thumbnailLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $originalProduct['id'], 'module' => 'oer', "id" => $originalProduct['id'], "mode" => $mode)));
                $thumbnailLink->link = $thumbnail . $makeAdaptation . '<br/>';
                $thumbnailLink->cssClass = 'original_product_listing_thumbail';


                $product = $thumbnailLink->show() . $titleLink->show();
            }

            $languageField = new dropdown('lagnguage');
            $languageCode = $this->getObject("languagecode", "language");
            $languageField->addOption($originalProduct['language'], $languageCode->getLanguage($originalProduct['language']));
         

            $languageField->cssClass = "product_language_dd";
            $product.='<br/><br/><h7>' . $this->objLanguage->languageText('mod_oer_languages', 'oer') . ':</h7>' . $languageField->show();

            $adaptionsCount = $this->dbproducts->getProductAdaptationCount($originalProduct['id']);
            $adaptationsLink = new link($this->uri(array("action" => "adaptationlist", "productid" => $originalProduct['id'])));
            $adaptationsLink->link = $adaptionsCount . '&nbsp;' . $this->objLanguage->languageText('mod_oer_adaptationscount', 'oer');
            $adaptationsLink->cssClass = 'original_product_listing_adaptation_count';
            $product.=$adaptationsLink->show();

            //addCell($str, $width=null, $valign="top", $align=null, $class=null, $attrib=Null,$border = '0')
            $table->addCell($product, null, "top", "left", "view_original_product");

            if ($count == $maxCol) {

                $table->endRow();
                $startNewRow = TRUE;
                $count = 1;
            }
            $count++;
        }

        $totalProducts = count($originalProducts);
        $reminder = $totalProducts % $maxCol;

        if ($reminder != 0) {

            $table->endRow();
        }

        $expandCollapseJS = '<script type="text/javascript" src="packages/oer/resources/expand.js"></script>';
        return $expandCollapseJS . $controlBand . $table->show();
    }

    /**
     * this returns a paginated list of the products
     * @param type $mode
     * @param type $filter
     * @return type 
     */
    function getOriginalProductListingPaginated($mode, $filterOptions, $filter='') {
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
        $objPagination->action = 'originalproductlistajax';
        $objPagination->extra = array("mode" => $mode, "filter" => $filter, "pagesize" => $pageSize);
        $objPagination->id = 'productlist_div';
        $objDb = $this->getObject('dbproducts', 'oer');
        $objPagination->currentPage = 0;
        $count = $objDb->getOriginalProductCount($filter);
        $pages = ceil($count / $pageSize);
        $objPagination->numPageLinks = $pages;
        return $objPagination->show();
    }

    /**
     * Creates side navigation links for moving in between forms when creating
     * a product 
     */
    function buildProductStepsNav($id, $step) {

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "build_product_steps_nav";
        $header->str = $this->objLanguage->languageText('mod_oer_jumpto', 'oer');


        $product = $this->dbproducts->getProduct($id);
        $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="79" height="101" align="bottom"/>';
        if ($product['thumbnail'] == '') {
            $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="79" height="101" align="bottom"/>';
        }

        $viewProductLink = new link($this->uri(array("action" => "vieworiginalproduct", "id" => $id)));
        $viewProductLink->link = $thumbnail;
        $content = $viewProductLink->show();
        $content .= $header->show();
        $content.='<ul id="nav-secondary">';

        $class = "";
        $link = new link($this->uri(array("action" => "editoriginalproductstep1", "id" => $id)));
        $link->link = $this->objLanguage->languageText('mod_oer_step1', 'oer');

        if ($step == '1') {
            $class = "current";
        } else {
            $class = "";
        }
        $content.='<li class="' . $class . '">' . $link->show() . '</li>';


        $link = new link($this->uri(array("action" => "editoriginalproductstep2", "id" => $id)));
        $link->link = $this->objLanguage->languageText('mod_oer_step2', 'oer');

        if ($step == '2') {
            $class = "current";
        } else {
            $class = "";
        }
        $content.='<li class="' . $class . '">' . $link->show() . '</li>';

        $link = new link($this->uri(array("action" => "editoriginalproductstep3", "id" => $id)));
        $link->link = $this->objLanguage->languageText('mod_oer_step3', 'oer');

        if ($step == '3') {
            $class = "current";
        } else {
            $class = "";
        }
        $content.='<li class="' . $class . '">' . $link->show() . '</li>';
        $link = new link($this->uri(array("action" => "editoriginalproductstep4", "id" => $id)));
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
     * this records the rating and return the total values of rating
     */
    function rateProduct() {
        $rateRaw = $this->getParam("rate");
        $rateParts = explode("|", $rateRaw);


        $rateValue = intval($rateParts[0]);
        $productId = $rateParts[1];
        $userId = $rateParts[2];

        $dbProductRating = $this->getObject("dbproductrating", "oer");
        $totalRating = $dbProductRating->getTotalRating($productId);
        $totalRating = $totalRating + $rateValue;

        $data = array(
            'rate' => $rateValue,
            'totalrating' => $totalRating,
            'productid' => $productId,
            'userid' => $userId
        );
        $dbProductRating->addRating($data);
        return $totalRating;
    }

    /**
     * makes a given product featured
     */
    function makefeatured() {
        $prodtype = "adaptation";
        $id = $this->getParam("productid");
        //Check if product is an original
        $isOriginalProduct = $this->dbproducts->isOriginalProduct($id);
        if ($isOriginalProduct) {
            $prodtype = "original";
        }
        $data = array(
            'productid' => $id,
            'prodtype' => $prodtype,
            'status' => "active",
            'featuredon' => date("Y-m-d H:i:s")
        );
        $dbFeaturedProduct = $this->getObject("dbfeaturedproduct", "oer");
        $dbFeaturedProduct->setFeaturedProduct($data);
    }

    /**
     * get the featured product
     * @param string $prodtype whether original or adaptation product
     * @return string contains featured product data
     */
    function getFeaturedProduct($prodtype) {
        $dbFeaturedProduct = $this->getObject("dbfeaturedproduct", "oer");
        $productId = $dbFeaturedProduct->getFeaturedProduct($prodtype);
        $product = $this->dbproducts->getProduct($productId);
        $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="136" height="176" align="left"/>';
        if ($product['thumbnail'] == '') {
            $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="136" height="176" align="left"/>';
        }

        $mode = "";
        if ($prodtype == "original") {
            $thumbnailLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $productId, 'module' => 'oer', "id" => $productId, "mode" => $mode)));
            $thumbnailLink->link = $thumbnail . '<br/>';
            $thumbnailLink->cssClass = 'featuredproduct_thumbnail';

            $titleLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $productId, 'module' => 'oer', "id" => $productId, "mode" => $mode)));
            $titleLink->cssClass = 'original_product_listing_title';
            $titleLink->link = $product['title'];
            $product = $titleLink->show();
        } else {
            $thumbnailLink = new link($this->uri(array("action" => "viewadaptation", 'identifier' => $productId, 'module' => 'oer', "id" => $productId, "mode" => $mode)));
            $thumbnailLink->link = $thumbnail . '<br/>';
            $thumbnailLink->cssClass = 'featuredproduct_thumbnail';

            $titleLink = new link($this->uri(array("action" => "viewadaptation", 'identifier' => $productId, 'module' => 'oer', "id" => $productId, "mode" => $mode)));
            $titleLink->cssClass = 'original_product_listing_title';
            $titleLink->link = $product['title'];
            $product = $titleLink->show();
        }


        $content = '<div id="featuredproduct">';
        $content.='<div id="featuredproduct_thumbnail">' . $thumbnailLink->show() . '</div>';
        $content.='<div id="featuredproduct_title">' . $titleLink->show() . '</div>';

        $adaptionsCount = $this->dbproducts->getProductAdaptationCount($productId);
        $adaptationsLink = new link($this->uri(array("action" => "adaptationlist", "productid" => $productId)));
        $adaptationsLink->link = $adaptionsCount . '&nbsp;' . $this->objLanguage->languageText('mod_oer_adaptationscount', 'oer');
        $adaptationsLink->cssClass = 'original_product_listing_adaptation_count';

        $content.='<div id="featuredproduct_thumbnail">' . $adaptationsLink->show() . '</div>';
        $content.="</div>";
        return $content;
    }

    /**
     * this constructs an adaptation that has been selected as featured
     * @param string $prodtype whether original or adaptation product
     * @return string contains featured product data
     */
    function getFeaturedAdaptation($prodtype) {
        //Load DB objects
        $dbFeaturedProduct = $this->getObject("dbfeaturedproduct", "oer");
        $dbInstitution = $this->getObject("dbinstitution", "oer");
        //Fetch featured product
        $productId = $dbFeaturedProduct->getFeaturedProduct($prodtype);
        //Fetch product data
        $product = $this->dbproducts->getProduct($productId);
        //Get adaptation count
        $adaptationCount = $this->dbproducts->getProductAdaptationCount($productId);
        //Get institution data
        $instData = $dbInstitution->getInstitutionById($product['institutionid']);

        $thumbnail = '<img class="featuredadaptation" src="usrfiles/' . $product['thumbnail'] . '"  width="65" height="85" align="left"/>';
        if ($product['thumbnail'] == '') {
            $thumbnail = '<img  class="featuredadaptation"  src="skins/oeru/images/product-cover-placeholder.jpg"  width="65" height="85" align="left"/>';
        }

        $thumbnailLink = new link($this->uri(array("action" => "viewadaptation", 'identifier' => $productId, "id" => $productId)));
        $thumbnailLink->link = $thumbnail . '<br/>';
        //$thumbnailLink->cssClass = 'featuredproduct_thumbnail';

        $titleLink = new link($this->uri(array("action" => "viewadaptation", 'identifier' => $productId, "id" => $productId)));
        //$titleLink->cssClass = 'featuredproduct_title';
        $titleLink->link = $product['title'];
        $titleLk = $titleLink->show();

        $originalPLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $product['parent_id'], "id" => $product['parent_id'])));
        //$titleLink->cssClass = 'featuredproduct_title';
        $originalPLink->link = $this->objLanguage->languageText('mod_oer_seeoriginalunesco', 'oer');
        $originalPLink = $originalPLink->show();

        $instthumbnail = '<img src="usrfiles/' . $instData['thumbnail'] . '" class="featuredadaptation"  width="65" height="85" align="left"/>';
        if ($instData['thumbnail'] == '') {
            $instthumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg" class="featuredadaptation" width="65" height="85" align="left"/>';
        }

        $instThumbnailLink = new link($this->uri(array("action" => "viewinstitution", 'id' => $instData['id'])));
        $instThumbnailLink->link = $instthumbnail . '<br/>';
        //$instThumbnailLink->cssClass = 'featuredproduct_thumbnail';

        $instTitleLink = new link($this->uri(array("action" => "viewinstitution", 'id' => $instData['id'])));
        $instTitleLink->link = $instData['name'];
        $instTitle = $instTitleLink->show();

        $seeAllAdaptations = $this->objLanguage->languageText('mod_oer_seeall', 'oer') . " " . strtolower($this->objLanguage->languageText('mod_oer_adaptationscount', 'oer'));

        $seeAllAdaptationsLink = new link($this->uri(array("action" => "adaptationlist", 'productid' => $productId)));
        //$seeAllAdaptationsLink->cssClass = 'featuredproduct_thumbnail';
        $seeAllAdaptationsLink->link = $seeAllAdaptations;
        $seeAllAdaptationsLink = $seeAllAdaptationsLink->show();


        $adaptedBy = $this->objLanguage->languageText('mod_oer_adaptedby', 'oer');

        $content = '<div id="featuredadaptation">';
        $content.='<div id="featuredadaptation_thumbnail">' . $thumbnailLink->show() . '</div>';
        $content.='<div id="featuredadaptation_prodtitle">' . $titleLk . '</div><br />';
        $content.='<div id="featuredadaptation_seeall">' . $seeAllAdaptationsLink . " (" . $adaptationCount['count'] . ")" . '</div>';
        $content.='<div id="featuredadaptation_seeall">' . $originalPLink . '</div><br /><br />';
        $content.='<div id="featuredadaptation_text">' . $adaptedBy . ':</div>';
        $content.='<div id="featuredadaptation_thumbnail">' . $instThumbnailLink->show();
        $content.='<div id="featuredadaptation_insttitle">' . $instTitle . '</div></div><br />';
        $content.="</div>";
        return $content;
    }

    /**
     * returns the number of adaptations by this institution 
     */
    function getAdaptationsByInstitution($institutionId) {
        $dbProducts = $this->getObject("dbproducts", "oer");
        $products = $dbProducts->getAdaptationsByInstitution($institutionId);
        $dbInstitutions = $this->getObject("dbinstitution", "oer");
        $content = "";
        foreach ($products as $product) {

            $product = $this->dbproducts->getProduct($product['id']);
            $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="45" height="49" align="left"/>';
            if ($product['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="45" height="49" align="left"/>';
            }

            $mode = "";
            $thumbnailLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $product['id'], 'module' => 'oer', "id" => $product['id'], "mode" => $mode)));
            $thumbnailLink->link = $thumbnail . '<br/>';
            $thumbnailLink->cssClass = 'featuredproduct_thumbnail';

            $titleLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $product['id'], 'module' => 'oer', "id" => $product['id'], "mode" => $mode)));
            $titleLink->cssClass = 'original_product_listing_title';
            $titleLink->link = $product['title'];
            $product = $titleLink->show();


            $content .= '<div id="product">';
            $content.='<div id="product_thumbnail">' . $thumbnailLink->show() . '</div>';
            $content.='<div id="product_title">' . $titleLink->show() . '</div>';

            $institution = $dbInstitutions->getInstitutionById($institutionId);

            $instthumbnail = '<img src="usrfiles/' . $institution['thumbnail'] . '" class="institution_thumbnail"  width="65" height="85" align="bottom"/>';
            if ($institution['thumbnail'] == '') {
                $instthumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg" class="institution_thumbnail" width="65" height="85" align="bottom"/>';
            }

            $instTitleLink = new link($this->uri(array("action" => "viewinstitution", 'id' => $institution['id'])));
            $instTitleLink->link = $institution['name'];
            $instTitle = $instTitleLink->show();

            $content.='<div id="institution_small_title>' . $this->objLanguage->languageText('mod_oer_adaptedby', 'oer') . ':<br/>' . $instTitle . '</div>';

            $adaptationCount = $this->dbproducts->getProductAdaptationCount($product['productid']);
            $adaptationsLink = new link($this->uri(array("action" => "adaptationlist", "productid" => $product['productid'])));
            $adaptationsLink->link = $adaptationCount . '&nbsp;' . $this->objLanguage->languageText('mod_oer_adaptationscount', 'oer');
            $adaptationsLink->cssClass = 'original_product_listing_adaptation_count';

            $content.='<div id="mostratedproduct_thumbnail">' . $adaptationsLink->show() . '</div>';
            $content.="</div>";
        }
        return $content;
    }

    /**
     * this gets the top 5 most adapted
     * @return string 
     */
    function getMostAdaptedProducts() {
        $dbProducts = $this->getObject("dbproducts", "oer");
        $productIds = $dbProducts->getMostAdaptedProducts();
        $content = '<div id="mostadaptedproducts">';
        $content.="<table>";
        foreach ($productIds as $productId) {

            $product = $this->dbproducts->getProduct($productId['productid']);
            $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="45" height="49" align="left"/>';
            if ($product['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="45" height="49" align="left"/>';
            }

            $mode = "";
            $thumbnailLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $productId['productid'], 'module' => 'oer', "id" => $productId['productid'], "mode" => $mode)));
            $thumbnailLink->link = $thumbnail . '<br/>';
            $thumbnailLink->cssClass = 'featuredproduct_thumbnail';

            $titleLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $productId['productid'], 'module' => 'oer', "id" => $productId['productid'], "mode" => $mode)));
            $titleLink->cssClass = 'original_product_listing_title';
            $titleLink->link = $product['title'];
            $product = $titleLink->show();


            $cell = '<div id="mostadaptedproduct">';
            $cell.='<div id="mostadaptedproduct_title">' . $titleLink->show() . '</div>';

            $adaptationCount = $this->dbproducts->getProductAdaptationCount($productId['productid']);
            $adaptationsLink = new link($this->uri(array("action" => "adaptationlist", "productid" => $productId['productid'])));
            $adaptationsLink->link = $adaptationCount . '&nbsp;' . $this->objLanguage->languageText('mod_oer_adaptationscount', 'oer');
            $adaptationsLink->cssClass = 'original_product_listing_adaptation_count';

            $cell.='<div id="mostratedproduct_thumbnail">' . $adaptationsLink->show() . '</div>';
            $cell.="</div>";
            $content.='<tr><td align="left" valign="top">' . $thumbnailLink->show() . '</td><td align="left" valign="top">' . $cell . '</td></tr>';
        }
        $content.="</table>";
        $content.="</div>";
        return $content;
    }

    /**
     * this gets the top 5 most commented products. The comments are pulled from
     * the wall module
     * @return string 
     */
    function getMostCommentedProducts() {
        $dbProducts = $this->getObject("dbproducts", "oer");
        $productIds = $dbProducts->getMostCommentedProducts();

        $content = '<div id="mostadaptedproducts">';
        $content.="<table>";
        foreach ($productIds as $productId) {

            $product = $this->dbproducts->getProduct($productId['productid']);
            $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="45" height="49" align="left"/>';
            if ($product['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="45" height="49" align="left"/>';
            }

            $mode = "";
            $thumbnailLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $productId['productid'], 'module' => 'oer', "id" => $productId['productid'], "mode" => $mode)));
            $thumbnailLink->link = $thumbnail . '<br/>';
            $thumbnailLink->cssClass = 'featuredproduct_thumbnail';

            $titleLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $productId['productid'], 'module' => 'oer', "id" => $productId['productid'], "mode" => $mode)));
            $titleLink->cssClass = 'original_product_listing_title';
            $titleLink->link = $product['title'];
            $product = $titleLink->show();


            $cell = '<div id="mostadaptedproduct">';
            $cell.='<div id="mostadaptedproduct_title">' . $titleLink->show() . '</div>';

            $adaptationCount = $this->dbproducts->getProductAdaptationCount($productId['productid']);
            $adaptationsLink = new link($this->uri(array("action" => "adaptationlist", "productid" => $productId['productid'])));
            $adaptationsLink->link = $adaptationCount . '&nbsp;' . $this->objLanguage->languageText('mod_oer_adaptationscount', 'oer');
            $adaptationsLink->cssClass = 'original_product_listing_adaptation_count';

            $cell.='<div id="mostratedproduct_thumbnail">' . $adaptationsLink->show() . '</div>';
            $cell.="</div>";
            $content.='<tr><td align="left" valign="top">' . $thumbnailLink->show() . '</td><td align="left" valign="top">' . $cell . '</td></tr>';
        }
        $content.="</table>";
        $content.="</div>";
        return $content;
    }

    /**
     * this gets the most rated  product
     * @return string 
     */
    function getMostRatedProducts() {
        $dbProductRating = $this->getObject("dbproductrating", "oer");
        $productIds = $dbProductRating->getMostRatedProducts();

        $content = '<div id="mostcommentedproducts">';
        $content.='<table>';
        foreach ($productIds as $productId) {

            $product = $this->dbproducts->getProduct($productId['productid']);
            $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="45" height="49" align="left"/>';
            if ($product['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="45" height="49" align="left"/>';
            }

            $mode = "";
            $thumbnailLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $productId['productid'], 'module' => 'oer', "id" => $productId['productid'], "mode" => $mode)));
            $thumbnailLink->link = $thumbnail . '<br/>';
            $thumbnailLink->cssClass = 'featuredproduct_thumbnail';

            $titleLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $productId['productid'], 'module' => 'oer', "id" => $productId['productid'], "mode" => $mode)));
            $titleLink->cssClass = 'original_product_listing_title';
            $titleLink->link = $product['title'];
            $product = $titleLink->show();


            $cell = '<div id="mostcommentedproduct">';
            $cell.='<div id="mostcommentedproduct_title">' . $titleLink->show() . '</div>';

            $adaptationCount = $this->dbproducts->getProductAdaptationCount($productId['productid']);
            $adaptationsLink = new link($this->uri(array("action" => "adaptationlist", "productid" => $productId['productid'])));
            $adaptationsLink->link = $adaptationCount . '&nbsp;' . $this->objLanguage->languageText('mod_oer_adaptationscount', 'oer');
            $adaptationsLink->cssClass = 'original_product_listing_adaptation_count';

            $cell.='<div id="mostcommentedproduct_thumbnail">' . $adaptationsLink->show() . '</div>';
            $cell.="</div>";
            $content.='<tr><td align="left" valign="top">' . $thumbnailLink->show() . '</td><td align="left" valign="top">' . $cell . '</td></tr>';
        }
        $content.="</table>";
        $content.="</div>";
        return $content;
    }

    /**
     * this get the most (A)daprated, (R)ated, (F)eatured 
     */
    function getMostARC() {
        $content = '<h2>' . $this->objLanguage->languageText('mod_oer_most', 'oer') . '</h2>';

        $content = '
<div class="tabber">

     <div class="tabbertab">
	  <h2 class="mostadapted">' . $this->objLanguage->languageText('mod_oer_mostadapted', 'oer') . '</h2>
	  ' . $this->getMostAdaptedProducts() . '
     </div>


     <div class="tabbertab">
	  <h2 class="mostrated">' . $this->objLanguage->languageText('mod_oer_mostrated', 'oer') . '</h2>
	    ' . $this->getMostRatedProducts() . '
     </div>


     <div class="tabbertab">
	  <h2 class="mostcommented">' . $this->objLanguage->languageText('mod_oer_mostcommented', 'oer') . '</h2>
	 ' . $this->getMostCommentedProducts() . '
     </div>

</div>
            

';
        return $content;
    }

}

?>
