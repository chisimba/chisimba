<?php

/**
 * PubSubHubbub Helper Class
 * 
 * Library methods for interacting with realtime feed hubs via the PubSubHubbub protocol.
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
 * @package   pubsubhubbub
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: pubsubhubbub_class_inc.php 18119 2010-06-21 13:40:47Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://code.google.com/p/pubsubhubbub/
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
 * PubSubHubbub Helper Class
 * 
 * Library methods for interacting with realtime feed hubs via the PubSubHubbub protocol.
 * 
 * @category  Chisimba
 * @package   pubsubhubbub
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: pubsubhubbub_class_inc.php 18119 2010-06-21 13:40:47Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://code.google.com/p/pubsubhubbub/
 */
class pubsubhubbub extends object
{
    /**
     * Instance of the curlwrapper class of the utilities module.
     *
     * @access protected
     * @var    object
     */
    protected $objCurl;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access protected
     * @var    object
     */
    protected $objSysConfig;

    /**
     * Initialises some object properties.
     *
     * @access public
     */
    public function init()
    {
        $this->objCurl      = $this->getObject('curlwrapper', 'utilities');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    }

    /**
     * Returns the URL of the hub as specified in the module configuration.
     *
     * @access public
     * @return string The URL of the hub.
     */
    public function getHub()
    {
        return $this->objSysConfig->getValue('hub', 'pubsubhubbub');
    }

    /**
     * Notifies a hub that a new entry has been posted to the feed.
     *
     * @access public
     * @param  string  $feed The feed containing the new entry.
     * @return boolean TRUE if the call was successful, FALSE otherwise.
     */
    public function publish($feed)
    {
        $url          = $this->objSysConfig->getValue('hub', 'pubsubhubbub');
        $params       = array('hub.mode' => 'publish', 'hub.url' => $feed);
        $responseCode = $this->objCurl->postCurl($url, $params, TRUE);
        $success      = ($responseCode == 204);

        return $success;
    }
}

?>
