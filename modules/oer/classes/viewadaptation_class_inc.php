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
 * This class contains util methods for displaying adaptations
 *
 * @author pwando
 */
class viewadaptation extends object {

    function init() {
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass("link", "htmlelements");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("radio", "htmlelements");
        $this->objLanguage = $this->getObject("language", "language");
        $this->objDbProducts = $this->getObject("dbproducts", "oer");
        $this->objDbProductComments = $this->getObject("dbproductcomments", "oer");
        $this->objDbInstitution = $this->getObject("dbinstitution", "oer");
        $this->objDbInstitutionType = $this->getObject("dbinstitutiontypes", "oer");
        $this->objAdaptationManager = $this->getObject("adaptationmanager", "oer");
        $this->objUser = $this->getObject("user", "security");
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->loadJS();
        //Flag to check if user has perms to manage adaptations
        $this->hasPerms = $this->objAdaptationManager->userHasPermissions();
        //Flag to check if user is logged in
        $this->isLoggedIn = $this->objUser->isLoggedIn();
        $this->setupLanguageItems();
    }

    /**
     * sets up necessary lang items for use in js
     */
    function setupLanguageItems() {
        // Serialize language items to Javascript
        $arrayVars['totalvotestext'] = "mod_oer_productrating";
        $arrayVars['confirm_delete_adaptation'] = "mod_oer_confirm_delete_adaptation";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oer');
    }

