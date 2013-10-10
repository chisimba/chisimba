<?php

/**
 * URL Shortener Operations Class
 * 
 * Converts a long URL into a short URL.
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
 * @package   urlshortener
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: membaseops_class_inc.php 18777 2010-08-21 15:36:07Z charlvn $
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
 * URL Shortener Operations Class
 * 
 * Converts a long URL into a short URL.
 * 
 * @category  Chisimba
 * @package   urlshortener
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: membaseops_class_inc.php 18777 2010-08-21 15:36:07Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class urlshortenerops extends object
{
    /**
     * Instance of the altconfig class of the config module.
     *
     * @access private
     * @var    object
     */
    private $objAltConfig;

    /**
     * Instance of the membaseops class of the membase module.
     *
     * @access private
     * @var    object
     */
    private $objMembase;

    /**
     * Initialises the object properties.
     *
     * @access public
     */
    public function init()
    {
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->objMembase = $this->getObject('membaseops', 'membase');
    }

    /**
     * Converts a short URL into a long URL.
     *
     * @access public
     * @param  string $short The short URL.
     * @return string The long URL.
     */
    public function getLong($short)
    {
        $long = $this->objMembase->get($short);

        return $long;
    }

    /*
     * Converts a long URL into a short URL.
     *
     * @access public
     * @param  string $long The long URL.
     * @return string The short URL.
     */
    public function getShort($long)
    {
        $short = $this->objMembase->get($long);

        if ($short === FALSE) {
            $next = $this->objMembase->increment('_next');
            $short = $this->objAltConfig->getsiteRoot().base_convert($next, 10, 36);

            $this->objMembase->set($short, $long);
            $this->objMembase->set($long, $short);
        }

        return $short;
    }
}
