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
* @version   CVS: $Id$
* @link      http://avoir.uwc.ac.za
*/



/**
*
* Class to parse a string (e.g. page content) that contains a presentation
* item from the a webpresent module, whether local, URL or remote API
*
* @author Derek Keats
*
*/
class parse4wpresent extends object
{
	/**
	*
	* String to hold an error message
	* @accesss private
	*/
	private $errorMessage;
    public $objConfig;
    public $objLanguage;
    public $objExpar;
    public $id;
    public $url;

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
        // Load the XML_RPC PEAR Class
        require_once($this->getPearResource('XML/RPC/Server.php'));
       // $this->objConfig = $this->getObject('altconfig', 'config');
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
    	//Instantiate the modules class to check if youtube is registered
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the webpresent API module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('webpresent', 'webpresent');
        // Get the viewer object.
        $objView = $this->getObject("viewer", "webpresent");
    	//Match filters based on a wordpress style
    	preg_match_all('/\\[WPRESENT:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
    	//Get all the ones in links
    	$counter = 0;

    	foreach ($results[0] as $item) {
            $this->item=$item;
        	$str = $results[1][$counter];
        	$ar= $this->objExpar->getArrayParams($str, ",");
            $this->setupPage();
            //See what type of call we are making
            switch ($this->type)
            {
                case "remote":
                    $this->setUpPage();
                    $replacement = $this->getByApi();
                    break;
                case "byurl":
                    $replacement = $this->showFlashUrl($this->url);
                    break;
                //Default if no type specified is an internal page
                case "_default":
                default:
                    if($isRegistered) {
                        $replacement = $objView->showFlash($this->id);
                    } else {
                        $replacement = $item;
                    }
                    break;
            }
        	$txt = str_replace($item, $replacement, $txt);
        	$counter++;
            //Clear the set params
            unset($this->id);
            unset($this->objExpar->id);
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
        if (isset($this->objExpar->id)) {
            $this->id = $this->objExpar->id;
        } else {
            $this->id=NULL;
        }
        if (isset($this->objExpar->url)) {
            $this->url = $this->objExpar->url;
        } else {
            $this->url=NULL;
        }
        if (isset($this->objExpar->type)) {
            $this->type = $this->objExpar->type;
        } else {
            $this->type="_default";
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
    public function showFlashUrl($uri)
    {
         $flashFile = $uri;
         $flashContent = '
           <div style="border: 1px solid #000; width: 534px; height: 402px; text-align: center;"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="540" height="400">
           <param name="movie" value="'.$flashFile.'">
           <param name="quality" value="high">
           <embed src="'.$flashFile.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="534" height="402"></embed>
          </object></div>';
        return $flashContent;
    }


    public function getByApi()
    {
        return "WORKING HERE";
    }
}
?>