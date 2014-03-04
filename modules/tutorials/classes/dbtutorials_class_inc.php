<?php
/* ----------- data class extends dbTable for tutorials database tables ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the tutorials database tables
* @author Kevin Cyster
*/

class dbtutorials extends dbTable
{
    /**
    * @var object $objLanguage: The language class in the language module
    * @access private
    */
    private $objLanguage;
    
    /**
    * @var object $objUser: The user class in the security module
    * @access private
    */
    private $objUser;
    
    /**
    * @var string $userId: The userid of the currently logged in user
    * @access private
    */
    private $userId;
    
    /**
    * @var object $objContext: The dbcontext class in the context module
    * @access private
    */
    private $objContext;
    
    /**
    * @var object $objGroups: The managegroups class in the contextgroups module
    * @access private
    */
    private $objGroups;
    
    /**
    * @var string $contextCode: The context code of the context the user is currently in
    * @access private
    */
    private $contextCode;
      
    /**
    * @var string $table: The current table name
    * @access private
    */
    private $table;
      
    /**
    * Method to construct the class
    *
    * @access public
    * @return void
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objContext->getContextCode();
        $this->isLecturer = $this->objUser->isContextLecturer();  
        $this->isStudent = $this->objUser->isContextStudent();  
    }
    
/***** Methods for switching tables *****/

    /**
    * Method to switch between tables
    *
    * @access private
    * @param string $table: The table to switch to
    * @return void
    */
    private function _changeTable($table)
    {   
        $this->table = $table;
        parent::init($table);
    }
    
    /**
    * Method to set the tutorials table
    * 
    * @access private
    * @return void
    */
    private function _setTutorials()
    {
        return $this->_changeTable('tbl_tutorials');
    }

    /**
    * Method to set the tutorials questions table
    * 
    * @access private
    * @return void
    */
    private function _setQuestions()
    {
        return $this->_changeTable('tbl_tutorials_questions');
    }

    /**
    * Method to set the tutorials answers table
    * 
    * @access private
    * @return void
    */
    private function _setAnswers()
    {
        return $this->_changeTable('tbl_tutorials_answers');
    }
    
    /**
    * Method to set the tutorials marking table
    * 
    * @access private
    * @return void
    */
    private function _setMarking()
    {
        return $this->_changeTable('tbl_tutorials_marking');
    }
    
    /**
    * Method to set the tutorials marking table
    * 
    * @access private
    * @return void
    */
    private function _setMarker()
    {
        return $this->_changeTable('tbl_tutorials_marker');
    }
    
    /**
    * Method to set the tutorials results table
    * 
    * @access private
    * @return void
    */
    private private function _setResults()
    {
        return $this->_changeTable('tbl_tutorials_results');
    }
    
    /**
    * Method to set the tutorials instructions table
    * 
    * @access private
    * @return void
    */
    private function _setInstructions()
    {
        return $this->_changeTable('tbl_tutorials_instructions');
    }
    
    /**
    * Method to set the tutorials late submissions table
    * 
    * @access private
    * @return void
    */
    private function _setLate()
    {
        return $this->_changeTable('tbl_tutorials_late');
    }
    
    /**
    * Method to set the tutorials audit table
    * 
    * @access private
    * @return void
    */
    private function _setAudit()
    {
        return $this->_changeTable('tbl_tutorials_audit');
    }
    
/***** General methods *****/

    /**
    * Method to get a student to mark
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @return string $studentId: The id of the student to mark
    */
    public function getStudentToMark($tutorialId)
    {
        $studentId = $this->getIncompleteMarked($tutorialId);
        if($studentId == FALSE){
            $studentId = $this->getStudent($tutorialId);
        }
        return $studentId;
    }
    
    /**
    * Method to archive results for a tutorial
    * 
    * @access public
    * @param string $id: The id of the tutorial
    * @return void
    */
    public function archiveResults($id)
    {
        $this->deleteTutorialAnswers($id);
        $this->deleteTutorialMarkers($id);
        $this->deleteTutorialMarking($id);            
        $this->deleteTutorialResults($id);
        $this->deleteTutorialLate($id);
    }
    
/***** Methods for tbl_tutorials *****/

    /**
    * Method to add a tutorial
    *
    * @access public
    * @param string $name: The name of the tutorial
    * @param string $type: The type of tutorial
    * @param string $desc: The description of the tutorial
    * @param string $perc: The percentage of the year mark for this tutorial
    * @param string $ansOpen: The date the answer phase opens
    * @param string $ansClose: The date the answer phase closes
    * @param string $markOpen: The date the marking phase opens
    * @param string $markClose: The date the marking phase closes
    * @param string $modOpen: The date the moderation phase opens
    * @param string $modClose: The date the moderation phase closes
    * @param string $penalty: The penalty pecentage for not marking
    * @return string|bool $id: The id on success | FALSE on failure
    */
    public function addTutorial($name, $type, $desc, $perc, $ansOpen, $ansClose, $markOpen, $markClose, $modOpen, $modClose, $penalty)
    {
        $this->_setTutorials();
        $tableName = $this->table;
        
        $fields = array();
        $fields['contextcode'] = $this->contextCode;
        $fields['name'] = $name;
        $fields['tutorial_type'] = $type;
        $fields['description'] = $desc;
        $fields['percentage'] = $perc;
        $fields['answer_open'] = $ansOpen;
        $fields['answer_close'] = $ansClose;
        $fields['marking_open'] = $markOpen;
        $fields['marking_close'] = $markClose;
        $fields['moderation_open'] = $modOpen;
        $fields['moderation_close'] = $modClose;
        $fields['penalty'] = $penalty;
        $fields['deleted'] = 0;
        $fields['updated'] = date('Y-m-d H:i:s');
        
        $id = $this->insert($fields);
        
        if($id != FALSE){
            foreach($fields as $key=>$field){
                $this->_addAuditRecord($tableName, $id, $key, NULL, $field, 'add');
            }    
        }
    
        return $id;
    }
    
