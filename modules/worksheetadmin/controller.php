<?php
/**
* @package worksheetadmin
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}


/**
* Controller class for the worksheet admin module.
*
* Worksheet provides functionality for lectures to create, edit and delete worksheets and mark
* answered worksheets submitted by the students in the context.
*
* Functionality is provided for students to answer the worksheet and submit it for marking, and
* view the marked worksheet.
*
* @author Megan Watson
* @author Tohir Solomons
* @copyright (c) 2004 UWC
* @package worksheetadmin
* @version 0.2
*/
class worksheetadmin extends controller
{
    /**
    * @var string $action The action parameter from the querystring
    */
   public $action;

    /**
    * Standard constructor method
    */
    public function init()
    {
        // Check if the module is registered and redirect if not.
        // Check if the assignment module is registered and can be linked to.
        $this->objModules =& $this->newObject('modules','modulecatalogue');
        if(!$this->objModules->checkIfRegistered('worksheetadmin')){
            Header('Location: '.$this->uri('','_default'));
        }
        $this->assignment = FALSE;
        if($this->objModules->checkIfRegistered('Assignment Management', 'assignmentadmin')){
            $this->assignment = TRUE;
        }
        $this->rubric = FALSE;
        if($this->objModules->checkIfRegistered(NULL, 'rubric')){
            $this->rubric = TRUE;
        }

        $this->action = $this->getParam('action', NULL);
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objWorksheet = $this->getObject('dbworksheet', 'worksheet');
        $this->objWorksheetQuestions = $this->getObject('dbworksheetquestions', 'worksheet');
        $this->objWorksheetAnswers = $this->getObject('dbworksheetanswers', 'worksheet');
        $this->objWorksheetResults = $this->getObject('dbworksheetresults', 'worksheet');
        $this->objDate = $this->newObject('dateandtime','utilities');
		  $this->objWorksheetDate = $this->newObject('datepicker','htmlelements');
        // User
        $this->objUser = $this->getObject('user', 'security');
        $this->user = $this->objUser->fullname();
        $this->userId = $this->objUser->userId();
        $this->objFile = $this->getObject('upload','filemanager');

        // Problem* $this->objFile->changeTables('tbl_worksheet_filestore','tbl_worksheet_blob');
        $this->objFileErase = $this->getObject('dbfile','filemanager');

        // Context Code
        $this->contextObject = $this->getObject('dbcontext', 'context');
        $this->objContentNodes = $this->getObject('dbcontentnodes', 'context');
        $this->contextCode = $this->contextObject->getContextCode();
        $this->contextTitle = $this->contextObject->getTitle();

        if ($this->contextCode == ''){
            $this->contextCode = 'root';
            $this->contextTitle = $this->objLanguage->languageText('word_inLobby');
        }

        $this->setVarByRef('contextCode', $this->contextCode);
        $this->setVarByRef('contextTitle', $this->contextTitle);

        // Multi Lingualisation
        $this->setVarByRef('objLanguage', $this->objLanguage);

        // Log this call if registered
        if(!$this->objModules->checkIfRegistered('logger', 'logger')){
            //Get the activity logger class
            $this->objLog=$this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();
        }
    }


