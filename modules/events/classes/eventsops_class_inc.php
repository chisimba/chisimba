<?php
/**
 *
 * Events helper class
 *
 * PHP version 5.1.0+
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
 * @package   events
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 AVOIR
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
 * Events helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package events
 *
 */
class eventsops extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objWashout String object property for holding the washout object
     *
     * @access public
     */
    public $objWashout;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;

    /**
     * @var string $objCurl String object property for holding the curl object
     *
     * @access public
     */
    public $objCurl;

    /**
     * @var string $objLangCode String object property for holding the language code object
     *
     * @access public
     */
    public $objLangCode;

    public $objTags;
    public $objUtils;
    public $objFoafOps;
    public $friendcount = 0;
    public $foafProfile;
    public $objSocial;
    public $objTwitOps;
    
    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objWashout    = $this->getObject('washout', 'utilities');
        $this->objUser       = $this->getObject('user', 'security');
        $this->objCurl       = $this->getObject('curlwrapper', 'utilities');
        $this->objLangCode   = $this->getObject('languagecode', 'language');
        $this->objTags       = $this->getObject('dbtags', 'tagging');
        $this->objCookie     = $this->getObject('cookie', 'utilities');
        $this->objDbEvents   = $this->getObject('dbevents');
        $this->objUtils      = $this->getObject('eventsutils');
        $this->objFoafOps    = $this->getObject('foafops', 'foaf');
        //the object needed to create FOAF files (RDF)
        $this->objFoaf       = $this->getObject('foafcreator', 'foaf');
        //Object to parse and display FOAF RDF
        $this->objFoafParser = $this->getObject('foafparser', 'foaf');
        $this->dbFoaf        = $this->getObject('dbfoaf', 'foaf');
        $this->objSocial     = $this->getObject('eventssocial');
        $this->objTwitOps    = $this->getObject('twitoasterops', 'twitoaster');
        $this->setupFoaf();
    }
    
    public function setupFoaf() {
        if($this->objUser->isLoggedIn()) {
            $this->objFoafOps->newPerson($this->objUser->userId());
            //add in other details if they exist
            $this->objFoafOps->myFoaf($this->objUser->userId());
            $this->objFoafOps->writeFoaf();
            $midcontent = $this->objFoafOps->foaf2Object($this->objUser->userId());
            $this->foafProfile = $midcontent;
            if(is_array($midcontent->foaf) && !empty($midcontent->foaf) && array_key_exists('knows', $midcontent->foaf)) {
                $this->friendcount = count($midcontent->foaf['knows']);
            }
         }
         else {
             return NULL;
         }
    }

    /**
     * Grabs the client IP address
     *
     * This function should be used to grab IP addresses, even those behind proxies, to gather data from
     *
     * @return string $ip
     */
    public function getIpAddr() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Convert IP to an integer
     *
     * Method used to create an integer from an IP address so that it can easily be stored, indexed and retrieved in a database table
     *
     * @param string $ip
     * @return integer
     */
    public function ip2integer($ip) {
        $ip_aton = sprintf("%u", ip2long($ip));
        return $ip_aton;
    }

    /**
     * Creates a dropdown list of countries
     *
     * This method is a simple local wrapper for the langcode method to generate a dropdown list of country names and codes
     *
     * @param string $country
     * @return string
     */
    public function countryDropdown($country = 'ZA') {
        return $this->objLangCode->countryAlpha($default);
    }
    
    public function fbComment() {
        $script = "<script src=\"http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_US\" type=\"text/javascript\"></script><script type=\"text/javascript\">FB.init(\"0793e611b2e1fd2d786329a6ee703947\");</script><fb:comments> </fb:comments>";
        return $script;
    }

    /**
     * Create a header for location
     *
     * Method to manipulate the header information for the location set and change methods
     *
     * @return string HTML of the header
     */
    public function locationHeader() {
        if($this->objCookie->exists('events_location') ) {
            $currLocation = $this->objCookie->get('events_location');
        }
        else {
            $currLocation = "(".$this->objLanguage->languageText("mod_events_locnotset", "events").")";
        }
        // Change location link
        $changeloclink = $this->newObject('link', 'htmlelements');
        $changeloclink->href = $this->uri(array('action' => 'changelocation'));
        $changeloclink->link = $this->objLanguage->languageText("mod_events_changeloc", "events");
        // set up the heading
        $this->loadClass ( 'htmlheading', 'htmlelements' );
        $header = new htmlheading();
        $header->type = 1;
        $header->str = $this->objLanguage->languageText("mod_events_guidefor", "events")." ".$currLocation;
        $clocheader = new htmlheading();
        $clocheader->type = 3;
        $clocheader->str = $changeloclink->show();

        return $header->show()."  ".$clocheader->show();
    }

    /**
     * Link list of the availale event categories
     *
     * The list is fetched from the database table that stores event types and dynamically generated
     *
     * @return string
     */
    public function browseEventsBox() {
        // Set up a featurebox object
        $objFeaturebox = $this->getObject('featurebox', 'navigation');
        // Set up a link box of all the categories
        $this->loadClass ( 'htmlheading', 'htmlelements' );
        // get the list of categories from the db
        $cats = $this->objDbEvents->categoryGetList();
        $list = NULL;
        // do a 0 cat first (home link)
        $catname = $this->objLanguage->languageText("mod_events_home", "events");
        $catid = 0;
        $header = new htmlheading();
        $catlink = $this->newObject('link', 'htmlelements');
        $header->type = 4;
        $header->str = $catname;
        $catlink->href = $this->uri(array('action' => 'showcat', 'cat' => $catid));
        $catlink->link = $header->show();
        $list .= $catlink->show();
        foreach($cats as $cat) {
            $catname = $cat['cat_name'];
            $catid = $cat['id'];
            $header = new htmlheading();
            $catlink = $this->newObject('link', 'htmlelements');
            $header->type = 4;
            $header->str = $catname;
            $catlink->href = $this->uri(array('action' => 'showcat', 'cat' => $catid));
            $catlink->link = $header->show();
            $list .= $catlink->show();
        }
        return $objFeaturebox->show($this->objLanguage->languageText('mod_events_cats', 'events'), $list);
    }

    /**
     * Welcome block
     *
     * Block to display welcome messages when logged in, or a sign in link if not
     *
     * @return string
     */
    public function showWelcomeBox() {
        $objFeaturebox = $this->getObject('featurebox', 'navigation');
        $linklist = NULL;
        if($this->objUser->isLoggedIn() == FALSE) {
            $signinlink = $this->newObject('alertbox', 'htmlelements');
            $signuplink = $this->newObject('alertbox', 'htmlelements');
            $registerlink = $this->newObject('alertbox', 'htmlelements');
            // Make sure to show the sign up link only if registrations are allowed!
            if(strtolower($this->objConfig->getallowSelfRegister()) == 'true') {
                $signuplink = $signuplink->show($this->objLanguage->languageText("mod_events_signup", "events"), $this->uri(array('action' => 'showregister'), 'userregistration'));
            }
            else {
                $signuplink = NULL;
            }
            $signinlink = $signinlink->show($this->objLanguage->languageText("mod_events_signin", "events"), $this->uri(array('action' => 'showsignin')))." ".$this->objLanguage->languageText("mod_events_toseeevents", "events").", ";
            $linklist .= $signinlink;
            $linklist .= $this->objLanguage->languageText("mod_events_orifyoudonthaveacc", "events").", ".$signuplink;
        }
        else {
            //user is logged in
            $invitelink = $this->newObject('alertbox', 'htmlelements');
            $invitelink = $invitelink->show($this->objLanguage->languageText("mod_events_invitefriends", "events"), $this->uri(array('action' => 'invitefriend')));

            $linklist .= $invitelink;
        }
        // location link is always visible
        $changeloclink = $this->newObject('link', 'htmlelements');
        $changeloclink->href = $this->uri(array('action' => 'changelocation'));
        $changeloclink->link = $this->objLanguage->languageText("mod_events_changeloc", "events");

        $linklist .= "<br />".$changeloclink->show();
        return $objFeaturebox->show($this->objLanguage->languageText("mod_events_welcome", "events"),$linklist);
    }

    /**
     * Sign in block
     *
     * Used in conjunction with the welcome block as a alertbox link. The sign in simply displays the block to sign in to Chisimba
     *
     * @return string
     */
    public function showSignInBox() {
        $objBlocks = $this->getObject('blocks', 'blocks');
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        return $objFeatureBox->show($this->objLanguage->languageText("mod_events_signin", "events"), $objBlocks->showBlock('login', 'security', 'none'));
    }

    /**
     * Sign up block
     *
     * Method to generate a sign up (register) block for the module. It uses a linked alertbox to format the response
     *
     * @return string
     */
    public function showSignUpBox() {
        $objBlocks = $this->getObject('blocks', 'blocks');
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        return $objFeatureBox->show($this->objLanguage->languageText("mod_events_signup", "events"), $objBlocks->showBlock('register', 'security', 'none'));
    }

    /**
     * Block to display local weather from the location set in the user cookie.
     *
     * The user cookie is read and a weather lookup generated accordingly. This feature requires a network connection
     *
     * @return string
     */
    public function showLocWeatherBox() {
        $objFeaturebox = $this->getObject('featurebox', 'navigation');
        $wtable = NULL;
        $latlon = $this->objCookie->get('events_latlon');
        $locarr = explode("|", $latlon);
        if($locarr[0] != '' && $locarr != '') {
            $lat = $locarr[0];
            $lon = $locarr[1];
            $weather = $this->findNearbyWeather($lat, $lon);
            if(empty($weather)) {
                $weather = $this->objLanguage->languageText("mod_events_weathernotfound", "events");
            }
            else {
                $ltemp = $this->objLanguage->languageText("mod_events_temperature", "events");
                $ldewpoint = $this->objLanguage->languageText("mod_events_dewpoint", "events");
                $lhumidity = $this->objLanguage->languageText("mod_events_humidity", "events");
                $lwindspeed = $this->objLanguage->languageText("mod_events_windspeed", "events");
                $lclouds = $this->objLanguage->languageText("mod_events_cloudcover", "events");
                $ldatetime = $this->objLanguage->languageText("mod_events_datetime", "events");
                // format into a table
                $wtable = $this->newObject('htmltable', 'htmlelements');
                $wtable->startRow();
                $wtable->addCell($ltemp);
                $wtable->addCell($weather['temperature']);
                $wtable->endRow();
                $wtable->startRow();
                $wtable->addCell($ldewpoint);
                $wtable->addCell($weather['dewpoint']);
                $wtable->endRow();
                $wtable->startRow();
                $wtable->addCell($lhumidity);
                $wtable->addCell($weather['humidity']);
                $wtable->endRow();
                $wtable->startRow();
                $wtable->addCell($lwindspeed);
                $wtable->addCell($weather['windspeed']);
                $wtable->endRow();
                $wtable->startRow();
                $wtable->addCell($lclouds);
                $wtable->addCell($weather['clouds']);
                $wtable->endRow();
                $wtable->startRow();
                $wtable->addCell($ldatetime);
                $wtable->addCell($weather['datetime']);
                $wtable->endRow();

                $wtable = $wtable->show();
            }
        }
        if($this->objCookie->exists('events_location') ) {
            $currLocation = $this->objCookie->get('events_location');
        }
        else {
            $currLocation = "(".$this->objLanguage->languageText("mod_events_locnotset", "events").")";
        }
        if($wtable == NULL) {
            $wtable = $this->objLanguage->languageText("mod_events_pleasesetlocation", "events");
        }
        return $objFeaturebox->show($this->objLanguage->languageText("mod_events_locweatherfor", "events")." ".$currLocation, $wtable);
    }
 
    public function archiveBox() {
        $objFeaturebox = $this->getObject('featurebox', 'navigation');
        return $objFeaturebox->show($this->objLanguage->languageText("mod_events_eventarchive", "events"), $this->getAllRecentContent());
    }
    
    /**
     * Block to display local weather from the location set in the user cookie.
     *
     * The user cookie is read and a weather lookup generated accordingly. This feature requires a network connection
     *
     * @return string
     */
    public function showPlaceWeatherBox($lat, $lon) {
        $objFeaturebox = $this->getObject('featurebox', 'navigation');
        $wtable = NULL;
        $weather = $this->findNearbyWeather($lat, $lon);
        if(empty($weather)) {
            $weather = $this->objLanguage->languageText("mod_events_weathernotfound", "events");
        }
        else {
            $ltemp = $this->objLanguage->languageText("mod_events_temperature", "events");
            $ldewpoint = $this->objLanguage->languageText("mod_events_dewpoint", "events");
            $lhumidity = $this->objLanguage->languageText("mod_events_humidity", "events");
            $lwindspeed = $this->objLanguage->languageText("mod_events_windspeed", "events");
            $lclouds = $this->objLanguage->languageText("mod_events_cloudcover", "events");
            $ldatetime = $this->objLanguage->languageText("mod_events_datetime", "events");
            // format into a table
            $wtable = $this->newObject('htmltable', 'htmlelements');
            $wtable->startRow();
            $wtable->addCell($ltemp);
            $wtable->addCell($weather['temperature']);
            $wtable->endRow();
            $wtable->startRow();
            $wtable->addCell($ldewpoint);
            $wtable->addCell($weather['dewpoint']);
            $wtable->endRow();
            $wtable->startRow();
            $wtable->addCell($lhumidity);
            $wtable->addCell($weather['humidity']);
            $wtable->endRow();
            $wtable->startRow();
            $wtable->addCell($lwindspeed);
            $wtable->addCell($weather['windspeed']);
            $wtable->endRow();
            $wtable->startRow();
            $wtable->addCell($lclouds);
            $wtable->addCell($weather['clouds']);
            $wtable->endRow();
            $wtable->startRow();
            $wtable->addCell($ldatetime);
            $wtable->addCell($weather['datetime']);
            $wtable->endRow();
            $wtable = $wtable->show();
        }
        return $objFeaturebox->show($this->objLanguage->languageText("mod_events_locweather", "events"), $wtable);
    }

    /**
     * Main container function (tabber) box to do the layout for the main template
     *
     * Chisimba tabber interface is used to create tabs that are dynamically switchable.
     *
     * @return string
     */
    public function middleContainer() {
        // get the tabbed box class
        $tabs = $this->getObject('tabber', 'htmlelements');

        $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_recent", "events"), 'content' => $this->getRecentContent(), 'onclick' => ''));
        //$tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_information", "events"), 'content' => $this->getWikipediaContent(), 'onclick' => ''));
        //$tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_today", "events"), 'content' => $this->getTodayContent(), 'onclick' => ''));
        //$tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_popular", "events"), 'content' => $this->getPopularContent(), 'onclick' => ''));
        $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_addevent", "events"), 'content' => $this->addEventContent(), 'onclick' => ''));
        //$tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_nearby", "events"), 'content' => $this->getNearbyContent($this->getParam('radius', 5)), 'onclick' => ''));
        //if($this->objUser->isLoggedIn()) {
        //    $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_friends", "events")." (".$this->friendcount.") ", 'content' => $this->getFriendContent(), 'onclick' => ''));
        //}
        //$tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_saerch", "events"), 'content' => $this->getSearchContent(), 'onclick' => ''));
        //$tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_fb", "events"), 'content' => $this->fbComment(), 'onclick' => ''));
        $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_eventarchive", "events"), 'content' => $this->archiveBox(), 'onclick' => ''));

        return $tabs->show();
    }

    /**
     * Method to get nearby content
     *
     * getNearbyContent will return a list of placenames that are found within a certain radius of the saved latlong coordinates
     * stored in the users cookie variable. The cookie is set when the user chooses their location. Max radius allowed is 300km
     *
     * @param $radius (Required) the radius in Km to search for nearby places.
     */
    public function getNearbyContent($radius) {
        $latlon = $this->objCookie->get('events_latlon');
        $locarr = explode("|", $latlon);
        $list = NULL;
        $this->loadClass('htmlheading', 'htmlelements');
        if($locarr[0] != '' && $locarr != '') {
            $lat = $locarr[0];
            $lon = $locarr[1];
            $nearbyplaces = $this->findNearbyRadius($lat, $lon, $radius);
        }
        else {
            $nearbyplaces[]['name'] = $this->objLanguage->languageText("mod_events_nonearbyplacesfound", "events");
        }
        if(is_array($nearbyplaces) && @$nearbyplaces['name'] != 'radius too large, radius is limited to maximal 300km') {
            foreach($nearbyplaces as $place) {
                $headernp = new htmlheading();
                $headernp->type = 3;
                $headernp->str = $place['name'];
                $list .= $headernp->show()."<br />";
            }
        }
        else {
            $list .= $nearbyplaces['name'];
        }
        return $list;
    }

    /**
     * Get a list of the most popular events in the system
     *
     * The popularity of an event is determined by how many users/friends agree that it is worth attending
     *
     * @return string
     */
    public function getPopularContent() {
        return $this->formatEventIntro(NULL);
    }
    
    public function getSearchContent() {
        $ret = NULL;
        if($this->objUser->isLoggedIn()) {
            // gets to look through private events too
            $ret .= '';
        }
        else {
            // search for events
            $headerinfo = new htmlheading();
            $headerinfo->type = 3;
            $headerinfo->str = $this->objLanguage->languageText("mod_events_headersearchnologin", "events");
            $ret .= $headerinfo->show();
        }
        return $ret;
    }

    /**
     * Container function for the add event functions
     *
     * This method only really checks the sign in state of the user and then displays the appropriate content
     *
     * @return string
     */
    public function addEventContent() {
        $ret = NULL;
        if($this->objUser->isLoggedIn() == FALSE) {
            $objFeaturebox = $this->getObject('featurebox', 'navigation');
            $signinlink = $this->newObject('alertbox', 'htmlelements');
            $signuplink = $this->newObject('alertbox', 'htmlelements');
            $registerlink = $this->newObject('alertbox', 'htmlelements');
            // Make sure to show the sign up link only if registrations are allowed!
            if(strtolower($this->objConfig->getallowSelfRegister()) == 'true') {
                $signuplink = $signuplink->show($this->objLanguage->languageText("mod_events_signup", "events"), $this->uri(array('action' => 'showregister'), 'userregistration'));
            }
            else {
                $signuplink = NULL;
            }
            $signinlink = $signinlink->show($this->objLanguage->languageText("mod_events_signin", "events"), $this->uri(array('action' => 'showsignin')))." ".$this->objLanguage->languageText("mod_events_toaddevents", "events")."<br /> (".$this->objLanguage->languageText("mod_events_youcanusetwitter", "events").")<br />";
            $ret .= $signinlink;
            $ret .= $this->objLanguage->languageText("mod_events_orifyoudonthaveacc", "events").", ".$signuplink;
        }
        else {
            $ret .= $this->addEditEventForm(NULL);
        }

        return $ret;
    }

    /**
     * Grabs Wikipedia content according to lat lon
     *
     * Get relevant (geographically) relevant wikipedia content according to the lat and lon gathered from the cookie data on users machine.
     *
     * @return string
     */
    public function getWikipediaContent() {
        $latlon = $this->objCookie->get('events_latlon');
        $locarr = explode("|", $latlon);
        $ret = NULL;
        $word_away = $this->objLanguage->languageText("mod_events_word_away", "events");
        $moretext = $this->objLanguage->languageText("mod_events_word_more", "events");
        if($locarr[0] != '' && $locarr != '') {
            $lat = $locarr[0];
            $lon = $locarr[1];
            $articles = $this->findNearbyWikipedia($lat, $lon);
        }
        else {
            $articles[0]['title'] = $this->objLanguage->languageText("mod_events_noarticlesfound", "events");
            $articles[0]['summary'] = $this->objLanguage->languageText("mod_events_noarticlesfound", "events");
            $articles[0]['distance'] = 0;
            $articles[0]['wikipediaUrl'] = $this->uri('');
        }
        $wikbox = $this->newObject('featurebox', 'navigation');
        foreach ($articles as $article) {
            if(is_object($article)) {
                $title = $article->title;
                $summary = $article->summary;
                $distance = $article->distance;
                $wikiurl = $article->wikipediaUrl;

                $morelink = $this->newObject('link', 'htmlelements');
                $morelink->href = "http://".$wikiurl;
                $morelink->link = $moretext;
                $morelink->target = "_blank";
                $morelink = $morelink->show();
                $ret .= $wikbox->show($title." "."(".$distance."km ".$word_away.") ".$morelink."...", $summary);
            }
            else {
                $ret .= $this->objLanguage->languageText("mod_events_pleasesetlocation", "events");
            }
        }
        return $ret;
    }
    
    public function markasfavourite($eventid) {
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setIcon('favourite', 'png', 'icons/events/');
        $objIcon->alt = $this->objLanguage->languageText("mod_events_favouritemark", "events");
        $favlink = $this->newObject('link', 'htmlelements');
        $favlink->href = $this->uri(array('action' => 'makefavourite', 'eventid' => $eventid));
        $favlink->link = $objIcon->show();
        
        return $favlink->show();
    }
    
    public function showAttendeesBox($eventdata) {
        $ret = NULL;
        $attcount = 0;
        $attlist = NULL;
        $promoters = $eventdata->promoters;
        $promoters = $this->objUtils->object2array($promoters);
        if(!is_null($promoters) && isset($promoters[0])) {
            $promo = $this->objUtils->object2array($promoters[0]);
            $maxppl = $promo['limitedto'];
            if($promo['canbringothers'] == 'on') {
                $numguests = $promo['numberguests'];
            }
        }
        else {
            $maxppl = $this->objLanguage->languageText("mod_events_nolimit", "events");
        }
        $objFB = $this->newObject('featurebox', 'navigation');
        $attarray = $this->objUtils->object2array($eventdata->attendees);
        if(empty($attarray)) {
            $ret .= "<em>".$this->objLanguage->languageText("mod_events_noattendeesyet", "events")."</em>";
            return $objFB->show($this->objLanguage->languageText("mod_events_attendees", "events"), $ret);
        }
        foreach($attarray as $att) {
            if($att->ans == 'no') {
                continue;
            }
            else {
                $attcount++;
                $attlist .= $this->objUser->getSmallUserImage($att->userid, $this->objUser->username($att->userid))."<br />".$this->objUser->fullName($att->userid)."<br /><hr />";
            }
        }
        $attends = $attcount." / ".$maxppl;
        $ret .= $attends."<br />".$attlist;
        return $objFB->show($this->objLanguage->languageText("mod_events_attendees", "events"), $ret);
    }
    
    public function showFavouritesBox($eventdata) {
    
    }
    
    public function showTicketBox($eventdata) {
        $ret = NULL;
        $ttable = NULL;
        $objFb = $this->newObject('featurebox', 'navigation');
        $tfree = $eventdata->event->ticket_free;
        $tprice = $eventdata->event->ticket_price;
        $ticketurl = $eventdata->event->ticket_url;
        $exturl = $eventdata->event->url;
        if($exturl == 'http://') {
            $exturl = NULL;
        }
        if($ticketurl == 'http://') {
            $ticketurl = NULL;
        }
        if($tfree == NULL && $tprice == NULL && $ticketurl == NULL && $exturl == NULL) {
            $ret .= $objFb->show($this->objLanguage->languageText("mod_events_ticketinfo", "events"), "<em>".$this->objLanguage->languageText("mod_events_noticketinfo", "events")."</em>");
        }
        else {
            if($tfree == 'on') {
                $tprice = $this->objLanguage->languageText("mod_events_ticketsarefree", "events");
            }
            $ttable .= $this->objLanguage->languageText("mod_events_tickurl", "events").": <br />".$this->objWashout->parseText($ticketurl)."<br />";
            $ttable .= $this->objLanguage->languageText("mod_events_ticketprice", "events").": <br />".$tprice."<br />";
            $ttable .= $this->objLanguage->languageText("mod_events_exturl", "events").": <br />".$this->objWashout->parseText($exturl)."<br />";
            
            $ret .= $objFb->show($this->objLanguage->languageText("mod_events_ticketinfo", "events"), $ttable);
        }
        return $ret;
    }
    
    /**
     * Grabs Wikipedia content according to lat lon
     *
     * Get relevant (geographically) relevant wikipedia content according to the lat and lon gathered from the cookie data on users machine.
     *
     * @return string
     */
    public function getWikipediaContentByLatLon($lat, $lon) {
        $ret = NULL;
        $disp = $this->newObject('featurebox', 'navigation');
        $word_away = $this->objLanguage->languageText("mod_events_word_away", "events");
        $moretext = $this->objLanguage->languageText("mod_events_word_more", "events");
        $articles = $this->findNearbyWikipedia($lat, $lon);
        $wikbox = $this->newObject('featurebox', 'navigation');
        foreach ($articles as $article) {
            if(is_object($article)) {
                $title = $article->title;
                $summary = $article->summary;
                $distance = $article->distance;
                $wikiurl = $article->wikipediaUrl;

                $morelink = $this->newObject('link', 'htmlelements');
                $morelink->href = "http://".$wikiurl;
                $morelink->link = $moretext;
                $morelink->target = "_blank";
                $morelink = $morelink->show();
                $ret .= $wikbox->show($title." "."(".$distance."km ".$word_away.") ".$morelink."...", $summary);
            }
            else {
                $ret .= $disp->show("Wikipedia", $this->objLanguage->languageText("mod_events_noarticlesfound", "events"));
            }
        }
        return $disp->show("Wikipedia", $ret);
    }

    /**
     * Grabs temporal data
     *
     * Temoral data for the evens database are formatted and returned for display
     *
     * @return string
     */
    public function getTodayContent() {
        return "todays content";
    }

    public function getCatContent($catid, $number = 20) {
        $events = $this->objDbEvents->eventGetLatestByCat($catid, $number);
        $userid = $this->objUser->userId();
        $today = strtotime(date('Y-m-d'));
        $ret = NULL;
        foreach($events as $event) {
            $startdate = strtotime($event['start_date']);
            // skip personal events, unless you or your friends network are the person
            if($event['personal'] == 'on') {
               if($userid != $event['userid']) {
                   continue;
               }
            }
            // skip events that have already happened. We keep today's events on until tomorrow in case folks are just late
            if($startdate < $today) {
                continue;
            }
            // send the data to a formatting prettifying function
            $ret .= $this->formatEventSummary($event);
        }
        // if ret is still null, there are no events... sigh
        if($ret == NULL) {
            $this->loadClass('htmlheading', 'htmlelements');
            $headerno = new htmlheading();
            $headerno->type = 1;
            $headerno->str = $this->objLanguage->languageText("mod_events_nothingtoshow", "events");
            $ret .= $headerno->show();
        }
        return $ret;
    }
    
    /**
     * Another temporal function, this time on a broader scale
     *
     * @return string
     */
    public function getRecentContent() {
        $events = $this->objDbEvents->eventGetLatest(10);
        $userid = $this->objUser->userId();
        $today = strtotime(date('Y-m-d'));
        $ret = NULL;
        foreach($events as $event) {
            $startdate = strtotime($event['start_date']);
            // skip personal events, unless you or your friends network are the person
            if($event['personal'] == 'on') {
               if($userid != $event['userid']) {
                   continue;
               }
            }
            // skip events that have already happened. We keep today's events on until tomorrow in case folks are just late
            if($startdate < $today) {
                continue;
            }
            // send the data to a formatting prettifying function
            $ret .= $this->formatEventSummary($event);
        }
        // if ret is still null, there are no events... sigh
        if($ret == NULL) {
            $this->loadClass('htmlheading', 'htmlelements');
            $headerno = new htmlheading();
            $headerno->type = 1;
            $headerno->str = $this->objLanguage->languageText("mod_events_nothingtoshow", "events");
            $ret .= $headerno->show();
        }
        return $ret;
    }
    
    /**
     * Another temporal function, this time on a broader scale
     *
     * @return string
     */
    public function getAllRecentContent() {
        $events = $this->objDbEvents->eventGetLatest(10);
        $userid = $this->objUser->userId();
        $today = strtotime(date('Y-m-d'));
        $ret = NULL;
        foreach($events as $event) {
            $startdate = strtotime($event['start_date']);
            // skip personal events, unless you or your friends network are the person
            if($event['personal'] == 'on') {
               if($userid != $event['userid']) {
                   continue;
               }
            }
            // send the data to a formatting prettifying function
            $ret .= $this->formatEventSummary($event);
        }
        // if ret is still null, there are no events... sigh
        if($ret == NULL) {
            $this->loadClass('htmlheading', 'htmlelements');
            $headerno = new htmlheading();
            $headerno->type = 1;
            $headerno->str = $this->objLanguage->languageText("mod_events_nothingtoshow", "events");
            $ret .= $headerno->show();
        }
        return $ret;
    }
    
    public function formatEventSummary($event) {
        $startdate = strtotime($event['start_date']);
        $datedisplay = '<div class="post-date-bg">';
        $month = date('M', $startdate);
        $datedisplay .= '<span>'.$month.'</span><br />';
        $day = date('d', $startdate);
        $year = date('Y', $startdate);
        $datedisplay .= '<span class="date">'.$day.'</span><br /><span>'.$year.'</span></div>';

        $objFb = $this->newObject('featurebox', 'navigation');
        $etbl = $this->newObject('htmltable', 'htmlelements');
        $etbl->callpadding = 10;
        $etbl->cellspacing = 5;
        $catinfo = $this->objDbEvents->categoryGetDetails($event['category_id']);
        $etbl->startRow();
        $etbl->addCell($datedisplay."<br />".$this->markasfavourite($event['id']), '15%', "top");
        $etbl->addCell($this->goYesNo($event['id']),'15%', "top");
        $etbl->addCell($catinfo[0]['cat_name'], '20%', "top");
        $etbl->addCell($this->objUtils->truncateDescription($event['id'], $event['description'], 200, ".", "..."), '50%', "top");
        $etbl->endRow();
        // check if the user is the logged in user and set up the edit/delete stuff
        if($this->objUser->userId() == $event['userid']) {
            $this->objIcon = $this->getObject('geticon', 'htmlelements');
            $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                'action' => 'eventedit',
                'id' => $event['id'],
                'module' => 'events'
            )));
            $delIcon = $this->objIcon->getDeleteIconWithConfirm($event['id'], array(
                'module' => 'events',
                'action' => 'eventdelete',
                'id' => $event['id']
            ) , 'events');
            return $objFb->show($event['name']." ".$edIcon." ".$delIcon, $etbl->show());
        }
        else {
            return $objFb->show($event['name'], $etbl->show());
        }
    }
    
    public function formatEventFull($event) {
        $allevent = $event;
        // make the object an array
        $event = $this->objUtils->object2array($allevent->event);
        $venue = $this->objUtils->object2array($allevent->venue); //$this->objDbEvents->venueGetInfo($event['venue_id']);
        // $venue = $venue[0];
        $startdate = strtotime($event['start_date']);
        $datedisplay = '<div class="post-date-bg">';
        $month = date('M', $startdate);
        $datedisplay .= '<span>'.$month.'</span><br />';
        $day = date('d', $startdate);
        $year = date('Y', $startdate);
        $datedisplay .= '<span class="date">'.$day.'</span><br /><span>'.$year.'</span></div>';
        
        // tweets
        $hashtag = $allevent->hashtag;
        if(!empty($hashtag)) {
            $eventhashtag = $hashtag->mediatag;
            $tweets = $allevent->tweets->twittersearch;
        }
        else {
            $eventhashtag = NULL;
            $tweets = array();
        }
        // twitoaster conversation
        // $twits = $allevent->tweets->twitoaster; 
        // $twitoaster = $this->objSocial->renderTwitoaster($twits);
        
        // an alertbox to hold *very* long text i.e. more than 500 chars long
        $morelink = '';
        $descrip = $this->objWashout->parseText($event['description']); //$this->objUtils->truncateBigDescription($event['id'], $this->objWashout->parseText($event['description']), 1000, " ", "..." );
        $objFb = $this->newObject('featurebox', 'navigation');
        $etbl = $this->newObject('htmltable', 'htmlelements');
        $etbl->callpadding = 10;
        $etbl->cellspacing = 5;
        $catinfo = $this->objDbEvents->categoryGetDetails($event['category_id']);
        $etbl->startRow();
        $etbl->addCell($datedisplay.$this->markasfavourite($event['id']).$this->goYesNo($event['id'])."<br />".$catinfo[0]['cat_name']."<br /> (".$catinfo[0]['cat_desc'].") <br /><br />".$descrip, '50%', "top");
        $etbl->addCell($this->viewLocMap($venue['geolat'], $venue['geolon'], 10), '50%', "top");
        $etbl->endRow();
        //$etbl->startRow();
        //$etbl->addCell($this->showPlaceWeatherBox($venue['geolat'], $venue['geolon'])."<br />".$this->objSocial->renderTweets($this->objUtils->object2array($tweets), $eventhashtag));
        //$etbl->addCell($this->getWikipediaContentByLatLon($venue['geolat'], $venue['geolon']));
        //$etbl->endRow();
        // check if the user is the logged in user and set up the edit/delete stuff
        if($this->objUser->userId() == $event['userid']) {
            $this->objIcon = $this->getObject('geticon', 'htmlelements');
            $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                'action' => 'eventedit',
                'id' => $event['id'],
                'module' => 'events'
            )));
            $delIcon = $this->objIcon->getDeleteIconWithConfirm($event['id'], array(
                'module' => 'events',
                'action' => 'eventdelete',
                'id' => $event['id']
            ) , 'events');
            return $objFb->show($event['name']." ".$edIcon." ".$delIcon, $etbl->show());
        }
        else {
            return $objFb->show($event['name'], $etbl->show());
        }
    }
    
    public function goYesNo($eventid) {
        $userid = $this->objUser->userId();
        // get the event details 
        $event = $this->objDbEvents->eventGet($eventid);
        // check if the user has already RSVP'd to this event
        $rsvp = $this->objDbEvents->userCheckAttend($userid, $eventid);
        $objIcon = $this->newObject('geticon', 'htmlelements');
        if($rsvp == FALSE) {
            // first check if a private event
            if($event['personal'] == 'on') {
                $objIcon->setIcon('locked_small', 'png', 'icons/events/');
                $objIcon->alt = $this->objLanguage->languageText("mod_events_privateevent", "events");
                $lock = $objIcon->show();
                $objIcon->setIcon('getinvited', 'png', 'icons/events/');
                $objIcon->alt = $this->objLanguage->languageText("mod_events_getinvited", "events");
                $getinvited = $objIcon->show();
                $gilink = $this->newObject('link', 'htmlelements');
                $gilink->href = $this->uri(array('action' => 'rsvpgetinvite', 'userid' => $userid, 'ans' => 'inv', 'eventid' => $eventid));
                $gilink->link = $getinvited; // $this->objLanguage->languageText("mod_events_getinvited", "events");
                $gilink = $gilink->show();
                return $lock.$gilink; //"<br />".$this->objLanguage->languageText("mod_events_privateevent", "events")."<br />".$gilink;
            }
            else {
                $objIcon->setIcon('unlocked_small', 'png', 'icons/events/');
                $objIcon->alt = $this->objLanguage->languageText("mod_events_publicevent", "events");
                // I will be attending this event!
                $yeslink = $this->newObject('link', 'htmlelements');
                $yeslink->href = $this->uri(array('action' => 'rsvp', 'userid' => $userid, 'ans' => 'yes', 'eventid' => $eventid));
                $yeslink->link = $this->objLanguage->languageText("mod_events_yesattending", "events");
                $yeslink = $yeslink->show();
                // and the not going link
                $nolink = $this->newObject('link', 'htmlelements');
                $nolink->href = $this->uri(array('action' => 'rsvp', 'userid' => $userid, 'ans' => 'no', 'eventid' => $eventid));
                $nolink->link = $this->objLanguage->languageText("mod_events_notattending", "events");
                $nolink = $nolink->show();
                if($this->objUser->isLoggedIn()) {
                    return $objIcon->show()."<br />".$this->objLanguage->languageText("mod_events_publicevent", "events")."<br />".$yeslink." | ".$nolink;
                }
                else {
                    $signinlink = $this->newObject('alertbox', 'htmlelements');
                    $signinlink = $signinlink->show($this->objLanguage->languageText("mod_events_signin", "events"), $this->uri(array('action' => 'showsignin')))." ".$this->objLanguage->languageText("mod_events_needsignintorsvp", "events");
                    return $objIcon->show().$signinlink; //."<br />".$this->objLanguage->languageText("mod_events_publicevent", "events")."<br />"
                }
            }
        }
        else {
            if($event['personal'] == 'on') {
                $objIcon->setIcon('locked_small', 'png', 'icons/events/');
                $objIcon->alt = $this->objLanguage->languageText("mod_events_privateevent", "events");
            }
            else {
                $objIcon->setIcon('unlocked_small', 'png', 'icons/events/');
                $objIcon->alt = $this->objLanguage->languageText("mod_events_publicevent", "events");
            }
            $privacy = $objIcon->show();
            // user has RSVP'ed, so give an opportunity to change minds?
            $objIcon->setIcon('changemind', 'png', 'icons/events/');
            $objIcon->alt = $this->objLanguage->languageText("mod_events_cmattending", "events");
            $cmicon = $objIcon->show();
            $cmlink = $this->newObject('link', 'htmlelements');
            $cmlink->href = $this->uri(array('action' => 'rsvp', 'userid' => $userid, 'ans' => 'swap', 'eventid' => $eventid));
            $cmlink->link = $cmicon; //$this->objLanguage->languageText("mod_events_cmattending", "events");
            $cmlink = $cmlink->show();
            if($rsvp[0]['ans'] == 'yes') {
                $objIcon->setIcon('yesattending', 'png', 'icons/events/');
                $objIcon->alt = $this->objLanguage->languageText("mod_events_yesattending", "events");
                $switcher = $objIcon->show();
            }
            else {
                $objIcon->setIcon('notattending', 'png', 'icons/events/');
                $objIcon->alt = $this->objLanguage->languageText("mod_events_notattending", "events");
                $switcher = $objIcon->show();
                //$switcher = $this->objLanguage->languageText("mod_events_notattending", "events");
            }
            
            return $privacy.$switcher.$cmlink;
        }
        
    }

    /**
     * Grabs content from friends
     *
     * @return string
     */
    public function getFriendContent() {
        $foaf = $this->foafProfile->foaf;
        $ret = NULL;
        //var_dump($foaf); die();
        if(is_array($foaf) && !empty($foaf) && array_key_exists('knows', $foaf)) {
            $ret .= $this->objSocial->searchFriendsForm();
            $friends = $foaf['knows'];
            foreach($friends as $pal) {
                // var_dump($pal); die();
                $objFb = $this->newObject('featurebox', 'navigation');
                $ret .= $objFb->show($pal['name'], "<img src='".$pal['img'][0]."' />");
            }
        }
        if($ret == NULL) {
            // there is no social stuff at all to display and the tabber needs a string to carry on
            $headersoc = new htmlheading();
            $headersoc->type = 2;
            $headersoc->str = $this->objLanguage->languageText("mod_events_nosocialfound", "events");
            $ret .= $headersoc->show();
            // display a form to add a friend or suggest some friends to the user
            $ret .= $this->objSocial->searchFriendsForm();
        }
        return $ret;
    }

    public function picUploadForm() {
        $ret = NULL;
        $this->loadClass('form', 'htmlelements');
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
        $objSelectFile->restrictFileList = array('jpg', 'jpeg', 'png', 'gif');
        $objSelectFile->name = 'pic';
        $form = new form ('uploadpic', $this->uri(array('action'=>'uploadpic'), 'events'));
        $form->addToForm($objSelectFile->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_userregistration_uploadpic", "userregistration"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();
        return $ret;
    }
    /**
     * Form used to invite friends to the site via mail invite
     *
     * @return string
     */
    public function showInviteForm() {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'events', 'Required').'</span>';
        $headerinv = new htmlheading();
        $headerinv->type = 1;
        $headerinv->str = $this->objLanguage->languageText('phrase_invitemate', 'userregistration').' '.$this->objConfig->getSitename();
        $ret = NULL;
        $ret .= $headerinv->show();
        // start the form
        $form = new form ('invite', $this->uri(array('action'=>'sendinvite'), 'userregistration'));
        // add some rules
        $form->addRule('friend_firstname', $this->objLanguage->languageText("mod_userregistration_needfriendname", "userregistration"), 'required');
        $form->addRule('friend_email', $this->objLanguage->languageText("mod_userregistration_needfriendemail", "userregistration"), 'email');
        // friend name
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $friendname = new textinput('friend_firstname');
        $friendnameLabel = new label($this->objLanguage->languageText('friendname', 'userregistration').'&nbsp;', 'input_friendname');
        $table->addCell($friendnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($friendname->show().$required);
        $table->endRow();
        // surname
        $table->startRow();
        $friendsurname = new textinput('friend_surname');
        $friendsurnameLabel = new label($this->objLanguage->languageText('friendsurname', 'userregistration').'&nbsp;', 'input_friendsurname');
        $table->addCell($friendsurnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($friendsurname->show());
        $table->endRow();
        // email
        $table->startRow();
        $friendemail = new textinput('friend_email');
        $friendemailLabel = new label($this->objLanguage->languageText('friendemail', 'userregistration').'&nbsp;', 'input_friendemail');
        $table->addCell($friendemailLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($friendemail->show().$required);
        $table->endRow();
        // message to include to mate
        $defmsg = $this->objLanguage->languageText("mod_userregistration_wordhi", "userregistration").", <br /><br /> ".$this->objUser->fullname()." (".$this->objUser->username().") ".$this->objLanguage->languageText("mod_userregistration_hasinvited", "userregistration")." ".$this->objConfig->getSiteName()."! <br /><br /> ".$this->objLanguage->languageText("mod_userregistration_pleaseclick", "userregistration")."<br />";
        $table->startRow();
        $friendmsg = $this->newObject('htmlarea', 'htmlelements');
        $friendmsg->name = 'friend_msg';
        $friendmsg->value = $defmsg;
        $friendmsg->width ='50%';
        $friendmsgLabel = new label($this->objLanguage->languageText('friendmessage', 'userregistration').'&nbsp;', 'input_friendmsg');
        $table->addCell($friendmsgLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $friendmsg->toolbarSet = 'simple';
        $table->addCell($friendmsg->show());
        $table->endRow();
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_userregistration_completeinvite", "userregistration"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }

    /**
     * Form used to add or edit an event
     *
     * Form fields generated to gather all the information required to create a new event in the system
     *
     * @param array $editparams (optional)
     * @return string
     */
    public function addEditEventForm($editparams = NULL) {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('label', 'htmlelements');

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $this->objHead = $this->newObject('htmlheading', 'htmlelements');
        $this->objEHead = $this->newObject('htmlheading', 'htmlelements');
        $mtable = $this->newObject('htmltable', 'htmlelements');
        $ftable = $this->newObject('htmltable', 'htmlelements');
        $ret = NULL;
        if(isset($editparams)) {
            $form = new form ('eventupdate', $this->uri(array('action'=>'eventupdate')));
        }
        else {
            $form = new form ('eventadd', $this->uri(array('action'=>'eventadd')));
        }
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'events', 'Required').'</span>';
        $mtable->cellpadding = 3;
        // a heading
        $this->objEHead->type = 2;
        $this->objEHead->str = $this->objLanguage->languageText("mod_events_addeventheader", "events");

        // event name
        $enamelabel = new label($this->objLanguage->languageText("mod_events_eventname", "events") . ':', 'input_ename');
        $ename = new textinput('eventname', NULL, NULL);
        if(isset($editparams['name'])) {
            $ename->setValue($editparams['name']);
        }
        $ftable->startRow();
        $ftable->addCell($enamelabel->show().$required);
        $ftable->endRow();
        $ftable->startRow();
        $ftable->addCell($ename->show());
        $ftable->endRow();
        // event category dropdown
        $ecatlabel = new label($this->objLanguage->languageText("mod_events_eventcategory", "events") . ':', 'input_ecat');
        $ecat = new dropdown('eventcategory', NULL, NULL);
        $cats = $this->objDbEvents->categoryGetList();
        $ecat->addOption();
        foreach($cats as $cat) {
            $ecat->addOption($cat['id'], $cat['cat_name']." (".$cat['cat_desc'].")");
        }
        if(isset($editparams['category_id'])) {
            $ecat->setSelected($editparams['category_id']);
        }
        $ftable->startRow();
        $ftable->addCell($ecatlabel->show().$required);
        $ftable->endRow();
        $ftable->startRow();
        $ftable->addCell($ecat->show());
        $ftable->endRow();
        // venue name
        $vnamelabel = new label($this->objLanguage->languageText("mod_events_venuename", "events") . ':', 'input_venuename');
        $vname = new textinput('venuename', NULL, NULL);
        if(isset($editparams['venuename'])) {
            $vname->setValue($editparams['venuename']);
        }
        $ftable->startRow();
        $ftable->addCell($vnamelabel->show().$required);
        $ftable->endRow();
        $ftable->startRow();
        $ftable->addCell($vname->show());
        $ftable->endRow();
        // start date and time
        $objsDatepick = $this->newObject('datepickajax', 'popupcalendar');
        if(isset($editparams['start_date'])) {
            $sdatepick = $objsDatepick->show('startdatetime', 'yes', 'yes', $editparams['start_date']." ".$editparams['start_time']);
        }
        else {
            $sdatepick = $objsDatepick->show('startdatetime', 'yes', 'yes', NULL);
        }
        $esdtlabel = new label($this->objLanguage->languageText("mod_events_startdatetime", "events") . ':', 'input_startdatetime');
        $ftable->startRow();
        $ftable->addCell($esdtlabel->show());
        $ftable->endRow();
        $ftable->startRow();
        $ftable->addCell($sdatepick.$required);
        $ftable->endRow();
        // end date and time
        $objeDatepick = $this->newObject('datepickajax', 'popupcalendar');
        if(isset($editparams['end_date'])) {
            $edatepick = $objeDatepick->show('enddatetime', 'yes', 'yes', $editparams['end_date']." ".$editparams['end_time']);
        }
        else {
            $edatepick = $objeDatepick->show('enddatetime', 'yes', 'yes', NULL);
        }
        $eedtlabel = new label($this->objLanguage->languageText("mod_events_enddatetime", "events") . ':', 'input_enddatetime');
        $ftable->startRow();
        $ftable->addCell($eedtlabel->show());
        $ftable->endRow();
        $ftable->startRow();
        $ftable->addCell($edatepick);
        $ftable->endRow();

        // put the event details into a fieldset
        $edfieldset = $this->newObject('fieldset', 'htmlelements');
        $edfieldset->legend = $this->objLanguage->languageText("mod_events_eventdetails", "events");;
        $edfieldset->contents = $ftable->show();

        $ftable2 = $this->newObject('htmltable', 'htmlelements');
        // event url
        $eurllabel = new label($this->objLanguage->languageText("mod_events_eventurl", "events") . ':', 'input_eurl');
        $eurl = new textinput('eventurl', NULL, NULL);
        if(isset($editparams['url'])) {
            $eurl->setValue($editparams['url']);
        }
        else {
            $eurl->setValue("http://");
        }
        $ftable2->startRow();
        $ftable2->addCell($eurllabel->show());
        $ftable2->endRow();
        $ftable2->startRow();
        $ftable2->addCell($eurl->show());
        $ftable2->endRow();
        // ticket url
        $turllabel = new label($this->objLanguage->languageText("mod_events_ticketurl", "events") . ':', 'input_turl');
        $turl = new textinput('ticketurl', NULL, NULL);
        if(isset($editparams['ticket_url'])) {
            $turl->setValue($editparams['ticket_url']);
        }
        else {
            $turl->setValue("http://");
        }
        $ftable2->startRow();
        $ftable2->addCell($turllabel->show());
        $ftable2->endRow();
        $ftable2->startRow();
        $ftable2->addCell($turl->show());
        $ftable2->endRow();
        // ticket price
        $tplabel = new label($this->objLanguage->languageText("mod_events_ticketprice", "events") . ':', 'input_ticketprice');
        $tflabel = new label($this->objLanguage->languageText("mod_events_ticketfree", "events") . ':', 'input_ticketfree');
        $ticketprice = new textinput('ticketprice', NULL, NULL);
        if(isset($editparams['ticket_price'])) {
            $ticketprice->setValue($editparams['ticket_price']);
        }
        if(isset($editparams['ticket_free'])) {
            $ticketfree = new checkbox('ticketfree', NULL, TRUE);
        }
        else {
            $ticketfree = new checkbox('ticketfree', NULL, NULL);
        }
        $ftable2->startRow();
        $ftable2->addCell($tflabel->show());
        $ftable2->endRow();
        $ftable2->startRow();
        $ftable2->addCell($ticketfree->show()." ".$this->objLanguage->languageText("mod_events_word_or", "events"));
        $ftable2->endRow();
        $ftable2->startRow();
        $ftable2->addCell($tplabel->show());
        $ftable2->endRow();
        $ftable2->startRow();
        $ftable2->addCell($ticketprice->show());
        $ftable2->endRow();

         // put the event details into a fieldset
        $edfieldset2 = $this->newObject('fieldset', 'htmlelements');
        $edfieldset2->legend = $this->objLanguage->languageText("mod_events_eventurlandtickets", "events");;
        $edfieldset2->contents = $ftable2->show();

        // event description textarea
        $dtable = $this->newObject('htmltable', 'htmlelements');
        $edesclabel = new label($this->objLanguage->languageText("mod_events_eventdescription", "events") . ':', 'input_description');
        $description = $this->newObject('htmlarea', 'htmlelements');
        $description->setName('description');
        $description->setRows = 5;
        if(isset($editparams['description'])) {
            $description->setContent($editparams['description']);
        }
        else {
            $description->setContent($this->objLanguage->languageText("mod_events_briefeventdescription", "events"));
        }
        $description->setBasicToolBar();
        $dtable->startRow();
        $dtable->addCell($edesclabel->show().$required);
        $dtable->endRow();
        $dtable->startRow();
        $dtable->addCell($description->show());
        $dtable->endRow();

        // put the event details into a fieldset
        $dfieldset = $this->newObject('fieldset', 'htmlelements');
        $dfieldset->legend = $this->objLanguage->languageText("mod_events_eventdescription", "events");;
        $dfieldset->contents = $dtable->show();

        // is this a personal/private event?
        $ftable3 = $this->newObject('htmltable','htmlelements');
        $personallabel = new label($this->objLanguage->languageText("mod_events_personal", "events") . ':', 'input_personal');
        if(isset($editparams['personal'])) {
            $personal = new checkbox('personal', NULL, TRUE);
        }
        else {
            $personal = new checkbox('personal', NULL, NULL);
        }
        $ftable3->startRow();
        $ftable3->addCell($personallabel->show());
        $ftable3->endRow();
        $ftable3->startRow();
        $ftable3->addCell($personal->show());
        $ftable3->endRow();
        // tags
        $taglabel = new label($this->objLanguage->languageText("mod_events_eventtags", "events") . ':', 'input_tags');
        $tags = new textinput('tags', NULL, NULL);
        $t = $this->objDbEvents->eventGetTags($editparams['id']);
        if($t != '') {
            $tags->setValue($t);
        }
        $ftable3->startRow();
        $ftable3->addCell($taglabel->show());
        $ftable3->endRow();
        $ftable3->startRow();
        $ftable3->addCell($tags->show());
        $ftable3->endRow();

        // self promotional event?
        $canbringotherslabel = new label($this->objLanguage->languageText("mod_events_canbringothers", "events") . ':', 'input_canbringothers');
        $yestheycanlabel = $this->objLanguage->languageText("mod_events_yestheycan", "events");
        $wordguests = $this->objLanguage->languageText("mod_events_word_guests", "events");
        $limitattendeeslabel = new label($this->objLanguage->languageText("mod_events_limitattendees", "events") . ':', 'input_limitattendees');
        $unlimitedlabel = new label($this->objLanguage->languageText("mod_events_unlimited", "events") . ':', 'input_unlimited');
        $howmanylabel = new label($this->objLanguage->languageText("mod_events_howmany", "events") . ':', 'input_howmany');
        $howmany = new textinput('howmany', NULL, NULL);
        $yestheycan = new dropdown('yestheycan', NULL, NULL);

        if(isset($editparams)) {
            $promo = $this->objDbEvents->getEventPromo($editparams['id']);
            if(!empty($promo)) {
                $promo = $promo[0];
                if($promo['canbringothers'] == 'on') {
                    $canbringothers = new checkbox('canbringothers', NULL, TRUE);
                }
                if($promo['canbringothers'] == 'on') {
                    $yestheycan->setSelected($promo['numberguests']);
                }
                $howmany->setValue($promo['limitedto']);
            }
            else {
                $canbringothers = new checkbox('canbringothers', NULL, NULL);
            }
        }
        else {
            $canbringothers = new checkbox('canbringothers', NULL, NULL);
        }
        $i = 1;
        $yestheycan->addOption();
        while($i <= 20) {
            $yestheycan->addOption($i, $i);
            $i++;
        }
        $sptable = $this->newObject('htmltable','htmlelements');
        $sptable->startRow();
        $sptable->addCell($canbringotherslabel->show());
        $sptable->endRow();
        $sptable->startRow();
        $sptable->addCell($canbringothers->show()." ".$yestheycanlabel." ".$yestheycan->show()." ".$wordguests);
        $sptable->endRow();
        $sptable->startRow();
        $sptable->addCell($limitattendeeslabel->show());
        $sptable->endRow();
        $sptable->startRow();
        $sptable->addCell($howmanylabel->show()." ".$howmany->show());
        $sptable->endRow();

        $spfieldset = $this->newObject('fieldset', 'htmlelements');
        $spfieldset->legend = $this->objLanguage->languageText("mod_events_organizer", "events");
        $spfieldset->contents = $sptable->show();

        $selfpromolabel = new label($this->objLanguage->languageText("mod_events_selfpromo", "events") . ':', 'input_selfpromotion');
        $selfpromo = new checkbox('selfpromotion', NULL, NULL);
        $selfpromo->extra = 'onclick="document.getElementById(\'info\').style.visibility = this.checked ? \'visible\' : \'hidden\'"';
        // organizer fields
        $ftable3->startRow();
        $ftable3->addCell($selfpromolabel->show()." ".$selfpromo->show().'<div id="info" style="visibility:hidden">'.$spfieldset->show().'</div>');
        $ftable3->endRow();

        // put the event details into a fieldset
        $edfieldset3 = $this->newObject('fieldset', 'htmlelements');
        $edfieldset3->legend = $this->objLanguage->languageText("mod_events_organizer", "events");;
        $edfieldset3->contents = $ftable3->show();
        
        if(isset($editparams)) {
            $this->loadClass('hiddeninput', 'htmlelements');
            $eventidinput = new hiddeninput('input_eventid', $editparams['id']);
            $eventidinput = $eventidinput->show();
        }
        else {
            $eventidinput = NULL;
        }

        // the rules
        $form->addRule('eventname', $this->objLanguage->languageText("mod_events_needeventname", "events"), 'required');
        $form->addRule('geotag', $this->objLanguage->languageText("mod_events_needlocation", "events"), 'required');
        $form->addRule('eventcategory', $this->objLanguage->languageText("mod_events_needcategory", "events"), 'required');
        $form->addRule('venuname', $this->objLanguage->languageText("mod_events_needvenue", "events"), 'required');
        $form->addRule('sdatepick', $this->objLanguage->languageText("mod_events_needstartdate", "events"), 'required');

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = '';
        $fieldset->contents = /*$mfieldset->show().*/$edfieldset->show().$edfieldset2->show().$dfieldset->show().$edfieldset3->show().$eventidinput;
        if(isset($editparams)) {
            $button = new button ('submitform', $this->objLanguage->languageText("mod_events_editevent", "events"));
        }
        else {
            $button = new button ('submitform', $this->objLanguage->languageText("mod_events_addevent", "events"));
        }
        $button->setToSubmit();
        $form->addToForm($fieldset->show().'<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }

    /**
     * Method used to set geolocation coordinates
     *
     * Users are able to set geographic coordinates by either completing a text input or clicking on a map
     *
     * @param array $editparams
     * @param boolean $eventform
     * @return string
     */
    public function geoLocationForm($editparams = NULL, $eventform = FALSE) {
        $this->loadClass('form', 'htmlelements');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $ret = NULL;
        $lat = 0;
        $lon = 0;
        $zoom = 2;
        $currLocation = $this->objCookie->get('events_latlon');
        $currloc = explode("|", $currLocation);
        if(!empty($currloc) && isset($currloc[0]) && isset($currloc[1])) {
            $lat = $currloc[0];
            $lon = $currloc[1];
            $zoom = 10;
        } 
        if($this->objModules->checkIfRegistered('simplemap') && $this->objModules->checkIfRegistered('georss'))
        {
            $form = new form ('geoloc', $this->uri(array('action'=>'setlocation')));
            $this->loadClass('label', 'htmlelements');
            $this->objHead = $this->getObject('htmlheading', 'htmlelements');
            $this->objHead->type = 3;
            $this->objHead->str = $this->objLanguage->languageText("mod_events_geoposition", "events");
            $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
            $css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: white;
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
            map = new OpenLayers.Map( 'map' , { controls: [] , 'numZoomLevels':20, projection: new OpenLayers.Projection(\"EPSG:900913\"), displayProjection: new OpenLayers.Projection(\"EPSG:4326\") });
            var normal = new OpenLayers.Layer.Google( \"Google Map\" , {type: G_NORMAL_MAP, 'maxZoomLevel':18} );
            var hybrid = new OpenLayers.Layer.Google( \"Google Hybrid Map\" , {type: G_HYBRID_MAP, 'maxZoomLevel':18} );
            
            map.addLayers([normal, hybrid]);

            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl( new OpenLayers.Control.MouseDefaults() );
            map.addControl( new OpenLayers.Control.LayerSwitcher() );
            map.addControl( new OpenLayers.Control.PanZoomBar() );

            map.setCenter(new OpenLayers.LonLat($lon,$lat), $zoom);

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
            $ptable = $this->newObject('htmltable', 'htmlelements');
            $ptable->cellpadding = 3;
            // a heading
            $ptable->startRow();
            //$ptable->addCell('');
            $ptable->addCell($this->objHead->show()); // , '100%', $valign="top", 'center', null, 'colspan=2','0');
            $ptable->endRow();
            // and now the map
            $ptable->startRow();
            $gtlabel = new label($this->objLanguage->languageText("mod_events_geoposition", "events") . ':', 'input_geotags');
            $gtags = '<div id="map"></div>';
            $geotags = new textinput('geotag', NULL, NULL, '100%');
            if (isset($editparams['geolat']) && isset($editparams['geolon'])) {
                $geotags->setValue($editparams['geolat'].", ".$editparams['geolon']);
            }
            //$ptable->addCell($gtlabel->show());
            $ptable->addCell($gtags.$geotags->show());
            $ptable->endRow();

            $fieldset = $this->newObject('fieldset', 'htmlelements');
            $fieldset->legend = '';
            $fieldset->contents = $ptable->show();
            $button = new button ('submitform', $this->objLanguage->languageText("mod_events_setlocation", "events"));
            $button->setToSubmit();
            $form->addToForm($fieldset->show().'<p align="center"><br />'.$button->show().'</p>');
            $ret .= $form->show();
        }
        else {
            $ret .= "Map cannot be shown";
        }

        return $ret;
    }

    /**
     * Method to find nearby place names from a geo coordinate
     *
     * This method returns a list of PLACE NAMES, not coordinates from a given latlon pair
     *
     * @param float $lat
     * @param float $lon
     * @return array
     */
    public function findNearby($lat, $lon) {
        $url = "http://ws.geonames.org/findNearbyPlaceNameJSON?lat=$lat&lng=$lon";
        $json = $this->objCurl->exec($url);
        $objLoc = json_decode($json);
        if(is_object($objLoc) && !empty($objLoc->geonames)) {
            $locarr = $objLoc->geonames[0];
            return $locarr;
        }
        else {
            return NULL;
        }
    }

    /**
     * Grabs placenames of nearby places in a given radius
     *
     * @param float $lat
     * @param float $lon
     * @param integer $radius
     * @return array
     */
    public function findNearbyRadius($lat, $lon, $radius = 5) {
        $locs = NULL;
        $url = "http://ws.geonames.org/findNearbyPlaceNameJSON?lat=$lat&lng=$lon&radius=$radius";
        $json = $this->objCurl->exec($url);
        $objLoc = json_decode($json);
        if(isset($objLoc->status->message)) {
            return $locs[] = array('name' => $objLoc->status->message, 'lat' => '', 'lon' => '', 'geonameid' => '', 'countryname' => '', 'countrycode' => '');
        }
        elseif(is_object($objLoc)) {
            // get all the places and lets see whats potting
            $locarr = $objLoc->geonames;
            foreach($locarr as $location) {
                $locs[] = array('name' => $location->name, 'lat' => $location->lat, 'lon' => $location->lng, 'geonameid' => $location->geonameId, 'countryname' => $location->countryName, 'countrycode' => $location->countryCode);
            }
        }
        else {
            $locs = NULL;
        }
        return $locs;
    }

    /**
     * Grab the local weather from a given place determined by a latlon key pair
     *
     * @param float $lat
     * @param float $lon
     * @return string
     */
    public function findNearbyWeather($lat, $lon) {
        $url = "http://ws.geonames.org/findNearByWeatherJSON?lat=$lat&lng=$lon";
        if(file_exists(md5($url))) {
            if(time() - filemtime(md5($url)) > 3600) {
                $json = $this->objCurl->exec($url);
                $tofile = serialize($json);
                file_put_contents(md5($url), $tofile);
            }
            else {
                $json = unserialize(file_get_contents(md5($url)));
            }
        }
        else {
            // the cache file does not exist!
            $json = $this->objCurl->exec($url);
            $tofile = serialize($json);
            file_put_contents(md5($url), $tofile);
        }
        $objLocWeather = json_decode($json);

        if(isset($objLocWeather->status->message)) {
            return array();
        }
        elseif(is_object($objLocWeather) && is_object($objLocWeather->weatherObservation)) {
            $objLocWeather = $objLocWeather->weatherObservation;
            $temp = $objLocWeather->temperature;
            $dp = $objLocWeather->dewPoint;
            $hum =  $objLocWeather->humidity;
            $ws = $objLocWeather->windSpeed;
            $clouds = $objLocWeather->clouds;
            $datetime = $objLocWeather->datetime;
            $weather = array('temperature' => $temp, 'dewpoint' => $dp, 'humidity' => $hum, 'windspeed' => $ws, 'clouds' => $clouds, 'datetime' => $datetime);
        }
        else {
            $weather = array();
        }
        return $weather;
    }

    /**
     * Utility function to fetch nearby wikipedia content
     *
     * @param float $lat
     * @param float $lon
     * @return string
     */
    public function findNearbyWikipedia($lat, $lon) {
        $url = "http://ws.geonames.org/findNearbyWikipediaJSON?lat=$lat&lng=$lon";
        if(file_exists(md5($url))) {
            // refresh the wikipedia data once a week, coz surely to goodness it can't be so horribly out of date? Surely?
            if(time() - filemtime(md5($url)) > 604800) {
                $json = $this->objCurl->exec($url);
                $tofile = serialize($json);
                file_put_contents(md5($url), $tofile);
            }
            else {
                $json = unserialize(file_get_contents(md5($url)));
            }
        }
        else {
            // the cache file does not exist!
            $json = $this->objCurl->exec($url);
            $tofile = serialize($json);
            file_put_contents(md5($url), $tofile);
        }
        // We have the data, now lets do something with it.
        $objWiki = json_decode($json);
        if(isset($objLocWeather->status->message)) {
            return array();
        }
        if(isset($objWiki->geonames)) {
            $articles = $objWiki->geonames;
            return $articles;
        }
        else {
            $articles[] = array();
            return $articles;
        }
    }

    public function getHeiracrchy($geoid) {
        $url = "http://ws.geonames.org/hierarchyJSON?geonameId=$geoid";
        $objHeir = json_decode($this->objCurl->exec($url));
        if(is_object($objHeir) && isset($objHeir)) {
            $objHeir = $objHeir->geonames;
            return $objHeir;
        }
        else {
            return NULL;
        } 
    }

    public function addEditVenueForm($eventid, $editparams = NULL) {
        // we need a geo lat and lon (got from map), venue name and description
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->objHead = $this->newObject('htmlheading', 'htmlelements');
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $vtable = $this->newObject('htmltable', 'htmlelements');

        $form = new form ('savevenue', $this->uri(array('action'=>'savevenue')));
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'events', 'Required').'</span>';
        $vtable->cellpadding = 3;
        // heading
        $this->objHead->type = 3;
        $this->objHead->str = $this->objLanguage->languageText("mod_events_addvenuelocation", "events");
        $vtable->startRow();
        $vtable->addCell($this->objHead->show());
        $vtable->endRow();
        // and now the form
        // venue name
        $vnlabel = new label($this->objLanguage->languageText("mod_events_venuename", "events") . ':', 'input_venuename');
        $vname = new textinput('venuename', NULL, NULL);
        if (isset($editparams['venuename'])) {
            $vname->setValue($editparams['venuename']);
        }
        $vtable->startRow();
        $vtable->addCell($vnlabel->show().$required);
        //$vtable->endRow();
        //$vtable->startRow();
        $vtable->addCell($vname->show());
        $vtable->endRow();

        // venue address
        $valabel = new label($this->objLanguage->languageText("mod_events_venueaddress", "events") . ':', 'input_venueaddress');
        $vadd = new textinput('venueaddress', NULL, NULL);
        if (isset($editparams['venueaddress'])) {
            $vadd->setValue($editparams['venueaddress']);
        }
        $vtable->startRow();
        $vtable->addCell($valabel->show());
        //$vtable->endRow();
        //$vtable->startRow();
        $vtable->addCell($vadd->show());
        $vtable->endRow();

        // venue city
        $vclabel = new label($this->objLanguage->languageText("mod_events_venuecity", "events") . ':', 'input_city');
        $vcity = new textinput('city', NULL, NULL);
        if (isset($editparams['city'])) {
            $vcity->setValue($editparams['city']);
        }
        $vtable->startRow();
        $vtable->addCell($vclabel->show());
        //$vtable->endRow();
        //$vtable->startRow();
        $vtable->addCell($vcity->show());
        $vtable->endRow();

        // venue zip/postal code
        $vzlabel = new label($this->objLanguage->languageText("mod_events_venuezip", "events") . ':', 'input_zip');
        $vzip = new textinput('zip', NULL, NULL);
        if (isset($editparams['zip'])) {
            $vzip->setValue($editparams['zip']);
        }
        $vtable->startRow();
        $vtable->addCell($vzlabel->show());
        //$vtable->endRow();
        //$vtable->startRow();
        $vtable->addCell($vzip->show());
        $vtable->endRow();

        // venue phone
        $vplabel = new label($this->objLanguage->languageText("mod_events_venuephone", "events") . ':', 'input_phone');
        $vphone = new textinput('phone', NULL, NULL);
        if (isset($editparams['phone'])) {
            $vphone->setValue($editparams['phone']);
        }
        $vtable->startRow();
        $vtable->addCell($vplabel->show());
        //$vtable->endRow();
        //$vtable->startRow();
        $vtable->addCell($vphone->show());
        $vtable->endRow();

        // venue url
        $vulabel = new label($this->objLanguage->languageText("mod_events_venueurl", "events") . ':', 'input_url');
        $vurl = new textinput('url', NULL, NULL);
        if (isset($editparams['url'])) {
            $vurl->setValue($editparams['url']);
        }
        else {
            $vurl->setValue("http://");
        }
        $vtable->startRow();
        $vtable->addCell($vulabel->show());
        //$vtable->endRow();
        //$vtable->startRow();
        $vtable->addCell($vurl->show());
        $vtable->endRow();

        // venue description
        $vdesclabel = new label($this->objLanguage->languageText("mod_events_venuedescription", "events") . ':', 'input_description');
        $vdescription = $this->newObject('htmlarea', 'htmlelements');
        $vdescription->setName('venuedescription');
        $vdescription->setRows = 5;
        if (isset($editparams['venuedescription'])) {
            $vdescription->setContent($editparams['venuedescription']);
        }
        else {
            $vdescription->setContent($this->objLanguage->languageText("mod_events_briefvenuedescription", "events"));
        }
        $vdescription->setBasicToolBar();
        $vtable->startRow();
        $vtable->addCell($vdesclabel->show());
        $vtable->endRow();
        $vtable->startRow();
        $vtable->addCell($vdescription->show());
        $vtable->endRow();

        // private venue
        $vprivlabel = new label($this->objLanguage->languageText("mod_events_venueprivate", "events") . ':', 'input_private');
        $vpriv = new checkbox('private', NULL, NULL);
        if (isset($editparams['private'])) {
            $vpriv->setValue($editparams['private']);
        }
        $vtable->startRow();
        $vtable->addCell($vprivlabel->show());
        //$vtable->endRow();
        //$vtable->startRow();
        $vtable->addCell($vpriv->show());
        $vtable->endRow();
        $this->loadClass('hiddeninput', 'htmlelements');
        $eventidinput = new hiddeninput('input_eventid', $eventid);

        // put the event details into a fieldset
        $vfieldset = $this->newObject('fieldset', 'htmlelements');
        $vfieldset->legend = $this->objLanguage->languageText("mod_events_addvenues", "events");;
        $vfieldset->contents = $vtable->show().$eventidinput->show();

        // the rules
        $form->addRule('geotag', $this->objLanguage->languageText("mod_events_needgeotag", "events"), 'required');

        $button = new button ('submitform', $this->objLanguage->languageText("mod_events_addvenue", "events"));
        $button->setToSubmit();
        $form->addToForm($this->geotagMap().$vfieldset->show().'<p align="center"><br />'.$button->show().'</p>');
        $ret = $form->show();

        return $ret;
    }

    public function formatVenues($venuelist) {
        if(!isset($venuelist) || empty($venuelist)) {
            return NULL;
        }
        else {
            $ret = NULL;
            // need a radio group for venue chooser
            $this->loadClass('radio', 'htmlelements');

            foreach($venuelist as $venue) {
                $radio = new radio('venue_radio');
                $vname = ucwords($venue['venuename']);
                $vadd = $venue['venueaddress'];
                $vcity = $venue['city'];
                $vzip = $venue['zip'];
                $vphone = $venue['phone'];
                $vurl = $venue['url'];
                $vdesc = $venue['venuedescription'];
                $privacy = $venue['privatevenue'];
                $objIcon = $this->getObject('geticon', 'htmlelements');

                if($privacy != 0 && $venue['userid'] != $this->objUser->userId()) {
                    continue;
                }
                // build up a nice display of the venue info
                $ventable = $this->newObject('htmltable', 'htmlelements');
                $ventable->startRow();
                $ventable->addCell($this->objLanguage->languageText("mod_events_vname", "events").": ");
                $ventable->addCell($vname);
                $ventable->endRow();
                $ventable->startRow();
                $ventable->addCell($this->objLanguage->languageText("mod_events_vaddress", "events").": ");
                $ventable->addCell($vadd);
                $ventable->endRow();
                $ventable->startRow();
                $ventable->addCell($this->objLanguage->languageText("mod_events_vcity", "events").": ");
                $ventable->addCell($vcity);
                $ventable->endRow();
                $ventable->startRow();
                $ventable->addCell($this->objLanguage->languageText("mod_events_vzip", "events").": ");
                $ventable->addCell($vzip);
                $ventable->endRow();
                $ventable->startRow();
                $ventable->addCell($this->objLanguage->languageText("mod_events_vphone", "events").": ");
                $ventable->addCell($vphone);
                $ventable->endRow();
                // build the URL into the fb header
                $vlink = $this->newObject('link', 'htmlelements');
                // var_dump($vurl);
                if($vurl == '' ||$vurl == NULL) {
                    $vurl = $this->uri('');
                }
                $vlink->href = $vurl;
                $vlink->link = $vname;
                $vlink->target = "_blank";
                if($privacy == 1) {
                    $objIcon->setIcon('locked', 'png', 'icons/events/');
                }
                else {
                    $objIcon->setIcon('unlocked', 'png', 'icons/events/');
                }
                $radio->addOption($venue['id'],$vname);
                // build up a display line with a radio button selector
                $rtable = $this->newObject('htmltable', 'htmlelements');
                $rtable->startRow();
                $rtable->addCell($radio->show());
                $rtable->addCell($objIcon->show());
                $rtable->addCell($ventable->show());
                $rtable->endRow();
                $fb = $this->newObject('featurebox', 'navigation');
                $ret[] = $fb->show($vlink->show(), $rtable->show());
            }
        }

        return $ret;
    }

    public function venueSelector($venueArr, $eventid) {
        $ret = NULL;
        $this->objHead = $this->newObject('htmlheading', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $form = new form ('selectvenue', $this->uri(array('action'=>'selectvenue')));
        $vtable = $this->newObject('htmltable', 'htmlelements');

        $form = new form ('venueselect', $this->uri(array('action'=>'venueselect')));
        $vtable->cellpadding = 3;
        // heading
        $this->objHead->type = 3;
        $this->objHead->str = $this->objLanguage->languageText("mod_events_selectvenuelocation", "events");
        $vtable->startRow();
        $vtable->addCell($this->objHead->show());
        $vtable->endRow();
        if(empty($venueArr)) {
            $venueArr = array();
        }
        foreach($venueArr as $venuebox) {
            $vtable->startRow();
            $vtable->addCell($venuebox);
            $vtable->endRow();
        }
        $this->loadClass('hiddeninput', 'htmlelements');
        $eventidinput = new hiddeninput('input_eventid', $eventid);

        $button = new button ('submitform', $this->objLanguage->languageText("mod_events_selectvenue", "events"));
        $button->setToSubmit();
        $vfieldset = $this->newObject('fieldset', 'htmlelements');
        $vfieldset->legend = $this->objLanguage->languageText("mod_events_selectvenues", "events");;
        $vfieldset->contents = $vtable->show().$eventidinput->show();
        $form->addToForm($vfieldset->show().'<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }

    public function geotagMap() {
        $mtable = $this->newObject('htmltable', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'events', 'Required').'</span>';
        $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
        $ret = NULL;
        $css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: white;
        }
        #searchField {
            width: 200px;
        }
        #results {
            border: 1px solid #666;
            border-bottom: 0px;
            font-size: 10px;
            font-family: arial;
            padding: 0px;
            display: none;
        }
        #results div {
            border-bottom: 1px solid #666;
            padding: 3px;
        }
        #results .selected {
            background-color: #666;
            color: #fff;
        }
        #results .unselected {
            background-color: #fff;
            color: #666;
        }
        </style>';
        $google = NULL;
        $olsrc = NULL;
        $js = NULL;
        $google = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$gmapsapikey."' type=\"text/javascript\"></script>";
        $olsrc = $this->getJavascriptFile('lib/OpenLayers.js','georss');
        $js = "<script type=\"text/javascript\">
            var lon = 5;
            var lat = 40;
            var zoom = 20;
            var map, layer, drawControl, g;

            OpenLayers.ProxyHost = \"/proxy/?url=\";
            function init(){
                g = new OpenLayers.Format.GeoRSS();
                map = new OpenLayers.Map( 'map' , { controls: [] , 'numZoomLevels':20 });
                var normal = new OpenLayers.Layer.Google( \"Google Map\" , {type: G_NORMAL_MAP, 'maxZoomLevel':18} );
                var hybrid = new OpenLayers.Layer.Google( \"Google Hybrid Map\" , {type: G_HYBRID_MAP, 'maxZoomLevel':18} );
                map.addLayers([normal, hybrid]);
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

        $acjs = "<script type=\"text/javascript\">
                 $(function(){
                     setAutoComplete(\"input_venuename\", \"results\", \"index.php?module=events&action=venuelist&part=\");
                 });
                 </script>";

        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $css.$google.$olsrc.$js); //.$acjq.$acacsrc.$acdims.$acjs.);
        $this->appendArrayVar('bodyOnLoad', "init();");

        $this->objHead->type = 3;
        $this->objHead->str = $this->objLanguage->languageText("mod_events_chooseeventlocation", "events");
        //$mtable->startRow();
        //$mtable->addCell($this->objEHead->show());
        //$mtable->endRow();
        $mtable->startRow();
        $mtable->addCell($this->objHead->show());
        $mtable->endRow();
        // and now the map
        $mtable->startRow();
        $gtlabel = new label($this->objLanguage->languageText("mod_events_geoposition", "events") . ':', 'input_geotags');
        $gtags = '<div id="map"></div>';
        $geotags = new textinput('geotag', NULL, NULL);
        if (isset($editparams['geolat']) && isset($editparams['geolon'])) {
            $geotags->setValue($editparams['geolat'].", ".$editparams['geolon']);
        }
        $mtable->addCell($gtags);
        $mtable->endRow();

        $ftable->cellpadding = 3;
        // geo tag box
        $geotaglabel = new label($this->objLanguage->languageText("mod_events_geotags", "events") . ':', 'input_geotag');
        $mtable->startRow();
        $mtable->addCell($geotaglabel->show().$required);
        $mtable->endRow();
        $mtable->startRow();
        $mtable->addCell($geotags->show());
        $mtable->endRow();

        // put the map into a fieldset
        $mfieldset = $this->newObject('fieldset', 'htmlelements');
        $mfieldset->legend = $this->objLanguage->languageText("mod_events_venuelocation", "events");;
        $mfieldset->contents = $mtable->show();
        
        return $mfieldset->show();

    }
    
    public function addSocialTagForm($eventid) {
        $otag = $this->objDbEvents->eventGetHashtag($eventid);
        $ret = NULL;
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->objHead = $this->newObject('htmlheading', 'htmlelements');
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $vtable = $this->newObject('htmltable', 'htmlelements');
        if(!empty($otag)) {
            $form = new form ('savesoctags', $this->uri(array('action'=>'savesoctags', 'eventid' => $eventid, 'mode' => 'update')));
        }
        else {
            $form = new form ('savesoctags', $this->uri(array('action'=>'savesoctags', 'eventid' => $eventid)));
        }
        $vtable = $this->newObject('htmltable', 'htmlelements');

        $vtable->cellpadding = 3;
        // heading
        $this->objHead->type = 3;
        $this->objHead->str = $this->objLanguage->languageText("mod_events_addsoctag", "events");
        $whatisthis = $this->newObject('alertbox', 'htmlelements');
        $whatisthat = $whatisthis->show($this->objLanguage->languageText("mod_events_whatisthat", "events"), $this->uri(array('action' => 'hashtagvideo')));
                
        $vtable->startRow();
        $vtable->addCell($this->objHead->show());
        $vtable->addCell($whatisthat);
        $vtable->endRow();
        
        $this->loadClass('textinput', 'htmlelements');
        $taglabel = new label($this->objLanguage->languageText("mod_events_socialtag", "events") . ':', 'input_hashtag');
        $tag = new textinput('hashtag', NULL, NULL);
        if(!empty($otag)) {
            $tag->setValue($otag[0]['mediatag']);
        }
        $vtable->startRow();
        $vtable->addCell($taglabel->show());
        $vtable->addCell("#".$tag->show());
        $vtable->endRow();
        
        $button = new button ('submitform', $this->objLanguage->languageText("mod_events_share", "events"));
        $button->setToSubmit();
        
        $vfieldset = $this->newObject('fieldset', 'htmlelements');
        $vfieldset->legend = $this->objLanguage->languageText("mod_events_selectsoclabels", "events");;
        $vfieldset->contents = $vtable->show();
        $form->addToForm($vfieldset->show().'<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }

    public function formatEventIntro($event) {
        $currLocation = "Events for...";
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        if($this->objCookie->exists('events_location') ) {
            $currLocation = $this->objCookie->get('events_location');
        }
        return $currLocation;
    }
    
    public function viewLocMap($lat, $lon, $zoom = 15) {
        $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
        $css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: white;
            z-index:-5;
        }
        </style>';

        $google = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$gmapsapikey."' type=\"text/javascript\"></script>";
        //$google = "<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=false'></script>";
        $olsrc = $this->getJavascriptFile('lib/OpenLayers.js','georss');
        $js = "<script type=\"text/javascript\">
        var lon = 5;
        var lat = 40;
        var zoom = $zoom;
        var map, layer, drawControl, g;

        OpenLayers.ProxyHost = \"/proxy/?url=\";
        function init(){
            g = new OpenLayers.Format.GeoRSS();
            map = new OpenLayers.Map( 'map' , { controls: [] , 'numZoomLevels':20, projection: new OpenLayers.Projection(\"EPSG:900913\"), displayProjection: new OpenLayers.Projection(\"EPSG:4326\") });
            var normal = new OpenLayers.Layer.Google( \"Google Map\" , {type: G_NORMAL_MAP, 'maxZoomLevel':18 } );
            var hybrid = new OpenLayers.Layer.Google( \"Google Hybrid Map\" , {type: G_HYBRID_MAP, 'maxZoomLevel':18 } );
            
            
            var markers = new OpenLayers.Layer.Markers( \"Markers\" );
            map.addLayer(markers);

            var size = new OpenLayers.Size(20,34);
            var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
            var icon = new OpenLayers.Icon('skins/_common/icons/marker.png',size,offset);

            var proj = new OpenLayers.Projection(\"EPSG:900913\");
            var point = new OpenLayers.LonLat($lon, $lat);
            point.transform(proj, map.getProjectionObject());


            markers.addMarker(new OpenLayers.Marker(point,icon));
            
            map.addLayers([normal, hybrid]);

            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl( new OpenLayers.Control.MouseDefaults() );
            map.addControl( new OpenLayers.Control.LayerSwitcher() );
            map.addControl( new OpenLayers.Control.PanZoomBar() );

            map.setCenter(point, $zoom);

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
        $gtags = '<div id="map"></div>';
        return $gtags;
    }

    public function goGears() {
        $gears = "<script type='text/javascript' src='".$this->getJavascriptFile("gears_init.js", "georss")."'></script>
<script type=\"text/javascript\">
var geo = google.gears.factory.create('beta.geolocation');

function updatePosition(position) {
  alert('Current lat/lon is: ' + position.latitude + ',' + position.longitude);
}

function handleError(positionError) {
  alert('Attempt to get location failed: ' + positionError.message);
}

geo.getCurrentPosition(updatePosition, handleError);
</script>";
    return $gears;
    
    }
    
    /**
     * Viewsingle container function (tabber) box to do the layout for the viewsingle template
     *
     * Chisimba tabber interface is used to create tabs that are dynamically switchable.
     *
     * @return string
     */
    public function viewsingleContainer($eventdata) {
        // get the tabbed box class
        $tabs = $this->getObject('tabber', 'htmlelements');
        $comm = $this->getObject('commentapi', 'blogcomments');
        $washout = $this->getObject("washout", "utilities");
        $objVideo = $this->getObject('video', 'html5elements');
        $objVideo->setVideo(198, 192, 'http://173.203.201.87:8000/theora.ogg', 'ogg', TRUE, FALSE, FALSE);

        $linklist = NULL;
        $linklist .= "<br />";
        if($this->objUser->isLoggedIn() == FALSE) {
            $signinlink = $this->newObject('alertbox', 'htmlelements');
            $signuplink = $this->newObject('alertbox', 'htmlelements');
            $registerlink = $this->newObject('alertbox', 'htmlelements');
            // Make sure to show the sign up link only if registrations are allowed!
            if(strtolower($this->objConfig->getallowSelfRegister()) == 'true') {
                $signuplink = $signuplink->show($this->objLanguage->languageText("mod_events_signup", "events"), $this->uri(array('action' => 'showregister'), 'userregistration'));
            }
            else {
                $signuplink = NULL;
            }
            $signinlink = $signinlink->show($this->objLanguage->languageText("mod_events_signin", "events"), $this->uri(array('action' => 'showsignin'))).", ";
            $linklist .= $signinlink;
            $linklist .= $this->objLanguage->languageText("mod_events_orifyoudonthaveacc", "events").", ".$signuplink;
        }
        
        $hashtag = $eventdata->hashtag;
        if(!empty($hashtag)) {
            $eventhashtag = $hashtag->mediatag;
            $tweets = $eventdata->tweets->twittersearch;
        }
        else {
            $eventhashtag = NULL;
            $tweets = array();
        }

        $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_eventinfo", "events"), 'content' => $this->formatEventFull($eventdata), 'onclick' => ''));
        $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_videoandcomment", "events"), 'content' => $objVideo->show()."<br />".$linklist."<br />".$comm->asyncComments($eventdata->event->id), 'onclick' => ''));
        // get the venue information and wikipedia content
        $venue = $this->objUtils->object2array($eventdata->venue);
        
        // $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_weather", "events"), 'content' => 
                                                        // $this->showPlaceWeatherBox($venue['geolat'], $venue['geolon']), 'onclick' => ''));
        $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_tweets", "events"), 'content' => 
                                                        $this->objSocial->renderTweets($this->objUtils->object2array($tweets), $eventhashtag), 'onclick' => ''));
        $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_wikipedia", "events"), 'content' => 
                                                        $this->getWikipediaContentByLatLon($venue['geolat'], $venue['geolon']), 'onclick' => ''));
                                                        
        // $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_events_saerch", "events"), 'content' => $this->getSearchContent(), 'onclick' => ''));

        return $tabs->show();
    }

}
?>
