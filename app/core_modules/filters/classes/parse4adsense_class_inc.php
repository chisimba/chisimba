<?php

/**
* Class to parse a string (e.g. page content) that contains a
* tag for displaying a Google AdSense ad at the point of the
* tag.
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
* @version   $Id$
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
* Class to parse a string (e.g. page content) that contains a reference
* to a Google AdSesnse filter
* @author Derek Keats
*
*/
class parse4adsense extends object
{
    /**
    *
    * String to hold an error message
    * @accesss private
    */
    private $errorMessage;

   /**
    * language object
    * @access public
    */
    public $objConfig;

   /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    */
    public $objLanguage;

    /**
    *
    * String object $objExpar is a string to hold the parameter extractor object
    * @access public
    *
    */
    public $objExpar;

    /**
     *
     * @var <type>
     */
    public $key;

    /**
     *
     * @var <type>
     */
    public $type;

    /**
     *
     * Constructor for the wikipedia parser
     *
     * @return void
     * @access public
     *
     */
    function init()
    {
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
    }

    /**
    *
    * Method to parse the string
    *
    * The filter takes the format
    * [ADSENSE: key=key, type=type]
    *
    * @param  String $str The string to parse
    * @return The parsed string
    *
    */
    public function parse($txt)
    {
    	// Instantiate the modules class to check if youtube is registered
        $objModule = $this->getObject('modules','modulecatalogue');
        // See if the webpresent API module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('googleadsense', 'gootleadsense');
        if ($isRegistered) {
            // Get an instance of the buildad object
            $objBuildAd = $this->getObject("buildad","googleadsense");
        }
        // Get an instance of the config object
        $objConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $allowPersonalAds = $objConfig->getValue('GOOGLEADSENSE_PERSONAL_ENABLED', 'googleadsense');
    	// Match filters based on a wordpress style
    	preg_match_all('/\\[ADSENSE:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
    	// Get all the ones in links
    	$counter = 0;
        // Loop over the results and extract each ad call
    	foreach ($results[0] as $item) {
            if ($isRegistered) {
                if ($allowPersonalAds == "TRUE") {
                    $this->item=$item;
                    $str = $results[1][$counter];
                    $ar= $this->objExpar->getArrayParams($str, ",");
                    $this->setupPage();
                    if ($this->key !== NULL && $this->key !== "") {
                        $objBuildAd->setKey($this->key);
                        $objBuildAd->setupByType($this->type);
                        $replacement = $objBuildAd->show();
                    } else {
                        $replacement = "<span class='error'>"
                        . $this->objLanguage->languageText('mod_filters_adsensenokey', 'filters')
                        . "</span>";
                    }
                    unset($this->key);
                    unset($this->objExpar->key);
                    unset($this->type);
                    unset($this->objExpar->type);
                } else {
                    // Display error that personal ads are disallowed on this server
                    $replacement = "<span class='error'>"
                        . $this->objLanguage->languageText('mod_filters_adsensenopersonal', 'filters')
                        . "</span>";
                }
            } else {
                // Display the error that the reqiured module is not installed
                $replacement = "<span class='error'>"
                  . $this->objLanguage->languageText('mod_filters_adsensenotinstalled', 'filters')
                  . "</span>";
            }
        	$txt = str_replace($item, $replacement, $txt);
        	$counter++;
    	}

        return $txt;
    }

    /**
     *
     * Method to set up the parameter / value pairs for the filter
     * @access public
     * @return VOID
     *
     */
    public function setUpPage()
    {
        if (isset($this->objExpar->key)) {
            $this->key = $this->objExpar->key;
        } else {
            $this->key=NULL;
        }
        if (isset($this->objExpar->type)) {
            $this->type = $this->objExpar->type;
        } else {
            $this->type="halfbanner";
        }
    }
}
?>