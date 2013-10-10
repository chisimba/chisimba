<?php

/**
 * Phirehose Stream
 * 
 * Class extending the Phirehose library class with a custom enqueueStatus method.
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
 * @version   $Id: phirehosestream_class_inc.php 16902 2010-02-21 14:38:36Z charlvn $
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
 * Phirehose Stream
 * 
 * Class extending the Phirehose library class with a custom enqueueStatus method.
 * 
 * @category  Chisimba
 * @package   phirehose
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: phirehosestream_class_inc.php 16902 2010-02-21 14:38:36Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class phirehosestream extends Phirehoselib
{
    /**
     * The method to call when a new tweet is received.
     *
     * @access protected
     * @var    array
     */
    protected $callback;

    /**
     * Gets called by the parent class when a new tweet is received.
     *
     * @access public
     * @param  string $json The JSON object representing the new tweet.
     */
    public function enqueueStatus($json)
    {
        $data = json_decode($json);
        call_user_func($this->callback, $data);
    }

    /**
     * Sets the method to call when a new tweet is received.
     *
     * @param array $callback The method to call in standard callback format (object, method).
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }
}

?>
