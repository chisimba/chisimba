<?php
//create table for lodging expenses
// Table Name
$tablename = 'tbl_odc';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table lodging is managed by the onlineinvoice module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
    'type' => 'text',
    'length' => 32,
    'notnull'     => 1,
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
    'type'        =>  'date'
    ),
    'updated'     =>  array(
    'type'        =>  'date',
    'notnull'     => 1
    ),
   'date'         =>  array(
      'type'      =>  'date',
      'notnull'     => 1
   ),
   'vendorname'       =>  array(
      'type'      =>  'text',
      'length'    =>  255,
      'notnull'     => 1
   ),
      'odcdescription'  =>  array(
      'type'        =>  'text',
      'length'      => 255,
      'notnull'     => 1
   ),

   'currency'     =>  array(
      'type'      =>  'text',
      'length'    =>  255,
      'notnull'     => 1
   ),
   'odccost'         =>  array(
    'type'        =>  'float',
    'notnull'     => 1
   ),
   'exchangerate' =>  array(
    'type'        =>  'float',
    'notnull'     => 1
    ),
    'quotesource' =>  array(
    'type'        =>  'text',
    'length'      =>  32,
    ),
    'odcexchratefile' =>  array(
    'type'        =>  'text',
    ),
    'attachreceipt' => array(
    'type'            =>  'text',
    ),
    'affidavitfilename' =>  array(
    'type'              =>  'text',
    ),
    'totroomrate'   =>  array(
    'type'          =>  'float',
    )
  )
?>
