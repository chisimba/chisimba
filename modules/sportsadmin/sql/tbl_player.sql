<?php

  $sqldata[]="CREATE TABLE tbl_player (
  id varchar(32) NOT NULL default '',
  name varchar(32) NOT NULL default '',
  dateOfBirth date,
  team varchar(50) not null default '',
  country varchar(32) NOT NULL default '',
  sportId varchar(50) NOT NULL default '', 
  playerimage varchar(50) NOT NULL default '',
  position varchar(32) NOT NULL default '',
  updated TIMESTAMP (14) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (sportId)
  REFERENCES tbl_sports(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE  
  
)TYPE=InnoDB
";


?>