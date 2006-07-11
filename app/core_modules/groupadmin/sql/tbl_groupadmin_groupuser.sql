<?php
$sqldata[] = "CREATE TABLE tbl_groupadmin_groupuser (
  id VARCHAR(32) NOT NULL,
  group_id VARCHAR(32) NOT NULL,
  user_id VARCHAR(32) NOT NULL,
  
  last_updated DATETIME NULL,
  last_updated_by VARCHAR(32) NULL,

  PRIMARY KEY(id),
  INDEX ind_groupuser_FK(group_id),
  INDEX ind_usergroup_FK(user_id),

  FOREIGN KEY(group_id)
    REFERENCES tbl_groupadmin_group(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE,

  FOREIGN KEY(user_id)
    REFERENCES tbl_users(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
 ) TYPE = INNODB COMMENT = 'This is the bridge table between user and group table';";
$sqldata[]=sprintf("insert into `tbl_groupadmin_groupuser` (id, group_id, user_id, last_updated, last_updated_by )
            values ( 'init_1', 'init_1', 'init_1', '%s', '1' )", date("Y:m:d H:i:s"));
            
// Table Name
$tablename = 'tbl_groupadmin_groupuser';

//Options line for comments, encoding and character set
$options = array('comment' => 'This is the bridge table between user and group table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'group_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'user_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'last_updated' => array(
		'type' => 'datetime',
		'length' => 100
		),
    'last_updated_by' => array(
		'type' => 'text',
		'length' => 32
		)
    );

//create other indexes here...

$name = 'ind_groupuser_FK';

$indexes = array(
                'fields' => array(
                	'group_id' => array(),
                    'user_id' => array(),
                )
        );
?>