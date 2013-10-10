<?php
//5ive definition
$tablename = 'tbl_forum_post_attachment';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'post_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'attachment_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'userid' => array(
		'type' => 'text',
        'length' => '25',
        'notnull' => 1
		),
    'datecreated' => array(
		'type' => 'timestamp'
		)
    );
    
//create other indexes here...

$name = 'tbl_forum_post_attachment_idx';

$indexes = array(
                'fields' => array(
                	'post_id' => array(),
                    'attachment_id' => array()
                )
        );

?>