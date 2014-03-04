<?php
/*
$sqldata[] = "CREATE TABLE tbl_pbl_content(
    id VARCHAR(32) NOT NULL, 
    classroomid VARCHAR(32) NOT NULL, 
    li TEXT, 
    hypothesis TEXT, 
    modified timestamp(10), 
    updated TIMESTAMP(14),
    PRIMARY KEY(id)) TYPE=INNODB ";
*/    
    
//5ive definition
$tablename = 'tbl_pbl_content';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the content created by students during a pbl session - learning objectives and hypotheses', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'classroomid' => array(
		'type' => 'text',
		'length' => 32
		),
	'li' => array(
		'type' => 'clob'
		),
	'hypothesis' => array(
		'type' => 'clob'
		),
	'modified' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'pbl_content_index';

$indexes = array(
                'fields' => array(
                	'classroomid' => array()
                )
        );
?>