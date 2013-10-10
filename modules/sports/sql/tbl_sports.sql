<?php
/*
  $sqldata[]="CREATE TABLE tbl_sports (
  id varchar(32) NOT NULL default '',
  name varchar(32) NOT NULL default '',
  userId varchar(50) NOT NULL default '',
  description text default '',
  evaluationMode varchar(100) default 'Goals', 
  dateCreated timestamp(14),  
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (userId)
  REFERENCES tbl_users(userId) 
  ON DELETE CASCADE
  ON UPDATE CASCADE
  
  
)TYPE=InnoDB
";


$sqldata[]=" INSERT INTO `tbl_sports` (id,name,userId,dateCreated) values ('gen13Srv39Nme19_1','Soccer','1',now());";
$sqldata[]=" INSERT INTO `tbl_sports` (id,name,userId,dateCreated) values ('gen13Srv39Nme19_2','Volleball','1',now());";
*/

//5ive definition
$tablename = 'tbl_sports';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold the names of the sports activities', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'name' => array(
		'type'=>'text',
		'length' => 32
		),
	'player_no' => array(
		'type' => 'integer',
		'length' =>2
	'userId' => array(
		'type'=>'text',
		'length' => 50
		),
	'description' => array(
		'type' =>'clob'
		),
	'evaluationMode' => array(
		'type' => 'text',
		'length' => 32
		),
	'dateCreated' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);
	
// create other indexes here...
$name = 'sports_index';

$indexes = array(
                'fields' => array(
                    'userId' => array()
                )
        );
?>