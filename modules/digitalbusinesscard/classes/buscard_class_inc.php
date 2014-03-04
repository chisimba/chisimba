<?php
/**
 *
 * Generate outputs for the business cards
 *
 * Create a digital business card that uses microformats and web standards,
 * and display it in a nice, CSS based layout. This class generates output
 * in microformat.
 *
 * @todo Add privacy settings
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
 * @package   oembed
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: imageprovider_class_inc.php 1 2010-01-01 16:48:15Z dkeats $
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
* Generate outputs for the business cards
*
* Create a digital business card that uses microformats and web standards,
* and display it in a nice, CSS based layout. This class generates output
* in microformat.
*
* @author Derek Keats
* @package oembed
*
*/
class buscard extends object
{

    /**
    *
    * This is a hardcoded array of the known social network providers
    * that will be supported by having Icons stored in this module
    *
    * @var array
    * @access public
    *
    */
    public $networks = array ('africator', 'delicious', 'digg', 'facebook',
        'flickr', 'friendfeed', 'google', 'identica', 'linkedin', 'muti',
        'opera', 'picasa', 'qik', 'slideshare', 'technorati', 'twitter',
        'youtube' );

    public $mapApiKey = "ABQIAAAASzlWuBpqyHQoPD8OwyyFRhT2yXp_ZAY8_ufC3CFXhHIE1NvwkxSWc071UMpm8NTrMcdPC-cIwpN4VA";


    /**
    *
    * Constructor for the provider class
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Get an instance of the user object to look up the file owner.
        $this->objUser = $this->getObject('user', 'security');
        // Get an instance of the userparams object to look up additional info.
        $this->objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
        // Get an instance of the language object.
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUserParams->readConfig();
        // Load the style sheet
        $this->loadCss();
    }

    /**
    *
    * Default show method to show the Digital Business card
    *
    * @param string $userId The userid of the user
    * @return string The rendered hcard
    * @access public
    *
    */
    public function show($userId)
    {
        if ($this->readParams($userId)) {
            $ret = $this->getContact($userId);
            foreach ($this->networks as $network) {
                $ret .= $this->getSocialNetwork($network, $userId);
            }
            $ret .= "<a rel=\"profile\" "
              . "href=\"http://microformats.org/profile/hcard\">"
              . $this->getLinkIcon('mf_hcard') . "</a>";
            $ret = $this->addToLeftCol($ret);
            $ret .= $this->addToRightCol($this->getLatLong($userId));
            // Start rendering.
            $ret = $this->addToVcard($ret);
            unset($this->objUserParams);
            return $this->addToOuterContainer($ret);
        } else {
            unset($this->objUserParams);
            return $this->objLanguage->languageText(
              'mod_digitalbusinesscard_usernotfound',
              'digitalbusinesscard'
            );
        }
    }

    /**
    *
    * Show the Digital Business card in a tabbed pane view
    *
    * @param string $userId The userid of the user
    * @return string The rendered hcard
    * @access public
    *
    */
    public function showTabbed($userId)
    {
        if ($this->readParams($userId)) {
            // Get the Map tab content.
            $mapTab = $this->getLatLong($userId);
            // Get the social networks tab content.
            $socialTab = "";
            foreach ($this->networks as $network) {
                $socialTab .= $this->getSocialNetwork($network, $userId);
            }
            // Get the contact info tab content.
            $contactTab = $this->getContact($userId, TRUE) . "<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;";
            $objTab = $this->newObject('tabpane', 'htmlelements');
            $objTab->addTab(array('name'=>' Contact ','url'=>'http://localhost','content' => $contactTab,'nested' => FALSE),'webfx-tab-style-sheet');
            $objTab->addTab(array('name'=>' Social networks ','url'=>'http://localhost','content' => $socialTab,'nested' => FALSE),'webfx-tab-style-sheet');
            if ($mapTab !== NULL) {
                $objTab->addTab(array('name'=>' Map ','url'=>'http://localhost','content' => $mapTab,'nested' => FALSE),'webfx-tab-style-sheet');
            }
            $tags = $this->getTags();
            if ($tags) {
                $objTab->addTab(array('name'=>' My tags ','url'=>'http://localhost','content' => $tags,'nested' => FALSE),'webfx-tab-style-sheet');
            }
            unset($this->objUserParams);
            return $objTab->show();
        } else {
            unset($this->objUserParams);
            return $this->objLanguage->languageText(
              'mod_digitalbusinesscard_usernotfound',
              'digitalbusinesscard'
            );
        }
    }

