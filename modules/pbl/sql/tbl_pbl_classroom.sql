<?php
/*
$sqldata[] = "CREATE TABLE tbl_pbl_classroom( 
    id VARCHAR(32) NOT NULL, 
    name VARCHAR(32), 
    caseid VARCHAR(32) NOT NULL, 
    context VARCHAR(255) NOT NULL, 
    owner VARCHAR(32), 
    facilitator VARCHAR(32), 
    chair VARCHAR(32), 
    scribe VARCHAR(32), 
    activescene VARCHAR(32) NOT NULL, 
    sessions INT DEFAULT '0', 
    status ENUM('c','o','e') default 'o', 
    opentime datetime, 
    updated TIMESTAMP(14),
    PRIMARY KEY (id)) TYPE=INNODB ";
*/    
    
//5ive definition
$tablename = 'tbl_pbl_classroom';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the groups of students', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 32
		),
	'caseid' => array(
		'type' => 'text',
		'length' => 32
		),
	'context' => array(
		'type' => 'text',
		'length' => 255
		),
	'owner' => array(
		'type' => 'text',
		'length' => 32
		),
	'facilitator' => array(
		'type' => 'text',
		'length' => 32
		),
	'chair' => array(
		'type' => 'text',
		'length' => 32
		),
	'scribe' => array(
		'type' => 'text',
		'length' => 32
		),
	'activescene' => array(
		'type' => 'text',
		'length' => 32
		),
	'sessions' => array(
		'type' => 'integer',
		'length' => 11,
		'notnull' => true,
		'default' => '0'
		),
	'status' => array(
		'type' => 'text',
		'length' => 2,
		'notnull' => true,
		'default' => 'o'
		),
	'opentime' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'pbl_classroom_index';

$indexes = array(
                'fields' => array(
                	'context' => array()
                )
        );
?>