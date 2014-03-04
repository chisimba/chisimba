<?php

/**
 *
 *
 */
class news extends controller {

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access protected
     * @var object
     */
    protected $objSysConfig;
    /**
     * Instance of the washout class of the utilities module.
     *
     * @access protected
     * @var object
     */
    protected $objWashOut;

    /**
     * Constructor for the Module
     */
    public function init() {
        $this->objNewsCategories = $this->getObject('dbnewscategories');
        $this->objNewsMenu = $this->getObject('dbnewsmenu');
        $this->objNewsStories = $this->getObject('dbnewsstories');

        $this->objArchivesStories = $this->getObject('dbarchivesstories');
        $this->objArchivesTags = $this->getObject('dbarchivestags');
        $this->objArchivesKeywords = $this->getObject('dbarchiveskeywords');

        $this->objKeywords = $this->getObject('dbnewskeywords');
        $this->objTags = $this->getObject('dbnewstags');
        $this->objComments = $this->getObject('dbnewscomments');

        $this->objLanguage = $this->getObject('language', 'language');

        $this->objNewsBlocks = $this->getObject('dbnewsblocks');
        $this->objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

        $this->loadClass('link', 'htmlelements');

        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objUser = $this->getObject('user', 'security');

        $this->objWashOut = $this->getObject('washout', 'utilities');
        
        $this->objModule = $this->getObject('modules', 'modulecatalogue');
        //Check if contentblocks is installed
        $this->cbExists = $this->objModule->checkIfRegistered("contentblocks");
        if ($this->cbExists) {
            $this->objBlocksContent = $this->getObject('dbcontentblocks', 'contentblocks');
        }
    }

