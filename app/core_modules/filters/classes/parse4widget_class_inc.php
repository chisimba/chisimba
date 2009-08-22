<?php

/**
* Class to parse a string (e.g. page content) that contains a filter
* code for including a Web widget. Web widgets can be from any 
* site that has widgets.
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
* Class to parse a string (e.g. page content) that contains filter for including
* tooltips. The class uses DomTT for the tooltip.
*
* @author Derek Keats
*
*/
class parse4widget extends object
{
    /**
    *
    * String to hold an error message
    * @accesss private
    */
    private $errorMessage;

    /**
     * Object pointing to config module
     * @access public
     */
    public $objConfig;

   /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;

    /**
     *
     * @var <type> 
     */
    public $objExpar;

    /**
     *
     * String $message is the Message of the popup
     * @access public
     *
     */
    public $message;

    /**
     *
     * String $title is the title for the message
     * @access public
     *
     */
    public $title;

    /**
     *
     * String $url is the URL of the link
     * @access public
     *
     */
    public $url;

    /**
     *
     * String $linkText is the link text of the link
     * @access public
     *
     */
    public $linkText;


    /**
     *
     * Constructor for the wikipedia parser
     *
     * @return void
     * @access public
     *
     */
    public function init()
    {
        // Get an instance of the language object
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
        // Match [WIDGET][/WIDGET].
        preg_match_all('/(\\[WIDGET\\])(.*?)(\\[\\/WIDGET\\])/ism', $txt, $results);
        $counter = 0;
        foreach ($results[0] as $item) {
            $widget = $results[2][$counter];
            $replacement = $this->getWidget($widget);
            $txt = str_replace($item, $replacement, $txt);
            $counter++;
        }
        return $txt;
    }

    /**
    * Method to extract the HTML entities from the
    * widget which have been converted to entitied
    * by pasting into the WYSWYG editor
    *
    * @access Private
    * @return string The formatted widget text
    *
    */
    private function getWidget($widget)
    {
        return html_entity_decode($widget);
    }

}
?>