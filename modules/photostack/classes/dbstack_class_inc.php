<?php
/**
 *
 * photostack database class
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
 * @package   photostack
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
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
 * photostack database class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package photostack
 *
 */
class dbstack extends dbtable {

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
     * @var string $objWashout String object property for holding the washout object
     *
     * @access public
     */
    public $objWashout;

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
        parent::init('tbl_photostack_album');
        $this->objLanguage  = $this->getObject('language', 'language');
        $this->objConfig    = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objWashout   = $this->getObject('washout', 'utilities');
        $this->objUser      = $this->getObject('user', 'security');
    }
    
    public function getAlbums($userid) {
        return $this->getAll("WHERE userid = '$userid'");
    }
    
    public function getAlbumById($id) {
        return $this->getAll("WHERE id = '$id'");
    }
    
    public function getAlbumFromPuid($puid) {
        return $this->getAll("WHERE puid = '$puid'");
    }
    
    public function createAlbum($albumarr) {
        return $this->insert($albumarr);
    }
    
    public function updateAlbum($albumarr) {
        return $this->update('id', $albumarr['id'], $albumarr, 'tbl_photostack_album');
    }
    
    public function albumCount() {
    
    }
    
    public function imgCount() {
    
    }
    
    public function deleteAlbum($id) {
        return $this->delete('id', $id, 'tbl_photostack_album');
    }
    
    public function addImage($imgarr) {
        $this->changeTable('tbl_photostack_images');
        $this->insert($imgarr);
        $this->changeTable('tbl_photostack_album');
        return TRUE;
    }
    
    public function getImagesByAlbum($albumid) {
        $this->changeTable('tbl_photostack_images');
        $images = $this->getAll("WHERE albumid = '$albumid'");
        $this->changeTable('tbl_photostack_album');
        return $images;
    }
    
    
    /**
     * Method to dynamically switch tables
     *
     * @param  string  $table
     * @return boolean
     * @access private
     */
    private function changeTable($table)
    {
        try {
            parent::init($table);
            return TRUE;
        }
        catch(customException $e) {
            customException::cleanUp();
            return FALSE;
        }
    }
}
?>
