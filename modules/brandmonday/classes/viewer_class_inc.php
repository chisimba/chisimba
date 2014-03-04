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
 * @package   brandmonday
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
 * @package brandmonday
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
        
        $this->objUser = $this->getObject ( 'user', 'security' );
        //$this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objWashout = $this->getObject ( 'washout', 'utilities' );
        $this->teeny = $this->getObject ( 'tiny', 'tinyurl');
    }

    public function renderCompView($plus = NULL, $minus = NULL, $mentions = NULL) {
        if(is_object($plus)) {
            $plus = $plus->results;
        }
        else {
            $plus = array();
        }
        if(is_object($minus)) {
            $minus = $minus->results;
        }
        else {
            $minus = array();
        }
        if(is_object($mentions)) {
            $mentions = $mentions->results;
        }
        else {
            $mentions = array();
        }
        $messages = NULL;
        $plusmessages = NULL;
        $minusmessages = NULL;
        $menmessages = NULL;
        // ok so lets do the iterations and build our output
        foreach($plus as $pos) {
            $text = $pos['tweet'];
            $pic = $pos['image'];
            $user = $pos['from_user'];
            $createdat = $pos['createdat'];
            $usrlink = $this->newObject('link', 'htmlelements');
            $usrlink->href = "http://twitter.com/$user";
            $usrlink->link = $user;
            $txt = "<b>".$usrlink->show()."</b> ".$text."<br />".$createdat;
            $image = "<a href='http://twitter.com/".$user."'><img src='$pic' height='48', width='48' /></a>";
            // bust out a table to format the lot, then bang it in a feturebox
            $msgtbl = $this->newObject('htmltable', 'htmlelements');
            $msgtbl->cellpadding = 3;
            $msgtbl->cellspacing = 3;
            $msgtbl->startRow();
            $msgtbl->addCell($image, 1);
            $msgtbl->addCell($this->objWashout->parseText($txt));
            $msgtbl->endRow();

            $plusmessages .= $msgtbl->show();
        }

        // now the minus messages
        foreach($minus as $neg) {
            $text = $neg['tweet'];
            $pic = $neg['image'];
            $user = $neg['from_user'];
            $createdat = $neg['createdat'];
            $usrlink = $this->newObject('link', 'htmlelements');
            $usrlink->href = "http://twitter.com/$user";
            $usrlink->link = $user;
            $txt = "<b>".$usrlink->show()."</b> ".$text."<br />".$createdat;
            $image = "<a href='http://twitter.com/".$user."'><img src='$pic' height='48', width='48' /></a>";
            // bust out a table to format the lot, then bang it in a feturebox
            $msgtbl2 = $this->newObject('htmltable', 'htmlelements');
            $msgtbl2->cellpadding = 3;
            $msgtbl2->cellspacing = 3;
            $msgtbl2->startRow();
            $msgtbl2->addCell($image, 1);
            $msgtbl2->addCell($this->objWashout->parseText($txt));
            $msgtbl2->endRow();

            $minusmessages .= $msgtbl2->show();
        }

        // mentions messages
        foreach($mentions as $men) {
            $text = $men['tweet'];
            $pic = $men['image'];
            $user = $men['from_user'];
            $createdat = $men['createdat'];
            $usrlink = $this->newObject('link', 'htmlelements');
            $usrlink->href = "http://twitter.com/$user";
            $usrlink->link = $user;
            $txt = "<b>".$usrlink->show()."</b> ".$text."<br />".$createdat;
            $image = "<a href='http://twitter.com/".$user."'><img src='$pic' height='48', width='48' /></a>";
            // bust out a table to format the lot, then bang it in a feturebox
            $msgtbl3 = $this->newObject('htmltable', 'htmlelements');
            $msgtbl3->cellpadding = 3;
            $msgtbl3->cellspacing = 3;
            $msgtbl3->startRow();
            $msgtbl3->addCell($image, 1);
            $msgtbl3->addCell($this->objWashout->parseText($txt));
            $msgtbl3->endRow();

            $menmessages .= $msgtbl3->show();
        }

        // $minusmessages = $minusmessages.$failmessages;

        // 2 more headings, BrandPlus and BrandMinus needed now
        $this->loadClass ( 'htmlheading', 'htmlelements' );
        $bp = new htmlHeading ( );
        $bp->str = $this->objLanguage->languageText ( 'mod_brandmonday_bp', 'brandmonday' );
        $bp->type = 3;

        $bm = new htmlHeading ( );
        $bm->str = $this->objLanguage->languageText ( 'mod_brandmonday_bm', 'brandmonday' );
        $bm->type = 3;

        $bigtbl = $this->newObject('htmltable', 'htmlelements');
        $bigtbl->cellpadding = 3;
        $bigtbl->cellspacing = 3;
        $bigtbl->border = 1;
        $bigtbl->startRow();
        $bigtbl->addCell($bp->show()."<br />".$plusmessages);
        $bigtbl->addCell($bm->show()."<br />".$minusmessages);
        $bigtbl->endRow();
        
        $mtbl = $this->newObject('htmltable', 'htmlelements');
        $mtbl->cellpadding = 3;
        $mtbl->cellspacing = 3;
        $mtbl->border = 1;
        $mtbl->startRow();
        $mtbl->addCell($menmessages);
        $mtbl->endRow();

        $mh = new htmlHeading ( );
        $mh->str = $this->objLanguage->languageText ( 'mod_brandmonday_mentions', 'brandmonday' );
        $mh->type = 3;
        header ( "Content-Type: text/html;charset=utf-8" );
        return $bigtbl->show()."<br />".$mh->show().$mtbl->show();
    }

    public function adBlocks() {
        $this->objWashout = $this->getObject("washout", "utilities");
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $adhead1 = $this->objSysConfig->getValue ( 'adhead1', 'brandmonday' );
        $adhead2 = $this->objSysConfig->getValue ( 'adhead2', 'brandmonday' );
        $fbhead = $this->objSysConfig->getValue ( 'fbhead', 'brandmonday' );
        $fbtext = $this->objSysConfig->getValue ( 'fbtext', 'brandmonday' );
        $adtext1 = $this->objSysConfig->getValue ( 'adtext1', 'brandmonday' );
        $adtext2 = $this->objSysConfig->getValue ( 'adtext2', 'brandmonday' );
        $ret = NULL;

        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret .= $objFeatureBox->show($fbhead, $this->objWashout->parseText($fbtext));

        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret .= $objFeatureBox->show($adhead1, $this->objWashout->parseText($adtext1));

        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret .= $objFeatureBox->show($adhead2, $this->objWashout->parseText($adtext2));

        return $ret;
    }

    public function chisimbaBlock() {
        $this->objWashout = $this->getObject("washout", "utilities");
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $chistext = $this->objSysConfig->getValue ( 'chistext', 'brandmonday' );
        $chishead = $this->objSysConfig->getValue ( 'chishead', 'brandmonday' );
        $ret = NULL;

        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret .= $objFeatureBox->show($chishead, $this->objWashout->parseText($chistext));
        
        return $ret;
    }

    public function awardsBlock() {
        $this->objWashout = $this->getObject("washout", "utilities");
        $ret = NULL;
        $linklist = NULL;

        // happiest tweeter
        $htlink = $this->newObject('alertbox', 'htmlelements');
        $linklist .= $htlink->show($this->objLanguage->languageText("mod_brandmonday_happypeeps", "brandmonday"), $this->uri(array('action' => 'happypeeps')));
        $linklist .= "<br />";
        // saddest tweeter
        $sadlink = $this->newObject('alertbox', 'htmlelements');
        $linklist .= $sadlink->show($this->objLanguage->languageText("mod_brandmonday_sadpeeps", "brandmonday"), $this->uri(array('action' => 'sadpeeps')));
        $linklist .= "<br />";
        // most tweets
        $aclink = $this->newObject('alertbox', 'htmlelements');
        $linklist .= $aclink->show($this->objLanguage->languageText("mod_brandmonday_activepeeps", "brandmonday"), $this->uri(array('action' => 'activepeeps')));
        $linklist .= "<br />";
        // best service
        $bslink = $this->newObject('alertbox', 'htmlelements');
        $linklist .= $bslink->show($this->objLanguage->languageText("mod_brandmonday_bestserv", "brandmonday"), $this->uri(array('action' => 'bestserv')));
        $linklist .= "<br />";
        // worst service
        $wslink = $this->newObject('alertbox', 'htmlelements');
        $linklist .= $wslink->show($this->objLanguage->languageText("mod_brandmonday_worstserv", "brandmonday"), $this->uri(array('action' => 'worstserv')));
        $linklist .= "<br />";
        // most mentions
        $menlink = $this->newObject('alertbox', 'htmlelements');
        $linklist .= $menlink->show($this->objLanguage->languageText("mod_brandmonday_mentions", "brandmonday"), $this->uri(array('action' => 'mentions')));
        $linklist .= "<br />";

        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret .= $objFeatureBox->show($this->objLanguage->languageText("mod_brandmonday_awards", "brandmonday"), $linklist);
        
        return $ret;
    }

    public function aboutBlock($showBox = true) {
        $this->objWashout = $this->getObject("washout", "utilities");
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $abouttext = $this->objSysConfig->getValue ( 'abouttext', 'brandmonday' );
        $abouthead = $this->objSysConfig->getValue ( 'abouthead', 'brandmonday' );
        $ret = NULL;

        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret .= ($showBox) ? $objFeatureBox->show($abouthead, $this->objWashout->parseText($abouttext)) : $this->objWashout->parseText($abouttext);
        
        return $ret;
    }

    public function tweetBlock($showBox) {
        $objTwitterRemote = $this->getObject("twitterremote", "twitter");
        $objTwitterRemote->userName = "CapeTown";
        $tweets = $objTwitterRemote->showTimeline(FALSE, 'user');

        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $text = $tweets;
        $ret = NULL;
        $ret .= $objFeatureBox->show("@CapeTown", $this->objWashout->parseText($text));
        
        return $ret;
    }

    public function disclaimerBlock() {
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $text = $this->objLanguage->languageText("mod_brandmonday_disclaimertext", "brandmonday");
        $ret = NULL;
        $ret .= $objFeatureBox->show($this->objLanguage->languageText("mod_brandmonday_disclaimer", "brandmonday"), $this->objWashout->parseText($text));
        
        return $ret;
    }

    public function rssBlock($showBox = TRUE) {
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $text = "Feeds"; //$this->objLanguage->languageText("mod_brandmonday_rss", "brandmonday");
        $ret = NULL;
        $happylink = $this->newObject('link', 'htmlelements');
        $happylink->href = $this->uri(array('action' => 'feed'));
        $happylink->link = "#BrandPlus RSS Feed";
        $sadlink = $this->newObject('link', 'htmlelements');
        $sadlink->href = $this->uri(array('action' => 'feed', 'mood' => 'uncool'));
        $sadlink->link = "#BrandMinus RSS Feed";
            
        $rss = $happylink->show()."<br />";
        $rss .= $sadlink->show();
        $ret .= ($showBox) ? $objFeatureBox->show($text, $rss) : $rss;
        
        return $ret;
    }
    
    public function tweetThisBox($showBox = true) {
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        
        $this->teeny = $this->getObject ( 'tiny', 'tinyurl');
        // set up a link for Tweet this
        $tlink = $this->newObject ( 'link', 'htmlelements' );
        $tlink->href = "http://twitter.com/home/?status=".urlencode($this->objLanguage->languageText ( "mod_brandmonday_accessgranted", "brandmonday" )).": ".$this->teeny->create(urlencode($this->uri('')));
        $tlink->link = $this->objLanguage->languageText ( "mod_brandmonday_tweetthis", "brandmonday" );
        $tlink->target = "_blank";
        $str = $tlink->show()."<br />".$this->objLanguage->languageText("mod_brandmonday_tweetthisblurb", "brandmonday");
        
        return ($showBox) ? $objFeatureBox->show($this->objLanguage->languageText ( "mod_brandmonday_tweetthis", "brandmonday" ), $str) : $str;
    }
    
    public function loginBlock() {
        $objLogin = $this->getObject('logininterface', 'security');
        return $objLogin->renderLoginBox('brandmonday');
    }

    
}
?>