    /**
    * Method to edit a tutorial
    *
    * @access public
    * @param string $id: The id of the tutorial to edit
    * @param string $name: The name of the tutorial
    * @param string $type: The type of tutorial
    * @param string $desc: The description of the tutorial
    * @param string $perc: The percentage of the year mark for this tutorial
    * @param string $ansOpen: The date the answer phase opens
    * @param string $ansClose: The date the answer phase closes
    * @param string $markOpen: The date the marking phase opens
    * @param string $markClose: The date the marking phase closes
    * @param string $modOpen: The date the moderation phase opens
    * @param string $modClose: The date the moderation phase closes
    * @param string $penalty: The penalty for not marking
    * @return string|bool $id: The id on success | FALSE on failure
    */
    public function editTutorial($id, $name, $type, $desc, $perc, $ansOpen, $ansClose, $markOpen, $markClose, $modOpen, $modClose, $penalty)
    {
        $data = $this->getTutorial($id);
        
        $this->_setTutorials();
        $tableName = $this->table;

        $fields = array();
        $fields['name'] = $name;
        $fields['tutorial_type'] = $type;
        $fields['description'] = $desc;
        $fields['percentage'] = $perc;
        $fields['answer_open'] = $ansOpen;
        $fields['answer_close'] = $ansClose;
        $fields['marking_open'] = $markOpen;
        $fields['marking_close'] = $markClose;
        $fields['moderation_open'] = $modOpen;
        $fields['moderation_close'] = $modClose;
        $fields['penalty'] = $penalty;
        $fields['updated'] = date('Y-m-d H:i:s');
        
        $update = $this->update('id', $id, $fields);
        
        if($update != FALSE){
            foreach($fields as $key=>$field){
                if($data[$key] != $field){
                    $this->_addAuditRecord($tableName, $id, $key, $data[$key], $field, 'edit');
                }
            }    
            return $id;
        }
        return FALSE;    
    }
    
    /**
    * Method to get a tutorial
    *
    * @access public
    * @param string $id: The id of the tutorial to retrieve
    * @return array|bool $data: The tutorial data on success | FALSE on failure
    */
    public function getTutorial($id)
    {
        $this->_setTutorials();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE id='".$id."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
    /**
    * Method to get tutorials within a context
    *
    * @access public 
    * @param string $contextCode: The code of the context the tutorials are in
    * @return array|bool $data: The tutorial data on success | FALSE on failure
    */
    public function getContextTuts($contextCode)
    {
        $this->_setTutorials();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE contextcode='".$contextCode."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
    
    /**
    * Method to delete a tutorial
    *
    * @access public
    * @param string $id: The id of the tutorial to detele
    * @return string|bool $id: The tutorial id on success | FALSE on failure
    */
    public function deleteTutorial($id)
    {
        $this->_setTutorials();
        $tableName = $this->table;
        
        $fields = array();
        $fields['deleted'] = 1;
        $fields['updated'] = date('Y-m-d H:i:s');
        
        $updated = $this->update('id', $id, $fields);
        
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $id, 'deleted', 0, 1, 'delete');
            $this->deleteTutorialQuestions($id);
            $this->deleteTutorialAnswers($id);
            $this->deleteTutorialMarkers($id);
            $this->deleteTutorialMarking($id);            
            $this->deleteTutorialResults($id);
            $this->deleteTutorialLate($id);
            return $id;            
        }
        return FALSE;       
    }
    
    /**
    * Method to update the total mark for the tutorial
    *
    * @access private
    * @param string $id: The tutorialId of the tutorial to total marks for
    * @return string|bool $tutorialId: The id of the tutorial on success | FALSE on failure
    */
    public function _updateMarks($id)
    {
        $tutorial = $this->getTutorial($id);
        $questions = $this->getQuestions($id);
        
        $this->_setTutorials();
        $tableName = $this->table;
        
        $total = 0;
        if($questions != FALSE){
            foreach($questions as $question){
                $total = $total + $question['question_value'];
            }
        }
        if($tutorial['total_mark'] != $total){
            $fields = array();
            $fields['total_mark'] = $total;
            $fields['updated'] = date('Y-m-d H:i:s');
            
            $updated = $this->update('id', $id, $fields);
            if($updated != FALSE){
                $this->_addAuditRecord($tableName, $id, 'total_mark', $tutorial['total_mark'], $total, 'edit');            
            }
            return $id;
        }
        return FALSE;
    }
    
/***** Methods for tbl_tutorials_audit *****/

    /** 
    * Method to add an audit record
    *
    * @access private
    * @param string $table: The name of the table affected
    * @param string $id: The id of the affected record
    * @param string $field: The name of the affected field
    * @param string $oldValue: The old value of the field
    * @param string $newValue: The new value of the field
    * @param string $transType: The type of transaction
    * @return string|bool $auditId: The auditId on success | FALSE on failure
    */
    public function _addAuditRecord($table, $id, $field, $oldValue, $newValue, $transType)
    {
        $this->_setAudit();
        $fields = array();
        $fields['contextcode'] = $this->contextCode;
        $fields['table_name'] = $table;
        $fields['record_id'] = $id;
        $fields['field_name'] = $field;
        $fields['old_value'] = $oldValue;
        $fields['new_value'] = $newValue;
        $fields['trans_type'] = $transType;
        $fields['modifier_id'] = $this->userId;
        $fields['date_modified'] = date('Y-m-d H:i:s');
        $fields['updated'] = date('Y-m-d H:i:s');
        
        $auditId = $this->insert($fields);
        
        return $auditId;
    }
    
/***** Methods for tbl_tutorials_instructions *****/

