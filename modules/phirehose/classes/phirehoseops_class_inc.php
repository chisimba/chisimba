<?php

/**
 * Phirehose Operations
 * 
 * Facade Class to the Phirehose Twitter Streaming API Library
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
 * @package   phirehose
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: phirehoseops_class_inc.php 16901 2010-02-21 14:26:16Z charlvn $
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
 * Phirehose Operations
 * 
 * Facade Class to the Phirehose Twitter Streaming API Library
 * 
 * @category  Chisimba
 * @package   phirehose
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: phirehoseops_class_inc.php 16901 2010-02-21 14:26:16Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class phirehoseops extends object
{
    /**
     * Loads the original Phirehose library and phirehosestream classes.
     *
     * @access public
     */
    public function init()
    {
        include_once $this->getResourcePath('Phirehose.php');
        $this->loadClass('phirehosestream', 'phirehose');
    }

    /**
     * Tracks a particular set of keywords on Twitter.
     *
     * @access public
     * @param  string $username The username of the Twitter account to use.
     * @param  string $password The password of the Twitter account to use.
     * @param  array  $keywords An array of the keywords to track.
     * @param  array  $callback The method to call when a new tweet is received.
     */
    public function track($username, $password, $keywords, $callback)
    {
        $stream = new phirehosestream($username, $password, Phirehoselib::METHOD_FILTER);
        $stream->setTrack($keywords);
        $stream->setCallback($callback);
        $stream->consume();
    }
}

?>
