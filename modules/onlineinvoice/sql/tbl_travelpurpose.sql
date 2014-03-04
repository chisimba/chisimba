<?php
// Table Name
$tablename = 'tbl_travelpurpose';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table travel purpose table is managed by the onlineinvoice module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
    'type' => 'text',
    'length' => 32,
    'notnull'     => 1
    ),
  'createdby'  => array(
     'type'  =>  'text',
     'length'=>  32
    ),
  'datecreated' =>  array(
     'type'  =>  'date'
    ),
  'modifiedby'  =>  array(
     'type'    =>  'text',
     'length'  =>  32
    ),
  'datemodified' =>  array(
     'type'        =>  'date'
    ),
  'updated'     =>  array(
     'type'        =>  'date'
    ),
  'date'  =>  array(
    'type'        =>  'date',
    'notnull'     => 1
   ),
  'travelpurpose' => array(
   'type'   =>  'text',
   'length' =>  32,
   'notnull'     => 1
   ),   
   );

//create other indexes here...



?>