    /** 
    * Method to get tutorial instructions for a course
    *
    * @access public
    * @return array|bool $data: The instruction data on success | FALSE on failure
    */
    public function getInstructions()
    {
        $this->_setInstructions();
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE contextcode='".$this->contextCode."'";
        $sql .= " AND deleted ='0'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
    /**
    * Method to add/edit instructions
    *
    * @access public
    * @param string $instructions: The instructions for the students
    * @return string|bool $id: The id on success | FALSE on failure
    */
    public function updateInstructions($instructions)
    {
        $data = $this->getInstructions();
        
        $this->_setInstructions();
        $tableName = $this->table;

        $fields = array();
        $fields['contextcode'] = $this->contextCode;
        $fields['instructions'] = $instructions;
        $fields['deleted'] = 0;
        $fields['updated'] = date('Y-m-d H:i:s');
        
        if($data == FALSE){
            $id = $this->insert($fields);
            if($id != FALSE){
                foreach($fields as $key=>$field){
                    $this->_addAuditRecord($tableName, $id, $key, NULL, $field, 'add');
                }
                return $id;    
            }            
        }else{
            $updated = $this->update('id', $data['id'], $fields);
            if($updated != FALSE){
                if($data['instructions'] != $fields['instructions']){
                    $this->_addAuditRecord($tableName, $data['id'], 'instructions', $data['instructions'], $fields['instructions'], 'edit');
                }
                return $data['id'];    
            }            
        }
        return FALSE;        
    }
    
    /**
    * Method to delete instructions
    *
    * @access public
    * @return string|bool $id: The id on success | FALSE on failure
    */
    public function deleteInstructions()
    {
        $this->_setInstructions();
        $tableName = $this->table;
        $data = $this->getInstructions();
        
        $fields = array();
        $fields['deleted'] = 1;
        $fields['updated'] = date('Y-m-d H:i:s');
        
        $updated = $this->update('contextcode', $this->contextCode, $fields);
        
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $data['id'], 'deleted', 0, 1, 'delete');
            return $data['id'];            
        }
        return FALSE;       
    }
    
/***** Methods for tbl_tutorials_questions *****/
    
    /** 
    * Method to get questions for a tutorial
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial to get the questions of
    * @return array|bool $data: The questions data on success | FALSE on failure
    */
    public function getQuestions($tutorialId)
    {
        $this->_setQuestions();
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND deleted='0'";
        $sql .= " ORDER BY question_order ASC";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
    
    /** 
    * Method to get a question by id
    *
    * @access public
    * @param string $id: The id of the question to get
    * @return array|bool $data: The questions data on success | FALSE on failure
    */
    public function getQuestionById($id)
    {
        $this->_setQuestions();
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE id='".$id."'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
    /**
    * Method to add a question
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $question: The question text
    * @param string $model: The model answer text
    * @param string $worth: The value of the question
    * @return string|bool $id: The id on success | FALSE on failure
    */
    public function addQuestion($tutorialId, $question, $model, $worth)
    {
        $questions = $this->getQuestions($tutorialId);
        $count = ($questions != FALSE) ? (count($questions) + 1) : 1;
        
        $this->_setQuestions();
        $tableName = $this->table;
        
        $fields = array();
        $fields['tutorial_id'] = $tutorialId;
        $fields['question'] = $question;
        $fields['model_answer'] = $model;
        $fields['question_value'] = $worth;
        $fields['question_order'] = $count;
        $fields['deleted'] = 0;
        $fields['updated'] = date('Y-m-d H:i:s');
        
        $id = $this->insert($fields);
        
        if($id != FALSE){
            foreach($fields as $key=>$field){
                $this->_addAuditRecord($tableName, $id, $key, NULL, $field, 'add');
            }
            $this->_updateMarks($tutorialId);    
            return $id;
        }        
        return FALSE;    
    }
    
    /**
    * Method to edit a question
    *
    * @access public
    * @param string $id: The id of the question to edit
    * @param string $question: Thye question text
    * @param string $model: The model answer text
    * @param string $worth: The value of the question
    * @return string|bool $id: The questionId on success | FALSE on failure
    */
    public function editQuestion($id, $question, $model, $worth)
    {
        $data = $this->getQuestionById($id);
        
        $this->_setQuestions();
        $tableName = $this->table;

        $fields = array();
        $fields['question'] = $question;
        $fields['model_answer'] = $model;
        $fields['question_value'] = $worth;
        $fields['updated'] = date('Y-m-d H:i:s');
        
        $update = $this->update('id', $id, $fields);
        
        if($update != FALSE){
            foreach($fields as $key=>$field){
                if($data[$key] != $field){
                    $this->_addAuditRecord($tableName, $id, $key, $data[$key], $field, 'edit');
                }
            }    
            $this->_updateMarks($data['tutorial_id']);    
            return $id;
        }
        return FALSE;    
    }
    
    /**
    * Method to delete a question
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $id: The id of the question to detele
    * @return string|bool $questionId: The question id on success | FALSE on failure
    */
    public function deleteQuestion($tutorialId, $id)
    {
        $this->_setQuestions();
        $tableName = $this->table;
        
        $fields = array();
        $fields['deleted'] = 1;
        $fields['updated'] = date('Y-m-d H:i:s');
        
        $updated = $this->update('id', $id, $fields);
        
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $id, 'deleted', 0, 1, 'delete');
            $this->_updateMarks($tutorialId);
            return $id;           
        }
        return FALSE;       
    }

    /**
    * Method to delete all tutorial question
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial to detele all questions from
    * @return string|bool $tutorialId: The tutorial id on success | FALSE on failure
    */
    public function deleteTutorialQuestions($tutorialId)
    {
        $questions = $this->getQuestions($tutorialId);
        
        if($questions != FALSE){
            foreach($questions as $question){
                $this->deleteQuestion($tutorialId, $question['id']);
            }
            return $tutorialId;
        }
        return FALSE;       
    }
    
    /**
    * Method to reorder questions
    *
    * @access public
    * @param string $tutorialId: The id of the titorial to reorder questions for
    * @return string|bool $tutorialId: The tutorial id on success | FALSE on failure
    */
    public function reorderQuestions($tutorialId)
    {
        $questions = $this->getQuestions($tutorialId);
        
        if($questions != FALSE){
            foreach($questions as $key => $question){
                $order = $question['question_order'];
                if($order != ($key + 1)){
                    
                    $this->_setQuestions();
                    $tableName = $this->table;

                    $fields = array();
                    $fields['question_order'] = ($key + 1);
                    $fields['updated'] = date('Y-m-d H:i:s');
        
                    $update = $this->update('id', $question['id'], $fields);
        
                    if($update != FALSE){
                        $this->_addAuditRecord($tableName, $question['id'], 'question_order', $order, ($key + 1), 'edit');
                    }
                }
            }
            return $tutorialId;   
        }
        return FALSE;
    }
    
    /**
    * Method to move a question in the question order
    *
    * @access public
    * @param string $tutorialId: The tutorial Id
    * @param string $id: The id of the question
    * @param string $dir: The direction to move
    * @return string|bool $id: The question id on success | FALSE on failure
    */
    public function moveQuestion($tutorialId, $id, $dir)
    {
        $questions = $this->getQuestions($tutorialId);
        
        foreach($questions as $key => $question){
            $order = $question['question_order'];
            if($question['id'] == $id){
                if($dir == 'down'){
                    $nextId = $questions[$key + 1]['id'];
                    $newOrder = ($order + 1);
                }else{
                    $nextId = $questions[$key - 1]['id'];
                    $newOrder = ($order - 1);                   
                }
                $this->_setQuestions();
                $tableName = $this->table;

                $array = array();
                $fields['question_order'] = $newOrder;
                $fields['updated'] = date('Y-m-d H:i:s');
                    
                $updated = $this->update('id', $id, $fields);
                    
                if($updated != FALSE){
                    $this->_addAuditRecord($tableName, $id, 'question_order', $order, $newOrder, 'edit');
                }

                $this->_setQuestions();
                $tableName = $this->table;

                $array = array();
                $fields['question_order'] = $order;
                $fields['updated'] = date('Y-m-d H:i:s');
                    
                $updated = $this->update('id', $nextId, $fields);
                    
                if($updated != FALSE){
                    $this->_addAuditRecord($tableName, $nextId, 'question_order', $newOrder, $order, 'edit');
                }
                return $id;
            }
        }
        return FALSE;
    }
    
    /**
    * Method to get questions and their associated answers for a student
    * 
    * @access public
    * @param string $tutorialId: The tutorial id
    * @return array|bool $data: The question and answer array on success | FALSE on failure
    */
    public function getStudentLinkData($tutorialId)
    {
        $this->_setQuestions();
        $sql = "SELECT * FROM ".$this->table." AS questions";
        $sql .= " RIGHT JOIN tbl_tutorials_answers AS answers";
        $sql .= " ON questions.id=answers.question_id";
        $sql .= " WHERE questions.tutorial_id='".$tutorialId."'";
        $sql .= " AND questions.deleted='0'";
        $sql .= " AND answers.student_id='".$this->userId."'";
        $sql .= " AND answers.deleted='0'";
        $sql .= " ORDER BY questions.question_order ASC";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;        
    }
    
    /**
    * Method to get questions and their associated answers with the associated marking for a student
    * 
    * @access public
    * @param string $tutorialId: The tutorial id
    * @param string $studentId: The id of the student
    * @return array|bool $data: The question and answer array on success | FALSE on failure
    */
    public function getMarkerLinkData($tutorialId, $studentId)
    {
        $this->_setQuestions();
        $sql = "SELECT * FROM ".$this->table." AS questions";
        $sql .= " LEFT JOIN tbl_tutorials_answers AS answers";
        $sql .= " ON questions.id=answers.question_id";
        $sql .= " LEFT JOIN tbl_tutorials_marking AS marking";
        $sql .= " ON answers.id=marking.answer_id";
        $sql .= " WHERE questions.tutorial_id='".$tutorialId."'";
        $sql .= " AND questions.deleted='0'";
        $sql .= " AND answers.student_id='".$studentId."'";
        $sql .= " AND answers.deleted='0'";
        $sql .= " AND marking.marker_id='".$this->userId."'";
        $sql .= " AND marking.deleted='0'";
        $sql .= " ORDER BY questions.question_order ASC";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;        
    }
    
