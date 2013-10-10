<?php

  $sqldata[]="CREATE TABLE tbl_team (
  id varchar(32) NOT NULL default '',
  name varchar(32) NOT NULL default '',
  homeGround varchar(32) NOT NULL default '',
  coach varchar(32) NOT NULL default '',
  motto varchar(32),
  logofile varchar(32) NOT NULL default '' ,
  sportId varchar(32) NOT NULL default '',
  PRIMARY KEY(id),
  FOREIGN KEY (sportId)
  REFERENCES tbl_sports(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE  
  
)TYPE=InnoDB
";


?>