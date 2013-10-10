<?php
/*
$sqldata[] = "CREATE TABLE tbl_etd_collections(
    id VARCHAR(32) NOT NULL,
    name VARCHAR(255) NOT NULL default 'collection',
    subject VARCHAR(255),
    submissionType VARCHAR(50),
    creatorId VARCHAR(32) NOT NULL,
    dateCreated DATETIME NOT NULL,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY (id)
    ) type=InnoDB COMMENT='Table defining the collections of etds'";
*/
    
//5ive definition
$tablename = 'tbl_etd_collections';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table defining the collections of ETDs', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1,
		'default' => 'collection'
		),
	'subject' => array(
		'type' => 'text',
		'length' => 255
		),
	'submissiontype' => array(
		'type' => 'text',
		'length' => 50
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		),
	);

// create other indexes here...

$name = 'etd_collections_id';

$indexes = array(
                'fields' => array(
                	'name' => array(),
                	'submissiontype' => array()
                )
        );
?>