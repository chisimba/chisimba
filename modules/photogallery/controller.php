<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * The photo gallery manages the galleries for different sections of the site including personal, context and site galleries
 * @author Wesley Nitsckie
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package photogallery
 *
 * This photo gallery runs off the local database but has a flick extension
 * biult in. It uses phpflickr as an API http://www.phpflickr.com
 *
 *
 * The flickr caching is done on the file system. Please be sure that
 * /usrfiles/phpflickrcache folder exists
 *
 * PEAR/HTTP_REQUEST is also a requirement for the flickr section to run
 *
 *
 * */
require_once('classes/phpFlickr.php');

class photogallery extends controller {

    /**
     * Constructor
     */
    public function init() {


        $this->_objDBContext = $this->getObject('dbcontext', 'context');
        $this->_objUser = $this->getObject('user', 'security');
        $this->_objUtils = $this->getObject('utils');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->_objConfig = $this->getObject('altconfig', 'config');
        $this->_objContextModules = & $this->getObject('dbcontextmodules', 'context');
        $this->_objDBAlbum = $this->getObject('dbalbum', 'photogallery');
        $this->_objDBImage = $this->getObject('dbimages', 'photogallery');
        $this->_objFileMan = $this->getObject('dbfile', 'filemanager');
        $this->_objDBComments = $this->getObject('dbcomments', 'photogallery');
        $this->_objConfig = $this->getObject('altconfig', 'config');
        $this->_objTags = $this->getObject('dbtags', 'tagging');
        $this->_objDBFlickrUsernames = $this->getObject('dbflickrusernames', 'photogallery');
        $this->secretCode = "";
        $this->apiKey = "";
        // Load scriptaclous since we can no longer guarantee it is there
        $scriptaculous = $this->getObject('scriptaculous', 'prototype');
        $this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));
    }

    /**
     * The standard dispatch method
     */
    public function dispatch() {
        $this->setVar('pageSuppressXML', true);
        $action = $this->getParam("action");
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $uselayout = $objSysConfig->getValue('USE_LAYOUT', 'photogallery');
        if ($uselayout) {
            if ($uselayout == 'true' || $uselayout == 'TRUE') {
                $this->setLayoutTemplate('layout_tpl.php');
            } else {
                $this->setLayoutTemplate(NULL);
            }
        } else {
            $this->setLayoutTemplate('layout_tpl.php');
        }
        $css = '<link rel="stylesheet" type="text/css" href="' . $this->getResourceUri('admin.css', 'photogallery') . '" />';
        $css .= '<link rel="stylesheet" type="text/css" href="' . $this->getResourceUri('style/default.css', 'photogallery') . '" />';
        $this->appendArrayVar('headerParams', $css);

        switch ($action) {
            //front view
            //default:


            case null:
                if ($this->_objUser->isLoggedIn()) {
                    return $this->nextAction('front'/* 'overview' */);
                } else {
                    return $this->nextAction('front');
                }

            case 'front':
                return $this->front();

            case 'viewalbum':
                return $this->viewAlbum();

            case 'viewslideshowalbum':
                return $this->viewSlideShow();


            case 'viewimage':
                return $this->viewImage();


            //comments
            case 'addcomment':
                if (!$this->_objUser->isLoggedIn() && (md5(strtoupper($this->getParam('request_captcha'))) != $this->getParam('captcha'))) {
                    $this->setErrorMessage($this->objLanguage->languageText('mod_photogallery_captcha', 'photogallery'));
                    return $this->viewImage();
                } else {
                    $this->_objDBComments->addComment();
                }
                return $this->nextAction('viewimage', array('albumid' => $this->getParam('albumid'), 'imageid' => $this->getParam('imageid')));

            case 'comments':
                $this->setVar('comments', $this->_objDBComments->getUserComments());
                return 'comments_tpl.php';
            case 'editcomment':
                $this->setVar('comment', $this->_objDBComments->getRow('id', $this->getParam('commentid')));
                return 'editcomment_tpl.php';
            case 'saveedit':
                $this->_objDBComments->saveEdit();
                return $this->nextAction('comments');
            case 'deletecomment':
                $this->_objDBComments->delete('id', $this->getParam('commentid'));
                return $this->nextAction('comments');
            case 'addflickrcomment':
                $this->initFlickr();
                $this->_objFlickr->photos_comments_addComment($this->getParam('imageid'), $this->getParam('comment'));
                return $this->nextAction('viewimage', array('albumid' => $this->getParam('albumid'), 'imageid' => $this->getParam('imageid'), 'mode' => 'flickr'));



            //upload section
            case 'uploadsection':
                if ($this->getParam('errmsg') != '') {
                    $this->setVar('errmsg', $this->getParam('errmsg'));
                }
                $this->setVar('albumbsArr', $this->_objDBAlbum->getUserAlbums());
                return 'upload_tpl.php';
            case 'upload':
                return $this->upload();

            case 'overview':
                $this->setVar('tencomments', $this->_objDBComments->getTenRecentComments());
                return 'overview_tpl.php';

            //edit section
            case 'editsection':
                $this->setVar('arrAlbum', $this->_objDBAlbum->getUserAlbums());
                if ($this->initFlickr()) {
                    $this->setVar('flickrusernames', $this->_objDBFlickrUsernames->getUsernames());
                } else {
                    $this->setVar('flickrusernames', NULL);
                }

                return 'edit_tpl.php';

            case 'editalbum':
                $this->setVar('album', $this->_objDBAlbum->getRow('id', $this->getParam('albumid')));
                $this->setVar('thumbnails', $this->_objDBImage->getAlbumImages($this->getParam('albumid')));
                $this->setVar('tagsStr', $this->_objUtils->getTagLIst($this->getParam('albumid')));
                return 'editalbum_tpl.php';
            case 'savealbumedit':
                $this->_objUtils->saveAlbumEdit();
//        		return $this->nextAction('editalbum',array('albumid' => $this->getParam('albumid')));
            case 'savealbumorder':
                $this->_objDBAlbum->reOrderAlbums();
                return $this->nextAction('editsection');
            case 'deletealbum':
                $this->_objUtils->deleteAlbum($this->getParam('albumid'));
                return $this->nextAction('editsection');
            case 'deleteimage':
                $this->_objUtils->deleteImage($this->getParam('imageid'));
                return $this->nextAction('editalbum', array('albumid' => $this->getParam('albumid')));
            case 'sortalbumimages':
                $this->setVar('album', $this->_objDBAlbum->getRow('id', $this->getParam('albumid')));
                $this->setVar('thumbnails', $this->_objDBImage->getAlbumImages($this->getParam('albumid')));
                return 'orderimages_tpl.php';
            case 'saveimageorder':
                $this->_objDBImage->reOrderImages($this->getParam('albumid'));
                return $this->nextAction('sortalbumimages', array('albumid' => $this->getParam('albumid')));
            case 'savealbumdescription':

                $this->setPageTemplate('');
                $this->setLayoutTemplate('');
                $this->_objDBAlbum->saveDescription($this->getParam('albumid'), $this->getParam('myparam'));
                echo $this->getParam('myparam');
                break;
            case 'saveimage':
                die('ha' . $this->getParam('imageid'));
                $this->setPageTemplate('');
                $this->setLayoutTemplate('');
                $this->_objDBImage->saveField($this->getParam('imageid'), $this->getParam('field'), $this->getParam('myparam'));
                echo $this->getParam('myparam');
                break;
            //flickr
            case 'flickr':
                //$this->initFlickr();
                if ($this->getParam('msg') != '') {
                    $this->setVar('msg', $this->getParam('msg'));
                }

                $this->setVar('usernames', $this->_objDBFlickrUsernames->getUsernames());
                return 'flickr_tpl.php';
            case 'validateflickusername':
                return $this->validateFlickrUsernames();

            case 'addtags':
                $uri = $this->uri(array('action' => 'viewalbum', 'albumid' => $this->getParam('albumid')));
                $this->setPageTemplate('');
                $this->setLayoutTemplate('');
                $this->_objTags->insertTags(array($this->getParam('myinput' . $this->getParam('imageid'))), $this->_objUser->userId(), $this->getParam('imageid'), 'photogallery', $uri);
                echo $this->_objUtils->getTagLIst($this->getParam('imageid'));
                break;

            case 'deletetag':

                $this->_objTags->delete('id', $this->getParam('tagid'), 'tbl_tags');
                return $this->nextAction('editalbum', array('albumid' => $this->getParam('albumid')));

            case 'popular':
                $this->setVar('cloud', $this->_objUtils->getPopular());
                $this->setVar('imagelist', $this->_objUtils->getPopularPhotos());
                if ($this->getParam('meta_value')) {
                    $this->setVar('taggedImages', $this->_objUtils->getTaggedImages($this->getParam('meta_value')));
                }
                return 'popular_tpl.php';

            case 'deleteflickrusername':
                $this->_objDBFlickrUsernames->deleteUsername($this->getParam('username'));
                return $this->nextAction('flickr');

            case 'savealbumfield':
                $field = $this->getParam('field');
                $value = $this->getParam('myparam');

            case 'getresults':
                $this->setPageTemplate('blankpage_tpl.php');
                $this->setLayoutTemplate('blank_tpl.php');
                $this->setVar('str', 'some stuff'/* $this->editAlbum($this->getParam('albumid')) */);
                return 'blank_tpl.php';
        }
    }

    /**
     * Method to initialize flickr api
     */
    public function initFlickr() {
        $url = "http://api.flickr.com/services/rest/";

        $objProxy = $this->getObject('proxy', 'utilities');
        $arrProxy = $objProxy->getProxy();

        $this->_objFlickr = new phpFlickr("0b4628c77c757049831c6d873107e533", "e71b890ec35750fb");
        //var_dump($arrProxy);
        //0b4628c77c757049831c6d873107e533
        //setup the proxy to get the flickr images
        if ($arrProxy['proxyserver'] != "") {
            $this->_objFlickr->setProxy($arrProxy['proxyserver'], $arrProxy['proxyport'], $arrProxy['proxyusername'], $arrProxy['proxypassword']);
        }
        //file system caching
        $this->_objFlickr->enableCache("fs", $this->_objConfig->getContentBasePath() . "/photogallery");

        //uncomment the next line if you want to use database caching
        //$this->_objFlickr->enableCache("db",KEWL_DB_DSN);
        $this->_objDBFlickrUsernames = $this->getObject('dbflickrusernames', 'photogallery');

        return true;
    }

    /**
     * Method to get all the albums from flickr
     *
     * @return array
     */
    public function getFlickrSharedAlbums() {
        //$this->_objFlickr = new phpFlickr("710e95b3b34ad8669fe36534a8343773", "d01ff0f7a912a1e3");
        $bigSet = array();
        $users = $this->_objDBFlickrUsernames->getAll();

        foreach ($users as $user) {
            $user['sets'] = array();
            $usr = $this->_objFlickr->people_findByUsername($user['flickr_username']);
            $sets = $this->_objFlickr->photosets_getList($usr['id']);
            array_push($user['sets'], $sets);
            $bigSet[] = $user;
        }

        return $bigSet;
    }

    /**
     * MEthod to view  a flickr SLide Show
     *
     */
    public function viewSlideShow() {

        $albumid = $this->getParam('albumid');
        $owner = $this->getParam('owner');
        $url = 'http://www.flickr.com/slideShow/index.gne?user_id=' . $owner . '&setid=' . $albumid;

        //$url = 'http://www.flickr.com/photos/'.$info['owner'].'/sets/'.$info['id'].'/show/';
        $this->setVar('url', $url);
        $this->initFlickr();
        $this->setVar('albumInfo', $this->_objFlickr->photosets_getInfo($albumid));

        return 'albumslideshow_tpl.php';
    }

    /**
     * Method to get the menu
     * @return string
     */
    public function getMenu() {
        return $this->_objUtils->getNav();
    }

    /**
     *
     */
    public function requiresLogin() {
        //var_dump( $this->getParam('action'));
        //die;
        switch ($this->getParam('action')) {
            default:
                return FALSE;
            case null:
                return FALSE;
            case 'front':
                return FALSE;
            case 'viewalbum':
                return FALSE;
            case 'viewimage':
                return FALSE;
            case 'addcomment':
                return FALSE;
            case 'addflickrcomment':
                return FALSE;
            case 'popular':
                return FALSE;
            case 'viewtag':
                return FALSE;
            case 'viewslideshowalbum':
                return FALSE;
            default:
                return TRUE;
        }
    }

    //The follow methods stems from the dispatch method calls. The reason is to try
    // and keep the code a bit cleaner

    /**
     * Method to validate a flickr username
     * the username will be used to retrieve the
     * photos from the flickr accounts
     *
     * @return template
     */
    public function validateFlickrUsernames() {
        //check if the flickr username is already added to the database
        //by some other user or by the the same user
        if ($this->_objDBFlickrUsernames->usernameExist($this->getParam('username'))) {
            $msg = "Username is already in the database";
            return $this->nextAction('flickr', array('msg' => $msg));
        }

        //check if the username is valid in flickr
        if ($this->initFlickr()) {
            if ($this->_objFlickr->people_findByUsername($this->getParam('username')) == FALSE) {
                $msg = 'The username you added was invalid';
            } else {
                $this->_objDBFlickrUsernames->addUsername();
                $msg = '';
            }
        } else {
            $msg = "Cannot connect Flickr";
        }
        return $this->nextAction('flickr', array('msg' => $msg));
    }

    /**
     * Method to upload an image
     *
     * @return template
     */
    public function upload() {
        //$this->_objUtils->UploadImage($this->getParam('galleryname'));
        if ($this->getParam('albumselected') == '' && $this->getParam('albumtitle') == '') {
            $errmsg = $this->objLanguage->languageText('mod_photogallery_needname', 'photogallery');
            return $this->nextAction('uploadsection', array('errmsg' => $errmsg));
        }

        if (count($_FILES) < 1) {
            $errmsg = $this->objLanguage->languageText('mod_photogallery_needfiles', 'photogallery');
            return $this->nextAction('uploadsection', array('errmsg' => $errmsg));
        }

        $this->_objUtils->doUpload($this->getParam('albumselect'));

        return $this->nextAction('front');
    }

    /**
     * Method to get the front page of the gallery
     *
     * @return template
     */
    public function front() {
        if ($this->_objUser->isLoggedIn()) {
            $this->setVar('albums', $this->_objDBAlbum->getUserAlbums());
        }

        $this->setVar('sharedalbums', $this->_objDBAlbum->getSharedAlbums());
        if (($this->getParam('mode') == 'shared') || ($this->_objUser->isLoggedIn() == FALSE)) {
            if ($this->initFlickr()) {
                $this->setVar('flickralbums', $this->getFlickrSharedAlbums());
            } else {
                $this->setVar('flickralbums', null);
            }
        }

        $this->setVar('pageTitle', 'Photo Gallery');
        return 'front_tpl.php';
    }

    /**
     * Method to view and album
     */
    public function viewAlbum() {
        if ($this->getParam('mode') == 'flickr') {
            $this->initFlickr();
            $this->setVar('images', $this->_objFlickr->photosets_getPhotos($this->getParam('albumid')));
            $this->setVar('nav', $this->_objUtils->getFlickrImageNav($this->_objFlickr));
            return 'viewalbum_flickr_tpl.php';
        }
        $this->_objDBAlbum->incrementHitCount($this->getParam('albumid'));
        $this->setVar('albums', $this->_objDBAlbum->getUserAlbums());
        $this->setVar('images', $this->_objDBImage->getAlbumImages($this->getParam('albumid')));
        return 'viewalbum_tpl.php';
    }

    /**
     * Method to view an image
     */
    public function viewImage() {
        if ($this->getParam('mode') == 'flickr') {
            $this->initFlickr();
            $this->setVar('albums', $this->_objFlickr->photosets_getInfo($this->getParam('albumid')));
            $this->setVar('image', $this->_objFlickr->photos_getInfo($this->getParam('imageid') /* photosets_getPhotos($this->getParam('albumid') */));
            $this->setVar('comments', $this->_objFlickr->photos_comments_getList($this->getParam('imageid')));
            return 'viewimage_flickr_tpl.php';
        } else {
            $this->_objDBImage->incrementHitCount($this->getParam('imageid'));
            $this->setVar('albums', $this->_objDBAlbum->getUserAlbums());
            $this->setVar('images', $this->_objDBImage->getAlbumImages($this->getParam('albumid')));
            $this->setVar('comments', $this->_objDBComments->getImageComments($this->getParam('imageid')));
            $this->setVar('image', $this->_objDBImage->getRow('id', $this->getParam('imageid')));
            return 'viewimage_tpl.php';
        }
    }

}

?>
