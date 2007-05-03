<?php

// Table Name
$tablename = 'tbl_sitepermissions_rule_condition';

//Options line for comments, encoding and character set
$options = array('comment' => 'Bridge table used to keep a list of conditions and rules.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
    'moduleid' => array(
		'type' => 'text',
		'length' => 32,
    ),
    'ruleid' => array(
		'type' => 'text',
		'length' => 32,
    ),
	'conditionid' => array(
		'type' => 'text',
		'length' => 32,
    ),
);

//create other indexes here...

$name = 'FK_rule_condition';

$indexes = array(
    'fields' => array(
        'moduleid' => array(),
        'ruleid' => array(),
        'conditionid' => array(),
    ),
);
?>