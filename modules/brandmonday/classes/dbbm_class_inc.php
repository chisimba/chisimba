<?php
/**
 * BrandMonday dbtable derived class
 *
 * Class to interact with the database for the brandmonday
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
 * @version   $Id: $
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

class dbbm extends dbTable {
    /**
     * Constructor
     *
     */
     
    public $excludeList;
    public $objCurl;
    
    public function init() {
        parent::init ( 'tbl_bmmentions' );
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->excludeList = $this->objSysConfig->getValue('exclusions', 'brandmonday');
        $this->excludeList = explode(",", $this->excludeList);
        $this->objCurl = $this->getObject('curlwrapper', 'utilities');
        $this->objConfig = $this->getObject('altconfig', 'config');
    }

    public function smartUpdate($resMinus, $resPlus, $resMentions) {
        // first the plus
        foreach($resPlus->results as $res) {
            if(!$this->tweetExists('tbl_bmplus', $res->id)) {
                $location = $this->checkUserLoc($res);
                $location = $location['location'];
                $this->insert(array('tweet' => $res->text, 'createdat' => $res->created_at, 'from_user' => $res->from_user, 'tweetid' => $res->id, 'lang' => $res->iso_language_code, 'source' => $res->source, 'image' => $res->profile_image_url, 'location' => $location, 'tweettime' => strtotime($res->created_at)), 'tbl_bmplus');
                $this->parseHashTags($res->text, $res->id, 'plus');
                $this->analyseWords('tbl_bmplus_words', $res->text);
                $latlon = explode(", ", $location);
                $lat = @$latlon[0];
                $lon = @$latlon[1];
                $tweet = $res->text;
                $datarrplus[] = array('lat' => $lat, 'lon' => $lon, 'tweet' => $tweet); 
                $this->tweetMapMarkers($datarrplus, 'plus');
            }
        }
        
        foreach($resMinus->results as $res) {
            if(!$this->tweetExists('tbl_bmminus', $res->id)) {
                $location = $this->checkUserLoc($res);
                $location = $location['location'];
                $this->insert(array('tweet' => $res->text, 'createdat' => $res->created_at, 'from_user' => $res->from_user, 'tweetid' => $res->id, 'lang' => $res->iso_language_code, 'source' => $res->source, 'image' => $res->profile_image_url, 'location' => $res->geo, 'tweettime' => strtotime($res->created_at)), 'tbl_bmminus');
                $this->parseHashTags($res->text, $res->id, 'minus');
                $this->analyseWords('tbl_bmminus_words', $res->text);
                $latlon = explode(", ", $location);
                $lat = @$latlon[0];
                $lon = @$latlon[1];
                $tweet = $res->text;
                $datarrminus[] = array('lat' => $lat, 'lon' => $lon, 'tweet' => $tweet); 
                $this->tweetMapMarkers($datarrminus, 'minus');
            }
        }
        foreach($resMentions->results as $res) {
            if(!isset($res->to_user)) {
                $res->to_user = NULL;
            }
            if(!$this->tweetExists('tbl_bmmentions', $res->id)) {
                $location = $this->checkUserLoc($res);
                $location = $location['location'];
                $this->insert(array('tweet' => $res->text, 'createdat' => $res->created_at, 'from_user' => $res->from_user, 'to_user' => $res->to_user, 'tweetid' => $res->id, 'lang' => $res->iso_language_code, 'source' => $res->source, 'image' => $res->profile_image_url, 'location' => $res->geo, 'tweettime' => strtotime($res->created_at)), 'tbl_bmmentions');
                // $this->parseHashTags($res->text, $res->id);
                $latlon = explode(", ", $location);
                $lat = @$latlon[0];
                $lon = @$latlon[1];
                $tweet = $res->text;
                $datarrmen[] = array('lat' => $lat, 'lon' => $lon, 'tweet' => $tweet);
                $this->tweetMapMarkers($datarrmen, 'mentions');
            }
        }
    }
    
    public function checkUserLoc($res) {
        // check if we already have the info for this user
        parent::init('tbl_bmlocations');
        $user = $res->from_user;
        $chk = $this->getRecordCount("WHERE user = '$user'");
        if($chk > 0) {
            $loc = $this->getAll("WHERE user = '$user'");
            return $loc[0];
        }
        else {
            $userlookupurl = "http://twitter.com/users/show.json?screen_name=".$res->from_user;
            $udata = json_decode($this->objCurl->exec($userlookupurl));
            $userLocation = @$udata->location;
            if($userLocation != '' || $userLocation != NULL) {
                $userLocation = explode(": ", $userLocation);
                if(isset($userLocation[1])) {
                    $res = array('user' => $res->from_user, 'location' => $userLocation[1], 'datecreated' => $this->now());
                }
                else {
                    $userLocation = explode(",", $userLocation[0]);
                    $userLocation = addslashes($userLocation[0]);
                    // look up the latlon on geonames.org database.
                    parent::init('tbl_geonames');
                    $loc = $this->getAll("WHERE name = '$userLocation'");
                    $loc = @$loc[0];
                    $lat = $loc['latitude'];
                    $lon = $loc['longitude'];
                    $res = array('user' => $res->from_user, 'location' => $lat.", ".$lon, 'datecreated' => $this->now());
                }
                // insert the record
                $this->insert($res, 'tbl_bmlocations');
                return $res;
            }
            else {
                $res = array('user' => $res->from_user, 'location' => 'Unknown', 'datecreated' => $this->now());
            }
        }
    }

    public function tweetMapMarkers($dataArray, $resultset) {
        // build up a set of markers for a google map
        $head = "<markers>";
        $body = NULL;
        foreach($dataArray as $data) {
            if($data['lat'] == "" || $data['lon'] == "") {
                continue;
            }
            else {
                $body .= '<marker lat="'.$data['lat'].'" lng="'.$data['lon'].'" tweet="'.htmlentities($data['tweet']).'" />';
            }
        }
        $tail = "</markers>";
        $data = $head.$body.$tail;
        $path = $this->objConfig->getModulePath()."brandmonday/".$resultset."markers.xml";
        if(!file_exists($path)) {
            touch($path);
            chmod($path, 0777);
        }
        else {
            unlink($path);
            touch($path);
            chmod($path, 0777);
        }
        file_put_contents($path, $data);
        
        return $data;
    }
    
    public function getMsgRecordCount ($table) {
        parent::init($table);
        return $this->getRecordCount();
    }

    public function tweetExists ($table, $tweetid) {
        parent::init($table);
        $cnt = $this->getRecordCount("WHERE tweetid = '$tweetid'");
        if($cnt > 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function getRange($table, $start, $num) {
        parent::init($table);
        $range = $this->getAll ( "ORDER BY tweettime DESC LIMIT {$start}, {$num}" );
        $results = new StdClass();
        $results->results = $range;
        
        return $results;
    }

    public function getHappyPeeps() {
        $plususers = $this->getArray("SELECT DISTINCT from_user FROM tbl_bmplus WHERE from_user != 'CapeTown' ORDER BY id");
        return $plususers;
    }

    public function getSadPeeps() {
        $minususers = $this->getArray("SELECT DISTINCT from_user FROM tbl_bmminus WHERE from_user != 'CapeTown' ORDER BY id");
        return $minususers;
    }

    public function getTagWeight($table, $user) {
        parent::init($table);
        $cnt = $this->getRecordCount("WHERE from_user = '$user'");
        return $cnt;
    }

    public function getServiceTagWeight($mood, $tag) {
        parent::init('tbl_tags');
        $cnt = $this->getRecordCount("WHERE module = 'brandmonday' and context = '$mood' and meta_value = '$tag'");
        return $cnt;
    }

    public function getServiceTagWeightWeekly($mood, $tag) {
        parent::init('tbl_tags');
        $cnt = $this->getRecordCount("WHERE module = 'brandmonday' and context = '$mood' and meta_value = '$tag'");
        return $cnt;
    }

    public function getUserMentions() {
        $menusers = $this->getArray("SELECT DISTINCT from_user FROM tbl_bmmentions ORDER BY id");
        return $menusers;
    }
    
    public function getBmTags($mood, $limit = NULL) {
        if($limit == NULL) {
            $sql = "SELECT DISTINCT meta_value FROM tbl_tags WHERE module = 'brandmonday' and context = '$mood'";
        }
        else {
            $sql = "SELECT DISTINCT meta_value FROM tbl_tags WHERE module = 'brandmonday' and context = '$mood' ORDER BY meta_value DESC LIMIT 0, {$limit}";
        }
        $tags = $this->getArray($sql);
        foreach($tags as $recs) {
            if(!in_array(strtolower($recs['meta_value']), $this->excludeList)) {
            //if(strtolower($recs['meta_value']) != 'brandmonday' && strtolower($recs['meta_value']) != 'brandplus' && strtolower($recs['meta_value']) != 'brandminus' && strtolower($recs['meta_value']) != 'fail' && strtolower($recs['meta_value']) != 'wtf' && strtolower($recs['meta_value']) != 'ftw' && strtolower($recs['meta_value']) != 'ftl') {
                $tagarr[] = $recs['meta_value'];
            }
        }
        return $tagarr;
    }

    public function getBmTagsWeekly($table) {
        parent::init($table);
        $time = time();
        $weekago = $time - 604800;
        $tweets = $this->getArray("SELECT tweetid FROM $table WHERE tweettime < $time AND tweettime > $weekago");
        // var_dump($tweets);
        foreach ($tweets as $ids) {
            $tweetid = $ids['tweetid'];
            $sql = "SELECT DISTINCT meta_value FROM tbl_tags WHERE item_id = '$tweetid'"; // AND meta_value != 'brandplus' AND meta_value != 'brandminus' AND meta_value != 'brandmonday' AND meta_value != 'wtf' AND meta_value != 'fail'  AND meta_value != 'ftw'";
            foreach($this->excludeList as $word) {
                $sql .= " AND meta_value != '$word' ";
            }
            $tags[] = $this->getArray($sql); 
        }
        return $tags;
    }

    
    public function parseHashtags($str, $tweetid, $mood)
    {
        $str = stripslashes($str);
        preg_match_all('/\#([a-zA-Z0-9_]{1,15}) ?/', $str, $results);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $memetag = array($item);
            // add the $item to tbl_tags
            $objTags = $this->getObject('dbtags', 'tagging');
            $objTags->insertHashTags($memetag, 1, $tweetid, 'brandmonday', NULL, $mood);
            
            $counter++;
        }

        return $str;
    }

    public function analyseWords($table, $str) {
        $arr = explode(" ", $str);
        parent::init($table);
        foreach($arr as $word) {
            $word = strtolower($word);
            $word = addslashes($word);
            $count = $this->getRecordCount("WHERE word = '$word'");
            $count = intval($count);
            if($count == 0) {
                // add in the word
                $this->insert(array('word' => $word, 'occurances' => 1));
            }
            else {
                $res = $this->getAll("WHERE word = '$word'");
                // var_dump($res);
                $occ = intval($res[0]['occurances']);
                $occ = $occ+1;
                $this->update('id', $res[0]['id'], array('word' => $word, 'occurances' => $occ));
            }
        }
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

    public function getLast($num, $mood) {
        if($mood == 'plus') {
            $table = 'tbl_bmplus';
        }
        else {
            $table = 'tbl_bmminus';
        } 
        return $this->getRange($table, 0, $num);
    }

    public function getLocations() {
        parent::init('tbl_bmlocations');
        return $this->getAll("WHERE location != ', '");
    }
    
    public function getLastByUser($mood) {
        $locs = $this->getLocations();
        
        foreach($locs as $loc) {
            $user = $loc['user'];
            $location = $loc['location'];
            $lastTweet = $this->getLastTweet($user, $mood); 
            if($lastTweet == "") {
                continue;
            }
            $location = explode(",", $location);
            $lat =  trim($location[0]);
            $lon = trim($location[1]);
            $dataArray[] = array('lat' => $lat, 'lon' => $lon, 'tweet' => $lastTweet);
        }
        $markers = $this->tweetMapMarkers($dataArray, $mood);
        
        return $markers;
    }

    public function getLastTweet($user, $mood) {
        $sql = "SELECT tweet FROM tbl_bm".$mood." WHERE from_user = '$user' ORDER BY tweettime desc LIMIT 0, 1";
        $res = $this->getArray($sql);
        if(!empty($res)) {
            return $res[0]['tweet'];
        }
    }
    
    public function getUserCount () {
        $users = $this->getArray("SELECT DISTINCT screen_name FROM tbl_twitterizer ORDER BY id");
        return count($users);
    }

    public function getAllPosts() {
        return $this->getAll("ORDER BY createdat ASC");
    }

    public function getSingle($id) {
        return $this->getAll("WHERE id = '$id'");
    }

    public function searcTable($keyword) {
        return $this->getAll("WHERE tweet LIKE '%%$keyword%%' OR screen_name LIKE '%%$keyword%%' OR name LIKE '%%$keyword%%' ");
    }

}
?>
