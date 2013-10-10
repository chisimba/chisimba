<?php
/**
 * This class contains util methods for displaying full original product details
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

 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author     pwando paulwando@gmail.com
 */

/**
 * This method contains util methods for comparing a product's adaptations
 *
 * @author pwando
 */
class compareadaptations extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->rootTitle = $this->objLanguage->languageText('mod_oer_none', 'oer');
        //Load htmlelement classes
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        //Get DB Objects
        $this->dbProducts = $this->getObject('dbproducts', 'oer');
        $this->objUser = $this->getObject("user", "security");
        $this->dbSectionNode = $this->getObject("dbsectionnodes", "oer");
        $this->objAdaptationManager = $this->getObject("adaptationmanager", "oer");
        $this->objDbInstitution = $this->getObject("dbinstitution", "oer");
        $this->sectionManager = $this->getObject('sectionmanager', 'oer');
    }

    /**
     * Build detailed section view
     * @param String $productId
     * @param String $sectionId
     * @return string
     */
    function buildCompareView($productId, $sectionId, $mode, $selected = "") {
        //Flag to check if user has perms to manage adaptations
        $hasPerms = $this->objAdaptationManager->userHasPermissions();

        //Get selected section-node data
        if (empty($selected)) {
            $node = $this->dbSectionNode->getSectionNode($selected);
        }
        $isOriginalProduct = $this->dbProducts->isOriginalProduct($productId);

        //get product data
        $product = $this->dbProducts->getProduct($productId);

        //Get product adaptations
        $productAdaptations = $this->dbProducts->getAllProductAdaptations($productId, '');

        $instData = $this->objDbInstitution->getInstitutionById($product["institutionid"]);

        //Flag to check if user has perms to manage adaptations
        $hasPerms = $this->objAdaptationManager->userHasPermissions();

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
            $navpath = $prodListPage . " > " . $product['title'];
            //Link for - view product for this section
            $viewProdTitleLink = new link($this->uri(array("action" => "vieworiginalproduct", "id" => $product["id"], "mode" => "grid")));
            $viewProdTitleLink->link = $product['title'];
            $viewProdTitle = $viewProdTitleLink->show();
        } else {
            //Get parent prod data
            $parentProduct = $this->dbProducts->getProduct($product["parent_id"]);

            //Link for - adaptation list
            $adaptListLink = new link($this->uri(array("action" => "viewadaptation", "id" => $productId)));
            $adaptListLink->link = $this->objLanguage->languageText('mod_oer_adaptations', 'oer');
            $adaptListPage = $adaptListLink->show();
            $navpath = $adaptListPage . " > " . $viewInstTitle . " > " . $product['title'];
            //Link for - original product for this adaptation
            $viewParentTitleLink = new link($this->uri(array("action" => "vieworiginalproduct", "id" => $product["parent_id"], "mode" => "grid")));
            $viewParentTitleLink->link = $parentProduct['title'];
            $viewParentTitle = $viewParentTitleLink->show();
        }

        $homeLink = new link($this->uri(array("action" => "home")));
        $homeLink->link = $this->objLanguage->languageText('mod_oer_home', 'system');


        $objTools = $this->newObject('tools', 'toolbar');
        $crumbs = array($homeLink->show());
        $objTools->addToBreadCrumbs($crumbs);

        $contentTable = $this->getObject("htmltable", "htmlelements");
        $contentTable->attributes = "style='table-layout:fixed;'";
        $contentTable->border = 0;
        $contentTable->cellpadding = 5;
        $contentTable->cellspacing = 5;

        //Fetch section for the original product/adaptation tree
        $navigator = $this->sectionManager->buildSectionsTree($productId, '', "false", 'compare', $selected, "", "", $productId);

        $rightContent = "";
        $rightContent = '<div class="compareProductNav"><div class="frame">' . $navigator . '</div></div>';
        $contentTable->startRow();
        if (count($productAdaptations) < 2) {
            $contentTable->addCell($rightContent, "", "top", "left", "", 'style="width:60px"');
        } else {
            $contentTable->addCell($rightContent, "", "top", "left", "", 'style="width:190px"');
        }
        //Show navigation for each of the product's adaptations
        if (count($productAdaptations) > 0) {
            foreach ($productAdaptations as $prodAdaptation) {
                $adaptNav = $this->sectionManager->buildSectionsTree($prodAdaptation["id"], '', "false", 'compare', $selected, "", "", $productId);
                $adaptContent = '<div class="compareAdaptationsNav"><div class="frame">' . $adaptNav . '</div></div>';
                $contentTable->addCell($adaptContent, "", "top", "left", '', 'style="width:190px"');
            }
        }
        $contentTable->endRow();

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
            $adaptationCount = $this->dbProducts->getProductAdaptationCount($productId);
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
        $seachElements = "";
        //Create fieldset for compare tools
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_comparetools', 'oer', 'Compare tools'));

        //Link for - compare selected
        if (!empty($selected)) {
            $compareIcon = '<img src="skins/oeru/images/product_theme.png" class="smallIcons" />';
            $compareSelectedLink = new link($this->uri(array("action" => "compare_selected", "productid" => $productId, "selected" => $selected)));
            $compareSelectedLink->link = $this->objLanguage->languageText('mod_oer_compareselected', 'oer', "Compare selected");
            $seachElements .= $compareIcon . " " . $compareSelectedLink->show();
        }

        //Link for - clear selection
        if (!empty($selected)) {
            $clearSelectionIcon = '<img src="skins/oeru/images/template_resources/sort-by-grid.png" class="smallIcons" />';
            $clearSelectionLink = new link($this->uri(array("action" => "compareadaptations", "productid" => $productId)));
            $clearSelectionLink->link = $this->objLanguage->languageText('mod_oer_clearselection', 'oer', "Clear selection");
            $seachElements .= "&nbsp;&nbsp;" . $clearSelectionIcon . " " . $clearSelectionLink->show();
        }
        //Build search box
        $textinput = new textinput('search_text');
        $textinput->size = 30;
        $seachElements .= "&nbsp;&nbsp;" . $textinput->show();

        //Search button
        $button = new button('save', $this->objLanguage->languageText('word_search', 'system', 'Search'));
        $button->setToSubmit();
        $seachElements .= '&nbsp;&nbsp;' . $button->show();

        $fieldset->addContent($seachElements);
        //Form for compare tools
        $formData = new form('compareadaptations', $this->uri(array("action" => "search_compare_adaptations", "productid" => $productId)));
        $formData->addToForm($fieldset);
        $searchForm = $formData->show();

        return '<div class="navPath">' . $navpath .
        '</div><div class="topContentHolder">' . $topStuff . '</div><br/><div class="searchCompare">' . $searchForm . '</div>
            <div class="mainContentHolder"><div class="frame">' . $contentTable->show() . '</div></div>';
    }

    /**
     * Build detailed section view
     * @param String $productId
     * @param String $sectionId
     * @return string
     */
    function buildCompareSelectedView($productId, $sectionId, $mode, $selected, $selSecId="", $selAdaptId="") {
        //Flag to check if user has perms to manage adaptations
        $hasPerms = $this->objAdaptationManager->userHasPermissions();

        //Get selected section-node data
        if (empty($selected)) {
            $node = $this->dbSectionNode->getSectionNode($selected);
        }
        $isOriginalProduct = $this->dbProducts->isOriginalProduct($productId);

        //get product data
        $product = $this->dbProducts->getProduct($productId);

        //Get product adaptations
        $productAdaptations = $this->dbProducts->getAllProductAdaptations($productId, '');

        $instData = $this->objDbInstitution->getInstitutionById($product["institutionid"]);

        //Flag to check if user has perms to manage adaptations
        $hasPerms = $this->objAdaptationManager->userHasPermissions();

        //Add bookmark
        $objBookMarks = $this->getObject('socialbookmarking', 'utilities');
        $objBookMarks->options = array('stumbleUpon', 'delicious', 'newsvine', 'reddit', 'muti', 'facebook', 'addThis');
        $objBookMarks->includeTextLink = FALSE;
        $bookmarks = $objBookMarks->show();

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
            $navpath = $prodListPage . " > " . $product['title'];
            //Link for - view product for this section
            $viewProdTitleLink = new link($this->uri(array("action" => "vieworiginalproduct", "id" => $product["id"], "mode" => "grid")));
            $viewProdTitleLink->link = $product['title'];
            $viewProdTitle = $viewProdTitleLink->show();
        } else {
            //Get parent prod data
            $parentProduct = $this->dbProducts->getProduct($product["parent_id"]);

            //Link for - adaptation list
            $adaptListLink = new link($this->uri(array("action" => "viewadaptation", "id" => $productId)));
            $adaptListLink->link = $this->objLanguage->languageText('mod_oer_adaptations', 'oer');
            $adaptListPage = $adaptListLink->show();
            $navpath = $adaptListPage . " > " . $viewInstTitle . " > " . $product['title'];
            //Link for - original product for this adaptation
            $viewParentTitleLink = new link($this->uri(array("action" => "vieworiginalproduct", "id" => $product["parent_id"], "mode" => "grid")));
            $viewParentTitleLink->link = $parentProduct['title'];
            $viewParentTitle = $viewParentTitleLink->show();
        }

        $homeLink = new link($this->uri(array("action" => "home")));
        $homeLink->link = $this->objLanguage->languageText('mod_oer_home', 'system');


        $objTools = $this->newObject('tools', 'toolbar');
        $crumbs = array($homeLink->show());
        $objTools->addToBreadCrumbs($crumbs);

        $contentTable = '<table border="0" style="table-layout:fixed;" >';


        //Fetch section for the original product/adaptation tree
        $navigator = $this->sectionManager->buildSectionsTree($productId, '', "false", 'compare', $selected, "", "", $productId);

        $rightContent = "";
        $rightContent = '<div class="compareProductNav"><div class="frame">' . $navigator . '</div></div>';
        $contentTable .= "<tr>";
        $selectedSecContent = "";
        //Show selected product nodes nav
        if (!empty($productId)) {
                //get the selected product sections
                $selectedProdNodes = $this->sectionManager->getSelectedNodes($productId, $selected);

                if (count($selectedProdNodes) > 0) {
                    foreach ($selectedProdNodes as $selectedNode) {
                        //product thumbnail
                        $prodthumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '" class="featuredadaptation" width="45" height="49" align="left"/>';
                        if ($product['thumbnail'] == '') {
                            $prodthumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg" class="featuredadaptation"  width="45" height="49" align="left"/>';
                        }
                        //product thumbnail link
                        $prodThumbnailLink = new link($this->uri(array("action" => "compare_selected",
                                            "productid" => $productId,
                                            "selected" => $selected,
                                            "selsecid" => $nodeData['id'],
                                            "seladaptid" => $prodAdaptation['id'])));
                        $prodThumbnailLink->link = $prodthumbnail;
                        $prodThumbnail = $prodThumbnailLink->show();
                        //Inst title link
                        $prodTitleLink = new link($this->uri(array("action" => "compare_selected",
                                            "productid" => $productId,
                                            "selected" => $selected,
                                            "selsecid" => $nodeData['id'],
                                            "seladaptid" => $prodAdaptation['id'])));
                        $prodTitleLink->link = $product['title'];
                        $prodTitle = $prodTitleLink->show();

                        $compareLk = $prodThumbnail . "" . $prodTitle;
                        //Get node data
                        $nodeData = $this->dbSectionNode->getSectionNode($selectedNode);

                        $newCtAdapt = "";
                        if ($hasPerms) {
                            //Link for - adapting product from existing adapatation
                            $newCtAdaptLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $prodAdaptation['id'], 'mode="new"')));
                            //Check if original product or an adaptation
                            $isOriginalProduct = $this->dbProducts->isOriginalProduct($prodAdaptation['id']);
                            if ($isOriginalProduct) {
                                $newCtAdaptLink->link = $this->objLanguage->languageText('mod_oer_makenewadaptation', 'oer');
                            } else {
                                $newCtAdaptLink->link = $this->objLanguage->languageText('mod_oer_makenewadaptation', 'oer');
                            }
                            $newCtAdapt = "<br/><br/>" . $newCtAdaptLink->show();
                        }

                        $contentTable .= '<td align="left" valign="top"><div class="compareSelectedAdaptationNav">' .
                                $compareLk . $newCtAdapt . '</div></td>';
                        if (empty($selSecId) && empty($selAdaptId)) {
                            $selSecId = $nodeData['id'];
                            $selAdaptId = $prodAdaptation["id"];
                        }
                        $nodeData = "";
                        $compareLk = "";
                        $newCtAdapt = "";
                    }
                }
            }
        //Show selected section nodes for this product adaptations
        if (count($productAdaptations) > 0) {
            foreach ($productAdaptations as $prodAdaptation) {

                //get the selected sections
                $selectedNodes = $this->sectionManager->getSelectedNodes($prodAdaptation["id"], $selected);

                if (count($selectedNodes) > 0) {
                    foreach ($selectedNodes as $selectedNode) {
                        //Get institution data
                        $instData = $this->objDbInstitution->getInstitutionById($prodAdaptation['institutionid']);
                        //Inst thumbnail
                        $instthumbnail = '<img src="usrfiles/' . $instData['thumbnail'] . '" class="featuredadaptation" width="45" height="49" align="left"/>';
                        if ($instData['thumbnail'] == '') {
                            $instthumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg" class="featuredadaptation"  width="45" height="49" align="left"/>';
                        }
                        //Inst thumbnail link
                        $instThumbnailLink = new link($this->uri(array("action" => "compare_selected",
                                            "productid" => $productId,
                                            "selected" => $selected,
                                            "selsecid" => $nodeData['id'],
                                            "seladaptid" => $prodAdaptation['id'])));
                        $instThumbnailLink->link = $instthumbnail;
                        $instThumbnail = $instThumbnailLink->show();
                        //Inst title link
                        $instTitleLink = new link($this->uri(array("action" => "compare_selected",
                                            "productid" => $productId,
                                            "selected" => $selected,
                                            "selsecid" => $nodeData['id'],
                                            "seladaptid" => $prodAdaptation['id'])));
                        $instTitleLink->link = $instData['name'];
                        $instTitle = $instTitleLink->show();

                        $compareLk = $instThumbnail . "" . $instTitle;
                        //Get node data
                        $nodeData = $this->dbSectionNode->getSectionNode($selectedNode);
                        
                        $newCtAdapt = "";
                        if ($hasPerms) {
                            //Link for - adapting product from existing adapatation
                            $newCtAdaptLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $prodAdaptation['id'], 'mode="new"')));
                            //Check if original product or an adaptation
                            $isOriginalProduct = $this->dbProducts->isOriginalProduct($prodAdaptation['id']);
                            if ($isOriginalProduct) {
                                $newCtAdaptLink->link = $this->objLanguage->languageText('mod_oer_makenewadaptation', 'oer');
                            } else {
                                $newCtAdaptLink->link = $this->objLanguage->languageText('mod_oer_makenewfromadaptation', 'oer');
                            }
                            $newCtAdapt = "<br/><br/>" . $newCtAdaptLink->show();
                        }

                        $contentTable .= '<td align="left" valign="top"><div class="compareSelectedAdaptationNav">' .
                                $compareLk . $newCtAdapt . '</div></td>';
                        if (empty($selSecId) && empty($selAdaptId)) {
                            $selSecId = $nodeData['id'];
                            $selAdaptId = $prodAdaptation["id"];
                        }
                        $nodeData = "";
                        $compareLk = "";
                        $newCtAdapt = "";
                    }
                }
            }
            //get node data
            $selectedSecContent = $this->sectionManager->buildSectionView($selAdaptId, $selSecId,null, false);
        }
        $contentTable .= "</tr></table>";

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
            $adaptationCount = $this->dbProducts->getProductAdaptationCount($productId);
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
        $seachElements = "";
        //Create fieldset for compare tools
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_comparetools', 'oer', 'Compare tools'));

        //Link for - compare selected
        if (!empty($selected)) {
            $compareIcon = '<img src="skins/oeru/images/product_theme.png" class="smallIcons" />';
            $compareSelectedLink = new link($this->uri(array("action" => "compare_selected", "productid" => $productId, "selected" => $selected)));
            $compareSelectedLink->link = $this->objLanguage->languageText('mod_oer_compareselected', 'oer', "Compare selected");
            $seachElements .= $compareIcon . " " . $compareSelectedLink->show();
        }

        //Link for - clear selection
        if (!empty($selected)) {
            $clearSelectionIcon = '<img src="skins/oeru/images/template_resources/sort-by-grid.png" class="smallIcons" />';
            $clearSelectionLink = new link($this->uri(array("action" => "compareadaptations", "productid" => $productId)));
            $clearSelectionLink->link = $this->objLanguage->languageText('mod_oer_clearselection', 'oer', "Clear selection");
            $seachElements .= "&nbsp;&nbsp;" . $clearSelectionIcon . " " . $clearSelectionLink->show();
        }

        return '<div class="navPath">' . $navpath .
        '</div><div class="topContentHolder">' . $topStuff . '</div><br/>
            <div class="contentTableHolder"><div class="frame">' . $contentTable .
        '</div></div><br/><br/>
                <div class="mainContentHolder">' .
        $selectedSecContent . '</div>';
    }

}

?>