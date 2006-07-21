<?php

// Table Name
$tablename = 'tbl_decisiontable_rule_condition';

//Options line for comments, encoding and character set
$options = array('comment' => 'Bridge table used to keep a list of conditions and rules.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'conditionId' => array(
		'type' => 'text',
		'length' => 32,
        ),
    'ruleId' => array(
		'type' => 'text',
		'length' => 32,
        )
    );

//create other indexes here...

$name = 'action_rule_FKIndex1';

$indexes = array(
                'fields' => array(
                	'conditionId' => array(),
                    'ruleId' => array()
                )
        );
?>