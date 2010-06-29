<?php

/**
 * Cache Helper Class
 * 
 * Uses Memcache or APC for caching key/value pairs. Memcache is preferred and APC is used as fallback.
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
 * @package   cache
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za/
 * @see       http://memcached.org/
 * @see       http://php.net/apc
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
 * Cache Helper Class
 * 
 * Uses Memcache or APC for caching key/value pairs. Memcache is preferred and APC is used as fallback.
 * 
 * @category  Chisimba
 * @package   cache
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za/
 * @see       http://memcached.org/
 * @see       http://php.net/apc
 */
class cache extends object
{
    /**
     * Is APC enabled?
     *
     * @access protected
     * @var    boolean
     */
    protected $apc;

    /**
     * The local cache.
     *
     * @access protected
     * @var    array
     */
    protected $cache;

    /**
     * Is Memcache enabled?
     *
     * @access protected
     * @var    boolean
     */
    protected $memcache;

    /**
     * The system configuration.
     *
     * @access protected
     * @var    object
     */
    protected $objAltConfig;

    /**
     * Initialises object properties.
     *
     * @access public
     */
    public function init()
    {
        // Initialise arrays and objects.
        $this->objCache     = array();
        $this->objAltConfig = $this->getObject('altconfig', 'config');

        // Set boolean flags.
        $this->apc      = extension_loaded('apc') && $this->objAltConfig->getenable_apc() == 'TRUE';
        $this->memcache = extension_loaded('memcache') && $this->objAltConfig->getenable_memcache() == 'TRUE';
    }

    /**
     * Retrieves the value associated with the given key from cache.
     *
     * @access public
     * @param  string $key The key to check.
     * @return mixed  If the key is found, a string representing the associated value. FALSE otherwise.
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->cache)) {
            $value = $this->cache[$key];
        } elseif ($this->memcache) {
            $value = chisimbacache::getMem()->get($key);
        } elseif ($this->apc) {
            $value = apc_fetch($key);
        } else {
            $value = FALSE;
        }

        return $value;
    }

    /**
     * Stores a new key/value pair in the cache. If the key already exists, the value will be overwritten.
     *
     * @access public
     * @param  string $key   The key.
     * @param  string $value The new value.
     */
    public function __set($key, $value)
    {
        // Update the local cache.
        $this->cache[$key] = $value;

        // Update the foreign cache.
        if ($this->memcache) {
            chisimbacache::getMem()->set($key, $value);
        } elseif ($this->apc) {
            apc_store($key, $value);
        }
    }
}

?>
