<?php

$tablename = 'tbl_librarysearch_sources';

$options = array('comment' => 'A collection of library sources grouped into clusters', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'cluster_id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'title' => array(
        'type' => 'text',
        'length' => 255
        ),
    'description' => array(
        'type' => 'text',
        'length' => 255
        ),
    'uri' => array(
        'type' => 'text',
        'length' => 255
        ),
    'workflow' => array(
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
