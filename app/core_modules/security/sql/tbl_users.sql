<?php
/*
CREATE TABLE tbl_users (
  id varchar(32) NOT NULL,
  userId varchar(25) NOT NULL default '0',
  username varchar(25) NOT NULL default '',
  title varchar(25) NOT NULL default '',
  firstName varchar(50) NOT NULL default '',
  surname varchar(50) NOT NULL default '',
  PASSWORD varchar(100) NOT NULL default '',
  creationDate date NOT NULL default '0000-00-00',
  emailAddress varchar(100) NOT NULL default '',
  logins int(11) default '0',
  sex char(1) default '',
  country char(2) default '',
  accesslevel char(1) default '0',
  isActive CHAR(1) DEFAULT '1',
  howCreated VARCHAR(32) DEFAULT 'unknown',
  updated timestamp(14),
  PRIMARY KEY  (id),
  INDEX userId (userId)
) TYPE=InnoDB  COMMENT='Primary user information';

INSERT INTO tbl_users (id,userId,username,title,firstName,surname,PASSWORD,creationDate,emailAddress,logins,sex,country,accessLevel,isActive,howCreated) VALUES ('init_1','1', 'admin', 'Dr', 'Administrative', 'User', '86f7e437faa5a7fce15d1ddcb9eaeaea377667b8', '0000-00-00', 'admin@localhost.local', 0, 'M', 'ZA','1','1','install');

*/
// Table Name
$tablename = 'tbl_users';

//Options line for comments, encoding and character set
$options = array('comment' => 'Primary user information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userId' => array(
		'type' => 'text',
		'length' => 25,
        'notnull' => TRUE,
        'default' => '0'
		),
    'username' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
		),
    'title' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
		),
    'firstName' => array(
		'type' => 'text',
        'length' => 50,
        'notnull' => TRUE
		),
    'surname' => array(
		'type' => 'text',
        'length' => 50,
        'notnull' => TRUE
		),
    'PASSWORD' => array(
		'type' => 'text',
        'length' => 100,
        'notnull' => TRUE
		),
    'creationDate' => array(
		'type' => 'date',
        'default' => '0000-00-00'
		),
    'emailAddress' => array(
		'type' => 'text',
        'length' => 100,
        'notnull' => TRUE
		),
    'logins' => array(
		'type' => 'integer',
        'length' => 11,
        'default' => 0
		),
    'sex' => array(
		'type' => 'text',
        'length' => 1
		),
    'country' => array(
		'type' => 'text',
        'length' => 2
		),
    'accesslevel' => array(
		'type' => 'text',
        'length' => 1,
        'default' => '0'
		),
    'isActive' => array(
		'type' => 'text',
        'length' => 1,
        'default' => '1'
		),
    'howCreated' => array(
		'type' => 'text',
        'length' => 32,
        'default' => 'unknown'
		),
    'update' => array(
		'type' => 'timestamp'
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