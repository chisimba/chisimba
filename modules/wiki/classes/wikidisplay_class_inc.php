<?php
/* ----------- templates class extends object ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Templates class for wiki version 2 module
* @author Kevin Cyster
*/

class wikidisplay extends object
{
    /**
    * @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;

    /**
    * @var object $objDbwiki: The dbwiki class in the wiki version 2 module
    * @access public
    */
    public $objDbwiki;

    /**
    * @var object $objIcon: The geticon class in the htmlelements module
    * @access public
    */
    public $objIcon;

    /**
    * @var object $objFeature: The featurebox class in the navigation module
    * @access public
    */
    public $objFeature;

    /**
    * @var object $objPopup: The windowpop class in the htmlelements module
    * @access public
    */
    public $objPopup;

    /**
    * @var object $objDate: The dateandtime class in the utilities module
    * @access public
    */
    public $objDate;

    /**
    * @var object $objUser: The user class in the securities module
    * @access public
    */
    public $objUser;

    /**
    * @var string $userId: The user id of the current logged in user
    * @access public
    */
    public $userId;

    /**
    * @var string $isLoggedIn: The login status of the user
    * @access public
    */
    public $isLoggedIn;

    /**
    * @var bool $isAdmin: TRUE if the user is in the Site Admin group FALSE if not
    * @access public
    */
    public $isAdmin;

    /**
    * @var bool $isContextLecturer: TRUE if the user is a lecturer of the current course group FALSE if not
    * @access public
    */
    public $isContextLecturer;

    /**
    * @var object $objTab: The tabpane class in the htmlelements module
    * @access public
    */
    public $objTab;

    /**
    * @var object $objBizCard: The userbizcard class in the useradmin module
    * @access public
    */
    public $objBizCard;

    /**
    * @var object $objUserAdmin: The useradmin_model2 class in the security module
    * @access public
    */
    public $objUserAdmin;

    /**
    * @var object $objBlocks: The blocks class in the blocks module
    * @access public
    */
    public $objBlocks;

    /**
    * @var object $objMailer: The email class in the mail module
    * @access public
    */
    public $objMailer;

    /**
    * @var object $objWash: The washout class in the utilities module
    * @access public
    */
    public $objWash;

    /**
    * @var object $objWiki: The wikitextparser class in the wiki version 2 module
    * @access public
    */
    public $objWiki;

    /**
    * @var object $objTextdiff: The wikitextdiff class in the wiki version 2 module
    * @access public
    */
    public $objTextdiff;

    /**
    * @var object $objContext: The dbcontext class in the context module
    * @access public
    */
    public $objContext;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // load html element classes
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('layer', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('iframe', 'htmlelements');

        // system classes
        $this->objLanguage = $this->getObject('language','language');
        $this->objDbwiki = $this->getObject('dbwiki', 'wiki');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objFeature = $this->newObject('featurebox', 'navigation');
        $this->objPopup = $this->newObject('windowpop', 'htmlelements');
        $this->objWiki = $this->getObject('wikitextparser', 'wiki');
        $this->objDate = $this->getObject('dateandtime', 'utilities');
        $this->objUser = $this->getObject('user', 'security');
        $this->objBlocks = $this->getObject('blocks', 'blocks');
        $this->userId = $this->objUser->userId();
        $this->isLoggedIn = $this->objUser->isLoggedIn();
        $this->isAdmin = $this->objUser->inAdminGroup($this->userId);
        $this->isContextLecturer = $this->objUser->isContextLecturer();

        $this->objTab = $this->newObject('tabber', 'htmlelements');
        $this->objBizCard = $this->getObject('userbizcard', 'useradmin');
        $this->objUserAdmin = $this->getObject('useradmin_model2','security');
        $this->objConfig = $this->getObject('altconfig', 'config');
		$this->objMailer = $this->getObject('mailer', 'mail');
		$this->objWash = $this->getObject('washout', 'utilities');
		$this->objTextdiff = $this->getObject('wikitextdiff', 'wiki');

        $contextExists = $this->getSession('context_exists');
        if($contextExists){
            $this->objContext = $this->getObject('dbcontext', 'context');
        }
    }

    /**
    * Method to create left column content of the wiki
    *
    * @access public
    * @return string $str: The output string
    **/
    public function showWikiToolbar()
    {
        // text elements
        $nameLabel = $this->objLanguage->languageText('mod_wiki_name', 'wiki');
        $mainLabel = $this->objLanguage->languageText('mod_wiki_main', 'wiki');
        $viewLabel = $this->objLanguage->languageText('mod_wiki_view', 'wiki');
        $addLabel = $this->objLanguage->languageText('mod_wiki_add', 'wiki');
        $formatLabel = $this->objLanguage->languageText('mod_wiki_format', 'wiki');
        $mainTitleLabel = $this->objLanguage->languageText('mod_wiki_maintitle', 'wiki');
        $viewTitleLabel = $this->objLanguage->languageText('mod_wiki_viewtitle', 'wiki');
        $addTitleLabel = $this->objLanguage->languageText('mod_wiki_addtitle', 'wiki');
        $formatTitleLabel = $this->objLanguage->languageText('mod_wiki_formattitle', 'wiki');
        $searchLabel = $this->objLanguage->languageText('word_search');
        $searchWikiLabel = $this->objLanguage->languageText('mod_wiki_searchwiki', 'wiki');
        $addedLabel = $this->objLanguage->languageText('mod_wiki_added', 'wiki');
        $updatedLabel = $this->objLanguage->languageText('mod_wiki_updated', 'wiki');
        $errorLabel = $this->objLanguage->languageText('mod_wiki_searcherror', 'wiki');
        $noPageLabel = $this->objLanguage->languageText('mod_wiki_nopages', 'wiki');
        $pageTitleLabel = $this->objLanguage->languageText('mod_wiki_pagetitle', 'wiki');
        $titleLabel = $this->objLanguage->languageText('mod_wiki_byname', 'wiki');
        $contentLabel = $this->objLanguage->languageText('mod_wiki_bycontent', 'wiki');
        $bothLabel = $this->objLanguage->languageText('mod_wiki_both', 'wiki');
        $authorLabel = $this->objLanguage->languageText('mod_wiki_authors', 'wiki');
        $authorsTitleLabel = $this->objLanguage->languageText('mod_wiki_authorstitle', 'wiki');
        $rankingLabel = $this->objLanguage->languageText('mod_wiki_viewranking', 'wiki');
        $rankingTitleLabel = $this->objLanguage->languageText('mod_wiki_rankingtitle', 'wiki');
        $watchLabel = $this->objLanguage->languageText('mod_wiki_watchlist', 'wiki');
        $watchTitleLabel = $this->objLanguage->languageText('mod_wiki_watchlisttitle', 'wiki');
        $linkLabel = $this->objLanguage->languageText('mod_wiki_viwlinks', 'wiki');
        $linkTitleLabel = $this->objLanguage->languageText('mod_wiki_viewlinktitle', 'wiki');
        $createLabel = $this->objLanguage->languageText('mod_wiki_createwiki', 'wiki');
        $createTitleLabel = $this->objLanguage->languageText('mod_wiki_createwikititle', 'wiki');
        $createContextLabel = $this->objLanguage->languageText('mod_wiki_createcontextwiki', 'wiki');
        $createContextTitleLabel = $this->objLanguage->languageText('mod_wiki_createcontexttitle', 'wiki');

        $str = '';
        // login box
        if(!$this->isLoggedIn){
            $loginBlock = $this->objBlocks->showBlock('login', 'security', '', 20, TRUE, TRUE, 'none');
            $str .= $loginBlock;
        }

        $string = $this->showSelector();

        // links
        $string .= '<ul>';
        if($this->isLoggedIn){
            // get data
            $hasWiki = $this->objDbwiki->hasPersonalWiki($this->userId);
            //if(!$hasWiki){
                // create wiki link
                $objLink = new link($this->uri(array(
                    'action' => 'add_wiki',
                ), 'wiki'));
                $objLink->link = $createLabel;
                $objLink->title = $createTitleLabel;
                $addLink = $objLink->show();
                $string .= '<li>'.$addLink.'</li>';
           // }

            $contextExists = $this->getSession('context_exists');
            if($contextExists){
                $contextCode = $this->objContext->getContextCode();
                if(!empty($contextCode)){
                    $hasContextWiki = $this->objDbwiki->hasContextWiki($contextCode);
                    if(!$hasContextWiki){
                        // create wiki link
                        $objLink = new link($this->uri(array(
                            'action' => 'context_wiki',
                            'contextCode' => $contextCode,
                        ), 'wiki'));
                        $objLink->link = $createContextLabel;
                        $objLink->title = $createContextTitleLabel;
                        $addLink = $objLink->show();
                        $string .= '<li>'.$addLink.'</li>';
                    }
                }
            }
        }

        // main page link
        $objLink = new link($this->uri(array(
            'action' => 'view_page',
        ), 'wiki'));
        $objLink->link = $mainLabel;
        $objLink->title = $mainTitleLabel;
        $mainLink = $objLink->show();
        $string .= '<li>'.$mainLink.'</li>';

        if($this->isLoggedIn){
            // add page link
            $objLink = new link($this->uri(array(
                'action' => 'add_page',
            ), 'wiki'));
            $objLink->link = $addLabel;
            $objLink->title = $addTitleLabel;
            $addLink = $objLink->show();
            $string .= '<li>'.$addLink.'</li>';
        }

        // view all page link
        $objLink = new link($this->uri(array(
            'action' => 'view_all',
        ), 'wiki'));
        $objLink->link = $viewLabel;
        $objLink->title = $viewTitleLabel;
        $viewLink = $objLink->show();
        $string .= '<li>'.$viewLink.'</li>';

        // view all authors link
        $objLink = new link($this->uri(array(
            'action' => 'view_authors',
        ), 'wiki'));
        $objLink->link = $authorLabel;
        $objLink->title = $authorsTitleLabel;
        $viewLink = $objLink->show();
        $string .= '<li>'.$viewLink.'</li>';

        // view ranking link
        $objLink = new link($this->uri(array(
            'action' => 'view_ranking',
        ), 'wiki'));
        $objLink->link = $rankingLabel;
        $objLink->title = $rankingTitleLabel;
        $rankLink = $objLink->show();
        $string .= '<li>'.$rankLink.'</li>';

        if($this->isLoggedIn){
            // watchlist link
            $objLink = new link($this->uri(array(
                'action' => 'view_watchlist',
            ), 'wiki'));
            $objLink->link = $watchLabel;
            $objLink->title = $watchTitleLabel;
            $watchLink = $objLink->show();
            $string .= '<li>'.$watchLink.'</li>';
        }

        // watchlist link
        $objLink = new link($this->uri(array(
            'action' => 'view_links',
        ), 'wiki'));
        $objLink->link = $linkLabel;
        $objLink->title = $linkTitleLabel;
        $linksLink = $objLink->show();
        $string .= '<li>'.$linksLink.'</li>';

        // popup link for formatting rules
        $objPopup = new windowpop();
        $objPopup->title = $formatTitleLabel;
        $objPopup->set('location', $this->uri(array(
            'action' => 'view_rules',
        ), 'wiki'));
        $objPopup->set('linktext', $formatLabel);
        $objPopup->set('width', '550');
        $objPopup->set('height', '500');
        $objPopup->set('left', '100');
        $objPopup->set('top', '100');
        $objPopup->set('scrollbars', 'yes');
        $objPopup->putJs(); // you only need to do this once per page
        $formatPopup = $objPopup->show();
        $string .= '<li>'.$formatPopup.'</li>';

        $string .= '</ul>';

        $str .= $this->objFeature->show($nameLabel, $string);

        // search box
        $objDrop = new dropdown('field');
        $objDrop->addOption(1, $bothLabel);
        $objDrop->addOption(2, $titleLabel);
        $objDrop->addOption(3, $contentLabel);
        $objDrop->setSelected(1);
        $fieldDrop = $objDrop->show();

        $objInput = new textinput('value', '', '', '');
        $valueInput = $objInput->show();

        $objButton = new button('search', $searchLabel);
        $objButton->setIconClass("search");
        $objButton->setToSubmit();
        $searchButton = $objButton->show();

        $objForm = new form('search', $this->uri(array(
            'action' => 'search_wiki',
        ), 'wiki'));
        $objForm->addToForm($fieldDrop.'<p />');
        $objForm->addToForm($valueInput);
        $objForm->addToForm($searchButton);
        $objForm->addRule('value', $errorLabel, 'required');
        $searchForm = $objForm->show();

        $str .= $this->objFeature->show($searchWikiLabel, $searchForm);

        // recently added pages
        $data = $this->objDbwiki->getRecentlyAdded();
        $string = '';
        if(!empty($data)){
            $string = '<ul>';
            foreach($data as $line){
                $name = $line['page_name'];
                $pageTitle = $this->objWiki->renderTitle($name);
                $objLink = new link($this->uri(array(
                    'action' => 'view_page',
                    'name' => $name,
                ), 'wiki'));
                $objLink->link = $pageTitle;
                $objLink->title = $pageTitleLabel;
                $pageLink = $objLink->show();
                $string .= '<li>'.$pageLink .'</li>';
            }
            $string .= '</ul>';
        }else{
            $string = '<ul><li>'.$noPageLabel.'</li></ul>';
        }
        $str .= $this->objFeature->show($addedLabel, $string);

        // recently updated pages
        $data = $this->objDbwiki->getRecentlyUpdated();
        $string = '';
        if(!empty($data)){
            $string = '<ul>';
            foreach($data as $line){
                $name = $line['page_name'];
                $pageTitle = $this->objWiki->renderTitle($name);
                $objLink = new link($this->uri(array(
                    'action' => 'view_page',
                    'name' => $name,
                ), 'wiki'));
                $objLink->link = $pageTitle;
                $objLink->title = $pageTitleLabel;
                $pageLink = $objLink->show();
                $string .= '<li>'.$pageLink .'</li>';
            }
            $string .= '</ul>';
        }else{
            $string = '<ul><li>'.$noPageLabel.'</li></ul>';
        }
        $str .= $this->objFeature->show($updatedLabel, $string);

        return $str.'<br />';
    }

