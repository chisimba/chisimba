<?php

/**
 * Official Twitter Button Generator
 * 
 * Generates the necessary HTML to include the official Twitter button.
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
 * @package   twitter
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: tweetbutton_class_inc.php 24594 2012-09-16 12:41:00Z dkeats $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://twitter.com/goodies/tweetbutton
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
// end security check

/**
 * Official Twitter Button Generator
 * 
 * Generates the necessary HTML to include the official Twitter button.
 * 
 * @category  Chisimba
 * @package   twitter
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: tweetbutton_class_inc.php 24594 2012-09-16 12:41:00Z dkeats $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://twitter.com/goodies/tweetbutton
 */
class tweetbutton extends object
{
    /**
     * Generates the necessary HTML to include the official Twitter button.
     *
     * @access public
     * @param  string $text    The link text.
     * @param  string $style   The style of the button (vertical, horizontal or none).
     * @param  string $via     A Twitter account that will be mentioned in the suggested tweet.
     * @param  string $related A related Twitter account.
     * @param  string $uri     The URI to post. Defaults to the current page.
     * @return string The generated HTML.
     */
    public function getButton($text='Tweet', $style='vertical', $via=NULL, $related=NULL, $uri=NULL)
    {
        // Create the HTML document.
        $doc = new DOMDocument('UTF-8');


        // Create the link.
        $a = $doc->createElement('a');
        $a->appendChild($doc->createTextNode($text));
        $a->setAttribute('class', 'twitter-share-button');
        $a->setAttribute('data-count', $style);
        $a->setAttribute('href', 'http://twitter.com/share');
        if ($text) {
            $a->setAttribute('data-text', $text);
        }
        if ($via) {
            $a->setAttribute('data-via', $via);
        }
        if ($related) {
            $a->setAttribute('data-related', $related);
        }
        if ($uri) {
            $a->setAttribute('data-url', $uri);
        }

        // Create the script element.
        $script = $doc->createElement('script');
        $script->setAttribute('src', 'http://platform.twitter.com/widgets.js');
        $script->setAttribute('type', 'text/javascript');

        // Add the link and the script to the document.
        $doc->appendChild($a);
        $doc->appendChild($script);

        // Return the serialised document.
        return '<div class=\'tweet_button\'>' . $doc->saveHTML() . '</div>';
    }
}