/***** Methods for tbl_tutorial_results *****/

    /**
    * Method to get the students results for a tutorial
    *
    * @access public
    * @param string $tutorialId: The tutorial id
    * @param string $studentId: The student id
    * @return array|bool $data: The results array on success | FALSE on failure
    */
    public function getResult($tutorialId, $studentId)
    {
        $this->_setResults();
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND student_id='".$studentId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
    /**
    * Method to get the students results for a tutorial for export
    *
    * @access public
    * @param string $tutorialId: The tutorial id
    * @param string $studentId: The student id
    * @return array|bool $data: The results array on success | FALSE on failure
    */
    public function getResultsForExport($tutorialId)
    {
        $this->_setResults();
        $sql = "SELECT * FROM ".$this->table." AS results";
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
    
    /**
    * Method to add a result
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @return array|bool $result: The result array on success | FALSE on failure
    */
    public function addResult($tutorialId)
    {
        $result = $this->getResult($tutorialId, $this->userId);
        
        if($result == FALSE){
            $this->_setResults();
            $tableName = $this->table;
        
            $fields = array();
            $fields['tutorial_id'] = $tutorialId;
            $fields['student_id'] = $this->userId;
            $fields['has_submitted'] = 0;            
            $fields['deleted'] = 0;
            $fields['updated'] = date('Y-m-d H:i:s');
        
            $id = $this->insert($fields);
            if($id != FALSE){
                foreach($fields as $key => $field){
                    $this->_addAuditRecord($tableName, $id, $key, NULL, $field, 'add'); 
                }
                $result = $this->getResult($tutorialId, $this->userId);
                return $result;
            }
        }else{
            return $result;
        }
        return FALSE;
    }

    /**
    * Method to get a student to mark
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @return string|bool $studentId: The id of the student to mark on sucess | FALSE on failure
    */
    public function getStudent($tutorialId)
    {
        $this->_setResults();

        $sql = "SELECT *, results.student_id AS sid,";
        $sql .= "COUNT(results.student_id) AS cnt";
        $sql .= " FROM ".$this->table." AS results";
        $sql .= " LEFT JOIN tbl_tutorials_marker AS marker";
        $sql .= " ON (results.tutorial_id=marker.tutorial_id";
        $sql .= " AND results.student_id=marker.student_id)";
        $sql .= " WHERE results.tutorial_id='".$tutorialId."'";
        $sql .= " AND results.student_id!='".$this->userId."'";
        $sql .= " AND results.has_submitted='1'";
        $sql .= " AND (marker.is_lecturer='0' OR marker.is_lecturer IS NULL)";
        $sql .= " AND (marker.marker_id!='".$this->userId."' OR marker.marker_id IS NULL)";
        $sql .= " AND results.deleted='0'";
        $sql .= " GROUP BY results.student_id";

        $data = $this->getArray($sql);      
        if($data != FALSE){
            foreach($data as $key => $line){
                if($line['cnt'] >= 3){
                    unset($data[$key]);
                }
            }
            if(empty($data)){
                return FALSE;
            }
            shuffle($data);
            $studentId = $data[0]['sid'];
            return $studentId;  
        }
        return FALSE;
    }

    /**
    * Method to update submission indicator
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $questionId: The id of the question
    * @param string $answer: The answer to the tutorial question
    * @return string|bool $id: The result id on success | FALSE on failure
    */
    public function updateSubmitted($tutorialId)
    {
        $result = $this->getResult($tutorialId, $this->userId);
        
        $this->_setResults();
        $tableName = $this->table;

        $fields = array();
        $fields['has_submitted'] = 1;
        $fields['updated'] = date('Y-m-d H:i:s');
            
        $updated = $this->update('id', $result['id'], $fields);
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $result['id'], 'has_submitted', $result['has_submitted'], 1, 'edit');
            return $result['id'];           
        }
        return FALSE;
    }

    /**
    * Method to update results for lecturer marking
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $questionId: The id of the question
    * @param string $answer: The answer to the tutorial question
    * @return string|bool $id: The answer id on success | FALSE on failure
    */
    public function updateMarks($tutorialId, $studentId)
    {
        $result = $this->getResult($tutorialId, $studentId);
        $marked = $this->getUsersMarkingForStudent($tutorialId, $studentId);
        
        $this->_setResults();
        $tableName = $this->table;
        
        $total = 0;
        foreach($marked as $mark){
            $total = $total + $mark['mark'];
        }

        $fields = array();
        $fields['mark_obtained'] = $total;
        $fields['updated'] = date('Y-m-d H:i:s');
            
        $updated = $this->update('id', $result['id'], $fields);
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $result['id'], 'mark_obtained', $result['mark_obtained'], $total, 'edit');
            return $result['id'];           
        }
        return FALSE;
    }

    /**
    * Method to update results for peer marking
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $questionId: The id of the question
    * @param string $answer: The answer to the tutorial question
    * @return string|bool $id: The answer id on success | FALSE on failure
    */
    public function updateStudentMarks($tutorialId, $studentId)
    {
        $result = $this->getResult($tutorialId, $studentId);
        $marked = $this->getUsersMarkingForStudent($tutorialId, $studentId);
        $count = $this->countCompletedMarked($tutorialId, $studentId);
        
        $this->_setResults();
        $tableName = $this->table;
        
        $score = 0;
        foreach($marked as $mark){
            $score = $score + $mark['mark'];
        }
        
        switch($count){
            case 1:
                $total = $score;
                break;
            case 2:
                $total = ($result['mark_obtained'] + $score) / 2;
                break;
            case 3:
                $total = (($result['mark_obtained'] * 2) + $score) / 3;
                break;
        }
        
        $fields = array();
        $fields['mark_obtained'] = $total;
        $fields['updated'] = date('Y-m-d H:i:s');
            
        $updated = $this->update('id', $result['id'], $fields);
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $result['id'], 'mark_obtained', $result['mark_obtained'], $total, 'edit');
            return $result['id'];           
        }
        return FALSE;
    }

    /**
    * Method to update results for moderator marking
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $questionId: The id of the question
    * @param string $answer: The answer to the tutorial question
    * @return string|bool $id: The answer id on success | FALSE on failure
    */
    public function updateModeratorMarks($tutorialId, $answerId, $studentId)
    {
        $result = $this->getResult($tutorialId, $studentId);
        $marked = $this->getMarkingForStudentAnswer($answerId, $studentId);
        
        $this->_setResults();
        $tableName = $this->table;
        
        $ave = 0;
        foreach($marked as $mark){
            if($mark['is_moderator'] == 1){
                $score = $mark['mark'];
            }else{
                $ave = ($ave + $mark['mark']);
            }
        }
        $ave = ($ave / 3);
        
        $total = ($result['mark_obtained'] + $score - $ave);
    
        $fields = array();
        $fields['mark_obtained'] = $total;
        $fields['updated'] = date('Y-m-d H:i:s');
            
        $updated = $this->update('id', $result['id'], $fields);
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $result['id'], 'mark_obtained', $result['mark_obtained'], $total, 'edit');
            return $result['id'];           
        }
        return FALSE;
    }

    /**
    * Method to delete tutorial results
    *
    * @access private
    * @param string $tutorialId: The tutorial id
    * @return string|bool $tutorialId: The tutorial id on success | FALSE on failure
    */
    public function deleteTutorialResults($tutorialId)
    {
        $this->_setResults();
        $tableName = $this->table;
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            foreach($data as $line){
                $this->_setResults();
                
                $fields = array();
                $fields['deleted'] = 1;
                $fields['updated'] = date('Y-m-d H:i:s');
                
                $updated = $this->update('id', $line['id'], $fields);
                if($updated != FALSE){
                    $this->_addAuditRecord($tableName, $line['id'], 'deleted', 0, 1, 'delete');
                }
            }
            return $tutorialId;   
        }
        return FALSE;
    }

