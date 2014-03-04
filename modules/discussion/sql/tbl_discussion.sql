<?php

//5ive definition
$tablename = 'tbl_discussion';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'discussion_context' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'discussion_workgroup' => array(
		'type' => 'text',
		'length' => 32
		),
    'discussion_type' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => 1,
        'default' => 'context'
		),
    'discussion_name' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => 1
		),
    'discussion_description' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => 1
		),
    'discussion_visible' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
		),
    'topics' => array(
		'type' => 'integer',
		'length' => 11,
        'notnull' => TRUE,
        'default' => 0
		),
    'post' => array(
		'type' => 'integer',
		'length' => 11,
        'notnull' => TRUE,
        'default' => 0
		),
    'lasttopic' => array(
		'type' => 'text',
		'length' => 32
		),
    'lastpost' => array(
		'type' => 'text',
		'length' => 32
		),
    'defaultdiscussion' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
		),
    'discussionlocked' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
		),
    'ratingsenabled' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
		),
    'studentstarttopic' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
		),
    'attachments' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
		),
    'subscriptions' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
		),
    'moderation' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
		),
    'archivedate' => array(
		'type' => 'date'
		),
    'datelastupdated' => array(
		'type' => 'timestamp'
		)
	);
?>