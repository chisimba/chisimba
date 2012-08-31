CREATE TABLE tbl_language_modules (
  id int(11) NOT NULL auto_increment,
  module_id varchar(50) NOT NULL default '',
  code varchar(50) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=InnoDB ;

INSERT INTO tbl_language_modules VALUES (77,'moduleadmin','ModuleAdmin');