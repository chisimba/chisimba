<?php
/* Table Name*/
$tablename = 'tbl_pastpaper_answers';

/*Options line for comments, encoding and character set*/
$options = array('comment' => 'The details of the past paper answers that are uploaded', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

/* Fields*/
$fields = array(
   'id' => array(
    'type' => 'text',
    'length' => 32,
    'notnull' => 1
    ),
  'paperid'  => array(
     'type'  =>  'text',
     'length' =>  32
       
    ),
   'addedby'  => array(
     'type'  =>  'text',
     'length' =>  32
       
    ),	
	
  'filename' =>  array(
      'type'  =>  'text',
      'length' => 50
      
    ),
    'dateuploaded'  =>  array(
      'type'    =>  'date'
      
      
    ),
    'published'  =>  array(
      'type'    =>  'integer',
	  'length'=>1
      
    ),
	'options'  =>  array(
      'type'    =>  'integer',
	  'length'=>1
      
    ),   
   
    'updated'  =>  array(
      'type'    =>  'timestamp'
     
    )
);


$indexes = array(
                'fields' => array(
                    'addedby' => array()
                    
                )
        );
?>
