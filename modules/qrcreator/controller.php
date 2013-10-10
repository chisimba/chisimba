<?php
/**
 * QRCreator controller class
 *
 * Class to control the QRCreator module
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
 * @package   qrcreator
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
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
 * qrcreator controller class
 *
 * Class to control the qrcreator module.
 *
 * @category  Chisimba
 * @package   qrcreator
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class qrcreator extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;
    public $objQrOps;

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            $this->objLanguage   = $this->getObject ( 'language', 'language' );
            $this->objConfig     = $this->getObject('altconfig', 'config');
            $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser       = $this->getObject('user', 'security');
            $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
            $this->objQrOps      = $this->getObject('qrops');
            $this->objDbQr       = $this->getObject('dbqr');
			
            if($this->objModuleCat->checkIfRegistered('activitystreamer'))
            {
                $this->objActStream = $this->getObject('activityops','activitystreamer');
                $this->eventDispatcher->addObserver(array($this->objActStream, 'postmade' ));
                $this->eventsEnabled = TRUE;
            } else {
                $this->eventsEnabled = FALSE;
            }
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

            case 'main' :
                $createbasic = $this->objQrOps->basicMsgForm();
                // $createbasic = $this->objQrOps->geoLocationForm(); //$this->objQrOps->showInviteForm();
                $this->setVarByRef('createbasic', $createbasic);
                return 'createbasicmsg_tpl.php';
                break;

            case 'create' :
                $msg = $this->getParam('msg', $this->objLanguage->languageText('mod_qrcreator_defaultmessage', 'qrcreator'));
                $latlon = $this->getParam('latlon', $this->objLanguage->languageText('mod_qrcreator_defaultlocation', 'qrcreator'));
                $ll = explode(',', $latlon);
                $userid = $this->objUser->userId();
                $lon = $ll[0];
                $lat = trim($ll[1]);
                var_dump($this->objQrOps->genQr($userid, $msg, $lat, $lon));
                break;
                
            case 'createbasic' :
                $msg = $this->getParam('msg', $this->objLanguage->languageText('mod_qrcreator_defaultmessage', 'qrcreator'));
                $userid = $this->objUser->userId();
                $imagearr = $this->objQrOps->genBasicQr($userid, strip_tags($msg));
                $this->setVarByRef('imagearr', $imagearr);
                
                return 'image_tpl.php';
                break;
                
            case 'viewcode' :
                $id = $this->getParam('id');
                // retrieve the code and display
                // get the record from the database to make sure we have a proper userid...
                $row = $this->objDbQr->getRecord($id);
                if(isset($row[0]) && is_array($row[0])) {
                    $imgsrc = $this->objConfig->getsiteRoot().$this->objConfig->getcontentPath().'users/'.$row[0]['userid'].'/'.'qr'.$row[0]['id'].'.png';
                    $message = NULL;
                }
                else {
                    $imagesrc = NULL;
                    $message = $this->objLanguage->languageText("mod_qrcreator_notfound", "qrcreator");
                }
                $this->setVarByRef('imgsrc', $imgsrc);
                $this->setVarByRef('message', $message);
                return 'pubview_tpl.php';
                break;
                
            case 'downloadcode' :
                $file = $this->getParam('file');
                $userid = $this->getParam('userid');
                
                // header("Content-type: application/force-download");
                echo $file;
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
    function requiresLogin($action='viewcode') {
        $allowedActions = array('viewcode');

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
?>