    /**
    *
    * Get the contact info for the hcard
    *
    * @param string $userId The userid of the user
    * @return string The rendered contact info
    * @access private
    *
    */
    public function getContact($userId, $useTable=FALSE)
    {
            $userImage = $this->getUserImage($userId);
            $fn = $this->getFn();
            $ret = "";
            if ($useTable) {
                $ret .= "<table class=\"vcard-contact\"><tr><td valign='top'>";
            }
            $ret .= "<table><tr><td>$userImage</td><td>$fn</td></tr></table>";
            $ret .= $this->addToTextInfo($this->getInfo('tagline'));
            $ret .= $this->getPhones();
            if ($useTable) {
                $ret .= "</td><td valign='top'>";
            }
            // Put all the addr stuff here.
            $this->extractAddress();
            $addr="";
            if ($this->hasHome) {
                $addr .= $this->homeAddr . "<br />";
            }
            if ($this->hasWork) {
                $addr .= $this->workAddr . "<br />";
            }
            $ret .= $this->addToAddress($addr
              . $this->getCountry());
            if ($useTable) {
                $ret .= "</td><td valign='top'>";
            }
            // End of addr stuff.
            $ret .= $this->getEmail();
            $ret .= $this->getHomePage($userId);
            if ($useTable) {
                $ret .= "</td></tr></table>";
            }
            return $ret;
    }

