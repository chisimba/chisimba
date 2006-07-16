<?php
/*
CREATE TABLE `tbl_loggedinusers` (
   id int(11) NOT NULL auto_increment,
  `userId` varchar(25) NOT NULL default '0',
  `ipAddress` varchar(100) NOT NULL default '',
  `sessionId` varchar(100) NOT NULL default '',
  `whenLoggedIn` datetime NOT NULL default '0000-00-00 00:00:00',
  `WhenLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  `isInvisible` tinyint(1) NOT NULL default '0',
  `coursecode` varchar(100) NOT NULL default '',
  `themeUsed` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB  COMMENT='This table is used to maintain state and enable communication';

*/
// Table Name
$tablename = 'tbl_loggedinusers';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table is used to maintain state and enable communication', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'integer',
		'length' => 11,
        'notnull' => TRUE,
        'autoincrement' => TRUE
		),
	'userId' => array(
		'type' => 'text',
		'length' => 25,
        'notnull' => TRUE,
        'default' => '0'
		),
    'ipAddress' => array(
		'type' => 'text',
        'length' => 100,

		),
    'sessionId' => array(
		'type' => 'text',
        'length' => 100,

		),
    'whenLoggedIn' => array(
		'type' => 'datetime',
        'notnull' => TRUE,
        'default' => '0000-00-00 00:00:00'
		),
    'WhenLastActive' => array(
		'type' => 'datetime',
        'notnull' => TRUE,
        'default' => '0000-00-00 00:00:00'
		),
    'isInvisible' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 0
		),
    'coursecode' => array(
		'type' => 'text',
        'length' => 100,

		),
    'themeUsed' => array(
		'type' => 'text',
        'length' => 100,

		)
    );

?>
