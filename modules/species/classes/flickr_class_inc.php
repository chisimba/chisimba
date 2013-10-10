<?php
/**
 *
 * Access to flickr images for Species
 *
 * Access to flickr images in order to show them in certain locations.
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
 * @package   species
 * @author    Derek Keats derek@localhost.local
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
 * Access to flickr images for Species
 *
 * Access to flickr images in order to show them in certain locations.
*
* @package   species
* @author    Derek Keats derek@localhost.local
*
*/
class flickr extends object
{
    
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
    *
    * Intialiser for the species operations class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Get an instance of the languate object
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     * 
     * Get images by scientific name from Flickr
     * 
     * @param string $scientificName The latin name for the species
     * @return string Formatted and linked images
     * @access public
     */
    public function getImages($scientificName)
    {
        
        $scientificName = str_replace(" ", "+", $scientificName);
        $sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $apiKey = $sysConfig->getValue('api_key', 'flickrshow');
        $baseUri = "http://api.flickr.com/services/rest/?method=flickr.photos.search";
        // Correct for weirdness with honeyguides
        if (strtolower($scientificName) == "indicator+indicator") {
            $scientificName = "Greater+Honeyguide+Indicator+indicator";
        }
        if (strtolower($scientificName) == "indicator+minor") {
            $scientificName = "Lesser+Honeyguide";
        }
        if (strtolower($scientificName) == "indicator+variegatus") {
            $scientificName = "Scaly-throated+Honeyguide";
        }
        
        $resUri = $baseUri 
          . "&api_key=" . $apiKey
          . "&text=" . $scientificName 
          . "&format=json&nojsoncallback=1";
        $obj = json_decode($this->getResults($resUri));
        $res="";
        $c = 0;
        $ar = array();
        if (isset($obj->photos)) {
            foreach ($obj->photos->photo as $photo) {
                $src = "http://farm". $photo->farm . ".static.flickr.com/" 
                  . $photo->server . "/" . $photo->id  . "_" 
                  . $photo->secret  . "_m.jpg";
                $ln = "http://www.flickr.com/photos/" . $photo->owner 
                  . "/" . $photo->id;
                $ar[$c]['src'] = $src;
                $ar[$c]['link'] = $ln;
                if ($c == 1) {
                    return $ar;
                }
                $c++;
            }
        }
        return $ar;
    }

    
    /**
     * 
     * Use curl to retrieve a api page
     * 
     * @param string $uri The URI to retrieve
     * @return string The contents of the rerturned page
     * @access private
     * 
     */
    private function getResults($uri)
    {
        $objCurl = $this->getObject('curlwrapper', 'utilities');
        return $objCurl->exec($uri);
    }

    
}
?>