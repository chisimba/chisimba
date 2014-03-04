<?php

/**
 * Zoopy library
 * 
 * Zoopy library class for Chisimba.
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
 * @package   zoopy
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2009 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @link      http://www.zoopy.com
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
 * Zoopy library
 * 
 * Zoopy library class for Chisimba.
 * 
 * @category  Chisimba
 * @package   zoopy
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2009 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @link      http://www.zoopy.com
 */

class zoopylib
{
    /**
     * The feed items to be displayed.
     *
     * @access protected
     * @var array
     */
    protected $items;

    /**
     * Initialises the instance variables.
     */
    public function init()
    {
        $this->items = array();
    }

    /**
     * Loads and parses the RSS feed from the URI specified.
     *
     * @access public
     * @param string $uri The URI to load the feed from.
     */
    public function loadFeed($uri)
    {
        $dom = new DOMDocument();
        $dom->load($uri);
        $domItems = $dom->getElementsByTagName('item');
        for ($i = 0; $i < $domItems->length; $i++) {
            $domItem = $domItems->item($i);
            $item = array();
            $item['title'] = $domItem->getElementsByTagName('title')->item(0)->textContent;
            $item['link'] = $domItem->getElementsByTagName('link')->item(0)->textContent;
            $item['image'] = $domItem->getElementsByTagName('content')->item(0)->getAttribute('url');
            preg_match_all('#/([0-9]+)/thumb#i', $item['image'], $matches);
            $item['image'] = 'http://www.zoopy.com/data/media/' . $matches[1][0] . '/thumb-150x150f.jpg';
            $this->items[] = $item;
        }
    }

    /**
     * Generates the HTML output to send back to the user agent.
     *
     * @access public
     * @return string The HTML output.
     */
    public function show()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $ul = $dom->createElement('ul');
        $ul->setAttribute('class', 'zoopy');
        foreach ($this->items as $item) {
            $img = $dom->createElement('img');
            $img->setAttribute('src', $item['image']);
            $img->setAttribute('alt', $item['title']);
            $img->setAttribute('title', $item['title']);
            $a = $dom->createElement('a');
            $a->setAttribute('href', $item['link']);
            $a->appendChild($img);       
            $li = $dom->createElement('li');
            $li->appendChild($a);
            $ul->appendChild($li);
        }

        return $dom->saveXML($ul);
    }
}