    /**
    *
    * Read the user parameters, which we should only have to do once
    * in a particular instance.
    *
    * @param string $userId The userid to read
    * @return boolean TRUE | FALSE True if read, false otherwise
    * @access private
    *
    */
    private function readParams($userId)
    {
        if ($this->objUser->isActive($userId)) {
            // Read all the properties at once
            $this->setUserProperties($userId);
            $this->objUserParams->setUserId($userId);
            $this->objUserParams->readConfig();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Show in text area so user can copy their own hcard and use
    * in a static way somewhere.
    *
    * @param string $userId The userId of the user
    * @return string The text area with the vcard
    *
    */
    public function showInTextbox($userId)
    {
        return '<textarea name="hcard" rows="20" cols="60">'
          . $this->show($userId) . '</textarea>';
    }

    /**
    *
    * Set class properties for all the userdetails obtained by the 
    * getUserDetails method.
    *
    * @param string $userId The userid to base it on
    * @return VOID
    * @access private
    *
    */
    private function setUserProperties($userId)
    {
        $userDetails = $this->objUser->getUserDetails($userId);
        if (count($userDetails) > 0) {
            foreach ($userDetails as $property=>$value) {
                $this->$property = $value;
            }
        }
    }

    /**
    *
    * Method to show the Digital Business cards in
    * a block
    *
    * @param string $userId The userid of the user to lookup
    * @return string The rendered business card
    * @access public
    *
    */
    public function showBlock($userId)
    {
        $ret = $this->getUserImage($userId);
        $ret .= $this->getFn($userId);
        $ret .= $this->getEmail($userId);
        $ret = "\n\n<div class='vcard'>\n"
          . $ret . "</div>\n\n"
          . '</div>';
        $ret .= $this->getHomePage($userId, TRUE);
        foreach ($this->networks as $network) {
            $ret .= $this->getSocialNetwork($network, $userId, TRUE);
        }
        $ret .= $this->getLatLong($userId, FALSE);
        return $this->addToOuterContainer($ret);
    }

    /**
    *
    * Method to show the Digital Business cars in
    * JSON format
    *
    * @param string $userId The userid of the user to lookup
    * @return string The rendered business card
    * @access public
    *
    */
    public function showJson($userId, $includeHtml=FALSE)
    {
        $networkAr = array();
        foreach ($this->networks as $network) {
            $identifier = $network . 'url';
            if ($item = $this->objUserParams->getValue($identifier)) {
                $networkAr[$network] = $item;
            }
        }
        $ar = array(
            'given_name' => $this->objUser->getFirstname($userId),
            'family_name' => $this->objUser->getSurName($userId),
            'email' => $this->objUser->email($userId),
            'latitude' => $this->objUserParams->getValue("latitude"),
            'longitude' => $this->objUserParams->getValue("longitude"),
            'urls' => $networkAr );
        return json_encode($ar);
    }

    /**
    *
    * Add the content to an outer DIV layer
    *
    * @param string $ret The content to add to the layer
    * @return string The content inside the layer tags
    * @access private
    *
    */
    private function addToOuterContainer($ret)
    {
        return "<div class='hcardOuter'>$ret</div>";
    }

    /**
    *
    * Add the content to an vcard layer
    *
    * @param string $ret The content to add to the layer
    * @return string The content inside the layer tags
    * @access private
    *
    */
    private function addToVcard($ret)
    {
        return "<div class='vcard'>$ret</div>";
    }

    /**
    *
    * Add the content to an addr vcard layer
    *
    * @param string $addr The content to add to the layer
    * @return string The content inside the layer tags
    * @access private
    *
    */
    private function addToAddress($addr)
    {
        return "<span class=\"vcard-addr\"><div class=\"adr\">$addr</div></span>";
    }

    /**
    *
    * Add the content to an floated left layer
    *
    * @param string $ret The content to add to the layer
    * @return string The content inside the layer tags
    * @access private
    *
    */
    private function addToLeftCol($ret)
    {
        return "<div class='vcard_left'>$ret</div>";
    }

    /**
    *
    * Add the content to an floated right layer
    *
    * @param string $ret The content to add to the layer
    * @return string The content inside the layer tags
    * @access private
    *
    */
    private function addToRightCol($ret)
    {
        return "<div class='vcard_right'>$ret</div>";
    }

    /**
    *
    * Add the content to an limited width textinfo span
    *
    * @param string $ret The content to add to the span
    * @return string The content inside the layer tags
    * @access private
    *
    */
    private function addToTextInfo($ret)
    {
        return "<div class='vcard-textinfo'>$ret</div>";
    }

    /**
    *
    * Get the full name of the user and render in in hcard format
    *
    * @param string $userId The userid of the user to look up
    * @return string The rendered full name
    * @access private
    *
    */
    private function getFn()
    {
        $givenName = '<span class="vcard-name-wrapper"><span class="given-name">'
          . $this->firstname . '</span>';
        $surName = '<span class="family-name">'
          . $this->surname . '</span></span>';
        return '<span class="fn n">'
          . $givenName . ' '
          . $surName . '</span><br />'
          . "\n\n";
    }

    /**
    *
    * Get the email address of the user and render in in hcard format
    *
    * @param string $userId The userid of the user to look up
    * @param boolean $noText TRUE|FALSE whether to return text, default yes
    * @return string The rendered email
    * @access private
    *
    */
    private function getEmail($noText=FALSE)
    {
        $email = $this->emailaddress;
        $icon = $this->getLinkIcon("email");
        if ($noText) {
            return "<a class='email' href='mailto:$email'>$icon</a><br />\n";
        } else {
            return "<a class='email' href='mailto:$email'>$icon $email</a><br />\n";
        }
    }

    /**
    *
    * Get the country of the user and render in in hcard format
    *
    * @param boolean $noText TRUE|FALSE whether to return text, default yes
    * @return string The rendered country with flag
    * @access private
    *
    */
    private function getCountry($noText=FALSE)
    {
        // Use this to get the country flag
        $objCountries = $this->getObject('countries', 'utilities');
        $countryName = $objCountries->getCountryName($this->country);
        $countryFlag = $objCountries->getCountryFlag($this->country);
        $ret = "<div class=\"country-name\">$countryName</div>";
        return "<table><tr><td> $countryFlag</td><td>$ret</td></tr></table>";
    }

    /**
    *
    * Get the social network and return it with linked icon
    * and optionally with or without text
    *
    * @param string $network The social network from the array of networks
    * @param string $userId The userid of the person to look up
    * @param boolean $noText TRUE|FALSE whether to return text, default yes
    * @return string The rendered icon/text
    * @access public
    *
    */
    public function getSocialNetwork($network, $userId, $noText=FALSE)
    {
        $identifier = $network . "url";
        if ($url = $this->objUserParams->getValue($identifier)) {
            $icon = $this->getLinkIcon($network);
            if ($noText) {
                return "<a class='url' rel='me' href='$url' "
                 . "target='_blank'>$icon</a><br />\n";
            } else {
                return "<a class='url' rel='me' href='$url' "
                  . "target='_blank'>$icon $url</a><br />\n";
            }
        }
    }

    /**
    *
    * Retrieve the stored phone numbers
    *
    * @return string The rendered phone numbers
    *
    */
    private function getPhones()
    {
        $home = $this->objUserParams->getValue("phone_home");
        $work = $this->objUserParams->getValue("phone_work");
        $mobile = $this->objUserParams->getValue("phone_mobile");
        $ret ="";
        if ($home || $work || $mobile) {
            $ret = '<span class="vcard-tel"><span class="vtel">';
            if ($home) {
                $ret .= "<span class=\"type\">home</span>: "
                  . "<span class=\"value\">$home</span><br />";
            }
            if ($work) {
                $ret .= "<span class=\"type\">work</span>: "
                  . "<span class=\"value\">$work</span><br />";
            }
            if ($mobile) {
                $ret .= "<span class=\"type\">cell</span>: "
                  . "<span class=\"value\">$mobile</span><br />";
            }
            $ret .= "</span></span>";
        }
        return $ret;
    }

    /**
    *
    * Get tags stored in the format tag1-tag2-tag3
    *
    * @return string The rendered tags or boolean FALSE if no tags found
    *
    */
    private function getTags()
    {
        $tags = $this->objUserParams->getValue("tags");
        if ($tags) {
            $tagsAr = explode("-", $tags);
            $ret = "";
            $terms="";
            $tagNo = count($tagsAr);
            $counter = 1;
            foreach ($tagsAr as $tag) {
                if ($counter == $tagNo) {
                    $terms .= $tag;
                } else {
                    $terms .= $tag . " OR ";
                }
                $ret .= $this->relTag($tag);
                $counter ++;
            }
            $terms = $this->widgetize("terms: $terms");
            return "<center>$terms <br /> $ret</center";
        } else {
            return FALSE;
        }
    }


    public function relTag($tag)
    {
        $ret = "<a href=\"http://widget.collecta.com/widget.html?query=term:$tag\" target=\"widgetframe\" rel=\"tag\">$tag</a> ";
        return $ret;
    }

    public function widgetize($terms) {
        $collecta = "";
        $title ="My favorite tags";
        $widget = '<iframe style="border: medium none ; overflow: hidden; width:640px; height:480px;"
                  src="http://widget.collecta.com/widget.html?query='.urlencode($terms).'&alias='.$title.'&
                  headerimg=&stylesheet=&delay=" id="widgetframe" frameborder="0" scrolling="no">
                  </iframe>';
        /*$widget = "<iframe style=\"border: medium none ; overflow: hidden; width: 600px; height: 400px;\""
           . "src=\"http://widget.collecta.com/widget.html?query="
          . "$terms&alias=$title&headerimg=&stylesheet=&delay= "
          . "id=\"widgetframe\" frameborder=\"0\" scrolling=\"no\"></iframe>";*/

        return $widget;
    }

    /**
    *
    * Extract the addresses and set class properties to correspond to
    * them.
    *
    * @return TRUE
    * @access private
    *
    */
    private function extractAddress()
    {
        $validAddr = array('addr_home', 'addr_work',
          'addr_city_home', 'addr_city_work',
          'addr_postalcode_home', 'addr_postalcode_work');
        foreach ($validAddr as $addrItem) {
            $this->$addrItem = $this->getInfo($addrItem);
        }
        $this->hasHome = FALSE;
        $this->hasWork = FALSE;
        // Note that the hard coded english is due to the specification
        if ($this->addr_work && $this->addr_city_work
          && $this->addr_postalcode_work) {
            $this->addr_work = str_replace("--", "<br />", $this->addr_work);
            $this->workAddr = "<span class=\"type\"><em>Work</em></span>:"
            . "<div class=\"street-address\">$this->addr_work</div>"
            . "<span class=\"locality\">$this->addr_city_work</span>&nbsp;&nbsp;"
            . "<span class=\"postal-code\">$this->addr_postalcode_work</span>";
            $this->hasWork = TRUE;
        }
        if ($this->addr_home && $this->addr_city_home
          && $this->addr_postalcode_home) {
            $this->addr_home = str_replace("--", "<br />", $this->addr_home);
            $this->homeAddr = "<span class=\"type\"><em>Home</em>:</span>:"
              . "<div class=\"street-address\">$this->addr_home</div>"
              . "<span class=\"locality\">$this->addr_city_home</span>&nbsp;&nbsp;"
              . "<span class=\"postal-code\">$this->addr_postalcode_home</span>";
            $this->hasHome = TRUE;
        }
        return TRUE;
    }



    /**
    *
    * Get the home page of the user
    *
    * @param string $userId The userid of the person to look up
    * @param boolean $noText TRUE|FALSE whether to return text, default yes
    * @return string The rendered icon/text
    * @access public
    *
    */
    public function getHomePage($userId, $noText=FALSE)
    {
        if ($url = $this->objUserParams->getValue("homepage")) {
            $icon = $this->getLinkIcon("home");
            if ($noText) {
                return "<a class='url' rel='me' href='$url' "
                 . "target='_blank'>$icon</a><br />\n";
            } else {
                return "<a class='url' rel='me' href='$url' "
                  . "target='_blank'>$icon $url</a><br />\n";
            }
        }
    }

    /**
    *
    * Method to get an icon for a particular link from the resources/icons
    * directory in this module. The file $network.png must exist.
    *
    * @param string $network The network icon to look up
    * @return string The rendered icon
    * @access private
    *
    */
    private function getLinkIcon($network)
    {
        $img = $this->getResourceUri("icons/$network.png", "digitalbusinesscard");
        return "<img style='border: 0px none; vertical-align:middle' class='snicon' src='$img' /> ";
    }

    /**
    *
    * Load the CSS required for some of the extended
    * functionality and layout
    *
    * @return VOID
    * @access private
    *
    */
    private function loadCss()
    {
        $css = "<link rel=\"stylesheet\" type=\"text/css\" href=\""
          . $this->getResourceUri("css/vcard.css", "digitalbusinesscard")
          . "\" />";
        $this->appendArrayVar('headerParams', $css);
    }

    /**
    *
    * Get the latitude and longitude of the user and return it in hcard format
    * while optionally rendering a google map
    *
    * @param string $userId The userid of the user to lookup
    * @param boolean $showMap Whether or not to show the map, default TRUE
    * @return string The rendered output
    * @access public
    *
    */
    public function getLatLong($userId, $showMap=TRUE)
    {
        $latitude = $this->objUserParams->getValue("latitude");
        $longitude = $this->objUserParams->getValue("longitude");
        if ($latitude && $longitude) {
            $ret = '<span class="geo">'
              . '<abbr class="latitude" title="' . $latitude
              . '">' . $latitude . "</abbr>\n"
              .  '<abbr class="longitude" title="'
              . $longitude . '">' . $longitude . "</abbr>\n"
              . "</span>\n";
            $ret = $this->getLinkIcon("earth") . $ret;
            if ($showMap) {
                $ret .= $this->getMap($latitude, $longitude);
            }
            return $ret;
        }
    }

    /**
    *
    * Method to render a simple google map
    *
    * @param string $latitude Latitude of user
    * @param string $longitude Longitude of user
    * @return string The rendered map
    * @access private
    *
    */
    private function getMap($latitude, $longitude)
    {
        $ret = '<br /><div class="vcard_map">'
          . '<iframe width="512" height="512" '
          . 'frameborder="0" scrolling="no" '
          . 'marginheight="0" marginwidth="0" '
          . 'src="http://maps.google.com/maps/api/staticmap?center='
          . $latitude .',' . $longitude 
          . '&zoom=17&size=512x512&maptype=hybrid'
          . '&markers=color:red|' . $latitude .','
          . $longitude . '&sensor=false&key=' . $this->mapApiKey
          . '"></iframe></div>';
        return $ret;
    }

    private function insertMap($latitude, $longitude)
    {
          $ret = "<iframe width=\"425\" height=\"350\" frameborder=\"0\" "
            . "scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" "
            . "src=\"http://maps.google.com/maps?f=q&amp;source=s_q&amp;"
            . "hl=en&amp;geocode=&amp;q=$latitude,$longitude&amp;"
            . "sll=$latitude,$longitude&amp;ie=UTF8&amp;ll=$latitude,$longitude&amp;spn=0.00982,0.020857&amp;t=h&amp;z=16&amp;output=embed\"></iframe><br /><small><a href=\"http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=-26.193161,+28.030544&amp;sll=37.0625,-95.677068&amp;sspn=34.259599,68.90625&amp;ie=UTF8&amp;ll=-26.19288,28.03038&amp;spn=0.00982,0.020857&amp;t=h&amp;z=16\" style=\"color:#0000FF;text-align:left\">View Larger Map</a></small>";
    }

    /**
    *
    * Return a rendered icon-sized image of the user
    *
    * @param string $userId The userid of the user to lookup
    * @return string the rendered image
    * @access private
    * 
    */
    private function getUserImage($userId)
    {
        return $this->objUser->getSmallUserImage($userId);
    }

    /**
    * Get any user information stored in userparams using the param code
    *
    * @param string $param The parameter to look up
    * @return string The value of the parameter
    *
    */
    private function getInfo($param)
    {
        if ($ret = $this->objUserParams->getValue($param)) {
            return $ret;
        }
    }
}
?>
