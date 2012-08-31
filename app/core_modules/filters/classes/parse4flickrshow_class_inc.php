<?php

/**
 * Class to Parse for flickrshow tags and render them as a flickrshow slideshow
 * It requires the flickrshow module to be installed
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
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2010 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: parse4mathml_class_inc.php 11052 2008-10-25 16:04:14Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       http://www.flickrshow.com
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class parse4flickrshow extends object {
    
    private $flickrshow;

    public function init() {
        $this->flickrshow->autostart = 0;
        $this->flickrshow->photoset = false;
        $this->flickrshow->speed = 3;
        $this->flickrshow->skindir = $this->getResourceURI('/', 'flickrshow');
        $this->flickrshow->size = '';
        $this->flickrshow->height = '400px';
        $this->flickrshow->width = '100%';
    }

    /**
    * Method to Parse a String for flickrshow tags and render them
    * @param  string $str String to Parse
    * @return string String with flickrshow code
    */
    public function parse($str) {
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the flickrshow module is registered
        if ($objModule->checkIfRegistered('flickrshow')) {
            
            $objFlickrshow = $this->getObject('flickrshowembed', 'flickrshow');
            preg_match_all('/\\[FLICKRSHOW:(.*?)\\]/i', $str, $results, PREG_PATTERN_ORDER);
            $paramPairs = explode(',', $results[1][0]);
            
            foreach ($paramPairs as $pair) {
                $tempPair = explode('=', trim($pair));
                if (isset($tempPair[1])) {
                    $this->flickrshow->$tempPair[0] = $tempPair[1];
                } else {
                    return "Syntax Error near `$pair` in $str";
                }
            }
            
            if ($this->flickrshow->photoset) {
                return $objFlickrshow->embedFlickrShow($this->flickrshow);
            }
                
        }

        // Return String
        return $str;
    }
}
?>