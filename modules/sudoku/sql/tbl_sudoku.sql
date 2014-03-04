<?php
/*
$sqldata[] = "CREATE TABLE tbl_sudoku(
    id VARCHAR(32) NOT NULL,    
    difficulty SMALLINT(1) NULL,
    solution TEXT NULL,
    puzzle TEXT NULL,
    saved TEXT NULL,
    dateSaved DATETIME NULL,
    solved TINYTEXT NULL,
    dateSolved DATETIME,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    PRIMARY KEY(id),
    KEY(creatorId),
    CONSTRAINT `sudoku` FOREIGN KEY (`creatorId`) REFERENCES `tbl_users` (`userId`)
    ) TYPE=InnoDB COMMENT='Sudoku puzzles'";
*/

//5ive definition
$tablename = 'tbl_sudoku';

//Options line for comments, encoding and character set
$options = array('comment' => 'Sudoku puzzles', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'difficulty' => array(
		'type' => 'integer',
		'length' => 1
		),
	'solution' => array(
		'type' => 'text'
		),
	'puzzle' => array(
		'type' => 'text'
		),
	'saved' => array(
		'type' => 'text'
		),
	'date_saved' => array(
		'type' => 'timestamp'
		),
	'solved' => array(
		'type' => 'integer',
		'length' => 1
		),
	'date_solved' => array(
		'type' => 'timestamp'
		),
	'time_taken' => array(
		'type' => 'text',
		'length' => 8  
		),
	'creator_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'date_created' => array(
		'type' => 'timestamp'
		),
	);

// create other indexes here...
$name = 'sudoku_creator_id';

$indexes = array(
                'fields' => array(
                	'creator_id' => array(),
                )
        );
?>