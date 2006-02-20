<?php
  $sqldata[] = "CREATE TABLE tbl_permissions_acl_description (
  id VARCHAR(32) NOT NULL,
  name VARCHAR(100) UNIQUE,
  description VARCHAR(100),
  
  last_updated DATETIME NOT NULL,
  last_updated_by VARCHAR(32) NULL,
  
  PRIMARY KEY (id)
) TYPE=InnoDB COMMENT='This table stores access control list acl description for debugig purposes.'";
?>
