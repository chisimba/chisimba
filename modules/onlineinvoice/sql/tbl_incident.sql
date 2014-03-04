<?php
// Table Name
$tablename = 'tbl_incident';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table incident is managed by the onlineinvoice module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'type'        =>  'date',
    'notnull'     => 1
    ),
   'date'         =>  array(
    'type'        =>  'date',
    'notnull'     => 1
   ),
   'vendor'       =>  array(
    'type'        =>  'text',
    'length'      =>  32,
    'notnull'     => 1
   ),
   'description'   => array(
    'type'         => 'text',
    'length'       => 255
   ),
   'currency'      => array(
    'type'         => 'text',
    'length'       => 255,
    'notnull'     => 1
   ),
   'cost'         =>  array(
    'type'        =>  'float',
    'notnull'     => 1
   ),
   'exchangerate' =>  array(
      'type'      =>  'float',
      'notnull'     => 1
  ),
  
  'incidentratefile'  =>  array(
      'type'          =>  'text'
  ),
  
  'quotesource'     =>  array(
      'type'        =>  'text',
      'length'      =>  32
  ),
  'receiptfiles'    =>  array(
      'type'        =>  'text'
  ),
  'affidavitfiles'  =>  array(
      'type'        =>  'text'
      
  ),
  'inidentexepense' =>array(
      'type'          =>  'float'
  )
  );
?>
