<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * The controller that extends the base controller for the cms module
 *
 * Note: 2006 12 16 - Converted to direct function access from action
 *   parameter, and cleaned up some very messy code -- D. Keats
 *
 * @package cms
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert
 * @author Wesley  Nitsckie
 * @author Warren Windvogel
 * @author Derek Keats
 *
 */
class cms extends controller {

    /**
     *
     * @var string object $_objContextCore A string to hold an instance of the contextcore object which ....
     * @access protected
     *
     */
    protected $_objContextCore;

    /**
     *
     * @var string object $_objSections A string to hold an instance of the sections
     * database access object from CMS admin
     * @access protected
     *
     */
    protected $_objSections;

    /**
     * @var string object $_objContent A string to hold an instance of
     * the Content object which ...
     * @access protected
     *
     */
    protected $_objContent;

    /**
     *
     * @var string object $_objUtils A string to hold an instance of the CMS
     * Utilities object
     * @access protected
     *
     */
    protected $_objUtils;

    /**
     *
     * @var string $contextCode A string to hold the contextCode for the
     * context that the user is in
     * @access protected
     *
     */
    protected $contextCode;

    /**
     *
     * @var string $inContextMode A string to hold the context code so that
     * we can call it by another name for no apparent reason or a reason known
     * only to the developer of this module who did not put in any explanation
     * anywhere.
     * @access protected
     *
     */
    protected $inContextMode;

    /**
     *
     * @var string object $_objUser A string to hold an instance of the user object
     * @access protected
     *
     */
    protected $_objUser;

    /**
     *
     * @var string $action A string to hold the value of the action parameter
     *  retrieved from the querystring
     * @access public
     *
     */
    public $action;

    /**
     * Feed creator object
     *
     * @var object
     */
    public $objFeedCreator;

    /**
     * Configuration object
     *
     * @var object
     */
    public $objConfig;
    public $objRPC;

    /**
     *
     * The standard init method to initialise the cms object and assign some of
     * the objects used in all action derived methods.
     *
     * @access public
     *
     */
    public function init() {
        try {
            $this->_objPreview = $this->getObject('dbcontentpreview', 'cmsadmin');
            // instantiate the database object for sections
            $this->_objSections = $this->getObject('dbsections', 'cmsadmin');
            $this->_objSecurity = $this->getObject('dbsecurity', 'cmsadmin');

            $this->_objSimpleTree = $this->getObject('simpletreemenu', 'cmsadmin');

            $this->objLayout = $this->getObject('cmslayouts', 'cms');
            $this->rss = $this->getObject('dblayouts', 'cmsadmin');
            //feed creator subsystem
            $this->objFeedCreator = &$this->getObject('feeder', 'feed');
            // instantiate the database object for content
            $this->_objContent = $this->getObject('dbcontent', 'cmsadmin');
            // instantiate the object for CMS utilities
            $this->_objUtils = $this->getObject('cmsutils', 'cmsadmin');
            // instantiate the context object so we can get where the contex the user is in
            $this->_objContext = $this->getObject('dbcontext', 'context');
            // instantiate the user object so we can retrieve user information
            $this->_objUser = $this->getObject('user', 'security');
            //config object
            $this->objConfig = $this->getObject('altconfig', 'config');
            //Create an instance of the language object for text rendering
            $this->objLanguage = $this->getObject('language', 'language');
            //Create an instance of the sysconfig object to get module configurations
            $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            //Get configuration on show menu
            $this->disablemenu = $this->objSysConfig->getValue('disablemenu', 'cms');
            //Create an instance of the modules object from modulecatalogue
            $objModule = $this->getObject('modules', 'modulecatalogue');
            //Use the modules object instantiated above to see if context is registered
            if ($objModule->checkIfRegistered('context')) {
                //If context is registered, assign the current context value to
                //   both the contextCode property and the inContextMode property
                //   of this object because.........
                $this->inContextMode = $this->_objContext->getContextCode();
                $this->contextCode = $this->inContextMode;
            } else {
                //If conmtext is not registered then assign boolean FALSE to
                //   the inContextMode property of this object
                $this->inContextMode = FALSE;
            }
            // xmlrpc api class
            $this->objRPC = $this->getObject('cmsrpcapi');
            //Get the activity logger class and log this module call
            $objLog = $this->getObject('logactivity', 'logger');
            $objLog->log();
        } catch (customException $e) {
            customException::cleanUp();
            exit;
        }
    }

