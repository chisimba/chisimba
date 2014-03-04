<?php
/**
 *
 * jQuery Twitter interface elements
 *
 * Twitter is a module that creates an integration between your Chisimba
 * site using your Twitter account. This class uses jQuery to access Twitter and
 * is developed using Tweet! which enables you to put twitter on your website
 * using javascript and some CSS!, an unobtrusive javascript plugin for jquery.
 *
 * You can get Tweet! at
 *    http://tweet.seaofclouds.com/ and http://seaofclouds.com/
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
* Class to supply a bunch of remote data grabbing methods for the
* module twitter.
*
* @Todo It needs to be re-factored to separate the data
* grabbing from the rendering logic.
*
* @author Derek Keats
* @package twitter
*
*/
class jqtwitter extends object
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
    * @var string $password The twitter password of the authenticating user
    * @access public
    *
    */
    public $password='';
    
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
    * Method to load the newer version of jQuery
    * @access public
    * @return VOID
    *
    */
    public function loadJquery()
    {
        die("DEPRECATED CODE USED: loadJquery");
    }
    
    /**
    *
    * Method to load the CSS 
    * @access public
    * @return VOID
    *
    */
    public function loadTweetCss()
    {
        $script = '<link href="'
          . $this->getResourceUri("jquery.tweet.css", "twitter")
          . '" media="all" rel="stylesheet" type="text/css"/>
          '
          . '<link href="'
          . $this->getResourceUri("jquery.tweet.query.css", "twitter")
          . '" media="all" rel="stylesheet" type="text/css"/>
          ';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

    /**
    *
    * Method to load the jQuery plugin
    * @param string $userName The username of the authenticating user
    * @access public
    * @return VOID
    *
    */
    public function loadTweetPlugin()
    {
        $script = '<script language="javascript" src="'
          . $this->getResourceUri("jquery.tweet.js", "twitter")
          . '" type="text/javascript"></script>';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
        
    }

    /**
    *
    * Method to load script for the jQuery plugin
    * @param string $userName The username of the authenticating user
    * @access public
    * @return VOID
    *
    */
    public function initializeTweetPlugin($userName)
    {
        $this->userName = urlencode($userName);
        $script = $this->wrapInScriptTags($this->loadUser($userName) . $this->loadToUser($userName));
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }


    /**
    *
    * Method to wrap the generated script components in SCRIPT and
    * document.ready function for the jQuery plugin
    * @param string $script all the code for the script to be wrapped
    * @access public
    * @return VOID
    *
    */
    public function wrapInScriptTags($script) {
        $wrappedScript = '<script type=\'text/javascript\'>
        jQuery(document).ready(function(){
            ' . $script
            . '
        });
        </script>';
        return $wrappedScript;
    }
    
    /**
    *
    * Method to load script for the jQuery plugin to show posts to
    * the logged in user.
    * 
    * @param string $userName The username of the authenticating user
    * @access public
    * @return VOID
    *
    */
    public function loadUser($userName)
    {
        $this->userName = urlencode($userName);
        $script ='jQuery(".tweet").tweet({
                join_text: "auto",
                username: "' . $userName . '",
                avatar_size: 32,
                count: 10,
                loading_text: "Loading tweets..."
            });
        ';
        return $script;
    }
    
    /**
    *
    * Method to load script for the jQuery plugin to show posts made to
    * the logged in user.
    * 
    * @param string $userName The username of the authenticating user
    * @access public
    * @return VOID
    *
    */
    public function loadToUser($userName)
    {
        $this->userName = urlencode($userName);
        $script ='jQuery("#touser").tweet({
          avatar_size: 32,
          count: 4,
          query: "to%3A' . $userName . '",
          loading_text: "Loading tweets..."
        });
        ';
        return $script;
    }

    /**
    *
    * Method to load script for the jQuery plugin to show posts
    * for a given query text. The purpose of this method is to
    * allow multiple queries to be generated in the same page.
    * The object and method calling this method should insert
    * a div tag in the page. Where multiple TWEETS will be used
    * the id of the DIV should include a hashtag. See the TWEET
    * filter in the filters module (core_modules) for an example.
    *
    * @param string $queryTxt The text of the given query
    * @param string $queryBase The base code for the qwery to which the number
    *        is appended
    * @param string $queryNumber a generated number from the calling method
    * @access public
    * @return VOID
    *
    */
    public function loadQueryByNumber($queryTxt, $queryBase, $queryNumber,
      $avatarSize=32, $displayCount=10)
    {
        $script ='jQuery("#' . $queryBase . "_"
          . $queryNumber . '").tweet({
          avatar_size: ' . $avatarSize . ',
          count: ' . $displayCount . ',
          query: "' . $queryTxt . '",
          loading_text: "Loading tweets..."
        });
        ';
        return $script;
    }
    
    /**
     * Method to create a CSS based retweet block for module content
     * 
     * This is a simple CSS and jQuery based method to create a retweet and count retweets block
     * 
     * @access public
     * @param $url string URL to retweet
     * @param $status string Status text to tweet
     * @param $style string Style (either retweet vert or NULL for horizontal alignment)
     */
     public function retweetCounter($url, $status, $style = NULL)
     {
         $headjs = $this->getJavascriptFile('retweet.js', 'twitter'); 
         $this->appendArrayVar('headerParams', $headjs);
         if($style == "retweet vert") {
             $retweet = '<a class="retweet vert" href="'.$url.'">'.$status.'</a>';
         }
         else {
             $retweet = '<a class="retweet" href="'.$url.'">'.$status.'</a>';
         }
         
         return $retweet;
     }
}
?>
