<?php
/*
CREATE TABLE `tbl_modules` (
   id int(11) NOT NULL auto_increment,
  module_id varchar(50) NOT NULL default '0',
  module_authors text,
  module_releasedate datetime default NULL,
  module_version varchar(20) default NULL,
  module_path varchar(255) default NULL,
  isAdmin tinyint(1) NOT NULL default '0',
  isVisible tinyint(1) NOT NULL default '1',
  hasAdminPage tinyint(1) default '1',
  isContextAware tinyint(1) NOT NULL default '0',
  dependsContext tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB;
*/
// Table Name
$tablename = 'tbl_modules';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'module_id' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE,
        'default' => '0'
		),
    'module_authors' => array(
		'type' => 'text'
		),
    'module_releasedate' => array(
		'type' => 'datetime'
		),
    'module_version' => array(
		'type' => 'text',
        'length' => 20
		),
    'module_path' => array(
		'type' => 'text',
		'length' => 255
		),
    'isAdmin' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 0
		),
    'isVisible' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 1
		),
    'hasAdminPage' => array(
		'type' => 'integer',
        'length' => 1,
        'default' => 1
		),
    'isContextAware' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 0
		),
    'dependsContext' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 0
		)
    );

?>