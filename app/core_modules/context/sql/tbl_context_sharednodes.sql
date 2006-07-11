<?
/*
  $sqldata[]="CREATE TABLE tbl_context_sharednodes (
  id VARCHAR(32) NOT NULL,
  shared_nodeid VARCHAR(32) NULL,
  root_nodeid VARCHAR(32) NOT NULL,
  nodeid VARCHAR(32) NULL, 
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id)
)
TYPE=InnoDB;
";*/

$tablename = 'tbl_context_sharednodes';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'shared_nodeid' => array(
		'type' => 'text',
		'length' => 32
		),
	'root_nodeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'nodeid' => array(
		'type' => 'text',
		'length' => 32
		),
    'updated' => array(
        'type' => 'timestamp'
        )
    );
    
?>