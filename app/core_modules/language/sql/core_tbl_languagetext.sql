######################################## Language ################################

CREATE TABLE tbl_languagetext (
  code varchar(50) NOT NULL default '',
  description varchar(255) default NULL,
  PRIMARY KEY  (code)
) TYPE=InnoDB ;