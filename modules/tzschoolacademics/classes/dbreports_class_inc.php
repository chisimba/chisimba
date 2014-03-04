<?php

/**
 * Description of reportDbTable_class_inc
 *  The database access class for reports section of the academic module
 * @author charles mhoja
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class dbreports extends dbTable {

    public $_tableName;

    //constructor
    public function init() {
        parent::init('tbl_academic_year');
        parent::init('tbl_class');
        parent::init('tbl_exam');
        parent::init('tbl_class_subjects');
        parent::init('tbl_grade');
        parent::init('tbl_result');
        parent::init('tbl_student');
        parent::init('tbl_student_class');
        parent::init('tbl_subjects');
        parent::init('tbl_term');
    }

    /* method to get school academic years
     * @returns academic_years  assocuative array containg all academic years
     */

    public function get_academic_year() {
        $this->_tableName = 'tbl_academic_year';
        $acadm_years = $this->getAll();
        if ($acadm_years) {
         return $acadm_years;  //associative array contain all the school academic years
        }
     else{
            return FALSE;
     }
        
    }


    /* method to get a specific academic years
     * @returns year_name  the name of a given academic yeaar
     */

    public function get_year($year_id) {
    $acadm_years = $this->getRow($pk_field='puid', $year_id, $table = 'tbl_academic_year');
        if ($acadm_years) {
          $year_name=$acadm_years['year_name'];
         return $year_name;  //associative array contain all the school academic years
        }
     else{
            return FALSE;
     }

    }

   /*method to get term details from a given specified term id
    * @param term_id
    * $return term_name  yhe name of the given semmister/term
    */

    public function get_term_name ($term_id)
  {
   $result =$this->getRow($pk_field='puid', $term_id, $table = 'tbl_term');
   if($result)
   {
       return $result['term_name'];
   }
   else
   {
       return FALSE;
   }

  }



    /* methid to get all classes in a given school in an array
     * @return class_list  an associative array contains all the classes in a school
     *
     */

    public function get_classes() {
        $this->_tableName = 'tbl_class';
        $classes = $this->getAll();
        if ($classes) {
            return $classes;   ///associative array containg all the classes
        }
      else{
          return FALSE;
      }
    }




    /* methid to get informations about a given class
     * @param class_id: id of the class whose details is to be found
     * @return class_details  an associative array contains all the classes in a school |FALSE if no details forund
     *
     */

    public function get_class_details($class_id) {
        $stmt="SELECT * FROM `tbl_class`
               WHERE tbl_class.`puid`='$class_id' ";
        $class_details = $this->getArray($stmt);
    
        if ($class_details) {
            return $class_details;   ///associative array containg class details
        }
      else{
          return FALSE;
      }
    }



    /*
     * method to get_all types of exams in an array
     * @return exam_type as associative array
     *
     */

    public function get_exam_type() {
        $this->_tableName = 'tbl_exam';
        $exams = $this->getAll();
        if ($exams) {
            return $exams;
        }
        else{
            return FALSE;
        }
    }

    /*
     * method exam details for a given specified exam type id
     * @param exam_id  id of the exam
     * @return exam_details as associative array
     *
     */

    public function get_exam_detail($exam_id) {
        $this->_tableName = 'tbl_exam';
        $filter="WHERE puid='$exam_id' ";
        $exam_details = $this->getAll($filter);
        if ($exam_details) {
            return $exam_details;
        }
        else{
            return FALSE;
        }
    }


    /*
     * method to check if student is registered to a given class in agiven academic year.
     * @param regno  student registration number
     * @param year   academic year
     * @param class  student class
     * return bolean TRUE if student is registered and FALSE if not registered
     */

    function validate_student($regno, $year, $class) {
        $this->_tableName = 'tbl_student_class';
        $filter = "Where tbl_student_reg_no='$regno' AND tbl_class_id='$class' AND tbl_academic_year_id='$year' ";
        $result = $this->getRecordCount($filter = $filter);
        if ($result == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * method to get all basic information of a student in a given academic year eg. student name,class.
     * @param regno:  student registration number
     * @param year_id:   academic year
     * return student_info:  associative array with one row of student informations in if student information exists
     */

    function get_students_information($regno, $year_id) {

        $sql = "SELECT `reg_no`,`firstname`, `lastname`, `othernames`, `gender`, `birthdate`, `religion`,`class_name`,`stream`,`year_name`
         FROM `tbl_student`,tbl_class,tbl_student_class, tbl_academic_year
         WHERE tbl_student_class.tbl_student_reg_no='$regno' AND tbl_student_class.`tbl_academic_year_id`=$year_id
             AND tbl_class.`puid`=tbl_student_class.`tbl_class_id` AND tbl_student.reg_no=tbl_student_class.tbl_student_reg_no
             AND tbl_academic_year .`puid`=$year_id  AND tbl_class.puid=tbl_student_class.tbl_class_id ";

        $sql_query = $this->query($sql);

        if ($sql_query) {
            return $sql_query;
        } else {
            return FALSE;
        }
    }

    /*
     * method to get students marks for all subject for given exam type,academic year and semmister
     * @param regno:  student registration number
     * @param  exam :  type of examination
     * @param term   academic year term/semmister
     * @param year_id:   academic year
     * return subject_marks:  associative array with student subject marks in if student information exists
     */

    function get_student_marks($regno, $exam, $term, $year_id) {
        $sql = "SELECT `subject_name`,exam_type,`score`,term_name  FROM `tbl_result`,tbl_subjects,tbl_exam,tbl_term
        WHERE tbl_subjects.`puid`=tbl_result.`tbl_subjects_id` AND tbl_term.puid=tbl_result.tbl_term_id 	
        AND tbl_result.tbl_student_reg_no='$regno' AND tbl_result.tbl_exam_id='$exam' AND tbl_result.tbl_academic_year_id='$year_id' 
        AND tbl_result.tbl_term_id='$term' AND tbl_exam.puid=tbl_result.tbl_exam_id ORDER BY subject_name";

        $subject_marks = $this->query($sql);
        if ($subject_marks) {
            return $subject_marks;
        }
       else{
            return FALSE;
       }
    }



    /*
     * method to calculate grade from the suplied student marks
     * @param marks :student marks to be given the grade
     * @param class: Id of the student's class 
     * return marks_grade  :array containing gade name and remarks for the given marks
     */

    function get_marks_grade($marks, $class) {
        if (!empty($marks) && !empty($class)) {
            $class_level = $this->getRow($pk_field = 'puid', $pk_value = $class, $table = 'tbl_class');
            $c_level = $class_level['level'];  ///students class level

            if ($c_level) {
                //getting grade name and ist remarks
                $grade_sql = "SELECT grade_name,remarks FROM tbl_grade
                  WHERE  level LIKE'$c_level%' AND min_value<='$marks' AND max_value>='$marks' LIMIT 0,1";

                $marks_grade = $this->query($grade_sql);
                if ($marks_grade) {
                    return $marks_grade;
                }
              else{
                return FALSE;
              }
            }
        }
    }

    /*
     * method to det all Student in class in a specified academic year
     * @param class_id  : class id
     * @param year_id   academic year id
     * @return array containing student in a class and their information
     */

    function get_class_student($class_id, $year_id) {
        $sql = "SELECT `reg_no`, `firstname`, `lastname`, `othernames`, `gender`, `birthdate`, `religion`,`class_name`,`stream`
      FROM `tbl_student`,tbl_student_class,tbl_class
      WHERE tbl_student_class.`tbl_academic_year_id`='$year_id'  AND tbl_student_class.`tbl_class_id`='$class_id'
      AND tbl_student.`reg_no`=tbl_student_class.`tbl_student_reg_no` AND  tbl_class.`puid`=tbl_student_class.`tbl_class_id`  ";
        $student_in_class=$this->query($sql);
        if($student_in_class){
        return  $student_in_class;
        }
       else{
            return FALSE;
       }
    }


    /*
     * method to get all  subjects in a class from the database
     * @param class_id : class indetification
     * @return array   : associative array containing all subjects in a class
     */
    function  get_class_subject($class_id){
     $filter="SELECT subject_name,tbl_subjects.puid as subj_id  from tbl_subjects,tbl_class_subjects
        WHERE tbl_class_subjects.tbl_class_id='$class_id' AND tbl_subjects.puid=tbl_class_subjects.tbl_subject_id ORDER BY subject_name ";
       $subjects=$this->query($filter);
      
     if($subjects){
         return $subjects;
     }
     else{
         return FALSE;
     }

    }


     /*
     * method to get all  subjects 
     * @return array   : associative array containing all subjects in a class
     */
    function  get_all_subject(){
      $this->_tableName='tbl_subjects';
     $subjects=$this->getAll();
     if($subjects){
         return $subjects;
     }
     else{
         return FALSE;
     }

    }



    /*
     * method to get a specific subjects details
     * @return subject_name   name of the subject
     */
    function  get_subject($subject_id){
     $subjects=$this->getRow($pk_field='puid', $pk_value=$subject_id,'tbl_subjects');
     if($subjects){
         return $subjects['subject_name'];
     }
     else{
         return FALSE;
     }

    }





    /*method to get student result marks
     *@param st_regno   :student registration no
     *@param subject_id :id of the subject
     *@param exam_type  :examination type
     *@param term_id    :term/semmister id
     * @param year_id   :academic year id
     */
    function get_student_specific_marks($st_regno,$subject_id,$exam_type, $term_id, $year_id){
         $marks_sql="SELECT `subject_name`,exam_type,`score`,term_name  FROM `tbl_result`,tbl_subjects,tbl_exam,tbl_term
        WHERE tbl_subjects.`puid`=tbl_result.`tbl_subjects_id` AND tbl_term.puid=tbl_result.tbl_term_id
        AND tbl_result.tbl_student_reg_no='$st_regno' AND tbl_result.tbl_exam_id='$exam_type' AND tbl_result.tbl_academic_year_id='$year_id'
        AND tbl_result.tbl_term_id='$term_id' AND tbl_exam.puid=tbl_result.tbl_exam_id AND tbl_result.tbl_subjects_id='$subject_id'
        ORDER BY subject_name";
       $result=$this->query($marks_sql);
       
       if($result){
           return $result;
       }
    else{
         $result['score']='_';
         return $result;
    }
      
    }



    /* method to check results for a subject if arleady uploaded
     *@param subject_id :id of the subject
     *@param exam_id  :examination type
     *@param term_id    :term/semmister id
     * @param year_id   :academic year id
     * return :TRUE if results have been uploaded for atleast one student in a given class under specifications parameters
     * return FALSE: if no data found
     */
    function subject_resultExist($subject_id,$exam_id, $term_id, $year_id){
        $filter="WHERE tbl_subjects_id='$subject_id' AND tbl_exam_id='$exam_id' AND tbl_academic_year_id='$year_id' AND tbl_term_id='$term_id' 	";
        $this->_tableName='tbl_result';
        $count=$this->getRecordCount($filter);  ////counting the no of rows returned by the query
        if($count>0){
            return TRUE;
        }
      else{
            return FALSE;
      }
    }





/*
     * method to check if a subject  belong to a certain class
     * @param subj_id id of the subject
     * @param class id of the class
     * @year academic year id
     * return :TRUE when subject  exists and FALSE otherwise
     */
    function check_subject_class($subj_id,$class_id,$year){   ///suggestion on the use of year

        $this->_tableName='tbl_class_subjects';
        $filter="WHERE tbl_class_id='$class_id' AND tbl_subject_id='$subj_id' ";
        $count=$this->getRecordCount($filter);
      if($count==1){
          return TRUE;
      }
      else{
          return FALSE;
      }

    }


    /*method to validate student results if exists
     *
     *
     *
     */

    function exist_student_result($st_regno,$exam_type, $term_id, $year_id){
        $this->_tableName='tbl_result';
        $filter=" WHERE tbl_result.tbl_student_reg_no='$st_regno' AND tbl_result.tbl_exam_id='$exam_type' AND tbl_result.tbl_academic_year_id='$year_id'
                  AND tbl_result.tbl_term_id='$term_id' ";
 
       
      $count=$this->getRecordCount($filter);
      if($count>=1){
          return TRUE;
      }
      else{
          return FALSE;
      }



    }


    /* method to get subject_marks in descending order
     *@param subject_id :id of the subject
     * @param class_id : id of the class for which the subject belongs
     *@param exam_type  :examination type
     *@param term_id    :term/semmister id
     * @param year_id   :academic year id
    */

    function get_subject_marks($subjectId,$class_id,$exam_id, $term_id, $year_id){


    }


    





////end of class
}

?>
