<?php

/**
 * Multimap library class.
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
 * @category  chisimba
 * @package   multimap
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2011 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbtriplestore_class_inc.php 21486 2011-05-15 19:32:27Z charlvn $
 * @link      http://avoir.uwc.ac.za/
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
 * Multimap library class.
 *
 * @category  chisimba
 * @package   multimap
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2011 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbtriplestore_class_inc.php 21486 2011-05-15 19:32:27Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class multimaplib extends object
{
    /**
     * Nothing to initialise.
     *
     * @access public
     */
    public function init()
    {
    }

    /**
     * Returns the HTML to display a map with a single POI.
     *
     * @access public
     * @param  string $width       The width of the iframe.
     * @param  string $height      The height of the iframe.
     * @param  float  $latitude    The latitude of the POI.
     * @param  float  $logitude    The longitude of the POI.
     * @param  string $description The description of the POI.
     * @return string The iframe HTML.
     */
    public function poi($width, $height, $latitude, $longitude, $description)
    {
        $document = new DOMDocument();
        $iframe = $document->createElement('iframe');
        $iframe->setAttribute('src', $this->getResourceUri('map.html', 'multimap').'#'.rawurlencode($latitude.'|'.$longitude.'|'.$description));
        $iframe->setAttribute('style', 'border:none;width:'.$width.';height:'.$height);
        $document->appendChild($iframe);
        return $document->saveHTML();
    }
}

?>
