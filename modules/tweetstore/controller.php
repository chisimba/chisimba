<?php

/**
 * Tweetstore Controller Class
 * 
 * Handles HTTP requests for the tweetstore module.
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
 * @package   tweetstore
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 18816 2010-08-28 23:51:37Z charlvn $
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
 * Tweetstore Controller Class
 *
 * Handles HTTP requests for the tweetstore module.
 *
 * @category  Chisimba
 * @package   tweetstore
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 18816 2010-08-28 23:51:37Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class tweetstore extends controller
{
    /**
     * Instance of the json class of the utilities module.
     *
     * @access private
     * @var    object
     */
    private $objJson;

    /**
     * Instance of the mongoops class of the mongo module.
     *
     * @access private
     * @var    object
     */
    private $objMongo;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objSysConfig;

    /**
     * List of whitelisted IPs to accept new tweets from.
     *
     * @access private
     * @var    array
     */
    private $whitelist;

    /**
     * Initialises the object properties.
     *
     * @access public
     */
    public function init()
    {
        // Initialise the objects.
        $this->objJson = $this->getObject('json', 'utilities');
        $this->objMongo = $this->getObject('mongoops', 'mongo');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

        // Set the mongo collection from the module configuration.
        $collection = $this->objSysConfig->getValue('collection', 'tweetstore');
        $this->objMongo->setCollection($collection);

        // Populate the whitelist from the module configuration.
        $whitelist = $this->objSysConfig->getValue('whitelist', 'tweetstore');
        $this->whitelist = explode('|', $whitelist);
        if (!in_array($_SERVER['SERVER_ADDR'], $this->whitelist)) {
            $this->whitelist[] = $_SERVER['SERVER_ADDR'];
        }
    }

    /**
     * Handles HTTP requests to this module.
     *
     * @access public
     */
    public function dispatch()
    {
        switch ($this->getParam('action')) {
            case 'add':
                if (in_array($_SERVER['REMOTE_ADDR'], $this->whitelist)) {
                    $json = file_get_contents('php://input');
                    $data = $this->objJson->decode($json);
                    if (is_array($data)) {
                        $this->objMongo->insert($data);
                    }
                } else {
                    header('HTTP/1.1 403 Forbidden');
                }
                break;
            default:
                $cursor = $this->objMongo->find();
                $data = iterator_to_array($cursor);
                $json = $this->objJson->encode($data);
                header('Content-Type: application/json; charset=UTF-8');
                echo $json;
        }
    }

    /**
     * Returns FALSE such that all actions are allowed without login.
     *
     * @access public
     * @param  string  $action The name of the action requested.
     * @return boolean Always will return FALSE.
     */
    public function requiresLogin($action)
    {
        return FALSE;
    }
}
