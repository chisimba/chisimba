<?php
/**
 *
 * Access to encyclopedia of life for Species
 *
 * Access to encyclopedia of life in order to access species data via
 * the API and scrapings.
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
 * Access to encyclopedia of life for Species
 *
 * Access to encyclopedia of life in order to access species data via
 * the API and scrapings.
*
* @package   species
* @author    Derek Keats derek@localhost.local
*
*/
class eol extends object
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
     * Get a sound file for this species via the EOL API
     * 
     * @param string $latinName The latin name of the species to lookup
     * @return string The embedded sound file with player
     * @access public
     * 
     */
    public function getSound($latinName)
    {
        $sounds =  $this->getSoundData($latinName);
        if (is_array($sounds)) {
            return $this->renderSounds($sounds);
        } else {
            return $sounds;
        }
    }
    
    /**
     * 
     * Get a sound file for this species via the EOL API
     * 
     * @param string $latinName The latin name of the species to lookup
     * @return string The embedded sound file with player
     * @access public
     * 
     */
    public function getImgs($latinName)
    {
        $images =  $this->getImageData($latinName, 2);
        //echo "<pre>";
        //print_r($images);
        //echo("</pre>");
        if (is_array($images)) {
            return $this->renderImages($images);
        } else {
            return $images;
        }
    }
    
    /**
     * 
     * Get a sound file for this species via the EOL API
     * 
     * @param string $latinName The latin name of the species to lookup
     * @return string The embedded sound file with player
     * @access public
     * 
     */
    public function getSoundData($latinName, $records='5')
    {
        $obj = $this->jsonSearch($latinName);
        $id = $obj->results[0]->id;
        $uri = $this->buildUri($id, 'sound', 5);
        $page = $this->getResults($uri);
        $obj = json_decode($page);
        $ret = array();
        // Check for at least one entry, then proceed.
        if (isset($obj->dataObjects[0])) {
            $count = 0;
            foreach ($obj->dataObjects as $dataObject) {
                if (isset($dataObject->mediaURL)) {
                    $url = $dataObject->mediaURL;
                    $licenseUrl = $dataObject->license;
                    $licenseCode = $this->extractLicense($licenseUrl);
                    $licenseIsFree = $this->isFree($licenseCode);
                    $isTrusted = $this->isTrusted($dataObject->vettedStatus);
                    if ($this->isMp3($url)) {

                        $ret[$count]['uri'] =  $url;
                        $ret[$count]['licenseurl'] =  $licenseUrl;
                        $ret[$count]['licensecode'] =  $licenseCode;
                        $ret[$count]['licenseisfree'] =  $licenseIsFree;
                        if (isset($dataObject->location)) {
                            $ret[$count]['location'] =  $dataObject->location;
                        } else {
                            $ret[$count]['location'] =  NULL;
                        }
                        $ret[$count]['rightsholder'] =  $dataObject->rightsHolder;
                        $ret[$count]['istrusted'] =  $isTrusted;
                        $count++;
                    }
                }

            }
        } else {
            $ret = $this->noAudioFound($latinName);
        }
        return $ret;
    }
    
    /**
     * 
     * Get data on images for the species from EOL
     * 
     * @param string $latinName The scientific name for the species
     * @param integer $records The number of records to return
     * @return string array An array of species image data
     * 
     */
    public function getImageData($latinName, $records='5')
    {
        $obj = $this->jsonSearch($latinName);
        $id = $obj->results[0]->id;
        $uri = $this->buildUri($id, 'image', 2);
        $page = $this->getResults($uri);
        $obj = json_decode($page);
        $ret = array();
        // Check for at least one entry, then proceed.
        if (isset($obj->dataObjects[0])) {
            $count = 0;
            foreach ($obj->dataObjects as $dataObject) {
                if (isset($dataObject->mediaURL)) {
                    $url = $dataObject->mediaURL;
                    $licenseUrl = $dataObject->license;
                    $licenseCode = $this->extractLicense($licenseUrl);
                    $licenseIsFree = $this->isFree($licenseCode);
                    $isTrusted = $this->isTrusted($dataObject->vettedStatus);
                    //if ($this->isMp3($url)) {
                    $ret[$count]['uri'] =  $url;
                    $ret[$count]['licenseurl'] =  $licenseUrl;
                    $ret[$count]['licensecode'] =  $licenseCode;
                    $ret[$count]['licenseisfree'] =  $licenseIsFree;
                    $ret[$count]['istrusted'] =  $isTrusted;
                    $count++;
                    //}
                }

            }
        } else {
            $ret = $this->noImageFound($latinName);
        }
        return $ret;
    }
    
    /**
     * 
     * Build the URL used to access the EOL API
     * 
     * @param string $id The EOL Id of the record
     * @param string $mediaType The type of media to get (sound, video, image)
     * @param integer $records The number of records to return
     * @return boolean
     * 
     */
    private function buildUri($id, $mediaType='sound', $records='5')
    {
        $baseUri = "http://eol.org/api/pages/1.0/$id.json?details=0&subjects=overview&text=0";
            
         switch ($mediaType) {
            case "sound":
                return $baseUri . "&images=0&videos=0&sounds=$records";
                break;
            case "image":
                return $baseUri . "&videos=0&sounds=0&images=$records";
                break;
            case "video":
                return $baseUri . "&images=0&sounds=0&videos=$records";
                break;
            default:
                return FALSE;
                break;
         }
    }
    
    /**
     * 
     * Render the sound players for the sounds
     * 
     * @param string Array $sounds An array of sounds from $this->getData
     * @access private
     * 
     */
    private function renderSounds($sounds)
    {        
        $objPlayer = $this->getObject('soundplayer', 'species');
        $ret = '';
        foreach ($sounds as $sound) {
            $url = $sound['uri'];
            $location = $sound['location'];
            $rightsHolder = $sound['rightsholder'];
            $licenseCode = $sound['licensecode'];
            // Embed the player.
            $player = $objPlayer->embedAudio($url);
            $doc = new DOMDocument('UTF-8');
            $div = $doc->createElement('div');
            $div->setAttribute('class', 'species_sounds');
            $frag = $doc->createDocumentFragment(); 
            $frag->appendXML($player);
            $div->appendChild($frag);
            // Add the rights holder if any.
            if ($rightsHolder !== NULL) {
                $br = $doc->createElement('br');
                $div->appendChild($br);
                $div->appendChild($doc->createTextNode("By: " . $rightsHolder));
            }
            
            // Add location information if any.
            if ($location !== NULL) {
                $br = $doc->createElement('br');
                $div->appendChild($br);
                $div->appendChild($doc->createTextNode("Recorded at: " . $location));
            }
            
            // Add the creative commons license icon
            if ($licenseCode !== NULL) {
               $objCc = $this->getObject('displaylicense', 'creativecommons');
               $lic = $objCc->show($licenseCode);
                
                $frag = $doc->createDocumentFragment(); 
                
                $frag->appendXML($lic);
                $div->appendChild($frag);
            }
            
            
            $doc->appendChild($div);
            $ret .= $doc->saveHTML();
        }
        return $ret;
    }
    
    /**
     * 
     * Check if the presumed MP3 file is really an MP3 file by looking
     * only at its extension.
     * 
     * @param string $url The URL of theproposed MP3 file
     * @return boolean
     * @access private
     * 
    */
    private function isMp3($url) {
        $ar = explode('.', $url);
        $pos = count($ar)-1;
        $fileExt = strtolower($ar[$pos]);
        if ($fileExt == 'mp3') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * 
     * Extract the CC license string
     * 
     * @param string $license The license URL
     * @return string The license extracted
     * @access private
     * 
     */
    private function extractLicense($license)
    {
        $tmp = str_replace('licenses/', '', stristr($license, 'license'));
        $tmp = trim(str_replace('/', ' ', $tmp));
        $lic = substr($tmp, 0, strpos($tmp, " "));
        if ($lic  == 'publicdomain') {
            $lic = 'pd';
        }
        return $lic;
    }
    
    /**
     * 
     * Evaluate whether the license is 
     * 
     * @param type $licenseCode
     * @return boolean
     * @access private
     * 
     */    
    private function isFree($licenseCode)
    {
        $arNonFree = array('nc', 'nd', 'c');
        foreach ($arNonFree as $chk) {
            if (strpos($licenseCode, $chk)) {
                return 0;
            }
        }
        return 1;
    }
    
    /**
     * 
     * Check the trust status from EOL
     * 
     * @param string $vettedStatus The vetted trust status from EOL
     * @return boolean
     * @access private
     * 
     */
    private function isTrusted($vettedStatus)
    {
        if (strtolower($vettedStatus) == 'trusted') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * 
     * Send a warning message if no sound file is found on EOL
     * 
     * @param string $latinName The latin name we are looking up
     * @return string The warning message
     * @access private
     * 
     */
    private function noAudioFound($latinName)
    {
        $repArray = array('species'=>$latinName);
        $res = $this->objLanguage->code2txt(
                "mod_species_eolnosound", "species", $repArray,
                "EOL has no linked sound file"
          );
        $doc = new DOMDocument('UTF-8');
        $div = $doc->createElement('div');
        $div->setAttribute('class', 'species_stub');
        $div->appendChild($doc->createTextNode($res));
        $doc->appendChild($div);
        return $this->italicizeSpecies($doc->saveHTML(), $latinName);
    }
    
    /**
     * 
     * Send a warning message if no image file is found on EOL
     * 
     * @param string $latinName The latin name we are looking up
     * @return string The warning message
     * @access private
     * 
     */
    private function noImageFound($latinName)
    {
        $repArray = array('species'=>$latinName);
        $res = $this->objLanguage->code2txt(
                "mod_species_eolnoimg", "species", $repArray,
                "EOL has no linked sound file"
          );
        $doc = new DOMDocument('UTF-8');
        $div = $doc->createElement('div');
        $div->setAttribute('class', 'species_stub');
        $div->appendChild($doc->createTextNode($res));
        $doc->appendChild($div);
        return $this->italicizeSpecies($doc->saveHTML(), $latinName);
    }
    
    /**
     * 
     * Embed the found audio file in a player, detecting Firefox which
     * won't play MP3 files as native HTML5 audio
     * 
     * @param type $url
     * @return type
     * @access private
     * 
     */
    private function embedAudio($url)
    {
        $objPlayer = $this->getObject('soundplayer', 'species');
        return $objPlayer->embedAudio($url);
    }
    
    /**
     * 
     * Render the images for display in a middle block
     * 
     * @param string array $images An array of image info
     * @return string Rendered images
     * @access private
     * 
     */
    private function renderImages($images) 
    {
        $ret = '';
        foreach ($images as $image) {
            $url = $image['uri'];
            $licenseCode = trim($image['licensecode']);
            $doc = new DOMDocument('UTF-8');
            $img = $doc->createElement('img');
            $img->setAttribute('src', $url);
            $div = $doc->createElement('div');
            $div->setAttribute('class', 'species_contrimages');
            $div->appendChild($img);
            $br = $doc->createElement('br');
            $div->appendChild($br);
            // Add the creative commons license icon
            if ($licenseCode !== NULL && $licenseCode !=="") {
                // There is no PD license.
                if ($licenseCode == 'pd') {
                    $imgDoc = new DOMDocument('UTF-8');
                    $ccimg = $imgDoc->createElement('img');
                    $ccimg->setAttribute('src', 
                      'skins/_common/icons/creativecommons_v3/pd_big.png');
                    $imgDoc->appendChild($ccimg);
                    $lic = $imgDoc->saveHTML();
                } else {
                    $objCc = $this->getObject('displaylicense', 'creativecommons');
                    $lic = $objCc->show($licenseCode);
                }

                
                $frag = $doc->createDocumentFragment(); 
                
                $frag->appendXML($lic);
                $div->appendChild($frag);
            }
            $doc->appendChild($div);
            $ret .= $doc->saveHTML();
        }
        return $ret;
    }

    /**
     * 
     * Get up to two images for the given species identified by its
     * scientific name. It returns the thumbnail, and sets the full size
     * image to $this->fullImage
     * 
     * @param string $scientificName The latin species name
     * @return string The URL for the thumbnail image
     * @access public
     * 
     */
    public function getImage($scientificName)
    {
        $obj = $this->jsonSearch($scientificName);
        $id = $obj->results[0]->id;
        $uri = "http://eol.org/api/pages/1.0/$id.json?"
          . "details=0&images=2&sounds=0&subjects=overview&text=0";
        $page = $this->getResults($uri);
        $obj = json_decode($page);
        if (isset($obj->dataObjects[0])) {
            $url = $obj->dataObjects[0]->eolThumbnailURL;
            $this->fullImage = $obj->dataObjects[0]->mediaURL;
            $this->eolImage = $obj->dataObjects[0]->eolMediaURL;
        } else {
            $url = NULL;
            $this->fullImage = NULL;
            $this->eolImage = NULL;
        }
        return $url;
    }

    /**
     * 
     * Carry out a search, returning JSON as a result
     * 
     * @param string $searchTerm The term to search
     * @return string A linked alphabetic list
     * @access public
     * 
     */
    public function jsonSearch($searchTerm)
    {
        $searchTerm = str_replace(' ', '%20', $searchTerm);
        $uri = 'http://eol.org/api/search/1.0/' . $searchTerm . '.json?exact=1';
        $page = $this->getResults($uri);
        return json_decode($page);
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
    
    /**
     * 
     * Italicise occurrences of the latin name in the text.
     * 
     * @param $string $txt The text to look in
     * @param string $latin The latin name
     * @return string The text with italics added
     * @access private
     * 
     */
    private function italicizeSpecies($txt, $latinName) {
        return str_replace(
          $latinName, 
          '<i class="species_latin">' 
          . $latinName . '</i>', $txt
        );
    }
    
    
}
?>