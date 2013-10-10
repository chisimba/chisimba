<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check
class feedback extends controller
{
	public $objFb;
	public $objLog;
	public $objLanguage;
	public $objDbFb;

	/**
     * Constructor method to instantiate objects and get variables
     */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->objUser = $this->getObject('user', 'security');
			//$this->display_post();
			$this->objFb = $this->getObject('fbform');
			$this->objDbFb = $this->getObject('dbresponses');
			//Get the activity logger class
			$this->objLog = $this->newObject('logactivity', 'logger');
			//Log this module call
			$this->objLog->log();
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}
	/**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
	public function dispatch($action = Null)
	{
		switch ($action) {
			default: 
				//return $this->display_post();
				//break;
            
			case 'save':
				$this->requiresLogin(FALSE);
				try {
					//echo "q_id = ".$this->getParam('inputQuestion_id_1');
                    //echo "q = ".$this->getParam('inputQuestion_1');
					return $this->save_post();
				}
				catch(customException $e) {
					customException::cleanUp();
					exit;
				}
				break;
            case 'delete_question': //echo "deleting .....";
                  //$whatever = $this->getParam('delete_id');
                  $question_id = $this->getParam('delete_id');
                   $this->delete_question($question_id);
                   //echo "whatever is ".$answerId;
                  return $this->delete_question($question_id);
                  break;
			case 'edit': 
				     //$this->save_questions();
                     //echo "in the edit";
				     $this->save_questions($action);
				     return $this->get_questions();
				     break;
			case 'view': $query_str = $this->getParam('start');
				     //echo "test = ".$query_str;
				     return $this->get_db_posts($query_str);
				     break;
			case 'save_questions': 
				//echo "saving questions ...";
				return $this->save_questions($action);
				break;
            case 'insert_questions':
                    if($this->getParam('cancel')== null){
                        $insert_questions = "true";
                        $question_number = $this->getParam('question_number');
                        $question_number ++;
                        $this->setVarByRef('insert_questions', $insert_questions);
                        $this->setVarByRef('question_number', $question_number);
                        
                        return $this->save_questions($action);
                    }
                    else
                        return $this->display_post();
                    break;
		}
	}
	    /**
     * Overide the login object in the parent class
     *
     * @param void
     * @return bool
     * @access public
     */
    public function requiresLogin()
    {
        return FALSE;
    }
    
//------------------------------------------------------------------------------------
    /****<<<EDIT>>> 
	*Displays the edit form that allows to edit the questions 
	* Loads up a edit form for editing the questions
	*/
    public function display_edit(){
	
    }
    /** Gets questions from the questions db
        here .....dbfb_questions


     */
    public function get_questions(){
		$objDb = $this->getObject('dbfb_questions');
    		try{
			    $arr = array();
			    $questions_array = $objDb->get_questions();
			    $this->setVarByRef('questions_array', $questions_array);
			    return $this->display_post();
		}
		catch(Exception $e){
		}
    }
    public function save_questions($action){
		$objDb = $this->getObject('dbfb_questions');
		try {
            //put a loop here
            $question_to_save = $this->getParam('question_to_save');
            $question_array_size = $this->getParam('question_array_size');
            
            if($action == save_questions){
                //echo "the action is edit";
			    for($i = 0; $i< $question_array_size; $i ++){
                $question = $this->getParam('question_'.($i + 1));
                $question_id = $this->getParam('question_id_'.($i + 1));
               //echo "question id ".$question_id." and ".$question;
                $objDb->save_questions(array('fb_question'=>$question, 'puid'=>$question_id));
                }
                
            }
            //echo "calling this from the save questions".$question_to_save;
            else if($question_to_save != null){
                   $objDb->save_questions(array('fb_question'=>$question_to_save));
            }
			return $this->display_post();
		}
		catch(customException $e) {
			customException::cleanUp();
			exit;
		}
    }
