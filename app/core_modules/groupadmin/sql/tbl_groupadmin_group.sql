<?php
/*
 $sqldata[] = "CREATE TABLE tbl_groupadmin_group (
  id VARCHAR(32) NOT NULL,
  parent_id VARCHAR(32) NULL,
  name VARCHAR(32) NULL,
  description VARCHAR(100) NULL,

  last_updated DATETIME NULL,
  last_updated_by VARCHAR(32) NULL,
  
  PRIMARY KEY(id),
  INDEX ind_groups_FK(parent_id)
) TYPE = INNODB COMMENT = 'The tbl_groupadmin_groups table is managed by the groupadmin module, its purpose to allow for groups and subgroups. This allows for users to have a context ( ie. In KNG, the permissions module will require information about the users context )';";

$sqldata[]=sprintf("insert into `tbl_groupadmin_group` (id, parent_id, name, description, last_updated, last_updated_by )
            values ('init_1', NULL , 'Site Admin', 'The site administration users group list', '%s', '1' )", date("Y:m:d H:i:s") );
$sqldata[]=sprintf("insert into `tbl_groupadmin_group` (id, parent_id, name, description, last_updated, last_updated_by )
            values ('init_2', NULL , 'Lecturers', 'The site wide Lecturers user group', '%s', '1' )", date("Y:m:d H:i:s") );
$sqldata[]=sprintf("insert into `tbl_groupadmin_group` (id, parent_id, name, description, last_updated, last_updated_by )
            values ('init_3', NULL , 'Students', 'The site wide Students user group', '%s', '1' )", date("Y:m:d H:i:s") );
$sqldata[]=sprintf("insert into `tbl_groupadmin_group` (id, parent_id, name, description, last_updated, last_updated_by )
            values ('init_4', NULL , 'Guest', 'The site wide Guest user group', '%s', '1' )", date("Y:m:d H:i:s") );
*/
// Table Name
$tablename = 'tbl_groupadmin_group';

//Options line for comments, encoding and character set
$options = array('comment' => 'The tbl_groupadmin_groups table is managed by the groupadmin module, its purpose to allow for groups and subgroups. This allows for users to have a context ( ie. In KNG, the permissions module will require information about the users context )', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'parent_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'name' => array(
		'type' => 'text',
		'length' => 32
		),
    'description' => array(
		'type' => 'text',
		'length' => 100
		),
    'last_updated' => array(
		'type' => 'date'
		),
    'last_updated_by' => array(
		'type' => 'text',
		'length' => 32
		)
    );

//create other indexes here...

$name = 'ind_groups_FK';

$indexes = array(
                'fields' => array(
                	'parent_id' => array()
                )
        );
?>