    /**
     *
     * This is a method that overrides the parent class to stipulate whether
     * the current module requires login. Having it set to false gives public
     * access to this module including all its actions.
     *
     * @access public
     * @return bool FALSE
     */
    public function requiresLogin() {
        return FALSE;
    }

    /**
     *
     * A standard method to handle actions from the querystring.
     * The dispatch function converts action values to function
     * names, and then calls those functions to perform the action
     * that was specified.
     *
     * @access public
     * @return string The results of the method denoted by the action
     *   querystring parameter. Usually this will be a template populated
     *   with content.
     *
     */
    public function dispatch() {
        //Create a local variable for whether the user is logged in
        $isLoggedIn = $this->_objUser->isLoggedIn();
        //$this->setLayoutTemplate('cms_layout_tpl.php');
        //Get action from query string and set default to view
        //  and assign the action to a property of this object
        $this->action = $this->getParam('action', 'home');
        $this->setVar('pageSuppressXML', TRUE);
        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        try {

            switch ($this->action) {
                case 'makepdf':

                    $sectionid = $this->getParam('sectionid');
                    //go and fetch the page in question from the db
                    $data = $this->_objContent->getContentPage($this->getParam('id'));

                    //create the pdf and send it out

                    $header = stripslashes($data['title']);

                    $body = stripslashes($data['body']);

                    $pagedate = $data['created'];

                    //put it all together
                    //get the pdfmaker classes
                    $objPdf = $this->getObject('fpdfwrapper', 'pdfmaker');

                    $text = $header . "  " . $pagedate . "\r\n" . html_entity_decode(strip_tags($body));
                    $objPdf->simplePdf($text);

                    $method = $this->_getMethod();

                    break;

                default:
                    $method = $this->_getMethod();
                    break;
            }
        } catch (Exception $e) {
            throw customException($e->getMessage());
            //customException::cleanUp();
            exit;
        }
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     * A method that corresponds to the previewcontent action parameter
     * from the querystring. It returns the formatted full page of content
     * text for a particular content item for preview purposes.
     *
     * @access private
     * @return string The populated cms_content_tpl.php template
     *
     */
    private function _previewcontent() {
        if ($this->disablemenu == 'true') {
            $this->setVar('pageSuppressToolbar', TRUE);
        }
        $this->setLayoutTemplate('cms_layout_tpl.php');
        $this->bbcode = $this->getObject('washout', 'utilities');

        $fromadmin = $this->getParam('fromadmin', FALSE);
        $sectionId = $this->getParam('sectionid', NULL);
        $siteTitle = $this->getParam('title');
        //$rss = $this->rss->getUserRss($sectionId);
        //$this->setVar('rss', $rss);
        $content = $this->objLayout->showBody(true);
        $content = $this->bbcode->parseText($content);
        //var_dump($content); exit;
        $this->setVar('pageTitle', $siteTitle);
        $this->setVar('content', $content);
        $this->setVar('sectionId', $sectionId);
        $this->setVar('fromadmin', $fromadmin);
        return 'cms_content_tpl.php';
    }

    /**
     * This method will return the child items for the particular
     * section for use the the jquery Simple Tree Menu (Ajax)
     *
     * @access private
     * @return string The populated cms_section_tpl.php template
     *
     */
    private function _getMenuChildNodes() {
        $this->setPageTemplate('');

        //Retrieve the section id from the querystring
        $id = $this->getParam('id');
        //var_dump($id);
        $content = $this->_objSimpleTree->getMenuChildNodes($id, TRUE);
        //var_dump($content);

        $this->setVar('content', $content);

        return "menu_child_node_tpl.php";
    }

    /**
     * A method that corresponds to the showsection action parameter
     * from the querystring. It returns the formatted section text for
     * display in the template.
     *
     * @access private
     * @return string The populated cms_section_tpl.php template
     *
     */
    private function _showsection() {
        if ($this->disablemenu == 'true') {
            $this->setVar('pageSuppressToolbar', TRUE);
        }
        $this->setLayoutTemplate('cms_layout_tpl.php');
        //Retrieve the section id from the querystring
        $sectionId = $this->getParam('id');
        //If the section id is not null, get the section and set the
        // site title, otherwise set the site title to empty.
        if ($sectionId != '') {
            $section = $this->_objSections->getSection($sectionId);
            $siteTitle = $section['title'];
        } else {
            $siteTitle = '';
        }
        //Set the page title to be equal to the siteTitle from the section
        $this->setVarByRef('pageTitle', $siteTitle);
        //We need the two if statements because pageTitle needs to be set first
        if ($sectionId != '') {
            $this->bbcode = $this->getObject('washout', 'utilities');
            $content = $this->objLayout->showSection('cms', $section);
            $content = $this->bbcode->parseText($content);
            $this->setVar('content', $content);
        } else {
            $this->setVar('content', '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_cms_novisiblesections', 'cms') . '</div>');
        }
        //Return the populated section template
        return 'cms_section_tpl.php';
    }

