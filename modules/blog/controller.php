<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for blog module
 *
 * @category  Chisimba
 * @package   blog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 24801 2012-12-09 12:00:21Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class blog extends controller {
    /**
     * Controller class for the blog module that extends the base controller
     *
     * @author    Paul Scott <pscott@uwc.ac.za>
     * @copyright 2007 AVOIR
     * @package   blog
     * @category  chisimba
     * @license   GPL
     */
    /**
     * User object
     *
     * @var user
     */
    public $objUser;
    /**
     * Language Object
     *
     * @var object
     */
    public $objLanguage;
    /**
     * Logger object
     *
     * @var object
     */
    public $objLog;
    /**
     * Feed object
     *
     * @var object
     */
    public $objFeed;
    /**
     * Feed creator object
     *
     * @var object
     */
    public $objFeedCreator;
    /**
     * HTTP Client object
     *
     * @var object
     */
    public $objClient;
    /**
     * Database abstraction object
     *
     * @var object
     */
    public $objDbBlog;
    /**
     * Configuration object
     *
     * @var object
     */
    public $objConfig;
    /**
     * Operations baseclass
     *
     * @var object
     */
    public $objblogOps;
    /**
     * HTML Cleaner object
     *
     * @var object
     */
    public $cleaner;
    /**
     * Icon Object
     *
     * @var object
     */
    public $objIcon;
    /**
     * Lucene indexer object
     *
     * @var object
     */
    public $luceneindexer;
    /**
     * Lucene document Object
     *
     * @var object
     */
    public $lucenedoc;
    /**
     * Blog import object
     *
     * @var object
     */
    public $objBlogImport;
    /**
     * IMAP / POP3 / NNTP comms class
     *
     * @var object
     */
    public $objImap;
    /**
     * DSN (data source name) for connecting to mail servers
     *
     * @var unknown_type
     */
    public $dsn;
    /**
     * Timeout message object
     *
     * @var object
     */
    public $objMsg;
    /**
     * Comment interface object
     *
     * @var object
     * @var object
     */
    public $objComments;
    /**
     * Trackback object
     *
     * @var object
     */
    public $objTB;
    /**
     * Proxyinfo object
     *
     * @var object
     */
    public $objProxy;
    /**
     * Description for public
     * @var    unknown
     * @access public
     */
    public $showfullname;
    /**
     * Description for public
     * @var    unknown
     * @access public
     */
    public $googleBlogPing;
    /**
     * Constructor method to instantiate objects and get variables
     *
     * @since  1.0.0
     * @return string
     * @access public
     */
    /**
     * YAML object
     *
     * @var object
     */
    public $objYaml;
    /**
     * Blog Profiles object
     *
     * @var    object
     * @access public
     */
    public $objblogProfiles;
    /**
     * Blog RSS object
     *
     * @var    object
     * @access public
     */
    public $objblogRss;
    /**
     * Blog links, pages and rolls object
     *
     * @var    object
     * @access public
     */
    public $objblogLinksandRoll;
    /**
     * Blog posts object
     *
     * @var    object
     * @access public
     */
    public $objblogPosts;
    /**
     * Blog categories object
     *
     * @var    object
     * @access public
     */
    public $objblogCategories;
    /**
     * Blog searching object
     *
     * @var    object
     * @access public
     */
    public $objblogSearching;
    /**
     * Blog extras object
     *
     * @var    object
     * @access public
     */
    public $objblogExtras;
    /**
     * Blog mail ops object
     *
     * @var    object
     * @access public
     */
    public $objblogMail;
    /**
     * Blog trackbacks object
     *
     * @var    object
     * @access public
     */
    public $objblogTrackbacks;

    /**
     * Object of the groupadminmodel class in the groupadmin module.
     *
     * @access protected
     * @var object $objGroup
     */
    protected $objGroup;

    /**
     * Object of terms dialogue class in the blog module.
     *
     * @access protected
     * @var object $objTermsDialogue
     */
    protected $objTermsDialogue;

    /**
     * Object of the dbuserparamsadmin class in the userparamsadmin module.
     *
     * @access protected
     * @var object $objUserParams
     */
    protected $objUserParams;

    public function init() {
        try {
            //grab the blogimporter class, just in case we need it.
            //I think that the import stuff should all be done in a seperate module...
            $this->objBlogImport = $this->getObject('blogimporter');
            //the bbcode parser object
            $this->getObject('bbcodeparser', 'utilities');
            //get the imap class to grab email to blog...
            //maybe a config here to check if we wanna use this?
            //feeds classes
            $this->objFeed = $this->getObject('feeds', 'feed');
            //group object
            $this->objGroup = $this->getObject('groupadminmodel', 'groupadmin');
            //user object
            $this->objUser = $this->getObject('user', 'security');
            //feed creator subsystem
            $this->objFeedCreator = $this->getObject('feeder', 'feed');
            //httpclient to grab remote data
            //$this->objClient = $this->getObject('client', 'httpclient');
            //language object
            $this->objLanguage = $this->getObject('language', 'language');
            //database abstraction object
            $this->objDbBlog = $this->getObject('dbblog');
            //blog operations object
            $this->objblogOps = $this->getObject('blogops');
            //HTML cleaner
            $this->cleaner = $this->getObject('htmlcleaner', 'utilities');
            //icon object
            $this->objIcon = $this->getObject('geticon', 'htmlelements');
            //comment interface class
            $this->objComments = &$this->getObject('commentapi', 'blogcomments');
            //Lucene indexing and search system
            //$this->lucenedoc = &$this->getObject('doc','lucene');
            //$this->luceneindexer = &$this->getObject('indexfactory', 'lucene');
            //timeoutmsg object
            $this->objMsg = $this->getObject('timeoutmessage', 'htmlelements');
            //config object
            $this->objConfig = $this->getObject('altconfig', 'config');
            //proxy object
            $this->objProxy = $this->getObject('proxyparser', 'utilities');
            //Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();
            //sys-config object
            $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            //$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->showfullname = $this->objSysConfig->getValue('show_fullname', 'blog');
            //load up the YAML config object
            $this->objYaml = $this->getObject('yaml', 'utilities');
            $this->objblogProfiles = $this->getObject('blogprofiles');
            $this->objblogRss = $this->getObject('blogrss');
            $this->objblogLinksandRoll = $this->getObject('bloglinksandroll');
            $this->objblogPosts = $this->getObject('blogposts');
            $this->objblogCategories = $this->getObject('blogcategories');
            $this->objblogSearching = $this->getObject('blogsearching');
            $this->objblogExtras = $this->getObject('blogopsextras');
            $this->objblogMail = $this->getObject('blogmail');
            $this->objblogTrackbacks = $this->getObject('blogtrackbacks');
            $this->objTermsDialogue = $this->getObject('blogtermsdialogue', 'blog');
            $this->objUserParams = $this->getObject('dbuserparamsadmin', 'userparamsadmin');
            // Load scriptaclous since we can no longer guarantee it is there
            $scriptaculous = $this->getObject('scriptaculous', 'prototype');
            $this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));
        }
        catch(customException $e) {
            //oops, something not there - bail out
            echo customException::cleanUp();
            //we don't want to even attempt anything else right now.
            die();
        }
    }
    /**
     * Method to process actions to be taken from the querystring
     *
     * @param  string $action String indicating action to be taken
     * @return string template
     */
    public function dispatch($action = Null) {
        switch ($action) {
            default:
            //we don't require login - preloin action
                $this->requiresLogin(FALSE);
                $blog_action = $this->objSysConfig->getValue('blog_action', 'blog');
                $blog_postcount = $this->objSysConfig->getValue('blog_postcount', 'blog');
                if (!empty($blog_action) && $blog_action == 'single user') {
                    $suuserid = $this->objSysConfig->getValue('blog_singleuserid', 'blog');
                    //$this->nextAction('', array('userid' => $suuserid));
                    $this->setVarByRef('userid', $suuserid);
                    //carry on...
                    //get the categories
                    $catarr = $this->objDbBlog->getCatsTree($suuserid);
                    //get the link categories
                    $linkcats = $this->objDbBlog->getAllLinkCats($suuserid);
                    //get all the posts by this user
                    if($blog_postcount != NULL && $blog_postcount != 'NA') {
                        $posts = $this->objDbBlog->getLastPosts($blog_postcount, $suuserid);
                    }
                    else {
                        $postcount = $this->objDbBlog->getMonthPostCount(time() , $suuserid);
                        if ($postcount <= 2 || $postcount >= 20) {
                            $posts = $this->objDbBlog->getLastPosts(10, $suuserid);
                        } else {
                            $posts = $this->objDbBlog->getPostsMonthly(time() , $suuserid);
                        }
                    }
                    //get the sticky posts too
                    $latestpost[0] = $this->objDbBlog->getLatestPost($suuserid);
                    $rss = $this->objDbBlog->getUserRss($suuserid);
                    $stickypost = $this->objDbBlog->getStickyPosts($suuserid);
                    $this->setVarByRef('stickypost', $stickypost);
                    //send all that to the template
                    $this->setVarByRef('rss', $rss);
                    $this->setVarByRef('latestpost', $latestpost);
                    $this->setVarByRef('posts', $posts);
                    $this->setVarByRef('linkcats', $linkcats);
                    $this->setVarByRef('cats', $catarr);

                    //return the template!
                    // $this->setVar('pageSuppressXML', TRUE);
                    return 'randblog_tpl.php';
                    break;
                }

                // the blog aggregator case:
                if (!empty($blog_action) && $blog_action == 'aggregate') {

                    // get the latest posts and display em...
                    $this->objLanguage = $this->getObject('language', 'language');
                    $this->blogPosts = $this->getObject('blogposts', 'blog');
                    $posts = $this->blogPosts->showLastTenPosts();
                    $this->setVarByRef('posts', $posts);

                    // dummy vals so tpl doesn't complain
                    $this->setVarByRef('stickypost', NULL);
                    //send all that to the template
                    $this->setVarByRef('rss', NULL);
                    $this->setVarByRef('latestpost', NULL);
                    $this->setVarByRef('linkcats', NULL);
                    $this->setVarByRef('cats', NULL);
                    return 'randblog_tpl.php';
                    break;
                }

                //check if the user is logged in
                if ($this->objUser->isLoggedIn() == TRUE) {
                    //get the action
                    $act = $this->getParam('action');
                    //is the user asking for a random blog?
                    if ($act != 'randblog') {
                        //no, so lets go to the viewblog page
                        // $this->setVar('pageSuppressXML', TRUE);
                        $this->nextAction('viewblog');
                        exit;
                    }
                }
                //get the userid if set
                $userid = $this->getParam('userid');
                if ($userid == '') {
                    $this->nextAction('allblogs');
                    exit;
                    /**
                     //no userid is set
                     $this->setVarByRef('message', $this->objLanguage->languageText("mod_blog_word_randomblog"));
                     //get a random blog from the blog table
                     $r = $this->objDbBlog->getRandBlog();
                     //a random blog is found!
                     if(!empty($r))
                     {
                     $userid = $r['userid'];
                     $this->setVarByRef('userid', $userid);
                     }
                     else {
                     //oh dear, no blogs on this instance of chisimba!
                     return 'noblogs_tpl.php';
                     }
                     */
                }
                // $this->requiresLogin(FALSE);
                $this->setVarByRef('userid', $userid);
                //carry on...
                //get the categories
                $catarr = $this->objDbBlog->getCatsTree($userid);
                //get the link categories
                $linkcats = $this->objDbBlog->getAllLinkCats($userid);
                //get all the posts by this user
                $postcount = $this->objDbBlog->getMonthPostCount(time() , $userid);
                if ($postcount <= 2 || $postcount >= 20) {
                    $posts = $this->objDbBlog->getLastPosts(10, $userid);
                } else {
                    $posts = $this->objDbBlog->getPostsMonthly(time() , $userid);
                }
                //get the sticky posts too
                $latestpost[0] = $this->objDbBlog->getLatestPost($userid);
                $rss = $this->objDbBlog->getUserRss($userid);
                $stickypost = $this->objDbBlog->getStickyPosts($userid);
                $this->setVarByRef('stickypost', $stickypost);
                //send all that to the template
                $this->setVarByRef('rss', $rss);
                $this->setVarByRef('latestpost', $latestpost);
                $this->setVarByRef('posts', $posts);
                $this->setVarByRef('linkcats', $linkcats);
                $this->setVarByRef('cats', $catarr);
                //return the template!
                // $this->setVar('pageSuppressXML', TRUE);
                return 'randblog_tpl.php';
                break;
            // The siteblog function added by Irshaad Hoosain

            case 'siteblog':
            //get the category ID if any
                $catid = $this->getParam('catid');
                //grab the user id
                $userid = 1;
                if (!isset($userid)) {
                    //fix the user id just in case
                    if ($this->objUser->isLoggedIn() == TRUE) {
                        $userid = 1; //$this->objUser->userId();

                    } else {
                        $this->nextAction('');
                        exit;
                    }
                }
                if (isset($catid)) {
                    //grab all the posts in that category
                    $posts = $this->objDbBlog->getAllPosts($userid, $catid);
                } else {
                }
                $posts = $this->objDbBlog->getAllPosts($userid, $catid);
                //send all that to the template
                $this->setVarByRef('catid', $catid);
                $this->setVarByRef('posts', $posts);
                $this->setVarByRef('linkcats', $linkcats);
                $this->setVarByRef('cats', $catarr);
                $this->setVarByRef('userid', $userid);
                //return the template
                // $this->setVar('pageSuppressXML', TRUE);
                return 'siteblog_tpl.php';
                break;

            case 'viewsingle':
            //single post view for the bookmarks/comments etc
                $comment = $this->getParam('comment');
                $useremail = $this->getParam('useremail');
                //echo $comment, $useremail;
                if (isset($comment) && isset($useremail)) {
                    $this->setVarByRef('comment', $comment);
                    $this->setVarByRef('useremail', $useremail);
                }
                $postid = $this->getParam('postid');
                $userid = $this->getParam('userid');
                $posts = $this->objDbBlog->getPostByPostID($postid);
                if (isset($posts[0]['post_title'])) {
                    $this->setVar('pageTitle', $posts[0]['post_title']);
                }
                if (isset($userid)) {
                    $catarr = $this->objDbBlog->getCatsTree($userid);
                    $this->setVarByRef('cats', $catarr);
                } else {
                    $userid = $posts[0]['userid'];
                    $catarr = $this->objDbBlog->getCatsTree($userid);
                    $this->setVarByRef('cats', $catarr);
                }
                //get the post with comments and trackbacks and display it.
                //$this->setVarByRef('addinfo', $addinfo);
                $this->setVarByRef('postid', $postid);
                $this->setVarByRef('posts', $posts);
                $this->setVarByRef('userid', $userid);
                //Add the stuff for rel=canonical
                $permaLinkForPost = $this->uri(array('postid' => $postid,
                    'action' => 'viewsingle',
                    'userid' => $userid),'blog');
                $permaLinkForPost = str_replace('&amp;', '&', $permaLinkForPost);
                $canonicalLink = '<link rel="canonical" href="' . $permaLinkForPost . '"/>';
                $this->appendArrayVar('headerParams', $canonicalLink);
                $this->commentsEnabled = $this->objSysConfig->getValue('enabled', 'blogcomments'); //-------------
                return 'viewsingle_tpl.php';
                break;

            case 'setupmail':
            //check that the person trying to set this up is logged in and an admin
                if ($this->objUser->isLoggedIn() == FALSE || $this->objUser->inAdminGroup($this->objUser->userId()) == FALSE) {
                    //user is not logged in, bust out of this case and go to the default
                    $this->nextAction('');
                    exit;
                } else {
                    $sprot = $this->getParam("mprot");
                    $muser = $this->getParam("muser");
                    $mpass = $this->getParam("mpass");
                    $mserver = $this->getParam("mserver");
                    $mport = $this->getParam("mport");
                    $mbox = $this->getParam("mbox");
                    //check that all the settings are there!
                    if (empty($sprot) || empty($muser) || empty($mpass) || empty($mserver) || empty($mport) || empty($mbox)) {
                        return 'mailsetup_tpl.php';
                    }
                    //create the DSN
                    $newsettings = array(
                            "BLOG_MAIL_DSN" => $sprot . '://' . $muser . ':' . $mpass . '@' . $mserver . ':' . $mport . '/' . $mbox
                    );
                    $this->objblogExtras->setupConfig($newsettings);
                    $this->nextAction('');
                    break;
                }
                //break to make dead sure we break...
                break;

            case 'mail2blog':
                echo $this->objblogMail->mail2blog();
                break;

            //Added by Derek Keats for the blog export functionality 2010 01 05
            case 'export':
                $objBlogItem = $this->getObject('blogexport', 'blog');
                $str = $objBlogItem->show();
                $this->setPageTemplate('plain_tpl.php');
                $this->setVarByRef('str', $str);
                return "dump_tpl.php";
                break;

            case 'listmail2blog':
                echo $this->objblogMail->listmail2blog();
                break;

            case 'importblog':
            //check if the user is logged in
                if ($this->objUser->isLoggedIn() == FALSE) {
                    //no, redirect to the main blog page
                    $this->nextAction('');
                    //get outta this action immediately
                    exit;
                }
                //get some info
                $username = $this->getParam('username');
                $server = $this->getParam('server');
                if (empty($username) || empty($server)) {
                    return "importform_tpl.php";
                } else {
                    try {
                        //set up to connect to the server
                        $this->objBlogImport->setup($server);
                        //connect to the remote db
                        $this->objBlogImport->_dbObject();
                        $blog = $this->objBlogImport->importBlog($username);
                        $userid = $this->objUser->userId();
                        foreach($blog as $blogs) {
                            //create the post array in a format the this blog can understand...
                            $postarr = array(
                                    'userid' => $userid,
                                    'postdate' => strtotime($blogs['dateadded']) ,
                                    'postcontent' => $this->objblogOps->html2txt(htmlentities($blogs['content']) , TRUE) ,
                                    'posttitle' => $this->objblogOps->html2txt(htmlentities($blogs['title']) , TRUE) ,
                                    'postcat' => 0,
                                    'postexcerpt' => $this->objblogOps->html2txt(htmlentities($blogs['headline']) , TRUE) ,
                                    'poststatus' => 0,
                                    'commentstatus' => 'Y',
                                    'postmodified' => $blogs['dateadded'],
                                    'commentcount' => 0,
                                    'postdate' => $blogs['dateadded']
                            );
                            //use the insertPost methods to populate...
                            $this->objblogPosts->quickPostAdd($userid, $postarr, 'import');
                            //clear $postarr
                            $postarr = NULL;
                        }
                        $this->nextAction('viewblog');
                    }
                    catch(customException $e) {
                        customException::cleanUp();
                        exit;
                    }
                    //}

                }
                break;

            case 'importallblogs':
            //check if the user is logged in
                if ($this->objUser->isLoggedIn() == FALSE) {
                    //no, redirect to the main blog page
                    $this->nextAction('');
                    //get outta this action immediately
                    exit;
                }
                //get some info
                $server = 'santec'; //$this->getParam('server');
                if (empty($server)) {
                    return "importform_tpl.php";
                } else {
                    try {
                        //set up to connect to the server
                        $this->objBlogImport->setup($server);
                        //connect to the remote db
                        $this->objBlogImport->_dbObject();
                        $blog = $this->objBlogImport->importAllBlogs();
                        foreach($blog as $blogs) {
                            $userArr = array();
                            $postarr = array();
                            // get the users new user id - if the user doesn't exist, then skip.
                            $sql = "SELECT * FROM tbl_users WHERE userid = '" . $blogs['userid'] . "'";
                            $userArr = $this->objUser->getArray($sql);
                            if (!empty($userArr)) {
                                $userid = $userArr[0]['userid'];
                                // create the post array in a format the this blog can understand...
                                $postarr = array(
                                        'userid' => $userid,
                                        'postdate' => strtotime($blogs['dateadded']) ,
                                        'postcontent' => $this->objblogOps->html2txt(htmlentities($blogs['content']) , TRUE) ,
                                        'posttitle' => $this->objblogOps->html2txt(htmlentities($blogs['title']) , TRUE) ,
                                        'postcat' => 0,
                                        'postexcerpt' => $this->objblogOps->html2txt(htmlentities($blogs['headline']) , TRUE) ,
                                        'poststatus' => 0,
                                        'commentstatus' => 'Y',
                                        'postmodified' => $blogs['dateadded'],
                                        'commentcount' => 0,
                                        'postdate' => $blogs['dateadded']
                                );
                                //use the insertPost methods to populate...
                                $this->objblogPosts->quickPostAdd($userid, $postarr, 'import');
                            }
                            //clear $postarr
                            $postarr = NULL;
                        }
                        $this->nextAction('viewblog');
                    }
                    catch(Exception $e) {
                        throw customException($e->getMessage());
                        exit();
                    }
                }
                break;

            case 'feedurl':
            //get the feed format parameter from the querystring
                $format = $this->getParam('feedselector');
                //and the userid of the blog we are interested in
                $userid = $this->getParam('userid');
                // Create the feed url
                $url = $this->uri(array(
                        'action' => 'feed',
                        'feedselector' => $format,
                        'userid' => $userid
                ));
                $this->setVarByRef('feed', $url);
                // $this->setVar('pageSuppressXML', TRUE);
                return 'showfeed_tpl.php';
                break;

            case 'feed':
            //get the feed format parameter from the querystring
                $format = $this->getParam('format');
                //and the userid of the blog we are interested in
                $userid = $this->getParam('userid');
                $bloggerprofile = $this->objDbBlog->checkProfile($userid);
                //grab the feed items
                $posts = $this->objDbBlog->getPostsMonthly(mktime(0, 0, 0, date("m", time()) , 1, date("y", time())) , $userid); //, $catid = NULL);
                if (empty($posts)) {
                    $posts = $this->objDbBlog->getLastPosts(10, $userid);
                }
                //set up the feed...
                //who's blog is this?
                if (isset($bloggerprofile['blog_name'])) {
                    $fullname = htmlentities($bloggerprofile['blog_name']); //$this->getParam('blog_name');

                } else {
                    $fullname = htmlentities($this->objUser->fullname($userid));
                }
                //title of the feed
                $feedtitle = htmlentities($fullname);
                //description
                if (isset($bloggerprofile['blog_descrip'])) {
                    $feedDescription = htmlentities($bloggerprofile['blog_descrip']); //$this->getParam('blog_name');

                } else {
                    $feedDescription = htmlentities($this->objLanguage->languageText("mod_blog_blogof", "blog")) . " " . $fullname;
                }
                //link back to the blog
                $feedLink = $this->objConfig->getSiteRoot() . "index.php?module=blog&action=randblog&userid=" . $userid;
                //sanitize the link
                $feedLink = htmlentities($feedLink);
                //set up the url
                $feedURL = $this->objConfig->getSiteRoot() . "index.php?module=blog&userid=" . $userid . "action=feed&format=" . $format;
                $feedURL = htmlentities($feedURL);
                //set up the feed
                $this->objFeedCreator->setupFeed(TRUE, $feedtitle, $feedDescription, $feedLink, $feedURL);
                //loop through the posts and create feed items from them
                foreach($posts as $feeditems) {
                    //use the post title as the feed item title
                    $itemTitle = $feeditems['post_title'];
                    $itemLink = $this->uri(array(
                            'action' => 'viewsingle',
                            'postid' => $feeditems['id'],
                            'userid' => $userid
                    )); //todo - add this to the posts table!
                    //description
                    $itemDescription = stripslashes($feeditems['post_content']);
                    //where are we getting this from
                    $itemSource = $this->objConfig->getSiteRoot() . "index.php?module=blog&userid=" . $userid;
                    //feed author
                    $itemAuthor = htmlentities($this->objUser->userName($userid)."<".$this->objUser->email($userid).">");
                    $itemDate = strtotime($feeditems['post_date']);
                    //add this item to the feed
                    $this->objFeedCreator->addItem($itemTitle, $itemLink, $itemDescription, $itemSource, $itemAuthor, $itemDate);
                }
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
                // $this->setVar('pageSuppressXML', TRUE);
                echo htmlentities($feed);
                break;

            case 'commentfeed':
                $format = 'RSS2';
                //and the userid of the blog we are interested in
                $userid = $this->getParam('userid');
                $bloggerprofile = $this->objDbBlog->checkProfile($userid);
                //grab the feed items
                $this->objDbBlogComments = $this->getObject('dbblogcomments', 'blogcomments');
                $posts = $this->objDbBlogComments->grabCommentsByUser($userid);
                if (empty($posts)) {
                    $posts = $this->objDbBlog->getLastPosts(10, $userid);
                }
                //set up the feed...
                //who's blog is this?
                if (isset($bloggerprofile['blog_name'])) {
                    $fullname = htmlentities($bloggerprofile['blog_name']); //$this->getParam('blog_name');

                } else {
                    $fullname = htmlentities($this->objUser->fullname($userid));
                }
                //title of the feed
                $feedtitle = htmlentities($fullname);
                //description
                if (isset($bloggerprofile['blog_descrip'])) {
                    $feedDescription = htmlentities($bloggerprofile['blog_descrip']); //$this->getParam('blog_name');

                } else {
                    $feedDescription = htmlentities($this->objLanguage->languageText("mod_blog_blogof", "blog")) . " " . $fullname;
                }
                //link back to the blog
                $feedLink = $this->objConfig->getSiteRoot() . "index.php?module=blog&action=randblog&userid=" . $userid;
                //sanitize the link
                $feedLink = htmlentities($feedLink);
                //set up the url
                $feedURL = $this->objConfig->getSiteRoot() . "index.php?module=blog&userid=" . $userid . "action=feed&format=" . $format;
                $feedURL = htmlentities($feedURL);
                //set up the feed
                $this->objFeedCreator->setupFeed(TRUE, $feedtitle, $feedDescription, $feedLink, $feedURL);
                //loop through the posts and create feed items from them
                foreach($posts as $feeditems) {
                    //use the post title as the feed item title
                    $itemTitle = $feeditems['comment_author'];
                    $itemLink = $this->uri(array(
                            'action' => 'viewsingle',
                            'postid' => $feeditems['comment_parentid'],
                            'userid' => $userid
                    ));
                    //description
                    $itemDescription = $feeditems['comment_content'];
                    //where are we getting this from
                    $itemSource = $this->objConfig->getSiteRoot() . "index.php?module=blog&userid=" . $userid;
                    //feed author
                    $itemAuthor = htmlentities($feeditems['comment_author']);
                    //add this item to the feed
                    $this->objFeedCreator->addItem($itemTitle, $itemLink, $itemDescription, $itemSource, $itemAuthor);
                }
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
                // $this->setVar('pageSuppressXML', TRUE);
                echo htmlentities($feed);
                break;

            case 'showallposts':
                $catid = NULL;
                $userid = $this->getParam('userid');
                if (!isset($userid)) {
                    //fix the user id just in case
                    if ($this->objUser->isLoggedIn() == TRUE) {
                        $userid = $this->objUser->userId();
                    } else {
                        $this->nextAction('');
                        exit;
                    }
                }
                //get the category tree
                $catarr = $this->objDbBlog->getCatsTree($userid);
                //get the links categories
                $linkcats = $this->objDbBlog->getAllLinkCats($userid);
                //make sure the category id is there
                if (isset($catid)) {
                    //grab all the posts in that category
                    $posts = $this->objDbBlog->getAbsAllPostsNoDrafts($userid);
                } else {
                    //otherwise grab all the Published posts
                    $posts = $this->objDbBlog->getAbsAllPostsNoDrafts($userid);
                }
                $latestpost[0] = $this->objDbBlog->getLatestPost($userid);
                $this->setVarByRef('latestpost', $latestpost);
                //grab the user defined rss feeds if any
                $rss = $this->objDbBlog->getUserRss($userid);
                $stickypost = $this->objDbBlog->getStickyPosts($userid);
                $this->setVarByRef('stickypost', $stickypost);
                //send all that to the template
                $this->setVarByRef('rss', $rss);
                $this->setVarByRef('catid', $catid);
                $this->setVarByRef('posts', $posts);
                $this->setVarByRef('linkcats', $linkcats);
                $this->setVarByRef('cats', $catarr);
                $this->setVarByRef('userid', $userid);
                //return the template
                // $this->setVar('pageSuppressXML', TRUE);
                return 'myblog_tpl.php';
                break;

            case 'viewblog':

               //get the category ID if any
                $catid = $this->getParam('catid');
                //grab the user id
                $userid = $this->getParam('userid');

                if ($userid == '') {
                    //fix the user id just in case
                    if ($this->objUser->isLoggedIn() == TRUE) {
                        $userid = $this->objUser->userId();
                    } else {
                        $this->nextAction('');
                        exit;
                    }
                }

                //get the category tree
                $catarr = $this->objDbBlog->getCatsTree($userid);
                //get the links categories
                $linkcats = $this->objDbBlog->getAllLinkCats($userid);
                //make sure the category id is there
                if (isset($catid) && $catid != '') {
                    //grab all the posts in that category
                    $posts = $this->objDbBlog->getAllPosts($userid, $catid);
                } else {
                    //otherwise grab all the Published posts
                    $blog_postcount = $this->objSysConfig->getValue('blog_postcount', 'blog');
                    if($blog_postcount != NULL && $blog_postcount != 'NA') {
                        $posts = $this->objDbBlog->getLastPosts($blog_postcount, $userid);
                    }
                    else {
                        $posts = $this->objDbBlog->getLastPosts(10, $userid); // getAllPosts($userid, $catid); // getPostsMonthly(time() , $userid);
                        if (count($posts) < 2) {
                            $posts = $this->objDbBlog->getLastPosts(10, $userid);
                        }
                        //$posts = $this->objDbBlog->getAllPosts($userid, 0);//getAbsAllPostsNoDrafts($userid);
                    }

                }
                $latestpost[0] = $this->objDbBlog->getLatestPost($userid);
                $this->setVarByRef('latestpost', $latestpost);
                $rss = $this->objDbBlog->getUserRss($userid);
                $stickypost = $this->objDbBlog->getStickyPosts($userid);
                $this->setVarByRef('stickypost', $stickypost);
                //send all that to the template
                $this->setVarByRef('rss', $rss);
                $this->setVarByRef('catid', $catid);
                $this->setVarByRef('posts', $posts);
                $this->setVarByRef('linkcats', $linkcats);
                $this->setVarByRef('cats', $catarr);
                $this->setVarByRef('userid', $userid);
                //return the template
                // $this->setVar('pageSuppressXML', TRUE);
                return 'myblog_tpl.php';
                break;

            case 'viewblogbytag':
            //get the tag
                $tag = $this->getParam('tag');
                //grab the user id
                $userid = $this->getParam('userid');
                if (!isset($userid)) {
                    //fix the user id just in case
                    if ($this->objUser->isLoggedIn() == TRUE) {
                        $userid = $this->objUser->userId();
                    } else {
                        $this->nextAction('');
                        exit;
                    }
                }
                //get the category tree
                $catarr = $this->objDbBlog->getCatsTree($userid);
                //get the links categories
                $linkcats = $this->objDbBlog->getAllLinkCats($userid);
                //make sure the category id is there
                if (isset($tag)) {
                    //grab all the posts with that tag
                    $posts = $this->objDbBlog->getAllPostsByTag($userid, $tag);
                } else {
                    //otherwise grab all the Published posts
                    $posts = $this->objDbBlog->getAllPosts($userid, 0); //getAbsAllPostsNoDrafts($userid);

                }
                $latestpost[0] = $this->objDbBlog->getLatestPost($userid);
                $this->setVarByRef('latestpost', $latestpost);
                $stickypost = $this->objDbBlog->getStickyPosts($userid);
                $this->setVarByRef('stickypost', $stickypost);
                //send all that to the template
                if(!isset($catid)) {
                    $catid = NULL;
                }
                $this->setVarByRef('catid', $catid);
                $this->setVarByRef('posts', $posts);
                $this->setVarByRef('linkcats', $linkcats);
                $this->setVarByRef('cats', $catarr);
                $this->setVarByRef('userid', $userid);
                //return the template
                // $this->setVar('pageSuppressXML', TRUE);
                return 'mytagsblog_tpl.php';
                break;

            case 'blogadmin':
            //make sure the user is logged in
                if ($this->objUser->isLoggedIn() == FALSE) {
                    //bail to the default page
                    $this->nextAction('');
                    //exit this action
                    exit;
                }
                //get the user id
                $userid = $this->objUser->userId();
                $this->setVarByRef('userid', $userid);
                // Check if the user is allowed to blog - added Dec 2008
                if (!($this->approvedBlogger())) {
                    return 'not_approved_tpl.php';
                    exit;
                }
                // Check to see if the user needs to accept terms and conditions before being able to blog.
                $terms = $this->objSysConfig->getValue('mod_blog_terms', 'blog');
                if ($terms) {
                    $acceptedBlogTerms = $this->objUserParams->getValue('accepted_blog_terms');
                    if (!$acceptedBlogTerms) {
                        $dialogueContent = file_get_contents($terms);
                        $this->objTermsDialogue->setContent($dialogueContent);
                    }
                }
                //check the mode
                $mode = $this->getParam('mode');
                switch ($mode) {
                    //return a specific template for the chosen mode

                    case 'writepost':
                        return 'writepost_tpl.php';
                        break;

                    case 'editpost':
                    //$this->setPageTemplate(NULL);
                        //$this->setLayoutTemplate("block_2layout_tpl.php");
                        return 'editpost_tpl.php';
                        break;

                    case 'editcats':
                        return 'editcats_tpl.php';
                        break;

                    case 'acceptterms':
                        $value = $this->objUserParams->setItem('accepted_blog_terms', 1);
                        $data = array('value' => $value);
                        $json = json_encode($data);
                        $this->setContentType('application/json');
                        echo $json;
                        return;
                }
                // return the default template for no mode set
                return 'blogadminmenu_tpl.php';
                break;

            case 'adminpg':
                $this->objblogPosts->managePosts($this->objUser->userId());

            case 'showarchives':
                ini_set('max_execution_time', -1);
                //get the date and user id
                $date = $this->getParam('year');
                $userid = $this->getParam('userid');
                //grab the posts by month
                $posts = $this->objDbBlog->getPostsMonthly($date, $userid);
                //send out to the template
                $this->setVarByRef('userid', $userid);
                $this->setVarByRef('posts', $posts);
                //return a specific template and break the action
                // $this->setVar('pageSuppressXML', TRUE);
                return 'archive_tpl.php';
                break;

            case 'catadd':
            //add a category
            //check for login
                if ($this->objUser->isLoggedIn() == FALSE) {
                    //not logged in - send to default action
                    $this->nextAction('');
                    exit;
                }
                //check the mode and cat name as wel as user id
                $mode = $this->getParam('mode');
                $list = $this->getParam('catname');
                $userid = $this->objUser->userId();
                $catname = $this->getParam('catname');
                $catparent = $this->getParam('catparent');
                $catdesc = $this->getParam('catdesc');
                $id = $this->getParam('id');
                //category quick add
                if ($mode == 'quickadd') {
                    if (empty($list)) {
                        $this->nextAction('');
                        break;
                    }
                    $this->objblogCategories->quickCatAdd($list, $userid);
                    $this->nextAction('');

                    break;
                }
                if ($mode == 'edit') {
                    //update the records in the db
                    //build the array again
                    $entry = $this->objDbBlog->getCatForEdit($userid, $id);
                    $catarr = array(
                            'userid' => $userid,
                            'cat_name' => $entry['cat_name'],
                            'cat_nicename' => $entry['cat_nicename'],
                            'cat_desc' => $entry['cat_desc'],
                            'cat_parent' => $entry['cat_parent'],
                            'id' => $id
                    );
                    //display the cat editor with the values in the array, set that form to editcommit
                    $this->setVarByRef('catarr', $catarr);
                    $this->setVarByRef('userid', $userid);
                    $this->setVarByRef('catid', $id);
                    return 'cedit_tpl.php';
                    break;
                }
                if ($mode == 'editcommit') {
                    $catarr = array(
                            'userid' => $userid,
                            'cat_name' => $catname,
                            'cat_nicename' => $catname,
                            'cat_desc' => $catdesc,
                            'cat_parent' => $catparent,
                            'id' => $id
                    );
                    $this->objDbBlog->setCats($userid, $catarr, $mode);
                    $this->nextAction('blogadmin', array(
                            'mode' => 'editcats'
                    ));
                }
                if ($mode == NULL) {
                    $catarr = array(
                            'userid' => $userid,
                            'cat_name' => $catname,
                            'cat_nicename' => $catname,
                            'cat_desc' => $catdesc,
                            'cat_parent' => $catparent
                    );
                    //insert the category into the db
                    $this->objDbBlog->setCats($userid, $catarr);
                    $this->nextAction('blogadmin', array(
                            'mode' => 'editcats'
                    ));
                    break;
                }
                break;

            case "add":
                $this->objIcon = &$this->getObject('geticon', 'htmlelements');
                $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                        'action' => 'postedit',
                        'id' => $post['id'],
                        'module' => 'blog'
                )));
                $commentLink = $this->objComments->addCommentLink($type = NULL);
                return "input_tpl.php";
                break;

            case 'postadd':
            //Added by Ishaad Hoosain for siteblog
            //if user clicked checkbox he is admin
                if ($this->objUser->isLoggedIn() == FALSE) {
                    $this->nextAction('');
                    exit;
                }
                $mode = $this->getParam('mode');
                if ($this->getParam('checkbox') != NULL) {
                    $userid = 1;
                } else {
                    $userid = $this->objUser->userId();
                }
                $id = $this->getParam('id');
                $posttitle = $this->getParam('posttitle');
                $postcontent = $this->getParam('postcontent');
                $postcontent = $postcontent;
                $cclic = $this->getParam('creativecommons');
                $postdate = $this->getParam('postdate');
                $cat = $this->getParam('cat');
                $status = $this->getParam('status');
                $commentsallowed = $this->getParam('commentsallowed');
                $stickypost = $this->getParam('stickypost');
                $showpdf = $this->getParam('showpdf');
                $excerpt = $this->getParam('postexcerpt');
                $postts = $this->getParam('post_ts');
                $tags = $this->getParam('tags');
                $tagarray = explode(",", $tags);
                $geotags = $this->getParam('geotag');
                if(isset($geotags[0]) && isset($geotags[1])) {
                    $geotags = explode(', ', $geotags);
                    $lat = $geotags[0];
                    $lon = $geotags[1];
                }
                else {
                    $geotags = NULL;
                    $lat = NULL;
                    $lon = NULL;
                }
                if ($stickypost == "on") {
                    $stickypost = 1;
                } else {
                    $stickypost = 0;
                }
                if ($showpdf == "on") {
                    $showpdf = 1;
                } else {
                    $showpdf = 0;
                }
                //post quick add
                if ($mode == 'quickadd') {
                    $postcontent = $this->getParam('quickpost_postcontent');
                    $this->objblogPosts->quickPostAdd($userid, array(
                            'posttitle' => $posttitle,
                            'postcontent' => nl2br(htmlentities($postcontent)) ,
                            'postcat' => $cat,
                            'postexcerpt' => '',
                            'poststatus' => '0',
                            'commentstatus' => 'Y',
                            'postmodified' => date('r') ,
                            'commentcount' => 0,
                            'postdate' => $postdate,
                            'cclic' => $cclic
                    ));
                    $this->nextAction('viewblog');
                    break;
                } elseif ($mode == 'editpost') {
                    $insarredit = array(
                            'id' => $id,
                            'posttitle' => $posttitle,
                            'postcontent' => $postcontent,
                            'postcat' => $cat,
                            'postexcerpt' => $excerpt,
                            'poststatus' => $status,
                            'commentstatus' => $commentsallowed,
                            'postmodified' => date('r') ,
                            'commentcount' => 0,
                            'postdate' => $postdate,
                            'postts' => $postts,
                            'cclic' => $cclic,
                            'stickypost' => $stickypost,
                            'showpdf' => $showpdf,
                            'geolat' => $lat,
                            'geolon' => $lon,
                    );
                    $this->objblogPosts->quickPostAdd($userid, $insarredit, $mode);
                    $etags = $this->objDbBlog->getPostTags($id);
                    foreach($etags as $rmtags) {
                        $this->objDbBlog->removeAllTags($rmtags['id']);
                    }
                    if (!empty($tagarray) && $tagarray[0] != "") {

                        //if (count($tagarray) < count($etags)) {
                            //remove all the tags for the post so that we can populate with the new ones

                            $this->objDbBlog->insertTags($tagarray, $userid, $id);
                    }
                    /*    //clean out the duplicate tags
                        //adding the extra tags
                        $tagarray = array_diff($tagarray, $etags);
                        if (!empty($etags)) {
                            foreach($etags as $t) {
                                $things[] = $t['meta_value'];
                            }
                        } else {
                            $things[] = NULL;
                        }
                        $tagarray = array_diff($tagarray, $things);
                        $this->objDbBlog->insertTags($tagarray, $userid, $id);
                    }*/
                    if ($status == 1) {
                        $this->nextAction('blogadmin', array('mode'=>'editpost'));
                    } else {
                        $this->nextAction('viewblog');
                    }
                    break;
                } else {
                    $this->objblogPosts->quickPostAdd($userid, array(
                            'id' => $id,
                            'posttitle' => $posttitle,
                            'postcontent' => $postcontent,
                            'postcat' => $cat,
                            'postexcerpt' => $excerpt,
                            'poststatus' => $status,
                            'commentstatus' => $commentsallowed,
                            'postmodified' => date('r') ,
                            'commentcount' => 0,
                            'postdate' => $postdate,
                            'cclic' => $cclic,
                            'stickypost' => $stickypost,
                            'showpdf' => $showpdf,
                            'geolat' => $lat,
                            'geolon' => $lon,
                    ));
                    //dump in the tags
                    if (!empty($tagarray) && $tagarray[0] != "") {
                        $posid = $this->objDbBlog->getLatestPost($userid);
                        if (isset($posid['drafts'][0]['post_ts']) && (!isset($posid['post_ts']) || $posid['post_ts'] < $posid['drafts'][0]['post_ts'])) {
                            $posid = $posid['drafts'][0]['id'];
                        } else {
                            $posid = $posid['id'];
                        }
                        $this->objDbBlog->insertTags($tagarray, $userid, $posid);
                    }
                    if ($status == 1) {
                        $this->nextAction('blogadmin', array('mode'=>'editpost'));
                    } else {
                        $this->nextAction('viewblog');
                    }
                    break;
                }
                break;

            case 'deletepost':
                if ($this->objUser->isLoggedIn() == FALSE) {
                    $this->nextAction('');
                    exit;
                }
                $id = $this->getParam('id');

                // Changes by Tohir
                // Allow admin to delete
                // check user is deleting own post
                $post = $this->objDbBlog->getPostByPostID($id);
                if (count($post) == 0) {
                    return $this->nextAction(NULL);
                } else {
                    $post = $post[0];
                    if ($post['userid'] == $this->objUser->userId() || $this->objUser->isAdmin()) {
                        $this->objDbBlog->deletePost($id);
                        if ($nextAction = $this->getParam('nextAction')) {
                            return $this->nextAction($nextAction);
                        }
                        return $this->nextAction('blogadmin', array(
                                'mode' => 'editpost'
                        ));
                    } else {
                        return $this->nextAction(NULL);
                    }
                }


                break;

            case 'postedit':
                if ($this->objUser->isLoggedIn() == FALSE) {
                    $this->nextAction('');
                    exit;
                }
                $userid = $this->objUser->userId();
                $id = $this->getParam('id');
                //check for the multidelete option
                $delarr = $this->getArrayParam('arrayList');
                if (!empty($delarr)) {
                    //delete the posts and go back to the template...
                    foreach($delarr as $deletes) {
                        $this->objDbBlog->deletePost($deletes);
                    }
                    $this->nextAction('blogadmin', array(
                            'mode' => 'editpost'
                    ));
                }
                $this->setVarByRef('editid', $id);
                $this->setVarByRef('userid', $userid);
                // $this->setVar('pageSuppressXML', TRUE);
               $this->setLayoutTemplate("block_2layout_tpl.php");
                return 'postedit_tpl.php';
                break;

            case 'allblogs':
                $ret = $this->objDbBlog->getUBlogs('userid', 'tbl_blog_posts');
                $this->setVarByRef('ret', $ret);
                // $this->setVar('pageSuppressXML', TRUE);
                return 'allblogs_tpl.php';
                break;

            case 'deletecat':
                if ($this->objUser->isLoggedIn() == FALSE) {
                    $this->nextAction('');
                    exit;
                }
                //grab the vars that we need
                $id = $this->getParam('id');
                $userid = $this->objUser->userId();
                $mode = $this->getParam('mode');
                //echo $id, $mode;
                //search through the posts table to find all the posts linked to the cat
                $posts = $this->objDbBlog->getPostsFromCat($userid, $id);
                foreach($posts as $post) {
                    //update the found posts and set their cat to '0'
                    $insarredit = array(
                            'id' => $post['id'],
                            'posttitle' => $post['post_title'],
                            'postcontent' => $post['post_content'],
                            'postcat' => 0,
                            'postexcerpt' => $post['post_excerpt'],
                            'poststatus' => $post['post_status'],
                            'commentstatus' => $post['comment_status'],
                            'postmodified' => $post['post_modified'],
                            'commentcount' => $post['comment_count'],
                            'postdate' => $post['post_date']
                    );
                    $this->objblogPosts->quickPostAdd($userid, $insarredit, 'editcommit');
                }
                //delete the cat from the table
                $this->objDbBlog->deleteCat($id);
                //nextaction back to the cats view thing
                $this->nextAction('blogadmin', array(
                        'mode' => 'editcats'
                ));
                break;

            case 'tagcloud':
                $this->objTC = $this->getObject('tagcloud', 'utilities');
                //for the blog cloud, we want to get all the categories as tags
                //then the count of posts for each cat as the weight
                //the url to the cat as a link
                //last post time as the time
                //build the array
                //dump it into a featurebox
                //echo the cloud out
                //this action is a test action, this functionality will be moved to blogops soon.
                $tagarray = array(
                        array(
                                'name' => 'PHP',
                                'url' => 'http://www.php.net',
                                'weight' => 1000,
                                'time' => strtotime('-56 days')
                        ) ,
                        array(
                                'name' => 'Google SA',
                                'url' => 'http://www.google.co.za',
                                'weight' => 1250,
                                'time' => strtotime('-6 days')
                        ) , //strtotime('-3 days')),
                        array(
                                'name' => 'AVOIR',
                                'url' => 'http://avoir.uwc.ac.za',
                                'weight' => 1950,
                                'time' => time()
                        ) ,
                        array(
                                'name' => 'FSIU',
                                'url' => 'http://fsiu.uwc.ac.za',
                                'weight' => 1560,
                                'time' => time()
                        )
                );
                print $this->objTC->buildCloud($tagarray);
                break;

            case 'tbreceive':
                $this->requiresLogin(FALSE);
                $req = $_REQUEST;
                $id = $this->getParam('postid');
                $userid = $this->getParam('userid');
                $this->setVarByRef('userid', $userid);
                $pd = $_POST;
                $pd['host'] = $_SERVER['REMOTE_ADDR'];
                $pd['id'] = $id;
                $pd['userid'] = $userid;
                $data = $pd;
                //do a check to see if it is valid
                if (!isset($data['title']) || !isset($data['excerpt']) || !isset($data['url']) || !isset($data['blog_name'])) {
                    $theurl = $this->uri(array(
                            'action' => $req['action'],
                            'module' => $req['module'],
                            'userid' => $req['userid'],
                            'postid' => $req['postid']
                    ));
                    $this->setVarByRef('theurl', $theurl);
                    return 'tburl_tpl.php';
                }
                //check for trackback spam
                require_once $this->getPearResource('Net/DNSBL.php');
                $dnsbl = new Net_DNSBL();
                $remoteIP = $pd['host'];
                $dnsbl->setBlacklists(array(
                        'dul.dnsbl.sorbs.net',
                        'rhsbl.sorbs.net',
                        'http.dnsbl.sorbs.net',
                        'socks.dnsbl.sorbs.net',
                        'misc.dnsbl.sorbs.net',
                        'smtp.dnsbl.sorbs.net',
                        'web.dnsbl.sorbs.net',
                        'block.dnsbl.sorbs.net',
                        'zombie.dnsbl.sorbs.net',
                        'badconf.rhsbl.sorbs.net',
                        'sbl.spamhaus.org',
                        'dnsbl.njabl.org',
                        'relays.ordb.org'
                ));
                if ($dnsbl->isListed($remoteIP)) {
                    return 'tburl_tpl.php';
                } else {
                    //add the $data array to a db table
                    $this->objDbBlog->setTrackback($data);
                    $options = array(
                            // Options for trackback directly
                            'strictness' => 1,
                            'timeout' => 30, // seconds
                            'fetchlines' => 30,
                            'fetchextra' => true,
                    );
                    $this->objTB = $this->getObject("trackback");
                    //use the factory
                    $this->objTB->setup($data, $options);
                    echo $this->objTB->recTB($data);
                }
                break;

            case 'tbsend':
                $postid = $this->getParam('postid');
                $title = $this->getParam('title');
                $excerpt = $this->getParam('excerpt');
                $bloggerprofile = $this->objDbBlog->checkProfile($this->objUser->userId());
                if (isset($bloggerprofile['blog_name'])) {
                    $blog_name = $bloggerprofile['blog_name'];
                } else {
                    $blog_name = $this->getParam('blog_name');
                }
                $url = $this->getParam('url');
                $url = urldecode($url);
                $trackback_url = $this->getParam('tburl');
                $extra = $this->getParam('extra');
                $tbarr = array(
                        'postid' => $postid,
                        'title' => $title,
                        'excerpt' => $excerpt,
                        'blog_name' => $blog_name,
                        'url' => $url,
                        'trackback_url' => $trackback_url
                );
                $this->setVarByRef('tbarr', $tbarr);
                //check that all necessary params arre there, otherwise return the template again...
                if (!isset($postid) || !isset($title) || !isset($excerpt) || !isset($blog_name) || !isset($url) || !isset($trackback_url) || empty($trackback_url)) {
                    return 'tbsend_tpl.php';
                    break;
                }
                //otherwise simply send the trackback and return to the blog. :)
                $data = array(
                        'id' => $postid,
                        'title' => $title,
                        'excerpt' => $excerpt,
                        'blog_name' => $blog_name,
                        'url' => $url,
                        'trackback_url' => $trackback_url,
                        'extra' => $extra
                );
                //get the proxy info if set
                $proxyArr = $this->objProxy->getProxy();
                if (isset($proxyArr['proxy_host'])) {
                    $options = array(
                            // Options for Services_Trackback directly
                            'strictness' => 1,
                            'timeout' => 30, // seconds
                            'fetchlines' => 30,
                            'fetchextra' => true,
                            // Options for HTTP_Request class
                            'httprequest' => array(
                                    'allowRedirects' => true,
                                    'maxRedirects' => 2,
                                    'method' => 'POST',
                                    'useragent' => 'Chisimba',
                                    'proxy_host' => $proxyArr['proxy_host'],
                                    'proxy_port' => $proxyArr['proxy_port'],
                                    'proxy_user' => $proxyArr['proxy_user'],
                                    'proxy_pass' => $proxyArr['proxy_pass']
                            ) ,
                    );
                } else {
                    $options = array(
                            // Options for Services_Trackback directly
                            'strictness' => 1,
                            'timeout' => 30, // seconds
                            'fetchlines' => 30,
                            'fetchextra' => true,
                            // Options for HTTP_Request class
                            'httprequest' => array(
                                    'allowRedirects' => true,
                                    'maxRedirects' => 2,
                                    'method' => 'POST',
                                    'useragent' => 'Chisimba',
                            ) ,
                    );
                }
                try {
                    $this->objTB = $this->getObject("trackback");
                    //use the factory
                    $this->objTB->setup($data, $options);
                    $this->objTB->sendTB($data);
                }
                catch(customException $e) {
                    customException::cleanUp();
                }
                $this->nextAction('');
                break;

            case "addcomment":
                $postid = $this->getParam('id');
                //$_SESSION['blogcomment'] = $_GET['id'];
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressIM', TRUE);
                //Suppress footer in the page (keep it simple)
                $this->setVar('suppressFooter', TRUE);
                // $this->setVar('pageSuppressXML', TRUE);
                return "commentinput_tpl.php";
                break;

            case "addmetadata":
            //metadata can be a tag, a keyword or whatever else you want about a post
                $userid = $this->getParam('userid');
                $postid = $this->getParam('postid');
                $metakey = $this->getParam('metakey');
                $metavalue = $this->getParam('metavalue');
                //ok so lets sanitize the inputs and dump them to a db
                if (!isset($userid)) {
                    if ($this->objUser->isLoggedIn() == TRUE) {
                        $userid = $this->objUser->userId();
                    } else {
                        //who knows who this is????
                        $this->nextAction('');
                        exit;
                    }
                }
                //lets strip out all the tags from the meta values as they should be plain text
                $metakey = strip_tags($metakey);
                $metavalue = strip_tags($metavalue);
                //is the post id sane?
                if (!isset($postid) || !isset($metakey) || !isset($metavalue)) {
                    return 'meta_tpl.php';
                    exit;
                }
                //all set to go, lets insert it to the db...
                $insarr = array(
                        'userid' => $userid,
                        'post_id' => $postid,
                        'meta_key' => $metakey,
                        'meta_value' => $metavalue
                );
                $ret = $this->objDbBlog->insertMeta($insarr);
                $this->nextAction('postedit', array(
                        'id' => $postid
                ));
                break;

            case 'addrss':
                $rssname = $this->getParam('name');
                $rssurl = $this->getParam('rssurl');
                $rssdesc = $this->getParam('description');
                $userid = $this->objUser->userId();
                $mode = $this->getParam('mode');
                if ($mode == 'edit') {
                    $id = $this->getParam('id');
                    $rdata = $this->objDbBlog->getRssById($id);
                    $this->setVarByRef('rdata', $rdata);
                    return 'rssedit_tpl.php';
                }
                //get the cache
                //get the proxy info if set
                $proxyArr = $this->objProxy->getProxy();
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
                //put in a timestamp
                $addtime = time();
                $addarr = array(
                        'userid' => $userid,
                        'url' => $rssurl,
                        'name' => $rssname,
                        'description' => $rssdesc,
                        'rsscache' => htmlentities($rsscache) ,
                        'rsstime' => $addtime
                );
                //write the file down for caching
                //check that the blog dir exists
                if (!file_exists($this->objConfig->getContentBasePath() . "/blog")) {
                    mkdir($this->objConfig->getContentBasePath() . "/blog");
                    chmod($this->objConfig->getContentBasePath() . "/blog", 0777);
                }
                $path = $this->objConfig->getContentBasePath() . "/blog/rsscache/";
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
                //echo $path;
                //add into the db
                $rssurl = urlencode($rssurl); //, ENT_QUOTES);
                $rssname = htmlentities($rssname, ENT_QUOTES);
                $rssdesc = htmlentities($rssdesc, ENT_QUOTES);
                $addarr = array(
                        'userid' => $userid,
                        'url' => $rssurl,
                        'name' => $rssname,
                        'description' => $rssdesc,
                        'rsscache' => $filename,
                        'rsstime' => $rsstime
                );
                $this->objDbBlog->addRss($addarr);
                $this->nextAction('viewblog');
                break;

            case 'rssedit':
                $mode = $this->getParam('mode');
                $rssname = $this->getParam('name');
                $rssurl = $this->getParam('rssurl');
                $rssdesc = $this->getParam('description');
                $userid = $this->objUser->userId();
                $id = $this->getParam('id');
                if ($mode == 'edit') {
                    $addarr = array(
                            'id' => $id,
                            'userid' => $userid,
                            'url' => $rssurl,
                            'name' => $rssname,
                            'description' => $rssdesc
                    );
                    $this->objDbBlog->addRss($addarr, 'edit');
                }
                $userid = $this->objUser->userid();
                $rss = $this->objDbBlog->getUserRss($userid);
                //send all that to the template
                $this->setVarByRef('rss', $rss);
                $this->setVarByRef('userid', $userid);
                // $this->setVar('pageSuppressXML', TRUE);
                return 'rssedit_tpl.php';
                break;

            case 'deleterss':
                $id = $this->getParam('id');
                $this->objDbBlog->delRSS($id);
                $this->nextAction('rssedit');
                break;

            case 'setprofile':
            //profile stuff
                $mode = $this->getParam('mode');
                $userid = $this->objUser->userId();
                //ok lets check if this user already has a profile or not...
                $check = $this->objDbBlog->checkProfile($userid);
                if ($check != FALSE) {
                    $this->setVarByRef('userid', $userid);
                    $this->setVarByRef('profile', $check);
                    //return the template for editing the profile
                    return 'profile_tpl.php';
                }
                if ($mode == 'saveprofile') {
                    $blogname = addslashes($this->getParam('blogname'));
                    $blogdesc = addslashes($this->getParam('blogdesc'));
                    $blogprofile = addslashes($this->getParam('blogprofile'));
                    //save the profile to the table
                    $prfarr = array(
                            'userid' => $userid,
                            'blog_name' => $blogname,
                            'blog_descrip' => $blogdesc,
                            'blogger_profile' => $blogprofile
                    );
                    $this->objDbBlog->saveProfile($prfarr);
                    $this->nextAction('viewblog');
                    break;
                }
                //set up the template
                $this->setVarByRef('userid', $userid);
                // $this->setVar('pageSuppressXML', TRUE);
                return 'profile_tpl.php';
                break;

            case 'editprofile':
                $mode = $this->getParam('mode');
                $userid = $this->objUser->userId();
                $id = $this->getParam('id');
                if ($mode == 'editprofile') {
                    $blogname = addslashes($this->getParam('blogname'));
                    $blogdesc = addslashes($this->getParam('blogdesc'));
                    $blogprofile = addslashes($this->getParam('blogprofile'));
                    //save the profile to the table
                    $prfarr = array(
                            'id' => $id,
                            'userid' => $userid,
                            'blog_name' => $blogname,
                            'blog_descrip' => $blogdesc,
                            'blogger_profile' => $blogprofile
                    );
                    $this->objDbBlog->updateProfile($prfarr);
                    $this->nextAction('viewblog');
                    break;
                } else {
                    $this->nextAction('viewblog');
                }
                break;

            case 'viewprofile':
                $userid = $this->getParam('userid');
                $vprofile = $this->objDbBlog->checkProfile($userid);
                $this->setVarByRef('vprofile', $vprofile);
                $this->setVarByRef('userid', $userid);
                // $this->setVar('pageSuppressXML', TRUE);
                return 'viewprofile_tpl.php';
                break;

            case 'makepdf':
                $userid = $this->getParam('userid');
                $postid = $this->getParam('postid');
                //go and fetch the post in question from the db
                $data = $this->objDbBlog->getPostByPostID($postid);
                //create the pdf and send it out
                $header = stripslashes($data[0]['post_title']);
                $body = stripslashes($data[0]['post_content']);
                $postdate = $data[0]['post_date'];
                //put it all together
                //get the pdfmaker classes
                $objPdf = $this->getObject('fpdfwrapper', 'pdfmaker');
                $text = $header . "  " . $postdate . "\r\n" . html_entity_decode(strip_tags($body));
                $objPdf->simplePdf($text);
                //$this->nextAction('');
                break;

            case 'mail2friend':
                $bloggerid = $this->getParam('bloggerid');
                $postid = $this->getParam('postid');
                $emailadd = $this->getParam('emailadd');
                $emailadd = explode(",", $emailadd);
                foreach($emailadd as $emails) {
                    $trimmed[] = trim($emails);
                }
                $emailadd = $trimmed;
                $message = $this->getParam('msg');
                $sendername = $this->getParam('sendername');
                if (empty($emailadd[0])) {
                    $m2fdata = array(
                            'bloggerid' => $bloggerid,
                            'postid' => $postid
                    );
                    $this->setVarByRef('m2fdata', $m2fdata);
                    //show the form
                    return 'mail2friend_tpl.php';
                } else {
                    //get the post from the post id
                    $postcontent = $this->objDbBlog->getPostById($postid);
                    //ok we have the content, lets parse for the [img] bbcode tags and replace them with real imgsrc
                    preg_match_all('/\[img\](.*)\[\/img\]/U', $postcontent[0]['post_content'], $matches, PREG_PATTERN_ORDER);
                    unset($matches[0]);
                    $mcount = 0;
                    foreach($matches as $match) {
                        if(isset($match[$mcount])) {
                            $postcontent[0]['post_content'] = preg_replace('/\[img\](.*)\[\/img\]/U', "<img src='" . $match[$mcount] . "'/>", $postcontent[0]['post_content']); //$postcontent[0]['post_content'], $results, PREG_PATTERN_ORDER);
                        }
                        $mcount++;
                    }
                    //thump together an email string (this must be html email as the post is html
                    $objMailer = $this->getObject('mailer', 'mail');
                    //munge together the bodyText...
                    $bodyText = $this->objLanguage->languageText("mod_blog_yourfriend", "blog") . ", " . $sendername . ", " . $this->objLanguage->languageText("mod_blog_interestedin", "blog") . ": <br /> " . "<a href='" . $this->uri(array(
                            'action' => 'viewsingle',
                            'postid' => $postid
                            ) , 'blog') . "'>" . $this->uri(array(
                            'action' => 'viewsingle',
                            'postid' => $postid
                            ) , 'blog') . "</a>";
                    $bodyText.= "<br /><br />";
                    if (!empty($message)) {
                        $bodyText.= $this->objLanguage->languageText("mod_blog_additionalcomments", "blog") . ": <br />";
                        $bodyText.= $message . "<br /><br />";
                    }
                    $bodyText.= stripslashes($postcontent[0]['post_date']);
                    $bodyText.= "<br /><br />";
                    $bodyText.= stripslashes($postcontent[0]['post_content']);
                    $objMailer->setValue('IsHTML', TRUE);
                    $objMailer->setValue('to', $emailadd);
                    $objMailer->setValue('from', 'noreply@uwc.ac.za');
                    $objMailer->setValue('fromName', $this->objLanguage->languageText("mod_blog_email2ffromname", "blog"));
                    $objMailer->setValue('subject', $this->objLanguage->languageText("mod_blog_email2fsub", "blog"));
                    $objMailer->setValue('body', $bodyText);
                    $objMailer->send(TRUE);
                    $this->nextAction('');
                }
                break;

            case 'checkgeo':
                $countrycode = $this->getParam('country');
                //check that the countrycode is a 2 letter string, otherwise try and rectify it.
                $place = $this->getParam('geoplace');
                //echo $place, $countrycode; die();
                $params = array();
                $params['place'] = urlencode($place);
                $params['countrycode'] = urlencode($countrycode);
                $return = $this->objblogOps->findGeoTag($params, '10');
                if (empty($return)) {
                    break;
                }
                $doc = simplexml_load_string($return);
                if ($doc->totalResultsCount > 1) {
                    foreach($doc->geoname as $items) {
                        print_r($items);
                    }
                    die();
                } else {
                    $country = $doc->geoname->countryName;
                    $cuntcode = $doc->geoname->countryCode;
                    $placename = $doc->geoname->name;
                    $lat = $doc->geoname->lat;
                    $lng = $doc->geoname->lng;
                }
                echo "Your info is: " . $placename . " in " . $country . " (" . $cuntcode . ") " . "at lat: " . $lat . " and long: " . $lng;
                break;

            case 'timeline':
                $userid = $this->getParam('userid');
                $info = $this->objDbBlog->getAbsAllPosts($userid);
                $tl = $this->objblogExtras->myBlogTimeline($info, $userid);
                //save the timeline as a file. (Not sure if this is necessary or not...
                $filename = $this->objConfig->getcontentBasePath() . "users/" . $userid . '/' . $userid . '_temptimeline.xml';
                $filepath = $this->objConfig->getcontentBasePath() . "users/" . $userid;
                //check first that the path eists
                if (!file_exists($filepath)) {
                    mkdir($filepath);
                    chmod($filepath, 0777);
                }
                $somecontent = $tl[0];
                if (!file_exists($filename)) {
                    touch($filename);
                    chmod($filename, 0777);
                } else {
                    unlink($filename);
                    touch($filename);
                    chmod($filename, 0777);
                }
                // Let's make sure the file exists and is writable first.
                if (is_writable($filename)) {
                    if (!$handle = fopen($filename, 'w')) {
                        exit;
                    }
                    // Write $somecontent to our opened file.
                    if (fwrite($handle, $somecontent) === FALSE) {
                        exit;
                    }
                    fclose($handle);
                } else {
                    exit;
                }
                $tlurl = $this->objConfig->getsiteRoot() . $this->objConfig->getcontentPath() . "users/" . $userid . '/' . $userid . '_temptimeline.xml';
                $this->setVarByRef('tlurl', $tlurl);
                $this->setVarByRef('userid', $userid);
                $this->setVarByRef('startdate', $tl[1]);
                //$this->setVar("pageSuppressXML", TRUE);
                return "tl_tpl.php";
                break;

            case 'addlink':
            //do some catching and inserting
                $mode = $this->getParam('mode');
                if ($mode == 'edit') {
                    $id = $this->getParam('id');
                    $editvars = $this->objDbBlog->getUserLink($id, $this->objUser->userId());
                    $this->setVarByRef('editvars', $editvars);
                    return 'bloglinks_tpl.php';
                }
                $lurl = $this->getParam('lurl');
                $lname = $this->getParam('lname');
                $ldescription = $this->getParam('ldescription');
                $ltarget = $this->getParam('ltarget');
                $ltype = $this->getParam('ltype');
                $lnotes = $this->getParam('lnotes');
                //echo $lurl, $lname, $ldescription, $ltarget, $ltype, $lnotes;
                //create the insert array and pop into the db
                $insarr = array(
                        'userid' => $this->objUser->userId() ,
                        'link_url' => $lurl,
                        'link_name' => $lname,
                        'link_image' => '',
                        'link_target' => $ltarget,
                        'link_category' => 'default',
                        'link_description' => $ldescription,
                        'link_visible' => '1',
                        'link_owner' => $this->objUser->userName($this->objUser->userId()) ,
                        'link_rating' => 0,
                        'link_updated' => time() ,
                        'link_rel' => '',
                        'link_notes' => $lnotes,
                        'link_rss' => '',
                        'link_type' => $ltype
                );
                $this->objDbBlog->insertUserLink($insarr);
                return 'bloglinks_tpl.php';
                break;

            case 'deletelink':
                $id = $this->getParam('id');
                $this->objDbBlog->deleteBlink($id);
                return 'bloglinks_tpl.php';
                break;

            case 'linkeditor':
                return 'bloglinks_tpl.php';
                break;

            case 'linkedit':
                $id = $this->getParam('id');
                $lurl = $this->getParam('lurl');
                $lname = $this->getParam('lname');
                $ldescription = $this->getParam('ldescription');
                $ltarget = $this->getParam('ltarget');
                $ltype = $this->getParam('ltype');
                $lnotes = $this->getParam('lnotes');
                //echo $lurl, $lname, $ldescription, $ltarget, $ltype, $lnotes;
                //create the insert array and pop into the db
                $insarr = array(
                        'userid' => $this->objUser->userId() ,
                        'link_url' => $lurl,
                        'link_name' => $lname,
                        'link_image' => '',
                        'link_target' => $ltarget,
                        'link_category' => 'default',
                        'link_description' => $ldescription,
                        'link_visible' => '1',
                        'link_owner' => $this->objUser->userName($this->objUser->userId()) ,
                        'link_rating' => 0,
                        'link_updated' => time() ,
                        'link_rel' => '',
                        'link_notes' => $lnotes,
                        'link_rss' => '',
                        'link_type' => $ltype
                );
                $this->objDbBlog->updateUserLink($id, $insarr);
                return 'bloglinks_tpl.php';
                break;

            case 'setpage':
            //page stuff
                $mode = $this->getParam('mode');
                //echo $mode;
                $userid = $this->objUser->userId();
                //ok lets check if this user already has a page or not...
                $check = $this->objDbBlog->getPages($userid);
                if ($mode == 'savepage') {
                    $pagename = addslashes($this->getParam('page_name'));
                    $pagecontent = addslashes($this->getParam('page_content'));
                    // save the page to the table
                    $prfarr = array(
                            'userid' => $userid,
                            'page_name' => $pagename,
                            'page_content' => $pagecontent
                    );
                    $this->setVarByRef('userid', $userid);
                    $this->objDbBlog->savepage($prfarr);
                    $this->nextAction('setpage');
                    break;
                } elseif ($mode == 'editpage') {
                    $pageid = $this->getParam('id');
                    $pagename = addslashes($this->getParam('page_name'));
                    $pagecontent = addslashes($this->getParam('page_content'));
                    if (!empty($pagename) && !empty($pagecontent)) {
                        // save the page to the table
                        $this->objDbBlog->updatePage($pageid, array(
                                'userid' => $userid,
                                'page_name' => $pagename,
                                'page_content' => $pagecontent
                        ));
                        $this->nextAction('setpage');
                    }
                    $userid = $this->objUser->userId();
                    // get the page
                    $page = $this->objDbBlog->getPageById($pageid);
                    $this->setVarByRef('pagetoedit', $page);
                    $this->setVarByRef('userid', $userid);
                    $this->setVarByRef('check', $check);
                    // $this->setVar('pageSuppressXML', TRUE);
                    return 'page_tpl.php';
                    break;
                }
                //set up the template
                $this->setVarByRef('check', $check);
                $this->setVarByRef('userid', $userid);
                // $this->setVar('pageSuppressXML', TRUE);
                return 'page_tpl.php';
                break;

            case 'showpage':
                $pageid = $this->getParam('pageid');
                // grab the page out of the db and display it
                $page = $this->objDbBlog->getPageById($pageid);
                $this->setVarByRef('page', $page);
                // $this->setVar('pageSuppressXML', TRUE);
                return 'pageview_tpl.php';
                break;

            case 'deletepage':
                $pageid = $this->getParam('id');
                $this->objDbBlog->deletePage($pageid);
                $this->nextAction('setpage');
                break;

            case 'viewgeoblog':
                $userid = $this->getParam('userid');
                $this->setVarByRef('userid', $userid);
                return 'geotagged_tpl.php';
                break;

            case 'blogsearch':
                $userid = $this->objUser->userId();
                $seekterm = $this->getParam('searchterm');
                $seekterm = trim($seekterm);
                $seekterm = strip_tags($seekterm);
                $res = $this->objblogSearching->quickSearch($seekterm);
                $this->setVarByRef('searchres', $res);
                $this->setVarByRef('userid', $userid);
                // $this->setVar('pageSuppressXML', TRUE);
                return 'searchres_tpl.php';
            case 'googlegadget':
                echo $this->objblogPosts->showLastTenPosts();
            case 'api':
                $this->requiresLogin(FALSE);
                $objApi = $this->getObject('blogxmlrpc');
                $objApi->serve();
                break;

            case 'updatesearch':
                $this->objDbBlog->updateSearch();
                return $this->nextAction('allblogs');

            case 'deletetb':
                $id = $this->getParam('id', NULL);
                $pid = $this->getParam('pid');
                $this->objDbBlog->deleteTrackBack($id);
                $this->nextAction('viewsingle', array(
                        'postid' => $pid,
                        'userid' => $this->objUser->userId()
                ));
            //echo $id; die();

        } //action

    }
    /**
     * Overide the login object in the parent class
     *
     * @param  void
     * @return bool
     * @access public
     */
    public function requiresLogin($action) {
        $actionsRequiringLogin = array(
                'blogadmin',
        );
        if (in_array($action, $actionsRequiringLogin)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    /**
     * Check if user is allowed to blog
     * @returns boolean
     */
    public function approvedBlogger() {
        $blogSetting = $this->objSysConfig->getValue('limited_users', 'blog');
        if ($blogSetting) {
            $groupId = $this->objGroup->getId('Bloggers');
            $userId = $this->objUser->userid();
            return $this->objGroup->isGroupMember($userId, $groupId);
        } else {
            return TRUE;
        }
    }
}
?>
