<?php
/**
* Class to parse a string (e.g. page content) that contains a presentation
* item from the a webpresent module, whether local or remote API 
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
* item from the a webpresent module, whether local or remote API
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
        $this->objConfig = $this->getObject('altconfig', 'config');
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
        if($isRegistered)
        {
        	//Match filters based on a wordpress style
        	preg_match_all('/\\[WPRESENT:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
        	//Get all the ones in links
        	$counter = 0;
        	
        	foreach ($results[0] as $item)
        	{
                $this->item=$item;
            	$str = $results[1][$counter];
            	$ar= $this->objExpar->getArrayParams($str, ",");
                //See what type of call we are making
                if (isset($this->objExpar->type)) {
                    $type = $this->objExpar->type;
                } else {
                    $type="_default";
                }
                switch ($type)
                {
                    case "remote":
                        $this->setUpPage();
                        $replacement = $this->getByApi();
                        break; 
                    //Default if no type specified is an internal page
                    case "_default":
                    default:
                        $this->setupPage();
                        $replacement = $this->getLocalFlash($this->id);
                        break;
                }
            	$txt = str_replace($item, $replacement, $txt);
            	$counter++;
        	}
        }
        return $txt;
    }

    public function setUpPage()
    {
        if (isset($this->objExpar->id)) {
            $this->id = $this->objExpar->id;
        } else {
            $this->id=NULL;
        }
    }
    
    public function getLocalFlash($id)
    {
         $flashFile = $this->objConfig->getcontentBasePath().'webpresent/'. $id .'/' . $id.'.swf';
         if (file_exists($flashFile)) {
             $flashFile = $this->objConfig->getcontentPath().'webpresent/' .$id .'/'. $id.'.swf';
             $flashContent = '
             <div style="border: 1px solid #000; width: 534px; height: 402px; text-align: center;"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="540" height="400">
             <param name="movie" value="'.$flashFile.'">
             <param name="quality" value="high">
             <embed src="'.$flashFile.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="534" height="402"></embed>
            </object></div>';
        } else {
            $flashContent = '<div class="noRecordsMessage" style="border: 1px solid #000; width: 540px; height: 302px; text-align: center;">Flash Version of Presentation being converted</div>';
        }
        return $flashContent;
    }

    public function getByApi()
    {
        return "WORKING HERE";
    }
}
?>