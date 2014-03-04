<?php
/*
$sqldata[] = "CREATE TABLE tbl_pbl_scenes( 
    id VARCHAR(32) NOT NULL, 
    caseid varchar(32), 
    name varchar(32), 
    display text, 
    istask int, 
    isdone int, 
    updated TIMESTAMP(14),
    PRIMARY KEY (id)) TYPE=INNODB ";
*/    
    
//5ive definition
$tablename = 'tbl_pbl_scenes';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the scenes / sections in a case', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'caseid' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 32
		),
	'display' => array(
		'type' => 'clob'
		),
	'istask' => array(
		'type' => 'integer',
		'length' => 11
		),
	'isdone' => array(
		'type' => 'integer',
		'length' => 11
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'pbl_scenes_index';

$indexes = array(
                'fields' => array(
                	'caseid' => array()
                )
        );
?>