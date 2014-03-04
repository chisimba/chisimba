<?php
/**
 * recipes controller class
 *
 * Class to control the metadata module
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
 * @package   recipes
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
 * Recipes controller class
 *
 * Class to control the Events module.
 *
 * @category  Chisimba
 * @package   recipes
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class recipes extends controller
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
            $this->requiresLogin();
            $this->objLanguage   = $this->getObject ( 'language', 'language' );
            $this->objConfig     = $this->getObject('altconfig', 'config');
            // Get the sysconfig variables for the Jabber user to set up the connection.
            $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser       = $this->getObject('user', 'security');
            $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
            $this->objOps        = $this->getObject('recipesops');
            $this->objDbCook     = $this->getObject('dbrecipes');
			
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
                return 'listcookbooks_tpl.php';
                break;
            
            case 'addcookbook' :
                return 'addcookbook_tpl.php';
                break;
                
            case 'editcookbook':
                $cbid = $this->getParam('cbid');
                $editparams = $this->objDbCook->getCookBook($cbid);
                $this->setVarByRef('editparams', $editparams);
                return 'addcookbook_tpl.php'; 
                break;
                
            case 'updatecookbook' :
                $cbid = $this->getParam('cbid');
                $cookbookname = $this->getParam('cookbookname');
                $userid = $this->objUser->userId();
                $license = $this->getParam('license');
                $cdesc = $this->getParam('cdesc');
                
                $insarr = array('cookbookname' => $cookbookname, 'cookbookdesc' => $cdesc, 'userid' => $userid, 'license' => $license);
                $this->objDbCook->updateCookbook($cbid, $insarr);
                $this->nextAction('');
                break;

            case 'newcookbook' :
                $cookbookname = $this->getParam('cookbookname');
                $userid = $this->objUser->userId();
                $license = $this->getParam('license');
                $cdesc = $this->getParam('cdesc');
                
                $insarr = array('cookbookname' => $cookbookname, 'cookbookdesc' => $cdesc, 'userid' => $userid, 'license' => $license);
                $this->objDbCook->addCookbook($insarr);
                $this->nextAction('');
                break;
                
            case 'deletecookbook' :
                $cbid = $this->getParam('cbid');
                $userid = $this->getParam('userid');
                $this->objDbCook->deleteCookBook($userid, $cbid);
                $this->nextAction('listcookbooks');
                break;
                
            case 'viewcookbook' :
                $cbid = $this->getParam('cbid');
                $rlist = $this->objDbCook->getRecipesPerBook($cbid);
                
                var_dump($rlist);
                break;
                
            case 'favcookbook' :
                $id = $this->getParam('id');
                $this->objDbCook->favCookBook($id, $this->objUser->userId());
                $this->nextAction('listcookbooks');
                break;
                
            case 'listcookbooks' :
                return 'listcookbooks_tpl.php';
                break;
                
            case 'addrecipe' :
                return 'addrecipe_tpl.php';
                break;
                
            case 'updaterecipe' :
                
                break;
                
            case 'deleterecipe' :
                
                break;
                
            case 'newrecipe' :
            
                break;
                
            case 'favouriterecipe' :
                
                break;
                
            case 'addrecipetocookbook' :
                
                break;
                
            case 'favouritecookbook' :
                
                break;
                
                
            default:
                $this->nextAction('');
                break;
        }
    }

    public function requiresLogin() {
        return FALSE;
    }
}
?>
