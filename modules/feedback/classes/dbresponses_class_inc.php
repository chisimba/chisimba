<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Data access (db model) Class for the feedback module
 *
 * This is a database model class for the feedback module. All database transaactions will go through
 * this class. This class is derived from the top level dbTable superclass in the framework core.
 *
 * @author Paul Scott
 * @filesource
 * @copyright AVOIR
 * @package feedback
 * @category chisimba
 * @access public
 */

class dbresponses extends dbTable
{
		
	/**
	 * Standard init function - Class Constructor
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		$this->objLanguage = $this->getObject("language", "language");
        parent::init('tbl_feedback_responses');
        //$this->table = 'tbl_feedback_questions';
        $this->table = 'tbl_feedback_responses';
	}
	
	public function saveRecord($insarr)
	{   
        $objUser = $this->getObject('user', 'security');
        $userid =  $objUser->userId();
        $insarr['userid'] = $objUser->userId();
        $insarr['modified'] = $this->now();
        $this->insert($insarr);
	}

	/**
	 * Method to dynamically switch tables
	 *
	 * @param string $table
	 * @return boolean
	 * @access private
	 */
	private function _changeTable($table)
	{
		try {
			parent::init($table);
			return TRUE;
		}
		catch (customException $e)
		{
			customException::cleanUp();
			return FALSE;


		}
	}
	public function get_comment($date){
        
		$dateArr = explode(' ', $date);
        $tmpArr = explode('-', $dateArr[0]);
        //echo '$tmpArr[0][0]  = '.$tmpArr[0][0].'<br/>';
        if ($tmpArr[1]< 10 && $tmpArr[1][0] != '0'){
             $tmpArr[1] = '0'.$tmpArr[1];
             $dateArr[0] = implode('-', $tmpArr);
        }
        //echo 'tmpArr[2] = '.$tmpArr[2].'<br/>';
        if ($tmpArr[2]< 10 && $tmpArr[2][0] != '0'){
             $tmpArr[2] = '0'.$tmpArr[2];
             $dateArr[0] = implode('-', $tmpArr);
        }
        //echo '$dateArr[0]  = '.$dateArr[0].'<br/>';
		$arr = array();	
		$where = "tbl_feedback_responses.modified LIKE '".$dateArr[0]."%'";
		$this->_changeTable('tbl_feedback_responses');	
        $objDb = $this->getObject('dbfb_questions');
        $questions_table = $objDb->table;
       // echo "question->table ".$questions_table;
        $response_table = $this->table;
       // echo '$where = '.$where;
        $query = "select fb_question, email, name, tbl_feedback_responses.*".
                 "  from  tbl_feedback_questions, tbl_feedback_responses, tbl_feedback_respondents where ".                 
                 "(tbl_feedback_questions.puid = question_id)  and (resp_id = tbl_feedback_respondents.puid) and ".$where." order by resp_id desc;";
       
        if(empty($date)){
                 $query = "select fb_question, email, name, tbl_feedback_responses.*".
                 "  from  tbl_feedback_questions, tbl_feedback_responses, tbl_feedback_respondents where ".                 
                 "(tbl_feedback_questions.puid = question_id)  and (resp_id = tbl_feedback_respondents.puid) order by resp_id desc;";
        }
        $data = $this->getArray($query);
        //echo "laskdflkjsda ".$data;
        if (!empty($data)) {
            return $data;
        }
		return $arr;
	}
	
}
?>