    /**
     * Method to turn off login for selected actions
     *
     * @access public
     * @param string $action Action being run
     * @return boolean Whether the action requires the user to be logged in or not
     */
    function requiresLogin($action='home') {
        $allowedActions = array(NULL, 'home', 'storyview', 'showmap', 'viewtimeline', 'generatetimeline', 'themecloud', 'generatekml', 'viewcategory', 'viewstory', 'viewbykeyword', 'generatekeywordtimeline', 'search', 'topstoriesfeed', 'liststories');

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Standard Dispatch Function for Controller
     *
     * @access public
     * @param string $action Action being run
     * @return string Filename of template to be displayed
     */
    public function dispatch($action) {
        // Method to set the layout template for the given action
        $this->putLayoutTemplate($action);

        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     * Method to set the layout template for a given action
     *
     * @access private
     * @param string $action Action being run
     */
    private function putLayoutTemplate($action) {
        $twoCols = array('admin', 'addcategory', 'managecategories', 'viewlocation', 'addstory', 'viewarchives', 'archivestory', 'editstory', 'themecloud', 'tagcloud', 'viewtimeline', 'viewbykeyword', 'viewcategory', 'viewlocation', 'viewstories', 'showmap', 'editmenuitem', 'liststories', 'addmenuitem', 'search', 'deletestory', 'deletecategory');

        if (in_array($action, $twoCols)) {
            $this->setLayoutTemplate('2collayout.php');
        } else {
            $this->setLayoutTemplate('layout.php');
        }
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__' . $action;
        } else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    // Beginning of Functions Relating to Actions in the Controller //

    /**
     *
     *
     */
    private function __home() {
        // $this->setLayoutTemplate('blocks_layout_tpl.php');
        $this->setLayoutTemplate(NULL);

        $topStories = $this->objNewsStories->getTopStoriesFormatted();
        $this->setVarByRef('topStories', $topStories['stories']);
        $this->setVarByRef('topStoriesId', $topStories['topstoryids']);

        $categories = $this->objNewsCategories->getCategoriesWithStories('categoryname');
        $this->setVarByRef('categories', $categories);

        // Load Blocks
        $rightBlocks = $this->objNewsBlocks->getBlocksAndSendToTemplate('frontpage', 'frontpage', 'right');
        $leftBlocks = $this->objNewsBlocks->getBlocksAndSendToTemplate('frontpage', 'frontpage', 'left');
        //Add content blocks if exists
        $contentSmallBlocks = "";
        $contentWideBlocks = "";
        if ($this->cbExists) {
            $contentSmallBlocks = $this->objBlocksContent->getBlocksArr('content_text');
            $this->setVarByRef('contentSmallBlocks', $contentSmallBlocks);

            $contentWideBlocks = $this->objBlocksContent->getBlocksArr('content_widetext');
            $this->setVarByRef('contentWideBlocks', $contentWideBlocks);
        }
        $this->setVar('pageType', 'frontpage');
        $this->setVar('pageTypeId', 'frontpage');
        $this->setVar('rightBlocks', $rightBlocks);
        $this->setVar('leftBlocks', $leftBlocks);

        $this->setLayoutTemplate("newslayout_tpl.php");
        return 'newshome_tpl.php';
    }

    /**
     * A Pseudo Action to require the user to login before accessing the home page of the news module
     */
    private function __login() {
        return $this->nextAction('home');
    }

    /**
     *
     *
     */
    private function __managecategories() {
        $categories = $this->objNewsCategories->getCategories();
        $this->setVarByRef('categories', $categories);

        $menuItems = $this->objNewsMenu->getMenuItems();
        $this->setVarByRef('menuItems', $menuItems);

        return 'managecategories.php';
    }

    /**
     *
     *
     */
    private function __addmenuitem() {
        return 'addmenuitem.php';
    }

    /**
     *
     *
     */
    private function __adddividertomenu() {
        $this->objNewsMenu->addDivider();
        return $this->nextAction('managecategories', array('newrecord' => 'divideradded'));
    }

    /**
     *
     *
     */
    private function __addurltomenu() {
        $title = $this->getParam('urlmenutitle');
        $url = $this->getParam('websiteurl');

        if ($title == '' || $url == '' || $url == 'http://') {
            return $this->nextAction('managecategories', array('error' => 'notitleandurlgiven', 'title' => $title, 'url' => urlencode($url)));
        }

        $objUrl = $this->getObject('url', 'strings');
        if (!$objUrl->isValidFormedUrl($url)) {
            return $this->nextAction('managecategories', array('error' => 'notvalidurl', 'title' => $title, 'url' => urlencode($url)));
        }

        $id = $this->objNewsMenu->addWebsite($title, $url);

        return $this->nextAction('managecategories', array('newrecord' => 'urladded', 'id' => $id));
    }

    /**
     *
     *
     */
    private function __addtexttomenu() {
        $text = $this->getParam('text');
        if (trim($text) == '') {
            return $this->nextAction('managecategories', array('error' => 'notext'));
        }

        $id = $this->objNewsMenu->addText($text);

        return $this->nextAction('managecategories', array('newrecord' => 'textadded', 'id' => $id));
    }

    /**
     *
     *
     */
    private function __addmoduletomenu() {
        $module = $this->getParam('themodule');

        if (trim($module) == '') {
            return $this->nextAction('managecategories', array('error' => 'nomodule'));
        }

        $id = $this->objNewsMenu->addModule($module);

        return $this->nextAction('managecategories', array('newrecord' => 'moduleadded', 'id' => $id));
    }

    /**
     *
     *
     */
    private function __addblocktomenu() {
        $block = $this->getParam('theblock');

        if (trim($block) == '') {
            return $this->nextAction('managecategories', array('error' => 'noblock'));
        }

        $id = $this->objNewsMenu->addBlock($block);

        return $this->nextAction('managecategories', array('newrecord' => 'blockadded', 'id' => $id));
    }

    /**
     *
     *
     */
    private function __savebasiccategory() {
        $categoryId = $this->objNewsCategories->addBasicCategory($this->getParam('basiccategory'), $this->getParam('basiccategorytype'));

        $id = $this->objNewsMenu->addCategory($categoryId, $this->getParam('basiccategory'));

        return $this->nextAction('managecategories', array('newrecord' => 'categoryadded', 'id' => $id));
    }

    private function __updatebasiccategory() {

        $id = $this->getParam('id');

        $item = $this->objNewsMenu->getItem($id);

        $name = $this->getParam('basiccategory');
        $categoryType = $this->getParam('basiccategorytype');

        $this->objNewsMenu->updateCategory($id, $name);

        $function = 'updateBasicCategory_' . $categoryType;
        $this->objNewsCategories->$function($item['itemvalue'], $name);

        $category = $this->objNewsCategories->getCategory($item['itemvalue']);

        if ($category != FALSE) {
            $this->objNewsStories->serializeStoryOrder($item['itemvalue'], str_replace('_', ' ', $category['itemsorder']));
        }

        $returnAction = $this->getParam('returnaction');

        if ($returnAction == 'managecategories') {
            return $this->nextAction('managecategories', array('message' => 'categoryupdated'));
        } else {
            return $this->nextAction('viewcategory', array('id' => $item['itemvalue'], 'message' => 'categoryupdated'));
        }
    }

    /**
     *
     *
     */
    private function __saveadvancecategory() {
        //echo '<pre>';
        //print_r($_POST);


        $name = $this->getParam('advancecategory');
        $categoryType = $this->getParam('advancecategorytype');
        $itemsOrder = $this->getParam('advanceorder');
        $defaultSticky = $this->getParam('defaultsticky');
        $blockOnFrontPage = $this->getParam('blockonfrontpage');
        $showIntroduction = $this->getParam('showintroduction');
        $introduction = $this->getParam('introduction');
        $numitems = $this->getParam('numitems');
        $othernum = $this->getParam('othernum', 10);
        $rssFeeds = $this->getParam('rssfeeds');
        $socialBookmarks = $this->getParam('socialbookmarks');

        if ($numitems == 'other') {
            $numitems = $othernum;
        }

        if (!is_numeric($numitems)) {
            $numitems = 10;
        }

        $categoryId = $this->objNewsCategories->addAdvancedCategory($name, $defaultSticky, $itemsOrder, $categoryType, $introduction, $showIntroduction, $blockOnFrontPage, $numitems, $rssFeeds, $socialBookmarks);

        $id = $this->objNewsMenu->addCategory($categoryId, $name);

        //echo $categoryId;

        return $this->nextAction('managecategories', array('newrecord' => 'categoryadded', 'id' => $id));
    }

    /**
     *
     *
     */
    private function __updateadvancecategory() {
        //echo '<pre>';
        //print_r($_POST);
        $id = $this->getParam('id');

        $item = $this->objNewsMenu->getItem($id);

        $name = $this->getParam('advancecategory');
        $categoryType = $this->getParam('advancecategorytype');
        $itemsOrder = $this->getParam('advanceorder');
        $defaultSticky = $this->getParam('defaultsticky');
        $blockOnFrontPage = $this->getParam('blockonfrontpage');
        $showIntroduction = $this->getParam('showintroduction');
        $introduction = $this->getParam('introduction');
        $numitems = $this->getParam('numitems');
        $othernum = $this->getParam('othernum', 10);
        $rssFeeds = $this->getParam('rssfeeds');
        $socialBookmarks = $this->getParam('socialbookmarks');

        if ($numitems == 'other') {
            $numitems = $othernum;
        }

        if (!is_numeric($numitems)) {
            $numitems = 10;
        }

        $this->objNewsCategories->updateAdvancedCategory($item['itemvalue'], $name, $defaultSticky, $itemsOrder, $categoryType, $introduction, $showIntroduction, $blockOnFrontPage, $numitems, $rssFeeds, $socialBookmarks);

        $this->objNewsMenu->updateCategory($id, $name);

        $this->objNewsStories->serializeStoryOrder($item['itemvalue'], str_replace('_', ' ', $itemsOrder));

        $returnAction = $this->getParam('returnaction');

        if ($returnAction == 'managecategories') {
            return $this->nextAction('managecategories', array('message' => 'categoryupdated'));
        } else {
            return $this->nextAction('viewcategory', array('id' => $item['itemvalue'], 'message' => 'categoryupdated'));
        }
    }

    /**
     *
     *
     */
    private function __addstory() {
        $this->setVar('mode', 'add');
        $this->setVar('ENABLE_PROTOTYPE', TRUE); 
        $categories = $this->objNewsCategories->getCategories('categoryname');
        $this->setVarByRef('categories', $categories);

        if (count($categories) == 0) {
            return 'nocategories.php';
        } else {
            return 'addeditstory.php';
        }
    }

    /**
     *
     *
     */
    private function __savestory() {
        $storyTitle = $this->getParam('storytitle');
        $storyDate = $this->getParam('storydate');
        $storyExpiryDate = $this->getParam('storyexpirydate');
        $storyCategory = $this->getParam('storycategory');
        $storyLocation = $this->getParam('location');
        $storyText = $this->getParam('storytext');
        $storySource = $this->getParam('storysource');
        $storyImage = $this->getParam('imageselect');
        $sticky = $this->getParam('sticky');

        $tags = $this->getParam('storytags');
        $keyTags = array($this->getParam('keytag1'), $this->getParam('keytag2'), $this->getParam('keytag3'));

        $publishdate = $this->getParam('publishon');

        if ($publishdate == 'now') {
            $publishdate = strftime('%Y-%m-%d %H:%M:%S', mktime());
        } else {
            $publishdate = $this->getParam('storydatepublish') . ' ' . $this->getParam('time');
        }

        $storyId = $this->objNewsStories->addStory(
                $storyTitle,
                $storyDate,

                $storyCategory,
                $storyLocation,
                $storyText,
                $storySource,
                $storyImage, $tags, $keyTags, $publishdate, $sticky,$storyExpiryDate);


        $category = $this->objNewsCategories->getCategory($storyCategory);

        if ($category != FALSE) {
            $this->objNewsStories->serializeStoryOrder($storyCategory, str_replace('_', ' ', $category['itemsorder']));
        }

        return $this->nextAction('viewstory', array('id' => $storyId));
    }

    /**
     *
     *
     */
    private function __viewstory() {
        $id = $this->getParam('id');
        $this->setLayoutTemplate(NULL);
        $story = $this->objNewsStories->getStory($id);
        // If story does not exist
        if ($story == FALSE) {
            
            return $this->nextAction('home', array('error' => 'nostory'));
        } else {

            // Get Category
            $category = $this->objNewsCategories->getCategory($story['storycategory']);
            // Check that category exists
            if ($category == FALSE) {
                return $this->nextAction('home', array('error' => 'nostory'));
            } else {

                $this->setVarByRef('currentCategory', $category['id']);

                // Check whether story is available to be viewed
                if (($story['dateavailable'] > strftime('%Y-%m-%d %H:%M:%S', mktime())) && !$this->isValid('viewfuturestory')) {
                    return $this->nextAction('home', array('error' => 'nostory'));
                } else {

                    $sectionLayout = $this->getObject('section_' . $category['itemsview']);
                    $this->setVarByRef('content', $sectionLayout->renderPage($story, $category));
                    $comments = $this->objComments->getStoryComments($id);
                    $this->setVarByRef('comments', $comments);
                    $this->setVarByRef('story', $story);
                    $this->setVarByRef('category', $category);

                    $menuId = $this->objNewsMenu->getIdCategoryItem($story['storycategory']);
                    $this->setVarByRef('menuId', $menuId);

                    //Load Blocks for Page
                    $this->objNewsBlocks->getBlocksAndSendToTemplate('story', $id);
                    $this->setVar('pageType', 'story');
                    $this->setVar('pageTypeId', $id);

                    $rightContent = '';
                    $rightContent .= $this->objNewsStories->getRelatedStoriesFormatted($story['id'], $story['storydate'], $story['datecreated']);
                    $rightContent .= $this->objKeywords->getStoryKeywordsBlock($story['id']);

                    // Send to Layout Template
                    $this->setVar('rightContent', $rightContent);
                    // Load Blocks
                    $rightBlocks = $this->objNewsBlocks->getBlocksAndSendToTemplate('story', 'story', 'right');
                    $leftBlocks = $this->objNewsBlocks->getBlocksAndSendToTemplate('story', 'story', 'left');
                    $this->setVar('rightBlocks', $rightBlocks);
                    $this->setVar('leftBlocks', $leftBlocks);
                    return 'viewstory.php';
                }
            }
        }
    }

    /**
     * Method to archive a story
     *
     */
    private function __archivestory() {
        $id = $this->getParam('id');
        $story = $this->objNewsStories->getStory($id);

        //First adding the story into archives
        $this->setVar('mode', 'archive');


        $categories = $this->objNewsCategories->getCategories('categoryname');
        $this->setVarByRef('categories', $categories);
        if (count($categories) == 0) {
            return 'nocategories.php';
        }


        if ($story == FALSE) {
            return $this->nextAction('home', array('error' => 'nostorytodelete'));
        } else {
            $storyTitle = $story['storytitle'];
            $storyDate = $story['storydate'];
            $storyCategory = $story['storycategory'];
            $storyLocation = $story['location'];
            $storyText = $story['storytext'];
            $storySource = $story['storysource'];
            $storyImage = $story['imageselect'];
            $sticky = $story['sticky'];
            $tags = $this->objTags->getStoryTags($id);
            //$keyTags =  array($tags['keytag1'], $tags['keytag2'], $tags['keytag3']);
            $keyTags = $this->objKeywords->getStoryKeywords($id);

            //print_r($tags);
            //die();
            $publishdate = $story['dateavailable'];

            if ($publishdate == 'now') {
                $publishdate = strftime('%Y-%m-%d %H:%M:%S', mktime());
            } else {
                $publishdate = $this->getParam('storydatepublish') . ' ' . $this->getParam('time');
            }

            $status = 'archive';

            $storyId = $this->objArchivesStories->addStory($storyTitle, $storyDate, $storyCategory, $storyLocation, $storyText, $storySource, $storyImage, $tags, $keyTags, $publishdate, $sticky, $status);


            $category = $this->objNewsCategories->getCategory($storyCategory);

            if ($category != FALSE) {
                $this->objArchivesStories->serializeStoryOrder($storyCategory, str_replace('_', ' ', $category['itemsorder']));
            }

            // return $this->nextAction('viewstory', array('id'=>$storyId));
        }
        //------------------END adding archives----------------------
        //Now deleting from the active table
        $id = $this->getParam('id');

        $story = $this->objNewsStories->getStory($id);
        //print_r($story);
        //die();
        if ($story == FALSE) {
            return $this->nextAction('home', array('error' => 'nostorytodelete'));
        } else {
            $mode = 'archive';
            $this->setVarByRef('mode', $mode);
            $this->setVarByRef('story', $story);

            $randomNumber = rand(0, 50000);
            $this->setSession('deletestory_' . $story['id'], $randomNumber);
            $this->setVarByRef('deleteValue', $randomNumber);

            return 'deletestory.php';
        }
        //----------END deleting news story---------------
    }

    /**
     * Method to list archived stories
     *
     */
    private function __viewarchives() {
        return 'viewarchives.php';
    }

    /**
     * Method to restore archived stories
     *
     */
    private function __restorearchives() {

        $id = $this->getParam('id');
        $restore_story = $this->objArchivesStories->getArchivedStories($id);
        $this->setVar('mode', 'restore');
        $this->setVarByRef('archive', $restore_story);

        if ($restore_story == FALSE) {
            return $this->nextAction('home', array('error' => 'nostory'));
        } else {
            //$storyTitle = $restore_story['storytitle'];
//print_r($restore_story);
//echo "@@####".$storyTitle."@@####";
//echo "@@####".$restore_story['storytitle']."@@####";
//die();
            foreach ($restore_story as $story) {
                $storyTitle = $story['storytitle'];
                $storyDate = $story['storydate'];
                $storyCategory = $story['storycategory'];
                $storyLocation = $story['storylocation'];
                $storyText = $story['storytext'];
                $storySource = $story['storysource'];
                $storyImage = $story['storyimage'];
                $sticky = $story['sticky'];
                //print_r($story);
//            die();
                $tags = $this->objArchivesTags->getStoryTags($id);
                //$keyTags =  array($tags['keytag1'], $tags['keytag2'], $tags['keytag3']);
                $keyTags = $this->objArchivesKeywords->getStoryKeywords($id);
                //$publishdate = $story['publishon'];
                $publishdate = strftime('%Y-%m-%d %H:%M:%S', mktime());
                $status = 'restored';
//	$this->objNewsMenu->updateText($id, $text);

                $this->objArchivesStories->updateStatus($id, $status);
                //print_r($storyCategory);
                //die();
                $storyId = $this->objNewsStories->addStory($storyTitle, $storyDate, $storyCategory, $storyLocation, $storyText, $storySource, $storyImage, $tags, $keyTags, $publishdate, $sticky);
            }
            return 'viewarchives.php';
        }
    }

    /**
     * Method to edit a story
     *
     */
    private function __editstory() {
        $id = $this->getParam('id');

        $story = $this->objNewsStories->getStory($id);
        $this->setVar('ENABLE_PROTOTYPE', TRUE);
        if ($story == FALSE) {
            return $this->nextAction('home', array('error' => 'nostory'));
        } else {
            $this->setVar('mode', 'edit');

            $this->setVarByRef('story', $story);



            $categories = $this->objNewsCategories->getCategories('categoryname');
            $this->setVarByRef('categories', $categories);

            $keywords = $this->objKeywords->getStoryKeywords($id);
            $this->setVarByRef('keywords', $keywords);

            $tags = $this->objTags->getStoryTags($id);
            $this->setVarByRef('tags', $tags);

            return 'addeditstory.php';
        }
    }

    /**
     * Method to update a story
     *
     */
    private function __updatestory() {
        //echo '<pre>';
        //print_r($_POST);

        $id = $this->getParam('id');
        $storyTitle = $this->getParam('storytitle');
        $storyDate = $this->getParam('storydate');
        $storyCategory = $this->getParam('storycategory');
        $storyLocation = $this->getParam('location');
        $storyText = $this->getParam('storytext');
        $storySource = $this->getParam('storysource');
        $storyImage = $this->getParam('imageselect');
        $sticky = $this->getParam('sticky');
        $storyExpiryDate = $this->getParam('storyexpirydate');

        $publishdate = $this->getParam('publishon');

        if ($publishdate == 'now') {
            $publishdate = strftime('%Y-%m-%d %H:%M:%S', mktime());
        } else {
            $publishdate = $this->getParam('storydatepublish') . ' ' . $this->getParam('time');
        }

        $tags = $this->getParam('storytags');
        $keyTags = array($this->getParam('keytag1'), $this->getParam('keytag2'), $this->getParam('keytag3'));

        $result = $this->objNewsStories->updateStory($id, $storyTitle, $storyDate, $storyCategory, $storyLocation, $storyText, $storySource, $storyImage, $tags, $keyTags, $publishdate, $sticky,$storyExpiryDate);

        $category = $this->objNewsCategories->getCategory($storyCategory);

        if ($category != FALSE) {
            $this->objNewsStories->serializeStoryOrder($storyCategory, str_replace('_', ' ', $category['itemsorder']));
        }

        return $this->nextAction('viewstory', array('id' => $id));
    }

    /**
     *
     *
     *
      $tags = $this->objTags->getStoryTags($id);/
      private function __ajaxkeywords()
      {
      $start = $this->getParam($this->getParam('tag'));

      $keywords = $this->objKeywords->getAjaxKeywords($start);

      if (count($keywords) > 0) {
      echo '<ul>';
      $counter = 1;
      foreach ($keywords as $keyword)
      {
      echo '<li id="'.$counter.'">'.$keyword['keyword'].'</li>';
      $counter++;
      }
      echo '</ul>';
      }
      }


      /**
     *
     *
     */
    private function __themecloud() {
        return 'themecloud.php';
    }

    /**
     *
     *
     */
    private function __tagcloud() {

    }

    /**
     *
     *
     */
    private function __viewtimeline() {
        return 'viewtimeline.php';
    }

    /**
     *
     *
     */
    private function __generatetimeline() {
        header('Content-type: text/xml');
        echo $this->objNewsStories->generateTimeline();
    }

    /**
     *
     *
     */
    private function __viewbykeyword() {
        $keyword = $this->getParam('id');

        $this->setLayoutTemplate('2collayout.php');

        $stories = $this->objNewsStories->getKeywordStories($keyword);

        if (count($stories) == 0) {
            return $this->nextAction('home');
        } else {
            $this->setVarByRef('keyword', $keyword);
            $this->setVarByRef('stories', $stories);

            return 'viewbykeyword.php';
        }
    }

    /**
     *
     *
     */
    private function __generatekeywordtimeline() {
        $keyword = $this->getParam('id');

        header('Content-type: text/xml');
        echo $this->objNewsStories->generateKeywordTimeline($keyword);
    }

    /**
     *
     *
     */
    private function __viewcategory() {
        $id = $this->getParam('id');

        $this->setLayoutTemplate('blocks_layout_tpl.php');

        $category = $this->objNewsCategories->getCategory($id);

        if ($category == FALSE) {
            $this->objNewsCategories->deleteCategory($id);
            $menuId = $this->objNewsMenu->getIdCategoryItem($id);
            $this->objNewsMenu->deleteCategory($menuId);

            return $this->nextAction(NULL, array('error' => 'categorydoesnotexist'));
        } else {

            //Load Blocks for Page
            $this->objNewsBlocks->getBlocksAndSendToTemplate('category', $id);
            $this->setVar('pageType', 'category');
            $this->setVar('pageTypeId', $id);

            $menuId = $this->objNewsMenu->getIdCategoryItem($id);
            $this->setVarByRef('menuId', $menuId);
            $sectionLayout = $this->getObject('section_' . $category['itemsview']);
            $this->setVarByRef('category', $category);
            $this->setVarByRef('currentCategory', $category['id']);
            $this->setVarByRef('content', $sectionLayout->renderSection($category));
            // Load Blocks
            $rightBlocks = $this->objNewsBlocks->getBlocksAndSendToTemplate('story', 'story', 'right');
            $leftBlocks = $this->objNewsBlocks->getBlocksAndSendToTemplate('story', 'story', 'left');
            $this->setVar('rightBlocks', $rightBlocks);
            $this->setVar('leftBlocks', $leftBlocks);
            return 'viewcategory.php';
        }
    }

    /**
     *
     *
     */
    private function __showmap() {
        $this->objBuildMap = $this->getObject('simplebuildmap', 'simplemap');

        $bodyParams = "onunload=\"GUnload()\"";
        $this->setVarByRef('bodyParams', $bodyParams);

        //Read the API key from sysconfig
        $apiKey = $this->objBuildMap->getApiKey();

        $hScript = "<script src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;key="
                . $apiKey . "\" type=\"text/javascript\"></script>";
        //Add the local script to the page header
        $this->appendArrayVar('headerParams', $hScript);



        return 'showmap.php';
    }

    /**
     *
     *
     */
    private function __generatekml() {
        header('Content-type: text/javascript');
        echo $this->objNewsStories->generateNewsSmap();
    }

    /**
     *
     *
     */
    private function __topstoriesfeed() {
        echo $this->objNewsStories->topStoriesFeed();
    }

    /**
     *
     *
     */
    private function __savecomment() {
        $storyId = $this->getParam('id');
        $name = $this->getParam('name');
        $email = $this->getParam('email');
        $comment = $this->getParam('comments');

        $this->objComments->addComment($storyId, $name, $email, $comment);

        return $this->nextAction('viewstory', array('id' => $storyId));
    }

    /**
     *
     *
     */
    private function __checklocation() {
        $location = $this->getParam('location');

        $objGeonames = $this->getObject('dbgeonames', 'geonames');
        $objCountries = $this->getObject('countries', 'utilities');

        $results = $objGeonames->getLocation($location);

        $this->loadClass('radio', 'htmlelements');

        echo '<strong>Results:</strong> ';

        if (count($results) > 0) {
            echo '<br />';

            $radio = new radio('location');
            $radio->setBreakSpace('<br />');


            foreach ($results as $result) {
                $locationName = $result['name'];

                if ($result['admin1name'] != '') {
                    $locationName .= ', ' . $result['admin1name'];
                }

                $locationName .= ', ' . $objCountries->getCountryName($result['countrycode']);

                $radio->addOption($result['geonameid'], $locationName);
            }

            $radio->setSelected($results[0]['geonameid']);

            echo $radio->show();
        } else {
            $objCurl = $this->getObject('curl', 'utilities');
            $data = $objCurl->exec('http://ws.geonames.org/search?name_equals=' . urlencode($location) . '&style=full&maxRows=20&fclass=P');
            $xml = simplexml_load_string($data);

            if (!$xml) {
                echo 'No results for <em>' . $location . '</em><br />';
            } else {
                echo '<p><span class="confirm">' . $this->objLanguage->languageText('mod_geonames_file', 'geonames', 'Results from Webservice') . '</span></p>';

                if (isset($xml->geoname)) {
                    $radio = new radio('location');
                    $radio->setBreakSpace('<br />');

                    foreach ($xml->geoname as $geoname) {
                        $objGeonames->insertFromXML($geoname);



                        $locationName = $geoname->name;

                        if ($geoname->adminName1 != '') {
                            $locationName .= ', ' . $geoname->adminName1;
                        }

                        $locationName .= ', ' . $geoname->countryName;

                        $radio->addOption($geoname->geonameId . '', $locationName);
                    }

                    echo $radio->show();
                } else {
                    echo '<p class="error">' . $this->objLanguage->languageText('mod_geonames_noresultsfromwebservice', 'geonames', 'No Results from the Geonames Webservice') . '<br />' . $this->objLanguage->languageText('mod_geonames_possiblespellingerror', 'geonames', 'Possibly a spelling error. Please try again') . '</p>';
                }
            }
            //echo 'No results for <em>'.$location.'</em><br />';
        }

        $results2 = $objGeonames->getLocationsStartingWith($location);

        if (count($results2) > 0) {
            echo '<br /><strong>Other Possible Results:</strong><br />';

            $divider = '';

            foreach ($results2 as $result) {
                echo $divider . '<a href="javascript:ck(\'' . addslashes($result['name']) . '\')">' . $result['name'] . '</a>';
                $divider = ', ';
            }
        }
    }

    /**
     *
     *
     */
    function __movecategoryup() {
        $id = $this->getParam('id');

        $result = $this->objNewsMenu->moveItemUp($id);

        $result = $result ? 1 : 0;

        return $this->nextAction('managecategories', array('id' => $id, 'act' => 'movedup', 'result' => $result));
    }

    /**
     *
     *
     */
    function __movecategorydown() {
        $id = $this->getParam('id');

        $result = $this->objNewsMenu->moveItemDown($id);

        $result = $result ? 1 : 0;

        return $this->nextAction('managecategories', array('id' => $id, 'act' => 'movedup', 'result' => $result));
    }

    /**
     *
     *
     */
    function __deletedivider() {
        $id = $this->getParam('id', 'nothing');

        $this->objNewsMenu->deleteDivider($id);

        return $this->nextAction('managecategories');
    }

    /**
     *
     *
     */
    function __deletetext() {
        $id = $this->getParam('id', 'nothing');

        $this->objNewsMenu->deleteText($id);

        return $this->nextAction('managecategories');
    }

    /**
     *
     *
     */
    function __deleteurl() {
        $id = $this->getParam('id', 'nothing');

        $this->objNewsMenu->deleteUrl($id);

        return $this->nextAction('managecategories');
    }

    /**
     *
     *
     */
    function __deletemodule() {
        $id = $this->getParam('id', 'nothing');

        $this->objNewsMenu->deleteModule($id);

        return $this->nextAction('managecategories');
    }

    /**
     *
     *
     */
    function __deleteblock() {
        $id = $this->getParam('id', 'nothing');

        $this->objNewsMenu->deleteBlock($id);

        return $this->nextAction('managecategories');
    }

    /**
     *
     *
     */
    function __editmenuitem() {
        $id = $this->getParam('id', 'nothing');

        $item = $this->objNewsMenu->getItem($id);

        if ($item == FALSE) {
            return $this->nextAction('managecategories', array('error' => 'unknownitem'));
        } else {

            $this->setVarByRef('item', $item);
            $this->setVarByRef('id', $id);

            switch ($item['itemtype']) {
                case 'text':
                    return 'managecategories_text.php';
                case 'category':
                    return 'managecategories_category.php';
                case 'divider':
                    return $this->nextAction('managecategories', array('error' => 'cannoteditdivider'));
                default:
                    return $this->nextAction('managecategories', array('error' => 'unknowntype'));
            }
        }
    }

    /**
     *
     *
     */
    function __updatemenu_text() {
        $text = $this->getParam('text');
        $id = $this->getParam('id');

        $this->objNewsMenu->updateText($id, $text);

        return $this->nextAction('managecategories');
    }

    /**
     *
     *
     */
    function __liststories() {
        $id = $this->getParam('id');

        $category = $this->objNewsCategories->getCategory($id);

        if ($category == FALSE) {

        } else {
            $this->objNewsStories->serializeCategoryOrder($category['id'], str_replace('_', ' ', $category['itemsorder']));

            $this->setVarByRef('category', $category);
            $stories = $this->objNewsStories->getCategoryStories($category['id'], str_replace('_', ' ', $category['itemsorder']), TRUE);

            $this->setVarByRef('stories', $stories);

            $menuId = $this->objNewsMenu->getIdCategoryItem($id);
            $this->setVarByRef('menuId', $menuId);

            $this->setVarByRef('message', $this->______categoryupdatemessages());

            return 'liststories.php';
        }
    }

    private function ______categoryupdatemessages() {
        switch ($this->getParam('result')) {
            default: $message = '';
                break;
            case 'storydeleted':
                $message = '<p><span class="confirm">' . $this->getParam('title') . ' story has been deleted</span></p>';
                break;
        }

        return $message;
    }

    /**
     *
     *
     */
    function __movepageup() {
        $id = $this->getParam('id');

        $story = $this->objNewsStories->getStory($id);

        if ($story == FALSE) {
            return $this->nextAction('home', array('error' => 'nostory'));
        } else {
            $result = $this->objNewsStories->moveItemUp($id);

            $result = $result ? 1 : 0;

            return $this->nextAction('liststories', array('id' => $story['storycategory'], 'act' => 'movedup', 'result' => $result));
        }
    }

    /**
     *
     *
     */
    function __movepagedown() {
        $id = $this->getParam('id');

        $story = $this->objNewsStories->getStory($id);

        if ($story == FALSE) {
            return $this->nextAction('home', array('error' => 'nostory'));
        } else {
            $result = $this->objNewsStories->moveItemDown($id);

            $result = $result ? 1 : 0;

            return $this->nextAction('liststories', array('id' => $story['storycategory'], 'act' => 'moveddown', 'result' => $result));
        }
    }

    /**
     *
     *
     */
    function __search() {
        $query = $this->getParam('q');


        $objLucene = $this->newObject('searchresults');
        $searchResults = $objLucene->show($query);
        // echo $searchResults; die();
        $searchResults = str_replace('&', '&amp;', $searchResults);
        $searchResults = str_replace(urlencode('[HIGHLIGHT]'), urlencode($query), $searchResults);

        $this->setVarByRef('searchResults', $searchResults);
        $this->setVarByRef('searchQuery', $query);


        return 'searchresults.php';
    }

    /**
     *
     *
     */
    function __deletestory() {
        $id = $this->getParam('id');

        $story = $this->objNewsStories->getStory($id);

        if ($story == FALSE) {
            return $this->nextAction('home', array('error' => 'nostorytodelete'));
        } else {
            $this->setVarByRef('story', $story);

            $randomNumber = rand(0, 50000);
            $this->setSession('deletestory_' . $story['id'], $randomNumber);
            $this->setVarByRef('deleteValue', $randomNumber);

            return 'deletestory.php';
        }
    }

    /**
     *
     *
     *
     */
    function __deletestoryconfirm() {
        $id = $this->getParam('id');
        $deletevalue = $this->getParam('deletevalue');
        $confirm = $this->getParam('confirm');

        if (($id != '') && ($deletevalue != '') && ($confirm == 'yes') && ($deletevalue == $this->getSession('deletestory_' . $this->getParam('id')))) {
            $story = $this->objNewsStories->getStory($id);

            if ($story == FALSE) {
                return $this->nextAction(NULL, array('couldnotdeletestorynotexist'));
            } else {
                $this->objComments->deleteStoryComments($id);

                $this->setSession('deletestory_' . $story['storyid'], NULL);

                $this->objNewsStories->deleteStory($id);

                return $this->nextAction('liststories', array('id' => $story['storycategory'], 'result' => 'storydeleted', 'title' => $story['storytitle']));
            }
        } else {
            return $this->nextAction('deletestory', array('id' => $id, 'error' => 'deletenotconfirmed'));
        }
    }

    function __deletecategory() {
        $id = $this->getParam('id');

        $item = $this->objNewsMenu->getItem($id);

        if ($item == FALSE) {
            return $this->nextAction('managecategories', array('error' => 'nocategorytodelete'));
        }

        $category = $this->objNewsCategories->getCategory($item['itemvalue']);

        if ($category == FALSE) {
            return $this->nextAction('managecategories', array('error' => 'nocategorytodelete'));
        } else {

            $numItems = $this->objNewsStories->getNumCategoryStories($item['itemvalue']);

            if ($numItems > 0) {
                return $this->nextAction('liststories', array('id' => $category['id'], 'error' => 'cannotdeletecategorywithstories'));
            }

            $this->setVarByRef('item', $item);
            $this->setVarByRef('category', $category);

            $randomNumber = rand(0, 50000);
            $this->setSession('deletecategory_' . $category['id'], $randomNumber);
            $this->setVarByRef('deleteValue', $randomNumber);

            return 'deletecategory.php';
        }
    }

    function __deletecategoryconfirm() {
        $id = $this->getParam('id');
        $deletevalue = $this->getParam('deletevalue');
        $confirm = $this->getParam('confirm');
        $category = $this->getParam('category');

        $item = $this->objNewsMenu->getItem($id);

        if ($item == FALSE) {
            return $this->nextAction('managecategories', array('error' => 'nocategorytodelete'));
        }

        $category = $this->objNewsCategories->getCategory($item['itemvalue']);

        if ($category == FALSE) {
            return $this->nextAction('managecategories', array('error' => 'nocategorytodelete'));
        }

        if (($id != '') && ($item != '') && ($item != '') && ($deletevalue != '') && ($confirm == 'yes') && ($deletevalue == $this->getSession('deletecategory_' . $this->getParam('category')))) {

            $numItems = $this->objNewsStories->getNumCategoryStories($item['itemvalue']);

            if ($numItems > 0) {
                return $this->nextAction('liststories', array('id' => $category['id'], 'error' => 'cannotdeletecategorywithstories'));
            } else {
                $this->objNewsMenu->deleteCategory($id);
                $this->objNewsCategories->deleteCategory($category['id']);

                return $this->nextAction('managecategories', array('result' => 'categorydeleted', 'title' => $category['categoryname']));
            }
        } else {
            return $this->nextAction('deletecategory', array('id' => $id, 'error' => 'deletenotconfirmed'));
        }
    }

    /**
     * Method to render a block
     */
    protected function __renderblock() {
        if ($this->objUser->isAdmin()) {
            $blockId = $this->getParam('blockid');
            $side = $this->getParam('side');

            $block = explode('|', $blockId);


            $blockId = $side . '___' . str_replace('|', '___', $blockId);

            if ($block[0] == 'block') {
                $objBlocks = $this->getObject('blocks', 'blocks');
                echo '<div id="' . $blockId . '" class="block highlightblock">' . $objBlocks->showBlock($block[1], $block[2], NULL, 20, TRUE, FALSE) . '</div>';
            } if


            ($block[0] == 'dynamicblock') {
                echo '<div id="' . $blockId . '" class="block highlightblock">' . $this->objDynamicBlocks->showBlock($block[1]) . '</div>';
            } else {
                echo '';
            }
        }
    }

    /**
     * Method to add a block
     */
    protected function __addblock() {
        if ($this->objUser->isAdmin()) {
            $blockId = $this->getParam('blockid');
            $pageType = $this->getParam('pagetype');
            $pageId = $this->getParam('pageid');
            $side = $this->getParam('side');

            $block = explode('|', $blockId);

            if ($block[0] == 'block' || $block[0] == 'dynamicblock') {
                // Add Block
                $result = $this->objNewsBlocks->addBlock($blockId, $side, $pageType, $pageId, $block[2]);

                if ($result == FALSE) {
                    echo '';
                } else {
                    echo $result;
                }
            } else {
                echo '';
            }
        }
    }

    /**
     * Method to remove a context block
     */
    protected function __removeblock() {
        if ($this->objUser->isAdmin()) {
            $blockId = $this->getParam('blockid');

            $result = $this->objNewsBlocks->removeBlock($blockId);

            if ($result) {
                echo 'ok';
            } else {
                echo 'notok';
            }
        }
    }

    /**
     * Method to move a context block
     */
    protected function __moveblock() {
        if ($this->objUser->isAdmin()) {
            $blockId = $this->getParam('blockid');
            $pageType = $this->getParam('pagetype');
            $pageId = $this->getParam('pageid');
            $side = $this->getParam('side');
            $direction = $this->getParam('direction');

            if ($direction == 'up') {
                $result = $this->objNewsBlocks->moveBlockUp($blockId, $pageType, $pageId);
            } else {
                $result = $this->objNewsBlocks->moveBlockDown($blockId, $pageType, $pageId);
            }

            if ($result) {
                echo 'ok';
            } else {
                echo 'notok';
            }
        }
    }

}

?>