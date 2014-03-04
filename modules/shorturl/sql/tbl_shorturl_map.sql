<?php

$tablename = 'tbl_shorturl_map';

$options = array('comment' => 'Short URL Mappings Table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
    'match_url' => array(
        'type' => 'text',
        'length' => 255
        ),
    'target_url' => array(
        'type' => 'text',
        'length' => 255
        ),
	'is_dynamic' => array(
		'type' => 'integer',
		'length' => 1
		),
    'key_id' => array(
        'type' => 'text',
        'length' => 255
        ),
    'ordering' => array(
		'type' => 'integer',
        'length' => 11,
		),
	'datestamp' => array(
		'type' => 'timestamp'
		)
	);

$indexes = array(
                'fields' => array(
                    'id' => array(),
                    'ordering' => array()
                )
        );
?>
