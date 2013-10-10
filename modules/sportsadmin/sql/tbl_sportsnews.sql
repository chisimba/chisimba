<?php

  $sqldata[]="CREATE TABLE tbl_sportsnews (
  id varchar(32) NOT NULL default '',
  news text,
  creator varchar(50) NOT NULL default '',
  teamId varchar(32) not NULL default '',
  modifiedBy varchar(30) NOT NULL default '',
  sportId varchar(50) not null,
  dateCreated timestamp,  
  updated timestamp,
  PRIMARY KEY(id),
  FOREIGN KEY (teamId)
  REFERENCES tbl_team(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (creator)
  REFERENCES tbl_users(userId) 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (sportId)
  REFERENCES tbl_sports(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (modifiedBy)
  REFERENCES tbl_users (userId)
  ON DELETE CASCADE
  ON UPDATE CASCADE
  
  
)TYPE=InnoDB
";


?>