/***** Methods for tbl_tutorial_answers *****/

    /**
    * Method to get the students answer for a question
    *
    * @access public
    * @param string $questionId: The id of the question
    * @param string $studentId: The id of the student
    * @return array|bool $data: The answer array on success | FALSE on failure
    */
    public function getAnswer($questionId, $studentId)
    {
        $this->_setAnswers();
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE question_id='".$questionId."'";
        $sql .= " AND student_id='".$studentId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
    /**
    * Method to get the all the answers for a question
    *
    * @access public
    * @param string $questionId: The id of the question
    * @param string $number: The starting number of the answers to retrieve
    * @return array|bool $data: The answer array on success | FALSE on failure
    */
    public function getAnswers($questionId, $number)
    {
        $this->_setAnswers();
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE question_id='".$questionId."'";
        $sql .= " AND deleted='0'";
        $sql .= " ORDER BY updated ASC";
        
        $answers = $this->getArray($sql);
        if(!empty($answers)){
            $data = array_slice($answers, ($number - 1), 10);
            $data[0]['count'] = count($answers);
            return $data;
        }
        return FALSE;
    }
    
    /**
    * Method to add/edit answers
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $questionId: The id of the question
    * @param string $answer: The answer to the tutorial question
    * @return string|bool $id: The answer id on success | FALSE on failure
    */
    public function updateAnswers($tutorialId, $questionId, $studentAnswer)
    {
        $answer = $this->getAnswer($questionId, $this->userId);
        
        $this->_setAnswers();
        $tableName = $this->table;

        $fields = array();
        $fields['tutorial_id'] = $tutorialId;
        $fields['question_id'] = $questionId;
        $fields['student_id'] = $this->userId;
        $fields['answer'] = $studentAnswer;
        $fields['moderation_complete'] = 0;
        $fields['deleted'] = 0;
        $fields['updated'] = date('Y-m-d H:i:s');
            
        if($answer == FALSE){         
            $id = $this->insert($fields);
            if($id != FALSE){
                foreach($fields as $key => $field){
                    $this->_addAuditRecord($tableName, $id, $key, NULL, $field, 'add'); 
                }
                return $id;               
            }
        }else{
            $updated = $this->update('id', $answer['id'], $fields);
            if($updated != FALSE){
                if($answer['answer'] != $fields['answer']){
                    $this->_addAuditRecord($tableName, $answer['id'], 'answer', $answer['answer'], $fields['answer'], 'edit');
                }
                return $answer['id'];    
            }            
        }
        return FALSE;
    }
    
    /**
    * Method to add moderation request
    *
    * @access public
    * @param string $id: The id of the answer
    * @param string $reason: The reason for the moderation request
    * @return string|bool $id: The answer id on success | FALSE on failure
    */
    public function addModeration($id, $reason)
    {
        $this->_setAnswers();
        $tableName = $this->table;

        $fields = array();
        $fields['moderation_reason'] = $reason;
        $fields['updated'] = date('Y-m-d H:i:s');
            
        $updated = $this->update('id', $id, $fields);
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $id, 'moderation_reason', NULL, $reason, 'edit');
            return $id;                
        }
        return FALSE;
    }
    
    /**
    * Method to update moderation completed indicator
    *
    * @access public
    * @param string $id: The id of the answer
    * @param string $reason: The reason for the moderation request
    * @return string|bool $id: The answer id on success | FALSE on failure
    */
    public function updateModeration($id)
    {
        $this->_setAnswers();
        $tableName = $this->table;

        $fields = array();
        $fields['moderation_complete'] = 1;
        $fields['updated'] = date('Y-m-d H:i:s');
            
        $updated = $this->update('id', $id, $fields);
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $id, 'moderation_complete', NULL, '1', 'edit');
            return $id;                
        }
        return FALSE;
    }
    
    /**
    * Method to get moderation requests
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @return string|bool $id: The answer id on success | FALSE on failure
    */
    public function getModerationRequests($tutorialId)
    {
        $this->_setAnswers();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND moderation_reason IS NOT NULL";
        $sql .= " AND moderation_complete='0'";
        $sql .= " AND deleted='0'";
        $sql .= " ORDER BY student_id ASC";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;   
        }        
        return FALSE;
    }
    
    /**
    * Method to check if moderation for a user is complete
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @return bool $mod: TRUE if no outstanding moderation requests | FALSE on failure
    */
    public function moderationComplete($tutorialId)
    {
        $this->_setAnswers();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND student_id='".$this->userId."'";
        $sql .= " AND moderation_reason IS NOT NULL";
        $sql .= " AND moderation_complete='0'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return FALSE;   
        }        
        return TRUE;
    }
    
    /**
    * Method to delete tutorial answers
    *
    * @access private
    * @param string $tutorialId: The tutorial id
    * @return string|bool $tutorialId: The tutorial id on success | FALSE on failure
    */
    public function deleteTutorialAnswers($tutorialId)
    {
        $this->_setAnswers();
        $tableName = $this->table;
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            foreach($data as $line){
                $this->_setAnswers();

                $fields = array();
                $fields['deleted'] = 1;
                $fields['updated'] = date('Y-m-d H:i:s');
                
                $updated = $this->update('id', $line['id'], $fields);
                if($updated != FALSE){
                    $this->_addAuditRecord($tableName, $line['id'], 'deleted', 0, 1, 'delete');
                }
            }
            return $tutorialId;   
        }
        return FALSE;
    }
    
