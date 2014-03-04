<?php

/* -------------------- eportflio class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller for the ePortfolio Module
 * @package eportfolio
 * @copyright 2008, University of Nairobi & AVOIR Project
 * @license GNU GPL
 * @author Paul Mungai
 *
 */
class eportfolio extends controller {

    /**
     * @var groupadminmodel Object reference.
     */
    public $objConfig;
    public $objLanguage;
    public $objUserAdmin;
    public $objUser;
    public $objFile;
    public $objFolders;
    public $objCleanUrl;
    public $objDate;
    public $objUserContext;
    public $objContextUser;
    public $objPopupcal;
    public $objUrl;
    public $_objGroupAdmin;
    public $_objManageGroups;
    public $objGroupsOps;
    public $objGroupUsers;
    public $_objDBContext;
    public $objContextUsers;
    public $_objDBAssgnment;
    public $_objDBEssay;
    public $objFSContext;
    public $objMysqlxml;
    public $lectGroupId;
    public $objDbAddressList;
    public $objDbContactList;
    public $objDbDemographicsList;
    public $objDbActivityList;
    public $objDbAffiliationList;
    public $objDbTranscriptList;
    public $objDbEmailList;
    public $objDbQclList;
    public $objDbGoalsList;
    public $objDbCompetencyList;
    public $objDbInterestList;
    public $objDbReflectionList;
    public $objDbAssertionList;
    public $objDbProductList;
    public $objDbCategoryList;
    public $objDbComment;
    public $objDbCategorytypeList;
    public $objGetall;
    public $objExport;
    public $userId;
    public $objEportfolioActivityStreamer;

    /**
     * Constructor
     */
    public function init() {
        $this->objConfig = &$this->getObject('altconfig', 'config');
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->objUserAdmin = &$this->getObject('useradmin_model2', 'security');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objFile = &$this->getObject('dbfile', 'filemanager');
        $this->objFolders = $this->getObject('dbfolder', 'filemanager');
        $this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $this->objDate = &$this->newObject('dateandtime', 'utilities');
        //$this->objUserContext = $this->getObject('utils', 'contextpostlogin');
        $this->objContextUser = $this->getObject('usercontext', 'context');
        $this->objPopupcal = $this->newObject('datepickajax', 'popupcalendar');
        $this->objUrl = $this->getObject('url', 'strings');
        $this->_objGAModel = $this->newObject('gamodel', 'groupadmin');
        $this->_objGroupAdmin = $this->newObject('groupadminmodel', 'groupadmin');
        $this->_objManageGroups = &$this->newObject('managegroups', 'contextgroups');
        //TEMPORARY Check if class groupops exists
        if (file_exists($this->objConfig->getsiteRootPath() . "core_modules/groupadmin/classes/groupops_class_inc.php")) {
            $this->objGroupsOps = $this->getObject('groupops', 'groupadmin');
        }
        $this->objGroupUsers = $this->getObject('groupusersdb', 'groupadmin');
        $this->_objDBContext = &$this->newObject('dbcontext', 'context');
        $this->objContextUsers = $this->getObject('contextusers', 'contextgroups');
        //$this->_objDBAssgnment = &$this->newObject('dbassignment','assignment');
        $this->_objDBEssay = &$this->newObject('dbessay_book', 'essay');
        $this->objFSContext = $this->newObject('fscontext', 'context');
        $this->objMysqlxml = &$this->newObject('mysqlxml_eportfolio', 'eportfolio');
        //$this->lectGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Lecturers' ) );
        // Get the DB object.
        $this->objDbAddressList = &$this->getObject('dbeportfolio_address', 'eportfolio');
        $this->objDbContactList = &$this->getObject('dbeportfolio_contact', 'eportfolio');
        $this->objDbDemographicsList = &$this->getObject('dbeportfolio_demographics', 'eportfolio');
        $this->objDbActivityList = &$this->getObject('dbeportfolio_activity', 'eportfolio');
        $this->objDbAffiliationList = &$this->getObject('dbeportfolio_affiliation', 'eportfolio');
        $this->objDbTranscriptList = &$this->getObject('dbeportfolio_transcript', 'eportfolio');
        $this->objDbEmailList = &$this->getObject('dbeportfolio_email', 'eportfolio');
        $this->objDbQclList = &$this->getObject('dbeportfolio_qcl', 'eportfolio');
        $this->objDbGoalsList = &$this->getObject('dbeportfolio_goals', 'eportfolio');
        $this->objDbCompetencyList = &$this->getObject('dbeportfolio_competency', 'eportfolio');
        $this->objDbInterestList = &$this->getObject('dbeportfolio_interest', 'eportfolio');
        $this->objDbReflectionList = &$this->getObject('dbeportfolio_reflection', 'eportfolio');
        $this->objDbAssertionList = &$this->getObject('dbeportfolio_assertion', 'eportfolio');
        $this->objDbProductList = &$this->getObject('dbeportfolio_product', 'eportfolio');
        $this->objDbCategoryList = &$this->getObject('dbeportfolio_category', 'eportfolio');
        $this->objDbComment = &$this->getObject('dbeportfolio_comment', 'eportfolio');
        $this->objDbCategorytypeList = &$this->getObject('dbeportfolio_categorytypes', 'eportfolio');
        $this->objGetall = &$this->getObject('getall_eportfolio', 'eportfolio');
        $this->objExport = &$this->getObject('export_eportfolio', 'eportfolio');
        $this->userId = $this->objUser->userId(); //To pick user userid
        $this->setVarByRef('userId', $this->userId);
        $this->userPid = $this->objUser->PKId($this->objUser->userId()); //To pick user id
        $this->setVarByRef('userPid', $this->userPid);
        $this->objUrl = $this->getObject('url', 'strings');
        $this->objAssignmentFunctions = $this->getObject('functions_assignment', 'assignment');
        $this->objWorksheetFunctions = &$this->getObject('functions_worksheet', 'worksheet');
        $this->objWorksheet = $this->getObject('dbworksheet', 'worksheet');
        $this->objWorksheetQuestions = $this->getObject('dbworksheetquestions', 'worksheet');
        $this->objWorksheetAnswers = $this->getObject('dbworksheetanswers', 'worksheet');
        $this->objWorksheetResults = $this->getObject('dbworksheetresults', 'worksheet');
        $this->dbTestadmin = $this->newObject('dbtestadmin', 'mcqtests');
        $this->dbQuestions = $this->newObject('dbquestions', 'mcqtests');
        $this->objMcqtestsFunctions = &$this->getObject('functions_mcqtests', 'mcqtests');
        $this->dbResults = $this->newObject('dbresults', 'mcqtests');
        $this->dbMarked = $this->newObject('dbmarked', 'mcqtests');
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->objDbRubricTables = &$this->getObject('dbrubrictables', 'rubric');
        $this->objDbRubricPerformances = &$this->getObject('dbrubricperformances', 'rubric');
        $this->objDbRubricObjectives = &$this->getObject('dbrubricobjectives', 'rubric');
        $this->objDbRubricCells = &$this->getObject('dbrubriccells', 'rubric');
        $this->objDbRubricAssessments = &$this->getObject('dbrubricassessments', 'rubric');
        $this->objRubricFunctions = &$this->getObject('functions_rubric', 'rubric');
        //Load eportfolio activity streamer class
        $this->objEportfolioActivityStreamer = $this->getObject('db_eportfolio_activitystreamer');
        //Load eportfolio blocks class
        $this->objEPBlocks = $this->getObject('eportfolioblocks');
        // Create an array of words to abstract
        $this->abstractionArray = array(
            'Lecturers' => ucwords($this->objLanguage->code2Txt('word_lecturers')),
            'Students' => ucwords($this->objLanguage->code2Txt('word_students'))
        );
        $this->_arrSubGroups = array();
        $this->_arrSubGroups['Group 1']['id'] = NULL;
        $this->_arrSubGroups['Group 1']['members'] = array(
            $this->userPid
        );
        $this->_arrSubGroups['Group 2']['id'] = NULL;
        $this->_arrSubGroups['Group 2']['members'] = array();
        //Check the DB in use and set the appropriate value for TRUE & FALSE
        if ($this->objEPBlocks->dbType == "pgsql") {
            $this->TRUE = 't';
            $this->FALSE = 'f';
        } else {
            $this->TRUE = 1;
            $this->FALSE = 0;
        }
    }