    /**
    * Standard dispatch method
    */
    public function dispatch()
    {
    		$this->setVar('pageSuppressXML',true);
         switch ($this->action) {
            // Display form to add a new worksheet
            case 'add':
                return $this->addWorksheetForm();

            // Save the new worksheet
            case 'addworksheet':
                $postSubmit = $this->getParam('save', '');
					 $postCancel = $this->getParam('cancel', '');
                if(isset($postCancel) && !empty($postCancel)){
                    return $this->nextAction('');
                }
                return $this->saveNewWorksheet();

            // View worksheet details
            case 'view':
                return $this->viewWorksheet($this->getParam('id'));

            // Display form to edit a worksheet
            case 'edit':
            case 'editworksheet':
                return $this->editWorksheet($this->getParam('id'));

            // Save the edited worksheet
            case 'updateworksheet':
					 $postCancel = $this->getParam('cancel', '');
                if(isset($postCancel) && !empty($postCancel)){
                    return $this->nextAction('');
                }
                return $this->updateWorksheet();

            // Delete a worksheet
            case 'delete':
            case 'deleteworksheet':
                return $this->deleteWorksheet($this->getParam('id'));

            // Display form to add a question to a worksheet
            case 'addquestion':
                return $this->addQuestion($this->getParam('id'));

            // Save a new question
            case 'savequestion':
                $postSubmit = $this->getParam('save', '');
                $postCancel = $this->getParam('cancel','');
                $wid = $this->getParam('worksheet_id');
                if(isset($postCancel) && !empty($postCancel)){
                    return $this->nextAction('view',array('id'=>$wid));
                }
                $array = $this->saveQuestion();
                $postSubmit = $this->getParam('saveadd', '');
                if(isset($postSubmit) && !empty($postSubmit)){
                    return $this->nextAction('addquestion', array('id' => $wid, 'count' => $array['order']));
                }
                return $this->nextAction('view', array('id'=>$this->getParam('worksheet_id')));

            // Save an editted question
            case 'updatequestion':
                $postSubmit = $this->getParam('save', '');
                $postCancel = $this->getParam('cancel','');
                if(isset($postCancel) && !empty($postCancel)){
                    return $this->nextAction('view',array('id'=>$this->getParam('worksheet_id')));
                }
                //Retriving values from the form
                $question=$this->getParam('question');
                $answer=$this->getParam('answer');
                $worth=$this->getParam('worth');
                $id=$this->getParam('id');
                $this->objWorksheetQuestions->updateQuestion($id, $question, $answer, $worth);

                return $this->nextAction('view', array('id'=>$this->getParam('worksheet_id')));

            // Display form to edit a question
            case 'editquestion':
                return $this->editQuestion($this->getParam('id'));

            case 'addimage':
                $questionId = $this->getParam('id', '');
                $worksheetId = $this->updateQuestion('add');
                return $this->nextAction('editquestion', array('worksheet'=>$worksheetId, 'id'=>$questionId));

            case 'removeimage':
                $questionId = $this->getParam('id', '');
                $worksheetId = $this->updateQuestion('remove');
                return $this->nextAction('editquestion', array('worksheet'=>$worksheetId, 'id'=>$questionId));

            // change the editor for the question
            case 'changeeditor':
                $questionId = $this->getParam('id', '');
                $worksheetId = $this->getParam('worksheet_id');
                $editor = $this->getParam('editor');
                if(!empty($questionId)){
                    $worksheetId = $this->updateQuestion('changeeditor');
                }else{
                    $array = $this->saveQuestion();
                    $questionId = $array['questionId'];
                }
                return $this->nextAction('editquestion', array('worksheet'=>$worksheetId, 'id'=>$questionId, 'editor'=>$editor));

            // Delete a question
            case 'deletequestion':
                return $this->deleteQuestion();

            // Move a question up in the order displayed
            case 'questionup':
                return $this->upQuestion();

            // Move a question down in the order displayed
            case 'questiondown':
                return $this->downQuestion();

            // Display a list of submitted worksheets for marking
            case 'mark':
            case 'listworksheet':
                return $this->listWorksheet($this->getParam('id'));

            // Display the first answered question for marking
            case 'markworksheet':
                return $this->firstQuestion();

            //Reopen a worksheet for a student
            case 'reopenworksheet':
                $worksheet_id = $this->getParam('id');
                $userId = $this->getParam('userId');
                $this->objWorksheetResults->reset4Student($userId, $worksheet_id);
                return $this->listWorksheet($worksheet_id);

            // Save the mark and exit or move to the next selected question
            case 'savemark':
                $postSave = $this->getParam('save');
                $postSubmit = $this->getParam('submitmark');
                $postExit = $this->getParam('exit');

                if(isset($postSubmit) && !empty($postSubmit)){
                    $this->saveMark();
                    $this->submitMark();
                }else  if(isset($postSave) && !empty($postSave)){
                    $this->saveMark();
                }else if(isset($postExit) && !empty($postExit)){
                		  return $this->nextAction('listworksheet',array('id'=>$this->getParam('worksheet')));
                }else{
                    $this->saveMark();
                    $postNextAction = $this->getParam('nextaction', 'next');
                    switch($postNextAction){
                        case 'first':
                            return $this->firstQuestion();
                        case 'prev':
                            $order=$this->getParam('order')-1;
                            return $this->getNext($order);
                        case 'next':
                            $order=$this->getParam('order')+1;
                            return $this->getNext($order);
                        case 'last':
                            return $this->getLast();
                    }
                }

                return $this->nextAction('listworksheet',array('id'=>$this->getParam('worksheet')));

            default:
                return $this->worksheetHome();
        }//switch
    } // dispatch

