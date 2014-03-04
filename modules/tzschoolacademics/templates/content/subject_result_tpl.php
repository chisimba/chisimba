<?php

// security check-must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/*
 * template for displaying data regarding various subject result
 *
 *  @author charles mhoja
 *  @email charlesmhoja@gmail.com
 */

//loading thereport display class
$displayObj = $this->newObject('reportdisplay', 'tzschoolacademics');
$objreportDb=$this->newObject('dbreports', 'tzschoolacademics');
if (strcmp($option, 'sub_result') == 0) {
    $subject_results=$displayObj->generate_subject_result($subject_id, $exam_id, $term_id, $year_id, $class_id);
if($subject_results){
   $class_name=$objreportDb->get_class_details($class_id);
    foreach ($class_name as $value) {
        $class_name=$value['class_name'].$value['stream'];
    }

   $year=$objreportDb->get_year($year_id);
   $exam_info=$objreportDb->get_exam_detail($exam_id);
   foreach ($exam_info as $exam_value) {
     $exam_name=$exam_value['exam_type'];
   }
   $term=$objreportDb->get_term_name ($term_id);
   $subject=$objreportDb->get_subject($subject_id);
     ///creating headings for results
        $heading='<h3>'.$exam_name.'  RESULTS </h3>';
        $heading .='<h5>CLASS: '.$class_name.'</h5>';
        $heading .='<h5>SUBJECT:  ' .$subject.'</h5>';
        $heading .='<h5>ACADEMIC YEAR: '.$year.'-'.$term.'</h5>';

      echo $heading.$subject_results;

}




 else{
    echo '<P> No. results found</P>';
 }
}
else {
    echo $displayObj->create_subject_result_form();
}
?>
