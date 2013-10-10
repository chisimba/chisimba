<?php
//5ive definition
$tablename = 'tbl_forum_temp_attachment';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'temp_id' => array(
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

$name = 'tbl_forum_temp_attachment_idx';

$indexes = array(
                'fields' => array(
                	'temp_id' => array(),
                    'attachment_id' => array()
                )
        );

?>