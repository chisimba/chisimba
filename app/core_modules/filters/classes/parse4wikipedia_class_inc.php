<?php

/**
* Class to parse a string (e.g. page content) that contains a wikipedia
* keyword, and return the page of content inside the Chisimba page
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
 * Class to parse a string (e.g. page content) that contains a link
 * to a yout tube video and render the video in the page
 *
 * @author Derek Keats
 *
 */

class parse4wikipedia extends object
{
    /**
    *
    * String to hold an error message
    * @accesss private
    */
    private $errorMessage;

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
        //Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
    }

    /**
     *
     * Method to parse the string
     * @param  String $str The string to parse
     * @return The    parsed string
     *
     */
    public function parse($txt)
    {
        //Match filters based on a wordpress style
        preg_match_all('/\\[WIKIPEDIA:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
        //Get all the ones in links
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $str = $results[1][$counter];
            $ar= $this->objExpar->getArrayParams($str, ",");
            if (isset($this->objExpar->topic)) {
                $topic = $this->objExpar->topic;
            } else {
                $topic="";
            }
            //Check if it is a valid link, if not return an error message
            if ($this->isWikipedia($str)) {
                $link = "http://en.wikipedia.org/wiki/" . trim($topic);
            	$replacement = $this->getWikiContents($link);
            } else {
            	$replacement = $this->errorMessage;
            }
            $txt = str_replace($item, $replacement, $txt);
            $counter++;
        }

        return $txt;
    }

    /**
     * gets the wiki content for the specified url
     * @param String $link wiki link
     * @return <type>
     */
    function getWikiContents($link)
    {
        $objCurl = $this->getObject('curlwrapper', 'utilities');
        $page = $objCurl->exec($link);
        preg_match_all('#<!-- start content -->(.*?)<!-- end content -->#es', $page, $ar);
        if (is_array($ar[1])) {
            $this->appendArrayVar('headerParams', $this->_getWikipediaCss());
            $page = $ar[1][0];
            //Remove the edit tags
            $rep = "";
            preg_match_all('/<span class=\"editsection\">(.*?)<\/span>/', $page, $matches, PREG_PATTERN_ORDER);
            $counter = 0;
            foreach ($matches[0] as $item) {
                $page = str_replace($item, $rep, $page);
            }
            //Remove the generic stuff
            if (preg_match("/<div id=\"globalWrapper\">(.*)<div class=\"printfooter\">/iseU", $page, $elems)) {
                 $page = $elems[1];
                 $page = "<div id=\"globalWrapper\">" . $page;
            }
            $page = str_replace("<div", "<span", $page);
            $page = str_replace("</div>", "</span>", $page);
            $page = str_replace("#content", "#wikipediacontent", $page);
            $page = str_replace("<a href=\"/", "<a href=\"http://en.wikipedia.org/", $page);
            //
            return $page;
        } else {
            return FALSE;
        }

    }
    /**
     * Method that uses curl to return the Wikipedia page
     * as a string
     *
     * @param string $link The Wikipedia URL
     * @return string The page as a string cleaned for display
     * @access public
     */
    function __getWikiContents($link)
    {
        $objCurl = $this->getObject('curlwrapper', 'utilities');
        $page = $objCurl->exec($link);
        if (preg_match("/<body.*>(.*)<\/body>/iseU", $page, $elems)) {
            $page = $elems[1];
            $this->appendArrayVar('headerParams', $this->_getWikipediaCss());
            //Remove the edit tags
            $rep = "";
            preg_match_all('/<span class=\"editsection\">(.*?)<\/span>/', $page, $matches, PREG_PATTERN_ORDER);
            $counter = 0;
            foreach ($matches[0] as $item) {
                $page = str_replace($item, $rep, $page);
            }
            //Remove the generic stuff
            if (preg_match("/<div id=\"globalWrapper\">(.*)<div class=\"printfooter\">/iseU", $page, $elems)) {
                 $page = $elems[1];
                 $page = "<div id=\"globalWrapper\">" . $page;
            }
            $page = str_replace("<div", "<span", $page);
            $page = str_replace("</div>", "</span>", $page);
            $page = str_replace("#content", "#wikipediacontent", $page);
            $page = str_replace("<a href=\"/", "<a href=\"http://en.wikipedia.org/", $page);
        } else {
            $page = NULL;
        }

        return $page;
    }

    private function _getWikipediaCss()
    {
        return "<link rel=\"stylesheet\" type=\"text/css\" "
          . "href=\"" . $this->getResourceUri("css/wikipedia.css", "filters") . "\" />";
    }


    /**
     *
     *  A method to validate a keyword as a valid wikipedia keyword
     *
     * @param  string  $keyWord The link to check
     * @return boolean TRUE|FALSE True if it is a valid link, false otherwise
     *
     * @Todo - implement this.
     *
     */
    private function isWikipedia($keyWord)
    {
    	$keyWord=strtolower($keyWord);

   	return TRUE;
    }

}
?>