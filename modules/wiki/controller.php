<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}
// end security check
/**
 * The wiki version 2 controller manages
 * the wiki
 *
 * @author Kevin Cyster
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package wiki version 2
 */

class wiki extends controller {

    /**
    * @var object $objLanguage: The language class in the language module
    * @access public
    */
    public $objLangauge;

    /**
    * @var object $objWikidisplay: The display class in the wiki version 2 module
    * @access public
    */
    public $objWikidisplay;

    /**
    * @var object $objDbwiki: The dbwiki class in the wiki version 2 module
    * @access public
    */
    public $objDbwiki;

    /**
    * @var object $objLock: The wikipagelock class in the wiki version 2 module
    * @access public
    */
    public $objLock;

    /**
    * @var object $objUser: The user class in the security module
    * @access public
    */
    public $objUser;

    /**
    * @var string $userId: The user id of the current logged in user
    * @access public
    */
    public $userId;

    /**
    * @var object $objModules: The modules class in the modulecatalogue module
    * @access public
    */
    public $objModules;

    /**
    * Method to initialise the controller
    *
    * @access public
    * @return void
    */
    public function init()
    {
        $wiki = $this->getParam('wiki');
        if ($wiki) {
            $this->setSession('wiki_id', $wiki);
        }

        $this->objLanguage = $this->getObject( 'language', 'language' );
        $this->objWikidisplay = $this->newObject('wikidisplay', 'wiki');
        $this->objDbwiki = $this->newObject('dbwiki', 'wiki');
        $this->objLock = $this->newObject('wikipagelock', 'wiki');
        $this->objUser = $this->newObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        if($this->objModules->checkIfRegistered('activitystreamer'))
        {
                $this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
                $this->eventDispatcher->addObserver ( array ($this->objActivityStreamer, 'postmade' ) );
                $this->eventsEnabled = TRUE;
        } else {
                $this->eventsEnabled = FALSE;
        }
        $contextExists = $this->objModules->checkIfRegistered('context');
        $this->setSession('context_exists', $contextExists);
        $wikiId = $this->getSession('wiki_id');
        if(empty($wikiId)){
            $this->setSession('wiki_id', 'init_1');
        }
        // Load scriptaclous since we can no longer guarantee it is there
        $scriptaculous = $this->getObject('scriptaculous', 'prototype');
        $this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));
    }

    /**
    * Method to check if login needed (depending on operation)
    *
    * @access public
    * @return boolean True or False
    */
    public function requiresLogin()
    {
        $action = $this->getParam("action", NULL);
        switch($action){
            case 'add_page':
            case 'create_page':
            case 'update_page':
            case 'delete_page':
            case 'restore_page':
            case 'validate_name':
            case 'preview_page':
            case 'deleted_page':
            case 'check_lock':
            case 'view_watchlist':
            case 'delete_watch':
            case 'update_link':
            case 'update_post':
            case 'delete_post':
            case 'restore_post':
            case 'add_wiki';
            case 'create_wiki':
            case 'add_rating':
            case 'change_visibility':
            case 'context_wiki':
                return TRUE;
            //Allow viewing anonymously
            case 'view_rules':
            case 'view_all':
            case 'view_page':
            case 'search_wiki':
            case 'view_authors':
            case 'view_ranking':
            case 'remove_watch':
            case 'show_diff':
            case 'show_links':
            case 'select_wiki':
            default:
                return FALSE;
        }
    }

    /**
    * Method the engine uses to kickstart the module
    *
    * @access public
    * @param string $action: The action to be performed
    * @return void
    */
    function dispatch( $action )
    {
        switch($action){
            case 'view_rules':
                $templateContent = $this->objWikidisplay->showFormattingRules();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('popup', TRUE);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'add_page':
                $name = $this->getParam('name');
                $templateContent = $this->objWikidisplay->showAddPage($name);
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'create_page':
                $name = $this->getParam('name');
                $sum = $this->getParam('summary');
                $choice = $this->getParam('choice');
                $content = $this->getParam('wikiContent');
                if($choice == 'yes'){
                    $summary = substr($content, 0, 255);
                }else{
                    $summary = $sum;
                }
                $data = array();
                $data['page_name'] = strip_tags($name);
                $data['page_summary'] = strip_tags($summary);
                //$data['page_content'] = strip_tags($content, '<code>');
                $data['page_content'] = $content;
                $data['version_comment'] = $this->objLanguage->languageText('mod_wiki_newpage', 'wiki');
                $pageId = $this->objDbwiki->addPage($data);

                $watch = $this->getParam('watch');
                if(!empty($watch)){
                    $this->objDbwiki->addWatch($name);
                }
                return $this->nextAction('view_page', array(
                    'name' => $name,
                ), 'wiki');
                break;

            case 'preview_iframe':
                $name = $this->getParam('preview_name', NULL);
                $content = $this->getParam('preview_content', NULL);
                $templateContent = $this->objWikidisplay->submitIframe($name, $content);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('iframe', TRUE);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'update_page':
                $name = $this->getParam('name');
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                $main = $this->getParam('main');
                $sum = $this->getParam('summary');
                $comment = $this->getParam('comment');
                $content = $this->getParam('wikiContent');
                $choice = $this->getParam('choice');
                if($choice == 'yes'){
                    $summary = substr($content, 0, 255);
                }else{
                    $summary = $sum;
                }
                //add to activity log
                if($this->eventsEnabled)
                {
                //The message to be posted
                $message = $this->objUser->getsurname()." ". $this->objLanguage->languageText('mod_wiki_updatedwikialert', 'wiki')."-".$name;
                $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $message,
                    'link'=> $this->uri(array()),
                    'contextcode' => $this->objContext->getContextCode(),
                    'author' => $this->objUser->fullname(),
                    'description'=>$message));

                $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $message,
                    'link'=> $this->uri(array()),
                    'contextcode' => null,
                    'author' => $this->objUser->fullname(),
                    'description'=>$message));
						         	}
                $data = array();
                $data['page_name'] = strip_tags($name);
                $data['main_page'] = $main;
                $data['page_summary'] = strip_tags($summary);
                $data['version_comment'] = strip_tags($comment);
                //$data['page_content'] = strip_tags($content, '<code>');
                // removed stripping of tags
                $data['page_content'] = $content;
                $pageId = $this->objDbwiki->addPage($data);
                $this->objWikidisplay->sendMail($name);
                $this->objLock->unlockEdit('tbl_wiki_pages', $id, $this->userId);
                return $this->nextAction('view_page', array(
                    'name' => $name,
                ), 'wiki');
                break;

            case 'delete_page':
                $name = $this->getParam('name');
                $this->objDbwiki->deletePage($name);
                return $this->nextAction('view_all', array(), 'wiki');
                break;

            case 'view_all':
                $templateContent = $this->objWikidisplay->showAllPages();
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'view_page':
                // Let it be the context wiki if in context
                if ($this->objContext->isInContext()) {
                    $contextCode =  $this->objContext->getContextCode();
                } else {
                    $contextCode = FALSE;
                }
                $name = $this->getParam('name', $contextCode);
                if(!empty($name)){
                    $page = $this->objDbwiki->getPage($name);
                    if(empty($page)){
                        return $this->nextAction('add_page', array(
                           'name' => $name,
                        ), 'wiki');
                    }elseif($page['page_status'] == 6){
                        return $this->nextAction('deleted_page', array(
                            'name' => $name,
                        ), 'wiki');
                    }
                }
                $version = $this->getParam('version');
                $tab = $this->getParam('tab', 0);
                $templateContent = $this->objWikidisplay->showMain($name, $version, $tab);
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'restore_page':
                $name = $this->getParam('name');
                $version = $this->getParam('version');
                $mode = $this->getParam('mode');
                if($mode == 'reinstate'){
                    $this->objDbwiki->reinstatePage($name, $version);
                }else{
                    $this->objDbwiki->restorePage($name, $version);
                }
                $templateContent = $this->objWikidisplay->showMain($name);
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'search_wiki':
                $field = $this->getParam('field');
                $value = $this->getParam('value');
                if($field == 2){
                    $column = 'page_name';
                }elseif($field == 3){
                    $column = 'page_content';
                }else{
                    $column = 'both';
                }
                $data = $this->objDbwiki->searchWiki($column, $value);
                $templateContent = $this->objWikidisplay->showSearch($data);
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'view_authors':
                $author = $this->getParam('author');
                $templateContent = $this->objWikidisplay->showAuthors($author);
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'validate_name':
                $name = $this->getParam('name');
                $divContent = $this->objWikidisplay->showValidateName($name);
                //header("Content-Type: text/html;charset=utf-8");
                return $divContent;
                break;

            case 'preview_page':
                $name = $this->getParam('name');
                $content = $this->getParam('wikiContent');
                $divContent = $this->objWikidisplay->showPreview($name, $content);
                header("Content-Type: text/html;charset=utf-8");
                return $divContent;
                break;

            case 'deleted_page':
                $name = $this->getParam('name');
                $templateContent = $this->objWikidisplay->showDeletedPage($name);
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'check_lock':
                $id = $this->getParam('id');
        		// If user can edit , then lock it and continue
        		$canEdit = $this->objLock->canUserEdit($id, $this->userId);
                if($canEdit){
                    // If page is locked, force unlock
                    $isLocked = $this->objLock->isEditLocked('tbl_wiki_pages', $id);
                    if($isLocked){
                        $this->objLock->forceEditUnlock('tbl_wiki_pages', $id);
                		$this->objLock->lockEdit('tbl_wiki_pages', $id, $this->userId);
                    }
                    return $this->objWikidisplay->showLockedMessage($id, TRUE);
                }else{
                // Page is currently locked by another user notify user
                    return $this->objWikidisplay->showLockedMessage($id);
                }
                break;

            case 'lock_page':
                $id = $this->getParam('id');
                $this->objLock->lockEdit('tbl_wiki_pages', $id, $this->userId);
                return $this->objWikidisplay->showLockedMessage($id, 'keeplocked');
                break;

            case 'add_rating':
                $name = $this->getParam('name');
                $rating = $this->getParam('rating');
                $this->objDbwiki->addRating($name, $rating);
                return $this->objWikidisplay->showRating($name, TRUE);
                break;

            case 'view_ranking':
                $templateContent = $this->objWikidisplay->showRanking();
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'view_watchlist':
                $templateContent = $this->objWikidisplay->showWatchlist();
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'delete_watch':
                $id =$this->getParam('id');
                $this->objDbwiki->deleteWatchById($id);
                return $this->nextAction('view_watchlist', array(), 'wiki');
                break;

            case 'update_watch':
                $mode = $this->getParam('mode');
                $name = $this->getParam('name');
                if($mode == 'add'){
                    return $this->objDbwiki->addWatch($name);
                }else{
                    return $this->objDbwiki->deleteWatchByName($name);
                }
                break;

            case 'remove_watch':
                $name = $this->getParam('name');
                $userId = $this->getParam('id');
                $this->objDbwiki->deleteWatchByName($name, $userId);
                return $this->nextAction('', array(), 'wiki');
                break;

            case 'show_diff':
                $name = $this->getParam('name');
                $from = $this->getParam('from');
                $to = $this->getParam('to');
                return $this->objWikidisplay->showDiff($name, $from, $to);
                break;

            case 'view_links':
                $templateContent = $this->objWikidisplay->showLinks();
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'update_link':
                $id = $this->getParam('id');
                $name = $this->getParam('name', $this->getParam('update_name'));
                $url = $this->getParam('url', $this->getParam('update_url'));
                if(empty($id)){
                    $this->objDbwiki->addLink($name, $url);
                }else{
                    $this->objDbwiki->editLink($id, $name, $url);
                }
                return $this->nextAction('view_links', array(), 'wiki');
                break;

            case 'update_post':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                $title = $this->getParam('post_title', $this->getParam('post_title_'.$id));
                $content = $this->getParam('post_content', $this->getParam('post_content_'.$id));
                if(empty($id)){
                    $this->objDbwiki->addPost($name, $title, $content);
                }else{
                    $this->objDbwiki->editPost($id, $title, $content);
                }
                return $this->nextAction('view_page', array(
                    'name' => $name,
                    'tab' => 6,
                ), 'wiki');
                break;

            case 'delete_post':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                $this->objDbwiki->deletePost($id);
                return $this->nextAction('view_page', array(
                    'name' => $name,
                    'tab' => 6,
                ), 'wiki');
                break;

            case 'restore_post':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                $this->objDbwiki->restorePost($id);
                return $this->nextAction('view_page', array(
                    'name' => $name,
                    'tab' => 6,
                ), 'wiki');
                break;

            case 'add_wiki':
                $templateContent = $this->objWikidisplay->showAddWiki();
                $this->setVarByRef('templateContent', $templateContent);
                header("Content-Type: text/html;charset=utf-8");
                return 'template_tpl.php';
                break;

            case 'create_wiki':
                $name = $this->getParam('name');
                $desc = $this->getParam('desc');
                $visibility = $this->getParam('visibility');
						         	//add to activity log
						         	if($this->eventsEnabled)
						         	{
						         	  //The message to be posted
                  $addmessage = $this->objUser->username()." ". $this->objLanguage->languageText('mod_wiki_newwikialert', 'wiki')."-".$name;
                  $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $addmessage,
						 																				'link'=> $this->uri(array()),
						 																				'contextcode' => $this->objContext->getContextCode(),
						 																				'author' => $this->objUser->fullname(),
						 																				'description'=>$addmessage));
						 																				
												 				 $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $addmessage,
						 																				'link'=> $this->uri(array()),
						 																				'contextcode' => null,
						 																				'author' => $this->objUser->fullname(),
						 																				'description'=>$addmessage));
						         	}
                $this->objDbwiki->addWiki($name, $desc, $visibility);
                return $this->nextAction('view_page', array(), 'wiki');
                break;

            case 'select_wiki':
                $wikiId = $this->getParam('wiki');
                $this->setSession('wiki_id', $wikiId);
                return $this->nextAction('view_page', array(), 'wiki');
                break;

            case 'change_visibility':
                $wikiId = $this->getParam('wikiId');
                $visibility = $this->getParam('visibility');
                $this->objDbwiki->editWiki($wikiId, $visibility);
                return $this->nextAction('view_page', array(), 'wiki');

            case 'context_wiki':
                $contextCode = $this->getParam('contextCode');
                $this->objDbwiki->createContextWiki($contextCode);
                return $this->nextAction('view_page', array(), 'wiki');

            default:
                return $this->nextAction('view_page', array(), 'wiki');
                break;
        }
    }
}
?>
