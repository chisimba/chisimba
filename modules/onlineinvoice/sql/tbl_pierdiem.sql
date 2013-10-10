<?php
//create table for lodging expenses
// Table Name
$tablename = 'tbl_pierdiem';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table pierdiem is managed by the onlineinvoice module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
      'length'  =>  32
    ),
   'datemodified' =>  array(
    'type'        =>  'date',
    'notnull'     => 1
    ),
    'updated'     =>  array(
      'type'        =>  'date',
      'notnull'     => 1
    ),
    'foreignordomestic'  => array(
      'type'      =>  'boolean'
    ), 
    'date'        =>  array(
      'type'        =>  'date'
      
    ),
    'bchoice' => array(
      'type'          => 'boolean'
    ),
    'blocation' =>  array(
      'type'            =>  'text',
      'length'          =>  32
    ),
    'btrate' =>  array(
      'type'        =>  'float'
    ),
    'lchoice' => array(
      'type'     => 'boolean'
    ),
    'llocation' =>  array(
      'type'        =>  'text',
      'length'      =>  32
      
    ),
    'lRate'     =>  array(
      'type'        =>  'float',
      'notnull'     => 1
    ),
    'dchoice' => array(
      'type'       => 'boolean'
    ),
    'dlocation' =>  array(
      'type'         =>  'text',
      'length'       =>  32
    ),
    'drrate'     =>  array(
      'type'         =>  'float',
      'notnull'     => 1
    ),
    'total'   =>  array(
    'type'    =>  'float'
    ),
    'finaltotal'  =>array(
    'type'        => 'float'
    )
    
    );
?>
