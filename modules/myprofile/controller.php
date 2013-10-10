<?php
/**
 * 
 * My profile
 * 
 * A profile module for users that provides basic profile functionality
 * allowing for others to view, and comment on your profile and recent
 * update information.
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
 * @author    Derek Keats derek@dkeats.com
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
* My profile
*
* A profile module for users that provides basic profile functionality
* allowing for others to view, and comment on your profile and recent
* update information.
*
* @author Derek Keats
* @package myprofile
*
*/
class myprofile extends controller
{

    /**
    *
    * @var string $objLog String object property for holding the 
    * logger object for logging user activity
    * @access private
    * 
    */
    private $objLog;

    /**
     *
     * @var string object Holds the ajax generation object
     * @access private
     *
     */
    private $objAjax;

    /**
    * 
    * Intialiser for the myprofile controller
    * @access public
    * 
    */
    public function init()
    {

        // Load the ajax stuff from the canvas module
        $this->objAjax = $this->getObject('userblockajax', 'canvas');
        // Get the activity logger class.
        $this->objLog=$this->newObject('logactivity', 'logger');
        // Log this module call.
        $this->objLog->log();

    }
    
    
    /**
     * 
     * The standard dispatch method for the myprofile module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     *
     * @access public
     * @return string The method is called and executed and its results returned
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'view');
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

    /**
    * 
    * Method corresponding to the view action. It shows the default
    * dynamic canvas template populated by whatever blocks are added
    * by the owner of the profile.
    *
    * @return string The populated template
    * @access private
    * 
    */
    private function __view()
    {
        // All the action is in the blocks, so just return the template.
        return "main_tpl.php";
    }

   

    /**
    *
    * Method to render a block for use by the ajax methods when 
    * 'Turn editing on' is enabled.
    *
    * @return string The rendered block
    * @access protected
    *
    */
    protected function __renderblock()
    {
        echo $this->objAjax->renderblock();
        die();
    }

    /**
    *
    * Method to add a block for use by the ajax methods when
    * 'Turn editing on' is enabled.
    *
    * @return string The results of the add action
    * @access protected
    *
    */
    protected function __addblock()
    {
        echo $this->objAjax->addblock();
        die();
    }

    /**
     * Method to remove a context block for use by the ajax methods when
    * 'Turn editing on' is enabled.
    *
    * @return string The results of the remove action
    * @access protected
    *
    */
    protected function __removeblock()
    {
        echo $this->objAjax->removeblock();
        die();
    }

    /**
     * Method to move a context block for use by the ajax methods when
    * 'Turn editing on' is enabled.
    *
    * @return string The results of the move action
    * @access protected
    *
    */
    protected function __moveblock()
    {
        echo $this->objAjax->moveblock();
        die();
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
        // Load an instance of the language object.
        $objLanguage = $this->getObject('language', 'language');
        $this->setVar('str', "<h3>"
          . $objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $action . "</h3>");
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
        $action=$this->getParam('action','NULL');
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