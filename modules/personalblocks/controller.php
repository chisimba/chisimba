<?php
/**
 *
 * Personal blocks
 *
 * Allows the creation of personal blocks for display on sidebar block areas.
 * Requires the blockalicious module to function. Personal blocks allow the
 * addition of web widgets in locations such as a blog.
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
 * @version   $Id: controller.php 9125 2008-05-14 19:10:11Z dkeats $
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
* Controller class for Chisimba for the module personal blocks
*
* Allows the creation of personal blocks for display on sidebar block areas.
* Requires the blockalicious module to function. Personal blocks allow the
* addition of web widgets in locations such as a blog.
*
* @author Derek Keats
* @package personalblocks
*
*/
class personalblocks extends controller
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
    * Intialiser for the twitter controller
    * @access public
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        //Get the activity logger class
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
        //Get action from query string and set default to view
        $this->action=$this->getParam('action', 'viewall');
        // retrieve the mode (edit/add/translate) from the querystring
        $mode = $this->getParam("mode", null);
        // retrieve the sort order from the querystring
        $order = $this->getParam("order", null);
        /*
        * Convert the action into a method (alternative to
        * using case selections)
        */
        $method = $this->__getMethod($this->action);
        /*
        * Return the template determined by the method resulting
        * from action
        */
        return $this->$method();
    }


    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    *
    * View all blocks
    *
    * @return string A template
    * @access private
    *
    */
    private function __viewall()
    {
        return "viewall_tpl.php";
    }
    
    public function __testing()
    {
        return "testing_tpl.php";
    }

    /**
    *
    * Delete a block
    *
    * @return string The viewall method and template
    * @access private
    *
    */
    private function __delete()
    {
        $id = $this->getParam('id', null);
        $objDb = $this->getObject("dbpersonalblocks", "personalblocks");
        // Make sure there is not a hole that allows someone elses to be deleted
        if ($objDb->validateDelete($id)) {
            $objDb = $this->getObject("dbpersonalblocks", "personalblocks");
            // Delete the record from the database
            $objDb->delete("id", $id);
            return $this->nextAction('viewall');
        } else {
            $str = "<span class=\"error\">"
              . $this->objLanguage->languageText('mod_personalblocks_spoofdetected','personalblocks')
              . "</span>";
        	$this->setVarByRef("str", $str);
            return "dump_tpl.php";
        }

    }

    /**
    *
    * Add a block
    *
    * @return string A template
    * @access private
    *
    */
    private function __addpblock()
    {
    	return "editadd_tpl.php";
    }

    /**
    *
    * Edit a block
    *
    * @return string A template
    * @access private
    *
    */
    private function __editblock()
    {
        return "editadd_tpl.php";
    }

    /**
    *
    * Save a bloc from edit or add
    *
    * @return string A template
    * @access private
    *
    */
    function __save()
    {
        $objDb = $this->getObject("dbpersonalblocks", "personalblocks");
        $objDb->saveRecord($this->objUser->userId());
        return $this->nextAction('viewall',null);
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
        $this->setVar('str', "<span class='error'>"
          . $this->objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $this->action . "</span>");
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
        return TRUE;
    }

}
?>