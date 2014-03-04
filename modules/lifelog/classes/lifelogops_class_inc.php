<?php
/**
 *
 * lifelog module operations class
 *
 * The lifelog ops clas provides functionality to build the lifelog 
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
 * @package   lifelog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
 *
 * lifelog module operations class
 *
 * The lifelog ops clas provides functionality to build the lifelog 
 *
 * @category  Chisimba
 * @package   lifelog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 */
class lifelogops extends object
{

    public $objCurl;
    public $objUserParams;

    /**
     * Constructor for the lifelogops class 
     *
     * @access public
     */
    public function init() {
        $this->objCurl = $this->getObject('curlwrapper', 'utilities');
        $this->objUserParams = $this->getObject ( 'dbuserparamsadmin', 'userparamsadmin' );
        $this->loadClass('href', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject('user', 'security');
    }
    
    public function getLifeLog($userid) {
        $feeds = $this->grabFeeds();
        $table = $this->formatFeeds($feeds);
        
        return $table;
    }
    
    private function grabFeeds() {
        $feeds = array(
               "blog"      => $this->objUserParams->getValue("myblog"), 
               //"delicious" => $this->objUserParams->getValue("mydelicious"), 
               "flickr"    => $this->objUserParams->getValue("myflickr"), 
               "twitter"   => $this->objUserParams->getValue("mytwitter"),
        );
        
        $details = array("title","link");
        $list = array();
        $rss = new DOMDocument();
        foreach ($feeds as $name => $feed) {
            if(is_null($feed)) {
                continue;
            }
            $feed = $this->objCurl->exec($feed);
            $rss->loadXML($feed);
            $items = $rss -> getElementsByTagName("item");
            foreach ($items as $item) {
                if ($item -> getElementsByTagName("pubDate") -> item(0)) {
                    $date = $item -> getElementsByTagName("pubDate") -> item(0) -> nodeValue;
                } 
                else {
                    $date = $item -> getElementsByTagName("date") -> item(0) -> nodeValue;
                }
                $date = strtotime(substr($date,0,25)); 
                $list[$date]["name"] = $name;
                foreach ($details as $detail) {
                    $list[$date][$detail] = $item -> getElementsByTagName($detail) -> item(0) -> nodeValue;
                }
            }
        }
        krsort($list);
        
        return $list;
    }
    
    private function formatFeeds($list) {
        $str = NULL;
        $day = "";
        $str = '<data date-time-format="iso8601">';
        foreach ($list as $timestamp => $item) {
            // start the event tag
            $str .= "<event ";
            // add in the event details
            $date = date("c", $timestamp); //date('Y-m-d', $timestamp); 
            $title = $item['name'];
            $plink = new href($item['link'], $this->objLanguage->languageText("mod_lifelog_viewitem", "lifelog") , 'target=_blank');
            $image = $item['name']; // use getIcon here to get appropriate thing.
            $str.= 'start="' . $date . '" title="' . $title . '" image="' . $image . '">';
            $str.= htmlentities($item['title'] . "<br />" . $plink->show());
            $str.= "</event>";
        }
        $startdate = date('Y'); //, $timestamp);
        $str.= "</data>";
        // var_dump($str);
        // save the file
        $userid = $this->objUser->userId();
        $filename = $this->objConfig->getcontentBasePath() . "users/" . $userid . '/' . $userid . '_lifelogtimeline.xml';
        file_put_contents($filename, $str);
        chmod($filename, 0777);
        $tlurl = $this->objConfig->getsiteRoot() . $this->objConfig->getcontentPath() . "users/" . $userid . '/' . $userid . '_lifelogtimeline.xml';
        return $this->parseTimeLine("WEEK", $startdate, $tlurl);
        //return array($str, $startdate);  
    }
    
    /**
     * Method to parse the timeline URI data
     *
     * @param  integer $int
     * @param  integer $fdate
     * @param  string  $timeline
     * @return mixed
     */
    public function parseTimeline($int, $fdate, $timeline)
    {
        $objIframe = $this->getObject('iframe', 'htmlelements');
        $objIframe->width = "100%";
        $objIframe->height = "500";
        $ret = $this->uri(array(
            "mode" => "plain",
            "action" => "viewtimeline",
            "timeLine" => $timeline,
            "intervalUnit" => $int,
            "focusDate" => $fdate,
            "tlHeight" => '700'
        ) , "timeline");
        $objIframe->src = $ret;
        return $objIframe->show();
    }
}
?>
