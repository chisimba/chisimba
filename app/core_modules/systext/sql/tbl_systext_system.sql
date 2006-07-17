<?php
/*
$sqldata[] = "CREATE TABLE tbl_systext_system(
    id VARCHAR(32) NOT NULL,
    systemType VARCHAR(15) NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    canDelete TINYTEXT NULL,
    PRIMARY KEY(id),
    KEY(creatorId),
    CONSTRAINT `Systext_system_creator` FOREIGN KEY (`creatorId`) REFERENCES `tbl_users` (`userId`)
    ) TYPE=InnoDB COMMENT='Table to hold system types for text abstraction'";

 */
// Table Name
$tablename = 'tbl_systext_system';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold system types for text abstraction', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,

		),
    'systemType' => array(
        'type' => 'text',
		'length' => 25
        ),
    'creatorId' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
		),
    'dateCreated' => array(
		'type' => 'date',
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
                	'creatorId' => array(),
                    'systemType' => array()
                )
        ); 
 /*
$sqldata[] = "INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated, canDelete) 
    values('PKVALUE', 'default', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated) 
    values('PKVALUE', 'elearn', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated) 
    values('PKVALUE', 'groups', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated) 
    values('PKVALUE', 'workgroups', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated) 
    values('PKVALUE', 'pgrad', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated) 
    values('PKVALUE', 'alumni', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated) 
    values('PKVALUE', 'content', '1', '0000-00-00')";
*/


?>