    public function dispatch($action) {
        //$this->setLayoutTemplate('eportfolioview_layout_tpl.php');
        $this->user = $this->objUserAdmin->getUserDetails($this->objUser->PKId($this->objUser->userId()));
        $this->userPid = $this->objUser->PKId($this->objUser->userId());
        $this->setVarByRef('user', $this->user);
        $this->setVarByRef('userPid', $this->userPid);
        switch ($action) {
            case "main":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return $this->showUserDetailsForm();
                break;

            case 'admin':
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return 'eportfolio_setup_tpl.php';
            case 'editblock':
                if (!$this->objUser->isAdmin()) {
                    return 'new_main_tpl.php';
                } else {
                    $this->setVar('heading', $this->objLanguage->languageText('mod_prelogin_editblock', 'prelogin'));
                    $block = $this->objEPBlocks->getRow('id', $this->getParam('id'));
                    $this->setVar('blockName', $block['title']);
                    $this->setVar('blockContent', $block['content']);
                    $this->setVar('location', $block['side']);
                    $this->setVar('block', array(
                        'module' => $block['blockmodule'],
                        'name' => $block['blockname']
                    ));
                    $this->setVar('id', $block['id']);
                    $bType = ($block['isblock'] == $this->TRUE) ? 'block' : 'nonblock';
                    $this->setVar('bType', $bType);
                    return 'eportfolio_setup_tpl.php';
                }
            case 'addblock':
                if (!$this->objUser->isAdmin()) {
                    return 'new_main_tpl.php';
                } else {
                    $this->setVar('heading', $this->objLanguage->languageText('mod_prelogin_addblock', 'prelogin'));
                    return 'eportfolio_setup_tpl.php';
                }
            case 'submitblock':
                if (!$this->objUser->isAdmin()) {
                    return 'new_main_tpl.php';
                } else {
                    $title = $this->getParam('title');
                    if ($title == '') {
                        $title = 'untitled';
                    }
                    $side = $this->getParam('side');
                    $bType = ($this->getParam('type') == 'block') ? $this->TRUE : $this->FALSE;
                    $content = htmlentities($this->getParam('content'), ENT_QUOTES);
                    //var_dump($content); die();
                    $block = $this->getParam('moduleblock');
                    if ($block) {
                        $arrBlock = explode('|', $block);
                        $blockModule = $arrBlock[0];
                        $blockName = $arrBlock[1];
                    } else {
                        $blockModule = '';
                        $blockName = '';
                    }
                    $data = array(
                        'title' => $title,
                        'side' => $side,
                        'content' => $content,
                        'isblock' => $bType,
                        'blockname' => $blockName,
                        'blockmodule' => $blockModule
                    );
                    if ($id = $this->getParam('id')) {
                        $result = $this->objEPBlocks->updateBlock($id, $data);
                    } else {
                        $result = $this->objEPBlocks->insertBlock($data);
                    }
                    //echo $result;
                    return $this->nextAction('admin', array(
                        'change' => '2'
                    ));
                }
            //move a block up

            case 'moveup':
                if (!$this->objUser->isAdmin()) {
                    return 'new_main_tpl.php';
                } else {
                    $this->objEPBlocks->moveRecUp($this->getParam('id'));
                    return $this->nextAction('admin', array(
                        'change' => '2'
                    ));
                }
            //move a block down

            case 'movedown':
                if (!$this->objUser->isAdmin()) {
                    return 'new_main_tpl.php';
                } else {
                    $this->objEPBlocks->moveRecDown($this->getParam('id'));
                    return $this->nextAction('admin', array(
                        'change' => '2'
                    ));
                }
            //delete a block record

            case 'delete':
                if (!$this->objUser->isAdmin()) {
                    return 'new_main_tpl.php';
                } else {
                    $this->objEPBlocks->delete('id', $this->getParam('id'));
                    return $this->nextAction('admin', array(
                        'change' => '2'
                    ));
                }
            case 'update':
                $vibe = array();
                //Get all blocks
                $blocks = $this->objEPBlocks->getAll();
                if (isset($blocks)) {
                    foreach ($blocks as $block) {
                        ($this->getParam($block['id'] . '_vis') == 'on') ? $vis = $this->TRUE : $vis = $this->FALSE;
                        if ($block['visible'] !== $vis) {
                            $this->objEPBlocks->updateVisibility($block['id'], $vis);
                        }
                    }
                }
                return $this->nextAction('admin', array(
                    'change' => '2'
                ));
            case "postcomment":
                $partid = $this->getParam('eportfoliopartid', NULL);
                $id = $this->objDbComment->insertSingle($partid, $this->getParam('newcomment', NULL), $isapproved = '0');
                // After processing return to view main
                $prevaction = $this->getParam('prevaction', NULL);
                $eportpartidvarname = $this->getParam('eportpartidvarname', NULL);
                //$this->setVarByRef("reflectId", $reflectId);
                return $this->nextAction($prevaction, array(
                    $eportpartidvarname => $partid
                ));
            case "singlereflection":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $reflectId = $this->getParam("reflectId");
                $this->setVarByRef("reflectId", $reflectId);
                //Stream Activity
                $description = $this->objLanguage->languageText('mod_eportfolio_view', 'eportfolio', 'View') . " " . $this->objLanguage->languageText('mod_eportfolio_wordReflection', 'eportfolio', 'Reflection');
                //$this->objEportfolioActivityStreamer->addRecord($this->userId, Null, Null, Null, 'eportfolio', 'singlereflection', $reflectId, $description, $endtime=NULL);
                return "viewreflection_tpl.php";
            case "singleassertion":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $assertionId = $this->getParam("assertionId");
                $this->setVarByRef("assertionId", $assertionId);
                return "viewassertion_tpl.php";
            case "singlegoal":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $goalId = $this->getParam("goalId");
                $this->setVarByRef("goalId", $goalId);
                return "viewgoal_tpl.php";
            case "singleinterest":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $interestId = $this->getParam("interestId");
                $this->setVarByRef("interestId", $interestId);
                return "viewinterest_tpl.php";
            case "singlecompetency":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $competencyId = $this->getParam("competencyId");
                $this->setVarByRef("competencyId", $competencyId);
                return "viewcompetency_tpl.php";
            case "singlequali":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $qualiId = $this->getParam("qualiId");
                $this->setVarByRef("qualiId", $qualiId);
                return "viewqualification_tpl.php";
            case "singletranscript":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $transId = $this->getParam("transId");
                $this->setVarByRef("transId", $transId);
                return "viewtranscript_tpl.php";
            case "singleaffiliation":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $affiId = $this->getParam("affiId");
                $this->setVarByRef("affiId", $affiId);
                return "viewaffiliation_tpl.php";
            case "singleactivity":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $atyId = $this->getParam("atyId");
                $this->setVarByRef("atyId", $atyId);
                return "viewactivity_tpl.php";
            case "rubricviewtable":
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $tableId = $this->getParam("tableId", "");
                $tableInfo = $this->objDbRubricTables->listSingle($tableId);
                $title = $tableInfo[0]['title'];
                $description = $tableInfo[0]['description'];
                $rows = $tableInfo[0]['rows'];
                $cols = $tableInfo[0]['cols'];
                $this->setVarByRef("title", $title);
                $this->setVarByRef("description", $description);
                $this->setVarByRef("rows", $rows);
                $this->setVarByRef("cols", $cols);
                // Build the performances array
                $performances = array();
                for ($j = 0; $j < $cols; $j++) {
                    $performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
                    $performances[] = $performance[0]['performance'];
                }
                $this->setVarByRef("performances", $performances);
                // Build the objectives array
                $objectives = array();
                for ($i = 0; $i < $rows; $i++) {
                    $objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
                    $objectives[] = $objective[0]['objective'];
                }
                $this->setVarByRef("objectives", $objectives);
                // Build the cells matrix
                $cells = array();
                for ($i = 0; $i < $rows; $i++) {
                    $cells[$i] = array();
                    for ($j = 0; $j < $cols; $j++) {
                        $cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
                        $cells[$i][$j] = $cell[0]['contents'];
                    }
                }
                $this->setVarByRef("cells", $cells);
                return "viewrubric_tpl.php";
            case 'rubricsassessments':
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                $tableId = $this->getParam('tableId', '');
                $this->setVarByRef('tableId', $tableId);
                $tableInfo = $this->objDbRubricTables->listSingle($tableId);
                $title = $tableInfo[0]['title'];
                //foreach($tableInfo1 as $tableInfo)
                //{
                //$title = $tableInfo['title'];
                $description = $tableInfo[0]['description'];
                //$description = $tableInfo['description'];
                $rows = $tableInfo[0]['rows'];
                //$rows = $tableInfo['rows'];
                $cols = $tableInfo[0]['cols'];
                //$cols = $tableInfo['cols'];
                //}
                $this->setVarByRef("title", $title);
                $this->setVarByRef("description", $description);
                $this->setVar('maxtotal', $cols * $rows);
                $assessments = $this->objDbRubricAssessments->listAll($tableId);
                $this->setVarByRef("assessments", $assessments);
                // Do we want to show student names ?
                $showStudentNames = $this->getParam("showStudentNames", "yes");
                $this->setVarByRef("showStudentNames", $showStudentNames);
                return "rubricassessments_tpl.php";
            case 'showtest':
                $this->setLayoutTemplate(NULL);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressSearch', TRUE);
                $this->setVar('suppressFooter', TRUE);
                return $this->showTest();
            case 'viewworksheet':
                $this->setLayoutTemplate(NULL);
                $id = $this->getParam('id');
                $worksheet = $this->objWorksheet->getWorksheet($id);
                if ($worksheet == FALSE) {
                    return $this->nextAction(NULL, array(
                        'error' => 'unknownworksheet'
                    ));
                }
                $this->setVarByRef('id', $id);
                $this->setVarByRef('worksheet', $worksheet);
                $questions = $this->objWorksheetQuestions->getQuestions($id);
                $this->setVarByRef('questions', $questions);
                $worksheetResult = $this->objWorksheetResults->getWorksheetResult($this->objUser->userId(), $id);
                if ($worksheet['activity_status'] == 'open' && !$worksheetResult) {
                    $this->setLayoutTemplate(NULL);
                    $this->setVar('pageSuppressToolbar', TRUE);
                    $this->setVar('pageSuppressBanner', TRUE);
                    $this->setVar('pageSuppressSearch', TRUE);
                    $this->setVar('suppressFooter', TRUE);
                    return $this->nextAction(NULL, array(
                        'error' => 'unknownworksheet'
                    ));
                    //return 'answerworksheet_tpl.php';
                } else {
                    $this->setVar('pageSuppressToolbar', TRUE);
                    $this->setVar('pageSuppressBanner', TRUE);
                    $this->setVar('pageSuppressSearch', TRUE);
                    $this->setVar('suppressFooter', TRUE);
                    $this->setVarByRef('worksheetResult', $worksheetResult);
                    return 'viewworksheet_tpl.php';
                }
            case 'export':
                $exportXML = $this->objMysqlxml->convertToXML();
                return $exportXML;
            case 'uploadeportfolio':
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                $folder = $this->getParam('parentfolder', null);
                $folderPath = $this->objFolders->getFolder($folder);
                $importXML = $this->objMysqlxml->importFromXML($folderPath['folderpath']);
                $this->setSession('displayconfirmationmessage', TRUE);
                return $this->nextAction('main', array(
                    'message' => 'uploadsuccessful'
                ));
            case 'uploaddonemessage':
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                echo '<div align="center" style="background-color:#A8A8A8;"><h3>' . $this->objLanguage->languageText('mod_eportfolio_congratulations', 'eportfolio') . '!</h3><br></br><p>' . $this->objLanguage->languageText('mod_eportfolio_successMessage', 'eportfolio') . "</p></div>";
                break;

            case 'import':
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return 'upload_eportfolio_tpl.php';
                break;

            case 'checkfolder':
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
                        $fileIdentification = 'Identification.xml';
                        $folderPath = $this->objFolders->getFolder($code);
                        $verifyFolder = $this->objFile->getFileFolder($filename, $folderPath['folderpath']);
                        $verifyFolderIdent = $this->objFile->getFileFolder($fileIdentification, $folderPath['folderpath']);
                        if ($verifyFolder[0]['filename'] == 'imsmanifest.xml' && $verifyFolderIdent[0]['filename'] == 'Identification.xml') {
                            echo 1;
                        } else {
                            echo 0;
                        }
                }
                break;

            case 'myview';
                //$this->setLayoutTemplate('plain_layout_tpl.php');
                $myid = $this->objUser->userId();
                $myPid = $this->objUser->PKId($this->objUser->userId());
                $address = $this->objGetall->getViewAddress($myid);
                $contacts = $this->objGetall->getViewContacts($myid);
                $emails = $this->objGetall->getViewEmail($myid);
                $demographics = $this->objGetall->getViewDemographics($myid);
                $activity = $this->objGetall->getViewActivity($myid);
                $affiliation = $this->objGetall->getViewAffiliation($myid);
                $transcripts = $this->objGetall->getViewTranscripts($myid);
                $qualification = $this->objGetall->getViewQualification($myid);
                $goals = $this->objGetall->getViewGoals($myid);
                $competency = $this->objGetall->getViewCompetency($myid);
                $interests = $this->objGetall->getViewInterests($myid);
                $reflections = $this->objGetall->getViewReflections($myid);
                $assertions = $this->objGetall->getViewAssertions($myPid);
                $identification = $this->objGetall->getViewIdentification($myid);
                $this->setVarByRef('myid', $myid);
                $this->setVarByRef('myPid', $myPid);
                $this->setVarByRef('address', $address);
                $this->setVarByRef('contact', $contacts);
                $this->setVarByRef('email', $emails);
                $this->setVarByRef('demographics', $demographics);
                $this->setVarByRef('activities', $activity);
                $this->setVarByRef('affiliation', $affiliation);
                $this->setVarByRef('transcripts', $transcripts);
                $this->setVarByRef('qualifications', $qualification);
                $this->setVarByRef('goals', $goals);
                $this->setVarByRef('competencies', $competency);
                $this->setVarByRef('interests', $interests);
                $this->setVarByRef('reflections', $reflections);
                $this->setVarByRef('assertions', $assertions);
                $this->setVarByRef('identification', $identification);
                return 'view_eportfolio_tpl.php';
                break;

            case 'makepdf':
                //$sectionid = $this->getParam('sectionid');
                $fullnames = $this->objUser->fullName() . "'s " . $this->objLanguage->languageText("mod_eportfolio_wordEportfolio", 'eportfolio');
                $myid = $this->objUser->userId();
                $myPid = $this->objUser->PKId($this->objUser->userId());
                $address = $this->objGetall->getAddress($myid);
                $contacts = $this->objGetall->getContacts($myid);
                $emails = $this->objGetall->getEmail($myid);
                $demographics = $this->objGetall->getDemographics($myid);
                $activity = $this->objGetall->getActivity($myid);
                $affiliation = $this->objGetall->getAffiliation($myid);
                $transcripts = $this->objGetall->getTranscripts($myid);
                $qualification = $this->objGetall->getQualification($myid);
                $goals = $this->objGetall->getGoals($myid);
                $competency = $this->objGetall->getCompetency($myid);
                $interests = $this->objGetall->getInterests($myid);
                $reflections = $this->objGetall->getReflections($myid);
                $assertions = $this->objGetall->getAssertions($myPid);
                //Avoid empty pdf output
                $createPdf = False;
                if (!empty($address) || !empty($contacts) || !empty($emails) || !empty($demographics) || !empty($activity) || !empty($affiliation) || !empty($transcripts) || !empty($qualification) || !empty($goals) || !empty($competency) || !empty($interests) || !empty($reflections) || !empty($assertions)) {
                    $createPdf = True;
                }
                //get the pdfmaker classes
                $objPdf = $this->getObject('tcpdfwrapper', 'pdfmaker');
                $text = '<h1>' . $fullnames . "</h1><br></br>\r\n" . $address . $contacts . $emails . $demographics;
                $otherText = $activity . $affiliation . $transcripts . $qualification . $goals . $competency . $interests . $reflections . $assertions;
                //Write pdf
                $objPdf->initWrite();
                if ($createPdf == True)
                    $objPdf->partWrite($text);
                if (!empty($activity))
                    $objPdf->partWrite($activity);
                if (!empty($affiliation))
                    $objPdf->partWrite($affiliation);
                if (!empty($transcripts))
                    $objPdf->partWrite($transcripts);
                if (!empty($qualification))
                    $objPdf->partWrite($qualification);
                if (!empty($competency))
                    $objPdf->partWrite($competency);
                if (!empty($goals))
                    $objPdf->partWrite($goals);
                if (!empty($interests))
                    $objPdf->partWrite($interests);
                if (!empty($reflections))
                    $objPdf->partWrite($reflections);
                if (!empty($assertions))
                    $objPdf->partWrite($assertions);
                if ($createPdf == True) {
                    return $objPdf->show();
                } else {
                    return $this->nextAction('main', array(
                        'message' => 'sorryemptypdf'
                    ));
                }
                break;

            case 'emptypdfmessage':
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                echo '<div align="center" style="background-color:#A8A8A8;"><h3>' . $this->objLanguage->languageText('mod_eportfolio_wordNotice', 'eportfolio') . '!</h3><br></br><p>' . $this->objLanguage->languageText('phrase_eportfolio_emptypdfmessage', 'eportfolio') . "</p></div>";
                break;

            case 'addparts':
                if (class_exists('groupops', false)) {
                    $selectedParts = $this->getArrayParam('arrayList');
                    $groupId = $this->getParam('mygroupId', NULL);
                    if (empty($groupId))
                        $groupId = $this->getSession('groupId', $groupId);
                    $this->setVarByRef('groupId', $groupId);
                    //Get user Groups
                    $userGroups = $this->_objGroupAdmin->getSubgroups($groupId);
                    if (!empty($userGroups[0])) {
                        foreach ($userGroups[0] as $userGroup) {
                            $group_define_name[] = $userGroup['group_define_name'];
                        }
                        foreach ($group_define_name as $partPid) {
                            $grpId = $this->_objGroupAdmin->getId($partPid);
                            $this->_objGroupAdmin->deleteGroup($grpId);
                        }
                    }
                    //Get the group_define_name which is similar from the selectedpartId from the userGroups array
                    $group_define_name = array();
                    if (empty($selectedParts)) {
                        if (!empty($userGroups[0])) {
                            foreach ($userGroups[0] as $userGroup) {
                                $group_define_name[] = $userGroup['group_define_name'];
                            }
                            foreach ($group_define_name as $partPid) {
                                $grpId = $this->_objGroupAdmin->getId($partPid);
                                $this->_objGroupAdmin->deleteGroup($grpId);
                            }
                        }
                    } else {
                        // Get the added member ids
                        $addList = array_diff($selectedParts, $group_define_name);
                        // Get the deleted member ids
                        $delList = array_diff($group_define_name, $selectedParts);
                        // Delete these members
                        if (count($delList) > 0) {
                            foreach ($delList as $partPid) {
                                $grpId = $this->_objGroupAdmin->getId($partPid);
                                $this->_objGroupAdmin->deleteGroup($grpId);
                            }
                        }
                        // Add these members
                        if (count($addList) > 0) {
                            $this->manageEportfolioViewers($addList, $groupId);
                        }
                        //Empty array
                        $selectedParts = array();
                    }
                    $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                    return 'allparts_tpl.php';
                } else {
                    $selectedParts = $this->getArrayParam('arrayList');
                    $groupId = $this->getParam('groupId', NULL);
                    $this->setVarByRef('groupId', $groupId);
                    //Get user Groups
                    $userGroups = $this->_objGroupAdmin->getUserDirectGroups($groupId);
                    if (empty($selectedParts)) {
                        $this->deleteGroupUsers($userGroups, $groupId);
                    } else {
                        // Get the added member ids
                        $addList = array_diff($selectedParts, $userGroups);
                        // Get the deleted member ids
                        $delList = array_diff($userGroups, $selectedParts);
                        // Delete these members
                        foreach ($delList as $partPid) {
                            $this->_objGroupAdmin->deleteGroupUser($partPid['group_id'], $groupId);
                        }
                        // Add these members
                        if (count($addList) > 0) {
                            $this->manageEportfolioViewersOld($addList, $groupId);
                        }
                        //Empty array
                        $selectedParts = array();
                    }
                    $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                    return 'allparts2_tpl.php';
                }
            case "add_group":
                return "add_group_tpl.php";
                break;

            case 'manage_eportfolio':
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $groupId = $this->getParam('id', null);
                $this->setVarByRef('groupId', $groupId);
                if (class_exists('groupops', false)) {
                    return "allparts_tpl.php";
                } else {
                    return "allparts2_tpl.php";
                }
                break;

            case 'view_others3_eportfolio':
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $groupId = $this->getParam('id', null);
                $this->setVarByRef('groupId', $groupId);
                return "others3_eportfolio_tpl.php";
                break;

            case 'view_others_eportfolio':
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $groupId = $this->getParam('id', null);
                $ownerId = $this->getParam('ownerId', null);
                $this->setVarByRef('groupId', $groupId);
                $this->setVarByRef('ownerId', $ownerId);
                return "others2_eportfolio_tpl.php";
                break;

            case 'manage_group':
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $myid = $this->getParam('id', null);
                return $this->showManagegroup($myid);
                break;

            case 'manage_form':
                $myid = $this->getParam('id', null);
                $this->setVarByRef('myid', $myid);
                return $this->processManagegroup($myid);
                break;

            case 'manage_stud':
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $myid = $this->getParam('id', null);
                return $this->showManage('Students', $myid);
                break;

            case 'manage_lect':
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $myid = $this->getParam('id', null);
                return $this->showManage('Lecturers', $myid);
                break;

            case 'students_form':
                $myid = $this->getParam('id', null);
                $this->setVarByRef('myid', $myid);
                return $this->processManage('Students', $myid);
                break;

            case 'lecturers_form':
                $myid = $this->getParam('id', null);
                $this->setVarByRef('myid', $myid);
                return $this->processManage('Lecturers', $myid);
                break;

            case "userdetails":
                return "userdetails_tpl.php";
                break;

            case "add_address":
                $address_type = $this->getParam('address_type', null);
                $this->setVarByRef('address_type', $address_type);
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_address_tpl.php";
                break;

            case "add_interest":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_interest_tpl.php";
                break;

            case "view_assertion":
            case "view_product":
            case "view_category":
            case "view_categorytype":
            case "view_reflection":
            case "view_demographics":
            case "view_affiliation":
            case "view_competency":
            case "view_transcript":
            case "view_interest":
            case "view_address":
            case "view_contact":
            case "view_email":
            case "view_activity":
            case "view_qcl":
            case "view_goals":
                return $this->nextAction('main');
                break;

            case "add_goals":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_goals_tpl.php";
                break;

            case "add_contact":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_contact_tpl.php";
                break;

            case "add_email":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_email_tpl.php";
                break;

            case "add_activity":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_activity_tpl.php";
                break;

            case "add_reflection":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_reflection_tpl.php";
                break;

            case "add_assertion":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_assertion_tpl.php";
                break;

            case "add_product":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_product_tpl.php";
                break;

            case "add_category":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_category_tpl.php";
                break;

            case "add_categorytype":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_categorytype_tpl.php";
                break;

            case "add_affiliation":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_affiliation_tpl.php";
                break;

            case "add_demographics":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_demographics_tpl.php";
                break;

            case "add_transcript":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_transcript_tpl.php";
                break;

            case "add_qcl":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_qcl_tpl.php";
                break;

            case "add_competency":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "add_competency_tpl.php";
                break;

            case 'changeimage':
                return $this->changePicture();
                break;

            case 'resetimage':
                return $this->resetImage($this->getParam('id'));
                break;

            case 'updateuserdetails':
                return $this->updateUserDetails();
                break;

            case "deleteconfirm":
                $this->nextAction($id = $this->getParam('id', null), $this->objDbAddressList->deleteSingle($id));
                // After processing return to view main
                return $this->nextAction('main');
                break;

            case "deletecontact":
                $this->nextAction($id = $this->getParam('id', null), $this->objDbContactList->deleteSingle($id));
                // After processing return to view main
                return $this->nextAction('main');
                break;

            case "deleteinterest":
                $this->nextAction($id = $this->getParam('id', null), $this->objDbInterestList->deleteSingle($id));
                // After processing return to view main
                return $this->nextAction('main');
                break;

            case "deletedemographics":
                $this->nextAction($id = $this->getParam('id', null), $this->objDbDemographicsList->deleteSingle($id));
                // After processing return to view main
                return $this->nextAction('main');
                break;

            case "deleteemail":
                $this->nextAction($myid = $this->getParam('myid', null), $this->objDbEmailList->deleteSingle($myid));
                // After processing return to view main
                return $this->nextAction('main');
                break;

            case "deletetranscript":
                $this->nextAction($id = $this->getParam('id', null), $this->objDbTranscriptList->deleteSingle($id));
                // After processing return to view main
                return $this->nextAction('main');
                break;

            case "deleteqcl":
                $this->nextAction($id = $this->getParam('id', null), $this->objDbQclList->deleteSingle($id));
                // After processing return to view main
                return $this->nextAction('main');
                break;

            case "deletecompetency":
                $this->nextAction($id = $this->getParam('id', null), $this->objDbCompetencyList->deleteSingle($id));
                return $this->nextAction('main');
                break;

            case "deleteaffiliation":
                $this->nextAction($myid = $this->getParam('id', null), $this->objDbAffiliationList->deleteSingle($myid));
                return $this->nextAction('main');
                break;

            case "deletegoals":
                $this->nextAction($myid = $this->getParam('id', null), $this->objDbGoalsList->deleteSingle($myid));
                return $this->nextAction('main');
                break;

            case "deletereflection":
                $this->nextAction($myid = $this->getParam('id', null), $this->objDbReflectionList->deleteSingle($myid));
                return $this->nextAction('main');
                break;

            case "deleteassertion":
                $this->nextAction($myid = $this->getParam('id', null), $this->objDbAssertionList->deleteSingle($myid));
                return $this->nextAction('main');
                break;

            case "deleteactivity":
                $this->nextAction($myid = $this->getParam('id', null), $this->objDbActivityList->deleteSingle($myid));
                return $this->nextAction('main');
                break;

            case "deleteproduct":
                $this->nextAction($myid = $this->getParam('id', null), $this->objDbProductList->deleteSingle($myid));
                return $this->nextAction('main');
                break;

            case "deletecategory":
                $this->nextAction($myid = $this->getParam('id', null), $this->objDbCategoryList->deleteSingle($myid));
                return $this->nextAction('main');
                break;

            case "deletecategorytype":
                $this->nextAction($myid = $this->getParam('id', null), $this->objDbCategorytypeList->deleteSingle($myid));
                return $this->nextAction('main');
                break;

            case "addaddressconfirm":
                //$link = $this->getParam('link', NULL);
                $id = $this->objDbAddressList->insertSingle($this->getParam('address_type', NULL), $this->getParam('street_no', NULL), $this->getParam('street_name', NULL), $this->getParam('locality', NULL), $this->getParam('city', NULL), $this->getParam('postcode', NULL), $this->getParam('postal_address', NULL));
                // After processing return to view main
                return $this->nextAction('main');
                break;

            case "addqclconfirm":
                //$link = $this->getParam('link', NULL);
                $id = $this->objDbQclList->insertSingle($this->getParam('qcl_type', NULL), $this->getParam('title', NULL), $this->getParam('organisation', NULL), $this->getParam('qcl_level', NULL), $this->getParam('award_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                return $this->nextAction('main');
                break;

            case "addgoalsconfirm":
                //$link = $this->getParam('link', NULL);
                $id = $this->objDbGoalsList->insertSingle($this->getParam('parentid', NULL), $this->getParam('goal_type', NULL), $this->getParam('start', NULL), $this->getParam('priority', NULL), $this->getParam('status', NULL), $this->getParam('status_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                return $this->nextAction('main');
                break;

            case "editqclconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbQclList->updateSingle($myid, $this->getParam('qcl_type', NULL), $this->getParam('title', NULL), $this->getParam('organisation', NULL), $this->getParam('qcl_level', NULL), $this->getParam('award_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "editgoalsconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbGoalsList->updateSingle($myid, $this->getParam('parentid', NULL), $this->getParam('goal_type', NULL), $this->getParam('start', NULL), $this->getParam('priority', NULL), $this->getParam('status', NULL), $this->getParam('status_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "editcompetencyconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbCompetencyList->updateSingle($myid, $this->getParam('competency_type', NULL), $this->getParam('award_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "addcompetencyconfirm":
                $id = $this->objDbCompetencyList->insertSingle($this->getParam('competency_type', NULL), $this->getParam('award_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                return $this->nextAction('main');
                break;

            case "editcompetency":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbCompetencyList->listSingle($id);
                $competency_type = $list[0]['type'];
                $award_date = $list[0]['award_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('competency_type', $competency_type);
                $this->setVarByref('award_date', $award_date);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "edit_competency_tpl.php";
                break;

            case "editassertionconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbAssertionList->updateSingle($myid, $this->getParam('language', NULL), $this->getParam('rationale', NULL), $this->getParam('creation_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "addgroupconfirm":
                if (class_exists('groupops', false)) {
                    $id = $this->addGroups($this->userId . "^" . $this->getParam('group', NULL));
                } else {
                    $id = $this->addGroupsOld($this->userId . "^" . $this->getParam('group', NULL));
                }
                return $this->nextAction('configureviews');
                break;

            case "addassertionconfirm":
                $id = $this->objDbAssertionList->insertSingle($this->getParam('language', NULL), $this->getParam('rationale', NULL), $this->getParam('creation_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                //Create Assertion Folder
                //check if the folder exist
                if ($this->objFSContext->folderExists($contextCode) == FALSE) {
                    //create the folder
                    $this->objFSContext->createContextFolder($contextCode);
                } else {
                    return FALSE;
                }
                // After processing return to view assertion
                //return $this->nextAction( 'view_assertion', array() );
                return $this->nextAction('main');
                break;

            case "editassertion":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbAssertionList->listSingle($id);
                $language = $list[0]['language'];
                $rationale = $list[0]['rationale'];
                $creation_date = $list[0]['creation_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('language', $language);
                $this->setVarByRef('rationale', $rationale);
                $this->setVarByref('creation_date', $creation_date);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "edit_assertion_tpl.php";
                break;

            case "displayassertion":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $thisid = $this->getParam('thisid', null);
                $this->setVarByRef('thisid', $thisid);
                $mylist = $this->objDbAssertionList->listSingle($thisid);
                $myinstructor = $mylist[0]['userid'];
                $mylanguage = $mylist[0]['language'];
                $myrationale = $mylist[0]['rationale'];
                $mycreation_date = $mylist[0]['creation_date'];
                $myshortdescription = $mylist[0]['shortdescription'];
                $mylongdescription = $mylist[0]['longdescription'];
                $this->setVarByRef('myinstructor', $myinstructor);
                $this->setVarByRef('mylanguage', $mylanguage);
                $this->setVarByRef('myrationale', $myrationale);
                $this->setVarByref('mycreation_date', $mycreation_date);
                $this->setVarByRef('myshortdescription', $myshortdescription);
                $this->setVarByRef('mylongdescription', $mylongdescription);
                return "display_assertion_tpl.php";
                break;

            case "displayothers_assertion":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $thisid = $this->getParam('thisid', null);
                $myfilter = explode(",", $thisid);
                $assertionId = $myfilter[0];
                $ownerId = $myfilter[1];
                $groupId = $myfilter[2];
                $this->setVarByRef('assertionId', $assertionId);
                $this->setVarByRef('ownerId', $ownerId);
                $this->setVarByRef('groupId', $groupId);
                $mylist = $this->objDbAssertionList->listSingle($assertionId);
                $myinstructor = $mylist[0]['userid'];
                $mylanguage = $mylist[0]['language'];
                $myrationale = $mylist[0]['rationale'];
                $mycreation_date = $mylist[0]['creation_date'];
                $myshortdescription = $mylist[0]['shortdescription'];
                $mylongdescription = $mylist[0]['longdescription'];
                $this->setVarByRef('myinstructor', $myinstructor);
                $this->setVarByRef('mylanguage', $mylanguage);
                $this->setVarByRef('myrationale', $myrationale);
                $this->setVarByref('mycreation_date', $mycreation_date);
                $this->setVarByRef('myshortdescription', $myshortdescription);
                $this->setVarByRef('mylongdescription', $mylongdescription);
                return "displayothers_assertion_tpl.php";
                break;

            case "displayothers_reflection":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $thisid = $this->getParam('thisid', null);
                $myfilter = explode(",", $thisid);
                $reflectionId = $myfilter[0];
                $ownerId = $myfilter[1];
                $groupId = $myfilter[2];
                $this->setVarByRef('reflectionId', $reflectionId);
                $this->setVarByRef('ownerId', $ownerId);
                $this->setVarByRef('groupId', $groupId);
                $list = $this->objDbReflectionList->listSingle($reflectionId);
                $language = $list[0]['language'];
                $rationale = $list[0]['rationale'];
                $creation_date = $list[0]['creation_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('rationale', $rationale);
                $this->setVarByref('creation_date', $creation_date);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "display_reflection_tpl.php";
                break;

            case "displayothers_interest":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $thisid = $this->getParam('thisid', null);
                $myfilter = explode(",", $thisid);
                $interestId = $myfilter[0];
                $ownerId = $myfilter[1];
                $groupId = $myfilter[2];
                $this->setVarByRef('interestId', $interestId);
                $this->setVarByRef('ownerId', $ownerId);
                $this->setVarByRef('groupId', $groupId);
                $list = $this->objDbInterestList->listSingle($interestId);
                $interesttype = $this->objDbCategorytypeList->listSingle($list[0]['type']);
                $interest_type = $interesttype[0]['type'];
                $creation_date = $list[0]['creation_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('interest_type', $interest_type);
                $this->setVarByref('creation_date', $creation_date);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "display_interest_tpl.php";
                break;

            case "displayothers_competency":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $thisid = $this->getParam('thisid', null);
                $myfilter = explode(",", $thisid);
                $competencyId = $myfilter[0];
                $ownerId = $myfilter[1];
                $groupId = $myfilter[2];
                $this->setVarByRef('competencyId', $competencyId);
                $this->setVarByRef('ownerId', $ownerId);
                $this->setVarByRef('groupId', $groupId);
                $list = $this->objDbCompetencyList->listSingle($competencyId);
                $competencytype = $this->objDbCategorytypeList->listSingle($list[0]['type']);
                $competency_type = $competencytype[0]['type'];
                $award_date = $list[0]['award_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('competency_type', $competency_type);
                $this->setVarByref('award_date', $award_date);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "display_competency_tpl.php";
                break;

            case "displayothers_transcript":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $thisid = $this->getParam('thisid', null);
                $myfilter = explode(",", $thisid);
                $transcriptId = $myfilter[0];
                $ownerId = $myfilter[1];
                $groupId = $myfilter[2];
                $this->setVarByRef('transcriptId', $transcriptId);
                $this->setVarByRef('ownerId', $ownerId);
                $this->setVarByRef('groupId', $groupId);
                $list = $this->objDbTranscriptList->listSingle($transcriptId);
                $type = $list[0]['type'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('type', $type);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "display_transcript_tpl.php";
                break;

            case "displayothers_activity":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $objDbContext = &$this->getObject('dbcontext', 'context');
                $thisid = $this->getParam('thisid', null);
                $myfilter = explode(",", $thisid);
                $activityId = $myfilter[0];
                $ownerId = $myfilter[1];
                $groupId = $myfilter[2];
                $this->setVarByRef('activityId', $activityId);
                $this->setVarByRef('ownerId', $ownerId);
                $this->setVarByRef('groupId', $groupId);
                $list = $this->objDbActivityList->listSingle($activityId);
                $activitytype = $this->objDbCategorytypeList->listSingle($list[0]['type']);
                $mycontextRecord = $objDbContext->getContextDetails($list[0]['contextid']);
                if (!empty($mycontextRecord)) {
                    $contexttitle = $mycontextRecord['title'];
                } else {
                    $contexttitle = $list[0]['contextid'];
                }
                $activityType = $activitytype[0]['type'];
                $activityStart = $list[0]['start'];
                $activityFinish = $list[0]['finish'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('contexttitle', $contexttitle);
                $this->setVarByRef('activityType', $activityType);
                $this->setVarByRef('activityStart', $activityStart);
                $this->setVarByRef('activityFinish', $activityFinish);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "display_activity_tpl.php";
                break;

            case "editreflectionconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbReflectionList->updateSingle($myid, $this->getParam('language', NULL), $this->getParam('rationale', NULL), $this->getParam('creation_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "addreflectionconfirm":
                $id = $this->objDbReflectionList->insertSingle($this->getParam('language', NULL), $this->getParam('rationale', NULL), $this->getParam('creation_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                return $this->nextAction('main');
                break;

            case "editreflection":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbReflectionList->listSingle($id);
                $language = $list[0]['language'];
                $rationale = $list[0]['rationale'];
                $creation_date = $list[0]['creation_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('language', $language);
                $this->setVarByRef('rationale', $rationale);
                $this->setVarByref('creation_date', $creation_date);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "edit_reflection_tpl.php";
                break;

            case "editproductconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbProductList->updateSingle($myid, $this->getParam('producttype', NULL), $this->getParam('comment', NULL), $this->getParam('referential_source', NULL), $this->getParam('referential_id', NULL), $this->getParam('assertion_id', NULL), $this->getParam('assignment_id', NULL), $this->getParam('essay_id', NULL), $this->getParam('creation_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "addproductconfirm":
                $id = $this->objDbProductList->insertSingle($this->getParam('producttype', NULL), $this->getParam('comment', NULL), $this->getParam('referential_source', NULL), $this->getParam('referential_id', NULL), $this->getParam('assertion_id', NULL), $this->getParam('assignment_id', NULL), $this->getParam('essay_id', NULL), $this->getParam('creation_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                return $this->nextAction('main');
                break;

            case "editproduct":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbProductList->listSingle($id);
                $producttype = $list[0]['type'];
                $comment = $list[0]['comment'];
                $referential_source = $list[0]['referential_source'];
                $referential_id = $list[0]['referential_id'];
                $assertion_id = $list[0]['assertion_id'];
                $assignment_id = $list[0]['assignment_id'];
                $essay_id = $list[0]['essay_id'];
                $creation_date = $list[0]['creation_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('producttype', $producttype);
                $this->setVarByref('comment', $comment);
                $this->setVarByRef('referential_source', $referential_source);
                $this->setVarByRef('referential_id', $referential_id);
                $this->setVarByref('assertion_id', $assertion_id);
                $this->setVarByRef('assignment_id', $assignment_id);
                $this->setVarByRef('essay_id', $essay_id);
                $this->setVarByref('creation_date', $creation_date);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                // After processing return to view product
                return "edit_product_tpl.php";
                break;

            case "editinterestconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbInterestList->updateSingle($myid, $this->getParam('interest_type', NULL), $this->getParam('creation_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "addinterestconfirm":
                $id = $this->objDbInterestList->insertSingle($this->getParam('interest_type', NULL), $this->getParam('creation_date', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                return $this->nextAction('main');
                break;

            case "editinterest":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbInterestList->listSingle($id);
                $interest_type = $list[0]['type'];
                $creation_date = $list[0]['creation_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('interest_type', $interest_type);
                $this->setVarByref('creation_date', $creation_date);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "edit_interest_tpl.php";
                break;

            case "editgoals":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbGoalsList->listSingle($id);
                $parentid = $list[0]['parentid'];
                $goal_type = $list[0]['type'];
                $start = $list[0]['start'];
                $priority = $list[0]['priority'];
                $status = $list[0]['status'];
                $status_date = $list[0]['status_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('id', $id);
                $this->setVarByRef('parentid', $parentid);
                $this->setVarByRef('goal_type', $goal_type);
                $this->setVarByRef('start', $start);
                $this->setVarByRef('priority', $priority);
                $this->setVarByRef('status', $status);
                $this->setVarByref('status_date', $status_date);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "edit_goals_tpl.php";
                break;

            case "editqcl":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbQclList->listSingle($id);
                $qcl_type = $list[0]['qcl_type'];
                $title = $list[0]['qcl_title'];
                $organisation = $list[0]['organisation'];
                $qcl_level = $list[0]['qcl_level'];
                $award_date = $list[0]['award_date'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('qcl_type', $qcl_type);
                $this->setVarByRef('title', $title);
                $this->setVarByRef('organisation', $organisation);
                $this->setVarByRef('qcl_level', $qcl_level);
                $this->setVarByRef('award_date', $award_date);
                $this->setVarByref('shortdescription', $shortdescription);
                $this->setVarByref('longdescription', $longdescription);
                return "edit_qcl_tpl.php";
                break;

            case "addaffiliationconfirm":
                $id = $this->objDbAffiliationList->insertSingle($this->getParam('affiliation_type', NULL), $this->getParam('classification', NULL), $this->getParam('role', NULL), $this->getParam('organisation', NULL), $this->getParam('start', NULL), $this->getParam('finish', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                return $this->nextAction('main');
                break;

            case "editaddress":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbAddressList->listSingle($id);
                $address_type = $list[0]['type'];
                $street_no = $list[0]['street_no'];
                $street_name = $list[0]['street_name'];
                $locality = $list[0]['locality'];
                $city = $list[0]['city'];
                $postcode = $list[0]['postcode'];
                $postal_address = $list[0]['postal_address'];
                $this->setVarByRef('address_type', $address_type);
                $this->setVarByRef('street_no', $street_no);
                $this->setVarByRef('street_name', $street_name);
                $this->setVarByRef('locality', $locality);
                $this->setVarByRef('city', $city);
                $this->setVarByref('postcode', $postcode);
                $this->setVarByref('postal_address', $postal_address);
                return "edit_address_tpl.php";
                break;

            case "editaffiliation":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbAffiliationList->listSingle($id);
                $affiliation_type = $list[0]['type'];
                $classification = $list[0]['classification'];
                $role = $list[0]['role'];
                $organisation = $list[0]['organisation'];
                $start = $list[0]['start'];
                $finish = $list[0]['finish'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('affiliation_type', $affiliation_type);
                $this->setVarByRef('classification', $classification);
                $this->setVarByRef('role', $role);
                $this->setVarByRef('organisation', $organisation);
                $this->setVarByRef('start', $start);
                $this->setVarByref('finish', $finish);
                $this->setVarByref('shortdescription', $shortdescription);
                $this->setVarByref('longdescription', $longdescription);
                return "edit_affiliation_tpl.php";
                break;

            case "editaddressconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbAddressList->updateSingle($myid, $this->getParam('address_type', NULL), $this->getParam('street_no', NULL), $this->getParam('street_name', NULL), $this->getParam('locality', NULL), $this->getParam('city', NULL), $this->getParam('postcode', NULL), $this->getParam('postal_address', NULL)));
                return $this->nextAction('main');
                break;

            case "editaffiliationconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbAffiliationList->updateSingle($myid, $this->getParam('affiliation_type', NULL), $this->getParam('classification', NULL), $this->getParam('role', NULL), $this->getParam('organisation', NULL), $this->getParam('start', NULL), $this->getParam('finish', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "addcontactconfirm":
                $id = $this->objDbContactList->insertSingle($this->getParam('contact_type', NULL), $this->getParam('contactType', NULL), $this->getParam('country_code', NULL), $this->getParam('area_code', NULL), $this->getParam('id_number', NULL));
                return $this->nextAction('main');
                break;

            case "editemail":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbEmailList->listSingle($id);
                $email_type = $list[0]['type'];
                $email = $list[0]['email'];
                $this->setVarByRef('email_type', $email_type);
                $this->setVarByRef('email', $email);
                return "edit_email_tpl.php";
                break;

            case "addemailconfirm":
                $id = $this->objDbEmailList->insertSingle($this->getParam('email_type', NULL), $this->getParam('email', NULL));
                return $this->nextAction('main');
                break;

            case "editemailconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbEmailList->updateSingle($myid, $this->getParam('email_type', NULL), $this->getParam('email', NULL)));
                return $this->nextAction('main');
                break;

            case "editcategory":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbCategoryList->listSingle($id);
                $category = $list[0]['category'];
                $this->setVarByRef('category', $category);
                return "edit_category_tpl.php";
                break;

            case "addcategoryconfirm":
                $id = $this->objDbCategoryList->insertSingle($this->getParam('category', NULL));
                return $this->nextAction('main');
                break;

            case "editcategoryconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbCategoryList->updateSingle($myid, $this->getParam('category', NULL)));
                return $this->nextAction('main');
                break;

            case "editcategorytype":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbCategorytypeList->listSingle($id);
                $categoryid = $list[0]['categoryid'];
                $categorytype = $list[0]['type'];
                $this->setVarByRef('categoryid', $categoryid);
                $this->setVarByRef('categorytype', $categorytype);
                return "edit_categorytype_tpl.php";
                break;

            case "addcategorytypeconfirm":
                $id = $this->objDbCategorytypeList->insertSingle($this->getParam('categoryid', NULL), $this->getParam('categorytype', NULL));
                return $this->nextAction('main');
                break;

            case "editcategorytypeconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbCategorytypeList->updateSingle($myid, $this->getParam('categoryid', NULL), $this->getParam('categorytype', NULL)));
                return $this->nextAction('main');
                break;

            case "editcontact":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbContactList->listSingle($id);
                $contact_type = $list[0]['type'];
                $contactType = $list[0]['contact_type'];
                $country_code = $list[0]['country_code'];
                $area_code = $list[0]['area_code'];
                $id_number = $list[0]['id_number'];
                $this->setVarByRef('contact_type', $contact_type);
                $this->setVarByRef('contactType', $contactType);
                $this->setVarByRef('country_code', $country_code);
                $this->setVarByRef('area_code', $area_code);
                $this->setVarByRef('id_number', $id_number);
                return "edit_contact_tpl.php";
                break;

            case "editcontactconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbContactList->updateSingle($myid, $this->getParam('contact_type', NULL), $this->getParam('contactType', NULL), $this->getParam('country_code', NULL), $this->getParam('area_code', NULL), $this->getParam('id_number', NULL)));
                return $this->nextAction('main');
                break;

            case "adddemographicsconfirm":
                $id = $this->objDbDemographicsList->insertSingle($this->getParam('demographics_type', NULL), $this->getParam('birth', NULL), $this->getParam('nationality', NULL));
                return $this->nextAction('main');
                break;

            case "editdemographics":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbDemographicsList->listSingle($id);
                $demographics_type = $list[0]['type'];
                $birth = $list[0]['birth'];
                $nationality = $list[0]['nationality'];
                $this->setVarByRef('demographics_type', $demographics_type);
                $this->setVarByRef('birth', $birth);
                $this->setVarByRef('nationality', $nationality);
                return "edit_demographics_tpl.php";
                break;

            case "editdemographicsconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                //Covert date to sql format
                $birth = $this->getParam('birth', NULL);
                //$this->setVarByRef('birth', $this->birth);
                //$mybirth = $this->objDate->sqlDate($birth);
                $this->nextAction($this->objDbDemographicsList->updateSingle($myid, $this->getParam('demographics_type', NULL), $birth, $this->getParam('nationality', NULL)));
                return $this->nextAction('main');
                break;

            case "addactivityconfirm":
                // associate activity to a course
                $associate = $this->getParam('contexttitle', NULL);
                if (isset($associate) && !empty($associate)) {
                    $contexttitle = $this->getParam('contexttitle', NULL);
                } else {
                    $contexttitle = 'None';
                }
                $id = $this->objDbActivityList->insertSingle($contexttitle, $this->getParam('activityType', NULL), $this->getParam('activityStart', NULL), $this->getParam('activityFinish', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                return $this->nextAction('main');
                break;

            case "addtranscriptconfirm":
                //$link = $this->getParam('link', NULL);
                $id = $this->objDbTranscriptList->insertSingle($this->getParam('type', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL));
                return $this->nextAction('main');
                break;

            case "editactivity":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbActivityList->listSingle($id);
                $contexttitle = $list[0]['contextid'];
                $activityType = $list[0]['type'];
                $activityStart = $list[0]['start'];
                $activityFinish = $list[0]['finish'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('contexttitle', $contexttitle);
                $this->setVarByRef('activityType', $activityType);
                $this->setVarByRef('activityStart', $activityStart);
                $this->setVarByRef('activityFinish', $activityFinish);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "edit_activity_tpl.php";
                break;

            case "editactivityconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                // associate activity to a course
                $associate = $this->getParam('contexttitle', NULL);
                if (isset($associate) && !empty($associate)) {
                    $contexttitle = $this->getParam('contexttitle', NULL);
                } else {
                    $contexttitle = 'None';
                }
                $this->nextAction($this->objDbActivityList->updateSingle($myid, $contexttitle, $this->getParam('activityType', NULL), $this->getParam('activityStart', NULL), $this->getParam('activityFinish', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "edittranscript":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $id = $this->getParam('id', null);
                $this->setVarByRef('id', $id);
                $list = $this->objDbTranscriptList->listSingle($id);
                $type = $list[0]['type'];
                $shortdescription = $list[0]['shortdescription'];
                $longdescription = $list[0]['longdescription'];
                $this->setVarByRef('type', $type);
                $this->setVarByRef('shortdescription', $shortdescription);
                $this->setVarByRef('longdescription', $longdescription);
                return "edit_transcript_tpl.php";
                break;

            case "edittranscriptconfirm":
                $myid = $this->getParam('id', null);
                $this->setVarByRef('id', $myid);
                $this->nextAction($this->objDbTranscriptList->updateSingle($myid, $this->getParam('type', NULL), $this->getParam('shortdescription', NULL), $this->getParam('longdescription', NULL)));
                return $this->nextAction('main');
                break;

            case "viewgroups":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                $this->setSession('showconfirmation', TRUE);
                $myId = $this->getParam('id', null);
                if (empty($myId))
                    $myId = $this->getSession('groupId');
                $this->setSession('groupId', $myId);
                return $this->groupsHome($myId);
                break;

            case 'searchforusers':
                return $this->searchForUsers();
                break;

            case 'viewsearchresults';
                $groupId = $this->getSession('groupId', Null);
                return $this->getResults($this->getParam('page', 1));
                break;

            case 'addusers':
                return $this->updateUserRoles();
                break;

            case 'removeuser':
                //		var_dump($this->getParam('userid'));
                //		exit;
                return $this->removeUserFromGroup($this->getParam('userid'), $this->getParam('group'));
                break;
            case 'removeallusers':
                return $this->removeAllUsersFromGroup($this->getParam('userId'), $this->getParam('group'));
                break;

            case "viewothersportfolio":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "view_others_tpl.php";
                break;

            case "configureviews":
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return "manage_views_tpl.php";
                break;

            default:
                $this->setLayoutTemplate('eportfolio_layout_tpl.php');
                return $this->showUserDetailsForm();
                break;
        }
    }

    private function showUserDetailsForm() {
        //Get Visible MAIN blocks
        $mainBlocks = $this->objEPBlocks->getVisibleBlocks('main', $this->userId);
        $this->setVarByRef('mainBlocks', $mainBlocks);
        return "new_main_tpl.php";
    }

    /**
     * Method to remove a user from a group
     * @param string $userId User Id of the User
     * @param string $group Group to be deleted from - either lecturers, students or guest
     */
    private function removeUserFromGroup($userId = NULL, $groupId = NULL) {
        if (is_null($userId)) {
/*
            return $this->nextAction(NULL, array(
                'message' => 'nouseridprovidedfordelete'
            ));
*/
            return $this->nextAction('viewgroups', array(
                'id' => $groupId,
                'message' => 'nouseridprovidedfordelete'
            ));
        }
        //$pkId = $this->objUser->PKId($userId);
        //Check if class groupops exists
        if (class_exists('groupops', false)) {
            $permid = $this->objGroupsOps->getUserByUserId($userId);
        }
        //log_debug('#!$1:'.var_export($permid, TRUE));
        $pkId = $permid['perm_user_id'];
        //log_debug('#!$2:'.var_export($pkId, TRUE));
        $deleteMember = $this->_objGroupAdmin->deleteGroupUser($groupId, $pkId);
        //log_debug('#!$3:'.var_export($deleteMember, TRUE));
        return $this->nextAction('viewgroups', array(
            'id' => $groupId,
            'message' => 'userdeletedfromgroup'
        ));
    }

    /**
     * Method to remove a user from a group
     * @param string $userId User Id of the User
     * @param string $group Group to be deleted from - either lecturers, students or guest
     */
    private function removeAllUsersFromGroup($arrUserId = NULL, $groupId = NULL) {
        //log_debug('#!'.var_export($arrUserId, TRUE));
        if (is_null($arrUserId)) {
/*
            return $this->nextAction(NULL, array(
                'message' => 'nouseridprovidedfordelete'
            ));
*/
            return $this->nextAction('viewgroups', array(
                'id' => $groupId,
                'message' => 'nouseridsprovidedfordelete'
            ));
        }
        foreach ($arrUserId as $userId) {
            //$pkId = $this->objUser->PKId($userId);
            //Check if class groupops exists
            if (class_exists('groupops', false)) {
                $permid = $this->objGroupsOps->getUserByUserId($userId);
            }
            $pkId = $permid['perm_user_id'];
            $deleteMember = $this->_objGroupAdmin->deleteGroupUser($groupId, $pkId);
        }
        return $this->nextAction('viewgroups', array(
            'id' => $groupId,
            'message' => 'usersdeletedfromgroup'
        ));
    }

    /**
     * Method to show the list of users in a context
     */
    private function groupsHome($group) {
        // Generate an array of users in the context, and send it to page template
        $this->prepareContextUsersArray();
        // Default Values for Search
        $searchFor = $this->getSession('searchfor', '');
        $this->setVar('searchfor', $searchFor);
        $field = $this->getSession('field', 'firstName');
        $course = $this->getSession('course', 'course');
        //$group=$this->getSession('group','group');
        $this->setVar('field', $field);
        $this->setVar('course', $course);
        $this->setVar('group', $group);
        $confirmation = $this->getSession('showconfirmation', FALSE);
        $this->setVar('showconfirmation', $confirmation);
        //$this->setSession('showconfirmation', FALSE);
        //Ehb-added-begin
        $currentContextCode = $this->_objDBContext->getContextCode();
        $where = "where contextCode<>" . "'" . $currentContextCode . "'";
        $data = $this->_objDBContext->getAll($where);
        $this->setVarByRef('data', $data);
        //Ehb-added-End
        return 'grouphome_tpl.php';
    }

    /**
     * Method to Prepare a List of Users in a Context sorted by lecturer, student, guest
     * The results are sent to the template
     */
    private function prepareContextUsersArray() {
        // Get Context Code
        $contextCode = $this->_objDBContext->getContextCode();
        $filter = " ORDER BY surname ";
        // Guests
        //$gid=$this->_objGroupAdmin->getLeafId(array($contextCode,'Guest'));
        $groupId = $this->getSession('groupId');
        $guests = $this->_objGroupAdmin->getGroupUsers($groupId, array(
                    'userid',
                    'firstName',
                    'surname',
                    'title',
                    'emailAddress',
                    'country',
                    'sex',
                    'staffnumber'
                        ), $filter);
        $guestsArray = array();
        if (count($guests) > 0) {
            foreach ($guests as $guest) {
                $guestsArray[] = $guest['userid'];
            }
        }
        // Send to Template
        $this->setVarByRef('guests', $guestsArray);
        $this->setVarByRef('guestDetails', $guests);
    }

    /**
     * Method to Update User Roles
     */
    private function updateUserRoles() {
        $groupId = $this->getSession('groupId');
        $changedItems = $_POST['changedItems'];
        $changedItems = explode(',', $changedItems);
        array_shift($changedItems);
        $changedItems = array_unique($changedItems);
        $groups = $this->_objGroupAdmin->getTopLevelGroups();
        foreach ($changedItems as $item) {
            //Check if class groupops exists
            if (class_exists('groupops', false)) {
                $permid = $this->objGroupsOps->getUserByUserId($item);
                $pkId = $permid['perm_user_id'];
                //remove users
                $this->objGroupsOps->removeUser($groupId, $pkId);
            }
            $this->_objGroupAdmin->addGroupUser($groupId, $pkId);
        }
        // die;
        return $this->nextAction('viewgroups', array(
            'message' => 'usersupdated'
        ));
    }

    /**
     * Method to Show the Results for a Search
     * @param int $page - Page of Results to show
     */
    private function getResults($page = 1) {
        $searchFor = $this->getSession('searchfor', '');
        $field = $this->getSession('field', 'firstName');
        //Ehb-added-begin
        $course = $this->getSession('course', 'course');
        $group = $this->getSession('group', 'group');
        //Ehb-added-End
        $order = $this->getSession('order', 'firstName');
        $numResults = $this->getSession('numresults', 20);
        $groupId = $this->getSession('groupId', Null);
        $this->setVar('searchfor', $searchFor);
        $this->setVar('field', $field);
        $this->setVar('order', $order);
        $this->setVar('numresults', $numResults);
        //Ehb-added-begin
        $this->setVar('course', $course);
        $this->setVar('group', $group);
        //Ehb-added-End
        // Prevent Corruption of Page Value - Negative Values
        if ($page < 1) {
            $page = 1;
        }
        $currentContextCode = $this->_objDBContext->getContextCode();
        $results = $this->objContextUsers->searchUsers($searchFor, $field, $order, $numResults, ($page - 1), $course, $group);
        $this->setVarByRef('results', $results);
        $countResults = $this->objContextUsers->countResults();
        $this->setVarByRef('countResults', $countResults);
        $this->setVarByRef('page', $page);
        $paging = $this->objContextUsers->generatePaging($searchFor, $field, $order, $numResults, ($page - 1));
        $this->setVarByRef('paging', $paging);
        $contextCode = $this->_objDBContext->getContextCode();
        $this->setVarByRef('contextCode', $contextCode);
        //Ehb-added-begin
        $currentContextCode = $this->_objDBContext->getContextCode();
        $where = "where contextCode<>" . "'" . $currentContextCode . "'";
        $data = $this->_objDBContext->getAll($where);
        $this->setVarByRef('data', $data);
        //Ehb-added-End
        // Get Users into Arrays
        $this->prepareContextUsersArray();
        return 'searchresults_tpl.php';
    }

    /**
     * Method to search for Users
     * This function sets them as a session and then redirects to the results
     */
    private function searchForUsers() {
        $searchFor = $this->getParam('search');
        $this->setSession('searchfor', $searchFor);
        $field = $this->getParam('field');
        $this->setSession('field', $field);
        //Ehb-added-begin
        $course = $this->getParam('course');
        $this->setSession('course', $course);
        $group = $this->getParam('group');
        $this->setSession('group', $group);
        //Ehb-added-End
        $order = $this->getParam('order');
        $this->setSession('order', $order);
        $numResults = $this->getParam('results');
        $this->setSession('numresults', $numResults);
        return $this->nextAction('viewsearchresults');
    }

    /**
     * Method to get a list of courses a user is registered for
     * @return array
     * @access public
     */
    public function getUserContexts() {
        //$usercontextcodes = $this->objUserContext->getContextList();
        $usercontextcodes2 = $this->objContextUser->getUserContext($this->userId);
        foreach ($usercontextcodes2 as $code) {
            $objDbContext = &$this->getObject('dbcontext', 'context');
            //$mycontextRecord[] = $objDbContext->getContextDetails($code['contextcode']);
            $mycontextRecord[] = $objDbContext->getContextDetails($code);
        } //End foreach
        return $mycontextRecord;
    }

    private function changePicture() {
        $fileId = $this->getParam('imageselect');
        if (isset($_POST['resetimage'])) {
            return $this->resetImage();
        }
        if ($fileId == '') {
            return $this->nextAction(NULL, array(
                'change' => 'image',
                'message' => 'nopicturegiven'
            ));
        }
        $filepath = $this->objFile->getFullFilePath($fileId);
        if ($fileId == FALSE) {
            return $this->nextAction(NULL, array(
                'change' => 'image',
                'message' => 'imagedoesnotexist'
            ));
        }
        $mimetype = $this->objFile->getFileMimetype($fileId);
        if (substr($mimetype, 0, 5) != 'image') {
            return $this->nextAction(NULL, array(
                'change' => 'image',
                'message' => 'fileisnotimage'
            ));
        }
        $objImageResize = $this->getObject('imageresize', 'files');
        $objImageResize->setImg($filepath);
        //Resize to 100x100 Maintaining Aspect Ratio
        $objImageResize->resize(100, 100, TRUE);
        $storePath = 'user_images/' . $this->objUser->userId() . '.jpg';
        $this->objCleanUrl->cleanUpUrl($storePath);
        $result = $objImageResize->store($storePath);
        //Resize to 100x100 Maintaining Aspect Ratio
        $objImageResize->resize(35, 35, TRUE);
        $storePath = 'user_images/' . $this->objUser->userId() . '_small.jpg';
        $this->objCleanUrl->cleanUpUrl($storePath);
        $result = $objImageResize->store($storePath);
        $this->setSession('showconfirmation', TRUE);
        return $this->nextAction(NULL, array(
            'change' => 'image',
            'message' => 'imagechanged'
        ));
    }

    private function updateUserDetails() {
        if (!$_POST) {
            return $this->nextAction(NULL);
        }
        // Get Details from Form
        $title = $this->getParam('eportfolio_title');
        $firstname = $this->getParam('eportfolio_othernames');
        $surname = $this->getParam('eportfolio_surname');
        $password = '';
        $userDetails = array(
            'title' => $title,
            'firstname' => $firstname,
            'surname' => $surname,
        );
        $this->setSession('userDetails', $userDetails);
        $results['detailschanged'] = TRUE;
        // Process Update
        $update = $this->objUserAdmin->updateUserDetails($this->user['id'], $this->user['username'], $firstname, $surname, $title, $this->user['emailaddress'], $this->user['sex'], $this->user['country'], $this->user['cellnumber'], $this->user['staffnumber'], $password);
        if (count($results) > 0) {
            $results['change'] = 'details';
        }
        $this->setSession('showconfirmation', TRUE);
        $this->objUser->updateUserSession();
        // Process Update Results
        if ($update) {
            // After processing return to view contact
            return $this->nextAction('view_contact', array());
        } else {
            return $this->nextAction(NULL, array(
                'change' => 'details',
                'error' => 'detailscouldnotbeupdated'
            ));
        }
    }

    private function checkFields($checkFields) {
        $allFieldsOk = TRUE;
        $this->messages = array();
        foreach ($checkFields as $field) {
            if ($field == '') {
                $allFieldsOk = FALSE;
            }
        }
        return $allFieldsOk;
    }

    public function formatDate($date) {
        $ret = $this->objDate->formatDate($date);
        return $ret;
    }

    function resetImage() {
        $this->objUserAdmin->removeUserImage($this->objUser->userId());
        $this->setSession('showconfirmation', TRUE);
        return $this->nextAction(NULL, array(
            'change' => 'image',
            'message' => 'userimagereset',
            'change' => 'image'
        ));
    }

    /**
     * Method to process the request to manage a member group.
     * @param string the group id to be managed.
     */
    function processManagegroup($myId) {
        $groupId = $myId;
        if ($this->getParam('button') == 'save' && $groupId <> '') {
            // Get the revised member ids
            if (is_array($this->getParam('list2'))) {
                $list = $this->getParam('list2');
            } else {
                $list = array();
            }
            // Get the original member ids
            $fields = array(
                'tbl_users.id'
            );
            $memberList = &$this->_objGroupAdmin->getGroupUsers($groupId, Null, Null);
            $member = array();
            if (!empty($memberList)) {
                foreach ($memberList as $theList) {
                    $member[] = $theList['id'];
                }
            }
            //$oldList = $this->_objGroupAdmin->getField($memberList, 'id');
            // Get the added member ids
            //$addList = array_diff($list, $oldList);
            $addList = array_diff($list, $member);
            // Get the deleted member ids
            $delList = array_diff($member, $list);
            // Add these members
            foreach ($addList as $userId) {
                if ($this->_objGroupAdmin->isGroupMember($userId, $groupId) == FALSE) {
                    $this->_objGroupAdmin->addGroupUser($groupId, $userId);
                }
            }
            // Delete these members
            foreach ($delList as $userId) {
                if ($this->_objGroupAdmin->isGroupMember($userId, $groupId) == TRUE) {
                    $this->_objGroupAdmin->deleteGroupUser($groupId, $userId);
                }
            }
        }
        if ($this->getParam('button') == 'cancel' && $groupId <> '') {

        }
        // After processing return to main
        return $this->nextAction('view_assertion', array());
    }

    /**
     * Method to process the request to manage a member group.
     * @param string the group to be managed.
     */
    function processManage($groupName, $myId) {
        $mygroupId = $this->_objGroupAdmin->getLeafId(array(
                    $myId,
                    $groupName
                ));
        $groupId = $this->getchildId($mygroupId, $groupName);
        if ($this->getParam('button') == 'save' && $groupId <> '') {
            // Get the revised member ids
            if (is_array($this->getParam('list2'))) {
                $list = $this->getParam('list2');
            } else {
                $list = array();
            }
            // Get the original member ids
            $fields = array(
                'tbl_users.id'
            );
            $memberList = &$this->_objGroupAdmin->getGroupUsers($groupId, $fields);
            $oldList = $this->_objGroupAdmin->getField($memberList, 'id');
            // Get the added member ids
            $addList = array_diff($list, $oldList);
            // Get the deleted member ids
            $delList = array_diff($oldList, $list);
            // Add these members
            foreach ($addList as $userId) {
                if ($this->_objGroupAdmin->isGroupMember($userId, $groupId) == FALSE) {
                    $this->_objGroupAdmin->addGroupUser($groupId, $userId);
                }
            }
            // Delete these members
            foreach ($delList as $userId) {
                if ($this->_objGroupAdmin->isGroupMember($userId, $groupId) == TRUE) {
                    $this->_objGroupAdmin->deleteGroupUser($groupId, $userId);
                }
            }
        }
        if ($this->getParam('button') == 'cancel' && $groupId <> '') {

        }
        // After processing return to main
        return $this->nextAction('view_assertion', array());
    }

    /**
     * Method to show the manage member group template.
     * @param string the group id to be managed.
     */
    function showManagegroup($myid) {
        // The member list of this group
        $fields = array(
            'firstName',
            'surname',
            'tbl_users.id'
        );
        $memberList = $this->_objGroupAdmin->getGroupUsers($myid, $fields);
        $memberIds = $this->_objGroupAdmin->getField($memberList, 'id');
        $filter = "'" . implode("', '", $memberIds) . "'";
        // Users list need the firstname, surname, and userId fields.
        $fields = array(
            'firstName',
            'surname',
            'id'
        );
        $usersList = $this->_objGroupAdmin->getUsers($fields, " WHERE id NOT IN($filter)");
        sort($usersList);
        // Members list dropdown
        $lstMembers = $this->newObject('dropdown', 'htmlelements');
        $lstMembers->name = 'list2[]';
        $lstMembers->extra = ' multiple="multiple" style="width:100pt" size="10" ondblclick="moveSelectedOptions(this.form[\'list2[]\'],this.form[\'list1[]\'],true); "';
        foreach ($memberList as $user) {
            $fullName = $user['firstname'] . " " . $user['surname'];
            $userPKId = $user['id'];
            //echo "<h1>userPKId ".$userPKId."</h1>";
            $lstMembers->addOption($userPKId, $fullName);
        }
        $tblLayoutM = &$this->newObject('htmltable', 'htmlelements');
        $tblLayoutM->row_attributes = 'align="center" ';
        $tblLayoutM->width = '100px';
        $tblLayoutM->startRow();
        $tblLayoutM->endRow();
        $tblLayoutM->startRow();
        $tblLayoutM->addCell($lstMembers->show());
        $tblLayoutM->endRow();
        $this->setVarByRef('lstMembers', $tblLayoutM);
        // Users list dropdown
        $lstUsers = $this->newObject('dropdown', 'htmlelements');
        $lstUsers->name = 'list1[]';
        $lstUsers->extra = ' multiple="multiple" style="width:100pt"  size="10" ondblclick="moveSelectedOptions(this.form[\'list1[]\'],this.form[\'list2[]\'],true)"';
        foreach ($usersList as $user) {
            $fullName = $user['firstname'] . " " . $user['surname'];
            $userPKId = $user['id'];
            $lstUsers->addOption($userPKId, $fullName);
        }
        $tblLayoutU = &$this->newObject('htmltable', 'htmlelements');
        $tblLayoutU->row_attributes = 'align="center"';
        $tblLayoutU->width = '100px';
        $tblLayoutU->startRow();
        $tblLayoutU->addCell($this->objLanguage->code2Txt('mod_contextgroups_ttlUsers', 'contextgroups'), '10%', null, null, 'heading');
        $tblLayoutU->endRow();
        $tblLayoutU->startRow();
        $tblLayoutU->addCell($lstUsers->show());
        $tblLayoutU->endRow();
        $this->setVarByRef('lstUsers', $tblLayoutU);
        // Link method
        $lnkSave = $this->newObject('link', 'htmlelements');
        $lnkSave->href = '#';
        $lnkSave->extra = 'onclick="javascript:';
        $lnkSave->extra.= 'selectAllOptions( document.forms[\'frmManage\'][\'list2[]\'] ); ';
        $lnkSave->extra.= 'document.forms[\'frmManage\'][\'button\'].value=\'save\'; ';
        $lnkSave->extra.= 'document.forms[\'frmManage\'].submit(); "';
        $lnkSave->link = $this->objLanguage->languageText('word_save');
        $lnkCancel = $this->newObject('link', 'htmlelements');
        $lnkCancel->href = '#';
        $lnkCancel->extra = 'onclick="javascript:';
        $lnkCancel->extra.= 'document.forms[\'frmManage\'][\'button\'].value=\'cancel\'; ';
        $lnkCancel->extra.= 'document.forms[\'frmManage\'].submit(); "';
        $lnkCancel->link = $this->objLanguage->languageText('word_cancel');
        $ctrlButtons = array();
        $ctrlButtons['lnkSave'] = $lnkSave->show();
        $ctrlButtons['lnkCancel'] = $lnkCancel->show();
        $this->setVar('ctrlButtons', $ctrlButtons);
        $navButtons = array();
        $navButtons['lnkRight'] = $this->navLink('>>', 'Selected', "forms['frmManage']['list1[]']", "forms['frmManage']['list2[]']");
        $navButtons['lnkRightAll'] = $this->navLink('All >>', 'All', "forms['frmManage']['list1[]']", "forms['frmManage']['list2[]']");
        $navButtons['lnkLeft'] = $this->navLink('<<', 'Selected', "forms['frmManage']['list2[]']", "forms['frmManage']['list1[]']");
        $navButtons['lnkLeftAll'] = $this->navLink('All <<', 'All', "forms['frmManage']['list2[]']", "forms['frmManage']['list1[]']");
        $this->setVar('navButtons', $navButtons);
        $frmManage = &$this->getObject('form', 'htmlelements');
        $frmManage->name = 'frmManage';
        $frmManage->displayType = '3';
        $frmManage->action = $this->uri(array(
                    'action' => 'manage_form',
                    'id' => $myid
                ));
        //$frmManage->action = $this->uri ( array( 'module'=>'eportfolio', 'action' => 'main', 'id'=>$myid) );
        $frmManage->addToForm("<input type='hidden' name='button' value='' />");
        $this->setVarByRef('frmManage', $frmManage);
        $title = $this->objLanguage->code2Txt('mod_contextgroups_ttlManageMembers', 'contextgroups', array(
                    'GROUPNAME' => $groupName,
                    'TITLE' => $this->_objDBContext->getTitle()
                ));
        $this->setVar('title', $title);
        return 'manage2_group_tpl.php';
    }

    /**
     * Method to show the manage member group template.
     * @param string the group to be managed.
     */
    function showManage($groupName, $myid) {
        $mygroupId = $this->_objGroupAdmin->getLeafId(array(
                    $myid,
                    $groupName
                ));
        //echo "<h1>mygroupId ".$mygroupId."</h1>";
        $groupId = $this->getchildId($mygroupId, $groupName);
        //echo "<h1>groupId ".$groupId."</h1>";
        // The member list of this group
        $fields = array(
            'firstName',
            'surname',
            'tbl_users.id'
        );
        $memberList = $this->_objGroupAdmin->getGroupUsers($groupId, $fields);
        $memberIds = $this->_objGroupAdmin->getField($memberList, 'id');
        $filter = "'" . implode("', '", $memberIds) . "'";
        // Users list need the firstname, surname, and userId fields.
        $fields = array(
            'firstName',
            'surname',
            'id'
        );
        $usersList = $this->_objGroupAdmin->getUsers($fields, " WHERE id NOT IN($filter)");
        sort($usersList);
        // Members list dropdown
        $lstMembers = $this->newObject('dropdown', 'htmlelements');
        $lstMembers->name = 'list2[]';
        $lstMembers->extra = ' multiple="multiple" style="width:100pt" size="10" ondblclick="moveSelectedOptions(this.form[\'list2[]\'],this.form[\'list1[]\'],true); "';
        foreach ($memberList as $user) {
            $fullName = $user['firstname'] . " " . $user['surname'];
            $userPKId = $user['id'];
            //echo "<h1>userPKId ".$userPKId."</h1>";
            $lstMembers->addOption($userPKId, $fullName);
        }
        $tblLayoutM = &$this->newObject('htmltable', 'htmlelements');
        $tblLayoutM->row_attributes = 'align="center" ';
        $tblLayoutM->width = '100px';
        $tblLayoutM->startRow();
        $tblLayoutM->endRow();
        $tblLayoutM->startRow();
        $tblLayoutM->addCell($lstMembers->show());
        $tblLayoutM->endRow();
        $this->setVarByRef('lstMembers', $tblLayoutM);
        // Users list dropdown
        $lstUsers = $this->newObject('dropdown', 'htmlelements');
        $lstUsers->name = 'list1[]';
        $lstUsers->extra = ' multiple="multiple" style="width:100pt"  size="10" ondblclick="moveSelectedOptions(this.form[\'list1[]\'],this.form[\'list2[]\'],true)"';
        foreach ($usersList as $user) {
            $fullName = $user['firstname'] . " " . $user['surname'];
            $userPKId = $user['id'];
            $lstUsers->addOption($userPKId, $fullName);
        }
        $tblLayoutU = &$this->newObject('htmltable', 'htmlelements');
        $tblLayoutU->row_attributes = 'align="center"';
        $tblLayoutU->width = '100px';
        $tblLayoutU->startRow();
        $tblLayoutU->addCell($this->objLanguage->code2Txt('mod_contextgroups_ttlUsers', 'contextgroups'), '10%', null, null, 'heading');
        $tblLayoutU->endRow();
        $tblLayoutU->startRow();
        $tblLayoutU->addCell($lstUsers->show());
        $tblLayoutU->endRow();
        $this->setVarByRef('lstUsers', $tblLayoutU);
        // Link method
        $lnkSave = $this->newObject('link', 'htmlelements');
        $lnkSave->href = '#';
        $lnkSave->extra = 'onclick="javascript:';
        $lnkSave->extra.= 'selectAllOptions( document.forms[\'frmManage\'][\'list2[]\'] ); ';
        $lnkSave->extra.= 'document.forms[\'frmManage\'][\'button\'].value=\'save\'; ';
        $lnkSave->extra.= 'document.forms[\'frmManage\'].submit(); "';
        $lnkSave->link = $this->objLanguage->languageText('word_save');
        $lnkCancel = $this->newObject('link', 'htmlelements');
        $lnkCancel->href = '#';
        $lnkCancel->extra = 'onclick="javascript:';
        $lnkCancel->extra.= 'document.forms[\'frmManage\'][\'button\'].value=\'cancel\'; ';
        $lnkCancel->extra.= 'document.forms[\'frmManage\'].submit(); "';
        $lnkCancel->link = $this->objLanguage->languageText('word_cancel');
        $ctrlButtons = array();
        $ctrlButtons['lnkSave'] = $lnkSave->show();
        $ctrlButtons['lnkCancel'] = $lnkCancel->show();
        $this->setVar('ctrlButtons', $ctrlButtons);
        $navButtons = array();
        $navButtons['lnkRight'] = $this->navLink('>>', 'Selected', "forms['frmManage']['list1[]']", "forms['frmManage']['list2[]']");
        $navButtons['lnkRightAll'] = $this->navLink('All >>', 'All', "forms['frmManage']['list1[]']", "forms['frmManage']['list2[]']");
        $navButtons['lnkLeft'] = $this->navLink('<<', 'Selected', "forms['frmManage']['list2[]']", "forms['frmManage']['list1[]']");
        $navButtons['lnkLeftAll'] = $this->navLink('All <<', 'All', "forms['frmManage']['list2[]']", "forms['frmManage']['list1[]']");
        $this->setVar('navButtons', $navButtons);
        $frmManage = &$this->getObject('form', 'htmlelements');
        $frmManage->name = 'frmManage';
        $frmManage->displayType = '3';
        $frmManage->action = $this->uri(array(
                    'action' => $groupName . '_form',
                    'id' => $myid
                ));
        //$frmManage->action = $this->uri ( array( 'module'=>'eportfolio', 'action' => 'main', 'id'=>$myid) );
        $frmManage->addToForm("<input type='hidden' name='button' value='' />");
        $this->setVarByRef('frmManage', $frmManage);
        $title = $this->objLanguage->code2Txt('mod_contextgroups_ttlManageMembers', 'contextgroups', array(
                    'GROUPNAME' => $groupName,
                    'TITLE' => $this->_objDBContext->getTitle()
                ));
        $this->setVar('title', $title);
        return 'manage_group_tpl.php';
    }

    /**
     * Method to create a navigation button link
     */
    function navLink($linkText, $moveType, $from, $to) {
        $link = $this->newObject('link', 'htmlelements');
        $link->href = '#';
        $link->extra = 'onclick="javascript:';
        $link->extra.= 'move' . $moveType . 'Options';
        $link->extra.= '( document.' . $from;
        $link->extra.= ', document.' . $to;
        $link->extra.= ', true );"';
        $link->link = htmlspecialchars($linkText);
        return $link->show();
    }

    /**
     * Method to get the child id where name = $groupName
     */
    function getchildId($parentid, $groupName) {
        $thisgroupId = $this->_objGroupAdmin->getChildren($parentid);
        //Get the id for the child that corresponds to $groupName
        foreach ($thisgroupId as $item) {
            $mygroupName = $item['name'];
            if ($mygroupName == $groupName) {
                $groupId = $item['id'];
            }
        }
        return $groupId;
    }

    /**
     * Method to create the groups for a new eportfolio user
     * @param string The user id.
     * @param string The Title of a new context.
     */
    function createGroups($userid, $title) {
        // Context node
        $eportfolioGroupId = $this->_objGroupAdmin->addGroup($userid, $title, NULL);
        // For each subgroup
        foreach ($this->_arrSubGroups as $groupName => $groupId) {
            $newGroupId = $this->_objGroupAdmin->addGroup($groupName, $this->objUser->PKId($this->objUser->userId()) . ' ' . $groupName, $eportfolioGroupId);
            // then add them as subGroups of the parent Group.
            $data = array(
                'group_id' => $eportfolioGroupId,
                'subgroup_id' => $newGroupId
            );
            $newSubGroupId = $this->objLuAdmin->perm->assignSubGroup($data);
            $this->_arrSubGroups[$groupName]['id'] = $newGroupId;
            $newGroupId = $this->_objGroupAdmin->addGroupUser($newGroupId, $this->objUser->userId());
        } // End foreach subgroup
        // Add groupMembers
        $this->addGroupMembers();
        // Now create the ACLS
        $this->_objManageGroups->createAcls($userid, $title);
    }

    /**
     * Method to create the groups for a new eportfolio user on kewl2
     * @param string The user id.
     * @param string The Title of a new context.
     */
    function createGroupsOld($userid, $title) {
        // Context node
        $eportfolioGroupId = $this->_objGroupAdmin->addGroup($userid, $title, NULL);
        // For each subgroup
        foreach ($this->_arrSubGroups as $groupName => $groupId) {
            $newGroupId = $this->_objGroupAdmin->addGroup($groupName, $this->objUser->PKId($this->objUser->userId()) . ' ' . $groupName, $eportfolioGroupId);
            $this->_arrSubGroups[$groupName]['id'] = $newGroupId;
        } // End foreach subgroup
        // Add groupMembers
        $this->addGroupMembers();
        $this->_objManageGroups->createAcls($userid, $title);
    }

    /**
     * Method to create more groups for an eportfolio user
     * @param string The user id.
     * @param string The Title of a new context.
     */
    function addGroups($title) {
        // user Pk id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        $usergroupId = $this->_objGroupAdmin->getId($userPid);
        // Add subgroup
        $newGroupId = $this->_objGroupAdmin->addGroup($title, $userPid . ' ' . $title, $usergroupId);
        // then add them as subGroups of the parent Group.
        $data = array(
            'group_id' => $usergroupId,
            'subgroup_id' => $newGroupId
        );
        $newSubGroupId = $this->objLuAdmin->perm->assignSubGroup($data);
        // Add groupMembers
        //$this->addGroupMembers();
        $groupId = $this->_objGroupAdmin->addGroupUser($newGroupId, $this->objUser->userId());
        // Now create the ACLS
        $this->_objManageGroups->createAcls($userPid, $title);
    }

// End addGroups

    /**
     * Method to create more groups for an eportfolio user for kewl 2.0
     * @param string The user id.
     * @param string The Title of a new context.
     */
    function addGroupsOld($title) {
        // user Pk id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        $usergroupId = $this->_objGroupAdmin->getId($userPid, $pkField = 'name');
        // Add subgroup
        $newGroupId = $this->_objGroupAdmin->addGroup($title, $userPid . ' ' . $groupName, $usergroupId);
        // Add groupMembers
        $this->addGroupMembers();
        // Now create the ACLS
        $this->_objManageGroups->createAcls($userPid, $title);
    }

// End createGroups

    /**
     * Method to add members to the groups for a new eportfolio user
     */
    function addGroupMembers() {
        foreach ($this->_arrSubGroups as $groupName => $row) {
            foreach ($row['members'] as $userPKId) {
                $this->_objGroupAdmin->addGroupUser($row['id'], $userPKId);
            } // End foreach member
        } // End foreach subgroup
    }

// End addGroupMembers

    /**
     * Method to get the eportfolio users
     * @return string
     */
    public function getEportfolioUsers() {
        //manage eportfolio users
        $objLink = new link();
        $objLanguage = &$this->getObject('language', 'language');
        $icon = &$this->newObject('geticon', 'htmlelements');
        $table = &$this->newObject('htmltable', 'htmlelements');
        $linkstable = &$this->newObject('htmltable', 'htmlelements');
        $objGroups = &$this->newObject('managegroups', 'contextgroups');
        $mngfeatureBox = &$this->newObject('featurebox', 'navigation');
        $table->width = '40%';
        $linkstable->width = '40%';
        $str = '';
        //Add Group Link
        $iconAdd = $this->getObject('geticon', 'htmlelements');
        $iconAdd->setIcon('add');
        $iconAdd->title = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
        $iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
        $addlink = new link($this->uri(array(
                            'module' => 'eportfolio',
                            'action' => 'add_group'
                        )));
        $addlink->link = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio') . ' ' . $objLanguage->languageText("mod_eportfolio_wordGroup", 'eportfolio');
        $objLink = &$this->getObject('link', 'htmlelements');
        $objLink->link($this->uri(array(
                    'module' => 'eportfolio',
                    'action' => 'add_group'
                )));
        $objLink->link = $iconAdd->show();
        $mylinkAdd = $objLink->show();
        $addlink->link = 'Add Group';
        $linkAdd = $addlink->show();
        $linkstableRow = array(
            '<hr/>' . $linkAdd . ' ' . $mylinkAdd
        );
        $linkstable->addRow($linkstableRow);
        //	$str .= $mngfeatureBox->show(NULL,$linkstable->show());
        //Get group members
        //Get group id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        $this->setVarByRef('userPid', $this->userPid);
        //get the descendents.
        if (class_exists('groupops', false)) {
            $usergroupId = $this->_objGroupAdmin->getId($userPid);
            $usersubgroups = $this->_objGroupAdmin->getSubgroups($usergroupId);
            //Check if empty
            if (!empty($usersubgroups)) {
                foreach ($usersubgroups as $subgroup) {
                    // The member list of this group
                    $myGroupId = array();
                    foreach (array_keys($subgroup) as $myGrpId) {
                        $myGroupId[] = $myGrpId;
                    }
                }
            }
            $fields = array(
                'firstName',
                'surname',
                'tbl_users.id'
            );
            //Check if empty
            if (!empty($usersubgroups)) {
                foreach ($myGroupId as $groupId) {
                    $membersList = $this->_objGroupAdmin->getGroupUsers($groupId, $fields);
                    $groupName = $this->_objGroupAdmin->getName($groupId);
                    $groupName = explode("^", $groupName);
                    if (count($groupName) == 2) {
                        $groupName = $groupName[1];
                        foreach ($membersList as $users) {
                            if ($users) {
                                $fullName = $users['firstname'] . " " . $users['surname'];
                                $userPKId = $users['id'];
                                $tableRow = array(
                                    $fullName
                                );
                                $table->addRow($tableRow);
                            } else {
                                $tableRow = array(
                                    '<div align="left" style="font-size:small;font-weight:bold;color:#sCCCCCC;font-family: Helvetica, sans-serif;">' . $this->objLanguage->languageText('mod_eportfolio_wordManage', 'eportfolio') . '</div>'
                                );
                                $table->addRow($tableRow);
                            }
                        }
                        //Add Users
                        $iconManage = $this->getObject('geticon', 'htmlelements');
                        $iconManage->setIcon('add_icon');
                        $iconManage->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio') . ' / ' . $objLanguage->languageText("word_edit") . ' ' . $groupName;
                        $iconManage->title = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio') . ' / ' . $objLanguage->languageText("word_edit") . ' ' . $groupName;
                        $mnglink = new link($this->uri(array(
                                            'module' => 'eportfolio',
                                            'action' => 'viewgroups',
                                            'id' => $groupId
                                        )));
                        //	    		$mnglink->link = $objLanguage->languageText("mod_eportfolio_wordManage",'eportfolio').' '.$subgroup['name'].' '.$iconManage->show();
                        $mnglink->link = $iconManage->show();
                        $linkManage = $mnglink->show();
                        //Manage Group
                        $iconShare = $this->getObject('geticon', 'htmlelements');
                        $iconShare->setIcon('fileshare');
                        $iconShare->alt = $objLanguage->languageText("mod_eportfolio_configure", 'eportfolio') . ' ' . $groupName . ' ' . $this->objLanguage->code2Txt("mod_eportfolio_view", 'eportfolio');
                        $iconShare->title = $objLanguage->languageText("mod_eportfolio_configure", 'eportfolio') . ' ' . $groupName . ' ' . $this->objLanguage->code2Txt("mod_eportfolio_view", 'eportfolio');
                        $mnglink = new link($this->uri(array(
                                            'module' => 'eportfolio',
                                            'action' => 'manage_eportfolio',
                                            'id' => $groupId
                                        )));
                        $mnglink->link = $iconShare->show();
                        $linkMng = $mnglink->show();
                        $tableRow = array(
                            '<hr/>' . $linkManage . '   ' . $linkMng
                        );
                        $table->addRow($tableRow);
                        $textinput = new textinput("groupname", $groupName);
                        $str.= $mngfeatureBox->show($groupName, $table->show());
                        $table = &$this->newObject('htmltable', 'htmlelements');
                        $managelink = new link();
                    }
                } //end foreach
            }
        }
        $str.= $mngfeatureBox->show(NULL, $linkstable->show());
        return $str;
        unset($users);
    }

//end method

    /**
     * Method to get the eportfolio users for kewl 2.0
     * @return string
     */
    public function getEportfolioUsersOld() {
        //manage eportfolio users
        $objLink = new link();
        $objLanguage = &$this->getObject('language', 'language');
        $icon = &$this->newObject('geticon', 'htmlelements');
        $table = &$this->newObject('htmltable', 'htmlelements');
        $linkstable = &$this->newObject('htmltable', 'htmlelements');
        $objGroups = &$this->newObject('managegroups', 'contextgroups');
        $mngfeatureBox = &$this->newObject('featurebox', 'navigation');
        $table->width = '40%';
        $linkstable->width = '40%';
        $str = '';
        //Add Group Link
        $iconAdd = $this->getObject('geticon', 'htmlelements');
        $iconAdd->setIcon('add');
        $iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
        $addlink = new link($this->uri(array(
                            'module' => 'eportfolio',
                            'action' => 'add_group'
                        )));
        $addlink->link = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio') . ' ' . $objLanguage->languageText("mod_eportfolio_wordGroup", 'eportfolio') . ' ' . $iconAdd->show();
        //$addlink->link = 'Add Group'.' '.$iconAdd->show();
        $linkAdd = $addlink->show();
        $linkstableRow = array(
            '<hr/>' . $linkAdd
        );
        $linkstable->addRow($linkstableRow);
        //	$str .= $mngfeatureBox->show(NULL,$linkstable->show());
        //Get group members
        //Get group id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        $this->setVarByRef('userPid', $this->userPid);
        $usergroupId = $this->_objGroupAdmin->getId($userPid, $pkField = 'name');
        //get the descendents.
        $usersubgroups = $this->_objGroupAdmin->getChildren($usergroupId);
        foreach ($usersubgroups as $subgroup) {
            // The member list of this group
            $fields = array(
                'firstName',
                'surname',
                'tbl_users.id'
            );
            $membersList = $this->_objGroupAdmin->getGroupUsers($subgroup['id'], $fields);
            foreach ($membersList as $users) {
                if ($users) {
                    $fullName = $users['firstname'] . " " . $users['surname'];
                    $userPKId = $users['id'];
                    $tableRow = array(
                        $fullName
                    );
                    $table->addRow($tableRow);
                } else {
                    $tableRow = array(
                        '<div align="center" style="font-size:small;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">' . $this->objLanguage->languageText('mod_eportfolio_wordManage', 'eportfolio') . '</div>'
                    );
                    $table->addRow($tableRow);
                }
            }
            //Add Users
            $iconManage = $this->getObject('geticon', 'htmlelements');
            $iconManage->setIcon('add_icon');
            $iconManage->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio') . ' / ' . $objLanguage->languageText("word_edit") . ' ' . $subgroup['name'];
            $mnglink = new link($this->uri(array(
                                'module' => 'eportfolio',
                                'action' => 'manage_group',
                                'id' => $subgroup["id"]
                            )));
            //$mnglink->link = $objLanguage->languageText("mod_eportfolio_wordManage",'eportfolio').' '.$subgroup['name'].' '.$iconManage->show();
            $mnglink->link = $iconManage->show();
            $linkManage = $mnglink->show();
            //Manage Group
            $iconShare = $this->getObject('geticon', 'htmlelements');
            $iconShare->setIcon('fileshare');
            $iconShare->alt = $objLanguage->languageText("mod_eportfolio_configure", 'eportfolio') . ' ' . $subgroup['name'] . ' ' . $this->objLanguage->code2Txt("mod_eportfolio_view", 'eportfolio');
            $mnglink = new link($this->uri(array(
                                'module' => 'eportfolio',
                                'action' => 'manage_eportfolio',
                                'id' => $subgroup["id"]
                            )));
            //$mnglink->link = $objLanguage->languageText("mod_eportfolio_wordManage",'eportfolio').' '.$this->objLanguage->code2Txt("mod_eportfolio_wordEportfolio",'eportfolio').' '.$iconShare->show();
            $mnglink->link = $iconShare->show();
            $linkMng = $mnglink->show();
            $tableRow = array(
                '<hr/>' . $linkManage . '   ' . $linkMng
            );
            $table->addRow($tableRow);
            $textinput = new textinput("groupname", $subgroup['name']);
            $str.= $mngfeatureBox->show($subgroup['name'], $table->show());
            $table = &$this->newObject('htmltable', 'htmlelements');
            $managelink = new link();
        } //end foreach
        $str.= $mngfeatureBox->show(NULL, $linkstable->show());
        return $str;
        unset($users);
    }

//end method
//Function for managing eportfolio group items/parts

    public function manageEportfolioViewers($selectedParts, $groupId) {
        if (empty($groupId))
            $groupId = $this->getSession('groupId', $groupId);
        // user Pk id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        foreach ($selectedParts as $partId) {
            //$thisId = $this->_objGroupAdmin->getId($partId, $pkField = 'name');
            $partList = $this->_objGroupAdmin->getId($partId, $pkField = 'name');
            if (empty($partList)) {
                $partGroupsId = $this->_objGroupAdmin->addGroup($partId, $partId, $groupId);
                // then add them as subGroups of the parent Group.
                $data = array(
                    'group_id' => $groupId,
                    'subgroup_id' => $partGroupsId
                );
                $newSubGroupId = $this->objLuAdmin->perm->assignSubGroup($data);
                //$newGroupId = $this->_objGroupAdmin->addGroupUser( $partGroupsId, $groupId );
                // Now create the ACLS
                //$this->_objManageGroups->createAcls($partGroupsId, $groupId);
            } else {
                $isSubGroup = $this->_objGroupAdmin->getSubgroups($groupId);
                $check = 0;
                if (!empty($isSubGroup)) {
                    foreach ($isSubGroup[0] as $subgrp) {
                        if ($partId == $subgrp['group_define_name']) {
                            $check = 1;
                        }
                    }
                }
                //If subgroup does not exist, create
                if ($check == 0) {
                    $data = array(
                        'group_id' => $groupId,
                        'subgroup_id' => $partList
                    );
                    $newSubGroupId = $this->objLuAdmin->perm->assignSubGroup($data);
                }
                /*
                  if (!$isGroupMember) {
                  $addGrpUser = $this->_objGroupAdmin->addGroupUser($groupId,$partList);
                  }
                 */
            }
        }
    }

//end function
//Function for managing eportfolio group items/parts

    public function manageEportfolioViewersOld($selectedParts, $groupId) {
        // user Pk id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        foreach ($selectedParts as $partId) {
            $thisId = $this->_objGroupAdmin->getId($partId, $pkField = 'name');
            $partList = $this->_objGroupAdmin->getId($partId, $pkField = 'name');
            if (empty($partList)) {
                $partGroupsId = $this->_objGroupAdmin->addGroup($partId, $partId, $userPid);
                $groupUser = $this->_objGroupAdmin->addGroupUser($partGroupsId, $groupId);
            } else {
                $isGroupMember = $this->_objGroupAdmin->isGroupMember($groupId, $partList);
                if (empty($isGroupMember)) {
                    $this->_objGroupAdmin->addGroupUser($partList, $groupId);
                }
            }
        }
    }

//end function

    public function checkIfExists($partId, $groupId) {
        // user Pk id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        //Get group PidisGroupMember
        $partPid = $this->_objGroupAdmin->getId($partId, $pkField = 'name');
        //Is Member?
        $isGroupMbr = $this->_objGroupAdmin->isGroupMember($groupId, $partPid);
        return $isGroupMbr;
    }

//End Function

    public function deleteGroupUsers($users, $groupId) {
        // Delete these members
        foreach ($users as $partId) {
            $this->_objGroupAdmin->deleteGroupUser($partId['group_id'], $groupId);
        }
        //Empty array
        $selectedParts = array();
    }

//end function

    /**
     * Method to display a completed test.
     * The method displays the test information, the students mark and a list
     * of the questions in the test with the correct answer, the students answer
     * and the lecturers comment on the students answer.
     *
     * @access private
     * @return
     */
    private function showTest() {
        $testId = $this->getParam('id');
        $studentId = $this->getParam('studentId');
        $result = $this->dbResults->getResult($studentId, $testId);
        $test = $this->dbTestadmin->getTests($this->contextCode, 'name, totalmark', $testId);
        $result = array_merge($result[0], $test[0]);
        $totalmark = $this->dbQuestions->sumTotalmark($testId);
        $qNum = $this->getParam('qnum');
        if (empty($qNum)) {
            $data = $this->dbQuestions->getQuestionCorrectAnswer($testId);
        } else {
            $data = $this->dbQuestions->getQuestionCorrectAnswer($testId, $qNum);
        }
        if (!empty($data)) {
            foreach ($data as $key => $line) {
                $marked = $this->dbMarked->getMarked($studentId, $line['questionid'], $testId);
                $data[$key]['studcorrect'] = $marked[0]['correct'];
                $data[$key]['studans'] = $marked[0]['answer'];
                $data[$key]['studorder'] = $marked[0]['answerorder'];
                $data[$key]['studcomment'] = $marked[0]['commenttext'];
            }
        }
        $this->setVarByRef('data', $data);
        $this->setVarByRef('result', $result);
        $this->setVarByRef('totalmark', $totalmark);
        return 'showtest_tpl.php';
    }

}

?>
