<?php
/* ----------- data class extends dbTable for tbl_survey ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_question_type
* @author Kevin Cyster
*/

class dbtype extends dbTable
{
    /**
     * @var string $table The name of the database table to be affected
     * @access private
     */
    private $table;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        parent::init('tbl_survey_question_type');
        $this->table='tbl_survey_question_type';
    }

    /**
    * Method for retrieving question types
    *
    * @access public
    * @return array $data The question type information.
    */
    public function listQuestionType()
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" ORDER BY 'question_type' ";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for retrieving a question type
    *
    * @access public
    * @param string $typeId The id of the question type
    * @return array $data The question type information.
    */
    public function getQuestionType($typeId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE id='$typeId'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            $questionType=$data[0]['question_type'];
            return $questionType;
        }
        return FALSE;
   }

    /**
    * Method for retrieving a question type description
    *
    * @access public
    * @param string $typeId The id of the question type
    * @return array $data The question type description
    */
    public function getQuestionTypeDescription($typeId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE id='$typeId'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            $questionDescription=$data[0]['question_description'];
            return $questionDescription;
        }
        return FALSE;
   }

    /**
    * Method for retrieving a question type id
    *
    * @access public
    * @param string $questionType The number of the question type
    * @return string $typeId The question type id
    */
    function getQuestionTypeId($questionType)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE question_type='$questionType'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            $typeId=$data[0]['id'];
            return $typeId;
        }
        return FALSE;
    }
}
?>