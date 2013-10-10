<?php
/**
 * Racemap controller class
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
 * @package   racemap
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
 * racemap controller class
 *
 * Class to control the qrcreator module.
 *
 * @category  Chisimba
 * @package   racemap
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
ini_set('max_execution_time', -1);
class racemap extends controller
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
			$this->raceOps       = $this->getObject('racemapops');
			$this->objKml        = $this->getObject('racemapkml');
			$this->objDbRace     = $this->getObject('dbracemap');
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
                // case to handle basic intro stuff
                return 'main_tpl.php';
                break;

            case 'captureline' :
                return 'map_tpl.php';
                break;
                
            case 'capturepoint' :
                return 'mappt_tpl.php';
                break;
                
            case 'profile' :
                $metaid = $this->getParam('metaid');
                $ele = $this->objConfig->getSiteRoot().'/usrfiles/graphs/'.$metaid.'_ele.png';
                $speed = $this->objConfig->getSiteRoot().'/usrfiles/graphs/'.$metaid.'_speed.png';
                $firstpoint = $this->objDbRace->getPoints($metaid, 0, 1);
                $first = $firstpoint[0];
                $this->setVarByRef('metaid', $metaid);
                $this->setVarByRef('ele', $ele);
                $this->setVarByRef('speed', $speed);
                $this->setVarByRef('first', $first);
                return 'profile_tpl.php';
                break;
                
            case 'ptprofile':
                $metaid = $this->getParam('metaid');
                $metadata = $this->objDbRace->getMetaFromId($metaid);
                $metadata = $metadata[0];
                
                $map = $this->raceOps->drawProfile($metaid, $width = 1000, $height = 350, $metadata['name'], 
                                                   $metadata['creationtime'], NULL, 
                                                   $this->objLanguage->languageText('mod_racemap_elevation', 'racemap'));
                                                   
                $speed = $this->raceOps->drawSpeed($metaid, $width = 1000, $height = 350, $metadata['name'], 
                                                   $metadata['creationtime'], NULL, 
                                                   $this->objLanguage->languageText('mod_racemap_speed', 'racemap'));
                $this->nextAction('kmlfromtrack', array('metaid' => $metaid));
                break;
                
            case 'kmlfromtrack':
                $metaid = $this->getParam('metaid'); // 'gen12Srv55Nme28_38959_1291895051';
                $this->objKml->kmlFromTrk($metaid);
                $this->nextAction('profile', array('metaid' => $metaid));
                break;
            
            case 'uploaddatafile':
                // upload gpx or other file for processing
                return 'upload_tpl.php';
                break;    
                
            case 'processfile':
                $file = $this->getParam('file');
                $objFile = $this->getObject('dbfile', 'filemanager');
                $filename = $objFile->getFileName($file);
                $filetype = substr(strrchr($filename,'.'),1);
                if(strtolower($filetype) == 'tcx') {
                    echo "tcx file";
                }
                else  {
                    // gpx file...
                    $filepath = $objFile->getFullFilePath($file);
                    $metaid = $this->raceOps->gpxParseMeta($filepath);
                    $this->nextAction('addmetainfo', array('metaid' => $metaid));
                }
                break;
                
            case 'addmetainfo':
                $metaid = $this->getParam('metaid');
                $metainfo = $this->objDbRace->getMetaFromId($metaid);
                $this->setVarByRef('metainfo', $metainfo);
                return 'metaedit_tpl.php';
                break;
                
            case 'updatemeta':
                $id = $this->getParam('id');
                $cc = $this->getParam('creativecommons');
                $name = $this->getParam('trkname');
                $description = $this->getParam('description');
                $keywords = $this->getParam('keywords');
                
                $updatearr = array('id' => $id, 'name' => $name, 'copyright' => $cc, 'description' => $description, 'keywords' => $keywords);
                $this->objDbRace->updateMeta($updatearr);
                $this->nextAction('ptprofile', array('metaid' => $id));
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
        $allowedActions = array('');

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
?>
