<?php
/**
 *
 * Access to Wikipedia species list page to build XML
 *
 * Access to Wikipedia species list page to build XML data that can 
 * be imported into the species module.
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
 * Access to Wikipedia species list page to build XML
 *
 * Access to Wikipedia species list page to build XML data that can 
 * be imported into the species module.
*
* @package   species
* @author    Derek Keats derek@localhost.local
*
*/
class wikipediasplist extends object
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
    public function getWikipediaListPage($url)
    {
        $page = $this->getResults($url);
        return $page;
    }
    
    public function show($url) 
    {
        $page = $this->getWikipediaListPage($url);
        return $this->parseWikipediaListPage($page);
    }
    
    public function parseWikipediaListPage($page)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($page);
        $tags = $dom->getElementsByTagName('li');
        $res = array();
        $l=0;
        foreach($tags as $tag) {
            $chk = $tag->nodeValue;
            if (strstr($chk, ',') && !strstr($chk, '.')) {
                $ar = explode(',', $chk);
                // The full name is straight forward.
                $fullname = $ar[0];
                // Reverse the full name to get the alpabetic name
                $alphAr = explode(' ', $fullname);
                $keyIndex = count($alphAr) - 1;
                $surname = $alphAr[$keyIndex];
                array_pop($alphAr);
                $surname2 = implode(" ", $alphAr);
                $alphabeticalname = $surname . ", " . $surname2;
                // The species is italicised so use that to pull it out & avoid status text.
                $spc = $tag->getElementsByTagName('i');
                foreach ($spc as $spVal) {
                    $spname = $spVal->nodeValue;
                }
                
                $res[$l]['alphabeticalname'] = $alphabeticalname;
                $res[$l]['fullname'] = $fullname;
                $res[$l]['scientificname'] = $spname;
            }
            $l++;
        }
        // Now build the XML output
        if (!empty($res)) {
            // Start XML file, create parent node
            $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><birds></birds>");
            // Loop over and create the child nodes
            $l=1;
            foreach ($res as $sp) {
                $bird = $xml->addChild('bird');
                $bird->addChild('id', $l);
                $bird->addChild('alphabeticalname', $sp['alphabeticalname']);
                $bird->addChild('fullname',  $sp['fullname']);
                $bird->addChild('scientificname', $sp['scientificname']);
                $l++;
            }
            print($xml->asXML());
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
    private function getResults($url)
    {
        $objCurl = $this->getObject('curlwrapper', 'utilities');
        return $objCurl->exec($url);
    }

    
}
?>