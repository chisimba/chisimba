<?php
/**
 *
 * Google adsense
 *
 * Google AdSense is an advertisement serving program run by Google. Website owners
 * can enroll in this program to enable text, image and, more recently, video advertisements
 * on their sites. These ads are administered by Google and generate revenue on either a
 * per-click or per-thousand-impressions basis. This module provides a means to incorporate
 * Google AdSense advertisements into Chisimba. The module provides AdSense blocks, as well
 * as properties and methods that can be used by developers to add this functionality within
 * a module. The module itself provides no end user functionality, other than a means for
 * the administrator of a site to test that the functionality is working. Data used by this
 * module are stored in parameters accessible via site administration.
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
 * @package   helloforms
 * @author    Derek Keats dkeats@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: buildad_class_inc.php 11947 2008-12-29 21:27:28Z charlvn $
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
* Class to build the Google AdSense ad
*
* @author Derek Keats
* @package Commandshell
*
*/
class buildad extends object
{

    /**
    *
    * Property to hold the user account from adsense
    * @access private
    *
    */
    private $adSenseKey;

    /**
    *
    * Property to hold the width for the ad
    * @access private
    *
    */
    private $adWidth;

    /**
    *
    * Property to hold the height for the ad
    * @access private
    *
    */
    private $adHeight;

    /**
    *
    * Property to hold the whether the module is enabled or not
    * @access private
    *
    */
    private $enabled;

    /**
    *
    * Property to hold the default width for the ad
    * @access private
    *
    */
    private $defaultWidth;

    /**
    *
    * Property to hold the default height for the ad
    * @access private
    *
    */
    private $defaultHeight;

    /**
    *
    * Array property to hold the valid ad types
    * @access private
    *
    */
    private $adTypes=array(
      "leaderboard" => "Leaderboard (728 x 90)",
      "banner" => "Banner (468 x 60)",
      "halfbanner" => "Half Banner (234x60)",
      "button" => "Button (125x125)",
      "skyscraper" => "Skyscraper (120x600)",
      "wideskyscraper" => "Wide Skyscraper (160x600)",
      "smallrectangle" => "Small Rectangle (180x150)",
      "verticalbanner" => "Vertical Banner (120 x 240)",
      "smallsquare" => "Small Square (200 x 200)",
      "square" => "Square (250 x 250)",
      "mediumrectangle" => "Medium Rectangle (300 x 250)",
      "largerectangle" => "Large Rectangle (336 x 280)"
    );

    /**
    *
    * Array property to hold the widths corresponding to the
    * valid ad types
    * @access private
    *
    */
    private $adWidths=array(
      "leaderboard" => 728,
      "banner" => 468,
      "halfbanner" => 234,
      "button" => 125,
      "skyscraper" => 120,
      "wideskyscraper" => 160,
      "smallrectangle" => 180,
      "verticalbanner" => 120,
      "smallsquare" => 200,
      "square" => 250,
      "mediumrectangle" => 300,
      "largerectangle" => 336
    );

    /**
    *
    * Array property to hold the heights corresponding to the
    * valid ad types
    * @access private
    *
    */
    private $adHeights=array(
      "leaderboard" => 90,
      "banner" => 60,
      "halfbanner" => 60,
      "button" => 125,
      "skyscraper" => 600,
      "wideskyscraper" => 600,
      "smallrectangle" => 150,
      "verticalbanner" => 240,
      "smallsquare" => 200,
      "square" => 250,
      "mediumrectangle" => 250,
      "largerectangle" => 280
    );

    /**
    *
    * Array property to hold the categories that the application
    * will recognize as valid
    * @access private
    *
    */
    private $adCategories=array("text", "image", "video");

    /**
    *
    * Intialiser for the buildad class
    *
    * @access public
    *
    */
    public function init()
    {
        //Create an instance of the language object
        $this->objLanguage = & $this->getObject('language', 'language');
        //Get an instance of the config object
        $objConfig = $this->getObject('dbsysconfig', 'sysconfig');
        //Get the value of the site key for AdSense
        $this->enabled = $objConfig->getValue('GOOGLEADSENSE_ENABLED', 'googleadsense');
        $this->adSenseKey = $objConfig->getValue('GOOGLEADSENSE_KEY', 'googleadsense');
        $this->defaultWidth = $objConfig->getValue('GOOGLEADSENSE_DEFAULT_WIDTH', 'googleadsense');
        $this->defaultHeight = $objConfig->getValue('GOOGLEADSENSE_DEFAULT_HEIGHT', 'googleadsense');
    }

    /**
    *
    * Method to set the width of the ad or get it
    * from the adType
    *
    * @param string $width The width of the ad
    * @access public
    * @return TRUE
    *
    */
    public function setWidth($width=NULL)
    {
        if (isset($width) && $width!==NULL) {
            $this->adWidth = $width;
        } else {
            //Get the width from the ad type
            $this->adWidth = $this->defaultWidth;
        }
        return TRUE;
    }

    /**
    *
    * Method to set the height of the ad or get it
    * from the adType
    *
    * @param string $width The width of the ad
    * @access public
    * @return TRUE
    *
    */
    public function setHeight($height)
    {
        if (isset($height) && $height!==NULL) {
            $this->adHeight = $height;
        } else {
            //Get the width from the ad type
            $this->adHeight = $this->defaultHeight;
        }
        return TRUE;
    }

    /**
    *
    * Method to set the height of the key (user account)
    *
    * @param string $width The width of the ad
    * @access public
    * @return TRUE
    *
    */
    public function setKey($key)
    {
        $this->adSenseKey=$key;
        return TRUE;
    }


    public function setupByType($type)
    {
        if ($this->isValidType($type)) {
            $this->adWidth = $this->adWidths[$type];
            $this->adHeight = $this->adHeights[$type];
        } else {
            $this->adWidth = $this->defaultWidth;
            $this->adHeight = $this->defaultHeight;
        }
        $this->adFormat = $this->adWidth . "x" . $this->adHeight . "_as";
    }


    public function isValidType($adType)
    {
        if (array_key_exists($adType, $this->adTypes)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }




    public function show()
    {
        if ($this->enabled=="TRUE") {
            $adSenseCode = '
            <!-- Begin Google Adsense code -->
            <script type="text/javascript"><!--
            google_ad_client = "' . $this->adSenseKey . '";
            google_ad_width = ' . $this->adWidth . ';
            google_ad_height = ' . $this->adHeight . ';
            google_ad_format = "' . $this->adFormat . '";
            google_ad_channel ="";
            //--></script>
            <script type="text/javascript"
              src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
            <!-- End Google Adsense code -->
            ';
            return $this->showHtml($adSenseCode);
        } else {
            return "<span class='error'>"
              . $this->objLanguage->languageText('mod_googleadsense_disabled', 'googleadsense')
              . "</span>";
        }

    }

    function showHtml($txt)
    {
        $ret = "<b>[CODE]</b><br />" . nl2br(htmlentities($txt)) . "<br /><b>[/CODE]</b>";
        $ret = str_replace("    ", "&nbsp;&nbsp;&nbsp;&nbsp;", $ret);
        return $ret;
    }

}
?>