<?php
/**
 * Class to handle blog elements (links and blogroll).
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
 *
 * PHP version 5
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
 * @version    $Id: blogposts_class_inc.php 24726 2012-10-01 05:30:33Z dkeats $
 * @package    blog
 * @subpackage blogops
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
 */
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
 * Class to handle blog elements (links and blogroll)
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @category  Chisimba
 * @package   blog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: blogposts_class_inc.php 24726 2012-10-01 05:30:33Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class blogposts extends object
{
    /**
     * Object of the Creative Commons Display License class.
     *
     * @access protected
     * @var object $objCC
     */
    protected $objCC;

    /**
     * Description for public
     *
     * @var    mixed
     * @access public
     */
    public $objConfig;

    /**
     * Instance of the tweetbutton class of the twitter module.
     *
     * @access private
     * @var    object
     */
    private $objTweetButton;

    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init()
    {
        try {
            $this->objCC = $this->getObject('displaylicense', 'creativecommons');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objDbBlog = $this->getObject('dbblog');
            $this->objTweetButton = $this->getObject('tweetbutton', 'twitter');
            $this->loadClass('href', 'htmlelements');
            $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->showfullname = $this->sysConfig->getValue('show_fullname', 'blog');
            $this->objUser = $this->getObject('user', 'security');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
        if (!extension_loaded("imap")) {
            $this->mail2blog = FALSE;
        }
    }

    /**
     * Clean up posts messed up by MS mail clients
     * added by davidwaf without any consideration of coding standards
     *
     * @param string $post THe post to clean
     * @return strnig The cleaned post
     *
     */
    function cleanPost($post){
        $lines=split("<br />",$post);
        $results="";
         foreach($lines as $line){
            $line=trim($line);
            if(trim($line) != '=20' ) {
                if(strlen(trim($line)) < 1){
                    $results.=$line.'<br/>';
                } else {
                    $results.=trim($line).' ';
                }
            }
        }
        $results2="";
        $lines2=split("<br/>",$results);
        foreach($lines2 as $line2){
            $line2=trim($line2);
            if(strlen(trim($line2)) < 1) {
                $results2.=$line2.'<br/>';
            } else {
                $pos = strrpos($line2, "</a>");
                if ($pos === false) { // note: three equal signs
                //not found
                } else {
                    $line2=str_replace('</a>','',$line2);
                    $line2.='</a>';
                }
                $results2.=trim($line2).'<br/>';
            }
        }
        $results2=str_replace('=20','',$results2);
        $results2=str_replace('= ','',$results2);
        return $results2;
    }

    /**
     * Method to display the posts per user
     *
     * @param  array  $posts
     * @return string
     */
    public function showPosts($posts, $showsticky = FALSE)
    {
        $cleanPost=$this->sysConfig->getValue('blog_clean_ms_chars', 'blog');
        $objCommentCounter = $this->getObject('dynamiccommentcounter', 'blog');
        $this->objComments = $this->getObject('commentapi', 'blogcomments');
        $this->objTB = $this->getObject("trackback");
        // set the trackback options
        $tboptions = array(
            // Options for trackback class
            'strictness' => 1,
            'timeout' => 30, // seconds
            'fetchlines' => 30,
            'fetchextra' => true,
            // Options for HTTP_Request class
            'httprequest' => array(
                'allowRedirects' => true,
                'maxRedirects' => 2,
                'method' => 'GET',
                'useragent' => 'Chisimba',
            ) ,
        );
        $ret = NULL;
        // Middle column (posts)!
        $this->objJqTwitter = $this->getObject('jqtwitter', 'twitter');
        // break out the ol featurebox...
        if (!empty($posts)) {
            // get the washout class and parse for all the bits and pieces
            $washer = $this->getObject('washout', 'utilities');
            // Humanize date object
            $objHumanizeDate = $this->getObject("translatedatedifference", "utilities");
            // Loop over the posts
            foreach($posts as $post) {
                $cleanpost = $washer->parseText($post['post_content']);
                $url = $this->uri(array(
                        'action' => 'viewsingle',
                        'postid' => $post['id'],
                        'userid' => $post['userid']
                    	));
                $related = $this->sysConfig->getValue('retweet_related', 'blog');
                $status = $this->sysConfig->getValue('retweet_status', 'blog');
                $style = $this->sysConfig->getValue('retweet_style', 'blog');
                $text = $this->sysConfig->getValue('retweet_text', 'blog');
                $type = $this->sysConfig->getValue('retweet_type', 'blog');
                $via = $this->sysConfig->getValue('retweet_via', 'blog');
                if($status == NULL){
                    $status = "Interesting read ";
                }
                if($style == NULL) {
                    $style = 'retweet vert';
                }
                if ($type == 'jquery') {
                    $rt = $this->objJqTwitter->retweetCounter($url, $status, $style);
                } else {
                    if (strpos($style, 'vert') !== FALSE) {
                        $style = 'vertical';
                    }
                    $rt = $this->objTweetButton->getButton($post['post_title'], $style, $via, $related, htmlspecialchars_decode($url));
                }
                $post['post_content'] = $cleanpost;
                if($cleanPost  == 'true'){
                    $post['post_content'] = $this->cleanPost($post['post_content']);
                }
                //$post['post_content']=quoted_printable_decode($post['post_content']);
                $objFeatureBox = $this->getObject('featurebox', 'navigation');
                // build the top level stuff
                $showabsolutedate = $this->sysConfig->getValue('showabsolutedate', 'blog', 'FALSE');
                if (strtoupper($showabsolutedate) == 'TRUE') {
                    $dt = date('r', $post['post_ts']);
                } else {
                    $dt = $objHumanizeDate->getDifference(date('Y-m-d H:i:s', $post['post_ts']));
                }
                $this->objUser = $this->getObject('user', 'security');
                $userid = $this->objUser->userId();
                if ($showsticky == FALSE) {
                    if ($post['stickypost'] == 1) {
                        unset($post);
                        continue;
                    }
                }
                $icons = '';
                if ($post['userid'] == $userid) {
                    $this->objIcon = $this->getObject('geticon', 'htmlelements');
                    $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                        'action' => 'postedit',
                        'id' => $post['id'],
                        'module' => 'blog'
                        )));
                    $icons .= " $edIcon";
                }
                if($post['userid'] == $userid || $this->objUser->isAdmin() || $this->objUser->inAdminGroup($this->objUser->userId() )) {
                    $this->objIcon = $this->getObject('geticon', 'htmlelements');
                    $delIcon = $this->objIcon->getDeleteIconWithconfirm($post['id'],array('action'=>'deletepost','id'=>$post['id'],'nextAction'=>'viewblog'),'blog');
                    $icons .= " $delIcon";
                }
                if ($post['stickypost'] == 1) {
                    $objStickyIcon = $this->newObject('geticon', 'htmlelements');
                    $objStickyIcon->setIcon('sticky_yes');
                    if($post['post_status'] == 1) {
                    	$headLink = new href($this->uri(array(
                        'action' => 'viewsingle',
                        'postid' => $post['id'],
                        'userid' => $post['userid']
                        )) , stripslashes($this->objLanguage->languageText("mod_blog_draft","blog")." ".$post['post_title']) , NULL);
                    } else {
                    	$headLink = new href($this->uri(array(
                        'action' => 'viewsingle',
                        'postid' => $post['id'],
                        'userid' => $post['userid']
                    	)) , stripslashes($post['post_title']) , NULL);
                    }
                    $head = $objStickyIcon->show() . $headLink->show()
                      . " $rt $icons<br /><span class='blog-head-date'>$dt</span>";
                } else {
                    if($post['post_status'] == 1) {
                        $headLink = new href($this->uri(array(
                        'action' => 'viewsingle',
                        'postid' => $post['id'],
                        'userid' => $post['userid']
                        )) , stripslashes($this->objLanguage->languageText("mod_blog_draft","blog")
                        ." ".$this->objLanguage->languageText("mod_blog_word_post","blog").": "
                        .$post['post_title']) , NULL);
                    } else {
                        $headLink = new href($this->uri(array(
                            'action' => 'viewsingle',
                            'postid' => $post['id'],
                            'userid' => $post['userid']
                    )) , stripslashes($post['post_title']) , NULL);
                    }
                    $head = $headLink->show()
                      . " $rt $icons<br /><span class='blog-head-date'>$dt</span><br />";
                }
                // dump in the post content and voila! you have it...
                // build the post content plus comment count and stats???
                // do the BBCode Parsing
                try {
                    $this->bbcode = $this->getObject('bbcodeparser', 'utilities');
                } catch(customException $e) {
                    customException::cleanUp();
                }
                // $post['post_content'] = stripslashes($this->bbcode->parse4bbcode($post['post_content']));
                $this->cleaner = $this->newObject('htmlcleaner', 'utilities');
                // set up the trackback link
                $bloggerprofile = $this->objDbBlog->checkProfile($this->objUser->userId());
                if (isset($bloggerprofile['blog_name'])) {
                    $blog_name = $bloggerprofile['blog_name'];
                    // $this->getParam('blog_name');

                } else {
                    if ($this->showfullname == 'FALSE') {
                        $blog_name = $this->objUser->userName($userid);
                    } else {
                        $blog_name = $this->objUser->fullname($userid);
                    }
                }
                // $blog_name = $this->objUser->fullname($userid);
                $url = $this->uri(array(
                    'action' => 'randblog',
                    'userid' => $userid,
                    'module' => 'blog'
                ));
                $trackback_url = $this->uri(array(
                    'action' => 'tbreceive',
                    'userid' => $post['userid'],
                    'module' => 'blog',
                    'postid' => $post['id']
                ));
                $pdfurl = $this->uri(array(
                    'action' => 'makepdf',
                    'userid' => $post['userid'],
                    'module' => 'blog',
                    'postid' => $post['id']
                ));
                $extra = NULL;
                $tbdata = array(
                    'id' => $post['id'],
                    'title' => $post['post_title'],
                    'excerpt' => $post['post_excerpt'],
                    'blog_name' => $blog_name,
                    'url' => $url,
                    'trackback_url' => $trackback_url,
                    'extra' => $extra
                );
                $this->objTB->setup($tbdata, $tboptions);
                $linktxt = $this->objLanguage->languageText("mod_blog_trackbackurl", "blog");
                $tburl = new href($trackback_url, $linktxt, NULL);
                $tburl = $tburl->show();
                // set up the link to SEND a trackback
                if (isset($bloggerprofile['blog_name'])) {
                    $blog_name = $bloggerprofile['blog_name'];
                    // $this->getParam('blog_name');

                } else {
                    if ($this->showfullname == 'FALSE') {
                        $blog_name = $this->objUser->userName($userid);
                    } else {
                        $blog_name = $this->objUser->fullname($userid);
                    }
                }
                // $blog_name = $this->objUser->fullname($userid);
                $sender = $this->uri(array(
                    'action' => 'tbsend',
                    'userid' => $post['userid'],
                    'module' => 'blog',
                    'postid' => $post['id'],
                    'blog_name' => $blog_name,
                    'title' => $post['post_title'],
                    'excerpt' => $post['post_excerpt'],
                    'url' => urlencode($this->uri(array(
                        'action' => 'viewsingle',
                        'userid' => $post['userid'],
                        'module' => 'blog',
                        'postid' => $post['id']
                    ))) ,
                ));
                $sendtblink = new href($sender, $this->objLanguage->languageText("mod_blog_sendtrackback", "blog") , NULL);
                $sendtblink = $sendtblink->show();
                $bmurl = $this->uri(array(
                    'action' => 'viewsingle',
                    'userid' => $post['userid'],
                    'module' => 'blog',
                    'postid' => $post['id']
                ));
                $permaHash = md5($bmurl);
                $wallDiv = '<div class="wall" id="' . $permaHash . '">';
                $bmurl = urlencode($bmurl);
                $bmlink = "http://www.addthis.com/bookmark.php?pub=&amp;url=" . $bmurl . "&amp;title=" . urlencode(addslashes(htmlentities($post['post_title'])));
                $bmtext = '<img src="'.$this->getResourceUri('button1-bm.gif', 'blog').'" width="125" height="16" border="0" alt="' . $this->objLanguage->languageText("mod_blog_bookmarkpost", "blog") . '"/>';
                // $this->objLanguage->languageText("mod_blog_bookmarkpost", "blog");
                $bookmark = new href($bmlink, $bmtext, NULL);
                // grab the number of trackbacks per post
                $pid = $post['id'];
                $numtb = $this->objDbBlog->getTrackbacksPerPost($pid);
                if ($numtb != 0) {
                    $pLink = $this->uri(array(
                        'module' => 'blog',
                        'action' => 'viewsingle',
                        'mode' => 'viewtb',
                        'postid' => $pid,
                        'userid' => $post['userid']
                    ));
                    $numtblnk = new href($pLink, $this->objLanguage->languageText("mod_blog_vtb", "blog") , NULL);
                    // $numtb, NULL);
                    $numtb = $numtblnk->show();
                } else {
                    $numtb = $this->objLanguage->languageText("mod_blog_trackbacknotrackback", "blog");
                }
                // do the cc licence part
                // do the cc licence part
                $cclic = $post['post_lic'];
                // get the lic that matches from the db
                if ($cclic == '') {
                    $cclic = 'copyright';
                }
                $iconList = $this->objCC->show($cclic);
                // $commentLink = $this->objComments->addCommentLink($type = NULL);
                if ($post['comment_status'] == 'Y' || $post['comment_status'] == 'on') {
                    $commentCount = $objCommentCounter->show($post['id']);
                    //$this->objComments->getCount($post['id']);
                }
                // edit icon in a table 1 row x however number of things to do
                if ($post['userid'] == $userid) {
                    $tburl = $tburl . "<br />" . $numtb . "<br />" . $sendtblink;

                    // Set the table name
                    $tbl = $this->newObject('htmltable', 'htmlelements');
                    $tbl->cellpadding = 3;
                    $tbl->width = "100%";
                    $tbl->align = "center";
                    $tbl->startRow();
                    $tbl->addCell($bookmark->show());
                    // bookmark link(s)
                    if ($post['comment_status'] == 'Y' || $post['comment_status'] == 'on') {
                        $tbl->addCell($this->setComments($post, FALSE) . " " . $commentCount);
                        // $commentLink);
                        // comment link(s)

                    }
                    $tbl->addCell($tburl);
                    // trackback URL
                    $tbl->addCell($iconList);
                    // cc licence
                    $pdficon = $this->newObject('geticon', 'htmlelements');
                    $pdficon->setIcon('filetypes/pdf');
                    $lblView = $this->objLanguage->languageText("mod_blog_saveaspdf", "blog");
                    $pdficon->alt = $lblView;
                    $pdficon->align = false;
                    $pdfimg = $pdficon->show();
                    $pdflink = new href($pdfurl, $pdfimg, NULL);
                    // and the mail to a friend icon
                    $mtficon = $this->newObject('geticon', 'htmlelements');
                    $mtficon->setIcon('filetypes/eml');
                    $lblmtf = $this->objLanguage->languageText("mod_blog_mailtofriend", "blog");
                    $mtficon->alt = $lblmtf;
                    $mtficon->align = false;
                    $mtfimg = $mtficon->show();
                    $mtflink = new href($this->uri(array(
                        'action' => 'mail2friend',
                        'postid' => $post['id'],
                        'bloggerid' => $post['userid']
                    )) , $mtfimg, NULL);
                    if ($post['showpdf'] == '1' || $post['showpdf'] == 'on') {
                        $tbl->addCell($pdflink->show() . $mtflink->show());
                    }
                    $tbl->endRow();

                    $bottombar = '<div class="blog_bottombar">' . $tbl->show() . '</div>';
                    // echo $this->objTB->autodiscCode();
                    // tack the tags onto the end of the post content...
                    $thetags = $this->objDbBlog->getPostTags($post['id']);
                    $linkstr = NULL;
                    foreach($thetags as $tags) {
                        $link = new href($this->uri(array(
                            'action' => 'viewblogbytag',
                            'userid' => $userid,
                            'tag' => $tags['meta_value']
                        )), stripslashes($tags['meta_value']), "rel=\"tag\"");
                        $linkstr.= $link->show();
                        $link = NULL;
                    }
                    if (empty($linkstr)) {
                        $linkstr = $this->objLanguage->languageText("mod_blog_word_notags", "blog");
                    }
                    $pcontent = "<div class='blog_content'>" . $post['post_content'] . "</div>";
                    $fboxcontent = $pcontent
                      . $this->cleaner->cleanHtml("<br /><hr />"
                      . "<div class='blog-item-base'><center><em><b>"
                      . $this->objLanguage->languageText("mod_blog_word_tags4thispost", "blog")
                      . "</b><br />" . $linkstr . "</em><hr />"
                      . $bottombar . "</center></div>");
                    $ret.= '<div class="blogpost_before"></div>'
                      . $objFeatureBox->showContent($head, $fboxcontent)
                      . '<div class="blogpost_after"></div>';
                } else {
                    // table of non logged in options
                    // Set the table name
                    $tblnl = $this->newObject('htmltable', 'htmlelements');
                    $tblnl->cellpadding = 3;
                    $tblnl->width = "100%";
                    $tblnl->align = "center";
                    $tblnl->startRow();
                    $tblnl->addCell($bookmark->show());
                    // bookmark link(s)
                    $tblnl->addCell($tburl . "&nbsp;" . $numtb);
                    // trackback URL
                    if ($post['comment_status'] == 'Y' || $post['comment_status'] == 'on') {
                        $tblnl->addCell($this->setComments($post, FALSE) . " " . $commentCount);
                    }
                    $tblnl->addCell($iconList);
                    // cc licence
                    $pdficon = $this->newObject('geticon', 'htmlelements');
                    $pdficon->setIcon('filetypes/pdf');
                    $lblView = $this->objLanguage->languageText("mod_blog_saveaspdf", "blog");
                    $pdficon->alt = $lblView;
                    $pdficon->align = false;
                    $pdfimg = $pdficon->show();
                    $pdflink = new href($pdfurl, $pdfimg, NULL);
                    // and the mail to a friend icon
                    $mtficon = $this->newObject('geticon', 'htmlelements');
                    $mtficon->setIcon('filetypes/eml');
                    $lblmtf = $this->objLanguage->languageText("mod_blog_mailtofriend", "blog");
                    $mtficon->alt = $lblmtf;
                    $mtficon->align = false;
                    $mtfimg = $mtficon->show();
                    $mtflink = new href($this->uri(array(
                        'action' => 'mail2friend',
                        'postid' => $post['id'],
                        'bloggerid' => $post['userid']
                    )) , $mtfimg, NULL);
                    if ($post['showpdf'] == '1' || $post['showpdf'] == 'on') {
                        $tblnl->addCell($pdflink->show() . $mtflink->show());
                    }
                    // pdf icon
                    $tblnl->endRow();
                    // Bottombar with bookmark, trackback, etc
                    $bottombar = '<div class="blog_bottombar">' . $tblnl->show()
                      . '</div>';
                    // echo $this->objTB->autodiscCode();
                    // tack the tags onto the end of the post content...
                    $thetags = $this->objDbBlog->getPostTags($post['id']);
                    $linkstr = NULL;
                    foreach($thetags as $tags) {
                        $link = new href($this->uri(array(
                            'action' => 'viewblogbytag',
                            'userid' => $post['userid'],
                            'tag' => $tags['meta_value']
                        )) , stripslashes($tags['meta_value']), "rel=\"tag\"");
                        $linkstr.= $link->show();
                        $link = NULL;
                    }
                    if (empty($linkstr)) {
                        $linkstr = $this->objLanguage->languageText("mod_blog_word_notags", "blog");
                    }
                    $pcontent = "<div class='blog_content'>" . $post['post_content'] . "</div>";
                    $ret.= '<div class="blogpost_before"></div>'
                      . $objFeatureBox->showContent($head, $pcontent
                      . $this->cleaner->cleanHtml("<br /><hr /><div class='blog-item-base'><center><em><b>"
                      . $this->objLanguage->languageText("mod_blog_word_tags4thispost", "blog")
                      . "</b><br />" . $linkstr . "</em><br />"
                      .  $bottombar . "</center></div>" ))
                      . '<div class="blogpost_after"></div>';
                }
            }
        } else {
            $ret = FALSE;
        }
        return $ret ;
    }
    /**
     * Method to quick add a post to the posts table
     *
     * @param integer $userid
     * @param array   $postarr
     * @param string  $mode
     */
    public function quickPostAdd($userid, $postarr, $mode = NULL)
    {
        // check the sysconfig as to whether we should enable the google ping
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->googleBlogPing = $this->objSysConfig->getValue('ping_google', 'blog');
        if ($this->googleBlogPing == 'TRUE') {
            $this->pingGoogle($userid);
        }
        if (!empty($postarr)) {
            if ($mode == NULL) {
                $this->objDbBlog->insertPost($userid, $postarr);
            } else {
                $this->objDbBlog->insertPost($userid, $postarr, $mode);
            }
        }
    }
    /**
     * Method to display the posts editor in its entirety
     *
     * @param  integer $userid
     * @param  integer $editid
     * @param  string $defaultText Default Text to be populated in the Editor
     * @return boolean
     */
    public function postEditor($userid, $editid = NULL, $defaultText = NULL)
    {
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        // $this->loadClass('heading', 'htmlelements');
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        if (isset($editid)) {
            $mode = 'editpost';
            // get the relevant post from the editid
            $editparams = $this->objDbBlog->getPostById($editid);
            if (!empty($editparams)) {
                //               print_r($editparams);
                $editparams = $editparams[0];
                $editparams['tags'] = $this->objDbBlog->getPostTags($editid);
            }
        }
        if (!isset($mode)) {
            $mode = NULL;
        }
        if (!isset($editparams)) {
            $editparams = NULL;
        }
        $postform = new form('postadd', $this->uri(array(
            'action' => 'postadd',
            'mode' => $mode,
            'id' => $editparams['id'],
            'postexcerpt' => $editparams['post_excerpt'],
            'postdate' => $editparams['post_date']
        )));
        $pfieldset = $this->newObject('fieldset', 'htmlelements');
        if ($this->getParam('action', 'add')=='postedit') {
            $pFieldSetLabel = $this->objLanguage->languageText('mod_blog_postedit', 'blog');
        } else {
            $pFieldSetLabel = $this->objLanguage->languageText('mod_blog_posthead', 'blog');
        }
        $pfieldset->setLegend(' ' . $pFieldSetLabel . ' ');
        $ptable = $this->newObject('htmltable', 'htmlelements');
        $ptable->cellpadding = 5;
        // post title field
        $ptable->startRow();
        $plabel = new label($this->objLanguage->languageText('mod_blog_posttitle', 'blog') . ':', 'input_posttitle');
        $title = new textinput('posttitle');
        $title->size = 60;
        $postform->addRule('posttitle', $this->objLanguage->languageText("mod_blog_phrase_ptitlereq", "blog") , 'required');
        if (isset($editparams['post_title'])) {
            $title->setValue(stripslashes($editparams['post_title']));
        }
        $ptable->addCell($plabel->show());
        $ptable->addCell($title->show());
        $ptable->endRow();
        // post category field
        // dropdown of cats
        $ptable->startRow();
        $pdlabel = new label($this->objLanguage->languageText('mod_blog_postcat', 'blog') . ':', 'input_cat');
        $pDrop = new dropdown('cat');
        if (isset($editparams['post_category'])) {
        	// var_dump($editparams);
        	// category voodoo
            if ($editparams['post_category'] == '0') {
                $epdisp = $this->objLanguage->languageText("mod_blog_word_default", "blog");
            } else {
                $mapcats = $this->objDbBlog->mapKid2Parent($editparams['post_category']);
                if (isset($mapcats[0])) {
                    $epdisp = $mapcats[0]['cat_name'];
                }
            }
            $pDrop->addOption($editparams['post_category'], $epdisp);
            $pDrop->setSelected($editparams['post_category']);
            $pDrop->addOption(1, $this->objLanguage->languageText("mod_blog_defcat", "blog"));
        } else {
            $pDrop->addOption(0, $this->objLanguage->languageText("mod_blog_defcat", "blog"));
        }
        $pcats = $this->objDbBlog->getAllCats($userid);
        foreach($pcats as $adds) {
            $pDrop->addOption($adds['id'], stripslashes($adds['cat_name']));
        }
        $ptable->addCell($pdlabel->show());
        $ptable->addCell($pDrop->show());
        $ptable->endRow();
        // post status dropdown
        $ptable->startRow();
        $pslabel = new label($this->objLanguage->languageText('mod_blog_poststatus', 'blog') . ':', 'input_status');
        $psDrop = new dropdown('status');
        $psDrop->addOption(0, $this->objLanguage->languageText("mod_blog_published", "blog"));
       	$psDrop->addOption(1, $this->objLanguage->languageText("mod_blog_draft", "blog"));
        if (isset($editparams['post_status']) && $editparams['post_status'] == 1) {
        	$psDrop->setSelected(1);
        } else {
       		$psDrop->setSelected(0);
        }
        $ptable->addCell($pslabel->show());
        $ptable->addCell($psDrop->show());
        $ptable->endRow();
        // allow comments?
        $this->loadClass("checkbox", "htmlelements");
        $commentsallowed = new checkbox('commentsallowed', $this->objLanguage->languageText("mod_blog_word_yes", "blog") , true);
        $ptable->startRow();
        $pcomlabel = new label($this->objLanguage->languageText('mod_blog_commentsallowed', 'blog') . ':', 'input_commentsallowed');
        $ptable->addCell($pcomlabel->show());
        $ptable->addCell($commentsallowed->show());
        $ptable->endRow();
        // Sticky post?
        $this->loadClass("checkbox", "htmlelements");
        if (isset($editparams['stickypost']) && $editparams['stickypost'] == 1) {
            $sticky = new checkbox('stickypost', 1, TRUE);
        } else {
            $sticky = new checkbox('stickypost', 1, FALSE);
        }
        $ptable->startRow();
        $pstickylabel = new label($this->objLanguage->languageText('mod_blog_stickypost', 'blog') . ':', 'input_stickypost');
        $ptable->addCell($pstickylabel->show());
        $ptable->addCell($sticky->show());
        $ptable->endRow();
        // show as a PDF?
        $this->loadClass("checkbox", "htmlelements");
        if (isset($editparams['showpdf']) && $editparams['showpdf'] == 1) {
            $showpdf = new checkbox('showpdf', 1, TRUE);
        } else {
            $showpdf = new checkbox('showpdf', 1, FALSE);
        }
        $ptable->startRow();
        $showpdflabel = new label($this->objLanguage->languageText('mod_blog_showpdf', 'blog') . ':', 'input_showpdf');
        $ptable->addCell($showpdflabel->show());
        $ptable->addCell($showpdf->show());
        $ptable->endRow();
        // post excerpt
        $this->loadClass('textarea', 'htmlelements');
        $pexcerptlabel = new label($this->objLanguage->languageText('mod_blog_postexcerpt', 'blog') . ':', 'input_postexcerpt');
        $pexcerpt = new textarea('postexcerpt');
        $pexcerpt->setName('postexcerpt');
        $ptable->startRow();
        if (isset($editparams['post_excerpt'])) {
            $pexcerpt->setcontent(stripslashes($editparams['post_excerpt']));
            // nl2br - htmmlentittes +

        }
        $ptable->addCell($pexcerptlabel->show());
        $ptable->addCell($pexcerpt->show());
        $ptable->endRow();
        // post content
        $pclabel = new label($this->objLanguage->languageText('mod_blog_pcontent', 'blog') . ':', 'input_postcontent');
        $pcon = $this->newObject('htmlarea', 'htmlelements');
        $pcon->setName('postcontent');
        $pcon->height = 400;
        $pcon->width = '100%';
        $pcon->setDefaultToolbarSet();
        if (isset($editparams['post_content'])) {
            $pcon->setcontent((stripslashes(($editparams['post_content']))));
        } else if (!is_null($defaultText)) {
            $pcon->setcontent($defaultText);
        }
        $ptable->startRow();
        $ptable->addCell($pclabel->show());
        $ptable->addCell($pcon->show());
        $ptable->endRow();
        // tags input box
        $ptable->startRow();
        $tlabel = new label($this->objLanguage->languageText('mod_blog_tags', 'blog') . ':', 'input_tags');
        $tags = new textinput('tags');
        $tags->size = '65%';
        if (isset($editparams['tags'])) {
            // this thing should be an array, so we need to loop thru and create the comma sep list again
            $tagstr = NULL;
            foreach($editparams['tags'] as $taglets) {
                $tagstr.= $taglets['meta_value'] . ",";
            }
            $tags->setValue(stripslashes($tagstr));
        }
        $ptable->addCell($tlabel->show());
        $ptable->addCell($tags->show());
        $ptable->endRow();
        // CC licence
        $lic = $this->getObject('licensechooser', 'creativecommons');
        $ptable->startRow();
        $pcclabel = new label($this->objLanguage->languageText('mod_blog_cclic', 'blog') . ':', 'input_cclic');
        $ptable->addCell($pcclabel->show());
        if (isset($editparams['post_lic'])) {
            $lic->defaultValue = $editparams['post_lic'];
        }
        $ptable->addCell($lic->show());
        $ptable->endRow();

        // geoTagging map part
        // only show this is simplemap module is installed - we need the gmaps api key stored there
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        if($this->objModules->checkIfRegistered('simplemap') && $this->objModules->checkIfRegistered('georss'))
        {
        	$this->objHead = $this->getObject('htmlheading', 'htmlelements');
        	$this->objHead->type = 3;
        	$this->objHead->str = $this->objLanguage->languageText("mod_blog_geotagposts", "blog");
        	$gmapsapikey = $this->sysConfig->getValue('mod_simplemap_apikey', 'simplemap');
        	$css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: grey;
        }
    </style>';

        	$google = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$gmapsapikey."' type=\"text/javascript\"></script>";
        	$olsrc = $this->getJavascriptFile('lib/OpenLayers.js','georss');
        	$js = "<script type=\"text/javascript\">
        var lon = 5;
        var lat = 40;
        var zoom = 17;
        var map, layer, drawControl, g;

        OpenLayers.ProxyHost = \"/proxy/?url=\";
        function init(){
            g = new OpenLayers.Format.GeoRSS();
            map = new OpenLayers.Map( 'map' , { controls: [] , 'numZoomLevels':20 });
            var hybrid = new OpenLayers.Layer.Google( \"Google Hybrid Map\" , {type: G_HYBRID_MAP, 'maxZoomLevel':18} );
            var wmsLayer = new OpenLayers.Layer.WMS( \"Public WMS\",
                \"http://labs.metacarta.com/wms/vmap0?\", {layers: 'basic'});

            map.addLayers([wmsLayer, hybrid]);

            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl( new OpenLayers.Control.MouseDefaults() );
            map.addControl( new OpenLayers.Control.LayerSwitcher() );
            map.addControl( new OpenLayers.Control.PanZoomBar() );

            map.setCenter(new OpenLayers.LonLat(0,0), 2);

            map.events.register(\"click\", map, function(e) {
                var lonlat = map.getLonLatFromViewPortPx(e.xy);
                OpenLayers.Util.getElement(\"input_geotag\").value = lonlat.lat + \",  \" +
                                          + lonlat.lon
            });

        }
    </script>";

        	// add the lot to the headerparams...
        	$this->appendArrayVar('headerParams', $css.$google.$olsrc.$js);
        	$this->appendArrayVar('bodyOnLoad', "init();");
        	// add the table row with the map in it.
        	// a heading
        	$ptable->startRow();
        	$ptable->addCell('');
        	$ptable->addCell($this->objHead->show()); // , '100%', $valign="top", 'center', null, 'colspan=2','0');
        	$ptable->endRow();
            // and now the map
        	$ptable->startRow();
        	$gtlabel = new label($this->objLanguage->languageText('mod_blog_geotag', 'blog') . ':', 'input_geotags');
        	$gtags = '<div id="map"></div>';
        	$geotags = new textinput('geotag', NULL, NULL, '100%');
        	if (isset($editparams['geolat']) && isset($editparams['geolon'])) {
            	$geotags->setValue($editparams['geolat'].", ".$editparams['geolon']);
        	}
        	$ptable->addCell($gtlabel->show());
        	$ptable->addCell($gtags.$geotags->show());
        	$ptable->endRow();
        }

        $ts = new textinput('post_ts', NULL, 'hidden', NULL);
        // $ts->extra = "hidden";
        if (isset($editparams['post_ts'])) {
            $ts->setValue($editparams['post_ts']);
        }
        $postform->addRule('posttitle', $this->objLanguage->languageText("mod_blog_phrase_ptitlereq", "blog") , 'required');
        // $postform->addRule('postcontent', $this->objLanguage->languageText("mod_blog_phrase_pcontreq", "blog"),'required');
        $pfieldset->addContent($ptable->show());
        $postform->addToForm($pfieldset->show() . $ts->show());
        $this->objPButton = &new button($this->objLanguage->languageText('mod_blog_word_post', 'blog'));
        $this->objPButton->setIconClass("save");
        $this->objPButton->setValue($this->objLanguage->languageText('mod_blog_word_post', 'blog'));
        $this->objPButton->setToSubmit();
        // $postform->addToForm($this->objPButton->show());
        // $postform = $postform->show();
        // return $postform;
        // check box Added By Irshaad Hoosain
        $this->loadClass('checkbox', 'htmlelements');
        $siteblogcheckbox = new checkbox('checkbox');
        // ,'unassign',false);
        $siteblogcheckbox = $siteblogcheckbox->show();
        // IS Admin
        $siteblogcheckbox = new checkbox('checkbox');
        // ,'unassign',false);
        $siteblogcheckbox = $siteblogcheckbox->show();
        // IS Admin
        $this->objUser = $this->getObject('user', 'security');
        if ($this->objUser->inAdminGroup($userid, 'Site Admin')) {
            $postform->addToForm('Site Blog' . ' ' . $siteblogcheckbox);
        } else {
        }
        $postform->addToForm('<br>' . ' ' . '</br>');
        $postbutton_text = $this->objPButton->show();
        $postform->addToForm($postbutton_text);
        $postform = $postform->show();
        return $postform;
    }
    /**
     * Method to get the archiveed posts array for manipulation
     *
     * @param  string  $userid
     * @return array
     * @access private
     */
    private function _archiveArr($userid)
    {
        // add in a foreach for each year
        $allposts = $this->objDbBlog->getAbsAllPosts($userid);
        // print_r($allposts);
        $revposts = array_reverse($allposts);
        $recs = count($revposts);
        if ($recs > 0) {
            $recs = $recs-1;
        }
        if (!empty($revposts)) {
            // echo count($revposts);
            $lastrec = $revposts[$recs]['post_ts'];
            $firstrec = $revposts[0]['post_ts'];
            $c1 = date("ym", $firstrec);
            $c2 = date("ym", $lastrec);
            $startdate = date("m", $firstrec);
            $enddate = date("m", $lastrec);
            // . " " .date("Y", $lastrec);
            // create a while loop to get all the posts between start and end dates
            $postarr = array();
            // echo $c1, $c2;
            // echo $startdate, $enddate;
            foreach($revposts as $themonths) {
                $months[] = date("ym", $themonths['post_ts']);
                $posts = array();
                // $this->objDbBlog->getPostsMonthly(mktime(0, 0, 0, date("m",$themonths['post_ts']), 1, date("y", $themonths['post_ts'])) , $userid);
                $postarr[date("Ym", $themonths['post_ts']) ] = $posts;
            }
            return $postarr;
        } else {
            return NULL;
        }
    }
    /**
     * Method to produce the archived posts box
     *
     * @param  string $userid
     * @param  objetc $featurebox
     * @return string
     */
    public function archiveBox($userid, $featurebox = FALSE, $showOrHide = 'none')
    {
        // get the posts for each month
        $posts = $this->_archiveArr($userid);
        // print_r($posts);die();
        if (!empty($posts)) {
            $yearmonth = array_keys($posts);
            $arks = NULL;
            foreach($yearmonth as $months) {
                $month = str_split($months, 4);
                $thedate = mktime(0, 0, 0, intval($month[1]) , 1, intval($month[0]));
                $arks[] = array(
                    'formatted' => date("F", $thedate) . " " . date("Y", $thedate) ,
                    'raw' => $month[1],
                    'rfc' => $thedate
                );
            }
            $thismonth = mktime(0, 0, 0, date("m", time()) , 1, date("y", time()));
            if ($featurebox == FALSE) {
                return $thismonth;
            } else {
                $objFeatureBox = $this->getObject('featurebox', 'navigation');
                $lnks = NULL;
                foreach($arks as $ark) {
                    $lnk = new href($this->uri(array(
                        'module' => 'blog',
                        'action' => 'showarchives',
                        'month' => $ark['raw'],
                        'year' => $ark['rfc'],
                        'userid' => $userid
                    )) , $ark['formatted']);
                    $lnks.= $lnk->show() . "<br />";
                }
                // $str = "<a href=\"javascript:;\" onclick=\"Effect.toggle('archivemenu','slide', adjustLayout());\">[...]</a>";
                // $str .='<div id="archivemenu"  style="width:170px;overflow: hidden;display:'.$showOrHide.';"> ';
                // $str .= $lnks;
                // $str .= '</div>';
                $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_archives", "blog") , $lnks, 'arkbox', 'none');
                return $ret;
            }
        } else {
            return NULL;
        }
    }
    /**
     * Method to edit and manage posts
     *
     * @param  integer $userid
     * @return string
     */
    public function managePosts($userid, $month = NULL, $year = NULL)
    {
        // create a table with the months posts, plus a dropdown of all months to edit
        // put the edit icon at the end of each row, with text linked to the postEditor() method
        // create an array with keys: cat, excerpt, title, content, catid for edit
        // start the edit table
        $editform = new form('postedit', $this->uri(array(
            'action' => 'postedit'
        )));
        // $edfieldset = $this->newObject('fieldset', 'htmlelements');
        // $edfieldset->setLegend($this->objLanguage->languageText('mod_blog_posthead', 'blog'));
        $edtable = $this->newObject('htmltable', 'htmlelements');
        $edtable->cellpadding = 5;
        // grab the posts for this month
        // $posts = $this->objDbBlog->getPostsMonthly(mktime(0,0,0,date("m", time()), 1, date("y", time())), $userid);
        // change this to get from the form input rather
        if ($month == NULL && $year == NULL) {
            if ($this->objUser->inAdminGroup($userid)) {
                $posts = $this->objDbBlog->getAbsAllPostsWithSiteBlogs($userid);
            }
            $posts = $this->objDbBlog->getAbsAllPosts($userid);
        }
        $count = count($posts);
        // print_r($posts);
        // add in a table header...
        $edtable->startHeaderRow();
        $edtable->addHeaderCell($this->objLanguage->languageText("mod_blog_posttitle", "blog"));
        $edtable->addHeaderCell($this->objLanguage->languageText("mod_blog_postdate", "blog"));
        $edtable->addHeaderCell($this->objLanguage->languageText("mod_blog_poststatus", "blog"));
        $edtable->addHeaderCell($this->objLanguage->languageText("mod_blog_postcat", "blog"));
        $edtable->addHeaderCell($this->objLanguage->languageText("mod_blog_editdelete", "blog"));
        $edtable->endHeaderRow();
        foreach($posts as $post) {
            (($count%2) == 0) ? $oddOrEven = 'even' : $oddOrEven = 'odd';
            $edtable->row_attributes = " onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='" . $oddOrEven . "'; \"";
            $edtable->startRow();
            $edtable->addCell($post['post_title']);
            $edtable->addCell(date('r', $post['post_ts']));
            // do some voodoo on the post status, so that it looks better
            switch ($post['post_status']) {
                case '0':
                    $post['post_status'] = $this->objLanguage->languageText("mod_blog_published", "blog");
                    break;

                case '1':
                    $post['post_status'] = $this->objLanguage->languageText("mod_blog_draft", "blog");
                    break;

                case '2':
                    $post['post_status'] = $this->objLanguage->languageText("mod_blog_hidden", "blog");
                    break;
            }
            $edtable->addCell($post['post_status']);
            // category voodoo
            if ($post['post_category'] == '0') {
                $post['post_category'] = $this->objLanguage->languageText("mod_blog_word_default", "blog");
            } else {
                $mapcats = $this->objDbBlog->mapKid2Parent($post['post_category']);
                if (isset($mapcats[0])) {
                    $post['post_category'] = $mapcats[0]['cat_name'];
                }
            }
            $edtable->addCell($post['post_category']);
            // do the edit and delete icon
            $this->objIcon = &$this->getObject('geticon', 'htmlelements');
            $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                'action' => 'postedit',
                'id' => $post['id'],
                'module' => 'blog'
            )));
            $delIcon = $this->objIcon->getDeleteIconWithConfirm($post['id'], array(
                'module' => 'blog',
                'action' => 'deletepost',
                'id' => $post['id']
            ) , 'blog');
            // do the checkboxen for the multi delete.
            $this->loadClass('checkbox', 'htmlelements');
            $cbox = new checkbox('arrayList[]');
            $cbox->cssId = 'checkbox_' . $post['id'];
            $cbox->setValue($post['id']);
            $edtable->addCell($edIcon . $delIcon . $cbox->show());
            $edtable->endRow();
        }
        // submit button for multidelete
        $this->objdelButton = &new button('deleteposts');
        $this->objdelButton->setValue($this->objLanguage->languageText('mod_blog_word_deleteselected', 'blog'));
        $this->objdelButton->setIconClass("delete");
        $this->objdelButton->setToSubmit();
        $editform->addToForm($edtable->show());
        $editform->addToForm($this->objdelButton->show());
        $editform = $editform->show();


        return $editform;
    }
    /**
     * Method to add a quick post as a blocklet
     *
     * @param  integer $userid
     * @param  bool    $featurebox
     * @return mixed
     */
    public function quickPost($userid, $featurebox = FALSE)
    {
        // form for the quick poster blocklet
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $qpform = new form('qpadd', $this->uri(array(
            'action' => 'postadd',
            'mode' => 'quickadd'
        )));
        $qpform->addRule('postcontent', $this->objLanguage->languageText("mod_blog_phrase_pcontreq", "blog") , 'required');
        $qptitletxt = $this->objLanguage->languageText("mod_blog_posttitle", "blog") . "<br />";
        $qptitle = new textinput('posttitle');
        // post content textarea
        $qpcontenttxt = $this->objLanguage->languageText("mod_blog_pcontent", "blog") . "<br />";
        $qpcontent = new textarea('quickpost_postcontent');
        // $qpcontent->setName('postcontent');
        // $qpcontent->setBasicToolBar();
        // dropdown of cats
        $qpcattxt = $this->objLanguage->languageText("mod_blog_postcat", "blog") . "<br />";
        $qpDrop = new dropdown('cat');
        $qpDrop->addOption(0, $this->objLanguage->languageText("mod_blog_defcat", "blog"));
        // loop through the existing cats and make sure not to add a child to the dd
        $pcats = $this->objDbBlog->getAllCats($userid);
        foreach($pcats as $adds) {
            $qpDrop->addOption($adds['id'], $adds['cat_name']);
        }
        // set up the form elements so they fit nicely in a box
        $qptitle->size = 15;
        $qpcontent->cols = 15;
        $qpcontent->rows = 5;
        $qpform->addToForm($qptitletxt . $qptitle->show());
        $qpform->addToForm("<br />");
        $qpform->addToForm($qpcontenttxt . $qpcontent->show());
        $qpform->addToForm("<br />");
        $qpform->addToForm($qpcattxt . $qpDrop->show());
        $this->objqpCButton = &new button('blogit');
        $this->objqpCButton->setIconClass("save");
        $this->objqpCButton->setValue($this->objLanguage->languageText('mod_blog_word_blogit', 'blog'));
        $this->objqpCButton->setToSubmit();
        $qpform->addToForm($this->objqpCButton->show());
        $qpform = $qpform->show();
        if ($featurebox == FALSE) {
            return $qpform;
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_qpdetails", "blog") , $this->objLanguage->languageText("mod_blog_quickaddpost", "blog") . "<br />" . $qpform);
            return $ret;
        }
    }
    /**
     * Method to display the last ten posts as a block
     *
     * @author Megan Watson
     * @access public
     * @param  integer $num        The number of posts to display. Default = 10
     * @param  bool    $featurebox Return the posts as a string or formatted in a featurebox. Default = false, return as a string
     * @return string  html
     */
    public function showLastTenPosts($num = 10, $featurebox = FALSE)
    {
        $objUser = $this->getObject('user', 'security');
        $this->loadClass('link', 'htmlelements');
        $data = $this->objDbBlog->getLastPosts($num);
        $str = '';
        // Display the posts
        if (!empty($data)) {
            foreach($data as $item) {
                $linkuri = $this->uri(array(
                    'action' => 'viewsingle',
                    'postid' => $item['id'],
                    'userid' => $item['userid']
                ));
                $link = new href($linkuri, stripslashes($item['post_title']));
                $str.= '<p>';
                $str.= '<b>' . $link->show() . '</b><br />';
                if ($this->showfullname == 'FALSE') {
                    $nameshow = $this->objUser->userName($item['userid']);
                } else {
                    $nameshow = $this->objUser->fullname($item['userid']);
                }
                $str.= '<font class="minute">' . $nameshow . '</font>';
                // $str .= '<br />'.$item['post_excerpt'];
                // TODO: put in a hr class (CSS) that takes up very little space
                $str.= '</p>';
            }
        }
        // Display either as a string for the block or in a featurebox
        if ($featurebox == FALSE) {
            return $str;
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_block_latestblogs", "blog") , $str);
            return $ret;
        }
    }

    public function showLastTenPostsStripped($num = 10, $featurebox = FALSE)
    {
        $objUser = $this->getObject('user', 'security');
        $this->loadClass('link', 'htmlelements');
        $data = $this->objDbBlog->getLastPosts($num);
        $str = '';
        // Display the posts
        if (!empty($data)) {
            foreach($data as $item) {
                $linkuri = $this->uri(array(
                    'action' => 'viewsingle',
                    'postid' => $item['id'],
                    'userid' => $item['userid']
                ));
                //$link = new href($linkuri, stripslashes($item['post_title']));

                //$str.= $link->show();
                if ($this->showfullname == 'FALSE') {
                    $nameshow = $this->objUser->userName($item['userid']);
                } else {
                    $nameshow = $this->objUser->fullname($item['userid']);
                }
                $str.= $nameshow." ".$item['post_title']." ".$linkuri."\r\n";
            }
        }
        // Display either as a string for the block or in a featurebox
        if ($featurebox == FALSE) {
            return $str;
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_block_latestblogs", "blog") , $str);
            return $ret;
        }
    }
    /**
     * Date manipulation method for getting posts by month/date
     *
     * @param  mixed selected date $sel_date
     * @return array
     */
    public function retDates($sel_date = NULL)
    {
        if ($sel_date == NULL) {
            $sel_date = mktime(0, 0, 0, date("m", time()) , 1, date("y", time()));
        }
        $t = getdate($sel_date);
        $start_date = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon'], 1, $t['year']);
        $start_date-= 86400*date('w', $start_date);
        $prev_year = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon'], $t['mday'], $t['year']-1);
        $prev_month = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon']-1, $t['mday'], $t['year']);
        $next_year = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon'], $t['mday'], $t['year']+1);
        $next_month = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon']+1, $t['mday'], $t['year']);
        return array(
            'mbegin' => $sel_date,
            'prevyear' => $prev_year,
            'prevmonth' => $prev_month,
            'nextyear' => $next_year,
            'nextmonth' => $next_month
        );
    }
    /**
     * Method to build a tag cloud from blog entry tags
     *
     * @param  string $userid
     * @return array
     */
    public function blogTagCloud($userid, $showOrHide = 'none')
    {
        $this->objTC = $this->getObject('tagcloud', 'utilities');
        // get all the tags
        $tagarr = $this->objDbBlog->getTagsByUser($userid);
        if (empty($tagarr)) {
            return NULL;
        }
        foreach($tagarr as $uni) {
            $t[] = $uni['meta_value'];
        }
        $utags = array_unique($t);
        foreach($utags as $tag) {
            // create the url
            $url = $this->uri(array(
                'action' => 'viewblogbytag',
                'tag' => $tag,
                'userid' => $userid
            ));
            // get the count of the tag (weight)
            $weight = $this->objDbBlog->getTagWeight($tag, $userid);
            $weight = $weight*1000;
            $tag4cloud = array(
                'name' => $tag,
                'url' => $url,
                'weight' => $weight,
                'time' => time()
            );
            $ret[] = $tag4cloud;
        }
        $icon = $this->getObject('geticon', 'htmlelements');
        $icon->setIcon('up');
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        return $objFeatureBox->show($this->objLanguage->languagetext("mod_blog_tagcloud", "blog") , $this->objTC->buildCloud($ret) , 'tagcloud', 'none');
    }
    /**
     * Function addCommentForm
     *
     */
    public function addCommentForm($postid, $userid, $captcha = FALSE, $comment = NULL, $useremail = NULL)
    {
        $this->objComApi = $this->getObject('commentapi', 'blogcomments');
        return $this->objComApi->commentAddForm($postid, 'blog', 'tbl_blog_posts', $userid, TRUE, TRUE, FALSE, $captcha, $comment, $useremail);
    }
    /**
     *
     */
    public function setComments($post, $icon = TRUE)
    {
        // COMMENTS
        if ($icon == TRUE) {
            $objLink = new link($this->uri(array(
                'action' => 'viewsingle',
                'postid' => $post['id'],
                'userid' => $post['userid']
            ) , 'blog'));
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
            )) , $this->objLanguage->languageText("mod_blog_comments", "blog") , NULL);
            return $objLink->show();
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid Parameter description (if any) ...
     * @return void
     * @access public
     */
    public function pingGoogle($userid)
    {
        $objBk = $this->getObject('background', 'utilities');
        $status = $objBk->isUserConn();
        $callback = $objBk->keepAlive();
        //$this->objProxy = $this->getObject('proxy', 'utilities');
        // set up for Google Blog API
        $changesURL = $this->uri(array(
            'module' => 'blog',
            'action' => 'feed',
            'userid' => $userid
        ));
        $name = $this->objUser->fullname($userid) . " Chisimba blog";
        $blogURL = $this->uri(array(
            'module' => 'blog',
            'action' => 'randblog',
            'userid' => $userid
        ));
        // OK lets put it together...
        $gurl = "http://blogsearch.google.com/ping";
        // do the http request
        // echo $gurl;
        $gurl = str_replace('%26amp%3B', "&", $gurl);
        $gurl = str_replace('&amp;', "&", $gurl);
        $gurl = $gurl . "?name=" . urlencode($name) . "&url=" . urlencode($blogURL) . "&changesUrl=" . urlencode($changesURL);
        //log_debug($gurl);
        // get the proxy info if set
        /*$proxyArr = $this->objProxy->getProxy(NULL);
        // print_r($proxyArr); die();
        if (!empty($proxyArr)) {
            $parr = array(
                'proxy_host' => $proxyArr['proxyserver'],
                'proxy_port' => $proxyArr['proxyport'],
                'proxy_user' => $proxyArr['proxyusername'],
                'proxy_pass' => $proxyArr['proxypassword']
            );
        }
        // echo $gurl; die();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gurl);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($proxyArr)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxyserver'] . ":" . $proxyArr['proxyport']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxyusername'] . ":" . $proxyArr['proxypassword']);
        }
        $code = curl_exec($ch);
        curl_close($ch);
        */
        $objCurl = $this->getObject('curl', 'utilities');
        $code = $objCurl->exec($gurl);
        switch ($code) {
            case "Thanks for the ping.":
                log_debug("Google blogs API Success! Google said: " . $code);
                break;

            default:
                log_debug("Google blogs API Failure! Google said: " . $code);
                break;
        }
    }

    public function showGeoTagMap($userid)
    {
    	$this->objConfig = $this->getObject('altconfig', 'config');
    	$tfile = $this->objConfig->getcontentBasePath() . 'users/' . $userid . '/geotags.txt';
    	$jstfile = $this->objConfig->getSiteRoot().'usrfiles/users/' . $userid . '/geotags.txt';
    	$geoposts = $this->objDbBlog->getGeoPosts($userid);
    	$data = "point\ttitle\tdescription\n";
    	foreach($geoposts as $posts)
    	{
    		$lat = $posts['geolat'];
    		$lon = $posts['geolon'];
    		$title = '<a href="'.$this->uri(array('action' => 'viewsingle', 'postid' => $posts['id'], 'userid' => $userid), 'blog').'">'.$posts['post_title'].'</a>';
    		$desc = $posts['post_excerpt'];
    		if($desc = '' || empty($desc))
    		{
    			$this->objUser = $this->getObject('user', 'security');
    			$desc = $this->objLanguage->languageText("mod_blog_viewfullprofile", "blog")." ".$this->objUser->userName($userid);
    		}
    		$data .= "$lat,$lon\t$title\t$desc\n";
    	}
    	file_put_contents($tfile, $data);

    	//ok now parse the text file and display the map.
    	$css = "<style type=\"text/css\">
        #map {
            width: 80%;
            height: 350px;
            border: 1px solid black;
        }
    </style>";

        	$olsrc = $this->getJavascriptFile('lib/OpenLayers.js','georss');
        	$js = "<script type=\"text/javascript\">
        	var map, layer;

        function init(){

            map = new OpenLayers.Map('map', { controls: [] });
            layer = new OpenLayers.Layer.WMS( \"Public WMS\",
                \"http://labs.metacarta.com/wms/vmap0\", {layers: 'basic'} );

            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl( new OpenLayers.Control.MouseDefaults() );
            map.addControl( new OpenLayers.Control.LayerSwitcher() );
            map.addControl( new OpenLayers.Control.PanZoomBar() );

            map.addLayer(layer);
            map.setCenter(new OpenLayers.LonLat(0, 0), 0);

            var newl = new OpenLayers.Layer.Text( \"text\", { location:\"".$jstfile."\"} );
            map.addLayer(newl);



            var size = new OpenLayers.Size(10,17);
            var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
            var icon = new OpenLayers.Icon('http://boston.openguides.org/markers/AQUA.png',size,offset);

            marker = new OpenLayers.Marker(new OpenLayers.LonLat(90,10),icon.clone());
            marker.events.register('mousedown', marker, function(evt) { alert(this.icon.url); OpenLayers.Event.stop(evt); });


            map.zoomToMaxExtent();

        }
    </script>";
        	// add the lot to the headerparams...
        	$this->appendArrayVar('headerParams', $css.$google.$olsrc.$js);
        	$this->appendArrayVar('bodyOnLoad', "init();");
    }
}
?>