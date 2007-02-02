<?php
/*
The table for the event log. The fields are:
 id - The framework generated primary key
 userId - The userId of the currently logged in user
 module - The module code from the querystring
 eventCode - A code to represent the event
 eventParamName - The type of event parameters sent
 eventParamValue - Any parameters the event needs to send
 context - The context of the event
 dateCreated - The datetime stamp for the event

*/

$tablename = 'tbl_logger';

$options = array('comment' => 'Table to hold the log events', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userId' => array(
		'type' => 'text',
		'length' => 25
		),
	'module' => array(
		'type' => 'text',
		'length' => 32
		),
	'eventcode' => array(
		'type' => 'text',
		'length' => 32
		),
    'eventParamName' => array(
		'type' => 'text',
		'length' => 32
		),
    'eventParamValue' => array(
		'type' => 'text',
		'length' => 255
		),
	'context' => array(
		'type' => 'text',
		'length' => '32',
		),
    'action' => array (
        'type' => 'text',
		'length' => '50',
        ),
    'ipaddress' => array (
        'type' => 'text',
		'length' => '50',
        ),
    'referrer' => array (
        'type' => 'text',
		'length' => '255',
        ),
	'dateCreated' => array(
		'type' => 'timestamp',
		),
	'dateLastUpdated' => array(
		'type' => 'timestamp'
		),
    'isLanguageCode' => array(
		'type' => 'text', // tiny int
		'length' => 10
		)
	);

$name = 'tbl_logger_idx';

$indexes = array(
                'fields' => array(
                	'userId' => array(),
                	'module' => array(),
                	'context' => array(),
                	'action' => array(),
                	'ipaddress' => array()
                )
        );


?>