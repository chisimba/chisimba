<?php
/**
 *
 * Database access for Species
 *
 * Database access for Species. This is a sample database model class
 * that you will need to edit in order for it to work.
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
* Database access for Species
*
* Database access for Species. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   species
* @author    Derek Keats derek@localhost.local
*
*/
class speciesops extends object
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
     * @var string Object $objWikipedia String for the Wikipedia object
     * @access public
     *
     */
    public $objWikipedia;

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
        $this->objWikipedia = $this->getObject('wikipedia', 'species');
    }

    /**
     * 
     * Get the whole alphabet linked to a URL that will return species beginning
     * with the letter clicked.
     * 
     * @return string A linked alphabetic list
     * @access public
     * 
     */
    public function alphaLinked()
    {
        $alpha = range('a', 'z');
        $ret = "| ";
        foreach ($alpha as $letter) {
            $url = $this->uri(array(
                'action' => 'byletter',
                'letter' => $letter
            ), 'species');
            $url = str_replace("&amp;", "&", $url);
            // Use the DOM to make a link.
            $doc = new DOMDocument('UTF-8');
            $a = $doc->createElement('a');
            $a->setAttribute('href', $url);
            $a->appendChild($doc->createTextNode($letter));
            $doc->appendChild($a);
            $ret .= $doc->saveHTML()  . " | ";
        }
        return $ret;
    }
    
    /**
     * 
     * List species once a letter has been clicked, list all species whose
     * alphabetical name begins with the particular letter.
     * 
     * @param string $letter The letter of the alphabet chosen
     * @param string $field The database field to search, normally alphabeticalname
     * @return string A formatted table of species
     * @access public
     * 
     */
    public function listSpeciesByLetter($letter, $field='alphabeticalname') {
        $objDbspecies = & $this->getObject('dbspecies', 'species');
        $spArr = $objDbspecies->getListByLetter($letter, $field);
        $ret = "";
        $tab = new DOMDocument('UTF-8');
        $table = $tab->createElement('table');
        $table->setAttribute('class', "species_list_table");
        $class="odd";
        foreach ($spArr as $species) {
            // Create a table row
            $tr = $tab->createElement('tr');
            
            // The linked alphabetical name.
            $td = $tab->createElement('td');
            $td->setAttribute('class', $class);
            $id = $species['id'];
            $alphabeticalName = $species['alphabeticalname'];
            $fullName = $species['fullname'];
            $scientificName = $species['scientificname'];
            $url = $this->uri(array(
                'action' => 'showsp',
                'id' => $id
            ), 'species');
            $url = str_replace("&amp;", "&", $url);
            // Use the DOM to make a link
            $aLink = $tab->createElement('a');
            $aLink->setAttribute('href', $url);
            $aLink->appendChild($tab->createTextNode($alphabeticalName));
            $td->appendChild($aLink);
            $tr->appendChild($td);
            
            // The linked scientific name.
            $td = $tab->createElement('td');
            $td->setAttribute('class', $class);
            // Use the DOM to make a link.
            $aLink = $tab->createElement('a');
            $aLink->setAttribute('href', $url);
            $aLink->appendChild($tab->createTextNode($scientificName));
            $td->appendChild($aLink);
            $tr->appendChild($td);
            
            
             // Add the row to the table
            $table->appendChild($tr);
            
            // Convoluted odd/even
            if ($class == "odd") { 
                $class = "even";
            } else {
                $class = "odd";
            }
        }
        $tab->appendChild($table);
        return $tab->saveHTML();
    }
    
    /**
     * 
     * Return a list of all the groups 
     * 
     * @return string Linked list of groups
     * @access public 
     * 
     */
    public function showGroupings()
    {
        $objDbspecies = & $this->getObject('dbspecies', 'species');
        $arList = $objDbspecies->getGroupings();
        $ret="";
        $doc = new DOMDocument('UTF-8');
        foreach ($arList as $group) {
            $url = $this->uri(array(
                'action' => 'bygroup',
                'group' => $group['groupname']
            ), 'species');
            $url = str_replace("&amp;", "&", $url);
            $link = $doc->createElement('a');
            $link->setAttribute('href', $url);
            $link->appendChild($doc->createTextNode(" " . $group['groupname'] . " "));
            $doc->appendChild($link);
        }
        return $doc->saveHTML();
    }
    
    /**
     * 
     * Show the info for a particular species by the record id
     * 
     * @param string $id The record id for the species in the database
     * @return The formatter species record from Wikipedia
     * @access public
     */
    public function showSpecies($id)
    {
        // Create an instance of the database class.
        $objDbspecies = & $this->getObject('dbspecies', 'species');
        $record = $objDbspecies->getRecord($id);
        $latin = $record['scientificname'];
        $common = $record['fullname'];
        $wikiname = str_replace('  ', ' ', $common);
        $wikiname = str_replace(' ', '_', $wikiname);
        $uri = 'http://en.wikipedia.org/wiki/' . $wikiname;
        $page = $this->objWikipedia->getWikipediaPage($uri);
        $wikiTxt = $this->objWikipedia->getContent($page);
        // If it didn't find the common name, then try the latin name.
        if (strstr($wikiTxt, "Other reasons this message may be displayed")) {
            $wikiname = str_replace(' ', '_', $latin);
            $uri = 'http://en.wikipedia.org/wiki/' . $wikiname;
            $page = $this->objWikipedia->getWikipediaPage($uri);
            $wikiTxt = $this->objWikipedia->getContent($page);
            // If it still didn't find it.
            if (strstr($wikiTxt, "Other reasons this message may be displayed")) {
                $doc = new DOMDocument('UTF-8');
                $div = $doc->createElement('div');
                $div->setAttribute('class', 'species_stub');
                $wikiTxt = "Unable to locate an article on Wikipedia using common name or scientific name.";
                $div->appendChild($doc->createTextNode($wikiTxt));
                $doc->appendChild($div);
                $wikiTxt = $doc->saveHTML();
            }
        }
        // Serialize item to Javascript for the summary block.
        $fullName = str_replace("'", "\'", $wikiname);
        $arrayVars['fullName'] = $fullName;
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->varsToJs($arrayVars);
        
        // Check if the article is a stub on Wikipedia.
        $isStub = $this->objWikipedia->checkStub($page);
        // Italicize the latin name.
        $wikiTxt = $this->objWikipedia->italicizeSpecies($wikiTxt, $latin);
        $commonLinked = "<a href='$uri' target='_blank'>$common</a>";
        $ret = '<div class="species_speciesrecord">'
          . '<div class="species_titletop>"'
          . '<span class="speciesrecord_common">' . $commonLinked . '</span><br />'
          . '<span class="speciesrecord_latin">' . $latin . '</span>'
          . $this->objWikipedia->getWikipediaIcon() . '</div>'
          . '<div class="species_txt">'. $wikiTxt . '</div>'
          . '</div>';
        if ($isStub) {
            $doc = new DOMDocument('UTF-8');
            $div = $doc->createElement('div');
            $div->setAttribute('class', 'species_stub');
            $stub = $this->objLanguage->languageText(
                "mod_species_stub", "species",
                "This article is a stub in Wikipedia");
            $div->appendChild($doc->createTextNode($stub));
            $doc->appendChild($div);
            $ret .= $doc->saveHTML();
        }
        return $ret;
    }
    
    /**
     * 
     * Show a table with images for each member of a particular group
     * 
     * @param string $group The group (e.g. batis)
     * @return string Formatted table with images.
     * @acess public
     * 
     */
    public function showOneGroup($group)
    {
        $objDbspecies = & $this->getObject('dbspecies', 'species');
        $spArr = $objDbspecies->getGroup($group);
        $ret = "";
        $tab = new DOMDocument('UTF-8');
        $table = $tab->createElement('table');
        $table->setAttribute('class', "species_onegroup_table");
        $class = "odd";
        $objEol = $this->getObject('eol', 'species');
        foreach ($spArr as $species) {
            // Retrieve the data.
            $id = $species['id'];
            $alphabeticalName = $species['alphabeticalname'];
            $fullName = $species['fullname'];
            $scientificName = $species['scientificname'];
            // Create a table row.
            $tr = $tab->createElement('tr');
            // Url linking to species detail.
            $url = $this->uri(array(
                'action' => 'showsp',
                'id' => $id
            ), 'species');
            $url = str_replace("&amp;", "&", $url);
            // Use the DOM to make a link for fullname.
            $td = $tab->createElement('td');
            $td->setAttribute('class', $class);
            $aLink = $tab->createElement('a');
            $aLink->setAttribute('href', $url);
            $aLink->appendChild($tab->createTextNode($fullName));
            $td->appendChild($aLink);
            $br = $tab->createElement('br');
            $td->appendChild($br);
            $aLink = $tab->createElement('a');
            $aLink->setAttribute('href', $url);
            $aLink->appendChild($tab->createTextNode($scientificName));
            $td->appendChild($aLink);
            // Add the cell to the row
            $tr->appendChild($td);
            
            // Get images via Flickr.
            $objFlickr = $this->getObject('flickr', 'species');
            $arPhots = $objFlickr->getImages($scientificName);
            if (count($arPhots >= 1)) {
                foreach ($arPhots as $photo) {
                    $img = $tab->createElement('img');
                    $img->setAttribute('src', $photo['src']);
                    $td = $tab->createElement('td');
                    $td->setAttribute('class', $class);
                    $a = $tab->createElement('a');
                    $a->setAttribute('href', $url);
                    $a->setAttribute('title', $scientificName);
                    $a->appendChild($img);
                    // Put the image in a div for styling
                    $imDiv = $tab->createElement('div');
                    $imDiv->setAttribute('class', 'species_thumb');
                    $imDiv->appendChild($a);
                    $br = $tab->createElement('br');
                    $imDiv->appendChild($br);
                    // Put the name and flickr link below the image.
                    $a = $tab->createElement('a');
                    $a->setAttribute('href', $photo['link']);
                    $a->setAttribute('target', "_blank");
                    $imgTitle = $this->objLanguage->languageText("mod_species_viewonflickr", "species", "View on Flickr");
                    $a->setAttribute('title', $imgTitle);
                    $a->appendChild($tab->createTextNode($fullName . ", " . $scientificName));
                    $imDiv->appendChild($a);
                    $td->appendChild($imDiv);
                    $tr->appendChild($td);
                }
            }
            
            // Add the row to the table.
            $table->appendChild($tr);
            // Convoluted odd/even
            if ($class == "odd") { 
                $class = "even";
            } else {
                $class = "odd";
            }
        }
        $tab->appendChild($table);
        return $tab->saveHTML();
    }
    
    /**
     * 
     * Render a block for changing the group (data table referenced)
     * 
     * @return string A list of hyperlinks to change data
     * @access public
     * 
     */
    public function renderChangeBlock()
    {
        // Add valid groups to this array to auto generate links.
        $groups = array('birds', 'gardenbirds', 'birds_brazil', 'mammals', 'plants_proteaceae');
        $doc = new DOMDocument('UTF-8');
        foreach ($groups as $group) {
            $url = $this->uri(array(
                'action' => 'setdata',
                'data' => $group
            ), 'species');
            $groupRep = str_replace('_', ': ', $group);
            $url = str_replace("&amp;", "&", $url);
            $link = $doc->createElement('a');
            $link->setAttribute('href', $url);
            $link->appendChild($doc->createTextNode(" " . $groupRep . " "));
            $doc->appendChild($link);
        }
        return $doc->saveHTML();
    }
    

    
    /**
     * 
     * Italicise occurrences of the latin name in the text.
     * 
     * @param $string $wikiTxt The text to look in
     * @param string $latin The latin name
     * @return string The text with italics added
     * @access private
     * 
     */
    private function italicizeSpecies($wikiTxt, $latin) {
        return str_replace($latin, '<i class="species_latin">' . $latin . '</i>', $wikiTxt);
    }
}
?>