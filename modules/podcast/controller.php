<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller for the podcast module
 * @author Tohir Solomons
 */
class podcast extends controller
{
    
    /**
     * Constructor
     *
     */
    public function init()
    {
        try{
            $this->objPodcast =& $this->getObject('dbpodcast');
            $this->objUser =& $this->getObject('user', 'security');
            $this->objDateTime =& $this->getObject('dateandtime', 'utilities');
            $this->objUtils = & $this->getObject('utils','contextadmin');
            $this->objLanguage =& $this->getObject('language', 'language');
            $this->objConfig =& $this->getObject('altconfig', 'config');
            $this->dbContext =& $this->getObject('dbcontext','context');
        
            //Get the activity logger class and log this module call
            $objLog = $this->getObject('logactivity', 'logger');
            $objLog->log();
        } catch(Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }

	/*public function uri($params = array(), $module = '', $mode = '', $omitServerName=FALSE, $javascriptCompatibility = FALSE)
	{
		if (file_exists(chisimabroot/.".htaccess")) {

} else {
	parent::uri($params, $module, $mod = '', $omitServerName=FALSE, $javascriptCompatibility = FALSE)
}
	}*/
    
    /**
     * Dispatch method to indicate action to be taken
     *
     * @param string $action Action to take
     * @return string Template
     */
    public function dispatch($action)
    {

	
        $this->setLayoutTemplate('tpl_podcast_layout.php');
        
        switch ($action)
        {
            case 'addpodcast':
                return 'tpl_addeditpodcast.php';
            case 'savenewpodcast':
                return $this->saveNewPodcast();
            case 'confirmadd':
                return $this->confirmAdd();
            case 'confirmsave':
                return $this->confirmSave();
            case 'editpodcast':
                return $this->editPodcast($this->getParam('id'));
            case 'playpodcast':
                return $this->playPodcast($this->getParam('id'));
            case 'viewpodcast':
                return $this->viewPodcast($this->getParam('id'));
            case 'deletepodcast':
                return $this->deletePodcast($this->getParam('id'));
            case 'downloadfile':
                return $this->downloadFile($this->getParam('id'));
            case 'byuser':
                return $this->showUserPodcasts($this->getParam('id'));
	    //case 'admin':
		//return $this->showAllPodcasts($this->getParam('id'));
            case 'rssfeed':
                return $this->showRssFeed($this->getParam('id'));
            case 'bycourse':
                return $this->showCoursePoadcasts($this->getParam('contextcode'));
            case 'rssfeedbycourse':
                return $this->showRssFeedByCourse($this->getParam('contextcode'));
            case 'uploadpodcast':
                return $this->uploadPodcast();
            default:
                return $this->podcastHome();
        }
        
    }
    
    /**
    * Method to turn off User Login Requirement for certain actions
    * @param string $action Action to be performed
    * @return boolean
    */
    public function requiresLogin($action='home')
    {
        if ($action == '') {
            $action = 'home';
        }
        
        $allowedPreloginActions = array ('home', 'podcast', 'rssfeed', 'playpodcast', 'viewpodcast', 'downloadfile', 'byuser', 'admin' , 'bycourse', 'rssfeedbycourse');
        
        if (in_array($action, $allowedPreloginActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /**
     * Method to show the podcast home page
     *
     * @return string Template
     */
    public function podcastHome()
    {
        $podcasts = $this->objPodcast->getLast5();
        $this->setVar('podcasts', $podcasts);
        
        return 'tpl_listpodcasts.php';
    }
    
    /**
     * Method to show the podcasts of a particular user
     *
     * @param string $id user Id
     * @return string Template
     */
    public function showUserPodcasts($username= '')
    {
        if ($username == '') {
            $username = $this->objUser->userName();
        }

        $id = $this->objUser->getUserId($username);
        
        $this->setVar('id', $id);
        
        $podcasts = $this->objPodcast->getUserPodcasts($id);
        $this->setVar('podcasts', $podcasts);
	
        
        return 'tpl_listpodcasts.php';
    }
    /*
     * Method to show all podcasts
     * @param string $id podcast Id
     * @return string Template
     * @author Nonhlanhla Gangeni
     */

    public function showAllPodcasts($id='')
    {
               
        $this->setVar('id', $id);
        
        $podcasts = $this->objPodcast->getPodcasts($id);
        $this->setVar('podcasts', $podcasts);
        
        return 'tpl_listpodcasts.php';
    }
    
    /**
     * Method to add a new podcast
     */
    public function saveNewPodCast()
    {
        if ($this->getParam('podcast') == '') {
            return $this->nextAction(NULL, array('error'=>'nofileselected'));
        }
        
        $result = $this->objPodcast->addPodcast($this->getParam('podcast'));
        
        if ($result == 'nofile') {
            return $this->nextAction(NULL, array('error'=>'podcastcouldnotbeadded'));
        } else if ($result == 'fileusedalready') {
            return $this->nextAction('confirmadd', array('fileid'=>$this->getParam('podcast')));
        } else {
            return $this->nextAction('confirmadd', array('id'=>$result));
        }
    }
    
    /**
     * Method to confirm podcast, after user has checked details
     */
    public function confirmAdd()
    {
        $id = $this->getParam('id', 'noid');
        $fileid = $this->getParam('fileid', 'noid');
        
        if ($id != 'noid') {
            $podcast = $this->objPodcast->getPodcast($id);
            
            if ($podcast == FALSE) {
                return $this->nextAction('byuser');
            }
            
            if ($podcast['creatorid'] != $this->objUser->userId()) {
                return $this->nextAction('byuser', array('message'=>'notyourpodcast'));
            }
            $this->setVarByRef('podcast', $podcast);
            $this->setVar('mode', 'confirm');
            return 'tpl_confirmadd.php';
        }
        
        if ($fileid != 'noid') {
            
            $podcast = $this->objPodcast->getPodcastByFileId($fileid, $this->objUser->userId());
            
            if ($podcast == FALSE) {
                return $this->nextAction(NULL);
            } else {
                $this->setVarByRef('podcast', $podcast);
                $this->setVar('mode', 'alreadyused');
                return 'tpl_confirmadd.php';    
            }
        }
        
        return $this->nextAction(NULL);
        
    }
    
    /**
     * Method to update a podcast
     */
    public function confirmSave()
    {
        $id = $this->getParam('id');
        $title = $this->getParam('title');
        $description = $this->getParam('description');
        $process = $this->getParam('process');
        $courses = $this->getParam('courses');
        $isUpdate = $this->getparam('isUpdate');

        if ($id == '') {
            return $this->nextAction(NULL);
        } else {
            $podcast = $this->objPodcast->getPodcast($id);
            
            if ($podcast == FALSE) {
                return $this->nextAction('byuser');
            }
            
            if ($podcast['creatorid'] != $this->objUser->userId()) {
                return $this->nextAction('byuser', array('message'=>'notyourpodcast'));
            }
            
            $this->objPodcast->updatePodcast ($id, $title, $description);
             
            if($isUpdate == "yes"){
                $this->objPodcast->deletePodcastContext($id);
                $this->objPodcast->addPodcastContext($courses, $id);
            }else{
                $this->objPodcast->addPodcastContext($courses, $id);
            }
             
            return $this->nextAction('byuser', array('message'=>$process, 'podcast'=>$id));
        }
    }
    
    /**
     * Method to edit a podcast
     *
     * @param string $id Record Id of the Podcast
     */
    public function editPodcast($id)
    {
        if ($id == '') {
            return $this->nextAction(NULL);
        } else {
            $podcast = $this->objPodcast->getPodcast($id);
            
            if ($podcast == FALSE) {
                return $this->nextAction('byuser');
            }
            
            if ($podcast['creatorid'] != $this->objUser->userId()) {
                return $this->nextAction('byuser', array('message'=>'notyourpodcast'));
            }
            
            $this->setVarByRef('podcast', $podcast);
            $this->setVar('isUpdate','yes');
            $this->setVar('mode', 'editpodcast');
            return 'tpl_confirmadd.php';  
        }
    }
    
    /**
     * Method to delete a podcast
     *
     * @param string $id Record id of the podcast
     */
    public function deletePodcast($id)
    {
        if ($id == '') {
            return $this->nextAction(NULL);
        }
        
        $podcast = $this->objPodcast->getPodcast($id);
        
        $result = $this->objPodcast->deletePodcast($id, $this->objUser->userId());
        
        return $this->nextAction('byuser', array('message'=>$result, 'id'=>$this->objUser->userName($podcast['creatorid'])));
    }
    
    /**
     * Method to download a podcast
     *
     * @param string $id Record Id of the podcast
     */
    public function downloadFile($id)
    {
        if ($id == '') {
            return $this->nextAction(NULL, array('message'=>'nopodcastprovided'));
        }
        
        $podcast = $this->objPodcast->getPodcast($id);
        
        if ($podcast == FALSE) {
            return $this->nextAction(NULL, array('message'=>'podcastdoesnotexist'));
        } else {
            return $this->nextAction('file', array('id'=>$podcast['fileid'], 'filename' => $podcast['filename']), 'filemanager');
        }
    }
    
    /**
     * Method to show the RSS Feed
     *
     * @param string $id User Id of the Feed
     */
    public function showRssFeed($id='')
    {
        $rssFeed = $this->getObject('itunesrssgenerator');
        
        if ($id == '') {
            $podcasts = $this->objPodcast->getLast5();
            $rssFeed->title = $this->objLanguage->languageText('mod_podcast_latestpodcastson', 'podcast').' '.$this->objConfig->getSiteName();
            $rssFeed->rssfeedlink = $this->uri(array('action'=>'rssfeed'));
            $rssFeed->description = $this->objLanguage->languageText('mod_podcast_latestpodcastdescription', 'podcast').' '.$this->objConfig->getSiteName().' - '.$this->objConfig->getsiteRoot();
            $rssFeed->author = $this->objConfig->getItem('KEWL_SYSTEM_OWNER');
            $rssFeed->email = $this->objConfig->getsiteEmail();
        } else {
            
            $id = $this->objUser->getUserId($id);
            
            $podcasts = $this->objPodcast->getUserPodcasts($id);
            $rssFeed->title = $this->objUser->fullname($id);
            $rssFeed->rssfeedlink = $this->uri(array('action'=>'rssfeed', 'id'=>$id));
            $rssFeed->description = $this->objLanguage->languageText('mod_podcast_latestpodcastsuploadedbyuser', 'podcast').' '.$this->objUser->fullname($id);
            $rssFeed->author = $this->objUser->email($id);
            $rssFeed->email = $this->objUser->fullname($id);
        }
        
        foreach ($podcasts as $podcast)
        {
            if ($podcast['artist'] == '') {
                $artist = $this->objUser->fullname($podcast['creatorid']);
            } else {
                $artist = $podcast['artist'];
            }
            
            $link = $this->uri(array('action'=>'downloadfile', 'id'=>$podcast['id']));
            $rssFeed->addItem(htmlentities($podcast['title']), $link, htmlentities($podcast['description']), $podcast['datecreated'], $artist, 'audio/mpeg', $podcast['filesize'], $podcast['playtime']);
        }
        
        $this->setVarByRef('feed', $rssFeed->show());
        
        $this->setPageTemplate(NULL);
        $this->setLayoutTemplate(NULL);
        
        return 'tbl_podcastfeed.php';
    }
    
    /**
     * Method to list to a podcast online
     *
     * @param string $id Record Id of the podcast
     */
    public function playPodcast($id)
    {
        $podcast = $this->objPodcast->getPodcast($id);
        
        $objFile = $this->getObject('dbfile', 'filemanager');
        
        if ($podcast == FALSE) {
            $this->setVar('content', '&nbsp;');
            $this->appendArrayVar('bodyOnLoad', 'window.close();');
        } else {
            $objSoundPlayer = $this->getObject('buildsoundplayer', 'files');
            $soundFile = str_replace('&', '&amp;', $this->objConfig->getsiteRoot().$objFile->getFilePath($podcast['fileid']));
            $soundFile = str_replace(' ', '%20', $soundFile);
            $objSoundPlayer->setSoundFile($soundFile);
            $this->setVarByRef('content', $objSoundPlayer->show());
            $this->setVar('bodyParams', ' class="popupwindow"');
        }
        
        $this->setVar('pageSuppressContainer', TRUE);
        $this->setVar('suppressFooter', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setLayoutTemplate(NULL);
        
        return 'tpl_listenonline.php';
    }
    
    /**
     * Method to listen to a podcast online
     *
     * @param string $id Record Id of the podcast
     */
    public function viewPodcast($id)
    {
        $podcast = $this->objPodcast->getPodcast($id);
        
        $objFile = $this->getObject('dbfile', 'filemanager');
        
        if ($podcast == FALSE) {
            $this->setVar('content', '&nbsp;');
            $this->appendArrayVar('bodyOnLoad', 'window.close();');
        } else {
            $objSoundPlayer = $this->getObject('buildsoundplayer', 'files');
            $objSoundPlayer->setSoundFile(str_replace('&', '&amp;', $this->objConfig->getsiteRoot().$objFile->getFilePath($podcast['fileid'])));
            $this->setVarByRef('content', $objSoundPlayer->show());
        }
        
        $this->setVarByRef('podcast', $podcast);
        
        return 'tpl_viewpodcast.php';
    }
    
    /**
     * Method to show the podcasts of a particular course
     * @author Mohamed Yusuf
     * @param string $id course Id
     * @return string Template
     */
    public function showCoursePoadcasts($contextCode)
    {
        
        $podcasts = $this->objPodcast->getCoursePodcasts($contextCode);
        $this->setVar('podcasts', $podcasts);
        
        return 'tpl_listpodcastbycourse.php';
    }

    /**
     * Method to show the RSS Feed by course
     * @author Mohamed Yusuf
     * @param string $id Context Id of the Feed
     */
    public function showRssFeedByCourse($contextCode)
    {
        $rssFeed = $this->getObject('itunesrssgenerator');
        
        $podcasts = $this->objPodcast->getCoursePodcasts($contextCode);
        $rssFeed->title = $this->objPodcast->getCourseName($contextCode);
        $rssFeed->rssfeedlink = $this->uri(array('action'=>'rssfeedbycourse', 'contextcode'=>$contextCode));
        $rssFeed->description = $this->objLanguage->languageText('mod_podcast_latestpodcastsforcourse', 'podcast').' '.$this->objPodcast->getCourseName($contextCode);
        
        
        foreach ($podcasts as $podcast)
        {
            $link = $this->uri(array('action'=>'downloadfile', 'id'=>$podcast[0]['id']));
            $rssFeed->addItem(htmlentities($podcast[0]['title']), $link, htmlentities($podcast[0]['description']), $podcast[0]['datecreated'], $this->objUser->fullname($podcast[0]['creatorid']), 'audio/mpeg', $podcast[0]['filesize'], $podcast[0]['playtime']);
        }
        
        $this->setVarByRef('feed', $rssFeed->show());
        
        $this->setPageTemplate(NULL);
        $this->setLayoutTemplate(NULL);
        
        return 'tbl_podcastfeed.php';
    }
    
    function uploadPodcast()
    {
        $objFileUpload = $this->getObject('uploadinput', 'filemanager');
        $objFileUpload->enableOverwriteIncrement = TRUE;
        $results = $objFileUpload->handleUpload('fileupload');
        
        // Technically, FALSE can never be returned, this is just a precaution
        // FALSE means there is no fileinput with that name
        if ($results == FALSE) {
            return $this->nextAction('addpodcast');
        } else {
            // If successfully Uploaded
            if ($results['success']) {
                
                // add to db as podcast
                $podcastResult = $this->objPodcast->addPodcast($results['fileid']);
                
                // check result of adding as podcast
                if ($podcastResult == 'nofile') {
                    return $this->nextAction(NULL, array('error'=>'podcastcouldnotbeadded'));
                } else if ($podcastResult == 'fileusedalready') {
                    return $this->nextAction('confirmadd', array('fileid'=>$results['fileid']));
                } else {
                    return $this->nextAction('confirmadd', array('id'=>$podcastResult));
                }
                
            } else {
                // If not successfully uploaded
                return $this->nextAction('addpodcast', array('error'=>$results['reason']));
            }
        }
    }
}
?>
