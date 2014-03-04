<?php
// Table Name
$tablename = 'tbl_essay_filestore';

//Options line for comments, encoding and character set
$options = array('comment' => 'The details of uploaded essay files', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
    'type' => 'text',
    'length' => 32,
    'notnull' => 1
  ),
  'contex_id'  => array(
     'type'  =>  'text',
     'length' =>  32,
     'notnull' => 1    
    ),
  'userid' =>  array(
      'type'  =>  'text',
      'length' => 32,
   'notnull' => 1  
    ),
    'fileid'  =>  array(
      'type'    =>  'text',
      'length'  =>  100,
      'notnull' => 1
    ),
    'filename'  =>  array(
      'type'    =>  'text',
      'length'  =>  120,
      'notnull' => 1
    ),
    'submitdate'  =>  array(
      'type'    =>  'date',
      'notnull' => 1
    ),
    'mark'  =>  array(
      'type'    =>  'float', 
      'length'  =>  4,
      'notnull' => 1 
    ),
    'filetype'  =>  array(
      'type'    =>  'text',
      'length'  =>  32,
      'notnull' => 1
    ),
    'size'  =>  array(
      'type'    =>  'integer', 
      'length'  =>  11,
      'notnull' => 1
    ),
    'uploadtime'  =>  array(
      'type'    =>  'integer',
      'length'  =>  11,
       'notnull' => 1
    ),
    'updated'  =>  array(
      'type'    =>  'timestamp',
      'length'  =>  14,
      'notnull' => 1
    )
);
?>