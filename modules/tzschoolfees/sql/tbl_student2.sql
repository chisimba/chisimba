<?php


/**
table for class
*/
    $tablename = 'tbl_student2';
/**

*/

    $options = array('comment' => 'Table for saving student information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
/**

*/


$fields = array(
'id'=>array(
'type'=>'text',
'length'=>32
),
'student_fname' => array(
       'type' => 'text',
       'length' => 25,
       'notnull' => TRUE
       ),
   'student_mname' => array(
       'type' => 'text',
       'length' => 25,
       'notnull' => TRUE
       ),
 'student_fname' => array(
       'type' => 'text',
       'length' => 25,
       'notnull' => TRUE
       ));
?>