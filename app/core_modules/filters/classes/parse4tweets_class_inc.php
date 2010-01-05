<?php
/**
* Class to parse a string (e.g. page content) that contains a filter
* code for including a twitter widget whis can have two formats 
*   [TWEETS: user=username]  or [TWEETS: query=querycodes]
* where querycodes match the Twitter API and can be one of
*   searchphrase (for example chisimba)
*   from: username
*   from:username&phrase=searchphrase
*   to: username
*   to:username&phrase=searchphrase
*   from:username+OR+from:anotherusername
* You can also use [TWEETS: query=querycodes|number=10|avatar=32]
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
* @package   filters
* @author    Derek Keats <dkeats@uwc.ac.za>
* @copyright 2007 Derek Keats
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id: parse4twitter_class_inc.php 12001 2008-12-29 22:44:14Z charlvn $
* @link      http://avoir.uwc.ac.za
*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


/**
 *
 * Class to parse a string (e.g. page content) that contains a filter
 * code for including a twitter widget (usually the latest twitter)
 *
 * @author Derek Keats
 *
 */
class parse4tweets extends object
{

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    
    /**
     *
     * String $username is the username of the twitter user
     * @access public
     *
     */
    public $username;

    /**
    *
    * String $twitterType is the type of display item
    * Valid: name, query
    * @access public
    *
    */
    public $twitterType;

    /**
     *
     * Constructor for the TWITTER filter parser
     *
     * @return void
     * @access public
     *
     */
    public function init()
    {
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Initialise the script property
        $this->script = "";
    }

    /**
    *
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
        //Instantiate the modules class to check if twitter is registered
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the youtube API module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('twitter', 'twitter');
        //Match filters based on a wordpress style
        preg_match_all('/\\[TWEETS:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
        if($isRegistered) {
            $this->jqTwit = $this->getObject("jqtwitter","twitter");
            $this->jqTwit->loadTweetCss();
            $this->jqTwit->loadTweetPlugin();
            $counter = 0;
            foreach ($results[0] as $item) {
                $str = $results[1][$counter];
                $str = $this->extractTweetParams($str);
                $ar = explode("=", $str, 2);
                $twitterType = strtolower(trim($ar[0]));
                $value = urlencode(strtolower(trim($ar[1])));
                $replacement = $this->getTweets($twitterType, $value, $counter);
                $txt = str_replace($item, $replacement, $txt);
                $counter++;
            }
        }
        $this->appendArrayVar('headerParams',
          $this->jqTwit->wrapInScriptTags($this->script));
        // Reset the script property to prevent it from accumulating.
        $this->script="";
        return $txt;
    }


    /**
    * Extract the parameters number and avatarsize from the filter.
    * It sets number and avatarsize as class properties.
    *
    * @param string $str The string to parse
    * @return string The part containing the Twitter query
    * @access private
    *
    */
    private function extractTweetParams($str)
    {
        $ar = explode("|", $str);
        $tweetRequest = array_shift($ar);
        if (count($ar) > 0) {
            foreach ($ar as $entry) {
                $ar2 = explode("=", $entry);
                $key = (string)$ar2[0];
                $value = $ar2[1];
                $this->$key = $value;
            }
        }
        return $tweetRequest;
    }


    /**
    * 
    * Method to retrieve the status from Twitter based on the
    * settings in the filter
    *
    * @param string $type The type of query
    * @param string $value The value associated with the query
    * @access private
    * @return String The formatted twitter status
    * 
    */
    private function getTweets($twitterType, $value, $counter)
    {
        $value = htmlentities($value);
        if (isset ($this->avatarsize)) {
            $avatarSize = $this->avatarsize;
        } else {
            $avatarSize = "32";
        }
        if (isset ($this->number)) {
            $displayCount = $this->number;
        } else {
            $displayCount = "6";
        }
        if ($twitterType == 'user') {
            $hashVal = md5(microtime());
            $this->script .= $this->jqTwit->loadQueryByNumber('from:'
              . $value, 'fromuser', $counter, $avatarSize, $displayCount);
            $str = "<div id='fromuser"  . $hashVal
              . "_" . $counter . "' class='query'></div>";
        } else {
            $hashVal = md5(microtime());
            $this->script .= $this->jqTwit->loadQueryByNumber($value, 'query'
              . $hashVal, $counter, $avatarSize, $displayCount);
            $str = "<div id='query" . $hashVal . "_"
              . $counter . "' class='query'></div>";
        }
        return $str;
    }

}
?>