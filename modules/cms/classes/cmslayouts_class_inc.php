<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to display the cms content / pages using the correct layout
 *
 * @author Megan Watson, Charl Mert
 * @version 0.1
 * @copyright (c) 2007 UWC
 * @licence GNU GPL
 * @package cms
 */
class cmslayouts extends object {

    /**
     * Constructor function
     *
     * @access public
     */
    public function init() {
        try {
            // Supressing Prototype and Setting jQuery Version with Template Variables
            $this->setVar('SUPPRESS_PROTOTYPE', false); //Can't stop prototype in the public space as this might impact blocks
            $this->setVar('SUPPRESS_JQUERY', false);
            $this->setVar('JQUERY_VERSION', '1.3.2');

            $this->_objJQuery = $this->newObject('jquery', 'jquery');

            $this->_objJQuery->loadCluetipPlugin();

            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->_objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->_objSecurity = $this->newObject('dbsecurity', 'cmsadmin');
            $this->_objSections = $this->newObject('dbsections', 'cmsadmin');
            $this->_objContent = $this->newObject('dbcontent', 'cmsadmin');
            $this->_objFrontPage = $this->newObject('dbcontentfrontpage', 'cmsadmin');
            $this->_objMenuStyles = $this->newObject('dbmenustyles', 'cmsadmin');
            $this->_objHtmlBlock = $this->newObject('dbhtmlblock', 'cmsadmin');
            $this->_objCmsUtils = $this->newObject('cmsutils', 'cmsadmin');
            $this->objModule = $this->getObject('modules', 'modulecatalogue');

            $this->objUser = $this->newObject('user', 'security');
            $this->objDate = $this->getObject('dateandtime', 'utilities');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objCCLicense = $this->getObject('displaylicense', 'creativecommons');

            $this->objRound = $this->newObject('roundcorners', 'htmlelements');
            $this->objHead = $this->newObject('htmlheading', 'htmlelements');

            $this->loadClass('href', 'htmlelements');

            $this->objIcon = $this->newObject('geticon', 'htmlelements');

            $this->loadClass('link', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');
            $this->loadClass('layer', 'htmlelements');
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }

    /**
     * Method to get the left side menu
     *
     * @access public
     * @param string $currentNode The id of the selected node
     * @param array $rss RSS feeds to display
     * @return string html
     */
    public function getLeftMenu($currentNode, $rss = NULL, $contentId = '') {
        $style = $this->getMenuStyle(TRUE);

        //Supressing the menu if the "none" menu style was selected.
        if ($style == 'none') {
            return '';
        }

        //Supressing the menu if the "none" menu style was selected for frontpage items.
        if ($style == 'none_frontpage') {
            $showMenu = TRUE;
            if ($contentId != '') {
                $frontPage = $this->_objFrontPage->getFrontPage($contentId);

                if (!empty($frontPage)) {
                    $showMenu = FALSE;
                } else {
                    $showMenu = TRUE;
                }
            } else {
                $showMenu = FALSE;
            }

            if (!$showMenu) {
                return '';
            }

            //TODO: Add the frontpage only as an option to all other meny styles as apposed to it's own menu
            $style = 'page';
        }

        $adminLn = $this->objLanguage->languageText('mod_cms_cmsadmin', 'cms');
        /*
          if ($this->objModule->checkIfRegistered('context', 'context')){
          $objContextUtils =  $this->getObject('utilities','context');
          $leftSide = $objContextUtils->getHiddenContextMenu('cms','none','show');
          } else {
         */
        // Create menu
        $leftSide = $this->getMenu($style, $currentNode);
        //}
        // Add admin link
        if ($this->objUser->isAdmin() || $this->_objCmsUtils->checkPermission()) {
            $objAdminLink = new link($this->uri(array(NULL), 'cmsadmin'));
            $objAdminLink->link = $adminLn;

            $leftSide .= '<br />';
            $leftSide .= $objAdminLink->show();
        } else if ($this->getParam('id', '') == '') {
            $objMenu = $this->getObject('dbpagemenu', 'cmsadmin');
            $menuKey = $this->getParam('menustate', '');
            $menucontent = $objMenu->getMenuText($menuKey);
            if ($menucontent == '') {
                $leftSide = '';
            }
        }


        // Additional items for the left side menu
        //$leftSide .= $this->additionalMenuItems($rss);

        return $leftSide;
    }

    /**
     * Method to display the menu
     *
     * @access public
     * @param string $style The menu style
     * @return string html
     */
    public function getMenu($style, $currentNode = '') {
        switch ($style) {
            case 'buttons':
                $menu = $this->showButtonsMenu($currentNode);
                break;

            case 'page':
                $menuKey = $this->getParam('menustate');
                $menu = $this->showPageMenu($menuKey, $currentNode);
                break;

            case 'tree':
                $menu = $this->showSuperFishMenu($currentNode);

            default:
                $menu = $this->showSuperFishMenu($currentNode);
            //$menu = $this->showTreeMenu($currentNode);
            //$menu = $this->showSimpleTreeMenu($currentNode);
        }
        return $menu;
    }

    /**
     * Method to determine which style of menu to display
     *
     * @access public
     * @param bool $reset If true - reset the session
     * @return string The style
     */
    public function getMenuStyle($reset = FALSE) {
        $active = $this->getSession('menuStyle');
        if (!empty($active) && !$reset) {
            return $active;
        }
        $active = $this->_objMenuStyles->getActive();
        $this->setSession('menuStyle', $active);
        return $active;
    }

    /**
     * Method to display the jQuery Super Fish menu
     *
     * @access private
     * @param string $currentNode The id of the selected node
     * @return string html
     * @author Charl Mert
     */
    private function showSuperFishMenu() {
        // bust out a featurebox for consistency
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $objTreeMenu = $this->newObject('superfishtree', 'cmsadmin');

        $currentNode = $this->getParam('sectionid');


        //jQuery SuperFish Menu
        $jQuery = $this->newObject('jquery', 'jquery');

        //Testing jQuery 1.2.6 SuperFish Menu
        $jQuery->loadSuperFishMenuPlugin();

        ob_start();
        ?>

        <script type="text/javascript"> 
            // initialise Superfish
            jQuery(document).ready(function(){
                jQuery("ul.sf-menu").superfish({
                    animation: {opacity:'show'},   // slide-down effect without fade-in
                    width: 300,
                    delay:     0,               // 1.2 second delay on mouseout
                    speed: 'fast',
                    dropShadows: false
                });
            });

        </script>


        <?PHP

        $script = ob_get_contents();
        ob_end_clean();

        $this->appendArrayVar('headerParams', $script);


        $head = $this->objLanguage->languageText("mod_cms_navigation", "cms");

        $objLayer = new layer();
        $objLayer->str = $objTreeMenu->getCMSTree($currentNode);
        $objLayer->id = 'cmsnavigation';

        $table = new htmltable();
        $table->startRow();
        $table->addCell($objLayer->show());
        $table->endRow();

        return $table->show();
//$display = $objTreeMenu->getSimpleCMSTree($currentNode);
//return $display;
    }

    /**
     * Method to display the jquery Tree style menu
     *
     * @access private
     * @param string $currentNode The id of the selected node
     * @return string html
     * @author Charl Mert
     */
    private function showSimpleTreeMenu() {
        // bust out a featurebox for consistency
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $objTreeMenu = $this->newObject('cmstree', 'cmsadmin');

        $currentNode = $this->getParam('sectionid');

        $head = $this->objLanguage->languageText("mod_cms_navigation", "cms");

        $script = "<script type=\"text/javascript\">
		jQuery(document).ready(function(){
				jQuery('#tree1').SimpleTree();
				jQuery('#tree2').SimpleTree({animate: true});
				jQuery('#tree3').SimpleTree({animate: true,autoclose:true});
				jQuery('#tree4').SimpleTree({
animate: true,
autoclose:true,
click:function(el){
alert(jQuery(el).text());
}
});

				});
</script>";

//Insert script for generating tree menu
//    $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery.js', 'cmsadmin'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('tree.js', 'cmsadmin'));
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" href="' . $this->objConfig->getsiteRoot() . 'packages/cmsadmin/resources/tree_uwc/style.css" />');
        $this->appendArrayVar('headerParams', $script);

        $objLayer = new layer();
        $objLayer->str = $objTreeMenu->getSimpleCMSTree($currentNode);
        $objLayer->id = 'cmsnavigation';
        return $objLayer->show();
//$display = $objTreeMenu->getSimpleCMSTree($currentNode);
//return $display;
    }

    /**
     * Method to display the Custom Style Menu
     *
     * @access private
     * @param string $currentNode The id of the selected node
     * @param string $menuKey The key that determines which sub menu to load
     * @return string html
     * @author Charl Mert
     */
    private function showPageMenu($menuKey = '', $currentNode = '') {
        // bust out a featurebox for consistency
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $objPageMenu = $this->newObject('pagemenu', 'cmsadmin');

        $currentNode = $this->getParam('sectionid');

        $head = $this->objLanguage->languageText("mod_cms_navigation", "cms");

        //TODO: Load JS and CSS from tables
        $javascript = '';
        $css = '';

        $this->appendArrayVar('headerParams', $css);
        $this->appendArrayVar('headerParams', $javascript);

        $objLayer = new layer();
        $objLayer->str = $objPageMenu->show($menuKey, $currentNode);
        $objLayer->id = 'cmsnavigation';

        return $objLayer->show();
    }

    /**
     * Method to display the Tree style menu
     *
     * @access private
     * @param string $currentNode The id of the selected node
     * @return string html
     */
    private function showTreeMenu($currentNode) {
        // bust out a featurebox for consistency
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $objTreeMenu = $this->newObject('cmstree', 'cmsadmin');

        $currentNode = $this->getParam('sectionid');

        $head = $this->objLanguage->languageText("mod_cms_navigation", "cms");

        $script = '	<script type="text/javascript">
		//<![CDATA[
		// Initialize and render the menu when it is available in the DOM

		YAHOO.util.Event.onContentReady("productsandservices", function () {

				/*
				   Instantiate the menu.  The first argument passed to the 
				   constructor is the id of the element in the DOM that 
				   represents the menu; the second is an object literal 
				   representing a set of configuration properties for 
				   the menu.
				 */

				var oMenu = new YAHOO.widget.Menu(
					"productsandservices", 
					{
position: "static", 
hidedelay: 750, 
lazyload: true, 
effect: { 
effect: YAHOO.widget.ContainerEffect.FADE,
duration: 0.25
} 
}
);

				/*
				   Call the "render" method with no arguments since the markup for 
				   this menu already exists in the DOM.
				 */

				oMenu.render();            

				});
//]]>
</script>
';

//Insert script for generating tree menu
        $css = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getResourceURI("menu/assets/skins/sam/menu.css", 'yahoolib') . '" />';
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('yahoo-dom-event/yahoo-dom-event.js', 'yahoolib'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('animation/animation.js', 'yahoolib'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('container/container_core.js', 'yahoolib'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('menu/menu.js', 'yahoolib'));
        $this->setVar('bodyParams', 'class=" yui-skin-sam"');
        $this->appendArrayVar('headerParams', $css);
        $this->appendArrayVar('headerParams', $script);

        $objLayer = new layer();
        $objLayer->str = $objTreeMenu->getCMSTree($currentNode);
        $objLayer->id = 'cmsnavigation';

        return $objLayer->show();
    }

    /**
     * Method to display the Buttons style menu
     *
     * @access private
     * @param string $currentNode The id of the selected node
     * @return string html
     */
    private function showButtonsMenu($currentNode) {
        // Get the sections
        $sectionData = $this->_objSections->getRootNodes(TRUE);

        // home link
        $sel = FALSE;
        if (empty($currentNode)) {
            $sel = TRUE;
        }
        $linkText = $this->objLanguage->languageText('word_home');
        $html = $this->createButton(array(), $linkText, $sel);

        if (!empty($sectionData)) {
            foreach ($sectionData as $item) {
                $selected = FALSE;
                if ($item['id'] == $currentNode) {
                    $selected = TRUE;
                }

                $linkUrl = array('action' => 'showsection', 'id' => $item['id'], 'sectionid' => $item['id']);
                $linkText = $item['menutext'];
                $html .= $this->createButton($linkUrl, $linkText, $selected);
            }
        }
        $objLayer = new layer();
        $objLayer->str = $html;
        $objLayer->id = 'cmsnavigation';
        return $objLayer->show() . '<br />';
    }

    /**
     * Method to create the button
     *
     * @access private
     * @param array $url The link array
     * @param string $text The link text
     * @return string html
     */
    private function createButton($url, $text, $selected = FALSE) {
        $objLink = new link($this->uri($url));
        $objLink->link = $text;

        $class = 'menuBtn';
        if ($selected) {
            $class = 'menuBtnOn';
        }
        $str = "<div class='{$class}'>" . $objLink->show() . '</div>';
        return $str;
    }

    /**
     * Method to get the additional items for the left menu
     *
     * @access private
     * @param array $rss RSS feeds to display
     * @return string html
     */
    private function additionalMenuItems($rss) {
        $str = '';
        if (!empty($rss)) {
            foreach ($rss as $feeds) {
                $timenow = time();
                if ($timenow - $feeds['rsstime'] > 43200) {
                    $url = $feeds['url'];
                } else {
                    $url = $feeds['rsscache'];
                }
                $str .= $this->rssBox($url, $feeds['name']);
            }
        }
        $id = $this->getParam('id');

        $objLayer = new layer();
        $objLayer->str = $this->showFeeds($id, TRUE, 'default');
        $objLayer->id = 'cmsrss';
        $str .= $objLayer->show();

        $str .= $this->_objHtmlBlock->displayBlock('');

        return $str;
    }

    /**
     * This method generates a link to the admin edit template in the case that the
     * user is allowed to edit the post.
     *
     * @param string $id the id of the post we're dealing with
     */
    public function getEditLink($id, $a_param = null) {
        //Security Check for write access to this content item
        if (!$this->_objSecurity->canUserWriteContent($id)) {
            return "";
        }

        $myid = $this->objUser->userId();
        $ret = '';
        //if (($this->objUser->inAdminGroup($myid,'CMSAuthors')) || ($this->objUser->inAdminGroup($myid,'Site Admin'))) {
        if ($a_param && !empty($a_param)) {
            $s_param = serialize($a_param);
        }
        if (!isset($s_param)) {
            $s_param = NULL;
        }

        $icon = $this->getObject('geticon', 'htmlelements');
        $icon->setIcon('edit');
        $icon->alt = $this->objLanguage->languageText('word_edit');
        $link = $this->getObject('link', 'htmlelements');
        $link->link($this->uri(array('action' => 'addcontent', 'id' => $id, 'frommodule' => $this->getParam('module'), 'fromaction' => $this->getParam('action'), 's_param' => $s_param), 'cmsadmin'));
        $link->link = $icon->show();
        $ret = " " . $link->show();
        //}

        return $ret;
    }

    /**
     * Method to get the Front Page Content
     * in a ordered way. It should also conform to the
     * section template for the section that this page is in
     *
     * @return string The content to be displayed on the front page
     * @access public
     */
    public function getFrontPageContent($displayId = NULL) {
        $lbRead = $this->objLanguage->languageText('phrase_readmore');
        $lbWritten = $this->objLanguage->languageText('phrase_writtenby');
        $arrFrontPages = $this->_objFrontPage->getFrontPages(TRUE);

        $str = '';
        //set a counter for the records .. display on the first 2  the rest will be dsiplayed as links
        $cnt = 0;

        $numPages = count($arrFrontPages);
        // If only 1 page is on the front - display the full page
        if ($numPages == 1) {
            $page = $this->_objContent->getContentPage($arrFrontPages[0]['content_id']);

            // Adding Title
            $this->objHead->str = $page['title'] . $this->getEditLink($page['id'], array('id' => $page['id']));
            if (isset($page['show_title'])) {
                if ($page['show_title'] == 'g') {
                    //Checking the global sys config
                    $globalShowTitle = $this->_objSysConfig->getValue('SHOW_TITLE', 'cmsadmin');
                    if ($globalShowTitle == 'n') {
                        //Only showing edit button
                        $this->objHead->str = $this->getEditLink($page['id'], array('id' => $page['id']));
                    }
                }
                if ($page['show_title'] == 'n') {
                    //Only showing edit button
                    $this->objHead->str = $this->getEditLink($page['id'], array('id' => $page['id']));
                }
            }

            if ($this->objHead->str != '') {
                $pageStr = $this->objHead->show();
            } else {
                $pageStr = '';
            }

            // Adding Author
            $showAuthorText = '<p><span class="date">' . $lbWritten . '&nbsp;' . $this->objUser->fullname($page['created_by']) . '</span>';
            $showAuthorText .= '</p>';

            if (isset($page['show_author'])) {
                if ($page['show_author'] == 'g') {
                    //Checking the global sys config
                    $globalShowAuthor = $this->_objSysConfig->getValue('SHOW_AUTHOR', 'cmsadmin');
                    if ($globalShowAuthor == 'n') {
                        $showAuthorText = '';
                    }
                }
                if ($page['show_author'] == 'n') {
                    $showAuthorText = '';
                }
            }

            $pageStr .= $showAuthorText;


            // Adding Date
            $showDateText = '<em><span class="date">' . $this->objDate->formatDate($page['created']) . '</span></em><br />';

            if (isset($page['show_date'])) {
                if ($page['show_date'] == 'g') {
                    //Checking the global sys config
                    $globalShowDate = $this->_objSysConfig->getValue('SHOW_DATE', 'cmsadmin');
                    if ($globalShowDate == 'n') {
                        $showDateText = '';
                    }
                }
                if ($page['show_date'] == 'n') {
                    $showDateText = '';
                }
            }

            $pageStr .= $showDateText;

            if (trim($page['introtext']) != '') {
                $pageStr .= stripslashes($page['introtext']);
                $pageStr .= '<br />';
            }

            $pageStr .= $page['body'];
            
            $objLayer = new layer();
            $objLayer->str = "<div class='CMS-frontpage-item'>" 
              . $pageStr . "<div>";
            $objLayer->id = 'cmscontent';
            
            return $objLayer->show();
        }

        // Display the selected page above the others
        if (!empty($displayId) && !empty($arrFrontPages)) {
            foreach ($arrFrontPages as $frontPage) {
                if ($displayId == $frontPage['content_id']) {
                    $str .= $this->showBody();
                    break;
                }
            }
        }

        // Display the introductions of all front pages
        if (!empty($arrFrontPages)) {
            foreach ($arrFrontPages as $frontPage) {
                //get the page content
                $page = $this->_objContent->getContentPage($frontPage['content_id']);
                $show_content = $frontPage['show_content'];

                // Check it's not the page displayed at the top.
                if (!empty($displayId) && $displayId == $frontPage['content_id']) {
                    // do nothing
                } else {
                    $pageStr = '';
                    $cnt++;

                    // Page heading - hide if set
                    if (isset($page['show_title']) && $page['show_title'] == 1) {
                        $pageStr = $this->getEditLink($page['id']);
                    } else {
                        $this->objHead->type = '2';
                        $this->objHead->str = $page['title'] . $this->getEditLink($page['id']);

                        $pageStr = $this->objHead->show();
                    }

                    if ($show_content) {
                        // Adding Written By
                        if (isset($page['show_author']) && $page['show_author'] != 1) {
                            $pageStr .= '<p><span class="user">' . $lbWritten . '&nbsp;' . $this->objUser->fullname($page['created_by']) . '</span></p>';
                        }

                        // Adding Date
                        if (isset($page['show_date']) && $page['show_date'] != 1) {
                            if (isset($page['created']) && !empty($page['created'])) {
                                $pageStr .= '<span class="date">' . $this->objDate->formatDate($page['created']) . '</span>';
                            }
                        }

                        $pageStr .= '<br />';
                        $pageStr .= stripslashes($page['introtext']);
                        $pageStr .= '<br />';
                    }

                    if ($show_content) {
                        $pageStr .= $page['body'];
                        $str .= $pageStr;
                    } else {
                        // Read more link
                        $moreLink = $this->uri(array('action' => 'showfulltext', 'id' => $page['id'], 'sectionid' => $page['sectionid']), 'cms');
                        //array('action' => 'showfulltext', 'sectionid' => $page['sectionid'], 'id' => $page['id']), 'cms');
                        $readMoreLink = new link($moreLink);
                        $readMoreLink->link = $lbRead . '...';
                        $readMoreLink->title = $page['title'];
                        $readMoreLink->cssClass = 'morelink';

                        // Display the page title and introduction
                        // Adding Written By
                        if (isset($page['show_author']) && $page['show_author'] != 1) {
                            $pageStr .= '<p><span class="user">' . $lbWritten . '&nbsp;' . $this->objUser->fullname($page['created_by']) . '</span></p>';
                        }

                        // Adding Date
                        if (isset($page['show_date']) && $page['show_date'] != 1) {
                            if (isset($page['created']) && !empty($page['created'])) {
                                $pageStr .= '<span class="date">' . $this->objDate->formatDate($page['created']) . '</span>';
                            }
                        }

                        $pageStr .= '<br />';
                        $pageStr .= stripslashes($page['introtext']) . '<br />' . $readMoreLink->show();
                        $pageStr .= '<br />';

                        if (isset($page['modified']) && !empty($page['modified'])) {
                            $pageStr .= '<p> <span class="date">Last updated : ' . $this->objDate->formatDate($page['modified']) . '</span></p>';
                        }

                        $str .= "<div class='CMS-frontpage-item'>" . $pageStr . "</div>";
                    }
                }
            }
        }

        $objLayer = new layer();
        $objLayer->str = $str;
        $objLayer->id = 'cmscontent';

        return $objLayer->show();
    }

    /**
     * Method to generate the content for a section
     *
     * @access public
     * @return string The content displayed when a section is selected
     */
    public function showSection($module = "cms", $arrSection = false) {
        if (!$arrSection) {
            //get the section record
            $sectionId = $this->getParam('id');
            $arrSection = $this->_objSections->getSection($sectionId);
        }

        switch (strtolower($arrSection['layout'])) {

            case 'previous':
                return $this->_layoutPrevious($arrSection, $module);

            case 'page':
                return $this->_layoutPage($arrSection, $module);

            case 'summaries':
                return $this->_layoutSummaries($arrSection, $module);

            case 'columns':
                return $this->_layoutColumns($arrSection, $module);

            case 'list':
            default:
                return $this->_layoutList($arrSection, $module);
        }
    }

    /**
     * Method to generate the layout for a section
     * in 'Previous Layout'
     *
     * @param array $arrSection The Section record
     * @access private
     * @return string
     */
    function _layoutPrevious($arrSection, $module) {
        $pageId = $this->getParam('pageid', '');
        $orderType = $arrSection['ordertype'];
        $showIntro = $arrSection['showintroduction'];
        $showDate = $arrSection['showdate'];
        $description = $arrSection['description'];

        switch ($orderType) {

            case null:
            case 'pageorder':
                $filter = 'ORDER BY ordering';
                break;

            case 'pagedate_asc':
                $filter = 'ORDER BY created';
                break;

            case 'pagedate_desc':
                $filter = 'ORDER BY created DESC';
                break;

            case 'pagetitle_asc':
                $filter = 'ORDER BY title';
                break;

            case 'pagetitle_desc':
                $filter = 'ORDER BY title DESC';
                break;
        }

        $arrPages = $this->_objContent->getAll('WHERE sectionid = \'' . $arrSection['id'] . '\' AND published=1 AND trash=0 ' . $filter);

        $cnt = 0;
        $returnStr = '';
        $strBody = '';
        $str = '';

        if ($pageId == '') {
            $pageId = $arrPages[0]['id'];
        }

        $foundPage = FALSE;

        if (!empty($arrPages)) {
            $str = '<ul>';
            foreach ($arrPages as $page) {
                if ($foundPage == TRUE) {
                    $link = new link($this->uri(array('action' => 'showsection', 'id' => $arrSection['id'], 'pageid' => $page['id'], 'sectionid' => $page['sectionid']), $module));
                    $link->link = $page['title'];
                    if ($showDate) {
                        $str .= '<li>' . $this->objDate->formatDate($page['created']) . ' - ' . $link->show() . '</li> ';
                    } else {
                        $str .= '<li>' . $link->show() . '</li> ';
                    }
                }

                if ($pageId == $page['id']) {
                    // hide the title if set
                    if (isset($page['show_title']) && $page['show_title'] == 1) {
                        $strBody = '';
                    } else {
                        $this->objHead->str = $page['title'];
                        $this->objHead->type = 2;
                        $strBody = $this->objHead->show();
                    }
                    $strBody .= stripslashes($page['body']);
                    $foundPage = TRUE;

                    $strBody = $this->objRound->show($strBody);
                }
            }
            $str .= '</ul>';
        }

        if ($showIntro && !empty($description)) {
            $returnStr = '<hr /><em>' . $description . '</em><hr />';
        }

        $returnStr .= '<p>' . $strBody . '</p><p>' . $str . '</p>';
        $objLayer = new layer();
        $objLayer->str = $returnStr;
        $objLayer->id = 'cmscontent';

        return $objLayer->show();
    }

    /**
     * Method to generate the layout for a section
     * in 'Previous Layout'
     *
     * @param array $arrSection The Section record
     * @access private
     * @return string Content to be displayed
     */
    function _layoutSummaries($arrSection, $module) {
        $str = '';

        $lbWritten = $this->objLanguage->languageText('phrase_writtenby');
        $lbRead = $this->objLanguage->languageText('phrase_readmore');

        $orderType = $arrSection['ordertype'];
        $showIntro = $arrSection['showintroduction'];
        $hideTitle = isset($arrSection['hidetitle']) ? $arrSection['hidetitle'] : FALSE;
        $showDate = $arrSection['showdate'];
        $description = $arrSection['description'];
        $sectionTitle = $arrSection['title'];

        //Add section title
        if ($hideTitle) {
            $headStr = '';
        } else {
            $this->objHead->type = 2;
            $this->objHead->str = $sectionTitle;
            $headStr = $this->objHead->show();
        }

        //Check if section intro should be displayed and act accordingly
        if ($showIntro && !empty($description)) {
            $headStr .= '<br />' . $description . '<hr />';
        }
        $str .= $headStr;

        switch ($orderType) {
            case 'pagedate_asc':
                $filter = 'ORDER BY created';
                break;

            case 'pagedate_desc':
                $filter = 'ORDER BY created DESC';
                break;

            case 'pagetitle_asc':
                $filter = 'ORDER BY title';
                break;

            case 'pagetitle_desc':
                $filter = 'ORDER BY title DESC';
                break;

            case 'pageorder':
            default:
                $filter = 'ORDER BY ordering';
                break;
        }
        $arrPages = $this->_objContent->getAll('WHERE sectionid = \'' . $arrSection['id'] . '\' AND published=1 AND trash=0 ' . $filter);

        if (!empty($arrPages)) {
            foreach ($arrPages as $page) {
                $pageStr = '';

                if (isset($page['show_title']) && $page['show_title'] == 1) {
                    $pageStr = '';
                } else {
                    $this->objHead->type = '4';
                    $this->objHead->str = $page['title'];
                    $pageStr .= $this->objHead->show();

                    $pageStr .= '<p>';

                    // Adding Written By
                    if (isset($page['show_author']) && $page['show_author'] != 1) {
                        if (isset($page['created_by'])) {
                            $pageStr .= '<span class="minute">' . $lbWritten . '&nbsp;' . $this->objUser->fullname($page['created_by']) . '</span>';
                        }
                    }

                    // Adding Written By
                    if (isset($page['show_date']) && $page['show_date'] != 1) {
                        $creationDate = $this->objDate->formatDate($page['created']);
                        $pageStr .= '<span class="date">' . $creationDate . '</span>';
                    }
                    $pageStr .= '</p>';
                }

                // introduction text
                $pageStr .= '<p>' . stripslashes($page['introtext']);

                // Read more link
                $uri = $this->uri(array('action' => 'showfulltext', 'sectionid' => $arrSection['id'], 'id' => $page['id']), $module);
                $readMoreLink = new link($uri);
                $readMoreLink->link = $lbRead . '...';
                $readMoreLink->title = $page['title'];
                $readMoreLink->cssClass = 'morelink';

                $pageStr .= '<br />' . $readMoreLink->show() . '</p><hr />';
                $str .= $pageStr;
            }
        }
        $objLayer = new layer();
        $objLayer->str = $str;
        $objLayer->id = 'cmscontent';

        return $objLayer->show();
    }

    /**
     * Method to generate the layout for a section
     * in 'Page Layout'
     *
     * @param array $arrSection The Section record
     * @access private
     * @return string Content to be displayed
     */
    function _layoutPage($arrSection, $module) {
        $pageId = $this->getParam('pageid', '');
        if (empty($arrSection)) {
            $showIntro = Null;
            $description = Null;
            $orderType = Null;
            $showDate = Null;
            $imagesrc = Null;
            $intId = Null;
            $intTitle = Null;
        } else {
            $showIntro = $arrSection['showintroduction'];
            $description = $arrSection['description'];
            $orderType = $arrSection['ordertype'];
            $showDate = $arrSection['showdate'];
            $imagesrc = $arrSection['link'];
            $intId = $arrSection['id'];
            $intTitle = $arrSection['title'];
        }
        $introStr = null;
        switch ($orderType) {

            case 'pagedate_asc':
                $filter = 'ORDER BY created';
                break;

            case 'pagedate_desc':
                $filter = 'ORDER BY created DESC';
                break;

            case 'pagetitle_asc':
                $filter = 'ORDER BY title';
                break;

            case 'pagetitle_desc':
                $filter = 'ORDER BY title DESC';
                break;

            case 'pageorder':
            default:
                $filter = 'ORDER BY ordering';
                break;
        }

        $arrPages = $this->_objContent->getAll('WHERE sectionid = \'' . $intId . '\' AND published=1 AND trash=0 ' . $filter);

        $cnt = 0;
        $topStr = '';
        $str = '';

        if ($pageId == '') {
            if (count($arrPages)) {
                $pageId = $arrPages[0]['id'];
            }
        }


        if (!empty($description)) {
            $introStr = null;
            $introStr .= '<p><hr /><span>' . $intTitle . '</span></p>';
            $introStr .= '<em><span>' . $description . '</span></em><br /><hr />';
        }

        // Display the selected page
        // Display links to the other pages
        if (!empty($arrPages)) {
            $pgCnt = count($arrPages);
            foreach ($arrPages as $page) {
                if ($pageId == $page['id']) {
                    if (isset($page['show_title']) && $page['show_title'] == 1) {
                        $pageStr = '';
                    } else {
                        $this->objHead->type = 2;
                        $this->objHead->str = $page['title'];
                        $pageStr = $this->objHead->show();
                    }
                    $pageStr .= stripslashes($page['body']);

                    $topStr = $this->objRound->show($pageStr);
                    if ($pgCnt > 1) {
                        $str .= $page['title'] . ' | ';
                    }
                } else {
                    $link = new link($this->uri(array('action' => 'showsection', 'pageid' => $page['id'], 'id' => $page['sectionid'], 'sectionid' => $page['sectionid']), $module));
                    $link->link = $page['title'];
                    $str .= $link->show() . ' | ';
                }
            }
        }

        // Remove the end pipe
        if (strlen($str) > 1) {
            $str = substr($str, 0, strlen($str) - 3);
        }

        $objLayer = new layer();
        $objLayer->str = $introStr . '<br />' . $topStr . '<p>' . $str . '</p>';
        $objLayer->id = 'cmscontent';

        return $objLayer->show();
    }

    /**
     * Method to display content in two columns
     *
     * @access private
     * @return string html
     */
    private function _layoutColumns($arrSection, $module) {
        return '';
    }

    /**
     * Method to generate the layout for a section
     * in 'List Layout'
     *
     * @param array $arrSection The Section record
     * @access private
     * @return string
     */
    function _layoutList($arrSection, $module) {
        $str = '';
        $introStr = '';
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        //$objMindMap = $this->getObject('parse4mindmap', 'filters');
        $objMath = $this->getObject('parse4mathml', 'filters');
        $orderType = $arrSection['ordertype'];

        if (!isset($arrSection['showintroduction'])) {
            $arrSection['showintroduction'] = '';
        }

        $showIntro = stripslashes($arrSection['showintroduction']);

        if (!isset($arrSection['showdate'])) {
            $arrSection['showdate'] = '';
        }

        $showDate = $arrSection['showdate'];
        $description = stripslashes($arrSection['description']);
        $imagesrc = $arrSection['link'];
        $title = $arrSection['title'];
        $hideTitle = isset($arrSection['hidetitle']) ? $arrSection['hidetitle'] : FALSE;

        switch ($orderType) {

            case 'pagedate_asc':
                $filter = 'ORDER BY created';
                break;

            case 'pagedate_desc':
                $filter = 'ORDER BY created DESC';
                break;

            case 'pagetitle_asc':
                $filter = 'ORDER BY title';
                break;

            case 'pagetitle_desc':
                $filter = 'ORDER BY title DESC';
                break;

            case 'pageorder':
            default:
                $filter = 'ORDER BY ordering';
                break;
        }
        $arrPages = $this->_objContent->getAll('WHERE sectionid = \'' . $arrSection['id'] . '\' AND published=1 AND trash=0 ' . $filter);

        if (!empty($arrPages)) {
            $str .= '<ul>';
            foreach ($arrPages as $page) {
                $link = new link($this->uri(array('action' => 'showcontent', 'id' => $page['id'], 'sectionid' => $page['sectionid']), $module));
                $link->link = $page['title'];
                $listStr = '';
                if ($showDate) {
                    $listStr = $this->objDate->formatDate($page['created']) . ' - ';
                }
                $listStr .= $link->show();
                $str .= '<li>' . $listStr . '</li>';
            }
            $str .= '</ul>';
        }

        if ($hideTitle) {
            $introStr = '';
        } else {
            $this->objHead->str = $title;
            $this->objHead->type = 2;
            $introStr = $this->objHead->show();
        }

        //parse the body stuff
        $objMindMap->parse($description);
        $objMath->parseAll($description);

        if ($showIntro && !empty($description)) {
            $introStr .= '<p><span>' . $description . '</span></p>';
        }

        $objLayer = new layer();
        $objLayer->str = $introStr . '<p>' . $str . '</p>';
        $objLayer->id = 'cmscontent';

        return $objLayer->show();
    }

    /**
     * Method to show the body of a page
     *
     * @access public
     * @param boolean $isPreview whether or not to generate a preview or a full view
     * @param string $content The content of the page
     * @return string The page content to be displayed
     */
    public function showBody($isPreview = false, $content = false) {
        $lbWritten = NULL;
        if (!$content) {
            $contentId = $this->getParam('id');
            $page = $this->_objContent->getContentPageFiltered($contentId);
        } else {
            $page = $content;
        }

        $objLayer = new layer();
        $objLayer->id = 'cmscontent';

        if ($page == '') {
            $objLayer->str = $this->objLanguage->languageText('mod_cms_pagemissing', 'cms');
            return $objLayer->show();
        }

        if ($page['trash'] == '1') {
            $objLayer->str = $this->objLanguage->languageText('mod_cms_pagearchived', 'cms');
            return $objLayer->show();
            //This item has been archived;
        }

        //Including Meta Tags Here
        $this->appendArrayVar('metaKeywords', $page['metakey']);
        $this->appendArrayVar('metaDescriptions', $page['metadesc']);

        $tblShowTitle = true;
        $tblShowAuthor = true;
        $tblShowDate = true;
        $tblShowPrint = true;
        $tblShowPdf = true;
        $tblShowMail = true;

        //TODO: Implement Full Page Preview (Currently only previewing FCK Contents)
        if ($isPreview) {
            $page['title'] = $this->getParam('title');
            $page['body'] = $this->getParam('body');
            $page['show_author'] = $this->getParam('show_author');
            $page['show_date'] = $this->getParam('show_date');
            $page['show_title'] = $this->getParam('show_title');
        }

        if ($this->_objSysConfig->getValue('SHOW_BOOKMARKS', 'cmsadmin') != 'n') {
            //Build Footer Items
            $bmurl = $this->uri(array(
                'action' => 'showsection',
                'module' => 'cms',
                'sectionid' => $page['sectionid']
                    ));
            $bmurl = urlencode($bmurl);
            $bmlink = "http://www.addthis.com/bookmark.php?pub=&amp;url=" . $bmurl . "&amp;title=" . urlencode(addslashes($page['title']));
            $bmtext = '<img src="core_modules/utilities/resources/socialbookmarking/button1-bm.gif" width="125" height="16" border="0" alt="' . $this->objLanguage->languageText("mod_cms_bookmarkarticle", "cms") . '"/>';
            $bookmark = new href($bmlink, $bmtext, NULL);

            //do the cc licence part
            $cclic = $page['post_lic'];

            //get the lic that matches from the db
            $this->objCC = $this->getObject('displaylicense', 'creativecommons');
            if ($cclic == '') {
                $cclic = 'copyright';
            }
            $iconList = $this->objCC->show($cclic);

            if (isset($page['show_flag'])) {
                switch ($page['show_flag']) {
                    case 'g':
                        //Checking the global sys config
                        $globalShowFlag = $this->_objSysConfig->getValue('SHOW_FLAG', 'cmsadmin');
                        if ($globalShowFlag == 'n') {
                            $flagContent = '';
                        } else {
                            $objIcon = $this->getObject('geticon', 'htmlelements');
                            $objIcon->setIcon('redflag');
                            $objIcon->title = $this->objLanguage->languageText('mod_cms_flag_content', 'cms');
                            $objIcon->extra = 'id="hover_redflag"';
                            $flagContent = '<a id="flag_link" href="?module=cmsadmin&action=ajaxforms&type=showflagoptions&id=' . $page['id'] . '" rel="?module=cmsadmin&action=ajaxforms&type=showflagoptions&id=' . $page['id'] . '">' . $objIcon->show();

                            $script = "<script type='text/javascript'>
										jQuery(document).ready(function(){
											jQuery('#flag_link').cluetip({sticky: true, closePosition: 'title', arrows: true});
										});
								   </script>";

                            $this->appendArrayVar('headerParams', $script);
                        }
                        break;

                    case 'n':
                        $flagContent = '';
                        break;

                    default:
                        $objIcon = $this->getObject('geticon', 'htmlelements');
                        $objIcon->setIcon('redflag');
                        $objIcon->title = $this->objLanguage->languageText('mod_cms_flag_content', 'cms');
                        $objIcon->extra = 'id="hover_redflag"';
                        $flagContent = '<a id="flag_link" href="?module=cmsadmin&action=ajaxforms&type=showflagoptions&id=' . $page['id'] . '" rel="?module=cmsadmin&action=ajaxforms&type=showflagoptions&id=' . $page['id'] . '">' . $objIcon->show();

                        $script = "<script type='text/javascript'>
									jQuery(document).ready(function(){
										jQuery('#flag_link').cluetip({sticky: true, closePosition: 'title', arrows: true});
									});
							   </script>";

                        $this->appendArrayVar('headerParams', $script);
                        break;
                }
            }

            $iconList .= $flagContent;

            //table of non logged in options
            //Set the table name
            $tblnl = $this->getObject('htmltable', 'htmlelements');
            $tblnl->cellpadding = 3;
            $tblnl->width = "100%";
            $tblnl->align = "center";
            $tblnl->startRow();
            $tblnl->addCell($bookmark->show(), null, null, 'left'); //bookmark link(s)
            $tblnl->addCell('<em class="date">' . $this->objLanguage->languageText("mod_cms_lastupdated", "cms") . ':' . $this->objDate->formatDate($page['modified']) . '</em>', null, null, 'left');
            $tblnl->addCell($iconList); //cc licence//Build Footer Items
            $tblnl->endRow();

            $table_bookmark = "<center>" . $tblnl->show() . "</center>";
        } else {
            $table_bookmark = '';
        }

        //pdf url
        $pdfurl = $this->uri(array(
            'action' => 'makepdf',
            'sectionid' => $page['sectionid'],
            'module' => 'cms',
            'id' => $page['id']
                ));
        //PDF Icon
        $pdficon = $this->newObject('geticon', 'htmlelements');
        $pdficon->setIcon('filetypes/pdf');
        $pdficon->alt = $this->objLanguage->languageText("mod_cms_saveaspdf", "cms");
        $pdficon->align = false;
        $pdfimg = $pdficon->show();
        $pdflink = new href($pdfurl, $pdfimg, NULL);

        //and the mail to a friend icon
        $mtficon = $this->newObject('geticon', 'htmlelements');
        $mtficon->setIcon('filetypes/eml');
        $mtficon->alt = $this->objLanguage->languageText("mod_cms_mailtofriend", "cms");
        $mtficon->align = false;
        $mtfimg = $mtficon->show();

        $mtflink = new href($this->uri(array('action' => 'mail2friend', 'sectionid' => $page['sectionid'], 'id' => $page['id'])), $mtfimg, NULL);

        //Print Icon
        $printicon = $this->newObject('geticon', 'htmlelements');
        $printicon->setIcon('print', 'gif', 'icons/cms');
        $printlink = new href('javascript:void(0)', $printicon->show(), 'onclick="javascript:window.print();"');

        //Checking to display pdf icon
        if (isset($page['show_pdf'])) {
            if ($page['show_pdf'] == 'g') {
                //Checking the global sys config
                $globalShowPdf = $this->_objSysConfig->getValue('SHOW_PDF', 'cmsadmin');
                if ($globalShowPdf == 'n') {
                    $pdflink = new href('', '', NULL);
                    $tblShowPdf = false;
                }
            }
            if ($page['show_pdf'] == 'n') {
                $pdflink = new href('', '', NULL);
                $tblShowPdf = false;
            }
        }

        //Checking to display mail 2 friend icon
        if (isset($page['show_email'])) {
            if ($page['show_email'] == 'g') {
                //Checking the global sys config
                $globalShowMtf = $this->_objSysConfig->getValue('SHOW_MAIL', 'cmsadmin');
                if ($globalShowMtf == 'n') {
                    $mtflink = new href('', '', NULL);
                    $tblShowMail = false;
                }
            }
            if ($page['show_email'] == 'n') {
                $mtflink = new href('', '', NULL);
                $tblShowMail = false;
            }
        }

        //Checking to display print icon
        if (isset($page['show_print'])) {
            if ($page['show_print'] == 'g') {
                //Checking the global sys config
                $globalShowPrint = $this->_objSysConfig->getValue('SHOW_PRINT', 'cmsadmin');
                if ($globalShowPrint == 'n') {
                    $printlink = new href('', '', NULL);
                    $tblShowPrint = false;
                }
            }
            if ($page['show_print'] == 'n') {
                $printlink = new href('', '', NULL);
                $tblShowPrint = false;
            }
        }

        // Adding Title
        $btnEditLink = $this->getEditLink($page['id'], array('sectionid' => $page['sectionid'], 'id' => $page['id']));
        $lblPageTitle = $page['title'];

        if (isset($page['show_title'])) {
            switch ($page['show_title']) {
                case 'g':
                    //Checking the global sys config
                    $globalShowTitle = $this->_objSysConfig->getValue('SHOW_TITLE', 'cmsadmin');
                    if ($globalShowTitle == 'n') {
                        //Only showing edit button
                        $lblPageTitle = '';
                        $tblShowTitle = false;
                    }
            }
            if ($page['show_title'] == 'n') {
                //Only showing edit button
                $lblPageTitle = '';
                $tblShowTitle = false;
            }
        }

        //Create heading
        //Lets format the header information for the page
        $tblh = $this->newObject('htmltable', 'htmlelements');
        $tblh->cellpadding = 3;
        $tblh->width = "100%";
        $tblh->align = "center";

        //Title and Edit Button
        $this->objHead->type = 2;
        $this->objHead->str = $lblPageTitle . $btnEditLink;

        $tblh->startRow();

        if ($lblPageTitle != '') {
            $tblh->addCell($this->objHead->show());
        }

        $tblh->addCell($pdflink->show() . $mtflink->show() . $printlink->show(), null, null, 'right', 'printpdfmailicons'); //pdf icon
        $tblh->endRow();

        $strBody = '';

        // Adding Author
        if (isset($page['show_author'])) {
            switch ($page['show_author']) {
                case 'n':
                    $tblShowAuthor = false;
                    break;

                case 'g':
                    //Checking the global sys config
                    $globalShowAuthor = $this->_objSysConfig->getValue('SHOW_AUTHOR', 'cmsadmin');
                    if ($globalShowAuthor == 'n') {
                        $tblShowAuthor = false;
                    } else {
                        $strBody = "<p><span class='date'>$lbWritten " . $this->objUser->fullname($page['created_by']) . '</span></p>';
                    }
                    break;

                default:
                    $strBody = "<p><span class='date'>$lbWritten " . $this->objUser->fullname($page['created_by']) . '</span></p>';
                    break;
            }
        } else {
            $strBody = "<p><span class='date'>$lbWritten " . $this->objUser->fullname($page['created_by']) . '</span></p>';
        }

        // Adding Date
        $showDateText = '<em><span class="date">' . $this->objDate->formatDate($page['created']) . '</span>';
        $showDateText .= '<br /></em>';

        if (isset($page['show_date'])) {
            if ($page['show_date'] == 'g') {
                //Checking the global sys config
                $globalShowDate = $this->_objSysConfig->getValue('SHOW_DATE', 'cmsadmin');
                if ($globalShowDate == 'n') {
                    $showDateText = '';
                    $tblShowDate = false;
                }
            }
            if ($page['show_date'] == 'n') {
                $showDateText = '';
                $tblShowDate = false;
            }
        }
        $strBody .= $showDateText;

        $strHeader = '';
        if ($tblShowTitle == false &&
                $tblShowAuthor == false &&
                $tblShowDate == false &&
                $tblShowPdf == false &&
                $tblShowMail == false &&
                $tblShowPrint == false) {

            if ($this->objUser->isLoggedIn()) {
                $strHeader = $this->objHead->show();
            }
        } else {
            $strHeader = '<hr />';
            $strHeader .= $tblh->show();
        }

        $strBody .= stripslashes($page['body']);

        $objLayer->str = $strHeader . $strBody . $table_bookmark . "<hr />";
        return $objLayer->show();
    }

    /**
     * The method generates the interface for the send mail to a
     * friend funtionality
     *
     * @param array $m2fdata
     * @return form interface
     */
    public function sendMail2FriendForm($m2fdata) {

        $this->objUser = $this->getObject('user', 'security');
        if ($this->objUser->isLoggedIn()) {
            $theuser = $this->objUser->fullName($this->objUser->userid());
        } else {
            $theuser = $this->objLanguage->languageText("mod_cms_word_anonymous", "cms");
        }
        //start a form object
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $mform = new form('mail2friend', $this->uri(array(
                            'action' => 'mail2friend', 'id' => $m2fdata['id']
                        )));
        $mfieldset = $this->newObject('fieldset', 'htmlelements');
        //$mfieldset->setLegend($this->objLanguage->languageText('mod_blog_sendmail2friend', 'blog'));
        $mtable = $this->newObject('htmltable', 'htmlelements');
        $mtable->cellpadding = 3;
        $mtable->startHeaderRow();
        $mtable->addHeaderCell('');
        $mtable->addHeaderCell('');
        $mtable->endHeaderRow();
        //your name
        $mtable->startRow();
        $mynamelabel = new label($this->objLanguage->languageText('mod_cms_myname', 'cms') . ':', 'input_myname');
        $myname = new textinput('sendername');
        $myname->size = '80%';
        $myname->setValue($theuser);
        $mtable->addCell($mynamelabel->show());
        $mtable->addCell($myname->show());
        $mtable->endRow();

        //Friend(s) email addresses
        $mtable->startRow();
        $femaillabel = new label($this->objLanguage->languageText('mod_cms_emailadd', 'cms') . ':', 'input_femail');
        $emailadd = new textinput('emailadd');
        $emailadd->size = '80%';
        if (isset($m2fdata['user'])) {
            $emailadd->setValue($m2fdata['user']);
        }
        $mtable->addCell($femaillabel->show());
        $mtable->addCell($emailadd->show());
        $mtable->endRow();
        //message for friends (optional)
        $mtable->startRow();
        $fmsglabel = new label($this->objLanguage->languageText('mod_cms_emailmsg', 'cms') . ':', 'input_femailmsg');
        $msg = new textarea('msg', '', 4, 68);
        $mtable->addCell($fmsglabel->show());
        $mtable->addCell($msg->show());
        $mtable->endRow();

        //add a rule
        $mform->addRule('emailadd', $this->objLanguage->languageText("mod_cms_phrase_emailreq", "cms"), 'required');
        $mfieldset->addContent($mtable->show());
        $mform->addToForm($mfieldset->show());
        $this->objMButton = new button($this->objLanguage->languageText('mod_cms_word_sendmail', 'cms'));
        $this->objMButton->setValue($this->objLanguage->languageText('mod_cms_word_sendmail', 'cms'));
        $this->objMButton->setToSubmit();
        $mform->addToForm($this->objMButton->show());
        $mform = $mform->show();

        //bust out a featurebox for consistency
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_cms_sendmail2friend", "cms"), $mform);
        return $ret;
    }

    /**
     * Method to build and create the feeds options box
     *
     * @author Paul Scott
     * @param integer $userid
     * @param bool $featurebox
     * @return string
     */
    public function showFeeds($pageid, $featurebox = FALSE, $showOrHide = 'none') {
        $this->objUser = $this->getObject('user', 'security');

        $leftCol = NULL;
        if ($featurebox == FALSE) {
            $leftCol .= "<em>" . $this->objLanguage->languageText("mod_cms_feedheader", "cms") . "</em><br />";
        }

        //RSS2.0
        $rss2 = $this->getObject('geticon', 'htmlelements');
        $rss2->setIcon('rss', 'gif', 'icons/filetypes');
        $rss2->align = "top";
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'rss2', 'pageid' => $pageid)), $this->objLanguage->languageText("mod_cms_word_rss2", "cms"));
        $rss2feed = $rss2->show() . "&nbsp;" . $link->show() . "<br />";

        //RSS0.91
        $rss091 = $this->getObject('geticon', 'htmlelements');
        $rss091->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'rss091', 'pageid' => $pageid)), $this->objLanguage->languageText("mod_cms_word_rss091", "cms"));
        $leftCol .= $rss091->show() . "&nbsp;" . $link->show() . "<br />";

        //RSS1.0
        $rss1 = $this->getObject('geticon', 'htmlelements');
        $rss1->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'rss1', 'pageid' => $pageid)), $this->objLanguage->languageText("mod_cms_word_rss1", "cms"));
        $leftCol .= $rss1->show() . "&nbsp;" . $link->show() . "<br />";

        //PIE
        $pie = $this->getObject('geticon', 'htmlelements');
        $pie->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'pie', 'pageid' => $pageid)), $this->objLanguage->languageText("mod_cms_word_pie", "cms"));
        $leftCol .= $pie->show() . "&nbsp;" . $link->show() . "<br />";

        //MBOX
        $mbox = $this->getObject('geticon', 'htmlelements');
        $mbox->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'mbox', 'pageid' => $pageid)), $this->objLanguage->languageText("mod_cms_word_mbox", "cms"));
        $leftCol .= $mbox->show() . "&nbsp;" . $link->show() . "<br />";

        //OPML
        $opml = $this->getObject('geticon', 'htmlelements');
        $opml->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'opml', 'pageid' => $pageid)), $this->objLanguage->languageText("mod_cms_word_opml", "cms"));
        $leftCol .= $opml->show() . "&nbsp;" . $link->show() . "<br />";

        //ATOM
        $atom = $this->getObject('geticon', 'htmlelements');
        $atom->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'atom', 'pageid' => $pageid)), $this->objLanguage->languageText("mod_cms_word_atom", "cms"));
        $atomfeed = $atom->show() . "&nbsp;" . $link->show() . "<br />";

        //Plain HTML
        $html = $this->getObject('geticon', 'htmlelements');
        $html->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'html', 'pageid' => $pageid)), $this->objLanguage->languageText("mod_cms_word_html", "cms"));
        $leftCol .= $html->show() . "&nbsp;" . $link->show() . "<br />";

        $icon = $this->getObject('geticon', 'htmlelements');
        $objIcon = &$this->getObject('geticon', 'htmlelements');
        $objIcon->setIcon('toggle');
        $str = "<a href=\"javascript:;\" onclick=\"Effect.toggle('feedmenu','slide', adjustLayout());\">" . $objIcon->show() . "</a>";

        $topper = $rss2feed . $atomfeed;

        $str .='<div id="feedmenu"  style="width:170px;overflow: hidden;display:none;"> ';
        $str .= $leftCol;
        $str .= '</div>';

        //$str .= '</div>';


        if ($featurebox == FALSE) {
            return $str;
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_cms_feedheader", "cms"), $topper . "<br />" . $str);
            return $ret;
        }
    }

    /**
     * Method to output a rss feeds box
     *
     * @param string $url
     * @param string $name
     * @return string
     */
    public function rssBox($url, $name) {
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $objRss = $this->getObject('rssreader', 'feed');
        $objRss->parseRss($url);
        $head = $this->objLanguage->languageText("mod_cms_word_headlinesfrom", "cmsadmin");
        $head .= " " . $name;
        $content = "<ul>\n";
        foreach ($objRss->getRssItems() as $item) {
            if (!isset($item['link'])) {
                $item['link'] = NULL;
            }
            @$content .= "<li><a href=\"" . htmlentities($item['link']) . "\">" . htmlentities($item['title']) . "</a></li>\n";
        }
        $content .= "</ul>\n";
        return $objFeatureBox->show($head, $content);
    }

    public function rssRefresh($rssurl, $name, $feedid) {
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $objRss = $this->getObject('rssreader', 'feed');
        $this->objConfig = $this->getObject('altconfig', 'config');

        //get the proxy info if set
        $objProxy = $this->getObject('proxyparser', 'utilities');
        $proxyArr = $objProxy->getProxy();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rssurl);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
            curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'] . ":" . $proxyArr['proxy_port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'] . ":" . $proxyArr['proxy_pass']);
        }
        $rsscache = curl_exec($ch);
        curl_close($ch);
        //var_dump($rsscache);
        //put in a timestamp
        $addtime = time();
        $addarr = array('url' => $rssurl, 'rsstime' => $addtime);

        //write the file down for caching
        $path = $this->objConfig->getContentBasePath() . "/cms/rsscache/";
        $rsstime = time();
        if (!file_exists($path)) {

            mkdir($path);
            chmod($path, 0777);
            $filename = $path . $this->objUser->userId() . "_" . $rsstime . ".xml";
            if (!file_exists($filename)) {
                touch($filename);
            }
            $handle = fopen($filename, 'wb');
            fwrite($handle, $rsscache);
        } else {
            $filename = $path . $this->objUser->userId() . "_" . $rsstime . ".xml";
            $handle = fopen($filename, 'wb');
            fwrite($handle, $rsscache);
        }
        //update the db
        $addarr = array('url' => htmlentities($rssurl), 'rsscache' => $filename, 'rsstime' => $addtime);
        //print_r($addarr);
        $this->objDbBlog->updateRss($addarr, $feedid);

        $objRss->parseRss($rsscache);
        $head = $this->objLanguage->languageText("mod_cms_word_headlinesfrom", "cmsadmin");
        $head .= " " . $name;
        $content = "<ul>\n";
        foreach ($objRss->getRssItems() as $item) {
            if (!isset($item['link'])) {
                $item['link'] = NULL;
            }
            @$content .= "<li><a href=\"" . htmlentities($item['link']) . "\">" . htmlentities($item['title']) . "</a></li>\n";
        }
        $content .= "</ul>\n";
        return $objFeatureBox->show($head, $content);
    }

    public function rssEditor($featurebox = FALSE, $rdata = NULL) {
        //print_r($rdata);
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');

        $this->objUser = $this->getObject('user', 'security');
        if ($rdata == NULL) {
            $rssform = new form('addrss', $this->uri(array(
                                'action' => 'addrss'
                            )));
        } else {
            $rdata = $rdata[0];
            $rssform = new form('addrss', $this->uri(array(
                                'action' => 'rssedit', 'mode' => 'edit', 'id' => $rdata['id']
                            )));
        }
        //add rules
        $rssform->addRule('rssurl', $this->objLanguage->languageText("mod_cms_phrase_rssurlreq", "cmsadmin"), 'required');
        $rssform->addRule('name', $this->objLanguage->languageText("mod_cms_phrase_rssnamereq", "cmsadmin"), 'required');
        //start a fieldset
        $rssfieldset = $this->getObject('fieldset', 'htmlelements');
        $rssadd = $this->newObject('htmltable', 'htmlelements');
        $rssadd->cellpadding = 3;

        //url textfield
        $rssadd->startRow();
        $rssurllabel = new label($this->objLanguage->languageText('mod_cms_rssurl', 'cmsadmin') . ':', 'input_rssuser');
        $rssurl = new textinput('rssurl');
        if (isset($rdata['url'])) {
            $rssurl->setValue($rdata['url']);
            // $rssurl->setValue('url');
        }
        $rssadd->addCell($rssurllabel->show());
        $rssadd->addCell($rssurl->show());
        $rssadd->endRow();

        //name
        $rssadd->startRow();
        $rssnamelabel = new label($this->objLanguage->languageText('mod_cms_rssname', 'cmsadmin') . ':', 'input_rssname');
        $rssname = new textinput('name');
        if (isset($rdata['name'])) {
            $rssname->setValue($rdata['name']);
        }
        $rssadd->addCell($rssnamelabel->show());
        $rssadd->addCell($rssname->show());
        $rssadd->endRow();

        //description
        $rssadd->startRow();
        $rssdesclabel = new label($this->objLanguage->languageText('mod_cms_rssdesc', 'cmsadmin') . ':', 'input_rssname');
        $rssdesc = new textarea('description');
        if (isset($rdata['description'])) {
            //var_dump($rdata['description']);
            $rssdesc->setValue($rdata['description']);
        }
        $rssadd->addCell($rssdesclabel->show());
        $rssadd->addCell($rssdesc->show());
        $rssadd->endRow();

        //end off the form and add the buttons
        $this->objRssButton = &new button($this->objLanguage->languageText('word_save', 'system'));
        $this->objRssButton->setValue($this->objLanguage->languageText('word_save', 'system'));
        $this->objRssButton->setToSubmit();
        $rssfieldset->addContent($rssadd->show());
        $rssform->addToForm($rssfieldset->show());
        $rssform->addToForm($this->objRssButton->show());
        $rssform = $rssform->show();

        //ok now the table with the edit/delete for each rss feed
        $efeeds = $this->objRss->getUserRss($this->objUser->userId());
        $ftable = $this->newObject('htmltable', 'htmlelements');
        $ftable->cellpadding = 3;
        //$ftable->border = 1;
        //set up the header row
        $ftable->startHeaderRow();
        $ftable->addHeaderCell($this->objLanguage->languageText("mod_cms_fhead_name", "cmsadmin"));
        $ftable->addHeaderCell($this->objLanguage->languageText("mod_cms_fhead_description", "cmsadmin"));
        $ftable->addHeaderCell('');
        $ftable->endHeaderRow();

        //set up the rows and display
        if (!empty($efeeds)) {
            foreach ($efeeds as $rows) {
                $ftable->startRow();
                $feedlink = new href($rows['url'], $rows['name']);
                $ftable->addCell($feedlink->show());
                //$ftable->addCell(htmlentities($rows['name']));
                $ftable->addCell(($rows['description']));
                $this->objIcon = &$this->getObject('geticon', 'htmlelements');
                $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                            'action' => 'addrss',
                            'mode' => 'edit',
                            'id' => $rows['id'],
                            //'url' => $rows['url'],
                            //'description' => $rows['description'],
                            'module' => 'cmsadmin'
                        )));
                $delIcon = $this->objIcon->getDeleteIconWithConfirm($rows['id'], array(
                    'module' => 'cmsadmin',
                    'action' => 'deleterss',
                    'id' => $rows['id']
                        ), 'cmsadmin');
                $ftable->addCell($edIcon . $delIcon);
                $ftable->endRow();
            }
            //$ftable = $ftable->show();
        }

        return $rssform . $ftable->show();
    }

    /**
     * Function addCommentForm
     *
     */
    public function addCommentForm($postid, $userid, $captcha = FALSE, $comment = NULL, $useremail = NULL) {
        $this->objComApi = $this->getObject('commentapi', 'cmscomments');
        return $this->objComApi->commentAddForm($postid, 'cms', 'tbl_cms_content', $userid, TRUE, TRUE, FALSE, $captcha, $comment, $useremail);
    }

    /**
     *
     */
    public function setComments($post, $icon = TRUE) {
        //COMMENTS
        if ($icon == TRUE) {
            $objLink = new link($this->uri(array(
                                'action' => 'viewsingle',
                                'postid' => $post['id'],
                                'userid' => $post['userid']
                                    ), 'blog'));
            $comment_icon = $this->newObject('geticon', 'htmlelements');
            $comment_icon->setIcon('comment');
            $lblView = $this->objLanguage->languageText("mod_blog_addcomment", "blog");
            $comment_icon->alt = $lblView;
            $comment_icon->align = false;
            $objLink->link = $comment_icon->show();
            return $objLink->show();
        } else {
            $objLink = new href($this->uri(array(
                                'action' => 'viewsingle',
                                'postid' => $post['id'],
                                'userid' => $post['userid']
                            )), $this->objLanguage->languageText("mod_blog_comments", "blog"), NULL);
            return $objLink->show();
        }
    }

}
?>
