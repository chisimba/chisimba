<?php

/**
 * Phirehose controller class
 * 
 * Handles HTTP requests for the Phirehose module.
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
 * @package   phirehose
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 16905 2010-02-21 15:33:44Z charlvn $
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
 * Phirehose controller class
 *
 * Handles HTTP requests for the Phirehose module.
 *
 * @category  Chisimba
 * @package   phirehose
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 16905 2010-02-21 15:33:44Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class phirehose extends controller
{
    /**
     * The character to separate list configuration parameters on.
     */
    const CONFIG_SEPARATOR = '|';

    /**
     * Keywords to track on Twitter.
     *
     * @access protected
     * @var    array
     */
    protected $keywords;

    /**
     * Instance of the curlwrapper class of the utilities module.
     *
     * @access protected
     * @var    object
     */
    protected $objCurl;

    /**
     * Instance of the json class of the utilities module.
     *
     * @access protected
     * @var    object
     */
    protected $objJson;

    /**
     * Instance of the phirehoseops class of the phirehose module.
     *
     * @access protected
     * @var    object
     */
    protected $objPhirehoseOps;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access protected
     * @var    object
     */
    protected $objSysConfig;

    /**
     * The password of the Twitter account to connect as.
     *
     * @access protected
     * @var    string
     */
    protected $password;

    /**
     * The username of the Twitter account to connect as.
     *
     * @access protected
     * @var    string
     */
    protected $username;

    /**
     * List of webhooks to push to.
     *
     * @access protected
     * @var    object
     */
    protected $webhooks;

    /**
     * Initialises the object properties.
     *
     * @access public
     */
    public function init()
    {
        // Initialise the object properties.
        $this->objCurl         = $this->getObject('curlwrapper', 'utilities');
        $this->objJson         = $this->getObject('json', 'utilities');
        $this->objPhirehoseOps = $this->getObject('phirehoseops', 'phirehose');
        $this->objSysConfig    = $this->getObject('dbsysconfig', 'sysconfig');

        // Initialise the data properties.
        $this->keywords = explode(self::CONFIG_SEPARATOR, $this->objSysConfig->getValue('keywords', 'phirehose'));
        $this->password = $this->objSysConfig->getValue('password', 'phirehose');
        $this->username = $this->objSysConfig->getValue('username', 'phirehose');
        $this->webhooks = explode(self::CONFIG_SEPARATOR, $this->objSysConfig->getValue('webhooks', 'phirehose'));
    }

    /**
     * Links into the Twitter Streaming API and listens for new tweets.
     *
     * @access public
     */
    public function dispatch()
    {
        if ($this->username && $this->password) {
            $callback = array($this, 'push');
            $this->objPhirehoseOps->track($this->username, $this->password, $this->keywords, $callback);
        }
    }

    /**
     * Responds to a new tweet by pushing it out via HTTP and/or XMPP.
     *
     * @access public
     * @param  array The data representing the new tweet.
     */
    public function push($data)
    {
        $json = $this->objJson->encode($data);
        foreach ($this->webhooks as $webhook) {
            $this->objCurl->postCurl($webhook, $json);
        }
    }

    /**
     * Allows all methods without login.
     *
     * @access public
     * @param  string  $action The name of the action.
     * @return boolean Always returns FALSE.
     */
    public function requiresLogin($action)
    {
        return FALSE;
    }
}

?>
