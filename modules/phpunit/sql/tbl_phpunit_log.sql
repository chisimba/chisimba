<?php

$tablename = 'tbl_phpunit_log';

$options = array('comment' => 'PHP Unit Log', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
    'module_name' => array(
        'type' => 'text',
        'length' => 255
        ),
    'request_vars' => array(
        'type' => 'blob'
        ),
	'datestamp' => array(
		'type' => 'timestamp'
		)
	);

$indexes = array(
                'fields' => array(
                    'id' => array(),
                )
        );
?>
