<?php
/**
 * 
 * Create a digital business card that uses microformats and web standards.
 * 
 * Create a digital business card that uses microformats and web standards, and display it in a nice, CSS based layout.
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
 * @package   helloforms
 * @author    Derek Keats derek[[AT]]dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: controller.php,v 1.4 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */
 
// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 * 
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *         
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
* 
* Controller class for Chisimba for the module digitalbusinesscard
*
* @author Derek Keats
* @package digitalbusinesscard
*
*/
class digitalbusinesscard extends controller
{
    
    /**
    * 
    * @var string $objConfig String object property for holding the 
    * configuration object
    * @access public;
    * 
    */
    public $objConfig;
    
    /**
    * 
    * @var string $objLanguage String object property for holding the 
    * language object
    * @access public
    * 
    */
    public $objLanguage;
    /**
    *
    * @var string $objLog String object property for holding the 
    * logger object for logging user activity
    * @access public
    * 
    */
    public $objLog;

    /**
    * 
    * Intialiser for the digitalbusinesscard controller
    * @access public
    * 
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        // Create an instance of the database class
        $this->objDbdigitalbusinesscard = & $this->getObject('dbdigitalbusinesscard', 'digitalbusinesscard');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the digitalbusinesscard module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'viewtabbed');
        // retrieve the mode (edit/add/translate) from the querystring
        $mode = $this->getParam("mode", null);
        // retrieve the sort order from the querystring
        $order = $this->getParam("order", null);
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return $this->$method();
    }
    
    
    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * 
    * Method corresponding to the view action.
    *
    * @return string The populated template
    * @access private
    * 
    */
    private function __view()
    {
        if ($userId = $this->getUserId()) {
            $objCard = $this->getObject('buscard', 'digitalbusinesscard');
            $str = $objCard->show($userId);
        } else {
            $str =  $this->objLanguage->languageText(
              'mod_digitalbusinesscard_usernotfound',
              'digitalbusinesscard');
        }
        $this->setVarByRef('str', $str);
        return "dump_tpl.php";
    }

    /**
    *
    * Method corresponding to the viewtabbed action. It shows
    * the hcard in a tabbed format
    *
    * @return string The populated template
    * @access private
    *
    */
    private function __viewtabbed()
    {
        if ($userId = $this->getUserId()) {
            $objCard = $this->getObject('buscard', 'digitalbusinesscard');
            $str = $objCard->showTabbed($userId);
        } else {
            $str =  $this->objLanguage->languageText(
              'mod_digitalbusinesscard_usernotfound',
              'digitalbusinesscard');
        }
        $this->setVarByRef('str', $str);
        return "dump_tpl.php";
    }

    /**
    *
    * Method corresponding to the showblock action.
    *
    * @return string The populated template
    * @access private
    *
    */
    private function __showblock()
    {
        $objCard = $this->getObject('buscard', 'digitalbusinesscard');
        $userId = $this->getParam('userid', $this->objUser->userId());
        $str = $objCard->showBlock($userId);
        $this->setVarByRef('str', $str);
        return "dump_tpl.php";
    }

    /**
    *
    * Method corresponding to the showblock action.
    *
    * @return string The populated template
    * @access private
    *
    */
    private function __showjson()
    {
        $objCard = $this->getObject('buscard', 'digitalbusinesscard');
        $userId = $this->getParam('userid', $this->objUser->userId());
        $str = $objCard->showJson($userId);
        $this->setVarByRef('str', $str);
        $this->setPageTemplate('json_tpl.php');
        return "conts_tpl.php";
    }

    
    /**
    * 
    * Method to return an error when the action is not a valid 
    * action method
    * 
    * @access private
    * @return string The dump template populated with the error message
    * 
    */
    private function __actionError()
    {
        $this->setVar('str', "<h3>"
          . $this->objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $this->getParam('action', NULL) . "</h3>");
        return 'dump_tpl.php';
    }
    
    /**
    * 
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (__). If it __action 
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return boolean TRUE|FALSE
    * 
    */
    function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * 
    * Method to convert the action parameter into the name of 
    * a method of this class.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return stromg the name of the method
    * 
    */
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }
    
    /*------------- END: Set of methods to replace case selection ------------*/
    
    /**
    * Get the userId by first trying the querystring, then trying
    * username lookup, falling back to the logged in user
    * 
    * @return string The userId or boolean FALSE if not found
    * 
    */
    private function getUserId()
    {
        $userId = $this->getParam('userid', FALSE);
        if (!$userId) {
            $userName = $this->getParam('username', FALSE);
            // Get the userId from the username.
            if ($userName) {
                $userId = $this->objUser->getUserId($userName);
                return $userId;
            } else {
                // Fall back to the logged in user.
                if ($this->objUser->isLoggedIn()) {
                    $userId = $this->objUser->userId();
                    return $userId;
                }
            }
        } else {
            return $userId;
        }
        return FALSE;
    }


    /**
    *
    * This is a method to determine if the user has to 
    * be logged in or not. Note that this is an example, 
    * and if you use it view will be visible to non-logged in 
    * users. Delete it if you do not want to allow annonymous access.
    * It overides that in the parent class
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        $action=$this->getParam('action','view');
        switch ($action)
        {
            case 'view':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
}
?>
