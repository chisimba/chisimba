<?php
// security check - must be included in all scripts
	if (!$GLOBALS['kewl_entry_point_run']){
		die("You cannot view this page directly");
	}
	/**
	*
	* Data access class for hellokinky module. This class
	* extends dbTable to access the table tbl_hellokinky
	* @author Acquim Matuli-Bulimwengu
	* @copyright (c) 2005 GNU GPL
	* @version 1.0
	*
	*/
	class dbfb_respondents extends dbtable
	{
 	/**
   	*
 	* Standard init method to define table and instantiate
 	* common objects.
	*/
 		function init()
 		{
  			//Set the table in the parent class
  			parent::init('tbl_feedback_respondents');
            $this->table = 'tbl_feedback_respondents';
 		}
        /* This function gets feedback questions from the database
        *  All the questions in the database are to be returned
        */
		function get_respondent(){
			$arr = array();
			$query = 'SELECT * FROM '.$this->table;
            //echo $query;
			$arr =  $this->getArray($query);
            $quesitons = $arr;
            //for($i = 0; $i < count($quesitons); $i++){
                //echo ($i+1)." question ".$questions[$i]['fb_question']."<br/>";
            //}
			return $arr;
		}
        public function delete_question($question_id){
             $this->delete('puid', $question_id);
        }
        //This functions saves a single row to the database
        function save_respondent($row){
            $id = $this->insert($row);
            $query = 'SELECT puid FROM '.$this->table." where id = '".$id."'";
            $arr =  $this->getArray($query);
            //for($i = 0; $i < count($arr); $i++){
              //  echo ($i+1)." puid ".$arr[$i]['puid']."<br/>";
            //}
            return $arr[0]['puid'];
        }
        public function get_table(){
            return $this->table;        
        }
	}
?>
