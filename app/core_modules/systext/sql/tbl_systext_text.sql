<?php

/*
$sqldata[] = "CREATE TABLE tbl_systext_text(
    id VARCHAR(32) NOT NULL,    
    text VARCHAR(50) NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    canDelete TINYTEXT NULL,	
    PRIMARY KEY(id),
    KEY(creatorId),
    CONSTRAINT `Systext_text_creator` FOREIGN KEY (`creatorId`) REFERENCES `tbl_users` (`userId`)
    ) TYPE=InnoDB COMMENT='List of text items to be abstracted'";
*/

// Table Name
$tablename = 'tbl_systext_text';

//Options line for comments, encoding and character set
$options = array('comments' => 'List of text items to be abstracted', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'text' => array( // BAD
        'type' => 'text',
		'length' => 50
        ),
    'creatorId' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
		),
    'dateCreated' => array(
		'type' => 'datetime',
        'notnull' => TRUE
		),
    'canDelete' => array(
		'type' => 'text',
        'length' => 3
		)
    );
 
//create other indexes here...

$name = 'creatorId';

$indexes = array(
                'fields' => array(
                	'creatorId' => array()
                )
        );

/*
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete) 
    values('PKVALUE', 'context', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'contexts', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'author', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'authors', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'organisation', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'organisations', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'readonly', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'readonlys', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'workgroup', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'workgroups', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'story', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'stories', '1', '0000-00-00', 'N')";
*/
?>