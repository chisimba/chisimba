<?php

/**
 * Membase Facade Class 
 * 
 * Library methods for interacting with the membase key-value store.
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
 * @package   membase
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: membaseops_class_inc.php 18784 2010-08-21 17:32:01Z charlvn $
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
 * Membase Facade Class
 * 
 * Library methods for interacting with the membase key-value store.
 * 
 * @category  Chisimba
 * @package   membase
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: membaseops_class_inc.php 18784 2010-08-21 17:32:01Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class membaseops extends object
{
    /**
     * Instance of the memcache class.
     *
     * @access private
     * @var    object
     */
    private $objMemcache;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objSysConfig;

    /**
     * Initialises the object properties.
     *
     * @access public
     */
    public function init()
    {
        // Retrieve the configuration variables.
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $servers = $this->objSysConfig->getValue('servers', 'membase');

        // Initialise the memcache object.
        $this->objMemcache = new Memcache();
        foreach (explode('|', $servers) as $server) {
            $server = explode(':', $server);
            $this->objMemcache->addServer($server[0], $server[1]);
        }
    }

    /**
     * Retrieves the value associated with the given key.
     *
     * @access public
     * @param  string $key The key.
     * @return string The value.
     */
    public function get($key)
    {
        return $this->objMemcache->get($key);
    }

    /**
     * Deletes a key-value pair.
     *
     * @access public
     * @param  string $key The key.
     */
    public function delete($key)
    {
        $this->objMemcache->delete($key);
    }

    /**
     * Increments the value of a key.
     *
     * @access public
     * @param  string  $key The key.
     * @return integer The new value.
     */
    public function increment($key)
    {
        $value = $this->objMemcache->increment($key);

        if ($value === FALSE) {
            $value = 1;
            $this->objMemcache->set($key, 1);
        }

        return $value;
    }

    /**
     * Sets the value of a key.
     *
     * @access public
     * @param  string $key   The key.
     * @param  string $value The value.
     */
    public function set($key, $value)
    {
        $this->objMemcache->set($key, $value);
    }
}
