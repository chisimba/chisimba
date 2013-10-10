<?php
/**
 *
 * Viewer class for rendering an array of messages to the browser
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
 * @category  Chisimba
 * @package   jabberblog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * Viewer class for rendering an array of messages to the browser
 *
 * @author Paul Scott
 * @package IM
 *
 */
class jbviewer extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    public $uImage;
    public $objWashout;
    public $teeny;
    public $objTwtOps;

    /**
     *
     * Constructor

     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        $this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
        $this->objIcon = $this->getObject ( 'geticon', 'htmlelements' );
        $this->objLink = $this->getObject ( 'link', 'htmlelements' );
        $this->objDBIM = $this->getObject ( 'dbjbim' );
        $this->objComment = $this->getObject ( 'commentapi', 'blogcomments' );
        $this->objUser = $this->getObject ( 'user', 'security' );
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->jposteruid = $this->objSysConfig->getValue ( 'jposteruid', 'jabberblog' );
        $this->uImage = $this->objUser->getSmallUserImage ( $this->jposteruid );
        $this->objWashout = $this->getObject ( 'washout', 'utilities' );
        $this->teeny = $this->getObject ( 'tiny', 'tinyurl');
        $this->objTwtOps = $this->getObject ( 'twitoasterops', 'twitoaster');
        $this->objUserPic = $this->getObject('imageupload', 'useradmin');
        $twttr = '<script src="http://platform.twitter.com/anywhere.js?id=Ga1VyPD18avD3OWSu2qYA&v=1"></script>';
        $this->appendArrayVar('headerParams', $twttr);
    }

    public function renderSingle($msg) {
        $hc = '<script type="text/javascript">
                   twttr.anywhere(function(twitter) {
	               twitter.hovercards();
                   });
               </script>';

        $this->loadClass ( 'htmlheading', 'htmlelements' );
        // Add in a comment heading
        $header = new htmlHeading ( );
        $header->str = $this->objLanguage->languageText ( 'mod_jabberblog_comments', 'jabberblog' );
        $header->type = 3;

        $msg = $msg [0];
        $msgbody = $this->objWashout->parseText ( $msg ['msgbody'] );
        // run the parsers on the body
        $msgbody = $this->renderHashTags( $msgbody );
        $msgbody = $this->renderAtTags( $msgbody );

        $msgid = $msg ['id'];
        $commenttxt = NULL;
        if(isset($msg['twitthreadid']) && $msg['twitthreadid'] != '') {
            // show twitter comments
            $data = json_decode($this->objTwtOps->showConvo($msg['twitthreadid']));
            $stats = $data->thread->stats;
            $totalreplies = $stats->total_replies;
            $replydata = $data->replies;
            if(!is_array($replydata)) {
               $replydata = array();
            }
            // Add in a heading
            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $this->loadClass ( 'htmlheading', 'htmlelements' );
            $header = new htmlHeading ( );
            $header->str = $this->objLanguage->languageText("mod_blogcomments_twittercomments4post", "jabberblog");
            $header->type = 3;
            $commenttxt .= $header->show();

            foreach($replydata as $replies) {
                $content = $replies->content;
                $dt = $replies->created_at->datetime_gmt;
                $name = "@".$replies->user->screen_name;
                $tlink = $this->newObject ( 'link', 'htmlelements' );
                $tlink->href = "http://twitter.com/".$replies->user->screen_name;
                $tlink->link = $name;
                $image = $replies->user->profile_image_url;
                $header = $tlink->show()." at ".$dt;

                $ctable = $this->newObject('htmltable', 'htmlelements');
                $ctable->cellpadding = 2;
                $ctable->startRow();
                $ctable->addCell("<img src='".$image."' /> ".$content);
                //$ctable->addCell($content);
                $ctable->endRow();
                $fbody = $ctable->show();
            
                $commenttxt .= $objFeatureBox->showComment("<span class='blog-head-date'>".$header."</span>", "<div class='blog-item-base'>".$this->objWashout->parseText($fbody)."<br /></div>");
            }
        }
        // Show regular website comments
        $commenttxt .= $this->objComment->showJblogComments ( $msgid );
        $comment = $this->objComment->commentAddForm ( $msgid, 'jabberblog', 'tbl_jabberblog', $postuserid = NULL, $editor = TRUE, $featurebox = FALSE, $showtypes = FALSE, $captcha = FALSE, $comment = NULL, $useremail = NULL );
        $objFeaturebox = $this->getObject ( 'featurebox', 'navigation' );
        $ret = $objFeaturebox->showContent ( "<span class='blog-head-date'>".'<strong>' . $this->objUser->fullName ( $this->jposteruid ) . '</strong> on ' . $msg ['datesent']."</span>", nl2br ( $msgbody ) . "<br />".$commenttxt . "<br />" . $comment."<br />" );
        $ret .= "<hr />".$hc;

        return $ret;
    }

    public function renderOutputForBrowser($msgs) {
        $ret = NULL;
        $hc = '<script type="text/javascript">
                   twttr.anywhere(function(twitter) {
	               twitter.hovercards();
                   });
               </script>';
        foreach ( $msgs as $msg ) {
            $msgbody = $this->objWashout->parseText ( $msg ['msgbody'] );
            // run the parsers on the body
            $msgbody = $this->renderHashTags( $msgbody );
            $msgbody = $this->renderAtTags( $msgbody );

            $fuser = $msg ['msgfrom'];
            $msgid = $msg ['id'];
            $sentat = $this->objLanguage->languageText ( 'mod_im_sentat', 'jabberblog' );
            $fromuser = $this->objLanguage->languageText ( 'mod_im_sentfrom', 'jabberblog' );
            // set up a link for comments
            $clink = $this->getObject ( 'link', 'htmlelements' );
            $clink->href = $this->uri ( array ('postid' => $msgid, 'action' => 'viewsingle' ) );
            $clink->link = $this->objLanguage->languageText ( "mod_jabberblog_leavecomment", "jabberblog" );
            // set up a link for Tweet this
            $tlink = $this->newObject ( 'link', 'htmlelements' );
            $tlink->href = "http://twitter.com/home/?status=".$this->objLanguage->languageText ( "mod_jabberblog_interestingpost", "jabberblog" ).": ".$this->teeny->create(urlencode($this->uri ( array ('postid' => $msgid, 'action' => 'viewsingle' ) )));
            $tlink->link = $this->objLanguage->languageText ( "mod_jabberblog_tweetthis", "jabberblog" );
            // get the comment count
            if(isset($msg['twitthreadid']) && $msg['twitthreadid'] != '') {
                // show twitter comments
                $data = json_decode($this->objTwtOps->showConvo($msg['twitthreadid']));
                $stats = $data->thread->stats;
                $totalreplies = $stats->total_replies;
                $data = NULL;
                $stats = NULL;
            }
            $comments = $this->objComment->getCount ( $msgid );
            $comments = $comments + $totalreplies;
            $totalreplies = 0;
            // alt featurebox
            $objFeaturebox = $this->getObject ( 'featurebox', 'navigation' );
            $ret .= $objFeaturebox->showContent ( "<span class='blog-head-date'>".'<strong>' . $this->objUser->fullName ( $this->jposteruid ) . '</strong> on ' . $msg ['datesent'] . " " . $clink->show () . "  (" . $comments . ")"." ".$tlink->show()."</span>", nl2br ( $msgbody ) . "<br /><br />" );
            $ret .= "<hr />".$hc;
        }
        header ( "Content-Type: text/html;charset=utf-8" );
        return $ret;
    }

    /**
     * Method to render stats
     */
    public function getStatsBox() {
        $this->objDbIm = $this->getObject ( 'dbjbim' );
        $this->objDbSubs = $this->getObject('dbsubs');
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->juser = $this->objSysConfig->getValue ( 'jabberuser', 'jabberblog' );
        $this->jdomain = $this->objSysConfig->getValue ( 'jabberdomain', 'jabberblog' );
        $str = $this->objLanguage->languageText ( "mod_jabberblog_subinfo", "jabberblog" ).": ".$this->juser."@".$this->jdomain;
        $str .= "<br />";
        $str .= "<br />";
        $str .= $this->objLanguage->languageText ( "mod_jabberblog_nomsgs", "jabberblog" );
        $str .= " " . $this->objDbIm->getNoMsgs ();
        $str .= "<br />";
        $str .= $this->objLanguage->languageText ( "mod_jabberblog_numsubs", "jabberblog" );
        $str .= " " . $this->objDbSubs->getNoSubs ();

        return $this->objFeatureBox->show ( $this->objLanguage->languageText ( "mod_jabberblog_stats", "jabberblog" ), $str );
    }

    public function showUserMenu() {
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $objWashout = $this->getObject ( 'washout', 'utilities' );
        $this->profiletext = $this->objSysConfig->getValue ( 'jposterprofile', 'jabberblog' );
        
        $head = NULL;
        $head .= '<div class="vcard">'."\n";
        $head .= '<span class="fn">'.$this->objUser->fullName ( $this->jposteruid ).'</span>'."\n";
		$body  = '<p align="center"><img class="photo" src="'.$this->objUserPic->userpicture($this->jposteruid).'" alt="'.$this->objUser->fullName($this->jposteruid).'" /></p>'."\n";
		
        $blurb = $objWashout->parseText ( $this->profiletext );
        $objFeature = $this->newObject ( 'featurebox', 'navigation' );

        return $objFeature->show ( $head, $body . "<br />" . $blurb );
    }

    public function searchBox() {
        $this->loadClass('textinput', 'htmlelements');
        $qseekform = new form('qseek', $this->uri(array(
            'action' => 'jbsearch',
        )));
        $qseekform->addRule('searchterm', $this->objLanguage->languageText("mod_jabberblog_phrase_searchtermreq", "jabberblog") , 'required');
        $qseekterm = new textinput('searchterm');
        $qseekterm->size = 15;
        $qseekform->addToForm($qseekterm->show());
        $this->objsTButton = &new button($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setValue($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setToSubmit();
        $qseekform->addToForm($this->objsTButton->show());
        $qseekform = $qseekform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_jabberblog_qseek", "jabberblog") , $this->objLanguage->languageText("mod_jabberblog_qseekinstructions", "jabberblog") . "<br />" . $qseekform);

        return $ret;
    }

    public function rssBox() {
        $this->objFeed = $this->getObject ( 'feeder', 'feed' );
        //$this->objFeed->setrssImage($iTitle, $iURL, $iLink, $iDescription, $iTruncSize = 500, $desHTMLSyn = true);
        $this->objFeed->setupFeed ( $stylesheet = false, 'Chisimba JabberBlog', 'Jabber based microblogging', $this->uri ( '' ), $this->uri ( '' ) );
        // get the latest say, 50 posts, and make a feed from em
        $this->objDbIm = $this->getObject ( 'dbjbim' );
        $count = $this->objDbIm->getNoMsgs ();
        $num = $count - 50;
        if ($num < 0) {
            $num = 0;
        }
        $items = $this->objDbIm->getRange ( $num, $count );
        array_reverse ( $items );
        // now we add the items to the feed
        foreach ( $items as $item ) {
            $this->objFeed->addItem ( $item ['msgfrom'], $this->uri ( array ('action' => 'viewsingle', 'postid' => $item ['id'] ), 'jabberblog' ), $item ['msgbody'], NULL, $this->objUser->userName ( $this->jposteruid ), $item ['datesent'] );
        }

        return $this->objFeed->output ( "RSS2.0" );
    }

    public function parseHashtags($str, $itemId)
    {
        $str = stripslashes($str);
        preg_match_all('/\#([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $memetag = array($item);
            // add the $item to tbl_tags as a jabberblog meme for later
            $objTags = $this->getObject('dbtags', 'tagging');
            $objTags->insertHashTags($memetag, $this->jposteruid, $itemId, 'jabberblog', NULL, NULL);
            $counter++;
        }

        return $str;
    }

    public function renderHashTags($str) {
        $str = stripslashes($str);
        preg_match_all('/\#([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item) {
            // set up a link to the URI to display all posts in the meme
            $hashlink = $this->getObject ( 'link', 'htmlelements' );
            $hashlink->href = $this->uri ( array ('meme' => $item, 'action' => 'viewmeme' ) );
            $hashlink->link = $item;
            //$str = str_replace($results[0][$counter], $hashlink->show()." ", $str);
            $counter++;
        }

        return $str;
    }

    public function parseAtTags($str, $itemId)
    {
        $str = stripslashes($str);
        preg_match_all('/\@([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $attag = array($item);
            // add the $item to tbl_tags as a jabberblog meme for later
            $objTags = $this->getObject('dbtags', 'tagging');
            $objTags->insertAtTags($attag, $this->jposteruid, $itemId, 'jabberblog', NULL, NULL);
            $counter++;
        }

        return $str;
    }

    public function renderAtTags($str) {
        $str = stripslashes($str);
        preg_match_all('/\@([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item) {
            // set up a link to the URI to display all posts in the meme
            $atlink = $this->getObject ( 'link', 'htmlelements' );
            $atlink->href = $this->uri ( array ('loc' => $item, 'action' => 'viewloc' ) );
            $atlink->link = $item;
            //$str = str_replace($results[0][$counter], $atlink->show()." ", $str);
            $counter++;
        }

        return $str;
    }

    public function renderBoxen() {
        $leftColumn = NULL;
        $objIcon = $this->newObject ( 'geticon', 'htmlelements' );
        $this->loadClass('href', 'htmlelements');
        $objIcon->alt = 'SIOC';
        $objIcon->setIcon('sioc', 'gif');
        $sioclink = new href($this->uri(array('action' => 'sioc', 'sioc_type' => 'site')), $objIcon->show());

        $rssLink = $this->newObject ( 'link', 'htmlelements' );
        $rssLink->href = $this->uri ( array ('action' => 'rss' ) );
        $rssLink->link = $this->objLanguage->languageText ( "mod_jabberblog_showrss", "jabberblog" );

        $cloudLink = $this->newObject ( 'link', 'htmlelements' );
        $cloudLink->href = $this->uri ( array ('action' => 'clouds' ) );
        $cloudLink->link = $this->objLanguage->languageText ( "mod_jabberblog_showtagclouds", "jabberblog" );

        $objLT = $this->getObject ( 'block_lasttweet', 'twitter' );

        $leftColumn .= $this->getStatsBox ();
        $leftColumn .= $this->objFeatureBox->show ( $this->objLanguage->languageText ( "mod_jabberblog_feed", "jabberblog" ), $rssLink->show ()."<br />".$sioclink->show()."<br />".$cloudLink->show() );
        $leftColumn .= $this->searchBox();
        // show the last tweet block from the 'ol twitter stream
        $leftColumn .= $this->objFeatureBox->show ( $this->objLanguage->languageText ( "mod_jabberblog_twitterfeed", "jabberblog" ), $objLT->show () );

        return $leftColumn;
    }

    public function doTags() {
        $this->objTC = $this->getObject('tagcloud', 'utilities');
        // get a list of the tags and their weights
        $this->objTags = $this->getObject('dbtags', 'tagging');
        $this->loadClass ( 'htmlheading', 'htmlelements' );
        // set up the hasthtags header
        $hheader = new htmlHeading ( );
        $hheader->str = $this->objLanguage->languageText ( 'mod_jabberblog_hashtags', 'jabberblog' );
        $hheader->type = 1;
        $cloud = $hheader->show();
        $cloud .= "<br />";

        // Get the hashtags
        $tags = $this->objTags->getHashTagsByModule('jabberblog');
        // ok now get the weights
        $tagarr = array();
        foreach ( $tags as $tag) {
            $weight = $this->objTags->getTagWeight($tag['meta_value'], $this->jposteruid);
            $tagarr['name'] = $tag['meta_value'];
            $tagarr['weight'] = $weight;
            $tagarr['url'] = $this->uri(array('action' => 'viewmeme', 'meme' => $tag['meta_value']), 'jabberblog');
            $tagarr['time'] = time();
            $taginfo[] = $tagarr;
        }
        $cloud .= $this->objTC->buildCloud($taginfo);
        $cloud .= "<br />";

        $lheader = new htmlHeading ( );
        $lheader->str = $this->objLanguage->languageText ( 'mod_jabberblog_loctags', 'jabberblog' );
        $lheader->type = 1;
        $cloud .= $lheader->show();
        $cloud .= "<br />";

        // and now the location tags
        $ltags = $this->objTags->getLocTagsByModule('jabberblog');
        // ok now get the weights
        $tagarr2 = array();
        foreach ( $ltags as $ltag) {
            $weight = $this->objTags->getTagWeight($ltag['meta_value'], $this->jposteruid);
            $tagarr2['name'] = $ltag['meta_value'];
            $tagarr2['weight'] = $weight;
            $tagarr2['url'] = $this->uri(array('action' => 'viewloc', 'loc' => $ltag['meta_value']), 'jabberblog');
            $tagarr2['time'] = time();
            $taginfo2[] = $tagarr2;
        }
        $this->objTC2 = $this->newObject('tagcloud', 'utilities');
        $cloud .= $this->objTC2->buildCloud($taginfo2);

        return $cloud;

    }
}
?>
