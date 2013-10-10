<?php

/**
 * Feediscovery
 * 
 * Interface to the feediscovery web service
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
 * @package   feediscovery
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2009 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: feediscovery_class_inc.php 16080 2009-12-28 22:10:59Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * @link      http://feediscovery.appspot.com/
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
 * Feediscovery
 * 
 * Interface to the feediscovery web service 
 * 
 * @category  Chisimba
 * @package   skype
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2009 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za/
 * @link      http://feediscovery.appspot.com/
 */

class feediscovery extends object
{
    /**
     * Instance of the curlwrapper class of the utilities module.
     *
     * @access protected
     * @var    object
     */
    protected $curl;

    /**
     * Standard constructor to load the necessary resources
     * and populate the new object's instance variables.
     *
     * @access public
     */
    public function init()
    {
        $this->curl = $this->getObject('curlwrapper', 'utilities');
    }

    /**
     * Retrieve the feed links from a (X)HTML page.
     *
     * @access public
     * @param  string $pageUrl The URL of the (X)HTML page to check.
     * @return array  The feeds found (empty array if none, FALSE on failure).
     */
    public function getFeeds($pageUrl)
    {
        // Determine the URL to use during the call to the web service.
        $webServiceUrl = 'http://feediscovery.appspot.com/?url=' . urlencode($pageUrl);
        
        // Perform the HTTP call to the REST web service.
        $json = $this->curl->exec($webServiceUrl);

        // Check to see if the HTTP fetch was successful.
        if (is_string($json)) {
            // Decode the response to a PHP object data structure.
            $feeds = json_decode($json);
        } else {
            // Set the result to FALSE in case of a failure.
            $feeds = FALSE;
        }

        // Return the result of the call.
        return $feeds;
    }
}