/***** Methods for tbl_tutorial_marking *****/

    /**
    * Method to get the user's marking for a student's tutorial
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $studentId: The id of the student
    * @return array|bool $data: The marking array on success | FALSE on failure
    */
    public function getUsersMarkingForStudent($tutorialId, $studentId)
    {
        $this->_setMarking();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND student_id='".$studentId."'";
        $sql .= " AND marker_id='".$this->userId."'";
        $sql .= " AND deleted='0'";
         
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
    
    /**
    * Method to get the marking for a student's answer
    *
    * @access public
    * @param string $answerId: The id of the answer
    * @param string $studentId: The id of the student
    * @return array|bool $data: The marking array on success | FALSE on failure
    */
    public function getMarkingForStudentAnswer($answerId, $studentId)
    {
        $this->_setMarking();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE answer_id='".$answerId."'";
        $sql .= " AND student_id='".$studentId."'";
        $sql .= " AND deleted='0'";
        $sql .= " ORDER BY is_moderator ASC";
         
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
    
    /**
    * Method to get the user's marking for a student's answer
    *
    * @access public
    * @param string $answerId: The id of the students andwer
    * @param string $markerId: The id of the marker
    * @return array|bool $data: The marking array on success | FALSE on failure
    */
    public function getUsersMarkingForAnswer($answerId, $markerId)
    {
        $this->_setMarking();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE answer_id='".$answerId."'";
        $sql .= " AND marker_id='".$markerId."'";
        $sql .= " AND deleted='0'";
         
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
    /**
    * Method to add/edit marking
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $answerId: The id of the answers
    * @param string $studentId: The id of the student
    * @param string $comment: The comment on the answer
    * @param string $mark: The mark given
    * @param bool $isMod: TRUE if the user is a moderator | FALSE if not
    * @return string|bool $id: The answer id on success | FALSE on failure
    */
    public function updateMarking($tutorialId, $answerId, $studentId, $comment, $mark)
    {
        $marking = $this->getUsersMarkingForAnswer($answerId, $this->userId);
        
        $this->_setMarking();
        $tableName = $this->table;

        $fields = array();
        $fields['tutorial_id'] = $tutorialId;
        $fields['answer_id'] = $answerId;
        $fields['student_id'] = $studentId;
        $fields['mark'] = $mark;
        $fields['markers_comment'] = $comment;
        $fields['marker_id'] = $this->userId;
        if($this->isLecturer == TRUE){
            $fields['is_moderator'] = 1;
        }else{
            $fields['is_moderator'] = 0;
        }
        $fields['deleted'] = 0;
        $fields['updated'] = date('Y-m-d H:i:s');
            
        if($marking == FALSE){         
            $id = $this->insert($fields);
            if($id != FALSE){
                foreach($fields as $key => $field){
                    $this->_addAuditRecord($tableName, $id, $key, NULL, $field, 'add'); 
                }
                return $id;               
            }
        }else{
            $updated = $this->update('id', $marking['id'], $fields);
            if($updated != FALSE){
                foreach($fields as $key => $field){
                    if($fields[$key] != $marking[$key]){
                        $this->_addAuditRecord($tableName, $marking['id'], $key, $marking[$key], $fields[$key], 'edit');
                    }
                }
                return $marking['id'];    
            }            
        }
        return FALSE;
    }
    
    /**
    * Method to delete tutorial marking
    *
    * @access private
    * @param string $tutorialId: The tutorial id
    * @return string|bool $tutorialId: The tutorial id on success | FALSE on failure
    */
    public function deleteTutorialMarking($tutorialId)
    {
        $this->_setMarking();
        $tableName = $this->table;
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            foreach($data as $line){
                $this->_setMarking();
                
                $fields = array();
                $fields['deleted'] = 1;
                $fields['updated'] = date('Y-m-d H:i:s');
                
                $updated = $this->update('id', $line['id'], $fields);
                if($updated != FALSE){
                    $this->_addAuditRecord($tableName, $line['id'], 'deleted', 0, 1, 'delete');
                }
            }
            return $tutorialId;   
        }
        return FALSE;
    }

/***** Methods for tbl_tutorials_late *****/

    /**
    * Method to get the late submissions for a student
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param stringt $studentId: The id of the student
    * @return array|bool $data: The late submission data on success | FALSE on failure
    */
    public function getLate($tutorialId, $studentId)
    {
        $this->_setLate();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND student_id='".$studentId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data['0'];
        }
        return FALSE;
    }

    /**
    * Method to add/edit late submissions
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $studentId: The id of the student
    * @param string $answerOpen: The date late submissions open
    * @param string $answerClose: The date late submissions close
    * @return string|bool $id: The late submission id on success | FALSE on failure
    */
    public function updateLate($tutorialId, $studentId, $answerOpen, $answerClose)
    {
        $late = $this->getLate($tutorialId, $studentId);
        
        $this->_setLate();
        $tableName = $this->table;

        $fields = array();
        $fields['tutorial_id'] = $tutorialId;
        $fields['student_id'] = $studentId;
        $fields['answer_open'] = $answerOpen;
        $fields['answer_close'] = $answerClose;
        $fields['deleted'] = 0;
        $fields['updated'] = date('Y-m-d H:i:s');

        if($late == FALSE){         
            $id = $this->insert($fields);
            if($id != FALSE){
                foreach($fields as $key => $field){
                    $this->_addAuditRecord($tableName, $id, $key, NULL, $field, 'add'); 
                }
                return $id;               
            }
        }else{
            $updated = $this->update('id', $late['id'], $fields);
            if($updated != FALSE){
                foreach($fields as $key => $field){
                    if($fields[$key] != $marking[$key]){
                        $this->_addAuditRecord($tableName, $late['id'], $key, $late[$key], $fields[$key], 'edit');
                    }
                }
                return $late['id'];    
            }            
        }
        return FALSE;
    }
    
    /**
    * Method to delete a late submission
    *
    * @access public
    * @param string $id: The id of the late submission to delete
    * @return string|bool $id: The late submission id on success | FALSE on failure
    */
    public function deleteLate($id)
    {
        $this->_setLate();
        $tableName = $this->table;
        
        $fields = array();
        $fields['deleted'] = 1;
        $fields['updated'] = date('Y-m-d H:i:s');
        
        $updated = $this->update('id', $id, $fields);
        
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $id, 'deleted', 0, 1, 'delete');
            return $id;           
        }
        return FALSE;       
    }

    /**
    * Method to delete tutorial late submissions
    *
    * @access private
    * @param string $id: The tutorial id
    * @return string|bool $id: The tutorial id on success | FALSE on failure
    */
    public function deleteTutorialLate($tutorialId)
    {
        $this->_setLate();
        $tableName = $this->table;
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            foreach($data as $line){
                $this->_setLate();

                $fields = array();
                $fields['deleted'] = 1;
                $fields['updated'] = date('Y-m-d H:i:s');
                
                $updated = $this->update('id', $line['id'], $fields);
                if($updated != FALSE){
                    $this->_addAuditRecord($tableName, $line['id'], 'deleted', 0, 1, 'delete');
                }
            }
            return $tutorialId;   
        }
        return FALSE;
    }
    
