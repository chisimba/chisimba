<?php
/**
 * 
 * A simple wall module
 * 
 * A simple wall module that makes use of OEMBED and that tries to look a bit
 * like Facebook's wall. This is the operations class. The module creates wall
 * posts (status messages) and comments (or replies) linked to each post or
 * status message
 *   WALL POST MESSAGE
 *       Reply to it
 *       Reply to it
 *   ANOTHER WALL POST MESSAGE
 *       Reply to it
 *
 *   ...etc...
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
* Controller class for Chisimba for the module wall
*
* @author Derek Keats
* @package wall
*
*/
class wall extends controller
{
    
    /**
    * 
    * @var string $objDbWall String object property for holding the
    * database connection object
    * @access public
    * 
    */
    public $objDbWall;

    /**
    *
    * @var string $objLanguage String object property for holding the language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    * 
    * Intialiser for the wall controller
    * @access public
    * @return VOID
    * 
    */
    public function init()
    {
        // Create an instance of the database class.
        $this->objDbwall = & $this->getObject('dbwall', 'wall');
        $this->objLanguage = & $this->getObject('language', 'language');
    }
    
    
    /**
     * 
     * The standard dispatch method for the wall module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     *
     * @access public
     * @return A call to the appropriate method
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
    
    
    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * 
    * Method corresponding to the view action for when you are in the
    * wall module itself
    *
    * @access private
    * @return string Template
    * 
    */
    private function __view()
    {
        // Set the layout template to compatible one
        $this->setLayoutTemplate('layout_template.php');
        // Figure out what type of wall we want
        $objGuessWall = $this->getObject('wallguesser','wall');
        $wallType = $objGuessWall->guessWall();
        switch ($wallType) {
            case "3":
                return "context_tpl.php";
                break;
            case "2":
                return "personal_tpl.php";
                break;
            case "1":
                return "site_tpl.php";
                break;
            default:
                return "main_tpl.php";
                break;
        }
        
    }

    private function __getsimpleblogwall()
    {
        $str = NULL;
        $objGuessWall = $this->getObject('wallguesser','wall');
        $wallType = $objGuessWall->guessWall();
        $objWallOps = $this->getObject('wallops', 'wall');
        $keyValue = $this->getParam('identifier', FALSE);
        if ($keyValue) {
            $str = $objWallOps->showObjectWall('identifier', $keyValue);
        }
        die($str);
        //$this->setVarByRef('str', $str);
        //return "dump_tpl.php";
    }
 
    /**
    * 
    * Method corresponding to the save action. It used die since it is
    * only ever called by an ajax request.
    * 
    * @access private
    * @return VOID
    * 
    */
    private function __save()
    {
        echo $this->objDbwall->savePost();
        die();
    }

    /**
     * Get a comment sent via ajax and return some text to be presented
     * to the user in an alert on failure
     *
     * @access public
     * @return VOID
     * 
     */
    public function __addcomment()
    {
        $objDbComment = $this->getObject('dbcomment', 'wall');
        $msg = $objDbComment->saveComment();
        switch($msg) {
            case 'true':
                echo 'true';
                break;
            case 'empty':
                echo "Something empty";
                break;
            case 'spoofattemptfailure':
                echo "Sooofffyyyyyy";
                break;
            default:
                echo $msg;
                break;
        }
        die();
    }

    /**
     *
     * Ajax response method to get the remaining comments and send them
     * back in response to an ajax request.
     *
     * @access public
     * @return VOID
     *
     */
    public function __morecomments()
    {
        $id = $this->getParam('id', FALSE);
        if ($id) {
            $objDbComment = $this->getObject('dbcomment', 'wall');
            $commentAr = $objDbComment->getComments($id, 100, 3);
            $ct = count($commentAr);
            $currentModule = $this->getParam('module', 'wall');
            $objUi = $this->getObject('wallops', 'wall');
            $comments = $objUi->loadComments($commentAr, $currentModule);
            echo $comments;
            die();
        } else {
            echo "ID NOT FOUND";
            die();
        }


    }
    
    /**
    * 
    * Method corresponding to the delete action. It requires a 
    * confirmation, and then delets the item, and then sets 
    * nextAction to be null, which returns the {yourmodulename} module 
    * in view mode. @TODO...implement this
    * 
    * @access private
    * 
    */
    private function __delete()
    {
        // retrieve the confirmation code from the querystring
        $id=$this->getParam("id", FALSE);
        if ($id) {
            echo $this->objDbwall->deletePost($id);
        }
        die();
    }

    /**
     *
     * Delete a specified comment and return a message for Ajax consumption
     * @access private
     * @return VOID
     *
     */
    private function __deletecomment()
    {
        $id=$this->getParam("id", FALSE);
        if ($id) {
            $objDbComment = $this->getObject('dbcomment', 'wall');
            echo $objDbComment->deleteComment($id);
            die();
        } else {
            die('deletefailed');
        }
        
    }

    /**
     *
     * Return a count of the total posts for consumption by Ajax
     * @access private
     * @return VOID
     *
     */
    private function __countPosts()
    {
        echo $this->objDbwall->countPosts(2, FALSE);
        die();
    }

    /**
     *
     * Get the next batch of posts for consumption by Ajax
     * @access private
     * @return VOID
     *
     */
    private function __getmoreposts()
    {
        $objWallOps = $this->getObject('wallops', 'wall');
        $wallid = $this->getParam('wallid', NULL);
        echo $objWallOps->nextPosts($wallid);
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
        $action=$this->getParam('action', NULL);
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
        $action=$this->getParam('action','view');
        switch ($action)
        {
            case 'view':
            case 'getmoreposts':
            case 'countposts':
            case 'morecomments':
            case 'getsimpleblogwall':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
}
?>