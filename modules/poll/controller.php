<?php
/**
* poll class extends controller
* @package poll
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Controller class for poll module
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class poll extends controller
{
    /**
    * @var string $contextCode The current context .. to do.
    * @access private
    */
    private $contextCode = 'root';
    
    /**
    * Method to construct the class.
    */
    public function init()
    {
        try{
            $this->pollTools = $this->getObject('polltools', 'poll');
            $this->dbPoll = $this->getObject('dbpoll', 'poll');
            $this->dbQuestions = $this->getObject('dbquestions', 'poll');
            $this->dbAnswers = $this->getObject('dbanswers', 'poll');
            $this->dbResponse = $this->getObject('dbresponse', 'poll');
	    $this->objContext = $this->getObject('dbcontext', 'context');                    
            $this->objUser = $this->getObject('user', 'security');
            //Get the activity logger class and log this module call
            $objLog = $this->getObject('logactivity', 'logger');
            $objLog->log();
            
            $this->setPollId();
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
    }

    /**
    * Standard dispatch function
    *
    * @access public
    * @param string $action The action to be performed
    * @return string Template to be displayed
    */
    public function dispatch($action)
    {
        switch($action){
            case 'showadd':
                $display = $this->pollTools->showAdd('');
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';
            case 'happyeval':
	        $this->setVar('pageSuppressContainer', TRUE);
	        $this->setVar('suppressFooter', TRUE); # suppress default page footer
	        $this->setVar('pageSuppressIM', TRUE);
	        $this->setVar('pageSuppressToolbar', TRUE);
	        $this->setVar('pageSuppressBanner', TRUE);
                $contextCode = $this->objContext->getContextCode();
		$poll = $this->dbPoll->getPoll($contextCode);	        
                $pollData = $this->dbQuestions->getQuestions($poll);
		$hasAccess = $this->objUser->isContextLecturer();
		if($hasAccess){
	                $display = $this->pollTools->showContextPolls($pollData);
		}else{
			//Check polls
			$config = $this->dbPoll->getPollByContext($contextCode);
			$contextPoll = $config['id'];
			$visible = '1';
			$isVisible = $this->dbQuestions->getQuestionsAccess($contextPoll, $visible);
			if(!empty($isVisible)){
	                	$display = $this->pollTools->castContextPolls($pollData);
			}else{
				$display = null;
			}
		}
                $this->setVarByRef('display', $display);
                return 'happy_polls_tpl.php';

            case 'analysecontextpolls':
	        $this->setVar('pageSuppressContainer', TRUE);
	        $this->setVar('suppressFooter', TRUE); # suppress default page footer
	        $this->setVar('pageSuppressIM', TRUE);
	        $this->setVar('pageSuppressToolbar', TRUE);
	        $this->setVar('pageSuppressBanner', TRUE);
                $contextCode = $this->objContext->getContextCode();
		$poll = $this->dbPoll->getPoll($contextCode);	        
                $pollData = $this->dbQuestions->getQuestions($poll);
		$hasAccess = $this->objUser->isContextLecturer();
		if(!$hasAccess){
	                $display = $this->pollTools->castContextPolls($pollData);
		}else{
	                $display = $this->pollTools->analyseContextPolls($pollData);
		}
                $this->setVarByRef('display', $display);
                return 'happy_polls_tpl.php';


            case 'addcontextpoll':
	        $this->setVar('pageSuppressContainer', TRUE);
	        $this->setVar('suppressFooter', TRUE); # suppress default page footer
	        $this->setVar('pageSuppressIM', TRUE);
	        $this->setVar('pageSuppressToolbar', TRUE);
	        $this->setVar('pageSuppressBanner', TRUE);
                $display = $this->pollTools->showAddInContext('');
                $this->setVarByRef('display', $display);
                return 'happy_polls_tpl.php';
            case 'showeditcontext':
	        $this->setVar('pageSuppressContainer', TRUE);
	        $this->setVar('suppressFooter', TRUE); # suppress default page footer
	        $this->setVar('pageSuppressIM', TRUE);
	        $this->setVar('pageSuppressToolbar', TRUE);
	        $this->setVar('pageSuppressBanner', TRUE);
                $id= $this->getParam('id');
                $data = $this->dbQuestions->getQuestion($id);
                $display = $this->pollTools->showAddInContext($data);
                $this->setVarByRef('display', $display);
                return 'happy_polls_tpl.php';

            case 'showedit':
                $id= $this->getParam('id');
                $data = $this->dbQuestions->getQuestion($id);
                $display = $this->pollTools->showAdd($data);
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';
                
            case 'deletequestion':
                $id= $this->getParam('id');
                $this->dbQuestions->deleteQuestion($id);
                return $this->nextAction('');
                
            case 'deletecontextqn':
                $id= $this->getParam('id');
                $testor = $this->dbQuestions->deleteQuestion($id);
		if(empty($testor)){
			echo 'ok';
		}else{
			echo 'notok';
		}
                break;

            case 'getanswerid':
                $questionId = $this->getParam('questionId');
                $answer = $this->getParam('answer');
                $myResult = $this->dbAnswers->getCurrentAnswer($questionId,$answer);
		$answerId = $myResult[0]['id'];
		echo $answerId;
                break;

            case 'deleteqntypeopenans':
                $id= $this->getParam('id');
                $testor = $this->dbAnswers->deleteAnswer($id);
		if(empty($testor)){
			echo 'ok';
		}else{
			echo 'notok';
		}
                break;
                
            case 'savecontextquestion':
                //$contextCode = $this->getParam('context');
                $contextCode = $this->objContext->getContextCode();
		$poll = $this->dbPoll->getPoll($contextCode);
                //$pollId = $this->getSession('pollId');
                $qnId = $this->getParam('qnId');
                $qnType = $this->getParam('type');
                $this->setVarByRef('type', $qnType);
		if($qnType=='open'){
	                $testor = $this->dbQuestions->saveQnTypeOpen($poll, $qnId);
		}else{
	                $testor = $this->dbQuestions->saveQnandAnswers($poll, $qnId);
		}
		if(!empty($testor)){
			echo 'ok';
		}else{
			echo 'notok';
		}
                break;

            case 'saveqntypeopen':
                $contextCode = $this->getParam('context');
                //$contextCode = $this->objContext->getContextCode();
		$poll = $this->dbPoll->getPoll($contextCode);
                //$pollId = $this->getSession('pollId');
                $id = $this->getParam('id');
                $qnType = $this->getParam('type');
                $this->setVarByRef('type', $qnType);
		if($qnType=='open'){
	                $testor = $this->dbQuestions->saveQnTypeOpen($poll, $id);
		}else{
	                $testor = $this->dbQuestions->saveQnandAnswers($poll, $id);
		}
		if(!empty($testor)){
			echo $testor;
		}else{
			echo 'notok';
		}
                break;

            case 'savecontextanswer':
                $qnId = $this->getParam('qnId');
                $id = $this->getParam('id');
                $qnType = $this->getParam('type');
		$this->dbAnswers->getAnswersDelete($qnId);
		if($qnType=='yes'){
			if($id==Null){
		                $this->dbAnswers->saveAnswer($qnId,'Yes', $id=Null);
		                $this->dbAnswers->saveAnswer($qnId,'No', $id=Null);
			}else{
		                $this->dbAnswers->saveAnswer($qnId,'Yes', $id);
		                $this->dbAnswers->saveAnswer($qnId,'No', $id);
			}
		}elseif($qnType=='bool'){
			if($id==Null){
		                $this->dbAnswers->saveAnswer($qnId,'True', $id=Null);
		                $this->dbAnswers->saveAnswer($qnId,'False', $id=Null);
			}else{
		                $this->dbAnswers->saveAnswer($qnId,'True', $id);
		                $this->dbAnswers->saveAnswer($qnId,'True', $id);
			}
		}
		$testor = $this->dbAnswers->getAnswers($qnId);
		if(!empty($testor)){
			echo 'ok';
		}else{
			echo 'notok';
		}
                break;
            case 'saveanswertypeopen':
                $qnId = $this->getParam('qnId');
                $id = $this->getParam('id');
                $qnType = $this->getParam('type');
                $answer = $this->getParam('answer');
		$this->dbAnswers->delAnswersBoolTf($qnId);
		if($qnType=='open'){
			if($id!==Null && $id!==0){
		                $this->dbAnswers->saveAnswer($qnId,$answer, $id);
			}else{
		                $this->dbAnswers->saveAnswer($qnId,$answer, $id=Null);
			}
		}
		$yourOptions = $this->dbAnswers->getAnswers($qnId);
		$str = "&nbsp;";
		if(!empty($yourOptions)){
			$str .= "<b>Options</b><br />";
			foreach($yourOptions as $options){
				$optAnswer = $options['answer'];
				$str .='<br /><label><input type="radio" name="'.$qnId.'" value="'.$optAnswer.'" />&nbsp;&nbsp;'.$optAnswer.'</label>  <a href="#" onclick="editAnswer(\''.$options['id'].'\',\''.$options['question_id'].'\',\''.$optAnswer.'\')">Edit</a> <a href="#" onclick="onDeleteAns(\''.$options['id'].'\')">Delete</a>';
			}
			echo $str;
		}else{
			echo 'notok';
		}
                break;
                
            case 'savequestion':
                $pollId = $this->getSession('pollId');
                $id = $this->getParam('id');
                $this->dbQuestions->saveQuestion($pollId, $id);
                return $this->nextAction('');
                
            case 'saveresponse':
                $this->dbResponse->addResponse();
                break;
            
            case 'saveconfig':
                $pollId = $this->getSession('pollId');
                $this->dbPoll->saveConfig($pollId);
                return $this->nextAction('');
                break;
            
            default:
                $pollId = $this->getSession('pollId');
                $pollData = $this->dbQuestions->getQuestions($pollId);
                $display = $this->pollTools->showPolls($pollData);
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';
        }
    }
    
    /**
    * Method sets the poll id in session - checks if the user is in a different context to reset the id
    *
    * @access private
    */
    private function setPollId()
    {
        $context = $this->getSession('poll_context');
        if(empty($context) || ($context != $this->contextCode)){
            $pollId = $this->dbPoll->getPoll($this->contextCode);
            
            $this->setSession('pollId', $pollId);
            $this->setSession('poll_context', $this->contextCode);
        }
    }
    
    /**
    * Method to allow user to view the poll without being logged in
    *
    * @access public
    */
    public function requiresLogin()
    {
        return FALSE;
    }
} // end of controller class
?>
