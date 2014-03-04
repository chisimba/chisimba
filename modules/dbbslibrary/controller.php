<?php
/**
* The DBBS library provides functionality for browsing, searching and uploading new documents to the digital library.
* 
* @author Megan Watson
* @copyright 2007 University of the Western Cape & AVOIR Project
* @license GNU GPL
* @package dbbslibrary
*/

// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
// end security check

/**
 * The DBBS library provides functionality for browsing, searching and uploading new documents to the digital library.
 * 
 * @author Megan Watson
 * @copyright 2007 University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package dbbslibrary
 */

class dbbslibrary extends controller 
{
    
    /**
	* Constructor
    */
    public function init()
    {
        try{
            // Set the permissions
            $this->dbbsLibTools = $this->getObject('dbbslibtools', 'dbbslibrary');
            $this->dbbsLibTools->setGroupPermissions();
            
            $this->dbbsTools = $this->getObject('dbbstools', 'dbbspostlogin');
            $this->manage = $this->getObject('managelibrary', 'dbbslibrary');
            $this->manage->setSubmitType('dbbs', 'dbbslibrary');
            
            $this->etdResource = $this->getObject('etdresource', 'etd');
            $this->dbThesis = $this->getObject('dbthesis', 'etd');
            $this->dbThesis->setSubmitType('dbbs');
            $this->dbStats = $this->getObject('dbstatistics', 'etd');
            $this->dbCitations = $this->getObject('dbcitations', 'etd');
            $this->etdFiles = $this->getObject('etdfiles', 'etd');
            $this->config = $this->getObject('configure', 'etd');
            $this->emailResults = $this->getObject('emailresults', 'etd');
            $this->emailResults->setModuleName('dbbslibrary');
        
            $this->etdSearch = $this->getObject('search', 'etd');
            $this->etdSearch->setMetaType('thesis', 'dbbs');
        
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objLangCode = $this->getObject('languagecode', 'language');
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objBlocks = $this->getObject('blocks', 'blocks');
            
        }catch(Exception $e){
            throw customException($e->getMessage());
        }
    }
    
