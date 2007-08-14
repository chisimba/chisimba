<?php
/**
 * This file contains the domtt class which is used to display popup tooltips
 * over anchors in an HTML page
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
 * @package   htmlelements
 * @copyright 2007 AVOIR
 * @author    Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */


// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
 * domtt class
 *
 * The DomTT is a class that displays a useful toot-tip popup over an
 * HTML anchor tag, when the user triggers a mouseover event
 *
 * @category  Chisimba
 * @package   htmlelements
 * @copyright 2007 AVOIR
 * @author    Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */

class domtt extends object
{


	/**
	 * Method to render the domtt object as HTML and javascript code
	 *
	 * @param string $title The title of the object
	 * @param string $message The message that will pop up to the user on a mouseover event
	 * @param string $linkText The text of the anchor tag
	 * @param string $url The url the tag points toward
	 * @param string $extra Any extra html that needs to be added to the anchor
	 * @return string The rendered object in displayable code
	 */
    public function show($title = 'Chisimba', $message = 'replace this message' , $linkText = "replace this link text", $url = "#" , $extra = null )
    {
    	$this->putScripts();
        $this->url = $url;

        $this->linkText = $linkText;

        $this->message = $message;

        $this->title = $title;

        $str = "<a  ".$extra."  href=\"".$this->url."\" onmouseover=\"this.style.color = '#D17E62'; domTT_activate(this, event, 'content', '".$this->title."&lt;p&gt;".$this->message."&lt;/p&gt;', 'trail', true, 'fade', 'both', 'fadeMax', 87, 'styleClass', 'niceTitle');\" onmouseout=\"this.style.color = ''; domTT_mouseout(this, event);\">".$this->linkText."</a>";
        return $str;
    }

	 /**
     * Method to get the javaScript that
     * needs to be added to the page header
     * for the tooltips to work
     *
     * @access public
     * @return string The javascript that must go in the header
     */
    public function putScripts()
    {

       $str = '<script type="text/javascript" language="javascript" src="installer/domtt/domLib.js"></script>
        <script type="text/javascript" language="javascript" src="installer/domtt/fadomatic.js"></script>
        <script type="text/javascript" language="javascript" src="installer/domtt/domTT.js"></script>
        <script>
            var domTT_styleClass = \'domTTOverlib\';
            var domTT_oneOnly = true;
        </script>
        <link rel="stylesheet" href="installer/domtt/example.css" type="text/css" />';

        $this->appendArrayVar('headerParams',$str );

        return $str;

    }
}
?>