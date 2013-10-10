<?php
/**
 *
 * Openzoom module controller
 *
 * The openzoom contoller provides various interfaces to the Chisimba
 * OpenZoom module, including (mainly) some testing capability during 
 * development.
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
 * @author    Derek Keats dkeats@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11066 2008-10-25 16:50:00Z charlvn $
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
* Controller class for Chisimba for the module YourModuleName
*
* @author Derek Keats
* @package twitter
*
*/
class openzoom extends controller
{

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
    * @var string $objUser String object property for holding the
    * user object
    * @access public
    *
    */
    public $objUser;

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
    * Intialiser for the twitter controller
    * @access public
    *
    */
    public function init()
    {
	// Instantiate the user object
        $this->objUser = $this->getObject('user', 'security');
        // Instantiate the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }


    /**
     *
     * The standard dispatch method for the twitter module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     */
    public function dispatch()
    {
        // Get action from query string and set default to view.
        $action=$this->getParam('action', 'demo');
        // Convert the action into a method.
        $method = $this->__getMethod($action);
        // Return the template determined by the method resulting from action
        return $this->$method();
    }


    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    *
    * Method corresponding to the demo action. It fetches the default user
    * twitter status and displays it.
    *
    * @access private
    *
    */
    private function __demo()
    {
        $str="<h1>WORKING HERE</h1>";
        $width = "500";
        $height = "375";
        $imagePath = "http://localhost/ch/usrfiles/users/7924090825/openzoom/testing/me.jpg";
        $xmlFile = "../../../../usrfiles/users/7924090825/openzoom/testing/me/image.xml";
        $objOpenZoom = $this->getObject('openzoomops', 'openzoom');
        $str .= "<br />" . $objOpenZoom->getImage($width, $height, $imagePath, $xmlFile);
        $objOpenZoom->loadJsLib();
        $this->setVarByRef('str', $str);
        return "dump_tpl.php";
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
    private function __validAction(& $action)
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
            case 'demo':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
}
?>
