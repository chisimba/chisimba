<?php
/**
 * Short description for file
 *
 * Long description (if any) ...
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
 * @package   blog
 * @author    Administrative User <pscott@uwc.ac.za>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: trackback_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
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
 * Trackback class for the blog module
 *
 * @author     Paul Scott
 * @copyright  AVOIR
 * @access     Public
 * @filesource
 */
class trackback extends object
{
    /**
     * The trackback object Instance
     *
     * @var object
     */
    public $objTrackBack;
    /**
     * Standard init function
     *
     * @access public
     * @param  void
     * @return void
     */
    public function init() 
    {
        //require_once($this->getResourcePath('Trackback.php', 'blog'));
        
    }
    /**
     * Setup function encapsulating the factory method (create)
     *
     * @param  array  $data
     * @param  array  $options
     * @return object instance
     */
    public function setup($data, $options) 
    {
        $this->objTrackBack = $this->getObject('servicestrackback');
        $this->objTrackBack->create($data, $options);
        return $this->objTrackBack;
    }
    /**
     * Method to create RDF Autodiscovery code for trackback agents
     *
     * @access public
     * @param  void
     * @return string
     */
    public function autodiscCode() 
    {
        return $this->objTrackBack->getAutodiscoveryCode();
    }
    /**
     * Method to set values in the object context
     *
     * @access public
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function setVal($key, $value) 
    {
        $this->objTrackBack->set($key, $value);
    }
    /**
     * Public method to try and get autodiscovery trackback URLS from a data array
     *
     * @access public
     * @param  void   (inherited from $data array
     * @see    setup
     * @return bool   true on success|error on failure
     */
    public function autoDisc() 
    {
        $res = $this->objTrackBack->autodiscover();
        if (PEAR::isError($res)) {
            return $res->getMessage();
        } else {
            return $res;
        }
    }
    /**
     * Public method to send a trackback to a URL
     *
     * @access public
     * @param  array   $sendData
     * @return message on failure|returned bool on success
     */
    public function sendTB($sendData) 
    {
        $tracker = $this->objTrackBack->send($sendData);
        if (PEAR::isError($tracker)) {
            return $tracker->getMessage();
        } else {
            return $tracker;
        }
    }
    /**
     * Method to receive a trackback from a remote blog
     *
     * @param  array $data
     * @return bool  true on success, message on failure
     */
    public function recTB($data) 
    {
        $res = $this->objTrackBack->receive($data);
        if (PEAR::isError($res)) {
            return $this->objTrackBack->getResponseError($res->getMessage() , 1);
        } else {
            return $this->objTrackBack->getResponseSuccess();
        }
    }
}
?>