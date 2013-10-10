<?php
/**
 * Class to Control Functionality around tbl_podcast for Podcast Module
 * @author Tohir Solomons
 */
class dbpodcast extends dbTable 
{
    
    /**
     * Constructor
     *
     */
    public function init()
    {
        parent::init('tbl_podcast');
        $this->objFile =& $this->getObject('dbfile', 'filemanager');
        $this->objUser =& $this->getObject('user', 'security');
        $this->dbContext =& $this->getObject('dbcontext','context');
    }
    
    /**
     * Method to add a podcast
     *
     * @param string $fileId Record Id of the File from File Manager
     * @return string Result, either being last insert id, or flag for error.
     */
    public function addPodcast($fileId, $userId = NULL, $title = NULL)
    {
    	if($userId == NULL)
    	{
    		$userId = $this->objUser->userId();
    	}
        $file = $this->objFile->getFileInfo($fileId);
        if ($file == FALSE) {
            return 'nofile';
        }
        
        
        if ($this->podcastUsedAlready($userId, $fileId) > 0) {
            return 'fileusedalready';
        }
        
        $podcastInfo = array();
        if($title == NULL)
        {
            $title = '[- No Title -]';
        }
        $podcastInfo['title'] = $title; // isset($file['title']) ? $title : '[- No Title -]';
        $podcastInfo['description'] = isset($file['description']) ? $file['description'] : '[- No Description -]';

        $podcastInfo['fileid'] = $fileId;
        $podcastInfo['creatorid'] = $userId;
        $podcastInfo['datecreated'] = strftime('%Y-%m-%d %H:%M:%S', mktime());
        
        $podcastId = $this->insert($podcastInfo);
        
        $objFileRegister =& $this->getObject('registerfileusage', 'filemanager');
        $objFileRegister->registerUse($fileId, 'podcast', 'tbl_podcast', $podcastId, 'fileid', '', '', FALSE, $userId);
        
        // Add to Search
        $objIndexData = $this->getObject('indexdata', 'search');
        
        // Prep Data
        $docId = 'podcast_entry_'.$podcastId;
        $docDate = $podcastInfo['datecreated'];
        //$this->objUser->getUserId('id', $id);
        //$this->objPodcast->getPodcast('podcast', $podcast);
        //$this->setVar('id', $id);
        //$this->setVar('podcast', $podcast);
        $url = $this->uri(array('action'=>'viewpodcast'), 'podcast');
        $title = $podcastInfo['title'];
        $contents = $podcastInfo['title'].' '.$podcastInfo['description'];
        $teaser = $podcastInfo['description'];
        $module = 'podcast';
        $userId = $podcastInfo['creatorid'];
        
        // Add to Index
        $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId);
        
        $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
        
        $objLanguage= $this->getObject('language','language');
        $title = $objLanguage->languageText('mod_podcast_latestpodcastsby', 'podcast', 'Latest Podcasts By').' '.$this->objUser->fullName($userId);
        
        $objDynamicBlocks->addBlock('podcast', 'podcastdynamicblock', 'showBlock', $userId, $title, 'site', NULL, 'small', $userId);
        $objDynamicBlocks->addBlock('podcast', 'podcastdynamicblock', 'showBlock', $userId, $title, 'user', NULL, 'small', $userId);
        
