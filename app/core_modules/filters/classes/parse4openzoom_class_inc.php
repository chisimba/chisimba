<?php

/**
 * Class to parse a string (e.g. page content) that contains a request
 * to load a an openzoom image in the form [OPENZOOM:url=url]Caption[/OPENZOOM]
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
 * @version   $Id: parse4feeds_class_inc.php 10361 2008-09-01 12:27:12Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
/**
*
* Class to parse a string (e.g. page content) that contains a request
* to load a RSS feed in the form [RSS]URL[/RSS]
*
* @author Derek Keats
*         
*/

class parse4openzoom extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     */
    public function init()
    {
        // Use this object to check if the feed module is registered.
        $this->objModules = $this->getObject('modules','modulecatalogue');
        // Get the config object.
        $this->objConfig = $this->getObject('altconfig', 'config');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");       
    }

    /**
    *
    * Method to parse the string
    * @param  string $txt The string to parse
    * @return string The parsed string
    *                
    */
    public function parse($txt)
    {
        // Check that the feed module is present and registered, else dont parse the tag
        if (!$this->objModules->checkIfRegistered('openzoom')) {
            return $txt;
        } else {
            $txt = stripslashes($txt);
            // Match filters based on a Chisimba style
            preg_match_all('/(\\[OPENZOOM:?)(.*?)\\](.*?)(\\[\\/OPENZOOM\\])/ism', $txt, $results);
            // Parse the first pattern (RSS)
            $counter = 0;
            foreach ($results[3] as $caption) {
                $this->caption = $caption;
                //Parse for the parameters
                $str = trim($results[2][$counter]);
                $this->objExpar->getArrayParams($str, ",");
                //The whole match must be replaced
                $replaceable = $results[0][$counter];
                
                $this->setupPage();
                $replacement = $this->getZoomImage();
                $txt = str_replace($replaceable, $replacement, $txt);
                $counter++;
            }
            $item=NULL;
            return $txt;
        }
    }
    
    /**
    * 
    * Method to get the zoom image and render it for output
    * 
    * @param  string $url The URL of the image 
    * @return string The rendered image with replacement code.
    *                
    */
    public function getZoomImage()
    {
        if ($this->url==NULL) {
            return $this->caption;
        } else {
            return $this->createZoom();
        }

    }
    
    /**
     * 
     * Method to build the image and insert the caption
     * 
     * @access private
     * @return string The rendered image tag
     */
    private function createZoom() {
        $this->objOpenZoom = $this->getObject('openzoomops', 'openzoom');
        $width = $this->width;
        $height = $this->height;
        $imagePath = $this->url;
        $xmlFile = $this->objOpenZoom->getXmlFileFromUrl($this->url);
        $img = $this->objOpenZoom->getImage($width, $height, $imagePath, $xmlFile);
        $this->objOpenZoom->loadJsLib();
        $ret = $img . "<br />" . $this->caption;
        return $ret;
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
        // Get data from url
        if (isset($this->objExpar->url)) {
            $this->url = $this->objExpar->url;
        } else {
            $this->url = NULL;
        }
        // Get data from width
        if (isset($this->objExpar->width)) {
            $this->width = $this->objExpar->width;
        } else {
            $this->width = '480';
        }
        // Get data from height
        if (isset($this->objExpar->height)) {
            $this->height = $this->objExpar->height;
        } else {
            $this->height = '320';
        }
    }
}
?>
