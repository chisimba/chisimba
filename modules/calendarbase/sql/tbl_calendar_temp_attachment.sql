<?php
// $sqldata[]="CREATE TABLE `tbl_calendar_temp_attachment` (
  // `id` varchar(32) NOT NULL default '',
  // `temp_id` varchar(64) NOT NULL default '',
  // `attachment_id` varchar(32) NOT NULL default '',
  // `userId` varchar(32) NOT NULL default '',
  // `dateLastUpdated` datetime NOT NULL default '0000-00-00 00:00:00',
  // PRIMARY KEY  (`id`),
  // KEY `attachment_id` (`attachment_id`, `userId`,`temp_id`)
// ) TYPE=InnoDB   COMMENT='This table stores temporary uploads while a post is being created';";


$tablename = 'tbl_calendar_temp_attachment';

$options = array('comment' => 'This table stores temporary uploads while an event is being created', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'temp_id' => array(
		'type' => 'text',
		'length' => 64
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

// Other Indexes

$name = 'attachment_id';

$indexes = array(
                'fields' => array(
                	'attachment_id' => array(),
                	'temp_id' => array(),
                	'userId' => array()
                )
        );

?>