//------------------------------------------------------------------------------------
    /***<<<VIEW COMMENTS>>>
	* Displays the comments posted by the users
	*/
    public function view_posts(){
	
    }
   /*** Gets posted messages from the comments-db
      **/
    public function get_db_posts($query){
		try{
			//$tem = $this->getObject("viewcomment", "feedback");
			$return_arr = array();
			//$userid = $this->objUser->userId();
			//$where = "WHERE fbname='acquim'";
			$return_arr = $this->objDbFb->get_comment($query);
			//echo "This is it".$query;
			//echo $return_arr[0]['fbname'];echo $return_arr[1]['fbname'];
			//echo $return_arr[0]['question'];echo $return_arr[0]['question'];
			$this->setVarByRef('return_arr', $return_arr);
			return $this->display_post();
		}
		catch(Exception $e){
		}
		
    }

//------------------------------------------------------------------------------------
    public function delete_question($question_id){
    	    $objDb = $this->getObject('dbfb_questions');
            try {
            //put a loop here
            $objDb->delete_question($question_id);
			return $this->display_post();
		}
		catch(customException $e) {
			customException::cleanUp();
			exit;
		}
            
    }
//------------------------------------------------------------------------------------
    /**<<<POST>>>
      ** Displays the post form
      ** 
      */
    public function display_post(){
	    $objDb = $this->getObject('dbfb_questions');
	    $questions_array = $objDb->get_questions();
	    $this->setvarByRef('questions',$questions_array);
	    return 'form_tpl.php';
    }
    /** Saves the contents obtained from the post form
     */
    public function  save_post(){
		  $captcha = $this->getParam('request_captcha');
		  $userid = $this->objUser->userId();
		  $fbname = $this->getParam('fbname');
		  $fbemail = $this->getParam('fbemail');
		  $fbww = $this->getParam('fbww');
		  $fbnw = $this->getParam('fbnw');
		  $fblo = $this->getParam('fblo');
		  $fbsp = $this->getParam('fbsp');
		  $fbee = $this->getParam('fbee');
		  $fbw = $this->getParam('fbw');
          
          $question = 	$this->getParam('inputQuestion_1');	
          $question_id = $this->getParam('inputQuestion_id_1');
		  //$this->display_post();
		  $objDb = $this->getObject('dbfb_questions');
		  if(!isset($fbname) && !isset($fbemail))
					{
						$insarr = array('userid' => $userid,
								'fbname' => $fbname,
								'fbemail' => $fbemail,
								'fbww' => $fbww, 
								'fbnw' => $fbnw,
								'fblo' => $fblo,
								'fbsp' => $fbsp,
								'fbee' => $fbee,
								'fbw' => $fbw);
						$msg = 'nodata';
						$this->setVarByRef('msg', $msg);
						$this->setVarByRef('insarr', $insarr);
						//return 'form_tpl.php';					
						return $this->display_post();
						}
					elseif (md5(strtoupper($captcha)) != $this->getParam('captcha') || empty($captcha))
					{
						$insarr = array('userid' => $userid,
								'fbname' => $fbname,
								'fbemail' => $fbemail,
							        'fbww' => $fbww,
								'fbnw' => $fbnw,
								'fblo' => $fblo,
								'fbsp' => $fbsp,
								'fbee' => $fbee,
								'fbw' => $fbw);
						$msg = 'badcaptcha';
						$this->setVarByRef('msg', $msg);
						$this->setVarByRef('insarr', $insarr);
						//return 'form_tpl.php';
						return $this->display_post();
					}
					else {
						//insert to db
                         $responses_array_size = $this->getParam('responses_array_size');
                         $objDbb = $this->getObject('dbfb_respondents');
                         $id = $objDbb->save_respondent(array('name' => $fbname,
                                                             'email' => $fbemail,
                                                      ));
                         //  echo "response_array_size = ".$responses_array_size."<br/>";
                        for($i = 0; $i < $responses_array_size; $i ++){
                            $question = $this->getParam('inputQuestion_'.($i + 1));
                            $question_id = $this->getParam('inputQuestion_id_'.($i + 1));
                            $insarr = array('userid' => $userid,
                                'resp_id'=> $id,
								'fb_response' => $question,
								'question_id' => $question_id
                                );
                             $this->objDbFb->saveRecord($insarr);
                        }
						
						//return a thanks template
						$msg = 'thanks';
						$this->setVarByRef('msg', $msg);
						//returns next action
						return $this->display_post();
					}	

    }
//------------------------------------------------------------------------------------
}
?>
