<?php
/**
 * Context Content controller
 *
 * Controller class for the Context Content Module in Chisimba
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
 * @package   learningcontent
 * @author    Paul Mungai <wandopm@gmail.com>
 * @copyright 2010 Paul Mungai
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 16778 2010-02-12 10:58:59Z qfenama $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end security check


/**
 * Context Content controller
 *
 * Controller class for the Context Content Module in Chisimba
 *
 * @category  Chisimba
 * @package   learningcontent
 * @author    Paul Mungai
 * @extension scorm functionality by Paul Mungai <pwando@uonbi.ac.ke>
 * @copyright 2010 Paul Mungai
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class learningcontent extends controller {
/**
 * @var string $contextCode Context Code of Current Context
 */
    protected $contextCode;
    /**
     * Constructor
     */
    public function init() {
        try {
        // Load Chapter Classes
            $this->objChapters = $this->getObject('db_learningcontent_chapters');
            $this->objContextChapters = $this->getObject('db_learningcontent_contextchapter');
            $this->objContentOrder = $this->getObject('db_learningcontent_order');
            // $this->objContentTitles = $this->getObject('db_learningcontent_titles');
            $this->objFiles = $this->getObject('dbfile','filemanager');
            $this->objFolders = $this->getObject('dbfolder','filemanager');

            // Load Content Classes
            $this->objContentPages = $this->getObject('db_learningcontent_pages');
            $this->objContentTitles = $this->getObject('db_learningcontent_titles');
            $this->objContentInvolvement = $this->getObject('db_learningcontent_involvement');
            $this->objContextActivityStreamer = $this->getObject('db_learningcontent_activitystreamer');

            $this->objDynamicBlocks = $this->getObject('dynamicblocks_learningcontent');
            //Load Module Catalogue Class
            $this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');

            // Load Context Object
            $this->objContext = $this->getObject('dbcontext', 'context');            

            // Store Context Code
            $this->contextCode = $this->objContext->getContextCode();

            $this->objFilePreviewFilter = $this->getObject('parse4filepreview', 'filters');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->userId();
            $this->sessionId = session_id();
            $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');
            //Check permissions
            $this->hasAccess = $this->objContextGroups->isContextLecturer(); 
            $this->hasAccess|= $this->objUser->isAdmin(); 
            //Load Activity Streamer

            if($this->objModuleCatalogue->checkIfRegistered('activitystreamer') && $this->objUser->isLoggedIn()) {
               // $this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
               // $this->eventDispatcher->addObserver ( array ($this->objActivityStreamer, 'postmade' ) );
                $this->eventsEnabled = TRUE;
            } else {
                $this->eventsEnabled = FALSE;
            }
            $this->objMenuTools = $this->getObject('tools', 'toolbar');
            $this->objConfig = $this->getObject('altconfig', 'config');
  	    $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig');
	    $this->objContextComments = $this->getObject('db_learningcontent_comment', 'learningcontent');
            $this->setVar('pageSuppressXML',TRUE);
        }
        catch(customException $e) {
        //oops, something not there - bail out
            echo customException::cleanUp();
            //we don't want to even attempt anything else right now.
            die();
        }


    }



    /**
     * Method to override login requirement for certain actions
     * @param string $action Action user is taking
     * @return boolean
     */
    function requiresLogin($action) {
	$actions = array('viewchapter','viewpage', 'rss', '');
	if(in_array($action, $actions)){
		return FALSE;
		//var_dump($action);
	}else{
		return TRUE;
	}
    }



    /**
     * Dispatch Method to run required action
     * @param string $action
     */
    public function dispatch($action) {
	$this->contextCode = ($this->getParam('rss_contextcode') != "") ? $this->getParam('rss_contextcode') : $this->contextCode ;
	//$this->getParam('contextcode');
        if ($this->contextCode == '' && $action != 'notincontext') {
            $action = 'notincontext';
        }
        $this->setLayoutTemplate('layout_chapter_tpl.php');
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery/jquery.livequery.js', 'jquery'));
        switch ($action) {
            case 'notincontext':
                return 'notincontext_tpl.php';
            case 'switchcontext':
                die('Switch Context'); // Fix Up
            case 'addpage':
                return $this->addPage($this->getParam('chapter'), $this->getParam('id', ''), $this->getParam('context', ''));
            case 'savepage':
                return $this->savePage();
            case 'autosavepage':
                return $this->autoSavePage();
            case 'addscorm':
                return $this->addScormChapter();
            case 'addtfpicschapter':
                return $this->addtfpicsChapter();
            case 'addscormpage':
                return $this->addScormPage($this->getParam('chapter'), $this->getParam('id', ''), $this->getParam('context', ''));
            case 'editscorm':
                return $this->editScormChapter($this->getParam('id'));
            case 'editpage':
                return $this->editPage($this->getParam('id'), $this->getParam('context'));
            case 'updatepage':
                return $this->updatePage();
            case 'viewpage':
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $trackPage = array();
                $trackPage['contextItemId'] = $this->getParam('id');
                $trackPage['prevpageid'] = $this->getParam('prevpageid');
                $trackPage['prevchapterid'] = $this->getParam('prevchapterid');
                $trackPage['contextCode'] = $this->contextCode;
                $trackPage['module'] = $this->getParam('module');
                $trackPage['datecreated'] = date('Y-m-d H:i:s');
                $trackPage['pageorchapter'] = 'page';
                $trackPage['description'] = $this->objLanguage->languageText('mod_learningcontent_viewpage', 'learningcontent');
                return $this->viewPage($this->getParam('id'),Null, $trackPage);
            case 'viewpageimage':
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                return $this->viewPage($this->getParam('id'),$this->getParam('imageId'));
            case 'trackviewimage':
                $trackPage = array();
                $trackPage['contextCode'] = $this->contextCode;
                $trackPage['action'] = $this->getParam('imagetype');
                $trackPage['module'] = $this->getParam('module');
                $trackPage['datecreated'] = date('Y-m-d H:i:s');
                $trackPage['description'] = $this->objLanguage->languageText('mod_learningcontent_viewimage', 'learningcontent');
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressIM', TRUE);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressContainer', TRUE);
                $this->setVar('suppressFooter', TRUE);
                return $this->trackImageView($this->getParam('imageId'), $trackPage);
            case 'viewpicorformula':
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                return $this->viewImage($this->getParam('imageId'));                
                break;
            case 'getimageurl':
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressIM', TRUE);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressContainer', TRUE);
                $this->setVar('suppressFooter', TRUE);
                return $this->viewImage2($this->getParam('imageId'));
                break;
            case 'imagewindowpopup':
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressIM', TRUE);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressContainer', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $this->appendArrayVar('bodyOnLoad', 'window.focus();');
                $imgString = $this->viewImage2($this->getParam('imageId'));
                $this->setVarByRef('imgString', $imgString);
                return 'viewimage_popup_tpl.php';
            case 'deletepage':            
                return $this->deletePage($this->getParam('id'), $this->getParam('context'));
            case 'deletepageconfirm':
                return $this->deletePageConfirm();
            case 'fixleftright':
                return $this->fixLeftRightValues();
            case 'movepageup':
                return $this->movePageUp($this->getParam('id'));
            case 'movepagedown':
                return $this->movePageDown($this->getParam('id'));
            case 'savechapter':
                return $this->saveChapter();

            case 'savescormpage':
                return $this->saveScormPage();
            case 'savescormchapter':
                return $this->saveScormChapter();
            case 'addchapter':
                return $this->addChapter();
            case 'editchapter':
                return $this->editChapter($this->getParam('id'));
            case 'updatechapter':
                return $this->updateChapter();
            case 'updatescormchapter':
                return $this->updateScormChapter();
            case 'deletechapter':
                return $this->deleteChapter($this->getParam('id'));
            case 'deletechapterconfirm':
                return $this->deleteChapterConfirm();
            case 'movechapterup':
                return $this->moveChapterUp($this->getParam('id'));
            case 'movechapterdown':
                return $this->moveChapterDown($this->getParam('id'));
            case 'viewchapter':
                $trackPage = array();
                $trackPage['contextItemId'] = $this->getParam('id');
                $trackPage['prevpageid'] = $this->getParam('prevpageid');
                $trackPage['contextCode'] = $this->contextCode;
                $trackPage['module'] = $this->getParam('module');
                $trackPage['datecreated'] = date('Y-m-d H:i:s');
                $trackPage['pageorchapter'] = 'chapter';
                $trackPage['description'] = $this->objLanguage->languageText('mod_learningcontent_viewchapter', 'learningcontent');
                return $this->viewChapter($this->getParam('id'), $trackPage);
            case 'viewprintchapter':
                return $this->viewPrintChapter($this->getParam('id'));
            case 'changenavigation':
                return $this->changeNavigation($this->getParam('type'), $this->getParam('id'));
            case 'movetochapter':
                return $this->moveToChapter($this->getParam('id'), $this->getParam('chapter'));
            case 'changebookmark':
                return $this->changeBookMark();
            case 'search':
                return $this->search($this->getParam('contentsearch'));
            case 'showcontexttools':
                return $this->showcontexttools();
            case 'chapterlistastree':
                return $this->getChapterListAsTree();
            case 'showcontextchapters':
                $trackPage = array();
                $trackPage['contextItemId'] = $this->getParam('chapterid');
                $trackPage['prevpageid'] = $this->getParam('prevpageid');
                $trackPage['contextCode'] = $this->contextCode;
                $trackPage['module'] = $this->getParam('module');
                $trackPage['datecreated'] = date('Y-m-d H:i:s');
                $trackPage['pageorchapter'] = 'chapter';
                $trackPage['description'] = $this->objLanguage->languageText('mod_learningcontent_viewchapter', 'learningcontent');
                return $this->showContextChapters($trackPage);
    	    case 'rss':
		return $this->viewRss();
	    case 'addcomment':
		return $this->addComment();
            case 'viewlogs':
                $this->setLayoutTemplate('layout_firstpage_tpl.php');
                return 'usersactivitylog_tpl.php';
            case 'jsongetlogs':
                $start = $this->getParam('start');
                $limit = $this->getParam('limit');
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $contextLogs = $this->objContextActivityStreamer->jsonContextLogs( $this->contextCode, $start, $limit );
                echo $contextLogs;
                 exit(0);
                break;
            default:
                return $this->showContextChapters();
        }
    }

    /**
     * Method to override isValid to enable administrators to perform certain action
     *
     * @param $action Action to be taken
     * @return boolean
     */
    public function isValid($action) {        
        $courseDetails = $this->objContext->getField('access',$this->contextCode);        
        if ($this->objUser->isAdmin () || $this->objContextGroups->isContextLecturer()) {
            return TRUE;
        } else {
            return FALSE;//parent::isValid ( $action );
        }
    }

    /**
     * Method to display the list of chapters in a context
     *
     * This is also the home page of the module
     */
    protected function showContextChapters($trackPage='') {

        if ($trackPage!='' && $this->eventsEnabled) {
         $ischapterlogged = $this->objContextActivityStreamer->getRecord($this->userId, $trackPage['contextItemId'], $this->sessionId);
         $recordId = $this->objContextActivityStreamer->getRecordId($this->userId, $trackPage['prevpageid'], $this->sessionId);
         //Log when user leaves a page
         if(!empty($recordId))
            $ischapterlogged = $this->objContextActivityStreamer->updateSingle($recordId);
         if ($ischapterlogged==FALSE) {
            $datetimenow = date('Y-m-d H:i:s');
            $ischapterlogged = $this->objContextActivityStreamer->addRecord($this->userId, $this->sessionId, $trackPage['contextItemId'], $this->contextCode,$trackPage['module'],$trackPage['datecreated'],$trackPage['pageorchapter'],$trackPage['description'], $datetimenow, Null);
         }
        }

        $numContextChapters = $this->objContextChapters->getNumContextChapters($this->contextCode);

        $this->setVarByRef('numContextChapters', $numContextChapters);

        $chapters = $this->objContextChapters->getContextChapters($this->contextCode);
        $this->setVarByRef('chapters', $chapters);

        $this->setVar('showScrollLinks', TRUE);

        $this->setLayoutTemplate('layout_firstpage_tpl.php');
        return 'listchapters_tpl.php';

    }
    /**
    *Method to return an image for viewing for jQuery
    */
    protected function viewImage($imageId) {
        $imageName = $this->objFiles->getFileName($imageId);
        $imageDesc = $this->objFiles->getFileInfo($imageId);
        if(empty($imageDesc['filedescription'])){
         $imageDesc = $imageName;
        }else{
         $imageDesc = $imageDesc['filedescription'];
        }
        $hformulatbl = $this->newObject('htmltable', 'htmlelements');
        $hformulatbl->cellpadding = 5;
        $hformulatbl->width = 1024;
        $hformulatbl->valign = 'center';
        //Text
        $hformulatbl->startRow();
        $hformulatbl->addCell('<div align="center" style="background-color:#A8A8A8;"><h3>'.$imageDesc.'</h3><br /><br /><br /><br /><br /><br /><br />'.$this->objFilePreviewFilter->parse('[FILEPREVIEW id="'.$imageId.'" comment="'.$imageName.'" /]')."<br /><br /><br /><br /><br /><br /><br /></div>");
        $hformulatbl->endRow();

        echo $hformulatbl->show();
    }
    /**
    *Method to return an image
    */
    protected function viewImage2($imageId) {
        $imageName = $this->objFiles->getFileName($imageId);
        $imageDesc = $this->objFiles->getFileInfo($imageId);
        if(empty($imageDesc['filedescription'])){
         $imageDesc = $imageName;
        }else{
         $imageDesc = $imageDesc['filedescription'];
        }
        return '<h3>'.$imageDesc.'</h3>'.$this->objFilePreviewFilter->parse('[FILEPREVIEW id="'.$imageId.'" comment="'.$imageName.'" /]')."<br />";
    }
    /**
     * Method to add a new chapter
     */
    protected function addChapter() {
        $this->setVar('mode', 'add');

        $this->setLayoutTemplate('layout_firstpage_tpl.php');

        return 'addeditchapter_tpl.php';
    }
    /**
     * Method to add a new scorm chapter
     */
    protected function addScormChapter() {
        $this->setVar('mode', 'add');

        $this->setLayoutTemplate('layout_firstpage_tpl.php');

        return 'addeditscormchapter_tpl.php';
    }
    /**
     * Method to add a new scorm chapter
     */
    protected function addtfpicsChapter() {
        $this->setVar('mode', 'add');

        $this->setLayoutTemplate('layout_firstpage_tpl.php');

        return 'addedittfpchapter_tpl.php';
    }
    /**
     * Method to save a newly create chapter
     */
    protected function saveChapter() {
        $title = $this->getParam('chapter');
        $intro = $this->getParam('intro');
        $picture = $this->getParam('picture');
        $formula = $this->getParam('formula');
        $visibility = $this->getParam('visibility');

        $chapterId = $this->objChapters->addChapter('', $title, $intro, $picture, $formula);

        $result = $this->objContextChapters->addChapterToContext($chapterId, $this->contextCode, $visibility);

        if ($result == FALSE) {
            return $this->nextAction(NULL, array('error'=>'couldnotcreatechapter'));
        } else {
        //add to activity log
            if($this->eventsEnabled) {
                $message = $this->objUser->getsurname()." ".$this->objLanguage->languageText('mod_learningcontent_addednewchapter', 'learningcontent')." ".$this->contextCode;
                $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $message,
                    'link'=> $this->uri(array()),
                    'contextcode' => $this->contextCode,
                    'author' => $this->objUser->fullname(),
                    'chapterId' => $chapterId,
                    'description'=>$message));
            }

            return $this->nextAction('viewchapter', array('message'=>'chaptercreated', 'id'=>$chapterId));
        }
    }

    /**
     * Method to save a newly created scorm chapter
     */
    protected function saveScormChapter() {
        $title = $this->getParam('chapter');
        $intro = $this->getParam('parentfolder');
        $visibility = $this->getParam('visibility');
        $scorm = $this->getParam('scorm');

        $chapterId = $this->objChapters->addChapter('', $title, $intro);

        $result = $this->objContextChapters->addChapterToContext($chapterId, $this->contextCode, $visibility, $scorm);

        if ($result == FALSE) {
            return $this->nextAction(NULL, array('error'=>'couldnotcreatechapter'));
        } else {
        //add to activity log
            if($this->eventsEnabled) {
                $message = $this->objUser->getsurname()." ".$this->objLanguage->languageText('mod_learningcontent_addednewchapter', 'learningcontent')." ".$this->contextCode;
                $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $message,
                    'link'=> $this->uri(array()),
                    'contextcode' => $this->contextCode,
                    'author' => $this->objUser->fullname(),
                    'description'=>$message));
            }
            return $this->nextAction(NULL, array('message'=>'chaptercreated', 'id'=>$result));
        }
    }

    /**
     * Method to edit a chapter
     *
     * @param string $id Record Id of the Chapter
     */
    protected function editChapter($id) {
        $chapter = $this->objContextChapters->getChapter($id);

        if ($chapter == FALSE) {
            return $this->nextAction(NULL, array('error'=>'editchapterdoesnotexist'));
        } else {
            $this->setVar('mode', 'edit');

            $this->setVarByRef('chapter', $chapter);
            $this->setVarByRef('id', $id);
            $this->setVarByRef('currentChapter', $id);

            $this->setVar('hideNavSwitch', TRUE);
            $this->setVar('currentPage', NULL);


            //$this->setLayoutTemplate('layout_firstpage_tpl.php');

            return 'addeditchapter_tpl.php';
        }
    }

    /**
     * Method to edit a scorm chapter
     *
     * @param string $id Record Id of the Chapter
     */
    protected function editScormChapter($id) {
        $chapter = $this->objContextChapters->getChapter($id);
        $ischapterlogged = $this->objContextActivityStreamer->deleteRecord($id);
        if ($chapter == FALSE) {
            return $this->nextAction(NULL, array('error'=>'editchapterdoesnotexist'));
        } else {
            $this->setVar('mode', 'edit');

            $this->setVarByRef('chapter', $chapter);
            $this->setVarByRef('id', $id);

            $this->setLayoutTemplate('layout_firstpage_tpl.php');

            return 'addeditscormchapter_tpl.php';
        }
    }
    /**
     * Method to update a chapter
     */
    protected function updateChapter() {

        $id = $this->getParam('id');
        $chaptercontentid = $this->getParam('chaptercontentid');
        $contextchapterid = $this->getParam('contextchapterid');
        $title = $this->getParam('chapter');
        $intro = $this->getParam('intro');
        $picture = $this->getParam('picture');
        $formula = $this->getParam('formula');
        $visibility = $this->getParam('visibility');

        if ($id == '' || $chaptercontentid == '' || $contextchapterid == '') {
            return $this->nextAction(NULL, array('error'=>'noidprovided'));
        } else {
        //Remove previous records on activity streamer
            $ischapterlogged = $this->objContextActivityStreamer->deleteRecord($id);

            $objChapterContent = $this->getObject('db_learningcontent_chaptercontent');

            $chapter = $objChapterContent->getRow('id', $chaptercontentid);

            if ($chapter == FALSE) {
                return $this->nextAction(NULL, array('error'=>'invalididprovided'));
            } else if ($chapter['chapterid'] != $id) {
                    return $this->nextAction(NULL, array('error'=>'invalididprovided'));
                } else {
                    $objChapterContent->updateChapter($chaptercontentid, $title, $intro, $picture, $formula);
                    $this->objContextChapters->updateChapterVisibility($contextchapterid, $visibility);

                    return $this->nextAction(NULL, array('message'=>'chapterupdated', 'id'=>$id));
                }
        }

    }
    /**
     * Method to update a scorm chapter
     */
    protected function updateScormChapter() {

        $id = $this->getParam('id');
        $chaptercontentid = $this->getParam('chaptercontentid');
        $contextchapterid = $this->getParam('contextchapterid');
        $title = $this->getParam('chapter');
        $intro = $this->getParam('parentfolder');
        $visibility = $this->getParam('visibility');
        $scorm = $this->getParam('scorm');
        if ($id == '' || $chaptercontentid == '' || $contextchapterid == '') {
            return $this->nextAction(NULL, array('error'=>'noidprovided'));
        } else {
            $objChapterContent = $this->getObject('db_learningcontent_chaptercontent');

            $chapter = $objChapterContent->getRow('id', $chaptercontentid);

            if ($chapter == FALSE) {
                return $this->nextAction(NULL, array('error'=>'invalididprovided'));
            } else if ($chapter['chapterid'] != $id) {
                    return $this->nextAction(NULL, array('error'=>'invalididprovided'));
                } else {
                    $objChapterContent->updateChapter($chaptercontentid, $title, $intro);
                    $this->objContextChapters->updateChapterVisibility($contextchapterid, $visibility);

                    return $this->nextAction(NULL, array('message'=>'chapterupdated', 'id'=>$id));
                }
        }

    }

    /**
     * Method to delete a chapter
     *
     * This function generates a confirmation form
     *
     * @param string $id Record Id of the Chapter
     */
    protected function deleteChapter($id) {
        $chapter = $this->objContextChapters->getChapter($id);

        if ($chapter == FALSE) {
            return $this->nextAction(NULL, array('error'=>'editchapterdoesnotexist'));
        } else {

            if ($this->objContextChapters->isContextChapter($this->contextCode, $id)) {
                $this->setVar('mode', 'edit');

                $this->setVarByRef('chapter', $chapter);
                $this->setVarByRef('id', $id);

                $numPages = $this->objContentOrder->getContextPages($this->contextCode, $id);
                $this->setVar('numPages', count($numPages));

                $this->setLayoutTemplate('layout_firstpage_tpl.php');

                return 'deletechapter_tpl.php';
            } else {
                return $this->nextAction(NULL, array('error'=>'chapternotinthiscontext'));
            }

        }
    }

    /**
     * Method to delete the chapter after confirmation
     *
     * If confirmation is not received, chapter is not deleted.
     */
    protected function deleteChapterConfirm() {


        $confirmation = $this->getParam('confirmation', 'N');
        $context = $this->getParam('context');
        $id = $this->getParam('id');

        // Check that confirmation has been received
        if ($confirmation == 'Y') {
        // Check that Context Matches
            if ($context != $this->contextCode) {
                return $this->nextAction(NULL, array('message'=>'attempttodeletechapteroutofcontext', 'id'=>$id));
            }

            // Check That Chapter is In Context
            if ($this->objContextChapters->isContextChapter($this->contextCode, $id)) {

            // Check how many other chapters also have this context
                $numContextWithChapter = $this->objContextChapters->getNumContextWithChapter($id);

                $chapter = $this->objContextChapters->getContextChapterTitle($id);

                // If only one, do full delete
                if ($numContextWithChapter == 1) {
                // Delete Chapter
                    $this->objContextChapters->removeChapterFromContext($id, $this->contextCode);
                    $this->objChapters->deleteChapter($id);

                    // Delete Pages in Chapter
                    $pages = $this->objContentOrder->getContextPages($this->contextCode, $id);

                    if (count($pages) > 0) {
                        foreach ($pages as $page) {
                            $this->objContentTitles->deleteTitle($page['titleid']);
                        //$this->objContentOrder->deletePage($page['id']);
                        }
                    }
                } else { // Else simply remove the chapter from this context.
                    $this->objContextChapters->removeChapterFromContext($id, $this->contextCode);
                }

                // Return Message
                return $this->nextAction(NULL, array('message'=>'chapterdeleted', 'chapter'=>$chapter));

            } else {
                return $this->nextAction(NULL, array('message'=>'chapternotincontext', 'id'=>$id));
            }
        } else {
            return $this->nextAction(NULL, array('message'=>'deletechaptercancelled', 'id'=>$id));
        }
    }
    /**
     * Method to add a page
     * @param string $chapter Record Id of Chapter under which page will be placed
     * @param string $parent Record Id of the Parent
     * @param string $contextCode Context Code
     */
    protected  function addPage($chapter, $parent='', $contextCode='') {
        if ($contextCode != '' && $contextCode != $this->contextCode) {
            return $this->nextAction('switchcontext');
        }

        $this->setLayoutTemplate(NULL);

        $this->setVar('mode', 'add');
        $this->setVar('formaction', 'savepage');
        $this->setVarByRef('chapter', $chapter);
        $this->setVarByRef('currentChapter', $chapter);

        $tree = $this->objContentOrder->getTree($this->contextCode, $chapter, 'dropdown');
        $this->setVarByRef('tree', $tree);

        return 'addeditpage_tpl.php';
    }

    /**
     * Method to add a scorm page
     * @param string $chapter Record Id of Chapter under which page will be placed
     * @param string $parent Record Id of the Parent
     * @param string $contextCode Context Code
     */
    protected  function addScormPage($chapter, $parent='', $contextCode='') {
        if ($contextCode != '' && $contextCode != $this->contextCode) {
            return $this->nextAction('switchcontext');
        }

        $this->setLayoutTemplate(NULL);

        $this->setVar('mode', 'add');
        $this->setVar('formaction', 'savepage');
        $this->setVarByRef('chapter', $chapter);
        $this->setVarByRef('currentChapter', $chapter);

        $tree = $this->objContentOrder->getTree($this->contextCode, $chapter, 'dropdown');
        $this->setVarByRef('tree', $tree);
        $this->setVar('mode', 'add');

        $this->setLayoutTemplate('layout_firstpage_tpl.php');

        return 'addeditscormpage_tpl.php';
    }
    /**
     * Method to save a newly added page
     */
    protected  function savePage() {
        $menutitle = stripslashes($this->getParam('menutitle'));
        $headerscripts = stripslashes($this->getParam('headerscripts'));
        $language = 'en';
        $pagecontent = stripslashes($this->getParam('pagecontent'));
        $parent = stripslashes($this->getParam('parentnode'));
        $chapter = stripslashes($this->getParam('chapter'));
        $picture = stripslashes($this->getParam('headerpicscripts'));
        $formula = stripslashes($this->getParam('headerformulascripts')); 

        $chapterTitle = $this->objContextChapters->getContextChapterTitle($chapter);
        $titleId = $this->objContentTitles->addTitle('', $menutitle, $pagecontent, $picture, $formula, $language, $headerscripts);


        $pageId = $this->objContentOrder->addPageToContext($titleId, $parent, $this->contextCode, $chapter);

        $this->setVar('mode', 'add');
        $this->setVar('formaction', 'savepage');
        //add to activity log
        if($this->eventsEnabled) {
            $message = $this->objUser->getsurname()." ".$this->objLanguage->languageText('mod_learningcontent_addednewpage', 'learningcontent')." ".$this->contextCode." ".$this->objLanguage->languageText('word_chapter', 'learningcontent').": ".$chapterTitle;
            $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $message,
                'link'=> $this->uri(array()),
                'contextcode' => $this->contextCode,
                'author' => $this->objUser->fullname(),
                'description'=>$message));
        }
        return $this->nextAction('viewpage', array('id'=>$pageId, 'message'=>'pagesaved'));
    }

    /**
     * Method to save a newly added page
     */
    protected  function autoSavePage() {

        $menutitle = stripslashes($this->getParam('menutitle'));
        $headerscripts = stripslashes($this->getParam('headerscripts'));
        $language = 'en';
        $pagecontent = stripslashes($this->getParam('pagecontent'));
        $picture = stripslashes($this->getParam('picture'));
        $formula = stripslashes($this->getParam('formula'));        
        $parent = stripslashes($this->getParam('parentnode'));
        $chapter = stripslashes($this->getParam('chapter'));
        $chapterTitle = $this->objContextChapters->getContextChapterTitle($chapter);
        $titleId = $this->objContentTitles->addTitle('', $menutitle, $pagecontent, $picture, $formula, $language, $headerscripts);


        $pageId = $this->objContentOrder->addPageToContext($titleId, $parent, $this->contextCode, $chapter);
        echo $pageId;
        die();

    }

    /**
     * Method to save a newly added scorm page
     */
    protected  function saveScormPage() {

        $menutitle = stripslashes($this->getParam('menutitle'));
        $headerscripts = stripslashes($this->getParam('headerscripts'));
        $language = 'en';
        $pagecontent = $this->getParam('parentfolder');
        $parent = stripslashes($this->getParam('parentnode'));
        $chapter = stripslashes($this->getParam('chapter'));
        $chapterTitle = $this->objContextChapters->getContextChapterTitle($chapter);
        $scorm=$this->getParam('scorm');


        $titleId = $this->objContentTitles->addTitle('', $menutitle, $pagecontent, $language, $headerscripts,$scorm);

        $pageId = $this->objContentOrder->addPageToContext($titleId, $parent, $this->contextCode, $chapter);

        $this->setVar('mode', 'add');
        $this->setVar('formaction', 'savepage');
        //add to activity log
        if($this->eventsEnabled) {
            $message = $this->objUser->getsurname()." ".$this->objLanguage->languageText('mod_learningcontent_addednewpage', 'learningcontent')." ".$this->contextCode." ".$this->objLanguage->languageText('word_chapter', 'learningcontent').": ".$chapterTitle;
            $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $message,
                'link'=> $this->uri(array()),
                'contextcode' => $this->contextCode,
                'author' => $this->objUser->fullname(),
                'description'=>$message));
        }
        return $this->nextAction('viewpage', array('id'=>$pageId, 'message'=>'pagesaved'));
    }


    /**
     * Method to track image view
     * @param string $pageId Record Id of the Page
     * @param string $imageId Selected Image Id
     * @param array string $trackPage Contains data to help track user transactions
     */
    protected  function trackImageView($imageId='', $trackImage='') {
        //Log in activity streamer only if logged in (Public courses dont need login)
        if(!empty($this->userId) && !empty($trackImage)){
         $isimagelogged = FALSE;
	 //$isimagelogged = $this->objContextActivityStreamer->getRecord($this->userId, $imageId, $this->sessionId);
         //if(!empty($isimagelogged))
         $recordId = $this->objContextActivityStreamer->getRecordId($this->userId, $imageId, $this->sessionId);
        //Log when user leaves an Image
        if(!empty($recordId)) {
            $isimagelogged = $this->objContextActivityStreamer->updateSingle($recordId);
            $str = 1;
        } else {
            $datetimenow = date('Y-m-d H:i:s');
            $isimagelogged = $this->objContextActivityStreamer->addRecord($this->userId, $this->sessionId, $imageId, $this->contextCode,$trackImage['module'],$trackImage['datecreated'],$trackImage['action'],$trackImage['description'], $datetimenow, Null);
           $str = 2;
         }
        }
        return $str;
    }
    /**
     * Method to view a page
     * @param string $pageId Record Id of the Page
     * @param string $imageId Selected Image Id
     * @param array string $trackPage Contains data to help track user transactions
     */
    protected  function viewPage($pageId='', $imageId='', $trackPage='') {
        if ($pageId == '') {
            return $this->nextAction(NULL);
        }
        if (!empty($imageId)) {
		        $this->setVarByRef('imageId', $imageId);
        }
        $page = $this->objContentOrder->getPage($pageId, $this->contextCode);
        if($page['scorm'] == 'Y') {
            return $this->nextAction('viewscorm',
            array(
            'folderId'=>$page['pagecontent'],
            'chapterid'=>$page['chapterid'],
            'id'=>$page['id'],
            'rght'=>$page['rght'],
            'lft'=>$page['lft'],
            'mode'=>'page'
            ),
            'scorm');
        }
        //Log in activity streamer only if logged in (Public courses dont need login)
        if(!empty($this->userId) && !empty($trackPage)){
	         $ischapterlogged = $this->objContextActivityStreamer->getRecord($this->userId, $pageId, $this->sessionId);
        if(!empty($trackPage['prevchapterid']))
         $recordId = $this->objContextActivityStreamer->getRecordId($this->userId, $trackPage['prevchapterid'], $this->sessionId);
        else
         $recordId = $this->objContextActivityStreamer->getRecordId($this->userId, $trackPage['prevpageid'], $this->sessionId);
        //Log when user leaves a page
        if(!empty($recordId))
            $ischapterlogged = $this->objContextActivityStreamer->updateSingle($recordId);

        if ($ischapterlogged==FALSE) {
            $datetimenow = date('Y-m-d H:i:s');//$this->now();
            $ischapterlogged = $this->objContextActivityStreamer->addRecord($this->userId, $this->sessionId, $pageId, $this->contextCode,$trackPage['module'],$trackPage['datecreated'],$trackPage['pageorchapter'],$trackPage['description'], $datetimenow, Null);
         }
        }

		if ($page == FALSE) {
        //echo 'page does not exist';
            return $this->nextAction(NULL, array('error'=>'pagedoesnotexist'));
        }

        $this->setVarByRef('page', $page);
        $this->setVarByRef('currentPage', $pageId);
        $this->setVarByRef('currentChapter', $page['chapterid']);
        $this->setVarByRef('pagelft', $page['lft']);

        $this->setVarByRef('nextPage', $this->objContentOrder->getNextPage($this->contextCode, $page['chapterid'], $page['lft']));
        $this->setVarByRef('prevPage', $this->objContentOrder->getPreviousPage($this->contextCode, $page['chapterid'], $page['lft']));        
        $this->setVarByRef('isFirstPageOnLevel', $this->objContentOrder->isFirstPageOnLevel($page['id']));
        $this->setVarByRef('isLastPageOnLevel', $this->objContentOrder->isLastPageOnLevel($page['id']));

        $breadcrumbs = $this->objContentOrder->getBreadcrumbs($this->contextCode, $page['chapterid'], $page['lft'], $page['rght']);

        $chapterTitle = $this->objContextChapters->getContextChapterTitle($page['chapterid']);

        $this->setVarByRef('currentChapterTitle', $chapterTitle);

        if ($chapterTitle != FALSE) {
            $chapterLink = new link ($this->uri(array('action'=>'viewchapter', 'id'=>$page['chapterid'])));
            $chapterLink->link = $this->objLanguage->languageText('word_chapter', 'word', 'Chapter').': '.$chapterTitle;

            array_unshift($breadcrumbs, $chapterLink->show());
        //array_unshift($breadcrumbs, 'Chapter: '.$chapterTitle);
        }

        $this->objMenuTools->addToBreadCrumbs($breadcrumbs);


        $chapters = $this->objContextChapters->getContextChapters($this->contextCode);
        $this->setVarByRef('chapters', $chapters);

        return 'viewpage_tpl.php';
    }

    /**
     * Method to edit a page
     * @param string $pageId Record Id of the Page
     */
    protected function editPage($pageId) {
        if ($pageId == '') {
            return $this->nextAction(NULL);
        }
        $page = $this->objContentOrder->getPage($pageId, $this->contextCode);

        if ($page == FALSE) {
            return $this->nextAction(NULL, array('error'=>'pagedoesnotexist'));
        }

        $this->setLayoutTemplate(NULL);

        $this->setVarByRef('page', $page);
        $this->setVarByRef('currentChapter', $page['chapterid']);

        $tree = $this->objContentOrder->getTree($this->contextCode, $page['chapterid'], 'dropdown', $page['parentid'], 'learningcontent', $page['id']);
        $this->setVarByRef('tree', $tree);

        $this->setVar('mode', 'edit');
        $this->setVar('formaction', 'updatepage');

        return 'addeditpage_tpl.php';
    }

    /**
     * Method to Update a Page
     */
    protected function updatePage() {
        $pageId = $this->getParam('id');
        $contextCode = $this->getParam('context');
        $menutitle = stripslashes($this->getParam('menutitle'));
        $headerScripts = stripslashes($this->getParam('headerscripts'));
        $pagecontent = stripslashes($this->getParam('pagecontent'));
        $picture = stripslashes($this->getParam('headerpicscripts'));
        $formula = stripslashes($this->getParam('headerformulascripts')); 
        $parentnode = stripslashes($this->getParam('parentnode'));

        if ($contextCode != '' && $contextCode != $this->contextCode) {
            return $this->nextAction('switchcontext');
        } else {
        //Remove previous records on activity streamer
            $ischapterlogged = $this->objContextActivityStreamer->deleteRecord($pageId);
            $page = $this->objContentOrder->getPage($pageId, $this->contextCode);
            $parentPage = $this->objContentOrder->getPage($parentnode, $this->contextCode);

            if ($page == FALSE) {
                return $this->nextAction(NULL, array('error'=>'pagedoesnotexist'));
            } else {
                $this->objContentPages->updatePage($page['pageid'], $menutitle, $pagecontent, $picture, $formula, $headerScripts);

                if ($parentnode != $page['parentid']) {
                //if ($parentnode != $page['parentid'] && ($page['lft'] > $parentPage['lft']) && ($page['rght'] < $parentPage['rght'])) {


                    $this->objContentOrder->changeParent($this->contextCode, $page['chapterid'], $pageId, $parentnode);
                }

                return $this->nextAction('viewpage', array('id'=>$pageId, 'message'=>'pageupdated'));
            }
        }
    }


    /**
     * Method to delete a page
     *
     * This method presents a confirmation form for users wanting to delete a page
     *
     * @param string $pageId Record Id of the Page
     */
    protected function deletePage($pageId) {
        if ($pageId == '') {
            return $this->nextAction(NULL);
        }

        $page = $this->objContentOrder->getPage($pageId, $this->contextCode);

        if ($page == FALSE) {
            return $this->nextAction(NULL, array('error'=>'pagedoesnotexist'));
        }

        $children = $page['rght'] - $page['lft'] - 1;

        if ($children != 0) {
            return $this->nextAction('viewpage', array('id'=>$pageId, 'message'=>'pagehassubpages'));
        }

        $this->setVarByRef('page', $page);

        return 'deletepage_tpl.php';
    }


    /**
     * Method to delete a page once confirmation has been received.
     */
    protected function deletePageConfirm() {
        $confirmation = $this->getParam('confirmation', 'N');
        $pageId = $this->getParam('id');
        $context = $this->getParam('context');

        if ($pageId == '' || $context == '') {
            return $this->nextAction(NULL, array('error'=>'pagedoesnotexist', 'attemptedaction'=>'delete'));
        }

        $page = $this->objContentOrder->getPage($pageId, $this->contextCode);

        if ($page == FALSE) {
            return $this->nextAction(NULL, array('error'=>'pagedoesnotexist', 'attemptedaction'=>'delete'));
        } else {
            $children = $page['rght'] - $page['lft'] - 1;
            if ($children == 0) {
                if ($confirmation == 'Y') {

                    $nextPage = $this->objContentOrder->getNextPageSQL($this->contextCode, $page['chapterid'], $page['lft']);

                    $this->objContentTitles->deleteTitle($page['titleid']);

                    $this->objContentOrder->rebuildChapter($this->contextCode, $page['chapterid']);

                    if (is_array($nextPage)) {
                        return $this->nextAction('viewpage', array('id'=>$nextPage['id'], 'message'=>'pagedeleted', 'title'=>urlencode($page['menutitle'])));
                    } else {
                        return $this->nextAction('viewchapter', array('id'=>$page['chapterid'], 'message'=>'pagedeleted', 'title'=>urlencode($page['menutitle'])));
                    }

                } else {
                    return $this->nextAction('viewpage', array('id'=>$pageId, 'message'=>'deletecancelled'));
                }
            } else {
                return $this->nextAction('viewpage', array('id'=>$pageId, 'message'=>'pagehassubpages'));
            }
        }
    }



    /**
     * Method to move a page up
     * @param string $id Record Id of the Page
     */
    protected function movePageUp($id) {
        $result = $this->objContentOrder->movePageUp($id);

        return $this->nextAction('viewpage', array('id'=>$id, 'message'=>'movepageup', 'result'=>$result));
    }


    /**
     * Method to move a page down
     * @param string $id Record Id of the Page
     */
    protected function movePageDown($id) {
    //$result = $this->objContextChapters->moveChapterDown($id);
        $result = $this->objContentOrder->movePageDown($id);

        return $this->nextAction('viewpage', array('id'=>$id, 'message'=>'movepageup', 'result'=>$result));
    }


    /**
     * Method to move a chapter up
     * @param string $id Record Id of the Chapter
     */
    protected function moveChapterUp($id) {
        $result = $this->objContextChapters->moveChapterUp($id);

        return $this->nextAction(NULL, array('id'=>$id, 'message'=>'movechapterup', 'result'=>$result));
    }


    /**
     * Method to move a chapter down
     * @param string $id Record Id of the Chapter
     */
    protected function moveChapterDown($id) {
        $result = $this->objContextChapters->moveChapterDown($id);

        return $this->nextAction(NULL, array('id'=>$id, 'message'=>'movechapterdown', 'result'=>$result));
    }


    /**
     * Method to view a chapter
     *
     * This method redirects to the first page in a chapter
     *
     * @param string $id Record Id of the Chapter
     */
    protected function viewChapter($id, $trackPage='') {

        $firstPage = $this->objContentOrder->getFirstChapterPage($this->contextCode, $id);
        if ($trackPage!='' && $this->eventsEnabled) {
         $ischapterlogged = $this->objContextActivityStreamer->getRecord($this->userId, $id, $this->sessionId);
         $recordId = $this->objContextActivityStreamer->getRecordId($this->userId, $trackPage['prevpageid'], $this->sessionId);
         //Log when user leaves a page
         if(!empty($recordId))
            $ischapterlogged = $this->objContextActivityStreamer->updateSingle($recordId);
         if ($ischapterlogged==FALSE) {
            $datetimenow = date('Y-m-d H:i:s');
            $ischapterlogged = $this->objContextActivityStreamer->addRecord($this->userId, $this->sessionId, $id, $this->contextCode,$trackPage['module'],$trackPage['datecreated'],$trackPage['pageorchapter'],$trackPage['description'], $datetimenow, Null);
         }
        }
        if ($firstPage == FALSE) {

            $chapter = $this->objContextChapters->getChapter($id);

            if ($chapter == FALSE) {
                return $this->nextAction(NULL, array('error'=>'chapterdoesnotexist'));
            } else {
                $this->setVarByRef('chapter', $chapter);

                $this->setVar('errorTitle', $this->objLanguage->languageText('mod_learningcontent_chapterhasnocontent', 'learningcontent', 'Chapter has no content'));
                $this->setVar('errorMessage', $this->objLanguage->languageText('mod_learningcontent_chapterhasnocontentinstruction', 'learningcontent', 'The chapter you have tried to view does not have any content, or had content which has now been deleted.'));
                return 'chapternocontent_tpl.php';
            }

        } else {
            return $this->nextAction('viewpage', array('id'=>$firstPage['id'], 'prevchapterid'=>$id, 'message'=>$this->getParam('message')));
        }
    }


    /**
     * Method to Render a Chapter in PDF
     * @param string $id Record Id of Chapter
     * @return PDF File
     */
    private function viewPrintChapter($id) {
    // Load Class to clean up paths
        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');

        // Get all the pages of the chapter
        $pages = $this->objContentOrder->getPages($id, $this->contextCode);

        //$contextCode = sha1($this->contextCode);
        $contextCode = ($this->contextCode);

        // Set the Path where the Files will be stored
        $path = $this->objConfig->getcontentBasePath().'/learningcontent/'.$contextCode;

        // Get Site Root Path
        $siteRoot = $this->objConfig->getSitePath().'/';

        // Clean up slashes
        $objCleanUrl->cleanUpUrl($siteRoot);

        // Load Class for Creating Directories
        $objMkdir = $this->newObject('mkdir', 'files');
        // Recursively create directories if it does not exist
        $objMkdir->mkdirs($path);

        // If Chapter has no pages
        if (count($pages) == 0) {
        // Send Error Message. Chapter has no Pages / Content
            $this->setVar('errorTitle', $this->objLanguage->languageText('mod_learningcontent_chapterhasnocontent', 'learningcontent', 'Chapter has no content'));
            $this->setVar('errorMessage', $this->objLanguage->languageText('mod_learningcontent_chapterhasnocontentinstruction', 'learningcontent', 'The chapter you have tried to view does not have any content, or had content which has now been deleted. Please choose another chapter'));

            return 'errormessage_tpl.php';
        } else {
        // Create Absolute Path to where PDF is stored
            $destination = $this->objConfig->getcontentBasePath().'/learningcontent/'.$contextCode.'/chapter_'.$id.'.pdf';
            // Clean Up slashes
            $objCleanUrl->cleanUpUrl($destination);

            // If PDF file exists
            if (file_exists($destination)) {
            // Redirect to PDF

            // Create Local Path to PDF
                $redirect = $this->objConfig->getcontentPath().'/learningcontent/'.$contextCode.'/chapter_'.$id.'.pdf';

                // Redirect
                header('location: '.$redirect);

            } else {
            // Else, Create PDF file

                /* Creating the PDF Process:
                Get All Pages
                Export all Pages into HTML format
                Use HTML Doc to convert HTML into PDF
                */

            // Array consisting of list of paths to HTML files
                $pagePath = array();

                // Loop through Chapter Pages
                foreach ($pages as $page) {
                // Get Page Content
                    $pageContent = $this->objContentOrder->getPage($page['id'], $this->contextCode);

                    // Create HTML filename
                    $filename = $path.'/'.$page['id'].'.html';

                    // Add HTML Path to array
                    $pagePath[] = $filename;

                    // Load File Object
                    $objFile = $this->getObject('dbfile', 'filemanager');

                    /*
                    To get images to work properly, we need to use the absolute
                    path to the image.
                    
                    This code basically uses regex to get all items that use 
                    filemanager, and convert them to absolute paths.
                    */

                    // Page Content
                    $pageText = $pageContent['pagecontent'];

                    // Replace XHTML
                    $pageText = str_replace('&amp;', '&', $pageText);



                    // Get Image Source Links
                    $results=spliti('src="',$pageText);
                    // Loop through the matches
                    foreach ($results as $line) {
                        $r2=split('"',$line);
                        $srcURL=$r2[0];
                        $r2=split('module=filemanager&action=file&id=',$srcURL);
                        if (isset($r2[1])) {
                            $r3=split('&',$r2[1]);
                            $fileId=$r3[0];
                            $filePath = $objFile->getFullFilePath($fileId);
                            $pageText=str_replace($srcURL,$filePath,$pageText);
                        }
                    }


                    // Create HTML Document
                    $content = '<html><head>';
                    $content .= '<title>'.$pageContent['menutitle'].'</title>';
                    $content .= '</head><body>';

                    $content .= $pageText;

                    $content .= '</body></html>';

                    // Write HTML Document to File System
                    $handle = fopen($filename, 'w');
                    fwrite($handle, $content);
                    fclose($handle);


                }

                // Load HTML Doc Class
                $objHtmlDoc = $this->getObject('htmldoc', 'htmldoc');

                // Create Parameters for HTML Doc Source Inputs
                $htmlSources = '';

                foreach ($pagePath as $htmlPage) {
                    $htmlSources .= $htmlPage.' ';
                }

                // Render to PDF
                $objHtmlDoc->render($htmlSources, TRUE, TRUE, $destination);

                // Check if PDF Exists - Prove that file was successfully created.
                if (file_exists($destination)) {
                    $redirect = $this->objConfig->getcontentPath().'/learningcontent/'.$contextCode.'/chapter_'.$id.'.pdf';
                    header('location: '.$redirect);
                } else {
                // Else Show Error Message
                    $this->setVar('errorTitle', $this->objLanguage->languageText('mod_learningcontent_couldnotcreatepdf', 'learningcontent', 'Could not create PDF Document'));
                    $this->setVar('errorMessage', ' ');
                    return 'errormessage_tpl.php';
                }
            }
        }
    }


    /**
     * Method to change the navigation approach for context
     * This method is a response to an ajax call
     * @param string $type Type of navigation to switch to
     * @param string $pageId Record Id of the Page
     */
    private function changeNavigation($type, $pageId='') {

        $page = $this->objContentOrder->getPage($pageId, $this->contextCode);

        if ($page == FALSE) {
            echo ''; // Return Nothing - AJAX won't do anything
        } else {
            if ($type == 'twolevel') {
                $this->setSession('navigationType', 'twolevel');
                echo $this->objContentOrder->getTwoLevelNav($this->contextCode, $page['chapterid'], $pageId);
                echo '<p><a href="javascript:changeNav(\'tree\');">'.$this->objLanguage->languageText('mod_learningcontent_viewastree', 'learningcontent', 'View as Tree').'...</a>';
                echo '<br /><a href="javascript:changeNav(\'bookmarks\');">'.$this->objLanguage->languageText('mod_learningcontent_viewbookmarkedpages', 'learningcontent', 'View Bookmarked Pages').'</a></p>';


            } else if ($type == 'tree') {
                    $this->setSession('navigationType', 'tree');
                    echo $this->objContentOrder->getTree($this->contextCode, $page['chapterid'], 'htmllist', $pageId, 'learningcontent');
                    echo '<p><a href="javascript:changeNav(\'twolevel\');">'.$this->objLanguage->languageText('mod_learningcontent_viewtwolevels', 'learningcontent', 'View Two Levels at a time').' ...</a><br /><a href="javascript:changeNav(\'bookmarks\');">'.$this->objLanguage->languageText('mod_learningcontent_viewbookmarkedpages', 'learningcontent', 'View Bookmarked Pages').'</a></p>';


                } else if ($type == 'bookmarks') {
                        $this->setSession('navigationType', 'bookmarks');

                        echo $this->objContentOrder->getBookmarkedPages($this->contextCode, $page['chapterid'], $pageId, 'learningcontent');

                        echo '<p><a href="javascript:changeNav(\'twolevel\');">'.$this->objLanguage->languageText('mod_learningcontent_viewtwolevels', 'learningcontent', 'View Two Levels at a time').' ...</a><br /><a href="javascript:changeNav(\'tree\');">'.$this->objLanguage->languageText('mod_learningcontent_viewastree', 'learningcontent', 'View as Tree').'...</a></p>';


                    } else {
                        echo ''; // Unknown Type - Return Nothing - AJAX won't do anything
                    }
        }
    }


    /**
     * Method to move a page to another a chapter
     * @param string $pageId Record Id of the Page
     * @param string $chapter Record Id of the Chapter
     */
    private function moveToChapter($pageId, $chapter) {
        $result = $this->objContentOrder->movePageToChapter($pageId, $chapter, $this->contextCode);

        return $this->nextAction('viewpage', array('id'=>$pageId, 'message'=>$result));
    }
    /**
     * Method to toggle the status of a bookmarked page
     */
    protected function changeBookMark() {
        $id = $this->getParam('id');
        $type = $this->getParam('type', 'on');

        if ($type == 'on') {
            $this->objContentOrder->bookmarkPage($id);
            echo '<a href="javascript:changeBookmark(\'off\');">'.$this->objLanguage->languageText('mod_learningcontent_removebookmark', 'learningcontent', 'Remove Bookmark').'</a>';
        } else {
            $this->objContentOrder->removeBookmark($id);
            echo '<a href="javascript:changeBookmark(\'on\');">'.$this->objLanguage->languageText('mod_learningcontent_bookmarkpage', 'learningcontent', 'Bookmark Page').'</a>';
        }

    }

    /**
     * Method to search for text within a context content
     * @param string $searchText Text to search for
     */
    protected function search($searchText) {
        $chapters = $this->objContextChapters->getContextChapters($this->contextCode);
        $this->setVarByRef('chapters', $chapters);

        $this->setLayoutTemplate('layout_firstpage_tpl.php');

        $objSearchResults = $this->getObject('searchresults', 'search');
        $searchResults = $objSearchResults->displaySearchResults($searchText, 'learningcontent', $this->contextCode);

        $this->setVarByRef('searchText', $searchText);
        $this->setVarByRef('searchResults', $searchResults);

        return 'searchresults_tpl.php';
    }

    /**
     * Method to get current session settings, as weill as list of chapters for a context
     * This is used for debugging purposes
     */
    public function home_debug() {
        echo '<pre>';
        //print_r($_SESSION);

        $numPages = $this->objContentOrder->getNumContextPages($this->contextCode);
        echo $numPages.'<br /><br />';

        $firstPage = $this->objContentOrder->getFirstPage($this->contextCode);
        print_r($firstPage);

        echo $this->contextCode;

        echo '<hr />';

        echo $this->objContextChapters->getContextChaptersSQL($this->contextCode);

        echo '<hr />';

        echo $this->objContextChapters->getNumContextChapters($this->contextCode);

        $results = $this->objContextChapters->getContextChapters($this->contextCode);

        print_r($results);
    }

    /**
     * Method to fix left right value - debugging purpose
     */
    protected function fixLeftRightValues() {
        $this->objContentOrder->rebuildContext($this->contextCode);
    }
	
	/**
     * Used to add the comment on the page
     *
     */
	public function addComment()
	{	
		$comment = htmlentities($this->getParam('comment') , ENT_QUOTES);
		$userid = $this->objUser->userId();
		$pageid = $this->getParam('pageid');
		if(!empty($comment) && !empty($userid) && !empty($pageid))
		{
			$id = $this->objContextComments->addPageComment($userid, $pageid, $comment);
		}
        return $this->nextAction('viewpage', array('id'=>$pageid));
	}

    /**
     * Used to display the latest presentations of a user RSS Feed
     *
     */
    public function viewRss()
    {
        $this->objFeedCreator = $this->getObject('feeder', 'feed');
        $format = 'RSS2.0'; // $this->getParam('feedselector');
        //grab the feed items
        $posts = $this->objContextChapters->getContextChapters($this->getParam('rss_contextcode'));
    	error_log(var_export($posts, true));
        //set up the feed...
        $fullname = $this->getParam('title');
        //title of the feed
        $feedtitle = htmlentities($fullname);
        //description
        $feedDescription = "RSS2.0 Feed of the $fullname stream";

        //link back to the blog
        $feedLink = $this->objConfig->getSiteRoot() . "index.php?module=learningcontent&rss_contextcode=".$this->getParam('rss_contextcode');
        //sanitize the link
        $feedLink = htmlentities($feedLink);
        //set up the url
        $feedURL = $this->objConfig->getSiteRoot() . "index.php?module=learningcontent&action=rss";
        $feedURL = htmlentities($feedURL);
        //set up the feed
        $this->objFeedCreator->setupFeed(TRUE, $feedtitle, $feedDescription, $feedLink, $feedURL);
        //loop through the posts and create feed items from them
        foreach($posts as $feeditems) {
            	//use the post title as the feed item title
		$itemTitle = $fullname.': '.$feeditems['chaptertitle'];
		$itemLink = str_replace('&amp;', '&', $this->uri(array('action' => 'viewchapter', 'id' => $feeditems['chapterid'], 'rss_contextcode' => $this->getParam('rss_contextcode'))));
                //description
		$itemDescription = substr(strip_tags($feeditems['introduction']), 0, 200).'...';
                //where are we getting this from
                $itemSource = $this->objConfig->getSiteRoot() . "index.php?module=learningcontent&rss_contextcode=".$this->getParam('rss_contextcode');
                //feed author
                //$auth = $feeditem['from_user'];
                //$itemAuthor = htmlentities($auth."<$auth@capetown.peeps.co.za>");
                //add this item to the feed
                $this->objFeedCreator->addItem($itemTitle, $itemLink, $itemDescription, $itemSource, $itemAuthor);
          }
        //check which format was chosen and output according to that
        $feed = $this->objFeedCreator->output(); //defaults to RSS2.0
        echo htmlentities($feed);
        break;

    }
}
?>