    /**
    * Method to display the main wiki content area
    *
    * @access public
    * @param string $name: The name of the page to show
    * @param integer $version: The version of the page to show
    * @param integer $tab: The tab to set to default
    * @return string $str: The output string
    **/
    public function showMain($name = NULL, $version = NULL, $tab = 0)
    {
        // add  javascript
        $headerParams = $this->getJavascriptFile('wiki.js', 'wiki');
        $this->appendArrayVar('headerParams', $headerParams);

        // get data
        if(empty($name)){
            $data = $this->objDbwiki->getMainPage();
        }else{
            $data = $this->objDbwiki->getPage($name, $version);
        }
        $wiki = $this->objDbwiki->getWiki($data['wiki_id']);
        $pageId = $data['id'];
        $name = $data['page_name'];
        $pageTitle = $this->objWiki->renderTitle($name);

        //Stopping htmlentities in output here:
        //$this->objWiki->setRenderConf('Xhtml', 'translate', null);

        $text = $this->objWiki->transform($data['page_content'], 'Xhtml', false);
        $wikiText = $this->objWash->parseText($text);
        $array = array(
            'date' => $this->objDate->formatDate($data['date_created']),
        );
        $modifiedLabel = $this->objLanguage->code2Txt('mod_wiki_modified', 'wiki', $array);

        // text elements
        $articleLabel = $this->objLanguage->languageText('word_article');
        $historyLabel = $this->objLanguage->languageText('word_history');
        $editLabel = $this->objLanguage->languageText('word_edit');
        $versionLabel = $this->objLanguage->languageText('word_version');
        $editArticelLabel = $this->objLanguage->languageText('mod_wiki_editarticle', 'wiki');
        $previewLabel = $this->objLanguage->languageText('word_preview');
        $ratingLabel = $this->objLanguage->languageText('word_rating');
        $diffLabel = $this->objLanguage->languageText('word_diff');
        $addedLabel = $this->objLanguage->languageText('mod_wiki_textadded', 'wiki');
        $removedLabel = $this->objLanguage->languageText('mod_wiki_textremoved', 'wiki');
        $legendLabel = $this->objLanguage->languageText('word_legend');
        $discussionLabel = $this->objLanguage->languageText('word_discussion');
        $deleteLabel = $this->objLanguage->languageText('mod_wiki_deletepage', 'wiki');
        $deleteTitleLabel = $this->objLanguage->languageText('mod_wiki_deletetitle', 'wiki');
        $delConfirmLabel = $this->objLanguage->languageText('mod_wiki_deleteconfirm', 'wiki');
        $visibilityLabel = $this->objLanguage->languageText('mod_wiki_visibility', 'wiki');

        // wiki page
        $contents = '';
        if(empty($version)){
            if($this->isLoggedIn && $data['main_page'] != 1){
                $objButton = new button('submit', $deleteLabel);
                $deleteButton = $objButton->show();

                $objLink = new link($this->uri(array(
                    'action' => 'delete_page',
                    'name' => $name,
                ), 'wiki'));
                $objLink->link = $deleteButton;
                $objLink->title = $deleteTitleLabel;
                $objLink->extra = 'onclick="javascript:if(!confirm(\''.$delConfirmLabel.'\')){return false};"';
                $contents .= $objLink->show();
            }
            $versionTitle = $pageTitle;
        }else{
            $versionTitle = $pageTitle.':&#160;'.$versionLabel.'&#160;'.$version;
        }
        $objHeader = new htmlheading();
        $objHeader->str = $versionTitle;
        $objHeader->type = 1;
        $heading = $objHeader->show();
        $contents .= $heading;

        // visibility nested tab
        if((($wiki['creator_id'] == $this->userId and $wiki['group_type'] == 'personal')
            or ($wiki['group_type'] == 'context' and ($this->isContextLecturer or $this->isAdmin)))
                and $data['page_name'] == 'MainPage'){
            $visibility = $this->_showVisibility($wiki);

            $objLayer = new layer();
            $objLayer->id = 'visibilityDiv';
            $objLayer->addToStr($visibility);
            $visibilityLayer = $objLayer->show();

            $visibilityTab = array(
                'name' => $visibilityLabel,
                'content' => $visibilityLayer,
            );
        }else{
            $visibilityTab = '';
        }

        // rating nested tab
        if(empty($version)){
            $rating = $this->showRating($name);

            $objLayer = new layer();
            $objLayer->id = 'ratingDiv';
            $objLayer->addToStr($rating);
            $ratingLayer = $objLayer->show();

            $ratingTab = array(
                'name' => $ratingLabel,
                'content' => $ratingLayer,
            );
        }

        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'visibilityTab';
        $this->objTab->addTab($visibilityTab);
        $string = $this->objTab->show();
        $contents .= $string.'<br />';

        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'ratingTab';
        $this->objTab->addTab($ratingTab);
        $string = $this->objTab->show();
        $contents .= $string.'<br />';

        $contents .= $wikiText;
        $contents .= '<hr />'.$modifiedLabel;

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($contents);
        $contentLayer = $objLayer->show();

        // wiki page tab
        $mainTab = array(
            'name' => $articleLabel,
            'content' => $contentLayer,
        );

        // edit page
        $objLayer = new layer();
        $objLayer->id = 'lockedDiv';
        $objLayer->cssClass = 'featurebox';
        $editLayer = $objLayer->show();

        // edit page tab
        $lockedTab = array(
            'name' => $editLabel,
            'content' => $editLayer,
        );

        $edit = $this->_showEditPage($pageId);
        $editTab = array(
            'name' => $editArticelLabel,
            'content' => $edit,
        );

        // preview iframe
        $objIframe = new iframe();
        $objIframe->id = 'submitIframe';
        $objIframe->frameborder = '0';
        $objIframe->width = '100%';
        $objIframe->src = $this->uri(array(
            'action' => 'preview_iframe'
        ), 'wiki');
        $submitIframe = $objIframe->show();

        $previewTab = array(
            'name' => $previewLabel,
            'content' => $submitIframe,
        );

        // page history
        $history = $this->_showPageHistory($name);

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($history);
        $contentLayer = $objLayer->show();

        // page history tab
        $historyTab = array(
            'name' => $historyLabel,
            'content' => $contentLayer,
        );

        // diff tab
        // legend
        $legend = '<font class="diff_add">'.$addedLabel.'</font>';
        $legend .= '<br />';
        $legend .= '<font class="diff_remove">'.$removedLabel.'</font>';

        $objLayer = new layer();
        $objLayer->id = 'legendDiv';
        $objLayer->addToStr($legend);
        $legendLayer = $objLayer->show();

        $legendTab = array(
            'name' => $legendLabel,
            'content' => $legendLayer,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'legendTab';
        $this->objTab->addTab($legendTab);
        $string = $this->objTab->show();

        $objLayer = new layer();
        $objLayer->id = 'diffDiv';
        $diffLayer = $objLayer->show();

        $objLayer = new layer();
        $objLayer->id = 'mainDiv';
        $objLayer->addToStr($string.'<br />'.$diffLayer);
        $objLayer->cssClass = 'featurebox';
        $mainLayer = $objLayer->show();

        $diffTab = array(
            'name' => $diffLabel,
            'content' => $mainLayer,
        );

        //discussion tab
        $discussion = $this->showDiscussion($name);

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($discussion.'<br />');
        $contentLayer = $objLayer->show();

        // add page tab
        $discussionTab = array(
            'name' => $discussionLabel,
            'content' => $contentLayer,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'mainTab';
        $this->objTab->addTab($mainTab);
        if($this->isLoggedIn){
            $this->objTab->addTab($lockedTab);
            $this->objTab->addTab($editTab);
            $this->objTab->addTab($previewTab);
        }
        $this->objTab->addTab($historyTab);
        $this->objTab->addTab($diffTab);
        $this->objTab->addTab($discussionTab);
        $this->objTab->setSelected = $tab;
        $str = $this->objTab->show();
        if(empty($version) && $this->isLoggedIn){
            $body = 'tabClickEvents("can_edit");';
        }else{
            if($this->isLoggedIn){
                $body = 'tabClickEvents("no_edit", "true");';
            }else{
                $body = 'tabClickEvents("no_edit", "false");';
            }
        }
        $this->appendArrayVar('bodyOnLoad', $body);
        return $str.'<br />';
    }

    /**
    * Method to show the page history
    *
    * @access private
    * @param string $name: The name of the page to show the history for
    * @return string $str: The table with the page history
    */
    private function _showPageHistory($name)
    {
        // get data
        $data = $this->objDbwiki->getPagesByName($name);
        $pageName = $data[0]['page_name'];
        $pageTitle = $this->objWiki->renderTitle($pageName);

        // text elements
        $versionLabel = $this->objLanguage->languageText('word_version');
        $authorLabel = $this->objLanguage->languageText('word_author');
        $dateLabel = $this->objLanguage->languageText('mod_wiki_datecreated', 'wiki');
        $noRecordsLabel = $this->objLanguage->languageText('mod_wiki_norecords', 'wiki');
        $pageTitleLabel = $this->objLanguage->languageText('mod_wiki_pagetitle', 'wiki');
        $authorTitleLabel = $this->objLanguage->languageText('mod_wiki_authortitle', 'wiki');
        $originalLabel = $this->objLanguage->languageText('word_original');
        $restoreLabel = $this->objLanguage->languageText('word_restore');
        $restoreTitleLabel = $this->objLanguage->languageText('mod_wiki_restoretitle', 'wiki');
        $confRestoreLabel = $this->objLanguage->languageText('mod_wiki_restoreconfirm', 'wiki');
        $restoredLabel = $this->objLanguage->languageText('word_restored');
        $overwrittenLabel = $this->objLanguage->languageText('word_overwritten');
        $commentLabel = $this->objLanguage->languageText('mod_wiki_comment', 'wiki');
        $reinstatedLabel = $this->objLanguage->languageText('word_reinstated');
        $reinstateLabel = $this->objLanguage->languageText('word_reinstate');
        $reinstateTitleLabel = $this->objLanguage->languageText('mod_wiki_reinstatetitle', 'wiki');
        $confReinstateLabel = $this->objLanguage->languageText('mod_wiki_reinstateconfirm', 'wiki');
        $archivedLabel = $this->objLanguage->languageText('word_archived');
        $deletedLabel = $this->objLanguage->languageText('word_deleted');
        $viewLabel = $this->objLanguage->languageText('mod_wiki_viewdiff', 'wiki');
        $viewTitleLabel = $this->objLanguage->languageText('mod_wiki_difftitle', 'wiki');
        $diffLabel = $this->objLanguage->languageText('word_diff');
        $fromLabel = $this->objLanguage->languageText('word_from');
        $toLabel = $this->objLanguage->languageText('word_to');

        // diff link
        $objButton = new button('submit', $viewLabel);
        $viewButton = $objButton->show();

        $objLink = new link('#');
        $objLink->link = $viewButton;
        $objLink->title = $viewTitleLabel;
        if($this->isLoggedIn){
            $objLink->extra = 'onclick="javascript:getDiff(\'true\', \''.$pageName.'\');"';
        }else{
            $objLink->extra = 'onclick="javascript:getDiff(\'false\', \''.$pageName.'\');"';
        }
        $diffLink = $objLink->show();
        if(count($data) > 1){
            $str = $diffLink;
        }else{
            $str = '';
        }

        // page heading
        $objHeader = new htmlheading();
        $objHeader->str = $pageTitle;
        $objHeader->type = 1;
        $heading = $objHeader->show();
        $str .= $heading;

        // create display table
        $objTable = new htmltable();
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        if(count($data) > 1){
            $objTable->addCell('<b>'.$diffLabel.'<br />'.$fromLabel.'&#160;|&#160;'.$toLabel.'</b>', '5%', '', 'center', 'heading', 'rowspan="2"');
        }
        $objTable->addCell('&#160;'.'<b>'.$versionLabel.'</b>', '', '', '', 'heading', '');
        $objTable->addCell('&#160;'.'<b>'.$authorLabel.'</b>', '', '', '', 'heading', '');
        $objTable->addCell('<b>'.$dateLabel.'</b>', '', '', 'center', 'heading', '');
        $objTable->addCell('&#160;', '', '', 'center', 'heading', 'rowspan="2"');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('&#160;'.'<b>'.$commentLabel.'</b>', '', '', '', 'heading', 'colspan="4"');
        $objTable->endRow();

        if(empty($data)){
            // no records
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
            $objTable->endRow();
        }else{
            // loop through data and display each record in the table
            $i = 0;
            foreach($data as $line){
                $class = (($i++%2) == 0)?'even':'odd';
                $pageId = $line['id'];
                $name = $line['page_name'];
                $pageVersion = $line['page_version'];
                $pageStatus = $line['page_status'];
                $versionComment = $line['version_comment'];

                if($pageVersion == 1 && $pageStatus != 5){
                    $version = $pageVersion.'&#160;-&#160;'.$originalLabel;
                }elseif($pageStatus == 2){
                    $version = $pageVersion.'&#160;-&#160;'.$restoredLabel;
                }elseif($pageStatus == 3){
                    $version = $pageVersion.'&#160;-&#160;'.$reinstatedLabel;
                }elseif($pageStatus == 4){
                    $version = $pageVersion.'&#160;-&#160;'.$overwrittenLabel;
                }elseif($pageStatus == 5){
                    $version = $pageVersion.'&#160;-&#160;'.$archivedLabel;
                }elseif($pageStatus == 6){
                    $version = $pageVersion.'&#160;-&#160;'.$deletedLabel;
                }else{
                    $version = $line['page_version'];
                }
                $authorId = $line['page_author_id'];
                $author = $this->objUser->fullname($authorId);
                $date = $this->objDate->formatDate($line['date_created']);

                // page name link
                if(count($data) == $pageVersion){
                    $action = $this->uri(array(
                        'action' => 'view_page',
                        'name' => $name,
                    ), 'wiki');
                }else{
                    $action = $this->uri(array(
                        'action' => 'view_page',
                        'name' => $name,
                        'version' => $pageVersion,
                    ), 'wiki');
                }
                $objLink = new link($action);
                $objLink->link = $version;
                $objLink->title = $pageTitleLabel;
                $pageLink = $objLink->show();

                // author link
                $objLink = new link($this->uri(array(
                    'action' => 'view_authors',
                    'author' => $authorId,
                ), 'wiki'));
                $objLink->link = $author;
                $objLink->title = $authorTitleLabel;
                $authorLink = $objLink->show();

                // restore link
                if($pageStatus < 5 && count($data) != $pageVersion){
                    $objLink = new link($this->uri(array(
                        'action' => 'restore_page',
                        'name' => $name,
                        'version' => $pageVersion,
                    ), 'wiki'));
                    $objLink->link = $restoreLabel;
                    $objLink->title = $restoreTitleLabel;
                    $objLink->extra = 'onclick="javascript:if(!confirm(\''.$confRestoreLabel.'\')){return false};"';
                    $restoreLink = $objLink->show();
                }elseif($pageStatus == 6 && $this->isAdmin){
                    $objLink = new link($this->uri(array(
                        'action' => 'restore_page',
                        'name' => $name,
                        'version' => $pageVersion,
                        'mode' => 'reinstate',
                    ), 'wiki'));
                    $objLink->link = $reinstateLabel;
                    $objLink->title = $reinstateTitleLabel;
                    $objLink->extra = 'onclick="javascript:if(!confirm(\''.$confReinstateLabel.'\')){return false};"';
                    $restoreLink = $objLink->show();
                }else{
                    $restoreLink = '&#160;';
                }

                // diff radios
                if($pageVersion != count($data)){
                    $objRadio = new radio('from');
                    $objRadio->addOption($pageVersion, '');
                    $objRadio->setSelected(count($data) - 1);
                    $objRadio->extra = 'style="vertical-align: middle;" onclick="javascript:manipulateRadios(this);"';
                    $fromRadio = $objRadio->show();
                }else{
                    $fromRadio = '&#160;';
                }

                if($pageVersion != 1){
                    $objRadio = new radio('to');
                    $objRadio->addOption($pageVersion, '');
                    $objRadio->setSelected(count($data));
                    if($pageVersion != count($data)){
                        $objRadio->extra = 'style="vertical-align: middle; visibility: hidden;" onclick="javascript:manipulateRadios(this);"';
                    }else{
                        $objRadio->extra = 'style="vertical-align: middle;" onclick="javascript:manipulateRadios(this);"';
                    }
                    $toRadio = $objRadio->show();
                }else{
                    $toRadio = '&#160;';
                }

                $subTable = new htmltable();
                $subTable->startRow();
                $subTable->addCell($fromRadio, '50%', '', 'right', '', '');
                $subTable->addCell('&#160;'.$toRadio, '50%', '', 'left', '', '');
                $subTable->endRow();
                $radioTable = $subTable->show();

                // data display
                $objTable->startRow();
                if(count($data) > 1){
                    $objTable->addCell($radioTable, '', '', 'center', $class, 'rowspan="2"');
                }
                $objTable->addCell('&#160;'.$pageLink, '', '', '', $class, '');
                $objTable->addCell('&#160;'.$authorLink, '30%', '', '', $class, '');
                $objTable->addCell($date, '20%', '', 'center', $class, '');
                $objTable->addCell($restoreLink, '', '', 'center', $class, 'rowspan="2"');
                $objTable->endRow();
                $objTable->startRow();
                $objTable->addCell('&#160;'.$versionComment, '', '', '', $class, 'colspan="3"');
                $objTable->endRow();
            }
        }
        $pageTable = $objTable->show();
        $str .= $pageTable;

        return $str;
    }

    /**
    * Method to edit wiki pages
    *
    * @access private
    * @param string $id: The id of the page to be edited
    * @return string $str: The output string
    */
    private function _showEditPage($id)
    {
        // add header params
        $objMk = $this->getObject('markitup', 'markitup');
        $objMk->setType('chiki');
        $headerParams = $objMk->show('id', 'input_wikiContent');
        $this->appendArrayVar('headerParams', $headerParams);

        // get data
        $data = $this->objDbwiki->getPageById($id);
        $getWatched = $this->objDbwiki->getUserPageWatch($data['page_name']);
        $pageTitle = $this->objWiki->renderTitle($data['page_name']);

        // text elements
        $contentLabel = $this->objLanguage->languageText('mod_wiki_pagecontent', 'wiki');
        $contentErrorLabel = $this->objLanguage->languageText('mod_wiki_contenterror', 'wiki');
        $commentLabel = $this->objLanguage->languageText('mod_wiki_comment', 'wiki');
        $commentErrorLabel = $this->objLanguage->languageText('mod_wiki_commenterror', 'wiki');
        $updateLabel = $this->objLanguage->languageText('mod_wiki_update', 'wiki');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');
        $summaryLabel = $this->objLanguage->languageText('mod_wiki_pagesummary', 'wiki');
        $summaryErrorLabel = $this->objLanguage->languageText('mod_wiki_summaryerror', 'wiki');

        // add to watchlist
        $watchList = $this->showAddWatchlist(!empty($getWatched), TRUE);

        // heading
        $objHeader = new htmlheading();
        $objHeader->str = $pageTitle;
        $objHeader->type = 1;
        $heading = $objHeader->show();

        // summary
        $objHeader = new htmlheading();
        $objHeader->str = $summaryLabel;
        $objHeader->type = 4;
        $heading .= $objHeader->show();

        // summary textarea
        $objText = new textarea('summary', $data['page_summary'], '4', '70');
        $summaryText = $objText->show();

        $objInput = new textinput('choice', 'no', 'hidden', '');
        $hiddenInput = $objInput->show();

        $objInput = new textinput('id', $data['id'], 'hidden', '');
        $hiddenInput .= $objInput->show();

        // summary layer
        $objLayer = new layer();
        $objLayer->addToStr($heading.$summaryText.$hiddenInput);
        $summaryLayer = $objLayer->show();

        // content
        $objHeader = new htmlheading();
        $objHeader->str = $contentLabel;
        $objHeader->type = 4;
        $heading = $objHeader->show();

        // page name textinput
        $objInput = new textinput('name', $data['page_name'], 'hidden', '');
        $nameInput = $objInput->show();

        // main page textinput
        $objInput = new textinput('main', $data['main_page'], 'hidden', '');
        $mainInput = $objInput->show();

        // content textarea
        $objText = new textarea('wikiContent', $data['page_content'], '25', '70');
        $contentText = $objText->show();

        // content layer
        $objLayer = new layer();
        $objLayer->addToStr($heading.$nameInput.$mainInput.$contentText);
        $contentLayer = $objLayer->show();

        // comment
        $objHeader = new htmlheading();
        $objHeader->str = $commentLabel;
        $objHeader->type = 4;
        $heading = $objHeader->show();

        // comment textarea
        $objText = new textarea('comment', '', '4', '70');
        $commentText = $objText->show();

        // comment layer
        $objLayer = new layer();
        $objLayer->addToStr($heading.$commentText);
        $commentLayer = $objLayer->show();

        // create button
        $objButton = new button('update', $updateLabel);
        $objButton->setIconClass("save");
        $objButton->extra = 'onclick="javascript: validateUpdatePage(\''.$summaryErrorLabel.'\', \''.$contentErrorLabel.'\', \''.$commentErrorLabel.'\');"';
        $updateButton = $objButton->show();

        // create button
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setIconClass("cancel");
        $objButton->extra = 'onclick="javascript:exitEdit();"';
        $cancelButton = $objButton->show();

        // button layer
        $objLayer = new layer();
        $objLayer->addToStr($updateButton.'&#160;'.$cancelButton);
        $buttonLayer = $objLayer->show();

        // form
        $objForm = new form('update', $this->uri(array(
            'action' => 'update_page',
        ), 'wiki'));
        $objForm->addToForm($watchList);
        $objForm->addToForm($summaryLayer);
        $objForm->addToForm($contentLayer);
        $objForm->addToForm($commentLayer);
        $objForm->addToForm($buttonLayer);
        $createForm = $objForm->show();
        $string = $createForm;

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($string);
        $contentLayer = $objLayer->show();
        $str = $contentLayer;
        return $str;
    }

    /**
    * Method to add wiki pages
    *
    * @access public
    * @param string $name: The name of the page if from a link
    * @return string $str: The output string
    */
    public function showAddPage($name = NULL)
    {
        // add  javascript
        $objMk = $this->getObject('markitup', 'markitup');
        $objMk->setType('chiki');
        $headerParams = $objMk->show('id', 'input_wikiContent');
        $this->appendArrayVar('headerParams', $headerParams);
        $headerParams = $this->getJavascriptFile('wiki.js', 'wiki');
        $this->appendArrayVar('headerParams', $headerParams);

        // text elements
        $addLabel = $this->objLanguage->languageText('mod_wiki_addarticle', 'wiki');
        $titleLabel = $this->objLanguage->languageText('mod_wiki_add', 'wiki');
        $pageLabel = $this->objLanguage->languageText('mod_wiki_pagename', 'wiki');
        $pageErrorLabel = $this->objLanguage->languageText('mod_wiki_pageerror', 'wiki');
        $nameErrorLabel = $this->objLanguage->languageText('mod_wiki_nameerror', 'wiki');
        $summaryLabel = $this->objLanguage->languageText('mod_wiki_pagesummary', 'wiki');
        $summaryErrorLabel = $this->objLanguage->languageText('mod_wiki_summaryerror', 'wiki');
        $contentLabel = $this->objLanguage->languageText('mod_wiki_pagecontent', 'wiki');
        $contentErrorLabel = $this->objLanguage->languageText('mod_wiki_contenterror', 'wiki');
        $createLabel = $this->objLanguage->languageText('mod_wiki_create', 'wiki');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');
/*
        $refreshLabel = $this->objLanguage->languageText('word_refresh');
        $refreshTitleLabel = $this->objLanguage->languageText('mod_wiki_refreshtitle', 'wiki');
*/
        $previewLabel = $this->objLanguage->languageText('word_preview');
        $noPreviewLabel = $this->objLanguage->languageText('mod_wiki_nopreview', 'wiki');
        $loadingLabel = $this->objLanguage->languageText('mod_wiki_loading', 'wiki');

        // add to watchlist
        $watchList = $this->showAddWatchlist();

        if(empty($name)){
            // page name
            $objHeader = new htmlheading();
            $objHeader->str = $pageLabel;
            $objHeader->type = 4;
            $heading = $objHeader->show();

            // page name textinput
            $objInput = new textinput('name', $name, '', '96');
            $objInput->extra = 'onblur="javascript:validateName(this);"';
            $nameInput = $objInput->show();
        }else{
            $pageTitle = $this->objWiki->renderTitle($name);
            // page name
            $objHeader = new htmlheading();
            $objHeader->str = $pageTitle;
            $objHeader->type = 1;
            $heading = $objHeader->show();

            // page name textinput
            $objInput = new textinput('name', $name, 'hidden', '96');
            $nameInput = $objInput->show();
        }

        // page name error layer
        $objLayer = new layer();
        $objLayer->id = 'errorDiv';
        $errorLayer = $objLayer->show();

        // page name layer
        $objLayer = new layer();
        $objLayer->addToStr($heading.$nameInput.$errorLayer);
        $pageLayer = $objLayer->show();

        // summary
        $objHeader = new htmlheading();
        $objHeader->str = $summaryLabel;
        $objHeader->type = 4;
        $heading = $objHeader->show();

        // summary textarea
        $objText = new textarea('summary', '', '4', '70');
        $summaryText = $objText->show();

        $objInput = new textinput('choice', 'no', 'hidden', '');
        $hiddenInput = $objInput->show();

        // summary layer
        $objLayer = new layer();
        $objLayer->addToStr($heading.$summaryText.$hiddenInput);
        $summaryLayer = $objLayer->show();

        // content
        $objHeader = new htmlheading();
        $objHeader->str = $contentLabel;
        $objHeader->type = 4;
        $heading = $objHeader->show();

        // content textarea
        $objText = new textarea('wikiContent', '', '25', '70');
        $contentText = $objText->show();

        // content layer
        $objLayer = new layer();
        $objLayer->addToStr($heading.$contentText);
        $contentLayer = $objLayer->show();

        // create button
        $objButton = new button('create', $createLabel);
        $objButton->extra = 'onclick="javascript: validateCreatePage(\''.$pageErrorLabel.'\', \''.$summaryErrorLabel.'\', \''.$contentErrorLabel.'\');"';
        $createButton = $objButton->show();

        // create button
        $objButton = new button('cancel', $cancelLabel);
        $objButton->extra = 'onclick="$(\'form_hidden\').submit();"';
        $cancelButton = $objButton->show();

        // button layer
        $objLayer = new layer();
        $objLayer->addToStr($createButton.'&#160;'.$cancelButton);
        $buttonLayer = $objLayer->show();

        // form
        $objForm = new form('create', $this->uri(array(
            'action' => 'create_page',
        ), 'wiki'));
        $objForm->addToForm($watchList);
        $objForm->addToForm($pageLayer);
        $objForm->addToForm($summaryLayer);
        $objForm->addToForm($contentLayer);
        $objForm->addToForm($buttonLayer);
        $createForm = $objForm->show();

        $objForm = new form('hidden', $this->uri(array(), 'wiki'));
        $hiddenForm = $objForm->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($createForm.$hiddenForm);
        $createLayer = $objLayer->show();

        // add page tab
        $addTab = array(
            'name' => $addLabel,
            'content' => $createLayer,
        );
        // preview iframe
        $objIframe = new iframe();
        $objIframe->id = 'submitIframe';
        $objIframe->frameborder = '0';
        $objIframe->width = '100%';
        $objIframe->src = $this->uri(array(
            'action' => 'preview_iframe'
        ), 'wiki');
        $submitIframe = $objIframe->show();

        $previewTab = array(
            'name' => $previewLabel,
            'content' => $submitIframe,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'addTab';
        $this->objTab->addTab($addTab);
        $this->objTab->addTab($previewTab);
        $str = $this->objTab->show();
        $body = 'tabClickEvents("");';
        $this->appendArrayVar('bodyOnLoad', $body);

        return $str;
    }

    /**
    * Method to display the preview content area
    *
    * @access public
    * @param string $name: The name of the page
    * @param string $content: The page content
    * @return string $str: The output string
    **/
    public function showPreview($name, $content)
    {
        // text eleements
        $pageLabel = $this->objLanguage->languageText('mod_wiki_pagename', 'wiki');
        $noPreviewLabel = $this->objLanguage->languageText('mod_wiki_nopreview', 'wiki');

        if(!empty($name) || !empty($content)){
            if(!empty($name)){
                $pageName = $this->objWiki->renderTitle($name);
            }else{
                $pageName = $pageLabel;
            }
            $objHeader = new htmlheading();
            $objHeader->str = $pageName;
            $objHeader->type = 1;
            $heading = $objHeader->show();
            $str = $heading;

            $text = $this->objWiki->transform($content);
            $str .= $this->objWash->parseText($text);
        }else{
            $str = '<ul><li>'.$noPreviewLabel.'</li></ul>';
        }

         echo $str;
    }

    /**
    * Method create a list all wiki pages
    *
    * @access private
    * @return string $str: The output string
    **/
    private function _showSummaries()
    {
        // text elements
        $pageLabel = $this->objLanguage->languageText('mod_wiki_pagename', 'wiki');
        $summaryLabel = $this->objLanguage->languageText('word_summary');
        $noRecordsLabel = $this->objLanguage->languageText('mod_wiki_norecords', 'wiki');
        $pageTitleLabel = $this->objLanguage->languageText('mod_wiki_pagetitle', 'wiki');

        // get data
        $data = $this->objDbwiki->getAllCurrentPages();

        // create display table
        $objTable = new htmltable();
        $objTable->id = 'summaryList';
        $objTable->css_class = 'sorttable';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        $objTable->addCell($pageLabel, '25%', '', '', 'heading', '');
        $objTable->addCell($summaryLabel, '', '', '', 'heading', '');
        $objTable->endRow();

        if(empty($data)){
            // no records
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
            $objTable->endRow();
        }else{
            // loop through data and display each record in the table
            foreach($data as $line){
                $name = $line['page_name'];
                $pageTitle = $this->objWiki->renderTitle($name);
                $summary = $this->objWiki->transform($line['page_summary']);

                // page name link
                $objLink = new link($this->uri(array(
                    'action' => 'view_page',
                    'name' => $name,
                ), 'wiki'));
                $objLink->link = $pageTitle;
                $objLink->title = $pageTitleLabel;
                $pageLink = $objLink->show();

                // data display
                $objTable->startRow();
                $objTable->addCell($pageLink, '', '', '', '', '');
                $objTable->addCell($summary, '', '', '', '', '');
                $objTable->endRow();
            }
        }
        $pageTable = $objTable->show();
        $str = $pageTable;

        return $str;
    }

    /**
    * Method create a list all wiki pages
    *
    * @access public
    * @return string $str: The output string
    **/
    public function showAllPages()
    {
        // add javascript to sort table
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        // text elements
        $titleLabel = $this->objLanguage->languageText('mod_wiki_view', 'wiki');
        $pageLabel = $this->objLanguage->languageText('mod_wiki_pagename', 'wiki');
        $authorLabel = $this->objLanguage->languageText('word_author');
        $dateLabel = $this->objLanguage->languageText('mod_wiki_datecreated', 'wiki');
        $noRecordsLabel = $this->objLanguage->languageText('mod_wiki_norecords', 'wiki');
        $editLabel = $this->objLanguage->languageText('word_edit');
        $editTitleLabel = $this->objLanguage->languageText('mod_wiki_edittitle', 'wiki');
        $pageTitleLabel = $this->objLanguage->languageText('mod_wiki_pagetitle', 'wiki');
        $authorTitleLabel = $this->objLanguage->languageText('mod_wiki_authortitle', 'wiki');
        $listLabel = $this->objLanguage->languageText('mod_wiki_listarticles', 'wiki');
        $summaryLabel = $this->objLanguage->languageText('mod_wiki_listsummaries', 'wiki');

        // get data
        $data = $this->objDbwiki->getAllCurrentPages();

        // create display table
        $objTable = new htmltable();
        $objTable->id = 'pageList';
        $objTable->css_class = 'sorttable';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        $objTable->addCell($pageLabel, '', '', '', 'heading', '');
        $objTable->addCell($authorLabel, '', '', '', 'heading', '');
        $objTable->addCell($dateLabel, '', '', 'center', 'heading', '');
        $objTable->endRow();

        if(empty($data)){
            // no records
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
            $objTable->endRow();
        }else{
            // loop through data and display each record in the table
            foreach($data as $line){
                $name = $line['page_name'];
                $pageTitle = $this->objWiki->renderTitle($name);
                $authorId = $line['page_author_id'];
                $author = $this->objUser->fullname($authorId);
                $date = $this->objDate->formatDate($line['date_created']);
                $type = $line['main_page'];

                // page name link
                $objLink = new link($this->uri(array(
                    'action' => 'view_page',
                    'name' => $name,
                ), 'wiki'));
                $objLink->link = $pageTitle;
                $objLink->title = $pageTitleLabel;
                $pageLink = $objLink->show();

                // author link
                $objLink = new link($this->uri(array(
                    'action' => 'view_authors',
                    'author' => $authorId,
                ), 'wiki'));
                $objLink->link = $author;
                $objLink->title = $authorTitleLabel;
                $authorLink = $objLink->show();

                // data display
                $objTable->startRow();
                $objTable->addCell($pageLink, '', '', '', '', '');
                $objTable->addCell($authorLink, '30%', '', '', '', '');
                $objTable->addCell($date, '20%', '', 'center', '', '');
                $objTable->endRow();
            }
        }
        $pageTable = $objTable->show();
        $list = $pageTable;

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($list);
        $contentLayer = $objLayer->show();

        // add page tab
        $pageTab = array(
            'name' => $listLabel,
            'content' => $contentLayer,
        );

        // list summaries
        $list = $this->_showSummaries();

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($list);
        $contentLayer = $objLayer->show();

        $string = $contentLayer;

        // add page tab
        $summaryTab = array(
            'name' => $summaryLabel,
            'content' => $string,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->addTab($pageTab);
        $this->objTab->addTab($summaryTab);
        $this->objTab->useCookie = 'false';
        $str = $this->objTab->show();

        return $str;
    }

    /**
    * Method to create the formatting rules popup
    *
    * @access public
    * @return string $str: The output string
    **/
    public function showFormattingRules()
    {
         // text elements
         $formatLabel = $this->objLanguage->languageText('mod_wiki_format', 'wiki');

         // formatting rule string
         $formattingRules = '[[toc]]
----
+++ General Notes
The markup described on this page is for the default {{Text_Wiki}} rules;
it is a combination of the [http://tavi.sourceforge.net WikkTikkiTavi]
and [http://develnet.org/ coWiki] markup styles.

All text is entered as plain text, and will be converted to HTML entities as
necessary.  This means that {{<}}, {{>}}, {{&}}, and so on are converted for
you (except in special situations where the characters are Wiki markup;
Text_Wiki is generally smart enough to know when to convert and when not to).

Just hit return twice to make a paragraph break.  If you want
to keep the same logical line but have to split it across
two physical lines (such as when your editor only shows a certain number
of characters per line), end the line with a backslash {{\}} and hit
return once:

<code>
This will cause the two lines to be joined on display, and the \
backslash will not show.
</code>

This will cause the two lines to be joined on display, and the \
backslash will not show.

(If you end a line with a backslash and a tab or space,
it will not be joined with the next line, and the backslash \
will be printed.)
----
+++ Inline Formatting
|| {{``//emphasis text//``}}                 || //emphasis text// ||
|| {{``**strong text**``}}                   || **strong text** ||
|| {{``//**emphasis and strong**//``}}       || //**emphasis and strong**// ||
|| {{``{{teletype text}}``}}                    || {{teletype text}} ||
|| {{``@@--- delete text +++ insert text @@``}} || @@--- delete text +++ insert text @@ ||
|| {{``@@--- delete only @@``}}                 || @@--- delete only @@ ||
|| {{``@@+++ insert only @@``}}                 || @@+++ insert only @@ ||
----
+++ Literal Text
If you dont want Text_Wiki to parse some text, enclose it in two backticks (not single-quotes).
<code>
This //text// gets **parsed**.
``This //text// does not get **parsed**.``
</code>
This //text// gets **parsed**.
``This //text// does not get **parsed**.``
----
+++ Headings
You can make various levels of heading by putting
plus-signs before the text (all on its own line):
<code>
+++ Level 3 Heading
++++ Level 4 Heading
+++++ Level 5 Heading
++++++ Level 6 Heading
</code>
+++ Level 3 Heading
++++ Level 4 Heading
+++++ Level 5 Heading
++++++ Level 6 Heading
----
+++ Table of Contents
To create a list of every heading, with a link to that heading, put a table of contents tag on its own line.
<code>
[[toc]]
</code>
----
+++ Horizontal Rules
Use four dashes ({{``----``}}) to create a horizontal rule.
----
+++ Lists
++++ Bullet Lists
You can create bullet lists by starting a paragraph with one or more asterisks.
<code>
* Bullet one
 * Sub-bullet
</code>
* Bullet one
 * Sub-bullet
++++ Numbered Lists
Similarly, you can create numbered lists by starting a paragraph
with one or more hashes.
<code>
# Numero uno
# Number two
 # Sub-item
</code>
# Numero uno
# Number two
 # Sub-item
++++ Mixing Bullet and Number List Items
You can mix and match bullet and number lists:
<code>
# Number one
 * Bullet
 * Bullet
# Number two
 * Bullet
 * Bullet
   * Sub-bullet
 # Sub-sub-number
 # Sub-sub-number
# Number three
 * Bullet
 * Bullet
</code>
# Number one
 * Bullet
 * Bullet
# Number two
 * Bullet
 * Bullet
  * Sub-bullet
 # Sub-sub-number
 # Sub-sub-number
# Number three
 * Bullet
 * Bullet
++++ Definition Lists
You can create a definition (description) list with the following syntax:
<code>
: Item 1 : Something
: Item 2 : Something else
</code>
: Item 1 : Something
: Item 2 : Something else
----
+++ Block Quotes
You can mark a blockquote by starting a line with one or more >
characters, followed by a space and the text to be quoted.
<code>
This is normal text here.
> Indent me! The quick brown fox jumps over the lazy dog.
Now this the time for all good men to come to the aid of
their country. Notice how we can continue the block-quote
in the same paragraph by using a backslash at the end of the line.
>
> Another block, leading to...
>> Second level of indenting.
Back to normal text.
</code>

This is normal text here.
> Indent me! The quick brown fox jumps over the lazy dog. Now this the time for all good men to come to the aid of their country. Notice how we can continue the block-quote \
in the same paragraph by using a backslash at the end of the line.
>
> Another block, leading to...
>> Second level of indenting.  This second is indented even more than \
the previous one.
Back to normal text.
----
+++ Links and Images
++++ Wiki Links
SmashWordsTogether to create a page link.
You can force a WikiPage name not to be clickable by putting
an exclamation mark in front of it.
<code>
WikiPage !WikiPage
</code>
WikiPage !WikiPage

You can create a described or labeled link to a wiki page by putting the page name in brackets, followed by some text.
<code>
[WikiPage Descriptive text for the link.]
</code>
[WikiPage Descriptive text for the link.]
> **Note:** existing wiki pages must be in the [RuleWikilink wikilink] {{pages}} configuration,  and the [RuleWikilink wikilink] {{view_url}} configuration value must be set for the linking to work.
++++ Interwiki Links
Interwiki links are links to pages on other Wiki sites.
Type the {{``SiteName:PageName``}} like this:
* MeatBall:RecentChanges
* Advogato:proj/WikkiTikkiTavi
* Wiki:WorseIsBetter
> **Note:** the interwiki site must be in the [RuleInterwiki interwiki] {{sites}} configuration array.
++++ URLs
Create a remote link simply by typing its URL: http://ciaweb.net.
If you like, enclose it in brackets to create a numbered reference and avoid cluttering the page; {{``[http://ciaweb.net/free/]``}} becomes [http://ciaweb.net/free/].
Or you can have a described-reference instead of a numbered reference:
<code>
[http://pear.php.net PEAR]
</code>
[http://pear.php.net PEAR]
++++ Images
You can put a picture in a page by typing the URL to the picture
(it must end in gif, jpg, or png).
<code>
http://c2.com/sig/wiki.gif
</code>
http://c2.com/sig/wiki.gif
You can use the described-reference URL markup to give the image an ALT tag:
<code>
[http://phpsavant.com/etc/fester.jpg Fester]
</code>
[http://phpsavant.com/etc/fester.jpg Fester]
----
+++ Code Blocks
Create code blocks by using {{<code>...</code>}} tags (each on its own line).
<code>
This is an example code block!
</code>
To create PHP blocks that get automatically colorized when you use PHP tags, simply surround the code with {{<code type=php>...</code>}} tags (the tags themselves should each be on their own lines, and no need for the {{<?php ... ?>}} tags).
<code>
 <code type="php">
 // Set up the wiki options
 $options = array();
 $options[view_url] = index.php?page=;
 // load the text for the requested page
 $text = implode(\, file($page . .wiki.txt));
 // create a Wiki objext with the loaded options
 $wiki = new Text_Wiki($options);
 // transform the wiki text.
 echo $wiki->transform($text);
 </code>
</code>
<code type="php">
// Set up the wiki options
$options = array();
$options[view_url] = index.php?page=;
// load the text for the requested page
$text = implode( file($page . .wiki.txt));
// create a Wiki object with the loaded options
$wiki = new Text_Wiki($options);
// transform the wiki text.
echo $wiki->transform($text);
</code>
----
+++ Tables
You can create tables using pairs of vertical bars:
<code>
||~ Heading one ||~ Heading two ||
|| cell one || cell two ||
|||| big long line ||
|| cell four || cell five ||
|| cell six || heres a very long cell ||
</code>
||~ Heading one ||~ Heading two ||
|| cell one || cell two ||
|||| big long line ||
|| cell four || cell five ||
|| cell six || heres a very long cell ||
<code>
|| lines must start and end || with double vertical bars || nothing ||
|| cells are separated by || double vertical bars || nothing ||
|||| you can span multiple columns by || starting each cell ||
|| with extra cell |||| separators ||
|||||| but perhaps an example is the easiest way to see ||
</code>
|| lines must start and end || with double vertical bars || nothing ||
|| cells are separated by || double vertical bars || nothing ||
|||| you can span multiple columns by || starting each cell ||
|| with extra cell |||| separators ||
|||||| but perhaps an example is the easiest way to see ||';

        // parse formatting string
        $string = $this->objWiki->transform($formattingRules);

        // create feature box
        $formatFeature = $this->objFeature->show($formatLabel, $string);

        // main display layer
        $objLayer = new layer();
        $objLayer->addToStr($formatFeature);
        $objLayer->padding = '10px';
        $str = $objLayer->show();
        return $str;
    }

    /**
    * Method to display the search results content area
    *
    * @access public
    * @param string $data: The seach data that was returned
    * @return string $str: The output string
    **/
    public function showSearch($data)
    {
        // text elements
        $listLabel = $this->objLanguage->languageText('mod_wiki_searchlist', 'wiki');
        $pageLabel = $this->objLanguage->languageText('mod_wiki_pagename', 'wiki');
        $authorLabel = $this->objLanguage->languageText('mod_wiki_author', 'wiki');
        $dateLabel = $this->objLanguage->languageText('mod_wiki_datecreated', 'wiki');
        $noRecordsLabel = $this->objLanguage->languageText('mod_wiki_norecords', 'wiki');
        $pageTitleLabel = $this->objLanguage->languageText('mod_wiki_pagetitle', 'wiki');
        $authorTitleLabel = $this->objLanguage->languageText('mod_wiki_authortitle', 'wiki');

        // create display table
        $objTable = new htmltable();
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        $objTable->addCell('<b>'.$pageLabel.'</b>', '', '', '', 'heading', '');
        $objTable->addCell('<b>'.$authorLabel.'</b>', '', '', '', 'heading', '');
        $objTable->addCell('<b>'.$dateLabel.'</b>', '', '', 'center', 'heading', '');
        $objTable->endRow();

        if(empty($data)){
            // no records
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
            $objTable->endRow();
        }else{
            // loop through data and display each record in the table
            $i = 0;
            foreach($data as $line){
                $class = (($i++ % 2) == 0)?'even':'odd';
                $name = $line['page_name'];
                $pageTitle = $this->objWiki->renderTitle($name);
                $authorId = $line['page_author_id'];
                $author = $this->objUser->fullname($authorId);
                $date = $this->objDate->formatDate($line['date_created']);
                $type = $line['main_page'];
                if(isset($line['page_lock'])) {
                    $locked = $line['page_lock'];
                }
                if(isset($line['page_locker_id'])) {
                    $lockerId = $line['page_locker_id'];
                }

                // page name link
                $objLink = new link($this->uri(array(
                    'action' => 'view_page',
                    'name' => $name,
                ), 'wiki'));
                $objLink->link = $pageTitle;
                $objLink->title = $pageTitleLabel;
                $pageLink = $objLink->show();

                // author link
                $objLink = new link($this->uri(array(
                    'action' => 'view_authors',
                    'author' => $authorId,
                ), 'wiki'));
                $objLink->link = $author;
                $objLink->title = $authorTitleLabel;
                $authorLink = $objLink->show();

                // data display
                $objTable->startRow();
                $objTable->addCell($pageLink, '', '', '', $class, '');
                $objTable->addCell($authorLink, '30%', '', '', $class, '');
                $objTable->addCell($date, '20%', '', 'center', $class, '');
                $objTable->endRow();
            }
        }
        $pageTable = $objTable->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($pageTable);
        $contentLayer = $objLayer->show();

        $string = $contentLayer;

        // add page tab
        $tabArray = array(
            'name' => $listLabel,
            'content' => $string,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->addTab($tabArray);
        $this->objTab->useCookie = 'false';
        $str = $this->objTab->show();

        return $str;
    }

    /**
    * Method to display the author area
    *
    * @access public
    * @param string $author: The id of the author to show
    * @return string $str: The output string
    **/
    public function showAuthors($author = NULL)
    {
        // add javascript to sort table
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        // text elements
        $listLabel = $this->objLanguage->languageText('mod_wiki_authorlist', 'wiki');
        $detailsLabel = $this->objLanguage->languageText('mod_wiki_author', 'wiki');
        $authorsLabel = $this->objLanguage->languageText('word_authors');
        $authorTitleLabel = $this->objLanguage->languageText('mod_wiki_authortitle', 'wiki');
        $numberLabel = $this->objLanguage->languageText('word_number');
        $noRecordsLabel = $this->objLanguage->languageText('mod_wiki_norecords', 'wiki');
        $articleLabel = $this->objLanguage->languageText('word_article');
        $pageTitleLabel = $this->objLanguage->languageText('mod_wiki_pagetitle', 'wiki');

        // get data
        if(empty($author)){
            $data = $this->objDbwiki->getAuthors();
            // create display table
            $objTable = new htmltable();
            $objTable->id = 'authorList';
            $objTable->css_class = 'sorttable';
            $objTable->cellpadding = '2';
            $objTable->border = '1';
            $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
            $objTable->startRow();
            $objTable->addCell($authorsLabel, '', '', '', 'heading', '');
            $objTable->addCell($numberLabel, '10%', '', 'center', 'heading', '');
            $objTable->endRow();

            if(empty($data)){
                // no records
                $objTable->startRow();
                $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"'   );
                $objTable->endRow();
            }else{
                // loop through data and display each record in the table
                foreach($data as $line){
                    $authorId = $line['page_author_id'];
                    $author = $this->objUser->fullname($authorId);
                    $number = $line['cnt'];

                    // author link
                    $objLink = new link($this->uri(array(
                        'action' => 'view_authors',
                        'author' => $authorId,
                    ), 'wiki'));
                    $objLink->link = $author;
                    $objLink->title = $authorTitleLabel;
                    $authorLink = $objLink->show();

                    // data display
                    $objTable->startRow();
                    $objTable->addCell($authorLink, '', '', '', '', '');
                    $objTable->addCell($number, '', '', 'center', '', '');
                    $objTable->endRow();
                }
            }
            $pageTable = $objTable->show();

            $objLayer = new layer();
            $objLayer->cssClass = 'featurebox';
            $objLayer->addToStr($pageTable);
            $contentLayer = $objLayer->show();

            $string = $contentLayer;

            // add page tab
            $tabArray = array(
                'name' => $listLabel,
                'content' => $string,
            );
        }else{
            $data = $this->objDbwiki->getAuthorArticles($author);
            $user = $this->objUserAdmin->getUserDetails($this->objUser->PKId($author));
            $this->objBizCard->setUserArray($user);
            $bizCard = $this->objBizCard->show();

            // create display table
            $objTable = new htmltable();
            $objTable->id = 'articleList';
            $objTable->css_class = 'sorttable';
            $objTable->cellpadding = '2';
            $objTable->border = '1';
            $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
            $objTable->startRow();
            $objTable->addCell($articleLabel, '', '', '', 'heading', '');
            $objTable->endRow();

            if(empty($data)){
                // no records
                $objTable->startRow();
                $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"'   );
                $objTable->endRow();
            }else{
                // loop through data and display each record in the table
                foreach($data as $line){
                    $name = $line['page_name'];
                    $pageTitle = $this->objWiki->renderTitle($name);

                    // page name link
                    $objLink = new link($this->uri(array(
                    'action' => 'view_page',
                        'name' => $name,
                    ), 'wiki'));
                    $objLink->link = $pageTitle;
                    $objLink->title = $pageTitleLabel;
                    $pageLink = $objLink->show();

                    // data display
                    $objTable->startRow();
                    $objTable->addCell($pageLink, '', '', '', '', '');
                    $objTable->endRow();
                }
            }
            $pageTable = $objTable->show();

            $objLayer = new layer();
            $objLayer->cssClass = 'featurebox';
            $objLayer->addToStr($bizCard.'<br />'.$pageTable);
            $contentLayer = $objLayer->show();

            // add page tab
            $tabArray = array(
                'name' => $detailsLabel,
                'content' => $contentLayer,
            );
        }
        //display tabs
        $this->objTab->init();
        $this->objTab->addTab($tabArray);
        $this->objTab->useCookie = 'false';
        $str = $this->objTab->show();

        return $str.'<br />';
    }

    /**
    * Method to create the page name error message
    *
    * @access public
    * @param string $name: The name of the page to validate
    * @return string $str: The output string
    **/
    public function showValidateName($name)
    {
        $camelcaseLabel = $this->objLanguage->languageText('mod_wiki_camelcase', 'wiki');
        $lettersonlyLabel = $this->objLanguage->languageText('mod_wiki_lettersonly', 'wiki');
        $existsLabel = $this->objLanguage->languageText('mod_wiki_exists', 'wiki');

        $errors = array();

        // check name
        $data = $this->objDbwiki->getPage($name);
        if(!empty($data)){
            $errors[] = $existsLabel;
        }

        // check alpha only
        if(!preg_match('/\A[a-z]*\z[a-z]*/i', $name)){
            $errors[] = $lettersonlyLabel;
        }

        // check camel case first
//        if(!ereg('^([A-Z]([a-z]+)){2,}$', $name)){
//            $errors[] = $camelcaseLabel;
//        }

        if(ereg('^([A-Z]){2,}', $name) || !ereg('^([A-Z]([a-z])*){2,}', $name)){
            $errors[] = $camelcaseLabel;
        }

        if(!empty($errors)){
            $string = '<ul>';
            foreach($errors as $error){
                $string .= '<li><font class="warning">'.$error.'</font></li>';
            }
            $string .= '</ul>';
        }else{
            $string = '';
        }

        echo $string;
    }

    /**
    * Method to display the main wiki content area
    *
    * @access public
    * @param string $name: The name of the page to show
    * @return string $str: The output string
    **/
    public function showDeletedPage($name)
    {
        // add  javascript
        $headerParams = $this->getJavascriptFile('wiki.js', 'wiki');
        $this->appendArrayVar('headerParams', $headerParams);

        // get data
        $data = $this->objDbwiki->getPage($name);

        $pageId = $data['id'];
        $name = $data['page_name'];
        $pageTitle = $this->objWiki->renderTitle($name);
        $wikiText = $this->objWiki->transform($data['page_content']);
        $array = array(
            'date' => $this->objDate->formatDate($data['date_created']),
        );
        $modifiedLabel = $this->objLanguage->code2Txt('mod_wiki_modified', 'wiki', $array);

        // text elements
        $articleLabel = $this->objLanguage->languageText('mod_wiki_deletedarticle', 'wiki');
        $content1Label = $this->objLanguage->languageText('mod_wiki_deletedpage_1', 'wiki');
        $content2Label = $this->objLanguage->languageText('mod_wiki_deletedpage_2', 'wiki');
        $content3Label = $this->objLanguage->languageText('mod_wiki_deletedpage_3', 'wiki');
        $content4Label = $this->objLanguage->languageText('mod_wiki_deletedpage_4', 'wiki');
        $deletedLabel = $this->objLanguage->languageText('word_deleted');
        $historyLabel = $this->objLanguage->languageText('word_history');
        $versionTitle = $pageTitle.':&#160;'.$deletedLabel;

        $objHeader = new htmlheading();
        $objHeader->str = $versionTitle;
        $objHeader->type = 1;
        $heading = $objHeader->show();
        $contents = $heading;

        $string = $content1Label;
        $string .= '<ul>';
        $string .= '<li>'.$content2Label.'</li>';
        $string .= '<li>'.$content3Label.'</li>';
        $string .= '<li>'.$content4Label.'</li>';
        $string .= '</ul>';

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($contents.$string);
        $contentLayer = $objLayer->show();

        // wiki page tab
        $mainTab = array(
            'name' => $articleLabel,
            'content' => $contentLayer,
        );

        // page history
        $history = $this->_showPageHistory($name);

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($history);
        $contentLayer = $objLayer->show();

        // page history tab
        $historyTab = array(
            'name' => $historyLabel,
            'content' => $contentLayer,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'mainTab';
        $this->objTab->addTab($mainTab);
        if($this->isAdmin){
            $this->objTab->addTab($historyTab);
        }
        $this->objTab->useCookie = 'false';
        $str = $this->objTab->show();

        return $str.'<br />';
    }

    /**
    * Method to display the locked page message
    *
    * @access public
    * @param string $id: The id of the page locked
    * @param var $lockedForEdit: An indicator to show if the page is locked for edit
    * @return string $str: The output string
    */
    public function showLockedMessage($id, $lockedForEdit = FALSE)
    {
        // get data
        $data = $this->objDbwiki->getPageById($id);
        $pageTitle = $this->objWiki->renderTitle($data['page_name']);

        // text elements
        $lockedLabel = $this->objLanguage->languageText('mod_wiki_locked', 'wiki');
        $retryLabel = $this->objLanguage->languageText('mod_wiki_retry', 'wiki');

        $objHeader = new htmlheading();
        $objHeader->str = $pageTitle;
        $objHeader->type = 1;
        $heading = $objHeader->show();
        $str = $heading;

        $str .= '<ul>';
        $str .= '<li>'.$lockedLabel.'</li>';
        $str .= '<li>'.$retryLabel.'</li>';
        $str .= '</ul>';

        if($lockedForEdit === 'keeplocked'){
            $objInput = new textinput('locked', 'locked', 'hidden');
            $str = $objInput->show();
        }elseif($lockedForEdit){
            $objInput = new textinput('locked', 'locked', 'hidden');
            $str = $objInput->show();
        }else{
            $objInput = new textinput('locked', 'unlocked', 'hidden');
            $str .= $objInput->show();
        }
        echo $str;
    }

    /**
    * method to display the rating div
    *
    * @access public
    * @param string $name: The name of the page
    * @param boolean $ajax: TRUE if the function is called via ajax | FALSE if not
    * @return string $str: THe output string
    */
    public function showRating($name, $ajax = FALSE)
    {
        // get data
        $data = $this->objDbwiki->getRating($name);
        $wasRated = $this->objDbwiki->wasRated($name);

        // text elements
        $ratedLabel = $this->objLanguage->languageText('word_rated');
        $badLabel = $this->objLanguage->languageText('mod_wiki_bad', 'wiki');
        $goodLabel = $this->objLanguage->languageText('mod_wiki_good', 'wiki');
        $notRatedLabel = $this->objLanguage->languageText('mod_wiki_notrated', 'wiki');
        $array = array(
            'num' => $data['votes'],
        );
        $votersLabel = $this->objLanguage->code2Txt('mod_wiki_voters', 'wiki', $array);
        $array = array(
            'num' => $data['rating'],
            'voters' => $data['votes'],
        );
        $countLabel = $this->objLanguage->code2Txt('mod_wiki_rated', 'wiki', $array);

        // rating radio
        $str = '';
        if(!$wasRated && $this->isLoggedIn){
            $objRadio = new radio('rating');
            for($i = 1; $i <= 5; $i++){
                $objRadio->addOption($i, '&#160;');
                $objRadio->extra = 'style="vertical-align: middle;" onclick="javascript:addRating(this.value);"';
            }
            $ratingRadio = $objRadio->show();

            $str .= '<b>'.$badLabel.'</b>';
            $str .= '&#160;&#160;'.$ratingRadio.'&#160;&#160;';
            $str .= '<b>'.$goodLabel.'</b><br />';
        }

        $str .= $ratedLabel.'&#160;&#160;';
        if($data['votes'] == 0){
            for($i = 1; $i <= 5; $i++){
                $this->objIcon->setIcon('grey_bullet');
                $this->objIcon->extra = 'style="vertical-align: middle;"';
                $this->objIcon->title = $notRatedLabel;
                $str .= $this->objIcon->show();
            }
        }else{
            if($data['rating'] == 0){
                for($i = 1; $i <= 5; $i++){
                    $this->objIcon->setIcon('grey_bullet');
                    $this->objIcon->extra = 'style="vertical-align: middle;"';
                    $this->objIcon->title = $countLabel;
                    $str .= $this->objIcon->show();
                }
            }else{
                for($i = 1; $i <= $data['rating']; $i++){
                    $this->objIcon->setIcon('green_bullet');
                    $this->objIcon->extra = 'style="vertical-align: middle;"';
                    $this->objIcon->title = $countLabel;
                    $str .= $this->objIcon->show();
                }
                for($i = 1; $i <= (5 - $data['rating']); $i++){
                    $this->objIcon->setIcon('grey_bullet');
                    $this->objIcon->extra = 'style="vertical-align: middle;"';
                    $this->objIcon->title = $countLabel;
                    $str .= $this->objIcon->show();
                }
            }
        }
        $str .= '&#160;&#160;'.$votersLabel;

        if($ajax){
            echo $str;
        }else{
            return $str;
        }
    }

    /**
    * Method to display page ranking
    *
    * @access public
    * @return string $str: The output string
    */
    public function showRanking()
    {
        // add javascript to sort table
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        // get data
        $data = $this->objDbwiki->getRanking();

        // text elements
        $pageLabel = $this->objLanguage->languageText('mod_wiki_pagename', 'wiki');
        $rankLabel = $this->objLanguage->languageText('word_rank');
        $rankingLabel = $this->objLanguage->languageText('word_ranking');
        $ratingLabel = $this->objLanguage->languageText('word_rating');
        $noRecordsLabel = $this->objLanguage->languageText('mod_wiki_norecords', 'wiki');
        $pageTitleLabel = $this->objLanguage->languageText('mod_wiki_pagetitle', 'wiki');
        // create display table
        $objTable = new htmltable();
        $objTable->id = 'rankList';
        $objTable->css_class = 'sorttable';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        $objTable->addCell($rankLabel, '10%', '', 'center', 'heading', '');
        $objTable->addCell($pageLabel, '', '', '', 'heading', '');
        $objTable->addCell($ratingLabel, '10%', '', 'center', 'heading', '');
        $objTable->endRow();
        if(empty($data)){
            // no records
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
            $objTable->endRow();
        }else{
            // loop through data and display each record in the table
            foreach($data as $key => $line){
                $name = $line['page_name'];
                $pageTitle = $this->objWiki->renderTitle($name);
                $rank = ($key + 1);
                $rating = ceil($line['tot'] / $line['cnt']);

                $objLink = new link($this->uri(array(
                    'action' => 'view_page',
                    'name' => $name,
                ), 'wiki'));
                $objLink->link = $pageTitle;
                $objLink->title = $pageTitleLabel;
                $pageLink = $objLink->show();

                $array = array(
                    'num' => $rating,
                    'voters' => $line['cnt'],
                );
                $countLabel = $this->objLanguage->code2Txt('mod_wiki_rated', 'wiki', $array);
                $str = '';
                for($i = 1; $i <= $rating; $i++){
                    $this->objIcon->setIcon('green_bullet');
                    $this->objIcon->extra = 'style="vertical-align: middle;"';
                    $this->objIcon->title = $countLabel;
                    $str .= $this->objIcon->show();
                }
                for($i = 1; $i <= (5 - $rating); $i++){
                    $this->objIcon->setIcon('grey_bullet');
                    $this->objIcon->extra = 'style="vertical-align: middle;"';
                    $this->objIcon->title = $countLabel;
                    $str .= $this->objIcon->show();
                }

                // data display
                $objTable->startRow();
                $objTable->addCell($rank, '', '', 'center', '', '');
                $objTable->addCell($pageLink, '', '', '', '', '');
                $objTable->addCell($str, '', '', 'center', '', '');
                $objTable->endRow();
            }
        }
        $pageTable = $objTable->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($pageTable);
        $contentLayer = $objLayer->show();

        // add page tab
        $tabArray = array(
            'name' => $rankingLabel,
            'content' => $contentLayer,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->addTab($tabArray);
        $str = $this->objTab->show();

        return $str.'<br />';
    }

    /**
    * Method to show the watchlist checkbox
    *
    * @access public
    * @param bool $watch: TRUE if the page should be watched
    * @param bool $onchange: TRUE if the checkbox should have an onclick
    * @return string $str: The output string
    */
    public function showAddWatchlist($watch = FALSE, $onchange = FALSE)
    {
        $watchLabel = $this->objLanguage->languageText('mod_wiki_watch', 'wiki');

        $objCheck = new checkbox('watch');
        if($watch){
            $objCheck->setChecked(TRUE);
        }
        if($onchange){
            $objCheck->extra= 'onchange="javascript:updateWatchlist(this.checked);"';
        }
        $watchCheck = $objCheck->show();

        $str = $watchCheck.'&#160;&#160;'.$watchLabel;

        return $str;
    }

    /**
    * Method to display page watchlist
    *
    * @access public
    * @return string $str: The output string
    */
    public function showWatchlist()
    {
        // add javascript to sort table
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        // get data
        $data = $this->objDbwiki->getAllUserWatches();

        // text elements
        $pageLabel = $this->objLanguage->languageText('mod_wiki_pagename', 'wiki');
        $watchlistLabel = $this->objLanguage->languageText('word_watchlist');
        $deleteLabel = $this->objLanguage->languageText('word_delete');
        $delConfirmLabel = $this->objLanguage->languageText('mod_wiki_deleteconfirm', 'wiki');
        $noRecordsLabel = $this->objLanguage->languageText('mod_wiki_norecords', 'wiki');
        $pageTitleLabel = $this->objLanguage->languageText('mod_wiki_pagetitle', 'wiki');
        $deleteTitleLabel = $this->objLanguage->languageText('mod_wiki_deletewatch', 'wiki');

        // create display table
        $objTable = new htmltable();
        $objTable->id = 'watchList';
        $objTable->css_class = 'sorttable';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        $objTable->addCell($pageLabel, '', '', '', 'heading', '');
        $objTable->addCell('', '10%', '', 'center', 'heading', '');
        $objTable->endRow();
        if(empty($data)){
            // no records
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
            $objTable->endRow();
        }else{
            // loop through data and display each record in the table
            foreach($data as $line){
                $name = $line['page_name'];
                $pageTitle = $this->objWiki->renderTitle($name);

                // name link
                $objLink = new link($this->uri(array(
                    'action' => 'view_page',
                    'name' => $name,
                ), 'wiki'));
                $objLink->link = $pageTitle;
                $objLink->title = $pageTitleLabel;
                $pageLink = $objLink->show();

                // delete link
                $objLink = new link($this->uri(array(
                    'action' => 'delete_watch',
                    'id' => $line['id'],
                ), 'wiki'));
                $objLink->link = $deleteLabel;
                $objLink->title = $deleteTitleLabel;
                $objLink->extra = 'onclick="javascript:if(!confirm(\''.$delConfirmLabel.'\')){return false};"';
                $deleteLink = $objLink->show();

                // data display
                $objTable->startRow();
                $objTable->addCell($pageLink, '', '', '', '', '');
                $objTable->addCell($deleteLink, '', '', 'center', '', '');
                $objTable->endRow();
            }
        }
        $pageTable = $objTable->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($pageTable);
        $contentLayer = $objLayer->show();

        // add page tab
        $tabArray = array(
            'name' => $watchlistLabel,
            'content' => $contentLayer,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->addTab($tabArray);
        $str = $this->objTab->show();

        return $str.'<br />';
    }

    /**
    * Method to send email notification of updates to the page
    *
    * @access public
    * @params string $name: The name of the page
    * @return bool $mail: TRUE if the mail was sent successfully FALSE if not
    */
    public function sendMail($name)
    {
        // get data
        $data = $this->objDbwiki->getPageWatches($name);
        $pagelink = $this->uri(array(
            'action' => 'view_page',
            'name' => $name,
        ), 'wiki');

        // text elements
        $fromLabel = $this->objLanguage->languageText('mod_wiki_name', 'wiki');
        $subjectLabel = $this->objLanguage->languageText('mod_wiki_subject', 'wiki');

        if(!empty($data)){
            foreach($data as $line){
                // get user data
                $user = $this->objUserAdmin->getUserDetails($this->objUser->PKId($line['creator_id']));

                // create remove watch link
                $removelink = $this->uri(array(
                    'action' => 'remove_watch',
                    'name' => $name,
                    'id' => $line['creator_id'],
                ), 'wiki');
                // create body text
                $array = array(
                    'name' => $user['firstname'].' '.$user['surname'],
                );
                $body = $this->objLanguage->code2Txt('mod_wiki_email_1', 'wiki', $array);
                $body .= "\r\n".$this->objLanguage->code2Txt('mod_wiki_email_2', 'wiki');
                $body .= "\r\n".$this->objLanguage->code2Txt('mod_wiki_email_3', 'wiki');
                $body .= ":-\r\n".$pagelink;
                $body .= "\r\n".$this->objLanguage->code2Txt('mod_wiki_email_4', 'wiki');
                $body .= ":-\r\n".$removelink;

                // set up email
                $this->objMailer->setValue('to', array($user['emailaddress']));
                $this->objMailer->setValue('from', 'noreply@uwc.ac.za');
                $this->objMailer->setValue('fromName', $fromLabel);
                $this->objMailer->setValue('subject', $subjectLabel);
                $this->objMailer->setValue('body', $body);
                $this->objMailer->send();
            }
        }

    }

    /**
    * Method to generate the diff output
    *
    * @access public
    * @param string $name: The name of the page
    * @param integer $from: The version to diff from
    * @param integer $to: The version to diff to
    * @return string $str: The output string
    */
    public function showDiff($name, $from, $to)
    {
        // get data
        $dataFrom = $this->objDbwiki->getPage($name, $from);
        $dataTo = $this->objDbwiki->getPage($name, $to);

        $summaryTo = explode("\n", $dataTo['page_summary']);
        $summaryFrom = explode("\n", $dataFrom['page_summary']);

        $contentTo = explode("\n", $dataTo['page_content']);
        $contentFrom = explode("\n", $dataFrom['page_content']);

        $summaryDiff = $this->objTextdiff->getDiffs($summaryFrom, $summaryTo);
        $contentDiff = $this->objTextdiff->getDiffs($contentFrom, $contentTo);
        $pageTitle = $this->objWiki->renderTitle($name);

        // get text elements
        $contentLabel = $this->objLanguage->languageText('mod_wiki_pagecontent', 'wiki');
        $summaryLabel = $this->objLanguage->languageText('mod_wiki_pagesummary', 'wiki');
        $noDiffLabel = $this->objLanguage->languageText('mod_wiki_nochange', 'wiki');
        $array = array(
            'num_1' => $to,
            'num_2' => $from,
        );
        $versionLabel = $this->objLanguage->code2Txt('mod_wiki_diff', 'wiki', $array);
        // heading
        $objHeader = new htmlheading();
        $objHeader->str = $pageTitle.':<br />'.$versionLabel;
        $objHeader->type = 1;
        $str = $objHeader->show();

        if(!empty($summaryDiff)){
            // summary
            $objHeader = new htmlheading();
            $objHeader->str = $summaryLabel;
            $objHeader->type = 4;
            $str .= $objHeader->show();

            $string = str_replace('<ins>', '<font class="diff_add">', nl2br($summaryDiff));
            $string = str_replace('</ins>', '</font>', $string);
            $string = str_replace('<del>', '<font class="diff_remove">', $string);
            $string = str_replace('</del>', '</font>', $string);

            $str .= $string;
        }

        if(!empty($contentDiff)){
            // content
            $objHeader = new htmlheading();
            $objHeader->str = $contentLabel;
            $objHeader->type = 4;
            $str .= $objHeader->show();

            $string = str_replace('<ins>', '<font class="diff_add">', nl2br($contentDiff));
            $string = str_replace('</ins>', '</font>', $string);
            $string = str_replace('<del>', '<font class="diff_remove">', $string);
            $string = str_replace('</del>', '</font>', $string);

            $str .= $string;
        }

        if(empty($summaryDiff) && empty($contentDiff)){
            $str .= '<ul><li>'.$noDiffLabel.'</li></ul>';
            echo $str;
        }else{
            echo $str;
        }
    }

    /**
    * Method create a list all wiki pages
    *
    * @access public
    * @return string $str: The output string
    **/
    public function showLinks()
    {
        // add javascript to sort table
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);
        $headerParams = $this->getJavascriptFile('wiki.js', 'wiki');
        $this->appendArrayVar('headerParams', $headerParams);

        // text elements
        $titleLabel = $this->objLanguage->languageText('mod_wiki_link', 'wiki');
        $nameLabel = $this->objLanguage->languageText('mod_wiki_linkname', 'wiki');
        $urlLabel = $this->objLanguage->languageText('mod_wiki_linkurl', 'wiki');
        $addLabel = $this->objLanguage->languageText('mod_wiki_addlink', 'wiki');
        $addTitleLabel = $this->objLanguage->languageText('mod_wiki_addlinktitle', 'wiki');
        $noRecordsLabel = $this->objLanguage->languageText('mod_wiki_norecords', 'wiki');
        $editLabel = $this->objLanguage->languageText('word_edit');
        $editTitleLabel = $this->objLanguage->languageText('mod_wiki_editlinktitle', 'wiki');
        $editLinkLabel = $this->objLanguage->languageText('mod_wiki_editlink', 'wiki');
        $wikiErrorLabel = $this->objLanguage->languageText('mod_wiki_wikierror', 'wiki');
        $urlErrorLabel = $this->objLanguage->languageText('mod_wiki_urlerror', 'wiki');

        $nameLabel = $this->objLanguage->languageText('mod_wiki_linkname', 'wiki');
        $urlLabel = $this->objLanguage->languageText('mod_wiki_linkurl', 'wiki');

        $createLabel = $this->objLanguage->languageText('mod_wiki_createlink', 'wiki');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');
        $updateLabel = $this->objLanguage->languageText('word_update');


        // get data
        $data = $this->objDbwiki->getLinks();

        $string = '';
        $addLink = '';
        if($this->isAdmin){
            // add link
            $objButton = new button('submit', $addLabel);
            $addButton = $objButton->show();

            $objLink = new link('#');
            $objLink->link = $addButton;
            $objLink->title = $addTitleLabel;
            $objLink->extra = 'id="addLink" onclick="javascript:showAddLink();"';
            $addLink = $objLink->show();
        }

        // link name
        $objHeader = new htmlheading();
        $objHeader->str = $nameLabel;
        $objHeader->type = 4;
        $string .= $objHeader->show();

        // page name textinput
        $objInput = new textinput('name', '', '', '96');
        $string .= $objInput->show();

        // link name
        $objHeader = new htmlheading();
        $objHeader->str = $urlLabel;
        $objHeader->type = 4;
        $string .= $objHeader->show();

        // page name textinput
        $objInput = new textinput('url', '', '', '96');
        $string .= $objInput->show();

        // content layer
        $objLayer = new layer();
        $objLayer->addToStr($string);
        $contentLayer = $objLayer->show();

        // create button
        $objButton = new button('create', $createLabel);
        $objButton->setToSubmit();
        $createButton = $objButton->show();

        // create button
        $objButton = new button('cancel', $cancelLabel);
        $objButton->extra = 'onclick="javascript:cancelAddLink();"';
        $cancelButton = $objButton->show();

        // button layer
        $objLayer = new layer();
        $objLayer->addToStr($createButton.'&#160;'.$cancelButton);
        $buttonLayer = $objLayer->show();

        // form
        $objForm = new form('create', $this->uri(array(
            'action' => 'update_link',
        ), 'wiki'));
        $objForm->addToForm($contentLayer);
        $objForm->addToForm($buttonLayer);
        $objForm->addRule('name', $wikiErrorLabel, 'required');
        $objForm->addRule('url', $urlErrorLabel, 'required');
        $createForm = $objForm->show();

        // add page tab
        $addData = array(
            'name' => $addLabel,
            'content' => $createForm,
        );
        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'addTab';
        $this->objTab->addTab($addData);
        $addTab = $this->objTab->show();

        $objLayer = new layer();
        $objLayer->id = 'addLinkLayer';
        $objLayer->display = 'none';
        $objLayer->addToStr($addTab.'<br />');
        $addLinkLayer = $objLayer->show();

        // page name textinput
        $objInput = new textinput('id', '', 'hidden', '');
        $string = $objInput->show();

        // link name
        $objHeader = new htmlheading();
        $objHeader->str = $nameLabel;
        $objHeader->type = 4;
        $string .= $objHeader->show();

        // page name textinput
        $objInput = new textinput('update_name', '', '', '96');
        $string .= $objInput->show();

        // link name
        $objHeader = new htmlheading();
        $objHeader->str = $urlLabel;
        $objHeader->type = 4;
        $string .= $objHeader->show();

        // page name textinput
        $objInput = new textinput('update_url', '', '', '96');
        $string .= $objInput->show();

        // content layer
        $objLayer = new layer();
        $objLayer->addToStr($string);
        $contentLayer = $objLayer->show();

        // create button
        $objButton = new button('create', $updateLabel);
        $objButton->setToSubmit();
        $createButton = $objButton->show();

        // create button
        $objButton = new button('cancel', $cancelLabel);
        $objButton->extra = 'onclick="javascript:cancelUpdateLink();"';
        $cancelButton = $objButton->show();

        // button layer
        $objLayer = new layer();
        $objLayer->addToStr($createButton.'&#160;'.$cancelButton);
        $buttonLayer = $objLayer->show();

        // form
        $objForm = new form('update', $this->uri(array(
            'action' => 'update_link',
        ), 'wiki'));
        $objForm->addToForm($contentLayer);
        $objForm->addToForm($buttonLayer);
        $objForm->addRule('update_name', $wikiErrorLabel, 'required');
        $objForm->addRule('update_url', $urlErrorLabel, 'required');
        $createForm = $objForm->show();

        // add page tab
        $addData = array(
            'name' => $editLinkLabel,
            'content' => $createForm,
        );
        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'updateTab';
        $this->objTab->addTab($addData);
        $addTab = $this->objTab->show();

        $objLayer = new layer();
        $objLayer->id = 'updateLinkLayer';
        $objLayer->display = 'none';
        $objLayer->addToStr($addTab.'<br />');
        $updateLinkLayer = $objLayer->show();

        // create display table
        $objTable = new htmltable();
        $objTable->id = 'linksList';
        $objTable->css_class = 'sorttable';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        $objTable->addCell($nameLabel, '', '', '', 'heading', '');
        $objTable->addCell($urlLabel, '', '', '', 'heading', '');
        if($this->isAdmin){
            $objTable->addCell('&#160;', '', '', '', 'heading', '');
        }
        $objTable->endRow();

        if(empty($data)){
            // no records
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
            $objTable->endRow();
        }else{
            // loop through data and display each record in the table
            foreach($data as $line){
                $name = $line['wiki_name'];
                $link = $line['wiki_link'];

                // page name link
                $objButton = new button('submit', $editLabel);
                $editButton = $objButton->show();

                $objLink = new link('#');
                $objLink->link = $editButton;
                $objLink->title = $editTitleLabel;
                $objLink->extra = 'onclick="javascript:setEdit(\''.$line['id'].'\', \''.$name.'\',\''.$link.'\')"';
                $editLink = $objLink->show();

                // data display
                $objTable->startRow();
                $objTable->addCell($name, '30%', '', '', '', '');
                $objTable->addCell($link, '', '', '', '', '');
                if($this->isAdmin){
                    $objTable->addCell($editLink, '15%', '', 'center', '', '');
                }
                $objTable->endRow();
            }
        }
        $pageTable = $objTable->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($addLink.$addLinkLayer.$updateLinkLayer.$pageTable);
        $contentLayer = $objLayer->show();

        // add page tab
        $linksTab = array(
            'name' => $titleLabel,
            'content' => $contentLayer,
        );
        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'linkTab';
        $this->objTab->addTab($linksTab);
        $str = $this->objTab->show();

        return $str;
    }

    /**
    * Method to display the discussion tab
    *
    * @access public
    * @param string $name: The wiki page name
    * @return string $str: The output string
    */
    public function showDiscussion($name)
    {
        // get data
        $data = $this->objDbwiki->getPosts($name);

        // text elements
        $noRecordsLabel = $this->objLanguage->languageText('mod_wiki_norecords', 'wiki');
        $editLabel = $this->objLanguage->languageText('mod_wiki_editpost', 'wiki');
        $editTitleLabel = $this->objLanguage->languageText('mod_wiki_postedit', 'wiki');
        $deleteLabel = $this->objLanguage->languageText('mod_wiki_deletepost', 'wiki');
        $deleteTitleLabel = $this->objLanguage->languageText('mod_wiki_deleteposttitle', 'wiki');
        $deleteConfirmLabel = $this->objLanguage->languageText('mod_wiki_postconfirm', 'wiki');
        $deletedLabel = $this->objLanguage->languageText('mod_wiki_postdeleted', 'wiki');
        $restoreLabel = $this->objLanguage->languageText('mod_wiki_restorepost', 'wiki');
        $restoreTitleLabel = $this->objLanguage->languageText('mod_wiki_restoreposttitle', 'wiki');

        $str = '';
        if($this->isLoggedIn){
            $str .= $this->_showAddPost($name);
        }

        if(empty($data)){
            $objTable = new htmltable();
            $objTable->cellpadding = '2';
            $objTable->border = '1';
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', '');
            $objTable->endRow();
            $str .= $objTable->show();
        }else{
            if(count($data) > 5){
                $sections = array_chunk($data, 5);
            }else{
                $sections[] = $data;
            }
            $this->objTab->init();
            $i = 1;
            foreach($sections as $key => $subsection){
                $string = '';
                $ii = 0;
                foreach($subsection as $line){
                    $class = (($ii++%2) == 0) ? 'even' : 'odd';
                    $postId = $line['id'];

                    // title
                    $array = array(
                        'name' => $this->objUser->fullname($line['author_id']),
                        'date' => $this->objDate->formatDate($line['date_created']),
                    );
                    $author = $this->objLanguage->code2Txt('mod_wiki_postauthor', 'wiki', $array);
                    $link = '';
                    if($this->isAdmin && $line['post_status'] == 1){
                        $objButton = new button('submit', $deleteLabel);
                        $deleteButton = $objButton->show();

                        $objLink = new link($this->uri(array(
                            'action' => 'delete_post',
                            'name' => $name,
                            'id' => $postId,
                        ), 'wiki'));
                        $objLink->link = $deleteButton;
                        $objLink->title = $deleteTitleLabel;
                        $objLink->extra = 'onclick="javascript:if(!confirm(\''.$deleteConfirmLabel.'\')){return false};"';
                        $link = $objLink->show();
                    }elseif($this->isAdmin && $line['post_status'] == 2){
                        $objButton = new button('submit', $restoreLabel);
                        $restoreButton = $objButton->show();

                        $objLink = new link($this->uri(array(
                            'action' => 'restore_post',
                            'name' => $name,
                            'id' => $postId,
                        ), 'wiki'));
                        $objLink->link = $restoreButton;
                        $objLink->title = $restoreTitleLabel;
                        $link = $objLink->show();
                    }

                    $content = $this->objWiki->transform($line['post_content']);

                    // edit post link
                    $objButton = new button('submit', $editLabel);
                    $editButton = $objButton->show();

                    $objLink = new link('#');
                    $objLink->link = $editButton;
                    $objLink->title = $editTitleLabel;
                    $objLink->extra = 'onclick="javascript:showUpdatePost(\''.$postId.'\', \''.$line['post_title'].'\', \''.$line['post_content'].'\')"';
                    $editLink = $objLink->show();

                    if(strtotime(date('Y-m-d H:i:s')) <= strtotime('+ 10 min', strtotime($line['date_created'])) && $this->userId == $line['author_id']){
                        $content .= $editLink;
                    }

                    if($this->isAdmin && $line['post_status'] == 2){
                        $content = '<ul><li><b>'.$deletedLabel.'</b></li></ul>'.$content;
                    }elseif($line['post_status'] == 2){
                        $content = '<ul><li><b>'.$deletedLabel.'</b></li></ul>';
                    }

                    $objLayer = new layer();
                    $objLayer->padding = '10px';
                    $objLayer->addToStr($content);
                    $contentLayer = $objLayer->show();

                    $objTable = new htmltable();
                    $objTable->cellpadding = '2';
                    $objTable->border = '1';
                    $objTable->startRow();
                    $objTable->addCell($i++.'.', '5%', 'center', 'center', 'heading', '');
                    $objTable->addCell('&#160;<b>'.$line['post_title'].'</b><br />&#160;'.$author, '', 'center', '', 'heading', '');
                    if(!empty($link)){
                        $objTable->addCell($link, '', 'center', 'center', 'heading', '');
                    }
                    $objTable->endRow();
                    $objTable->startRow();
                    $objTable->addCell($contentLayer, '', '', '', $class, 'colspan="3"');
                    $objTable->endRow();
                    $string .= $objTable->show().'<br />';
                }
                $start = (($key * 5) + 1);
                $end = (($key * 5) + count($subsection));
                $tabArray = array(
                    'name' => $start.' - '.$end,
                    'content' => $string,
                );
                $this->objTab->addTab($tabArray);
            }
            $this->objTab->tabId = 'sectionTab';
            $str .= $this->objTab->show();
        }
        $objLayer = new layer();
        $objLayer->id = 'tableLayer';
        $objLayer->addToStr($str);
        $string = $objLayer->show();
        if(!empty($data)){
            foreach($data as $line){
                $string .= $this->_showEditPost($line['id']);
            }
        }
        $str = $string;
        return $str;
    }

    /**
    * Method to display the add post tabs
    *
    * @access private
    * @param string $nameId: The name of the wiki page
    * @return string $str: The output string
    */
    private function _showAddPost($name)
    {
        $addLabel = $this->objLanguage->languageText('mod_wiki_addpost', 'wiki');
        $addTitleLabel = $this->objLanguage->languageText('mod_wiki_titlepost', 'wiki');
        $titleLabel = $this->objLanguage->languageText('mod_wiki_posttitle', 'wiki');
        $contentLabel = $this->objLanguage->languageText('mod_wiki_postcontent', 'wiki');
        $createLabel = $this->objLanguage->languageText('mod_wiki_createpost', 'wiki');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');

        // add post link
        $objButton = new button('submit', $addLabel);
        $addButton = $objButton->show();

        $objLink = new link('#');
        $objLink->link = $addButton;
        $objLink->title = $addTitleLabel;
        $objLink->extra = 'id="addLink" onclick="javascript:showAddPost();"';
        $str = $objLink->show();

        // post title
        $objHeader = new htmlheading();
        $objHeader->str = $titleLabel;
        $objHeader->type = 4;
        $string = $objHeader->show();

        $objInput = new textinput('post_title', '', '', '96');
        $string .= $objInput->show();

        // post content
        $objHeader = new htmlheading();
        $objHeader->str = $contentLabel;
        $objHeader->type = 4;
        $string .= $objHeader->show();

        $objText = new textarea('post_content', '', '4', '70');
        $string .= $objText->show();

        // create button
        $objButton = new button('create', $createLabel);
        $objButton->setToSubmit();
        $createButton = $objButton->show();

        // cancel button
        $objButton = new button('cancel', $cancelLabel);
        $objButton->extra = 'onclick="javascript:cancelAddPost()"';
        $cancelButton = $objButton->show();

        // button layer
        $objLayer = new layer();
        $objLayer->addToStr($createButton.'&#160;'.$cancelButton);
        $buttonLayer = $objLayer->show();

        // form
        $objForm = new form('create', $this->uri(array(
            'action' => 'update_post',
            'name' => $name,
        ), 'wiki'));
        $objForm->addToForm($string);
        $objForm->addToForm($buttonLayer);
        $createForm = $objForm->show();

        // add page tab
        $tab = array(
            'name' => $addLabel,
            'content' => $createForm,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->tabId = 'addTab';
        $this->objTab->addTab($tab);
        $addTab = $this->objTab->show();

        $objLayer = new layer();
        $objLayer->id = 'addDiv';
        $objLayer->addToStr($addTab);
        $objLayer->display = 'none';
        $str .= $objLayer->show().'<br />';

        return $str;
    }

    /**
    * Method to display the edit post tabs
    *
    * @access private
    * @param string $postId: The id of the post
    * @return string $str: The output string
    */
    private function _showEditPost($postId)
    {
        // text elements
        $titleLabel = $this->objLanguage->languageText('mod_wiki_posttitle', 'wiki');
        $contentLabel = $this->objLanguage->languageText('mod_wiki_postcontent', 'wiki');
        $updateLabel = $this->objLanguage->languageText('word_update');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');
        $editLabel = $this->objLanguage->languageText('mod_wiki_editpost', 'wiki');
        // post title
        $objHeader = new htmlheading();
        $objHeader->str = $titleLabel;
        $objHeader->type = 4;
        $string = $objHeader->show();

        $objInput = new textinput('post_title_'.$postId, '', '', '96');
        $string .= $objInput->show();

        // post content
        $objHeader = new htmlheading();
        $objHeader->str = $contentLabel;
        $objHeader->type = 4;
        $string .= $objHeader->show();

        $objText = new textarea('post_content_'.$postId, '', '4', '70');
        $string .= $objText->show();

        // create button
        $objButton = new button('create', $updateLabel);
        $objButton->setToSubmit();
        $createButton = $objButton->show();

        // cancel button
        $objButton = new button('cancel', $cancelLabel);
        $objButton->extra = 'onclick="javascript:cancelUpdatePost(\''.$postId.'\')"';
        $cancelButton = $objButton->show();

        // button layer
        $objLayer = new layer();
        $objLayer->addToStr($createButton.'&#160;'.$cancelButton);
        $buttonLayer = $objLayer->show();

        // form
        $objForm = new form('update_'.$postId, $this->uri(array(
            'action' => 'update_post',
            'id' => $postId,
        ), 'wiki'));
        $objForm->addToForm($string);
        $objForm->addToForm($buttonLayer);
        $updateForm = $objForm->show();

        // add page tab
        $tab = array(
            'name' => $editLabel,
            'content' => $updateForm,
        );

        //display tabs
        $this->objTab->init();
        $this->objTab->addTab($tab);
        $this->objTab->tabId = $postId;
        $editTab = $this->objTab->show();

        $objLayer = new layer();
        $objLayer->id = 'tab_'.$postId;
        $objLayer->addToStr($editTab);
        $objLayer->display = 'none';
        $str = $objLayer->show();

        return $str;
    }

    /**
    * Method to add wikis
    *
    * @access public
    * @return string $str: The output string
    */
    public function showAddWiki()
    {
        // add  javascript
        $headerParams = $this->getJavascriptFile('wiki.js', 'wiki');
        $this->appendArrayVar('headerParams', $headerParams);

        // text elements
        $wikiLabel = $this->objLanguage->languageText('mod_wiki_createwiki', 'wiki');
        $nameLabel = $this->objLanguage->languageText('mod_wiki_linkname', 'wiki');
        $descLabel = $this->objLanguage->languageText('mod_wiki_description', 'wiki');
        $visibilityLabel = $this->objLanguage->languageText('mod_wiki_visibility', 'wiki');
        $createLabel = $this->objLanguage->languageText('mod_wiki_wiki', 'wiki');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');
        $publicLabel = $this->objLanguage->languageText('word_public');
        $openLabel = $this->objLanguage->languageText('word_open');
        $privateLabel = $this->objLanguage->languageText('mod_wiki_wordprivate', 'wiki');
        $publicTextLabel = $this->objLanguage->languageText('mod_wiki_public', 'wiki');
        $openTextLabel = $this->objLanguage->languageText('mod_wiki_open', 'wiki');
        $privateTextLabel = $this->objLanguage->languageText('mod_wiki_private', 'wiki');
        $nameErrorLabel = $this->objLanguage->languageText('mod_wiki_errorwiki', 'wiki');
        $descErrorLabel = $this->objLanguage->languageText('mod_wiki_errordesc', 'wiki');

        // wiki name
        $objHeader = new htmlheading();
        $objHeader->str = $nameLabel;
        $objHeader->type = 4;
        $content = $objHeader->show();

        // wiki name textinput
        $objInput = new textinput('name', '', '', '96');
        $content .= $objInput->show();

        // wiki description
        $objHeader = new htmlheading();
        $objHeader->str = $descLabel;
        $objHeader->type = 4;
        $content .= $objHeader->show();

        // wiki name textinput
        $objText = new textarea('desc', '', '4', '70');
        $content .= $objText->show();

        // wiki description
        $objHeader = new htmlheading();
        $objHeader->str = $visibilityLabel;
        $objHeader->type = 4;
        $content .= $objHeader->show();

        $objRadio = new radio('visibility');
        $objRadio->addOption(1, '&#160;'.$publicLabel);
        $objRadio->extra = 'style="vertical-align: middle;"';
        $content .= '<b>'.$objRadio->show().'</b>';
        $content .= '<ul><li>'.$publicTextLabel.'</li></ul>';

        $objRadio = new radio('visibility');
        $objRadio->addOption(2, '&#160;'.$openLabel);
        $objRadio->extra = 'style="vertical-align: middle;"';
        $content .= '<b>'.$objRadio->show().'</b>';
        $content .= '<ul><li>'.$openTextLabel.'</li></ul>';

        $objRadio = new radio('visibility');
        $objRadio->addOption(3, '&#160;'.$privateLabel);
        $objRadio->extra = 'style="vertical-align: middle;"';
        $objRadio->setSelected(3);
        $content .= '<b>'.$objRadio->show().'</b>';
        $content .= '<ul><li>'.$privateTextLabel.'</li></ul>';

        // create button
        $objButton = new button('create', $createLabel);
        $objButton->setToSubmit();
        $createButton = $objButton->show();

        // create button
        $objButton = new button('cancel', $cancelLabel);
        $objButton->extra = 'onclick="javascript:history.back();"';
        $cancelButton = $objButton->show();

        // button layer
        $objLayer = new layer();
        $objLayer->addToStr($createButton.'&#160;'.$cancelButton);
        $buttonLayer = $objLayer->show();

        // form
        $objForm = new form('create', $this->uri(array(
            'action' => 'create_wiki',
        ), 'wiki'));
        $objForm->addToForm($content);
        $objForm->addToForm($buttonLayer);
        $objForm->addRule('name', $nameErrorLabel, 'required');
        $objForm->addRule('desc', $descErrorLabel, 'required');
        $createForm = $objForm->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($createForm);
        $createLayer = $objLayer->show();

        // add page tab
        $addTab = array(
            'name' => $wikiLabel,
            'content' => $createLayer,
        );

         //display tabs
        $this->objTab->init();
        $this->objTab->addTab($addTab);
        $this->objTab->useCookie = 'false';
        $str = $this->objTab->show();

        return $str;
    }

    /**
    * Method to create the wiki selector
    *
    * @access public
    * @return string $str: The output string
    */
    public function showSelector()
    {
        // get data
        if(!$this->isLoggedIn){
            $data = $this->objDbwiki->getPublicWikis();
        }else{
            $data = $this->objDbwiki->getUserWikis();
        }

        // text elements
        $selectLabel = $this->objLanguage->languageText('mod_wiki_select', 'wiki');
        $objDrop = new dropdown('wiki');
        $objDrop->addOption(NULL, $selectLabel);
        foreach($data as $line){
            $objDrop->addOption($line['id'], $line['wiki_name']);
        }
        $objDrop->setSelected($this->getSession('wiki_id'));
        $objDrop->extra = 'onchange="javascript:if(this.value != \'\'){$(\'form_select\').submit();}else{return false}"';
        $selectDrop = $objDrop->show();

        $objForm = new form('select', $this->uri(array(
            'action' => 'select_wiki',
        ), 'wiki'));
        $objForm->addToForm($selectDrop);
        $str = $objForm->show();

        return $str;
    }

    /**
    * Method to display the contents of an iframe to submit the preview
    *
    * @access public
    * @return string $str: The output string
    */
    public function submitIframe($name = NULL, $content = NULL)
    {

        $refreshLabel = $this->objLanguage->languageText('mod_wiki_loadpreview', 'wiki');
        $refreshTitleLabel = $this->objLanguage->languageText('mod_wiki_loadtitle', 'wiki');

        $loadingLabel = $this->objLanguage->languageText('mod_wiki_loading', 'wiki');
        $noPreviewLabel = $this->objLanguage->languageText('mod_wiki_nopreview', 'wiki');

        // refresh link
        $objButton = new button('submit', $refreshLabel);
        $refreshButton = $objButton->show();

        $objLink = new link('#');
        $objLink->link = $refreshButton;
        $objLink->title = $refreshTitleLabel;
        $objLink->extra = 'onclick="javascript:Element.show(\'loadingDiv\');$(\'form_iframe_form\').submit();"';
        $refreshLink = $objLink->show();

        // loading bar
        $this->objIcon->setIcon('loading_bar');
        $this->objIcon->title = $loadingLabel;
        $loadingIcon = $this->objIcon->show();

        $objLayer = new layer();
        $objLayer->id = 'loadingDiv';
        $objLayer->display = 'none';
        $objLayer->addToStr($loadingLabel.'<br />'.$loadingIcon);
        $loadingLayer = $objLayer->show();

        $objInput = new textinput('preview_name', $name);
        $nameInput = $objInput->show();

        $objText = new textarea('preview_content', $content);
        $contentText = $objText->show();

        $objForm = new form('iframe_form', $this->uri(array(
            'action' => 'preview_iframe',
        ), 'wiki'));
        $objForm->addToForm($nameInput. $contentText);
        $previewForm = $objForm->show();

        $objLayer = new layer();
        $objLayer->id = 'formDiv';
        $objLayer->display = 'none';
        $objLayer->addToStr($previewForm);
        $formLayer = $objLayer->show();

        $string = '';
        if(!empty($name)){
            $title = $this->objWiki->renderTitle($name);
            $objHeader = new htmlheading();
            $objHeader->str = $title;
            $objHeader->type = 1;
            $string .= $objHeader->show();
        }
        if(!empty($content)){
            $string .= $this->objWiki->transform($content);
        }
        if(empty($name) && empty($content)){
            $string .= '<ul><li><b>'.$noPreviewLabel.'</b></li></ul>';
        }

        $objLayer = new layer();
        $objLayer->id = 'contentDiv';
        $objLayer->padding = '10px';
        $objLayer->addToStr($string);
        $contentLayer = $objLayer->show();

        $objLayer = new layer();
        $objLayer->id = 'iframeDiv';
        $objLayer->padding = '10px';
        $objLayer->cssClass = 'featurebox';
        $objLayer->addToStr($refreshLink.$loadingLayer.$formLayer.$contentLayer);
        $frameLayer = $objLayer->show();

        $str = $frameLayer;

        return $str;
    }

    /**
    * Method to return a wiki page for use in an api
    *
    * @access public
    * @param string $name: The name of the wiki
    * @param string $pageName: The name of the wiki page (CamelCase)
    * @return string $str: The rendered wiki page
    */
    public function showPage($name, $pageName)
    {
        $notFound = $this->objLanguage->languageText('mod_wiki_notfound', 'wiki');

        $data = $this->objDbwiki->getWikiPage($name, $pageName);
        if(!empty($data)){
            $pageTitle = $this->objWiki->renderTitle($data['page_name']);
            $array = array(
                'date' => $this->objDate->formatDate($data['date_created']),
            );
            $modifiedLabel = $this->objLanguage->code2Txt('mod_wiki_modified', 'wiki', $array);

            $objHeader = new htmlheading();
            $objHeader->str = $pageTitle;
            $objHeader->type = 1;
            $heading = $objHeader->show();

            $str = $heading;
            $str .= $this->objWiki->transform($data['page_content']);
            $str .= '<hr />'.$modifiedLabel;
            return $str;
        }else{
            $str = '<ul>';
            $str .= '<li><b>'.$notFound.'</b></li>';
            $str .= '</ul>';
            return $str;
        }
    }

    /**
    * Method to show the wiki visibility tab
    *
    * @access private
    * @param string $wiki_id: The id of the wiki
    * @return string $str: The output string
    */
    private function _showVisibility($wiki)
    {
        // text elements
        $visibilityLabel = $this->objLanguage->languageText('mod_wiki_changevisibility', 'wiki');
        $publicLabel = $this->objLanguage->languageText('word_public');
        $openLabel = $this->objLanguage->languageText('word_open');
        $privateLabel = $this->objLanguage->languageText('mod_wiki_wordprivate', 'wiki');

        // set up htmlelements
        $objRadio = new radio('visibility');
        $objRadio->addOption(1, '&#160;'.$publicLabel.'&#160;&#160;&#160;');
        $objRadio->addOption(2, '&#160;'.$openLabel.'&#160;&#160;&#160;');
        $objRadio->addOption(3, '&#160;'.$privateLabel);
        $objRadio->setSelected($wiki['wiki_visibility']);
        $changeRadio = $objRadio->show();

        // create button
        $objButton = new button('change', $visibilityLabel);
        $objButton->setToSubmit();
        $changeButton = $objButton->show();

        $objForm = new form('visibility', $this->uri(array(
            'action' => 'change_visibility',
            'wikiId' => $wiki['id'],
        ), 'wiki'));
        $objForm->addToForm($changeRadio.'<p />');
        $objForm->addToForm($changeButton);
        $str = $objForm->show();

        return $str.'<br />';
    }
}
?>