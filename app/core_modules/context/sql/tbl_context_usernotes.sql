<?php
/*
$sqldata[]="CREATE TABLE `tbl_chat_users` (
  `id` varchar(32) NOT NULL default '',
  `username` varchar(20) default NULL,
  `contextId` bigint(20) default NULL,  
  `start` bigint(20) default NULL,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB ROW_FORMAT=DYNAMIC;
";*/

$tablename = 'tbl_context_usernotes';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'username' => array(
		'type' => 'text',
		'length' => 25
		),
	'contextId' => array(
		'type' => 'integer',
		'length' => 20
		),
    'start' => array(
		'type' => 'integer',
		'length' => 20
		),
    'updated' => array(
        'type' => 'timestamp'
        )
    );
    

?>