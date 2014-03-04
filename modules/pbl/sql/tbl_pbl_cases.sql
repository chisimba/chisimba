<?php
/*
$sqldata[] = "CREATE TABLE tbl_pbl_cases( 
    id VARCHAR(32) NOT NULL, 
    name VARCHAR(30), 
    entry_point VARCHAR(32) NOT NULL default '0', 
    context VARCHAR(255), 
    owner VARCHAR(32), 
    updated TIMESTAMP(14),
    PRIMARY KEY (id)) TYPE=INNODB ";
*/    
    
//5ive definition
$tablename = 'tbl_pbl_cases';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing a list of cases in each context', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 50
		),
	'entry_point' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => true,
		'default' => '0'
		),
	'context' => array(
		'type' => 'text',
		'length' => 255
		),
	'owner' => array(
		'type' => 'text',
		'length' => 32
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'pbl_cases_index';

$indexes = array(
                'fields' => array(
                	'context' => array()
                )
        );
?>