<?php
/**
* Class to parse a string (e.g. page content) that contains a FLICKR
* item, and return the content inside the Chisimba page
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
* Class to parse a string (e.g. page content) that contains a link
* to a flickr filter and render th desired content.
*
* @author Derek Keats
*
*/

class parse4flickr extends object
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
        $this->objLanguage = $this->getObject('language', 'language');
        //Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
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
        //Match filters based on a wordpress style
        preg_match_all('/\\[FLICKR:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
        //Get all the ones in links
        $counter = 0;

        foreach ($results[0] as $item)
        {
            $str = $results[1][$counter];
            $ar = $this->objExpar->getArrayParams($str, ",");
            if (isset($this->objExpar->type)) {
                $type = $this->objExpar->type;
            } else {
                $type="error";
            }
            switch ($type)
            {
                case "slideshow":
                    $replacement = $this->getSlideshow();
                    break;

                case "feed":
                    $this->objRss = $this->getObject('rssreader', 'feed');
                    $replacement = "<div class=\"feedhopper\" id=\"feedhopper" . $counter . "\">" . $this->getFeed() . "</div>";
                    break;

                default:
                    $replacement = $item . "<br .<span class=\"error\">"
                      . $this->objLanguage->languageText("mod_filters_error_flickr_invalid" , "filters") . "</span>";
                    break;
            }
            $txt = str_replace($item, $replacement, $txt);
            $counter++;
            $this->objExpar->width = NULL;
            $this->objExpar->height = NULL;
            $this->objExpar->tag = NULL;
            $this->objExpar->userid = NULL;
            $this->objExpar->groupid = NULL;
            
            
        }
        return $txt;
    }

    /**
     *
     * Method to return a flickr slideshow
     *
     * @return string The formatted slideshow object
     *
     */
    private function getSlideshow()
    {
        //Initialize extras
        $extras = "";
        //Get and set the width
        if (isset($this->objExpar->width)) {
            $width = $this->objExpar->width;
        } else {
            $width = 500;
        }
        //Get and set the height
        if (isset($this->objExpar->height)) {
            $height = $this->objExpar->height;
        } else {
            $height = 500;
        }
        //Get and set the tag
        if (isset($this->objExpar->tag)) {
            $tag = $this->objExpar->tag;
        } else {
            $tag = NULL;
        }
        //Get and set the userid
        if (isset($this->objExpar->userid)  && $this->objExpar->userid !== "") {
            $userid = $this->objExpar->userid;
            $what = "user_id=$userid";
        }
        //Get and set the groupid
        if (isset($this->objExpar->groupid) && $this->objExpar->groupid !== "") {
            $groupid = $this->objExpar->groupid;
            $what = "group_id=$groupid";
        }
        
        

        return "<object type=\"text/html\" "
          . "data=\"http://www.flickr.com/slideShow/index.gne?$what"
          . "&tags=$tag\" "
          . "width=\"$width\" height=\"$height\" $extras> "
          . "</object>";
    }
    /**
     *
     * Method to get a flickr feed by parsing a filter of the form
     * [FLICKR: type=feed,feed=http://flickrfeedurl]
     *
     * @return string A formatted table with the flickr feed in it
     *
     */
    private function getFeed()
    {
        //http://api.flickr.com/services/feeds/photos_public.gne?id=93242958@N00&lang=en-us&format=rss_200
        //Get the FEED URL
        if (isset($this->objExpar->url)) {
            $feed = $this->objExpar->url;
        } else {
            return "<span class=\"error\">"
              . $this->objLanguage->languageText("mod_filters_error_flickr_nofeedurl", "filters")
              . "</span>";
        }
        // Make sure it has a good chance of being a flickr site
        $pos = strpos($feed, "flickr.com");
        if ($pos === FALSE) {
            return "<span class=\"error\">"
              . $this->objLanguage->languageText("mod_filters_error_flickr_notfeed", "filters")
              . ": " . $feed . "</span>";
        }
        // See how many columns to display, defaulting to two
        if (isset($this->objExpar->cols)) {
            $cols = $this->objExpar->cols;
        } else {
            $cols = 2;
        }
        //Clean up the &amp; that might be inserted by the editor or &eq; for named entities
        $feed =  str_replace("&amp;", "&", $feed);
        $feed =  str_replace("&eq;", "=", $feed);
        //die ($feed);
        $this->objRss->parseRss($feed);
        $ar = $this->objRss->getRssItems();
        $total = count($ar);
        // Set up the table for output and start the first row
        $ret = "<table>\n";
        $fillCells = $total % $cols;
        if ($fillCells > 0) {
            $count = $fillCells - 1;
            $filler = "";
            while ($count !== 0)
            {
                $count--;
                $filler .="<td>&nbsp;</td>";
            }
        }
        if ($fillCells !==0) {
            $closingCell = $filler . "</tr>";
        } else {
            $closingCell = "";
        }
        //Loop and build the output string
        $counter=0;
        $newRowCounter=0;
        foreach ($ar as $item) {
            $counter++;
            if(!isset($item['link'])) {
                $item['link'] = NULL;
            }
            // Putting them in two rows
            if ($this->isNewRow($newRowCounter, $cols)==TRUE) {
                @$ret .= "<tr><td><a href=\"" . htmlentities($item['link'])
                  . "\">" . htmlentities($item['title']) . "</a><br />\n"
                  . $item['description'] . "</td>";
                $newRowCounter = 0;
            } else  {
                @$ret .= "<td><a href=\"" . htmlentities($item['link'])
                  . "\">" . htmlentities($item['title']) . "</a><br />\n"
                  . $item['description'] . "</td>";
                  if ($newRowCounter == $cols) {
                      $ret .= "</tr>";
                  }
            }
            $newRowCounter++;

        }
        $ret .= $closingCell . "</table>\n";
        return $ret;
    }

    /**
    *
    * Method to determine if we should insert a new row
    * according to the desired number of columns
    *
    * @access public
    * @param  int $num The number to test
    * @param  int $cols The desired number of columns
    * @return boolean TRUE|FALSE depending on status of $num
    *
    */
    function isNewRow($num, $cols)
    {
        $remainder = $num % $cols;
        if( $remainder == 0 ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>