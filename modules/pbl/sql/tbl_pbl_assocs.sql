<?php
/*
$sqldata[] = "CREATE TABLE tbl_pbl_assocs( 
    id VARCHAR(32) NOT NULL, 
    left_assoc_id VARCHAR(32) NOT NULL, 
    left_assoc_type VARCHAR(32) NOT NULL, 
    right_assoc_id VARCHAR(32) NOT NULL, 
    right_assoc_type VARCHAR(32) NOT NULL, 
    cid VARCHAR(32) NOT NULL, 
    updated TIMESTAMP(14),
    PRIMARY KEY (id)) TYPE=INNODB ";
    */
    
//5ive definition
$tablename = 'tbl_pbl_assocs';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table defining the associations between scenes in a case', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'left_assoc_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'left_assoc_type' => array(
		'type' => 'text',
		'length' => 32
		),
	'right_assoc_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'right_assoc_type' => array(
		'type' => 'text',
		'length' => 32
		),
	'cid' => array(
		'type' => 'text',
		'length' => 32
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'pbl_assocs_index';

$indexes = array(
                'fields' => array(
                	'cid' => array()
                )
        );
?>