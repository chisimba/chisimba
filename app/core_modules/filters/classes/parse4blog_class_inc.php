<?php

/**
 *
 * BLOG filter for inserting blog posts from other sites or users.
 *
 * This filter uses jQuery to dynamically load blog posts from another
 * server using a filter of the type
 *   [BLOG: server=server/path/to/chisimba, postid=somelongstring]
 * and inserts it at the point of the filter in the text of the current
 * content.
 *
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
 * @version   $Id: parse4ss_class_inc.php 12758 2009-03-06 20:14:13Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see       
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
 * BLOG filter for inserting blog posts from other sites or users.
 *
 * This filter uses jQuery to dynamically load blog posts from another
 * server using a filter of the type
 *   [BLOG: server=server/path/to/chisimba, postid=somelongstring]
 * and inserts it at the point of the filter in the text of the current
 * content.
 *
 * @author Derek Keats
 *
 */

class parse4blog extends object
{
    
    /**
     * 
     * 
     * @return void  
     * @access public
     */
    public function init()
    {
        
    }
    
    /**
    *
    * Method to parse the string
    *
    * @param  String $str The string to parse
    * @return string The parsed string
    * @access public
    *                
    */
    public function parse($str)
    {
            $regEx = "/\\[blog:(.*?)\\]/i";
            preg_match_all($regEx, $str, $results, PREG_PATTERN_ORDER);
        //Extract all the matches
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $txt = $results[1][$counter];
            $ar = explode(",", $txt);
            if (count($ar) > 0) {
                foreach ($ar as $entry) {
                    $ar2 = explode("=", $entry);
                    $key = (string)$ar2[0];
                    $value = $ar2[1];
                    $this->$key = $value;
                }
            }
            $uri = "http://" . $this->server
              . "/index.php?module=blog&action=export&postid="
              . $this->postid;
            $replacement = $this->getReplacement($uri);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        // Return the string, parsed or not, depending on if oembed is isntalled
        return $str;
    }

    /**
    *
    * Method to get the replacement for the filter.
    *
    * @access private
    * @param string $oembedUrl The URL for a compatible object
    * @return string The code and DIV tag for embedding
    *
    */
    private function getReplacement($uri)
    {
        $hashVal = md5(microtime());
        $retScript = "\n\n<script>jQuery(document).ready(function() "
          . "{\n jQuery('#$hashVal').load('$uri');})</script>\n";
        $ret = "<div id='$hashVal'></div>\n\n";
        return $retScript . $ret;
    }
}
?>