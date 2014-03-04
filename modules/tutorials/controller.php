<?php
/* -------------------- tutorials extends controller ----------------*/

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* Module class to create, answer and mark online tutorials
* @copyright (c) 2008 KEWL.NextGen
* @version 1.0
* @package tutorials
* @author Kevin Cyster
*
* $Id: controller.php
*/

class tutorials extends controller
{
    /**
    * @var object $objUser: The user class in the security module
    * @access public
    */
    public $objUser;

    /**
    * @var string $userId: The userId of the current user
    * @access public
    */
    public $userId;

    /**
    * @var string $name: The full name of the current user
    * @access public
    */
    public $name;

    /**
    * @var boolean $isAdmin: TRUE if the user is in the site admin group, FALSE if not
    * @access public
    */
    public $isAdmin;

    /**
    * @var object $objContext: The dbcontext class in the context module
    * @access public
    */
    public $objContext;

    /**
    * @var string $contextCode: The context code if the user is in a context
    * @access public
    */
    public $contextCode;

    /**
    * @var boolean $isLecturer: TRUE if the user is a lecturer for this context, FALSE if not
    * @access public
    */
    public $isLecturer;

    /**
    * @var boolean $isStudent: TRUE if the user is a student for this context, FALSE if not
    * @access public
    */
    public $isStudent;

   /**
    * @var object $objTutDisplay: The display class in the messaging module
    * @access protected
    */
    public $objTutDisplay;

    /**
    * @var object $objDatetime: The dateandtime class in the utilities module
    * @access public
    */
    public $objDatetime;

    /**
    * @var object $objLanguage: The language class in the language module
    * @access public
    */
    public $objLanguage;

    /**
    * @var object $objDbTut: The dbtutorial class in the tutorial module
    * @access public
    */
    public $objDbTutorials;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // system objects
        $this->objUser = $this->getObject('user', 'security');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objDatetime = $this->getObject('dateandtime', 'utilities');
        $this->objLanguage = $this->getObject('language', 'language');

        // system variables
        $this->userId = $this->objUser->userId();
        $this->name = $this->objUser->fullname($this->userId);
        $this->isAdmin = $this->objUser->inAdminGroup($this->userId);
        $this->contextCode = $this->objContext->getContextCode();
        $this->isLecturer = $this->objUser->isContextLecturer();  
        $this->isStudent = $this->objUser->isContextStudent();  
        
