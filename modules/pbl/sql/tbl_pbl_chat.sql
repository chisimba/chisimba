<?php
/*
$sqldata[] = "CREATE TABLE tbl_pbl_chat(
    id VARCHAR(32) NOT NULL, 
    classroomid VARCHAR(32) NOT NULL, 
    studentid VARCHAR(32) NOT NULL, 
    msg varchar(255), 
    status int default '0', 
    entrydate datetime, 
    updated TIMESTAMP(14),
    INDEX(classroomid), 
    PRIMARY KEY (id)) TYPE=INNODB ";
*/    
    
//5ive definition
$tablename = 'tbl_pbl_chat';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table the chat / conversations from the pbl sessions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'classroomid' => array(
		'type' => 'text',
		'length' => 32
		),
	'studentid' => array(
		'type' => 'text',
		'length' => 32
		),
	'msg' => array(
		'type' => 'text',
		'length' => 255
		),
	'status' => array(
		'type' => 'integer',
		'length' => 11,
		'notnull' => true,
		'default' => '0'
		),
	'entrydate' => array(
		'type' => 'timestamp',
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'pbl_chat_index';

$indexes = array(
                'fields' => array(
                	'classroomid' => array()
                )
        );
?>