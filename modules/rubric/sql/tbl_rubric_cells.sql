<?php
// Table Name
$tablename = 'tbl_rubric_cells';

//Options line for comments, encoding and character set
$options = array('comment' => 'The ruberic cells is managed by the ruberic module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
   'row'   =>  array(
   'type'      =>  'integer',
   'length'    =>   11,
   'notnull'  =>  0
   ),
   'col'   =>  array(
   'type'      =>  'integer',
   'length'    =>  11,
   'notnull'  =>  0 
   ),
   'contents'  =>  array(
   'type'       =>  'text',
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
