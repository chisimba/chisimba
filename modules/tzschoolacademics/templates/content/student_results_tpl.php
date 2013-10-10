<?php

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check


/*
 * template for student result menu
 *
 *  @author charles mhoja
 *  @email charlesmhoja@gmail.com
 */

//loading thereport display class
$displayObj=$this->newObject('reportdisplay', 'tzschoolacademics');
if(strcmp($option,'view')==0){
echo $displayObj->generate_student_resut($regno, $exam, $term, $year_id, $class);
  
}
else{ 
echo $displayObj->create_students_result_form();
}
?>
