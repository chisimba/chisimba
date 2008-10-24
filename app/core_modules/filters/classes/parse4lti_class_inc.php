<?php

/**
* Class to parse a string (e.g. page content) that contains a presentation
* item from the a webpresent module, whether local, URL or remote API
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
* @version   CVS: $Id: parse4wpresent_class_inc.php 3156 2007-12-12 08:14:16Z kevinc $
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
* Class to parse a string (e.g. page content) that contains a call to 
* A IMS LTI resource
*
* @author Derek Keats
*
*/
class parse4lti extends object
{
   /**
    *
    * String to hold an error message
    * @accesss private
    */
    private $errorMessage;

    /**
     *
     * pointer to the config module
     */
    public $objConfig;

   /**
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    *
    */
    public $objLanguage;

   /**
    *
    * String object $objExpar is a string to hold the parameter extractor object
    * @access public
    */
    public $objExpar;

    /**
     *
     * @var String
     * @access public
     */
    public $secret;

    /**
     *
     * @var String
     * @access public
     */
    public $url;

    /**
     * used to check if module is registered
     * @access public
     */
    public $isRegistered;

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
        //Instantiate the modules class to check if IMSLTI is registered
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the IMSLTI module is registered and set a param
        $this->isRegistered = $objModule->checkIfRegistered('imslti', 'imslti');
        if($this->isRegistered){
            //Set up the XML message object
            $this->objMsg = $this->getObject("ltixmlmsg", "imslti");
            //Set up the fetcher class
            $this->objFetcher = $this->getObject("ltifetcher", "imslti");
            //Set upt the wrapper
            $this->objWrapper = $this->getObject("ltiwrapper", "imslti");
        }
    }

    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The parsed string
    *
    */
    public function parse($txt)
    {
    	//Match filters based on a wordpress style
    	preg_match_all('/\\[LTI:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
    	//Get all the ones in links
    	$counter = 0;
    	foreach ($results[0] as $item) {
            $this->item=$item;
            $str = $results[1][$counter];
            //See what type of call we are making
            $ar= $this->objExpar->getArrayParams($str, ",");
            $this->setUpPage();
            if($this->isRegistered){
                //Deal with the type (currently only rest)
                switch ($this->type)
                {
                    case "rest":
                        $replacement = $this->showLtiFrame();
                        break;
                    //Default if no type specified is an internal page
                    case "_default":
                    default:
                        $replacement = $item;
                        break;
                }
            } else {
                $replacement = "[LTI: type=" . $this->type
                  . ", url=" . $this->url
                  . ", secret=XXXXXX]<br />"
                  . "<span class=\"error\">"
                  . $this->objLanguage->languageText("mod_filters_error_ltinotinstalled", "filters")
                  . "</span>";
            }
        	$txt = str_replace($item, $replacement, $txt);
        	$counter++;
            //Clear the set params
            unset($this->id);
            unset($this->objExpar->secret);
            unset($this->url);
            unset($this->objExpar->url);
            unset($this->type);
            unset($this->objExpar->type);
    	}

        return $txt;
    }

    /**
     *
     * Method to set up the parameter / value pairs for th efilter
     * @access public
     * @return VOID
     *
     */
    public function setUpPage()
    {
        if (isset($this->objExpar->secret)) {
            $this->secret = $this->objExpar->secret;
            if($this->isRegistered){
                $this->objFetcher->set("secret", $this->secret);
            }
        } else {
            $this->secret=NULL;
        }
        if (isset($this->objExpar->url)) {
            $this->url = urldecode($this->objExpar->url);
        } else {
            $this->url=NULL;
        }
        if (isset($this->objExpar->type)) {
            $this->type = $this->objExpar->type;
        } else {
            $this->type="rest";
        }
    }

    /**
     *
     * A method to return the flash presentation for rendering in the page
     * @param string $uri The URL of the flash file to show
     * @return string the flash file rendered for viewing within a div
     * @access public
     *
     */
    public function showLtiFrame()
    {
         $myXml = $this->objMsg->show();
         $this->objFetcher->set("xmlPacket", $myXml);
         $gotBack = $this->objFetcher->getUrl($this->url);

         return $this->objWrapper->show($gotBack);
         
    }

}
?>