        return $podcastId;
    }
    
    /**
     * Method to update a podcast
     *
     * @param string $id Record Id of the Podcast
     * @param string $title Title of Podcast
     * @param string $description Description of Podcast
     * @return boolean result of update
     */
    public function updatePodcast ($id, $title, $description)
    {
        $result = $this->update('id', $id, 
            array(
                'title' => $title, 
                'description' => $description,
                'modifierid' => $this->objUser->userId(),
                'datemodified' => strftime('%Y-%m-%d %H:%M:%S', mktime())
            ));
        
        $podcast = $this->getRow('id', $id);
        
        // Add to Search
        $objIndexData = $this->getObject('indexdata', 'search');
        
        // Prep Data
        $docId = 'podcast_entry_'.$id;
        $docDate = strftime('%Y-%m-%d %H:%M:%S', mktime());
        $url = $this->uri(array('action'=>'viewpodcast', 'id'=>$id), 'podcast');
        $title = $title;
        $contents = $title.' '.$description;
        $teaser = $description;
        $module = 'podcast';
        $userId = $podcast['creatorid'];
        
        // Add to Index
        $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId);
        
        return $result;
    }
    
    /**
     * Method to get the last 5 podcasts
     *
     * @return array
     */
    public function getLast5()
    {
        $sql = 'SELECT tbl_podcast.*, artist, filename, playtime, filesize, license, path FROM tbl_podcast 
        INNER JOIN tbl_files ON (tbl_podcast.fileid = tbl_files.id)
        LEFT JOIN tbl_files_metadata_media ON (tbl_podcast.fileid = tbl_files_metadata_media.fileid)
        ORDER BY tbl_podcast.datecreated DESC LIMIT 5';
        
        return $this->getArray($sql);
    }
    
    /**
     * Method to get the last podcast
     *
     * @return array
     */
    public function getLastPodcast()
    {
        $sql = 'SELECT tbl_podcast.*, artist, filename, playtime, filesize, license FROM tbl_podcast 
        INNER JOIN tbl_files ON (tbl_podcast.fileid = tbl_files.id)
        INNER JOIN tbl_files_metadata_media ON (tbl_podcast.fileid = tbl_files_metadata_media.fileid)
        ORDER BY tbl_podcast.datecreated DESC LIMIT 1';
        
        $results = $this->getArray($sql);
        
        if (count($results)==0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }
    
    /**
     * Method to get the podcasts by a particular user
     *
     * @param string $userId User Id of the User
     * @return array
     */
    public function getUserPodcasts($userId)
    {
        //return $this->getAll('ORDER BY datecreated LIMIT 10');
        $sql = 'SELECT tbl_podcast.*, artist, filename, playtime, filesize, license, path FROM tbl_podcast 
        INNER JOIN tbl_files ON (tbl_podcast.fileid = tbl_files.id)
        LEFT JOIN tbl_files_metadata_media ON (tbl_podcast.fileid = tbl_files_metadata_media.fileid)
        WHERE tbl_podcast.creatorid = \''.$userId.'\'
        ORDER BY tbl_podcast.datecreated DESC LIMIT 5';
        
        return $this->getArray($sql);
    }
    
    /**
     * Method to get ALL the podcasts by a particular user
     *
     * @param string $userId User Id of the User
     * @return array
     */
    public function getAllUserPodcasts($userId)
    {
        //return $this->getAll('ORDER BY datecreated LIMIT 10');
        $sql = 'SELECT tbl_podcast.*, artist, filename, playtime, filesize, license, path FROM tbl_podcast 
        INNER JOIN tbl_files ON (tbl_podcast.fileid = tbl_files.id)
        LEFT JOIN tbl_files_metadata_media ON (tbl_podcast.fileid = tbl_files_metadata_media.fileid)
        WHERE tbl_podcast.creatorid = \''.$userId.'\'
        ORDER BY tbl_podcast.datecreated DESC';
        
        return $this->getArray($sql);
    }
    
    /**
     * Method to delete a podcast
     *
     * @param string $id Record Id of Podcast
     * @param string $user User deleting podcast
     * @return string Flag indicating Result
     */
    public function deletePodcast($id, $user)
    {
        $podcast = $this->getRow('id', $id);
        
        $canDelete = FALSE;
        
        if ($podcast['creatorid'] == $user) {
            $canDelete = TRUE;
        }
        
        if ($this->objUser->isAdmin()) {
            $canDelete = TRUE;
        }
        
        if ($podcast == FALSE) {
            return 'norecord';
        } else if (!$canDelete) {
            return 'deleteothers';
        } else {
            $this->delete('id', $id);
            $this->delete('podcastId',$id, "tbl_podcast_context");
            
            $objIndexData = $this->getObject('indexdata', 'search');
            $objIndexData->removeIndex('podcast_entry_'.$id);
            
            $numPodcasts = $this->getNumFeeds($podcast['creatorid']);
            
            if ($numPodcasts == 0) {
                $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
                
                $objDynamicBlocks->removeBlock('podcast', 'podcastdynamicblock', 'showBlock', $podcast['creatorid'], 'site');
                $objDynamicBlocks->removeBlock('podcast', 'podcastdynamicblock', 'showBlock', $podcast['creatorid'], 'user');
            }
            return 'podcastdeleted';
        }
    }
    
    /**
     * Method to get a single podcast
     *
     * @param string $id Record Id of Podcast
     * @return array
     */
    public function getPodcast($id)
    {
         $sql = 'SELECT tbl_podcast.*, artist, filename, playtime, filesize, license, path, tbl_files_metadata_media.title as metatitle
        FROM tbl_podcast 
        INNER JOIN tbl_files ON (tbl_podcast.fileid = tbl_files.id)
        INNER JOIN tbl_files_metadata_media ON (tbl_podcast.fileid = tbl_files_metadata_media.fileid)
        WHERE tbl_podcast.id = \''.$id.'\' LIMIT 1';
        
        $results = $this->getArray($sql);
        
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }
    
    /**
     * Method to list all the podcasts
     *
     * @return array
     */
    public function listPodcasters()
    {
        $sql = 'SELECT DISTINCT tbl_users.id, userid, username, firstname, surname FROM tbl_podcast 
        INNER JOIN tbl_users ON (tbl_podcast.creatorid = tbl_users.userid)
        ORDER BY firstname, surname ';
        
        return $this->getArray($sql);
    }
    
    /**
     * Method to get the number of podcasts (optionally by user)
     *
     * @param string $userId User Id
     * @return int
     */
    public function getNumFeeds($userId= '')
    {
        if ($userId == '') {
            $where = '';
        } else {
            $where = ' WHERE creatorId=\''.$userId.'\'';
        }
        return $this->getRecordCount($where);
    }
    
    /**
     * Method to determine whether a podcast has been used already
     *
     * @param string $userId
     * @param string $fileId
     * @return int
     */
    public function podcastUsedAlready($userId, $fileId)
    {
        return $this->getRecordCount(' WHERE creatorid=\''.$userId.'\' AND fileid=\''.$fileId.'\'');
    }
    
    /**
     * Method to get a record by providing the fileid, not record id
     *
     * @param string $fileId File Id as per File Manager
     * @param string $userId User Id
     * @return array|false
     */
    public function getPodcastByFileId($fileId, $userId)
    {
        $result = $this->getAll(' WHERE creatorid=\''.$userId.'\' AND fileid=\''.$fileId.'\' LIMIT 1');
        
        if (count($result) == 0) {
            return FALSE;
        } else {
            return $this->getPodcast($result[0]['id']);
        }
    }
     
     
    
            
    /**
     *	Method to add podcast to course
     *	@param array $courses
     *	@param string $id
     *	@author Mohamed Yusuf 
     *	@date	2007-02-13
     */
    public function addPodcastContext($courses, $id){
        $contextDetail = array();
        $contextDetail['podcastId'] = $id;
        if(!empty($courses)){
            foreach($courses as $course)
            {
                $contextDetail['contextcode'] = $course;
                $this->insert($contextDetail, 'tbl_podcast_context');
            }
        }
    }
     
    /**
     *	Method to get course id
     *	@param string $podcastid
     *	@author Mohamed Yusuf 
     *	@return array
     *	@date	2007-02-13
     */
    public function getContextCode($podcastId)
    {
        $sql = "SELECT contextcode from tbl_podcast_context where podcastid=\"$podcastId\"";
        return $this->getArray($sql);
    }
     
    /**
     *	Method to get list of course of particular podcast
     *	@param string $contextcode
     *	@author Mohamed Yusuf 
     *	@return array
     *	@date	2007-02-13
     */
    public function getPodcastContext($contextcode){
        $sql = "SELECT * from tbl_context where contextcode=\"$contextcode\"";
        return $this->getArray($sql);
    }
     
    /**
     *	Method to delete podcast courses
     *	@author Mohamed Yusuf 
     *	void
     *	@date	2007-02-13
     */
    public function deletePodcastContext($id)
    {
        $this->delete('podcastId',$id, "tbl_podcast_context");
    }

    /**
     * Method to list all the podcasts of a context
     * @author Mohamed Yusuf
     * @return array
     */
    public function listPodcastCourses()
    {
        $sql = "SELECT id FROM tbl_podcast";
        $tempListCourses = array();
        $listCourses = array();
        $listContext = array();
        $tempListCourses = $this->getArray($sql);
        
        foreach ($tempListCourses as $key => $courses)
        {
            $podcastId = $courses['id'];
            $sql = "SELECT contextcode FROM tbl_podcast_context where podcastId=\"$podcastId\"";
            $listContext = $this->getArray($sql);
            
            foreach ($listContext as $context => $listC)
            {
                $listCourses[] = $this->dbContext->getContextDetails($listC['contextcode']);
            }
        }
        return $listCourses;
    }

    /**
     * Method to get the podcasts by a particular course
     * @author Mohamed Yusuf
     * @param string $contextCode contextcode of the podcast
     * @return array
     */
    public function getCoursePodcasts($contextCode)
    {
        $sql = "SELECT podcastid from tbl_podcast_context where contextcode=\"$contextCode\"";
        $contextId = array();
        $contextId = $this->query($sql);
        $podcasts = array();
        foreach ($contextId as $id)
        {

            $sql = 'SELECT tbl_podcast.*, artist, filename, playtime, filesize, license, path FROM tbl_podcast 
            INNER JOIN tbl_files ON (tbl_podcast.fileid = tbl_files.id)
            LEFT JOIN tbl_files_metadata_media ON (tbl_podcast.fileid = tbl_files_metadata_media.fileid)
            WHERE tbl_podcast.id = \''.$id['podcastid'].'\'
            ORDER BY tbl_podcast.datecreated DESC LIMIT 5';
            $podcasts[] = $this->getArray($sql);
        }
        
        return $podcasts;
    }

    /**
     * Method to get number of feeds by course
     * @author Mohamed Yusuf
     * @param string $contextCode contextcode
     * @return string id
     */
    public function getNumFeedsByCourse($contextCode)
    {
        $sql = "SELECT count(contextcode) as count from tbl_podcast_context where contextcode=\"$contextCode\"";
        $value = $this->getArray($sql);
        return $value[0]['count'];
    }

    /**
     * Method to get name of the context
     * @author Mohamed Yusuf
     * @param string $contextCode contextcode
     * @return string name
     */
    public function getCourseName($contextCode)
    {
        return $this->dbContext->getTitle($contextCode);
    }
    
    /**
     * Method to get name of the context
     * @author Mohamed Yusuf
     * @param string $podcastId podcastid
     * @return string rray 
     */
    public function listOfContextCode($podcastId)
    {
        $sql = "SELECT contextcode FROM tbl_podcast_context where podcastId=\"$podcastId\"";
        return $this->getArray($sql);	
    
    }

}


?>