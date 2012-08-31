<?php

/**
 * Class to parse a string (e.g. page content) that contains a
 * URL for a shared google doc.
 *
 * It takes the form
 * [GOOGLEDOC]http://spreadsheets.google.com/pub?key=p4puNISripKN-EXKJ2xCwRw[/GOOGLEDOC]
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
 * @version   $Id: parse4iframe_class_inc.php 2808 2007-08-03 09:05:13Z paulscott $
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
 * Class to parse a string (e.g. page content) that contains a
 * URL for a shared google doc.
 *
 * It takes the form
 * [GOOGLEDOC]http://spreadsheets.google.com/pub?key=p4puNISripKN-EXKJ2xCwRw[/GOOGLEDOC]
 * or the form
 * [GOOGLEDOC: height=240, width=600,
 * caption=A spreadsheet with some stuff in it.]
 * http://spreadsheets.google.com/pub?key=p4puNISripKN-EXKJ2xCwRw
 * [/GOOGLEDOC]
*
* @author Derek Keats
*
*/

class parse4googledoc extends object
{

    /**
     *
     * Standard Chisimba init method.
     * to use
     *
     */
    public function init()
    {
        // Get an instance of the params extractor
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
        $txt = stripslashes($txt);
        preg_match_all('/(\\[GOOGLEDOC:?)(.*?)\\](.*?)(\\[\\/GOOGLEDOC\\])/ism', $txt, $results);
        $counter = 0;
        foreach ($results[3] as $item) {
            //Parse for the parameters
            $str = trim($results[2][$counter]);
            //The whole match must be replaced
            $replaceable = $results[0][$counter];
            $ar= $this->objExpar->getArrayParams($str, ",");
            $this->setupPage();
            $replacement =  $this->getDoc($item);
            $txt = str_replace($replaceable, $replacement, $txt);
            $counter++;
        }

        return $txt;
    }

    /**
    *
    * Method to set up the parameter / value pairs for th efilter
    * @access public
    * @return VOID
    *
    */
    private function setUpPage()
    {
        //Get type
        if (isset($this->objExpar->width)) {
            $this->width = $this->objExpar->width;
        } else {
            $this->width=500;
        }
        //Get title
        if (isset($this->objExpar->height)) {
            $this->height = $this->objExpar->height;
        } else {
            $this->height=400;
        }
        //Get comment
        if (isset($this->objExpar->caption)) {
            $this->caption = "<br /><span class=\"warning\">"
              . $this->objExpar->caption . "</span>";
        } else {
            $this->caption=NULL;
        }
    }

    /**
    *
    * Method to get the javascript for displaying delicious tags
    * for $deliciousUser
    *
    * @param  string $deliciousUser The username on del.icio.us
    * @return string The javascript
    *
    */
    public function getDoc($item)
    {
        return "<iframe src=\"$item\" width=\"$this->width\" frameborder=\"0\" height=\"$this->height\"></iframe>$this->caption";
    }

}
?>