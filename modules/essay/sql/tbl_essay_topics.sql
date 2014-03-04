<?php
// Table Name
$tablename = 'tbl_essay_topics';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table equipment is managed by the onlineinvoice module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
    'type' => 'text',
    'length' => 32
    ),
  'name'  => array(
     'type'  =>  'text',
     'length'=>  255
    ),
    'context' =>  array(
     	'type'  =>  'text',
    	'length' =>  255
    ),
  'description'  =>  array(
      	'type'    =>  'clob'
    ),
   'instructions' =>  array(
    	'type'        =>  'clob'
    ),
    'closing_date'     =>  array(
    	'type'        =>  'timestamp',
    ),
   'bypass'         =>  array(
      	'type'      =>  'integer',
    	'length'=>  4
   ),
   'forceone'  =>  array(
      'type'        =>  'integer',
       'length'=>  4
   ),
   'rubric'  =>  array(
      'type'        =>  'text',
      'length'      => 32
   ),
   'percentage'  =>  array(
      'type'        =>  'integer',
      'length'      => 11
   ),
   'userid'  =>  array(
      'type'        =>  'text',
    'length' =>   32
   ),
    'last_modified' =>  array(
    'type'        =>  'timestamp'
    ),
   'updated'  =>  array(
      'type'        =>  'timestamp'
      )
   );
// Other indicies
$name = 'contextcodex';
$indexes = array(
    'fields' => array(
        'context' => array()
    )
);
?>



