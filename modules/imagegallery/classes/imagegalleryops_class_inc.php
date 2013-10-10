<?php
/**
 *
 * The operations class for the image Gallery
 *
 * The operations class for Image gallery. This a place where you can upload your images and share them with your friends.
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
 * @version    0.001
 * @package    gallery
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
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
 * 
 * The operations class for Image gallery.
 *
 * The operations class for Image gallery. This a place where you can upload your images and share them with your friends.
 *
 * @category  Chisimba
 * @package    gallery
 * @author     Kevin Cyster kcyster@gmail.com
 * @version   0.001
 * @copyright 2010 AVOIR
 *
 */
class imagegalleryops extends object
{
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        try 
        {
            // Load core system objects.
            $this->objContext = $this->getObject('dbcontext', 'context');
            $this->objUserContext = $this->getObject('usercontext', 'context');
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->userId();
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objConfirm = $this->newObject('jqueryconfirm', 'utilities');
            $this->objSvars = $this->getObject('serializevars', 'utilities');
            $this->objModules = $this->getObject('modules', 'modulecatalogue');
            $this->objMime = $this->getObject('mimetypes', 'files');
            $this->objDir = $this->getObject('mkdir', 'files');
            $this->objArchive = $this->getObject('archivefactory', 'archivemanager');
            $this->objFileMan = $this->getObject('dbfile', 'filemanager');
            $this->objUpload = $this->newObject('upload', 'filemanager');
            $this->objAnalyzeMediaFile = $this->getObject('analyzemediafile', 'filemanager');
            $this->objMediaFileInfo = $this->getObject('dbmediafileinfo', 'filemanager');
            $this->objThumbnails = $this->getObject('thumbnails', 'filemanager');

            $this->leftLabel = $this->objLanguage->languageText('mod_htmlelements_charactersleft', 'htmlelements', 'ERROR: mod_htmlelements_charactersleft');
            
            // Load html elements.
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->objTable = $this->loadClass('htmltable', 'htmlelements');
            $this->objInput = $this->loadClass('textinput', 'htmlelements');
            $this->objText = $this->loadClass('textarea', 'htmlelements');
            $this->objForm = $this->loadClass('form', 'htmlelements');
            $this->objCheck = $this->loadClass('checkbox', 'htmlelements');
            $this->objLink = $this->loadClass('link', 'htmlelements');
            $this->objDrop = $this->loadClass('dropdown', 'htmlelements');
            
            // Load db classes,
            $this->objDBgalleries = $this->getObject('dbgalleries', 'imagegallery');
            $this->objDBalbums = $this->getObject('dbalbums', 'imagegallery');
            $this->objDBimages = $this->getObject('dbimages', 'imagegallery');
            $this->objDBcomments = $this->getObject('dbcomments', 'imagegallery');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
     *
     * Method to generate an error string for display
     * 
     * @access private
     * @param string $errorText The error string
     * @return string $string The formated error string
     */
    private function error($errorText)
    {
        $string = '<span class="error">' . $errorText . '</span>';
        return $string;
    }
    
    /**
     *
     * Method to generate a warning string for display
     * 
     * @access private
     * @param string $errorText The error string
     * @return string $string The formated error string
     */
    private function warning($errorText)
    {
        $string = '<span class="warning">' . $errorText . '</span>';
        return $string;
    }
    
    /**
     *
     * Method to show the main page of the image gallery
     * 
     * @access public
     * @return string $string The html display string 
     */
    public function showMain()
    {
        $userLabel = $this->objLanguage->languageText('mod_imagegallery_personalimages', 'imagegallery', 'ERROR: mod_imagegallery_personalimages');
        $sharedLabel = $this->objLanguage->languageText('mod_imagegallery_sharedimages', 'imagegallery', 'ERROR: mod_imagegallery_sharedimages');

        $tabs = $this->getParam('tabs');
        $tabsArray = array(0, 0);
        if (!empty($tabs))
        {
            $tabsArray = explode("|", $tabs);
        }

        $contexts = $this->objUserContext->getUserContext($this->userId);
        
        $galleryId = $this->getParam('gallery_id');
        $albumId = $this->getParam('album_id');
        $imageId = $this->getParam('image_id');
        $shared = $this->getParam('shared', NULL);
       
        if (empty($shared))
        {
            if (!empty($contexts))
            {
                $gallery = $this->objDBgalleries->getGallery($galleryId);
                $album = $this->objDBalbums->getAlbum($albumId);            
                $image = $this->objDBimages->getImage($imageId);            
                $userId = NULL;
                $userId = (!empty($gallery['user_id'])) ? $gallery['user_id'] : $userId;
                $userId = (!empty($album['user_id'])) ? $album['user_id'] : $userId;
                $userId = (!empty($image['user_id'])) ? $image['user_id'] : $userId;

                if (!empty($userId))
                {
                    if (empty($galleryId) && empty($albumId) && empty($imageId))
                    {
                        $content = $this->showUserGalleries();
                    }
                    elseif (!empty($galleryId))
                    {
                        $content = $this->showUserGalleryAlbums($galleryId);
                    }
                    elseif (!empty($albumId))
                    {
                        $content = $this->showUserAlbumImages($albumId);
                    }
                    else
                    {
                        $content = $this->showUserImage($imageId);
                    }

                    $contextTab = $this->showContextGalleries($contexts, $tabsArray[1]);
                }
                else
                {
                    $content = $this->showUserGalleries();

                    if (empty($galleryId) && empty($albumId) && empty($imageId))
                    {
                        $contextTab = $this->showContextGalleries($contexts, $tabsArray[1]);
                    }
                    elseif (!empty($galleryId))
                    {
                        $contextTab = $this->showContextGalleryAlbums($galleryId, $contexts, $tabsArray[1]);
                    }
                    elseif (!empty($albumId))
                    {
                        $contextTab = $this->showContextAlbumImages($albumId, $contexts, $tabsArray[1]);
                    }
                    else
                    {
                        $contextTab = $this->showContextImage($imageId, $contexts, $tabsArray[1]);
                    }
                }

                $userTab = array(
                    'title' => $userLabel,
                    'content' => $content,
                );
            }        
            else
            {
                if (empty($galleryId) && empty($albumId) && empty($imageId))
                {
                    $content = $this->showUserGalleries();
                }
                elseif (!empty($galleryId))
                {
                    $content = $this->showUserGalleryAlbums($galleryId);
                }
                elseif (!empty($albumId))
                {
                    $content = $this->showUserAlbumImages($albumId);
                }
                else
                {
                    $content = $this->showUserImage($imageId);
                }

                $userTab = array(
                    'title' => $userLabel,
                    'content' => $content,
                );
            }
            $sharedTab = array(
                'title' => $sharedLabel,
                'content' => $this->showSharedImages(),
            );
        }
        else
        {
            $userTab = array(
                'title' => $userLabel,
                'content' => $this->showUserGalleries(),
            );
            
            $sharedTab = array(
                'title' => $sharedLabel,
                'content' => $this->viewImage($imageId, 1, TRUE),
            );
            
            $contextTab = $this->showContextGalleries($contexts, $tabsArray[1]);
        }

        $objTabs = $this->newObject('tabs', 'jquerycore');
        $objTabs->setCssId('main_tabs');
        $objTabs->setSelected((int) $tabsArray[0]);
        $objTabs->addTab($userTab);
        $objTabs->addTab($sharedTab);
        if (!empty($contexts))
        {
            $objTabs->addTab($contextTab);
        }
        $string = $objTabs->show();            

        $string .= $this->showAddGalleryDialog();
        $string .= $this->showEditGalleryDialog();
        $string .= $this->showViewGalleryDetailsDialog();
        $string .= $this->showAddAlbumDialog();
        $string .= $this->showEditAlbumDialog();
        $string .= $this->showViewAlbumDetailsDialog();
        $string .= $this->showEditImageDialog();
        $string .= $this->showViewImageDetailsDialog();
                
        return $string;
    }
    
    /**
     *
     * Method to display the user galleries
     * 
     * @access private
     * @return string $content The galleries display string 
     */
    private function showUserGalleries()
    {
        $myGalleriesLabel = $this->objLanguage->languageText('mod_imagegallery_mygalleries', 'imagegallery', 'ERROR: mod_imagegallery_mygalleries');
        $defineGalleryLabel = $this->objLanguage->languageText('mod_imagegallery_definegallery', 'imagegallery', 'ERROR: mod_imagegallery_definegallery');

        $galleries = $this->objDBgalleries->getUserGalleries($this->userId);
        $content = '<span class="success">' . $defineGalleryLabel . '</span><br />';
        $content .= '<h1>' . $myGalleriesLabel . '</h1>';
        $content .= $this->showGalleryDisplay(TRUE, $galleries);
        
        return $content;
    }
    
    /**
     *
     * Method to display user gallery albums 
     * 
     * @access private
     * @param string $galleryId The id of the gallery to show albus for
     * @return string $content The albums display string 
     */
    private function showUserGalleryAlbums($galleryId)
    {
        $myGalleriesLabel = $this->objLanguage->languageText('mod_imagegallery_mygalleries', 'imagegallery', 'ERROR: mod_imagegallery_mygalleries');
        $myAlbumsLabel = $this->objLanguage->languageText('mod_imagegallery_myalbums', 'imagegallery', 'ERROR: mod_imagegallery_myalbums');
        $defineAlbumLabel = $this->objLanguage->languageText('mod_imagegallery_definealbum', 'imagegallery', 'ERROR: mod_imagegallery_definealbum');

        $gallery = $this->objDBgalleries->getGallery($galleryId);
        $albums = $this->objDBalbums->getGalleryAlbums($galleryId);

        $uri = $this->uri(array('action' => 'view'), 'imagegallery');
        $objLink = new link($uri);
        $objLink->link = $myGalleriesLabel;
        $galleriesLink = $objLink->show();

        $content = '<span class="success">' . $defineAlbumLabel . '</span><br />';
        $content .= '<h1>' . $galleriesLink . '&nbsp;|&nbsp;' . $gallery['title'] . '&nbsp;-&nbsp;' . $myAlbumsLabel . '</h1>';
        $content .= $this->showAlbumDisplay($albums, $galleryId);
        
        return $content;
    }
    
    /**
     *
     * Method to display user album images
     * 
     * @access private
     * @param string $galleryId The id of the gallery to show albus for
     * @return string $content The albums display string 
     */
    private function showUserAlbumImages($albumId)
    {
        $myGalleriesLabel = $this->objLanguage->languageText('mod_imagegallery_mygalleries', 'imagegallery', 'ERROR: mod_imagegallery_mygalleries');
        $myImagesLabel = $this->objLanguage->languageText('mod_imagegallery_myimages', 'imagegallery', 'ERROR: mod_imagegallery_myimages');

        $album = $this->objDBalbums->getAlbum($albumId);
        $gallery = $this->objDBgalleries->getGallery($album['gallery_id']);
        $images = $this->objDBimages->getAlbumImages($albumId);

        $uri = $this->uri(array('action' => 'view'), 'imagegallery');
        $objLink = new link($uri);
        $objLink->link = $myGalleriesLabel;
        $galleriesLink = $objLink->show();

        $uri = $this->uri(array('action' => 'view', 'gallery_id' => $gallery['id']), 'imagegallery');
        $objLink = new link($uri);
        $objLink->link = $gallery['title'];
        $galleryLink = $objLink->show();

        $content = '<h1>' . $galleriesLink . '&nbsp;|&nbsp;' . $galleryLink . '&nbsp;|&nbsp;' . $album['title'] . '&nbsp;-&nbsp;' . $myImagesLabel . '</h1>';
        $content .= $this->showImageDisplay($images, $albumId);
        
        return $content;
    }
    
    /**
     *
     * Method to display the context galleries
     * 
     * @access private
     * @param array $contexts The array of user contexts
     * @param integer $selected The tab selected
     * @return string $content The galleries display string 
     */
    private function showContextGalleries($contexts, $selected)
    {
        $defineGalleryLabel = $this->objLanguage->languageText('mod_imagegallery_definegallery', 'imagegallery', 'ERROR: mod_imagegallery_definegallery');
        $contextLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextimages', 'imagegallery', NULL, 'ERROR: mod_imagegallery_contextimages');
        $contextGalleriesLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextgalleries', 'imagegallery', NULL, 'ERROR: mod_imagegallery_contextgalleries');

        $contextGalleries = array();
        foreach ($contexts as $contextCode)
        {
            $galleries = $this->objDBgalleries->getContextGalleries($contextCode);
            $contextGalleries[$contextCode] = $galleries;
        }

        $content = '<span class="success">' . $defineGalleryLabel . '</span><br />';
        $objTabs = $this->newObject('tabs', 'jquerycore');                
        $objTabs->setCssId('context_tabs');
        $objTabs->setSelected((int) $selected);
        foreach ($contextGalleries as $contextCode => $galleries)
        {
            $context = $this->objContext->getContext($contextCode);
            $contextContent = '<h1>' . ucfirst(strtolower($contextGalleriesLabel)) . '</h1>';
            $tab = array(
                'title' => ucfirst(strtolower($context['title'])),
                'content' => $contextContent . $this->showGalleryDisplay(FALSE, $galleries, $contextCode, $selected),
            );
            $objTabs->addTab($tab);
        }
        $content .= $objTabs->show();

        $contextTab = array(
            'title' => ucfirst(strtolower($contextLabel)),
            'content' => $content,
        );                

        return $contextTab;
    }
    
    /**
     *
     * Method to display the context gallery albums
     * 
     * @access private
     * @param string The id of the gallery to get albums for
     * @param array $contexts The array of user contexts
     * @param integer $selected The tab selected
     * @return string $content The albums display string 
     */
    private function showContextGalleryAlbums($galleryId, $contexts, $selected)
    {
        $defineAlbumLabel = $this->objLanguage->languageText('mod_imagegallery_definealbum', 'imagegallery', 'ERROR: mod_imagegallery_definealbum');
        $contextLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextimages', 'imagegallery', NULL, 'ERROR: mod_imagegallery_contextimages');
        $contextAlbumsLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextalbums', 'imagegallery', NULL, 'ERROR: mod_imagegallery_contextalbums');
        $contextGalleriesLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextgalleries', 'imagegallery', NULL, 'ERROR: mod_imagegallery_contextgalleries');

        $gallery = $this->objDBgalleries->getGallery($galleryId);

        $contextAlbums = array();
        foreach ($contexts as $contextCode)
        {
            $albums = $this->objDBalbums->getContextAlbums($gallery['context_code']);
            if ($contextCode == $gallery['context_code'])
            {
                $contextAlbums[$contextCode] = $albums;
            }
            else
            {
                $contextAlbums[$contextCode] = array();
            }
        }

        $content = '<span class="success">' . $defineAlbumLabel . '</span><br />';
        $objTabs = $this->newObject('tabs', 'jquerycore');                
        $objTabs->setCssId('context_tabs');
        $objTabs->setSelected((int) $selected);
        foreach ($contextAlbums as $contextCode => $albums)
        {
            $context = $this->objContext->getContext($contextCode);
            if ($contextCode == $gallery['context_code'])
            {
                $uri = $this->uri(array('action' => 'view', 'tabs' => '2|' . (int) $selected), 'imagegallery');
                $objLink = new link($uri);
                $objLink->link = ucfirst(strtolower($contextGalleriesLabel));
                $galleriesLink = $objLink->show();

                $contextContent = '<h1>' . $galleriesLink . '&nbsp;|&nbsp;' . $gallery['title'] . '&nbsp;-&nbsp;' . ucfirst(strtolower($contextAlbumsLabel)) . '</h1>';
                $tab = array(
                    'title' => ucfirst(strtolower($context['title'])),
                    'content' => $contextContent . $this->showAlbumDisplay($albums, $galleryId, $selected),
                );
                $objTabs->addTab($tab);
            }
            else
            {
                $galleries = $this->objDBgalleries->getContextGalleries($contextCode);
                $contextContent = '<h1>' . ucfirst(strtolower($contextGalleriesLabel)) . '</h1>';
                $tab = array(
                    'title' => ucfirst(strtolower($context['title'])),
                    'content' => $contextContent . $this->showGalleryDisplay(FALSE, $galleries, $contextCode),
                );
                $objTabs->addTab($tab);
            }
        }
        $content .= $objTabs->show();

        $contextTab = array(
            'title' => ucfirst(strtolower($contextLabel)),
            'content' => $content,
        );                

        return $contextTab;
    }
    
    /**
     *
     * Method to display the context album images
     * 
     * @access private
     * @param string The id of the album to get images for
     * @param array $contexts The array of user contexts
     * @param integer $selected The tab selected
     * @return string $content The images display string 
     */
    private function showContextAlbumImages($albumId, $contexts, $selected)
    {
        $contextLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextimages', 'imagegallery', NULL, 'ERROR: mod_imagegallery_contextimages');
        $contextImagesLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextimages', 'imagegallery', NULL, 'ERROR: mod_imagegallery_conteximages');
        $contextGalleriesLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextgalleries', 'imagegallery', NULL, 'ERROR: mod_imagegallery_contextgalleries');

        $album = $this->objDBalbums->getAlbum($albumId);
        $gallery = $this->objDBgalleries->getGallery($album['gallery_id']);

        $contextImages = array();
        foreach ($contexts as $contextCode)
        {
            $images = $this->objDBimages->getAlbumImages($albumId);
            if ($contextCode == $album['context_code'])
            {
                $contextImages[$contextCode] = $images;
            }
            else
            {
                $contextImages[$contextCode] = array();
            }
        }

        $objTabs = $this->newObject('tabs', 'jquerycore');                
        $objTabs->setCssId('context_tabs');
        $objTabs->setSelected((int) $selected);
        foreach ($contextImages as $contextCode => $images)
        {
            $context = $this->objContext->getContext($contextCode);
            if ($contextCode == $album['context_code'])
            {
                $uri = $this->uri(array('action' => 'view', 'tabs' => '2|' . (int) $selected), 'imagegallery');
                $objLink = new link($uri);
                $objLink->link = ucfirst(strtolower($contextGalleriesLabel));
                $galleriesLink = $objLink->show();

                $uri = $this->uri(array('action' => 'view', 'gallery_id' => $album['gallery_id'], 'tabs' => '2|' . (int) $selected), 'imagegallery');
                $objLink = new link($uri);
                $objLink->link = $gallery['title'];
                $galleryLink = $objLink->show();

                $contextContent = '<h1>' . $galleriesLink . '&nbsp;|&nbsp;' . $galleryLink . '&nbsp;-&nbsp;' . $album['title'] . '&nbsp;-&nbsp;' . $contextImagesLabel . '</h1>';
                $tab = array(
                    'title' => ucfirst(strtolower($context['title'])),
                    'content' => $contextContent . $this->showImageDisplay($images, $albumId, $selected),
                );
                $objTabs->addTab($tab);
            }
            else
            {
                $galleries = $this->objDBgalleries->getContextGalleries($contextCode);
                $contextContent = '<h1>' . ucfirst(strtolower($contextGalleriesLabel)) . '</h1>';
                $tab = array(
                    'title' => ucfirst(strtolower($context['title'])),
                    'content' => $contextContent . $this->showGalleryDisplay(FALSE, $galleries, $contextCode),
                );
                $objTabs->addTab($tab);
            }
        }
        $content = $objTabs->show();

        $contextTab = array(
            'title' => ucfirst(strtolower($contextLabel)),
            'content' => $content,
        );                

        return $contextTab;
    }
    
    /**
     *
     * Method to generate the gallery display 
     * 
     * @access private
     * @param boolean $isUser TRUE if gthe galleries are user galleries | FALSE if not
     * @param array $galleries The user galleries
     * @param string $contextCode The context code of the gallery
     * @param integer $selected The tab of the selected context
     * @return string $string The html display string for user galleries
     */
    private function showGalleryDisplay($isUser, $galleries, $contextCode = NULL, $selected = NULL)
    {
        $addGalleryLabel = $this->objLanguage->languageText('mod_imagegallery_addgallery', 'imagegallery', 'ERROR: mod_imagegallery_addgallery');
        $noGalleriesLabel = $this->objLanguage->languageText('mod_imagegallery_nogalleries', 'imagegallery', 'ERROR: mod_imagegallery_definegallery');
        $clickLabel = $this->objLanguage->languageText('mod_imagegallery_galleryclick', 'imagegallery', 'ERROR: mod_imagegallery_galleryclick');
        $emptyLabel = $this->objLanguage->languageText('word_empty', 'system', 'ERROR: word_empty');
        $oneAlbumLabel = $this->objLanguage->languageText('mod_imagegallery_onealbum', 'imagegallery', 'ERROR: mod_imagegallery_onealbum');
        $oneImageLabel = $this->objLanguage->languageText('mod_imagegallery_oneimage', 'imagegallery', 'ERROR: mod_imagegallery_oneimage');
        $sharedLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        $yesLabel = $this->objLanguage->languageText('word_yes', 'system', 'ERROR: word_yes');
        $noLabel = $this->objLanguage->languageText('word_no', 'system', 'ERROR: word_no');
        $browseLabel = $this->objLanguage->languageText('mod_imagegallery_browsealbums', 'imagegallery', 'ERROR: mod_imagegallery_browsealbums');
        $editLabel = $this->objLanguage->languageText('mod_imagegallery_editgallery', 'imagegallery', 'ERROR: mod_imagegallery_editgallery');
        $deleteLabel = $this->objLanguage->languageText('mod_imagegallery_deletegallery', 'imagegallery', 'ERROR: mod_imagegallery_deletegallery');
        $addLabel = $this->objLanguage->languageText('mod_imagegallery_addalbum', 'imagegallery', 'ERROR: mod_imagegallery_addalbum');        
        $uploadLabel = $this->objLanguage->languageText('mod_imagegallery_upload', 'imagegallery', 'ERROR: mod_imagegallery_upload');
        $confirmLabel = $this->objLanguage->languageText('mod_imagegallery_confrimgallery', 'imagegallery', 'ERROR: mod_imagegallery_confrimgallery');
        $warningLabel = $this->objLanguage->languageText('mod_imagegallery_warngallery', 'imagegallery', 'ERROR: mod_imagegallery_warngallery');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $detailsLabel = $this->objLanguage->languageText('mod_imagegallery_viewgallerydetails', 'imagegallery', 'ERROR: mod_imagegallery_viewgallerydetails');
        $optionsLabel = $this->objLanguage->languageText('mod_imagegallery_galleryoptions', 'imagegallery', 'ERROR: mod_imagegallery_galleryoptions');
        $emptyClickLabel = $this->objLanguage->languageText('mod_imagegallery_galleryemptyclick', 'imagegallery', 'ERROR: mod_imagegallery_galleryemptyclick');
        $resetLabel = $this->objLanguage->languageText('mod_imagegallery_resetgallerycover', 'imagegallery', 'ERROR: mod_imagegallery_resetgallerycover');

        $string = NULL;
        if (empty($galleries))
        {
            $string .= $this->error($noGalleriesLabel) . '<br />';
        }
        
        $this->objIcon->setIcon('gallery_add', '');        
        $this->objIcon->title = $addGalleryLabel;
        $this->objIcon->alt = $addGalleryLabel;
        $addIcon = $this->objIcon->show();

        if ($isUser)
        {
            $string .= '<a href="#" id="' . $this->userId . '" class="gallery_user">' . $addIcon . '&nbsp;' . $addGalleryLabel . '</a><br />';
        }
        else
        {
            $string .= '<a href="#" id="' . $contextCode . '" class="gallery_context">' . $addIcon . '&nbsp;' . $addGalleryLabel . '</a><br />';            
        }

        if (!empty($galleries))
        {
            foreach ($galleries as $gallery)
            {
                $options = NULL;
                $albums = $this->objDBalbums->getGalleryAlbums($gallery['id']);
                $images = $this->objDBimages->getGalleryImages($gallery['id']);

                if (empty($gallery['cover_image_id']))
                {
                    $this->objIcon->setIcon('no_photo', 'png');
                    $this->objIcon->title = '';
                    $this->objIcon->alt = '';
                    $image = $this->objIcon->show();
                }
                else
                {
                    $filename = $this->objFileMan->getFileName($gallery['cover_image_id']); 
                    $path = $this->objThumbnails->getThumbnail($gallery['cover_image_id'], $filename);
                    $image = '<img src="' . $path . '"/>';
                }

                $random = time() . '_' . mt_rand();
                $objTooltip = $this->newObject('tooltip', 'jquerycore');
                $objTooltip->setCssId($random);
                $objTooltip->setShowUrl(FALSE);
                $objTooltip->load();
                
                if (count($albums) > 0)
                {
                    $string .= '<div class="gallery" id="' . $gallery['id'] . '">';
                    $title = $gallery['title'] . ' - ' . $clickLabel;                    
                    $string .= '<div id="' . $random . '" title="' . $title . '" class="gallery_albums">' . $image . '</div>';
                }
                else
                {
                    $string .= '<div class="gallery" id="' . $gallery['id'] . '">';
                    $title = $gallery['title'] . ' - ' . $emptyClickLabel;
                    $string .= '<div id="' . $random . '" title="' . $title . '" class="gallery_empty">' . $image . '</div>';
                }
                                
                switch (count($albums))
                {
                    case 0:
                        $string .= '<div class="gallery_info">(' . strtolower($emptyLabel) . ')</div>';
                        break;
                    case 1:
                        $string .= '<div class="gallery_info">' . $oneAlbumLabel . '</div>';
                        if (count($images) == 1)
                        {
                            $string .= '<div class="gallery_info">' . $oneImageLabel . '</div>';
                        }
                        elseif (count($images) > 1)
                        {
                            $array = array('count' => count($images));
                            $string .= '<div class="gallery_info">' . $this->objLanguage->code2Txt('mod_imagegallery_manyimages', 'imagegallery', $array, 'ERROR: mod_imagegallery_manyimages') . '</div>';
                        }
                        else
                        {
                            $string .= '';
                        }
                        break;
                    default:
                        $array = array('count' => count($albums));
                        $string .= '<div class="gallery_info">' . $this->objLanguage->code2Txt('mod_imagegallery_manyalbums', 'imagegallery', $array, 'ERROR: mod_imagegallery_manyalbums') . '</div>';
                        if (count($images) == 1)
                        {
                            $string .= '<div class="gallery_info">' . $oneImageLabel . '</div>';
                        }
                        elseif (count($images) > 1)
                        {
                            $array = array('count' => count($images));
                            $string .= '<div class="gallery_info">' . $this->objLanguage->code2Txt('mod_imagegallery_manyimages', 'imagegallery', $array, 'ERROR: mod_imagegallery_manyimages') . '</div>';
                        }
                        break;                        
                }
                
                $string .= '<br /><div id="title_' . $gallery['id'] . '"><b>' . $titleLabel . ': </b>' . $gallery['title'] . '</div>';

                $shared = ($gallery['is_shared'] == 1) ? $yesLabel : $noLabel;                
                $string .= '<div id="shared_' . $gallery['id'] . '"><b>' . $sharedLabel . ': </b>' . $shared . '</div>';

                $this->objIcon->setIcon('gallery_options', 'png');        
                $this->objIcon->title = $optionsLabel;
                $this->objIcon->alt = $optionsLabel;
                $optionIcon = $this->objIcon->show();
                $string .= '<a href="#" class="gallery_options" id="' . $gallery['id'] . '">' . $optionIcon . '&nbsp;' . $optionsLabel . '</a><br />'; 

                $string .= '</div>';    
                
                $options = '<b>' . $gallery['title'] . '</b><br /><br />';
                
                if (count($albums) > 0)
                {
                    $this->objIcon->setIcon('gallery_go', 'png');
                    $this->objIcon->title = $browseLabel;
                    $this->objIcon->alt = $browseLabel;
                    $image = $this->objIcon->show();
                    
                    $uri = $this->uri(array('action' => 'view', 'gallery_id' => $gallery['id']), 'imagegallery');
                    
                    $objLink = new link($uri);
                    $objLink->link = $image . '&nbsp;' . $browseLabel;
                    $link = $objLink->show();
                    $options .= $link . '<br />';
                }

                $this->objIcon->setIcon('gallery_magnify', 'png');
                $this->objIcon->title = $detailsLabel;
                $this->objIcon->alt = $detailsLabel;
                $image = $this->objIcon->show();                
                $options .= '<a href="#" class="gallery_details" id="' . $gallery['id'] . '">' . $image . '&nbsp;' . $detailsLabel . '</a><br />'; 

                $this->objIcon->setIcon('gallery_edit', 'png');
                $this->objIcon->title = $editLabel;
                $this->objIcon->alt = $editLabel;
                $image = $this->objIcon->show();                
                $options .= '<a href="#" class="gallery_edit" id="' . $gallery['id'] . '">' . $image . '&nbsp;' . $editLabel . '</a><br />'; 

                $this->objIcon->setIcon('gallery_delete', 'png');
                $this->objIcon->title = $deleteLabel;
                $this->objIcon->alt = $deleteLabel;
                $icon = $this->objIcon->show();
                if (empty($gallery['context_code']))
                {
                    $location = $this->uri(array('action' => 'deletegallery', 'gallery_id' => $gallery['id']), 'imagegallery');
                }
                else
                {
                    $location = $this->uri(array('action' => 'deletegallery', 'gallery_id' => $gallery['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                }
                $message = $confirmLabel . '<br />' . $this->error($warningLabel);
                $this->objConfirm->setConfirm($icon . '&nbsp;' . $deleteLabel, $location, $message);
                $delete = $this->objConfirm->show();
                $options .= $delete . '<br />';

                $this->objIcon->setIcon('album_add', 'png');
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $image = $this->objIcon->show();                
                $options .= '<a href="#" class="album_add" id="' . $gallery['id'] . '">' . $image . '&nbsp;' . $addLabel . '</a><br />'; 

                $this->objIcon->setIcon('picture_upload', 'png');
                $this->objIcon->title = $uploadLabel;
                $this->objIcon->alt = $uploadLabel;
                $image = $this->objIcon->show();                
                $options .= '<a href="#" class="gallery_image_upload" id="' . $gallery['id'] . '">' . $image . '&nbsp;' . $uploadLabel . '</a><br />'; 

                if (!empty($gallery['cover_image_id']))
                {
                    $this->objIcon->setIcon('gallery_reset', 'png');
                    $this->objIcon->title = $resetLabel;
                    $this->objIcon->alt = $resetLabel;
                    $icon = $this->objIcon->show();                

                    if (empty($gallery['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'resetgallerycover', 'gallery_id' => $gallery['id']), 'imagegallery');
                    }
                    else
                    {
                        $uri = $this->uri(array('action' => 'resetgallerycover', 'gallery_id' => $gallery['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $link = new link($uri);
                    $link->link = $icon . '&nbsp;' . $resetLabel;
                    $options .= $link->show() . '<br />';
                }

                $string .= $this->showGalleryOptionsDialog($gallery['id'], $options);
            }
        }  
        return $string;
    }
    
    /**
     *
     * Method to display the add gallery dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showAddGalleryDialog()
    {
        $newLabel = $this->objLanguage->languageText('mod_imagegallery_newgallery', 'imagegallery', 'ERROR: mod_imagegallery_newgallery');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $shareLabel = $this->objLanguage->languageText('mod_imagegallery_sharegallery', 'imagegallery', 'ERROR: mod_imagegallery_sharegallery');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'word_cancel');
        $noTitleLabel = $this->objLanguage->languageText('mod_imagegallery_gallerytitle', 'imageGallery', 'ERROR: mod_imagegallery_gallerytitle');
        $noDescLabel = $this->objLanguage->languageText('mod_imagegallery_gallerydesc', 'imageGallery', 'ERROR: mod_imagegallery_gallerydesc');
        
        $arrayVars = array();
        $arrayVars['no_gallery_title'] = $noTitleLabel;
        $arrayVars['no_gallery_desc'] = $noDescLabel;
        $this->objSvars->varsToJs($arrayVars);

        $objInput = new textinput('gallery_add_user_id', '', 'hidden', '');
        $hiddenInput = $objInput->show();
        
        $objInput = new textinput('gallery_add_context_code', '', 'hidden', '');
        $hiddenInput .= $objInput->show();
        
        $objInput = new textinput('gallery_add_tabs', '', 'hidden', '');
        $hiddenInput .= $objInput->show();

        $objInput = new textinput('gallery_add_title', '', '', '50');
        $titleInput = $objInput->show();

        $objText = new textarea('gallery_add_description', '', '4', '49', '250', $this->leftLabel);
        $descText = $objText->show();
        
        $objCheck = new checkbox('gallery_add_shared');
        $objCheck->setValue('on');
        $shareCheck = $objCheck->show();
        
        $objButton = new button('save', $saveLabel);
        $objButton->setId('gallery_add_save');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('gallery_add_cancel');
        $cancelButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($titleInput, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($descText, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $shareLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($shareCheck, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($hiddenInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $galleryTable = $objTable->show();

        $objForm = new form('gallery_add', $this->uri(array(
            'action' => 'saveaddgallery',
        ), 'imagegallery'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($galleryTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_gallery_add');
        $dialog->setTitle($newLabel);
        $dialog->setContent($form);
        $dialog->setWidth(745);
        $dialog->unsetButtons();
        $string = $dialog->show();
        
        return $string;        
    }
    
    /**
     *
     * Method to display the gallery option dialog
     * 
     * @access private
     * @param string $id The id of the gallery
     * @param string $options The options string to be added to the dialog
     * @return string $string The html string to display the dialog 
     */
    private function showGalleryOptionsDialog($id, $options)
    {
        $optionsLabel = $this->objLanguage->languageText('mod_imagegallery_galleryoptions', 'imagegallery', 'ERROR: mod_imagegallery_galleryoptions');
        $closeLabel = $this->objLanguage->languageText('word_close', 'system', 'ERROR: word_close');
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_gallery_options_' . $id);
        $dialog->setTitle($optionsLabel);
        $dialog->setContent($options);
        $dialog->setWidth(220);
        $dialog->setButtons(array($closeLabel => 'jQuery("#dialog_gallery_options_' . $id . '").dialog("close");'));
        $string = $dialog->show();
        
        $string .= '<div id="upload_dialog"></div>';

        return $string;        
    }

    /**
     *
     * Method to display the veiw gallery details dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showViewGalleryDetailsDialog()
    {
        $viewLabel = $this->objLanguage->languageText('mod_imagegallery_viewgallerydetails', 'imagegallery', 'ERROR: mod_imagegallery_viewgallerydetails');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $shareLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '150px', '', '', '', '', '');
        $objTable->addCell('<div id="gallery_title"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell('<div id="gallery_description"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $shareLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell('<div id="gallery_shared"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $galleryTable = $objTable->show();

        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_gallery_view');
        $dialog->setTitle($viewLabel);
        $dialog->setContent($galleryTable);
        $dialog->setWidth(500);
        $string = $dialog->show();
        
        return $string;        
    }
    
    /**
     *
     * Method to display the edit gallery dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showEditGalleryDialog()
    {
        $editLabel = $this->objLanguage->languageText('mod_imagegallery_editgallery', 'imagegallery', 'ERROR: mod_imagegallery_editgallery');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $shareLabel = $this->objLanguage->languageText('mod_imagegallery_sharegallery', 'imagegallery', 'ERROR: mod_imagegallery_sharegallery');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'word_cancel');
        
        $objInput = new textinput('gallery_edit_gallery_id', '', 'hidden', '');
        $hiddenInput = $objInput->show();
        
        $objInput = new textinput('gallery_edit_tabs', '', 'hidden', '');
        $hiddenInput .= $objInput->show();

        $objInput = new textinput('gallery_edit_title', '', '', '50');
        $titleInput = $objInput->show();

        $objText = new textarea('gallery_edit_description', '', '4', '49', '250', $this->leftLabel);
        $descText = $objText->show();
        
        $objCheck = new checkbox('gallery_edit_shared');
        $objCheck->setValue('on');
        $shareCheck = $objCheck->show();
        
        $objButton = new button('save', $saveLabel);
        $objButton->setId('gallery_edit_save');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('gallery_edit_cancel');
        $cancelButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($titleInput, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($descText, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $shareLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($shareCheck, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($hiddenInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $galleryTable = $objTable->show();

        $objForm = new form('gallery_edit', $this->uri(array(
            'action' => 'saveeditgallery',
        ), 'imagegallery'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($galleryTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_gallery_edit');
        $dialog->setTitle($editLabel);
        $dialog->setContent($form);
        $dialog->setWidth(745);
        $dialog->unsetButtons();
        $string = $dialog->show();
        
        return $string;        
    }
    
    /**
     *
     * Method to display the edit album dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showEditAlbumDialog()
    {
        $editLabel = $this->objLanguage->languageText('mod_imagegallery_editalbum', 'imagegallery', 'ERROR: mod_imagegallery_editalbum');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $sharedLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'word_cancel');
        
        $objInput = new textinput('album_edit_album_id', '', 'hidden', '');
        $hiddenInput = $objInput->show();
        
        $objInput = new textinput('album_edit_gallery_id', '', 'hidden', '');
        $hiddenInput .= $objInput->show();
        
        $objInput = new textinput('album_edit_tabs', '', 'hidden', '');
        $hiddenInput .= $objInput->show();

        $objInput = new textinput('album_edit_title', '', '', '50');
        $titleInput = $objInput->show();

        $objText = new textarea('album_edit_description', '', '4', '49', '250', $this->leftLabel);
        $descText = $objText->show();
        
        $objCheck = new checkbox('album_edit_shared');
        $sharedCheck = $objCheck->show();
        
        $objButton = new button('save', $saveLabel);
        $objButton->setId('album_edit_save');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('album_edit_cancel');
        $cancelButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($titleInput, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($descText, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $sharedLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($sharedCheck, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($hiddenInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $albumTable = $objTable->show();

        $objForm = new form('album_edit', $this->uri(array(
            'action' => 'saveeditalbum',
        ), 'imagegallery'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($albumTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_album_edit');
        $dialog->setTitle($editLabel);
        $dialog->setContent($form);
        $dialog->setWidth(745);
        $dialog->unsetButtons();
        $string = $dialog->show();
        
        return $string;        
    }
    
    /**
     *
     * Method to display the add album dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showAddAlbumDialog()
    {
        $newLabel = $this->objLanguage->languageText('mod_imagegallery_newalbum', 'imagegallery', 'ERROR: mod_imagegallery_newalbum');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $sharedLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'word_cancel');
        $noTitleLabel = $this->objLanguage->languageText('mod_imagegallery_albumtitle', 'imageGallery', 'ERROR: mod_imagegallery_albumtitle');
        $noDescLabel = $this->objLanguage->languageText('mod_imagegallery_albumdesc', 'imageGallery', 'ERROR: mod_imagegallery_albumdesc');
        
        $arrayVars = array();
        $arrayVars['no_album_title'] = $noTitleLabel;
        $arrayVars['no_album_desc'] = $noDescLabel;
        $this->objSvars->varsToJs($arrayVars);

        $objInput = new textinput('album_add_gallery_id', '', 'hidden', '');
        $hiddenInput = $objInput->show();
        
        $objInput = new textinput('album_add_tabs', '', 'hidden', '');
        $hiddenInput .= $objInput->show();

        $objInput = new textinput('album_add_title', '', '', '50');
        $titleInput = $objInput->show();

        $objText = new textarea('album_add_description', '', '4', '49', '250', $this->leftLabel);
        $descText = $objText->show();
        
        $objCheck = new checkbox('album_add_shared');
        $sharedCheck = $objCheck->show();
        
        $objButton = new button('save', $saveLabel);
        $objButton->setId('album_add_save');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('album_add_cancel');
        $cancelButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($titleInput, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($descText, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $sharedLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($sharedCheck, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($hiddenInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $galleryTable = $objTable->show();

        $objForm = new form('album_add', $this->uri(array(
            'action' => 'saveaddalbum',
        ), 'imagegallery'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($galleryTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_album_add');
        $dialog->setTitle($newLabel);
        $dialog->setContent($form);
        $dialog->setWidth(745);
        $dialog->unsetButtons();
        $string = $dialog->show();
        
        return $string;        
    }

    /**
     *
     * Method to generate the album display 
     * 
     * @access private
     * @param array $albums The user albums
     * @param string $galleryId The id of the gallery in which the album is in
     * @param integer $selected The selected context tab
     * @return string $string The html display string for user galleries
     */
    private function showAlbumDisplay($albums, $galleryId, $selected = NULL)
    {
        $addAlbumLabel = $this->objLanguage->languageText('mod_imagegallery_addalbum', 'imagegallery', 'ERROR: mod_imagegallery_addalbum');
        $noAlbumsLabel = $this->objLanguage->languageText('mod_imagegallery_noalbums', 'imagegallery', 'ERROR: mod_imagegallery_noalbums');
        $clickLabel = $this->objLanguage->languageText('mod_imagegallery_albumclick', 'imagegallery', 'ERROR: mod_imagegallery_albumclick');
        $emptyLabel = $this->objLanguage->languageText('word_empty', 'system', 'ERROR: word_empty');
        $oneImageLabel = $this->objLanguage->languageText('mod_imagegallery_oneimage', 'imagegallery', 'ERROR: mod_imagegallery_oneimage');
        $sharedLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        $yesLabel = $this->objLanguage->languageText('word_yes', 'system', 'ERROR: word_yes');
        $noLabel = $this->objLanguage->languageText('word_no', 'system', 'ERROR: word_no');
        $browseLabel = $this->objLanguage->languageText('mod_imagegallery_browseimages', 'imagegallery', 'ERROR: mod_imagegallery_browseimages');
        $editLabel = $this->objLanguage->languageText('mod_imagegallery_editalbum', 'imagegallery', 'ERROR: mod_imagegallery_editalbum');
        $deleteLabel = $this->objLanguage->languageText('mod_imagegallery_deletealbum', 'imagegallery', 'ERROR: mod_imagegallery_deletealbum');
        $uploadLabel = $this->objLanguage->languageText('mod_imagegallery_upload', 'imagegallery', 'ERROR: mod_imagegallery_upload');
        $confirmLabel = $this->objLanguage->languageText('mod_imagegallery_confirmalbum', 'imagegallery', 'ERROR: mod_imagegallery_confirmalbum');
        $warningLabel = $this->objLanguage->languageText('mod_imagegallery_warnalbum', 'imagegallery', 'ERROR: mod_imagegallery_warnalbum');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $detailsLabel = $this->objLanguage->languageText('mod_imagegallery_viewalbumdetails', 'imagegallery', 'ERROR: mod_imagegallery_viewalbumdetails');
        $optionsLabel = $this->objLanguage->languageText('mod_imagegallery_albumoptions', 'imagegallery', 'ERROR: mod_imagegallery_albumoptions');
        $emptyClickLabel = $this->objLanguage->languageText('mod_imagegallery_albumemptyclick', 'imagegallery', 'ERROR: mod_imagegallery_albumemptyclick');
        $coverLabel = $this->objLanguage->languageText('mod_imagegallery_setgallerycover', 'imagegallery', 'ERROR: mod_imagegallery_setgallerycover');
        $resetLabel = $this->objLanguage->languageText('mod_imagegallery_resetalbumcover', 'imagegallery', 'ERROR: mod_imagegallery_resetalbumcover');

        $string = NULL;
        if (empty($albums))
        {
            $string .= $this->error($noAlbumsLabel) . '<br />';
        }
        
        $this->objIcon->setIcon('album_add', '');        
        $this->objIcon->title = $addAlbumLabel;
        $this->objIcon->alt = $addAlbumLabel;
        $addIcon = $this->objIcon->show();

        $string .= '<a href="#" id="' . $galleryId . '" class="album_add">' . $addIcon . '&nbsp;' . $addAlbumLabel . '</a><br />';            

        if (!empty($albums))
        {
            foreach ($albums as $album)
            {
                $options = NULL;
                $images = $this->objDBimages->getAlbumImages($album['id']);

                if (empty($album['cover_image_id']))
                {
                    $this->objIcon->setIcon('no_photo', 'png');
                    $this->objIcon->title = '';
                    $this->objIcon->alt = '';
                    $image = $this->objIcon->show();
                }
                else
                {
                    $filename = $this->objFileMan->getFileName($album['cover_image_id']); 
                    $path = $this->objThumbnails->getThumbnail($album['cover_image_id'], $filename);
                    $image = '<img src="' . $path . '"/>';
                }

                $random = time() . '_' . mt_rand();
                $objTooltip = $this->newObject('tooltip', 'jquerycore');
                $objTooltip->setCssId($random);
                $objTooltip->setShowUrl(FALSE);
                $objTooltip->load();
                
                if (count($images) > 0)
                {
                    $string .= '<div class="album" id="' . $album['id'] . '">';
                    $title = $album['title'] . ' - ' . $clickLabel;
                    
                    if(empty($album['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'view', 'album_id' => $album['id']), 'imagegallery');
                    }
                    else
                    {
                    $uri = $this->uri(array('action' => 'view', 'album_id' => $album['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $link = new link($uri);
                    $link->cssId = $random;
                    $link->title = $title;
                    $link->link = $image;
                    $string .= $link->show();
                }
                else
                {
                    $string .= '<div class="album" id="' . $album['id'] . '">';
                    $title = $album['title'] . ' - ' . $emptyClickLabel;
                    $string .= '<div id="' . $random . '" title="' . $title . '" class="album_empty">' . $image . '</div>';
                }
                                
                switch (count($images))
                {
                    case 0:
                        $string .= '<em class="album_info">(' . strtolower($emptyLabel) . ')</em><br />';
                        break;
                    case 1:
                        $string .= '<br /><em class="album_info">' . $oneImageLabel . '</em>';
                        break;
                    default:
                        $array = array('count' => count($images));
                        $string .= '<br /><em class="album_info">' . $this->objLanguage->code2Txt('mod_imagegallery_manyimages', 'imagegallery', $array, 'ERROR: mod_imagegallery_manyimages') . '</em>';
                        break;                        
                }

                $string .= '<div id="title_' . $album['id'] . '"><b>' . $titleLabel . ': </b>' . $album['title'] . '</div>';

                $shared = ($album['is_shared'] == 1) ? $yesLabel : $noLabel;                
                $string .= '<div id="shared_' . $album['id'] . '"><b>' . $sharedLabel . ': </b>' . $shared . '</div>';

                $this->objIcon->setIcon('album_options', 'png');        
                $this->objIcon->title = $optionsLabel;
                $this->objIcon->alt = $optionsLabel;
                $optionIcon = $this->objIcon->show();
                $string .= '<a href="#" class="album_options" id="' . $album['id'] . '">' . $optionIcon . '&nbsp;' . $optionsLabel . '</a><br />'; 

                $string .= '</div>';    
                
                $options = '<b>' . $album['title'] . '</b><br /><br />';
                
                if (count($images) > 0)
                {
                    $this->objIcon->setIcon('album_go', 'png');
                    $this->objIcon->title = $browseLabel;
                    $this->objIcon->alt = $browseLabel;
                    $icon = $this->objIcon->show();
                    
                    $uri = $this->uri(array('action' => 'view', 'album_id' => $album['id']), 'imagegallery');
                    
                    $objLink = new link($uri);
                    $objLink->link = $icon . '&nbsp;' . $browseLabel;
                    $link = $objLink->show();
                    $options .= $link . '<br />';
                }


                $this->objIcon->setIcon('album_magnify', 'png');
                $this->objIcon->title = $detailsLabel;
                $this->objIcon->alt = $detailsLabel;
                $icon = $this->objIcon->show();                
                $options .= '<a href="#" class="album_details" id="' . $album['id'] . '">' . $icon . '&nbsp;' . $detailsLabel . '</a><br />'; 

                $this->objIcon->setIcon('album_edit', 'png');
                $this->objIcon->title = $editLabel;
                $this->objIcon->alt = $editLabel;
                $icon = $this->objIcon->show();                
                $options .= '<a href="#" class="album_edit" id="' . $album['id'] . '">' . $icon . '&nbsp;' . $editLabel . '</a><br />'; 

                $this->objIcon->setIcon('album_delete', 'png');
                $this->objIcon->title = $deleteLabel;
                $this->objIcon->alt = $deleteLabel;
                $icon = $this->objIcon->show();
                if (empty($album['context_code']))
                {
                    $location = $this->uri(array('action' => 'deletealbum', 'gallery_id' => $album['gallery_id'], 'album_id' => $album['id']), 'imagegallery');
                }
                else
                {
                    $location = $this->uri(array('action' => 'deletealbum', 'gallery_id' => $album['gallery_id'], 'album_id' => $album['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                }
                $message = $confirmLabel . '<br />' . $this->error($warningLabel);
                $this->objConfirm->setConfirm($icon . '&nbsp;' . $deleteLabel, $location, $message);
                $delete = $this->objConfirm->show();
                $options .= $delete . '<br />';

                $this->objIcon->setIcon('picture_upload', 'png');
                $this->objIcon->title = $uploadLabel;
                $this->objIcon->alt = $uploadLabel;
                $icon = $this->objIcon->show();                
                $options .= '<a href="#" class="album_image_upload" id="' . $album['id'] . '">' . $icon . '&nbsp;' . $uploadLabel . '</a><br />'; 

                if (!empty($album['cover_image_id']))
                {
                    $this->objIcon->setIcon('album_cover', 'png');
                    $this->objIcon->title = $coverLabel;
                    $this->objIcon->alt = $coverLabel;
                    $icon = $this->objIcon->show();                

                    if (empty($album['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'setgallerycover', 'file_id' => $album['cover_image_id'], 'gallery_id' => $album['gallery_id']), 'imagegallery');
                    }
                    else
                    {
                        $uri = $this->uri(array('action' => 'setgallerycover', 'file_id' => $album['cover_image_id'], 'gallery_id' => $album['gallery_id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $link = new link($uri);
                    $link->link = $icon . '&nbsp;' . $coverLabel;
                    $options .= $link->show() . '<br />';

                    $this->objIcon->setIcon('album_reset', 'png');
                    $this->objIcon->title = $resetLabel;
                    $this->objIcon->alt = $resetLabel;
                    $icon = $this->objIcon->show();                
                    if (empty($album['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'resetalbumcover', 'album_id' => $album['id'], 'gallery_id' => $album['gallery_id']), 'imagegallery');
                    }
                    else
                    {
                        $uri = $this->uri(array('action' => 'resetalbumcover', 'album_id' => $album['id'], 'gallery_id' => $album['gallery_id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $link = new link($uri);
                    $link->link = $icon . '&nbsp;' . $resetLabel;
                    $options .= $link->show() . '<br />';
                }

                $string .= $this->showAlbumOptionsDialog($album['id'], $options);
            }
        }  
        return $string;
    }
    
    /**
     *
     * Method to display the album option dialog
     * 
     * @access private
     * @param string $id The id of the album
     * @param string $options The options string to be added to the dialog
     * @return string $string The html string to display the dialog 
     */
    private function showAlbumOptionsDialog($id, $options)
    {
        $optionsLabel = $this->objLanguage->languageText('mod_imagegallery_albumoptions', 'imagegallery', 'ERROR: mod_imagegallery_albumoptions');
        $closeLabel = $this->objLanguage->languageText('word_close', 'system', 'ERROR: word_close');
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_album_options_' . $id);
        $dialog->setTitle($optionsLabel);
        $dialog->setContent($options);
        $dialog->setWidth(220);
        $dialog->setButtons(array($closeLabel => 'jQuery("#dialog_album_options_' . $id . '").dialog("close");'));
        $string = $dialog->show();
        
        $string .= '<div id="upload_dialog"></div>';
        
        return $string;        
    }

    /**
     *
     * Method to display the veiw album details dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showViewAlbumDetailsDialog()
    {
        $viewLabel = $this->objLanguage->languageText('mod_imagegallery_viewalbumdetails', 'imagegallery', 'ERROR: mod_imagegallery_viewalbumdetails');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $shareLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '150px', '', '', '', '', '');
        $objTable->addCell('<div id="album_title"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell('<div id="album_description"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $shareLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell('<div id="album_shared"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $albumTable = $objTable->show();

        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_album_view');
        $dialog->setTitle($viewLabel);
        $dialog->setContent($albumTable);
        $dialog->setWidth(500);
        $string = $dialog->show();
        
        return $string;        
    }
    
    /**
     *
     * Method to show the upload files dialog
     * 
     * @access public
     * @param string $galleryId The id of the gallery
     * @param string $albumId The id of the album
     * @return string $string The display string 
     */
    public function ajaxShowUpload($galleryId = NULL, $albumId = NULL)
    {
        $dialogLabel = $this->objLanguage->languageText('mod_imagegallery_upload', 'imagegallery', 'ERROR: mod_imagegallery_upload');
        $defineLabel = $this->objLanguage->languageText('mod_imagegallery_uploaddesc', 'imagegallery', 'ERROR: mod_imagegallery_uploaddesc');
        $warnLabel = $this->objLanguage->languageText('mod_imagegallery_uploadwarning', 'imagegallery', 'ERROR: mod_gallery_uploadwarning');
        $uploadLabel = $this->objLanguage->languageText('word_upload', 'system', 'ERROR: word_upload');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $noImageLabel = $this->objLanguage->languageText('mod_imagegallery_noupload', 'imagegallery', 'ERROR: mod_imagegallery_noupload');
        $moreLabel = $this->objLanguage->languageText('mod_imagegallery_moreboxes', 'imagegallery', 'ERROR: mod_imagegallery_moreboxes');
        $lessLabel = $this->objLanguage->languageText('mod_imagegallery_lessboxes', 'imagegallery', 'ERROR: mod_imagegallery_lessboxes');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $newLabel = $this->objLanguage->languageText('mod_imagegallery_newalbum', 'imagegallery', 'ERROR: mod_imagegallery_newalbum');
        $uploadToLabel = $this->objLanguage->languageText('mod_imagegallery_uploadto', 'imagegallery', 'ERROR: mod_imagegallery_uploadto');
        $inGalleryLabel = $this->objLanguage->languageText('mod_imagegallery_ingallery', 'imagegallery', 'ERROR: mod_imagegallery_ingallery');
        $noTitleLabel = $this->objLanguage->languageText('mod_imagegallery_albumtitle', 'imagegallery', 'ERROR: mod_imagegallery_albumtitle');
        $noDescLabel = $this->objLanguage->languageText('mod_imagegallery_albumdesc', 'imagegallery', 'ERROR: mod_imagegallery_albumdesc');
        
        $defineLabel = '<span class="success">' . $defineLabel . '</span><br />' . $this->error($warnLabel);
        
        if (!empty($galleryId))
        {
            $gallery = $this->objDBgalleries->getGallery($galleryId);            
        }
        else
        {
            $album = $this->objDBalbums->getAlbum($albumId);
            $galleryId = $album['gallery_id'];
            $gallery = $this->objDBgalleries->getGallery($galleryId);            
        }
        
        
        $albums = $this->objDBalbums->getGalleryAlbums($galleryId);

        $script = '<script type="text/javascript">';
        $script .= 'var no_album_title = "' . $noTitleLabel . '";';
        $script .= 'var no_album_desc = "' . $noDescLabel . '";';
        $script .= 'var no_image = "' . $noImageLabel . '";';
        $script .= '</script>';

        if (empty($albumId))
        {
            if (count($albums) > 0)
            {
                $objDrop = new dropdown('image_album_id');
                $objDrop->addOption('', $newLabel);
                $objDrop->addFromDB($albums, 'title', 'id');
                $idDrop = $objDrop->show();
                
                $div = '<div id="new_or_existing_album"><b>' . ucfirst(strtolower($uploadToLabel)) . '</b>&nbsp;' . $idDrop;
                $div .= '<br /><b>' . ucfirst(strtolower($inGalleryLabel)) . '</b>&nbsp;' . $gallery['title'] . '</div>';
            }
            else
            {
                $div = '<div id="new_album"><b>' . ucfirst(strtolower($uploadToLabel)) . '</b>&nbsp;' . $newLabel;
                $div .= '<br /><b>' . ucfirst(strtolower($inGalleryLabel)) . '</b>&nbsp;' . $gallery['title'] . '</div>';
            }
            $albumInput = NULL;
        }
        else
        {
            $objInput = new textinput('image_album_id', $albumId, 'hidden', '50');
            $albumInput = $objInput->show();
            
            $div = '<div id="existing_album"><b>' . ucfirst(strtolower($uploadToLabel)) . '</b>&nbsp;' . $album['title'];
            $div .= '<br /><b>' . ucfirst(strtolower($inGalleryLabel)) . '</b>&nbsp;' . $gallery['title'] . '</div>';
        }
        
        if (empty($albumId))
        {
            $objInput = new textinput('image_album_title', '', '', '50');
            $nameInput = $objInput->show();

            $objText = new textarea('image_album_description', '', '3', '49', '250', $this->leftLabel);
            $descriptionText = $objText->show();

            $newDiv = '<div id="upload_new"><b>' . $titleLabel . ':&nbsp;</b><br />' . $nameInput;
            $newDiv .= '<br /><b>' . $descLabel . ':&nbsp;</b><br />' . $descriptionText . '</div>';        
        }
        else
        {
            $newDiv = NULL;
        }
        
        $objInput = new textinput('image_gallery_id', $galleryId, 'hidden', '50');
        $galleryInput = $objInput->show();
        
        $objInput = new textinput('files[]', '', 'file', '50');
        $fileInput = $objInput->show();
        
        $objInput = new textinput('upload_tabs', '', 'hidden', '');
        $tabsInput = $objInput->show();

        $objButton = new button('upload', $uploadLabel);
        $objButton->setId('image_add_save');
        $objButton->setToSubmit();
        $uploadButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('image_add_cancel');
        $cancelButton = $objButton->show();

        $moreLink = '<a href="#" id="more_images"><em class="warning">' . $moreLabel . '<em></a>';
        $lessLink = '<a href="#" id="less_images" style="display: none;"><em class="warning">' . $lessLabel . '<em></a>';

        $objTable = new htmltable();
        $objTable->id = "upload_table";
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($defineLabel, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($div, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($newDiv, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        for ($i = 1; $i <= 4; $i++)
        {
            $class = ($i <= 2) ? '' : 'more_boxes';
            $objTable->startRow($class);
            $objTable->addCell($fileInput, '', 'top', '', '', '', '');
            $objTable->addCell($fileInput, '', 'top', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell($tabsInput . $galleryInput . $albumInput . $uploadButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($moreLink . $lessLink, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $albumTable = $objTable->show();

        $objForm = new form('image_add', $this->uri(array(
            'action' => 'saveimages',
        ), 'imagegallery'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($albumTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_image_add');
        $dialog->setTitle($dialogLabel);
        $dialog->setContent($form);
        $dialog->setWidth(900);
        $dialog->unsetButtons();
        $dialog->setAutoAppendScript(FALSE);
        $string = $dialog->show();
        $script .= $dialog->script;
        
        return $script . $string;        
   }

    /**
     *
     * Method to do the upload of the images
     * 
     * @access public
     * @return string $albumId The id of the album the images were uploaded to 
     */
    public function doUpload()
    {
        $galleryId = $this->getParam('image_gallery_id');
        $albumId = $this->getParam('image_album_id');
        
        $gallery = $this->objDBgalleries->getGallery($galleryId);
        
        $fields = array();
        $fields['gallery_id'] = $gallery['id'];
        $fields['user_id'] = $gallery['user_id'];
        $fields['context_code'] = $gallery['context_code'];
        $fields['is_shared'] = $gallery['is_shared'];

        if (empty($albumId))
        {
            $title = $this->getParam('image_album_title');
            $description = $this->getParam('image_album_description');
            
            $fields = array();
            $fields['gallery_id'] = $gallery['id'];
            $fields['user_id'] = $gallery['user_id'];
            $fields['context_code'] = $gallery['context_code'];
            $fields['title'] = $title;
            $fields['description'] = $description;
            $fields['is_shared'] = $gallery['is_shared'];
            
            $albumId  = $this->objDBalbums->addAlbum($fields);
        }

        $results = $this->uploadFiles($gallery['context_code']);
        $fields['album_id'] = $albumId;
        $this->addImageToTable($fields, $results);
        return $albumId;
    }


    /**
     *
     * Method to do the physical upload to the database and file system
     * 
     * @access private
     * @param string $contextCode The code of the context if applicable
     * @return array $results An array of results from the uploads
     */
    private function uploadFiles($contextCode = NULL)
    {
        if (empty($contextCode)) {
            $uploadFolder = 'users/' . $this->userId . '/images/';
            $this->objUpload->setUploadFolder($uploadFolder);
        } else {
            $uploadFolder = 'context/' . $contextCode . '/images/';
            $this->objUpload->setUploadFolder($uploadFolder);
        }
        $results = $this->objUpload->uploadFilesArray('files');

        $mimetypes = $this->objArchive->getSupportedTypes();
        if($results) {
            $objConfig = $this->getObject('altconfig','config');
            $removePattern = $objConfig->getsiteRootPath();
            
            foreach($results as $file) {
                // If it is a zip archive
                if ( in_array($file['mimetype'], $mimetypes) ) {
                    $filePath = $this->objFileMan->getFilePath($file['fileid']);
                    $zip = $this->objArchive->open($filePath);
                    $filePath = str_replace(".", "_", $filePath);
                    $fullFilePath = $this->objFileMan->getFullFilePath($file['fileid']);
                    $fullFilePath = str_replace(".", "_", $fullFilePath);
                    $this->objDir->mkdirs($fullFilePath);
                    $zip->extractTo($fullFilePath);
                    $this->objFileMan->deleteFile($file['fileid'],TRUE);
                    //list files in
                    $handle = opendir($fullFilePath);
                    if ($handle)
                    {
                        while (false !== ($dirfile = readdir($handle)))
                        {
                            $mimetype = $this->objMime->getMimeType($fullFilePath . "/" . $dirfile);
                            if(strstr($mimetype, "image/"))
                            {
                                $infoArray = array();
                                // 1) Add to Database
                                
                                
                                
                                $fixedPath = str_replace($removePattern . 'usrfiles/', '', $fullFilePath);
                                $fileId = $this->objFileMan->addFile($dirfile, $fixedPath 
                                  . "/" . $dirfile, filesize($fullFilePath . "/" . $dirfile),
                                  $mimetype, "images", 1, $this->userId, NULL, 
                                  $this->getParam('creativecommons_files', ''));
                                // Get Media Info
                                $fileInfo = $this->objAnalyzeMediaFile->analyzeFile($fullFilePath 
                                  . "/" . $dirfile);
                                                                
                                // Add Information to Databse
                                $this->objMediaFileInfo->addMediaFileInfo($fileId, $fileInfo[0]);
                                // Check whether mimetype needs to be updated
                                if ($fileInfo[1] != '')
                                {
                                    $this->objFileMan->updateMimeType($fileId, $fileInfo[1]);
                                }
                                $this->objThumbnails->createThumbailFromFile($fullFilePath 
                                  . "/" . $dirfile, $fileId);                                        
                                // Update Return Array Details                                        
                                $infoArray['fileid'] = $fileId;
                                $infoArray['success'] = TRUE;
                                $infoArray['path'] = $filePath . "_temp";
                                $infoArray['fullpath'] = $fullFilePath . "/";
                                $infoArray['subfolder'] = "images";
                                $infoArray['original_folder'] = "images";
                                $infoArray['name'] = $dirfile;
                                $infoArray['mimetype'] = $mimetype;
                                $infoArray['errorcode'] = 0;
                                $infoArray['size'] = filesize($fullFilePath . "/" . $dirfile);
                                $results[$dirfile] = $infoArray;
                            } else {
                                @unlink($fullFilePath . "/" . $dirfile);
                            }
                        }
                    }
                    closedir($handle);                            
                } 
            }
        }
        return $results;
    }

    
    /**
     *
     * Method to add the image to the image table
     * 
     * @access private
     * @param array $fields The array of data that has to added to the database
     * @param array $results The results of the file upload
     * @return VOID 
     */
    private function addImageToTable($fields, $results)
    {
        if($results == null)
        {
            return FALSE;
        } 
        
        foreach($results as $result)
        {
            if (!isset ($result['fileid']))
            {
                $result['fileid'] = '';
            }
            if($result['fileid'] != '')
            {
                unset($fields['title']);
                unset($fields['description']);
                $fileName = $result['name'];
                $fileName = str_replace('_', ' ', $fileName);
                $fileName = str_replace('-', ' ', $fileName);
                $fileName = str_replace('.', ' .', $fileName);
                $fields['caption'] = $fileName;
                $fields['file_id'] = $result['fileid'];
                $this->objDBimages->addImage($fields);
            }
        }
    }
    
    /**
     *
     * Method to generate the image display 
     * 
     * @access private
     * @param array $images The user images
     * @param string $albumId The id of the album the images are in
     * @param integer $selected The tab of the context the image is in
     * @return string $string The html display string for image display
     */
    private function showImageDisplay($images, $albumId, $selected = NULL)
    {
        $uploadImageLabel = $this->objLanguage->languageText('mod_imagegallery_upload', 'imagegallery', 'ERROR: mod_imagegallery_upload');
        $noImagesLabel = $this->objLanguage->languageText('mod_imagegallery_noimages', 'imagegallery', 'ERROR: mod_imagegallery_noimages');
        $clickLabel = $this->objLanguage->languageText('mod_imagegallery_imageclick', 'imagegallery', 'ERROR: mod_imagegallery_imageclick');
        $sharedLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        $yesLabel = $this->objLanguage->languageText('word_yes', 'system', 'ERROR: word_yes');
        $noLabel = $this->objLanguage->languageText('word_no', 'system', 'ERROR: word_no');
        $editLabel = $this->objLanguage->languageText('mod_imagegallery_editimagedata', 'imagegallery', 'ERROR: mod_imagegallery_editimagedata');
        $deleteLabel = $this->objLanguage->languageText('mod_imagegallery_deleteimage', 'imagegallery', 'ERROR: mod_imagegallery_deleteimage');
        $confirmLabel = $this->objLanguage->languageText('mod_imagegallery_confirmimage', 'imagegallery', 'ERROR: mod_imagegallery_confirmimage');
        $captionLabel = $this->objLanguage->languageText('word_caption', 'system', 'ERROR: word_caption');
        $detailsLabel = $this->objLanguage->languageText('mod_imagegallery_viewimagedetails', 'imagegallery', 'ERROR: mod_imagegallery_viewimagedetails');
        $optionsLabel = $this->objLanguage->languageText('mod_imagegallery_imageoptions', 'imagegallery', 'ERROR: mod_imagegallery_imageoptions');
        $coverLabel = $this->objLanguage->languageText('mod_imagegallery_setalbumcover', 'imagegallery', 'ERROR: mod_imagegallery_setalbumcover');
        $viewLabel = $this->objLanguage->languageText('mod_imagegallery_viewimage', 'imagegallery', 'ERROR: mod_imagegallery_viewimage');

        $string = NULL;
        if (empty($images))
        {
            $string .= $this->error($noImagesLabel) . '<br />';
        }
        
        $this->objIcon->setIcon('picture_upload', '');        
        $this->objIcon->title = $uploadImageLabel;
        $this->objIcon->alt = $uploadImageLabel;
        $addIcon = $this->objIcon->show();

        $string .= '<a href="#" id="' . $albumId . '" class="album_image_upload">' . $addIcon . '&nbsp;' . $uploadImageLabel . '</a><br />';            

        if (!empty($images))
        {
            foreach ($images as $image)
            {
                $options = NULL;

                $filename = $this->objFileMan->getFileName($image['file_id']); 
                $path = $this->objThumbnails->getThumbnail($image['file_id'], $filename);
                $thumbnail = '<img src="' . $path . '" />';

                $random = time() . '_' . mt_rand();
                $objTooltip = $this->newObject('tooltip', 'jquerycore');
                $objTooltip->setCssId($random);
                $objTooltip->setShowUrl(FALSE);
                $objTooltip->load();
                
                $string .= '<div class="image" id="' . $image['id'] . '">';
                $title = $image['caption'] . ' - ' . $clickLabel;
                
                if (empty($image['context_code']))
                {
                    $uri = $this->uri(array('action' => 'view', 'image_id' => $image['id']), 'imagegallery');
                }
                else
                {
                    $uri = $this->uri(array('action' => 'view', 'image_id' => $image['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                }
                $link = new link($uri);
                $link->link = $thumbnail;
                $link->cssId = $random;
                $link->title = $title;
                $string .= $link->show();
                
                $string .= '<div id="title_' . $image['id'] . '"><b>' . $captionLabel . ': </b>' . $image['caption'] . '</div>';

                $shared = ($image['is_shared'] == 1) ? $yesLabel : $noLabel;                
                $string .= '<div id="shared_' . $image['id'] . '"><b>' . $sharedLabel . ': </b>' . $shared . '</div>';

                $this->objIcon->setIcon('picture_options', 'png');        
                $this->objIcon->title = $optionsLabel;
                $this->objIcon->alt = $optionsLabel;
                $optionIcon = $this->objIcon->show();
                $string .= '<a href="#" class="image_options" id="' . $image['id'] . '">' . $optionIcon . '&nbsp;' . $optionsLabel . '</a><br />'; 

                //$string .= '</div>';    
                
                $options = '<b>' . $image['caption'] . '</b><br /><br />';
                
                $this->objIcon->setIcon('picture_go', 'png');
                $this->objIcon->title = $viewLabel;
                $this->objIcon->alt = $viewLabel;
                $icon = $this->objIcon->show();

                $uri = $this->uri(array('action' => 'view', 'image_id' => $image['id']), 'imagegallery');
                $link = new link($uri);
                $link->link = $icon . '&nbsp;' . $viewLabel;
                $options .= $link->show() . '<br />';

                $this->objIcon->setIcon('picture_magnify', 'png');
                $this->objIcon->title = $detailsLabel;
                $this->objIcon->alt = $detailsLabel;
                $icon = $this->objIcon->show();                
                $options .= '<a href="#" class="image_details" id="' . $image['id'] . '">' . $icon . '&nbsp;' . $detailsLabel . '</a><br />'; 

                $this->objIcon->setIcon('picture_edit', 'png');
                $this->objIcon->title = $editLabel;
                $this->objIcon->alt = $editLabel;
                $icon = $this->objIcon->show();                
                $options .= '<a href="#" class="image_edit" id="' . $image['id'] . '">' . $icon . '&nbsp;' . $editLabel . '</a><br />'; 

                $this->objIcon->setIcon('picture_delete', 'png');
                $this->objIcon->title = $deleteLabel;
                $this->objIcon->alt = $deleteLabel;
                $icon = $this->objIcon->show();
                if (empty($image['context_code']))
                {
                    $location = $this->uri(array('action' => 'deleteimage', 'album_id' => $image['album_id'], 'image_id' => $image['id']), 'imagegallery');
                }
                else
                {
                    $location = $this->uri(array('action' => 'deleteimage', 'album_id' => $image['album_id'], 'image_id' => $image['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                }
                $message = $confirmLabel;
                $this->objConfirm->setConfirm($icon . '&nbsp;' . $deleteLabel, $location, $message);
                $delete = $this->objConfirm->show();
                $options .= $delete . '<br />';

                $this->objIcon->setIcon('picture_cover', 'png');
                $this->objIcon->title = $coverLabel;
                $this->objIcon->alt = $coverLabel;
                $icon = $this->objIcon->show();                

                if (empty($image['context_code']))
                {
                    $uri = $this->uri(array('action' => 'setalbumcover', 'file_id' => $image['file_id'], 'album_id' => $image['album_id'], 'gallery_id' => $image['gallery_id']), 'imagegallery');
                }
                else
                {
                    $uri = $this->uri(array('action' => 'setalbumcover', 'file_id' => $image['file_id'], 'album_id' => $image['album_id'], 'gallery_id' => $image['gallery_id'], 'tabs' => '2|' . $selected), 'imagegallery');
                }
                $link = new link($uri);
                $link->link = $icon . '&nbsp;' . $coverLabel;
                $options .= $link->show() . '<br />';

                $string .= $this->showImageOptionsDialog($image['id'], $options);
                $string .= '</div>';  
            }
        }  
        $string .= '<div id="upload_dialog"></div>';        

        return $string;
    }
    
    /**
     *
     * Method to display the image option dialog
     * 
     * @access private
     * @param string $id The id of the image
     * @param string $options The options string to be added to the dialog
     * @return string $string The html string to display the dialog 
     */
    private function showImageOptionsDialog($id, $options)
    {
        $optionsLabel = $this->objLanguage->languageText('mod_imagegallery_imageoptions', 'imagegallery', 'ERROR: mod_imagegallery_imageoptions');
        $closeLabel = $this->objLanguage->languageText('word_close', 'system', 'ERROR: word_close');
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_image_options_' . $id);
        $dialog->setTitle($optionsLabel);
        $dialog->setContent($options);
        $dialog->setWidth(220);
        $dialog->setButtons(array($closeLabel => 'jQuery("#dialog_image_options_' . $id . '").dialog("close");'));
        $string = $dialog->show();
        
        $string .= '<div id="upload_dialog"></div>';
        
        return $string;        
    }

    /**
     *
     * Method to view the image
     * 
     * @access private
     * @param string $imageId The id of the image to view 
     * @return string $string The html display string
     */
    private function showUserImage($imageId)
    {
        $myGalleriesLabel = $this->objLanguage->languageText('mod_imagegallery_mygalleries', 'imagegallery', 'ERROR: mod_imagegallery_mygalleries');

        $image = $this->objDBimages->getImage($imageId);
        $album = $this->objDBalbums->getAlbum($image['album_id']);
        $gallery = $this->objDBgalleries->getGallery($image['gallery_id']);

        $uri = $this->uri(array('action' => 'view'), 'imagegallery');
        $objLink = new link($uri);
        $objLink->link = $myGalleriesLabel;
        $galleriesLink = $objLink->show();

        $uri = $this->uri(array('action' => 'view', 'gallery_id' => $gallery['id']), 'imagegallery');
        $objLink = new link($uri);
        $objLink->link = $gallery['title'];
        $galleryLink = $objLink->show();

        $uri = $this->uri(array('action' => 'view', 'album_id' => $album['id']), 'imagegallery');
        $objLink = new link($uri);
        $objLink->link = $album['title'];
        $albumLink = $objLink->show();

        $content = '<h1>' . $galleriesLink . '&nbsp;|&nbsp;' . $galleryLink . '&nbsp;|&nbsp;' . $albumLink . '&nbsp;|&nbsp;' . $image['caption'] . '</h1>';
        $content .= $this->viewImage($imageId);
        
        return $content;
    }

    /**
     *
     * Method to display the veiw image details dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showViewImageDetailsDialog()
    {
        $viewLabel = $this->objLanguage->languageText('mod_imagegallery_viewimagedetails', 'imagegallery', 'ERROR: mod_imagegallery_viewimagedetails');
        $captionLabel = $this->objLanguage->languageText('word_caption', 'system', 'ERROR: word_caption');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $shareLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $captionLabel . ': </b>', '150px', '', '', '', '', '');
        $objTable->addCell('<div id="image_caption"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell('<div id="image_description"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $shareLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell('<div id="image_shared"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $imageTable = $objTable->show();

        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_image_view');
        $dialog->setTitle($viewLabel);
        $dialog->setContent($imageTable);
        $dialog->setWidth(500);
        $string = $dialog->show();
        
        return $string;        
    }
    
    /**
     *
     * Method to display the edit image dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showEditImageDialog()
    {
        $editLabel = $this->objLanguage->languageText('mod_imagegallery_editimagedata', 'imagegallery', 'ERROR: mod_imagegallery_editimagedata');
        $captionLabel = $this->objLanguage->languageText('word_caption', 'system', 'ERROR: word_caption');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $sharedLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'word_cancel');
        $noTitleLabel = $this->objLanguage->languageText('mod_imagegallery_imagetitle', 'imagegallery', 'ERROR: mod_imagegallery_imagetitle');
        $noCaptionLabel = $this->objLanguage->languageText('mod_imagegallery_imagecaption', 'imagegallery', 'ERROR: mod_imagegallery_imagecaption');
        $noDescLabel = $this->objLanguage->languageText('mod_imagegallery_imagedesc', 'imagegallery', 'ERROR: mod_imagegallery_imagedesc');
        
        $arrayVars = array();
        $arrayVars['no_image_caption'] = $noCaptionLabel;
        $arrayVars['no_image_desc'] = $noDescLabel;
        $this->objSvars->varsToJs($arrayVars);

        $objInput = new textinput('image_edit_image_id', '', 'hidden', '');
        $hiddenInput = $objInput->show();
        
        $objInput = new textinput('image_edit_album_id', '', 'hidden', '');
        $hiddenInput .= $objInput->show();
        
        $objInput = new textinput('tabs', '', 'hidden', '');
        $hiddenInput .= $objInput->show();

        $objInput = new textinput('image_edit_caption', '', '', '50');
        $captionInput = $objInput->show();

        $objText = new textarea('image_edit_description', '', '4', '49', '250', $this->leftLabel);
        $descText = $objText->show();
        
        $objCheck = new checkbox('image_edit_shared');
        $sharedCheck  = $objCheck->show();
        
        $objButton = new button('save', $saveLabel);
        $objButton->setId('image_edit_save');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('image_edit_cancel');
        $cancelButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $captionLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($captionInput, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($descText, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $sharedLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($sharedCheck, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($hiddenInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $imageTable = $objTable->show();

        $objForm = new form('image_edit', $this->uri(array(
            'action' => 'saveeditimage',
        ), 'imagegallery'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($imageTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_image_edit');
        $dialog->setTitle($editLabel);
        $dialog->setContent($form);
        $dialog->setWidth(745);
        $dialog->unsetButtons();
        $string = $dialog->show();
        
        return $string;        
    }
    
    /**
     *
     * Method to view the image
     * 
     * @access private
     * @param string $imageId The id of the image to view 
     * @param array $contexts The array of contexts
     * @param integer $selected The selected context tab
     * @return string $string The html display string
     */
    private function showContextImage($imageId, $contexts, $selected)
    {
        $contextLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextimages', 'imagegallery', NULL, 'ERROR: mod_imagegallery_contextimages');
        $contextGalleriesLabel = $this->objLanguage->code2Txt('mod_imagegallery_contextgalleries', 'imagegallery', NULL, 'ERROR: mod_imagegallery_contextgalleries');

        $image = $this->objDBimages->getImage($imageId);
        $album = $this->objDBalbums->getAlbum($image['album_id']);
        $gallery = $this->objDBgalleries->getGallery($image['gallery_id']);

        $objTabs = $this->newObject('tabs', 'jquerycore');                
        $objTabs->setCssId('context_tabs');
        $objTabs->setSelected((int) $selected);
        foreach ($contexts as $contextCode)
        {
            $context = $this->objContext->getContext($contextCode);
            if ($contextCode == $album['context_code'])
            {
                $uri = $this->uri(array('action' => 'view', 'tabs' => '2|' . $selected), 'imagegallery');
                $objLink = new link($uri);
                $objLink->link = ucfirst(strtolower($contextGalleriesLabel));
                $galleriesLink = $objLink->show();

                $uri = $this->uri(array('action' => 'view', 'gallery_id' => $gallery['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                $objLink = new link($uri);
                $objLink->link = $gallery['title'];
                $galleryLink = $objLink->show();

                $uri = $this->uri(array('action' => 'view', 'album_id' => $album['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                $objLink = new link($uri);
                $objLink->link = $album['title'];
                $albumLink = $objLink->show();

                $content = '<h1>' . $galleriesLink . '&nbsp;|&nbsp;' . $galleryLink . '&nbsp;|&nbsp;' . $albumLink . '&nbsp;|&nbsp;' . $image['caption'] . '</h1>';
                $content .= $this->viewImage($imageId, $selected);

                $tab = array(
                    'title' => ucfirst(strtolower($context['title'])),
                    'content' => $content,
                );
                $objTabs->addTab($tab);
            }
            else
            {
                $galleries = $this->objDBgalleries->getContextGalleries($contextCode);
                $contextContent = '<h1>' . ucfirst(strtolower($contextGalleriesLabel)) . '</h1>';
                $tab = array(
                    'title' => ucfirst(strtolower($context['title'])),
                    'content' => $contextContent . $this->showGalleryDisplay(FALSE, $galleries, $contextCode),
                );
                $objTabs->addTab($tab);
            }
        }
        $content = $objTabs->show();

        $contextTab = array(
            'title' => ucfirst(strtolower($contextLabel)),
            'content' => $content,
        );                

        return $contextTab;
    }

    /**
     *
     * Method to show the image
     * 
     * @access private
     * @param string $imageId The id of the image to show
     * $param integer $selected The tab of the context if applicable
     * @param boolean $shared TRUE if this is for shared images
     * @return string $string The html display string 
     */
    private function viewImage($imageId, $selected = NULL, $shared = FALSE)
    {
        $sharedImagesLabel = $this->objLanguage->languageText('mod_imagegallery_sharedimages', 'imagegallery', 'ERROR: mod_imagegallery_sharedimages');
        $ownerLabel = $this->objLanguage->languageText('word_owner', 'system', 'ERROR: word_owner');
        $captionLabel = $this->objLanguage->languageText('word_caption', 'system', 'ERROR: word_caption');
        $descLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $closeLabel = $this->objLanguage->languageText('word_close', 'system', 'ERROR: word_close');
        $noCaptionLabel = $this->objLanguage->languageText('mod_imagegallery_nocaption', 'imagegallery', 'ERROR: mod_imagegallery_nocaption');
        $clickLabel = $this->objLanguage->languageText('mod_imagegallery_viewfullimage', 'imagegallery', 'ERROR: mod_imagegallery_viewfullimage');
        $editLabel = $this->objLanguage->languageText('mod_imagegallery_editimagedata', 'imagegallery', 'ERROR: mod_imagegallery_editimagedata');
        $noneLabel = $this->objLanguage->languageText('mod_imagegallery_nonegiven', 'imagegallery', 'ERROR: mod_imagegallery_nonegiven');
        $noneEditLabel = $this->objLanguage->languageText('mod_imagegallery_nonegivenedit', 'imagegallery', 'ERROR: mod_imagegallery_nonegivenedit');
        $sharedLabel = $this->objLanguage->languageText('word_shared', 'system', 'ERROR: word_shared');
        $yesLabel = $this->objLanguage->languageText('word_yes', 'system', 'ERROR: word_yes');
        $noLabel = $this->objLanguage->languageText('word_no', 'system', 'ERROR: word_no');
        $viewsLabel = $this->objLanguage->languageText('word_views', 'system', 'ERROR: word_views');
        $leaveCommentLabel = $this->objLanguage->languageText('mod_imagegallery_leavecomment', 'imagegallery', 'ERROR: mod_imagegallery_leavecomment');
        $editCommentLabel = $this->objLanguage->languageText('mod_imagegallery_editcomment', 'imagegallery', 'ERROR: mod_imagegallery_editcomment');
        $deleteCommentLabel = $this->objLanguage->languageText('mod_imagegallery_deletecomment', 'imagegallery', 'ERROR: mod_imagegallery_deletecomment');
        $confirmCommentLabel = $this->objLanguage->languageText('mod_imagegallery_commentconfirm', 'imagegallery', 'ERROR: mod_imagegallery_commentconfirm');
        $lastCommentsLabel = $this->objLanguage->code2Txt('mod_imagegallery_recentcomments', 'imagegallery', array('count' => 5), 'ERROR: mod_imagegallery_recentcomments');
        $noCommentsLabel = $this->objLanguage->languageText('mod_imagegallery_nocommentsfound', 'imagegallery', 'ERROR: mod_imagegallery_nocommentsfound');
        $postedLabel = $this->objLanguage->languageText('mod_imagegallery_postedon', 'imagegallery', 'ERROR: mod_imagegallery_postedon');

        $image = $this->objDBimages->getImage($imageId);
        $comments = $this->objDBcomments->getImageComments($imageId);

        if (!empty($comments))
        {
            $accordion = $this->newObject('accordion', 'jquerycore');
            $accordion->setCssId('image_comments');
            foreach ($comments as $comment)
            {
                if ($this->userId == $comment['user_id'])
                {
                    $this->objIcon->setIcon('edit_comment', 'png');
                    $this->objIcon->title = $editCommentLabel;
                    $this->objIcon->alt = $editCommentLabel;
                    $icon = $this->objIcon->show();                
                    $editCommentLink = '<br /><a href="#" class="image_edit_comment" id="' . $comment['id'] . '">' . $icon . '&nbsp;' . $editCommentLabel . '</a>'; 
                }
                else
                {
                    $editCommentLink = NULL;
                }
                
                if ($this->userId == $comment['user_id'] || $this->userId == $image['user_id'])
                {
                    $this->objIcon->setIcon('comment_delete', 'png');
                    $this->objIcon->title = $deleteCommentLabel;
                    $this->objIcon->alt = $deleteCommentLabel;
                    $icon = $this->objIcon->show();
                    if ($this->userId == $comment['user_id'])
                    {
                        $location = $this->uri(array('action' => 'deletecomment', 'image_id' => $comment['image_id'], 'comment_id' => $comment['id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                    }
                    else
                    {
                        $location = $this->uri(array('action' => 'deletecomment', 'image_id' => $comment['image_id'], 'comment_id' => $comment['id']), 'imagegallery');
                    }
                    $message = $confirmCommentLabel;
                    $this->objConfirm->setConfirm($icon . '&nbsp;' . $deleteCommentLabel, $location, $message);
                    $deleteCommentLink = '&nbsp;' . $this->objConfirm->show();
                }
                else
                {
                    $deleteCommentLink = NULL;
                }
                 
                $section = array();
                $section['title'] = $this->objUser->fullName($comment['user_id']) . '&nbsp;-&nbsp;' . $postedLabel . ': ' . date('j M Y, H:i');
                $section['content'] = $comment['comment_text'] . $editCommentLink . $deleteCommentLink;
                $accordion->addSection($section);    
            }
            $userComments = $accordion->show();
        }
        else
        {
            $userComments = $this->error($noCommentsLabel);
        }
        
        $navigation = $this->showNavigation($image, $selected, $shared);

        $sharedState = ($image['is_shared'] == 1) ? $yesLabel : $noLabel;
        
        $info = getimagesize($this->objFileMan->getFullFilePath($image['file_id']));
  
        if (isset($info[0])) {
            $width = $info[0];
        } else {
            $width = 500;
        }
        if ($width > 500) {
            $width = 500;
        }
        
        $fullPath = $this->objFileMan->getFilePath($image['file_id']);

        $random = time() . '_' . mt_rand();
        $objTooltip = $this->newObject('tooltip', 'jquerycore');
        $objTooltip->setCssId($random);
        $objTooltip->load();
        
        $normalImage = '<img class="view_image" id="' . $image['id'] . '" src="' . $fullPath. '" width="' . $width . '" />';

        $string = NULL;
        if ($shared)
        {
            $uri = $this->uri(array('action' => 'view', 'tabs' => '1|'), 'imagegallery');
            $link = new link($uri);
            $link->link = $sharedImagesLabel;
            $link->title = $sharedImagesLabel;
            $sharedLink = $link->show();                

            $string .= '<h1>' . $sharedLink . '</h1>';
        }
        $string .= '<div id="image">';
        $string .= '<div id="' . $random . '" title="' . $clickLabel . '" style="width: 505px;">' . $normalImage . '</div>';
        $string .= '<div id="image_nav">' . $navigation . '</div>';
        $string .= '</div>';
        
        $this->objIcon->setIcon('picture_edit', 'png');
        $this->objIcon->title = $editLabel;
        $this->objIcon->alt = $editLabel;
        $icon = $this->objIcon->show();                
        $editLink = '<a href="#" class="image_edit_location" id="' . $image['id'] . '">' . $icon . '&nbsp;' . $editLabel . '</a><br />'; 

        $this->objIcon->setIcon('picture_comment', 'png');
        $this->objIcon->title = $leaveCommentLabel;
        $this->objIcon->alt = $leaveCommentLabel;
        $icon = $this->objIcon->show();                
        $commentLink = '<a href="#" class="image_add_comment" id="' . $image['id'] . '">' . $icon . '&nbsp;' . $leaveCommentLabel . '</a><br />'; 

        if (!$shared)
        {
            $caption = (empty($image['caption'])) ? '<span class="warning">' . $noneEditLabel . '</span>' : $image['caption'];
            $description = (empty($image['description'])) ? '<span class="warning">' . $noneEditLabel . '</span>' : $image['description'];
        }
        else
        {
            $caption = (empty($image['caption'])) ? '<span class="warning">' . $noneLabel . '</span>' : $image['caption'];
            $description = (empty($image['description'])) ? '<span class="warning">' . $noneLabel . '</span>' : $image['description'];
        }
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->width = '100%';
        if (!$shared)
        {
            $objTable->startRow();
            $objTable->addCell($editLink, '', '', '', '', 'colspan="2"', '');
            $objTable->endRow();
        }
        else
        {
            if (!empty($image['user_id']))
            {
                $owner = $this->objUser->fullname($image['user_id']);
            }
            else
            {
                $context = $this->objContext->getContext($image['context_code']);
                $owner = $context['title'];
            }
            $objTable->startRow();
            $objTable->addCell($commentLink, '', '', '', '', 'colspan="2"', '');
            $objTable->endRow();

            $objTable->startRow();
            $objTable->addCell('<b>' . $ownerLabel . ': </b>', '', '', '', '', '', '');
            $objTable->addCell($owner, '', '', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell('<b>' . $captionLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($caption, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $descLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($description, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $sharedLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($sharedState, '', '', '', '', '', '');
        $objTable->endRow();
        if (!$shared)
        {
            $objTable->startRow();
            $objTable->addCell('<b>' . $viewsLabel . ': </b>', '', 'top', '', '', '', '');
            $objTable->addCell($image['view_count'], '', '', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell('<b>' . $lastCommentsLabel . ': </b>', '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($userComments, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $imageTable = $objTable->show();
        
        $string .= '<div id="image_details">' . $imageTable . '</div>';
        
        $random = time() . '_' . mt_rand();
        $objTooltip = $this->newObject('tooltip', 'jquerycore');
        $objTooltip->setCssId($random);
        $objTooltip->setExtraClass('tooltip_always_on_top');
        $objTooltip->load();

        if (empty($image['caption']))
        {
            $title = $noCaptionLabel;
        }
        else
        {
            $title = $image['caption'];
        }

        $fullImage = '<img src="' . $fullPath. '"/>';
        $imageElement = '<div id="' . $random . '" title="' . $title . '">' . $fullImage . '</div>';
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_view_image');
        $dialog->setTitle($image['caption']);
        $dialog->setContent($imageElement);
        $dialog->setShow("slide");
        $dialog->setHide("drop");
        $dialog->setButtons(array($closeLabel => 'jQuery("#dialog_view_image").dialog("close");'));
        $string .= $dialog->show();

        return $string . $this->showAddCommentDialog() . $this->showEditCommentDialog();
    }
    
    /**
     *
     * Method to show the shared images
     * 
     * @access private
     * @return string $string The html display string 
     */
    private function showSharedImages()
    {
        $ownerLabel = $this->objLanguage->languageText('word_owner', 'system', 'ERROR: word_owner');
        $captionLabel = $this->objLanguage->languageText('word_caption', 'system', 'ERROR: word_caption');
        $clickLabel = $this->objLanguage->languageText('mod_imagegallery_viewfullimage', 'imagegallery', 'ERROR: mod_imagegallery_viewfullimage');
        $noSharedLabel = $this->objLanguage->languageText('mod_imagegallery_noshared', 'imagegallery', 'ERROR: mod_imagegallery_noshared');
        
        $images = $this->objDBimages->getSharedImages();
        
        $string = NULL;
        if (empty($images))
        {
            $noImages = $this->error($noSharedLabel);
            $string = $noImages;
        }
        else
        {
            foreach ($images as $image)
            {
                if (!empty($image['user_id']))
                {
                    $owner = $this->objUser->fullname($image['user_id']);
                }
                else
                {
                    $context = $this->objContext->getContext($image['context_code']);
                    $owner = $context['title'];
                }
                $filename = $this->objFileMan->getFileName($image['file_id']); 
                $path = $this->objThumbnails->getThumbnail($image['file_id'], $filename);
                $thumbnail = '<img src="' . $path . '" />';

                $random = time() . '_' . mt_rand();
                $objTooltip = $this->newObject('tooltip', 'jquerycore');
                $objTooltip->setCssId($random);
                $objTooltip->setShowUrl(FALSE);
                $objTooltip->load();
                
                $string .= '<div class="image" id="' . $image['image_id'] . '">';
                $title = $image['caption'] . ' - ' . $clickLabel;
                
                $uri = $this->uri(array('action' => 'view', 'image_id' => $image['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                $link = new link($uri);
                $link->link = $thumbnail;
                $link->cssId = $random;
                $link->title = $title;
                $string .= $link->show();
                
                $string .= '<div id="owner"_' . $image['image_id'] . '"><b>' . $ownerLabel . ': </b>' . $owner . '</div>';

                $string .= '<div id="title_' . $image['image_id'] . '"><b>' . $captionLabel . ': </b>' . $image['caption'] . '</div>';
                
                $string .= '</div>';
            }
        }
        return $string;
    }
    
    /**
     *
     * Method to display the image navigation
     * 
     * @access private
     * @param array $image The current image
     * @param integer $selected The tab of the selected context if applicable
     * @return string $string The navigaion html display 
     */
    private function showNavigation($image, $selected, $shared = FALSE)
    {
        $prevLabel = $this->objLanguage->languageText('word_previous', 'system', 'ERROR: word_previous');
        $nextLabel = $this->objLanguage->languageText('word_next', 'system', 'ERROR: word_next');
        $firstLabel = $this->objLanguage->languageText('word_first', 'system', 'ERROR: word_first');
        $lastLabel = $this->objLanguage->languageText('word_last', 'system', 'ERROR: word_last');

        if (!$shared)
        {
            $images = $this->objDBimages->getAlbumImages($image['album_id']);
        }
        else
        {
            $images = $this->objDBimages->getSharedImages();
        }

        $this->objIcon->setIcon('first', 'png');
        $this->objIcon->title = $firstLabel;
        $this->objIcon->alt = $firstLabel;
        $first = $this->objIcon->show();                

        $this->objIcon->setIcon('first_grey', 'png');
        $this->objIcon->title = $firstLabel;
        $this->objIcon->alt = $firstLabel;
        $firstGrey = $this->objIcon->show();                

        $this->objIcon->setIcon('prev', 'png');
        $this->objIcon->title = $prevLabel;
        $this->objIcon->alt = $prevLabel;
        $prev = $this->objIcon->show();                

        $this->objIcon->setIcon('prev_grey', 'png');
        $this->objIcon->title = $prevLabel;
        $this->objIcon->alt = $prevLabel;
        $prevGrey = $this->objIcon->show();                

        $this->objIcon->setIcon('next', 'png');
        $this->objIcon->title = $nextLabel;
        $this->objIcon->alt = $nextLabel;
        $next = $this->objIcon->show();                

        $this->objIcon->setIcon('next_grey', 'png');
        $this->objIcon->title = $nextLabel;
        $this->objIcon->alt = $nextLabel;
        $nextGrey = $this->objIcon->show();                

        $this->objIcon->setIcon('last', 'png');
        $this->objIcon->title = $lastLabel;
        $this->objIcon->alt = $lastLabel;
        $last = $this->objIcon->show();                

        $this->objIcon->setIcon('last_grey', 'png');
        $this->objIcon->title = $lastLabel;
        $this->objIcon->alt = $lastLabel;
        $lastGrey = $this->objIcon->show();                

        foreach ($images as $key => $line)
        {
            if (array_key_exists('image_id', $line))
            {
                $imageId = $line['image_id'];
            }
            else
            {
                $imageId = $line['id'];
            }

            if ($imageId == $image['id'])
            {
                if ($key == 0)
                {
                    if (count($images) > 1)
                    {
                        if ($shared)
                        {
                            $uri = $this->uri(array('action' => 'view', 'image_id' => $images[1]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                        }
                        elseif (empty($image['context_code']))
                        {
                            $uri = $this->uri(array('action' => 'view', 'image_id' => $images[1]['id']), 'imagegallery');
                        }
                        else
                        {
                            $uri = $this->uri(array('action' => 'view', 'image_id' => $images[1]['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                        }
                        $objLink = new link($uri);
                        $objLink->link = $next;
                        $nextLink = $objLink->show();

                        if ($shared)
                        {
                            $uri = $this->uri(array('action' => 'view', 'image_id' => $images[(count($images) - 1)]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                        }    
                        elseif (empty($image['context_code']))
                        {
                            $uri = $this->uri(array('action' => 'view', 'image_id' => $images[(count($images) - 1)]['id']), 'imagegallery');
                        }
                        else
                        {
                            $uri = $this->uri(array('action' => 'view', 'image_id' => $images[(count($images) - 1)]['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                        }
                        $objLink = new link($uri);
                        $objLink->link = $last;
                        $lastLink = $objLink->show();

                        $string = $firstGrey . '&nbsp;' . $prevGrey . '&nbsp;' . $nextLink . '&nbsp;' . $lastLink;
                    }
                    else
                    {
                        if ($shared)
                        {
                            $uri = $this->uri(array('action' => 'view', 'image_id' => $images[0]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                        }    
                        elseif (empty($image['context_code']))
                        {
                            $uri = $this->uri(array('action' => 'view', 'image_id' => $images[0]['id']), 'imagegallery');
                        }
                        else
                        {
                            $uri = $this->uri(array('action' => 'view', 'image_id' => $images[0]['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                        }

                        $string = $firstGrey . '&nbsp;' . $prevGrey . '&nbsp;' . $nextGrey . '&nbsp;' . $lastGrey;
                    }
                }
                elseif ($key == (count($images) - 1))
                {
                    if ($shared)
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[$key - 1]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                    }
                    elseif (empty($image['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[$key - 1]['id']), 'imagegallery');
                    }
                    else
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[$key - 1]['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $objLink = new link($uri);
                    $objLink->link = $prev;
                    $prevLink = $objLink->show();

                    if ($shared)
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[0]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                    }
                    elseif (empty($image['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[0]['id']), 'imagegallery');
                    }
                    else
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[0]['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $objLink = new link($uri);
                    $objLink->link = $first;
                    $firstLink = $objLink->show();

                    $string = $firstLink . '&nbsp;' . $prevLink . '&nbsp;' . $nextGrey . '&nbsp;' . $lastGrey;
                }
                else
                {
                    if ($shared)
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[$key - 1]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                    }
                    elseif (empty($image['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[$key - 1]['id']), 'imagegallery');
                    }
                    else
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[$key - 1]['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $objLink = new link($uri);
                    $objLink->link = $prev;
                    $prevLink = $objLink->show();

                    if ($shared)
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[0]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                    }
                    elseif (empty($image['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[0]['id']), 'imagegallery');
                    }
                    else
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[0]['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $objLink = new link($uri);
                    $objLink->link = $first;
                    $firstLink = $objLink->show();

                    if ($shared)
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[$key + 1]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                    }
                    elseif (empty($image['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[$key + 1]['id']), 'imagegallery');
                    }
                    else
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[$key + 1]['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $objLink = new link($uri);
                    $objLink->link = $next;
                    $nextLink = $objLink->show();

                    if ($shared)
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[(count($images) - 1)]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
                    }
                    elseif (empty($image['context_code']))
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[(count($images) - 1)]['id']), 'imagegallery');
                    }
                    else
                    {
                        $uri = $this->uri(array('action' => 'view', 'image_id' => $images[(count($images) - 1)]['id'], 'tabs' => '2|' . $selected), 'imagegallery');
                    }
                    $objLink = new link($uri);
                    $objLink->link = $last;
                    $lastLink = $objLink->show();

                    $string = $firstLink . '&nbsp;' . $prevLink . '&nbsp;' . $nextLink . '&nbsp;' . $lastLink;
                }
            }
        }

        return $string;
    }

    /**
     *
     * Method to display the add album dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showAddCommentDialog()
    {
        $leaveCommentLabel = $this->objLanguage->languageText('mod_imagegallery_leavecomment', 'imagegallery', 'ERROR: mod_imagegallery_leavecomment');
        $commentLabel = $this->objLanguage->languageText('word_comment', 'system', 'ERROR: word_comment');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'word_cancel');
        $noCommentLabel = $this->objLanguage->languageText('mod_imagegallery_nocomment', 'imageGallery', 'ERROR: mod_imagegallery_nocomment');
        
        $arrayVars = array();
        $arrayVars['no_comment'] = $noCommentLabel;
        $this->objSvars->varsToJs($arrayVars);

        $objInput = new textinput('add_comment_image_id', '', 'hidden', '');
        $hiddenInput = $objInput->show();
        
        $objText = new textarea('add_comment_comment', '', '4', '49', '250', $this->leftLabel);
        $commentText = $objText->show();
        
        $objButton = new button('save', $saveLabel);
        $objButton->setId('add_comment_save');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('add_comment_cancel');
        $cancelButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $commentLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($commentText, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($hiddenInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $galleryTable = $objTable->show();

        $objForm = new form('comment_add', $this->uri(array(
            'action' => 'saveaddcomment',
        ), 'imagegallery'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($galleryTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_add_comment');
        $dialog->setTitle($leaveCommentLabel);
        $dialog->setContent($form);
        $dialog->setWidth(745);
        $dialog->unsetButtons();
        $string = $dialog->show();
        
        return $string;        
    }

    /**
     *
     * Method to display the add album dialog
     * 
     * @access private
     * @return string $string The html string to display the dialog
     */
    private function showEditCommentDialog()
    {
        $editCommentLabel = $this->objLanguage->languageText('mod_imagegallery_editcomment', 'imagegallery', 'ERROR: mod_imagegallery_editcomment');
        $commentLabel = $this->objLanguage->languageText('word_comment', 'system', 'ERROR: word_comment');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'word_cancel');
        $noCommentLabel = $this->objLanguage->languageText('mod_imagegallery_nocomment', 'imageGallery', 'ERROR: mod_imagegallery_nocomment');
        
        $arrayVars = array();
        $arrayVars['no_comment'] = $noCommentLabel;
        $this->objSvars->varsToJs($arrayVars);

        $objInput = new textinput('edit_comment_id', '', 'hidden', '');
        $hiddenInput = $objInput->show();
        
        $objInput = new textinput('edit_comment_image_id', '', 'hidden', '');
        $hiddenInput .= $objInput->show();
        
        $objText = new textarea('edit_comment_comment', '', '4', '49', '250', $this->leftLabel);
        $commentText = $objText->show();
        
        $objButton = new button('save', $saveLabel);
        $objButton->setId('edit_comment_save');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('edit_comment_cancel');
        $cancelButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $commentLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($commentText, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($hiddenInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $galleryTable = $objTable->show();

        $objForm = new form('comment_edit', $this->uri(array(
            'action' => 'saveeditcomment',
        ), 'imagegallery'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($galleryTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_edit_comment');
        $dialog->setTitle($editCommentLabel);
        $dialog->setContent($form);
        $dialog->setWidth(745);
        $dialog->unsetButtons();
        $string = $dialog->show();
        
        return $string;        
    }
    
    /**
     *
     * Method to show a random shared image in a block
     * 
     * @access public
     * @return string $string The html display string 
     */
    public function showRandomImage()
    {
        $noSharedLabel = $this->objLanguage->languageText('mod_imagegallery_noshared', 'imagegallery', 'ERROR: mod_imagegallery_noshared');
        $clickLabel = $this->objLanguage->languageText('mod_imagegallery_viewfullimage', 'imagegallery', 'ERROR: mod_imagegallery_viewfullimage');
        $ownerLabel = $this->objLanguage->languageText('word_owner', 'system', 'ERROR: word_owner');

        $javascriptFile = $this->getJavaScriptFile('imagegallery_block.js', 'imagegallery');
        $this->appendArrayVar('headerParams', $javascriptFile);

        $sharedImages = $this->objDBimages->getSharedImages();
        
        if (!empty($sharedImages))
        {
            $thumbnails = array();
            foreach ($sharedImages as $key => $image)
            {
                if (!empty($image['user_id']))
                {
                    $owner = $this->objUser->fullname($image['user_id']);
                }
                else
                {
                    $context = $this->objContext->getContext($image['context_code']);
                    $owner = $context['title'];
                }

                $filename = $this->objFileMan->getFileName($image['file_id']); 
                $path = $this->objThumbnails->getThumbnail($image['file_id'], $filename);
                $thumbnails[$key]['source'] = $path;
                $thumbnails[$key]['image_id'] = $image['image_id'];
                $thumbnails[$key]['caption'] = $image['caption'] . ' - ' . $clickLabel;
                $thumbnails[$key]['owner'] = '<b>' . $ownerLabel . ':&nbsp;</b>' . $owner;

                $objTooltip = $this->newObject('tooltip', 'jquerycore');

                $thumbnails[$key]['title'] = $thumbnails[$key]['caption'];
            }

            $arrayVars = array();
            $arrayVars['random_images'] = json_encode($thumbnails);
            $this->objSvars->varsToJs($arrayVars);

            $string = '<div id="' . $thumbnails[0]['image_id'] . '" class="random_image">';
            $title = $thumbnails[0]['title'];

            $uri = $this->uri(array('action' => 'view', 'image_id' => $thumbnails[0]['image_id'], 'tabs' => '1|', 'shared' => 'true'), 'imagegallery');
            $link = new link($uri);
            $link->link = '<img class="random_image" src="' . $thumbnails[0]['source'] . '" />';
            $link->cssClass = "random_image";
            $link->title = $title;
            
            $string .= $link->show();
            $string .= '<p class="random_image">' . $thumbnails[0]['owner'] . '</p>';
            $string .= '</div>';

            return $string;
        }
        else
        {
            return $this->error($noSharedLabel);
        }        
    }
}
?>