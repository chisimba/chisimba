<?php
/**
 * Class to handle blog elements.
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
 * @version    $Id: blogops_class_inc.php 24209 2012-05-02 09:51:59Z jsc $
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
 * Class to handle blog elements
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @category  Chisimba
 * @package   blog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: blogops_class_inc.php 24209 2012-05-02 09:51:59Z jsc $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class blogops extends object
{
    /**
     * Description for public
     *
     * @var    mixed
     * @access public
     */
    public $objConfig;
    /**
     * Description for public
     *
     * @var    boolean
     * @access public
     */
    public $mail2blog = TRUE;
    /**
     * Description for public
     *
     * @var    string
     * @access public
     */
    public $showfullname;
    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init()
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objDbBlog = $this->getObject('dbblog');
            $this->loadClass('href', 'htmlelements');
            $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->showfullname = $this->sysConfig->getValue('show_fullname', 'blog');
            $this->objUser = $this->getObject('user', 'security');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
    /**
     * Method to display the login box for prelogin blog operations
     *
     * @param  bool   $featurebox
     * @return string
     */
    public function loginBox($featurebox = FALSE)
    {
        $objBlocks = $this->getObject('blocks', 'blocks');
        if ($featurebox == FALSE) {
            return $objBlocks->showBlock('login', 'security') . "<br />" . $objBlocks->showBlock('register', 'security');
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            return $objFeatureBox->show($this->objLanguage->languageText("word_login", "system") , $objBlocks->showBlock('login', 'security', 'none')
              . "<br />" . $objBlocks->showBlock('register', 'security', 'none') );
        }
    }
    /**
     * Method to display a link to all the blogs on the system
     * Setting $featurebox = TRUE will display the link in a block style featurebox
     *
     * @param  bool   $featurebox
     * @return string
     */
    public function showBlogsLink($featurebox = FALSE)
    {
        // set up a link to the other users blogs...
        $oblogs = new href($this->uri(array(
            'action' => 'allblogs'
        )) , $this->objLanguage->languageText("mod_blog_viewallblogs", "blog") , NULL);
        $oblogsTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
          . $oblogs->show() . "<br /></span>";

        // Link for siteblogs Added by Irshaad Hoodain
        $ositeblogs = new href($this->uri(array(
            'action' => 'siteblog'
        )) , $this->objLanguage->languageText("mod_blog_viewsiteblogs", "blog") , NULL);
        $ositeblogsTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
          . $ositeblogs->show() . "<br /></span>";

        $defmodLink = new href($this->uri(array() , '_default'),
           $this->objLanguage->languageText("mod_blog_returntosite", "blog"),
           NULL);
        $defmodLinkTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
          . $defmodLink->show()  . "<br /></span>";

        if ($featurebox == FALSE) {
            $ret = $oblogsTxt . $defmodLinkTxt;
        } else {
            $boxContent = $oblogsTxt;
            $boxContent.= $defmodLinkTxt;
            //
            // database abstraction object
            $this->objDbBlog = $this->getObject('dbblog');
            //@@@@@@@ BUG NEEDS FIXING
            $postresults = $this->objDbBlog->getAllPosts($userid = 1, null);
            if (!$postresults == null) {
                $boxContent.= $ositeblogsTxt;
            }
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_otherblogs", "blog") , $boxContent);
        }
        return $ret;
    }
    /**
     * Method to display the link to blog admin on post login
     *
     * @param  bool   $featurebox
     * @return string
     */
    public function showAdminSection($featurebox = FALSE, $blogadmin = FALSE, $showOrHide = 'none')
    {
        // admin section
        if ($featurebox == FALSE) {
            $ret = "<em>" . $this->objLanguage->languageText("mod_blog_admin", "blog") . "</em><br />";
        } else {
            $ret = NULL;
        }
        // blog admin page
        $admin = new href($this->uri(array(
            'action' => 'blogadmin'
        )) , $this->objLanguage->languageText("mod_blog_blogadmin", "blog"));
        // profile
        $profile = new href($this->uri(array(
            'action' => 'setprofile'
        )) , $this->objLanguage->languageText("mod_blog_setprofile", "blog"));
        // blog importer
        $import = new href($this->uri(array(
            'action' => 'importblog'
        )) , $this->objLanguage->languageText("mod_blog_blogimport", "blog"));
        // mail setup
        if ($this->mail2blog == FALSE) {
            $mailsetup = NULL;
        } else {
            $mailsetup = new href($this->uri(array(
                'action' => 'setupmail'
            )) , $this->objLanguage->languageText("mod_blog_setupmail", "blog"));
        }
        // write new post link
        $newpost = new href($this->uri(array(
            'action' => 'blogadmin',
            'mode' => 'writepost'
        )) , $this->objLanguage->languageText("mod_blog_writepost", "blog"));
        // edit existing posts
        $editpost = new href($this->uri(array(
            'action' => 'blogadmin',
            'mode' => 'editpost'
        )) , $this->objLanguage->languageText("mod_blog_word_editposts", "blog"));
        // moderate comments
        $moderatecomments = new href($this->uri(array('action' => 'unapproved'), 'blogcomments'),
            $this->objLanguage->languageText("mod_blog_moderate_comments", "blog"));
        // edit/create cats
        $editcats = new href($this->uri(array(
            'action' => 'blogadmin',
            'mode' => 'editcats'
        )) , $this->objLanguage->languageText("mod_blog_word_categories", "blog"));
        // add/delete RSS feeds
        $rssedits = new href($this->uri(array(
            'action' => 'rssedit'
        )) , $this->objLanguage->languageText("mod_blog_rssaddedit", "blog"));
        // add/delete RSS feeds
        $linksedits = new href($this->uri(array(
            'action' => 'linkeditor'
        )) , $this->objLanguage->languageText("mod_blog_linksaddedit", "blog"));
        // view all other blogs
        $addeditpages = new href($this->uri(array(
            'action' => 'setpage'
        )) , $this->objLanguage->languageText("mod_blog_blogpages", "blog"));
        // moderate comments
        $modcomms = new href($this->uri(array(
            'action' => 'moderate'
        ) , 'blogcomments') , $this->objLanguage->languageText("mod_blog_modcomms", "blog"));
        // view all other blogs
        $viewblogs = new href($this->uri(array(
            'action' => 'allblogs'
        )) , $this->objLanguage->languageText("mod_blog_viewallblogs", "blog"));
        // go back to your blog
        $viewmyblog = new href($this->uri(array(
            'action' => 'viewblog'
        )) , $this->objLanguage->languageText("mod_blog_viewmyblog", "blog"));
        // go to siteblog
        $siteblog = new href($this->uri(array(
            'action' => 'siteblog'
        )) , $this->objLanguage->languageText("mod_blog_viewmyblog", "blog"));
        if ($blogadmin == TRUE) {
            // build up a bunch of featureboxen and send em out
            // this will only happen with the front page (blogadmin template)
            $this->objUser = $this->getObject('user', 'security');
            if ($this->objUser->inAdminGroup($this->objUser->userId())) {
                $linksarr = array(
                    $admin,
                    $profile,
                    $import,
                    $mailsetup,
                    $newpost,
                    $editpost,
                    $moderatecomments,
                    $editcats,
                    $rssedits,
                    $linksedits,
                    $viewblogs,
                    $viewmyblog
                );
            } else {
                $linksarr = array(
                    $admin,
                    $profile,
                    $import,
                    $newpost,
                    $editpost,
                    $moderatecomments,
                    $editcats,
                    $rssedits,
                    $linksedits,
                    $viewblogs,
                    $viewmyblog
                );
            }
            foreach($linksarr as $links) {
                $objFeatureBox = $this->newObject('featurebox', 'navigation');
                // Make it stylable #BLOGSTYLE.
                $linkTxt = "<span class=\"featureboxlink\">"
                  . $links->show() . "</span>";
                $ret.= $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_admin", "blog") , $linkTxt);
            }
            return $ret;
        } else {
            // build the links
            $this->objUser = $this->getObject('user', 'security');
            if ($this->objUser->inAdminGroup($this->objUser->userId())) {
                if ($this->mail2blog == FALSE) {
                    // Make it stylable #BLOGSTYLE.
                    $topper = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
                      . $newpost->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
                      . $editpost->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
                      . $moderatecomments->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
                      . $viewmyblog->show() . "</span>";
                    $ret.= "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
                      . $admin->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
                      . $profile->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
                      . $import->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
                      . $editcats->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $rssedits->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $linksedits->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $addeditpages->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $modcomms->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $viewblogs->show() . "</span>";
                } else {
                    // Make it stylable #BLOGSTYLE.
                    $topper = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $newpost->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $editpost->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $moderatecomments->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $viewmyblog->show() . "</span>";
                    $ret.= "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $admin->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $profile->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $import->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $mailsetup->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $editcats->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $rssedits->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $linksedits->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $addeditpages->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $modcomms->show() . "<br /></span>"
                      . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $viewblogs->show() . "</span>";
                }
            } else {
                // Make it stylable #BLOGSTYLE.
                $topper =  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$newpost->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$editpost->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$moderatecomments->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$viewmyblog->show() . "</span></span>";
                $ret.=  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$admin->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$profile->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$import->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$editcats->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$rssedits->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$linksedits->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$addeditpages->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$modcomms->show() . "<br /></span>"
                  .  "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" .$viewblogs->show() . "</span>";
            }
        }
        if ($featurebox == FALSE) {
            return $ret;
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $objIcon = &$this->getObject('geticon', 'htmlelements');
            $objIcon->setIcon('toggle');
            $str = "<a href=\"javascript:;\" onclick=\"Effect.toggle('adminmenu','slide');\">" . $this->objLanguage->languageText("mod_blog_moreoptions", "blog") . "</a>";
            // $objIcon->show() . "</a>";
            $str.= '<div id="adminmenu"  style="width:170px;overflow: hidden;display:' . $showOrHide . ';"> ';
            $str.= $ret;
            $str.= '</div>';
            $box = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_admin", "blog") , $topper . "<br />" . $str);
            return $box;
        }
    }

    /**
     * Method to scrub grubby html
     *
     * @param  string $document
     * @return string
     */
    function html2txt($document, $scrub = TRUE)
    {
        if ($scrub == TRUE) {
            $search = array(
                '@<script[^>]*?>.*?</script>@si', // Strip out javascript
                /*'@<[\/\!]*?[^<>]*?>@si',*/
                // Strip out HTML tags
                /*'@<style[^>]*?>.*?</style>@siU',*/
                // Strip style tags properly
                '@<![\s\S]*?--[ \t\n\r]*>@'
                // Strip multi-line comments including CDATA

            );
        } else {
            $search = array(
                '@<script[^>]*?>.*?</script>@si', // Strip out javascript
                '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
                /*'@<style[^>]*?>.*?</style>@siU',*/
                // Strip style tags properly
                '@<![\s\S]*?--[ \t\n\r]*>@', // Strip multi-line comments including CDATA
                '!(\n*(.+)\n*!x', // Strip out newlines

            );
        }
        $text = preg_replace($search, '', $document);
        $text = str_replace("<br /><br />", "", $text);
        $text = str_replace("<br />  <br />", "<br />", $text);
        $text = str_replace("<br />", "\n", $text);
        $text = str_replace("<br\">", "\n", $text);
        $text = str_replace("<", " <", $text);
        $text = str_replace(">", "> ", $text);
        $text = rtrim($text, "\n");
        return $text;
    }
    /**
     * Methid to build a table of all available bloggers on the system
     *
     * @param  array  $rec
     * @return string
     */
    public function buildBloggertable($rec)
    {
        $lastentry = $this->objDbBlog->getLatestPost($rec['id']);
        $imglink = new href($this->uri(array('action' => 'randblog', 'userid' => $rec['id'])) , $rec['img']);
        if(!empty($lastentry))
        {
        	$link = new href($this->uri(array(
            	'action' => 'randblog',
            	'userid' => $rec['id']
        	)) , stripslashes($lastentry['post_title']));
        	//$imglink = new href($this->uri(array( 'action' => 'randblog', 'userid' => $rec['id'])) , $rec['img']);
        }
        else {
        	$link = $this->objLanguage->languageText("mod_blog_onlydrafts", "blog");
        }
        $this->cleaner = $this->newObject('htmlcleaner', 'utilities');
        $txt = stripslashes($lastentry['post_excerpt']);
        $txt = stripslashes($txt);
        $txtlen = 100;
        $str_to_count = $txt;
        if (strlen($str_to_count) <= $txtlen) {
            $txt = $this->cleaner->cleanHtml($txt);
        } else {
            $txt = substr($str_to_count, 0, $txtlen-3);
            $txt.= $txt . "...";
            $txt = $this->cleaner->cleanHtml($txt);
        }

        if(!empty($lastentry))
        {
        	$lastpost = $link->show() . "<br />" . $txt;
        }
        else {
        	$lastpost = $link;
        }
        $stable = $this->newObject('htmltable', 'htmlelements');
        $stable->cellpadding = 2;
        // set up the header row
        $stable->startHeaderRow();
        $stable->addHeaderCell('');
        $stable->addHeaderCell('');
        $stable->endHeaderRow();
        $stable->startRow();
        $stable->addCell($imglink->show()); // $rec['img']);
        $stable->addCell($this->objLanguage->languageText("mod_blog_lastseen", "blog") . " : " . $rec['laston'] . "<br />" . $lastpost);
        $stable->endRow();
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_blogger", "blog") . " : " . "<em>" . $rec['name'] . "</em>", $stable->show());
        return $ret;
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
}
?>
