<?php
/**
 * photostack controller class
 *
 * Class to control the photostack module
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
 * @package   photostack
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
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
 * photostack controller class
 *
 * Class to control the artdir module.
 *
 * @category  Chisimba
 * @package   photostack
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
class photostack extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;
    
    /**
     * Object of terms dialogue class in the blog module.
     *
     * @access protected
     * @var object $objTermsDialogue
     */
    protected $objTermsDialogue;

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            $this->objLanguage      = $this->getObject ( 'language', 'language' );
            $this->objConfig        = $this->getObject('altconfig', 'config');
            $this->objSysConfig     = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser          = $this->getObject('user', 'security');
            $this->objModuleCat     = $this->getObject('modules', 'modulecatalogue');
            $this->stackUi          = $this->getObject('stackui');
            $this->objDbStack       = $this->getObject('dbstack');
            $this->objFile          = $this->getObject('dbfile', 'filemanager');
        }
        catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            case NULL:
                if($this->objUser->userId() != NULL) {
                    $userid = $this->objUser->userId();
                }
                else {
                    $userid = $this->objSysConfig->getValue('default_user', 'photostack');
                    if($userid == '') {
                        $userid = 1;
                    }
                }
                $this->setVarByRef('userid', $userid);
                return 'default_tpl.php';
                break;
                
            case 'showsignin' :
                $objUi = $this->getObject('stackui');
                echo $objUi->signinBox();
                break;
                
            case 'album' :
                $puid = $this->getParam('puid');
                // get the album details
                $album = $this->objDbStack->getAlbumFromPuid($puid);
                $album = $album[0];
                // now we get the files from the album
                $albumid = $album['id'];
                $pics = $this->objDbStack->getImagesByAlbum($albumid);
                foreach($pics as $p) {
                    // get the file path for each picture
                    $img[] = $this->objFile->getFilePath($p['imageid']);
                }
                
                $encoded 	= json_encode($img);
                echo $encoded;
                unset($encoded);
                break;
                
            case 'createalbum' :
                $mode = $this->getParam('mode', NULL);
                $albumarr = NULL;
                if (isset($mode) && $mode == 'edit') {
                    $id = $this->getParam('id');
                    $albumarr = $this->objDbStack->getAlbumById($id);
                    $albumarr = $albumarr[0];
                }
                $userid = $this->objUser->userId();
                $this->setVarByRef('userid', $userid);
                $this->setVarByRef('mode', $mode);
                $this->setVarByRef('albumarr', $albumarr);
                return 'albumcreate_tpl.php';
                break;
                
            case 'savealbum' :
                $userid = $this->objUser->userId();
                $albumname = $this->getParam('albumname');
                $desc = $this->getParam('desc');
                $thumb = $this->getParam('thumb');
                $albumarr = array('userid' => $userid, 'albumname' => $albumname, 'description' => $desc, 'thumbnail' => $thumb);
                $this->objDbStack->createAlbum($albumarr);
                $this->nextAction('');
                break;
                
            case 'editalbum' :
                $id = $this->getParam('id');
                $userid = $this->objUser->userId();
                $albumname = $this->getParam('albumname');
                $desc = $this->getParam('desc');
                $thumb = $this->getParam('thumb');
                $albumarr = array('id' => $id, 'userid' => $userid, 'albumname' => $albumname, 'description' => $desc, 'thumbnail' => $thumb);
                $this->objDbStack->updateAlbum($albumarr);
                $this->nextAction('');
                break;
                
            case 'deletealbum' :
                $id = $this->getParam('id');
                $this->objDbStack->deleteAlbum($id);
                $this->nextAction('');
                break;
                
            case 'managealbum' :
                $albumid = $this->getParam('id');
                $this->setVarByRef('albumid', $albumid);
                return 'imgadd_tpl.php';
                break;
            
            case 'addimage' :
                $imageid = $this->getParam('image');
                $albumid = $this->getParam('albumid');
                $userid = $this->objUser->userId();
                $imgarr = array('userid' => $userid, 'albumid' => $albumid, 'imageid' => $imageid);
                $this->objDbStack->addImage($imgarr);
                $this->nextAction(array('action' => 'managealbum', 'albumid' => $albumid));
                break;
                
            default:
                $this->nextAction('');
                break;
        }
    }

    /**
     * Method to turn off login for selected actions
     *
     * @access public
     * @param string $action Action being run
     * @return boolean Whether the action requires the user to be logged in or not
     */
    function requiresLogin($action='') {
        $allowedActions = array('', 'showsignin', 'album', NULL);

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
?>
