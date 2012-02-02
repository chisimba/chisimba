<?php

/**
 * Context controller
 *
 * Controller class for the context in Chisimba
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
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!/**
         * Description for $GLOBALS
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS ['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Context controller
 *
 * Controller class for the context in Chisimba
 *
 * @category  Chisimba
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class context extends controller {

    /**
     * Public context object
     *
     * @var object $objContext
     */
    public $objContext;
    /**
     * Current Context Code
     *
     * @var string $contextCode
     */
    private $contextCode;

    /**
     * Constructor
     */
    public function init() {
        try {
            $this->objContext = $this->getObject('dbcontext');

            $this->contextCode = $this->objContext->getContextCode();
            $this->setVarByRef('contextCode', $this->contextCode);

            $this->contextTitle = $this->objContext->getTitle();
            $this->setVarByRef('contextTitle', $this->contextTitle);

            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');

            $this->objContextBlocks = $this->getObject('dbcontextblocks');
            $this->objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

            //Load Module Catalogue Class
            $this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');

            $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');

            if ($this->objModuleCatalogue->checkIfRegistered('activitystreamer')) {
                $this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
                $this->eventDispatcher->addObserver(array($this->objActivityStreamer, 'postmade'));
                $this->eventsEnabled = TRUE;
            } else {
                $this->eventsEnabled = FALSE;
            }
            $this->dbSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $disableActivityStreamer = $this->dbSysConfig->getValue('DISABLE_ACTIVITYSTREAMER', 'context');
            if ($disableActivityStreamer == 'TRUE' || $disableActivityStreamer == 'true') {
                $this->eventsEnabled = FALSE;
            }
        } catch (customException $e) {
            customException::cleanUp ();

            //Load Module Catalogue Class
            //$this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');
        }
    }

    /**
     * Method to turn off login requirement for certain actions
     */
    public function requiresLogin($action) {
        $requiresLogin = array('controlpanel', 'manageplugins', 'updateplugins', 'renderblock', 'addblock', 'removeblock', 'moveblock', 'updatesettings', 'updatecontext', 'viewuseractivitybyid', 'showuseractivitybymodule', 'selectuseractivitybymodulesdates','selectcontextactivitydates','selecttoolsactivitydates','showcontextactivity','showtoolsactivity');
        if (in_array($action, $requiresLogin)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to override isValid to enable administrators to perform certain action
     *
     * @param $action Action to be taken
     * @return boolean
     */
    public function isValid($action) {
        if ($this->objUser->isAdmin() || $this->objContextGroups->isContextLecturer()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Standard Dispatch Function for Controller
     *
     * @access public
     * @param string $action Action being run
     * @return string Filename of template to be displayed
     */
    public function dispatch($action) {
        // Method to set the layout template for the given action
        $this->setLayoutTemplate('contextlayout_tpl.php');
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery.livequery.js', 'jquery'));
// echo $this->getJavaScriptFile ( 'jquery.livequery.js', 'jquery' ); die();
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
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     */
    protected function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__' . $action;
        } else {
            return '__home';
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
    protected function validAction($action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to show the context home page
     *
     */
    protected function __home() {
        if ($this->contextCode == 'root') {
            return $this->nextAction('join');
        }

        $this->_preventRootAccess();

        $this->setLayoutTemplate(NULL);

        $leftBlocks = $this->objContextBlocks->getContextBlocks($this->contextCode, 'left');
        $this->setVarByRef('leftBlocksStr', $leftBlocks);

        $rightBlocks = $this->objContextBlocks->getContextBlocks($this->contextCode, 'right');
        $this->setVarByRef('rightBlocksStr', $rightBlocks);

        $middleBlocks = $this->objContextBlocks->getContextBlocks($this->contextCode, 'middle');
        $this->setVarByRef('middleBlocksStr', $middleBlocks);

        $allContextBlocks = $this->objContextBlocks->getContextBlocksArray($this->contextCode);
        $this->setVarByRef('allContextBlocks', $allContextBlocks);

        $smallDynamicBlocks = $this->objDynamicBlocks->getSmallContextBlocks($this->contextCode);
        $this->setVarByRef('smallDynamicBlocks', $smallDynamicBlocks);

        $wideDynamicBlocks = $this->objDynamicBlocks->getWideContextBlocks($this->contextCode);
        $this->setVarByRef('wideDynamicBlocks', $wideDynamicBlocks);

        $objBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
        $smallBlocks = $objBlocks->getBlocks('normal', 'context|site');
        $this->setVarByRef('smallBlocks', $smallBlocks);

        $wideBlocks = $objBlocks->getBlocks('wide', 'context|site');
        $this->setVarByRef('wideBlocks', $wideBlocks);

        return 'context_home_tpl.php';
    }

    /**
     * Method to show a list of contexts user can join
     */
    protected function __join() {
        $this->setLayoutTemplate(NULL);
        return 'needtojoin_tpl.php';
    }

    /**
     * Method to join a context
     */
    protected function __joincontext() {
        $contextCode = $this->getParam('contextcode');

        if ($contextCode == '') {
            return $this->nextAction('join', array('error' => 'nocontext'));
        } else {
            if ($this->objContext->joinContext($contextCode)) {
                //add to activity log

                if ($this->eventsEnabled) {
                    $message = $this->objUser->getsurname() . ' ' . $this->objLanguage->languageText('mod_context_hasentered', 'context') . ' ' . $this->objContext->getContextCode();
                    $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                        'link' => $this->uri(array()),
                        'contextcode' => $this->objContext->getContextCode(),
                        'author' => $this->objUser->fullname(),
                        'description' => $message));

                    $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                        'link' => $this->uri(array()),
                        'contextcode' => null,
                        'author' => $this->objUser->fullname(),
                        'description' => $message));
                }
                $contextRedirectURI = $this->getParam('contextredirecturi', NULL);
                if (!is_null($contextRedirectURI)) {
                    $contextRedirectURI_ = urldecode($contextRedirectURI);
                    header('Location: '.$contextRedirectURI_);
                    return NULL;
                }
                //--
                $contextModule=$this->getParam('contextmodule'); //--
                if ($contextModule!=''){
                    $contextAction=$this->getParam('contextaction');
                    return $this->nextAction($contextAction,array('id'=>$this->getParam('contextdata')),$contextModule);
                }//--
                return $this->nextAction('home');
            } else {
                return $this->nextAction('join', array('error' => 'unabletoenter'));
            }
        }
    }

    /**
     * Method to join a context
     */
    protected function __gotomodule() {
        $contextCode = $this->getParam('contextcode');
        $module = $this->getParam('moduleid', 'context');

        if ($contextCode == '') {
            return $this->nextAction('join', array('error' => 'nocontext'));
        } else {
            if ($this->objContext->joinContext($contextCode)) {
                return $this->nextAction(NULL, NULL, $module);
            } else {
                return $this->nextAction('join', array('error' => 'unabletoenter'));
            }
        }
    }

    /**
     * Method to prevent access to certain portions without being logged into a context
     */
    private function _preventRootAccess() {
        if ($this->contextCode == 'root' || $this->contextCode == '') {
            return $this->nextAction('error', array('error' => 'cantaccessrootcontrolpanel'));
        }
    }

    /**
     * Method to show the context control panel
     */
    protected function __controlpanel() {
        $this->_preventRootAccess();

        $this->setLayoutTemplate('contextlayout_tpl.php');

        return 'controlpanel_tpl.php';
    }

    /**
     * Method to show the form for users to add/remove context plugins
     */
    protected function __manageplugins() {
        $this->_preventRootAccess();

        $objContextModules = $this->getObject('dbcontextmodules');
        $objModules = $this->getObject('modules', 'modulecatalogue');

        $contextModules = $objContextModules->getContextModules($this->contextCode);
        $plugins = $objModules->getListContextPlugins();

        $this->setVarByRef('contextModules', $contextModules);
        $this->setVarByRef('plugins', $plugins);

        return 'manageplugins_tpl.php';
    }

    /**
     * Method to update the list of context plugins
     */
    protected function __updateplugins() {
        $this->_preventRootAccess();

        $plugins = $this->getParam('plugins');

        $objContextModules = $this->getObject('dbcontextmodules');
        $objContextModules->deleteModulesForContext($this->contextCode);

        if (is_array($plugins) && count($plugins) > 0) {
            foreach ($plugins as $plugin) {
                $objContextModules->addModule($this->contextCode, $plugin);
            }
        }

        return $this->nextAction('controlpanel', array('message' => 'pluginsupdated'));
    }

    /**
     * Method to display error messages
     */
    protected function __error() {
        return $this->nextAction(NULL, NULL, '_default');
    }

    /**
     * Method to render a block
     */
    protected function __renderblock() {
        $blockId = $this->getParam('blockid');
        $side = $this->getParam('side');

        $block = explode('|', $blockId);

        $blockId = $side . '___' . str_replace('|', '___', $blockId);

        if ($block [0] == 'block') {
            $objBlocks = $this->getObject('blocks', 'blocks');
            echo '<div id="' . $blockId . '" class="block highlightblock">' . $objBlocks->showBlock($block [1], $block [2], NULL, 20, TRUE, FALSE) . '</div>';
        }
        if ($block [0] == 'dynamicblock') {
            echo '<div id="' . $blockId . '" class="block highlightblock">' . $this->objDynamicBlocks->showBlock($block [1]) . '</div>';
        } else {
            echo '';
        }
    }

    /**
     * Method to add a block
     */
    protected function __addblock() {
        $blockId = $this->getParam('blockid');
        $side = $this->getParam('side');

        $block = explode('|', $blockId);

        if ($block [0] == 'block' || $block [0] == 'dynamicblock') {
            // Add Block
            $result = $this->objContextBlocks->addBlock($blockId, $side, $this->contextCode, $block [2]);

            if ($result == FALSE) {
                echo '';
            } else {
                echo $result;
            }
        } else {
            echo '';
        }
    }

    /**
     * Method to remove a context block
     */
    protected function __removeblock() {
        $blockId = $this->getParam('blockid');

        $result = $this->objContextBlocks->removeBlock($blockId);

        if ($result) {
            echo 'ok';
        } else {
            echo 'notok';
        }
    }

    /**
     * Method to move a context block
     */
    protected function __moveblock() {
        $blockId = $this->getParam('blockid');
        $direction = $this->getParam('direction');

        if ($direction == 'up') {
            $result = $this->objContextBlocks->moveBlockUp($blockId, $this->contextCode);
        } else {
            $result = $this->objContextBlocks->moveBlockDown($blockId, $this->contextCode);
        }

        if ($result) {
            echo 'ok';
        } else {
            echo 'notok';
        }
    }

    /**
     * Method to show a form to update context settings
     */
    protected function __updatesettings() {
        $context = $this->objContext->getContextDetails($this->contextCode);
        $objContextForms = $this->getObject('contextforms');

        $form = $objContextForms->editContextForm($context);
        $this->setVarByRef('form', $form);

        return 'editcontextsettings_tpl.php';
    }

    /**
     * Method to Update a Context Settings
     */
    protected function __updatecontext() {
        $contextCode = $this->getParam('contextcode');
        $title = $this->getParam('title');
        $status = $this->getParam('status');
        $access = $this->getParam('access');
        $about = $this->getParam('about');
        $image = $this->getParam('imageselect');


        //$emailalert =

        //$alerts = '';
        //if ($emailalert == 'on') {
            //$alerts.='e';
        //}
        $alerts = $this->getParam('emailalertopt') == 'on'?'1':'0';
        if ($contextCode == $this->contextCode && $title != '') {
            $result = $this->objContext->updateContext(
                            $contextCode,
                            $title,
                            $status,
                            $access,
                            $about,
                            FALSE,
                            'Y',
                            $alerts);

            if ($image != '') {
                $objContextImage = $this->getObject('contextimage', 'context');
                $objContextImage->setContextImage($contextCode, $image);
            }

            return $this->nextAction('controlpanel');
        } else {
            return $this->nextAction('updatesettings', array('error' => 'inccompletefields'));
        }
    }

    /**
     * Add Context Search
     */
    protected function __search() {
        $search = $this->getParam('search');

        $objSearchResults = $this->getObject('searchresults', 'search');
        $searchResults = $objSearchResults->displaySearchResults($search, NULL, $this->contextCode);

        $this->setVarByRef('searchResults', $searchResults);
        $this->setVarByRef('searchText', $search);

        return 'searchresults_tpl.php';
    }

    /**
     * Method to display a context created message
     *
     * @access protected
     */
    protected function __contextcreatedmessage() {
        echo '<h3>' . $this->objLanguage->code2Txt('mod_context_congratscontextcreated', 'context', NULL, 'Congratulations! Your [-context-] has been created') . '.</h3>
        <p>' . $this->objLanguage->code2Txt('mod_context_contextcreatedmessage1', 'context', NULL, 'This is the home page of your [-context-] You can modify the contents of the page, by clicking "Turn Editing On"') . '.
        ' . $this->objLanguage->languageText('mod_context_contextcreatedmessage2', 'context', 'This will allow you to add different types of content blocks to this page') . '.</p>
        <p>' . $this->objLanguage->code2Txt('mod_context_contextcreatedmessage3', 'context', NULL, 'To add [-readonlys-] to your [-context-], or to add/remove [-context-] plugins, go to the [-context-] control panel') . '.</p>
        ';
    }

    /**
     * Method to get contexts via ajax
     */
    protected function __ajaxgetcontexts() {
        $letter = $this->getParam('letter');

        $contexts = $this->objContext->getContextStartingWith($letter);

        if (count($contexts) == 0) {

        } else {
            $objDisplayContext = $this->getObject('displaycontext', 'context');

            foreach ($contexts as $context) {
                echo $objDisplayContext->formatContextDisplayBlock($context, FALSE, FALSE) . '<br />';
            }
        }
    }

    /**
     * Method to get user contexts via ajax
     */
    protected function __ajaxgetusercontexts() {
        $objUserContext = $this->getObject('usercontext', 'context');
        $contexts = $objUserContext->getUserContext($this->objUser->userId());

        $con = array();
        if (count($contexts) > 0) {
            foreach ($contexts as $context) {
                $con[] = $this->objContext->getContext($context);
            }
        }
        $contexts = $con;
        if (count($contexts) == 0) {

        } else {
            $objDisplayContext = $this->getObject('displaycontext', 'context');

            foreach ($contexts as $context) {
                echo $objDisplayContext->formatContextDisplayBlock($context, FALSE, FALSE) . '<br />';
            }
        }
    }

    /**
     * Added by Paul Mungai
     * Method to list all user contexts
     * @access protected
     */
    protected function __jsonusercontexts() {
        $ctstart = $this->getParam('start');
        if (empty($ctstart)) {
            $ctstart = 0;
        }
        $ctlimit = $this->getParam('limit');
        if (empty($ctlimit)) {
            $ctlimit = 50;
        }
        $objUserContext = $this->getObject('usercontext', 'context');
        $objDisplayContext = $this->getObject('displaycontext', 'context');
        $userContexts = $objUserContext->jsonUserCourses($this->objUser->userId(), $ctstart, $ctlimit);
        if (count($userContexts) > 0) {
            echo $objDisplayContext->jsonContextOutput($userContexts);
            exit(0);
        }
    }

    /**
     * Method to get all contexts via ajax
     */
    protected function __ajaxgetallcontexts() {
        $objUtils = $this->getObject('utilities');
        echo $objUtils->searchBlock();
    }

    /**
     * Method to leave a context
     *
     * @access protected
     */
    protected function __searchcontext() {
        $objUtils = $this->getObject('utilities');
        $items = $objUtils->getContextList();

        $q = $this->getParam('q');
        foreach ($items as $key => $value) {
            if (strpos(strtolower($key), $q) !== false) {
                echo "$key|$value\n";
            }
        }
        exit(0);
    }

    /**
     * Method to leave a context
     *
     * @access protected
     */
    protected function __searchusers() {
        $objUtils = $this->getObject('utilities');
        $items = $objUtils->getUserList();

        $q = $this->getParam('q');
        foreach ($items as $key => $value) {
            if (strpos(strtolower($key), $q) !== false) {
                echo "$key|$value\n";
            }
        }
        exit(0);
    }

    /**
     * Method to leave a context
     *
     * @access protected
     */
    protected function __leavecontext() {

        if ($this->eventsEnabled) {
            $message = $this->objUser->getsurname() . ' ' . $this->objLanguage->languageText('mod_context_hasleft', 'context') . ' ' . $this->objContext->getContextCode();
            $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                'link' => $this->uri(array()),
                'contextcode' => $this->objContext->getContextCode(),
                'author' => $this->objUser->fullname(),
                'description' => $message));
            $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                'link' => $this->uri(array()),
                'contextcode' => null,
                'author' => $this->objUser->fullname(),
                'description' => $message));
        }
        $this->objContext->leaveContext();

        return $this->nextAction(NULL, NULL, '_default');
    }

    /**
     * Method to format a context
     *
     * @access protected
     */
    protected function __ajaxgetselectedcontext() {
        $objUtils = $this->getObject('utilities');
        echo $objUtils->formatSelectedContext($this->getParam('contextcode'));
        exit(0);
    }

    /**
     * Method to format the user context list
     *
     * @access protected
     */
    protected function __ajaxgetselectedusercontext() {
        $objUtils = $this->getObject('utilities');
        echo $objUtils->formatUserContext($this->getParam('username'));
        exit(0);
    }

    /**
     * Method to list all he context
     *
     * @access protected
     */
    protected function __ajaxlistcontext() {
        $objUtils = $this->getObject('utilities');
        echo $objUtils->listContexts();
        exit(0);
    }

    /**
     * Method to list all the context
     *
     * @access protected
     */
    protected function __jsonlistcontext() {
        $objUtils = $this->getObject('utilities');
        echo $objUtils->jsonListContext($this->getParam('start'), $this->getParam('limit'));
        exit(0);
    }

    /**
     * Method to list all the context
     *
     * @access protected
     */
    protected function __jsonlistallcontext() {
        $objUtils = $this->getObject('utilities');
        echo $objUtils->jsonListAllContext();
        exit(0);
    }

    protected function __jsongetcontexts() {
        $objUtils = $this->getObject('utilities');
        echo $objUtils->getContext($this->getParam('start'), $this->getParam('limit'));
        exit(0);
    }

    public function __transfercontextusers() {
        $start = '0';
        $limit = '100';
        $this->objGroups = $this->getObject('managegroups', 'contextgroups');
        $data = $this->objGroups->usercontextcodeslimited($this->objUser->userId(), $start, $limit);
        $this->setVarByRef('data', $data);
        return "transfercontextusers_tpl.php";
    }

    public function __savetransfercontextusers() {
        $context1 = $this->getParam('context1');
        $context2 = $this->getParam('context2');
        if ($context1 == $context2) {
            $message = ucwords($this->objLanguage->code2Txt('mod_context_transferfail', 'context', null, 'Transfer failed. You selected same [-context-] twice.'));
            $this->setVarByRef("message", $message);
            return "confirmusertransfer_tpl.php";
        }
        $objUtils = $this->getObject('utilities');
        $objUtils->copyStudentsFromOneCourseToNext($context1, $context2);
        $message = ucwords($this->objLanguage->code2Txt('mod_context_complete', 'context', null, 'Transfer complete.'));
        $this->setVarByRef("message", $message);
        return "confirmusertransfer_tpl.php";
    }

    /**
     * for displaying user activity
     * @return <type>
     */
    function __showuseractivitybymodule() {
        $startDate = $this->getParam('startdate');
        $endDate = $this->getParam('enddate');
        $studentsonly = $this->getParam('studentsonly');
        $module = $this->getParam('moduleid');
        $objUserActivity = $this->getObject('dbuseractivity');
        $contextcode=  $this->getParam("contextcode");
        if($contextcode == null){
            $contextcode=$this->contextCode;
        }

        $groupOps = $this->getObject('groupops', 'groupadmin');
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $contextGroupId = $objGroups->getId($contextcode . '^Students');
        $usersInContext = $groupOps->getUsersInGroup($contextGroupId);

        $data = $objUserActivity->getUserActivityByModule($startDate, $endDate, $module, $studentsonly, $usersInContext, $contextcode);
        $this->setVarByRef("data", $data);
        $this->setVarByRef("startdate", $startDate);
        $this->setVarByRef("enddate", $endDate);
        $this->setVarbyRef("modulename", $module);
        return "useractivitybymodule_tpl.php";
    }

    /**
     * for displaying user activity
     * @return <type>
     */
    function __viewUserActivityById() {
        $startDate = $this->getParam('startdate');
        $endDate = $this->getParam('enddate');
        $module = $this->getParam('moduleid');
        $userid = $this->getParam('userid');
        $objUserActivity = $this->getObject('dbuseractivity');
        $data = $objUserActivity->getUserActivityById($startDate, $endDate, $module, $userid, $this->contextCode);
        $this->setVarByRef("data", $data);
        $this->setVarByRef("startdate", $startDate);
        $this->setVarByRef("enddate", $endDate);
        $this->setVarbyRef("modulename", $module);
        $this->setVarbyRef("userid", $userid);
        return "useractivitybyid_tpl.php";
    }

    /**
     *  returns a date template
     * @return <type>
     */
    function __selectUserActivityByModuleDates() {
        $action = "showuseractivitybymodule";
        $title = $this->objLanguage->languageText('mod_context_useractivity', 'context', 'User activity');
        $this->setVarByRef("action", $action);
        $this->setVarByRef("title", $title);
        return "selectdates_tpl.php";
    }

    function __selecttoolsactivitydates() {
        $action = "showtoolsactivity";
        $title = $this->objLanguage->languageText('mod_context_toolsactivity', 'context', 'Tools activity');
        $this->setVarByRef("action", $action);
        $this->setVarByRef("title", $title);
        return "selectdates_tpl.php";
    }

    function __showtoolsactivity() {
        $startDate = $this->getParam('startdate');
        $endDate = $this->getParam('enddate');

        $objModules = $this->getObject('modules', 'modulecatalogue');
        $plugins = $objModules->getListContextPlugins();

        $contextcode=  $this->getParam("contextcode");
        if($contextcode == null){
            $contextcode=$this->contextCode;
        }
        $context=  $this->objContext->getContext($contextcode);
        $objUserActivity = $this->getObject('dbuseractivity');
        $data = $objUserActivity->getToolsActivity($startDate, $endDate, $contextcode,$plugins);
        $this->setVarByRef("data", $data);
        $this->setVarByRef("startdate", $startDate);
        $this->setVarByRef("enddate", $endDate);
        $this->setVarByRef("coursetitle",$context['title']);
        $this->setVarByRef("contextcode",$context['contextcode']);

        return "toolsactivity_tpl.php";
    }

    function __selectcontextsactivitydates() {
        $action = "showcontextactivity";
        $title = $this->objLanguage->code2Txt('mod_context_allcoursesacitivity', 'context', NULL, 'All [-contexts-] activity');
        $this->setVarByRef("action", $action);
        $this->setVarByRef("title", $title);
        return "selectdates_tpl.php";
    }

    function __showcontextactivity() {
        $startDate = $this->getParam('startdate');
        $endDate = $this->getParam('enddate');
        $contexts = $this->objContext->getListOfContext();

        $objUserActivity = $this->getObject('dbuseractivity');
        $data = $objUserActivity->getContextsActivity($startDate, $endDate, $contexts);
        $this->setVarByRef("data", $data);
        $this->setVarByRef("startdate", $startDate);
        $this->setVarByRef("enddate", $endDate);
        return "contextsactivity_tpl.php";
    }

}

?>
