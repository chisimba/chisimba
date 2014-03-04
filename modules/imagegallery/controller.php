<?php
/**
 * 
 * imagegallery
 * 
 * This a place where you can upload your images and share them with your friends
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
 * @package   imagegallery
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
* Controller class for Chisimba for the module imagegallery
*
* @author Kevin Cyster
* @package imagegallery
*
*/
class imagegallery extends controller
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
    * Intialiser for the imagegallery controller
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
        $this->objOps = $this->getObject('imagegalleryops', 'imagegallery');
        $this->objDBgalleries = $this->getObject('dbgalleries', 'imagegallery');
        $this->objDBalbums = $this->getObject('dbalbums', 'imagegallery');
        $this->objDBimages = $this->getObject('dbimages', 'imagegallery');
        $this->objDBcomments = $this->getObject('dbcomments', 'imagegallery');
        
        //Get the activity logger class
        $this->objLog = $this->newObject('logactivity', 'logger');
        $this->objLog->log();

        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('imagegallery.js', 'imagegallery'));
    }
    
    
    /**
     * 
     * The standard dispatch method for the imagegallery module.
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
     * Method corresponding to the saveaddgallery action
     *  
     * @access private
     * @return VOID
     */
    private function __saveaddgallery()
    {
        $userId =$this->getParam('gallery_add_user_id');
        $contextCode = $this->getParam('gallery_add_context_code');
        $title = $this->getParam('gallery_add_title');
        $desc = $this->getParam('gallery_add_description');
        $share = $this->getParam('gallery_add_shared');
        $tabs = $this->getParam('gallery_add_tabs');
        
        $fields = array();
        if (!empty($userId))
        {
            $fields['user_id'] = $userId;
        }
        else
        {
            $fields['context_code'] = $contextCode;
        }
        $fields['title'] = $title;
        $fields['description'] = $desc;
        $fields['is_shared'] = ($share == 'on') ? '1' : '0';
        
        $this->objDBgalleries->addGallery($fields);
        
        return $this->nextAction("view", array('tabs' => $tabs), "imagegallery");
    }
    
    /**
     *
     * Method corresponding to the ajaxGetGalleryData action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxGetGalleryData()
    {
        $galleryId = $this->getParam('gallery_id');
        $tabs = $this->getParam('tabs');
        
        $gallery = $this->objDBgalleries->getGallery($galleryId);
        
        $data = array();
        $data['gallery_id'] = $gallery['id'];
        $data['title'] = $gallery['title'];
        $data['desc'] = $gallery['description'];
        $data['shared'] = ($gallery['is_shared'] == 1) ? 'on' : '';
        $data['tabs'] = $tabs;

        echo json_encode($data);
        die();        
    }
    
    /**
     *
     * Method corresponding to the ajaxGetGalleryViewData action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxGetGalleryViewData()
    {
        $yesLabel = $this->objLanguage->languageText('word_yes', 'system', 'ERROR: word_yes');
        $noLabel = $this->objLanguage->languageText('word_no', 'system', 'ERROR: word_no');
        
        $galleryId = $this->getParam('gallery_id');
        
        $gallery = $this->objDBgalleries->getGallery($galleryId);
        
        $data = array();
        $data['title'] = $gallery['title'];
        $data['desc'] = $gallery['description'];
        $data['shared'] = ($gallery['is_shared'] == 1) ? $yesLabel : $noLabel;

        echo json_encode($data);
        die();        
    }
    
    /**
     *
     * Method corresponding to the saveeditgallery action
     *  
     * @access private
     * @return VOID
     */
    private function __saveeditgallery()
    {
        $galleryId = $this->getParam('gallery_edit_gallery_id');
        $title = $this->getParam('gallery_edit_title');
        $desc = $this->getParam('gallery_edit_description');
        $share = $this->getParam('gallery_edit_shared');
        $tabs = $this->getParam('gallery_edit_tabs');

        $fields = array();
        $fields['title'] = $title;
        $fields['description'] = $desc;
        $fields['is_shared'] = ($share == 'on') ? '1' : '0';
        
        $this->objDBgalleries->updateGallery($galleryId, $fields);
        
        return $this->nextAction("view", array('tabs' => $tabs), "imagegallery");
    }
    
    /**
     *
     * Method corresponding to the deletegallery action
     *  
     * @access private
     * @return VOID
     */
    private function __deletegallery()
    {
        $galleryId = $this->getParam('gallery_id');
        $tabs = $this->getParam('tabs');

        $this->objDBgalleries->deleteGallery($galleryId);
        
        return $this->nextAction("view", array('tabs' => $tabs), "imagegallery");
    }
    
    /**
     *
     * Method corresponding to the saveaddalbum action
     *  
     * @access private
     * @return VOID
     */
    private function __saveaddalbum()
    {
        $galleryId =$this->getParam('album_add_gallery_id');
        $title = $this->getParam('album_add_title');
        $desc = $this->getParam('album_add_description');
        $shared = $this->getParam('album_add_shared');
        $tabs = $this->getParam('album_add_tabs');
        
        $gallery = $this->objDBgalleries->getGallery($galleryId);
        
        $fields = array();
        $fields['gallery_id'] = $galleryId;
        $fields['user_id'] = $gallery['user_id'];
        $fields['context_code'] = $gallery['context_code'];
        $fields['title'] = $title;
        $fields['description'] = $desc;
        $fields['is_shared'] = ($shared == 'on') ? '1' : '0';
        
        $this->objDBalbums->addAlbum($fields);
        
        return $this->nextAction("view", array('gallery_id' => $galleryId, 'tabs' => $tabs), "imagegallery");
    }
    
    /**
     *
     * Method corresponding to the ajaxGetAlbumViewData action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxGetAlbumViewData()
    {
        $yesLabel = $this->objLanguage->languageText('word_yes', 'system', 'ERROR: word_yes');
        $noLabel = $this->objLanguage->languageText('word_no', 'system', 'ERROR: word_no');
        
        $albumId = $this->getParam('album_id');
        
        $album = $this->objDBalbums->getALbum($albumId);
        
        $data = array();
        $data['title'] = $album['title'];
        $data['desc'] = $album['description'];
        $data['shared'] = ($album['is_shared'] == 1) ? $yesLabel : $noLabel;

        echo json_encode($data);
        die();        
    }
    
    /**
     *
     * Method corresponding to the ajaxGetImageViewData action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxGetImageViewData()
    {
        $yesLabel = $this->objLanguage->languageText('word_yes', 'system', 'ERROR: word_yes');
        $noLabel = $this->objLanguage->languageText('word_no', 'system', 'ERROR: word_no');
        $noneLabel = $this->objLanguage->languageText('mod_imagegallery_nonegiven', 'imagegallery', 'ERROR: mod_imagegallery_nonegiven');
        
        $imageId = $this->getParam('image_id');
        
        $image = $this->objDBimages->getImage($imageId);
        
        $data = array();
        $data['caption'] = (empty($image['caption'])) ? '<em class="warning">' . $noneLabel . '</em>' : $image['caption'];
        $data['desc'] = (empty($image['description'])) ? '<em class="warning">' . $noneLabel . '</em>' : $image['description'];
        $data['shared'] = ($image['is_shared'] == 1) ? $yesLabel : $noLabel;

        echo json_encode($data);
        die();        
    }
    
    /**
     *
     * Method corresponding to the deletealbum action
     *  
     * @access private
     * @return VOID
     */
    private function __deletealbum()
    {
        $albumId = $this->getParam('album_id');
        $galleryId = $this->getParam('gallery_id');
        $tabs = $this->getParam('tabs');

        $this->objDBalbums->deleteAlbum($albumId);
        
        return $this->nextAction("view", array('tabs' => $tabs, 'gallery_id' => $galleryId), "imagegallery");
    }
    
    /**
     *
     * Method corresponding to the ajaxGetAlbumData action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxGetAlbumData()
    {
        $albumId = $this->getParam('album_id');
        $tabs = $this->getParam('tabs');
        
        $album = $this->objDBalbums->getAlbum($albumId);
        
        $data = array();
        $data['gallery_id'] = $album['gallery_id'];
        $data['album_id'] = $album['id'];
        $data['title'] = $album['title'];
        $data['desc'] = $album['description'];
        $data['tabs'] = $tabs;
        $data['shared'] = ($album['is_shared'] == 1) ? 'on' : '';

        echo json_encode($data);
        die();        
    }
    
    /**
     *
     * Method corresponding to the saveeditalbum action
     *  
     * @access private
     * @return VOID
     */
    private function __saveeditalbum()
    {
        $galleryId = $this->getParam('album_edit_gallery_id');
        $albumId = $this->getParam('album_edit_album_id');
        $title = $this->getParam('album_edit_title');
        $desc = $this->getParam('album_edit_description');
        $share = $this->getParam('album_edit_shared');
        $tabs = $this->getParam('album_edit_tabs');

        $fields = array();
        $fields['title'] = $title;
        $fields['description'] = $desc;
        $fields['is_shared'] = ($share == 'on') ? '1' : '0';
        
        $this->objDBalbums->updateAlbum($albumId, $fields);
        
        return $this->nextAction("view", array('tabs' => $tabs, 'gallery_id' => $galleryId), "imagegallery");
    }
    
    /**
     *
     * Method corresponding to the ajaxShowUpload action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxShowUpload()
    {
        $galleryId = $this->getParam('gallery_id');
        $albumId = $this->getParam('album_id');
        
        $string = $this->objOps->ajaxShowUpload($galleryId, $albumId);
        
        echo $string;
        die();
    }
    
    /**
     *
     * Method corresponding to the saveimages action
     * 
     * @access private
     * @return VOID 
     */
    private function __saveimages()
    {
        $tabs = $this->getParam('upload_tabs');
        $albumId = $this->objOps->doUpload();

        return $this->nextAction('view', array('album_id' => $albumId, 'tabs' => $tabs), 'imagegallery');
    }
    
    /**
     *
     * Method corresponding to the deleteimage action
     * 
     * @access private
     * @return VOID 
     */
    private function __deleteimage()
    {
        $albumId = $this->getParam('album_id');
        $imageId = $this->getParam('image_id');
        $tabs = $this->getParam('tabs');
        
        $this->objDBimages->deleteImage($imageId);
        
        return $this->nextAction('view', array('album_id' => $albumId, 'tabs' => $tabs), 'imagegallery');
    }
    
    /**
     *
     * Method corresponding to the ajaxGetImageData action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxGetImageData()
    {
        $imageId = $this->getParam('image_id');
        $tabs = $this->getParam('tabs');
        
        $image = $this->objDBimages->getImage($imageId);
        
        $data = array();
        $data['image_id'] = $image['id'];
        $data['album_id'] = $image['album_id'];
        $data['caption'] = $image['caption'];
        $data['desc'] = $image['description'];
        $data['shared'] = ($image['is_shared'] == 1) ? 'on' : '';
        $data['tabs'] = $tabs;

        echo json_encode($data);
        die();        
    }
    
    /**
     *
     * Method corresponding to the saveeditimage action
     *  
     * @access private
     * @return VOID
     */
    private function __saveeditimage()
    {
        $albumId = $this->getParam('image_edit_album_id');
        $imageId = $this->getParam('image_edit_image_id');
        $caption = $this->getParam('image_edit_caption');
        $desc = $this->getParam('image_edit_description');
        $share = $this->getParam('image_edit_shared');
        $tabs = $this->getParam('tabs');
        $location = $this->getParam('location');

        $fields = array();
        $fields['caption'] = $caption;
        $fields['description'] = $desc;
        $fields['is_shared'] = ($share == 'on') ? '1' : '0';
        
        $this->objDBimages->updateImage($imageId, $fields);
        
        if (empty($location))
        {
            return $this->nextAction("view", array('tabs' => $tabs, 'album_id' => $albumId), "imagegallery");
        }
        else
        {
            return $this->nextAction("view", array('tabs' => $tabs, 'image_id' => $imageId), "imagegallery");
        }
    }
    
    /**
     *
     * Method corresponding to the setalbumcover action
     * 
     * @access private
     * @return VOID 
     */
    private function __setalbumcover()
    {
        $fileId = $this->getParam('file_id');
        $albumId = $this->getParam('album_id');
        $galleryId = $this->getParam('gallery_id');
        $tabs = $this->getParam('tabs');
        
        $fields = array();
        $fields['cover_image_id'] = $fileId;
        
        $this->objDBalbums->updateAlbum($albumId, $fields);
        
        return $this->nextAction('view', array('gallery_id' => $galleryId, 'tabs' => $tabs), 'imagegallery');
    }

    /**
     *
     * Method corresponding to the setalbumcover action
     * 
     * @access private
     * @return VOID 
     */
    private function __resetalbumcover()
    {
        $albumId = $this->getParam('album_id');
        $galleryId = $this->getParam('gallery_id');
        $tabs = $this->getParam('tabs');
        
        $fields = array();
        $fields['cover_image_id'] = NULL;
        
        $this->objDBalbums->updateAlbum($albumId, $fields);
        
        return $this->nextAction('view', array('gallery_id' => $galleryId, 'tabs' => $tabs), 'imagegallery');
    }

    /**
     *
     * Method corresponding to the setgallerycover action
     * 
     * @access private
     * @return VOID 
     */
    private function __setgallerycover()
    {
        $fileId = $this->getParam('file_id');
        $galleryId = $this->getParam('gallery_id');
        $tabs = $this->getParam('tabs');
        
        $fields = array();
        $fields['cover_image_id'] = $fileId;
        
        $this->objDBgalleries->updateGallery($galleryId, $fields);
        
        return $this->nextAction('view', array('tabs' => $tabs), 'imagegallery');
    }
    
    /**
     *
     * Method corresponding to the resetgallerycover action
     * 
     * @access private
     * @return VOID 
     */
    private function __resetgallerycover()
    {
        $galleryId = $this->getParam('gallery_id');
        $tabs = $this->getParam('tabs');
        
        $fields = array();
        $fields['cover_image_id'] = NULL;
        
        $this->objDBgalleries->updateGallery($galleryId, $fields);
        
        return $this->nextAction('view', array('tabs' => $tabs), 'imagegallery');
    }
    
    /**
     *
     * Method to update the view count via ajax
     * 
     * @access private
     * @param string $imageId The id of the image to update the count for
     * @return VOID 
     */
    private function __ajaxUpdateViewCount()
    {
        $imageId = $this->getParam('image_id');
        $this->objDBimages->updateViewCount($imageId);
        
        echo ' true';
        die();
    }
    
    /**
     *
     * Method corresponding to the save add comment action
     * 
     * @access private 
     * @return VOID 
     */
    private function __saveaddcomment()
    {
        $imageId = $this->getParam('add_comment_image_id');
        $comment = $this->getParam('add_comment_comment');
       
        $fields = array();
        $fields['comment_text'] = $comment;
        $fields['image_id'] = $imageId;
        $fields['user_id'] = $this->objUser->userId();
        
        $this->objDBcomments->addComment($fields);
        
        return $this->nextAction('view', array('image_id' => $imageId, 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
    }
    
    /**
     *
     * Method corresponding to the save edit comment action
     * 
     * @access private 
     * @return VOID 
     */
    private function __saveeditcomment()
    {
        $imageId = $this->getParam('edit_comment_image_id');
        $commentId = $this->getParam('edit_comment_id');
        $comment = $this->getParam('edit_comment_comment');

        $fields = array();
        $fields['comment_text'] = $comment;
        
        $this->objDBcomments->updateComment($commentId, $fields);

        return $this->nextAction('view', array('image_id' => $imageId, 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
    }
    
    /**
     *
     * Method corresponding to the ajaxGetCommentData action
     * 
     * @access private
     * @retur VOID 
     */
    private function __ajaxGetCommentData()
    {
        $commentId = $this->getParam('comment_id');
        
        $comment = $this->objDBcomments->getComment($commentId);
        
        $data = array();
        $data['comment_id'] = $commentId;
        $data['image_id'] = $comment['image_id'];
        $data['comment'] = $comment['comment_text'];
        
        echo json_encode($data);
        die();
    }
    
    /**
     *
     * Method corresponding to the deletecomment action
     *  
     * @access private
     * @return VOID
     */
    private function __deletecomment()
    {
        $commentId = $this->getParam('comment_id');
        $imageId =$this->getParam('image_id');
        $tabs = $this->getParam('tabs');
        $shared = $this->getParam('shared', NULL);
        
        $this->objDBcomments->deleteComment($commentId);
        
        if (empty($shared))
        {
            return $this->nextAction('view', array('image_id' => $imageId), 'imagegallery');
        }
        else
        {
            return $this->nextAction('view', array('image_id' => $imageId, 'tabs' => $tabs, 'shared' => $shared), 'imagegallery');            
        }
        
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
            default:
                return TRUE;
                break;
        }
    }
}
?>