    /**
     * The standard dispatch function
     */
    public function dispatch($action)
    {
        switch ($action){
            
            /* ** Resource functions ** */
            
            case 'viewauthor':
            case 'viewtitle':
                $this->unsetSession('resource');
                $metaId = $this->getParam('id');
                $resource = $this->dbThesis->getMetadata($metaId);
                $citationList = $this->dbCitations->getList($resource['submitid']);
                $metaTags = $this->etdResource->getMetadataTags($resource);
                $this->setVarByRef('resource', $resource);
                $this->setVarByRef('citationList', $citationList);
                $this->setVarByRef('metaTags', $metaTags);
                $this->dbStats->recordVisit($metaId);
                $this->setSession('resourceId', $metaId);
                $leftSide = $this->objBlocks->showBlock('resourcemenu', 'dbbslibrary', '','','', FALSE);
                $this->dbbsTools->setLeftSide($leftSide);
                return 'showetd_tpl.php';
                
            case 'printresource':
                $search = $this->getSession('resource');
                $this->setVarByRef('search', $search);
                return 'print_tpl.php';

            case 'emailresource':
                $resource = $this->getSession('resource');
                $head = $this->objLanguage->languageText('phrase_emailresource');
                $message = $this->objLanguage->languageText('mod_etd_attachmentresource', 'etd');
                $shortName = $this->objConfig->getinstitutionShortName().':';
                $subject = $this->objLanguage->code2Txt('mod_etd_requestedresource', 'etd', array('shortname' => $shortName));
                $this->emailResults->setHeading($head);
                $this->emailResults->setSubject($subject, FALSE);
                $this->emailResults->setMessage($message);
                $this->emailResults->setEmailBody($resource);
                $email = $this->emailResults->showEmail();
                $this->setVarByRef('display', $email);
                return 'main_tpl.php';

            case 'registerdownload':
                $this->dbStats->recordDownload();
                break;
                
            
            case 'sendemail':
                $confirm = $this->objLanguage->languageText('mod_etd_confirmemailsent', 'etd');
                $link = $this->objLanguage->languageText('mod_etd_returnsearch', 'etd');
                $return = $this->getSession('return');
                $pos = strpos($return['action'], 'browse');
                if(!($pos === FALSE)){
                    $link = $this->objLanguage->languageText('mod_etd_returnbrowse', 'etd');
                }
                $email = $this->emailResults->sendEmail();
                
                $objLink = new link($this->uri($return));
                $objLink->link = $link;
                $display = '<p class="confirm">'.$confirm.'</p><p>'.$objLink->show().'</p>';
                $this->setVarByRef('display', $display);
                return 'main_tpl.php';
                
            /* ** Search functions ** */
            
            case 'advsearch':
                $this->unsetSession('resource');
                // set a session to use when returning from a resource or from emailing a resource.
                $session['displayLimit'] = $this->getParam('displayLimit');
                $session['displayStart'] = $this->getParam('displayStart');
                $session['action'] = 'advsearch';
                $this->setSession('return', $session);

                $pageTitle = $this->objLanguage->languageText('phrase_searchresults');
                $objViewBrowse = $this->getObject('viewbrowse', 'etd');
                $objViewBrowse->setModuleName('dbbslibrary');
                $objViewBrowse->create($this->etdSearch);
                $objViewBrowse->setAccess( FALSE );
                $objViewBrowse->showAlpha(FALSE);
                $objViewBrowse->showPrint();
                $objViewBrowse->setNumCols(3);
                $objViewBrowse->setPageTitle($pageTitle);
                
                //$this->objLink = new link($this->uri(array('action'=>'search')));
                //$this->objLink->link = $this->objLanguage->languageText('phrase_newsearch');
                $criteria = $this->etdSearch->getSession('criteria');
                $objViewBrowse->addExtra($criteria); //.'<p>'.$this->objLink->show().'</p>');
                $display = $objViewBrowse->show();
                $this->setVarByRef('display', $display);
                //$this->etdTools->setLeftBlocks(FALSE, TRUE, FALSE);
                return 'main_tpl.php';
                
            case 'printsearch':
                $pageTitle = $this->objLanguage->languageText('phrase_searchresults');
                $objViewBrowse = $this->getObject('viewbrowse', 'etd');
                $objViewBrowse->setModuleName('dbbslibrary');
                $objViewBrowse->create($this->etdSearch);
                $search = $objViewBrowse->getResults();
                $this->setVarByRef('search', $search);
                return 'print_tpl.php';

            case 'emailsearch':
                $pageTitle = $this->objLanguage->languageText('phrase_searchresults');
                $shortName = $this->objConfig->getinstitutionShortName().':';
                $subject = $this->objLanguage->code2Txt('mod_etd_requestedsearchresults', 'etd', array('shortname' => $shortName));
                $message = $this->objLanguage->languageText('mod_etd_attachmentsearchresults', 'etd');
                $objViewBrowse = $this->getObject( 'viewbrowse', 'etd' );
                $objViewBrowse->setModuleName('dbbslibrary');
                $objViewBrowse->create($this->etdSearch);
                $search = $objViewBrowse->getResults();
                $this->emailResults->setEmailBody($search);
                $this->emailResults->setSubject($subject, TRUE);
                $this->emailResults->setMessage($message);
                $email = $this->emailResults->showEmail();
                $this->setVarByRef('display', $email);
                return 'main_tpl.php';
                
            /* ** Browse functions ** */
            
            case 'browseauthor':
                $this->unsetSession('resource');
                // set a session to use when returning from a resource or from emailing a resource.
                $session = array();
                $session['searchForLetter'] = $this->getParam('searchForLetter');
                $session['displayLimit'] = $this->getParam('displayLimit');
                $session['displayStart'] = $this->getParam('displayStart');
                $session['action'] = 'browseauthor';
                $this->setSession('return', $session);

                $objAuthor = $this->getObject('dbthesis', 'etd');
                $objAuthor->setBrowseType('author');
                $this->setVar('num', 3);
                $this->setVarByRef('browseType', $objAuthor);
                return 'browse_tpl.php';

            case 'browsetitle':
                $this->unsetSession('resource');
                // set a session to use when returning from a resource or from emailing a resource.
                $session = array();
                $session['searchForLetter'] = $this->getParam('searchForLetter');
                $session['displayLimit'] = $this->getParam('displayLimit');
                $session['displayStart'] = $this->getParam('displayStart');
                $session['action'] = 'browsetitle';
                $this->setSession('return', $session);

                $objTitle = $this->getObject('dbthesis', 'etd');
                $objTitle->setBrowseType('title');
                $this->setVar('num', 3);
                $this->setVarByRef('browseType', $objTitle);
                return 'browse_tpl.php';
                
            /* ** Management and new Submissions ** */
            
            case 'managesubmissions':
                $mode = $this->getParam('mode');
                $display = $this->manage->show($mode);
                //$this->etdTools->setLeftBlocks(FALSE, TRUE, FALSE);
                $this->setVarByRef('display', $display);
                return 'main_tpl.php';
                
            case 'savesubmissions':
                $save = $this->getParam('save');
                $mode = $this->getParam('mode');
                $nextmode = $this->getParam('nextmode');
                if(!empty($save)){
                    $this->manage->show($mode);
                }
                return $this->nextAction('managesubmissions', array('mode' => $nextmode));

            /* *** Functions for configuring the archive *** */

            case 'showconfig':
                $mode = $this->getParam('mode');
                $display = $this->config->show($mode);
                //$this->etdTools->setLeftBlocks(FALSE, TRUE, FALSE);
                $this->setVarByRef('display', $display);
                return 'main_tpl.php';
                break;

            case 'saveconfig':
                $save = $this->getParam('save');
                $mode = $this->getParam('mode');
                $nextmode = $this->getParam('nextmode');
                if(!empty($save)){
                    $this->config->show($mode);
                }
                return $this->nextAction('showconfig', array('mode' => $nextmode));
                break;

            default:
                $display = '';
                $this->setVarByRef('display', $display);
                return 'main_tpl.php';
        }
    }
    
    /**
    * Temporary fix for context permissions.
    * The method builds an array of groups in which the user is a member. The group determines the users level of access in the site.
    *
    * @access private
    * @return void
    *
    private function setGroupPermissions()
    {
        if($this->objUser->isLoggedIn()){
            $access = $this->getSession('accessLevel');
            if(!(isset($access) && !empty($access))){
                $accessLevel = array();
                $accessLevel[] = 'user';
                $groupId = $this->objGroup->getLeafId(array('ETD Managers'));
                if($this->objGroup->isGroupMember($this->userPkId, $groupId)){
                    $accessLevel[] = 'manager';
                }
                $groupId = $this->objGroup->getLeafId(array('ETD Editors'));
                if($this->objGroup->isGroupMember($this->userPkId, $groupId)){
                    $accessLevel[] = 'editor';
                }
                $groupId = $this->objGroup->getLeafId(array('ETD Exam Board'));
                if($this->objGroup->isGroupMember($this->userPkId, $groupId)){
                    $accessLevel[] = 'board';
                }
                $groupId = $this->objGroup->getLeafId(array('Students'));
                if($this->objGroup->isGroupMember($this->userPkId, $groupId)){
                    $accessLevel[] = 'student';
                }
                $this->setSession('accessLevel', $accessLevel);
            }
        }
    }
    */
}
?>