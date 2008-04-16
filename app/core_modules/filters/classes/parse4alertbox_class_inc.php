<?php
/**
*
* Class to parse a string (e.g. page content) that contains a filter
* tag to rais an alert box
*
* It takes the form
* [ALERT: url=http://somesite.com]The text of the alert[/ALERT]
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
* @version   CVS: $Id: parse4flag_class_inc.php 3695 2008-03-29 21:39:23Z dkeats $
* @link      http://avoir.uwc.ac.za
*/



/**
*
* Class to parse a string (e.g. page content) that contains a filter
* tag to rais an alert box
*
* It takes the form
* [ALERT: url=http://somesite.com]The text of the alert[/ALERT]
*
* This causes the text to be placed in a facebox alert using the alertbox
* class in HTML elements. It needs the jQuery resources in HTMLelements.
*
* @author Derek Keats
*
*/
class parse4alertbox extends object
{

    /**
    *
    * @var string $objLanguage String object property for holding the
    * alert HTML object
    * @access public
    *
    */
    public $objALert;

    /**
    *
    * String object $objExpar is a string to hold the parameter extractor object
    * @access public
    *
    */
    public $objExpar;

    /**
     *
     * String $url is the URL of display item
     * Valid: text, textime
     * @access public
     *
     */
    public $url;

    /**
     *
     * Constructor for the TWITTER filter parser
     *
     * @return void
     * @access public
     *
     */
    public function init()
    {
        // Get an instance of the language object
        $this->objAlert = $this->getObject('alertbox', 'htmlelements');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
    }

    /**
    *
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
       	//Match filters based on a wordpress style
        //Note the ? in the regex is important to enable the multiline
        //   feature, else it greedy
        preg_match_all('/(\\[ALERT:)(.*?)\\](.*?)(\\[\\/ALERT\\])/ism', $txt, $results);
       	$counter = 0;
       	foreach ($results[3] as $item) {
            //Parse for the parameters
            $str = trim($results[2][$counter]);
            //The whole match must be replaced
            $replaceable = $results[0][$counter];
        	$ar= $this->objExpar->getArrayParams($str, ",");
            $this->setupPage();
            $replacement = $this->getAlert($this->url, $item);
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
        //Get url
        if (isset($this->objExpar->url)) {
            $this->url = urldecode($this->objExpar->url);
        } else {
            $this->url=NULL;
        }
        //die( $this->url);
    }

    private function getAlert($url, &$item)
    {
        if (isset($url) && $url !== NULL) {
            return $this->objAlert->show($item, $url);
        } else {
        	return "URL NOT SET:";
        }
    }
}