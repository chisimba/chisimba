<?php

/**
 * Context Admin Controller
 *
 * Controller class for the Context Creation/Management in Chisimba
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
 * @package   contextadmin
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
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
 * Context Admin Controller
 *
 * Controller class for the Context Creation/Management in Chisimba
 *
 * @category  Chisimba
 * @package   contextadmin
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class contextadmin extends controller {

    /**
     * The user Object
     *
     * @var object $objUser
     */
    public $objUser;
    /**
     * The Context Object
     *
     * @var object $objContext
     */
    public $objContext;
    /**
     * The Learner Outcomes Object
     *
     * @var object $objDBLearnerOutcomes
     */
    public $objDBLearnerOutcomes;
    /**
     * The User Context Object
     *
     * @var object $objUserContext
     */
    public $objUserContext;
    /**
     * The Language Object
     *
     * @var object $objLanguage
     */
    public $objLanguage;

    /**
     * Constructor
     */
    public function init() {
        $this->objUser = $this->getObject('user', 'security');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objDBLearnerOutcomes = $this->getObject('dbcontext_learneroutcomes', 'context');
        $this->objUserContext = $this->getObject('usercontext', 'context');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
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
        $this->setLayoutTemplate('contextadmin_layout.php');

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
     *
     */
    private function getMethod(& $action) {
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
    private function validAction(& $action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Context Admin Home
     */
    private function __home() {
        $content = $this->objUserContext->getUserContextsFormatted($this->objUser->userId());

        $this->setVarByRef('content', $content);

        $title = $this->objLanguage->code2Txt('phrase_mycourses', 'system', NULL, 'My [-contexts-]');

        $this->setVarByRef('title', $title);

        return 'main.php';
    }

    /**
     * Method to create a new context
     */
    private function __add() {
        $this->setVar('mode', 'add');
        return 'step1.php';
    }

    /**
     * Method to save step 1 of creating a context - context details
     */
    private function __savestep1() {
        $mode = $this->getParam('mode');
        $contextCode = $this->getParam('contextcode');
        $title = $this->getParam('title');
        $canvas = $this->getParam('canvas');
        $showcomment = $this->getParam('showcomment');

        $status = $this->getParam('status');
        $access = $this->getParam('access');
        $about = '';

        $emailalert = $this->getParam('emailalertopt');
        $alerts = '';
        if ($emailalert == 'on') {
            $alerts.='e';
        }

        if ($contextCode == '') {
            $result = FALSE;
        } else {
            $result = $this->objContext->createContext($contextCode, $title, $status, $access, $about, '', $showcomment, $alerts, $canvas);
        }

        // If successfully created
        if ($result) {

            $this->setSession('fixup', NULL);
            $this->setSession('contextCode', $contextCode);

            $objContextBlocks = $this->getObject('dbcontextblocks', 'context');
            $objContextBlocks->addBlock('block|aboutcontext|context', 'middle', $contextCode, 'context');

            return $this->nextAction('step2', array('mode' => 'add', 'contextcode' => $contextCode));
        } else { // Else fix up errors
            $fixup = array('contextcode' => $contextCode, 'title' => $title, 'status' => $status, 'showcomment' => $showcomment, 'access' => $access);
            $this->setSession('fixup', $fixup);

            return $this->nextAction('add', array('mode' => 'fixup', 'contextcode' => $contextCode));
        }
    }

    /**
     * Step 2 of Creating a Context - Adding About Info and Image
     */
    private function __step2() {
        $contextCode = $this->getSession('contextCode');

        $context = $this->objContext->getContext($contextCode);

        if ($context == FALSE) {
            return $this->nextAction(NULL);
        }
        $this->setSession('contextCode', $contextCode);
        $this->setVar('mode', $this->getParam('mode'));
        $this->setVar('contextCode', $contextCode);
        $this->setVar('context', $context);

        return 'step2.php';
    }

    /**
     * Method to save step 2 of creating a context - about info and image
     */
    private function __savestep2() {
        if ($this->getParam('contextCode') != $this->getSession('contextCode')) {
            return $this->nextAction(NULL);
        } else {
            $contextCode = $this->getSession('contextCode');
            $about = $this->getParam('about');
            $image = $this->getParam('imageselect');
            $mode = $this->getParam('mode');

            // Check Context Image
            if ($image != '') {
                $objContextImage = $this->getObject('contextimage', 'context');
                $objContextImage->setContextImage($contextCode, $image);
            }

            $this->objContext->updateAbout($contextCode, $about);
            $this->setSession('contextCode', $contextCode);
            return $this->nextAction('step3', array('mode' => $mode, 'contextCode' => $contextCode));
        }
    }

    /**
     * Step 3 of Creating a Context - Adding Goals
     */
    private function __step3() {
        if ($this->getParam('contextCode') != $this->getSession('contextCode')) {
            return $this->nextAction(NULL);
        } else {
            //Get Mode
            $mode = $this->getParam('mode');
            //Fetch contextCode
            $contextCode = $this->getSession('contextCode');
            $context = $this->objContext->getContext($contextCode);
            //Get context Learner Outcomes if its an edit
            if ($mode == 'edit') {
                $contextLO = $this->objDBLearnerOutcomes->getContextOutcomes($contextCode);
            } else {
                $contextLO = "";
            }
            if ($context == FALSE) {
                return $this->nextAction(NULL);
            }
            $this->setVar('mode', $mode);
            $this->setVar('contextCode', $contextCode);
            $this->setVar('context', $context);
            $this->setVar('contextLO', $contextLO);
            $this->setVar('outcomesCount', $this->getParam("outcomesCount", Null));
            return 'step3.php';
        }
    }

    /**
     * Method to save step 3 of creating a context - goals
     */
    private function __savestep3() {
        if ($this->getParam('contextCode') != $this->getSession('contextCode')) {
            return $this->nextAction(NULL);
        } else {
            $actionDelete = $this->getParam('deleteoutcomes', Null);
            $actionUpdate = $this->getParam('savecontext', Null);
            $lodrops = $this->getParam('lodrops', Null);
            $outCount = $this->getParam('outcount', Null);
            $addFields = $this->getParam('addfields', Null);
            $backPressed = $this->getParam('back', Null);
            if ($backPressed == "Back") {
                //Delete and go back to step 3
                $contextCode = $this->getSession('contextCode');
                //Get number of outcomes
                $outCount = $this->getParam('loAddCount', 0);
                $loEditdrops = $this->getParam('loEditCount', 0);
                $mode = $this->getParam('mode');
                //String to hold fetched LO's
                $getAddLO = Null;
                $getEditLO = Null;
                //Add if there are new outcomes, add
                if ($outCount > 0) {
                    $getAddLO = $this->getLearnerOutcomes($outCount, "add", $contextCode);
                }
                //If edit, fetch the new values & update or delete if textinput is empty
                if ($mode == 'edit' && $loEditdrops > 0) {
                    $getEditLO = $this->getLearnerOutcomes($loEditdrops, "edit", $contextCode);
                }
                //check If there are records to add/update
                if (array($getAddLO)) {
                    $addLO = $this->addLearnerOutcomes($contextCode, $getAddLO);
                }
                $goals = $this->getParam('goals');
                $this->objContext->updateGoals($contextCode, $goals);
                return $this->nextAction('step2', array('mode' => "edit", 'contextCode' => $contextCode));
            }
            //Update if not delete action and move to next step
            if ($actionDelete != Null) {
                //Delete and go back to step 3
                $contextCode = $this->getSession('contextCode');
                //Get number of outcomes
                $outCount = $this->getParam('loAddCount', 0);
                $loEditdrops = $this->getParam('loEditCount', 0);
                $mode = $this->getParam('mode');
                //String to hold fetched LO's
                $getAddLO = Null;
                $getEditLO = Null;
                //Add if there are new outcomes, add
                if ($outCount > 0) {
                    $getAddLO = $this->getLearnerOutcomes($outCount, "add", $contextCode);
                }
                //If edit, fetch the new values & update or delete if textinput is empty
                if ($mode == 'edit' && $loEditdrops > 0) {
                    $getEditLO = $this->getLearnerOutcomes($loEditdrops, "edit", $contextCode);
                }
                //check If there are records to add/update
                if (array($getAddLO)) {
                    $addLO = $this->addLearnerOutcomes($contextCode, $getAddLO);
                }
                $goals = $this->getParam('goals');
                $this->objContext->updateGoals($contextCode, $goals);
                return $this->nextAction('step3', array('mode' => $mode, 'contextCode' => $contextCode, 'outcomesCount' => $lodrops, 'messagesuccess' => 'success'));
            } else if ($addFields != Null) {
                //Delete and go back to step 3
                $contextCode = $this->getSession('contextCode');
                //Get number of outcomes
                //$lodrops = $this->getParam('lodrops', 0);
                $loEditdrops = $this->getParam('loEditCount', 0);
                $mode = $this->getParam('mode');
                //String to hold fetched LO's
                $getAddLO = Null;
                $getEditLO = Null;
                //Add if there are new outcomes, add
                if ($outCount > 0) {
                    $getAddLO = $this->getLearnerOutcomes($outCount, "add", $contextCode);
                }
                //If edit, fetch the new values & update or delete if textinput is empty
                if ($mode == 'edit' && $loEditdrops > 0) {
                    $getEditLO = $this->getLearnerOutcomes($loEditdrops, "edit", $contextCode);
                }
                //check If there are records to add/update
                if (array($getAddLO)) {
                    $addLO = $this->addLearnerOutcomes($contextCode, $getAddLO);
                }
                $goals = $this->getParam('goals');
                $this->objContext->updateGoals($contextCode, $goals);
                return $this->nextAction('step3', array('mode' => $mode, 'contextCode' => $contextCode, 'outcomesCount' => $lodrops, 'messagesuccess' => 'success'));
            } else {
                $contextCode = $this->getSession('contextCode');
                //Get number of outcomes
                $outCount = $this->getParam('loAddCount', 0);
                $loEditdrops = $this->getParam('loEditCount', 0);
                $mode = $this->getParam('mode');

                //String to hold fetched LO's
                $getAddLO = Null;
                $getEditLO = Null;
                //Add if there are new outcomes, add
                if ($outCount > 0) {
                    $getAddLO = $this->getLearnerOutcomes($outCount, "add", $contextCode);
                }
                //If edit, fetch the new values & update or delete if textinput is empty
                if ($mode == 'edit' && $loEditdrops > 0) {
                    $getEditLO = $this->getLearnerOutcomes($loEditdrops, "edit", $contextCode);
                }
                //check If there are records to add/update
                if (array($getAddLO)) {
                    $addLO = $this->addLearnerOutcomes($contextCode, $getAddLO);
                }
                $goals = $this->getParam('goals');
                $this->objContext->updateGoals($contextCode, $goals);
                return $this->nextAction('step4', array('mode' => $mode, 'contextcode' => $contextCode));
            }
        }
    }

    /**
     * Method to get all the posted learner outcomes
     *
     * @param string $lodrops The number of outcomes added
     * @param string $mode Add/Edit mode
     * @return array : The submitted learner outcomes
     */
    private function getLearnerOutcomes($lodrops=Null, $mode="add", $contextCode) {
        $count = 1;

        //Get all context Learner Outcomes and for each, get updated value
        $contextLO = $this->objDBLearnerOutcomes->getContextOutcomes($contextCode);
        $existingOutcomes = count($contextLO);
        if ($existingOutcomes > 0) {
            $count = $existingOutcomes + 1;
            $lodrops = $existingOutcomes + $lodrops;
        }
        //Array to store the outcomes
        $loArray = array();
        if ($mode != "edit") {
            while ($count <= $lodrops) {
                $learneroutcome = $this->getParam('learneroutcome_' . $count);
                //Check to avoid storing empty record
                if (!empty($learneroutcome)) {
                    $loArray[] = $learneroutcome;
                }
                $count++;
            }
        } else if ($mode == "edit") {
            foreach ($contextLO as $thisLO) {
                //Get Textinput name on step 3 form for this outcome
                $loTxtInput = $this->getParam($thisLO["id"]);
                $learneroutcome = $this->getParam("update" . $loTxtInput);
                $delLO = $this->getParam("delete" . $loTxtInput);
                //If empty LO delete record, otherwise update with new value
                if ($delLO == "on") {
                    $id = $this->objDBLearnerOutcomes->deleteSingle($thisLO["id"]);
                } elseif (!empty($learneroutcome)) {
                    $id = $this->objDBLearnerOutcomes->updateSingle($thisLO["id"], $learneroutcome);
                    $loArray[] = $learneroutcome;
                }
            }
        }
        return $loArray;
    }

    /**
     * Method to get all the posted learner outcomes
     *
     * @param string $contextCode
     * @param array $lodrops
     * @return array : The id's of submitted learner outcomes
     */
    private function addLearnerOutcomes($contextcode, $loList) {
        //Array to hold the Id's
        $arrId = array();
        if (!empty($loList)) {
            //Insert each record to LO table
            foreach ($loList as $lo) {
                $id = $this->objDBLearnerOutcomes->insertSingle($contextcode, $lo);
                $arrId[] = $id;
            }
        }
        return $arrId;
    }

    /**
     * Step 4 - Selecting modules to be used in context
     */
    private function __step4() {
        $contextCode = $this->getSession('contextCode');
        //Get Context Title
        $contextTitle = $this->objContext->getTitle($contextCode);
        if (empty($contextTitle))
            $contextTitle = $this->objContext->getMenuText($contextCode);
        if ($contextTitle == FALSE) {
            return $this->nextAction(NULL);
        }
        $this->setVar('mode', $this->getParam('mode'));
        $this->setVar('contextCode', $contextCode);
        $this->setVar('contextTitle', $contextTitle);

        $objContextModules = $this->getObject('dbcontextmodules', 'context');
        $objModules = $this->getObject('modules', 'modulecatalogue');

        $contextModules = $objContextModules->getContextModules($contextCode);
        $plugins = $objModules->getListContextPlugins();

        $this->setVarByRef('contextModules', $contextModules);
        $this->setVarByRef('plugins', $plugins);

        return 'step4.php';
    }

    /**
     * Method to save step 3 of creating a context - module selection
     */
    private function __savestep4() {
        if ($this->getParam('contextCode') != $this->getSession('contextCode')) {
            return $this->nextAction(NULL);
        } else {
            $plugins = $this->getParam('plugins');
            $contextCode = $this->getParam('contextCode');
            $mode = $this->getParam('mode');

            $objContextModules = $this->getObject('dbcontextmodules', 'context');
            $objContextModules->deleteModulesForContext($contextCode);


            if (is_array($plugins) && count($plugins) > 0) {
                foreach ($plugins as $plugin) {
                    $objContextModules->addModule($contextCode, $plugin);
                }
            }

            if ($mode == 'add') {
                return $this->nextAction(NULL, array('message' => 'contextsetup'), 'context');
            } else {
                $this->setSession('contextcode', NULL);
                $this->setSession('displayconfirmationmessage', TRUE);
                return $this->nextAction(NULL, array('message' => 'contextupdated', 'contextcode' => $contextCode));
            }
        }
    }

    /**
     * Method to show a form to update a context
     */
    private function __edit() {
        $contextCode = $this->getParam('contextcode');
        $context = $this->objContext->getContext($contextCode);
        
        if ($context == FALSE) {
            return $this->nextAction(NULL);
        }

        // Todo - Check Permissions
        $this->setVarByRef('context', $context);
        $this->setSession('contextCode', $contextCode);

        $this->setVar('mode', 'edit');
        return 'step1.php';
    }

    /**
     * Method to update the details of a context
     */
    private function __updatecontext() {
        $contextCode = $this->getParam('editcontextcode');
        $title = $this->getParam('title');
        $canvas = $this->getParam('canvas');
       
        $status = $this->getParam('status');
        $showcomment = $this->getParam('showcomment');
        $access = $this->getParam('access');
        $goals = $this->getParam('goals');
        $mode = $this->getParam('mode');
        $emailalert = $this->getParam('emailalertopt');
        $alerts = '';
        if ($emailalert == 'on') {
            $alerts.='e';
        }
        if ($contextCode != $this->getSession('contextCode')) {
            $newContext = $this->getSession('contextCode');
            return $this->nextAction(NULL, array('message' => 'contextswitch', 'context1' => $contextCode, 'context2' => $newContext));
        } else {

            $context = $this->objContext->getContext($contextCode);

            if ($context == FALSE) {
                return $this->nextAction(NULL, array('message' => 'editnonexistingcontext'));
            } else {
                /**
                 * $contextCode,
            $title=FALSE,
            $status=FALSE,
            $access=FALSE,
            $about=FALSE,
            $goals=FALSE,
            $showcomment=FALSE,
            $alerts='',
            $lastaccessed=FALSE,
            $canvas=""
                 */
                $this->objContext->updateContext(
                        $contextCode,
                        $title,
                        $status,
                        $access,
                        $context['about'],
                        $goals,
                        $showcomment,
                        $alerts, null,$canvas);

                return $this->nextAction('step2', array('mode' => 'edit'));
            }
        }
    }

    /**
     * Method to delete a context
     */
    private function __delete() {
        $contextCode = $this->getParam('contextcode');

        $context = $this->objContext->getContext($contextCode);

        if ($context == FALSE) {
            return $this->nextAction(NULL, array('error' => 'deletenoneexistingcourse', 'context' => $contextCode));
        } else {
            $this->setVarByRef('context', $context);
            $this->setSession('deletecontext', $context['contextcode']);

            return 'delete.php';
        }
    }

    /**
     * Method to process delete confirmation
     */
    private function __deleteconfirm() {

        $contextCode = $this->getParam('contextcode');

        $context = $this->objContext->getContext($contextCode);

        if ($context == FALSE) {
            return $this->nextAction(NULL, array('error' => 'deletenoneexistingcourse', 'context' => $contextCode));
        } else {

            if ($contextCode != $this->getSession('deletecontext')) {
                return $this->nextAction(NULL, array('error' => 'incompletedelete', 'context' => $contextCode));
            }

            $deleteConfirm = $this->getParam('deleteconfirm', 'no');
            if ($deleteConfirm == 'yes') {
                $result = $this->objContext->deleteContext($contextCode);
                $result = $result ? 'y' : 'n';
                return $this->nextAction(NULL, array('message' => 'contextdeleted', 'context' => $contextCode, 'title' => $context['title'], 'result' => $result));
            } else {
                return $this->nextAction(NULL, array('message' => 'deletecancelled', 'context' => $contextCode));
            }
        }
    }

    /**
     * Ajax function to detect whether a context code has been taken already or not
     */
    private function __checkcode() {
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
                if ($this->objContext->contextExists($code)) {
                    echo 'exists';
                } else {
                    echo 'ok';
                }
        }
    }

    /**
     * Ajax function to remove a context image
     */
    private function __removeimage() {
        $contextCode = $this->getParam('contextcode');

        if ($contextCode != $this->getSession('contextCode')) {
            echo 'notok';
        } else {
            $objContextImage = $this->getObject('contextimage', 'context');
            if ($objContextImage->removeContextImage($contextCode)) {
                echo 'ok';
            } else {
                echo 'notok';
            }
        }
    }

    /**
     * Method to browse for contexts
     */
    private function __browseother() {
        $letter = $this->getParam('letter', 'A');

        $results = $this->objContext->getContextStartingWith($letter);

        if (count($results) == 0) {
            $this->setVar('content', 'No search results for ' . $letter);
        } else {
            $this->setVarByRef('contexts', $results);
        }
        $this->setVar('title', ucfirst($this->objLanguage->code2Txt('mod_contextadmin_contextsstartingwith', 'contextadmin', NULL, '[-contexts-] starting with')) . ' ' . $letter);

        return 'main.php';
    }

    /**
     * Method to search for contexts
     */
    private function __search() {
        $search = $this->getParam('search');
        $results = $this->objContext->searchContext($search);

        if (count($results) == 0) {
            $this->setVar('content', $this->objLanguage->languageText('mod_contextadmin_nosearchresultsfor', 'contextadmin', 'No search results for') . ' ' . $search);
        } else {
            $this->setVarByRef('contexts', $results);
        }
        $this->setVar('title', $this->objLanguage->languageText('phrase_searchresultsfor', 'system', 'Search Results for') . ' <em>' . $search . '</em>');

        return 'main.php';
    }

    /**
     * returns a template that allows uploading of themes
     * @return <type>
     */
    public function __uploadtheme() {
        return "uploadtheme_tpl.php";
    }

    /**
     * handles a theme uploaded from filemanager
     * @return <type> 
     */
    public function __saveuploadedtheme() {

        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objContext->getContextCode();
        $objFileUpload = $this->getObject('uploadinput', 'filemanager');
        $path = '/context/' . $this->contextCode . '/canvases';
        $objFileUpload->customuploadpath = $path;
        $objFileUpload->enableOverwriteIncrement = TRUE;
        $results = $objFileUpload->handleUpload('fileupload');
        $file = $_FILES[$objFileUpload->name];
        $filename = $file['name'];

        // Technically, FALSE can never be returned, this is just a precaution
        // FALSE means there is no fileinput with that name
        if ($results == FALSE) {
            return $this->nextAction('view', array('id' => $this->getParam('id'), 'error' => 'unabletoupload'));
        } else {
            // If successfully Uploaded
            if ($results['success']) {
                $objZip = $this->newObject('wzip', 'utilities');
                $objZip->unzip($this->objConfig->getcontentBasePath() . '/' . $path . '/' . $filename, $this->objConfig->getcontentBasePath() . '/' . $path );
                return $this->nextAction('edit', array('contextcode' => $this->contextCode));
            } else {
                // If not successfully uploaded
                return $this->nextAction('uploadtheme', array('error' => $results['reason']));
            }
        }
    }

    private function removeExtension($strName) {
        $ext = strrchr($strName, '.');

        if ($ext !== false) {
            $strName = substr($strName, 0, -strlen($ext));
        }
        return $strName;
    }

}

?>
