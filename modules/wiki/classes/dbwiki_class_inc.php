<?php
/* ------ data class extends dbTable for all wiki version 2 database tables ------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the site permissions database tables
* @author Kevin Cyster
*/

class dbwiki extends dbTable
{
    /**
    * @var object $objLanguage: The language class in the language module
    * @access private
    */
    private $objLanguage;
    /**
    * @var object $objUser: The user class in the security module
    * @access private
    */
    private $objUser;

    /**
    * @var string $userId: The user id of the current user
    * @access private
    */
    private $userId;

    /**
    * @var string $wikiId: The id of the wiki the user is currently browsing
    * @access public
    */
    public $wikiId;

    /**
    * @var object $objContext: The dbcontext class in the context module
    * @access public
    */
    public $objContext;

    /**
    * @var object $objGroups: The managegroups class in the contextgroups module
    * @access public
    */
    public $objGroups;

    /**
    * @var object $objModules: The modules class in the modulecatalogue module
    * @access public
    */
    public $objModules;

    /**
    * @var bool $contextExists: TRUE if context is registered|FALSE if not
    * @access public
    */
    public $contextExists;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->wikiId = $this->getSession('wiki_id');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $this->contextExists = $this->objModules->checkIfRegistered('context');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objGroups = $this->getObject('managegroups', 'contextgroups');
    }

/* ----- Functions for changeing tables ----- */

    /**
    * Method to dynamically switch tables
    *
    * @access private
    * @param string $table: The name of the table
    * @return boolean: TRUE on success FALSE on failure
    */
    private function _changeTable($table)
    {
        try{
            parent::init($table);
            return TRUE;
        }catch(customException $e){
            customException::cleanUp();
            return FALSE;
        }
    }

    /**
    * Method to set the wiki table
    *
    * @access private
    * @return boolean: TRUE on success FALSE on failure
    */
    private function _setWiki()
    {
        return $this->_changeTable('tbl_wiki_wikis');
    }

    /**
    * Method to set the pages table
    *
    * @access private
    * @return boolean: TRUE on success FALSE on failure
    */
    private function _setPages()
    {
        return $this->_changeTable('tbl_wiki_pages');
    }

    /**
    * Method to set the rating table
    *
    * @access private
    * @return boolean: TRUE on success FALSE on failure
    */
    private function _setRating()
    {
        return $this->_changeTable('tbl_wiki_rating');
    }

    /**
    * Method to set the watch table
    *
    * @access private
    * @return boolean: TRUE on success FALSE on failure
    */
    private function _setWatch()
    {
        return $this->_changeTable('tbl_wiki_watch');
    }

    /**
    * Method to set the links table
    *
    * @access private
    * @return boolean: TRUE on success FALSE on failure
    */
    private function _setLinks()
    {
        return $this->_changeTable('tbl_wiki_links');
    }

    /**
    * Method to set the forum table
    *
    * @access private
    * @return boolean: TRUE on success FALSE on failure
    */
    private function _setForum()
    {
        return $this->_changeTable('tbl_wiki_forum');
    }

