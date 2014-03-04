<?php
// Table Name
$tablename = 'tbl_supplies';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table supplies is managed by the onlineinvoice module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
      'type'      =>  'date',
      'notnull'     => 1
   ),
   'vendorname'  =>  array(
      'type'        =>  'text',
      'notnull'     => 1
   ),
   'itemdescription'  =>  array(
      'type'        =>  'text',
      'length'      => 255,
      'notnull'     => 1
   ),
   'currency'  =>  array(
      'type'        =>  'text',
      'lenght'      => 3,
      'notnull'     => 1
   ),
   'supplycost'  =>  array(
      'type'        =>  'float',
      'notnull'     => 1
   ),
    'quotesource' =>  array(
    'type'        =>  'text',
    'length'      =>  32
    ),
   'exchangerate'  =>  array(
      'type'        =>  'float',
      'notnull'     => 1
   ),
    'supplyexchratefile' =>  array(
    'type'        =>  'text',
    
   ),
   'attachreceipt' => array(
   'type' 	   =>  'text',
   ),
   'affidavitfilename' =>  array(
   'type'              =>  'text',
   )

   );





