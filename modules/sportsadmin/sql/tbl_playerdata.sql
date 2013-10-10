<?php

  $sqldata[]="CREATE TABLE tbl_playerdata(
  id varchar(32) NOT NULL default '',
  playerId varchar(32) NOT NULL default '',
  event text(32) NOT NULL default '',
  enteredBy vaRchar (32) NOT NULL default '',
  updated datetime,
  dateEntered timestamp,
  updatedBy varchar(32) NOT NULL default '',
  PRIMARY KEY(id),
  FOREIGN KEY (playerId)
  REFERENCES tbl_player(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (updatedBy) 
  REFERENCES tbl_users(userId)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (enteredBy) 
  REFERENCES tbl_users(userId)
  ON DELETE CASCADE
  ON UPDATE CASCADE    
  
)TYPE=InnoDB
";


?>