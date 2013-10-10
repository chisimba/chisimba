<?php
/**
 *
* Controller class for the worksheet module.
*
* Worksheet provides functionality for lectures to create, edit and delete worksheets and mark
* answered worksheets submitted by the students in the context.
*
* Functionality is provided for students to answer the worksheet and submit it for marking, and
* view the marked worksheet.
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
 * @package   worksheet
 * @author    Tohir Solomons tsolomons@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 20076 2010-12-21 11:37:14Z joconnor $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Controller class for the worksheet module.
*
* Worksheet provides functionality for lectures to create, edit and delete worksheets and mark
* answered worksheets submitted by the students in the context.
*
* Functionality is provided for students to answer the worksheet and submit it for marking, and
* view the marked worksheet.
*
* @author Tohir Solomons
* @package worksheet
*
*/
class worksheet extends controller
{

    /**
    *
    * @var string $objConfig String object property for holding the
    * configuration object
    * @access public;
    *
    */
    public $objConfig;

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;
    /**
    *
    * @var string $objLog String object property for holding the
    * logger object for logging user activity
    * @access public
    *
    */
    public $objLog;

    /**
    *
    * Intialiser for the worksheet controller
    * @access public
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();

        $this->objWashout = $this->getObject('washout','utilities');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objContext->getContextCode();

        $this->objWorksheet = $this->getObject('dbworksheet', 'worksheet');
        $this->objWorksheetQuestions = $this->getObject('dbworksheetquestions', 'worksheet');
        $this->objWorksheetAnswers = $this->getObject('dbworksheetanswers', 'worksheet');
        $this->objWorksheetResults = $this->getObject('dbworksheetresults', 'worksheet');
								//Include the activity streamer
								//Load Module Catalogue Class
								$this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');

								$this->objContextGroups = $this->getObject('managegroups', 'contextgroups');

								if($this->objModuleCatalogue->checkIfRegistered('activitystreamer'))
								{
									$this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
									$this->eventDispatcher->addObserver ( array ($this->objActivityStreamer, 'postmade' ) );
									$this->eventsEnabled = TRUE;
								} else {
									$this->eventsEnabled = FALSE;
								}
    }

    /**
     * Method to override permissions check
     * @param string $action Name of the Action to be run
     * @return boolean Whether user has permission to access action or not
     */
    public function isValid($action)
    {
        $lecturerActions = array('add', 'deleteworksheet', 'saveworksheet', 'worksheetinfo', 'managequestions', 'savequestion', 'activate', 'updatestatus', 'viewstudentworksheet', 'editquestion', 'preview');

        if (in_array($action, $lecturerActions)) {
            if ($this->objUser->isContextLecturer($this->objUser->userid(),$this->contextCode)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }


    /**
     *
     * The standard dispatch method for the worksheet2 module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     */
    public function dispatch($action='home')
    {
        if ($this->contextCode == '') {
            return $this->nextAction(NULL, array('error'=>'notincontext'), '_default');
        }

        if (!$this->isValid($action)) {
            return $this->nextAction(NULL);
        }

        $this->setLayoutTemplate('context_layout_tpl.php');


        /*
        * Convert the action into a method (alternative to
        * using case selections)
        */
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting
        * from action
        */
        return $this->$method();
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
    function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Method to convert the action parameter into the name of
    * a method of this class.
    *
    * @access private
    * @param string $action The action parameter passed byref
    * @return stromg the name of the method
    *
    */
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__home";
        }
    }



    /*------------- BEGIN: Set of methods to replace case selection ------------*/



    /**
    * Method to show the worksheet home page
    * @access private
    */
    private function __home()
    {
        $worksheets = $this->objWorksheet->getWorksheetsInContext($this->contextCode);
        $this->setVarByRef('worksheets', $worksheets);

        return 'home_tpl.php';
    }

    /**
    * Method to add a worksheet
    * @access private
    */
    private function __add()
    {
        $this->setVar('mode', 'add');

        return 'step1_tpl.php';
    }

	 /**
    * Method to add a worksheet
    * @access private
    */
    private function __edit()
    {
        $this->setVar('mode', 'edit');
		$this->setVar('worksheet', $this->objWorksheet->getWorksheet($this->getParam("id")));
        return 'step1_tpl.php';
    }

	/**
    * Method to save a worksheet
    * @access private
    */
    private function __saveworksheetedit()
    {
        //var_dump($_POST);

        $title = $this->getParam('title');
        $id = $this->getParam('id');
        $description = $this->getParam('description');
        $date = $this->getParam('calendardate');
        $time = $this->getParam('time');
        $percentage = $this->getParam('percentage');

        $activity_status = $this->getParam('activity_status');
        $closing_date = $date.' '.$time;
		$lastUpdated = strftime('%Y-%m-%d %H:%M:%S', mktime());

        $id = $this->objWorksheet->updateWorkSheet($id, $this->contextCode, $title, $activity_status, $percentage, $closing_date, $description, $this->objUser->userId(), $lastUpdated);

        return $this->nextAction('home');
    }

    /**
    * Method to save a worksheet
    * @access private
    */
    private function __saveworksheet()
    {
        //var_dump($_POST);

        $title = $this->getParam('title');
        $description = $this->getParam('description');
        $date = $this->getParam('calendardate');
        $time = $this->getParam('time');
        $percentage = $this->getParam('percentage');

        $activity_status = 'inactive';
        $closing_date = $date.' '.$time;
								//activity streamer, create message and post it
      		$message = $this->objUser->getSurname()." ".$this->objLanguage->languageText('mod_worksheet_newalert', 'worksheet')." ".$this->contextCode;
      	 $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $message,
																		'link'=> $this->uri(array()),
																		'contextcode' => $this->objContext->getContextCode(),
																		'author' => $this->objUser->fullname(),
																		'description'=>$message));

