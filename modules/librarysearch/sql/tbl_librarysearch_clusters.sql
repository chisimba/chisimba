<?php

$tablename = 'tbl_librarysearch_clusters';

$options = array('comment' => 'The top level grouping of library sources', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
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
