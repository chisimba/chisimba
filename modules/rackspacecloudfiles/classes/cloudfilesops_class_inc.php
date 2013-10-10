<?php
/**
 *
 * rackspacecloudfiles helper class
 *
 * PHP version 5.1.0+
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
 * @package   rackspacecloudfiles
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * rackspacecloudfiles helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package rackspacecloudfiles
 *
 */
class cloudfilesops extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;
    
    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        
        // get the required username and apikey
        $this->apikey        = $this->objSysConfig->getValue('apikey', 'rackspacecloudfiles');
        $this->username      = $this->objSysConfig->getValue('username', 'rackspacecloudfiles');
        require_once($this->getResourcePath('cloudfiles.php', 'rackspacecloudfiles'));
        $this->connect();
    }
    
    private function connect() {
        // Connect to Rackspace
        $this->auth = new CF_Authentication($this->username, $this->apikey);
        $this->auth->authenticate();
        // this code will be used on rackspace servers, so we connect locally for MOAR speed!
        $sn_url = "https://snet-" . substr($this->auth->storage_url, strlen("https://"));
        $this->auth->storage_url = $sn_url;
        $this->conn = new CF_Connection($this->auth);
    }
    
    public function createContainer($container) {
        $this->conn->create_container($container);
    }
    
    public function getContainer($container) {
        $this->container = $this->conn->get_container($container);
        return $this->container;
    }
    
    public function getAllContainers() {
        $clist = $this->conn->get_containers();
        var_dump($clist);
    }
    
    public function uploadFile($username, $filename, $file) {
        $container = $this->getContainer($username); 
        $object = $container->create_object($filename);
        $object->load_from_filename($file);
    }
    
    public function containerList($limit = 0, $marker = NULL) {
        return $this->conn->list_containers($limit, $marker);
    }
}
?>