    /**
    * Method to display a list of worksheets in context to the user.
    * @return The template for the worksheet home page.
    */
    public function worksheetHome()
    {
        $ar = $this->objWorksheet->getWorksheetsInContext($this->contextCode);

//echo '<pre>'; print_r($ar); echo'</pre>';

        if(!empty($ar)){
            foreach($ar as $key=>$row){
            	if (isset($row['chapter'])){
                	$sql = "SELECT title FROM tbl_context_nodes WHERE ";
                	$sql .= "id = '".$row['chapter']."'";
					$nodes = $this->objContentNodes->getArray($sql);
                }

                if(!empty($nodes)){
                    $ar[$key]['node'] = $nodes[0]['title'];
                }else{
                    $ar[$key]['node'] = '';
                }
                $ar[$key]['date'] = $this->formatDate($row['closing_date']);
            }
        }

        $this->setVarByRef('ar', $ar);
        //die('1');
        return 'worksheet_home_tpl.php';
    }

    /**
    * Method to display template to add or edit a worksheet.
    * @return The template to add or edit a worksheet.
    */
    public function addWorksheetForm()
    {
        $mode = 'add';
        $this->setVarByRef('mode', $mode);

        $sheet = NULL;
        $this->setVarByRef('sheet', $sheet);

        $nodesSQL = 'SELECT tbNodes.id AS chapter_id, tbNodes.title AS chapter_title FROM tbl_context_nodes AS tbNodes
        INNER JOIN tbl_context_parentnodes AS tbParent ON ( tbNodes.tbl_context_parentnodes_id = tbParent.id )
        WHERE tbParent.tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode = "'.$this->contextCode.'"';// AND tbNodes.parent_Node = "" ';

        $nodes = $this->objContentNodes->getArray($nodesSQL);

        $this->setVarByRef('nodes', $nodes);

        return 'addedit_worksheet_tpl.php';
    }

    /**
    * Method to save a new worksheet in the database.
    * @return The next action: default.
    */
    public function saveNewWorksheet()
    {
        $context = $this->getParam('context', '');
        $chapter = $this->getParam('chapter', '');
        $worksheet_name = $this->getParam('worksheet_name', '');
        $activity_status = 'inactive';
        $percentage = $this->getParam('percentage', 0);
        $closing_date = $this->getParam('closing_date', date('Y-m-d'));
        $description = $this->getParam('description', '');
        $userId = $this->objUser->userId();
        $LastModified = mktime();

        $id = $this->objWorksheet->insertWorkSheet($context, $chapter, $worksheet_name, $activity_status, $percentage, $closing_date, $description, $userId, $LastModified);

        $msg = $this->objLanguage->languageText('mod_worksheetadmin_worksheetsaved','worksheetadmin');
        $this->setSession('confirm', $msg);
        return $this->nextAction('view', array('id'=>$id, 'confirm'=>'yes'));
    }

    /**
    * Method to save the changes to an edited worksheet.
    * @return The next action: view.
    */
    public function updateWorksheet()
    {
        $id = $this->getParam('id', '');
        $chapter = $this->getParam('chapter', '');
        $worksheet_name = $this->getParam('worksheet_name', '');
        $activity_status = $this->getParam('activity_status', '');
        $percentage = $this->getParam('percentage', 0);
        $closing_date = $this->getParam('closing_date', date('Y-m-d'));
        $description = $this->getParam('description', '');
        $userId = $this->objUser->userId();
        $LastModified = mktime();

        $this->objWorksheet->updateWorkSheet($id, $chapter, $worksheet_name, $activity_status, $percentage, $closing_date, $description, $userId, $LastModified);

        $msg = $this->objLanguage->languageText('mod_worksheetadmin_worksheetsaved','worksheetadmin');
        $this->setSession('confirm', $msg);
        return $this->nextAction('view', array('id'=>$id, 'confirm'=>'yes'));
    }

