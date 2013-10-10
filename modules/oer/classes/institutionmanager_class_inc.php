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

class institutionmanager extends object {

    private $_institutionList;
    private $_institution;
    private $_objDbInstitution;
    private $_objDbInstitutionType;
    private $_objCountry;
    private $_validation;
    /**
     * @var object $objLanguage Language Object
     */
    private $_objLanguage;

    function init() {
        $this->_objDbInstitution = $this->getObject('dbinstitution');
        $this->_objDbInstitutionType = $this->getObject('dbinstitutiontypes');
        $this->_objCountry = $this->getObject('languagecode', 'language');
        $this->_objLanguage = $this->getObject('language', 'language');
        $this->objLanguage = $this->getObject("language", "language");
        $this->_institution = $this->getObject('institution');
        $this->_institutionList = array();
        $this->_validation['valid'] = TRUE;
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject("user", "security");
        $this->setupLanguageItems();
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('institutionedit.js'));
    }

    /**
     * sets up necessary lang items for use in js
     */
    function setupLanguageItems() {
// Serialize language items to Javascript
        $arrayVars['confirm_delete_institution'] = "mod_oer_confirmdeleteinstitution";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oer');
    }

    public function institutionNameExists($name) {
        $checkName = $this->_objDbInstitution->getInstitutionName($name);

        if (strlen($checkName) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function addInstitution($name, $description, $type, $country, $address1, $address2, $address3, $zip, $city, $websiteLink, $keyword1, $keyword2, $thumbnail) {
//Check if institution exists
        return $this->_objDbInstitution->addInstitution($name, $description, $type, $country, $address1, $address2, $address3, $zip, $city, $websiteLink, $keyword1, $keyword2, $thumbnail);
    }

    public function editInstitution($id, $name, $description, $type, $country, $address1, $address2, $address3, $zip, $city, $websiteLink, $keyword1, $keyword2, $thumbnail) {
//First check if an institution with a similar name exists        
        $this->_objDbInstitution->editInstitution($id, $name, $description, $type, $country, $address1, $address2, $address3, $zip, $city, $websiteLink, $keyword1, $keyword2, $thumbnail);
    }

    public function removeInstitution($id) {
        $this->_objDbInstitution->deleteInstitution($id);
    }

    public function getInstitution($id) {
        $this->_institution = $this->constructInstitution($id);

        return $this->_institution;
    }

    /**
     * this returns formatted list of institutions. This list includes the 
     * institution logo, with controls for editing/deleting the institution
     */
    public function getAllInstitutions($message) {

        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objUser = $this->getObject("user", "security");
        $this->_institutionList = $this->_objDbInstitution->getAllInstitutions();
        $table = $this->getObject("htmltable", "htmlelements");
        $dbInstitutionType = $this->getObject("dbinstitutiontypes", "oer");
        $canEdit = $objUser->isLoggedIn();
        foreach ($this->_institutionList as $institution) {
            $table->startRow();
            $objIcon->setIcon("edit");
            $editLink = new link($this->uri(array("action" => "institutionedit", "mode" => "edit", "id" => $institution['id'])));
            $editLink->link = $objIcon->show();

            $objIcon->setIcon("delete");
            $deleteLink = new link($this->uri(array("action" => "deleteinstitution", "id" => $institution['id'])));
            $deleteLink->cssClass = "deleteinstitution";
            $deleteLink->link = $objIcon->show();

            $viewLink = new link($this->uri(array("action" => "viewinstitution", "id" => $institution['id'])));
            $viewLink->link = $institution['name'];


            $thumbnail = '<img src="usrfiles/' . $institution['thumbnail'] . '"   width="45" height="49"  align="bottom"/>';
            if ($institution['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="45" height="49"  align="bottom"/>';
            }

            $table->addCell($thumbnail);
            $table->addCell($viewLink->show() . $editLink->show() . $deleteLink->show());
            $table->addCell($institution['country']);
            $table->addCell($dbInstitutionType->getType($institution['type']));
            $table->endRow();
        }
        $addLink = new link($this->uri(array("action" => 'institutionedit')));
        $addLink->link = $this->_objLanguage->languageText('mod_oer_institution_heading_new', 'oer');

        $controlBand = '<h1>' . $this->_objLanguage->languageText('mod_oer_institutions', 'oer') . '</h1>'
                . '<div id="institution_controlband">';
        $newthumbnail = '&nbsp;<img src="skins/oeru/images/document-new.png" width="19" height="15"/>';
        $controlBand.= '<h1>' . $newthumbnail . $addLink->show() . '</h1>';
        $controlBand.= '</div> ';

        $notification = '';
        if ($message != '') {
            $notification = '<script type="text/javascript">
                        showNotification({
                            message: "' . $message . '",
                            type: "error",
                            autoClose: true,
                            duration: 5                                        
                        });
                    </script>
';
        }
        return $controlBand . $table->show() . $notification;
    }

    private function constructInstitution($id) {
        $parameters = $this->_objDbInstitution->getInstitutionById($id);

        $myInstitution = $this->getObject('institution', 'unesco_oer');
        $myInstitution->setId($parameters[0]['id']);
        $myInstitution->setName($parameters[0]['name']);
        $myInstitution->setDescription($parameters[0]['description']);
        $myInstitution->setType($parameters[0]['type']);
        $myInstitution->setCountry($parameters[0]['country']);
        $myInstitution->setAddress1($parameters[0]['address1']);
        $myInstitution->setAddress2($parameters[0]['address2']);
        $myInstitution->setAddress3($parameters[0]['address3']);
        $myInstitution->setZip($parameters[0]['zip']);
        $myInstitution->setCity($parameters[0]['city']);
        $myInstitution->setWebsiteLink($parameters[0]['websitelink']);
        $myInstitution->setKeyword1($parameters[0]['keyword1']);
        $myInstitution->setKeyword2($parameters[0]['keyword2']);
        $myInstitution->setThumbnail($parameters[0]['thumbnail']);

        return $myInstitution;
    }

    public function getIdOfAddedInstitution() {
        $id = $this->_objDbInstitution->getLastInstitutionId();

        return $id[0]['id'];
    }

    function getInstitutionId() {
        return $this->_institution->getId();
    }

    function getInstitutionName() {
        return $this->_institution->getName();
    }

    function getInstitutionDescription() {
        return $this->_institution->getDescription();
    }

    function getInstitutionType() {
        $typeId = $this->_institution->getType();

        return $this->_objDbInstitutionType->getType($typeId);
    }

    function getInstitutionTypeID() {
        return $this->_institution->getType();
    }

    function getInstitutionZip() {
        return $this->_institution->getZip();
    }

    function getInstitutionCity() {
        return $this->_institution->getCity();
    }

    function getInstitutionWebsiteLink() {
        return $this->_institution->getWebsiteLink();
    }

//Get the country name by using the country ID stored in the database
    function getInstitutionCountry() {
        $countryId = $this->_institution->getCountry();

        return $this->_objCountry->getName($countryId);
    }

    function getInstitutionCountryId() {
        return $this->_institution->getCountry();
    }

    function getInstitutionThumbnail() {
        return $this->_institution->getThumbnail();
    }

    function getInstitutionKeywords() {
        $keywords = array(
            "keyword1" => $this->_institution->getKeyword1(),
            "keyword2" => $this->_institution->getKeyword2());

        return $keywords;
    }

    function getInstitutionAddress() {
        $address = array(
            "address1" => $this->_institution->getAddress1(),
            "address2" => $this->_institution->getAddress2(),
            "address3" => $this->_institution->getAddress3());

        return $address;
    }

//Get the values of all the current institution in an array
    function getInstitutionData() {
        $institutionData['name'] = $this->getInstitutionName();
        $institutionData['id'] = $this->getInstitutionId();
        $institutionData['description'] = $this->getInstitutionDescription();
        $institutionData['type'] = $this->getInstitutionType();
        $institutionData['country'] = $this->getInstitutionCountryId();
        $address = $this->getInstitutionAddress();
        $institutionData['address1'] = $address['address1'];
        $institutionData['address2'] = $address['address2'];
        $institutionData['address3'] = $address['address3'];
        $institutionData['zip'] = $this->getInstitutionZip();
        $institutionData['city'] = $this->getInstitutionCity();
        $institutionData['websiteLink'] = $this->getInstitutionWebsiteLink();
        $keywords = $this->getInstitutionKeywords();
        $institutionData['keyword1'] = $keywords['keyword1'];
        $institutionData['keyword2'] = $keywords['keyword2'];
        $institutionData['thumbnail'] = $this->getInstitutionThumbnail();

        return $institutionData;
    }

    /**
     * Used fo uploading institution thumbnail
     * 
     */
    function doajaxupload() {
        $dir = $this->objConfig->getcontentBasePath();

        $generatedid = $this->getParam('id');
        $filename = $this->getParam('filename');

        $objMkDir = $this->getObject('mkdir', 'files');

        $institutionId = $this->getParam('itemid');
        $destinationDir = $dir . '/oer/institutions/' . $institutionId;

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
            $data = array("thumbnail" => "/oer/institutions/" . $institutionId . "/" . $filename);
            $dbInstitions = $this->getObject("dbinstitution", "oer");
            $dbInstitions->updateInstitution($data, $institutionId);

            $params = array('action' => 'showthumbnailuploadresults', 'id' => $generatedid, 'fileid' => $id, 'filename' => $filename);

            return $params;
        }
    }

    function validate($name, $description, $type, $country, $address1, $address2, $address3, $zip, $city, $websiteLink, $keyword1, $keyword2, $thumbnail) {
        $this->_validation['valid'] = TRUE;
//Check if a name has been provided
        if (empty($name)) {
            $this->_validation['valid'] = FALSE;
            $nameErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_name_error', 'unesco_oer');
            $this->_validation['name'] = $nameErrMsg;
        }

//Ensure that a description has been provided
        if (empty($description)) {
            $this->_validation['valid'] = FALSE;
            $descriptionErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_type_error', 'unesco_oer');
            $this->_validation['description'] = $descriptionErrMsg;
        }

//Ensure that a type has been selected
        if (empty($type)) {
            $this->_validation['valid'] = FALSE;
            $typeErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_description_error', 'unesco_oer');
            $this->_validation['type'] = $typeErrMsg;
        }

//Ensure that a country has been selected
        if (empty($country)) {
            $this->_validation['valid'] = FALSE;
            $countryErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_country_error', 'unesco_oer');
            $this->_validation['country'] = $countryErrMsg;
        }
//Ensure that an address1 has been provided
        if (empty($address1)) {
            $this->_validation['valid'] = FALSE;
            $addressErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_address_error', 'unesco_oer');
            $this->_validation['address1'] = $addressErrMsg;
        }

//Ensure that a city has been provided
        if (empty($city)) {
            $this->_validation['valid'] = FALSE;
            $cityErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_city_error', 'unesco_oer');
            $this->_validation['city'] = $cityErrMsg;
        }

//Ensure that a zip has been provided
        if (empty($zip)) {
            $this->_validation['valid'] = FALSE;
            $zipErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_zip_error', 'unesco_oer');
            $this->_validation['zip'] = $zipErrMsg;
        }

//Ensure that a websitelink has been provided
        if (empty($websiteLink)) {
            $this->_validation['valid'] = FALSE;
            $urlErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_websitelink_error', 'unesco_oer');
            $this->_validation['websiteLink'] = $urlErrMsg;
        }

//Ensure that at least 1 keyword has been provided
        if (empty($keyword1)) {
            $this->_validation['valid'] = FALSE;
            $keywordErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_keyword_error', 'unesco_oer');
            $this->_validation['keyword1'] = $keywordErrMsg;
        }

//Ensure that thumbnail is provided
        if (empty($thumbnail)) {
            $this->_validation['valid'] = FALSE;
            $thumbnailErrMsg = $this->_objLanguage->languageText('mod_unesco_oer_institution_thumbnail_error', 'unesco_oer');
            $this->_validation['thumbnail'] = $thumbnailErrMsg;
        }

        return $this->_validation;
    }

    /**
     * creates a view of the institution with adaptations
     * @param type $institutionId
     * @return type 
     */
    function buildViewInstitutionDetails($institutionId) {
        $this->loadClass("link", "htmlelements");
        $objLanguage = $this->getObject("language", "language");
        $objDbInstitution = $this->getObject("dbinstitution", "oer");
        $institution = $objDbInstitution->getInstitutionById($institutionId);

        $table = $this->getObject("htmltable", "htmlelements");

        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getId("InstitutionCreators");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $userId = $this->objUser->userId();

        $table->attributes = "style='table-layout:fixed;'";

        $leftContent = "";
        $thumbnail = '<img src="usrfiles/' . $institution['thumbnail'] . '"  width="136" height="176" align="left"/>';
        if ($institution['thumbnail'] == '') {
            $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"   width="136" height="176" align="left"/>';
        }

        $editControls = "";
        if ($objGroupOps->isGroupMember($groupId, $userId)) {
            $editImg = '<img src="skins/oeru/images/icons/edit.png">';
            $deleteImg = '<img src="skins/oeru/images/icons/delete.png">';
            $adaptImg = '<img src="skins/oeru/images/icons/add.png">';
            $featuredImg = '<img src="skins/oeru/images/featured.png">';


            $editLink = new link($this->uri(array("action" => "editoriginalproductstep1", "id" => $institutionId, "mode" => "edit")));
            $editLink->link = $editImg;
            $editLink->cssClass = "editoriginalproduct";
            $editControls.="" . $editLink->show();

            $deleteLink = new link($this->uri(array("action" => "deleteoriginalproduct", "id" => $institutionId)));
            $deleteLink->link = $deleteImg;
            $deleteLink->cssClass = "deleteoriginalproduct";
            $editControls.="" . $deleteLink->show();

            $featuredLink = new link($this->uri(array("action" => "featureoriginalproduct", "productid" => $institutionId)));
            $featuredLink->link = $featuredImg;
            $featuredLink->cssClass = "featuredoriginalproduct";
            $editControls.="" . $featuredLink->show();
        }



        $leftContent.='<h1 class="viewproduct_title">' . $institution['name'] . '</h1>';
        $leftContent.='<div id="viewproduct_coverpage">' . $thumbnail . '</div>';


        $leftContent.=$institution['description'];


        $dbProducts = $this->getObject("dbproducts", "oer");
        $adaptationCount = $dbProducts->getProductAdaptationCountByInstitution($institutionId);
        $limit = 10;
        $fragment = 0;
//$fragment = $limit / $adaptationCount;

        $adaptations = $dbProducts->getRandomAdaptationsByInstitution($fragment, $limit);

        $randomAdaptations = '<div id="randomadaptations">';

        foreach ($adaptations as $adaptation) {

            $thumbnail = '<img src="usrfiles/' . $adaptation['thumbnail'] . '"  width="45" height="49"  align="left"/>';
            if ($adaptation['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"   width="45" height="49"  align="left"/>';
            }

            $title = $adaptation['title'];
            $link = new link($this->uri(array("action" => 'viewadaptation', "id" => $adaptation['id'])));
            $link->link = $title;

            $language = $adaptation['language'];
            $randomAdaptations.='<div id ="randomadaptation">';
            $randomAdaptations.=$thumbnail . '<br/>';
            $randomAdaptations.='<h4 class="view_institution">' . $link->show() . '</h4>';
            $randomAdaptations.=$objLanguage->languageText('mod_oer_adaptedin', 'oer') . ':' . $language;
            $randomAdaptations.='</div>';
        }

        $randomAdaptations.='</div>';
        $filtermanager = $this->getObject("filtermanager", "oer");
        $filter = $filtermanager->buildFilterProductsForm('filteradaptations', 'mod_oer_typeofadaptation');

        $sectionsContent = '<div id="institution_sections"><table><tr><td align="left" valign="top">' . $filter . '</td><td class="institution_randomadaptation" align="left" valign="top">' . $randomAdaptations . '</td></tr></table></div>';

        $leftContent.=$sectionsContent;

        $rightContent = "";
//Add bookmark
        $objBookMarks = $this->getObject('socialbookmarking', 'utilities');
        $objBookMarks->options = array('stumbleUpon', 'delicious', 'newsvine', 'reddit', 'muti', 'facebook', 'addThis');
        $objBookMarks->includeTextLink = FALSE;
        $bookmarks = $objBookMarks->show();


//Get institution type
        $objDbInstitutionType = $this->getObject("dbinstitutiontypes", "oer");
        $instType = $objDbInstitutionType->getType($institution['type']);

        $instName = $this->objLanguage->languageText('mod_oer_fullinfo', 'oer');

        $rightContent.='<div id="viewadaptation_label"><b>' . $this->objLanguage->languageText('mod_oer_typeofinstitution_label', 'oer') . ':</b></div>
            <div id="viewadaptation_unesco_contacts_text"> ' . $instType . '</div><br/><br/>';
        $rightContent.='<div id="viewadaptation_label">' . $this->objLanguage->languageText('mod_oer_group_country', 'oer') . ':</div>
            <div id="viewadaptation_text">' . $institution['country'] . '</div><br/><br/>';

        $rightContent.='<div id="viewproduct_keywords_label">' . $objLanguage->languageText('mod_oer_keywords', 'oer') . ': ' . $institution['keyword1'] . $institution['keyword2'] . '</div><br/><br/>';

        $rightContent.='<div id="viewproduct_relatedevents_label">' . $objLanguage->languageText('mod_oer_relatedevents', 'oer') . ':</div><br/><br/>';


        $table->startRow();


        $table->addCell('<div id="viewproduct_leftcontent">' . $leftContent . '</div>', "60%", "top", "left");


        $table->addCell('<div id="viewproduct_rightcontent>' . $rightContent . '</div>', "40%", "top", "left");

        $table->endRow();

        $homeLink = new link($this->uri(array("action" => "home")));
        $homeLink->link = $objLanguage->languageText('mod_oer_home', 'system');


        return '<br/><div id="viewinstitution">' . $table->show() . '</div>';
    }

}

?>