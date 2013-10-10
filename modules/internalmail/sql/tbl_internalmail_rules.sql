<?php
//5ive definition
$tablename = 'tbl_internalmail_rules';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing email folder rules.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'mail_action' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'mail_field' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'criteria' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'rule_action' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'dest_folder_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'email_rules_index';

$indexes = array(
                'fields' => array(
                    'user_id' => array(),
                ),
        );
?>