    /**
    * Method to view a worksheets details.
    * @param string $id The id of the worksheet being viewed.
    * @return The template to view a worksheet.
    */
    public function viewWorksheet($id)
    {
        $sheet = $this->objWorksheet->getRow('id', $id);

        if ($sheet == '') {
            return $this->nextAction(NULL);
        }

        $sql = "SELECT title FROM tbl_context_nodes WHERE ";
        $sql .= "id = '".$sheet['chapter']."'";
        $nodes = $this->objContentNodes->getArray($sql);

        if(!empty($nodes)){
            $sheet['node'] = $nodes[0]['title'];
        }else{
            $sheet['node'] = '';
        }
        $sheet['date'] = $this->formatDate($sheet['closing_date']);

        $this->setVarByRef('sheet', $sheet);

        $questions = $this->objWorksheetQuestions->getAll(' WHERE worksheet_id="'.$id.'" ORDER BY question_order');

        $this->setVarByRef('questions', $questions);

        // Set confirmation message
        $confirm = $this->getParam('confirm', NULL);
        if($confirm == 'yes'){
            $msg = $this->getSession('confirm');
            $this->unsetSession('confirm');
            $this->setVar('msg', $msg);
        }

        return 'view_worksheet_tpl.php';
    }

    /**
    * Method to display a template to edit a worksheets details.
    * @param string $id The id of the worksheet being edited.
    * @return The template to add or edit a worksheet.
    */
    public function editWorksheet($id)
    {
        $sheet = $this->objWorksheet->getRow('id', $id);

        if ($sheet == '') {
            return $this->nextAction(NULL);
        }

        $this->setVarByRef('sheet', $sheet);

        $nodesSQL = 'SELECT tbl_context_nodes.id AS chapter_id, tbl_context_nodes.title AS chapter_title FROM tbl_context_nodes
        INNER JOIN tbl_context_parentnodes ON ( tbl_context_parentnodes_id = tbl_context_parentnodes.id )
        WHERE tbl_context_parentnodes.tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode = "'.$this->contextCode.'"';// AND parent_Node = "" ';
        $nodes = $this->objContentNodes->getArray($nodesSQL);

        $this->setVarByRef('nodes', $nodes);

        $mode = 'edit';

        $this->setVarByRef('mode', $mode);

        return 'addedit_worksheet_tpl.php';
    }

    /**
    * Method to delete the worksheet and its associated questions.
    * @param string $id The id of the worksheet to be deleted.
    * @return The next action: default.
    */
    public function deleteWorksheet($id)
    {
    		$data =$this->objWorksheetQuestions->getAll(' WHERE worksheet_id="'.$id.'" ORDER BY question_order');
        	$this->objWorksheet->delete('id',$id);

        // Delete questions associated with worksheet.
        $this->objWorksheetQuestions->delete('worksheet_id',$id);
        foreach ($data as $line ){
        				$this->objWorksheetAnswers->delete('question_id',$line['id']);
        }

        $back = $this->getParam('back');

        if($back){
            //return $this->uri('viewbyletter',array('module'=>'assignment'));
            Header("Location: ".$this->uri(array('action'=>'viewbyletter'),$back));
        }else{
            return $this->nextAction('');
        }
    }

    /**
    * Method to display a template to add a new question.
    * @param string $id The id of the current worksheet.
    * @return The template to add or edit questions.
    */
    public function addQuestion($id)
    {
        $worksheet = $this->objWorksheet->getRow('id', $id);
        $worksheet['num_questions']=$this->getParam('count');

        if ($worksheet == '') {
            return $this->nextAction(NULL);
        }

        $this->setVarByRef('worksheet', $worksheet);

        $mode='add';
        $this->setVarByRef('mode',$mode);

        return 'addedit_question_tpl.php';
    }

    /**
    * Method to insert worksheet question into database.
    * @return The next action: view or addquestion.
    */
    public function saveQuestion()
    {
        $wid = $this->getParam('worksheet_id', '');
        $question = $this->getParam('question', '');
        $answer = $this->getParam('answer', '');
        $worth = $this->getParam('worth', 0);
        $order = $this->objWorksheetQuestions->getLastOrder($wid);
        $userId = $this->objUser->userId();
        $LastModified = date('Y-m-d H:i:s');
        $total = $worth;
        $imageId = '';
        $imageName = '';
        $imConfirm = $this->getParam('imageconfirm');

        // Check for an image and upload it
        if($imConfirm == 'yes'){
            if(isset($_FILES['imagefile']['name']) && !empty($_FILES['imagefile']['name'])){
                $fileId = $this->objFile->uploadFile($_FILES['imagefile']);
                $imageId = $fileId;
                $imageName = $_FILES['imagefile']['name'];
            }
        }

        $questionId = $this->objWorksheetQuestions->insertSingle($wid, $question, $answer, $worth, $order, $userId, $LastModified, $imageId, $imageName);

//         $postQuestion2 = $this->getParam('question2', NULL);
//         if($postQuestion2){
//             $fileId = '';
//             $imageId = '';
//             $imageName = '';
//             $question = $postQuestion2;
//             $answer = $this->getParam('answer2', '');
//             $worth = $this->getParam('worth2', 0);
//             $total = $total+$worth;
//             $order = $order+1;
//             $imConfirm2 = $this->getParam('imageconfirm2');
//
//             // Check for an image and upload it
//             if($imConfirm2 == 'yes'){
//                 if(isset($_FILES['imagefile2']['name']) && !empty($_FILES['imagefile2']['name'])){
//                     $fileId = $this->objFile->uploadFile($_FILES['imagefile2']);
//                     $imageId = $fileId;
//                     $imageName = $_FILES['imagefile2']['name'];
//                 }
//             }
//
//             $orders = $this->objWorksheetQuestions->insertSingle($wid, $question, $answer, $worth, $order, $userId, $LastModified, $imageId, $imageName);
//         }

        // save total
        $this->objWorksheet->setTotal($wid,$total,TRUE);

        return array('order' => $order, 'questionId' => $questionId);
    }