/* ----- Functions for tbl_wiki_wikis ----- */

    /**
    * Method to check if a user has a wiki
    *
    * @access public
    * @param string $userId: The user id of the user to check
    * @return bool $hasWiki: TRUE if the user has a wiki | FALSE if not
    */
    public function hasPersonalWiki($userId)
    {
        $this->_setWiki();
        $sql = "WHERE group_type = 'personal'";
        $sql .= " AND group_id = '".$userId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
    * Method to add a wiki
    *
    * @access public
    * @param string $name: The wiki name
    * @param string $desc: The wiki description
    * @param integer $visibility: THe visibility of the wiki
    * @return string|bool $wikiId: The wiki id |False on failure
    */
    public function addWiki($name, $desc, $visibility)
    {
        $this->_setWiki();
        $fields = array();
        $fields['group_type'] = 'personal';
        $fields['group_id'] = $this->userId;
        $fields['wiki_name'] = $name;
        $fields['wiki_description'] = $desc;
        $fields['wiki_visibility'] = $visibility;
        $fields['creator_id'] = $this->userId;
        $fields['date_created'] = date('Y-m-d H:i:s');
        $wikiId = $this->insert($fields);
        $this->setSession('wiki_id', $wikiId);
        $this->wikiId = $wikiId;
        $this->personalMainPage();
        return $wikiId;
    }

    /**
    * Method to check if a context has a wiki
    *
    * @access public
    * @param string $contextCode: The contextCode of the context to check
    * @return bool $hasWiki: TRUE if the context has a wiki | FALSE if not
    */
    public function hasContextWiki($contextCode)
    {
        $this->_setWiki();
        $sql = "WHERE group_type = 'context'";
        $sql .= " AND group_id = '".$contextCode."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
    * Method to get the context wiki
    *
    * @access public
    * @param string $contextCode: The context to get the wiki of
    * @return string $wikiId: The id of the context wiki
    */
    public function getContextWiki($contextCode)
    {
        $this->_setWiki();
        $sql = "WHERE group_type = 'context'";
        $sql .= " AND group_id = '".$contextCode."'";
        $data = $this->getAll($sql);
        if(empty($data)){
            $wikiId = $this->createContextWiki($contextCode);
        }else{
            $wikiId = $data[0]['id'];
        }
        return $wikiId;
    }

    /**
    * Method to auto create a context wiki
    *
    * @access public
    * @param string $contextCode: The context for the wiki
    * @return string|bool $wikiId: The id of the wiki for the context
    */
    public function createContextWiki($contextCode)
    {
        $data = $this->objContext->getContextDetails($contextCode);
        $array = array(
            'name' => $data['title'],
        );
        $descLabel = $this->objLanguage->code2Txt('mod_wiki_contextdesc', 'wiki', $array);
        $wikiLabel = $this->objLanguage->languageText('mod_wiki_name', 'wiki');

        switch($data['access']){
            case 'Public':
                $visibility = 1;
                break;
            case 'Open':
                $visibility = 2;
                break;
            case 'Private':
                $visibility = 3;
                break;
            default:
                $visibility = 1;
                break;
        }

        $this->_setWiki();
        $fields = array();
        $fields['group_type'] = 'context';
        $fields['group_id'] = $contextCode;
        $fields['wiki_name'] = ucfirst(strtolower($data['title'].' '.$wikiLabel));
        $fields['wiki_description'] = $descLabel;
        $fields['wiki_visibility'] = $visibility;
        $fields['creator_id'] = $this->userId;
        $fields['date_created'] = date('Y-m-d H:i:s');
        $wikiId = $this->insert($fields);
        $this->setSession('wiki_id', $wikiId);
        $this->wikiId = $wikiId;
        $this->contextMainPage();
        return $wikiId;
    }

    /**
    * Method to get the public wikis
    * but this function is called also by blocks..so check if in context or not.
    * If in context, then those
    * @access public
    * @return array|bool $data: The wiki data on success|FALSE on failure
    */
    public function getPublicWikis()
    {
        $this->_setWiki();
        $sql = " WHERE wiki_visibility= '1'";
        //are we in a context? if so, get hold of the context wikis too
        if($this->objUser->isLoggedIn()){
            $contextCode=$this->objContext->getContextCode();
            $sql .= " OR group_id = '".$contextCode."'";
        }
      
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get a wikis
    *
    * @access public
    * @param string $wikiId
    * @return array|bool $data: The wiki data on success|FALSE on failure
    */
    public function getWiki($wikiId)
    {
        $this->_setWiki();
        $sql = " WHERE id = '".$wikiId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to update the wiki visibility
    *
    * @access public
    * @param string $id: The id of the wiki to update
    * @param string $visibility: The wiki visibility status
    * @return string|bool $wikiId: The id of the wiki on SUCCESS| FALSE on failure
    */
    public function editWiki($id, $visibility)
    {
        $this->_setWiki();
        $fields = array();
        $fields['wiki_visibility'] = $visibility;
        $fields['date_created'] = date('Y-m-d H:i:s');
        $this->update('id', $id, $fields);
        //print_r($fields);
        //die();
        return $id;
    }

    /**
    * Method to get the open wikis
    *
    * @access public
    * @return array|bool $data: The wiki data on success|FALSE on failure
    */
    public function getUserWikis()
    {
        if($this->contextExists){
            $contexts = $this->objGroups->usercontextcodes($this->userId);
        }
        $this->_setWiki();
        $sql = " WHERE wiki_visibility < '3'";
        $sql .= " OR (group_type = 'personal' AND group_id = '".$this->userId."')";
        if($this->contextExists && !empty($contexts)){
            foreach($contexts as $context){
                $sql .= " OR (group_type = 'context' AND group_id = '".$context."')";
            }
        }
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

/* ----- Functions for tbl_wiki_pages ----- */

    /**
    * Method to get a wiki page
    * @access public
    * @param string $name: The name of the wiki
    * @param string $pageName: The name of the wiki page
    * @return array|bool $data: The wiki page data on success|FALSE on failure
    */
    public function getWikiPage($name, $pageName)
    {
        $this->_setPages();
        $sql = "SELECT * FROM tbl_wiki_pages AS pages";
        $sql .= " LEFT JOIN tbl_wiki_wikis AS wikis";
        $sql .= " ON pages.wiki_id = wikis.id";
        $sql .= " WHERE wikis.wiki_name = '".$name."'";
        $sql .= " AND pages.page_name = '".$pageName."'";
        $sql .= " AND pages.page_status < 4";
        $sql .= " ORDER BY page_version DESC";
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }


    /**
    * Method to auto create the main page after creating a personal wiki
    *
    * @access public
    * @param string $wikiId: The id of the wiki the main page is being created for
    * @return string|bool $pageId: The wiki page id on success|FALSE on failure
    */
    public function personalMainPage()
    {
        // get text elements
        $text1Label = $this->objLanguage->languageText('mod_wiki_default_4', 'wiki');
        $text2Label = $this->objLanguage->languageText('mod_wiki_default_3', 'wiki');
        $text3Label = $this->objLanguage->languageText('mod_wiki_default_2', 'wiki');
        $text4Label = $this->objLanguage->languageText('mod_wiki_newpage', 'wiki');
        $this->_setPages();
        $fields = array();
        $fields['page_name'] = 'MainPage';
        $fields['page_summary'] = $text1Label;
        $fields['page_content'] = $text2Label."\n".$text3Label;
        $fields['version_comment'] = $text4Label;
        $fields['main_page'] = 1;

        $this->addPage($fields);
    }

    /**
    * Method to auto create the main page after creating a context wiki
    *
    * @access public
    * @param string $wikiId: The id of the wiki the main page is being created for
    * @return string|bool $pageId: The wiki page id on success|FALSE on failure
    */
    public function contextMainPage()
    {
        // get text elements
        $text1Label = $this->objLanguage->code2Txt('mod_wiki_default_1', 'wiki');
        $text3Label = $this->objLanguage->languageText('mod_wiki_default_2', 'wiki');
        $text4Label = $this->objLanguage->languageText('mod_wiki_newpage', 'wiki');
        $this->_setPages();
        $fields = array();
        $fields['page_name'] = 'MainPage';
        $fields['page_summary'] = $text1Label;
        $fields['page_content'] = "* ".$text1Label."\n".$text3Label;
        $fields['version_comment'] = $text4Label;
        $fields['main_page'] = 1;

        $this->addPage($fields);
    }

    /**
    * Method to add a wiki page
    *
    * @access public
    * @param array $data: The array of wiki page data
    * @return string|bool $pageId: The wiki page id |False on failure
    **/
    public function addPage($data)
    {
        if(!is_array($data) || empty($data)){
            return FALSE;
        }

        $data['wiki_id'] = $this->wikiId;
        $data['page_version'] = $this->getVersion($data['page_name']);
        $data['page_status'] = 1;
        $data['page_author_id'] = $this->userId;
        $data['date_created'] = date('Y-m-d H:i:s');
        $pageId = $this->insert($data);
        return $pageId;
    }

    /**
    * Method to get all latest (current version) wiki pages
    *
    * @access public
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getAllCurrentPages()
    {
        $this->_setPages();
        $sql = "WHERE (page_name, page_version)";
        $sql .= " IN (SELECT page_name, MAX(page_version)";
        $sql .= "     FROM tbl_wiki_pages GROUP BY page_name)";
        $sql .= " AND wiki_id = '".$this->wikiId."'";
        $sql .= " AND page_status < 4";
        $sql .= " ORDER BY date_created DESC";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get all wiki pages
    *
    * @access public
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getAllPages()
    {
        $this->_setPages();
        $sql = "WHERE (page_name, page_version)";
        $sql .= " IN (SELECT page_name, MAX(page_version)";
        $sql .= "     FROM tbl_wiki_pages GROUP BY page_name)";
        if(isset($this->wikiId))
        {
            $sql .= " AND wiki_id = '".$this->wikiId."'";
        }
        $sql .= " ORDER BY date_created DESC";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get all wiki pages by name
    *
    * @access public
    * @param string $name: The wiki page name
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getPagesByName($name)
    {
        $this->_setPages();
        $sql = "WHERE page_name = '".$name."'";
        if(isset($this->wikiId))
        {
            $sql .= " AND wiki_id = '".$this->wikiId."'";
        }
        $sql .= " ORDER BY page_version DESC";
        log_debug($sql);
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get a wiki page by name
    *
    * @access public
    * @param string $name: The wiki page name
    * @param integer $version: The wiki page version
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getPage($name, $version = NULL)
    {
        $this->_setPages();
        $sql = "WHERE page_name = '".$name."'";
        if(!empty($version)){
            $sql .= " AND page_version = '".$version."'";
        }
        if(isset($this->wikiId))
        {
            $sql .= " AND wiki_id = '".$this->wikiId."'";
        }
        $sql .= " ORDER BY page_version DESC";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to get a wiki page by id
    *
    * @access public
    * @param string $id: The wiki page id
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getPageById($id)
    {
        $this->_setPages();
        $sql = "WHERE id = '".$id."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to get a main page
    *
    * @access public
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getMainPage()
    {
        $this->_setPages();
        $sql = "WHERE main_page = '1'";
        $sql .= " AND wiki_id = '".$this->wikiId."'";
        $sql .= " ORDER BY page_version DESC";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to get recently added wiki pages
    *
    * @access public
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getRecentlyAdded()
    {
        $this->_setPages();
        $sql = "WHERE page_version = 1";
        $sql .= " AND page_status < 5";
        $sql .= " AND wiki_id = '".$this->wikiId."'";
        $sql .= " ORDER BY date_created DESC LIMIT 5";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get recently updated wiki pages
    *
    * @access public
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getRecentlyUpdated()
    {
        $this->_setPages();
        $sql = "WHERE (page_name, page_version)";
        $sql .= " IN (SELECT page_name, MAX(page_version)";
        $sql .= "     FROM tbl_wiki_pages GROUP BY page_name)";
        $sql .= " AND page_version > 1";
        $sql .= " AND page_status < 5";
        if(isset($this->wikiId))
        {
            $sql .= " AND wiki_id = '".$this->wikiId."'";
        }
        $sql .= " ORDER BY date_created DESC LIMIT 5";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get recently updated wiki pages
    *
    * @access public
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getRecentlyUpdatedAPI($date)
    {
        $this->_setPages();
        $sql = "WHERE (page_name, page_version)";
        $sql .= " IN (SELECT page_name, MAX(page_version)";
        $sql .= "     FROM tbl_wiki_pages GROUP BY page_name)";
        $sql .= " AND page_version > 1";
        $sql .= " AND page_status < 5";
        $sql .= " AND wiki_id = '".$this->wikiId."'";
        $sql .= " AND date_created > '$date'";
        //log_debug($sql);
        $data = $this->getAll($sql);
        return $data;
    }

    /**
    * Method to edit a field
    *
    * @access public
    * @param string $id: The wiki page to edit
    * @param string $field: The field to be edited
    * @param string $value: The new value of the field
    * @return void
    */
    public function editPageField($id, $field, $name)
    {
        $this->_setPages();
        $this->update('id', $id, array($field => $value));
    }

    /**
    * Method to mark a wiki page as deleted
    *
    * @access public
    * @param string $name: The name of the page
    * @return void
    */
    public function deletePage($name)
    {
        $this->_setPages();
        $data = $this->getPagesByName($name);
        foreach($data as $line){
            $this->update('id', $line['id'], array(
                'page_status' => 6,
                ));
        }
    }

    /**
    * Method to restore a wiki page
    *
    * @access public
    * @param string $name: The page name to restore
    * @param integer $version: The page version to restore
    * @return string $pageId: The id of the resored page
    */
    public function restorePage($name, $version)
    {
        $array = array(
            'num' => $version,
        );
        $restoreLabel = $this->objLanguage->code2Txt('mod_wiki_restoration', 'wiki', $array);

        $this->_setPages();
        $sql = "WHERE page_name = '".$name."'";
        $sql .= " AND page_version > '".$version."'";
        $sql .= " AND wiki_id = '".$this->wikiId."'";
        $overWrittenPages = $this->getAll($sql);
        if(!empty($overWrittenPages)){
            foreach($overWrittenPages as $page){
                $this->update('id', $page['id'], array(
                    'page_status' => 4,
                    ));
            }
        }

        $pageToRestore = $this->getPage($name, $version);
        $temp = array_pop($pageToRestore);
        $temp = array_shift($pageToRestore);
        $pageToRestore['wiki_id'] = $this->wikiId;
        $pageToRestore['page_version'] = $this->getVersion($name);
        $pageToRestore['version_comment'] = $restoreLabel;
        $pageToRestore['page_status'] = 2;
        $pageToRestore['page_author_id'] = $this->userId;
        $pageToRestore['date_created'] = date('Y-m-d H:i:s');
        $pageId = $this->insert($pageToRestore);

        return $pageId;
    }

    /**
    * Method to restore a wiki page
    *
    * @access public
    * @param string $name: The page name to restore
    * @param integer $version: The page version to restore
    * @return string $pageId: The id of the resored page
    */
    public function reinstatePage($name, $version)
    {
        $array = array(
            'num' => $version,
        );
        $reinstateLabel = $this->objLanguage->code2Txt('mod_wiki_reinstatement', 'wiki', $array);

        $this->_setPages();
        $sql = "WHERE page_name = '".$name."'";
        $sql .= " AND wiki_id = '".$this->wikiId."'";
        $archivedPages = $this->getAll($sql);
        if(!empty($archivedPages)){
            foreach($archivedPages as $page){
                $this->update('id', $page['id'], array(
                    'page_status' => 5,
                    ));
            }
        }

        $pageToReinstate = $this->getPage($name, $version);
        $temp = array_pop($pageToReinstate);
        $temp = array_shift($pageToReinstate);
        $pageToReinstate['wiki_id'] = $this->wikiId;
        $pageToReinstate['page_version'] = $this->getVersion($name);
        $pageToReinstate['version_comment'] = $reinstateLabel;
        $pageToReinstate['page_status'] = 3;
        $pageToReinstate['page_author_id'] = $this->userId;
        $pageToReinstate['date_created'] = date('Y-m-d H:i:s');
        $pageId = $this->insert($pageToReinstate);

        return $pageId;
    }

    /**
    * Method to get the page version for entry
    *
    * @access private
    * @param string $name: The name of the page
    * @return integer $version: The page version
    */
    private function getVersion($name)
    {
        $pages = $this->getPagesByName($name);
        if(!empty($pages)){
            $version = count($pages) + 1;
        }else{
            $version = 1;
        }
        return $version;
    }

    /**
    * Method to search the wiki
    *
    * @access public
    * @param string $column: The column(s) to search
    * @param string $value: The value to search for
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function searchWiki($column, $value)
    {
        $this->_setPages();
        $smashValue = str_replace(' ', '', ucwords($value));
        $sql = "WHERE (page_name, page_version)";
        $sql .= " IN (SELECT page_name, MAX(page_version)";
        $sql .= "     FROM tbl_wiki_pages GROUP BY page_name)";
        $sql .= " AND page_status < 4";
        if($column == 'both'){
            $sql .= " AND (page_name = '".$smashValue."'";
            $sql .= " OR page_content LIKE '%".$value."%')";
        }elseif($column == 'page_name'){
            $sql .= " AND ".$column." = '".$smashValue."'";
        }else{
            $sql .= " AND LOWER(".$column.") LIKE '%".strtolower($value)."%'";
        }
        $sql .= " AND wiki_id = '".$this->wikiId."'";
        $sql .= " ORDER BY date_created DESC";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get a list of all authors and their page count
    *
    * @access public
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getAuthors()
    {
        $this->_setPages();
        $sql = "SELECT *, COUNT(page_author_id) AS cnt FROM tbl_wiki_pages";
        $sql .= " WHERE wiki_id = '".$this->wikiId."'";
        $sql .= " GROUP BY page_author_id";
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get a list of articles by an author
    *
    * @access public
    * @param string $author: The id of the author
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getAuthorArticles($author)
    {
        $this->_setPages();
        $sql = "WHERE (page_name, page_version)";
        $sql .= " IN (SELECT page_name, MAX(page_version)";
        $sql .= "     FROM tbl_wiki_pages GROUP BY page_name)";
        $sql .= " AND page_status < 4";
        $sql .= " AND page_author_id = '".$author."'";
        $sql .= " AND wiki_id = '".$this->wikiId."'";
        $sql .= " ORDER BY date_created DESC";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

/* ----- Functions for tbl_wiki_rating ----- */

    /**
    * Method to add a wiki page rating
    *
    * @access public
    * @param string $wikiId: The id of the wiki the page is for
    * @param string $name: The wiki page name
    * @param integer $rating: The rating the page recieved
    * @return string|bool $ratingId: The wiki page rating id |False on failure
    */
    public function addRating($name, $rating)
    {
        $this->_setRating();
        $fields = array();
        $fields['wiki_id'] = $this->wikiId;
        $fields['page_name'] = $name;
        $fields['page_rating'] = $rating;
        $fields['creator_id'] = $this->userId;

        return $ratingId = $this->insert($fields);
    }

    /**
    * Method to get a wiki page rating
    *
    * @access public
    * @param string $wikiId: The id of the wiki the page is for
    * @param string $name: The wiki page name
    * @return array $array: The array of wiki page rating and votes
    */
    public function getRating($name)
    {
        $this->_setRating();
        $sql = "WHERE wiki_id = '".$this->wikiId."'";
        $sql .= " AND page_name = '".$name."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            $vote = 0;
            foreach($data as $line){
                $vote = $vote + $line['page_rating'];
            }
            $votes = count($data);
            if($vote > 0){
                $rating = ceil($vote / $votes);
            }else{
                $rating = 0;
            }
            $array = array(
                'rating' => $rating,
                'votes' => $votes,
            );
            return $array;
        }
        $array = array(
            'rating' => 0,
            'votes' => 0,
        );
        return $array;
    }

    /**
    * Method to check if a user has rated a page
    *
    * @access public
    * @param string $wikiId: The id of the wiki the page is for
    * @param string $name: The wiki page name
    * @return boolean $rated: TRUE if the user has rated the page | FALSE if not
    */
    public function wasRated($name)
    {
        $this->_setRating();
        $sql = "WHERE wiki_id = '".$this->wikiId."'";
        $sql .= " AND page_name = '".$name."'";
        $sql .= " AND creator_id = '".$this->userId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return TRUE;
        }
        return FALSE;
    }

    /**
    * Method to add a wiki page ranking
    *
    * @access public
    * @param string $wikiId: The id of the wiki the page is for
    * @param string $name: The wiki page name
    * @return float $ranking: The wiki page ranking
    */
    public function getRanking()
    {
        $this->_setRating();
        $sql = "SELECT *, COUNT(page_name) AS cnt, SUM(page_rating) AS tot";
        $sql .= " FROM tbl_wiki_rating ";
        $sql .= " WHERE wiki_id = '".$this->wikiId."'";
        $sql .= " GROUP BY page_name";
        $sql .= " ORDER BY tot DESC, cnt DESC";
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

/* ----- Functions for tbl_wiki_watch ----- */

    /**
    * Method to add a page to your watchlist
    *
    * @access public
    * @param string $wikiId: The id of the wiki the page falls under
    * @param string $name: The name of the wiki page
    * @return string|bool $watchId: The wiki page watch id |False on failure
    */
    public function addWatch($name)
    {
        $watch = $this->getUserPageWatch($name);
        if(!empty($watch)){
            $watchId = $watch['id'];
        }else{
            $this->_setWatch();
            $fields['wiki_id'] = $this->wikiId;
            $fields['page_name'] = $name;
            $fields['creator_id'] = $this->userId;

            $watchId = $this->insert($fields);
        }
        return $watchId;
    }

    /**
    * Method to get a page from your watchlist
    *
    * @access public
    * @param string $wikiId: The id of the wiki the page falls under
    * @param string $name: The name of the wiki page
    * @param string $userId: The id of the user with a page watch
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getUserPageWatch($name, $userId = NULL)
    {
        $userId = isset($userId) ? $userId : $this->userId;

        $this->_setWatch();
        $sql = "WHERE wiki_id = '".$this->wikiId."'";
        $sql .= " AND page_name = '".$name."'";
        $sql .= " AND creator_id = '".$userId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to delete a page from your watchlist
    *
    * @access public
    * @param string $id: The id of the watch to delete
    * @return void
    */
    public function deleteWatchById($id)
    {
        $this->_setWatch();
        $this->delete('id', $id);
    }

    /**
    * Method to delete a page from your watchlist
    *
    * @access public
    * @param string $name: The name of the watch to delete
    * @param string $userId: The id of the user who has a page watch
    * @return void
    */
    public function deleteWatchByName($name, $userId = NULL)
    {
        $userId = isset($userId) ? $userId : $this->userId;
        $watch = $this->getUserPageWatch($name, $userId);
        if(!empty($watch)){
            $this->_setWatch();
            $this->delete('id', $watch['id']);
        }
    }

    /**
    * Method to get pages you are watching
    *
    * @access public
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getAllUserWatches()
    {
        $this->_setWatch();
        $sql = "WHERE creator_id = '".$this->userId."'";
        $sql .= " AND wiki_id = '".$this->wikiId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get all watchers of a page
    *
    * @access public
    * @param string $name: The name of the page
    * @return array|bool $data: Wiki page data on success | False on failure
    */
    public function getPageWatches($name)
    {
        $this->_setWatch();
        $sql = "WHERE wiki_id = '".$this->wikiId."'";
        $sql .= " AND page_name = '".$name."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

/* ----- Functions for tbl_wiki_links ----- */

    /**
    * Method to add a interwiki link
    *
    * @access public
    * @param string $name: The name of the wiki site
    * @param string $link: The url of the wiki site
    * @return string|bool $linkId: The interwiki link id |False on failure
    */
    public function addLink($name, $link)
    {
        $this->_setLinks();

        $wiki = $this->getLinkByName($name);
        if(!empty($wiki)){
            $linkId = $wiki['id'];
        }else{
            $fields = array();
            $fields['wiki_name'] = $name;
            $fields['wiki_link'] = $link;
            $fields['creator_id'] = $this->userId;
            $linkId = $this->insert($fields);
        }
        return $linkId;
    }

    /**
    * Method to get a list interwiki links
    *
    * @access public
    * @return array|bool $data: Interwiki link data on success | False on failure
    */
    public function getLinks()
    {
        $this->_setLinks();
        $data = $this->getAll();
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get an interwiki link
    *
    * @access public
    * @param string $name: The name of the wiki site
    * @return array|bool $data: Interwiki link data on success | False on failure
    */
    public function getLinkByName($name)
    {
        $this->_setLinks();
        $sql = "WHERE wiki_name = '".$name."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to get an interwiki link
    *
    * @access public
    * @param string $id: The wiki link id
    * @return array|bool $data: Interwiki link data on success | False on failure
    */
    public function getLinkById($id)
    {
        $this->_setLinks();
        $sql = "WHERE id = '".$id."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to edit an interwiki link
    *
    * @access public
    * @param string $id: The wiki link id
    * @param string $name: The name wiki site
    * @param string $link: The url of the wiki site
    * @return void
    */
    public function editLink($id, $name, $link)
    {
        $this->_setLinks();
        $fields = array();
        $fields['wiki_name'] = $name;
        $fields['wiki_link'] = $link;
        $fields['creator_id'] = $this->userId;
        $this->update('id', $id, $fields);
    }

/* ----- Functions for tbl_wiki_forum ----- */

    /**
    * Method to add a wiki discussion post
    *
    * @access public
    * @param string $name: The wiki page name
    * @param string $title: The discussion post title
    * @param string $content: The discussion post text
    * @return string|bool $postId: The discussion post id|False on failure
    */
    public function addPost($name, $title, $content)
    {
        $this->_setForum();

        $data = $this->getPosts($name);
        if(!empty($data)){
            $count = count($data) + 1;
        }else{
            $count = 1;
        }

        $fields = array();
        $fields['wiki_id'] = $this->wikiId;
        $fields['page_name'] = $name;
        $fields['post_title'] = $title;
        $fields['post_content'] = $content;
        $fields['post_order'] = $count;
        $fields['post_status'] = '1';
        $fields['author_id'] = $this->userId;
        $fields['date_created'] = date('Y-m-d H:i:s');
        $postId = $this->insert($fields);

        return $postId;
    }

    /**
    * Method to get a list of wiki discussion posts
    *
    * @access public
    * @param string $name: The wiki page name
    * @return array|bool $data: Discussion post data on success | False on failure
    */
    public function getPosts($name)
    {
        $this->_setForum();
        $sql = "WHERE wiki_id = '".$this->wikiId."'";
        $sql .= " AND page_name = '".$name."'";
        $sql .= " ORDER BY post_order ASC";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to edit a wiki discussion post
    *
    * @access public
    * @param string $id: The wiki discussion post id
    * @param string $content: The discussion post text
    * @return void
    */
    public function editPost($id, $title, $content)
    {
        $this->_setForum();
        $fields = array();
        $fields['post_title'] = $title;
        $fields['post_content'] = $content;
        $fields['date_modified'] = date('Y-m-d H:i:s');
        $this->update('id', $id, $fields);
    }

    /**
    * Method to delete a wiki discussion post
    *
    * @access public
    * @param string $id: The wiki discussion post id
    * @return void
    */
    public function deletePost($id)
    {
        $this->_setForum();
        $fields = array();
        $fields['post_status'] = '2';
        $this->update('id', $id, $fields);
    }

    /**
    * Method to restore a wiki discussion post
    *
    * @access public
    * @param string $id: The wiki discussion post id
    * @return void
    */
    public function restorePost($id)
    {
        $this->_setForum();
        $fields = array();
        $fields['post_status'] = '1';
        $this->update('id', $id, $fields);
    }
}
?>