<?php
// Table Name
$tablename = 'tbl_rubric_assessments';

//Options line for comments, encoding and character set
$options = array('comment' => 'The rubric assesment is managed by the ruberic module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
    'type'     => 'text',
    'length'   => 32,
    'notnull'  => 1
    ),
   'tableId'   =>  array(
    'type'     =>  'text',
    'length'   =>  32,
    'notnull'  =>  0
   ), 
   'teacher'   =>  array(
   'type'      =>  'text',
   'length'    =>   30,
   'notnull'  =>  0
   ),
   'student'   =>  array(
   'type'      =>  'text',
   'length'    =>  30,
   'notnull'  =>  0 
   ),
   'studentNo'  =>  array(
   'type'       =>  'text',
   'length'     =>  25,
   'notnull'  =>  0
   ),
   'scores'     =>  array(            /*check in db*/
   'type'       =>  'text',
   ),
   'timestamp'  =>  array(
   'type'       =>  'date',         /**datetime not used/allowed**/
   ),
   'updated'    =>  array(
   'type'       =>  'date',      /*timestamp does not work in five*/
   ),
   );
// Other indicies
$name = 'tableIdx';
$indexes = array(
    'fields' => array(
        'tableId' => array()
    )
);
?>