    /**
    * Method to update worksheet question in database.
    * @param string $action The action to take - add or remove an image. Default = null.
    * @return The next action: view.
    */
    public function updateQuestion($action = NULL)
    {
        $wid = $this->getParam('worksheet_id', '');
        $imageFile = NULL;
        $fileId = $this->getParam('fileId', NULL);
        $imConfirm = $this->getParam('imageconfirm');

        // include an image
        if($action == 'add' || ($imConfirm == 'yes' && !empty($_FILES['imagefile']['name']))){
            // Remove original image if exists
            if($fileId){
                $this->objFileErase->deleteFile($fileId);
            }
            $fileId = $this->objFile->uploadFile($_FILES['imagefile']);
            $imageFile['id'] = $fileId;
            $imageFile['name'] = $_FILES['imagefile']['name'];
        }

        // Remove an image
        if($action == 'remove' || ($imConfirm == 'no' && $action != 'add')){
            $this->objFileErase->deleteFile($fileId);
            $imageFile['id'] = '';
            $imageFile['name'] = '';
        }

        $userId = $this->objUser->userId();
		$this->objworksheetquestions = $this->newObject('dbworksheetquestions','worksheet');
        //$this->objWorksheetQuestions->saveRecord('edit', $userId, $imageFile);

        $markOld = $this->getParam('old_worth', 0);
        $markNew = $this->getParam('worth', 0);

        $this->objWorksheet->setTotal($wid,$mark,TRUE);
        
        return $wid;
    }

    /**
    * Method to edit a question in the worksheet.
    * @return The template to add or edit questions.
    */
    public function editQuestion()
    {
        $wid = $this->getParam('worksheet');
        $worksheet= $this->objWorksheet->getRow('id', $wid);

        $this->setVar('worksheet', $worksheet);
		$numQuestions = $this->objWorksheetQuestions->getNumQuestions($wid);
        $questions=$this->objWorksheetQuestions->getQuestion($this->getParam('id'));
        $this->setVar('questions',$questions);

        $mode='edit';
        $this->setVar('mode',$mode);
		$this->setVar('numQuestions',$numQuestions);

        return 'addedit_question_tpl.php';
    }

    /**
    * Method to delete a worksheet question.
    * @return The next action: view.
    */
    public function deleteQuestion()
    {
        $id=$this->getParam('id');
        $worksheet=$this->getParam('worksheet');
        $mark=$this->getParam('mark');
        $this->objWorksheetQuestions->deleteQuestion($id);
        $this->objWorksheet->setTotal($worksheet,-$mark,TRUE);
        return $this->nextAction('view', array('id'=>$worksheet));
    }

    /**
    * Method to move the selected question up in the order.
    * @return The next action: view.
    */
    public function upQuestion()
    {
        $id=$this->getParam('id');
        $worksheet=$this->getParam('worksheet');
        $this->objWorksheetQuestions->changeOrder($id,TRUE);
        return $this->nextAction('view', array('id'=>$worksheet));
    }

    /**
    * Method to move the selected question down in the order.
    * @return The next action: view.
    */
    public function downQuestion()
    {
        $id=$this->getParam('id');
        $worksheet=$this->getParam('worksheet');
        $this->objWorksheetQuestions->changeOrder($id,FALSE);
        return $this->nextAction('view', array('id'=>$worksheet));
    }

