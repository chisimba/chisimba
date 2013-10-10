<?php
/* Table Name*/
$tablename = 'tbl_pastpapers';

/*Options line for comments, encoding and character set*/
$options = array('comment' => 'The details of the past papers that are uploaded', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

/* Fields*/
$fields = array(
   'id' => array(
    'type' => 'text',
    'length' => 32,
    'notnull' => 1
    ),
  'contextcode'  => array(
     'type'  =>  'text',
     'length' =>  32
       
    ),
   'topic'  => array(
     'type'  =>  'text',
     'length' =>  100
       
    ),	
	
  'userid' =>  array(
      'type'  =>  'text',
      'length' => 32
      
    ),
    'filename'  =>  array(
      'type'    =>  'text',
      'length'  =>  100
      
    ),
    'dateuploaded'  =>  array(
      'type'    =>  'timestamp'
      
    ),
	'options'  =>  array(
      'type'    =>  'integer',
	  'length'=>1
      
    ),
    'hasanswers'  =>  array(
       'type' => 'integer',
        'length' => 1
     
    ),
    'examyear'  =>  array(
      'type'    =>  'date'       
    ),
   
    'updated'  =>  array(
      'type'    =>  'timestamp'
     
    )
);


$indexes = array(
                'fields' => array(
                    'userid' => array(),
                    'contextcode' => array()
                ),
        );
?>
