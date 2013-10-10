<?php

  $sqldata[]="CREATE TABLE tbl_gameinfo(
  id varchar(32) NOT NULL default '',
  sportId varchar(32),
  tournamentId varchar(32) default '',
  teamAId varchar(32) NOT NULL default '',
  teamBId varchar(32) NOT NULL default '',
  teamAscores int(3),
  teamBscores int(3),
  creationDate timestamp,
  updatedBy varchar(32),
  PRIMARY KEY(id),
  FOREIGN KEY (sportId)
  REFERENCES tbl_sports(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (tournamentId)
  REFERENCES tbl_tournament(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY( teamAId)
  REFERENCES tbl_team(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY( teamBId)
  REFERENCES tbl_team(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE
 
  
)TYPE=InnoDB
";

?>