    /**
     * Method to setup the mail to a friend functionality
     * The method sets up the mail object
     * and sends email about the article the user finds interesting
     * @access private
     */
    private function _mail2friend() {
        $pageid = $this->getParam('id');
        $sectionId = $this->getParam('sectionid', NULL);
        $emailadd = $this->getParam('emailadd');
        $emailadd = explode(",", $emailadd);
        foreach ($emailadd as $emails) {
            $trimmed[] = trim($emails);
        }
        $emailadd = $trimmed;
        $message = $this->getParam('msg');
        $sendername = $this->getParam('sendername');

        if (empty($emailadd[0])) {
            $m2fdata = array('id' => $pageid);
            $this->setVarByRef('m2fdata', $m2fdata);
            //show the form
            return 'mail2friend_tpl.php';
        } else {
            //get the post from the page id
            $postcontent = $this->_objContent->getContentPage($pageid);
            //ok we have the content, lets parse for the [img] bbcode tags and replace them with real imgsrc
            preg_match_all('/\[img\](.*)\[\/img\]/U', $postcontent['body'], $matches, PREG_PATTERN_ORDER);
            unset($matches[0]);
            $mcount = 0;
            foreach ($matches as $match) {
                $postcontent['body'] = preg_replace('/\[img\](.*)\[\/img\]/U', "<img src='" . @$match[$mcount] . "'/>", $postcontent['body']);
                $mcount++;
            }
            //thump together an email string (this must be html email as the post is html
            $objMailer = $this->getObject('email', 'mail');
            //munge together the bodyText...
            $bodyText = $this->objLanguage->languageText("mod_cms_yourfriend", "cms") . ", " . $sendername . ", " .
                    $this->objLanguage->languageText("mod_cms_interestedin", "cms") . ": <br /> " .
                    "<a href='" . $this->uri(array('action' => 'showfulltext', 'id' => $pageid, 'sectionid' => $sectionId), 'cms') . "'>" . $this->uri(array('action' => 'showfulltext', 'id' => $pageid, 'sectionid' => $sectionId), 'cms') . "</a>";
            $bodyText .= "<br /><br />";
            if (!empty($message)) {
                $bodyText .= $this->objLanguage->languageText("mod_cms_additionalcomments", "cms") . ": <br />";
                $bodyText .= $message . "<br /><br />";
            }
            $bodyText .= stripslashes($postcontent['created']);
            $bodyText .= "<br /><br />";
            $bodyText .= stripslashes($postcontent['body']);
            $objMailer->setValue('IsHTML', TRUE);
            $objMailer->setValue('to', $emailadd);
            $objMailer->setValue('from', 'noreply@uwc.ac.za');
            $objMailer->setValue('fromName', $this->objLanguage->languageText("mod_cms_email2ffromname", "cms"));
            $objMailer->setValue('subject', $this->objLanguage->languageText("mod_cms_email2fsub", "cms"));
            $objMailer->setValue('body', $bodyText);
            $objMailer->send(TRUE);
            $this->nextAction('');
        }
    }

