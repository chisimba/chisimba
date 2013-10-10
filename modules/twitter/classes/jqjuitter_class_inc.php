<?php
/**
 *
 * jQuery Juitter interface elements
 *
 * Twitter is a module that creates an integration between your Chisimba
 * site using your Twitter account. This class uses jQuery to access the
 * juitter plugin for jQuery
 *
 * You can get Juitter! at
 *    http://juitter.com/
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
 * @package   twitter
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: twitterremote_class_inc.php 16033 2009-12-23 16:48:15Z charlvn $
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
* jQuery Juitter interface elements
*
* @author Derek Keats
* @package twitter
*
*/
class jqjuitter extends object
{
    /**
    *
    * @var string $userName The twitter username of the authenticating user
    * @access public
    *
    */
    public $userName='';
    
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
    * Constructor for the twitterremote class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
    *
    * Method to load the jQuery plugin
    *
    * @access public
    * @return VOID
    *
    */
    public function loadJuitterPlugin()
    {
        $script = '<script language="javascript" src="'
          . $this->getResourceUri("jquery.juitter.js", "twitter")
          . '" type="text/javascript"></script>';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;

    }

    /**
    *
    * Method to load the juitter default system.js
    *
    * @access public
    * @return VOID
    *
    */
    public function loadJuitterSystem()
    {
        $script = '<script language="javascript" src="'
          . $this->getResourceUri("system.js", "twitter")
          . '" type="text/javascript"></script>';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;

    }

    /**
    *
    * Method to load the CSS 
    * @access public
    * @return VOID
    *
    */
    public function loadJuitterCss()
    {
        $script = '<link href="'
          . $this->getResourceUri("juitter.css", "twitter")
          . '" media="all" rel="stylesheet" type="text/css"/>
          ';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

    /**
    *
    * Method to load the <DIV> tags
    * @param string $userName The username of the authenticating user
    * @access public
    * @return VOID
    *
    */
    public function loadJuitterDiv()
    {
        return "<div id=\"juitterContainer\"></div>";
    }

    /**
    *
    * Method to load the <DIV> tags
    * @param string $userName The username of the authenticating user
    * @access public
    * @return VOID
    *
    */
    public function loadJuitterSearchForm()
    {
        return "<form method=\"post\" id=\"juitterSearc\" action=\"\">\n"
          . "<p>Search Twitter: <input type=\"text\" class=\"juitterSearch\" "
          . "value=\"Type a word and press enter\" /></p>\n</form>";
    }

    public function wrapCode($codeString)
    {
        return "jQuery(document).ready(function() {\n"
          . $codeString
          . "});";
    }

    /**
     *
     * Method to create the jQuery start function.
     *
     * @param string $searchType The type of search, either "searchWord",
     *     "fromUser", "toUser"
     * @param string $searchObject needed, you can insert a username here or
     *     a word to be searched for, if you wish multiple search, separate
     *     the words by comma.
     * @param string $lang restricts the search by the given language (e.g. en)
     * @param string $refreshSeconds  The time in seconds to wait before
     *     requesting the Twitter API for updates.
     * @param string $placeHolder The placeholder div tag for the display
     * @param string $loadMSG The message loading text
     * @param string $total Number of tweets to be show - max 100
     *
     */
    public function createStart($searchType, $searchObject, $lang="en",
      $refreshSeconds="120", $placeHolder="juitterContainer", $total="10")
    {
        // loadMSG - Loading message, if you want to show an image, fill it with "image/gif" and go to the next variable to set which image you want to use on\
        // imgName - Loading image, to enable it, go to the loadMSG var above and change it to "image/gif"
         //readMore - read more message to be show after the tweet content
        // nameUser - insert "image" to show avatar of "text" to show the name of the user that sent the tweet
        // openExternalLinks - here you can choose how to open link to external websites, "newWindow" or "sameWindow"
       // insert the words you want to hide from the tweets followed by what you want to show instead example: "sex->censured" or "porn->BLOCKED WORD" you can define as many as you want, if you don't want to replace the word, simply remove it, just add the words you want separated like this "porn,sex,fuck"... Be aware that the tweets will still be showed, only the bad words will be removed
            $ret = "jQuery.Juitter.start({\n"
              . "searchType:\"$searchType\", \n"
              . "searchObject:\"" . $searchObject . "\",\n"
              . "lang:\"" . $lang . "\", \n"
              . "live:\"live-" . $refreshSeconds . "\",\n"
              . "placeHolder:\"" . $placeHolder . "\", \n"
              . "loadMSG: \"Loading messages...\", \n"
              . "imgName: \"loader.gif\", "
              . "total: " . $total . ", \n"
              . "readMore: \"Read it on Twitter\", \n"
              . "nameUser:\"image\", \n"
              . "openExternalLinks:\"newWindow\", \n"
              . "filter:\"sex->*BAD word*,porn->*BAD word*,fuck->*BAD word*,shit->*BAD word*\" \n"
              . "});";
    }
}
?>