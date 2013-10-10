<?php
/**
 *
 * jQuery real-time tweets interface
 *
 * Twitter is a module that creates an integration between your Chisimba
 * site using your Twitter account. This class uses jQuery rea;-time tweets
 * plugin to add a real-time tweets bar related to your posts from your twitter
 * timeline.
 *
 * You can get real-time tweets at
 *    http://www.moretechtips.net/2009/09/realtime-related-tweets-bar-another.html
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
* Class to use the jQuery real-time tweets interface
*
* @author Derek Keats
* @package twitter
*
*/
class jqrtt extends object
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
    * @var string $jqTwitter String object property for holding the
    * jqTwitter object
    * @access public
    *
    */
    public $jqTwitter;

    /**
    *
    * Constructor for the jqrtt class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->jqTwitter = $this->getObject('jqtwitter', 'twitter');
        $this->jqTwitter->loadTweetCss();
    }


    /**
    *
    * Method to load the jQuery plugin fpr real-time tweet
    * @access public
    * @return VOID
    *
    */
    public function loadRttPlugin()
    {
        $script = '<script language="javascript" src="'
          . $this->getResourceUri("jquery.relatedtweets-1.0.min.js", "twitter")
          . '" type="text/javascript"></script>';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }
    
    /**
    *
    * Method to load the jQuery plugin fpr real-time tweet
    * @access public
    * @return VOID
    *
    */
    public function loadRelatedDiv()
    {
        return '<div class="related-tweets">Loading tweets...</div>';
    }

    public function loadNearbyDiv($latitude, $longitude, $distance='25km')
    {
        $this->setDivClass("nearby-tweets");
        return "<div class=\"nearby-tweets\" options=\"{\n"
          . "debug:true,geocode:'" . $latitude . "," . $longitude
          . "," . $distance . "'}\">Loading tweets...</div>";
    }

    public function setDivClass($divClass)
    {
        $script = "<script type=\"text/javascript\">\n"
          . "jQuery(document).ready(function(){\n"
          . "jQuery('#" . $divClass . "').relatedTweets({\n"
          . "debug:true\n"
          . "});\n});\n</script>\n\n";
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }
}
?>