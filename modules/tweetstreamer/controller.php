<?php

/**
 * Tweet Streamer Controller Class
 *
 * Class to control the Tweet Streamer module.
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
 * @package   tweetstreamer
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 19666 2010-11-08 21:36:36Z charlvn $
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
// end security check

/**
 * Weather Tweet Streamer Class
 *
 * Class to control the Tweet Streamer module.
 *
 * @category  Chisimba
 * @package   tweetstreamer
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 19666 2010-11-08 21:36:36Z charlvn $
 * @link      http://avoir.uwc.ac.za
 */

class tweetstreamer extends controller
{
    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objSysConfig;

    /**
     * Initialises object properties.
     *
     * @access public
     */
    public function init()
    {
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    }

    /**
     * Handles incoming HTTP requests.
     *
     * @access public
     * @return string The name of the template to load.
     */
    public function dispatch()
    {
        // Retrieve module configuration variables.
        $socketio_host = $this->objSysConfig->getValue('socketio_host', 'tweetstreamer');
        $socketio_port = $this->objSysConfig->getValue('socketio_port', 'tweetstreamer');

        // Add the configuration variables to the template.
        $this->setVar('socketio_host', $socketio_host);
        $this->setVar('socketio_port', $socketio_port);

        // Return the name of the template.
        return 'main_tpl.php';
    }

    /**
     * Prevents the framework from requiring a login to access the module.
     *
     * @access public
     * @return boolean FALSE since no login is required.
     */
    public function requiresLogin()
    {
        return FALSE;
    }
}
