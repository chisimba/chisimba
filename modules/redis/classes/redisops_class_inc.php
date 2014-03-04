<?php

/**
 * Facade class to the php-redis library.
 * 
 * Library methods for interacting with the redis key-value store.
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
 * @package   redis
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: redisops_class_inc.php 18719 2010-08-16 20:54:49Z charlvn $
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
 * Facade class to the php-redis library.
 * 
 * Library methods for interacting with the redis key-value store.
 * 
 * @category  Chisimba
 * @package   redis
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: redisops_class_inc.php 18719 2010-08-16 20:54:49Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class redisops extends object
{
    /**
     * Instance of the php_redis class.
     *
     * @access private
     * @var    object
     */
    private $objRedis;

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
        $host = $this->objSysConfig->getValue('host', 'redis');
        $port = $this->objSysConfig->getValue('port', 'redis');

        // Load the php-redis library.
        include $this->getResourcePath('redis.php');
        $this->objRedis = new php_redis($host, $port);
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
        return $this->objRedis->get($key);
    }

    /**
     * Deletes a key-value pair.
     *
     * @access public
     * @param  string $key The key.
     */
    public function delete($key)
    {
        $this->objRedis->delete($key);
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
        $this->objRedis->set($key, $value);
    }
}
