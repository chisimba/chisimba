<?php
// Table Name
$tablename = 'tbl_eportfolio_comment';

//Options line for comments, encoding and character set
$options = array('comment' => 'store comments made on an eportfolio part i.e. reflection', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'eportfoliopartid' => array(
		'type' => 'text',
		'length' => 50
		),
	'commentoruserid' => array(
		'type' => 'text',
		'length' => 50
		),
	'comment' => array(
		'type' => 'text',
		),
	'isapproved' => array(
		'type' => 'text',
		'length' => 1
		),
	'postdate' => array(
		'type' => 'timestamp'
		),
	'isdeleted' => array(
		'type' => 'text',
		'length' => 1
		)		
	);
?>