/***** Methods for tbl_tutorials_marker *****/

    /**
    * Method to get an incompletly marked student's tutorial
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @return string|bool $studentId: The id of the student to mark on sucess | FALSE on failure
    */
    public function getIncompleteMarked($tutorialId)
    {
        $this->_setMarker();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND marker_id='".$this->userId."'";
        $sql .= " AND is_completed='0'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            $studentId = $data[0]['student_id'];
            return $studentId;
        }
        return FALSE;
    }
    
    /**
    * Method to check if a student has been marked by the user
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $studentId: The id of the student
    * @return string|bool $marked: The id of the record if the student has been marked | FALSE if not
    */
    public function checkMarked($tutorialId, $studentId)
    {
        $this->_setMarker();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND student_id='".$studentId."'";
        $sql .= " AND marker_id='".$this->userId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            return $data[0]['id'];
        }
        return FALSE;        
    }
    
    /**
    * Method to check if a lecturer has marked a student's tutorial
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $studentId: The id of the student
    * @return string|bool $marked: The count of the records | ZERO if none
    */
    public function checkLecturerMarked($tutorialId, $studentId)
    {
        $this->_setMarker();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND student_id='".$studentId."'";
        $sql .= " AND is_lecturer='1'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            return $data[0]['id'];
        }
        return FALSE;        
    }
    
    /**
    * Method to count the number of times a student has been selected to be marked
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $studentId: The id of the student
    * @return string|bool $marked: The count of the records | ZERO if none
    */
    public function countMarked($tutorialId, $studentId)
    {
        $this->_setMarker();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND student_id='".$studentId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            return count($data);
        }
        return 0;        
    }
    
    /**
    * Method to count the number of times a student has been completely marked
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $studentId: The id of the student
    * @return string|bool $marked: The count of the records | ZERO if none
    */
    public function countCompletedMarked($tutorialId, $studentId)
    {
        $this->_setMarker();
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND student_id='".$studentId."'";
        $sql .= " AND is_completed='1'";
        $sql .= " AND is_lecturer='0'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            return count($data);
        }
        return 0;        
    }
    
    /**
    * Method to add a student to mark
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $studentId: The id of the student
    * @return string/bool $id: The marker id on success | FALSE on failure
    */
    public function setStudentToMark($tutorialId, $studentId)
    {
        $id = $this->checkMarked($tutorialId, $studentId);
        if($id == FALSE){
            $this->_setMarker();
            $tableName = $this->table;
        
            $fields = array();
            $fields['tutorial_id'] = $tutorialId;
            $fields['student_id'] = $studentId;
            $fields['marker_id'] = $this->userId;
            $fields['is_completed'] = 0;
            $fields['is_lecturer'] = 0;
            $fields['deleted'] = 0;
            $fields['updated'] = date('Y-m-d H:i:s');
            
            $id = $this->insert($fields);
            if($id != FALSE){
                foreach($fields as $key => $field){
                    $this->_addAuditRecord($tableName, $id, $key, NULL, $field, 'add'); 
                }
            }        
        }
        return $id;               
    }

    /**
    * Method to update is completed indicator
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @param string $studentId: The id of the student
    * @return string|bool $id: The marker id on success | FALSE on failure
    */
    public function updateMarker($tutorialId, $studentId)
    {
        $id = $this->checkMarked($tutorialId, $studentId);
        
        $this->_setMarker();
        $tableName = $this->table;

        $fields = array();
        $fields['is_completed'] = 1;
        if($this->isLecturer == TRUE){
            $fields['is_lecturer'] = 1;
        }else{
            $fields['is_lecturer'] = 0;
        }
        $fields['updated'] = date('Y-m-d H:i:s');
            
        $updated = $this->update('id', $id, $fields);
        if($updated != FALSE){
            $this->_addAuditRecord($tableName, $id, 'is_completed', '0', '1', 'edit');
            $this->_addAuditRecord($tableName, $id, 'is_lecturer', '0', $fields['is_lecturer'], 'edit');
            return $id;           
        }
        return FALSE;
    }

    /**
    * Method to get the students that have been marked by the user
    *
    * @access public
    * @param string $tutorialId: The id of the tutorial
    * @return array|bool $data: The marked student data array on success | FALSE on failure
    */
    public function getMarkedStudents($tutorialId, $userId = NULL)
    {
        $this->_setMarker();
        
        if(empty($userId)){
            $userId = $this->userId;    
        }
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND marker_id='".$userId."'";
        $sql .= " AND is_completed='1'";
        $sql .= " AND deleted='0'";
         
        $data = $this->getArray($sql);
        if(!empty($data)){
            return count($data);
        }
        return 0;
    }

    /**
    * Method to delete tutorial markers
    *
    * @access private
    * @param string $tutorialId: The tutorial id
    * @return string|bool $tutorialId: The tutorial id on success | FALSE on failure
    */
    public function deleteTutorialMarkers($tutorialId)
    {
        $this->_setMarker();
        $tableName = $this->table;
        
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE tutorial_id='".$tutorialId."'";
        $sql .= " AND deleted='0'";
        
        $data = $this->getArray($sql);
        if($data != FALSE){
            foreach($data as $line){
                $this->_setMarker();

                $fields = array();
                $fields['deleted'] = 1;
                $fields['updated'] = date('Y-m-d H:i:s');
                
                $updated = $this->update('id', $line['id'], $fields);
                if($updated != FALSE){
                    $this->_addAuditRecord($tableName, $line['id'], 'deleted', 0, 1, 'delete');
                }
            }
            return $tutorialId;   
        }
        return FALSE;
    }
}
?>