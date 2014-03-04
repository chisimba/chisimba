<?php
/**
 * beatstream controller class
 *
 * Class to control the beatstream module
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
 * @package   beatstream
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
 * beatstream controller class
 *
 * Class to control the beatstream module.
 *
 * @category  Chisimba
 * @package   beatstream
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
class beatstream extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;

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
            $this->objOps        = $this->newObject('beatops');
            $this->objDbFeatures = $this->getObject('dbbeats');
            $this->objUI         = $this->getObject('beatui');
            $this->objPodcast    = $this->getObject('dbpodcast', 'podcast');
            $this->getObject('jquery', 'jquery');
			
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
                $ip	= sprintf('%u',ip2long($_SERVER['REMOTE_ADDR']));
                $sug = $this->objDbFeatures->getSuggestions($ip);
                $str = $this->objUI->formatData($sug);
                $this->setVarByRef('str', $str);
                return 'view_tpl.php';
                break;
            
            case 'uploadbeat' :
                $beat = $this->getParam('beatfile');
                $beatid = $this->objPodcast->addPodcast($beat);
                $insarr = array('suggestion' => $beatid);
                if($beatid == 'fileusedalready' || $beatid == 'nofile') {
                    $this->nextAction('');
                    break;
                }
	            $insid = $this->objDbFeatures->insertSuggestion($insarr);
	            $arr = array(
			        'id'			=> $insid,
			        'suggestion'	=> $beatid
		            );
		        $this->objOps->setData($arr);
	            $this->nextAction('');
                break;
                
            case 'vote':
                // If the request did not come from AJAX, exit:
                //var_dump($_SERVER); die();
                //if($_SERVER['REQUEST_METHOD'] !='XMLHttpRequest'){
                //    echo "I only speak AJAX";
	                //$this->nextAction('');
	        //        break;
               // }
                $ip	= sprintf('%u',ip2long($_SERVER['REMOTE_ADDR']));
                $vote = $this->getParam('vote');
                $v = intval($vote);
                
                $id = $this->getParam('id');
	            // $id = intval($id);

	            if($v != -1 && $v != 1){
		            echo "Invalid vote";
		            break;
	            }
	            // check if the record exists
	            if($this->objDbFeatures->checkRecord($id) > 0) {
	                // insert to the suggestions votes table
	                $insarr = array('suggestion_id' => $id, 'ip' => $ip, 'vote' => $v);
	                $this->objDbFeatures->upsertVote($insarr);
	            }
	            // $this->nextAction('');
                break;
                
            case 'submit':
                $ip	= sprintf('%u',ip2long($_SERVER['REMOTE_ADDR']));
                $content = $this->getParam('content');
                $content = htmlspecialchars(strip_tags($content));
                // check for chars
                if(mb_strlen($content, 'utf-8') < 3) {
		            exit;
	            }
	            $insarr = array('suggestion' => $content);
	            $insid = $this->objDbFeatures->insertSuggestion($insarr);
	            $arr = array(
			        'id'			=> $insid,
			        'suggestion'	=> $content
		            );
		        $this->objOps->setData($arr);
		           
	            //echo json_encode(array(
		        //    'html'	=> (string)($this->objOps)
	            //));
	            $this->nextAction('');
                break;
                
            case 'deletebeat' :
                $beatid = $this->getParam('id');
                $this->objDbFeatures->deleteBeat($beatid);
                $this->nextAction('');
                break;
                
            case 'viewsingle' :
                $ip	= sprintf('%u',ip2long($_SERVER['REMOTE_ADDR']));
                $id = $this->getParam('id');
                $sug = $this->objDbFeatures->getSingle($ip, $id);
                $str = $this->objUI->formatData($sug);
                $this->setVarByRef('str', $str);
                return 'viewsingle_tpl.php';
                
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
        $allowedActions = array('','vote', 'submit', 'viewsingle');

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
?>
