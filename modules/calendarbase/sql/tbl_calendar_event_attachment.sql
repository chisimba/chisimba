<?php
/*
$sqldata[]="CREATE TABLE `tbl_calendar_event_attachment` (
  `id` varchar(32) NOT NULL default '',
  `event_id` varchar(32) NOT NULL default '',
  `attachment_id` varchar(32) NOT NULL default '',
  `userId` varchar(25) NOT NULL default '',
  `dateLastUpdated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `attachment_id` (`event_id`, `attachment_id`, `userId`)
) TYPE=InnoDB   COMMENT='This table stores temporary uploads while a post is being created';";
*/

$tablename = 'tbl_calendar_event_attachment';

$options = array('comment' => 'This table stores temporary uploads while a post is being created', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'event_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'attachment_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userId' => array(
		'type' => 'text',
		'length' => 25
		),
	'dateLastUpdated' => array(
		'type' => 'date',

		)
	);


//create other indexes here...

$name = 'attachment_id';

$indexes = array(
                'fields' => array(
                	'event_id' => array(),
                	'attachment_id' => array(),
                	'userId' => array()
                )
        );

?>