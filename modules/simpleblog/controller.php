<?php
/**
 *
 * Simple blog
 *
 * A simplified version of the blog module that makes use of the blog module but creates a simplified, dynamic canvas interface allowing users to turn on side blocks using page level blocks.
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
 * @package   simpleblog
 * @author    Administrative User admin@localhost.local
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
 * Simple blog: controller
 *
 * A simplified version of the blog module that makes use of the blog module but creates a simplified, dynamic canvas interface allowing users to turn on side blocks using page level blocks.
*
* @author Administrative User
* @package simpleblog
*
*/
class simpleblog extends controller
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
        // Load the module block ajax helper.
        $this->objAjax = $this->getObject('moduleblockajax', 'canvas');
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
        $action=htmlentities($this->getParam('action', 'view'));
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
    * by the owner of the module. This uses module blocks
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
     */
    private function __getfeed()
    {
        $objPostOps = $this->getObject('simpleblogops', 'simpleblog');
        $blogId = $this->getParam('blogid', 'site');
        die($objPostOps->renderFeed($blogId));
        
    }
    /**
     * 
     * Render output of a particular post for display using an Ajax call.
     * 
     * @return NULL
     * 
     */
    private function __ajaxById()
    {
        $objPostOps = $this->getObject('simpleblogops', 'simpleblog');
        $id = $this->getParam('id', NULL);
        die($objPostOps->showById($id));
    }
    
    /**
     * 
     * Render output by tag for display using an Ajax call.
     * 
     * @return NULL
     * 
     */
    private function __ajaxByTag()
    {
        $objGuesser = $this->getObject('guesser', 'simpleblog');
        $blogId = $objGuesser->guessBlogId();
        $objPostOps = $this->getObject('simpleblogops', 'simpleblog');
        $tag = $this->getParam('tag', NULL);
        die ($objPostOps->showTag($blogId, $tag));
    }

    /**
     *
     * Edit a blog post
     * 
     * @return string The edit template populated
     * @acecess public
     *  
     */
    public function __edit()
    {
        $objEditor = $this->getObject('editor', 'simpleblog');
        $str = $objEditor->editForm();
        $this->setVarByRef('str', $str);
        return 'edit_tpl.php';
    }

    /**
     *
     * Save a blog post
     * 
     * @return string Next action
     * @access public
     * 
     */
    public function __savepost()
    {
        $objDb = $this->getObject('dbsimpleblog', 'simpleblog');
        $objDb->savePost();
        $blogType = $this->getParam('post_type', NULL);
        if ($blogType == "context") {
            return $this->nextAction('home', array(), 'context');
        } else {
            return $this->nextAction(NULL);
        }
    }

    /**
     *
     * Delete a blog post by its ID from the link
     * 
     * @access public
     * @return VOID
     *  
     */
    public function __delpost()
    {
        $postId=$this->getParam('postid', FALSE);
        if ($postId) {
            // Eliminate non bloggers.
            $this->objSec = $this->getObject('simpleblogsecurity', 'simpleblog');
            if (!$this->objSec->checkBloggingRights()) {
                // If they don't have any blogging rights, then no edit/del.
                die('norighttodelete');
            }
            // Get the blog posts db.
            $objDbPosts = $this->getObject('dbsimpleblog', 'simpleblog');
            
            $ar = $objDbPosts->getForEdit($postId);
            $blogId = $ar['blogid'];
            $bloggerId = $ar['userid'];
            $blogType = $ar['post_type'];
            $objSec = $this->getObject('simpleblogsecurity', 'simpleblog');
            $userId = $this->objUser->userId();
            if ($objSec->checkRights($bloggerId, $userId, $blogType)) {
                
                // PUT SECURITY HERE
                if ($objDbPosts->deletePost($postId)) {
                    echo "{postid}_deleted";
                    die();
                } else {
                    die('unknownerror');
                }
            } else {
                echo 'norighttodelete';
            }
        } else {
            echo 'nopostid';
        }
        die('unknownerror');
    }
    
    /**
     * 
     * Edit the blog description
     * 
     * @return string The edit template
     * 
     */
    public function __editdescription()
    {
        $this->getObject('editdescription', 'simpleblog');
        $str = $objDesc->getForm(TRUE);
        $this->setVarByRef('str', $str);
        return 'edit_tpl.php';
    }

    /**
     *
     * Sasve the blog description
     * 
     * @return string Nextaction 
     * 
     */
    public function __savedescription()
    {
        $objDb = $this->getObject('dbdescriptions', 'simpleblog');
        $objDb->saveBlog();
        return $this->nextAction('edit', 
          array('mode' => 'add', 
                'blogid'=>$this->objUser->userId()
          )
        );
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
        $action=htmlentities($this->getParam('action', NULL));
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
    function __validAction($action)
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
    function __getMethod($action)
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
        $action=$this->getParam('action', NULL);
        switch ($action)
        {
            case 'view':
            case 'ajaxById':
            case 'ajaxByTag':
            case 'getfeed':
            case NULL:
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
}
?>