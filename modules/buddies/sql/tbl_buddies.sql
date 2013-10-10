<?php
/*$sqldata[]="
CREATE TABLE `tbl_buddies` (
`id` varchar(32) NOT NULL,
`userId` varchar(25) default NULL,
`buddyId` varchar(25) default NULL,
`isBuddy` char(1) default NULL,
`isFan` char(1) default NULL,
`updated` timestamp(14),
PRIMARY KEY  (`id`),
KEY `userId` (`userId`),
KEY `buddyId` (`buddyId`),
CONSTRAINT `tbl_buddies_ibfk_2` FOREIGN KEY (`buddyId`) REFERENCES `tbl_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `tbl_buddies_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `tbl_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
)
TYPE=InnoDB ROW_FORMAT=DYNAMIC
";*/

//5ive definition
$tablename = 'tbl_buddies';

//Options line for comments, encoding and character set
$options = array('comment' => 'Used to store your buddy list', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
	),
	'userid' => array(
		'type' => 'text',
		'length' => 25
	),
	'buddyid' => array(
		'type' => 'text',
		'length' => 25
	),
	'isBuddy' => array(
		'type' => 'text',
		'length' => 1
	),
	'isFan' => array(
		'type' => 'text',
		'length' => 1
	),	
	'updated' => array(
	'type' => 'timestamp'
	)
);
	


?>
