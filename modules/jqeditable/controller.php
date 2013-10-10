<?php
/**
 *
 * Interface to the jQuery Jeditable plugin
 *
 * Interface to the jQuery Jeditable plugin for building an editable area
 * or table cell. It allows a user to click and edit the content of different
 * xhtml elements. User clicks text on web page. Block of text becomes a form.
 * User edits contents and presses submit button. New text is sent to webserver
 * and saved. Form becomes normal text again. It is based on Jeditable by
 * Mika Tuupola available at:
 *   http://www.appelsiini.net/projects/jeditable
 *
 * Code above does several things: Elements with class edit or edit_area
 * become editable.
 *
 * Editing starts with single mouse click. Form input element is text. Width
 * and height of input element matches the original element. If users clicks
 * outside form changes are discarded. Same thing happens if users hits ESC.
 * When user hits ENTER browser submits text to the appropriate PHP file.
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
 * @author    Interface to the jQuery ingrid plugin derek.keats@wits.ac.za
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
 * Interface to the jQuery Jeditable plugin
 *
 * Interface to the jQuery Jeditable plugin for building an editable area
 * or table cell. It allows a user to click and edit the content of different
 * xhtml elements. User clicks text on web page. Block of text becomes a form.
 * User edits contents and presses submit button. New text is sent to webserver
 * and saved. Form becomes normal text again. It is based on Jeditable by
 * Mika Tuupola available at:
 *   http://www.appelsiini.net/projects/jeditable
 *
 * Code above does several things: Elements with class edit or edit_area
 * become editable.
 *
 * Editing starts with single mouse click. Form input element is text. Width
 * and height of input element matches the original element. If users clicks
 * outside form changes are discarded. Same thing happens if users hits ESC.
 * When user hits ENTER browser submits text to the appropriate PHP file.
*
* @author Derek Keats
* @package jqingrid
*
*/
class jqeditable extends controller
{
    /**
    *
    * Intialiser for the jqingrid controller
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
        $this->objTh = & $this->getObject('jqeditablehelper', 'jqeditable');
    }


    /**
     *
     * The standard dispatch method for the jqingrid module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'view');
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
    * Method corresponding to the view action. It fetches the stories
    * into an array and passes it to a main_tpl content template.
    * @access private
    *
    */
    private function __view()
    {
        $str="<h1>WORKING HERE</h1>";
        $this->objTh->loadJs();
        $arrayParams =  array('indicator' => 'Saving...',
            'tooltip' => 'Click to edit...',
            'type' => 'textarea',
            'cancel' => 'Cancel',
            'submit' => 'OK');
        $targetUrl = $this->uri(array('action' => 'save'), 'jqeditable');
        $targetUrl = str_replace('&amp;', '&', $targetUrl);
        $this->objTh->buildReadyFunction($arrayParams, $targetUrl);
        $this->objTh->loadReadyFunction();
        $this->setVarByRef('str', $str);
        return "dump_tpl.php";
    }

    /**
    *
    * Method corresponding to the save action. It gets the mode from
    * the querystring to and saves the data then sets nextAction to be
    * null, which returns the {yourmodulename} module in view mode.
    *
    * @access private
    *
    */
    private function __save()
    {
        $this->setPageTemplate('plain_tpl.php');
        $str = $this->getParam('value', 'Failed to retrieve value for dolor');
        $this->setVarByRef('str', $str);
        return "postsave_tpl.php";
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