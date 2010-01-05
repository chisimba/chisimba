<?php

/**
* Class to parse a string (e.g. page content) that contains a filter
* code for including a twitter widget (usually the latest twitter)
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
 * Class to parse a string (e.g. page content) that contains a filter
 * code for including a twitter widget (usually the latest twitter)
 *
 * @author Derek Keats
 *
 */
class parse4twitter extends object
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
     * String object $objExpar is a string to hold the parameter extractor object
     * @access public
     *
     */
    public $objExpar;

    /**
     *
     * String $username is the username of the twitter user
     * @access public
     *
     */
    public $username;

    /**
     *
     * String $password is the password for the twitter user
     * @access public
     *
     */
    public $password;

    /**
     *
     * String $type is the type of display item
     * Valid: text, textime 
     * @access public
     *
     */
    public $type;

    /**
     *
     * String $showimage whether to show the twitter user's image
     * TRUE for show FALSE or leave it out for not showimg image
     * @access public
     *
     */
    public $showimage;


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
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");

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
           preg_match_all('/\\[TWITTER:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
        if($isRegistered) {
            // Create an instance of the twitterremote class
            $this->objTwitterRemote = & $this->getObject('twitterremote', 'twitter');
        }
           //Get all the ones in links
           $counter = 0;
           foreach ($results[0] as $item) {
            $this->item=$item;
            $str = $results[1][$counter];
            $ar= $this->objExpar->getArrayParams($str, ",");
            $this->setupPage();
            if($isRegistered) {
                $replacement = $this->getTweets();
            } else {
                $replacement = $item . "<br /><span class=\"error\">" 
                  . $this->objLanguage->languageText("mod_filters_twitternotinstalled", "filters")
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
    private function setUpPage()
    {
        //Get username
        if (isset($this->objExpar->username)) {
            $this->username = $this->objExpar->username;
        } else {
            $this->username=NULL;
        }
        //password
        if (isset($this->objExpar->password)) {
            $this->password = $this->objExpar->password;
        } else {
            $this->password=NULL;
        }
        //Get type
        if (isset($this->objExpar->type)) {
            $this->type = $this->objExpar->type;
        } else {
            $this->type=NULL;
        }
        //Get showimage
        if (isset($this->objExpar->showimage)) {
            $this->showimage = $this->objExpar->showimage;
        } else {
            $this->showimage=NULL;
        }
    }

    /**
     * 
     * Method to retrieve the status from Twitter based on the
     * settings in the filter
     * @access private
     * @return String The formatted twitter status
     * 
     */
    private function getTweets()
    {
        $this->objTwitterRemote->initializeConnection($this->username, $this->password);
        if (strtoupper($this->showimage) == "TRUE") {
            $showImage=TRUE;
        } else {
            $showImage=FALSE;
        }
        switch ($this->type) {
            case null:
            case "text":
                $str = $this->objTwitterRemote->showStatus(FALSE, $showImage);
                break;
            case "texttime":
                $str = $this->objTwitterRemote->showStatus(TRUE, $showImage);
                break;
            default:
                $str = $this->objTwitterRemote->showStatus(FALSE, $showImage);
        }

        return $str;
    }

}
?>
