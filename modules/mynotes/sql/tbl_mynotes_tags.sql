<?php
/**
*
* The sql file to set up the table to save tags in
*
*/
// Table Name
$tablename = 'tbl_mynotes_tags';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of tags for the mynotes module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 32
		),
        'count' => array(
                'type' => 'text',
                'length' => 16
                ),
        'userid' => array(
                'type' => 'text',
                'length' => 32
                ),
        'modifiedby' => array(
                'type' => 'text',
                'length' => 32
                ),
        'datecreated' => array(
		'type' => 'timestamp'
		),
        'datemodified' => array(
		'type' => 'timestamp'
		)
	);

//create other indexes here...

$name = 'tbl_mynotes_tags_idx';
$indexes = array(
    'fields' => array(
         'name' => array(),
    )
);
?>
