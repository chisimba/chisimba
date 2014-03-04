<?php
// Table Name
$tablename = 'tbl_liftclub_howto';

//Options line for comments, encoding and character set
$options = array('comment' => 'lift club howto', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50
		),
	'timeposted' => array(
		'type' => 'timestamp'
		),
	'markasactive' => array(
		'type' => 'boolean'
		),
	'markasdeleted' => array(
		'type' => 'boolean'
		),	
	'title' => array(
		'type' => 'text',
		'length' => 150
		),
	'body' => array(
		'type' => 'blob'
		),
	'orderhowtos' => array(
		'type' => 'integer',
		'length' => 3,
  'unsigned' => true,
		)
	);
?>
