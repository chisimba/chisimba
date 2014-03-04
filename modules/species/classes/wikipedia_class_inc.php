<?php
/**
 *
 * Access to Wikipedia data for Species
 *
 * Access to Wikipedia data in order to render the main text content for the
 * species results.
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
* Access to Wikipedia data for Species
*
* Access to Wikipedia data in order to render the main text content for the
* species results.
*
* @package   species
* @author    Derek Keats derek@localhost.local
*
*/
class wikipedia extends object
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
     * Use curl to retrieve a wikipedia page
     * 
     * @param string $uri The wikipedia URI to retrieve
     * @return string The contents of the page
     * @access public
     * 
     */
    public function getWikipediaPage($uri)
    {
        $page = $this->getResults($uri);
        $this->appendArrayVar('headerParams', $this->getWikipediaCss());
        return $page;
    }
    
    /**
     * 
     * Get the page content from the DIV with id=bodyContent by using the 
     * dom document
     * 
     * @param string $page The retrieved Wikipedia page
     * @return string The extracted content
     * @access public
     * 
     */
    public function getContent($page)
    {
        $wasFound = FALSE;
        $tree = new DOMDocument();
        @$tree->loadHTML($page);
        $count = 1;
        $output = "";
        foreach($tree->getElementsByTagName('div') as $div) {
            if($div->getAttribute('id') == "bodyContent") {
                foreach($div->getElementsByTagName('p') as $p) {
                    $output .= "<p>".$p->nodeValue."</p>";
                }
                // Remove the [##] links
                $output = preg_replace('/\[[^\]]*\]/', '', $output);
                return $output;
            }
        }
        return NULL;
    }
    
    /**
     * 
     * Get an array of thumbnails from the Wikipedia page
     * 
     * @param string $page The wikipedia page contents
     * @return string Array An array of HTML links to thumb images
     * @access public
     * 
     */
    public function getImageThumbs($page)
    {
        $tree = new DOMDocument();
        @$tree->loadHTML($page);
        $arImgs = array();
        foreach($tree->getElementsByTagName('div') as $div) {
            if($div->getAttribute('id') == "bodyContent") {
                foreach($div->getElementsByTagName('img') as $img) {
                    if($div->getAttribute('class') == "thumbimage") {
                        $arImgs[] = $img->getAttribute('src');
                    }
                }
            }
        }
        return $arImgs;
    }
    
    /**
     * 
     * Get the wikipedia CSS from the filters module
     * 
     * @return string The formatted link for the page header
     * @access public
     * 
     */
    public function getWikipediaCss()
    {
        return "<link rel=\"stylesheet\" type=\"text/css\" "
          . "href=\"" . $this->getResourceUri("css/wikipedia.css", "filters") . "\" />";
    }
    
    /**
     * 
     * Italicise occurrences of the latin name in the text.
     * 
     * @param $string $wikiTxt The text to look in
     * @param string $latin The latin name
     * @return string The text with italics added
     * @access public
     * 
     */
    public function italicizeSpecies($wikiTxt, $latin) {
        return str_replace($latin, '<i class="species_latin">' . $latin . '</i>', $wikiTxt);
    }
    
    /**
     * 
     * Get the wikipedia icon
     * 
     * @return string The IMG tag for the icon
     * @access public
     * 
     */
    public function getWikipediaIcon()
    {
        $icon = $this->getResourceURI('icons/wikipedia64.png');
        return "<img src='$icon' class='speciesrecord_wikipicon'>";
    }
    
    /**
     * 
     * Check if an article returned is a stub
     * 
     * @param string $wikiTxt The text of the page
     * @return boolean TRUE|FALSE
     * @access public
     * 
     */
    public function checkStub($wikiTxt)
    {
        if (strpos($wikiTxt, 'Wikipedia:Stub')) {
            return TRUE;
        } else {
            return FALSE;
        }
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