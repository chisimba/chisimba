<?php
/**
 * Events controller class
 *
 * Class to control the events module
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
 * @package   events
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
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
 * Events controller class
 *
 * Class to control the Events module.
 *
 * @category  Chisimba
 * @package   events
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class events extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;
    public $objOps;
    public $objCurl;
    public $objDbTags;
    public $objUtils;
    public $ip2Country;
    public $objWashout;
    public $objTwtOps;
    public $objTeeny;
    public $objSocial;
    public $dbFoaf;
    public $objModuleCat;
    public $objActStream;
    public $eventsEnabled;

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            $this->requiresLogin();
            $this->objLanguage   = $this->getObject ( 'language', 'language' );
            $this->objConfig     = $this->getObject('altconfig', 'config');
            // Get the sysconfig variables for the Jabber user to set up the connection.
            $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser       = $this->getObject('user', 'security');
            $this->objCurl       = $this->getObject('curlwrapper', 'utilities');
            $this->objDbEvents   = $this->getObject('dbevents');
            $this->objAJTags     = $this->getObject('ajaxtags', 'tagging');
            $this->objOps        = $this->getObject('eventsops');
            $this->objCookie     = $this->getObject('cookie', 'utilities');
            $this->objDbTags     = $this->getObject('dbtags', 'tagging');
            $this->objUtils      = $this->getObject('eventsutils');
            $this->ip2Country    = $this->getObject('iptocountry', 'utilities');
            $this->objWashout    = $this->getObject('washout', 'utilities');
            $this->objTwtOps     = $this->getObject('twitoasterops', 'twitoaster');
            $this->objTeeny      = $this->getObject ( 'tiny', 'tinyurl');
            $this->objSocial     = $this->getObject('eventssocial');
            $this->dbFoaf        = $this->getObject('dbfoaf', 'foaf');
            $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
            if($this->objModuleCat->checkIfRegistered('activitystreamer'))
            {
                $this->objActStream = $this->getObject('activityops','activitystreamer');
                $this->eventDispatcher->addObserver(array($this->objActStream, 'postmade' ));
                $this->eventsEnabled = TRUE;
            } else {
                $this->eventsEnabled = FALSE;
            }
        }
        catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            case NULL:

            case 'main' :
                $ip = $this->objOps->getIpAddr();
                $ccode = $this->ip2Country->getCountryByIP($ip);
                $country = $this->ip2Country->getCountryNameByIp($ip);
                // get the country info from the service if it doesn't exist.
                $countryinfo = $this->objDbEvents->metroGetCountryInfo($ccode);
                $countryinfo = $countryinfo[0];
                $this->setVarByRef('countryinfo', $countryinfo);
                return 'main_tpl.php';
                break;

            case 'viewsingle' :
                $eventid = $this->getParam('eventid', NULL);
                $eventdata = $this->objDbEvents->getEventInfo($eventid);
                $this->setVarByRef('eventdata', $eventdata);
                return 'viewsingle_tpl.php';
                break;
                
            case 'eventdesconly' :
                $eventid = $this->getParam('eventid');
                $data = $this->objDbEvents->eventGetDescription($eventid);
                echo $this->objWashout->parseText($data[0]['description']);
                break;
            
            case 'vieweventjson' :
                $eventid = $this->getParam('eventid', NULL);
                $eventdata = $this->objDbEvents->getEventInfo($eventid);
                header("Content-Type: application/json");
                echo $eventdata;
                break;

            case 'view' :
                echo "View";
                break;

            case 'showsignin' :
                echo $this->objOps->showSignInBox();
                break;

            case 'showsignup' :
                echo $this->objOps->showSignUpBox();
                break;

            case 'invitefriend' :
                echo $this->objOps->showInviteForm();
                break;

            case 'changelocation' :
                return 'location_tpl.php';
                break;

            case 'setlocation' :
                $locstr = $this->getParam('geotag');
                $locarr = explode(",", $locstr);
                $lat = trim($locarr[0]);
                $lon = trim($locarr[1]);
                $locarr = $this->objOps->findNearby($lat, $lon);
                if($this->objCookie->exists('events_location') ) {
                    $this->objCookie->cookiedelete('events_location');
                    $this->objCookie->cookiedelete('events_latlon');
                    $this->objCookie->cookiedelete('events_geoid');
                    $this->objCookie->set( 'events_location', $locarr->name, time()+60*60*24*30);
                    $this->objCookie->set( 'events_latlon', $locarr->lat."|".$locarr->lng, time()+60*60*24*30);
                }
                else {
                    $this->objCookie->set( 'events_location', $locarr->name, time()+60*60*24*30);
                    $this->objCookie->set( 'events_latlon', $locarr->lat."|".$locarr->lng, time()+60*60*24*30);
                }
                // let everyone know where you are
                $title = $this->objLanguage->languageText("mod_events_setlocation", "events");
                $link = $this->uri(array('action' => 'viewlocation', 'lat' => $lat, 'lon' => $lon));
                $contextCode = NULL;
                if($this->objUser->isloggedIn()) {
                    $author = $this->objUser->fullName();
                }
                else {
                    $author = $this->objLanguage->languageText("mod_events_wordguest", "events");
                }
                $message = $author." ".$this->objLanguage->languageText("mod_events_locationsetto", "events")." ".$locarr->name;
                $this->eventDispatcher->post($this->objActStream, 'events', array('title' => $title, 'link' => $link, 'contextcode' => $contextCode, 'author' => $author, 'description' => $message));
                $this->nextAction('');
                break;

            case 'eventadd' :
                $eventname = $this->getParam('eventname', NULL);
                $eventcat = $this->getParam('eventcategory', NULL);
                $venuename = $this->getParam('venuename', NULL);
                $startdatetime = $this->getParam('startdatetime', NULL);
                // split to start date and time
                $startarr = explode(" ", $startdatetime);
                if(is_array($startarr) && !empty($startarr) && $startarr[0] != '') {
                    $startdate = trim($startarr[0]);
                    $starttime = trim($startarr[1]);
                }
                else {
                    $startdate = NULL;
                    $starttime = NULL;
                }
                $enddatetime = $this->getParam('enddatetime', NULL);
                $endarr = explode(" ", $enddatetime);
                if(is_array($endarr) && !empty($endarr) && $endarr[0] != '') {
                    $enddate = trim($endarr[0]);
                    $endtime = trim($endarr[1]);
                }
                else {
                    $enddate = NULL;
                    $endtime = NULL;
                }
                $eventurl = $this->getParam('eventurl', NULL);
                $ticketurl = $this->getParam('ticketurl', NULL);
                $ticketprice = $this->getParam('ticketprice', NULL);
                $ticketfree = $this->getParam('ticketfree', NULL);
                $description = $this->getParam('description', NULL);
                $personal = $this->getParam('personal', NULL);
                $tags = $this->getParam('tags', NULL);
                $selfpromo = $this->getParam('selfpromotion', NULL);
                // check that stuff is not NULL
                if($eventname == NULL || $eventcat == NULL || $description == NULL) {
                    $message = $this->getObject('timeoutmessage', 'htmlelements');
                    $message->setMessage( $this->objLanguage->languageText("mod_events_requiredfieldsmissing", "events" ) );
                    $this->setVarByRef('message', $message);
                    return 'main_tpl.php';
                }

                // Add the event to the database, the event will be updated by the venue script afterwards with the venue info
                $insarr =  array('userid' => $this->objUser->userId(), 'name' => $eventname, 'venue_id' => NULL, 'category_id' => $eventcat,
                                 'start_date' => $startdate, 'end_date' => $enddate, 'start_time' =>  $starttime, 'end_time' => $endtime,
                                 'description' => $description, 'url' => $eventurl, 'personal' => $personal, 'selfpromotion' => $selfpromo,
                                 'ticket_url' => $ticketurl, 'ticket_price' => $ticketprice, 'ticket_free' => $ticketfree, 'creationtime' => time(),);
                // insert the event info
                $eventret = $this->objDbEvents->addEventArray($insarr);
                $tagarray = explode(",", $tags);
                // send the tags to the tags database
                $this->objDbTags->insertTags($tagarray, $this->objUser->userId(), $eventret, 'events', $this->uri(''), NULL);
                if($selfpromo == 'on') {
                    // organizer thing
                    $canbringothers = $this->getParam('canbringothers', NULL);
                    $yestheycan = $this->getParam('yestheycan', NULL);
                    $howmany = $this->getParam('howmany', NULL);
                    $orgarr = array('userid' => $this->objUser->userId(), 'event_id' => $eventret, 'canbringothers' => $canbringothers, 'numberguests' => $yestheycan, 'limitedto' => $howmany);
                    $this->objDbEvents->addEventPromo($orgarr);
                }

                // now we can check if the venue has been defined before or not and get some details there...
                $venuelist = $this->objDbEvents->venueCheckExists($venuename);
                $venlist = $this->objOps->formatVenues($venuelist);
                $venueform = $this->objOps->venueSelector($venlist, $eventret);
                $this->setVarByRef('venueform', $venueform);
                $this->setVarByRef('eventid', $eventret);
                return 'venue_tpl.php';
                break;
                
            case 'eventupdate' :
                $eventid = $this->getParam('input_eventid');
                $eventname = $this->getParam('eventname', NULL);
                $eventcat = $this->getParam('eventcategory', NULL);
                $venuename = $this->getParam('venuename', NULL);
                $startdatetime = $this->getParam('startdatetime', NULL);
                // split to start date and time
                $startarr = explode(" ", $startdatetime);
                if(is_array($startarr) && !empty($startarr) && $startarr[0] != '') {
                    $startdate = trim($startarr[0]);
                    $starttime = trim($startarr[1]);
                }
                else {
                    $startdate = NULL;
                    $starttime = NULL;
                }
                $enddatetime = $this->getParam('enddatetime', NULL);
                $endarr = explode(" ", $enddatetime);
                if(is_array($endarr) && !empty($endarr) && $endarr[0] != '') {
                    $enddate = trim($endarr[0]);
                    $endtime = trim($endarr[1]);
                }
                else {
                    $enddate = NULL;
                    $endtime = NULL;
                }
                $eventurl = $this->getParam('eventurl', NULL);
                $ticketurl = $this->getParam('ticketurl', NULL);
                $ticketprice = $this->getParam('ticketprice', NULL);
                $ticketfree = $this->getParam('ticketfree', NULL);
                $description = $this->getParam('description', NULL);
                $personal = $this->getParam('personal', NULL);
                $tags = $this->getParam('tags', NULL);
                $selfpromo = $this->getParam('selfpromotion', NULL);
                // check that stuff is not NULL
                if($eventname == NULL || $eventcat == NULL || $description == NULL) {
                    $message = $this->getObject('timeoutmessage', 'htmlelements');
                    $message->setMessage( $this->objLanguage->languageText("mod_events_requiredfieldsmissing", "events" ) );
                    $this->setVarByRef('message', $message);
                    return 'main_tpl.php';
                }
                // check the venue id
                $venueid = $this->objDbEvents->venueGetByName($venuename);
                if(empty($venueid)) {
                    // note we will need the venue creator and selector interface again...
                    $venuid = NULL;
                }
                else {
                    $venueid = $venueid[0]['id'];
                }
                // Add the event to the database, the event will be updated by the venue script afterwards with the venue info
                $editarr =  array('id' => $eventid, 'userid' => $this->objUser->userId(), 'name' => $eventname, 'venue_id' => $venueid, 'category_id' => $eventcat,
                                 'start_date' => $startdate, 'end_date' => $enddate, 'start_time' =>  $starttime, 'end_time' => $endtime,
                                 'description' => $description, 'url' => $eventurl, 'personal' => $personal, 'selfpromotion' => $selfpromo,
                                 'ticket_url' => $ticketurl, 'ticket_price' => $ticketprice, 'ticket_free' => $ticketfree, 'creationtime' => time(),);
                
                // now udate the record!
                $this->objDbEvents->eventUpdateArr($editarr);
                // send the tags to the tags database
                $this->objDbEvents->eventAddReplaceTags($this->objUser->userId(), $eventid, $tags);
                if($selfpromo == 'on') {
                    // organizer thing
                    $canbringothers = $this->getParam('canbringothers', NULL);
                    $yestheycan = $this->getParam('yestheycan', NULL);
                    $howmany = $this->getParam('howmany', NULL);
                    $orgarr = array('userid' => $this->objUser->userId(), 'event_id' => $eventid, 'canbringothers' => $canbringothers, 'numberguests' => $yestheycan, 'limitedto' => $howmany);
                    $this->objDbEvents->updateEventPromo($eventid, $orgarr);
                }
                // ok all is updated, but now we gotta check if the venue has changed or not. If it has, we need to update that whole lot too
                if($venueid == NULL) {
                    $venuelist = $this->objDbEvents->venueCheckExists($venuename);
                    $venlist = $this->objOps->formatVenues($venuelist);
                    $venueform = $this->objOps->venueSelector($venlist, $eventid);
                    $this->setVarByRef('venueform', $venueform);
                    $this->setVarByRef('eventid', $eventid);
                    return 'venue_tpl.php';
                    break;
                }
                else {
                    $this->nextAction('');
                }
                break;

            case 'addvenue' :
                echo $this->objOps->addEditVenueForm();
                break;

            case 'venueselect' :
                $venueid = $this->getParam('venue_radio');
                $eventid = $this->getParam('input_eventid');
                $this->objDbEvents->updateEventWithVenueId($eventid, $venueid);
                $this->nextAction('selectmediatag', array('eventid' => $eventid));
                break;

            case 'test' : 
                //header("Content-Type: application/json");
                //echo $this->objDbEvents->getEventInfo('gen21Srv31Nme28_62509_1254836051');
                //$this->objOps->grabTwitterBySearch('Chisimba');
                //$this->objUtils->createMediaTag('Brand monday party!');
                //var_dump($this->objDbEvents->addTwtId(123, 'gen21Srv31Nme28_62509_1254836051'));
                //$this->objCollecta = $this->getObject('collecta', 'utilities');
                //header("Content-Type: application/json");
                //echo $this->objCollecta->search("Chisimba", array('format' => 'json'));
                $this->objTpic = $this->getObject('twitpicops', 'twitter');
                $username = 'peepscoza';
                $password = 'pongid56';
                $message = 'Testing #Chisimba Twitpic API class';
                $media = "/var/www/chisimbalogo.png";
                //$ret = $this->objTpic->uploadPicPostMsg($media, $username, $password, $message);
                echo "<img src =".$this->objTpic->getSmall('n4vq7', 'thumb').">";
                var_dump($ret);
                break;
            
            case 'picupload' :
                echo $this->objOps->picUploadForm();
                
                break;

            case 'savevenue' :
                $eventid = $this->getParam('input_eventid');
                $venuename = $this->getParam('venuename');
                $venueaddress = $this->getParam('venueaddress');
                $city = $this->getParam('city');
                $zip = $this->getParam('zip');
                $phone = $this->getParam('phone');
                $url = $this->getParam('url');
                if($url == 'http://') {
                    $url = '';
                }
                $description = $this->getParam('venuedescription');
                $private = $this->getParam('private');
                if($private == 'on') {
                    $private = 1;
                }
                else {
                    $private = 0;
                }
                $geo = $this->getParam('geotag');
                $geo = explode(",", $geo);
                $lat = trim($geo[0]);
                $lon = trim($geo[1]);
                // This is always an insert as it is always new. 
                $insarr = array('userid' => $this->objUser->userId(), 'venuename' => $venuename, 'venueaddress' => $venueaddress, 
                                'city' => $city, 'zip' => $zip, 'phone' => $phone, 'url' => $url, 'venuedescription' => $description, 
                                'geolat' => $lat, 'geolon' => $lon, 'privatevenue' => $private);
                $venueid = $this->objDbEvents->venueAddArray($insarr);
                $this->objDbEvents->updateEventWithVenueId($eventid, $venueid);
                // do a lookup and see where this place is, then fill out heirarchy to country scale (or more?)
                $locarr = $this->objOps->findNearby($lat, $lon);
                $heir = $this->objOps->getHeiracrchy($locarr->geonameId);
                foreach($heir as $h) {
                    // insert record to db with venue id as key
                    $h->venueid = $venueid;
                    $h->userid = $this->objUser->userId();
                    // we now need to convert the object to an array for dbTable::insert();
                    $h = $this->objUtils->object2array($h);
                    $this->objDbEvents->venueInsertHeirarchy($h);
                }
                $this->nextAction('selectmediatag', array('eventid' => $eventid));
                break;
                
            case 'selectmediatag' : 
                $eventid = $this->getParam('eventid');
                $this->setVarByRef('eventid', $eventid);
                return 'hashtag_tpl.php';
                break;
                
            case 'savesoctags' :
                $eventid = $this->getParam('eventid');
                $tag = $this->getParam('hashtag');
                $mode = $this->getParam('mode');
                // activity stream
                $title = $this->objLanguage->languageText("mod_events_neweventadded", "events");
                $link = $this->uri(array('action' => 'viewsingle', 'eventid' => $eventid));
                $contextCode = NULL;
                if($this->objUser->isloggedIn()) {
                    $author = $this->objUser->fullName();
                }
                else {
                    $author = $this->objLanguage->languageText("mod_events_wordguest", "events");
                }
                if($this->objDbEvents->eventAddHashtag($eventid, $tag) == TRUE) {
                    // send the tweet with your new meme and have fun
                    $eventinfo = $this->objDbEvents->getEventInfo($eventid);
                    $eventinfo = json_decode($eventinfo);
                    $eventname = $eventinfo->event->name;
                    $eventuri = $this->uri(array('action' => 'viewsingle', 'id' => $eventid), 'events');
                    // tinyurl the uri now to save space
                    $eventuri = $this->objTeeny->create(urlencode($eventuri));
                    // a message
                    $tweet = $this->objLanguage->languageText ( "mod_events_newevent", "events" ).": ".ucwords($eventname)." ".$eventuri." #".$tag;
                    // log_debug($tweet);
                    //$returnobj = json_decode($this->objTwtOps->userUpdate( $tweet ));
                    //$thread = $returnobj->thread;
                    //$threadid = $thread->id;
                    //$threadid = rand(0, 99999);
                    // now update the event with the tweetid to track twitter conversations on this tweet.
                    $this->objDbEvents->addTwtId($threadid, $eventid);
                    // let everyone know
                    $message = $author." ".$this->objLanguage->languageText("mod_events_addednewevent", "events");
                    $this->eventDispatcher->post($this->objActStream, 'events', array('title' => $title, 'link' => $link, 'contextcode' => $contextCode, 'author' => $author, 'description' => $message));
                    $this->nextAction('viewsingle', array('eventid' => $eventid));
                 }
                 elseif($mode == 'update') {
                     // let everyone know
                     $message = $author." ".$this->objLanguage->languageText("mod_events_updatedevent", "events");
                     $this->eventDispatcher->post($this->objActStream, 'events', array('title' => $title, 'link' => $link, 'contextcode' => $contextCode, 'author' => $author, 'description' => $message));
                     $this->nextAction('viewsingle', array('eventid' => $eventid));
                 }
                 else {
                     $message = $this->getObject('timeoutmessage', 'htmlelements');
                     $message->setMessage( $this->objLanguage->languageText("mod_events_hashtagnotunique", "events" ) );
                     $this->setVarByRef('message', $message);
                     $this->setVarByRef('eventid', $eventid);
                     return 'hashtag_tpl.php';
                 }
                break;
                
            case 'hashtagvideo' :
                echo $this->objWashout->parseText("[YOUTUBE]http://www.youtube.com/watch?v=aAHitI26MmE[/YOUTUBE]");
                break;
                
            case 'rsvp' :
                $userid = $this->getParam('userid', NULL);
                $ans = $this->getParam('ans', NULL);
                $eventid = $this->getParam('eventid', NULL);
                // activity stream
                $title = $this->objLanguage->languageText("mod_events_rsvpadded", "events");
                $link = $this->uri(array('action' => 'viewsingle', 'eventid' => $eventid));
                $contextCode = NULL;
                if($this->objUser->isloggedIn()) {
                    $author = $this->objUser->fullName();
                }
                else {
                    $author = $this->objLanguage->languageText("mod_events_wordguest", "events");
                }
                if($userid == NULL || $ans == NULL || $eventid == NULL) {
                    $message = $this->objLanguage->languageText("mod_events_needsignin", "events");
                }
                $rsvparr = array('ans' => $ans, 'userid' => $userid, 'eventid' => $eventid);
                $this->objDbEvents->userDoRSVP($rsvparr);
                // let everyone know
                if($ans == 'yes') {
                    $message = $author." ".$this->objLanguage->languageText("mod_events_rsvpeventgoing", "events");
                }
                elseif($ans == 'no') {
                    $message = $author." ".$this->objLanguage->languageText("mod_events_rsvpeventnotgoing", "events");
                }
                else {
                    $message = $author." ".$this->objLanguage->languageText("mod_events_rsvpeventchangedmind", "events");
                }
                $this->eventDispatcher->post($this->objActStream, 'events', array('title' => $title, 'link' => $link, 'contextcode' => $contextCode, 'author' => $author, 'description' => $message));
                $this->nextAction('');
                break;
                
            case 'rsvpgetinvite' :
                $eventid = $this->getParam('eventid', NULL);
                $ans = $this->getParam('ans', NULL);
                $userid = $this->getParam('userid', $this->objUser->userId());
                if($ans != 'inv' || $eventid == '' || $eventid == NULL) {
                    $this->nextAction('');
                }
                else {
                    // get the event organiser's details, or at least the person that added the event details
                    $edata = $this->objDbEvents->getEventInfo($eventid);
                    header("Content-Type: application/json");
                    echo $edata; die();
                }
                break;
            
            case 'eventdelete' :
                $id = $this->getParam('id');
                $this->objDbEvents->eventDelete($id);
                $this->nextAction('');
                break;
                
            case 'eventedit' :
                $eventid = $this->getParam('id');
                $eventdata = $this->objDbEvents->eventGet($eventid);
                // Send the data to the edit form
                $this->setVarByRef('eventdata', $eventdata);
                return 'eventedit_tpl.php';
                break;
                
            case 'showcat' :
                $catid = $this->getParam('cat');
                if($catid == 0) {
                    $this->nextAction('');
                }
                $eventdata = $this->objOps->getCatContent($catid, 20);
                $catname = $this->objDbEvents->categoryGetDetails($catid);
                $this->setVarByRef('catname', $catname);
                $this->setVarByRef('eventdata', $eventdata);
                return 'catevent_tpl.php';
                break;
                
            case 'searchfriends' :
                $name = $this->getParam('friend_name');
                $res = $this->objDbEvents->userSearch($name);
                $poss = $this->objSocial->formatFriendSearch($res);
                $this->setVarByRef('poss', $poss);
                return 'friendselect_tpl.php';
                break;
                
            case 'makefriend' :
                $fid = $this->getParam('fuserid');
                $myid = $this->objUser->userId();
                $this->dbFoaf->insertFriend(array('userid' => $myid, 'fuserid' => $fid));
                // update the activity streamer
                $title = $this->objLanguage->languageText("mod_events_newconnection", "events");
                $link = $this->uri(array('action' => '#'));
                $contextCode = NULL;
                $author = $this->objUser->fullName($myid);
                $message = $author." ".$this->objLanguage->languageText("mod_events_isnofriendswith", "events")." ".$this->objUser->fullName($fid);
                $this->eventDispatcher->post($this->objActStream, 'events', array('title' => $title, 'link' => $link, 'contextcode' => $contextCode, 'author' => $author, 'description' => $message));
                $this->nextAction('');
                break;
                
            case 'viewlocation' :
                $lat = $this->getParam('lat');
                $lon = $this->getParam('lon');
                $this->setVarByRef('lat', $lat);
                $this->setVarByRef('lon', $lon);
                return 'viewloc_tpl.php';
                break;
                
            case 'placeweather' :
                $lat = $this->getParam('lat');
                $lon = $this->getParam('lon');
                echo $this->objOps->showPlaceWeatherBox($lat, $lon);
                break;
                
            case 'makefavourite' :
                $userid = $this->objUser->userId();
                $eventid = $this->getParam('eventid');
                // update the activity streamer
                $title = $this->objLanguage->languageText("mod_events_favouriteadded", "events");
                $link = $this->uri(array('action' => 'viewsingle', 'eventid' => $eventid));
                $contextCode = NULL;
                $author = $this->objUser->fullName($userid);
                $message = $author." ".$this->objLanguage->languageText("mod_events_madefavourite", "events");
                $this->eventDispatcher->post($this->objActStream, 'events', array('title' => $title, 'link' => $link, 'contextcode' => $contextCode, 'author' => $author, 'description' => $message));
                $this->nextAction('');
                break;
                
            default:
                $this->nextAction('');
                break;
        }
    }

    public function requiresLogin() {
        return FALSE;
    }
}
?>
