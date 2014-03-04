<?php

$tablename = 'tbl_phpunit_reports';

$options = array('comment' => 'PHPUnit Reports', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'optimization_target' => array(
		'type' => 'text',
		'length' => 255
		),
	'optimization_reason' => array(
		'type' => 'text',
		'length' => 255
		),
	'optimization_suggestion' => array(
		'type' => 'text',
		'length' => 255
		),
	'missing_language_items' => array(
		'type' => 'text',
		'length' => 255
		),
	'php_notices' => array(
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
