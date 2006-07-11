<?php
/*
CREATE TABLE `tbl_userloginhistory` (
  `id` varchar(32) NOT NULL,
  `userId` char(25) NOT NULL default '0',
  `lastLoginDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`)
) TYPE=InnoDB  ROW_FORMAT=FIXED COMMENT='Used to hold the loginhistory of a user';

*/
// Table Name
$tablename = 'tbl_loggedinusers';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table is used to maintain state and enable communication', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'userId' => array(
		'type' => 'text',
		'length' => 25,
        'notnull' => TRUE,
        'default' => '0'
		),
    'lastLoginDateTime' => array(
		'type' => 'datetime',
        'notnull' => TRUE,
        'default' => '0000-00-00 00:00:00'
		)
    );

//create other indexes here...

$name = 'userId';

$indexes = array(
                'fields' => array(
                	'userId' => array()
                )
        );
?>