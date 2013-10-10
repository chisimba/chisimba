<?php
/**
 * tweetlic controller class
 *
 * Class to control the tweetlic module
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
 * @package   tweetlic
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
 * tweetlic controller class
 *
 * Class to control the qrcreator module.
 *
 * @category  Chisimba
 * @package   tweetlic
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
class tweetlic extends controller
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
            $this->objOps        = $this->getObject('tweetlicops');
            $this->objDbTweets   = $this->getObject('dbtweetlic');
			
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
                
            case 'lictweet':
                $lic = $cc = $this->getParam('creativecommons');
                $name = $this->getParam('screen_name');
                $insarr = array('copyright' => $lic, 'screen_name' => $name);
                $this->objDbTweets->upsertRecord($insarr);
                $this->setVarByRef('name', $name);
                return 'thanks_tpl.php';
                break;
                
            case 'viewlic':
                $screen_name = $this->getParam('user');
                $res = $this->objDbTweets->getUserDetails($screen_name);
                if(empty($res)) {
                    $this->setVarByRef('screen_name', $screen_name);
                    return 'nouser_tpl.php';
                }
                else {
                    $this->setVarByRef('screen_name', $screen_name);
                    $this->setVarByRef('res', $res);
                    return 'userdetails_tpl.php';
                }
                break;
                
            case 'usersearch' :
                $name = $this->getParam('searchterm');
                $this->nextAction('viewlic', array('user' => $name));
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
        $allowedActions = array('', 'lictweet', 'viewlic', 'usersearch');

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
?>
