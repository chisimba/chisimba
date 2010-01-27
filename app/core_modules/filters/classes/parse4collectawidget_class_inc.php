<?php
/**
 * Class to insert a Collecta widget for a given term in the content. 
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
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id:  $
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
 * Class to insert a Collecta widget for a given term in the content.  
 * 
 *
 * @author Paul Scott <pscott@uwc.ac.za>
 */

class parse4collectawidget extends object
{
    /**
     *
     * String to hold an error message
     * @accesss private
     */
    private $errorMessage;

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
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
        //Note the ? in the regex is important to enable the multiline
        //   feature, else it greedy
        preg_match_all('/(\\[COLLECTAWIDGET:)(.*?)\\]/ism', $txt, $results);
        $counter = 0;
        foreach ($results[2] as $item) {
            //Parse for the parameters
            $str = trim($results[1][$counter]);
            //The whole match must be replaced
            $replaceable = $results[0][$counter];
            $ar = $this->objExpar->getArrayParams($item, ",");
            if (isset($this->objExpar->term)) {
                $term = $this->objExpar->term;
            } else {
                $term = 'Chisimba';
            }
            if (isset($this->objExpar->title)) {
                $title = $this->objExpar->title;
            } else {
                $title = 'Chisimba';
            }
            if (isset($this->objExpar->width)) {
                $width = $this->objExpar->width;
            } else {
                $width = '200';
            }
            if (isset($this->objExpar->height)) {
                $height = $this->objExpar->height;
            } else {
                $height = '300';
            }
            $replacement = $this->widgetize($term, $title, $width, $height);
            $txt = str_replace($replaceable, $replacement, $txt);
            $counter++;
        }
        return $txt;
    }
    
    private function widgetize($term, $title, $width, $height){
        $widget = '<iframe style="border: medium none ; overflow: hidden; width: '.$width.'px; height: '.$height.'px;" 
                  src="http://widget.collecta.com/widget.html?query='.urlencode($term).'&alias='.$title.'&
                  headerimg=&stylesheet=&delay=" id="widgetframe" frameborder="0" scrolling="no">
                  </iframe>';
        
        return $widget;
    }

}
?>
