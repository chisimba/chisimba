<?php
// Table Name
$tablename = 'tbl_tutorials_marking';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold marks given for answers', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'tutorial_id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'answer_id' => array(
        'type' => 'text',
        'length' => '32',
    ),
	'student_id' => array(
		'type' => 'text',
		'length' => '32',
	),
    'mark' => array(
        'type' => 'integer',
        'length' => '3',
    ),
    'markers_comment' => array(
        'type' => 'clob',
    ),
    'marker_id' => array(
        'type' => 'text',
        'length' => '32',
    ),
    'is_moderator' => array(
        'type' => 'integer',
        'length' => '1',
    ),
    'deleted' => array(
        'type' => 'integer',
        'length' => '1',
    ),
	'updated' => array(
        'type' => 'timestamp',
	),
);
?>