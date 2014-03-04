<?php
/*$sqldata[]=" CREATE TABLE tbl_essay_blob ("
    ."id varchar(32),"
    ."fileId varchar(100),"
    ."segment int,"
    ."filedata blob,"
    ."`updated` TIMESTAMP(14) NOT NULL"
    .") type=InnoDB 
    COMMENT='The segments of uploaded essay files'";*/

// Table Name
$tablename = 'tbl_essay_blob';

//Options line for comments, encoding and character set
$options = array('comment' => 'The segments of uploaded essay files', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
    'type' => 'text',
    'length' => 32,
    'notnull' => 1  
    ),
  'fieldid'  => array(
     'type'  =>  'text',
     'length'=>  100,
     'notnull' => 1    
    ),
  'segment' =>  array(
      'type'  =>  'integer',
      'length'=>  11,
      'notnull' => 1   
    ),
  'filedata'  =>  array(
      'type'    =>  'blob',
      'notnull' => 1
    ),
    'updated'  => array(
     'type'  =>  'timestamp',
     'length'=>  14,
     'notnull' => 1
    )
);
?>
