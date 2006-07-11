<?php
/*
CREATE TABLE tbl_languagetext (
  code varchar(50) NOT NULL default '',
  description varchar(255) default NULL,
  PRIMARY KEY  (code)
) TYPE=InnoDB ;
*/
// Table Name
$tablename = 'tbl_languagetext';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'code' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE
		),
    'description' => array(
		'type' => 'text',
		'length' => 255
		)
    );

?>