        // messaging objects
        $this->objTutDisplay = $this->getObject('tutorialsdisplay', 'tutorials');
        $this->objDbTutorials = $this->getObject('dbtutorials', 'tutorials');

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
    * This is the main method of the class
    * It calls other functions depending on the value of $action
    *
    * @access public
    * @param string $action
    * @return
    */
    public function dispatch($action)
    {
        // Now the main switch statement to pass values for $action
        switch($action){
            case 'home':
                if($this->isLecturer == TRUE || $this->isAdmin == TRUE){
                    $templateContent = $this->objTutDisplay->showLecturerHome();
                }else{
                    $templateContent = $this->objTutDisplay->showStudentHome();
                }
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
                break;
                
            case 'tutorial':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id', NULL);
                    $templateContent = $this->objTutDisplay->showAddEditTut($id);
                    $this->setVarByRef('templateContent', $templateContent);
                    return 'template_tpl.php';
                }
                break;
                
            case 'savetutorial':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id', NULL);
                    $name = $this->getParam('name');
                    $type = $this->getParam('type');
                    $description = $this->getParam('description');
                    $percentage = $this->getParam('percentage');
                    $answerOpen = $this->getParam('answerOpen');
                    $answerClose = $this->getParam('answerClose');
                    $markOpen = $this->getParam('markOpen');
                    $markClose = $this->getParam('markClose');
                    $moderateOpen = $this->getParam('moderateOpen');
                    $moderateClose = $this->getParam('moderateClose');
                    $penalty = $this->getParam('penalty');
                    if(empty($id)){
																						 	//add to activity log
																						 	if($this->eventsEnabled)
																						 	{
																						 		$message = $this->objUser->getsurname(). ' '.$this->objLanguage->languageText('mod_tutorials_added', 'tutorials')."-".$name;
																						 	 	$this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $message,
																																			'link'=> $this->uri(array()),
																																			'contextcode' => $this->objContext->getContextCode(),
																																			'author' => $this->objUser->fullname(),
																																			'description'=>$message));
																						 	}
                        //Add the tutorial
                        $tutorialId = $this->objDbTutorials->addTutorial($name, $type, $description, $percentage, $answerOpen, $answerClose, $markOpen, $markClose, $moderateOpen, $moderateClose, $penalty);
                    }else{
																						 	//add to activity log
																						 	if($this->eventsEnabled)
																						 	{
																						 		$messageed = $this->objUser->getsurname(). ' '.$this->objLanguage->languageText('mod_tutorials_edited', 'tutorials')."-".$name;
																						 	 	$this->eventDispatcher->post($this->objActivityStreamer, "context", array('title'=> $messageed,
																																			'link'=> $this->uri(array()),
																																			'contextcode' => $this->objContext->getContextCode(),
																																			'author' => $this->objUser->fullname(),
																																			'description'=>$messageed));
																						 	}                        
                        //Edit the tutorial
                        $tutorialId = $this->objDbTutorials->editTutorial($id, $name, $type, $description, $percentage, $answerOpen, $answerClose, $markOpen, $markClose, $moderateOpen, $moderateClose, $penalty);
                    }
                    return $this->nextAction('view', array(
                        'id' => $tutorialId,
                    ), 'tutorials');
                }
                break;
                
            case 'view':
                $id = $this->getParam('id');
                if($this->isStudent == TRUE){
                    $status = $this->objTutDisplay->tutStatus($id, TRUE);
                    $markedBy = $this->objDbTutorials->countCompletedMarked($id, $this->userId);
                    $lecturer = $this->objDbTutorials->checkLecturerMarked($id, $this->userId);
                    $error = $this->getParam('error', FALSE);
                    $order = $this->getParam('order', 1);
                    if($status['value'] < 6){
                        return $this->nextAction('', array(), 'tutorials');
                    }elseif($status['value'] == 6){
                        if($markedBy < 3 and $lecturer == FALSE){
                            return $this->nextAction('', array(), 'tutorials');
                        }else{
                            $templateContent = $this->objTutDisplay->showStudentView($id, $order, $error);
                        }
                    }else{
                        if($lecturer == TRUE or $markedBy == 3){
                            $templateContent = $this->objTutDisplay->showStudentView($id, $order, $error);
                        }else{
                            return $this->nextAction('', array(), 'tutorials');
                        }
                    }
                }else{
                    $templateContent = $this->objTutDisplay->showLecturerView($id);
                }
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
                break;
                
            case 'deletetutorial':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $this->objDbTutorials->deleteTutorial($id);
                    return $this->nextAction('', array(
                        'id' => $id,
                    ), 'tutorials');
                }
                break;

            case 'instructions':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $templateContent = $this->objTutDisplay->showInstructions();
                    $this->setVarByRef('templateContent', $templateContent);
                    return 'template_tpl.php';
                }
                break;
                
            case 'saveinstructions':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $instructions = $this->getParam('instructions');
                    $this->objDbTutorials->updateInstructions($instructions);
                    return $this->nextAction('home', array(), 'tutorials');
                }
                break;
                
            case 'deleteinstructions':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $this->objDbTutorials->deleteInstructions();
                    return $this->nextAction('home', array(), 'tutorials');
                }
                break;
                
            case 'questions':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $tutId = $this->getParam('tutId');
                    $id = $this->getParam('id', NULL);
                    $error = $this->getParam('error', NULL);
                    $question = $this->getParam('question', NULL);
                    $model = $this->getParam('model', NULL);
                    $worth = $this->getParam('worth', NULL);
                    $templateContent = $this->objTutDisplay->showAddEditQuestions($tutId, $id, $error, $question, $model, $worth);
                    $this->setVarByRef('templateContent', $templateContent);
                    return 'template_tpl.php';
                }
                break;                
                
            case 'savequestion':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $tutId = $this->getParam('tutId');
                    $id = $this->getParam('id', NULL);
                    $question = $this->getParam('question');
                    $model = $this->getParam('model');
                    $worth = $this->getParam('worth');
                    $add = $this->getParam('submitAdd', NULL);
                    if($question == ''){
                        return $this->nextAction('questions', array(
                            'tutId' => $tutId,
                            'id' => $id,
                            'error' => 'question',
                            'question' => $question,
                            'model' => $model,
                            'worth' => $worth,
                        ), 'tutorials');
                    }
                    if($model == ''){
                        return $this->nextAction('questions', array(
                            'tutId' => $tutId,
                            'id' => $id,
                            'error' => 'model',
                            'question' => $question,
                            'model' => $model,
                            'worth' => $worth,
                        ), 'tutorials');
                    }
                    if(empty($id)){
                        $questionId = $this->objDbTutorials->addQuestion($tutId, $question, $model, $worth);
                    }else{
                        $questionId = $this->objDbTutorials->editQuestion($id, $question, $model, $worth);
                    }
                    if(isset($add)){
                        return $this->nextAction('questions', array(
                            'tutId' => $tutId,
                        ), 'tutorials');
                    }else{
                        return $this->nextAction('view', array(
                            'id' => $tutId,
                        ), 'tutorials');
                    }
                }
                break;
                
            case 'deletequestion':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $tutId = $this->getParam('tutId');
                    $id = $this->getParam('id');
                    $this->objDbTutorials->deleteQuestion($tutId, $id);
                    $this->objDbTutorials->reorderQuestions($tutId);
                    return $this->nextAction('view', array(
                        'id' => $tutId,
                    ), 'tutorials');
                }
                break;

            case 'deleteall':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $tutId = $this->getParam('tutId');
                    $this->objDbTutorials->deleteTutorialQuestions($tutId);
                    return $this->nextAction('view', array(
                        'id' => $tutId,
                    ), 'tutorials');
                }
                break;

            case 'movequestions':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $tutId = $this->getParam('tutId');
                    $id = $this->getParam('id');
                    $dir = $this->getParam('dir');
                    $this->objDbTutorials->moveQuestion($tutId, $id, $dir);
                    return $this->nextAction('view', array(
                        'id' => $tutId,
                    ), 'tutorials');
                }
                break;
                
            case 'answer':
                $id = $this->getParam('id');
                $order = $this->getParam('order', 1);
                $status = $this->objTutDisplay->tutStatus($id, TRUE);
                if($status['value'] == 2){
                    $result = $this->objDbTutorials->addResult($id);
                    if($result == FALSE){
                        return $this->nextAction('', array(), 'tutorials');
                    }elseif($result['has_submitted'] == 0){
                        $templateContent = $this->objTutDisplay->showAnswer($id, $order);
                        $this->setVarByRef('templateContent', $templateContent);
                        $this->setVar('answer', TRUE);
                        return 'template_tpl.php';
                    }else{
                        return $this->nextAction('', array(), 'tutorials');
                    }
                }else{
                    return $this->nextAction('', array(), 'tutorials');
                }
                break;
                
            case 'saveanswer':
                $id = $this->getParam('id');
                $qId = $this->getParam('qId');
                $answer = $this->getParam('answer');
                $order = $this->getParam('order');
                $next = $this->getParam('next', NULL);
                $previous = $this->getParam('previous', NULL);
                $exit = $this->getParam('exit', NULL);
                $submit = $this->getParam('inpSubmit', NULL);
                $this->objDbTutorials->updateAnswers($id, $qId, $answer);
                if(!empty($next)){
                    return $this->nextAction('answer', array(
                        'id' => $id,
                        'order' => ($order + 1),
                        ), 'tutorials');
                }
                if(!empty($previous)){
                    return $this->nextAction('answer', array(
                        'id' => $id,
                        'order' => ($order - 1),
                        ), 'tutorials');
                }
                if(!empty($exit)){
                    return $this->nextAction('', array(), 'tutorials');
                }
                if(!empty($submit)){
                    $this->objDbTutorials->updateSubmitted($id);
                    return $this->nextAction('', array(), 'tutorials');
                }
                break;
                
            case 'liststudents':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $status = $this->getParam('status', NULL);
                    $templateContent = $this->objTutDisplay->showStudentList($id, $status);
                    $this->setVarByRef('templateContent', $templateContent);
                    return 'template_tpl.php';
                 }
                break;
                
            case 'mark':
                if($this->isStudent == TRUE){
                    $id = $this->getParam('id');
                    $results = $this->objDbTutorials->getResult($id, $this->userId);
                    $count = $this->objDbTutorials->getMarkedStudents($id);
                    $status = $this->objTutDisplay->tutStatus($id, TRUE);
                    if($count < 3 and $results['has_submitted'] == 1 and $status['value'] == 4){
                        $studentId = $this->objDbTutorials->getStudentToMark($id);
                        if($studentId != FALSE){
                            $this->objDbTutorials->setStudentToMark($id, $studentId);
                            $order = $this->getParam('order', 1);
                            $error = $this->getParam('error', FALSE);
                            $comment = $this->getParam('comment', NULL);
                            $mark = $this->getParam('mark', NULL);
                            $templateContent = $this->objTutDisplay->showMarking($id, $studentId, $order, $error, $comment, $mark, $this->isStudent);
                            $this->setVarByRef('templateContent', $templateContent);
                            $this->setVar('answer', TRUE);
                            return 'template_tpl.php';
                        }else{
                            return $this->nextAction('', array(), 'tutorials');
                        }
                    }else{
                        return $this->nextAction('', array(), 'tutorials');
                    }
                }else{
                    $id = $this->getParam('id');
                    $studentId = $this->getParam('studentId');
                    $this->objDbTutorials->setStudentToMark($id, $studentId);
                    $order = $this->getParam('order', 1);
                    $error = $this->getParam('error', FALSE);
                    $comment = $this->getParam('comment', NULL);
                    $mark = $this->getParam('mark', NULL);
                    $templateContent = $this->objTutDisplay->showMarking($id, $studentId, $order, $error, $comment, $mark);
                    $this->setVarByRef('templateContent', $templateContent);
                    $this->setVar('answer', TRUE);
                    return 'template_tpl.php';
                 }
                break;
                
            case 'savemarking':
                $id = $this->getParam('id');
                $aId = $this->getParam('aId');
                $sId = $this->getParam('sId');
                $order = $this->getParam('order');
                $comment = $this->getParam('comment');
                $mark = $this->getParam('mark');
                if($comment == ''){
                    if($this->isStudent == TRUE){
                        return $this->nextAction('mark', array(
                            'id' => $id,
                            'order' => $order,
                            'error' => TRUE,
                            'comment' => $comment,
                            'mark' => $mark,
                        ), 'tutorials');
                    }else{
                        return $this->nextAction('mark', array(
                            'id' => $id,
                            'studentId' => $sId,
                            'order' => $order,
                            'error' => TRUE,
                            'comment' => $comment,
                            'mark' => $mark,
                        ), 'tutorials');
                    }
                }
                $next = $this->getParam('next', NULL);
                $previous = $this->getParam('previous', NULL);
                $exit = $this->getParam('exit', NULL);
                $submit = $this->getParam('inpSubmit', NULL);
                $this->objDbTutorials->updateMarking($id, $aId, $sId, $comment, $mark);
                if(!empty($next)){
                    if($this->isStudent == TRUE){
                        return $this->nextAction('mark', array(
                            'id' => $id,
                            'order' => ($order + 1),
                        ), 'tutorials');
                    }else{
                        return $this->nextAction('mark', array(
                            'id' => $id,
                            'studentId' => $sId,
                            'order' => ($order + 1),
                        ), 'tutorials');
                    }
                }
                if(!empty($previous)){
                    if($this->isStudent == TRUE){
                        return $this->nextAction('mark', array(
                            'id' => $id,
                            'order' => ($order - 1),
                        ), 'tutorials');
                    }else{
                        return $this->nextAction('mark', array(
                            'id' => $id,
                            'studentId' => $sId,
                            'order' => ($order - 1),
                        ), 'tutorials');
                    }
                }
                if(!empty($exit)){
                    return $this->nextAction('liststudents', array(
                        'id' => $id,
                    ), 'tutorials');
                }
                if(!empty($submit)){
                    $this->objDbTutorials->updateMarker($id, $sId);
                    if($this->isStudent == TRUE){
                        $this->objDbTutorials->updateStudentMarks($id, $sId);
                        return $this->nextAction('', array(), 'tutorials');
                    }else{
                        $this->objDbTutorials->updateMarks($id, $sId);
                        return $this->nextAction('liststudents', array(
                            'id' => $id,
                        ), 'tutorials');
                    }
                }
                break;
                
            case 'late':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $studentId = $this->getParam('studentId');
                    $mode = $this->getParam('mode', NULL);
                    $templateContent = $this->objTutDisplay->showLate($id, $studentId, $mode);
                    $this->setVarByRef('templateContent', $templateContent);
                    return 'template_tpl.php';
                }
                break;
                 
            case 'savelate':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $studentId = $this->getParam('studentId');
                    $open = $this->getParam('answerOpen');
                    $close = $this->getParam('answerClose');
                    $this->objDbTutorials->updateLate($id, $studentId, $open, $close);
                    return $this->nextAction('late', array(
                        'id' => $id,
                        'studentId' => $studentId,
                    ), 'tutorials');
                 }
                break;

            case 'deletelate':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $tutId = $this->getParam('tutId');
                    $id = $this->getParam('id');
                    $this->objDbTutorials->deleteLate($id);
                    return $this->nextAction('liststudents', array(
                        'id' => $tutId,
                    ), 'tutorials');
                }
                break;
                
            case 'import':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $templateContent = $this->objTutDisplay->showImport($id);
                    $this->setVarByRef('templateContent', $templateContent);
                    return 'template_tpl.php';
                }
                break;
                
            case 'saveimport':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $file = $_FILES;
                    $overwrite = $this->getParam('overwrite');
                    if($file['file']['error'] == 0){
                        $this->objTutDisplay->doImport($id, $file, $overwrite);
                    }
                    return $this->nextAction('view', array(
                        'id' => $id,
                    ), 'tutorials');
                }
                break;
                
            case 'saverequest':
                $id = $this->getParam('id');
                $order = $this->getParam('order');
                $aId = $this->getParam('aId');
                $reason = $this->getParam('reason');
                if($reason == ''){
                    return $this->nextAction('view', array(
                        'id' => $id,
                        'order' => $order,
                        'error' => TRUE,
                    ), 'tutorials');
                }else{
                    $aId = $this->objDbTutorials->addModeration($aId, $reason);
                    return $this->nextAction('view', array(
                        'id' => $id,
                        'order' => $order,
                    ), 'tutorials');
                }
                break;
                
            case 'moderate':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $error = $this->getParam('error', FALSE);
                    $comment = $this->getParam('comment', NULL);
                    $mark = $this->getParam('mark', NULL);
                    $templateContent = $this->objTutDisplay->showModerate($id, $error, $comment, $mark);
                    $this->setVarByRef('templateContent', $templateContent);
                    return 'template_tpl.php';
                }
                break;
                
            case 'savemod':
                $id = $this->getParam('id');
                $aId = $this->getParam('aId');
                $sId = $this->getParam('sId');
                $comment = $this->getParam('comment');
                $mark = $this->getParam('mark');
                $emailList = $this->getParam('emailList');
                $marks = $this->getParam('marks');
                $order = $this->getParam('order');
                if($comment == ''){
                    return $this->nextAction('moderate', array(
                        'id' => $id,
                        'error' => TRUE,
                        'comment' => $comment,
                        'mark' => $mark,
                    ), 'tutorials');
                }
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $this->objDbTutorials->updateMarking($id, $aId, $sId, $comment, $mark);
                    $this->objDbTutorials->updateModeratorMarks($id, $aId, $sId);
                    $this->objDbTutorials->updateModeration($aId);
                    $this->objTutDisplay->emailModeration($id, $order, $emailList, $marks, $mark, $comment);
                    return $this->nextAction('moderate', array(
                        'id' => $id,
                    ), 'tutorials');
                }                
                break;
                
            case 'answerlist':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $order = $this->getParam('order', 1);
                    $num = $this->getParam('num', 1);
                    $templateContent = $this->objTutDisplay->showAnswerList($id, $order, $num);
                    $this->setVarByRef('templateContent', $templateContent);
                    return 'template_tpl.php';
                }
                break;
                
            case 'export':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $templateContent = $this->objTutDisplay->showExport($id);
                    $this->setVarByRef('templateContent', $templateContent);
                    return 'template_tpl.php';
                }
                break;

            case 'doexport':
                if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $type = $this->getParam('type', 1);
                    $status = $this->objTutDisplay->doExport($id, $type);
                    $this->objTutDisplay->emailResults($id);
                    return $this->nextAction('liststudents', array(
                        'id' => $id,
                        'status' => $status,
                    ), 'tutorials');
                }
                break;
                
            case 'archive':
                 if($this->isStudent == TRUE){
                    return $this->nextAction('', array(), 'tutorials');
                }else{
                    $id = $this->getParam('id');
                    $this->objDbTutorials->archiveResults($id);
                    return $this->nextAction('liststudents', array(
                        'id' => $id,
                    ), 'tutorials');
                }
                break;

            default:
                return $this->nextAction('home', array(), 'tutorials');
        }
    }
}
?>