    /**
     * A method that corresponds to the showfulltext action parameter
     * from the querystring. It returns the formatted full page of content
     * text for a particular content item.
     *
     * @access private
     * @return string The populated cms_content_tpl.php template
     *
     */
    private function _showfulltext() {
        if ($this->disablemenu == 'true') {
            $this->setVar('pageSuppressToolbar', TRUE);
        }
        $page['id'] = $this->getParam('id');
        //Security Check for Public Access
        if (!$this->_objSecurity->isContentPublic($page['id'])) {
            $this->setVar('errMessage', $this->objLanguage->languageText('mod_cms_mustlogin', 'cms'));
            $this->setVar('mustlogin', TRUE);
            return 'cms_nopermissions_tpl.php';
        }

        $this->setLayoutTemplate('cms_layout_tpl.php');
        $fromadmin = $this->getParam('fromadmin', FALSE);
        $sectionId = $this->getParam('sectionid', NULL);
        //$rss = $this->rss->getUserRss($sectionId);
        //$this->setVar('rss', $rss);
        $page = $this->_objContent->getContentPageFiltered($this->getParam('id'));
        $this->bbcode = $this->getObject('washout', 'utilities');
        $content = $this->objLayout->showBody(false, $page);
        $content = $this->bbcode->parseText($content);
        $this->setVar('sectionId', $sectionId);
        $this->setVar('fromadmin', $fromadmin);
        $this->setVar('content', $content);
        $this->setVar('pageTitle', $page['title']);
        return 'cms_content_tpl.php';
    }

    /**
     * A method that corresponds to the showcontent action parameter
     * from the querystring. It returns the formatted full page of content
     * text for a particular content item. Since its the same as
     * showfulltext, it is not obvious why its needs to be here, but
     * it was in the dispatch method so I put it here.
     *
     * @access private
     * @return string The populated cms_content_tpl.php template
     * @todo The author needs to check if this method is necessary
     *
     */
    private function _showcontent() {
        return $this->_showfulltext();
    }

