<?php
/**
 * 
 * bookmarks
 * 
 * Manage bookmarking within the system
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
 * @package   bookmarks
 * @author    Kevin Cyster kcyster@gmail.com
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
* Controller class for Chisimba for the module bookmarks
*
* @author Kevin Cyster
* @package bookmarks
*
*/
class bookmarks extends controller
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
    * Intialiser for the bookmarks controller
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
        $this->objDBbookmarks = $this->getObject('dbbookmarks', 'bookmarks');
        $this->objDBfolders = $this->getObject('dbbookmarkfolders', 'bookmarks');
        $this->objOps = $this->getObject('bookmarksops', 'bookmarks');
        $this->objContext = $this->getObject('dbcontext', 'context');
        
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('bookmarks.js',
          'bookmarks'));
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the bookmarks module.
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
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
        // Set the layout template to compatible one
        $this->setLayoutTemplate('layout_tpl.php');
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return $this->$method();
    }
    
    
    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * 
    * Method corresponding to the view action. It shows the default
    * dynamic canvas template, showing you how to create block based
    * view templates
    * @access private
    * 
    */
    private function __view()
    {
        // All the action is in the blocks
        return "main_tpl.php";
    }
    
    /**
    * 
    * Method corresponding to the form action.
    * @access private
    * 
    */
    private function __form()
    {
        return 'folder_tpl.php';
    }   
    
    /**
    * 
    * Method corresponding to the form action.
    * @access private
    * 
    */
    private function __validate()
    {
        $cancel = $this->getParam('cancel', NULL);
        if (!empty($cancel))
        {
            $this->setSession('errors', array());
            return $this->nextAction('view');
        }
        
        $type = $this->getParam('type');
        $id = $this->getParam('id', NULL);
        $data = array();
        $data['parent_id'] = $this->getParam('parent_id');
        $data['folder_name'] = $this->getParam('folder_name');
        $data['user_id'] = $this->objUser->PKId();
        $errorsFound = $this->objOps->validate($type, $data);
        if ($errorsFound)
        {
            return $this->nextAction('form', array('type' => 'folder', 'id' => $id));
        }
        else
        {
            if (empty($id))
            {
                $data['created_by'] = $this->objUser->PKId();
                $data['date_created'] = date('Y-m-d H:i:s');
                $this->objDBfolders->saveFolder($data);
            }
            else
            {
                $data['id'] = $id;
                $data['modified_by'] = $this->objUser->PKId();
                $data['date_modified'] = date('Y-m-d H:i:s');
                $this->objDBfolders->updateFolder($id, $data);
            }
            $this->setSession('errors', array());
            return$this->nextAction('view');                
        }
    }   
    
    /**
    * 
    * Method corresponding to the delete action.
    * @access private
    * 
    */
    private function __delete()
    {
        $type = $this->getParam('type');
        if ($type == 'folder')
        {
            $id = $this->getParam('id');
            $this->objDBfolders->deleteFolder($id);
            $this->objDBbookmarks->deleteFolderBookmarks($id);
            $subFolderArray = $this->objDBfolders->getSubFolders($id);
            if (!empty($subFolderArray))
            {
                foreach ($subFolderArray as $subFolderId)
                {
                    $this->objDBfolders->deleteFolder($subFolderId);
                    $this->objDBbookmarks->deleteFolderBookmarks($subFolderId);
                }
            }            
            $array = array();
        }
        else
        {
            $id = $this->getParam('id');
            $folderId = $this->getParam('folder_id');
            
            $this->objDBbookmarks->deleteBookmark($id);
            $array = array('folder_id' => $folderId);
        }
        return $this->nextAction('view', $array);
    }   
    
    /**
     *
     * Method to display subfolders
     * 
     * @access private
     * @return VIOD 
     */
    private function __ajaxGetBookmarks()
    {
        return $this->objOps->ajaxGetBookmarks();
    }
    
    /**
     *
     * Method to save the modal added bookmarks
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxSaveBookmark()
    {
        $contextCode = $this->objContext->getContextCode();
        
        $data = array();
        $data['user_id'] = $this->objUser->PKId();
        $data['folder_id'] = $this->getParam('folder_id');
        $data['contextcode'] = $contextCode;
        $data['bookmark_name'] = $this->getParam('bookmark_name');
        $data['location'] = str_replace('|', '&', str_replace('/', '=', $this->getParam('location')));
        $data['created_by'] = $this->objUser->PKId();
        $data['date_created'] = date('Y-m-d H:i:s');
        $id = $this->objDBbookmarks->saveBookmark($data);

        echo $id;
        die();
    }
    
    /**
     *
     * Method to display subfolders
     * 
     * @access private
     * @return VIOD 
     */
    private function __ajaxGetBlockBookmarks()
    {
        return $this->objOps->ajaxGetBlockBookmarks();
    }
    
    /**
     *
     * Method to set the context
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxSetContext()
    {
        $contextcode = $this->getParam('contextcode');
        
        $context = $this->objContext->getContextCode();

        if ($contextcode != $context)
        {
            $this->objContext->leaveContext();
            $this->objContext->joinContext($contextcode);
        }
        echo 'true';
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
    private function __getMethod(& $action)
    {
//        die($action);
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
