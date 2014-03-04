<?php
/* 
 * The contents of this file are subject to the Meeting Manager Public license you may not use or change this file except in
 * compliance with the License. You may obtain a copy of the License by emailing this address udsmmeetingmanager@googlegroups.com
 *  @author victor katemana
 *  @email princevickatg@gmail.com


 */

if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class dbsubjectmodule extends dbTable
{


 public function init()
 {
//Set the table in the parent class
parent::init('tbl_subjects');
parent::init('tbl_class');
parent::init('tbl_term');
parent::init('tbl_academic_year');
parent::init('tbl_exam');
parent::init('tbl_student');
parent::init('tbl_student_class_stream');
 }

  
 public function  view_bysub($subj,$acayear,$term)
 {

     $sql = "SELECT score, tbl_student_reg_no, tbl_academic_year_id, tbl_term_id, firstname, lastname, othernames, score
                      FROM tbl_result
             LEFT JOIN tbl_student ON tbl_student_reg_no = reg_no
               where tbl_subjects_id=$subj AND tbl_academic_year_id=$acayear AND tbl_term_id=$term";

      $filter = "where tbl_subjects_id=$subj AND tbl_academic_year_id=$acayear AND tbl_term_id=$term";
       $this->_tableName = "tbl_result";

       //checks if there are records of the above query
       if( $this->getRecordCount($filter) > 0)
       {
           $this->query($sql);

           if ($this->query($sql)) {
            return $this->query($sql);
        } else {
            return FALSE;
        }
     }
     else
      return 0;
 }

  public function  edit_results($regno,$subj,$acayear,$term)
  {
    $sql = "SELECT score, tbl_student_reg_no, tbl_academic_year_id, tbl_term_id, firstname, lastname, othernames, score
FROM tbl_result
LEFT JOIN tbl_student ON tbl_student_reg_no = reg_no
WHERE tbl_subjects_id =$subj
AND tbl_academic_year_id =$acayear
AND tbl_term_id =$term
AND tbl_student_reg_no = '$regno'";
  //echo $sql;
           $this->query($sql);

           if ($this->query($sql)) {
            return $this->query($sql);
        } else {
            return FALSE;
        }

  }

/**
    * Method to return subject name
    *
    * @access public
    * @param  string $subj_id the primary key of the table
    * @return  subject name, or FALSE on failure
    */
 public function return_subjectname($subj_id)
 {

   $subjname = $this->getRow('puid', $subj_id, $table = 'tbl_subjects');

   foreach ($subjname as $row)
   {
    $subject = $row['subject_name'];
   }
   if(!empty ($subject) )
   {
     return $subject;

   }
 else {
       return FALSE;
   }

 }

 /**
    * Method to return subject academic year
    *
    * @access public
    * @param  string $aca_id the primary key of the table
    * @return academic year, or FALSE on failure
    */
 public function  return_academicyear($aca_id)
 {
   $acayear = $this->getRow('puid', $aca_id, $table = 'tbl_academic_year');

   foreach ($acayear as $row)
   {
     $academicyear = $row['year_name'];
   }
   if( !empty($academicyear ))
   {
     return  $academicyear;
   }
  else
  {
    return False;
  }


 }

 /**
    * Method to return term
    *
    * @access public
    * @param  string $term_id the primary key of the table
    * @return term, or FALSE on failure
    */

 public function return_term($term_id)
 {
     $term = $this->getRow('puid', $term_id, $table = 'tbl_term');

     foreach ($term as $row)
     {
       $term_out = $row['term_name'];
     }
     if(!empty ($term_out))
     {
         return $term_out;
     }
     else
     {
         return FALSE;
     }
 }

 /**
    * Method to return class name
    *
    * @access public
    * @param  string $class_id the primary key of the table
    * @return class name, or FALSE on failure
    */
 public function  return_class($class_id)
 {
   $class = $this->getRow($puid, $class_id, $table = 'tbl_class');

   foreach ($class as $row)
   {
     $class_name = $row['class_name']. $row['stream'];
   }
   if(!empty($class_name))
   {
       return $class_name;
   }
   else
   {
     return False;
   }
 }

  //$regno students registration number
 // score students marks

  public function update_marks($regno,$field_array)
  {
     if($this->update('tbl_student_reg_no', $regno, $field_array, 'tbl_result'))
     {
         return '<p> Edit successful done </p>';
     }



  }


}
?>
