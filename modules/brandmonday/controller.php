<?php
/**
 * brandmonday controller class
 *
 * Class to control the brandmonday module
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
 * @category  chisimba
 * @package   brandmonday
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

class brandmonday extends controller {

    public $teeny;
    public $objLanguage;
    public $objModules;
    public $objTwitterLib;
    public $objCurl;
    public $objViewer;
    public $objDbBm;
    public $objBmOps;

    public function init() {
        try {
            $this->teeny = $this->getObject ( 'tiny', 'tinyurl' );
            $this->objCurl = $this->getObject('curl', 'utilities');
            $this->objConfig = $this->getObject('altconfig', 'config');
            //Create an instance of the language object
            $this->objLanguage = $this->getObject ( 'language', 'language' );
            $this->objModules = $this->getObject ( 'modules', 'modulecatalogue' );
            $this->objViewer = $this->getObject('viewer');
            $this->objDbBm = $this->getObject('dbbm');
            $this->objBmOps = $this->getObject('bmops');
            if ($this->objModules->checkIfRegistered ( 'twitter' )) {
                // Get other places to upstream content to
                $this->objTwitterLib = $this->getObject ( 'twitterlib', 'twitter' );
            }
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method to handle stuff
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            
            case 'json_getbrandplus':
                $resPlus = $this->objDbBm->getRange('tbl_bmplus', 0, 100);
                echo json_encode(array('totalCount' =>count($resPlus->results), 'tweets'=> $resPlus->results));
                exit(0);
                break;
            
            case 'json_getbrandminus':
                $resMinus = $this->objDbBm->getRange('tbl_bmminus', 0, 100);
                echo json_encode(array('totalCount' =>count($resMinus->results), 'tweets'=> $resMinus->results));
                exit(0);
                break;
            
            case 'json_getmentions':
                $resMentions = $this->objDbBm->getRange('tbl_bmmentions', 0, 100);
                echo json_encode(array('totalCount' =>count($resMentions->results), 'tweets'=> $resMentions->results));
                exit(0);
                break;
                
            case 'getplusmap':
                header('Content-type: text/xml');
                echo $this->objDbBm->getLastByUser('plus');
                break;
                
            case 'getminusmap':
                header('Content-type: text/xml');
                echo $this->objDbBm->getLastByUser('minus');
                break;
            
            case 'getmentionsmap':
                header('Content-type: text/xml');
                echo $this->objDbBm->getLastByUser('mentions');
                break;
                
            case 'main' :
                break;

            default: 
            	
                $this->requiresLogin('default');
                $path = $this->objConfig->getModulePath()."brandmonday/update";
                if(!file_exists($path)) {
                    touch($path);
                    chmod($path, 0777);
                }
                $lastupdate = file_get_contents($path);
                $lastupdate = explode("|", $lastupdate);
                if($lastupdate[0] == '' || $lastupdate[1] == '' || $lastupdate[2] == '') {
                    $minusurl = "http://search.twitter.com/search.json?q=&ands=BrandMinus&phrase=&ors=&nots=BrandPlus&lang=all&from=&to=&ref=&since_id=$lastupdate&rpp=100";
                    $plusurl = "http://search.twitter.com/search.json?q=&ands=BrandPlus&phrase=&ors=&nots=BrandMinus&lang=all&from=&to=&ref=&since_id=$lastupdate&rpp=100";
                    $menurl = "http://search.twitter.com/search.json?q=%23BrandMonday&lang=all&since_id=$lastupdate";
                }
                else {
                    $minusurl = "http://search.twitter.com/search.json?".$lastupdate[0];
                    $plusurl = "http://search.twitter.com/search.json?".$lastupdate[1];
                    $menurl = "http://search.twitter.com/search.json?".$lastupdate[2];
                }
                $resMinus = $this->objCurl->exec($minusurl);
                $resMinus = json_decode($resMinus);
                $resPlus = $this->objCurl->exec($plusurl);
                $resPlus = json_decode($resPlus);
                $resMentions = $this->objCurl->exec($menurl);
                $resMentions = json_decode($resMentions);
                $pluslast = $resPlus->refresh_url;
                $minlast = $resMinus->refresh_url;
                $menlast = $resMentions->refresh_url;
                $this->objDbBm->smartUpdate($resMinus, $resPlus, $resMentions);
                if(file_exists($path)) {
                    unlink($path);
                    touch($path);
                    chmod($path, 0777);
                    if(is_object($resMinus) && is_object($resPlus) && is_object($resMentions)) {
                        file_put_contents($path, $resMinus->refresh_url."|".$resPlus->refresh_url."|".$resMentions->refresh_url);
                    }
                }

                $resMinus = $this->objDbBm->getRange('tbl_bmminus', 0, 100);
                $resPlus = $this->objDbBm->getRange('tbl_bmplus', 0, 100);
                $resMentions = $this->objDbBm->getRange('tbl_bmmentions', 0, 100);
               
                $this->setVarByRef('resMentions', $resMentions);
                $this->setVarByRef('resMinus', $resMinus);
                $this->setVarByRef('resPlus', $resPlus);
				return "main_tpl.php";
                return 'view_tpl.php';
                break;

            case 'happypeeps':
                echo $this->objBmOps->happyPeepsTagCloud();
                exit(0);
                break;

            case 'sadpeeps':
                echo $this->objBmOps->sadPeepsTagCloud();
                break;

            case 'activepeeps':
                echo $this->objBmOps->activePeepsTagCloud();
                break;

            case 'bestserv':
                $ret = NULL;
                $ret .= "<div class=\"bestserv\">";
                $ret .=  $this->objBmOps->bestServiceTagCloud();
                $ret .= "<h2>All time:</h2>";
                $ret .= $this->objBmOps->bestServicePieChartAll();
                $ret .= "<span class=\"thisweek\"><h2>This week:</h2>";
                $ret .= $this->objBmOps->bestServicePieChartWeek()."</span></div>";
                echo $ret;
                break;
                
            case 'bestservcloud':
            	echo  $this->objBmOps->bestServiceTagCloud();
				break;
				
			case 'bestservalltime':
            	echo  $this->objBmOps->bestServicePieChartAll();
				break;
			
			case 'bestservthisweek':
            	echo  $this->objBmOps->bestServicePieChartWeek();
				break;
					
            case 'worstserv':
                $ret = NULL;
                $ret .= "<div class=\"bestserv\"><h2>Cloud:</h2><br />";
                $ret .= $this->objBmOps->worstServiceTagCloud();
                $ret .= "<br /><h2>All time:</h2><br />";
                $ret .= $this->objBmOps->worstServicePieChartAll();
                $ret .= "<br /><h2>This week:</h2><br />";
                $ret .= $this->objBmOps->worstServicePieChartWeek().'</div>';
                echo $ret;
                break;
                
            case 'worstcloud':
            	echo  $this->objBmOps->worstServiceTagCloud();
				break;
				
			case 'worstalltime':
            	echo  $this->objBmOps->worstServicePieChartAll();
				break;
			
			case 'worstthisweek':
            	echo  $this->objBmOps->worstServicePieChartWeek();
				break;

            case 'mentions':
                echo $this->objBmOps->mentionsTagCloud();
                break;

            case 'allbsdata':
                echo $this->objBmOps->getAllBsData();
                break;

            case 'weekbsdata':
                echo $this->objBmOps->getWeekBsData();
                break;

            case 'allwsdata':
                echo $this->objBmOps->getAllWsData();
                break;

            case 'weekwsdata':
                echo $this->objBmOps->getWeekWsData();
                break;

            case 'feed':
                $this->objFeedCreator = $this->getObject('feeder', 'feed');
                //get the feed format parameter from the querystring
                $mood = $this->getParam('mood', 'plus');
                $format = 'RSS2.0'; // $this->getParam('feedselector');
                //grab the feed items
                $posts = $this->objBmOps->getLastPosts(50, $mood);

                //set up the feed...
                $fullname = "#BrandMonday";
                //title of the feed
                $feedtitle = htmlentities($fullname);
                //description
                $feedDescription = "RSS2.0 Feed of the #BrandMonday stream";

                //link back to the blog
                $feedLink = $this->objConfig->getSiteRoot() . "index.php?module=brandmonday";
                //sanitize the link
                $feedLink = htmlentities($feedLink);
                //set up the url
                $feedURL = $this->objConfig->getSiteRoot() . "index.php?module=brandmonday&action=feed";
                $feedURL = htmlentities($feedURL);
                //set up the feed
                $this->objFeedCreator->setupFeed(TRUE, $feedtitle, $feedDescription, $feedLink, $feedURL);
                //loop through the posts and create feed items from them
                foreach($posts as $feeditems) {
                    foreach($feeditems as $feeditem) {
                        //use the post title as the feed item title
                        $itemTitle = $feeditem['from_user'];
                        $itemLink = $this->uri('');
                        //description
                        $itemDescription = stripslashes($feeditem['tweet']);
                        //where are we getting this from
                        $itemSource = $this->objConfig->getSiteRoot() . "index.php?module=brandmonday";
                        //feed author
                        $auth = $feeditem['from_user'];
                        $itemAuthor = htmlentities($auth."<$auth@capetown.peeps.co.za>");
                        //add this item to the feed
                        $this->objFeedCreator->addItem($itemTitle, $itemLink, $itemDescription, $itemSource, $itemAuthor);
                    }
                }
                //check which format was chosen and output according to that
                $feed = $this->objFeedCreator->output(); //defaults to RSS2.0
                echo htmlentities($feed);
                break;


        }
    }

    /**
     * Overide the login object in the parent class
     *
     * @param  void
     * @return bool
     * @access public
     */
    public function requiresLogin($action) {
        return FALSE;
    }

}
?>
