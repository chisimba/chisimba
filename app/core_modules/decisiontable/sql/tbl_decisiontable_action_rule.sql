<?php
// Table Name
$tablename = 'tbl_decisiontable_action_rule';

//Options line for comments, encoding and character set
$options = array('comment' => 'Bridge table used to keep a list of rules and actions.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'actionId' => array(
        'type' => 'text',
        'length' => 32,
        )
    );

//create other indexes here...

$name = 'action_rule_FKIndex1';

$indexes = array(
                'fields' => array(
                    'actionId' => array(),
                    'ruleId' => array()
                )
        );
?>