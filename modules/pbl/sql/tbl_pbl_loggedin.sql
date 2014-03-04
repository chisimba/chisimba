<?php
/*
$sqldata[] = "CREATE TABLE tbl_pbl_loggedin(
    id VARCHAR(32) NOT NULL, 
    classroomid VARCHAR(32) NOT NULL, 
    studentid VARCHAR(32) NOT NULL, 
    position ENUM('c','s','f','n') NOT NULL default 'n', 
    isavailable INT NOT NULL default 0, 
    notes TEXT, 
    updated TIMESTAMP(14),
    PRIMARY KEY (id)) TYPE=INNODB ";
*/    
    
//5ive definition
$tablename = 'tbl_pbl_loggedin';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table listing the students currently in the groups', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'position' => array(
		'type' => 'text',
		'length' => 2,
		'notnull' => true,
		'default' => 'n'
		),
	'isavailable' => array(
		'type' => 'integer',
		'length' => 11,
		'notnull' => true,
		'default' => '0'
		),
	'notes' => array(
		'type' => 'clob'
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'pbl_loggedin_index';

$indexes = array(
                'fields' => array(
                	'classroomid' => array()
                )
        );
?>