<?php

/*
  $sqldata[] = "CREATE TABLE tbl_permissions_acl (
  id VARCHAR(32) NOT NULL,

  acl_id VARCHAR(32) NULL,
  user_id  VARCHAR(32) NULL,
  group_id VARCHAR(32) NULL,

  last_updated DATETIME NOT NULL,
  last_updated_by VARCHAR(32) NULL,

  PRIMARY KEY (id),

  INDEX ind_acl_FK(acl_id),
  INDEX ind_groupuser_FK(group_id),
  INDEX ind_usergroup_FK(user_id),

  FOREIGN KEY(acl_id)
    REFERENCES tbl_permissions_acl_description(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
      
  FOREIGN KEY(group_id)
    REFERENCES tbl_groupadmin_group(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE,

  FOREIGN KEY(user_id)
    REFERENCES tbl_users(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='This table stores access control list for permissions.';";
*/

// Table Name
$tablename = 'tbl_permissions_acl';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores access control list for permissions.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'acl_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'user_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'group_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'last_updated' => array(
		'type' => 'date',
		),
    'last_updated_by' => array(
		'type' => 'text',
		'length' => 32
		)
    );

//create other indexes here...

$name = 'ind_acl_FK';

$indexes = array(
                'fields' => array(
                	'acl_id' => array(),
                    'group_id' => array(),
                    'user_id' => array()
                )
        );
?>
