<?php

$tablename = 'tbl_shorturl_key';

$options = array('comment' => 'Short URL Key Mappings Table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'map_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'key_nr' => array(
		'type' => 'integer',
		'length' => 4
		),
	'tbl_name' => array(
		'type' => 'text',
		'length' => 255
		),
	'tbl_field' => array(
		'type' => 'text',
		'length' => 255
		)
	);

$indexes = array(
                'fields' => array(
                    'id' => array()
                )
        );
?>
