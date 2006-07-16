<?php
/*
CREATE TABLE `tbl_modules_owned_tables` (
  `id` int(10) NOT NULL auto_increment,
  `kng_module` varchar(50) NOT NULL default '0',
  `tablename` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `tbl_kng_modules_owned_tables_FKIndex1` (`kng_module`),
  KEY `kng_module` (`kng_module`)
) TYPE=InnoDB ;

#INSERT INTO tbl_modules_owned_tables (kng_module,tablename) VALUES ('moduleadmin','tbl_modules');
#INSERT INTO tbl_modules_owned_tables (kng_module,tablename) VALUES ('moduleadmin','tbl_modules_dependencies');
#INSERT INTO tbl_modules_owned_tables (kng_module,tablename) VALUES ('moduleadmin','tbl_modules_owned_tables');
#INSERT INTO tbl_modules_owned_tables (kng_module,tablename) VALUES ('moduleadmin','tbl_language_modules');

#INSERT INTO tbl_modules_owned_tables (kng_module,tablename) VALUES ('security','tbl_users');
#INSERT INTO tbl_modules_owned_tables (kng_module,tablename) VALUES ('security','tbl_loggedinusers');
#INSERT INTO tbl_modules_owned_tables (kng_module,tablename) VALUES ('security','tbl_userloginhistory');
*/
// Table Name
$tablename = 'tbl_modules_owned_tables';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'kng_module' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE,
        'default' => '0'
		),
    'tablename' => array(
		'type' => 'text',
        'length' => 150,
        'notnull' => TRUE
		)
    );

//create other indexes here...

$name = 'module_tables';

$indexes = array(
                'fields' => array(
                	'kng_module' => array()
                )
        );
?>