    /**
     * A method that corresponds to the home action parameter
     * from the querystring. It returns the formatted content
     * or next action depending on whether there is front page
     * comtent or not.
     *
     * @access private
     * @return string The populated cms_content_tpl.php template
     * @todo The author needs to explain the logic here
     *
     */
    private function _home() {
        if ($this->disablemenu == 'true') {
            $this->setVar('pageSuppressToolbar', TRUE);
        }
        $this->setLayoutTemplate('cms_layout_tpl.php');
        $displayId = $this->getParam('displayId');
        $content = $this->objLayout->getFrontPageContent($displayId);
        if (!empty($content)) {
            $this->bbcode = $this->getObject('washout', 'utilities');
            $content = $this->bbcode->parseText($content);
            $this->setVarByRef('content', $content);
            return 'cms_section_tpl.php';
        } else {
            $firstSectionId = $this->_objSections->getFirstSectionId(TRUE);
            return $this->nextAction('showsection', array('id' => $firstSectionId, 'sectionid' => $firstSectionId));
        }
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter
     * @return stromg the name of the method
     *
     */
    private function _getMethod() {
        if ($this->_validAction()) {
            return "_" . $this->action;
        } else {
            return "_actionError";
        }
    }

    private function _releaselock() {
        $this->nextAction('', array('action' => 'viewsection'), 'cmsadmin');
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (_). If the action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter
     * @return boolean TRUE|FALSE
     *
     */
    private function _validAction() {
        if (method_exists($this, "_" . $this->action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Method to return an error when the action is not a valid
     * action method
     *
     * @access private
     * @return string The dump template populated with the error message
     *
     */
    private function _actionError() {

        $this->setVar('str', $this->objLanguage->languageText("mod_cms_errorbadaction", 'cms') . ": <em>" . $this->action . "</em>");
        return 'dump_tpl.php';
    }

    private function _serverpc() {
        // cannot require any login, as remote clients use this. Auth is done internally
        $this->requiresLogin();

        // start the server.
        $this->objRPC->serve();
        // break to be pedantic, although not strictly needed.
        // break;
    }

    /**
     * Method to check if the user is in the CMS Authors group
     *
     * @access public
     */
    public function checkPermission() {
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getLeafId(array('CMSAuthors'));
        if ($objGroups->isGroupMember($this->_objUser->pkId(), $groupId)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //THE METHODS BELOW HERE SEEM TO SERVE NO PURPOSE.-------------------------

    /**
     * Method to get the Sections on the left side of the menu
     *
     * @access public
     * @return string
     */
    public function getSectionMenu() {
        $calArr = array('text' => 'Calendar', 'uri' => $this->uri(array('action' => 'ical')));
        return $this->_objUtils->getSectionMenu();
    }

    /**
     * Method to get the Bread Crumbs
     *
     * @access public
     * @return string Html for the breadcrumbs
     */
    public function getBreadCrumbs() {
        return $this->_objUtils->getBreadCrumbs();
    }

    public function _feed() {
        //get the feed format parameter from the querystring
        $format = $this->getParam('format');
        $pageid = $this->getParam('pageid');
        //grab the feed items
        if (isset($pageid)) {
            $posts = $this->_objContent->getContentPage($pageid);
        } else {
            $posts['body'] = $this->objLayout->getFrontPageContent($pageid);
            $posts['title'] = $this->objConfig->getSiteName();
        }
        //print_r($posts); die();
        //set up the feed...
        //title of the feed
        $feedtitle = htmlentities($posts['title']);
        //description
        $feedDescription = null;
        if (isset($posts['menutext'])) {
            $feedDescription = htmlentities($posts['menutext']);
        }

        //link back to the blog
        $feedLink = $this->objConfig->getSiteRoot() . "index.php?module=cms";
        //sanitize the link
        $feedLink = htmlentities($feedLink);
        //set up the url
        $feedURL = $this->objConfig->getSiteRoot() . "index.php?module=cms&action=feed&format=" . $format;
        //print_r($feedURL);
        $feedURL = htmlentities($feedURL);

        //set up the feed
        $result = $this->objFeedCreator->setupFeed(TRUE, $feedtitle, $feedDescription, $feedLink, $feedURL);

        //use the post title as the feed item title
        $itemTitle = $posts['title'];
        $itemLink = $this->uri(array('action' => 'showcontent', 'id' => $posts['id'], 'sectionid' => $posts['sectionid'])); //todo - add this to the posts table!
        //description
        $itemDescription = $posts['body'];
        //where are we getting this from
        $itemSource = $this->objConfig->getSiteRoot() . "index.php?module=cms&action=showcontent&id=" . $posts['id'] . "&sectionid = " . $posts['sectionid'];
        //feed author
        $itemAuthor = htmlentities($posts['created_by']);
        //add this item to the feed
        $this->objFeedCreator->addItem($itemTitle, $itemLink, $itemDescription, $itemSource, $itemAuthor);


        //check which format was chosen and output according to that
        switch ($format) {
            case 'rss2':
                $feed = $this->objFeedCreator->output('RSS2.0'); //defaults to RSS2.0
                break;
            case 'rss091':
                $feed = $this->objFeedCreator->output('RSS0.91');
                break;
            case 'rss1':
                $feed = $this->objFeedCreator->output('RSS1.0');
                break;
            case 'pie':
                $feed = $this->objFeedCreator->output('PIE0.1');
                break;
            case 'mbox':
                $feed = $this->objFeedCreator->output('MBOX');
                break;
            case 'opml':
                $feed = $this->objFeedCreator->output('OPML');
                break;
            case 'atom':
                $feed = $this->objFeedCreator->output('ATOM0.3');
                break;
            case 'html':
                $feed = $this->objFeedCreator->output('HTML');
                break;
            case 'js':
                $feed = $this->objFeedCreator->output('JS');
                break;

            default:
                $feed = $this->objFeedCreator->output(); //defaults to RSS2.0
                break;
        }
        //output the feed
        echo htmlentities($feed);
    }

}

?>
