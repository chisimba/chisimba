<?php
// Table Name
$tablename = 'tbl_liftclub_faq';

//Options line for comments, encoding and character set
$options = array('comment' => 'lift club faqs', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'faqtitle' => array(
		'type' => 'text',
		'length' => 150
		),
	'faqbody' => array(
		'type' => 'blob'
		),
	'faqorder' => array(
		'type' => 'integer',
		'length' => 3
		)
	);
?>
