<?php
 $sqldata[] = "ALTER TABLE tbl_groupadmin_group 
ADD FOREIGN KEY(parent_id)
    REFERENCES tbl_groupadmin_group(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE;";
?>