    /**
    * Method to list student submissions for a worksheet.
    * @param string $id The id of the worksheet.
    * @return The template for listing student worksheets.
    */
    public function listWorksheet($id)
    {
        $status = $this->getParam('status');
        $worksheet=$this->objWorksheet->getWorksheet($id);
        $results = $this->objWorksheetResults->getResults($id);
        $this->setVarByRef('results', $results);
        $this->setVarByRef('status', $status);
        $this->setVarByRef('worksheetName', $worksheet[0]['name']);
        return 'list_worksheet_tpl.php';
    }

    /**
    * Method to display the first of a students answers for marking.
    * @return The template for marking a worksheet.
    */
    public function firstQuestion()
    {
        $worksheet_id=$this->getParam('worksheet');
        $student_id=$this->getParam('student');

        $worksheet=$this->objWorksheet->getWorksheet($worksheet_id);
        $this->setVarByRef('worksheet',$worksheet);

        $data=$this->objWorksheetQuestions->getFirstQuestion($worksheet_id);
        $answer=$this->objWorksheetAnswers->getAnswer($data[0]['id'], $student_id);

        $answer[0]['answer_id']=$answer[0]['id'];
        $data=array_merge($data[0],$answer[0]);
        $this->setVarByRef('data',$data);
        return 'mark_worksheet_tpl.php';
    }

    /**
    * Method to get the next question and answer for a particular student.
    * @param string $order The position of the next/previous question in the worksheet.
    * @return The template for marking a worksheet.
    */
    public function getNext($order)
    {
        $worksheet_id=$this->getParam('worksheet');
        $student_id=$this->getParam('student');

        $worksheet=$this->objWorksheet->getWorksheet($worksheet_id);
        $this->setVarByRef('worksheet',$worksheet);

        $data=$this->objWorksheetQuestions->getNextQuestion($worksheet_id, $order);
        $answer=$this->objWorksheetAnswers->getAnswer($data[0]['id'], $student_id);

        $answer[0]['answer_id']=$answer[0]['id'];
        $data=array_merge($data[0],$answer[0]);
        $this->setVarByRef('data',$data);

        return 'mark_worksheet_tpl.php';
    }

    /**
    * Method to get the question and answer already marked for a particular student.
    * @return The template for marking a worksheet.
    */
    public function getLast()
    {
        $worksheet_id=$this->getParam('worksheet');
        $student_id=$this->getParam('student');

        $data=$this->objWorksheetAnswers->getMarkedAnswer($worksheet_id, $student_id);

        if(!empty($data)){
            $data[0]['answer_id']=$data[0]['id'];

            $worksheet=$this->objWorksheet->getWorksheet($worksheet_id);
            $this->setVarByRef('worksheet',$worksheet);

            $data[0]['count']=$this->objWorksheetQuestions->getNumQuestions($worksheet_id);

            $this->setVarByRef('data',$data[0]);

            return 'mark_worksheet_tpl.php';
        }else{
            return $this->firstQuestion();
        }
    }

    /**
    * Method to save the mark given to the student for a question and the lecturers comment.
    * @return.
    */
    public function saveMark()
    {
        $id=$this->getParam('answer_id', '');
        $fields=array();
        $fields['mark']=$this->getParam('mark', 0);
        $fields['comments']=$this->getParam('comment', '');
        $fields['dateMarked']=date('Y-m-d H:i:s');
        $fields['lecturer_id']=$this->userId;
        $this->objWorksheetAnswers->insertMark($fields,$id);
    }

    /**
    * Method to add up the marks given to a student for a worksheet and insert the percentage into the results table.
    * @return.
    */
    public function submitMark()
    {
        $worksheet_id=$this->getParam('worksheet');
        //var_dump($worksheet_id);
        $student_id=$this->getParam('student');
			//var_dump($student_id);
        $mark=$this->objWorksheetAnswers->addMarks($worksheet_id,$student_id);
        $total=$this->objWorksheet->getWorksheet($worksheet_id,'total_mark');

        $marks=($mark/$total['total_mark'])*100; //[0] JOC 20090818

        $result_id = $this->objWorksheetResults->getId($student_id,$worksheet_id);

        if($result_id){
            $fields=array();
            $fields['mark']=$marks;

            $this->objWorksheetResults->addResult($fields,$result_id);
        }
    }

    /**
    * Method to take a datetime string and reformat it as text.
    * @param string $date The date in datetime format.
    * @return string $ret The formatted date.
    */
    public function formatDate($date)
    {
        $ret = $this->objDate->formatDate($date);
        return $ret;
    }

} // end of class
?>
