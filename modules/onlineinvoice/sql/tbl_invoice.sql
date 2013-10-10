<?php
// Table Name
$tablename = 'tbl_invoice';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table invoiced is managed by the onlineinvoice module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
    'type' => 'text',
    'length' => 32,
    'notnull'     => 1
    ),
  'createdby'  => array(
     'type'  =>  'text',
     'length'=>  32,
     'notnull'     => 1
    ),
  'datecreated' =>  array(
      'type'  =>  'date',
      'notnull'     => 1
    ),
  'modifiedby'  =>  array(
      'type'    =>  'text',
      'length'  =>  32,
      'notnull'     => 1
    ),
   'datemodified' =>  array(
    'type'        =>  'date',
    'notnull'     => 1
    ),
   'updated'     =>  array(
    'type'       =>  'date',
    'notnull'     => 1
    ),    
   'begindate'   => array(
    'type'       => 'date',
    'notnull'     => 1
   ),
   'enddate'     => array(
   'type'        => 'date',
   'notnull'     => 1
   )
  );

?>
