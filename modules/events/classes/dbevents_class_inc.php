<?php
/**
 *
 * Events database class
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
 * Events database class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package events
 *
 */
class dbevents extends dbtable {

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

    public $objLangCode;
    public $objUtils;
    public $objTwitOps;

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        parent::init('tbl_events_event');
        $this->objLanguage  = $this->getObject('language', 'language');
        $this->objConfig    = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objWashout   = $this->getObject('washout', 'utilities');
        $this->objUser      = $this->getObject('user', 'security');
        $this->objCurl      = $this->getObject('curlwrapper', 'utilities');
        $this->objLangCode  = $this->getObject('languagecode', 'language');
        $this->objTags      = $this->getObject('dbtags', 'tagging');
        $this->objUtils     = $this->getObject('eventsutils');
        $this->objTwitOps   = $this->getObject('twitoasterops', 'twitoaster');
    }

    /**
     * Events
     */

    /**
     * Retrieve event information and metadata for public and private events.
     *
     * If you want to get information for a private event, you will need auth
     *
     * @param $eventid The Event ID
     */
    public function getEventInfo($eventid) {
        $this->changeTable('tbl_events_events');
        $event = new StdClass();
        $eventdata = $this->getAll("WHERE id = '$eventid'");
        $eventdata = $eventdata[0];
        // now we make the eventdata an object and a property of the overall object
        $eventobj = $this->objUtils->array2object($eventdata);
        $event->event = $eventobj;
        // the organiser data
        $prmo = $this->eventGetPromoters($eventid);
        $event->promoters = $this->objUtils->array2object($prmo);
        // now lets get the venue data
        $this->changeTable('tbl_events_venues');
        $vid = $eventdata['venue_id'];
        $vdata = $this->getAll("WHERE id = '$vid'");
        $vdata = $vdata[0];
        // add the venue info
        $event->venue = $this->objUtils->array2object($vdata);
        // now the venue metadata
        $this->changeTable('tbl_events_venue_location');
        $vmeta = $this->getAll("WHERE venueid = '$vid'");
        $event->venuelocation = $this->objUtils->array2object($vmeta);
        // hashtag
        $hashtag = $this->eventGetHashtag($eventdata['id']);
        if(!empty($hashtag)) {
            $event->hashtag = $hashtag[0];
            $this->grabTwitterBySearch($event->hashtag['mediatag'], $eventid);
        }
        else {
            $event->hashtag = NULL;
        }
        // grab a list of all the people attending the event
        $pplattend = $this->eventGetAttendees($eventid);
        $event->attendees = $this->objUtils->array2object($pplattend);
        // retrieve the user comments, pictures, tweets, flickr, MXit blah blah whatever etc as well
        $tweets = json_decode($this->objTwitOps->showConvo($eventdata['twitoasterid'], 'json'));
        $event->tweets->twitoaster = $tweets;
        // other tweets
        $stweets = $this->getTweetsByEvent($eventid, 50);
        $event->tweets->twittersearch = $this->objUtils->array2object($stweets);
        return json_encode($event);
    }
    
    public function eventGet($eventid) {
        $this->changeTable('tbl_events_events');
        $eventdata = $this->getAll("WHERE id = '$eventid'");
        $eventdata = $eventdata[0];
        // get the Venue name from the venue table and replace the ID in the eventdata array for edit
        $this->changeTable('tbl_events_venues');
        $vid = $eventdata['venue_id'];
        $vdata = $this->getAll("WHERE id = '$vid'");
        $vdata = $vdata[0];
        $eventdata['venuename'] = $vdata['venuename'];
        $cat = $this->categoryGetDetails($eventdata['category_id']);
        $cat = $cat[0];
        $eventdata['cat_name'] = $cat['cat_name'];
        $eventdata['cat_desc'] = $cat['cat_desc'];
        // get the event tags
        
        return $eventdata;
    }
    
    public function eventGetRange($start, $num) {
        $this->changeTable('tbl_events_events');
        $range = $this->getAll ( "ORDER BY creationtime ASC LIMIT {$start}, {$num}" );
        return $range;
    }

    public function eventGetRangeByCat($start, $num, $cat) {
        $this->changeTable('tbl_events_events');
        $range = $this->getAll ( "WHERE category_id = '$cat' ORDER BY creationtime ASC LIMIT {$start}, {$num}" );
        return $range;
    }
    
    public function eventGetRecordCount () {
        $this->changeTable('tbl_events_events');
        return $this->getRecordCount();
    }
    
    public function eventGetLatest($number) {
        $this->changeTable('tbl_events_events');
        $end = $this->eventGetRecordCount();
        $start = $end - $number;
        if($start < 0) {
            $start = 0;
        }
        return $this->eventGetRange($start, $number);
    }
    
    public function eventGetLatestByCat($cat, $number = 20) {
        $this->changeTable('tbl_events_events');
        $end = $this->eventGetRecordCount();
        $start = $end - $number;
        if($start < 0) {
            $start = 0;
        }
        return $this->eventGetRangeByCat($start, $number, $cat);
    }
    
    public function eventGetAttendees($eventid) {
        $this->changeTable('tbl_events_rsvp');
        $useratt = $this->getAll("WHERE eventid = '$eventid' AND ans = 'yes'");
        if(empty($useratt) || $useratt == NULL) {
            return FALSE;
        }
        else {
            return $useratt;
        }
    }
    
    public function eventGetPromoters($eventid) {
        $this->changeTable('tbl_events_promoters');
        return $this->getAll("WHERE event_id = '$eventid'");
    }
    
    public function eventDelete($id) {
        $this->changeTable('tbl_events_events');
        return $this->delete('id', $id, 'tbl_events_events'); 
    }
    
    public function eventGetDescription($eventid) {
        $this->changeTable('tbl_events_events');
        return $this->getArray("SELECT description from tbl_events_events WHERE id = '$eventid'");
    }

    /**
     * Add a new event to the database. This method requires authentication.
     *
     * @param $userid (Required) An authenticated user id.
     * @param $name (Required) The name of the event.
     * @param $venue_id (Numeric, Required) The venue_id of the event. To get a venue_id, try the venue* series of functions.
     * @param $category_id (Numeric, Required) The category_id of the event. To get a category_id, try the category* series of functions.
     * @param $start_date (YYYY-MM-DD, Required) The start date of the event, formatted as YYYY-MM-DD.
     * @param $end_date (YYYY-MM-DD, Optional) The end date of the event, formatted as YYYY-MM-DD.
     * @param $start_time (HH:MM:SS, Optional) The start time of the event, formatted as HH:MM:SS.
     * @param $end_time (HH:MM:SS, Optional) The end time of the event, formatted as HH:MM:SS.
     * @param $description (Optional) A textual description of the event.
     * @param $url (Optional) The website URL for the event.
     * @param $personal (1 or 0, Optional, Defaults to 0) A flag indicating whether the event should be public (0), or shown only to your friends (1).
     * @param $selfpromotion (1 or 0, Optional, Defaults to 0) A flag indicating whether the event should be marked as a normal event (0), or as a self-promotional event (1).
     * @param $ticket_url (Optional) The website URL for purchasing tickets to the event.
     * @param $ticket_price (Optional) The price of a ticket to the event.
     * @param $ticket_free (1 or 0, Optional, Defaults to 0) A flag indicating if the event is free (1) or not (0).
     */
    public function addEvent($userid, $name, $venue_id, $category_id, $start_date, $end_date = NULL, $start_time, $end_time = NULL, $description = NULL, $url = NULL, $personal = 0, $selfpromotion = 0, $ticket_url = NULL, $ticket_price = NULL, $ticket_free = 0) {
        $this->changeTable('tbl_events_events');
        $eventarr = array (
            'userid'        => $userid,
            'name'          => $name,
            'venue_id'      => $venue_id,
            'category_id'   => $category_id,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'start_time'    => $start_time,
            'end_time'      => $end_time,
            'description'   => $description,
            'url'           => $url,
            'personal'      => $personal,
            'selfpromotion' => $selfpromotion,
            'ticket_url'    => $ticket_url,
            'ticket_price'  => $ticket_price,
            'ticket_free'   => $ticket_free,
            'creationtime'  => time(),
        );

        return $this->addEventArray($eventarr);
    }

    public function addEventArray($eventArr) {
        $this->changeTable('tbl_events_events');
        return $this->insert($eventArr);
    }
    
    public function eventAddHashtag($eventid, $tag) {
        $this->changeTable('tbl_events_eventtag');
        $count = $this->getRecordCount("WHERE mediatag = '$tag'");
        if($count == '0') {
            $this->insert(array('eventid' =>$eventid, 'mediatag' => $tag));
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    public function eventGetHashtag($eventid) {
        $this->changeTable('tbl_events_eventtag');
        return $this->getAll("WHERE eventid = '$eventid'");
    }
    
    public function addTwtId($threadid, $eventid) {
        $this->changeTable('tbl_events_events');
        $event = $this->getAll("WHERE id = '$eventid'");
        $event[0]['twitoasterid'] = $threadid;
        $this->update('id', $eventid, $event[0]);
        return TRUE; 
    }

    public function addEventPromo($orgarr) {
        $this->changeTable('tbl_events_promoters');
        return $this->insert($orgarr);
    }
    
    public function getEventPromo($eventid) {
        $this->changeTable('tbl_events_promoters');
        return $this->getAll("WHERE event_id = '$eventid'");
    }

    public function updateEventPromo($eventid, $orgarr) {
        $this->changeTable('tbl_events_promoters');
        return $this->update('id', $eventid, $orgarr);
    }

    /**
     * Edit an event
     *
     * Edit an event in the database. Missing parameters will clear out their corresponding values in the event.
     * You must authenticate as the user who added the event to do this.
     * This method requires authentication.
     *
     * @param $userid (Required) An authenticated user id.
     * @param $event_id (Required) The id of the event to edit.
     * @param $name (Required) The name of the event.
     * @param $venue_id (Numeric, Required) The venue_id of the event. To get a venue_id, try the venue* series of functions.
     * @param $category_id (Numeric, Required) The category_id of the event. To get a category_id, try the category* series of functions.
     * @param $start_date (YYYY-MM-DD, Required) The start date of the event, formatted as YYYY-MM-DD.
     * @param $end_date (YYYY-MM-DD, Optional) The end date of the event, formatted as YYYY-MM-DD.
     * @param $start_time (HH:MM:SS, Optional) The start time of the event, formatted as HH:MM:SS.
     * @param $end_time (HH:MM:SS, Optional) The end time of the event, formatted as HH:MM:SS.
     * @param $description (Optional) A textual description of the event.
     * @param $url (Optional) The website URL for the event.
     * @param $personal (1 or 0, Optional, Defaults to 0) A flag indicating whether the event should be public (0), or shown only to your friends (1).
     * @param $selfpromotion (1 or 0, Optional, Defaults to 0) A flag indicating whether the event should be marked as a normal event (0), or as a self-promotional event (1).
     * @param $ticket_url (Optional) The website URL for purchasing tickets to the event.
     * @param $ticket_price (Optional) The price of a ticket to the event.
     * @param $ticket_free (1 or 0, Optional, Defaults to 0) A flag indicating if the event is free (1) or not (0).
     */
    public function editEvent($userid, $event_id, $name, $venue_id, $category_id, $start_date, $end_date = NULL, $start_time, $end_time = NULL, $description = NULL, $url = NULL, $personal = 0, $selfpromotion = 0, $ticket_url = NULL, $ticket_price = NULL, $ticket_free = 0) {
        $this->changeTable('tbl_events_events');
        $eventarr = array (
            'userid'        => $userid,
            'name'          => $name,
            'venue_id'      => $venue_id,
            'category_id'   => $category_id,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'start_time'    => $start_time,
            'end_time'      => $end_time,
            'description'   => $description,
            'url'           => $url,
            'personal'      => $personal,
            'selfpromotion' => $selfpromotion,
            'ticket_url'    => $ticket_url,
            'ticket_price'  => $ticket_price,
            'ticket_free'   => $ticket_free,
        );

        return $this->update('id', $event_id, $eventarr);
    }

    public function updateEventWithVenueId($eventid, $venueid) {
        $this->changeTable('tbl_events_events');
        return $this->update('id', $eventid, array('venue_id' => $venueid));
    }
    
    public function eventUpdateArr($eventarr) {
        $this->changeTable('tbl_events_events');
        return $this->update('id', $eventarr['id'], $eventarr);
    }

    /**
     * Add/Replace tags on an event
     *
     * Add/Replace the user's current tags on an event.
     * This method expects to receive a comma delimited string with all the tags the user wishes to have on the object.
     * It will replace the current set of user's tags on the event with the new list.
     * This method requires the user to be authenticated
     *
     * @param $userid (Required) An authenticated user id.
     * @param $event_id (String, Required) The event_id of the event. To get a event_id, try the eventSearch function.
     * @param $tags (String, Required) A comma-separated list of tags. Optionally surround multi-word tags with quotes.
     * @see eventSearch()
     */
    public function eventAddReplaceTags($userid, $event_id, $tags) {
        $userid = $this->objUser->userId();
        // first we need to delete the existing tags (if any) in order to replace them
        foreach ( $this->objTags->getPostTags($event_id, 'events') as $deltags ) {
            $this->objTags->deleteTags($deltags['id'], 'events');
        }
        $uri = $this->uri(array('module' => 'events', 'action' => 'viewtags', 'eventid' => $event_id));
        $tagarr = explode(",", $tags);

        return $this->objTags->insertTags($tagarr, $userid, $event_id, 'events', $uri, NULL);
    }

    /**
     * Remove a single tag from an event. This method requires the user to be authenticated
     *
     * @param $userid (Required) An authenticated user id.
     * @param $event_id (String, Required) The event_id of the event. To get a event_id, try the eventSearch function.
     * @param $tag (String, Required) A single "raw" tag to remove. By Raw, I mean the exact word/tag. There is no intelligent matching done for case etc!
     */
    public function eventRemoveTag($userid, $event_id, $tag) {
        // find the tag
        $alltags = $this->objTags->getPostTags($event_id, 'events');
        foreach ( $alltags as $thetags ) {
            if($thetags['meta_value'] == $tag) {
                $this->objTags->deleteTags($thetags['id'], 'events');
            }
        }
    }
    
    public function eventGetTags($eventid) {
        $alltags = $this->objTags->getPostTags($eventid, 'events');
        foreach ( $alltags as $thetags ) {
            $tags[] = $thetags['meta_value'];
        }
        if(!isset($tags)) {
            $tags = array();
        }
        return implode(",",$tags);
    }

    /**
     * Search for public events by multiple facets. If optional authentication is provided, eventSearch also searches private events.
     *
     * @param $search_text (Optional) The search terms to be used to look for events. To collect all events with other filters applied, do not pass a search_text.
     * @param $location (Optional) Only for use in proximity search, the location parameter, if provided, will attempt to restrict search results to areas near that location.
     * @param $radius (km) (Optional, Default: 50km., Max: 100km.) If location is specified, then eventSearch will look for a radius parameter. Otherwise, it will use 50km. as the radius of the search.
     * @param $place_id (Optional) An string ID like 'kH8dL0ubBZrvX_YZ', denoting a specific named geographical area.
     * @param $country_id (Numeric, Optional) The country_id of the event, used to narrow down the responses. To get a country_id, try the metroGetCountryList function.
     * @param $state_id (Numeric, Optional) The state_id of the event, used to narrow down the responses. To get a state_id, try the metroGetStateList function.
     * @param $metro_id (Numeric, Optional) The metro_id of the event, used to narrow down the responses. To get a metro_id, try the metroGetList function.
     * @param $venue_id (Numeric, Optional) A venue_id to search within. To get a venue_id, try the venue* series of functions.
     * @param $woeid (Optional) The WOEID of the place to which search results will be restricted.
     * @param $category_id (CSV Numeric, Optional) A category_id integer or comma-separated list of category ids to search within. To get a category_id, try the categoryGetList function.
     * @param $min_date (YYYY-MM-DD, Optional) Search all events after this date, formatted as YYYY-MM-DD.
     * @param $max_date (YYYY-MM-DD, Optional) Search all events before this date, formatted as YYYY-MM-DD.
     * @param $tags (Optional) A comma-separated list of tags. Events that have been tagged with any of the tags passed will be returned. 20 tags max.
     * @param $ticket_sources (Optional) A comma-separated list of ticket sources. Events having tickets from a specific ticket source (computicket, or user).
     * @param $per_page (Numeric, Optional, Default = 100) Number of results to return per page. Max is 100 per page.
     * @param $page (Numeric, Optional, Default = 1) The page number of results to return.
     * @param $sort (String, Optional, Default = start-date-asc) The field and direction on which to sort the results. Distance sorts must ONLY be used if location is specified.
     * @param $backfill (String, Optional) If the first page of results returned has fewer than per_page results, try expanding the search.
     * @param $variety (Boolean (1, or 0), Optional ) Attempt to provide more varied results. Currently, this is implemented as not showing more than one event of each category. This will greatly reduce the amount of results returned.
     * @param $rollup (String, Optional) Used to display all future events of an event. By default only the last event of an event is displayed.
     */
    public function eventSearch($search_text = NULL, $location = NULL, $radius = NULL, $place_id = NULL, $country_id, $state_id, $metro_id = NULL, $venue_id = NULL, $woeid = NULL, 
                                $category_id = NULL, $min_date = NULL, $max_date = NULL, $tags = NULL, $ticket_sources = NULL, $per_page = 100, $page = 1, 
                                $sort = 'start-date-asc', $backfill = NULL, $variety = 0, $rollup = NULL) {
        // OK this function is already in need of refactoring, and I haven't written it yet. Will be splitting it to a bunch of eventSearch*() functions rather...
    }
    
    public function eventSearchDescText($keyword) {
        $this->changeTable('tbl_events_events');
        return $this->getAll("WHERE description LIKE '%%$keyword%%'");
    }

    /**
     * Get a watchlist for an event
     *
     * Get a watchlist for an event.
     * You will only be shown your own private events plus those of people who have marked you as a friend.
     * Returns user nodes for each user on the watchlist.
     * Returns either status="attend" or status="watch"
     *
     * @param $event_id (Required) The id of the event.
     */
    public function eventGetWatchlist($event_id) {

    }

    /**
     * Get event groups
     *
     * For a given event_id, retrieve group information and metadata for public and private groups that include the event in their group calendar.
     *
     * @param $event_id (Required) The id number of the event.
     * @param $token (Optional) An authentication token. Pass to see even private groups.
     */
    public function eventGetGroups($event_id, $token = NULL) {

    }

    /**
     * Get best in place
     *
     * Search for featured or popular events in a specified place.
     *
     * @param $location (Optional) Only for use in proximity search, the location parameter, if provided, will attempt to restrict search results to areas near that location.
     * @param $radius (km) (Optional, Default: 50km., Max: 100km.) If location is specified, then event.search will look for a radius parameter. Otherwise, it will use 50km. as the radius of the search.
     * @param $place_id (Optional) An string ID like 'kH8dL0ubBZrvX_YZ', denoting a specific named geographical area.
     * @param $country_id (Numeric, Optional) The country_id of the event, used to narrow down the responses. To get a country_id, try the metroGetCountryList function.
     * @param $state_id (Numeric, Optional) The state_id of the event, used to narrow down the responses. To get a state_id, try the metroGetStateList function.
     * @param $metro_id (Numeric, Optional) The metro_id of the event, used to narrow down the responses. To get a metro_id, try the metroGetList function.
     * @param $woeid (Optional) The WOEID of the place to which search results will be restricted.
     * @param $per_page (Numeric, Optional, Default = 10) Number of results to return per page. Max is 10 per page.
     * @param $sort (String, Optional) The field and direction on which to sort the results.
     * @param $filter (String, Optional, Default = popular) Use this to filter the search results to get the best type of events in the given place.
     */
    public function eventBestInPlace($location = NULL, $radius = 50, $place_id = NULL, $country_id = 28, $state_id = NULL, $metro_id = NULL, $woeid = NULL, $per_page = 10, $sort = NULL, $filter = NULL) {

    }

    public function getEventList() {
    
    }

    /**
     * Metro functions
     */

    /**
     * Get country list of the countries currently with events or activity in the database
     *
     * Retrieve a list of all active countries in the database.
     *
     */
    public function metroGetCountryList() {
        $this->changeTable('tbl_events_venue_location');
        $ret = $this->getArray("SELECT DISTINCT(countrycode), countryname FROM tbl_events_venue_location where countryname != 'NULL'");
        return $ret;
    }
    
    /**
     * Get country information from a given 2 letter country code
     *
     * Retrieve a list of all active countries in the database or query service to get country information, insert, then return the info
     * 
     * @param $countrycode (Required) 2 letter Country code e.g. 'ZA'
     */
    public function metroGetCountryInfo($countrycode) {
        $this->changeTable('tbl_events_countryinfo');
        if($countrycode == NULL) {
            $countrycode = 'ZA';
        }
        if($this->getRecordCount("WHERE countrycode = '$countrycode'") > 0) {
            return $this->getAll("WHERE countrycode = '$countrycode'");
        }
        else {
            $url = "http://ws.geonames.org/countryInfoJSON?country=$countrycode";
            $json = $this->objCurl->exec($url);
            $objCinfo = json_decode($json);
            $arr = $this->objUtils->object2array($objCinfo);
            foreach($arr['geonames'] as $place) {
                $place = $this->objUtils->object2array($place);
                $this->insert($place);
            }
            return $this->getAll("WHERE countrycode = '$countrycode'");
        }
     }

    /**
     * Get state or province list of a country currently with events or activity in the database
     *
     * Retrieve a list of all active provinces/states in the database.
     * 
     * @param $countryCode string (Required) Uppercase 2 letter country code that you want to query
     */
    public function metroGetStateList($countryCode) {
        $this->changeTable('tbl_events_venue_location');
        $ret = $this->getArray("SELECT DISTINCT(name) FROM tbl_events_venue_location WHERE fcode = 'ADM1' and countrycode = '$countryCode';");
        return $ret;
    }

    /**
     * Get a list of metros in a state or province currently with events or activity in the database
     *
     * Retrieve a list of all active metros in the database.
     * 
     * @param $statename string (Required) state name that you want to query. e.g. Western Cape
     */
    public function metroGetList($statename) {
        $this->changeTable('tbl_events_venue_location');
        $ret = $this->getArray("SELECT DISTINCT(name) FROM tbl_events_venue_location WHERE fcode = 'PPL' and adminname1 = '$statename';");
        return $ret;
    }

    /**
     * Get available information about a metro with events or activity in the database
     *
     * Retrieve some information (geographical) of particular active metros in the database.
     * 
     * @param $metroname string (Required) the metro name(s) (this can be a comma seperated list) that you want to query. e.g. Vasco,Torrano,
     */
    public function metroGetInfo($metroname) {
        // metro id can be a comma sep list!
        $metroname = explode(",", $metroname);
        $this->changeTable('tbl_events_venue_location');
        foreach($metroname as $metname) {
            $ret[] = $this->getAll("WHERE name = '$metname'");
        }
        return $ret;
    }

    /**
     * Get a list of metros associated with the currently logged in user with events or activity in the database
     *
     * Retrieve some information (geographical) of particular active metros in the database.
     * 
     */
    public function metroGetMyList() {
        $this->changeTable('tbl_events_venue_location');
        if($this->objUser->isLoggedIn()) {
            $userid = $this->objUser->$userid();
            return $this->getAll("WHERE userid = '$userid'");
        }
        else {
            return FALSE;
        }
    }

    /**
     * Search for a given metro
     *
     * @param $search_text (Required) The search text to use. Supports quoted strings and empty parameter (to display all). Please restrict by another parameter when using blank values.
     * @param $country_id (Optional) The country_id of the event, used to narrow down the responses. To get a country_id, try the metroGetCountryList function.
     * @param $state_id (Optional) The state_id of the event, used to narrow down the responses. To get a state_id, try the metroGetStateList function.
     */
    public function metroSearch($search_text, $countryCode = '', $statename = '', $countryname = '') {
        $this->changeTable('tbl_events_venue_location');
        $ret = $this->getAll("WHERE fcodename LIKE '%%$search_text%%' OR countryname LIKE '%%$search_text%%' OR countrycode LIKE '%%$search_text%%' OR fclname LIKE '%%$search_text%%' 
                              OR name LIKE '%%$search_text%%' OR admincode1 LIKE '%%$search_text%%' OR adminname1 LIKE '%%$search_text%%' OR countrycode = '$countryCode' OR
                              adminname1 = '$statename' OR countryname = '$countryname'");
        return $ret;
    }

    public function metroGetForLatLong($lat, $lon) {

    }

    /**
     * State API
     */

    /**
     * Retrieve the details about a state.
     *
     * @param $state_id (Required) The state_id number of the state to look within.
     *                  State ID's are referenced in other methods, such as metroGetStateList and metroGetInfo.
     *                  To run getInfo on multiple states, simply pass a comma-separated list of state_id numbers.
     */
    public function stateGetInfo($state_id) {

    }

    /**
     * Country API
     */

    /**
     * Gets country Info
     *
     * Retrieve the details about a country.
     * @param $country_id The country_id number of the country to look within.
     *                    Country ID's are referred to within other API methods, such as metroGetStateList and stateGetInfo.
     *                    To run getInfo on multiple countries, simply pass a comma-separated list of country_id numbers.
     */
    public function countryGetInfo($country_id) {

    }

    /**
     * Venue API
     */

    /**
     * venueAdd
     *
     * Add a new venue to the database. You must pass authentication parameters for this function.
     *
     * @param $venuename (Required) The name of the venue.
     * @param $venueaddress (Required) The address of the venue.
     * @param $venuecity (Required) The city of the venue.
     * @param $venuezip (Optional) The venue's Zip Code or equivalent.
     * @param $venuephone (Optional) The venue's phone number.
     * @param $venueurl (Optional) The url of the venue's website (if any).
     * @param $venuedescription (Optional) A textual description of the venue.
     * @param $private (1 or 0, Optional, Defaults to 0) A flag indicating whether the venue should be public (0), or shown only to your friends (1).
     */
    public function venueAdd($venuename, $venueaddress, $venuecity, $venuezip, $venuephone, $venueurl, $venuedescription, $private = 0) {
        $insarr = array('userid' => $this->objUser->userId(), 'venuename' => $venuename, 'venueaddress' => $venueaddress, 'city' => $venuecity, 'zip' => $venuezip, 'phone' => $venuephone,
                        'url' => $venueurl, 'venuedescription' => $venuedescription, 'private' => $private);
        return $this->venueAddArray($insarr);
    }

    /**
     * venueAddArray
     *
     * Add a new venue to the database. You must pass authentication parameters for this function.
     *
     * @param $insarr (Required) The array of venue data.
     */
    public function venueAddArray($insarr) {
        parent::init('tbl_events_venues');
        return $this->insert($insarr);
    }

    public function venueCheckExists($venuename) {
        $venuename = addslashes($venuename);
        parent::init('tbl_events_venues');
        $ret = $this->getAll("WHERE venuename like '%$venuename%'");

        return $ret;
    }

    /**
     * venueEdit
     *
     * Edit a venue. Only the authenticated user that added the venue may edit it. You must pass authentication parameters for this function.
     *
     * @param $token (Required) An authentication token.
     * @param $venue_id (Numeric, Required) The id of the venue.
     * @param $venuename (Required) The name of the venue.
     * @param $venueaddress (Required) The address of the venue.
     * @param $venuecity (Required) The city of the venue.
     * @param $metro_id (Numeric,required if no location ) The metro_id of the venue. To get a metro_id, try the metro* series of functions.
     * @param $location (Required if no metro_id) Location parameter accepts comma separated address fields and adds the venue based on your input.
     * @param $venuezip (Optional) The venue's Zip Code or equivalent.
     * @param $venuephone (Optional) The venue's phone number.
     * @param $venueurl (Optional) The url of the venue's website (if any).
     * @param $venuedescription (Optional) A textual description of the venue.
     * @param $private (1 or 0, Optional, Defaults to 0) A flag indicating whether the venue should be public (0), or shown only to your friends (1).
     */
    public function venueEdit($token, $venue_id, $venuename, $venueaddress, $venuecity, $metro_id, $location, $venuezip, $venuephone, $venueurl, $venuedescription, $private = 0) {

    }

    /**
     * Retrieve the details about a venue.
     *
     * @param $venue_id (Required) The venue_id number of the venue to look within. To find venue_id's, use venueGetList.
     *                             You can also pass multiple venue_id's separated by commas to getInfo on multiple venues.
     */
    public function venueGetInfo($venue_id) {
        $this->changeTable('tbl_events_venues');
        return $this->getAll("WHERE id = '$venue_id'");
    }

    public function venueGetByName($venuename) {
        $this->changeTable('tbl_events_venues');
        return $this->getAll("WHERE venuename = '$venuename'");
    }

    /**
     * Get a venue list
     *
     * Retrieve a list of venues for a particular metro.
     *
     * @param $metro_id (Required) The metro_id number of the metro to look within. To find metro_id's, use metroGetList.
     * @param $token (Optional) An authentication token. Pass to return private venues.
     */
    public function venueGetList($metro_id, $token) {

    }

    /**
     * venueSearch
     *
     * Allows searching through venues.
     *
     * @param $search_text (Optional) The search string to use when looking for venues. Supports quoted phrases and blank values for searching all venues. Please restrict by another parameter when using blank values.
     * @param $location (Optional) Only for use in proximity search, the location parameter, if provided, will attempt to restrict search results to areas near that location. This may either be formatted as a comma-separated latitude and longitude (i.e. "37.821, -111.179"), or a fulltext. Any search that uses the location parameter will add the additional data elements "distance" and "distance_units" to the result set.
     * @param $radius (km) (Optional, Default: 50km., Max: 100km.) If location is specified, then eventSearch will look for a radius parameter. Otherwise, it will use 50km. as the radius of the search.
     * @param $country_id (Numeric, Optional) The country_id of the event, used to narrow down the responses. To get a country_id, try the metroGetCountryList function.
     * @param $state_id (Numeric, Optional) The state_id of the event, used to narrow down the responses. To get a state_id, try the metroGetStateList function.
     * @param $metro_id (Numeric, Optional) The metro_id of the event, used to narrow down the responses. To get a metro_id, try the metroGetList function.
     * @param $per_page (Numeric, Optional, Default = 100) Number of results to return per page. Max is 100 per page.
     * @param $page (Numeric, Optional, Default = 1) The page number of results to return.
     * @param $sort (One of name-desc, name-asc, distance-asc, distance-desc, Default = name-asc) The field and direction on which to sort the results. Distance sorts must ONLY be used if location is specified.
     */
    public function venueSearch($search_text, $location, $radius, $country_id, $state_id, $metro_id, $per_page, $page, $sort) {

    }

    public function venueInsertHeirarchy($heirarchy) {
        parent::init('tbl_events_venue_location');
        return $this->insert($heirarchy);
    }

    /**
     * Category API
     */

    /**
     * Retrieve a list of valid event categories.
     *
     * @return list of categories
     */
    public function categoryGetList() {
        $this->changeTable('tbl_events_cats');
        return $this->getAll();
    }

    public function categoryGetDetails($catid) {
        $this->changeTable('tbl_events_cats');
        return $this->getAll("WHERE id = '$catid'");
    }

    /**
     * Watchlist API
     */

    /**
     * watchlistGetList
     * Retrieve the watchlist for a user.
     *
     * @param $token (Required) An authentication token.
     * @param $min_date (YYYY-MM-DD, Optional) Get watchlisted events on or after this date, formatted as YYYY-MM-DD.
     * @param $max_date (YYYY-MM-DD, Optional) Get watchlisted events on or before this date, formatted as YYYY-MM-DD.
     * @param $sort ('start-date-asc', 'start-date-desc', 'post-date-asc', 'post-date-desc') [Default: 'post-date-asc'] Sort the watchlisted events by start date or post date.
     */
    public function watchlistGetList($token, $min_date, $max_date, $sort) {

    }

    /**
     * watchlistAdd
     * Add an event to a user's watchlist.
     * This function will delete an existing watchlist setting and replace it with the new one, so you don't have to call watchlistRemove first.
     *
     * @param $token (Required) An authentication token.
     * @param $event_id (Numeric, Required) The event_id of the event. To get a event_id, try the eventSearch function.
     * @param $status (Either 'attend' or 'watch', Optional, Default = 'watch') A setting indicating whether you plan to attend or watch this event.
     */
    public function watchlistAdd($token, $event_id, $status = 'watch') {

    }

    /**
     * watchlistRemove
     *
     * Remove a watchlist record from a user's watchlist.
     *
     * @param $token (Required) An authentication token.
     * @param $watchlist_id (Numeric, Required) The watchlist_id of the event. To get a watchlist_id, try the watchlistGetList function.
     */
    public function watchlistRemove($token, $watchlist_id) {

    }

    /**
     * User functions
     */

    /**
     * userGetInfo
     *
     * Retrieve the details about a user.
     *
     * @param $user_id (Required) The user_id number of the user to look within. To run getInfo on multiple users, simply pass a comma-separated list of user_id numbers.
     */
    public function userGetInfo($user_id) {

    }

    /**
     * userGetInfoByUsername
     *
     * Retrieve the details about a user.
     *
     * @param $username (Required) The username (or screen name) of the user to look within. To run getInfoByUsername on multiple users, simply pass a comma-separated list of username strings.
     */
    public function userGetInfoByUsername($username) {

    }

    /**
     * userGetInfoByEmail
     *
     * Retrieve the details about a user.
     *
     * @param $email (Required) The email of the user to look within. To run getInfoByEmail on multiple addresses, simply pass a comma-separated list of valid email addresses.
     */
    public function userGetInfoByEmail($email) {

    }

    /**
     * userGetMetroList
     *
     * Retrieve a list of metros for a particular user.
     *
     * @param $token (Required) An authentication token.
     */
    public function userGetMetroList($token) {

    }

    /**
     * userGetWatchlist
     *
     * Gets all events in the watchlist for a user.
     * You may optionally pass authentication parameters for this function to get back private events from people who have authenticated user as a friend.
     * The 'username' returned is the username of the watchlist owner.
     * It also returns either status="attend" or status="watch".
     * Watchlists for personal events that are created by friends of the user authenticated are shown.
     * In other words, you pass a username and password.
     * Naturally, you'll have access to see any events created by others who have you as a friend. If the user_id you query has any of those specific personal events as an item in their watchlist,
     * they will show up in this function.
     * Additionally, by default, userGetWatchlist only returns events with a start date >= today, or upcoming events.
     * To get all events ever in a user's watchlist, or to get past events only, pass the "show" parameter.
     *
     * @param $token (Optional) An authentication token.
     * @param $user_id (Required) The user_id requested.
     * @param $show (Optional, Default: 'upcoming') May be 'upcoming', 'all', or 'past' to retrieve corresponding events.
     */
    public function userGetWatchlist($userid, $show) {

    }

    /**
     * userGetMyFriendsEvents
     *
     * Retrieve the events being watched/attended by a user's friends.
     * These events can either be public or created by a person who calls the user a friend.
     *
     * @param $per_page (Numeric, Optional, Default = 100) Number of results to return per page. Max is 100 per page.
     * @param $page (Numeric, Optional, Default = 1) The page number of results to return.
     */
    public function userGetMyFriendsEvents($per_page = 100, $page = 1) {

    }
    
    public function userCheckAttend($userid, $eventid) {
        $this->changeTable('tbl_events_rsvp');
        $useratt = $this->getAll("WHERE userid = '$userid' AND eventid = '$eventid'");
        if(empty($useratt) || $useratt == NULL) {
            return FALSE;
        }
        else {
            return $useratt;
        }
    }
    
    public function userDoRSVP($rsvparr) {
        $this->changeTable('tbl_events_rsvp');
        if($rsvparr['ans'] != 'swap') {
            return $this->insert($rsvparr);
        }
        else {
            $det = $this->userCheckAttend($rsvparr['userid'], $rsvparr['eventid']);
            $det = $det[0];
            if($det['ans'] == 'yes') {
                $rsvparr['ans'] = 'no';
            }
            elseif($det['ans'] == 'no') {
                $rsvparr['ans'] = 'yes';
            }
            return $this->update('id', $det['id'], $rsvparr);
        }
    }
    
    public function userSearch($name) {
        $this->changeTable('tbl_users');
        // userName, firstname, surname or emailaddress
        $res = $this->getAll("WHERE username LIKE '%%$name%%' OR firstname LIKE '%%$name%%' OR surname LIKE '%%$name%%' OR emailaddress LIKE '%%$name%%'");
        
        return $res;
    }

    /**
     * Group API
     */

    /**
     * groupGetInfo
     *
     * Retrieve group information and metadata for public and private groups.
     *
     * @param $group_id (Required) The id number of the group. You can also pass multiple group_id's separated by commas to getInfo on multiple groups.
     * @param $token (Optional) An authentication token. Pass to see even private groups.
     */
    public function groupGetInfo($group_id, $token) {

    }

    /**
     * groupGetMembers
     *
     * Retrieve group member user information and metadata for any public group or private group that the authenticated user is a member of.
     *
     * @param $token (Required) An authentication token.
     * @param $group_id (Numeric, Required) The group id requested.
     * @param $membersPerPage (Numeric, Optional) To restrict the number of members per page of results. Default is 100.
     * @param $page (Numeric, Optional) Page # to return. Starts with 1.
     * @param $order (Either 'member_timestamp' or 'username', default: 'username') Member_timestamp orders by date joined.
     * @param $dir (Either 'asc' or 'desc', default: asc) Sort direction.
     */
    public function groupGetMembers($token, $group_id, $membersPerPage, $page, $order, $dir) {

    }

    /**
     * groupGetEvents
     *
     * Retrieve group event information and metadata for any public group or private group that the authenticated user is a member of.
     *
     * @param $token (Optional) An authentication token.
     * @param $group_id (Numeric, Required) The group id requested.
     * @param $eventsPerPage (Numeric, Optional) To restrict the number of members per page of results.
     * @param $page (Numeric, Optional) Page # to return. Starts with 1.
     * @param $order (Either 'event_time' or 'time_added', default: 'event_time') Event_time orders by event start date, time_added orders by the time the event was added to the group.
     * @param $dir (Either 'asc' or 'desc', default: asc) Sort direction.
     * @param $show_past (Either 1 or 0, default: 0) Whether to exclusively show past results (instead of upcoming) in the event results.
     */
    public function groupGetEvents($token, $group_id, $eventsPerPage, $page, $order, $dir, $show_past) {

    }

    /**
     * groupGetMyGroups
     *
     * Retrieve group information and metadata for all groups that the authenticated user is a member of. This method requires authentication.
     *
     * @param $token (Required) An authentication token.
     */
    public function groupGetMyGroups($token) {

    }

    /**
     * groupAdd
     *
     * Add a new group to the database. This method requires authentication.
     *
     * @param $token (Required) An authentication token.
     * @param $name (Required) The name of the group.
     * @param $description (Optional) The group's description. May contain some HTML.
     * @param $event_moderation (Numeric, either 1 or 0) Whether to enable moderation of event suggestions. Default is 0.
     * @param $member_moderation (Numeric, either 1 or 0) Whether to enable moderation of new members. Default is 0.
     * @param $is_private (Number, 1 or 0) Indicates whether it should be a private, invite-only group(1), or available for public view and searching (0).
     */
    public function groupAdd($token, $name, $description, $event_moderation, $member_moderation, $is_private) {

    }

    /**
     * groupEdit
     *
     * Edit a group. Only an admin of a group may edit it.This method requires authentication.
     *
     * @param $token (Required) An authentication token.
     * @param $group_id (Numeric, Required) The id of the group to edit.
     * @param $name (Required) The name of the group.
     * @param $description (Optional) The group's description. May contain some HTML.
     * @param $event_moderation (Numeric, either 1 or 0) Whether to enable moderation of event suggestions. Default is 0.
     * @param $member_moderation (Numeric, either 1 or 0) Whether to enable moderation of new members. Default is 0.
     * @param $is_private (Number, 1 or 0) Indicates whether it should be a private, invite-only group(1), or available for public view and searching (0).
     */
    public function groupEdit($token, $group_id, $name, $description, $event_moderation, $member_moderation, $is_private) {

    }

    /**
     * groupJoin
     *
     * Try to join a group. If the group is moderated, the request may be queued for administrator review instead of processed immediately. This method requires authentication.
     *
     * @param $token (Required) An authentication token.
     * @param $group_id (Required) The id of the group to join.
     */
    public function groupJoin($token, $group_id) {

    }

    /**
     * groupLeave
     *
     * Try to leave a group. If the user leaving was the last member of the group, the group is permanently deleted, and may not be rejoined. If the user who left was the last admin in the group, the user remaining with the earliest join timestamp becomes an admin.
     *
     * @param $token (Required) An authentication token.
     * @param $group_id (Required) The id of the group to leave.
     */
    public function groupLeave($token, $group_id) {

    }

    /**
     * groupAddEventTo
     *
     * Try to add an event to a group. If the group is moderated, the request may be queued for administrator review instead of processed immediately.
     *
     * @param $token (Required) An authentication token.
     * @param $group_id (Required) The id of the group.
     * @param $event_id (Required) The id of the event to send.
     */
    public function groupAddEventTo($token, $groupid, $event_id) {

    }

    /**
     * groupAdminRemoveEvent
     *
     * Try to remove an event to a group. This method can only be called by an authenticated group admin, or by the user who added the event to the group.
     *
     * @param $token (Required) An authentication token.
     * @param $group_id (Required) The id of the group.
     * @param $event_id (Required) The id of the event to remove.
     */
    public function groupAdminRemoveEvent($token, $group_id, $event_id) {

    }

    public function grabTwitterBySearch($hashtag, $eventid) {
        $path = $this->objConfig->getModulePath()."events/".$hashtag."tweetupdate";
        if(!file_exists($path)) {
            touch($path);
            chmod($path, 0777);
        }
        $lastupdate = file_get_contents($path);
        if($lastupdate == '') {
            $hashtag = urlencode("#".$hashtag);
            $url = "http://search.twitter.com/search.json?q=&ands=$hashtag&phrase=&ors=&nots=&lang=all&from=&to=&ref=&refresh_url=$lastupdate&rpp=100";
        }
        else {
            $url = "http://search.twitter.com/search.json?$lastupdate";
        }
        $res = $this->objCurl->exec($url);
        $res = json_decode($res);
        $this->twitterSmartUpdate($res, $eventid);
        if(file_exists($path)) {
            unlink($path);
            touch($path);
            chmod($path, 0777);
            if(is_object($res)) {
                if(isset($res->refresh_url)) {
                    file_put_contents($path, $res->refresh_url);
                }
            }
        }
    }
    
    public function twitterSmartUpdate($res, $eventid) {
        foreach($res->results as $result) {
            if(!$this->tweetExists($result->id)) {
                if(!isset($result->location)) {
                    $result->location = NULL;
                }
                if(!isset($result->iso_language_code)) {
                    $result->iso_language_code = NULL;
                }
                $this->insert(array('eventid' => $eventid, 'tweet' => $result->text, 'createdat' => $result->created_at, 'from_user' => $result->from_user, 
                                    'tweetid' => $result->id, 'lang' => $result->iso_language_code, 'source' => $result->source, 
                                    'image' => $result->profile_image_url, 'location' => $result->location, 'tweettime' => strtotime($result->created_at)), 'tbl_events_tweets');
                $this->parseHashTags($result->text, $result->id);
            }
        }
    }

    public function parseHashtags($str, $tweetid)
    {
        $str = stripslashes($str);
        preg_match_all('/\#([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $memetag = array($item);
            // add the $item to tbl_tags
            $this->objTags->insertHashTags($memetag, 1, $tweetid, 'events', $this->uri(''));
            $counter++;
        }
        return $str;
    }
    
    public function tweetExists ($tweetid) {
        parent::init('tbl_events_tweets');
        $cnt = $this->getRecordCount("WHERE tweetid = '$tweetid'");
        if($cnt > 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    public function getTweetsByEvent($eventid, $limit = 100) {
        $this->changeTable('tbl_events_tweets');
        $ret = $this->getAll("WHERE eventid = '$eventid' ORDER BY tweettime ASC LIMIT $limit");
        return $ret;
    }

    /**
     * changeTable method to switch tables dynamically
     *
     * @param $table (Required) A string table name to switch to
     */
    public function changeTable($table) {
        parent::init($table);
    }
}
?>