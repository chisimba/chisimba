<?php
/*
CREATE TABLE tbl_language_modules (
  id int(11) NOT NULL auto_increment,
  module_id varchar(50) NOT NULL default '',
  code varchar(50) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=InnoDB ;

INSERT INTO tbl_language_modules VALUES (77,'moduleadmin','ModuleAdmin');
*/
// Table Name
$tablename = 'tbl_language_modules';

//Options line for comments, encoding and character set
$options = array('comment' => 'language modules','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'module_id' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE
		),
    'code' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE
		)
    );

?>
