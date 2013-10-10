<?php

/*
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
 */

/**
 * This method contains util methods for managing product sections
 *
 * @author davidwaf
 */
class sectionmanager extends object {

    function init() {
        $this->dbproducts = $this->getObject('dbproducts', 'oer');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject("user", "security");
        $this->rootTitle = $this->objLanguage->languageText('mod_oer_none', 'oer');
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
        $this->loadClass('treenode', 'tree');
        $this->loadClass('htmllist', 'tree');
        $this->loadClass('treemenu', 'tree');
        $this->loadClass('htmldropdown', 'tree');
        $this->loadClass('dhtml', 'tree');

        $this->addJS();
        $this->setupLanguageItems();
    }

    /**
     * adds essential js
     */
    function addJS() {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('sections.js', 'oer'));
    }

    /**
     * sets up necessary lang items for use in js
     */
    function setupLanguageItems() {
        // Serialize language items to Javascript
        $arrayVars['status_success'] = "mod_oer_status_success";
        $arrayVars['status_fail'] = "mod_oer_status_fail";
        $arrayVars['confirm_delete_section'] = "mod_oer_confirm_delete_section";
        $arrayVars['loading'] = "mod_oer_loading";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oer');
    }

    /**
     * Saves new curriculum 
     * @return string productId
     */
    function saveCurriculum() {
        $productId = $this->getParam("productid");
        $id = $this->getParam("id");
        $data = array(
            "product_id" => $productId,
            "title" => $this->getParam("title"),
            "forward" => $this->getParam("forward"),
            "background" => $this->getParam("background"),
            "status" => $this->getParam("status")
        );

        $dbCurriculum = $this->getObject("dbcurriculums", "oer");
        if ($id == Null) {
            $id = $dbCurriculum->addCurriculum($data);
        } else {
            $dbCurriculum->updateCurriculum($data, $id);
        }
        //here we must return the product id to be used for creating section tree
        return $productId;
    }

    /**
     * updates existing curriculum
     * @return string productId
     */
    function updateCurriculum() {
        $productId = $this->getParam("productid");
        $id = $this->getParam("id");
        $data = array(
            "title" => $this->getParam("title"),
            "forward" => $this->getParam("forward"),
            "background" => $this->getParam("background"),
            "introduction" => $this->getParam("introduction"),
            "status" => $this->getParam("status")
        );

        $dbCurriculum = $this->getObject("dbcurriculums", "oer");
        $dbCurriculum->updateCurriculum($data, $id);
        //here we must return the product id to be used for creating section tree
        return $productId;
    }

    /**
     * generates a random string
     * @return string 
     */
    public function genRandomString() {
        $length = 5;
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $string = "";

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;
    }

    /**
     * Saves a given section, depending on the type of node selected
     * @return string id
     */
    function saveSectionNode() {

        $parentid = $this->getParam('selectednode');

        $dbSections = $this->getObject("dbsectionnodes", "oer");
        $sectionNode = $dbSections->getSectionNode($parentid);
        $parent = $sectionNode['path'];
        $name = $this->getParam("title");
        $nodeType = $this->getParam("nodetype");
        $status = $this->getParam("status");
        $sectionId = null;

        $path = "";
        $child = $this->genRandomString();
        if ($parent) {
            $path = $parent . '/' . $child;
        } else {
            $path = $child;
        }

        $data = array(
            "product_id" => $this->getParam("productid"),
            "section_id" => $sectionId,
            "title" => $name,
            "path" => $path,
            "status" => $status,
            "nodetype" => $nodeType,
            "level" => count(explode("/", $path))
        );
        $id = $dbSections->addSectionNode($data);
        return $id;
    }

    /**
     * Saves section content to the db
     * @return string id
     */
    function saveSectionContent() {
        $data = array(
            "node_id" => $this->getParam("sectionid"),
            "title" => $this->getParam("title"),
            "deleted" => 'N',
            "content" => $this->getParam("content"),
            "status" => $this->getParam("status"),
            "contributedby" => $this->getParam("contributedby")
        );


        $dbSectionContent = $this->getObject("dbsectioncontent", "oer");
        $id = $dbSectionContent->addSectionContent($data);
        return $id;
    }

    /**
     * Function that updates section node
     *
     * @return String id
     */
    function updateSectionNode() {

        $parentid = $this->getParam('selectednode');
        $sectionId = $this->getParam("id");
        $dbSections = $this->getObject("dbsectionnodes", "oer");
        $sectionNode = $dbSections->getSectionNode($parentid);
        $parent = $sectionNode['path'];
        $name = $this->getParam("title");
        $nodeType = $this->getParam("nodetype");
        $status = $this->getParam("status");


        $path = "";
        $child = $this->genRandomString();
        if ($parent) {
            $path = $parent . '/' . $child;
        } else {
            $path = $name;
        }

        $data = array(
            "product_id" => $this->getParam("productid"),
            "section_id" => $sectionId,
            "title" => $name,
            "path" => $path,
            "status" => $status,
            "nodetype" => $nodeType,
            "level" => count(explode("/", $path))
        );

        $id = $dbSections->updateSectionNode($data, $sectionId);
        return $id;
    }

    /**
     * Updates section info by setting deleted to false
     * @return String id
     */
    function deleteSectionNode() {
        $sectionId = $this->getParam("id");

        $data = array(
            "deleted" => "Y"
        );
        $dbSections = $this->getObject("dbsectionnodes", "oer");
        $id = $dbSections->updateSectionNode($data, $sectionId);
        return $id;
    }

    /**
     * updates section content
     * @return string id
     */
    function updateSectionContent() {
        $data = array(
            "node_id" => $this->getParam("sectionid"),
            "title" => $this->getParam("title"),
            "content" => $this->getParam("content"),
            "status" => $this->getParam("status"),
            "contributedby" => $this->getParam("contributedby")
        );

        $id = $this->getParam("id");
        $dbSectionContent = $this->getObject("dbsectioncontent", "oer");
        $dbSectionContent->updateSectionContent($data, $id);
        return $id;
    }

    /**
     * Builds a form for managing section
     * @return form
     */
    function buildAddEditCuriculumForm($productId, $id=null, $parentid=null, $isOriginalProduct = null) {

        $objTable = $this->getObject('htmltable', 'htmlelements');
        $product = $this->dbproducts->getProduct($productId);
        $dbCurriculum = $this->getObject("dbcurriculums", "oer");
        $curriculum = $dbCurriculum->getCurriculum($productId);

        $hidId = new hiddeninput('productid');
        $hidId->cssId = "productid";
        $hidId->value = $productId;
        $objTable->startRow();
        $objTable->addCell($hidId->show());
        $objTable->endRow();

        if ($id != null) {
            $hidId = new hiddeninput('id');
            $hidId->cssId = "id";
            $hidId->value = $id;
            $objTable->startRow();
            $objTable->addCell($hidId->show());
            $objTable->endRow();
        }


        if ($parentid != null) {
            $hidId = new hiddeninput('parentid');
            $hidId->cssId = "parentid";
            $hidId->value = $parentid;
            $objTable->startRow();
            $objTable->addCell($hidId->show());
            $objTable->endRow();
        }


        //title
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_title', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $textinput = new textinput('title');
        $textinput->size = 60;
        $textinput->cssClass = "required";
        $textinput->value = $curriculum['title'];
        $objTable->addCell($textinput->show());
        $objTable->endRow();



        //forward
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_forward', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $forward = $this->newObject('htmlarea', 'htmlelements');
        $forward->name = 'forward';
        $forward->value = $curriculum['forward'];
        $forward->height = '150px';
        $objTable->addCell($forward->show());
        $objTable->endRow();


        //background
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_background', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $background = $this->newObject('htmlarea', 'htmlelements');
        $background->name = 'background';
        $background->height = '150px';
        $background->value = $curriculum['background'];
        $objTable->addCell($background->show());
        $objTable->endRow();


        //description
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_introduction', 'oer'));
        $objTable->endRow();

        $objTable->startRow();
        $description = $this->newObject('htmlarea', 'htmlelements');
        $description->name = 'introduction';
        $description->height = '150px';
        $description->value = $curriculum['introduction'];
        $objTable->addCell($description->show());
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

        $published->setSelected($curriculum['status']);
        $objTable->addCell($published->show());
        $objTable->endRow();


        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_curriculum', 'oer'));
        $fieldset->addContent($objTable->show());


        $title = $curriculum['title'];
        if ($title == '') {
            $title = $this->objLanguage->languageText('mod_oer_section', 'oer');
        }
        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "original_product_section";
        $header->str = $title . '-' . $this->objLanguage->languageText('mod_oer_curriculum', 'oer');

        $action = 'createcurriculum';
        if ($id != null) {
            $action = "editcurriculum";
        }

        $form = new form('curriculumform', $this->uri(array('action' => $action, "productid" => $productId)));
        $form->addToForm($fieldset->show());
        $button = new button('create', $this->objLanguage->languageText('word_save', 'system'));
        $button->setToSubmit();
        $form->addToForm('<br/>' . $button->show());
        return $header->show() . $form->show();
    }

    /**
     * Returns a top level curriculum form or dynamic forms depending on the 
     * sections being created
     * @param type $productId
     * @param type $sectionsExist
     * @param type $name
     * @return type 
     */
    function buildCreateEditNodeForm($productId, $sectionId, $isOriginalProduct) {

        $dbCurriculum = $this->getObject("dbcurriculums", "oer");
        $curriculum = $dbCurriculum->getCurriculum($productId);
        if ($curriculum == Null) {
            return $this->buildAddEditCuriculumForm($productId, $sectionId, $isOriginalProduct);
        } else {
            if ($sectionId == $curriculum['id']) {
                return $this->buildAddEditCuriculumForm($productId, $sectionId, $isOriginalProduct);
            } else {
                return $this->getAddEditNodeForm($productId, $sectionId, $isOriginalProduct);
            }
        }
    }

    /**
     * builds a forms for adding and editing section nodes
     *
     * @param String $productId
     * @param String $sectionId
     * @param String $isOriginalProduct
     * @return form
     */
    function getAddEditNodeForm($productId, $sectionId, $isOriginalProduct) {
        $dbSections = $this->getObject("dbsectionnodes", "oer");
        $section = $dbSections->getSectionNode($sectionId);

        $action = "createsectionnode";
        if ($section != null) {
            $action = 'updatesectionnode';
        }

        $form = new form('createsectionnode', $this->uri(array('action' => $action, "productid" => $productId)));

        if ($section != null) {
            $hidId = new hiddeninput('id');
            $hidId->cssId = "id";
            $hidId->value = $sectionId;
            $form->addToForm($hidId->show());
        }
        $hidOP = new hiddeninput('isoriginalproduct');
        $hidOP->cssId = "id";
        $hidOP->value = $isOriginalProduct;
        $form->addToForm($hidOP->show());

        $label = new label($this->objLanguage->languageText('mod_oer_nodetype', 'oer'), 'input_sectionname');
        $nodeType = new dropdown('nodetype');
        $nodeType->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $nodeType->cssClass = "required";
        $nodeType->addOption('folder', $this->objLanguage->languageText('mod_oer_folder', 'oer'));
        $nodeType->addOption('section', $this->objLanguage->languageText('mod_oer_section', 'oer'));
        if ($section != null) {
            $nodeType->setSelected($section['nodetype']);
        }

        $form->addToForm('<br/>' . $label->show());
        $form->addToForm('<br/>' . $nodeType->show());

        //title
        $form->addToForm('<br/>' . $this->objLanguage->languageText('mod_oer_title', 'oer'));
        $textinput = new textinput('title');
        $textinput->size = 60;
        $textinput->cssClass = "required";

        if ($section != null) {
            $textinput->value = $section['title'];
        }
        $form->addToForm('<br/>' . $textinput->show());


        $statusField = new dropdown('status');
        $statusField->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $statusField->cssClass = "required";
        $statusField->addOption('disabled', $this->objLanguage->languageText('mod_oer_disabled', 'oer'));
        $statusField->addOption('draft', $this->objLanguage->languageText('mod_oer_draft', 'oer'));
        $statusField->addOption('published', $this->objLanguage->languageText('mod_oer_published', 'oer'));
        if ($section != null) {
            $statusField->setSelected($section['status']);
        }

        $createIn = '<div id="createin">' . $this->objLanguage->languageText('mod_oer_createin', 'oer') . '<br/>' .
                $selected = '';
        if ($section != null) {
            $selected = $section['path'];
        }
        $createIn.= $this->buildSectionsTree($productId, '', "false", 'htmldropdown', $selected) . '</div>';

        $form->addToForm("<br/>" . $createIn);
        $form->addToForm('<br/>' . $this->objLanguage->languageText('mod_oer_status', 'oer') . '<br/>' . $statusField->show());

        $button = new button('create', $this->objLanguage->languageText('word_save', 'system'));
        $button->setToSubmit();
        $form->addToForm('<br/>' . $button->show());

        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        //Check if original product or adaptation
        if ($isOriginalProduct == 1) {
            $uri = $this->uri(array("action" => "vieworiginalproduct", "id" => $productId));
        } else {
            $uri = $this->uri(array("action" => "viewadaptation", "id" => $productId));
        }
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $form->addToForm('&nbsp;&nbsp;' . $button->show());

        $fs = new fieldset();
        $fs->setLegend($this->objLanguage->languageText('mod_oer_nodename', 'oer'));
        $fs->addContent($form->show());

        $title = $this->objLanguage->languageText('mod_oer_addnode', 'oer');
        if ($section != null) {
            $title = $section['title'];
        }

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "createnode_title";
        $header->str = $title;


        return $header->show() . $fs->show();
    }

    /**
     * this formats and presents content of a selected node on sections tree of
     * a product
     * @param string $productId
     * @param string $sectionId
     * @return string 
     */
    function getSectionContent($productId, $sectionId) {
        $dbSections = $this->getObject("dbsectioncontent", "oer");
        $dbSectionNode = $this->getObject("dbsectionnodes", "oer");
        $dbProducts = $this->getObject("dbproducts", "oer");
        $node = $dbSectionNode->getSectionNode($sectionId);
        $section = $dbSections->getSectionContent($sectionId);
        $product = $dbProducts->getProduct($productId);
        $content = "";
        $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="79" height="101" align="left"/>';
        if ($product['thumbnail'] == '') {
            $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="79" height="101" align="left"/>';
        }
        $content.='<div id="sectionheader">';

        $content.='<a href="?module=oer&action=vieworiginalproduct&id=' . $productId . '">' . $thumbnail . '</a>';
        $content.='<a href="?module=oer&action=vieworiginalproduct&id=' . $productId . '">' . '<h1>' . $product['title'] . '</h1></a>';
        $content.='</div>';
        $content.='<div id="sectionbody">';

        //Add bookmark
        $objBookMarks = $this->getObject('socialbookmarking', 'utilities');
        $objBookMarks->options = array('stumbleUpon', 'delicious', 'newsvine', 'reddit', 'muti', 'facebook', 'addThis');
        $objBookMarks->includeTextLink = FALSE;
        $bookmarks = $objBookMarks->show();

        $leftContent = "";
        $leftContent .= '<p>' . $bookmarks . '</p>';
        $leftContent .= '<h2>' . $node['title'] . '</h2>';
        $leftContent .= $this->objLanguage->languageText('mod_oer_contributedby', 'oer') . '&nbsp;' . $section['contributedby'];
        $leftContent .= $section['content'];

        $rightContent = "";
        $rightContent.=$this->buildSectionsTree($productId, $sectionId);

        $table = $this->getObject("htmltable", "htmlelements");
        $table->startRow();
        $table->addCell($leftContent, "60%", "top", "left");
        $table->addCell('<div id="viewproduct_rightcontent>' . $rightContent . '</div>', "40%", "top", "left");
        $table->endRow();

        $content.=$table->show();
        $content.='</div>';
        return $content;
    }

    /**
     * Build detailed section view
     * @param String $productId
     * @param String $sectionId
     * @param Boolean $showtopview
     * @return string
     */
    function buildSectionView($productId, $sectionId, $nodeType, $showTopView=true) {
        //Get DB Objects
        $dbSections = $this->getObject("dbsectioncontent", "oer");
        $dbSectionNode = $this->getObject("dbsectionnodes", "oer");
        $dbProducts = $this->getObject("dbproducts", "oer");
        $objDbInstitution = $this->getObject("dbinstitution", "oer");
        $objAdaptationManager = $this->getObject("adaptationmanager", "oer");
        $dbCurriculum = $this->getObject("dbcurriculums", "oer");

        //Flag to check if user has perms to manage adaptations
        $hasPerms = $objAdaptationManager->userHasPermissions();

        //Get section data
        $node = $dbSectionNode->getSectionNode($sectionId);
        $section = $dbSections->getSectionContent($sectionId);
        $isOriginalProduct = $dbProducts->isOriginalProduct($productId);

        $curriculum = null;
        if ($nodeType == 'curriculum') {
            $curriculum = $dbCurriculum->getCurriculum($productId);
        }


        $product = $dbProducts->getProduct($productId);
        $instData = $objDbInstitution->getInstitutionById($product["institutionid"]);

        //Flag to check if user has perms to manage adaptations
        $hasPerms = $objAdaptationManager->userHasPermissions();

        //Add bookmark
        $objBookMarks = $this->getObject('socialbookmarking', 'utilities');
        $objBookMarks->options = array('stumbleUpon', 'delicious', 'newsvine', 'reddit', 'muti', 'facebook', 'addThis');
        $objBookMarks->includeTextLink = FALSE;
        $bookmarks = $objBookMarks->show();

        $table = $this->getObject("htmltable", "htmlelements");
        $table->attributes = "style='table-layout:fixed;'";
        $table->border = 0;

        $newAdapt = "";
        if ($hasPerms) {
            //Link for - adapting product from existing adapatation
            $newAdaptLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $productId, 'mode="new"')));
            $newAdaptLink->link = $this->objLanguage->languageText('mod_oer_makenewfromadaptation', 'oer');
            $newAdapt = $newAdaptLink->show();
        }

        //Link for - original product title
        $viewParentProdLink = new link($this->uri(array("action" => "vieworiginalproduct", "id" => $product["parent_id"], "mode" => "grid")));
        $viewParentProdLink->link = $this->objLanguage->languageText('mod_oer_fullprodview', 'oer');
        $viewParentProd = $viewParentProdLink->show();

        //Link for - See existing adaptations of this UNESCO Product
        $viewParentInstLink = new link($this->uri(array("action" => "viewinstitution", "id" => $product["institutionid"])));
        $viewParentInstLink->link = $this->objLanguage->languageText('mod_oer_fullviewinst', 'oer');
        $viewParentInst = $viewParentInstLink->show();

        //Link for - parent inst title
        $viewInstTitleLink = new link($this->uri(array("action" => "viewinstitution", "id" => $product["institutionid"])));
        $viewInstTitleLink->link = $instData['name'];
        $viewInstTitle = $viewInstTitleLink->show();



        //Build navigation path
        if ($isOriginalProduct) {
            //Link for - product list
            $prodListLink = new link($this->uri(array("action" => "home")));
            $prodListLink->link = $this->objLanguage->languageText('mod_oer_maintitle2', 'oer');
            $prodListPage = $prodListLink->show();
            $navpath = $prodListPage . " > " . $section['title'];
            //Link for - view product for this section
            $viewProdTitleLink = new link($this->uri(array("action" => "vieworiginalproduct", "id" => $product["id"], "mode" => "grid")));
            $viewProdTitleLink->link = $product['title'];
            $viewProdTitle = $viewProdTitleLink->show();
        } else {
            //Get parent prod data
            $parentProduct = $dbProducts->getProduct($product["parent_id"]);

            //Link for - adaptation list
            $adaptListLink = new link($this->uri(array("action" => "viewadaptation", "id" => $productId)));
            $adaptListLink->link = $this->objLanguage->languageText('mod_oer_adaptations', 'oer');
            $adaptListPage = $adaptListLink->show();
            $navpath = $adaptListPage . " > " . $viewInstTitle . " > " . $section['title'];
            //Link for - original product for this adaptation
            $viewParentTitleLink = new link($this->uri(array("action" => "vieworiginalproduct", "id" => $product["parent_id"], "mode" => "grid")));
            $viewParentTitleLink->link = $parentProduct['title'];
            $viewParentTitle = $viewParentTitleLink->show();
        }


        //Fetch section tree
        $navigator = $this->buildSectionsTree($productId, $sectionId);

        $homeLink = new link($this->uri(array("action" => "home")));
        $homeLink->link = $this->objLanguage->languageText('mod_oer_home', 'system');


        $objTools = $this->newObject('tools', 'toolbar');
        $crumbs = array($homeLink->show());
        $objTools->addToBreadCrumbs($crumbs);

        $leftCol = "";

        $leftCol .= '<div class="headingHolder">            
            <div class="heading2">
            <h1 class="greyText">' . $section['title'] . '</h1></div>';
        if ($curriculum == null) {
            $leftCol.='    <p class="greyText">' . $this->objLanguage->languageText('mod_oer_contributedby', 'oer') . " : " .
                    $section['contributedby'] . '</p>';
        }
        $leftCol.='      </div>';

        $table = $this->getObject("htmltable", "htmlelements");
        $table->attributes = "style='table-layout:fixed;'";
        $table->border = 0;

        $leftContent = "";
        $rightContent = "";
        $sectContent = $section['content'];
        if ($curriculum != null) {
            $sectContent = $curriculum['forward'] . '<p/>' . $curriculum['background'] . '<p/>' . $curriculum['introduction'];
        }
        $leftContent = '<div class="viewadaptation_leftcontent">' . $leftCol . '<div class="contentDivThreeWider">' . $sectContent . '</div>';
        if (!$isOriginalProduct) {
            $leftContent .= '<div class="adaptationNotesContent"><p><b>' . $this->objLanguage->languageText('mod_oer_adaptationotes', 'oer') . '</b></p><p>'
                    . $section['adaptation_notes'] . '</p></div>';
        }
        $leftContent .= '</div>';
        $rightContent = '<div class="rightColumnDivWide rightColumnPadding"><div class="frame">' . $navigator . '</div>
            <br/><br/><div class="sectionkeywords"><b>' . $this->objLanguage->languageText('mod_oer_sectionkeywords', 'oer', "Section keywords")
                . ':</b><p>' . $section['keywords'] . '</p></div></div>';
        $table->startRow();
        $table->addCell($leftContent, "", "top", "left", "", 'style="width:75%"');
        $table->addCell("<br /><br />" . $rightContent, "", "top", "left", "", 'style="width:25%"');
        $table->endRow();

        $topStuff = "";


        //Heading varies depending on whether its an original product or adaptation
        if ($isOriginalProduct) {
            //Get icons
            $prodIconOne = '<img src="skins/oeru/images/icon-product.png" alt="' . $this->objLanguage->languageText('mod_oer_bookmark', 'oer') .
                    '" class="smallIcons" />';
            $prodIconTwo = '<img src="skins/oeru/images/document-new.png" alt="' . $this->objLanguage->languageText('mod_oer_bookmark', 'oer') .
                    '" class="smallIcons" />';
            $prodIconThree = '<img src="skins/oeru/images/sort-by-grid.png" alt="' . $this->objLanguage->languageText('mod_oer_bookmark', 'oer') .
                    '" class="smallIcons" />';
            //Get count of adaptations
            $adaptationCount = $dbProducts->getProductAdaptationCount($productId);
            //Get prod thumbnail
            $prodthumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="59" height="76" align="left"/>';
            if ($product['thumbnail'] == '') {
                $prodthumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="59" height="76" align="left"/>';
            }
            //Link for - Full view of product
            $fullProdViewLink = new link($this->uri(array("action" => "vieworiginalproduct", "id" => $productId, "identifier" => $productId, "mode" => "grid")));
            $fullProdViewLink->link = $this->objLanguage->languageText('mod_oer_fullviewofproduct', 'oer');
            $fullProdView = $prodIconOne . " " . $fullProdViewLink->show();
            //Link for - make new adaptation
            $makeAdaptationLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $productId, "mode" => "new")));
            $makeAdaptationLink->link = $this->objLanguage->languageText('mod_oer_makenewfromadaptation', 'oer');
            $makeAdaptation = $prodIconTwo . " " . $makeAdaptationLink->show();
            //Link for - view adaptations if count is >0
            $viewAdaptations = "";
            if ($adaptationCount > 0) {
                $viewAdaptationsLink = new link($this->uri(array("action" => "adaptationlist", "productid" => $productId)));
                $viewAdaptationsLink->link = $this->objLanguage->languageText('mod_oer_existingadaptations', 'oer') . " (" . $adaptationCount . ")";
                $viewAdaptations = $prodIconThree . " " . $viewAdaptationsLink->show();
            }
            $toplinks = $viewAdaptations;
            //Form title
            if ($hasPerms) {
                $toplinks = $makeAdaptation . " " . $viewAdaptations;
            }
            $topStuff = '<div class="adaptationListViewTop"><div class="leftTopImage">' . $prodthumbnail .
                    '</div><div><h3>' . $viewProdTitle . '</h3>
                        <p>' . $fullProdView . '</p>
                            <p>' . $toplinks . '</p></div></div>';
        } else {
            //Get prod & inst thumbnails
            $prodthumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="45" height="49" align="left"/>';
            if ($product['thumbnail'] == '') {
                $prodthumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="45" height="49" align="left"/>';
            }
            $instthumbnail = '<img src="usrfiles/' . $instData['thumbnail'] . '"   width="45" height="49"  align="bottom"/>';
            if ($instData['thumbnail'] == '') {
                $instthumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="45" height="49"  align="bottom"/>';
            }
            $topStuff = '<div class="adaptationListViewTop">
            <div class="tenPixelLeftPadding tenPixelTopPadding">
                        <div class="productAdaptationViewLeftColumnTop">
                            <div class="leftTopImage">' . $prodthumbnail . '</div>
                            <div class="leftFloatDiv">
                                <h3>' . $viewParentTitle . '</h3>
                                <img src="skins/oeru/images/icon-product.png" alt="' . $this->objLanguage->languageText('mod_oer_bookmark', 'oer') .
                    '" class="smallLisitngIcons" />
                                <div class="leftTextNextToTheListingIconDiv">' . $viewParentProd . '</a></div>
                            </div>
                    	</div>
                        <div class="middleAdaptedByIcon">
                        	<img src="skins/oeru/images/icon-adapted-by.png" alt="' .
                    $this->objLanguage->languageText('mod_oer_adaptedby', 'oer') . '" width="24" height="24"/><br />
                        	<span class="pinkText">' . $this->objLanguage->languageText('mod_oer_adaptedby', 'oer') . '</span>
                        </div>


                        <div class="productAdaptationViewMiddleColumnTop">
                            <div class="leftTopImage">' . $instthumbnail . '</div>
                            <div class="middleFloatDiv">
                                <h3 class="darkGreyColour">' . $viewInstTitle . '</h3>
                                <img src="skins/oeru/images/icon-product.png" alt="' .
                    $this->objLanguage->languageText('mod_oer_adaptedby', 'oer') . '" class="smallLisitngIcons" />
                                <div class="middleTextNextToTheListingIconDiv">' . $viewParentInst . '</div>
                            </div>
                    	</div>

                    <div class="productAdaptationViewRightColumnTop">
                        <div class="rightAdaptedByIcon">
                        	<img src="skins/oeru/images/icon-managed-by.png" alt="' .
                    $this->objLanguage->languageText('mod_oer_managedby', 'oer') . '" width="24" height="24"/><br />
                        	<span class="greenText">' . $this->objLanguage->languageText('mod_oer_managedby', 'oer') . '</span>
                        </div>
                            <div class="rightFloatDiv">
                                <h3 class="greenText">' . $instData['name'] . '</h3>
                                <div class="textNextToTheListingIconDiv"><a href="#" class="greenTextLink">View group</a></div>
                            </div>
                    	</div>
                    </div></div>';
        }
        if ($showTopView) {
            return '<div class="navPath">' . $navpath .
                    '</div><div class="topContentHolder">' . $topStuff . '</div><br/><br/><div class="mainContentHolder">
            <div class="navPath">' . $navpath .
                    '</div>' . $table->show() . '
            <div class="hunderedPercentGreyHorizontalLine">' . '</div></div></div>';
        } else {
            return $table->show();
        }
    }

    /**
     * this creates a form for filling in section details
     * @param string $productId
     * @param string $sectionId
     * @return string
     */
    function getAddEditSectionForm($productId, $sectionId) {
        $dbSections = $this->getObject("dbsectioncontent", "oer");
        $dbSectionNode = $this->getObject("dbsectionnodes", "oer");
        $node = $dbSectionNode->getSectionNode($sectionId);
        $section = $dbSections->getSectionContent($sectionId);
        $contentId = null;
        $action = "createsectioncontent";
        if ($section != null) {
            $action = 'updatesectioncontent';
            $contentId = $section['id'];
        }

        $form = new form('createsectioncontent', $this->uri(array('action' => $action, "sectionid" => $sectionId, "productid" => $productId)));

        if ($section != null) {
            $hidId = new hiddeninput('id');
            $hidId->cssId = "id";
            $hidId->value = $section['id'];
            $form->addToForm($hidId->show());
        }

        //title
        $form->addToForm('<br/><h2>' . $node['title'] . '</h2>');

        //content
        $form->addToForm('<br/>' . $this->objLanguage->languageText('mod_oer_content', 'oer'));

        $contentField = $this->newObject('htmlarea', 'htmlelements');
        $contentField->name = 'content';
        if ($section != null) {
            $contentField->value = $section['content'];
        }
        $contentField->height = '350px';
        $form->addToForm('<br/>' . $contentField->show());



        $statusField = new dropdown('status');
        $statusField->addOption('', $this->objLanguage->languageText('mod_oer_select', 'oer'));
        $statusField->cssClass = "required";
        $statusField->addOption('disabled', $this->objLanguage->languageText('mod_oer_disabled', 'oer'));
        $statusField->addOption('draft', $this->objLanguage->languageText('mod_oer_draft', 'oer'));
        $statusField->addOption('published', $this->objLanguage->languageText('mod_oer_published', 'oer'));
        if ($section != null) {
            $statusField->setSelected($section['status']);
        }

        $form->addToForm('<br/>' . $this->objLanguage->languageText('mod_oer_status', 'oer') . '<br/>' . $statusField->show());
        $form->addToForm('<br/>' . $this->objLanguage->languageText('mod_oer_contributedby', 'oer'));

        $textarea = new textarea('contributedby', '', 5, 55);
        // $textarea->cssClass = 'required';
        if ($section != null) {
            $textarea->value = $section['contributedby'];
        }
        $form->addToForm('<br/>' . $textarea->show());


        $button = new button('create', $this->objLanguage->languageText('word_save', 'system'));
        $button->setToSubmit();
        $form->addToForm('<br/>' . $button->show());

        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $uri = $this->uri(array("action" => "vieworiginalproduct", "id" => $productId));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $form->addToForm('&nbsp;&nbsp;' . $button->show());

        $fs = new fieldset();
        $fs->setLegend($this->objLanguage->languageText('mod_oer_content', 'oer'));
        $fs->addContent($form->show());

        $title = $this->objLanguage->languageText('mod_oer_addnewcontent', 'oer');
        if ($section != null) {
            $title = $section['title'];
        }

        $header = new htmlheading();
        $header->type = 2;
        $header->cssClass = "createsection_title";
        $header->str = $title;


        return $header->show() . $fs->show();
    }

    /**
     * Returns the parent of a given node. If the node is the top one, the title
     * ofthe curriculum is returned instead
     * @param string $path
     * @param string $productId
     * @return array
     */
    function getParent($path, $productId) {

        $parent = "";
        $parts = explode("/", $path);
        $count = count($parts);
        for ($i = 0; $i < $count - 1; $i++) {
            if ($parent == '') {
                $parent.= $parts[$i];
            } else {
                $parent.="/" . $parts[$i];
            }
        }
        if ($parent == '') {
            $dbSections = $this->getObject("dbsectionnodes", "oer");
            $sectionsExist = $dbSections->sectionsExist($productId);
            if (!$sectionsExist) {
                $parent = $this->rootTitle;
            } else {
                
            }
        }
        return $parent;
    }

    /**
     * Gets the selected section node ids
     *
     * @param string $productId
     * @param string $selected
     * @return array id of selected nodes
     */
    function getSelectedNodes($productId, $selected) {
        //Get DB object classes
        $dbProduct = $this->getObject("dbproducts", "oer");
        $dbsections = $this->getObject("dbsectionnodes", "oer");
        //Get nodes
        $sectionNodes = $dbsections->getSectionNodes($productId);
        $selectedNodesArr = array();
        $selectedTitle = "";
        $selectedId = $selected;

        if ($selected != '') {
            //Get the selected item data to compare with other nodes
            $sectionNode = $dbsections->getSectionNode($selectedId);

            if ($sectionNode == Null || empty($sectionNode)) {
                $selectedTitle = $selectedId;
            } else {
                $selectedTitle = $sectionNode['title'];
            }

            if (count($sectionNodes) > 0) {
                foreach ($sectionNodes as $sectionNode) {
                    //Check if the folderShortText contains some words in selected
                    $arr_selectedtxt = explode(" ", $selectedTitle);
                    $arr_count = count($arr_selectedtxt);
                    $text = $sectionNode['title'];
                    //Check if the node is selected
                    if (!empty($selectedId)) {
                        $cnt = 0;
                        $exists == false;
                        do {
                            $exists = strpos(strtolower($text), strtolower($arr_selectedtxt[$cnt]));
                            $cnt++;
                        } while ($exists === false && $cnt <= $arr_count);
                    }
                    if ($exists !== false) {
                        $selectedNodesArr[] = $sectionNode['id'];
                    }
                }
            }
        }
        return $selectedNodesArr;
    }

    /**
     * Builds a tree structure that represents sections within a product. This method
     * can also build a dropdown-like true, depending on the options passed
     * @param string $productId The product to build sections for
     * @param string $sectionId
     * @param string $treeType Type of tree to build: could be dropdown or dhtml
     * @param string $selected The preselected node
     * @param string $treeMode
     * @param string $action
     * @return string
     */
    function buildSectionsTree($productId, $sectionId, $showThumbNail=null, $treeType='dhtml', $selected='', $treeMode='side', $action='', $compareProdId='') {
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getId("ProductCreators");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $userId = $this->objUser->userId();

        $dbsections = $this->getObject("dbsectionnodes", "oer");
        $sectionNodes = $dbsections->getSectionNodes($productId);

        $dbProduct = $this->getObject("dbproducts", "oer");
        $product = $dbProduct->getProduct($productId);
        $selectedTitle = "";
        $selectedId = $selected;
        if ($treeType == "compare") {
            if ($selected != '') {
                //Get the selected item data to compare with other nodes
                $sectionNode = $dbsections->getSectionNode($selectedId);
                if ($sectionNode == Null || empty($sectionNode)) {
                    $selectedTitle = $selectedId;
                } else {
                    $selectedTitle = $sectionNode['title'];
                }
            } else {
                $sectionNode = $dbsections->getSectionNode($sectionId);
            }
            $selected = $sectionNode['title'];
        } else {
            if ($selected == '') {
                $sectionNode = $dbsections->getSectionNode($sectionId);
                $selected = $sectionNode['title'];
            }
        }

        $icon = 'folder.gif';
        $expandedIcon = 'folder-expanded.gif';
        $cssClass = "";

        $dbCurriculum = $this->getObject("dbcurriculums", "oer");
        $curriculum = $dbCurriculum->getCurriculum($productId);
        $rootId = "-1";

        if ($curriculum != null) {
            $this->rootTitle = $curriculum['title'];
            $rootId = $curriculum['id'];
        }


        if ($treeType == 'htmldropdown') {
            $allFilesNode = new treenode(array('text' => $this->rootTitle, 'link' => '-1'));
        } else if ($treeType == "compare") {
            $allFilesNode = new treenode(array('text' => $this->rootTitle, 'link' => $this->uri(array('action' => 'compareadaptations', "productid" => $compareProdId))));
        } else {
            $allFilesNode = new treenode(array('text' => $this->rootTitle, 'link' => $this->uri(array('action' => 'viewsection', "productid" => $productId, 'sectionid' => $rootId, 'nodetype' => 'curriculum', 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass))));
        }

        $refArray = array();
        $refArray[$this->rootTitle] = & $allFilesNode;

        //Create a new tree
        $menu = new treemenu();

        $sectionEditor = FALSE;

        if ($objGroupOps->isGroupMember($groupId, $userId)) {
            $sectionEditor = TRUE;
        }

        if (count($sectionNodes) > 0) {
            foreach ($sectionNodes as $sectionNode) {
                $maxLen = 50;
                $nodeType = $sectionNode['nodetype'];
                if ($nodeType == 'folder') {
                    $icon = 'folder.gif';
                }
                if ($nodeType == 'section') {
                    $icon = 'document.png';
                    $expandedIcon = $icon;
                }


                $text = $sectionNode['title'];
                if (strlen($text) > $maxLen) {
                    $text = substr($sectionNode['title'], 0, $maxLen) . '...';
                }
                $folderText = $text;
                $folderShortText = $text;

                if ($sectionNode['path'] == $selected) {
                    $folderText = '<strong>' . $folderText . '</strong>';
                    $cssClass = 'confirm';
                } else {
                    $cssClass = '';
                }

                if ($sectionNode['deleted'] == 'Y') {
                    $cssClass = 'deleted';
                }
                if ($treeType == 'htmldropdown') {
                    // echo "css class == $cssClass<br/>";
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $sectionNode['id'], 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                } else if ($treeType == "compare") {
                    //Check if the folderShortText contains some words in selected
                    $arr_selectedtxt = explode(" ", $selectedTitle);
                    if (!empty($selectedId)) {
                        foreach ($arr_selectedtxt as $selectedtxt) {
                            $exists = -1;
                            if ($selectedtxt != '') {
                                $exists = strpos(strtolower($sectionNode['title']), strtolower($selectedtxt));
                            }
                            if ($exists !== false) {
                                $folderShortText = '<span class="adaptnodeselect">' . $folderShortText . "</span>";
                            }
                        }
                    }

                    $link = new link($this->uri(array('action' => 'compareadaptations', "productid" => $compareProdId, 'selected' => $sectionNode['id'])));
                    $link->cssClass = 'sectionlink';
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $link->href, 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass, 'expanded' => true));
                } else {

                    $link = new link($this->uri(array('action' => 'viewsection', "productid" => $sectionNode['product_id'], 'sectionid' => $sectionNode['id'], 'nodetype' => $sectionNode['nodetype'])));
                    $link->cssClass = 'sectionlink';
                    $txtLink = $link->href;
                    /*
                      if ($sectionNode['nodetype'] == 'folder') {
                      $txtLink = '#';
                      } */
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $txtLink, 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass, 'expanded' => true));
                }

                $parent = $this->getParent($sectionNode['path'], $productId);

                if (array_key_exists($parent, $refArray)) {
                    $refArray[$parent]->addItem($node);
                }

                $refArray[$sectionNode['path']] = & $node;
            }
        }

        $menu->addItem($allFilesNode);
        if ($treeType == 'htmldropdown') {
            $treeMenu = new htmldropdown($menu, array('inputName' => 'selectednode', 'id' => 'input_parentfolder', 'selected' => $selected));
        } else {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
            $this->setVar('pageSuppressXML', TRUE);
            $objSkin = $this->getObject('skin', 'skin');
            $treeMenu = new dhtml($menu, array('images' => 'skins/_common/icons/tree', 'defaultClass' => 'treeMenuDefault'));
        }

        $thumbnail = "";
        $space = "";
        if ($showThumbNail == 'true') {
            $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="79" height="101" align="left"/>';
            if ($product['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="79" height="101" align="left"/>';
            }
            $space = '<br/>';
        }
        return '<div id="navthumbnail">' . $thumbnail . '</div>' . '<div id="sectionstree">' . $space . $treeMenu->getMenu() . '</div>';
    }

    /**
     * notifies the user theat s/he selected a folder node, which doesnt cannot 
     * be displayed
     * @return type 
     */
    function viewFolderNode($productId) {
        $text = $this->objLanguage->languageText('mod_oer_viewsection', 'oer');
        $back = $this->objLanguage->languageText('word_back', 'system');

        $button = new button('cancel', $back);
        $uri = $this->uri(array("action" => "vieworiginalproduct", "id" => $productId));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');

        return '<div class="error">' . $text . '<br/>'.$button->show().'</div>';
    }

}

?>
