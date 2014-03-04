<?php
// Table Name
$tablename = 'tbl_rubric_tables';
//Options line for comments, encoding and character set
$options = array('comment' => 'The ruberic tables is managed by the ruberic module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id'             => array(
    'type'         => 'text',
    'length'       => 32,
    'notnull'      => 1
    ),
   'contextCode'   =>  array(
    'type'         =>  'text',
    'length'       =>  50,
    'notnull'      =>  0
   ), 
   'title'         => array(
   'type'          => 'text',
   'length'        => 50,
   'notnull'       =>  0
   ),
   'description'   => array(
   'type'          => 'text',
   'length'        => 100,
   'notnull'       =>  0
   ),
   'rows'       =>  array(
   'type'       =>  'integer',
   'length'     =>   11,
   'notnull'    =>  0
   ),
   'cols'       =>  array(
   'type'       =>  'integer',
   'length'     =>  11,
   'notnull'    =>  0 
   ),
   'updated'    =>  array(
   'type'       =>  'date',      /*timestamp does not work in five*/
   ),
   'userId'     =>  array(
   'type'       =>  'text',
   'length'     =>  25,
   'notnull'    =>  0 
   )
   );
// Other indicies
$name = 'contextCodex';
$indexes = array(
    'fields' => array(
        'contextCode' => array()
    )
);
?>
