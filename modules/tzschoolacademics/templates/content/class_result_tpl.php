<?php
/* 
 *
 *   @author charles mhoja
 *   @email charlesmdack@gmail.com
 */
$displayObj=$this->newObject('reportdisplay', 'tzschoolacademics');
$objreportDb=$this->newObject('dbreports', 'tzschoolacademics');
if(strcmp($option,'class_result')==0){
 $class_result=$displayObj->generate_class_result($exam_type, $term_id, $year_id, $class_id);
 if($class_result){
    $class_name=$objreportDb->get_class_details($class_id);
    foreach ($class_name as $value) {
        $class_name=$value['class_name'].$value['stream']; 
    }

   $year=$objreportDb->get_year($year_id);
   $exam_info=$objreportDb->get_exam_detail($exam_type);
   foreach ($exam_info as $exam_value) {
     $exam_name=$exam_value['exam_type'];
   }
   $term=$objreportDb->get_term_name ($term_id);
   
     ///creating headings for results
        $heading='<h3>'.$exam_name.'  RESULTS </h3>';
        $heading .='<h5>CLASS: '.$class_name.'</h5>';
        $heading .='<h5>ACADEMIC YEAR: '.$year.'-'.$term.'</h5>';
               
      echo $heading.$class_result;
 }

 else{
  echo  "<p>No Details Found please check</p>";
 }
}


else{
echo $displayObj->create_class_result_form();
}

?>
