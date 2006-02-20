<?php
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
?>
