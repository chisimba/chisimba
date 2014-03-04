<?php

/**
 * Scorm controller
 *
 * Controller class for the Scorm Module in Chisimba
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
 * @package   scorm
 * @author    Paul Mungai <pwando@uonbi.ac.ke>
 * @copyright 2008 Paul Mungai
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: controller.php 9085 2008-05-08 17:55:52Z paul $ //correct this
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts

/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * scorm controller
 *
 * Controller class for the Context Content Module in Chisimba
 *
 * @category  Chisimba
 * @package   contextcontent
 * @author    Paul Mungai <pwando@uonbi.ac.ke>
 * @copyright 2008 Paul Mungai
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class scorm extends controller {

    public $objLanguage;
    public $objUser;
    public $objReadXml;
    public $objFolders;
    public $objTreeNode;
    public $objTreeMenu;

    /**
     * Constructor
     */
    public function init() {

        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        // Load Scorm Classes
        $this->objReadXml = & $this->getObject('readxml_scorm', 'scorm');
        $this->objFiles = $this->getObject('dbfile', 'filemanager');
        $this->objFolders = $this->getObject('dbfolder', 'filemanager');
        $this->objTreeMenu = & $this->getObject('treemenu', 'tree');
        $this->objTreeNode = & $this->loadClass('treenode', 'tree');
        //remove this objContext
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objContextChapter = $this->getObject('db_contextcontent_contextchapter', 'contextcontent');
        // Store Context Code
        $this->contextCode = $this->objContext->getContextCode();
        
        $this->objContextActivityStreamer = $this->getObject('db_contextcontent_activitystreamer', 'contextcontent');
        //Store user Id
        $this->userId = $this->objUser->userId();
        $this->objContentOrder = $this->getObject('db_contextcontent_order', 'contextcontent');

        $this->objContextChapters = $this->getObject('db_contextcontent_contextchapter', 'contextcontent');
        /*
        $this->appendArrayVar('headerParams', $extbase);
        $this->appendArrayVar('headerParams', $extalljs);
        $this->appendArrayVar('headerParams', $extallcss);
         */
    }

    /**
     * Dispatch Method to run required action
     * @param string $action
     */
    public function dispatch($action) {
        //check if in context, if not, give in
        if (!$this->objContext->isInContext()) {
            return "notincontext_tpl.php";
        }

        // Method to set the layout template for the given action
        //$this->setLayoutTemplate('scorm_layout.php');me

        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     * Method to turn off login requirement for certain actions
     */
    public function requiresLogin($action) {
        $requiresLogin = array('viewscorm', 'getNext', 'getPrev', 'checkfolder');
        if (in_array($action, $requiresLogin)) {
            return FALSE;
        } else if (empty($this->userId)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    private function __viewscorm() {
        $folderId = $this->getParam('folderId', NULL);
        $mode = $this->getParam('mode');
        if ($folderId == null) {
            return $this->nextAction('home', NULL, 'contextcontent');
        }
        $chapterId = $this->getParam('chapterid', $this->objContextChapter->getChapterId($this->contextCode));

        if (!empty($this->userId)) {
            //Log in activity streamer
            $ischapterlogged = $this->objContextActivityStreamer->getRecord($this->userId, $chapterId, $this->contextCode);
            if ($ischapterlogged == FALSE) {
                //($userId, $sessionid, $contextItemId, $contextCode, $modulecode, $datecreated=Null, $pageorchapter=NULL, $description=NULL, $sessionstarttime=NULL, $sessionendtime=NULL)
                $ischapterlogged = $this->objContextActivityStreamer->addRecord($this->userId, Null, $chapterId, $this->contextCode, "scorm", Null, "chapter");
            }
        }
        $this->setVarByRef('folderId', $folderId);
        if ($mode == 'page') {
            $this->setVarByRef('lft', $this->getParam('lft'));
            $this->setVarByRef('rght', $this->getParam('rght'));
            $this->setVarByRef('mode', $this->getParam('mode'));
            $this->setVarByRef('currentChapter', $chapterId);
            $this->setVarByRef('id', $this->getParam('id'));
            $this->setLayoutTemplate('layout_chapter_tpl.php');
        }
        if ($mode == 'chapter') {
            // $link = new link ($this->uri(array('action'=>'viewscorm','mode'=>'chapter', 'folderId'=>$chapter['introduction'], 'chapterid'=>$chapter['chapterid']), $module = 'scorm'));
            $this->setVarByRef('chapterid', $chapterId);
            // $this->setLayoutTemplate('layout_firstpage_tpl.php');
        }
        $chapters = $this->objContextChapters->getContextChapters($this->contextCode);
        $this->setVarByRef('chapters', $chapters);
        $this->setVarByRef('mode', $mode);
        $contextCodeMessage = Null;
        $this->setVarByRef('contextCodeMessage', $contextCodeMessage);
        $this->setVarByRef('id', $folderId);
        $this->setVarByRef('chapterId', $chapterId);
        return 'handle_scorm_tpl.php';
    }

    //Ajax function to get the next page
    private function __getNext() {
        //$this->requiresLogin('getNext');
        $this->setPageTemplate(NULL);
        $this->setLayoutTemplate(NULL);

        $page = $this->getParam('page');
        $folderpath = $this->getParam('folderpath');
        if (!empty($folderpath)) {
            $myResult = $this->objReadXml->xmlNextPage($page, $folderpath);
        }
        if (!empty($myResult)) {
            echo $myResult;
        } else {
            echo 'omega';
        }
    }

    //Ajax function to get the next page
    private function __getPrev() {
        //$this->requiresLogin('getPrev');
        $this->setPageTemplate(NULL);
        $this->setLayoutTemplate(NULL);

        $page = $this->getParam('page');
        $folderpath = $this->getParam('folderpath');
        if (!empty($folderpath)) {
            $myResult = $this->objReadXml->xmlPrevPage($page, $folderpath);
        }
        if (!empty($myResult)) {
            echo $myResult;
        } else {
            echo 'alpha';
        }
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    private function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__' . $action;
        } else {
            return '__viewscorm';
        }
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
    private function validAction(& $action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Ajax function to detect whether a context code has been taken already or not
     * By Paul Mungai
     */
    private function __checkfolder() {
        $this->setPageTemplate(NULL);
        $this->setLayoutTemplate(NULL);
        $code = $this->getParam('code');

        switch (strtolower($code)) {
            case NULL:
                break;
            case 'root':
                echo 'reserved';
                break;
            default:
                $filename = 'imsmanifest.xml';
                $folderPath = $this->objFolders->getFolder($code);
                $verifyFolder = $this->objFiles->getFileFolder($filename, $folderPath['folderpath']);
                if ($verifyFolder[0]['filename'] == 'imsmanifest.xml') {
                    echo 'ok';
                } else {
                    echo 'notok';
                }
        }
    }

}

?>
