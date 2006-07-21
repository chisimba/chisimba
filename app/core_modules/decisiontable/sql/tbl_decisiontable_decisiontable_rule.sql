<?php

// Table Name
$tablename = 'tbl_decisiontable_decisiontable_rule';

//Options line for comments, encoding and character set
$options = array('comment' => 'Bridge table used to keep a list of rules and decision tables.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'ruleId' => array(
		'type' => 'text',
		'length' => 32,
        ),
    'decisiontableId' => array(
		'type' => 'text',
		'length' => 255,
        )
    );

//create other indexes here...

$name = 'decisiontable_rule_FKIndex1';

$indexes = array(
                'fields' => array(
                	'decisiontableId' => array(),
                    'ruleId' => array()
                )
        );
?>