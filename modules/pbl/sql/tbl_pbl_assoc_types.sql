<?php
/*
$sqldata[] = "CREATE TABLE tbl_pbl_assoc_types( 
    id VARCHAR(32) NOT NULL, 
    name varchar(30), 
    descr varchar(100), 
    wordsid int, 
    updated TIMESTAMP(14),
    PRIMARY KEY (id)) TYPE=INNODB ";
*/
    
    
//5ive definition
$tablename = 'tbl_pbl_assoc_types';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table defining the types of associations between scenes in a case', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 30
		),
	'descr' => array(
		'type' => 'text',
		'length' => 100
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'pbl_assoc_types_index';

$indexes = array(
                'fields' => array(
                	'name' => array()
                )
        );
?>