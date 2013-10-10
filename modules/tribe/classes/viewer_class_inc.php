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
 * @package   tribe
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
 * @package tribe
 *
 */
class viewer extends object {

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
    public $dbUsers;
    public $objDbAt;
    public $objDBIM;
    public $objConfig;

    /**
     *
     * Constructor

     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
        $this->objIcon = $this->getObject ( 'geticon', 'htmlelements' );
        $this->objLink = $this->getObject ( 'link', 'htmlelements' );
        $this->objDBIM = $this->getObject ( 'dbmsgs' );
        $this->objUser = $this->getObject ( 'user', 'security' );
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objWashout = $this->getObject ( 'washout', 'utilities' );
        $this->teeny = $this->getObject ( 'tiny', 'tinyurl');
        $this->dbUsers = $this->getObject('dbusers');
        $this->objDbAt = $this->getObject('dbatreplies');
    }

    public function renderSingle($msg) {
        $msg = $msg [0];
        $msgbody = $this->objWashout->parseText ( $msg ['msgbody'] );
        // run the parsers on the body
        $msgbody = $this->renderHashTags( $msgbody );
        $msgbody = $this->renderAtTags( $msgbody );
        $msgbody = $this->renderStarTags( $msgbody );

        $msgid = $msg ['id'];
        $objFeaturebox = $this->getObject ( 'featurebox', 'navigation' );
        $ret = $objFeaturebox->showContent ( '<strong>' . $this->objUser->fullName ( $this->objUser->userId() ) . '</strong> on ' . $msg ['datesent'], nl2br ( $msgbody ));
        $ret .= "<hr />";

        return $ret;
    }

    public function renderOutputForBrowser($msgs) {
        $ret = NULL;
        foreach ( $msgs as $msg ) {
            $msgtbl = $this->newObject('htmltable', 'htmlelements');
            $msgtbl->cellpadding = 5;
            $msgtbl->cellspacing = 5;
            $msgtbl->startRow();
            $msgbody = $this->objWashout->parseText ( $msg ['msgbody'] );
            // run the parsers on the body
            $msgbody = $this->renderHashTags( $msgbody );
            $msgbody = $this->renderAtTags( $msgbody );
            $msgbody = $this->renderStarTags( $msgbody );

            $fuser = $msg ['msgfrom'];
            $msgid = $msg ['id'];
            $sentat = $this->objLanguage->languageText ( 'mod_im_sentat', 'tribe' );
            $fromuser = $this->objLanguage->languageText ( 'mod_im_sentfrom', 'tribe' );
            if($msg['userid'] == NULL) {
                $user = $this->objLanguage->languageText("mod_tribe_unknownuser", "tribe");
            }
            else {
                $user = $this->objUser->userName ( $msg['userid'] );

            }
            $uimage = $this->objUser->getSmallUserImage($msg['userid'], $user);
            $msgtbl->addCell($uimage, 1);
            $msgtbl->addCell(nl2br($msgbody));
            $msgtbl->endRow();
            // The link to users page
            $userlink = $this->getObject ( 'link', 'htmlelements' );
            $userlink->href = $this->uri ( array ('user' => $user, 'action' => 'myhome' ) );
            $userlink->link = $user;
            // alt featurebox
            $objFeaturebox = $this->getObject ( 'featurebox', 'navigation' );
            $ret .= $objFeaturebox->showContent ( '<strong>' . $userlink->show() . '</strong>' ." ".$this->deltaTimes($msg ['datesent']), $msgtbl->show() . "<br />" );
            $ret .= "<hr />";
        }
        header ( "Content-Type: text/html;charset=utf-8" );
        return $ret;
    }

    /**
     * Method to render stats
     */
    public function getStatsBox() {
        $this->objDbIm = $this->getObject ( 'dbmsgs' );
        $this->objDbSubs = $this->getObject('dbsubs');
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->juser = $this->objSysConfig->getValue ( 'jabberuser', 'tribe' );
        $this->jdomain = $this->objSysConfig->getValue ( 'jabberdomain', 'tribe' );
        $str = $this->objLanguage->languageText ( "mod_tribe_subinfo", "tribe" ).": ".$this->juser."@".$this->jdomain;
        $str .= "<br />";
        $str .= "<br />";
        $str .= $this->objLanguage->languageText ( "mod_tribe_nomsgs", "tribe" );
        $str .= " " . $this->objDbIm->getNoMsgs ();
        $str .= "<br />";
        $str .= $this->objLanguage->languageText ( "mod_tribe_numsubs", "tribe" );
        $str .= " " . $this->objDbSubs->getNoSubs ();

        return $this->objFeatureBox->show ( $this->objLanguage->languageText ( "mod_tribe_stats", "tribe" ), $str );
    }