    /**
     * JS an CSS for product rating and product download
     */
    function loadJS() {
        $ratingUIJs = '<script language="JavaScript" src="' . $this->getResourceUri('jquery.ui.stars.js') . '" type="text/javascript"></script>';
        $ratingEffectJs = '<script language="JavaScript" src="' . $this->getResourceUri('ratingeffect.js') . '" type="text/javascript"></script>';

        $ratingUICSS = '<link rel="stylesheet" type="text/css" href="skins/oeru/jquery.ui.stars.min.css">';
        $crystalCSS = '<link rel="stylesheet" type="text/css" href="skins/oeru/crystal-stars.css">';
        $dialogCSS = '<link rel="stylesheet" type="text/css" href="skins/oeru/download-dialog.css">';

        $uiAllCSS = '<link rel="stylesheet" type="text/css" href="' . $this->getResourceUri('plugins/ui/development-bundle/themes/base/jquery.ui.all.css', 'jquery') . '"/>';
        $this->appendArrayVar('headerParams', $uiAllCSS);
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.core.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.widget.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.mouse.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.draggable.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.position.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.resizable.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/ui/development-bundle/ui/jquery.ui.dialog.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('downloader.js'));
        $this->appendArrayVar('headerParams', $ratingEffectJs);
        $this->appendArrayVar('headerParams', $ratingUIJs);
        $this->appendArrayVar('headerParams', $ratingUICSS);
        $this->appendArrayVar('headerParams', $crystalCSS);
        $this->appendArrayVar('headerParams', $dialogCSS);
    }

    /**
     * Builds a div for rating
     * @return string
     */
    function createRatingDiv($productId) {
        $options = array(
            1 => array('title' => $this->objLanguage->languageText('mod_oer_notsogreat', 'oer')),
            2 => array('title' => $this->objLanguage->languageText('mod_oer_quitegood', 'oer')),
            3 => array('title' => $this->objLanguage->languageText('mod_oer_good', 'oer')),
            4 => array('title' => $this->objLanguage->languageText('mod_oer_great', 'oer')),
            5 => array('title' => $this->objLanguage->languageText('mod_oer_excellent', 'oer')));
        $dbProductRating = $this->getObject("dbproductrating", "oer");
        $totalRating = $dbProductRating->getTotalRating($productId);
        $avg = $totalRating;
        foreach ($options as $id => $val) {
            $options[$id]['disabled'] = 'disabled="disabled"';
            $options[$id]['checked'] = $id == $avg ? 'checked="checked"' : '';
        }

        $div = '<form id="rat" action="" method="post">';

        //$radio = new radio('rate');
        foreach ($options as $id => $rb) {
            $div.='<input type="radio" name="rate" value="' . $id . '|' . $productId . '|' . $this->objUser->userId() . '" title="' . $rb['title'] . ' ' . $rb['checked'] . ' ' . $rb['disabled'] . '/>';
        }
        $div.='</form><div id="loader"><div style="padding-top: 5px;">' . $this->objLanguage->languageText('mod_oer_pleasewait', 'oer') . '...</div></div>';
        $div.='<div id="votes">' . $this->objLanguage->languageText('mod_oer_productrating', 'oer') . ': ' . $totalRating . '</div>';

        return $div;
    }

    /**
     * Function that creates a view of existing adaptations
     *
     * @param string $productId
     * @return form
     */
    function buildAdaptationView($productId) {
        $product = $this->objDbProducts->getProduct($productId);

        $table = $this->getObject("htmltable", "htmlelements");
        $table->attributes = "style='table-layout:fixed;'";
        $table->border = 0;

        $leftContent = "";

        $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="79" height="101" align="left"/>';
        if ($product['thumbnail'] == '') {
            $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="79" height="101" align="left"/>';
        }
        $leftContent.='<div id="viewadaptation_coverpage">' . $thumbnail . '</div>';
        $ratingDiv = $this->createRatingDiv($productId);

        $newAdapt = "";
        if ($this->hasPerms) {
            //Link for - adapting product from existing adapatation
            $newAdaptLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $productId, 'mode="new"')));
            $newAdaptLink->link = $this->objLanguage->languageText('mod_oer_makenewfromadaptation', 'oer');
            $newAdaptLink->extra = 'alt="' . $this->objLanguage->languageText('word_edit', "system", "Edit") . '"';
            $newAdaptLink->title = $this->objLanguage->languageText('word_edit', "system", "Edit");
            $newAdapt = $newAdaptLink->show();
        }

        //Link for - See existing adaptations of this UNESCO Product
        $existingAdaptationsLink = new link($this->uri(array("action" => "adaptationlist", "productid" => $productId)));
        $existingAdaptationsLink->link = $this->objLanguage->languageText('mod_oer_existingadaptations', 'oer');
        $existingAdaptations = $existingAdaptationsLink->show();

        //Link for - Full view of product
        $fullProdViewLink = new link($this->uri(array("action" => "fullviewadaptation", "id" => $productId)));
        $fullProdViewLink->link = $this->objLanguage->languageText('mod_oer_productfullinfo', 'oer');
        $fullProdView = $fullProdViewLink->show();

        //Link for - Full view of product
        $compareAdaptsLink = new link($this->uri(array("action" => "compareadaptations", "productid" => $productId)));
        $compareAdaptsLink->link = $this->objLanguage->languageText('mod_oer_compareadaptation', 'oer', 'Compare adaptations');
        $compareAdapts = $compareAdaptsLink->show();

        $fullProdView .= "<br/><br/>" . $compareAdapts . "<br/>";

        $sections = "";
        $sectionTitle = '<h3>' . $this->objLanguage->languageText('mod_oer_sections', 'oer') . '</h3>';
        if ($this->hasPerms) {
            $addSectionIcon = '<img src="skins/oeru/images/add-node.png"/>';
            $addNodeLink = new link($this->uri(array("action" => "addsectionnode", "productid" => $productId)));
            $addNodeLink->link = $addSectionIcon . "&nbsp;&nbsp;" . $this->objLanguage->languageText('mod_oer_addnode', 'oer');
            $newNodeLink->extra = 'alt="' . $this->objLanguage->languageText('word_add', 'system') . '"';
            $sections.=$addNodeLink->show();
        }

        //Get comments
        $prodcomments = "";
        $objWallOps = $this->getObject('wallops', 'wall');

        $numOfPostsToDisplay = 10;
        $wallType = '4';
        $comments = '';
        if ($this->hasPerms) {
            $comments = $objWallOps->showObjectWall('identifier', $productId, 0, $numOfPostsToDisplay);
        } else {
            $keyValue = $productId;
            $keyName = 'identifier';
            $dbWall = $this->getObject('dbwall', 'wall');
            $posts = $dbWall->getMorePosts($wallType, 0, $keyName, $keyValue, $numOfPostsToDisplay);
            $numPosts = $dbWall->countPosts($wallType, FALSE, $keyName, $keyValue);
            $str = '';
            if ($numPosts <= 10) {
                $str = $objWallOps->showPosts($posts, $numPosts, $wallType, $keyValue, $numOfPostsToDisplay, TRUE, FALSE, FALSE);
            } else {
                $str = $objWallOps->showPosts($posts, $numPosts, $wallType, $keyValue, $numOfPostsToDisplay, FALSE, FALSE, FALSE);
            }

            $comments = "\n\n<div class='wall_wrapper' id='wall_wrapper_{$keyValue}'>\n" . $str . "\n</div>\n\n";
        }
        $prodcomments.='<div id="viewproduct_usercomments_label">' . $this->objLanguage->languageText('mod_oer_usercomments', 'oer') . ':' .
                $comments .
                '</div>';

        $sectionManager = $this->getObject("sectionmanager", "oer");

        $navigator = $sectionManager->buildSectionsTree($product["id"], '');

        $rightContent = "";
        //Get institution details
        if (!empty($product["institutionid"])) {
            //Get adaptation manager
            $managedby = "";
            //Get comments
            $comments = "";
            //Get language..Need to translate lang proper 
            $adaptlang = $product['language'];
            /* if ($product['language'] == "en") {
              $adaptlang = "English";
              } */
            //Get inst data
            $instData = $this->objDbInstitution->getInstitutionById($product["institutionid"]);
            if (!empty($instData)) {
                //Get institution type
                $instType = $this->objDbInstitutionType->getType($instData['type']);

                $instName = $this->objLanguage->languageText('mod_oer_fullinfo', 'oer');
                ;
                $instNameLink = new link($this->uri(array("action" => "viewinstitution", "id" => $product["institutionid"])));
                $instNameLink->link = $instName;
                $instNameLink->cssClass = "viewinstitutionlink";
                $instNameLk = "" . $instNameLink->show();
                $languageCode = $this->getObject("languagecode", "language");
                /* $rightContent.='<div id="viewadaptation_author_label"></div>
                  <div id="viewadaptation_author_text"></div><br/><br/>'; */
                $rightContent.='<div id="viewadaptation_label">' . $this->objLanguage->languageText('mod_oer_adaptedby', 'oer') . ': </div>
            <div id="viewadaptation_text"></div><div class="pinkText">' . $instData['name'] . '</div><br/><br/>';
                $rightContent.='<div id="viewadaptation_label">' . $this->objLanguage->languageText('mod_oer_typeofinstitution_label', 'oer') . ':</div>
            <div id="viewadaptation_unesco_contacts_text"> ' . $instType . '</div><br/><br/>';
                $rightContent.='<div id="viewadaptation_label">' . $this->objLanguage->languageText('mod_oer_group_country', 'oer') . ':</div>
            <div id="viewadaptation_text">' . $languageCode->getName($instData['country']) . '</div><br/><br/>';
                $rightContent.='<div id="viewadaptation_category_label">' . $this->objLanguage->languageText('mod_oer_adaptedin', 'oer') . ':</div>
            <div id="viewadaptation_category_text"> ' . $adaptlang . '</div><br/><br/>';
                $rightContent.='<div id="viewadaptation_keywords_text"> ' . $instNameLk . '</div><br/><br/>';
                $rightContent.='<div id="viewadaptation_keywords_label">' . $this->objLanguage->languageText('mod_oer_managedby', 'oer') . ':</div>
            <div id="viewadaptation_keywords_text"> ' . $managedby . '</div><br/><br/>';
                $rightContent.='<div id="viewadaptation_keywords_text"> ' . $this->objLanguage->languageText('mod_oer_viewgroup', 'oer') . '</div><br/><br/>';
            }
        }
        $featuredAdaptation = "";
        if ($this->hasPerms) {
            //Get images
            $adaptImg = '<img src="skins/oeru/images/icons/add.png">';
            $editImg = '<img src="skins/oeru/images/icons/edit.png">';
            $deleteImg = '<img src="skins/oeru/images/icons/delete.png">';

            //Link create an adaptation
            $adaptLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $productId, "mode" => "new")));
            $adaptLink->link = $adaptImg;
            $adaptLink->cssClass = "createanadaptation";
            $adaptLink->extra = 'alt="' . $this->objLanguage->languageText('mod_oer_makeadaptation', "oer", "Create an adaptation") . '"';
            $adaptLink->title = $this->objLanguage->languageText('mod_oer_createadaptation', "oer", "Create an adaptation");
            $featuredAdaptation.= $adaptLink->show();

            //Link edit adaptation
            $editLink = new link($this->uri(array("action" => "editadaptationstep1", "id" => $productId, "mode" => "edit")));
            $editLink->link = $editImg;
            $editLink->extra = 'alt="' . $this->objLanguage->languageText('word_edit', "system", "Edit") . '"';
            $editLink->title = $this->objLanguage->languageText('word_edit', "system", "Edit");
            $featuredAdaptation .="&nbsp;" . $editLink->show();

            //Link delete adaptation
            $delLink = new link($this->uri(array("action" => "deleteadaptation", "id" => $productId)));
            $delLink->link = $deleteImg;
            $delLink->cssClass = "confirmdeleteadaptation";
            $delLink->extra = 'alt="' . $this->objLanguage->languageText('word_delete', 'system') . '"';
            $delLink->title = $this->objLanguage->languageText('word_delete', 'system');
            $featuredAdaptation .="&nbsp;" . $delLink->show() . "&nbsp;";
            //Add mark as featured adaptation
            $featuredImg = '<img src="skins/oeru/images/featured.png">';
            $featuredLink = new link($this->uri(array("action" => "featureoriginalproduct", "productid" => $productId)));
            $featuredLink->link = $featuredImg;
            $featuredLink->cssClass = "featuredoriginalproduct";
            $featuredLink->extra = 'alt="' . $this->objLanguage->languageText('mod_oer_markfeatured', 'oer', 'Mark as Featured') . '"';
            $featuredLink->title = $this->objLanguage->languageText('mod_oer_markfeatured', 'oer', 'Mark as Featured');
            $featuredAdaptation .= "" . $featuredLink->show();
        }


        $table->startRow();
        $table->addCell('<div id="viewadaptation_leftcontent">' . $leftContent . '</div>', "", "top", "left", "", 'colspan="1", style="width:15%"');
        $table->addCell('<div id="viewadaptation_leftcontent">' . $product['abstract'] . '</div>', "", "top", "left", "", 'colspan="1", style="width:55%"');
        $table->addCell('<div id="viewadaptation_rightcontent">' . $rightContent . $prodcomments . '</div>', "", "top", "left", "", 'rowspan="8", style="width:30%"');
        $table->endRow();
        $table->startRow();
        $table->addCell('&nbsp;', "", "top", "left", "", 'style="width:15%"');
        $table->addCell('&nbsp;', "", "top", "left", "", 'style="width:55%"');
        $table->endRow();
        $table->startRow();
        $table->addCell('&nbsp;', "", "top", "left", "", 'style="width:15%"');
        $table->addCell('<div id="viewadaptation_leftcontent">' . $fullProdView . '</div>', "", "top", "left", "", 'style="width:55%"');
        $table->endRow();
        $table->startRow();
        $table->addCell('&nbsp;', "", "top", "left", "", 'style="width:15%"');
        $table->addCell('<div id="viewadaptation_leftcontent">' . $newAdapt . '</div>', "", "top", "left", "", 'style="width:55%"');
        $table->endRow();
        $table->startRow();
        $table->addCell('&nbsp;', "", "top", "left", "", 'style="width:15%"');
        $table->addCell('<div id="viewadaptation_leftcontent">' . $existingAdaptations . '</div>', "", "top", "left", "", 'style="width:55%"');
        $table->endRow();
        $table->startRow();
        $table->addCell('&nbsp;', "", "top", "left", "", 'style="width:15%"');
        $table->addCell('<div id="viewadaptation_leftcontent">' . $ratingDiv . '</div>', "", "top", "left", "", 'style="width:55%"');
        $table->endRow();

        $table->startRow();
        $table->addCell('<div id="viewadaptation_leftcontent">' . $sectionTitle . '</div>', "", "top", "left", "", 'style="width:15%"');
        $table->addCell('<div id="viewadaptation_leftcontent">' . $sections . '</div>', "", "top", "right", "", 'style="width:55%"');
        $table->endRow();

        $table->startRow();
        $table->addCell('<div id="viewadaptation_navigator">' . $navigator . '</div>', "", "top", "left", "", 'colspan="2",style="width:70%"');
        $table->endRow();

        $homeLink = new link($this->uri(array("action" => "home")));
        $homeLink->link = $this->objLanguage->languageText('mod_oer_home', 'system');


        $objTools = $this->newObject('tools', 'toolbar');
        $crumbs = array($homeLink->show());
        $objTools->addToBreadCrumbs($crumbs);

        //Add bookmark
        $objBookMarks = $this->getObject('socialbookmarking', 'utilities');
        $objBookMarks->options = array('stumbleUpon', 'delicious', 'newsvine', 'reddit', 'muti', 'facebook', 'addThis');
        $objBookMarks->includeTextLink = FALSE;
        $bookmarks = $objBookMarks->show();

        //Add download link
        $printImg = '<img src="skins/oeru/images/icons/icon-download.png">';

        // Download link
        $prodTitle = "";
        if (!$this->isLoggedIn) {
            $printLink = new link("#dialog");
            $printLink->link = $printImg;
            $printLink->cssClass = "downloaderedit";
            $printLink->extra = 'name="modal" onclick="showDownload(); "alt="' . $this->objLanguage->languageText('mod_oer_download', 'oer') . '"';
            $printLink->title = $this->objLanguage->languageText('mod_oer_download', 'oer');
            $printLk = "" . $printLink->show();

            // Login link
            $objLoginLk = new link($this->uri(array("action" => "login"), "security"));
            $objLoginLk->cssId = "loginlink";
            $objLoginLk->link = $this->objLanguage->languageText('mod_oer_clicktologin', 'oer');

            // Register link
            $objRegisterLk = new link($this->uri(array("action" => "selfregister"), "oeruserdata"));
            $objRegisterLk->cssId = "registerlink";
            $objRegisterLk->link = $this->objLanguage->languageText('mod_oer_clickhere', 'oer');

            //Dialogue content
            $toolTipStr = $this->objLanguage->languageText('mod_oer_downloadlnone', 'oer') . ".<br /><br />";
            $toolTipStr .= $this->objLanguage->languageText('mod_oer_downloadlntwo', 'oer') . ".<br /><br />";
            $toolTipStr .= $objRegisterLk->show() . " " . $this->objLanguage->languageText('mod_oer_downloadlnthree', 'oer')
                    . ". " . $this->objLanguage->languageText('mod_oer_readmore', 'oer') . " " . $this->objLanguage->languageText('mod_oer_downloadlnfour', 'oer') . ".<br /><br />";
            $toolTipStr .= $this->objLanguage->languageText('mod_oer_downloadlnfive', 'oer') . " " . $objLoginLk->show() . ". "
                    . $this->objLanguage->languageText('mod_oer_downloadlnsix', 'oer') . ".<br /><br />" . $this->objLanguage->languageText('mod_oer_downloadlnseven', 'oer');
            $buttonTitle = $this->objLanguage->languageText('word_next');
            //$buttonNxt = new button('submit', $buttonTitle);
            $objNextLk = new link($this->uri(array("action" => "downloaderedit", "productid" => $productId, "mode" => "add", 'producttype' => 'adaptation')));
            $objNextLk->cssId = "nextbtnspan";
            $objNextLk->link = $this->objLanguage->languageText('word_next');

            $toolTipStr .= " " . $objNextLk->show();

            $dialogTitle = $this->objLanguage->languageText('mod_oer_downloadproduct', 'oer') . " (" . $this->objLanguage->languageText('mod_oer_adaptation', 'oer') . ")";

            $shareViaEmail = $this->objLanguage->languageText('mod_oer_shareviaemail', 'oer');
            $emailImg = '<img src="skins/_common/icons/em.gif" alt="' . $shareViaEmail . '" title="' . $shareViaEmail . '"/>';
            $bodyLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $product['id'], 'module' => 'oer', "id" => $product['id'])));
            $bodyLink->link = $product['title'];
            $emailLink = '<a  href="mailto:?subject=' . $product['title'] . '&body=' . $bodyLink->href . '">' . $emailImg . '</a>';

            $prodTitle .= '<div class="displaybookmarks">' . $bookmarks . " " . " " . $printLk . '&nbsp;' . $emailLink . '</div><div id="downloader"  title="' . $dialogTitle . '">' . $toolTipStr . '</div>';
        } else {
            $printLink = new link($this->uri(array("action" => "downloaderedit", "productid" => $productId, "mode" => "edit", 'producttype' => 'adaptation')));
            $printLink->link = $printImg;
            $printLink->cssClass = "downloaderedit";
            $printLink->extra = 'alt="' . $this->objLanguage->languageText('mod_oer_download', 'oer') . '"';
            $printLink->title = $this->objLanguage->languageText('mod_oer_download', 'oer');
            //$printLink->target = "_blank";
            $printLk = "" . $printLink->show();

            $shareViaEmail = $this->objLanguage->languageText('mod_oer_shareviaemail', 'oer');
            $emailImg = '<img src="skins/_common/icons/em.gif" alt="' . $shareViaEmail . '" title="' . $shareViaEmail . '"/>';
            $bodyLink = new link($this->uri(array("action" => "vieworiginalproduct", 'identifier' => $product['id'], 'module' => 'oer', "id" => $product['id'])));
            $bodyLink->link = $product['title'];
            $emailLink = '<a  href="mailto:?subject=' . $product['title'] . '&body=' . $bodyLink->href . '">' . $emailImg . '</a>';


            $prodTitle .= '<div class="displaybookmarks">' . $featuredAdaptation . $bookmarks . " " . $printLk . '&nbsp;' . $emailLink . '</div>';
        }

        $prodTitle .= '<h1 class="adaptationListingLink">' . $product['title'] . '</h1>';
        return '<br/><div id="adaptationsBackgroundColor">' . $prodTitle . $table->show() . '</div>';
    }

    /**
     * Function that builds adaptation view for print
     * @param string $productId
     * @return string
     */
    function buildAdaptationForPrint($productId) {
        $product = $this->objDbProducts->getProduct($productId);
        //String holders for content
        $rightContent = "";
        $leftContent = "";
        $thumbnail = '<img src="usrfiles/' . $product['thumbnail'] . '"  width="79" height="101" align="left"/>';
        if ($product['thumbnail'] == '') {
            $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="79" height="101" align="left"/>';
        }
        $leftContent.= $thumbnail;

        //Get institution details
        if (!empty($product["institutionid"])) {
            //Get adaptation manager
            $managedby = "";
            //Get comments
            $comments = "";
            //Get language
            $adaptlang = "";
            if ($product['language'] == "en") {
                $adaptlang = "English";
            }
            //Get keywords
            $kwords = $product['thumbnail'];
            //get group
            $group = "";

            //Get inst data
            $instData = $this->objDbInstitution->getInstitutionById($product["institutionid"]);
            if (!empty($instData)) {
                //Get institution type
                $instType = $this->objDbInstitutionType->getType($instData['type']);

                $instName = $this->objLanguage->languageText('mod_oer_fullinfo', 'oer');
                $instNameLink = new link($this->uri(array("action" => "viewinstitution", "id" => $product["institutionid"])));
                $instNameLink->link = $instName;
                $instNameLink->cssClass = "viewinstitutionlink";
                $instNameLk = "" . $instNameLink->show();

                if (!empty($instData["name"])) {
                    $rightContent.='<b>' . $this->objLanguage->languageText('mod_oer_adaptedby', 'oer') . ': </b>' . $instData['name'] . '<br /><br />';
                }
                if (!empty($instType)) {
                    $rightContent.='<b>' . $this->objLanguage->languageText('mod_oer_typeofinstitution_label', 'oer') . ': </b>' . $instType . '<br /><br />';
                }
                if (!empty($instData['country'])) {
                    $languageCode = $this->getObject("languagecode", "language");
                    $rightContent.='<b>' . $this->objLanguage->languageText('mod_oer_group_country', 'oer') . ': </b>' . $languageCode->getName($instData['country']) . '<br /><br />';
                }
                if (!empty($adaptlang)) {
                    $rightContent.='<b>' . $this->objLanguage->languageText('mod_oer_adaptedin', 'oer') . ': </b>' . $adaptlang . '<br /><br />';
                }
                if (!empty($instNameLk)) {
                    $rightContent.=' ' . $instNameLk . '<br></br>';
                }
                if (!empty($managedby)) {
                    $rightContent.='<b>' . $this->objLanguage->languageText('mod_oer_managedby', 'oer') . ':</b> ' . $managedby . '<br /><br />';
                }
                if (!empty($group)) {
                    $rightContent.='<b> ' . $this->objLanguage->languageText('mod_oer_viewgroup', 'oer') . '</b><br /><br />';
                }
            }
        }

        $strAd = "<p>" . $leftContent . '<b><br /> ' . $this->objLanguage->languageText('mod_oer_abstract', 'oer') . '</b><br />' .
                $this->objWashout->parseText($product['abstract']) . "<br />" . '<b> ' . $this->objLanguage->languageText('mod_oer_description', 'oer') . '</b><br />' .
                $this->objWashout->parseText($product['description']) . "<br />" . $rightContent . "</p>";

        $prodTitle = '<h1>' . $product['title'] . '</h1>';

        return $prodTitle . $strAd;
    }

}

?>