        $id = $this->objWorksheet->insertWorkSheet($this->contextCode, NULL, $title, $activity_status, $percentage, $closing_date, $description );

        return $this->nextAction('managequestions', array('id'=>$id));
    }

    /**
    * Delete the worksheet
    * @access private
    */
    private function __deleteworksheet()
    {
        $id = $this->getParam('id');
        $this->objWorksheet->deleteWorksheet($id);
        return $this->nextAction('home', array());
    }

    /**
    * Method to show the worksheet information, students who have submitted, etc.
    * @access private
    */
    private function __worksheetinfo()
    {
        $this->setVar('mode', 'edit');

        $id = $this->getParam('id');

        $worksheet = $this->objWorksheet->getWorksheet($id);

        if ($worksheet == FALSE) {
            return $this->nextAction(NULL, array('error'=>'unknownworksheet'));
        }

        $this->setVarByRef('id', $id);
        $this->setVarByRef('worksheet', $worksheet);

        $questions = $this->objWorksheetQuestions->getQuestions($id);
        $this->setVarByRef('questions', $questions);

        $worksheetResults = $this->objWorksheetResults->getResults($id);
        $this->setVarByRef('worksheetResults', $worksheetResults);

        return 'worksheetinfo_tpl.php';
    }

    /**
    * Method to add/remove questions to the worksheet
    * @access private
    */
    private function __managequestions()
    {
        $id = $this->getParam('id');

        $worksheet = $this->objWorksheet->getWorksheet($id);

        if ($worksheet == FALSE) {
            return $this->nextAction(NULL);
        }


        if ($worksheet['context'] != $this->contextCode) {
            return $this->nextAction(NULL);
        }

        $this->setVarByRef('id', $id);
        $this->setVarByRef('worksheet', $worksheet);

        $questions = $this->objWorksheetQuestions->getQuestions($id);
        $this->setVarByRef('questions', $questions);

        return 'step2_tpl.php';
    }

    /**
    * Method to save a question
    * @access private
    */
    private function __savequestion()
    {

        $question        = $this->getParam('question');
        $modelanswer     = $this->getParam('modelanswer');
        $question_worth  = $this->getParam('mark');
        $worksheet_id    = $this->getParam('worksheet');

        $result = $this->objWorksheetQuestions->insertSingle($worksheet_id, $question, $modelanswer, $question_worth);

        return $this->nextAction('managequestions', array('msg'=>'questionadded', 'id'=>$worksheet_id, 'question'=>$result));
    }

    /**
    * Method to activate a worksheet - change activity status
    * @access private
    */
    private function __activate()
    {
        $this->setVar('mode', 'edit');

        $id = $this->getParam('id');

        $worksheet = $this->objWorksheet->getWorksheet($id);

        if ($worksheet == FALSE) {
            return $this->nextAction(NULL, array('error'=>'unknownworksheet'));
        }

        $this->setVarByRef('id', $id);
        $this->setVarByRef('worksheet', $worksheet);

        $questions = $this->objWorksheetQuestions->getQuestions($id);
        $this->setVarByRef('questions', $questions);

        return 'step3_tpl.php';
    }

    /**
    * Method to update a worksheet's activity status
    * @access private
    */
    private function __updatestatus()
    {
        $id = $this->getParam('id');
        $activityStatus = $this->getParam('activity_status');
        $closingDate = $this->getParam('calendardate').' '.$this->getParam('time');

        $result = $this->objWorksheet->updateStatus($id, $activityStatus, $closingDate);

        if ($result) {
            return $this->nextAction(NULL, array('message'=>'statusupdate', 'id'=>$id));
        } else {
            return $this->nextAction(NULL, array('error'=>'unabletofindworksheet'));
        }
    }

    /**
    * Method to view a worksheet - student view
    * @access private
    */
    private function __viewworksheet()
    {

        $id = $this->getParam('id');

        $worksheet = $this->objWorksheet->getWorksheet($id);

        if ($worksheet == FALSE) {
            return $this->nextAction(NULL, array('error'=>'unknownworksheet'));
        }

        $this->setVarByRef('id', $id);
        $this->setVarByRef('worksheet', $worksheet);

        $questions = $this->objWorksheetQuestions->getQuestions($id);
        $this->setVarByRef('questions', $questions);

        $worksheetResult = $this->objWorksheetResults->getWorksheetResult($this->objUser->userId(), $id);

        if ($worksheet['activity_status'] == 'open' && !$worksheetResult) {
            //$this->setLayoutTemplate(NULL);
            //$this->setVar('pageSuppressToolbar', TRUE);
            //$this->setVar('pageSuppressBanner', TRUE);
            //$this->setVar('pageSuppressSearch', TRUE);
            //$this->setVar('suppressFooter', TRUE);
            return 'answerworksheet_tpl.php';
        } else {

            $this->setVarByRef('worksheetResult', $worksheetResult);

            return 'viewworksheet_tpl.php';
        }
    }

    /**
    * Method to view a worksheet - lecturer view
    * @access private
    */

    private function __preview()
    {


        $id = $this->getParam('id');

        $worksheet = $this->objWorksheet->getWorksheet($id);

        if ($worksheet == FALSE) {
            return $this->nextAction(NULL, array('error'=>'unknownworksheet'));
        }

        $this->setVarByRef('id', $id);
        $this->setVarByRef('worksheet', $worksheet);

        $questions = $this->objWorksheetQuestions->getQuestions($id);
        $this->setVarByRef('questions', $questions);
        $this->setLayoutTemplate(NULL);
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('pageSuppressSearch', TRUE);
        $this->setVar('suppressFooter', TRUE);
        return 'preview_tpl.php';
    }

    /**
    * Method to save the answers a student submits
    * @access private
    */
    private function __saveanswers()
    {

        $id = $this->getParam('id');

        $worksheet = $this->objWorksheet->getWorksheet($id);

        if ($worksheet == FALSE) {
            return $this->nextAction(NULL, array('error'=>'unknownworksheet'));
        }

        if ($this->getParam('user') != $this->objUser->userId()) {
            return $this->nextAction(NULL, array('error'=>'userswitched'));
        }

        $this->objWorksheetAnswers->saveAnswers($id, $this->objUser->userId());

        if (isset($_POST['saveandclose'])) {

            $this->objWorksheetResults->setWorksheetCompleted($this->objUser->userId(), $id);

            return $this->nextAction(NULL, array('message'=>'worksheetsaved', 'id'=>$id));
        } else {
            return $this->nextAction('viewworksheet', array('message'=>'worksheetsaved', 'id'=>$id));
        }
    }

    /**
    * Method to view the answers a student submitted
    * @access private
    */
    private function __viewstudentworksheet()
    {

        $resultId = $this->getParam('id');

        $result = $this->objWorksheetResults->getRow('id', $resultId);

        if ($result == FALSE) {
            return $this->nextAction(NULL, array('error'=>'resultnotavailable'));
        }

        $worksheet = $this->objWorksheet->getWorksheet($result['worksheet_id']);

        if ($worksheet == FALSE) {
            return $this->nextAction(NULL, array('error'=>'unknownworksheet'));
        }

        $this->setVarByRef('id', $result['worksheet_id']);
        $this->setVarByRef('worksheet', $worksheet);

        $questions = $this->objWorksheetQuestions->getQuestions($result['worksheet_id']);
        $this->setVarByRef('questions', $questions);
        $this->setVarByRef('worksheetResult', $result);

        return 'viewstudentworksheet_tpl.php';
    }

    /**
    * Method to save a lecturer marking a student worksheet
    * @access private
    */
    private function __savestudentmark()
    {
        //var_dump($_POST);

        $student = $this->getParam('student');
        $worksheet = $this->getParam('worksheet');

        $this->objWorksheetAnswers->saveMarks($student, $worksheet, $this->objUser->userId());

        $resultId = $this->objWorksheetResults->getWorksheetResult($student, $worksheet);

        return $this->nextAction('viewstudentworksheet', array('id'=>$resultId['id'], 'message'=>'worksheetmarked'));
    }

    /**
     * Method to edit a question
     */
    private function __editquestion()
    {
        $id = $this->getParam('id');
        $question = $this->objWorksheetQuestions->getQuestion($id);

        if ($question == FALSE) {
            return $this->nextAction(NULL);
        }

        $worksheet = $this->objWorksheet->getWorksheet($question['worksheet_id']);
        $numQuestions = $this->objWorksheetQuestions->getNumQuestions($question['worksheet_id']);

        $this->setVarByRef('question', $question);
        $this->setVarByRef('worksheet', $worksheet);
        $this->setVarByRef('id', $worksheet['id']);
        $this->setVarByRef('numQuestions', $numQuestions);

        return 'editquestion_tpl.php';
    }

    /**
     * Method to update a question
     */
    private function __updatequestion()
    {
        //var_dump($_POST);
        $id = $this->getParam('id');
        $question = $this->getParam('question');
        $modelanswer = $this->getParam('modelanswer');
        $mark = $this->getParam('mark');

        $result = $this->objWorksheetQuestions->updateQuestion($id, $question, $modelanswer, $mark);

        if ($result) {
            $questionInfo = $this->objWorksheetQuestions->getQuestion($id);

            return $this->nextAction('managequestions', array('id'=>$questionInfo['worksheet_id'], 'message'=>'questionupdated', 'question'=>$id));
        } else {
            return $this->nextAction(NULL, array('error'=>'couldnotupdatequestion'));
        }
    }

    /**
     * Method to delete a question
     */
    private function __deletequestion()
    {
        //var_dump($_REQUEST);

        $question = $this->getParam('question');
        $worksheet = $this->getParam('worksheet');

        if ($question == '' || $worksheet == '') {
            return $this->nextAction(NULL, array('error'=>'unabletodeletequestion'));
        }

        $questionInfo = $this->objWorksheetQuestions->getQuestion($question);

        if ($questionInfo == FALSE) {
            return $this->nextAction(NULL, array('error'=>'unabletodeletequestion'));
        }

        $this->objWorksheetQuestions->deleteQuestion($question);

        $this->objWorksheet->updateTotalMark($worksheet);

        return $this->nextAction('managequestions', array('id'=>$worksheet, 'message'=>'questiondeleted'));
    }

    /*------------- END: Set of methods to replace case selection ------------*/
}

?>