    /*public function showUserMenu() {
    $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
    $objWashout = $this->getObject ( 'washout', 'utilities' );
    $this->profiletext = $this->objSysConfig->getValue ( 'jposterprofile', 'tribe' );
    $menu = "<center>" . $this->objUser->getUserImage ( $this->jposteruid, FALSE, 'user_image' ) . "</center>";
    $blurb = $objWashout->parseText ( $this->profiletext );
    $objFeature = $this->newObject ( 'featurebox', 'navigation' );

    return $objFeature->show ( $this->objUser->fullName ( $this->jposteruid ), $menu . "<br />" . $blurb );
    }*/

    public function searchBox() {
        $this->loadClass('textinput', 'htmlelements');
        $qseekform = new form('qseek', $this->uri(array(
        'action' => 'jbsearch',
        )));
        $qseekform->addRule('searchterm', $this->objLanguage->languageText("mod_tribe_phrase_searchtermreq", "tribe") , 'required');
        $qseekterm = new textinput('searchterm');
        $qseekterm->size = 15;
        $qseekform->addToForm($qseekterm->show());
        $this->objsTButton = &new button($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setValue($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setToSubmit();
        $qseekform->addToForm($this->objsTButton->show());
        $qseekform = $qseekform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_tribe_qseek", "tribe") , $qseekform);

        return $ret;
    }

    public function rssBox($userid = NULL) {
        $this->objFeed = $this->getObject ( 'feeder', 'feed' );
        $this->objFeed->setupFeed ( $stylesheet = false, $this->objConfig->getSiteName(), 'Mobile Communities', $this->uri ( '' ), $this->uri ( '' ) );
        // get the latest say, 50 posts, and make a feed from em
        $this->objDbIm = $this->getObject ( 'dbmsgs' );
        $count = $this->objDbIm->getNoMsgs ();
        $num = $count - 50;
        if ($num < 0) {
            $num = 0;
        }
        $items = $this->objDbIm->getRange ( $num, $count );
        array_reverse ( $items );
        // now we add the items to the feed
        foreach ( $items as $item ) {
            $this->objFeed->addItem ( @$this->dbUsers->getUsernameFromJid($item ['msgfrom']), $this->uri ( array ('action' => 'viewsingle', 'postid' => $item ['id'] ), 'tribe' ), $item ['msgbody'], NULL, $this->objUser->userName ( $userid ), strtotime($item ['datesent']) );
        }

        return $this->objFeed->output ( "RSS2.0" );
    }

    public function parseHashtags($str, $itemId, $userid)
    {
        $str = stripslashes($str);
        preg_match_all('/\#([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $memetag = array($item);
            // add the $item to tbl_tags as a tribe meme for later
            $objTags = $this->getObject('dbtags', 'tagging');
            $objTags->insertHashTags($memetag, $userid, $itemId, 'tribe', NULL, NULL);
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
            $str = str_replace($results[0][$counter], "#".$hashlink->show()." ", $str);
            $counter++;
        }

        return $str;
    }

    public function parseAtTags($str, $itemId, $userid)
    {
        // test for an email addess, in which case leave it alone...
        preg_match_all('/\@([\w\-]+\.)+([a-zA-Z0-9_]{1,15}) ?/', $str, $email);
        if(isset($email[0][0]) && isset($email[2][0])) {
            $str = str_replace('@', '(at)', $str);
        }
        $str = stripslashes($str);
        preg_match_all('/\@([\w\-]+\.)+([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $attag = array($item);
            // add the $item to tbl_tags as a meme for later
            $objTags = $this->getObject('dbtags', 'tagging');
            $objTags->insertAtTags($attag, $userid, $itemId, 'tribe', NULL, NULL);
            $counter++;
        }

        return $str;
    }

    public function getAtTagsArr($str) {
        preg_match_all('/\@([\w\-]+\.)+([a-zA-Z0-9_]{1,15}) ?/', $str, $email);
        if(isset($email[0][0]) && isset($email[2][0])) {
            $str = str_replace('@', '(at)', $str);
        }
        $str = stripslashes($str);
        preg_match_all('/\@([a-zA-Z0-9_]{1,15}) ?/', $str, $results);

        return $results[1];
    }

    public function renderAtTags($str) {
        preg_match_all('/\@([\w\-]+\.)+([a-zA-Z0-9_]{1,15}) ?/', $str, $email);
        if(isset($email[0][0]) && isset($email[2][0])) {
            $str = str_replace('@', '(at)', $str);
        }
        $str = stripslashes($str);
        preg_match_all('/\@([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item) {
            // set up a link to the URI to display all posts in the meme
            $atlink = $this->getObject ( 'link', 'htmlelements' );
            $atlink->href = $this->uri ( array ('user' => $item, 'action' => 'myhome' ) );
            $atlink->link = $item;
            $str = str_replace($results[0][$counter], "@".$atlink->show()." ", $str);
            $counter++;
        }

        return $str;
    }

    public function parseStarTags($str, $itemId, $userid)
    {
        $str = stripslashes($str);
        preg_match_all('/\*([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $memetag = array($item);
            // add the $item to tbl_tags as a tribe meme for later
            $objTags = $this->getObject('dbtags', 'tagging');
            $objTags->insertStarTags($memetag, $userid, $itemId, 'tribe', NULL, NULL);
            $counter++;
        }

        return $str;
    }

    public function renderStarTags($str) {
        $str = stripslashes($str);
        preg_match_all('/\*([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item) {
            // set up a link to the URI to display all posts in the meme
            $starlink = $this->getObject ( 'link', 'htmlelements' );
            $starlink->href = $this->uri ( array ('loc' => $item, 'action' => 'viewlocation' ) );
            $starlink->link = $item;
            $str = str_replace($results[0][$counter], "*".$starlink->show()." ", $str);
            $counter++;
        }

        return $str;
    }

    public function renderLeftBoxen() {
        $leftColumn = NULL;
        $objIcon = $this->newObject ( 'geticon', 'htmlelements' );
        $this->loadClass('href', 'htmlelements');
        $objIcon->alt = 'SIOC';
        $objIcon->setIcon('sioc', 'gif');
        $sioclink = new href($this->uri(array('action' => 'sioc', 'sioc_type' => 'site')), $objIcon->show());

        $rssLink = $this->newObject ( 'link', 'htmlelements' );
        $rssLink->href = $this->uri ( array ('action' => 'rss' ) );
        $rssLink->link = $this->objLanguage->languageText ( "mod_tribe_showrss", "tribe" );

        $cloudLink = $this->newObject ( 'link', 'htmlelements' );
        $cloudLink->href = $this->uri ( array ('action' => 'clouds' ) );
        $cloudLink->link = $this->objLanguage->languageText ( "mod_tribe_showtagclouds", "tribe" );

        $objLT = $this->getObject ( 'block_lasttweet', 'twitter' );

        //$leftColumn .= $this->getStatsBox ();
        $leftColumn .= $this->objFeatureBox->show ( $this->objLanguage->languageText ( "mod_tribe_stuff", "tribe" ), $rssLink->show ()."<br />".$sioclink->show()."<br />".$cloudLink->show() );
        $leftColumn .= $this->searchBox();
        $leftColumn .= $this->userChecker();
        // show the last tweet block from the 'ol twitter stream
        //$leftColumn .= $this->objFeatureBox->show ( $this->objLanguage->languageText ( "mod_tribe_twitterfeed", "tribe" ), $objLT->show () );

        return $leftColumn;
    }

    public function renderRightBoxen($userid) {
        $rightColumn = NULL;
        if($userid != NULL) {
            // get the @ replies
            $atlimit = 10;
            $atreplies = $this->getAtReplies($userid, $atlimit);
            $rightColumn .= $this->objFeatureBox->show ("Last $atlimit @ replies", $atreplies);
            $createGrpLink = $this->getObject('alertbox', 'htmlelements');
            $ujid = $this->dbUsers->getJidfromUserId($this->objUser->userId());
            if($ujid != NULL) {
                $rightColumn .= $this->objFeatureBox->show ($this->objLanguage->languageText("mod_tribe_groups", "tribe"), $createGrpLink->show($this->objLanguage->languageText("mod_tribe_creategroup", "tribe"), $this->uri(array('action' => 'creategrpform'))));
            }
            else {
                $addlink = $this->getObject('alertbox', 'htmlelements');
                $rightColumn .= $this->objFeatureBox->show ($this->objLanguage->languageText("mod_tribe_groups", "tribe"), $addlink->show($this->objLanguage->languageText("mod_tribe_addjidnow", "tribe"), $this->uri(array('action' => 'addjidajax')))); // $this->objFeatureBox->show ($this->objLanguage->languageText("mod_tribe_groups", "tribe"), $this->objLanguage->languageText("mod_tribe_addjidnow", "tribe"));
            }

        }
        $number = 10;
        $rightColumn .= $this->objFeatureBox->show ($this->objLanguage->languageText("mod_tribe_latest", "tribe")." ".$number." ".$this->objLanguage->languageText("mod_tribe_groups", "tribe"), $this->showLatestGrpsBox($number));

        return $rightColumn;
    }

    public function renderTopBoxen() {
        $sitename = $this->objConfig->getSiteName();
        $this->objShare = $this->getObject('share', 'toolbar');
        $this->objShare->setup($this->uri(''), $sitename, $sitename.' Post: ');
        $middleColumn = $this->objShare->show();

        $vaLink = $this->newObject ( 'link', 'htmlelements' );
        $vaLink->href = $this->uri ( array ('action' => 'viewall' ) );
        $vaLink->link = $this->objLanguage->languageText ( "mod_tribe_viewpublic", "tribe" );
        $middleColumn .= " ".$vaLink->show();

        // lets do a text box so that folks can enter a message on the site also
        // first check that the user is logged in
        if($this->objUser->isLoggedIn() == TRUE) {
            $middleColumn .= "<br />";
            $middleColumn .= $this->doUpdateForm();
        }
        return $middleColumn;
    }

    public function showLatestGrpsBox($number) {
        $this->objGroups = $this->getObject('dbgroups');
        $this->objGrpMembers = $this->getObject('dbgroupmembers');
        $arr = $this->objGroups->getLastAll($number);
        $str = NULL;
        foreach($arr as $grp) {
            $jLink = $this->newObject ( 'link', 'htmlelements' );
            $jLink->href = $this->uri ( array ('action' => 'joingroup', 'groupid' => $grp['id'] ) );
            $jLink->link = $this->objLanguage->languageText ( "mod_tribe_joingroup", "tribe" );

            $gLink = $this->newObject ( 'link', 'htmlelements' );
            $gLink->href = $this->uri ( array ('action' => 'myhome', 'user' => $grp['groupname'] ) );
            $gLink->link = $grp['groupname'];


            // check that the user is not already a member
            if($this->objGrpMembers->isAMember($this->objUser->userId(), $grp['id'])) {
                $member = TRUE;
            }
            else {
                $member = FALSE;
            }
            if($grp['privacy'] == 'public') {
                $str .= $gLink->show()." ";
            }
            elseif($grp['privacy'] == 'private' && $member == TRUE) {
                $str .= $gLink->show()." ";
            }
            else {
                $str .= $grp['groupname']." ";
            }

            if($grp['privacy'] == 'public' && $this->objUser->isLoggedIn() && $member != TRUE) {
                $str .= "(".$grp['privacy'].")"." ".$jLink->show()."<br />";
            }
            elseif($grp['privacy'] == 'private' && $this->objUser->isLoggedIn() && $member != TRUE) {
                $str .= "(".$grp['privacy'].")"." ".'get invited <br />'; //$jLink->show()."<br />";
            }
            elseif($this->objUser->isLoggedIn() && $member == TRUE)  {
                $remLink = $this->newObject ( 'link', 'htmlelements' );
                $remLink->href = $this->uri ( array ('action' => 'leavegroup', 'groupid' => $grp['id'], 'userid' => $this->objUser->userId() ) );
                $remLink->link = $this->objLanguage->languageText ( "mod_tribe_leavegroup", "tribe" );

                $str .= "(".$grp['privacy'].")"." ".$remLink->show()."<br />";
            }
            else {
                $str .= "(".$grp['privacy'].")"."<br />";
            }
        }

        return $str;
    }

    public function getAtReplies($userid, $limit) {
        $replies = $this->objDbAt->getReplies($userid, $limit);
        $repstr = NULL;
        foreach ($replies as $rep) {
            $name = $this->objUser->userName($rep['fromid']);
            $msg = $this->objDBIM->getPostById($rep['msgid']);
            if(!empty($msg)) {
                $repstr .= $name.": ".$msg[0]['msgbody']."<hr />";
            }
        }
        return $repstr;

    }
    public function doTags() {

        // get a list of the tags and their weights
        $this->objTags = $this->getObject('dbtags', 'tagging');
        $this->loadClass ( 'htmlheading', 'htmlelements' );
        // set up the hasthtags header
        $hheader = new htmlHeading ( );
        $hheader->str = $this->objLanguage->languageText ( 'mod_tribe_hashtags', 'tribe' );
        $hheader->type = 1;
        $cloud = $hheader->show();
        $cloud .= "<br />";

        // Get the hashtags
        $tags = $this->objTags->getHashTagsByModule('tribe');
        // ok now get the weights
        $tagarr = array();
        $taginfo = NULL;
        foreach ( $tags as $tag) {
            $weight = $this->objTags->getModuleTagWeight($tag['meta_value'], 'tribe');
            $tagarr['name'] = $tag['meta_value'];
            $tagarr['weight'] = $weight;
            $tagarr['url'] = $this->uri(array('action' => 'viewmeme', 'meme' => $tag['meta_value']), 'tribe');
            $tagarr['time'] = time();
            $taginfo[] = $tagarr;
        }
        if($taginfo != NULL) {
            $this->objTC = $this->newObject('tagcloud', 'utilities');
            $cloud .= $this->objTC->buildCloud($taginfo);
            $cloud .= "<br />";
        }

        $lheader = new htmlHeading ( );
        $lheader->str = $this->objLanguage->languageText ( 'mod_tribe_loctags', 'tribe' );
        $lheader->type = 1;
        $cloud .= $lheader->show();
        $cloud .= "<br />";

        // and now the location tags
        $ltags = $this->objTags->getStarTagsByModule('tribe');
        // ok now get the weights
        $tagarr2 = array();
        $taginfo2 = NULL;
        foreach ( $ltags as $ltag) {
            $weight = $this->objTags->getModuleTagWeight($ltag['meta_value'], 'tribe');
            $tagarr2['name'] = $ltag['meta_value'];
            $tagarr2['weight'] = $weight;
            $tagarr2['url'] = $this->uri(array('action' => 'viewloc', 'loc' => $ltag['meta_value']), 'tribe');
            $tagarr2['time'] = time();
            $taginfo2[] = $tagarr2;
        }

        if($taginfo2 != NULL) {
            $this->objTC2 = $this->newObject('tagcloud', 'utilities');
            $cloud .= $this->objTC2->buildCloud($taginfo2);
            $cloud .= "<br />";
        }

        return $cloud;

    }

    /**
     * change RFC formatted time string to a "a few minutes ago..." style human time
     *
     * @param string RFC $time
     */
    public function deltaTimes($time) {
        // Get the time to a timestamp
        $ts = strtotime($time);
        $fudge = 1.25;
        $delta = time() - intval($ts);

        if($delta < 1 * $fudge) {
            return 'about a second ago';
        }
        elseif($delta < (60 * (1/$fudge))) {
            return "about ".intval($delta)." seconds ago";
        }
        elseif($delta < (60 * $fudge)) {
            return "about a minute ago";
        }
        elseif($delta < (60*60*(1/$fudge))) {
            return "about ".intval($delta/60)." minutes ago";
        }
        elseif($delta < (60 * 60 * $fudge)) {
            return 'about an hour ago';
        }
        elseif($delta < (60 * 60 * 24 * (1/$fudge))) {
            return "about  ".intval($delta / (60 * 60))." hours ago";
        }
        elseif($delta < (60 * 60 * 24 * $fudge)) {
            return 'about a day ago';
        }
        else {
            return "about ".intval($delta / (60 * 60 * 24))." days ago";
        }
    }

    public function userChecker() {
        if ($this->objUser->isLoggedIn()) {
            $uid = $this->objUser->userId();
            // check the JID exists in the table
            $jid = $this->dbUsers->getJidfromUserId($uid);
            if ($jid != NULL) {
                $objFeatureBox = $this->getObject('featurebox', 'navigation');
                $user = $this->objUser->userName();
                $userlink = $this->getObject ( 'link', 'htmlelements' );
                $userlink->href = $this->uri ( array ('user' => $user, 'action' => 'myhome' ) );
                $userlink->link = $user;
                $changelink = $this->getObject('alertbox', 'htmlelements');

                return $objFeatureBox->show($this->objLanguage->languageText("mod_tribe_loggedinas", "tribe"), $userlink->show()."<br />"."(".$jid.")"."<br />".$changelink->show($this->objLanguage->languageText("mod_tribe_changejid", "tribe"), $this->uri(array('action' => 'changejid'))));
            }
            else {
                return $this->showSignupBox();
            }
        }
        else {
            // show a login and register box
            return $this->loginBox();
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
            return $objBlocks->showBlock('login', 'security').$objBlocks->showBlock('register', 'security');
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            return $objFeatureBox->show($this->objLanguage->languageText("word_login", "system") , $objBlocks->showBlock('login', 'security').$objBlocks->showBlock('register', 'security') );
        }
    }

    public function showSignupBox($mode = NULL) {
        // show a box to register a jid and start playing!
        $this->loadClass('textinput', 'htmlelements');
        if ($mode == 'update') {
            $ajform = new form('addjid', $this->uri(array('action' => 'addjid', 'mode' => 'update')));
        }
        else {
            $ajform = new form('addjid', $this->uri(array('action' => 'addjid')));
        }
        if ($mode != 'ajax') {
            $ajform->addRule('jid', $this->objLanguage->languageText("mod_tribe_phrase_jidreq", "tribe") , 'required');
        }
        $ajterm = new textinput('jid');
        if($mode == 'update') {
            $ajterm->setValue($this->dbUsers->getJidfromUserId($this->objUser->userId()));
        }
        $ajterm->size = 15;
        $ajform->addToForm($ajterm->show());
        $this->obajButton = new button($this->objLanguage->languageText('word_add', 'system'));
        $this->obajButton->setValue($this->objLanguage->languageText('word_add', 'system'));
        $this->obajButton->setToSubmit();
        $ajform->addToForm($this->obajButton->show());
        $ajform = $ajform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_tribe_join", "tribe") , $this->objLanguage->languageText("mod_tribe_joininstructions", "tribe") . "<br />" . $ajform);

        return $ret;
    }

    public function createGroupBox() {
        // show a box to register a group and start playing!
        $this->loadClass('textinput', 'htmlelements');
        $gform = new form('creategroup', $this->uri(array('action' => 'creategroup')));
        $gform->addRule('groupname', $this->objLanguage->languageText("mod_tribe_phrase_groupnamereq", "tribe") , 'required');
        $gname = new textinput('groupname');

        // privacy dropdown
        $dd = new dropdown('privacy');
        $dd->addOption('public','Public');
        $dd->addOption('private','Private');

        $gname->size = 30;
        $gform->addToForm($gname->show()."<br />".$dd->show());
        $this->obGButton = new button($this->objLanguage->languageText('word_creatgroup', 'tribe'));
        $this->obGButton->setValue($this->objLanguage->languageText('word_creategroup', 'tribe'));
        $this->obGButton->setToSubmit();
        $gform->addToForm($this->obGButton->show());
        $gform = $gform->show();
        //$objFeatureBox = $this->getObject('featurebox', 'navigation');
        //$ret = $objFeatureBox->show($this->objLanguage->languageText("mod_tribe_creategroup", "tribe") , $this->objLanguage->languageText("mod_tribe_joininstructions", "tribe") . "<br />" . $ajform);

        return $gform;
    }

    public function doUpdateForm() {
        $upstuff = NULL;
        $this->loadClass ( 'htmlheading', 'htmlelements' );
        // set up the hasthtags header
        $upheader = new htmlHeading ( );
        $upheader->str = $this->objLanguage->languageText ( 'mod_tribe_updatenow', 'tribe' );
        $upheader->type = 2;
        $upstuff .= $upheader->show();
        // $upstuff .= "<br />";
        $upform = new form('updatestatus', $this->uri(array('action' => 'updatestatus')));
        $upform->addRule('update', $this->objLanguage->languageText("mod_tribe_phrase_updatereq", "tribe") , 'required');
        $this->loadClass('textarea', 'htmlelements');
        $textarea = new textarea('update' , '', 3, 80);
        $upform->addToForm($textarea->show()."<br />");

        $this->obUpButton = new button($this->objLanguage->languageText('word_update', 'tribe'));
        $this->obUpButton->setValue($this->objLanguage->languageText('word_update', 'tribe'));
        $this->obUpButton->setToSubmit();
        $upform->addToForm($this->obUpButton->show());
        $upstuff .= $upform->show();

        return $upstuff;
    }
}
?>